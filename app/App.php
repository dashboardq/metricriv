<?php

namespace app;

use app\models\Follow;
use app\models\Setting;
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
    }

    public function cacheDate($vars, $view) {
        if($view == 'head' || $view == 'foot') {
            $vars['cache_date'] = '2022-07-15';
        }

        return $vars;
    }

    public function getDates($user_id, $range_string, $ago_string) {
        $output = []; 

        $timezone = Setting::get($user_id, 'timezone');
        $week_day = Setting::get($user_id, 'week_day');

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
        // Right now only accepting y, m, d
        $range = preg_split('/([0-9]+[alymwdhsin]+)/', strtolower($range_string), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if(!count($range)) {
            $range = ['all'];
        }

        // Should be passed with the time lengths from largest to smallest.
        // now, 1y2m3w4d5h6min7s
        // Right now only accepting y, m, d
        $ago = preg_split('/([0-9]+[nowymdhsi]+)/', strtolower($ago_string), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if(!count($ago)) {
            $ago = ['now'];
        }

        $dt = new DateTime();
        // Right now only accepting y, m, w, d (others included for future use)
        $dt->setTime(0, 0);
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

        $largest_range_type = preg_replace('/[0-9]+/', '', $range[0]);

        if($largest_range_type == 'y') {
            // Set dt to the beginning
            $dt->setDate($dt->format('Y'), 1, 1);
            $start = $dt->format('Y-m-d');
        } elseif($largest_range_type == 'm') {
            $dt->setDate($dt->format('Y'), $dt->format('m'), 1);
            $start = $dt->format('Y-m-d');
        } elseif($largest_range_type == 'w') {
            if($dt->format('l') == $week_day) {
                $start = $dt->format('Y-m-d');
            } else {
                $dt->modify('previous ' . $week_day);
                $start = $dt->format('Y-m-d');
            }   
        } elseif($largest_range_type == 'd') {
            $start = $dt->format('Y-m-d H:i:s');
        }  

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


        // Modify times for timezones
        $tz = new DateTimeZone($timezone);
        $utc = new DateTimeZone('UTC');

        $start_dt = new DateTime($start, $tz);
        $start_dt->setTimezone($utc);
        $timestamp_start = $start_dt->getTimestamp();
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($end, $tz);
        $end_dt->setTimezone($utc);
        $timestamp_end = $end_dt->getTimestamp();
        $end = $end_dt->format('Y-m-d H:i:s');

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
}
