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

class GithubService {
    public static function notifications($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'name' => ['required'],
            'interval' => ['required', ['in' => [$intervals]]],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);
        $connection = Connection::find($req->params['connection_id']);

        // Check that the connection is valid
        $headers = [
            'Authorization: token ' . $connection->data['values']['access']['access_token'],
            'User-Agent: ' . ao()->env('GITHUB_USER_AGENT'),
        ];
        $rest = new REST($headers);
        $url = 'https://api.github.com/notifications?per_page=1';
        list($headers, $result) = $rest->get($url, [], 'headers,array');
        if(isset($result->error)) {
            $res->error('There was a problem accessing the API. If you continue to have issues, please contact NumbersQ support.');
        }

        $total = self::parseTotal($headers['link'] ?? '', $result);

        $data = [];
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
        $args['method'] = json_encode(['app\services\trackings\GithubService', 'notificationsUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        TrackingService::update($tracking->id, $total);

        $res->success('You have successfully added a new number to track!', '/numbers');
    }
    public static function notificationsUpdate($tracking, $manual_result = null) {
        if($manual_result) {
            $total = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            $headers = [
                'Authorization: token ' . $connection->data['values']['access']['access_token'],
                'User-Agent: ' . ao()->env('GITHUB_USER_AGENT'),
            ];
            $rest = new REST($headers);
            $url = 'https://api.github.com/notifications?per_page=1';
            list($headers, $result) = $rest->get($url, [], 'headers,array');
            if(isset($result->error)) {
                $total = -1;
            } else {
                $total = self::parseTotal($headers['link'] ?? '', $result);
            }
        }

        // TODO: Handle Error
        if(false) {
            // Handle failures
            $tracking->failData();
        } else {
            $tracking->updateData($total);
        }
    }

    // Use per_page=1 and the last page header response to find out how many items there are.
    // https://stackoverflow.com/questions/30636798/get-user-total-starred-count-using-github-api-v3
    // Need the list in order to differentiate 0 & 1 (where the links are empty)
    public static function parseTotal($links, $list) {
        $total = 0;
        if(!$links) {
            $total = count($list);
        } else {
            preg_match('/.*rel="next".*per_page=1&page=(\d+)>; rel="last".*/', $links, $matches);
            if($matches[1]) {
                $total = $matches[1];
            }
        }

        return $total;
    }
}
