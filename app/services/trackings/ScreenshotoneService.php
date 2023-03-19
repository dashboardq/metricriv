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

class ScreenshotoneService {
    public static function allottedScreenshots($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
            'priority' => ['required', 'int'],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        // Advanced connection
        $connection = Connection::find($req->params['connection_id']);

        $rest = new REST();
        $url = 'https://api.screenshotone.com/usage?access_key=' . urlencode($connection->data['values']['api_key']);
        $body = $rest->get($url);
        $result = self::allottedScreenshotsParse($body);
        if($result == -1) {
            $res->error('There was a problem accessing the API. Please confirm that your API Key and other info were entered correctly. If you continue to have issues, please contact support.');
        }


        $data = [];
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        //$args['connection_id'] = 0;
        $args['connection_id'] = $connection->id;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\ScreenshotoneService', 'allottedScreenshotsUpdate']);
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
    public static function allottedScreenshotsParse($body) {
        if(isset($body->total)) {
            return number_format($body->total);
        } else {
            return -1;
        }
    }
    public static function allottedScreenshotsUpdate($tracking, $manual_result = null) {
        // Advanced connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            $rest = new REST();
            $url = 'https://api.screenshotone.com/usage?access_key=' . urlencode($connection->data['values']['api_key']);
            $body = $rest->get($url);
            $result = self::allottedScreenshotsParse($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }

    public static function availableScreenshots($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
            'priority' => ['required', 'int'],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        // Advanced connection
        $connection = Connection::find($req->params['connection_id']);

        $rest = new REST();
        $url = 'https://api.screenshotone.com/usage?access_key=' . urlencode($connection->data['values']['api_key']);
        $body = $rest->get($url);
        $result = self::availableScreenshotsParse($body);
        if($result == -1) {
            $res->error('There was a problem accessing the API. Please confirm that your API Key and other info were entered correctly. If you continue to have issues, please contact support.');
        }


        $data = [];
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        //$args['connection_id'] = 0;
        $args['connection_id'] = $connection->id;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\ScreenshotoneService', 'availableScreenshotsUpdate']);
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
    public static function availableScreenshotsParse($body) {
        if(isset($body->available)) {
            return number_format($body->available);
        } else {
            return -1;
        }
    }
    public static function availableScreenshotsUpdate($tracking, $manual_result = null) {
        // Advanced connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            $rest = new REST();
            $url = 'https://api.screenshotone.com/usage?access_key=' . urlencode($connection->data['values']['api_key']);
            $body = $rest->get($url);
            $result = self::availableScreenshotsParse($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }

    public static function usedScreenshots($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
            'priority' => ['required', 'int'],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);


        // Advanced connection
        $connection = Connection::find($req->params['connection_id']);

        $rest = new REST();
        $url = 'https://api.screenshotone.com/usage?access_key=' . urlencode($connection->data['values']['api_key']);
        $body = $rest->get($url);
        $result = self::usedScreenshotsParse($body);
        if($result == -1) {
            $res->error('There was a problem accessing the API. Please confirm that your API Key and other info were entered correctly. If you continue to have issues, please contact support.');
        }


        $data = [];
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        //$args['connection_id'] = 0;
        $args['connection_id'] = $connection->id;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\ScreenshotoneService', 'usedScreenshotsUpdate']);
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
    public static function usedScreenshotsParse($body) {
        if(isset($body->used)) {
            return number_format($body->used);
        } else {
            return -1;
        }
        if(isset($body->count)) {
            return number_format($body->count);
        } else {
            return -1;
        }
    }
    public static function usedScreenshotsUpdate($tracking, $manual_result = null) {
        // Advanced connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            $rest = new REST();
            $url = 'https://api.screenshotone.com/usage?access_key=' . urlencode($connection->data['values']['api_key']);
            $body = $rest->get($url);
            $result = self::usedScreenshotsParse($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }

}
