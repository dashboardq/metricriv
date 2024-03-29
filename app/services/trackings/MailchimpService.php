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

class MailchimpService {
    public static function listContactCount($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'list_id' => ['required'],

            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
            'priority' => ['required', 'int'],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);


        $connection = Connection::find($req->params['connection_id']);
        $api_key = $connection->data['values']['api_key'];

        $rest = new Rest([], 'user:' . $api_key);
        $dc = substr($api_key, strpos($api_key, '-') + 1); 
        $url = 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $val['list_id'] . '?include_total_contacts=true';
        $body = $rest->get($url);
        $result = self::listContactCountParse($body);


        $data = [];
        $data['list_id'] = $val['list_id'];
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
        $args['method'] = json_encode(['app\services\trackings\MailchimpService', 'listContactCountUpdate']);
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
    public static function listContactCountParse($body) {
        if(isset($body->stats->total_contacts)) {
            return number_format($body->stats->total_contacts);
        } else {
            return -1;
        }
    }
    public static function listContactCountUpdate($tracking, $manual_result = null) {
        // Advanced connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);
            $api_key = $connection->data['values']['api_key'];

            $rest = new Rest([], 'user:' . $api_key);
            $dc = substr($api_key, strpos($api_key, '-') + 1); 
            $url = 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $tracking->data['values']['list_id'] . '?include_total_contacts=true';
            $body = $rest->get($url);
            $result = self::listContactCountParse($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }

}
