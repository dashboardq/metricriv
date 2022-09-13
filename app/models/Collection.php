<?php

namespace app\models;

use mavoc\core\Model;

class Collection extends Model {
    public static $table = 'collections';
    // Only need to set when the model has dynamic data.
    // Make sure to update when migration changes columns.
    // TODO: Maybe have migration files structured in a way so that columns can be pulled dynamically.
    // Or cache the columns on each migration up or down in settings/tables/collections.php.
    //   - Then hook to make sure the column protections can be modified.
    public static $columns = [
        'id',
        'user_id',
        'username_id',
        'name',
        'slug',
        'private',
        'created_at',
        'updated_at',
    ];

    public static $hooked = false;

    public function __construct($args) {

        // Only add the hook once (otherwise it gets added everytime compounding the calls.
        // TODO: Figure out a better way to add dynamic data. Maybe Model.php could have a place to put them.
        if(!Collection::$hooked) {
            ao()->filter('ao_model_process_' . Collection::$table . '_data', [$this, 'process']);
            Collection::$hooked = true;
        }

        // May want to look at using hooks instead of __construct().
        parent::__construct($args);
    }   

    public function process($data) {
        $username = Username::find($data['username_id']);
        $data['title'] = $username->data['name'] . ' ' . $data['name'];
        //$updated_at = new \DateTime($data['updated_at'] ?? '');
        //$data['updated'] = $updated_at->format('M j, Y H:i');

        return $data;
    }
}
