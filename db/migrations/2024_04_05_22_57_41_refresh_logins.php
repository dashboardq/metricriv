<?php

// Up
$up = function($db) {
    $sql = $db->createTable('refresh_logins', [
        'id' => 'id',
        'user_id' => 'id',
        'refresh_hash' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expired_at' => 'datetime',
    ]);

    $db->query($sql);
};

// Down
$down = function($db) {
    $sql = $db->dropTable('refresh_logins');
    $db->query($sql);
};
