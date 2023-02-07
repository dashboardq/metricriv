<?php

// Up
$up = function($db) {
    $slug = '{{class_slug}}';

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
