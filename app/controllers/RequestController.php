<?php

namespace app\controllers;

use app\models\Comment;
use app\models\Request;
use app\models\Vote;

class RequestController {
    public function list($req, $res) {
        $list = Request::all('data');

        $res->view('requests/list', compact('list'));
    }

    public function add($req, $res) {
        $res->view('requests/add');
    }

    public function comment($req, $res) {
        if(!$req->user) {
            $res->error('You must be logged in to perform this action.', '/requests');
        }

        $val = $req->val('params', [
            'id' => ['required', ['dbExists' => ['requests']]],
        ]);

        $val2 = $req->val('data', [
            'comment' => ['required'],
        ]);

        $args = [];
        $args['author_id'] = $req->user_id;
        $args['request_id'] = $val['id'];
        $args['content'] = $val2['comment'];
        Comment::create($args);

        $res->back();
    }

    public function create($req, $res) {
        if(!$req->user) {
            $res->error('You must be logged in to perform this action.', '/requests');
        }
        $val = $req->val('data', [
            'title' => ['required'],
            'message' => ['required'],
        ]);

        $args = [];
        $args['author_id'] = $req->user_id;
        $args['title'] = $val['title'];
        $args['content'] = $val['message'];
        Request::create($args);

        $res->redirect('requests');
    }

    public function missing($req, $res) {
        $res->view('requests/missing');
    }

    public function view($req, $res) {
        $val = $req->val('params', [
            'id' => ['required', ['dbExists' => ['requests']]],
        ]);
        $item = Request::find($req->params['id'], 'data');

        $comments = Comment::where('request_id', $item['id'], 'data');

        $res->view('requests/view', compact('comments', 'item'));
    }

    public function vote($req, $res) {
        if(!$req->user) {
            $res->error('You must be logged in to perform this action.', '/requests');
        }

        $val = $req->val('params', [
            'id' => ['required', ['dbExists' => ['requests']]],
        ]);

        $val2 = $req->val('data', [
            'direction' => ['required', ['in' => [['up', 'down']]]],
        ]);

        Vote::delete(['request_id' => $val['id'], 'user_id' => $req->user_id]);

        $args = [];
        if($val2['direction'] == 'down') {
            $args['score'] = -1;
        } else {
            $args['score'] = 1;
        }
        $args['user_id'] = $req->user_id;
        $args['request_id'] = $val['id'];
        Vote::create($args);

        $request = Request::find($val['id']);
        $request->updateScore();

        $res->back();
    }

    public function delete($req, $res) {
        $val = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['trackings', 'id', $req->user_id]]],
        ]);

        Tracking::delete($val['id']);

        $res->success('Item successfully deleted.');

    }
}
