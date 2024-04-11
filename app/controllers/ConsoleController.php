<?php

namespace app\controllers;

use app\models\Tracking;

use app\services\TrackingService;

class ConsoleController {
    public function rsync($in, $out) {
        $error = '';
        $success = '';
        $servers = ao()->env('RSYNC_SERVERS');
        $sources = [];
        $destinations = [];

        if(isset($in->params[0]) && isset($in->params[1])) {
            $server = $in->params[0];
            if(isset($servers[$server])) {
                if(in_array($in->params[1], ['db'])) {
                    $dir = $in->params[1];
                    $sources[] = ao()->env('AO_DB_DIR') . '/';
                    $destinations[] = $servers[$server] . '/' . $dir . '/';
                } else {
                    $error = 'Please enter a valid directory like "db".';
                }
            } else {
                $error = 'The server entered is not valid.';
            }
        } elseif(isset($in->params[0])) {
            $server = $in->params[0];
            if(isset($servers[$server])) {
                $sources[] = ao()->env('AO_APP_DIR') . '/';
                $destinations[] = $servers[$server] . '/' . 'app' . '/';
                $sources[] = ao()->env('AO_DB_DIR') . '/';
                $destinations[] = $servers[$server] . '/' . 'db' . '/';
                $sources[] = ao()->env('AO_MAVOC_DIR') . '/';
                $destinations[] = $servers[$server] . '/' . 'mavoc' . '/';
                $sources[] = ao()->env('AO_PLUGIN_DIR') . '/';
                $destinations[] = $servers[$server] . '/' . 'plugins' . '/';
                $sources[] = ao()->env('AO_PUBLIC_DIR') . '/';
                $destinations[] = $servers[$server] . '/' . 'public' . '/';
            } else {
                $error = 'The server entered is not valid.';
            }
        } else {
            $error = 'Please include a server like "prod".';
        }
        if(count($sources)) {
            foreach($sources as $i => $source) {
                $destination = $destinations[$i];

                $out->write('rsync -avzh ' . $source . ' ' . $destination, 'green');
                $output = [];
                exec('rsync -avzh ' . $source . ' ' . $destination . ' 2>&1', $output, $exit_code);
                $out->write('exit_code: ' . $exit_code, 'green');
                $out->write(implode("\n", $output), 'green');
                $out->write('', 'green');
            }

            $success = 'The syncing is complete.';
        }

        if($error) {
            $out->write($error, 'red');
        }
        if($success) {
            $out->write($success, 'green');
        }
    }

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
