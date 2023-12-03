<?php

namespace app\models;

use mavoc\core\Model;
use mavoc\core\Secret;

use DateTime;
use DateTimeZone;

class Tracking extends Model {
    public static $table = 'trackings';
    public static $order = ['priority' => 'desc'];

    // Only need to set when the model has dynamic data.
    // Make sure to update when migration changes columns.
    // TODO: Maybe have migration files structured in a way so that columns can be pulled dynamically.
    public static $columns = [
        'id',
        'user_id',
        'number_id',
        'collection_id',
        'connection_id',
        'name',
        'status',
        'method',
        'data',
        'encrypted',
        'check_interval',
        'priority',
        'next_check_at',
        'created_at',
        'updated_at',
        'target_interval',
    ];

    public static $hooked = false;

    public function __construct($args) {
        // Only add the hook once (otherwise it gets added everytime compounding the calls.
        // TODO: Figure out a better way to add dynamic data. Maybe Model.php could have a place to put them.
        if(!Tracking::$hooked) {
            ao()->filter('ao_model_process_' . Tracking::$table . '_data', [$this, 'process']);
            Tracking::$hooked = true;
        }


        // May want to look at using hooks instead of __construct().
        parent::__construct($args);
    }   

    public function process($data) {
        // Really need to figure out a better way to pull in these relationships 
        // ->data can easily fail
        $data['number'] = Number::find($data['number_id'])->data;
        $data['category'] = Category::find($data['number']['category_id'])->data;
        $data['collection'] = Collection::find($data['collection_id'])->data;
        if(isset($data['connection_id']) && $data['connection_id']) {
            $data['connection'] = Connection::find($data['connection_id'])->data;
        }

        $updated_at = new DateTime($data['updated_at'] ?? '');
        if(ao()->type == 'web') {
            $user_id = ao()->request->user_id;
        } else {
            $user_id = 0;
        }
        if($updated_at && $user_id) {
            $timezone = Setting::get($user_id, 'timezone');
            $tz = new DateTimeZone($timezone);
            $updated_at->setTimezone($tz);
            $data['updated'] = $updated_at->format('M j, Y g:i a');
        } elseif($updated_at) {
            $data['updated'] = $updated_at->format('M j, Y g:i a');
        } else {
            $data['updated'] = '';
        }

        if(!isset($data['encrypted'])) {
            $data['encrypted'] = 0;
        }

        if(ao()->env('APP_ENCRYPT_NUMBERS')) {
            if(
                !$data['encrypted'] 
                && ($data['data'] || $data['name'] || $data['method'])
            ) {
                // Needs to encrypt data
                $secret = new Secret(ao()->env('APP_ENCRYPT_NUMBERS'));

                // title, values, and function are not saved to the database
                $data['title'] = $data['name'];
                $data['title_raw'] = $data['name'];
                $data['values'] = $data['data'];
                if(isset($data['method'])) {
                    $data['function'] = $data['method'];
                } else {
                    $data['function'] = '';
                }

                $json_data = [];
                $json_data['name'] = $data['name'];
                $json_data['data'] = $data['data'];
                if(isset($data['method'])) {
                    $json_data['method'] = $data['method'];
                } else {
                    $json_data['method'] = '';
                }

                $data['name'] = '';
                $data['method'] = '';
                $data['data'] = $secret->encrypt(json_encode($json_data));

            } elseif(
                $data['encrypted'] 
                && ($data['data'] && !isset($data['values']))
            ) {
                // Needs to unencrypt data
                $secret = new Secret(ao()->env('APP_ENCRYPT_NUMBERS'));
                $json = $secret->decrypt($data['data']);
                $json_data = json_decode($json, true);

                $data['title'] = $this->parseTitle($json_data['name']);
                $data['title_raw'] = $json_data['name'];
                $data['values'] = $json_data['data'];
                $data['function'] = $json_data['method'];

            } else {
                // No data to encrypt/unencrypt
                if(!isset($data['title'])) {
                    $data['title'] = '';
                }
                if(!isset($data['title_raw'])) {
                    $data['title_raw'] = '';
                }
                if(!isset($data['values'])) {
                    $data['values'] = [];
                }
                if(!isset($data['function'])) {
                    $data['function'] = '';
                }
            }

            $data['encrypted'] = 1;
        } else {
            $data['title'] = $this->parseTitle($data['name']);
            $data['title_raw'] = $data['name'];
            $data['values'] = $data['data'];
            $data['function'] = $data['method'];

            $data['encrypted'] = 0;
        }

        return $data;
    }

    public function failData() {
        $dt = new DateTime();
        if($this->data['check_interval'] == '5 minutes') {
            $dt->modify('+5 minutes');
        } else {
            $dt->modify('+1 hour');
        }

        $this->data['values']['number'] = -1;
        $this->data['values']['color'] = 'red';

        $this->data['encrypted'] = 0;
        $this->data['status'] = 'failed';
        $this->data['next_check_at'] = $dt;

        $this->data['data'] = $this->data['values'];
        $this->data['name'] = $this->data['title_raw'];
        $this->data['method'] = $this->data['function'];
        $this->data['encrypted'] = 0;
        $this->save();
    }

    public function parseJSON($input) {
        $data = json_decode($input, true);
        if(is_array($data)) {
            if($this->data['user_id'] && isset($data['ago']) && isset($data['format'])) {
                // The $this->data['collection'] may not be available yet.
                $collection = Collection::find($this->data['collection_id']);
                $user_id = $collection->data['user_id'];

                //$dates = ao()->app->getDates($user_id, '1d', $data['ago']);
                if(isset($data['range'])) {
                    $dates = ao()->app->getDates($user_id, $data['range'], $data['ago']);
                } else {
                    // Should be passed with the time lengths from largest to smallest.
                    // all, 1y2m3w4d5h6min7s
                    // Right now only accepting y, m, w, d
                    $range = preg_split('/([0-9]+[alymwdhsin]+)/', strtolower($data['ago']), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                    if(!count($range)) {
                        $range = ['1d'];
                    }

                    // Strip out 0 values
                    foreach($range as $i => $item) {
                        if(in_array($item, ['0y', '0m', '0w', '0d'])) {
                            unset($range[$i]);
                        }
                    }
                    $range = array_values($range);

                    // If 0d passed in for ago, there will be no range values so set to 1d.
                    if(count($range) == 0) {
                        $range = ['1d'];
                    }

                    $largest_range_type = preg_replace('/[0-9]+/', '', $range[0]);
                    $dates = ao()->app->getDates($user_id, '1' . $largest_range_type, $data['ago']);
                }

                //$dates = ao()->app->getDates($user_id, '1m', '4m');
                //print_r($dates);die;

                $timezone = Setting::get($user_id, 'timezone');
                $tz = new DateTimeZone($timezone);
                $utc = new DateTimeZone('UTC');

                $dt = new DateTime($dates['start'], $utc);
                $dt->setTimezone($tz);
                //echo $dt->format($data['format']);die;
                return $dt->format($data['format']);
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function parseTitle($input) {
        $parts = preg_split('/({[^}]*})/', $input, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $output = '';
        foreach($parts as $part) {
            if(preg_match('/^{[^}]*}$/', $part)) {
                $output .= $this->parseJSON($part);
            } else {
                $output .= $part;
            }
        }

        return $output;
    }

    public function updateData($values) {
        $dt = new DateTime();
        if($this->data['check_interval'] == '5 minutes') {
            $dt->modify('+5 minutes');
        } else {
            $dt->modify('+1 hour');
        }

        if($this->data['target_interval'] != 'auto') {
            // Set the minutes for the next check
            $dt->setTime($dt->format('G'), $this->data['target_interval']);
        }

        $this->data['status'] = 'active';
        $this->data['next_check_at'] = $dt;

        if(!is_array($values)) {
            $this->data['values']['number'] = $values;
            $this->data['values']['color'] = 'black';
        } else {
            $this->data['values'] = array_merge($this->data['values'], $values);
        }

        $this->data['data'] = $this->data['values'];
        $this->data['name'] = $this->data['title_raw'];
        $this->data['method'] = $this->data['function'];
        $this->data['encrypted'] = 0;
        $this->save();
    }
}
