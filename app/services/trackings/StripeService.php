<?php

namespace app\services\trackings;

use app\services\TrackingService;

use app\models\Category;
use app\models\Connection;
use app\models\Number;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class StripeService {
        public static function monthlyRevenue($req, $res) {
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

        // Advanced connection
        $connection = Connection::find($req->params['connection_id']);

        // Check that the connection is valid
        $created = new DateTime();
        $created->modify('-28 days');

        $rest = new REST($connection->data['values']['api_key']);
        $url = 'https://api.stripe.com/v1/payouts?limit=100&created[gte]=' . $created->format('U');
        $body = $rest->get($url);
        if(!isset($body->data)) {
            $res->error('There was a problem accessing the API. Please confirm that your API Key and other info were entered correctly. If you continue to have issues, please contact support.');
        }

        $result = self::monthlyRevenueParse($body);


        $data = [];
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = $connection->id;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\StripeService', 'monthlyRevenueUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }
    public static function monthlyRevenueParse($body) {
        $total = '';
        $total_cents = 0;
        $currency = 'usd';
        foreach($body->data as $item) {
            if($item->currency == 'usd') {
                $currency = $item->currency;
                $total_cents += $item->amount;
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
    public static function monthlyRevenueUpdate($tracking, $manual_result = null) {
        // Advanced connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            $created = new DateTime();
            $created->modify('-28 days');

            $rest = new REST($connection->data['values']['api_key']);
            $url = 'https://api.stripe.com/v1/payouts?limit=100&created[gte]=' . $created->format('U');
            $body = $rest->get($url);
            if(!isset($body->data)) {
                $res->error('There was a problem accessing the API. Please confirm that your API Key and other info were entered correctly. If you continue to have issues, please contact support.');
            }

            $result = self::monthlyRevenueParse($body);

        }

        if(isset($result->error)) {
            // Handle failures
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }

    }


}
