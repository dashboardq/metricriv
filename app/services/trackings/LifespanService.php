<?php

namespace app\services\trackings;

use app\models\Category;
use app\models\Connection;
use app\models\Number;
use app\models\Restriction;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class LifespanService {
    public static function main($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = 0;
        $args['name'] = $val['name'];
        $args['status'] = 'active';
        $args['method'] = '';
        $args['check_interval'] = $val['interval'];
        $args['data'] = ['number' => $number->data['data']];
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }
}
