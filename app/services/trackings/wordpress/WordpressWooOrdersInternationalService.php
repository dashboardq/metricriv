<?php

namespace app\services\trackings\wordpress;

use app\services\TrackingService;
use app\services\trackings\WordpressService;

use app\models\Category;
use app\models\Connection;
use app\models\Number;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class WordpressWooOrdersInternationalService {
        public static function wooOrdersInternational($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'period' => ['required'],

            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
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
        $type = 'woo_orders_international';
        $connection = Connection::find($req->params['connection_id']);

        $api_key = $connection->data['values']['api_key'];
        $url = WordpressService::parseURL(ao()->request->user_id, $api_key, $type, $range, $ago);
        if($url === false) {
            $res->error('There appears to be a problem with the API key. Please confirm that the API key is entered correctly. If you continue to have issues, please contact support.');
        }

        $rest = new REST();
        $body = $rest->get($url);
        $result = self::parseBody($body);
        if($result == -1) {
            $res->error('There was a problem accessing the API. Please confirm that your API Key and other info were entered correctly. If you continue to have issues, please contact support.');
        }

        $data = [];
        $data['type'] = $type;
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
        $args['method'] = json_encode(['app\services\trackings\wordpress\WordpressWooOrdersInternationalService', 'wooOrdersInternationalUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }
    public static function parseBody($body) {
        if(isset($body->value)) {
            return number_format($body->value);
        } else {
            return -1;
        }
    }
    public static function wooOrdersInternationalUpdate($tracking, $manual_result = null) {
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            $api_key = $connection->data['values']['api_key'];
            $url = WordpressService::parseURL($tracking->data['user_id'], $api_key, $tracking->data['values']['type'], $tracking->data['values']['range'], $tracking->data['values']['ago']);

            $rest = new REST();
            $body = $rest->get($url);
            $result = self::parseBody($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }


}
