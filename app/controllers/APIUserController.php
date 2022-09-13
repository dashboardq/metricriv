<?php

namespace app\controllers;

use app\models\User;

class APIUserController {
    public function count($req, $res) {
        return ['count' => User::count()];
    }
}
