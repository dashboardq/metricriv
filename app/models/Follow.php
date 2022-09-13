<?php

namespace app\models;

use mavoc\core\Model;

class Follow extends Model {
    public static $table = 'follows';
    // Only need to set when the model has dynamic data.
    // Make sure to update when migration changes columns.
    // TODO: Maybe have migration files structured in a way so that columns can be pulled dynamically.
    public static $columns = [
        'id',
        'user_id',
        'collection_id',
        'priority',
        'created_at',
        'updated_at',
    ];

    public static $hooked = false;

    public function __construct($args) {
        // Only add the hook once (otherwise it gets added everytime compounding the calls.
        // TODO: Figure out a better way to add dynamic data. Maybe Model.php could have a place to put them.
        if(!Follow::$hooked) {
            ao()->filter('ao_model_process_' . Follow::$table . '_data', [$this, 'process']);
            Follow::$hooked = true;
        }


        // May want to look at using hooks instead of __construct().
        parent::__construct($args);
    }   

    public function process($data) {
        $data['collection'] = Collection::find($data['collection_id'])->data;

        return $data;
    }
}
