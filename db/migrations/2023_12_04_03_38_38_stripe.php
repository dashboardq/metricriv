<?php

// Up
$up = function($db) {
    $slug = 'stripe';

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
UPDATE `numbers` SET `active` = 0 WHERE `slug` = 'monthly-revenue' AND `category_id` = ?;
SQL;
    $db->query($sql, $category_id);
};

// Down
$down = function($db) {
    $slug = 'stripe';

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
UPDATE `numbers` SET `active` = 1 WHERE `slug` = 'monthly-revenue' AND `category_id` = ?;
SQL;
    $db->query($sql, $category_id);
};
