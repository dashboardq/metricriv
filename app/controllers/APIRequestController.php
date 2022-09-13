<?php

namespace app\controllers;

use app\models\Request;
use app\models\Vote;

class APIRequestController {
    public function count($req, $res) {
        return ['count' => Request::count()];
    }

    public function countVotes($req, $res) {
        return ['count' => Vote::count()];
    }
}
