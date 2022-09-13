<?php

// Up
$up = function($db) {
    $slug = 'plausible';
    $category_name = 'Plausible';

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



    $name = 'Total Visitors';
    $short_name = 'Site: Visitors';
    $number_slug = 'total-visitors';
    $needs_connection = 1;

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $needs_connection);


};
    

// Down
$down = function($db) {
    $slug = 'plausible';

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
