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


    $sql = <<<'SQL'
UPDATE `numbers` SET `active` = 0 WHERE `slug` = 'total-visitors' AND `category_id` = ?;
SQL;
    $db->query($sql, $category_id);
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
    } else {
        echo 'No category found';
        die;
    }


    $sql = <<<'SQL'
UPDATE `numbers` SET `active` = 1 WHERE `slug` = 'total-visitors' AND `category_id` = ?;
SQL;
    $db->query($sql, $category_id);
};
