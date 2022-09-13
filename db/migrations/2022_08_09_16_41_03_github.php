<?php

// Up
$up = function($db) {
    $slug = 'github';
    $category_name = 'Github';

    $sql = <<<'SQL'
SELECT `id` FROM `categories` WHERE `slug` = ? LIMIT 1;
SQL;
    $results = $db->query($sql, $slug);

    if(count($results)) {
        $category_id = $results[0]['id'];
    } else {

        $sql = <<<'SQL'
INSERT INTO `categories` SET `name` = ?, `slug` = ?, `created_at` = NOW(), `updated_at` = NOW();
SQL;
        $db->query($sql, $category_name, $slug);

        $category_id = $db->lastInsertId();
    }



    $name = 'Notifications';
    $short_name = 'GH: Notifications';
    $number_slug = 'notifications';
    $needs_connection = 2; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $needs_connection);


};
    

// Down
$down = function($db) {
    $slug = 'github';

    $sql = <<<'SQL'
SELECT `id` FROM `categories` WHERE `slug` = ? LIMIT 1;
SQL;
    $results = $db->query($sql, $slug);

    if(count($results)) {
        $category_id = $results[0]['id'];

        $sql = <<<'SQL'
DELETE FROM `numbers` WHERE `category_id` = ?;
SQL;
        $db->query($sql, $category_id);

        $sql = <<<'SQL'
DELETE FROM `categories` WHERE `id` = ?;
SQL;
        $db->query($sql, $category_id);
    }
};
