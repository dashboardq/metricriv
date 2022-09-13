<?php

// Up
$up = function($db) {
    $sql = <<<'SQL'
SELECT `id` FROM `categories` WHERE `slug` = 'hackernews' LIMIT 1;
SQL;
    $results = $db->query($sql);

    if(count($results)) {
        $category_id = $results[0]['id'];
    } else {

        $sql = <<<'SQL'
INSERT INTO `categories` SET `name` = 'HackerNews', `slug` = 'hackernews', `created_at` = NOW(), `updated_at` = NOW();
SQL;
        $db->query($sql);

        $category_id = $db->lastInsertId();
    }



    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Total Karma', `short_name` = 'HN: Total Karma', `slug` = 'total-karma', `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


};
    

// Down
$down = function($db) {
    $sql = <<<'SQL'
SELECT `id` FROM `categories` WHERE `slug` = 'hackernews' LIMIT 1;
SQL;
    $results = $db->query($sql);

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
