<?php

namespace mavoc;

use mavoc\Mavoc;
use mavoc\core\Exception;

require_once 'Mavoc.php';

// Boot allows for loading config and checking for maintenance before loading the full app.
class Boot {
    public $envs = [];

    public function __construct() {
        // Load config variables.
        $this->envs = require '..' . DIRECTORY_SEPARATOR . '.env.php';
    }

    public function init() {
        global $ao;

        if(is_file('..' . DIRECTORY_SEPARATOR . '.boot_start.php')) {
            require '..' . DIRECTORY_SEPARATOR . '.boot_start.php';
        }

        // Display errors if environment is not production.
        if(!in_array($this->envs['APP_ENV'], ['prod', 'production'])) {
            ini_set('display_errors', 1);   
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL); 
        }

        // Check if maintenance needs to be loaded.
        $maintenance = $this->envs['AO_MAINTENANCE'];
        $exclude = $this->envs['AO_MAINTENANCE_EXCLUDE'];
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        if($maintenance && !in_array($ip, $exclude)) {
            $app_name = $this->envs['APP_NAME'];
            $title = 'Maintenance';
            $started = $this->envs['AO_MAINTENANCE_STARTED'];
            $ending = $this->envs['AO_MAINTENANCE_ENDING'];
            $ending_relative = !preg_match('/^\d\d\d\d-\d\d-\d\d.*/', $ending);
            $view = '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'alt' . DIRECTORY_SEPARATOR . 'maintenance.php';
            if(is_file($view)) {
                include $view;
            } else {
                $htm = '';
                $htm .= '<h1>' . htmlspecialchars($title) . '</h1>';
                $htm .= '<p>';
                if($ending_relative) {
                    $htm .= 'The site is currently undergoing maintenance. ';
                    $htm .= 'It started at ' . htmlspecialchars($started) . ' ';
                    $htm .= 'and should last about ' . htmlspecialchars($ending) . '.';
                } else {
                    $htm .= 'The site is currently undergoing maintenance. ';
                    $htm .= 'It started at ' . htmlspecialchars($started) . ' ';
                    $htm .= 'and should end around ' . htmlspecialchars($ending) . '.';
                }
                $htm .= '</p>';
                echo $htm;
            }
            exit;
        }

        if(is_file('..' . DIRECTORY_SEPARATOR . '.boot_end.php')) {
            require '..' . DIRECTORY_SEPARATOR . '.boot_end.php';
        }

        $error_response_type = '';

        try {
            $ao = new Mavoc($this->envs);
            $ao->init();
        } catch(Exception $e) {
            $error_response_type = $e->getResponseType();
        } catch(\Throwable $e) {
            if(isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] == 'application/json') {
                $error_response_type = 'json';
            } else {
                // If an API key is passed, then send a JSON response.
                if(isset($_SERVER['PHP_AUTH_USER']) || isset($_SERVER['PHP_AUTH_PW'])) {
                    $error_response_type = 'json';
                } else {
                    $error_response_type = 'html';
                }
            }
        }

        if($error_response_type == 'html') {
            http_response_code(500);

            $app_name = $this->envs['APP_NAME'];
            $title = 'Error';
            // Don't look for the 500.php file - that will be used if Mavoc is able to fully run $ao->init()
            $view = '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'alt' . DIRECTORY_SEPARATOR . 'boot-error.php';
            if(is_file($view)) {
                include $view;
            } else {
                $htm = '';
                $htm .= '<!DOCTYPE html>';
                $htm .= '<html>';
                $htm .= '<head>';
                $htm .= '<title>Error</title>';
                $htm .= '</head>';
                $htm .= '<body>';
                $htm .= '<h1>' . htmlspecialchars($title) . '</h1>';
                $htm .= '<p>';
                $htm .= 'There appears to be a problem with the server. If this problem persists, please contact support.';
                $htm .= '</p>';
                if(!in_array($this->envs['APP_ENV'], ['prod', 'production'])) {
                    $htm .= '<p>';
                    $htm .= $e->getMessage();
                    $htm .= ' in ';
                    $htm .= $e->getFile();
                    $htm .= ' on line ';
                    $htm .= $e->getLine();
                    $htm .= '</p>';

                    $htm .= '<p>';
                    $htm .= $e->getTraceAsString();
                    $htm .= '</p>';
                }
                $htm .= '</body>';
                $htm .= '</html>';
                echo $htm;
            }
            exit;
        } elseif($error_response_type == 'json') {
            $output = [];
            $output['status'] = 'error';
            $output['messages'] = [$e->getMessage()];
            $output['meta'] = new \stdClass();
            $output['data'] = new \stdClass();

            echo json_encode($output);
            exit;
        }
    }
}
