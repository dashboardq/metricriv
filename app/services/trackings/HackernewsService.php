<?php

namespace app\services\trackings;

use app\services\TrackingService;

use app\models\Category;
use app\models\Connection;
use app\models\Number;
use app\models\Restriction;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class HackernewsService {
    public static function totalKarma($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'username' => ['required'],
            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        // Check that the connection is valid
        $rest = new REST();
        $url = 'https://hn.algolia.com/api/v1/users/' . $val['username'];
        $result = $rest->get($url);

        if(!isset($result->karma)) {
            $res->error('There was a problem accessing the data. Please confirm all the information is entered correctly. If you continue to have issues, please contact NumbersQ support.');
        }

        $data = [];
        $data['username'] = $val['username'];
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        //$args['connection_id'] = $connection->id;
        $args['connection_id'] = 0;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\HackernewsService', 'totalKarmaUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }
    public static function totalKarmaUpdate($tracking, $manual_result = null) {
        if($manual_result) {
            $result = $manual_result;
        } else {
            $rest = new REST();
            $url = 'https://hn.algolia.com/api/v1/users/' . $tracking->data['values']['username'];
            $result = $rest->get($url);
        }

        if(!isset($result->karma)) {
            $tracking->failData();
        } else {
            $tracking->updateData($result->karma);
        }
    }
}
