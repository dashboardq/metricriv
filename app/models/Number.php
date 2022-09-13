<?php

namespace app\models;

use mavoc\core\Model;

class Number extends Model {
    public static $table = 'numbers';
    public static $order = ['name' => 'asc'];
}
