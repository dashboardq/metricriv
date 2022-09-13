<?php

namespace app\services\connections;

use app\models\Category;
use app\models\Connection;
use app\models\Number;
use app\models\Restriction;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class ScreenshotoneConnectionService {
    public static function main($req, $res, $state) {
        $val = $req->val('data', [
            'name' => ['required'],
            'api_key' => ['required'],
        ]);

        $val = $req->clean($val, [
            'api_key' => ['trim'],
        ]);

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
