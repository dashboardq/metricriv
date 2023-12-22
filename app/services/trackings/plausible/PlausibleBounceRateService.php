<?php

namespace app\services\trackings\plausible;

use app\services\TrackingService;

use app\models\Category;
use app\models\Collection;
use app\models\Connection;
use app\models\Number;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class PlausibleBounceRateService {
        public static function bounceRate($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'period' => ['required'],

            'site' => ['required'],
            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
            'priority' => ['required', 'int'],
        ]);

        // If other, the other values are required
        if($val['period'] == 'other') {
            $other = $req->val('data', [
                'years_ago' => ['required', 'int'],
                'months_ago' => ['required', 'int'],
                'weeks_ago' => ['required', 'int'],
                'days_ago' => ['required', 'int'],

                'years_range' => ['required', 'int'],
                'months_range' => ['required', 'int'],
                'weeks_range' => ['required', 'int'],
                'days_range' => ['required', 'int'],
            ]);
        }

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        $range = 'all';
        $ago = 'now';
        if($val['period'] == 'other') {
            $range = '';
            $range .= $other['years_range'] . 'y';
            $range .= $other['months_range'] . 'm';
            $range .= $other['weeks_range'] . 'w';
            $range .= $other['days_range'] . 'd';

            $ago = '';
            $ago .= $other['years_ago'] . 'y';
            $ago .= $other['months_ago'] . 'm';
            $ago .= $other['weeks_ago'] . 'w';
            $ago .= $other['days_ago'] . 'd';
        } elseif(strpos($val['period'], '_') !== false) {
            $parts = explode('_', $val['period']);
            if(count($parts) == 2) {
                $range = $parts[0];
                $ago = $parts[1];
            }
        }

        // Advanced connection
        $connection = Connection::find($req->params['connection_id']);
        $collection = Collection::find($req->params['collection_id']);
        $result = self::callBounceRate($val['site'], $connection, $collection->data['user_id'], $range, $ago);
        if($result == -1) {
            $res->error('There was a problem accessing the API. Please confirm that the API key and site domain is entered correctly. If you continue to have issues, please contact support.');
        }


        $data = [];
        $data['site'] = $val['site'];
        $data['range'] = $range;
        $data['ago'] = $ago;
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = $connection->id;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\plausible\PlausibleBounceRateService', 'bounceRateUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['priority'] = $val['priority'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }

    public static function callBounceRate($site, $connection, $user_id, $range, $ago) {
        $dates = ao()->app->getDates($user_id, $range, $ago, 'Y-m-d', $user_id);

        $rest = new REST($connection->data['values']['api_key']);
        // Dates are inclusive:
        // https://plausible.io/docs/stats-api#time-periods
        //$url = 'https://plausible.io/api/v1/stats/aggregate?site_id=' . urlencode($site) . '&period=custom&date=' . $dates['start'] . ',' . $dates['end'] . '&metrics=visitors,pageviews,bounce_rate,visit_duration';
        $url = 'https://plausible.io/api/v1/stats/aggregate?site_id=' . urlencode($site) . '&period=custom&date=' . $dates['start'] . ',' . $dates['end'] . '&metrics=bounce_rate';
        $result = $rest->get($url);

        if(isset($result->error)) {
            // The error does not branch out based on API Key or incorrect site. It is the same error returned by Plausible.
             return -1;
        } elseif(!isset($result->results->bounce_rate->value)) {
             return -1;
        }

        $output = $result->results->bounce_rate->value;

        return $output;
    }

    public static function bounceRateUpdate($tracking, $manual_result = null) {
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);
            $result = self::callBounceRate($tracking->data['values']['site'], $connection, $tracking->data['collection']['user_id'], $tracking->data['values']['range'], $tracking->data['values']['ago']);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }


}
