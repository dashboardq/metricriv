<?php

// Up
$up = function($db) {
    $sql = <<<'SQL'
CREATE TABLE `trackings` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint unsigned NOT NULL DEFAULT '0',
    `number_id` bigint unsigned NOT NULL DEFAULT '0',
    `collection_id` bigint unsigned NOT NULL DEFAULT '0',
    `connection_id` bigint unsigned NOT NULL DEFAULT '0',
    `name` varchar(255) NOT NULL DEFAULT '',
    `status` varchar(255) NOT NULL DEFAULT '',
    `method` varchar(255) NOT NULL DEFAULT '',
    `check_interval` varchar(255) NOT NULL DEFAULT '1 hour',
    `data` longtext NOT NULL,
    `encrypted` tinyint(1) NOT NULL DEFAULT '0',
    `priority` int NOT NULL DEFAULT '0',
    `next_check_at` timestamp NULL DEFAULT NULL,
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
DROP TABLE `trackings`;
SQL;

    $db->query($sql);
};
