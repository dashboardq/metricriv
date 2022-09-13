<?php

// Up
$up = function($db) {
    $slug = '{{class_slug}}';
    $category_name = '{{class_name}}';
    $premium_level = {{class_premium_level}};
    $user_ids = '{{class_user_ids}}';

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



    {{numbers}}

};
    

// Down
$down = function($db) {
    $slug = '{{class_slug}}';

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
