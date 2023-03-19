<?php

namespace app\controllers;

use app\models\Access;
use app\models\Follow;
use app\models\Collection;
use app\models\Number;
use app\models\Tracking;
use app\models\Username;

use mavoc\core\Email;

class CollectionsController {
    public function list($req, $res) {
        $usernames = Username::where('user_id', $req->user->data['id']);

        // First time logging in, make sure they have a username
        if(count($usernames) == 0) {
            $res->redirect('/username/add');
        }

        $owns = Collection::owns($req->user_id, 'data');
        $edits = Collection::edits($req->user_id, 'data');
        $views = Collection::views($req->user_id, 'data');

        return compact('owns', 'edits', 'views');
    }

    public function add($req, $res) {
        $usernames = Username::where('user_id', $req->user_id, 'data');
        return compact('usernames');
    }

    public function create($req, $res) {
        $val = $req->val('data', [
            'name' => ['required'],
            'username_id' => ['required', ['dbOwner' => ['usernames', 'id', $req->user_id]]],
            'visibility' => ['required', ['in' => ['Public', 'Private']]],
            '_titles' => ['username_id' => 'Slug'],
            '_messages' => ['username_id' => 'The {title} field needs to be selected and valid.'],
        ]);

        $val2 = $req->val('data', [
            'slug_' . $val['username_id'] => ['required', ['match' => '/^[A-Za-z0-9_]+$/']],
        ]);

        $username = Username::find($val['username_id'], 'data');

        // Make sure slug is unique
        $slug = '/' . $username['name'] . '/' . $val2['slug_' . $val['username_id']];
        $count = Collection::count('slug', $slug);
        if($count > 0) {
            $res->error('The selected Slug already exists. Please enter a different slug.');
        }

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['username_id'] = $val['username_id'];
        $args['name'] = $val['name'];
        $args['slug'] = $slug;
        if($val['visibility'] == 'Private') {
            $args['private'] = 1;
        } else {
            $args['private'] = 0;
        }
        $collection = Collection::create($args);

        $res->success('Item successfully updated.', '/collection/view/' . $collection->id);
    }

    public function delete($req, $res) {
        $val = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
        ]);

        $collection = Collection::find($val['id'], 'data');

        $username = Username::find($collection['username_id'], 'data');

        // Make sure slug is unique
        $primary_slug = '/' . $username['name'];

        if($collection['slug'] == $primary_slug) {
            $res->error('The primary collection cannot be deleted.');
        }

        $count = Tracking::count('collection_id', $collection['id']);
        if($count > 0) {
            $res->error('A collection with numbers cannot be deleted. You will need to delete the numbers in the collection first.');
        }

        Collection::delete($val['id']);

        $res->success('Item successfully deleted.');

    }

    public function edit($req, $res) {
        $val = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
            //'id' => ['required', 'dbEditorCollection'],
        ]);

        $item = Collection::find($req->params['id']);
        $usernames = Username::where('user_id', $req->user_id, 'data');

        if($item->data['slug_suffix'] == '') {
            $res->error('The primary collection cannot be edited.', '/collections');
        }

        $res->fields['name'] = $item->data['name'];
        $res->fields['username_id'] = $item->data['username_id'];
        $res->fields['slug_' . $item->data['username_id']] = $item->data['slug_suffix'];
        $res->fields['visibility'] = $item->data['private'] ? 'Private' : 'Public';

        return compact('item', 'usernames');
    }

    public function update($req, $res) {
        $params = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
        ]);

        $collection = Collection::find($params['id']);
        if($collection->data['slug_suffix'] == '') {
            $res->error('The primary collection cannot be edited.', '/collections');
        }

        $val = $req->val('data', [
            'name' => ['required'],
            'username_id' => ['required', ['dbOwner' => ['usernames', 'id', $req->user_id]]],
            'visibility' => ['required', ['in' => ['Public', 'Private']]],
            '_titles' => ['username_id' => 'Slug'],
            '_messages' => ['username_id' => 'The {title} field needs to be selected and valid.'],
        ]);

        $val2 = $req->val('data', [
            'slug_' . $val['username_id'] => ['required', ['match' => '/^[A-Za-z0-9_]+$/']],
        ]);

        $username = Username::find($val['username_id'], 'data');

        // Make sure slug is unique
        $slug = '/' . $username['name'] . '/' . $val2['slug_' . $val['username_id']];
        $count = Collection::count(['slug' => $slug, 'id' => ['!=', $params['id']]]);
        if($count > 0) {
            $res->error('The selected Slug already exists. Please enter a different slug.');
        }

        $collection->data['username_id'] = $val['username_id'];
        $collection->data['name'] = $val['name'];
        $collection->data['slug'] = $slug;
        if($val['visibility'] == 'Private') {
            $collection->data['private'] = 1;
        } else {
            $collection->data['private'] = 0;
        }
        $collection->save();

        $res->success('Item successfully updated.', '/collections');
    }

    public function view($req, $res) {
        $params = $req->val('params', [
            //'id' => ['required', ['dbExists' => ['collections', 'id']]],
            'id' => ['required', 'dbEditorCollection'],
        ]);

        $collection = Collection::find($params['id']);

        $list = Tracking::where('collection_id', $collection->data['id'], 'data');

        return compact('collection', 'list');
    }

    public function sortOrder($req, $res) {
        $params = $req->val('params', [
            //'id' => ['required', ['dbExists' => ['collections', 'id']]],
            'id' => ['required', 'dbEditorCollection'],
        ]);

        $collection = Collection::find($params['id']);

        $list = Tracking::where('collection_id', $collection->data['id'], 'data');

        return compact('collection', 'list');
    }

    public function sortOrderPost($req, $res) {
        $params = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
        ]);

        $data = $req->val('data', [
            'ids' => ['required'],
            'sort_orders' => ['required'],
        ]);

        $ids = $data['ids'];
        $sort_orders = $data['sort_orders'];

        foreach($sort_orders as $i => $sort_order) {
            $id = $ids[$i];
            $tracking = Tracking::find($id);
            // Make sure each of the tracking ids passed in are part of the collection.
            if($tracking->data['collection_id'] == $params['id']) {
                $tracking->data['priority'] = intval($sort_order);
                $tracking->save();
            }
        }

        $collection = Collection::find($params['id']);
        $collection->resort();

        $res->success('Sort order successfully updated.', '/collection/view/' . $params['id']);
    }
}
