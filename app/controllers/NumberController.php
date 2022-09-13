<?php

namespace app\controllers;

use app\models\Category;
use app\models\Collection;
use app\models\Connection;
use app\models\Number;
use app\models\Restriction;
use app\models\Tracking;
use app\models\Username;

use app\services\ConnectionService;
use app\services\TrackingService;

use mavoc\core\Email;
use mavoc\core\Secret;

class NumberController {
    public function add($req, $res) {
        $collections = [];

        $list = Collection::where('user_id', $req->user_id);
        foreach($list as $item) {
            $label = $item->data['slug'];
            if($item->data['private']) {
                $label .= ' (Private)';
            } else {
                $label .= ' (Public)';
            }
            $collections[] = [
                'label' => $label,
                'value' => $item->data['id'],
            ];
        }

        $back = '/numbers';
        $res->view('numbers/add', compact('back', 'collections'));
    }
    public function addPost($req, $res) {
        $val = $req->val('data', [
            'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
        ]);

        $collection = Collection::find($val['collection_id']);
        $collection = ao()->hook('app_add_collection', $collection, $req, $res);

        $res->redirect('/number/add/' . $val['collection_id']);

    }

    public function addCategory($req, $res) {
        $list = Category::all('data');


        $restriction = Restriction::fullAccess($req->user_id, 'data');
        $restriction = ao()->hook('app_add_restriction', $restriction, $req, $res);

        $parts = explode('/', $req->path);
        array_pop($parts);
        $back = implode('/', $parts);
        $res->view('numbers/add-category', compact('back', 'list', 'restriction'));
    }

    public function addConnection($req, $res) {
        $val = $req->val('params', [
            'category_slug' => ['required', ['dbAccessList' => ['categories', 'slug', $req->user_id, 'user_ids']]],
            'number_slug' => ['required', ['dbAccessList' => ['numbers', 'slug', $req->user_id, 'user_ids']]],
        ], '/number/add');


        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        $category_slug = $category->data['slug'];
        $number_slug = $number->data['slug'];

        $restriction = Restriction::fullAccess($req->user_id, 'data');
        $restriction = ao()->hook('app_add_restriction', $restriction, $req, $res, $category, $number);

        $connections = Connection::where([
            'user_id' => $req->user_id,
            'category_id' => $category->id,
        ]);
        $radios = [];
        if(count($connections)) {
            foreach($connections as $connection) {
                $radios[] = [
                    'label' => $connection->data['values']['name'],
                    'value' => $connection->id,
                ];
            }

            if($number->data['needs_connection'] == 1) {
                $radios[] = [
                    'label' => 'Create a new connection below',
                    'value' => 0,
                ];
            }
        }

        $parts = explode('/', $req->path);
        array_pop($parts);
        $back = implode('/', $parts);
        //$res->view('numbers/' . $category_slug . '/' . $number_slug, compact('intervals', 'number'));
        $res->view('numbers/' . $category_slug . '/' . 'connection', compact('back', 'category', 'number', 'radios'));
    }
    public function addConnectionPost($req, $res) {
        // Make sure the current user owns the collection.
        $val = $req->val('params', [
            'category_slug' => ['required', ['dbAccessList' => ['categories', 'slug', $req->user_id, 'user_ids']]],
            'number_slug' => ['required', ['dbAccessList' => ['numbers', 'slug', $req->user_id, 'user_ids']]],
            'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
        ], '/number/add');

        $val = $req->val('data', [
            'connection_id' => ['required', 'int'],
        ]);

        if($val['connection_id'] != 0) {
            $res->redirect($req->path . '/' . $val['connection_id']);
        } else {
            ConnectionService::handle($req, $res);
        }
    }


    public function addNumber($req, $res) {
        $category = Category::by('slug', $req->params['category_slug']);
        $list = Number::where('category_id', $category->data['id'], 'data');

        $val = $req->val('params', [
            'category_slug' => ['required', ['dbAccessList' => ['categories', 'slug', $req->user_id, 'user_ids']]],
        ], '/number/add');

        $restriction = Restriction::fullAccess($req->user_id, 'data');
        $restriction = ao()->hook('app_add_restriction', $restriction, $req, $res, $category);

        $parts = explode('/', $req->path);
        array_pop($parts);
        $back = implode('/', $parts);
        $res->view('numbers/add-number', compact('back', 'list', 'restriction'));
    }

    public function addTracking($req, $res) {
        $val = $req->val('params', [
            'category_slug' => ['required', ['dbAccessList' => ['categories', 'slug', $req->user_id, 'user_ids']]],
            'number_slug' => ['required', ['dbAccessList' => ['numbers', 'slug', $req->user_id, 'user_ids']]],
        ], '/number/add');

        // Validate user owns connection if there is a connection
        $connection = null;
        if($req->params['connection_id'] != 0) {
            $val2 = $req->val('params', [
                'connection_id' => ['required', ['dbOwner' => ['connections', 'id', $req->user_id, 'user_id']]],
            ], '/number/add');

            $connection = Connection::find($val2['connection_id']);
        }


        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        // Make sure that if 0 is passed for the connection_id that the number doesn't require a connection_id
        if($req->params['connection_id'] == 0 && $number->data['needs_connection']) {
            $res->error('There was a problem accessing the connection information.');
        }

        $restriction = Restriction::fullAccess($req->user_id, 'data');
        $restriction = ao()->hook('app_add_restriction', $restriction, $req, $res, $category, $number);

        $category_slug = $category->data['slug'];
        $number_slug = $number->data['slug'];

        if(in_array($category_slug, ['lifespan', 'plausible'])) {
            $number_slug = 'main';
        }

        $available_intervals = ['1 hour' => true, '5 minutes' => true];
        $intervals = [];
        $list = explode(',', $restriction['allowed_intervals']);
        foreach($list as $item) {
            if($item == 'static') {
                continue;
            }
            $intervals[] = [
                'label' => $item,
                'name' => 'interval',
                'value' => $item,
            ];
            unset($available_intervals[$item]);
        }
        foreach($available_intervals as $item => $temp) {
            $intervals[] = [
                'label' => $item . ' (upgrade service plan to enable)',
                'name' => 'interval',
                'value' => $item,
                'extra' => 'disabled',
            ];
        }

        // Add any extra data or variables (like a list of options from an API).
        $extras_func = ['\app\services\extras\\' . classify($category_slug) . 'ExtraService', 'main'];
        if(is_callable($extras_func)) {
            $extras = call_user_func($extras_func, $req, $res, $category, $number, $connection);
        } else {
            $extras = [];
        }
        $extras = ao()->hook('app_add_tracking_extras', $extras, $req, $res);

        $parts = explode('/', $req->path);
        array_pop($parts);
        if(!$connection) {
            // There is no connectino /0 page so need to skip that one too.
            array_pop($parts);
        }
        $back = implode('/', $parts);
        $res->view('numbers/' . $category_slug . '/' . $number_slug, compact('back', 'extras', 'intervals', 'number'));
    }

    public function addTrackingPost($req, $res) {
        // Make sure the current user owns the collection.
        $val = $req->val('params', [
            'category_slug' => ['required', ['dbAccessList' => ['categories', 'slug', $req->user_id, 'user_ids']]],
            'number_slug' => ['required', ['dbAccessList' => ['numbers', 'slug', $req->user_id, 'user_ids']]],
            'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
        ], '/number/add');

        // Validate user owns connection if there is a connection
        if($req->params['connection_id'] != 0) {
            $val2 = $req->val('params', [
                'connection_id' => ['required', ['dbOwner' => ['connections', 'id', $req->user_id, 'user_id']]],
            ], '/number/add');
        }

        $collection = Collection::find($val['collection_id']);
        $collection = ao()->hook('app_add_collection', $collection, $req, $res);

        TrackingService::handle($req, $res);
    }

    public function list($req, $res) {
        $usernames = Username::where('user_id', $req->user->data['id']);

        // First time logging in, make sure they have a username
        if(count($usernames) == 0) {
            $res->redirect('/username/add');
        }

        $collections = Collection::where('user_id', $req->user_id);

        $list = Tracking::where('user_id', $req->user->data['id'], 'data');

        $res->view('numbers/list', compact('collections', 'list'));
    }

    public function delete($req, $res) {
        $val = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['trackings', 'id', $req->user_id]]],
        ]);

        Tracking::delete($val['id']);

        $res->success('Item successfully deleted.');

    }
}
