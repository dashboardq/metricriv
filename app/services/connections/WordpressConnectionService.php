<?php

namespace app\services\connections;

use app\models\Category;
use app\models\Connection;
use app\models\Number;
use app\models\Restriction;
use app\models\Tracking;

use app\services\trackings\WordpressService;

use mavoc\core\REST;

use DateTime;

class WordpressConnectionService {
    public static function main($req, $res, $state) {
        $val = $req->val('data', [
            'name' => ['required'],
            'api_key' => ['required'],
        ]);

        $val = $req->clean($val, [
            'api_key' => ['trim'],
        ]);
        



        // Confirm API Key is valid
        $api_key = $val['api_key'];
        $type = 'wp_users';
        $range = '1d';
        $ago = '1d';
        $url = WordpressService::parseURL(ao()->request->user_id, $api_key, $type, $range, $ago);
        if($url === false) {
            $res->error('There appears to be a problem with the API key. Please confirm that the API key is entered correctly. If you continue to have issues, please contact support.');
        }

        $rest = new REST();
        $body = $rest->get($url);
        $result = WordpressService::parseBody($body);
        if($result == -1) {
            $res->error('The API Key entered does not appear to return valid information. Please confirm that your API Key and other info were entered correctly. If you continue to have issues, please contact support.');
        }






        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        $data = [
            'name' => $val['name'],
            'api_key' => $val['api_key'],
        ];

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['category_id'] = $category->id;
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $connection = Connection::create($args);

        $res->redirect($req->path . '/' . $connection->id);
    }
}
