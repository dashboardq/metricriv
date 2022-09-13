<?php

namespace app\models;

use mavoc\core\Model;

class Comment extends Model {
    public static $table = 'comments';
    public static $order = ['id' => 'asc'];
}
