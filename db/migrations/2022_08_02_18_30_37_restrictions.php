<?php

// Up
$up = function($db) {
    $sql = <<<'SQL'
CREATE TABLE `restrictions` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint unsigned NOT NULL DEFAULT '0',
    `public_max` int NOT NULL DEFAULT '100',
    `private_max` int NOT NULL DEFAULT '2',
    `username_max` int NOT NULL DEFAULT '1',
    `allowed_intervals` varchar(255) NOT NULL DEFAULT '',
    `additional_users` int NOT NULL DEFAULT '0',
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
DROP TABLE `restrictions`;
SQL;

    $db->query($sql);
};
