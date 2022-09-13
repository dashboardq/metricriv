<?php

namespace app\controllers;

use app\models\Access;
use app\models\Follow;
use app\models\Collection;
use app\models\Number;
use app\models\Tracking;
use app\models\Username;

use app\services\ReservedService;

use mavoc\core\Email;
use mavoc\core\Secret;

class UsernameController {
    public function list($req, $res) {
        $list = Username::where('user_id', $req->user->data['id']);

        $res->view('usernames/list', compact('list'));
    }

    public function add($req, $res) {
        $res->view('usernames/add');
    }

    public function create($req, $res) {
        $val = $req->val($req->data, [
            'name' => ['required', ['match' => '/^[A-Za-z0-9_]+$/'], ['notIn' => [ReservedService::usernames()]], ['dbUnique' => 'usernames']],
        ]);


        $val['name'] = ao()->hook('app_username_create_username', $val['name']);
        $val['user_id'] = ao()->hook('app_username_create_user_id', $req->user_id);

        // If this is the first username created, mark it as the primary name.
        $usernames = Username::where('user_id', $req->user_id);
        if(count($usernames) == 0) {
            $val['primary'] = 1;
        }

        $username = Username::create($val);

        // Add two collections
        $args = [];
        $args['user_id'] = $req->user_id;
        $args['username_id'] = $username->data['id'];
        $args['name'] = 'Public';
        $args['slug'] = '/' . $username->data['name'];
        $collection_public = Collection::create($args);

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['username_id'] = $username->data['id'];
        $args['name'] = 'Private';
        $args['slug'] = '/' . $username->data['name'] . '/private';
        $args['private'] = 1;
        $collection_private = Collection::create($args);


        // Add follow to both collections
        $args = [];
        $args['user_id'] = $req->user_id;
        $args['collection_id'] = $collection_public->data['id'];
        $args['priority'] = 20;
        $follow_public = Follow::create($args);

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['collection_id'] = $collection_private->data['id'];
        $args['priority'] = 10;
        $follow_private = Follow::create($args);

        // Add access to collections (really only need private but adding both for now)
        $args = [];
        $args['user_id'] = $req->user_id;
        $args['collection_id'] = $collection_public->data['id'];
        $access_public = Access::create($args);

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['collection_id'] = $collection_private->data['id'];
        $access_private = Access::create($args);

        $trackings = Tracking::where('user_id', $req->user_id);
        if(count($trackings) == 0) {
            // Add tracking for each collection
            $number_id_1 = rand(1, 23);
            do {
                $number_id_2 = rand(1, 23);
            } while($number_id_2 == $number_id_1);
            do {
                $number_id_3 = rand(1, 23);
            } while($number_id_3 == $number_id_2 || $number_id_3 == $number_id_1);

            $number_1 = Number::find($number_id_1);
            $number_2 = Number::find($number_id_2);
            $number_3 = Number::find($number_id_3);

            $args = [];
            $args['user_id'] = $req->user_id;
            $args['number_id'] = $number_1->data['id'];
            $args['collection_id'] = $collection_public->data['id'];
            $args['name'] = $number_1->data['short_name'];
            //$args['data'] = $number_1->data['data'];
            $args['data'] = ['number' => $number_1->data['data']];
            $args['encrypted'] = 0;
            $args['status'] = 'active';
            $args['check_interval'] = 'static';
            $tracking_1 = Tracking::create($args);


            $args = [];
            $args['user_id'] = $req->user_id;
            $args['number_id'] = $number_2->data['id'];
            $args['collection_id'] = $collection_public->data['id'];
            $args['name'] = $number_2->data['short_name'];
            //$args['data'] = $number_2->data['data'];
            $args['data'] = ['number' => $number_2->data['data']];
            $args['encrypted'] = 0;
            $args['status'] = 'active';
            $args['check_interval'] = 'static';
            $tracking_2 = Tracking::create($args);

            $args = [];
            $args['user_id'] = $req->user_id;
            $args['number_id'] = $number_3->data['id'];
            $args['collection_id'] = $collection_private->data['id'];
            $args['name'] = $number_3->data['short_name'];
            //$args['data'] = $number_3->data['data'];
            $args['data'] = ['number' => $number_3->data['data']];
            $args['encrypted'] = 0;
            $args['status'] = 'active';
            $args['check_interval'] = 'static';
            $tracking_3 = Tracking::create($args);
        }

        $res->success('Your username has been created. Below are the numbers that are currently being tracked. You can add a new number by pressing the "Add Number" button at the bottom of the list.', '/numbers');
    }
}
