<?php

// Up
$up = function($db) {
    // Disable services where the API no longer works
    $slug = 'indiehackers';
    $sql = <<<'SQL'
UPDATE `categories` SET `active` = 0 WHERE `slug` = ?;
SQL;
    $results = $db->query($sql, $slug);
    

    $slug = 'twitter';
    $sql = <<<'SQL'
UPDATE `categories` SET `active` = 0 WHERE `slug` = ?;
SQL;
    $results = $db->query($sql, $slug);
};

// Down
$down = function($db) {
    // Enable services 
    $slug = 'indiehackers';
    $sql = <<<'SQL'
UPDATE `categories` SET `active` = 1 WHERE `slug` = ?;
SQL;
    $results = $db->query($sql, $slug);
    

    $slug = 'twitter';
    $sql = <<<'SQL'
UPDATE `categories` SET `active` = 1 WHERE `slug` = ?;
SQL;
    $results = $db->query($sql, $slug);
};
