<?php

// Up
$up = function($db) {
    $slug = 'wordpress';

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

    
    $name = 'WooCommerce Products';
    $short_name = 'Products';
    $number_slug = 'woo-products';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 1; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'WooCommerce Revenue';
    $short_name = 'Revenue';
    $number_slug = 'woo-revenue';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 1; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'WooCommerce Shipping Charges';
    $short_name = 'Shipping';
    $number_slug = 'woo-shipping';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 1; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'WooCommerce International Orders';
    $short_name = 'Int Orders';
    $number_slug = 'woo-orders-international';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 1; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'WooCommerce Repeat Customer Orders';
    $short_name = 'Repeat Orders';
    $number_slug = 'woo-orders-repeat';
    $premium_level = 0;
    $user_ids = '';
    $needs_connection = 1; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);


    $name = 'WooCommerce International Revenue';
    $short_name = 'Int Revenue';
    $number_slug = 'woo-revenue-international';
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
    $slug = 'wordpress';

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
