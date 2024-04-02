<?php

namespace app\controllers;

use app\models\Tracking;

use app\services\TrackingService;

class ConsoleController {
    public function track($in, $out) {
        $force = false;
        //out('wait_timeout: ' . print_r(ao()->db->query('SHOW VARIABLES LIKE "%timeout"'), true));
        //out('net_read_timeout: ' . print_r(ao()->db->query('SHOW VARIABLES LIKE "net_read_timeout"'), true));
        //out('wait_timeout: ' . print_r(ao()->db->query('SHOW VARIABLES LIKE "wait_timeout"'), true));
        ao()->db->query('SET SESSION wait_timeout = 28800');
        //out('net_read_timeout: ' . print_r(ao()->db->query('SHOW VARIABLES LIKE "net_read_timeout"'), true));
        //out('wait_timeout: ' . print_r(ao()->db->query('SHOW VARIABLES LIKE "wait_timeout"'), true));
        if(isset($in->params[0])) {
            $tracking_id = $in->params[0];
            //$trackings = ao()->db->query('SELECT id FROM trackings WHERE next_check_at IS NOT NULL AND next_check_at <= ? AND id = ?', now(), $tracking_id);
            $trackings = ao()->db->query('SELECT id FROM trackings WHERE next_check_at IS NOT NULL AND id = ?', $tracking_id);
            $force = true;
        } else {
            $trackings = ao()->db->query('SELECT id FROM trackings WHERE next_check_at IS NOT NULL AND next_check_at <= ?', now());
        }

        foreach($trackings as $data) {
            if($force) {
                TrackingService::update($data['id'], null, true);
            } else {
                TrackingService::update($data['id']);
            }
            out('Processed: ' . $data['id'], 'green');
        }

        if(count($trackings) == 0) {
            out('Nothing to process.', 'red');
        }
    }
}
