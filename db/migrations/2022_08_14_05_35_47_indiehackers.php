<?php

// Up
$up = function($db) {
    $slug = 'indiehackers';
    $category_name = 'IndieHackers';
    $premium_level = 10;
    $user_ids = '';

    $sql = <<<'SQL'
SELECT `id` FROM `categories` WHERE `slug` = ? LIMIT 1;
SQL;
    $results = $db->query($sql, $slug);

    if(count($results)) {
        $category_id = $results[0]['id'];
    } else {

        $sql = <<<'SQL'
INSERT INTO `categories` SET `name` = ?, `slug` = ?, `user_ids` = ?, `premium_level` = ?, `created_at` = NOW(), `updated_at` = NOW();
SQL;
        $db->query($sql, $category_name, $slug, $user_ids, $premium_level);

        $category_id = $db->lastInsertId();
    }



    
    $name = 'Total Followers';
    $short_name = 'IH: Followers';
    $number_slug = 'total-followers';
    $premium_level = 10;
    $user_ids = '';
    $needs_connection = 0; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'Total Points';
    $short_name = 'IH: Points';
    $number_slug = 'total-points';
    $premium_level = 10;
    $user_ids = '';
    $needs_connection = 0; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);



};
    

// Down
$down = function($db) {
    $slug = 'indiehackers';

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
