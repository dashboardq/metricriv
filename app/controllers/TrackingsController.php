<?php

namespace app\controllers;

use app\models\Access;
use app\models\Follow;
use app\models\Collection;
use app\models\Number;
use app\models\Tracking;
use app\models\Username;

use mavoc\core\Email;

class TrackingsController {
    public function list($req, $res) {
        if(isset($req->params['username']) && isset($req->params['collection'])) {
            $slug = '/' . $req->params['username'] . '/' . $req->params['collection'];
        } elseif(isset($req->params['username'])) {
            $slug = '/' . $req->params['username'];
        }

        $pass = true;
        $collection = Collection::by('slug', $slug);

        // Check if collection exists.
        if(!$collection) {
            $pass = false;
        }

        // Check if collection is private and if user has access.
        if($pass && $collection->data['private']) {
            $access = Access::where(['user_id' => $req->user_id, 'collection_id' => $collection->data['id']]);
            if(count($access) == 0) {
                $pass = false;
            }
        }

        if(!$pass) {
            $res->error('The collection you are trying to access does not appear to exist or you do not have access.', ao()->env('APP_PRIVATE_HOME'));
        }

        $list = Tracking::where('collection_id', $collection->data['id']);

        $title = $collection->data['title'];

        //echo '<pre>'; print_r($list);die;

        $res->view('trackings/list', compact('list', 'title'));
    }
}
