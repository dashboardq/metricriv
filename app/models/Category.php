<?php

namespace app\models;

use mavoc\core\Model;

class Category extends Model {
    public static $table = 'categories';
    public static $order = ['name' => 'asc'];
}
