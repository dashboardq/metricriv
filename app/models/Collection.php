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

    public static function owns($user_id, $return_type = 'all') {
        $output = Collection::where('user_id', $user_id, $return_type);

        foreach($output as $i => $item) {
            if($return_type == 'data') {
                $output[$i]['numbers'] = Tracking::count('collection_id', $item['id']);
            } else {
                $output[$i]->data['numbers'] = Tracking::count('collection_id', $item->data['id']);
            }
        }

        return $output;
    }

    public static function edits($user_id, $return_type = 'all') {
        $editors = Viewer::where(['viewer_id' => $user_id, 'type' => 'editor'], $return_type);

        $ids = [];
        foreach($editors as $editor) {
            if($return_type == 'data') {
                $ids[] = $editor['collection_id'];
            } else {
                $ids[] = $editor->data['collection_id'];
            }
        }

        $output = Collection::whereIn('id', $ids, $return_type);

        foreach($output as $i => $item) {
            if($return_type == 'data') {
                $output[$i]['numbers'] = Tracking::count('collection_id', $item['id']);
            } else {
                $output[$i]->data['numbers'] = Tracking::count('collection_id', $item->data['id']);
            }
        }

        return $output;
    }

    public static function views($user_id, $return_type = 'all') {
        $viewers = Viewer::where(['viewer_id' => $user_id, 'type' => 'viewer'], $return_type);

        $ids = [];
        foreach($viewers as $viewer) {
            if($return_type == 'data') {
                $ids[] = $viewer['collection_id'];
            } else {
                $ids[] = $viewer->data['collection_id'];
            }
        }

        $output = Collection::whereIn('id', $ids, $return_type);

        foreach($output as $i => $item) {
            if($return_type == 'data') {
                $output[$i]['numbers'] = Tracking::count('collection_id', $item['id']);
            } else {
                $output[$i]->data['numbers'] = Tracking::count('collection_id', $item->data['id']);
            }
        }

        return $output;
    }

    public function access($user_id, $type = 'viewer') {
        if($this->data['user_id'] == $user_id) {
            return true;
        }

        if($type == 'viewer') {
            $counts = Viewer::count(['collection_id' => $this->data['id'], 'viewer_id' => $user_id]);
            if($counts) {
                return true;
            }
        } elseif($type == 'editor') {
            $counts = Viewer::count(['collection_id' => $this->data['id'], 'viewer_id' => $user_id, 'type' => 'editor']);
            if($counts) {
                return true;
            }
        }

        return false;
    }

    public function process($data) {
        $username = Username::find($data['username_id']);
        $data['title'] = $username->data['name'] . ' - ' . $data['name'];
        $data['slug_suffix'] = preg_replace('/^\/[^\/]+\/?/', '', $data['slug']);
        $data['visibility'] = (isset($data['private']) && $data['private']) ? 'Private' : 'Public';
        //$updated_at = new \DateTime($data['updated_at'] ?? '');
        //$data['updated'] = $updated_at->format('M j, Y H:i');

        return $data;
    }
}
