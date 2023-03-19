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

class TwitterService {
    public static function totalFollowers($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'username' => ['required'],

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
        $rest = new REST([], '', false);
        $url = 'https://nitter.net/' . $val['username'];
        $body = $rest->get($url, [], 'string');
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
        $args['method'] = json_encode(['app\services\trackings\TwitterService', 'totalFollowersUpdate']);
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
    public static function totalFollowersParse($body) {
        preg_match_all('|.*"followers".*?"profile-stat-num">([^<]*)</span>.*|s', $body, $matches);
        if(isset($matches[1][0])) {
            return num($matches[1][0]);
        } else {
            return -1;
        }

    }
    public static function totalFollowersUpdate($tracking, $manual_result = null) {
        // Basic connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $rest = new REST([], '', false);
            $url = 'https://nitter.net/' . $tracking->data['values']['username'];
            $body = $rest->get($url, [], 'string');
            $result = self::totalFollowersParse($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }

    public static function totalTweets($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'username' => ['required'],

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
        $rest = new REST([], '', false);
        $url = 'https://nitter.net/' . $val['username'];
        $body = $rest->get($url, [], 'string');
        $result = self::totalTweetsParse($body);


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
        $args['method'] = json_encode(['app\services\trackings\TwitterService', 'totalTweetsUpdate']);
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
    public static function totalTweetsParse($body) {
        preg_match_all('|.*"posts".*?"profile-stat-num">([^<]*)</span>.*|s', $body, $matches);
        if(isset($matches[1][0])) {
            return num($matches[1][0]);
        } else {
            return -1;
        }

    }
    public static function totalTweetsUpdate($tracking, $manual_result = null) {
        // Basic connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $rest = new REST([], '', false);
            $url = 'https://nitter.net/' . $tracking->data['values']['username'];
            $body = $rest->get($url, [], 'string');
            $result = self::totalTweetsParse($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }


}
