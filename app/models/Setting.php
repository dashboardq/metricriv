<?php

namespace app\models;

use mavoc\core\Model;


class Setting extends Model {
    public static $table = 'settings';
    public static $order = ['name' => 'asc'];

    public static $defaults = [
        'timezone' => [
            'name' => 'Timezone',
            'key' => 'timezone',
            'value' => 'UTC',
            'editable' => 1,
        ],
        'week_start' => [
            'name' => 'Start Of Week',
            'key' => 'week_start',
            'value' => 'Sunday',
            'editable' => 1,
        ],
    ];

    public static function get($user_id = 0, $key = null) {
        $output = null;

        if(is_array($key)) {
            $results = Setting::where('user_id', $user_id, 'data');
            $settings = [];
            foreach($results as $item) {
                if(in_array($item['key'], $key)) {
                    $settings[$item['key']] = $item['value'];
                }
            }

            // Set defaults
            foreach(self::$defaults as $default) {
                if(in_array($default['key'], $key) && !isset($settings[$default['key']])) {
                    $settings[$default['key']] = $default['value'];
                }
            }

            $output = $settings;
        } elseif($key) {
            $result = Setting::by(['user_id' => $user_id, 'key' => $key], '', 'data');

            if($result) {
                $output = $result['value'];
            } elseif(isset(self::$defaults[$key])) {
                $output = self::$defaults[$key]['value'];
            }
        } else {
            $results = Setting::where('user_id', $user_id, 'data');
            $settings = [];
            foreach($results as $item) {
                $settings[$item['key']] = $item['value'];
            }

            // Set defaults
            foreach(self::$defaults as $default) {
                if(!isset($settings[$default['key']])) {
                    $settings[$default['key']] = $default['value'];
                }
            }

            $output = $settings;
        }

        return $output;
    }

    public static function set($user_id = 0, $key = null, $value = null) {
        if(is_array($key)) {
            foreach($key as $k => $v) {
                $item = Setting::by(['user_id' => $user_id, 'key' => $k]);
                if($item) {
                    $item->data['value'] = $v;
                    $item->save();
                } else {
                    $item = Setting::create([
                        'user_id' => $user_id, 
                        'name' => self::$defaults[$k]['name'], 
                        'editable' => self::$defaults[$k]['editable'], 
                        'key' => $k, 
                        'value' => $v,
                    ]);
                }
            }
        } else {
            $item = Setting::by(['user_id' => $user_id, 'key' => $key]);
            if($item) {
                $item->data['value'] = $value;
                $item->save();
            } else {
                $item = Setting::create([
                    'user_id' => $user_id, 
                    'name' => self::$defaults[$key]['name'], 
                    'editable' => self::$defaults[$key]['editable'], 
                    'key' => $key, 
                    'value' => $value,
                ]);
            }
        }
    }
}
