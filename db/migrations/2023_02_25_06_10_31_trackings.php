<?php

// Up
$up = function($db) {
    $sql = <<<'SQL'
ALTER TABLE `trackings`
  ADD COLUMN `target_interval` varchar(255) NOT NULL DEFAULT 'auto'
SQL;

    $db->query($sql);
};

// Down
$down = function($db) {
    $sql = <<<'SQL'
ALTER TABLE `trackings`
  DROP COLUMN `target_interval`;
SQL;

    $db->query($sql);
};
