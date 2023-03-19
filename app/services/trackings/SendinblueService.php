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

class SendinblueService {
    
    public static function totalContacts($req, $res) {
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

        // Check that the connection is valid
        // Non-typical header
        $headers = [
            'api-key: ' . $connection->data['values']['api_key'],
        ];
        $rest = new REST($headers);
        $url = 'https://api.sendinblue.com/v3/contacts?limit=50&offset=0&sort=desc';
        $body = $rest->get($url);
        $result = self::totalContactsParse($body);
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
        $args['method'] = json_encode(['app\services\trackings\SendinblueService', 'totalContactsUpdate']);
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
    public static function totalContactsParse($body) {
        if(isset($body->count)) {
            return number_format($body->count);
        } else {
            return -1;
        }
    }
    public static function totalContactsUpdate($tracking, $manual_result = null) {
        // Advanced connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            $headers = [
                'api-key: ' . $connection->data['values']['api_key'],
            ];
            $rest = new REST($headers);
            $url = 'https://api.sendinblue.com/v3/contacts?limit=50&offset=0&sort=desc';
            $body = $rest->get($url);
            $result = self::totalContactsParse($body);
        }

        if($result == -1) {
            // Handle failures
            $tracking->failData();
        } else {
            //$values = [];
            //$values['number'] = $result->results->visitors->value;
            //$values['color'] = 'black';
            $tracking->updateData($result);
        }
    }


}
