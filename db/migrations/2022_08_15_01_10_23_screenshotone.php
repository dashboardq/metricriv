<?php

// Up
$up = function($db) {
    $slug = 'screenshotone';
    $category_name = 'ScreenshotOne';
    $premium_level = 0;
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



    
    $name = 'Allotted Screenshots';
    $short_name = 'Allotted Screenshots';
    $number_slug = 'allotted-screenshots';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 1; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'Available Screenshots';
    $short_name = 'Available Screenshots';
    $number_slug = 'available-screenshots';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 1; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'Used Screenshots';
    $short_name = 'Used Screenshots';
    $number_slug = 'used-screenshots';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 1; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);



};
    

// Down
$down = function($db) {
    $slug = 'screenshotone';

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
