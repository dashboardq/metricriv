<?php

namespace mavoc\core;

use app\models\APIKey;
use app\models\RefreshLogin;
use app\models\User;

class Session {
    public $data = [];
    public $flash;
    public $next_flash;
    public $flash_types = ['fields'];
    public $type = 'web';
    public $user;
    public $user_id = 0;
    public $id;

    public function __construct() {
        register_shutdown_function([$this, 'save']);
    }   

    public function init() {
        session_set_cookie_params(ao()->env('APP_SESSION_SECONDS'), '/'); 
        session_start();
        $this->id = session_id();

        // API call is being made.
        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $this->type = 'api';

            $api_username = $_SERVER['PHP_AUTH_USER'];
            $api_username = ao()->hook('ao_session_api_username', $api_username);

            $api_password = $_SERVER['PHP_AUTH_PW'];
            $api_password = ao()->hook('ao_session_api_password', $api_password);

            $user = APIKey::validate($api_username, $api_password);

            $this->user = $user;
            $this->user_id = $user->id;
            $this->flash = [];
            $this->data = [];

            ao()->hook('ao_session_api_passed', $user->id);
        } else {
            if(isset($_SESSION['flash'])) {
                $this->flash = $_SESSION['flash'];
            } else {
                $this->flash = [];
            }

            if(in_array(ao()->env('APP_LOGIN_TYPE'), ['db', 'list'])) {
                if(isset($_SESSION['user_id'])) {
                    if(ao()->env('APP_LOGIN_TYPE') == 'db') { 
                        $this->user = User::find($_SESSION['user_id']);
                    } elseif(ao()->env('APP_LOGIN_TYPE') == 'list') { 
                        $this->user = User::local($_SESSION['user_id']);
                    }
                    if($this->user) {
                        $this->user_id = $_SESSION['user_id'];
                    } else {
                        $this->user_id = 0;
                    }
                } else {
                    $this->user_id = 0;
                }
            }

            if(!isset($_SESSION['user_id']) && $this->user_id == 0) {
                $refresh = RefreshLogin::refresh();
                if(isset($refresh['user']) && isset($refresh['user_id'])) {
                    $this->user = $refresh['user'];
                    $this->user_id = $refresh['user_id'];
                }
            }

            if(isset($_SESSION['data'])) {
                $this->data = $_SESSION['data'];
            } else {
                $this->data = [];
            }
        }
    }

    public function flash($type, $value) {
        if(!isset($this->next_flash[$type])) {
            $this->next_flash[$type] = [];
        }

        // Specific types are handled differently like "fields"
        if(in_array($type, $this->flash_types)) {
            $this->next_flash[$type] = $value;
        } else {
            if(is_array($value) || is_object($value)) {
                foreach($value as $field => $message) {
                    if(!isset($this->next_flash[$type][$field])) {
                        $this->next_flash[$type][$field] = [];
                    }
                    if(is_array($message)) {
                        foreach($message as $msg) {
                            $this->next_flash[$type][$field][] = $msg;
                        }
                    } else {
                        $this->next_flash[$type][$field][] = $message;
                    }
                }
            } else {
                $field = 'general';

                if(!isset($this->next_flash[$type][$field])) {
                    $this->next_flash[$type][$field] = [];
                }

                $this->next_flash[$type][$field][] = $value;
            }

        }
    }

    public function logout() {
        session_destroy();

        RefreshLogin::destroy();
    }

    public function save() {
        $_SESSION['flash'] = $this->next_flash;
        $_SESSION['data'] = $this->data;
        $_SESSION['user'] = $this->user;
        $_SESSION['user_id'] = $this->user_id;
    }
}
