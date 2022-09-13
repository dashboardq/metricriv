<?php

namespace app\services\trackings;

use app\services\TrackingService;

use app\models\Category;
use app\models\Connection;
use app\models\Number;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class IndiehackersService {
    public static function totalFollowers($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'username' => ['required'],

            'name' => ['required'],
            'interval' => ['required', ['in' => [$intervals]]],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);


        // Basic connection
        $rest = new REST([], '', false);
        $rest = ao()->hook('app_headless_rest', $rest);

        $args = [];
        $args['access_key'] = ao()->env('SCREENSHOTONE_API_KEY');
        $args['url'] = 'https://www.indiehackers.com/' . $val['username'];
        $args['format'] = 'html';
        $args['selector'] = '.user-stats__stat:nth-child(1) .user-stats__number';
        $args['format'] = 'html';
        $args['wait_until'] = 'networkidle0';
        $args['error_on_selector_not_found'] = 'true';
        $args = ao()->hook('app_headless_args', $args);
        $url = ao()->env('SCREENSHOTONE_URL') . '/take?' . http_build_query($args);
        $url = ao()->hook('app_headless_url', $url);
        $body = $rest->get($url, [], 'string');
        $body = ao()->hook('app_headless_body', $body);
        $result = self::totalFollowersParse($body);

        if($result == -1) {
            $res->error('There was a problem accessing the data. Please confirm all the information is entered correctly. If you continue to have issues, please contact support.');
        }


        $data = [];
        $data['username'] = $val['username'];
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
        $args['method'] = json_encode(['app\services\trackings\IndiehackersService', 'totalFollowersUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/numbers');
    }
    public static function totalFollowersParse($body) {
        $output = trim(strip_tags($body));
        if(is_numeric($output)) {
            // Remove any commas that may be there.
            $output = str_replace(',', '', $output);
            return number_format($output);
        } else {
            return -1;
        }
    }
    public static function totalFollowersUpdate($tracking, $manual_result = null) {
        // Basic connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            // Basic connection
            $rest = new REST([], '', false);
            $rest = ao()->hook('app_headless_rest', $rest);

            $args = [];
            $args['access_key'] = ao()->env('SCREENSHOTONE_API_KEY');
            $args['url'] = 'https://www.indiehackers.com/' . $tracking->data['values']['username'];
            $args['format'] = 'html';
            $args['selector'] = '.user-stats__stat:nth-child(1) .user-stats__number';
            $args['format'] = 'html';
            $args['wait_until'] = 'networkidle0';
            $args['error_on_selector_not_found'] = 'true';
            $args = ao()->hook('app_headless_args', $args);
            $url = ao()->env('SCREENSHOTONE_URL') . '/take?' . http_build_query($args);
            $url = ao()->hook('app_headless_url', $url);
            $body = $rest->get($url, [], 'string');
            $body = ao()->hook('app_headless_body', $body);
            $result = self::totalFollowersParse($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }

    public static function totalPoints($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'username' => ['required'],

            'name' => ['required'],
            'interval' => ['required', ['in' => [$intervals]]],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        // Basic connection
        $rest = new REST([], '', false);
        $rest = ao()->hook('app_headless_rest', $rest);

        $args = [];
        $args['access_key'] = ao()->env('SCREENSHOTONE_API_KEY');
        $args['url'] = 'https://www.indiehackers.com/' . $val['username'];
        $args['format'] = 'html';
        $args['selector'] = '.user-stats__stat:nth-child(2) .user-stats__number';
        $args['format'] = 'html';
        $args['wait_until'] = 'networkidle0';
        $args['error_on_selector_not_found'] = 'true';
        $args = ao()->hook('app_headless_args', $args);
        $url = ao()->env('SCREENSHOTONE_URL') . '/take?' . http_build_query($args);
        $url = ao()->hook('app_headless_url', $url);
        $body = $rest->get($url, [], 'string');
        $body = ao()->hook('app_headless_body', $body);
        $result = self::totalPointsParse($body);

        if($result == -1) {
            $res->error('There was a problem accessing the data. Please confirm all the information is entered correctly. If you continue to have issues, please contact support.');
        }


        $data = [];
        $data['username'] = $val['username'];
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
        $args['method'] = json_encode(['app\services\trackings\IndiehackersService', 'totalPointsUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/numbers');
    }
    public static function totalPointsParse($body) {
        $output = trim(strip_tags($body));
        if(is_numeric($output)) {
            // Remove any commas that may be there.
            $output = str_replace(',', '', $output);
            return number_format($output);
        } else {
            return -1;
        }
    }
    public static function totalPointsUpdate($tracking, $manual_result = null) {
        // Basic connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            // Basic connection
            $rest = new REST([], '', false);
            $rest = ao()->hook('app_headless_rest', $rest);

            $args = [];
            $args['access_key'] = ao()->env('SCREENSHOTONE_API_KEY');
            $args['url'] = 'https://www.indiehackers.com/' . $tracking->data['values']['username'];
            $args['format'] = 'html';
            $args['selector'] = '.user-stats__stat:nth-child(2) .user-stats__number';
            $args['format'] = 'html';
            $args['wait_until'] = 'networkidle0';
            $args['error_on_selector_not_found'] = 'true';
            $args = ao()->hook('app_headless_args', $args);
            $url = ao()->env('SCREENSHOTONE_URL') . '/take?' . http_build_query($args);
            $url = ao()->hook('app_headless_url', $url);
            $body = $rest->get($url, [], 'string');
            $body = ao()->hook('app_headless_body', $body);
            $result = self::totalFollowersParse($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }

}
