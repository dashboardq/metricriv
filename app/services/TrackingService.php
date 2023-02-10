<?php

namespace app\services;

use app\models\Category;
use app\models\Number;
use app\models\Restriction;
use app\models\Tracking;

class TrackingService {
    public static function handle($req, $res) {
        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        $restriction = Restriction::fullAccess($req->user_id, 'data');
        $restriction = ao()->hook('app_tracking_service_restriction', $restriction, $category, $number);

        $class_name = classify($category->data['slug']) . 'Service';
        $class_name = ao()->hook('app_tracking_service_class_name', $class_name);

        $class = '\app\services\trackings\\' . $class_name;
        $class = ao()->hook('app_tracking_service_class', $class);

        $file_name = $class_name .'.php';
        $file_name = ao()->hook('app_tracking_service_file_name', $file_name);

        $file = ao()->dir('app/services/trackings') . DIRECTORY_SEPARATOR . $file_name;
        $file = ao()->hook('app_tracking_service_file', $file);



        $subclass_name = classify($category->data['slug']) . classify($number->data['slug']) . 'Service';
        $subclass_name = ao()->hook('app_tracking_service_subclass_name', $subclass_name);

        $subclass = '\app\services\trackings\\' . underscorify($category->data['slug']) . '\\'. $subclass_name;
        $subclass = ao()->hook('app_tracking_subservice_class', $subclass);
        
        $subfile_name = $subclass_name .'.php';
        $subfile_name = ao()->hook('app_tracking_service_subfile_name', $subfile_name);

        $subfile = ao()->dir('app/services/trackings/' . $category->data['slug']) . DIRECTORY_SEPARATOR . $subfile_name;
        $subfile = ao()->hook('app_tracking_service_file', $subfile);


        $method = methodify($number->data['slug']);
        $method = ao()->hook('app_tracking_service_method', $method);

        if(is_file($subfile)) {
            include_once $subfile;
            if(is_callable([$subclass, $method])) {
                call_user_func([$subclass, $method], $req, $res);
            } else {
                $method = 'main';
                call_user_func([$subclass, $method], $req, $res);
            }
        } elseif(is_file($file)) {
            include_once $file;
            if(is_callable([$class, $method])) {
                call_user_func([$class, $method], $req, $res);
            } else {
                $method = 'main';
                call_user_func([$class, $method], $req, $res);
            }
        }

        // If nothing found, show an error.
        $res->error('There was a problem processing the request. Please contact support.');
    }

    public static function update($tracking_id, $manual_result = null, $force = false) {
        $tracking = Tracking::find($tracking_id);

        $proceed = false;
        if($force && in_array($tracking->data['status'], ['active', 'failed'])) {
            $proceed = true;
        } elseif($tracking->data['status'] == 'initial') {
            $proceed = true;
        } elseif(in_array($tracking->data['status'], ['active', 'failed']) && $tracking->data['next_check_at'] <= now()) {
            $proceed = true;
        }

        // TODO: Add error handling.
        if($proceed && $tracking->data['function']) {
            //echo '<pre>'; print_r($tracking);die;
            $class_method = json_decode($tracking->data['function']);
            $class_method = ao()->hook('app_tracking_service_update_class_method', $class_method);

            $class = $class_method[0];
            $class = ao()->hook('app_tracking_service_update_class', $class);

            $real_file = '';
            if(isset($tracking->data['values']['file'])) {
                $real_file = ao()->dir($tracking->data['values']['file']);
                $real_file = ao()->hook('app_tracking_service_update_real_file', $real_file);
            }
            $file = ao()->dir($class) . '.php';
            $file = ao()->hook('app_tracking_service_update_file', $file);

            $method = $class_method[1];
            $method = ao()->hook('app_tracking_service_update_method', $method);

            if($real_file && is_file($real_file)) {
                include_once $real_file;
                call_user_func([$class, $method], $tracking, $manual_result);
            } elseif(is_file($file)) {
                include_once $file;
                call_user_func([$class, $method], $tracking, $manual_result);
            }
        }
    }
}
