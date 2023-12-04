<?php

namespace app\services\trackings\stripe;

use app\services\TrackingService;

use app\models\Category;
use app\models\Collection;
use app\models\Connection;
use app\models\Number;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class StripePayoutsService {
        public static function payouts($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'period' => ['required'],

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
        $result = self::callPayouts($connection, $collection->data['user_id'], $range, $ago);
        if($result == -1) {
            $res->error('There was a problem accessing the API. Please confirm that the API key is entered correctly. If you continue to have issues, please contact support.');
        }


        $data = [];
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
        $args['method'] = json_encode(['app\services\trackings\stripe\StripePayoutsService', 'payoutsUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['priority'] = $val['priority'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }

    public static function callPayouts($connection, $user_id, $range, $ago) {
        $dates = ao()->app->getDates($user_id, $range, $ago, 'U', $user_id);

        $rest = new REST($connection->data['values']['api_key']);
        $url = 'https://api.stripe.com/v1/payouts?limit=100&created[gte]=' . $dates['start'] . '&created[lte]=' . $dates['end'];
        $body = $rest->get($url);

        if(!isset($body->data)) {
            return -1;
        }

        $total = '';
        $total_cents = 0;
        $currency = 'usd';
        $last_id = '';
        foreach($body->data as $item) {
            if($item->currency == $currency) {
                $currency = $item->currency;
                $total_cents += $item->amount;
            }
            $last_id = $item->id;
        }

        while($last_id && $body->has_more) {
            $url = 'https://api.stripe.com/v1/payouts?limit=100&created[gte]=' . $dates['start'] . '&created[lte]=' . $dates['end'] . '&starting_after=' . $last_id;
            $body = $rest->get($url);

            $last_id = '';

            if(!isset($body->data)) {
                continue;
            }

            foreach($body->data as $item) {
                if($item->currency == $currency) {
                    $total_cents += $item->amount;
                }
                $last_id = $item->id;
            }
        }

        $total = number_format($total_cents / 100, 2);

        if($currency == 'usd') {
            $output = '$' . $total;
        } else {
            $output = $total;
        }

        return $output;
    }

    public static function payoutsUpdate($tracking, $manual_result = null) {
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);
            $result = self::callPayouts($connection, $tracking->data['collection']['user_id'], $tracking->data['values']['range'], $tracking->data['values']['ago']);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }


}
