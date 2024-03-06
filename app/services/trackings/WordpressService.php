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

class WordpressService {
    public static function users($req, $res) {
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
        $type = 'wp_users';
        $connection = Connection::find($req->params['connection_id']);
        $collection = Collection::find($req->params['collection_id']);

        $api_key = $connection->data['values']['api_key'];
        $url = WordpressService::parseURL($collection->data['user_id'], $api_key, $type, $range, $ago);
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
        $args['method'] = json_encode(['app\services\trackings\WordpressService', 'usersUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['priority'] = $val['priority'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        $collection->resort();

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
    public static function usersUpdate($tracking, $manual_result = null) {
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            $api_key = $connection->data['values']['api_key'];
            // Use the collection user owner's timezone
            $url = WordpressService::parseURL($tracking->data['collection']['user_id'], $api_key, $tracking->data['values']['type'], $tracking->data['values']['range'], $tracking->data['values']['ago']);

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

    // key_1234_1.0.0_https_www.example.com_wp-admin.admin-ajax_abcdefghijklmnop_end
    public static function parseURL($user_id, $api_key, $type, $range, $ago) {
        $parts = explode('_', $api_key);

        // Check if there are extra underscores.
        if(count($parts) > 8) {
            $scheme = $parts[3];
            $host = $parts[4];
            $temp = [];
            $i = 5;
            while($i < (count($parts) - 2)) {
                $temp[] = $parts[$i];
                $i++;
            }
            $path = implode('_', $temp);
            $path = str_replace($path);
        } elseif(count($parts) == 8) {
            $scheme = $parts[3];
            $host = $parts[4];
            $path = str_replace('.', '/', $parts[5]);
        } else {
            return false;
        }


        $url = $scheme . '://' . $host . '/' . $path . '.php';

        $url .= '?action=' . 'metricriv_data';
        $url .= '&key=' . urlencode($api_key);
        $url .= '&type=' . urlencode($type);
        if($range != 'all') {
            $dates = ao()->app->getDates($user_id, $range, $ago);
            $url .= '&range=' . 'custom';
            $url .= '&start=' . urlencode($dates['start']);
            $url .= '&end=' . urlencode($dates['end']);
        } else {
            $url .= '&range=' . 'all';
            $url .= '&start=';
            $url .= '&end=';
        }

        $url = ao()->hook('app_wp_parse_url', $url, $user_id);

        return $url;
    }


}
