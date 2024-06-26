<?php

namespace mavoc;

if(is_file('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
}

use app\App;
use app\Local;

use mavoc\console\Main as ConsoleMain;

use mavoc\core\Confs;
use mavoc\core\Console;
use mavoc\core\DB;
use mavoc\core\Email;
use mavoc\core\Exception;
use mavoc\core\Hooks;
use mavoc\core\HTML;
use mavoc\core\Plugins;
use mavoc\core\Route;
use mavoc\core\Router;
use mavoc\core\Request;
use mavoc\core\Response;
use mavoc\core\Session;

// Probably should handle this with the autoload code. For now, it works for what is needed.
require_once 'other/helpers.php';

//require_once 'console/Main.php';

require_once 'core/Clean.php';
require_once 'core/Cleaners.php';
require_once 'core/Confs.php';
require_once 'core/Console.php';
require_once 'core/DB.php';
require_once 'core/GenericController.php';
require_once 'core/Email.php';
require_once 'core/Exception.php';
require_once 'core/Hooks.php';
require_once 'core/HTML.php';
require_once 'core/InternalREST.php';
require_once 'core/Model.php';
require_once 'core/Plugins.php';
require_once 'core/Route.php';
require_once 'core/Router.php';
require_once 'core/Request.php';
require_once 'core/Response.php';
require_once 'core/REST.php';
require_once 'core/Secret.php';
require_once 'core/Session.php';
require_once 'core/Validate.php';
require_once 'core/Validators.php';

// Handle autoloading any other files.
// TODO: Eventually need to move this to where it can hooked.
spl_autoload_register(function($class) {
    $parts = explode('\\', $class);
    $file = '..' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';
    if(is_file($file)) {
        include $file;
    }
});

class Mavoc {
    public $app;
    public $confs;
    public $console;
    public $db;
    public $email;
    public $envs = [];
    public $hooks;
    public $html;
    public $local;
    public $plugins;
    public $request;
    public $response;
    public $router;
    public $session;

    public $type = 'web';

    // Probably need to move this to some kind of alternative $this->conf() system but for true/false checkpoints.
    // Maybe create $this->checkPoint()
    public $session_initialized = false;

    public function __construct($envs) {
        $this->envs = $envs;
    }

    // This will not work well with interactive commands.
    // All output is suppressed.
    public function command($command) {
        return $this->console->call($command);
    }

    public function conf($key, $value = null) {
        $output = $this->confs->conf($key, $value);
        $output = $this->hook('ao_conf', $output);
        $output = $this->hook('ao_conf_' . $key, $output);
        return $output;
    }

    public function dir($input) {
        $subdir = preg_replace('|[\\\/]|', DIRECTORY_SEPARATOR, $input);
        $output = ao()->env('AO_BASE_DIR') . DIRECTORY_SEPARATOR . $subdir;
        $output = $this->hook('ao_dir', $output);
        return $output;
    }

    public function env($key, $value = null) {
        // Don't try to hook if it is not available yet.
        if(!$this->confs || $key == 'AO_OUTPUT_HOOKS') {
            $output = $this->envs[$key] ?? null;
            return $output;
        } else {
            $output = $this->envs[$key] ?? null;
            $output = $this->hook('ao_env', $output);
            $output = $this->hook('ao_env_' . $key, $output);
            return $output;
        }
    }

    public function error($message) {
        throw new \Exception($message);
    }

    public function filter($key, $args = [], $priority = 10) {
        return $this->hooks->filter($key, $args, $priority);
    }

    //public function hook($key, $item = null, $args = []) {
    //}
    public function hook() {
        $func_args = func_get_args();
        $key = $func_args[0];
        $item = null;
        if(isset($func_args[1])) {
            $item = $func_args[1];
        }
        $args = [];
        $args[] = $key;
        $args[] = $item;
        for($i = 2; $i < count($func_args); $i++) {
            $args[] = $func_args[$i];
        }
        //return $this->hooks->hook($key, $item, $args);
        return call_user_func_array([$this->hooks, 'hook'], $args);
    }

    public function init() {
        // These are set up here so that ao() can be used.
        // Create the hook and configuration system and then override it later like all the other classes.
        $this->hooks = new Hooks();
        $this->confs = new Confs();

        $this->hook('ao_start');

        // Have a separate creation and then init for hook purposes.
        // Allows setting things up in the constructor and then making 
        // additional overrides in the init() method. Specifically useful
        // for loading and activating (like plugins).
        $this->plugins = new Plugins();
        $this->plugins = $this->hook('ao_plugins', $this->plugins);
        $func = [$this->plugins, 'init'];
        $func = $this->hook('ao_plugins_init', $func);
        call_user_func($func);
        $this->plugins = $this->hook('ao_plugins_initialized', $this->plugins);


        $this->hooks = $this->hook('ao_hooks', $this->hooks);
        $func = [$this->hooks, 'init'];
        $func = $this->hook('ao_hooks_init', $func);
        call_user_func($func);
        $this->hooks = $this->hook('ao_hooks_initialized', $this->hooks);


        $this->confs = $this->hook('ao_confs', $this->confs);
        $func = [$this->confs, 'init'];
        $func = $this->hook('ao_confs_init', $func);
        call_user_func($func);
        $this->confs = $this->hook('ao_confs_initialized', $this->confs);

        $this->console = new Console();
        $this->console = $this->hook('ao_console', $this->console);
        $func = [$this->console, 'init'];
        $func = $this->hook('ao_console_init', $func);
        call_user_func($func);
        $this->console = $this->hook('ao_console_initialized', $this->console);

        // Maybe have this fixed with autoloading
        $app_file = ao()->env('AO_APP_DIR') . DIRECTORY_SEPARATOR . 'App.php';
        $app_file = $this->hook('ao_app_file', $app_file);
        if(is_file($app_file)) {
            require_once $app_file;
            $this->app = new App();
            $this->app = $this->hook('ao_app', $this->app);
            $func = [$this->app, 'init'];
            $func = $this->hook('ao_app_init', $func);
            call_user_func($func);
            $this->app = $this->hook('ao_app_initialized', $this->app);
        }

        $local_file = ao()->env('AO_BASE_DIR') . DIRECTORY_SEPARATOR . '.Local.php';
        $local_file = $this->hook('ao_local_file', $local_file);
        if(is_file($local_file)) {
            require_once $local_file;
            $this->local = new Local();
            $this->local = $this->hook('ao_local', $this->local);
            $func = [$this->local, 'init'];
            $func = $this->hook('ao_local_init', $func);
            call_user_func($func);
            $this->local = $this->hook('ao_local_initialized', $this->local);
        }

        $this->hook('ao_ready');

        $db_use = ao()->env('DB_USE');
        if($db_use) {
            $this->db = new DB();
            $this->db = $this->hook('ao_db', $this->db);
            $func = [$this->db, 'init'];
            $func = $this->hook('ao_db_init', $func);
            call_user_func($func);
            $this->db = $this->hook('ao_db_initialized', $this->db);
        }

        $this->session = new Session();
        $this->session = $this->hook('ao_session', $this->session);
        $func = [$this->session, 'init'];
        $func = $this->hook('ao_session_init', $func);
        call_user_func($func);
        $this->session = $this->hook('ao_session_initialized', $this->session);
        $this->session_initialized = true;

        $this->html = new HTML();
        $this->html = $this->hook('ao_html', $this->html);
        $func = [$this->html, 'init'];
        $func = $this->hook('ao_html_init', $func);
        call_user_func($func);
        $this->html = $this->hook('ao_html_initialized', $this->html);

        $this->router = new Router();
        $this->router = $this->hook('ao_router', $this->router);
        $func = [$this->router, 'init'];
        $func = $this->hook('ao_router_init', $func);
        call_user_func($func);
        $this->router = $this->hook('ao_router_initialized', $this->router);


        $this->request = new Request();
        $this->request = $this->hook('ao_request', $this->request);
        $func = [$this->request, 'init'];
        $func = $this->hook('ao_request_init', $func);
        call_user_func($func);
        $this->request = $this->hook('ao_request_initialized', $this->request);


        $this->response = new Response();
        $this->response = $this->hook('ao_response', $this->response);
        $func = [$this->response, 'init'];
        $func = $this->hook('ao_response_init', $func);
        call_user_func($func);
        $this->response = $this->hook('ao_response_initialized', $this->response);

        $this->request = $this->hook('ao_request_available', $this->request);
        $this->response = $this->hook('ao_response_available', $this->response);
        $this->session = $this->hook('ao_session_available', $this->session);

        $this->email = new Email();
        $this->email = $this->hook('ao_email', $this->email);
        $func = [$this->email, 'init'];
        $func = $this->hook('ao_email_init', $func);
        call_user_func($func);
        $this->email = $this->hook('ao_email_initialized', $this->email);


        $this->email->req = $this->request;
        $this->email->res = $this->response;
        $this->email->session = $this->session;

        $this->html->req = $this->request;
        $this->html->res = $this->response;
        $this->html->session = $this->session;

        $this->response->req = $this->request;
        $this->response->session = $this->session;
        $this->response->html = $this->html;

        $this->request->res = $this->response;
        $this->request->session = $this->session;
        if($this->session->user) {
            $this->request->user = $this->session->user;
            $this->request->user = $this->hook('ao_user', $this->request->user, $this->request, $this->response);

            $this->request->user_id = $this->session->user_id;
            $this->request->user_id = $this->hook('ao_user_id', $this->request->user_id, $this->request, $this->response);
        }

        try {
            $this->router->route($this->request, $this->response);
        } catch(Exception $e) {
            $response_type = $e->getResponseType();

            if($response_type == 'html') {
                $redirect = $e->getRedirect();
                $redirect = $this->hook('ao_final_exception_redirect', $redirect, $e, $this->request, $this->response);
                $this->response->error($e->getMessage(), $redirect);
            } elseif($response_type == 'json') {
                $output = [];
                $output['status'] = 'error';
                $output['messages'] = [$e->getMessage()];
                $output['meta'] = new \stdClass();
                $output['data'] = new \stdClass();

                echo json_encode($output);
                exit;
            }
        } catch(\Throwable $e) {
            if(isset($this->request->last_url)) {
                $redirect = $this->request->last_url;
            } else {
                $redirect = '/';
            }
            $redirect = $this->hook('ao_final_exception_redirect', $redirect, $e, $this->request, $this->response);

            $this->response->error($e->getMessage(), $redirect);
        }

        $this->hook('ao_end');
    }

    public function once($key, $args = [], $priority = 10) {
        return $this->hooks->once($key, $args, $priority);
    }

    public function unfilter($key, $args = [], $priority = 10) {
        return $this->hooks->unfilter($key, $args, $priority);
    }

}
