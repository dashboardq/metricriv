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

        // Old method of getting collections that the user owns.
        //$list = Collection::where('user_id', $req->user_id);

        // Get collections that the user owns or can edit.
        $owns = Collection::owns($req->user_id);
        $edits = Collection::edits($req->user_id);
        $list = array_merge($owns, $edits);
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

        $back = '/collections';
        $res->view('numbers/add', compact('back', 'collections'));
    }
    public function addPost($req, $res) {
        $val = $req->val('data', [
            //'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
            'collection_id' => ['required', 'dbEditorCollection'],
        ]);

        $collection = Collection::find($val['collection_id']);
        $collection = ao()->hook('app_add_collection', $collection, $req, $res);

        $res->redirect('/number/add/' . $val['collection_id']);

    }

    public function addCategory($req, $res) {
        $val = $req->val('params', [
            //'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
            'collection_id' => ['required', 'dbEditorCollection'],
        ], '/number/add');

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
            //'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
            'collection_id' => ['required', 'dbEditorCollection'],
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
            //'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
            'collection_id' => ['required', 'dbEditorCollection'],
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
        $list = Number::where(['category_id' => $category->data['id'], 'active' => 1], 'data');

        $val = $req->val('params', [
            'category_slug' => ['required', ['dbAccessList' => ['categories', 'slug', $req->user_id, 'user_ids']]],
            //'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
            'collection_id' => ['required', 'dbEditorCollection'],
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
            //'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
            'collection_id' => ['required', 'dbEditorCollection'],
        ], '/number/add');

        // Validate user owns connection if there is a connection
        $connection = null;
        if($req->params['connection_id'] != 0) {
            // I've debated back and forth on whether or not other editors should have access to a connection.
            // Or if the owner of a collection should have access to the editor's connection.
            // Ultimately I think each user should have their own connections that only they can access.
            // If an owner wants to give access to a specific number to an editor or viewer, that editor
            // or viewer should not be able to access all the restricted data from that connection.
            // This should work vice versa too. An editor could give access to an owner for a specific number
            // but may not want the owner to have full access to a connection.
            // Need to hide the copy button when a user does not have access to the connection.
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


        $name = 'period';
        $other = false;
        if(isset($res->session->flash['fields'][$name])) {
            $other = ($res->session->flash['fields'][$name] == 'other');
        } elseif(isset($res->fields[$name])) {
            $other = ($res->fields[$name] == 'other');
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

        $res->view('numbers/' . $category_slug . '/' . $number_slug, compact('back', 'extras', 'intervals', 'number', 'other'));
    }

    public function addTrackingPost($req, $res) {
        // Make sure the current user owns the collection.
        $val = $req->val('params', [
            'category_slug' => ['required', ['dbAccessList' => ['categories', 'slug', $req->user_id, 'user_ids']]],
            'number_slug' => ['required', ['dbAccessList' => ['numbers', 'slug', $req->user_id, 'user_ids']]],
            //'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
            'collection_id' => ['required', 'dbEditorCollection'],
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

    public function edit($req, $res) {
        $val = $req->val('params', [
            //'id' => ['required', ['dbOwner' => ['trackings', 'id', $req->user_id]]],
            'id' => ['required', 'dbEditorTracking'],
        ]);

        $tracking = Tracking::find($req->params['id']);

        $res->fields['name'] = $tracking->data['title_raw'];
        $res->fields['check_interval'] = $tracking->data['check_interval'];
        $res->fields['target_interval'] = $tracking->data['target_interval'];

        $restriction = Restriction::fullAccess($req->user_id, 'data');
        $restriction = ao()->hook('app_add_restriction', $restriction, $req, $res);

        $available_intervals = ['1 hour' => true, '5 minutes' => true];
        $checks = [];
        $list = explode(',', $restriction['allowed_intervals']);
        foreach($list as $item) {
            if($item == 'static') {
                continue;
            }
            $checks[] = [
                'label' => $item,
                'name' => 'check_interval',
                'value' => $item,
            ];
            unset($available_intervals[$item]);
        }
        foreach($available_intervals as $item => $temp) {
            $checks[] = [
                'label' => $item . ' (upgrade service plan to enable)',
                'name' => 'check_interval',
                'value' => $item,
                'extra' => 'disabled',
            ];
        }

        $target_interval = [];
        $targets = [];
        $targets[] = [
            'label' => 'Auto',
            'name' => 'target_interval',
            'value' => 'auto',
        ];
        for($i = 0; $i < 60; $i++) {
            $targets[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        $res->view('numbers/edit', compact('checks', 'tracking', 'targets'));
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

    public function copy($req, $res) {
        $params = $req->val('params', [
            //'id' => ['required', ['dbOwner' => ['trackings', 'id', $req->user_id]]],
            'id' => ['required', 'dbEditorTracking'],
        ]);

        $tracking = Tracking::find($params['id']);

        // /number/add/COLLECTION_ID/CATEGORY_SLUG/NUMBER_SLUG/CONNECTION_ID
        $url = '';
        $url .= '/number/add/';
        $url .= $tracking->data['collection_id'];
        $url .= '/';
        $url .= $tracking->data['category']['slug'];
        $url .= '/';
        $url .= $tracking->data['number']['slug'];
        $url .= '/';
        $url .= $tracking->data['connection_id'];

        // Set up the fields to pass
        $res->session->next_flash['fields'] = [];
        $res->session->next_flash['fields']['name'] = $tracking->data['title_raw'];
        $res->session->next_flash['fields']['interval'] = $tracking->data['check_interval'];
        $res->session->next_flash['fields']['priority'] = $tracking->data['priority'] - 5;

        if(isset($tracking->data['values']['range']) && isset($tracking->data['values']['ago'])) {
            if($tracking->data['values']['range'] == 'all' && $tracking->data['values']['ago'] == 'now') {
                $res->session->next_flash['fields']['period'] = 'all';
            } elseif(preg_match('/.*y.*m.*w.*d/', $tracking->data['values']['range'])) {
                $res->session->next_flash['fields']['period'] = 'other';

                $ago = preg_split('/([0-9]+[alymwdhsin]+)/', strtolower($tracking->data['values']['ago']), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                $range = preg_split('/([0-9]+[alymwdhsin]+)/', strtolower($tracking->data['values']['range']), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

                $res->session->next_flash['fields']['years_ago'] = intval($ago[0]);
                $res->session->next_flash['fields']['months_ago'] = intval($ago[1]);
                $res->session->next_flash['fields']['weeks_ago'] = intval($ago[2]);
                $res->session->next_flash['fields']['days_ago'] = intval($ago[3]);

                $res->session->next_flash['fields']['years_range'] = intval($range[0]);
                $res->session->next_flash['fields']['months_range'] = intval($range[1]);
                $res->session->next_flash['fields']['weeks_range'] = intval($range[2]);
                $res->session->next_flash['fields']['days_range'] = intval($range[3]);
            } else {
                $res->session->next_flash['fields']['period'] = $tracking->data['values']['range'] . '_' . $tracking->data['values']['ago'];
            }
        }

        $res->success('Update the info below as needed and then submit to create a new number.', $url);
    }

    public function delete($req, $res) {
        $val = $req->val('params', [
            //'id' => ['required', ['dbOwner' => ['trackings', 'id', $req->user_id]]],
            'id' => ['required', 'dbEditorTracking'],
        ]);

        Tracking::delete($val['id']);

        $res->success('Item successfully deleted.');
    }

    public function update($req, $res) {
        $params = $req->val('params', [
            //'id' => ['required', ['dbOwner' => ['trackings', 'id', $req->user_id]]],
            'id' => ['required', 'dbEditorTracking'],
        ]);

        $data = $req->val('data', [
            'name' => ['required'],
        ]);

        if(isset($req->data['check_interval'])) {
            $intervals = ['1 hour', '5 minutes', 'static'];
            $intervals = ao()->hook('app_intervals', $intervals);

            $data2 = $req->val('data', [
                'check_interval' => ['required', ['in' => $intervals]],
                'target_interval' => ['required', ['match' => '/^auto$|^[0-5][0-9]$/']],
            ]);
        }

        $tracking = Tracking::find($params['id']);


        // Probably need to figure out a better way to do this. Because this is saving the name
        // which should be encrypted, the other encrypted data needs to be saved too.
        $tracking->data['data'] = $tracking->data['values'];
        $tracking->data['method'] = $tracking->data['function'];

        $tracking->data['name'] = $data['name'];

        if(isset($req->data['check_interval'])) {
            $tracking->data['check_interval'] = $data2['check_interval'];
            $tracking->data['target_interval'] = $data2['target_interval'];
        }

        $tracking->data['encrypted'] = 0;
        $tracking->save();

        $res->success('Item successfully updated.', '/collection/view/' . $tracking->data['collection_id']);
    }

}
