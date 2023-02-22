<?php

namespace app\models;

use mavoc\core\Model;

class Restriction extends Model {
    public static $table = 'restrictions';

    public static function fullAccess($user_id, $return_type = 'all') {
        $args = [];
        $args['user_id'] = $user_id;
        $args['public_max'] = 1000;
        $args['private_max'] = 1000;
        $args['username_max'] = 1;
        $args['allowed_intervals'] = 'static,1 hour,5 minutes';
        $args['additional_users'] = 1000;
        $args['premium_level'] = 30;
        $restriction = new Restriction($args);

        if($return_type == 'data') {
            return $restriction->data;
        } else {
            return $restriction;
        }
    }
}
