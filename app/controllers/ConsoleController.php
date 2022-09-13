<?php

namespace app\controllers;

use app\models\Tracking;

use app\services\TrackingService;

class ConsoleController {
    public function track($in, $out) {
        $trackings = ao()->db->query('SELECT id FROM trackings WHERE next_check_at IS NOT NULL AND next_check_at <= ?', now());

        foreach($trackings as $data) {
            TrackingService::update($data['id']);
            out('Processed: ' . $data['id'], 'green');
        }

        if(count($trackings) == 0) {
            out('Nothing to process.', 'red');
        }
    }
}
