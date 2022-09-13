<?php

// Up
$up = function($db) {
    $sql = <<<'SQL'
CREATE TABLE `requests` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `author_id` bigint unsigned NOT NULL DEFAULT '0',
    `title` varchar(255) NOT NULL DEFAULT '',
    `content` text,
    `score` int NOT NULL DEFAULT '0',
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
DROP TABLE `requests`;
SQL;

    $db->query($sql);
};
