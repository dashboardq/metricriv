<?php

// Up
$up = function($db) {
    $sql = <<<'SQL'
CREATE TABLE `connections` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint unsigned NOT NULL DEFAULT '0',
    `category_id` bigint unsigned NOT NULL DEFAULT '0',
    `data` text NOT NULL,
    `encrypted` tinyint(1) NOT NULL DEFAULT '0',
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
DROP TABLE `connections`;
SQL;

    $db->query($sql);
};
