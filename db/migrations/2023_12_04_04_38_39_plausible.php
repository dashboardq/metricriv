<?php

// Up
$up = function($db) {
    $slug = 'plausible';

    $sql = <<<'SQL'
SELECT `id` FROM `categories` WHERE `slug` = ? LIMIT 1;
SQL;
    $results = $db->query($sql, $slug);

    if(count($results)) {
        $category_id = $results[0]['id'];
    } else {
        echo 'No category found';
        die;
    }

    
    $name = 'Visitors';
    $short_name = 'Visitors';
    $number_slug = 'visitors';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 1; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'Page Views';
    $short_name = 'Page Views';
    $number_slug = 'page-views';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 1; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'Visit Duration';
    $short_name = 'Visit Duration';
    $number_slug = 'visit-duration';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 1; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'Bounce Rate';
    $short_name = 'Bounce Rate';
    $number_slug = 'bounce-rate';
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
    $slug = 'plausible';

    $sql = <<<'SQL'
SELECT `id` FROM `categories` WHERE `slug` = ? LIMIT 1;
SQL;
    $results = $db->query($sql, $slug);

    if(count($results)) {
        // This should probably be set up to only delete specific numbers.
        // Skipping for now.
        /*
        $category_id = $results[0]['id'];

        $sql = <<<'SQL'
DELETE FROM `categories` WHERE `id` = ?;
SQL;
        $db->query($sql, $category_id);
         */
    }
};
