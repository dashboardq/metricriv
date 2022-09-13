<?php

// Up
$up = function($db) {
    $sql = <<<'SQL'
CREATE TABLE `comments` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `author_id` bigint unsigned NOT NULL DEFAULT '0',
    `request_id` bigint unsigned NOT NULL DEFAULT '0',
    `content` text,
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
DROP TABLE `comments`;
SQL;

    $db->query($sql);
};
