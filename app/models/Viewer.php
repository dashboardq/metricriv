<?php

namespace app\models;

use mavoc\core\Model;

use DateTime;

class Viewer extends Model {
    public static $table = 'viewers';
    // Only need to set when the model has dynamic data.
    // Make sure to update when migration changes columns.
    // TODO: Maybe have migration files structured in a way so that columns can be pulled dynamically.
    public static $columns = [
        'id',
        'user_id',
        'viewer_id',
        'collection_id',
        'type',
        'username',
        'created_at',
        'updated_at',
    ];

    public static $hooked = false;

    public function __construct($args) {
        if(!self::$hooked) {
            ao()->filter('ao_model_process_' . self::$table . '_data', [$this, 'process']);
            self::$hooked = true;
        }

        // May want to look at using hooks instead of __construct().
        parent::__construct($args);
    }

    public function process($data) {
        $data['collection'] = Collection::find($data['collection_id'])->data;
        $created_at = new DateTime($data['created_at'] ?? '');
        $data['created'] = $created_at->format('M j, Y H:i');
        $updated_at = new DateTime($data['updated_at'] ?? '');
        $data['updated'] = $updated_at->format('M j, Y H:i');

        $data['type_fmt'] = ($data['type'] == 'editor') ? 'Viewer & Editor' : 'Viewer';

        return $data;
    }
}
