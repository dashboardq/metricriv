<?php

namespace app\services\connections;

use app\models\Category;
use app\models\Connection;
use app\models\Number;
use app\models\Restriction;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class GithubConnectionService {
    public static $SERVICE = 'GITHUB';

    public static function redirect($req, $res) {
        $val = $req->val('query', [
            'state' => ['required'],
            'code' => ['required'],
        ], ao()->env('APP_PRIVATE_HOME'));

        // Need to check the state against session data.
        $state = $req->query['state'];
        if(ao()->session->data[strtolower(self::$SERVICE) . '_state'] != $state) {
            $res->error('There was a problem with the login. Please try again and if the problem happens again, please contact NumbersQ support.', ao()->env('APP_PRIVATE_HOME'));
        }

        $rest = new REST();
        // Make a curl call.
        $url = ao()->env(self::$SERVICE . '_URL_TOKEN');
        $post = [
            'client_id' => ao()->env(self::$SERVICE . '_CLIENT_ID'),
            'client_secret' => ao()->env(self::$SERVICE . '_CLIENT_SECRET'),
            'code' => $val['code'],
            'redirect_uri' => ao()->env(self::$SERVICE . '_URL_REDIRECT'),
        ];
        $access = $rest->post($url, $post, [], true);
        if(!isset($access) || isset($access->error)) {
            // TODO: Log error

            $res->error('There was a problem completing the login. Please try again and if the problem happens again, please contact NumbersQ support.', ao()->env('APP_PRIVATE_HOME'));
        }

        $category_id = ao()->session->data[strtolower(self::$SERVICE) . '_category_id'];
        
        // Create the connection
        $args = [];
        $args['user_id'] = $req->user_id;
        $args['category_id'] = $category_id;
        $args['data'] = ['access' => $access, 'name' => 'Main'];
        $args['encrypted'] = 0;
        $connection = Connection::create($args);
        
        $res->redirect('/connection/edit/' . $connection->id);
    }

    public static function start($req, $res, $state) {
        $scope = 'repo';

        $args = [];
        $args['client_id'] = ao()->env(self::$SERVICE . '_CLIENT_ID');
        $args['redirect_uri'] = ao()->env(self::$SERVICE . '_URL_REDIRECT');
        $args['scope'] = $scope;
        $args['state'] = $state;

        $url = ao()->env(self::$SERVICE . '_URL_AUTHORIZE');
        $url .= '?' . http_build_query($args);

        $res->redirect($url);
    }
}
