<?php

namespace app\controllers;

use app\models\Collection;
use app\models\Viewer;
use app\models\Username;

class ViewersController {
    public function list($req, $res) {
        $list = Viewer::where('user_id', $req->user->data['id'], 'data');

        $res->view('viewers/list', compact('list'));
    }

    public function add($req, $res) {
        $list = Collection::where([
            'user_id' => $req->user_id,
        ], 'data');

        ao()->hook('app_add_viewer', $req->user_id, $req, $res);

        $collections = [];
        foreach($list as $item) {
            $collections[] = [
                'label' => $item['name'],
                'value' => $item['id'],
            ];
        }

        return compact('collections');
    }

    public function create($req, $res) {
        $data = $req->val('data', [
            'username' => ['required', ['dbExists' => ['usernames', 'name']]],
            'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
            'type' => ['required', ['in' => ['viewer', 'editor']]],
        ]);

        ao()->hook('app_add_viewer', $req->user_id, $req, $res);

        $username = Username::by('name', $data['username']);

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['viewer_id'] = $username->data['user_id'];
        $args['collection_id'] = $data['collection_id'];
        $args['type'] = $data['type'];
        $args['username'] = $data['username'];
        Viewer::create($args);

        $res->redirect('viewers');
    }

    public function delete($req, $res) {
        $params = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['connections', 'id', $req->user_id]]],
        ]);

        Connection::delete($params['id']);

        $res->success('Item successfully deleted.');

    }

    public function edit($req, $res) {
        $params = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['viewers', 'id', $req->user_id]]],
        ]);

        $viewer = Viewer::find($params['id']);

        $list = Collection::where([
            'user_id' => $req->user_id,
        ], 'data');

        $collections = [];
        foreach($list as $item) {
            $collections[] = [
                'label' => $item['name'],
                'value' => $item['id'],
            ];
        }

        $res->fields['username'] = $viewer->data['username'];
        $res->fields['collection_id'] = $viewer->data['collection_id'];
        $res->fields['type'] = $viewer->data['type'];

        return compact('collections', 'item');
    }

    public function update($req, $res) {
        $params = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['viewers', 'id', $req->user_id]]],
        ]);

        $data = $req->val('data', [
            'username' => ['required', ['dbExists' => ['usernames', 'name']]],
            'collection_id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
            'type' => ['required', ['in' => ['viewer', 'editor']]],
        ]);

        $username = Username::by('name', $data['username']);
        $viewer = Viewer::find($params['id']);

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['viewer_id'] = $username->data['user_id'];
        $args['collection_id'] = $data['collection_id'];
        $args['type'] = $data['type'];
        $args['username'] = $data['username'];
        $viewer->update($args);

        $res->success('Item successfully updated.', '/viewers');
    }
}
