<?php

// Up
$up = function($db) {
    $sql = <<<'SQL'
CREATE TABLE `categories` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL DEFAULT '',
    `slug` varchar(255) NOT NULL UNIQUE,
    `user_ids` varchar(255) NOT NULL DEFAULT '',
    `active` tinyint(1) NOT NULL DEFAULT '1',
    `custom` tinyint(1) NOT NULL DEFAULT '0',
    `premium_level` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
);
SQL;

    $db->query($sql);
};

// Down
$down = function($db) {
    $sql = <<<'SQL'
DROP TABLE `categories`;
SQL;

    $db->query($sql);
};
