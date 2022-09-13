<?php

// Up
$up = function($db) {
    $sql = <<<'SQL'
CREATE TABLE `votes` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint unsigned NOT NULL DEFAULT '0',
    `request_id` bigint unsigned NOT NULL DEFAULT '0',
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
DROP TABLE `votes`;
SQL;

    $db->query($sql);
};
