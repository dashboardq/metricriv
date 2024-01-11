<?php

// Up
$up = function($db) {
    // Disable the old extras
    $old_slug = 'numbersq-calculations';
    $sql = <<<'SQL'
UPDATE `categories` SET `active` = 0 WHERE `slug` = ?;
SQL;
    $results = $db->query($sql, $old_slug);


    $slug = 'metric-calculations';
    $category_name = 'Metric Calculations';
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



    
    $name = 'Fixed Number';
    $short_name = 'Fixed';
    $number_slug = 'fixed';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 0; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);

    
    $name = 'Math Operation';
    $short_name = 'Results';
    $number_slug = 'math-operation';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 0; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);

    
    $name = 'Current Day Number';
    $short_name = 'Current Day';
    $number_slug = 'current-day';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 0; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'Days In Period';
    $short_name = 'Days In Period';
    $number_slug = 'days-period';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 0; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);

};

// Down
$down = function($db) {
    $slug = 'metric-calculations';

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
    
    // Enable the old extras
    $old_slug = 'numbersq-calculations';
    $sql = <<<'SQL'
UPDATE `categories` SET `active` = 1 WHERE `slug` = ?;
SQL;
    $results = $db->query($sql, $old_slug);
};
