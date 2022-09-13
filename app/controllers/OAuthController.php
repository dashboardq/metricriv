<?php

namespace app\controllers;

use app\models\Category;
use app\services\ConnectionService;

use mavoc\core\REST;

class OAuthController {
    public function redirect($req, $res) {
        ConnectionService::handle($req, $res, 'redirect');
    }

    public function start($req, $res) {
        $services = [
                'github',
        ];
        $val = $req->val('params', [
            'category_slug' => ['required', ['in' => [$services]]],
        ]);

        $val2 = $req->val('data', [
            'path' => ['required', ['match' => '|^/number/add/[0-9]/[A-Za-z0-9-]+/[A-Za-z0-9-]+$|']],
        ]);


        $slug = $val['category_slug'];
        $category = Category::by('slug', $val['category_slug']);

        $session_id = ao()->session->id;
        if(!$session_id) {
            // If for some reason the session_id is not returned, use random values.
            // Based on: https://developer.okta.com/blog/2018/07/09/five-minute-php-app-auth
            // https://www.php.net/random_bytes
            $session_id = bin2hex(random_bytes(5));
        }
        $state = hash('sha256', $session_id);
        ao()->session->data[$slug . '_category_id'] = $category->id;
        ao()->session->data[$slug . '_state'] = $state;
        ao()->session->data[$slug . '_redirect'] = $val2['path'];

        ConnectionService::handle($req, $res, 'start', $state);
    }
}
