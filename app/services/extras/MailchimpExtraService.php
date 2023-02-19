<?php

namespace app\services\extras;

use app\models\Category;
use app\models\Connection;
use app\models\Number;
use app\models\Restriction;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

class MailchimpExtraService {
    public static function main($req, $res, $category, $number, $connection) {
        $extras = [];
        $radios = [];

        $api_key = $connection->data['values']['api_key'];

        $rest = new Rest([], 'user:' . $api_key);
        $dc = substr($api_key, strpos($api_key, '-') + 1); 
        $url = 'https://' . $dc . '.api.mailchimp.com/3.0/lists';
        $body = $rest->get($url);

        if(!isset($body->lists) || count($body->lists) == 0) {
            $res->error('The API Key that was entered does not have any associated Mailchimp lists. If you have not created any lists, you will need to create a list on Mailchimp. If a list exists and the problems continues, please contact support.', ao()->env('APP_PRIVATE_HOME'));
        }

        foreach($body->lists as $list) {
            $radios[] = [
                'label' => $list->name,
                'value' => $list->id,
            ];
        }

        $extras['radios'] = $radios;
        return $extras;
    }
}
