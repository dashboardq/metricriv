<?php

namespace app\services;

use app\models\Category;
use app\models\Connection;
use app\models\Number;

class ConnectionService {
    public static function handle($req, $res, $method = 'main', $state = '') {
        $category = Category::by('slug', $req->params['category_slug']);
        /* Not used and throws errors with OAuth connection redirect
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);
         */

        $class_name = classify($category->data['slug']) . 'ConnectionService';
        $class_name = ao()->hook('app_connection_service_class_name', $class_name);

        $class = '\app\services\connections\\' . $class_name;
        $class = ao()->hook('app_connection_service_class', $class);

        $file_name = $class_name .'.php';
        $file_name = ao()->hook('app_connection_service_file_name', $file_name);

        $file = ao()->dir('app/services/connections') . DIRECTORY_SEPARATOR . $file_name;
        $file = ao()->hook('app_connection_service_file', $file);

        $method = ao()->hook('app_connection_service_method', $method);

        if(is_file($file)) {
            include_once $file;
            if(is_callable([$class, $method])) {
                call_user_func([$class, $method], $req, $res, $state);
            } else {
                $method = 'main';
                call_user_func([$class, $method], $req, $res, $state);
            }
        }

        // If nothing found, show an error.
        $res->error('There was a problem processing the request. Please contact support.');
    }
}
