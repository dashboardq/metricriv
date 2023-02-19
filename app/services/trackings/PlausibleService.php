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

class PlausibleService {
    public static function main($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'site' => ['required'],
            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);
        $connection = Connection::find($req->params['connection_id']);

        // Check that the connection is valid
        $rest = new REST($connection->data['values']['api_key']);
        $future_date = (date('Y') + 2) . '-01-01';
        $url = 'https://plausible.io/api/v1/stats/aggregate?site_id=' . $val['site'] . '&period=custom&date=2000-01-01,' . $future_date . '&metrics=visitors,pageviews,bounce_rate,visit_duration';
        $result = $rest->get($url);
        if(isset($result->error)) {
            // The error does not branch out based on API Key or incorrect site. It is the same error returned by Plausible.
            $res->error('There was a problem accessing the Plausible API. Please confirm that your API Key and Site name were entered correctly. If you continue to have issues, please contact NumbersQ support.');
        } elseif(!isset($result->results->visitors->value)) {
            $res->error('There was a problem accessing the Plausible API. Please confirm that your API Key and Site name were entered correctly. If you continue to have issues, please contact NumbersQ support.');
        }


        $data = [];
        $data['site'] = $val['site'];
        $data['type'] = $number->data['slug'];
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = $connection->id;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\PlausibleService', 'update']);
        $args['check_interval'] = $val['interval'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }
    public static function update($tracking, $manual_result = null) {
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);
            $rest = new REST($connection->data['values']['api_key']);
            $future_date = (date('Y') + 2) . '-01-01';
            $url = 'https://plausible.io/api/v1/stats/aggregate?site_id=' . $tracking->data['values']['site'] . '&period=custom&date=2000-01-01,' . $future_date . '&metrics=visitors,pageviews,bounce_rate,visit_duration';
            $result = $rest->get($url);
        }

        if(isset($result->error)) {
            // Handle failures
            $tracking->failData();
        } else {
            $number = Number::find($tracking->data['number_id']);
            if($number->data['slug'] == 'total-visitors') {
                if(isset($result->results->visitors->value)) {
                    //$values = [];
                    //$values['number'] = $result->results->visitors->value;
                    //$values['color'] = 'black';
                    $tracking->updateData($result->results->visitors->value);
                } else {
                    $tracking->failData();
                }
            }
        }
    }
}
