<?php

namespace app\controllers;

use app\models\Tracking;

class APITrackingController {
    public function count($req, $res) {
        return ['count' => Tracking::count()];
    }
}
