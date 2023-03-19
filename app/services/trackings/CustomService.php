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

class CustomService {
        public static function basicGet($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'url' => ['required'],
            'object' => ['required'],

            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
            'priority' => ['required', 'int'],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        // Basic connection
        $rest = new REST();
        $url = $val['url'];
        $body = $rest->get($url, [], 'array');
        $result = self::basicGetParse($body, $val['object']);
        if($result == -1) {
            $res->error('There was a problem accessing the data. Please confirm all the information is entered correctly. If you continue to have issues, please contact support.');
        }


        $data = [];
        $data['url'] = $val['url'];
        $data['object'] = $val['object'];
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = 0;
        //$args['connection_id'] = $connection->id;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\CustomService', 'basicGetUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['priority'] = $val['priority'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        $collection = Collection::find($req->params['collection_id']);
        $collection->resort();

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }
    public static function basicGetParse($body, $object) {
        $parts = explode('.', $object);
        $item = $body;
        // There is probably a better way to do this but it works for now.
        foreach($parts as $part) {
            if(isset($item[$part])) {
                $item = $item[$part];
            } else {
                $item = -1;
                break;
            }
        }
        return number_format($item);
    }
    public static function basicGetUpdate($tracking, $manual_result = null) {
        // Basic connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $rest = new REST();
            $url = $tracking->data['values']['url'];
            $body = $rest->get($url, [], 'array');
            $result = self::basicGetParse($body, $tracking->data['values']['object']);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }

}
