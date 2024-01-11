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

class MetricRivCalculationsService {
    public static function fixed($req, $res) {
        $val = $req->val('data', [
            'name' => ['required'],
            'value' => ['required'],
            'priority' => ['required', 'int'],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        $data = [];
        $data['header'] = $val['header'];
        $data['number'] = $val['value'];
        $data['color'] = 'primary';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = 0;
        $args['name'] = $val['name'];
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
