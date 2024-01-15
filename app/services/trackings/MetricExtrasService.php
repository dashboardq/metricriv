<?php

namespace app\services\trackings;

use app\services\TrackingService;

use app\models\Category;
use app\models\Collection;
use app\models\Connection;
use app\models\Number;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class MetricExtrasService {
    public static function header($req, $res) {
        $val = $req->val('data', [
            'header' => ['required'],
            'priority' => ['required', 'int'],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);



        $data = [];
        $data['type'] = 'header';
        $data['header'] = $val['header'];
        $data['color'] = 'primary';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = 0;
        $args['name'] = $val['header'];
        $args['status'] = 'active';
        $args['method'] = '';
        $args['check_interval'] = 'static';
        $args['priority'] = $val['priority'];
        //$args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        $collection = Collection::find($req->params['collection_id']);
        $collection->resort();

        $res->success('You have successfully added the item.', '/collection/view/' . $req->params['collection_id']);
    }

    public static function hideOutput($req, $res) {
        $val = $req->val('data', [
            'priority' => ['required', 'int'],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        $data = [];
        $data['type'] = 'hide';
        $data['color'] = 'primary';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = 0;
        $args['name'] = 'Hide Output';
        $args['status'] = 'active';
        $args['method'] = '';
        $args['check_interval'] = 'static';
        $args['priority'] = $val['priority'];
        //$args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        $collection = Collection::find($req->params['collection_id']);
        $collection->resort();

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }

    public static function newline($req, $res) {
        $val = $req->val('data', [
            'priority' => ['required', 'int'],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        $data = [];
        $data['type'] = 'newline';
        $data['color'] = 'primary';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = 0;
        $args['name'] = 'Newline';
        $args['status'] = 'active';
        $args['method'] = '';
        $args['check_interval'] = 'static';
        $args['priority'] = $val['priority'];
        //$args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        $collection = Collection::find($req->params['collection_id']);
        $collection->resort();

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }

}
