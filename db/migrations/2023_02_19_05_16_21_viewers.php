<?php

// Up
$up = function($db) {
    $sql = <<<'SQL'
CREATE TABLE `viewers` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint unsigned NOT NULL DEFAULT '0',
    `viewer_id` bigint unsigned NOT NULL DEFAULT '0',
    `collection_id` bigint unsigned NOT NULL DEFAULT '0',
    `type` varchar(255) NOT NULL DEFAULT 'viewer',
    `username` varchar(255) NOT NULL DEFAULT '',
    `premium_user` tinyint(1) NOT NULL DEFAULT '1',
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
DROP TABLE `viewers`;
SQL;

    $db->query($sql);
};
