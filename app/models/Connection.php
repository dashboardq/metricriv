<?php

namespace app\models;

use mavoc\core\Model;
use mavoc\core\Secret;

use DateTime;

class Connection extends Model {
    public static $table = 'connections';
    // Only need to set when the model has dynamic data.
    // Make sure to update when migration changes columns.
    // TODO: Maybe have migration files structured in a way so that columns can be pulled dynamically.
    public static $columns = [
        'id',
        'user_id',
        'category_id',
        'connection_type_id',
        'data',
        'encrypted',
        'created_at',
        'updated_at',
    ];

    public static $hooked = false;

    public function __construct($args) {
        // Only add the hook once (otherwise it gets added everytime compounding the calls.
        // TODO: Figure out a better way to add dynamic data. Maybe Model.php could have a place to put them.
        if(!Connection::$hooked) {
            ao()->filter('ao_model_process_' . Connection::$table . '_data', [$this, 'process']);
            Connection::$hooked = true;
        }


        // May want to look at using hooks instead of __construct().
        parent::__construct($args);
    }   

    public static function delete($id) {
        $trackings = Tracking::where('connection_id', $id);
        foreach($trackings as $tracking) {
            Tracking::delete($tracking->id);
        }

        parent::delete($id);
    }

    public function process($data) {
        $data['category'] = Category::find($data['category_id'])->data;
        $created_at = new DateTime($data['created_at'] ?? '');
        $data['created'] = $created_at->format('M j, Y H:i');
        $updated_at = new DateTime($data['updated_at'] ?? '');
        $data['updated'] = $updated_at->format('M j, Y H:i');

        if(!isset($data['encrypted'])) {
            $data['encrypted'] = 0;
        }

        if(ao()->env('APP_ENCRYPT_CONNECTIONS')) {
            if(
                !$data['encrypted'] 
                && $data['data']
            ) {
                // Needs to encrypt data
                $secret = new Secret(ao()->env('APP_ENCRYPT_CONNECTIONS'));

                // values is not saved to the database
                $data['values'] = $data['data'];

                $json_data = [];
                $json_data['data'] = $data['data'];

                $data['data'] = $secret->encrypt(json_encode($json_data));

            } elseif(
                $data['encrypted'] 
                && $data['data']
            ) {
                // Needs to unencrypt data
                $secret = new Secret(ao()->env('APP_ENCRYPT_CONNECTIONS'));
                $json = $secret->decrypt($data['data']);
                $json_data = json_decode($json, true);

                $data['values'] = $json_data['data'];

            } else {
                // No data to encrypt/unencrypt
                if(!isset($data['values'])) {
                    $data['values'] = [];
                }
            }

            $data['encrypted'] = 1;
        } else {
            $data['values'] = $data['data'];

            $data['encrypted'] = 0;
        }

        return $data;
    }
}
