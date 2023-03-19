<?php

namespace app\controllers;

use app\models\Access;
use app\models\Follow;
use app\models\Collection;
use app\models\Number;
use app\models\Setting;
use app\models\Tracking;
use app\models\Username;

use mavoc\core\Email;

use DateTime;
use DateTimeZone;

class TrackingsController {
    public function list($req, $res) {
        if(isset($req->params['username']) && isset($req->params['collection'])) {
            $slug = '/' . $req->params['username'] . '/' . $req->params['collection'];
        } elseif(isset($req->params['username'])) {
            $slug = '/' . $req->params['username'];
        }

        $pass = true;
        $private = false;
        $collection = Collection::by('slug', $slug);

        // Check if collection exists.
        if(!$collection) {
            $pass = false;
        }

        // Check if collection is private and if user has access.
        if($pass && $collection->data['private']) {
            $private = true;
            if(!$collection->access($req->user_id)) {
                $pass = false;
            }
        }

        if(!$pass) {
            $logged_in = ao()->session->user_id;
            // If the collection is private and the user is logged out, direct them to login.
            if($private && !$logged_in) {
                $req->session->data['login_redirect'] = $req->uri;
                $res->redirect(ao()->env('APP_PUBLIC_HOME'));
            } else {
                $res->error('The collection you are trying to access does not appear to exist or you do not have access.', ao()->env('APP_PRIVATE_HOME'));
            }
        }

        $list = Tracking::where('collection_id', $collection->data['id']);

        $title = $collection->data['title'];

        $midnight = false;
        $collection_user_id = $collection->data['user_id'];
        $timezone = Setting::get($collection_user_id, 'timezone');
        $tz = new DateTimeZone($timezone);
        $dt = new DateTime('now', $tz);
        if($dt->format('H') == '00') {
            $midnight = true;
        }

        $res->view('trackings/list', compact('midnight', 'list', 'title'));
    }
}
