<?php

namespace app;

use app\models\Collection;
use app\models\Follow;
use app\models\Setting;
use app\models\Tracking;
use app\models\User;

use DateTime;
use DateTimeZone;

class App {
    public function init() {
        // Run migrations if the user is not running a command line command and the db needs to be migrated.
        if(!defined('AO_CONSOLE_START') && ao()->env('DB_USE') && ao()->env('DB_INSTALL')) {
            ao()->once('ao_db_loaded', [$this, 'install']);
        }

        ao()->filter('ao_gen_key_names', [$this, 'keyNames']);

        ao()->filter('ao_response_partial_args', [$this, 'headerApp']);
        ao()->filter('ao_response_partial_args', [$this, 'cacheDate']);

        ao()->filter('ao_helpers_classify_words', [$this, 'classify']);

        ao()->filter('ao_validator_init', [$this, 'validator']);
    }

    public function cacheDate($vars, $view) {
        if($view == 'head' || $view == 'foot' || $view == 'footer') {
            $vars['cache_date'] = '2024-01-10';
        }

        return $vars;
    }

    public function classify($words) {
        $parts = explode(' ', $words);
        foreach($parts as $i => $word) {
            if($word == 'Numbersq') {
                $parts[$i] = 'NumbersQ';
            } elseif($word == 'Metricriv') {
                $parts[$i] = 'MetricRiv';
            }
        }

        $words = implode(' ', $parts);

        return $words;
    }

    public function getDates($user_id, $range_string, $ago_string, $format = 'Y-m-d H:i:s', $timezone_user_id = 0) {
        $output = []; 

        $timezone = Setting::get($user_id, 'timezone');
        $week_start = Setting::get($user_id, 'week_start');

        // Modify times for timezones
        $tz = new DateTimeZone($timezone);
        $utc = new DateTimeZone('UTC');

        // Right now only accepting y, m, w, d (others included for future use)
        $types = [];
        $types['y'] = 'year';
        $types['m'] = 'month';
        $types['w'] = 'week';
        $types['d'] = 'day';
        $types['h'] = 'hour';
        $types['min'] = 'minute';
        $types['s'] = 'seconds';

        // Should be passed with the time lengths from largest to smallest.
        // all, 1y2m3w4d5h6min7s
        // Right now only accepting y, m, w, d
        $range = preg_split('/([0-9]+[alymwdhsin]+)/', strtolower($range_string), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if(!count($range)) {
            $range = ['all'];
        }

        // Should be passed with the time lengths from largest to smallest.
        // now, 1y2m3w4d5h6min7s
        // Right now only accepting y, m, w, d
        $ago = preg_split('/([0-9]+[nowymdhsi]+)/', strtolower($ago_string), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if(!count($ago)) {
            $ago = ['now'];
        }

        $dt = new DateTime('now', $tz);
        // Right now only accepting y, m, w, d (others included for future use)
        $dt->setTime(0, 0);

        // Strip out 0 values
        foreach($range as $i => $item) {
            if(in_array($item, ['0y', '0m', '0w', '0d'])) {
                unset($range[$i]);
            }
        }
        $range = array_values($range);

        $largest_range_type = preg_replace('/[0-9]+/', '', $range[0]);

        if($largest_range_type == 'y') {
            // Set dt to the beginning
            $dt->setDate($dt->format('Y'), 1, 1);
        } elseif($largest_range_type == 'm') {
            $dt->setDate($dt->format('Y'), $dt->format('m'), 1);
        } elseif($largest_range_type == 'w') {
            if($dt->format('l') != $week_start) {
                $dt->modify('previous ' . $week_start);
            }   
        }  

        if($ago[0] != 'now') {
            foreach($ago as $item) {
                $value = preg_replace('/[a-z]+/', '', $item);
                $type = preg_replace('/[0-9]+/', '', $item);
                if(isset($types[$type])) {
                    //$dt->modify('-1 year');
                    $dt->modify('-' . $value . ' ' . $types[$type]);
                }   
            }   
        }   

        // Format based on range type
        if($largest_range_type == 'y') {
            $start = $dt->format('Y-m-d');
        } elseif($largest_range_type == 'm') {
            $start = $dt->format('Y-m-d');
        } elseif($largest_range_type == 'w') {
            if($dt->format('l') == $week_start) {
                $start = $dt->format('Y-m-d');
            } else {
                $start = $dt->format('Y-m-d');
            }   
        } elseif($largest_range_type == 'd') {
            $start = $dt->format('Y-m-d H:i:s');
        } else {
            $start = $dt->format('Y-m-d H:i:s');
        }  

        /* Original 
        if($largest_range_type == 'y') {
            // Set dt to the beginning
            $dt->setDate($dt->format('Y'), 1, 1);
            $start = $dt->format('Y-m-d');
        } elseif($largest_range_type == 'm') {
            $dt->setDate($dt->format('Y'), $dt->format('m'), 1);
            $start = $dt->format('Y-m-d');
        } elseif($largest_range_type == 'w') {
            if($dt->format('l') == $week_start) {
                $start = $dt->format('Y-m-d');
            } else {
                $dt->modify('previous ' . $week_start);
                $start = $dt->format('Y-m-d');
            }   
        } elseif($largest_range_type == 'd') {
            $start = $dt->format('Y-m-d H:i:s');
        } else {
            $start = $dt->format('Y-m-d H:i:s');
        }  
         */

        foreach($range as $item) {
            $value = preg_replace('/[a-z]+/', '', $item);
            $type = preg_replace('/[0-9]+/', '', $item);
            if(isset($types[$type])) {
                //$dt->modify('+1 year');
                $dt->modify('+' . $value . ' ' . $types[$type]);
            }   
        }   

        // Because the date search is inclusive, subtract one second
        $dt->modify('-1 second');
        $end = $dt->format('Y-m-d H:i:s');

        $start_dt = new DateTime($start, $tz);
        // If the $timezone_user_id is the same as the $user_id, then use the user_id timezone.
        if($timezone_user_id == 0 || $timezone_user_id != $user_id) {
            $start_dt->setTimezone($utc);
        }
        $timestamp_start = $start_dt->getTimestamp();
        //$start = $start_dt->format('Y-m-d H:i:s');
        $start = $start_dt->format($format);

        $end_dt = new DateTime($end, $tz);
        // If the $timezone_user_id is the same as the $user_id, then use the user_id timezone.
        if($timezone_user_id == 0 || $timezone_user_id != $user_id) {
            $end_dt->setTimezone($utc);
        }
        $timestamp_end = $end_dt->getTimestamp();
        //$end = $end_dt->format('Y-m-d H:i:s');
        $end = $end_dt->format($format);

        $output = compact('start', 'end');
        return $output;
    }

    public function headerApp($vars, $view) {
        if($view == 'header_app') {
            $user_id = ao()->request->user_id;
            $vars['follows'] = Follow::where('user_id', $user_id);

            $additional_links = [0 => ['url' => '/account', 'name' => 'Account']];
            $additional_links = ao()->hook('app_header_additional_links', $additional_links);
            $vars['additional_links'] = $additional_links;
        }

        return $vars;
    }

    public function install() {
        try {
            $count = User::count();
        } catch(\Exception $e) {
            //ao()->command('work');
            ao()->command('mig init');
            ao()->command('mig up');

            // Redirect to home page now that the database is installed.
            header('Location: /');
            exit;
        }
    }

    public function keyNames($list) {
        $list = [];
        $list[] = 'CONNECTIONS_1';
        $list[] = 'NUMBERS_1';

        return $list;
    }

    public function validator($validator) {
        $validator->_add('dbEditorCollection', [$this, 'dbEditorCollection']);
        $validator->_add('dbEditorCollectionMessage', [$this, 'dbEditorCollectionMessage']);

        $validator->_add('dbEditorTracking', [$this, 'dbEditorTracking']);
        $validator->_add('dbEditorTrackingMessage', [$this, 'dbEditorTrackingMessage']);
    }

    public function dbEditorCollection($input, $field) {
        $value = $input[$field];

        // Check if the user owns the collection.
        $collection = Collection::find($value);
        if($collection->data['user_id'] == ao()->request->user_id) {
            return true;
        }

        // User does not own the collection so check to see if the user has access to the collection.
        $results = ao()->db->query('SELECT * FROM viewers WHERE type = "editor" AND viewer_id = ? AND collection_id = ? LIMIT 1', ao()->request->user_id, $value);

        if(count($results)) {
            return true;
        } else {
            return false;
        }
    }   
    public function dbEditorCollectionMessage($input, $field) {
        $output = 'Your account does not have the access needed to perform the requested action.';
        return $output;
    }

    public function dbEditorTracking($input, $field) {
        $value = $input[$field];

        $tracking = Tracking::find($value);
        $collection = Collection::find($tracking->data['collection_id']);
        // Check if the user owns the collection where the tracking was created.
        if($collection->data['user_id'] == ao()->request->user_id) {
            return true;
        }

        $results = ao()->db->query('SELECT * FROM viewers WHERE type = "editor" AND viewer_id = ? AND collection_id = ? LIMIT 1', ao()->request->user_id, $collection->id);

        if(count($results)) {
            return true;
        } else {
            return false;
        }
    }   
    public function dbEditorTrackingMessage($input, $field) {
        $output = 'Your account does not have the access needed to perform the requested action.';
        return $output;
    }
}
