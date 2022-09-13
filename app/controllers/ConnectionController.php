<?php

namespace app\controllers;

use app\models\Connection;

class ConnectionController {
    public function list($req, $res) {
        $list = Connection::where('user_id', $req->user->data['id'], 'data');

        $res->view('connections/list', compact('list'));
    }

    public function delete($req, $res) {
        $val = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['connections', 'id', $req->user_id]]],
        ]);

        Connection::delete($val['id']);

        $res->success('Item successfully deleted.');

    }

    public function edit($req, $res) {
        $val = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['connections', 'id', $req->user_id]]],
        ]);

        $item = Connection::find($val['id']);

        $res->view('connections/edit', compact('item'));
    }

    public function update($req, $res) {
        $val = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['connections', 'id', $req->user_id]]],
        ]);

        $val2 = $req->val('data', [
            'name' => ['required'],
        ]);

        $connection = Connection::find($val['id']);

        $values = $connection->data['values'];
        $values['name'] = $val2['name'];

        $connection->data['data'] = $values;
        $connection->data['encrypted'] = 0;
        $connection->save();

        $redirect = ao()->session->data[$connection->data['category']['slug'] . '_redirect'];
        if($redirect) {
            unset(ao()->session->data[$connection->data['slug']]);
            $redirect .= '/' . $connection->id;
            $res->redirect($redirect);
        } else {
            $res->success('Item successfully updated.', '/connections');
        }

        // Redirect so to edit the name of the connection
    }
}
