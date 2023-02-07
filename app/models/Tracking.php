<?php

namespace app\models;

use mavoc\core\Model;
use mavoc\core\Secret;

use DateTime;

class Tracking extends Model {
    public static $table = 'trackings';
    public static $order = ['id' => 'asc'];

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
        if($data['connection_id']) {
            $data['connection'] = Connection::find($data['connection_id'])->data;
        }
        $updated_at = new DateTime($data['updated_at'] ?? '');
        $data['updated'] = $updated_at->format('M j, Y H:i');

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
                $data['values'] = $data['data'];
                $data['function'] = $data['method'];

                $json_data = [];
                $json_data['name'] = $data['name'];
                $json_data['data'] = $data['data'];
                $json_data['method'] = $data['method'];

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

                $data['title'] = $json_data['name'];
                $data['values'] = $json_data['data'];
                $data['function'] = $json_data['method'];

            } else {
                // No data to encrypt/unencrypt
                if(!isset($data['title'])) {
                    $data['title'] = '';
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
            $data['title'] = $data['name'];
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
        $this->data['name'] = $this->data['title'];
        $this->data['method'] = $this->data['function'];
        $this->data['encrypted'] = 0;
        $this->save();
    }

    public function updateData($values) {
        $dt = new DateTime();
        if($this->data['check_interval'] == '5 minutes') {
            $dt->modify('+5 minutes');
        } else {
            $dt->modify('+1 hour');
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
        $this->data['name'] = $this->data['title'];
        $this->data['method'] = $this->data['function'];
        $this->data['encrypted'] = 0;
        $this->save();
    }
}
