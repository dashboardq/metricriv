<?php

namespace app\services\trackings;

use app\services\TrackingService;

use app\models\Category;
use app\models\Connection;
use app\models\Number;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class AirtableService {
        public static function rowCount($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'base_id' => ['required'],
            'table_name' => ['required'],
            'view_name' => ['required'],

            'name' => ['required'],
            'interval' => ['required', ['in' => [$intervals]]],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);


        $connection = Connection::find($req->params['connection_id']);

        $rest = new REST($connection->data['values']['api_key']);

        $args = [];
        $args['pageSize'] = 100;
        $args['view'] = $val['view_name'];
        // Spaces need to be "%20" instead of "+" so using rawurlencode
        $url = 'https://api.airtable.com/v0/' . rawurlencode($val['base_id']) . '/' . rawurlencode($val['table_name']);
        $url .= '?' . http_build_query($args);
        $body = $rest->get($url);
        if(isset($body->error->message)) {
            $res->error('Airtable Error: ' . $body->error->message);
        }
        // Just validate that it is working
        $result = self::rowCountParse($body, true);
        if($result == -1) {
            $res->error('There was a problem accessing the API. Please confirm that your API Key and other info were entered correctly. If you continue to have issues, please contact support.');
        }


        $data = [];
        $data['base_id'] = $val['base_id'];
        $data['table_name'] = $val['table_name'];
        $data['view_name'] = $val['view_name'];
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
        $args['method'] = json_encode(['app\services\trackings\AirtableService', 'rowCountUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        // Do not try to update for this one. The Airtable call could take a long time.
        // Only validating that the connection works for the initial add.
        //TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track. It could take a few minutes to start loading the data.', '/numbers');
    }
    public static function rowCountParse($body, $validate = false) {
        if($validate) {
            if(isset($body->records)) {
                return 1;
            } else {
                return -1;
            }
        } else {
            if(isset($body->records) && isset($body->offset)) {
                return [count($body->records), $body->offset];
            } elseif(isset($body->records) && !isset($body->offset)) {
                return [count($body->records), ''];
            } else {
                return [-1, ''];
            }
        }
    }
    public static function rowCountUpdate($tracking, $manual_result = null) {
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            $rest = new REST($connection->data['values']['api_key']);

            $offset = '';
            $total = 0;
            $call_count = 0;
            do {
                $call_count++;
                // Make sure only 5 calls are made per second.
                if($call_count % 5 == 0) {
                    sleep(1);
                }

                $args = [];
                $args['pageSize'] = 100;
                $args['view'] = $tracking->data['values']['view_name'];
                if($offset) {
                    $args['offset'] = $offset;
                }
                // Spaces need to be "%20" instead of "+" so using rawurlencode
                $url = 'https://api.airtable.com/v0/' . rawurlencode($tracking->data['values']['base_id']) . '/' . rawurlencode($tracking->data['values']['table_name']);
                $url .= '?' . http_build_query($args);
                $body = $rest->get($url);
                // Just validate that it is working
                list($count, $offset) = self::rowCountParse($body);
                if($count == -1) {
                    $total = -1;
                } else {
                    $total += $count;
                }
            } while($total != -1 && $call_count < 100 && $offset);

            $result = $total;
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }


}
