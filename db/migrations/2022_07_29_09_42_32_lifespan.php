<?php

// Up
$up = function($db) {
    $sql = <<<'SQL'
SELECT `id` FROM `categories` WHERE `slug` = 'lifespan' LIMIT 1;
SQL;
    $results = $db->query($sql);

    if(count($results)) {
        $category_id = $results[0]['id'];
    } else {

        $sql = <<<'SQL'
INSERT INTO `categories` SET `name` = 'Lifespan', `slug` = 'lifespan', `created_at` = NOW(), `updated_at` = NOW();
SQL;
        $db->query($sql);

        $category_id = $db->lastInsertId();
    }



    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Alligator Lifespan', `short_name` = 'Avg Alligator Life', `slug` = 'avg-alligator-life', `data` = '30-50 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Bear Lifespan', `short_name` = 'Avg Bear Life', `slug` = 'avg-bear-life', `data` = '10-20 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Beaver Lifespan', `short_name` = 'Avg Beaver Life', `slug` = 'avg-beaver-life', `data` = '10-12 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Bison Lifespan', `short_name` = 'Avg Bison Life', `slug` = 'avg-bison-life', `data` = '10-20 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Cow Lifespan', `short_name` = 'Avg Cow Life', `slug` = 'avg-cow-life', `data` = '15-20 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Elephant Lifespan', `short_name` = 'Avg Elephant Life', `slug` = 'avg-elephant-life', `data` = '60-70 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Dolphin Lifespan', `short_name` = 'Avg Dolphin Life', `slug` = 'avg-dolphin-life', `data` = '10-20 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Frog Lifespan', `short_name` = 'Avg Frog Life', `slug` = 'avg-frog-life', `data` = '2-10 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Giraffe Lifespan', `short_name` = 'Avg Giraffe Life', `slug` = 'avg-giraffe-life', `data` = '10-15 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Horse Lifespan', `short_name` = 'Avg Horse Life', `slug` = 'avg-horse-life', `data` = '25-30 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Lion Lifespan', `short_name` = 'Avg Lion Life', `slug` = 'avg-lion-life', `data` = '10-14 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Octopus Lifespan', `short_name` = 'Avg Octopus Life', `slug` = 'avg-octopus-life', `data` = '1-5 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Rabbit Lifespan', `short_name` = 'Avg Rabbit Life', `slug` = 'avg-rabbit-life', `data` = '3-8 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Rhinoceros Lifespan', `short_name` = 'Avg Rhino Life', `slug` = 'avg-rhino-life', `data` = '35-50 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Shark Lifespan', `short_name` = 'Avg Shark Life', `slug` = 'avg-shark-life', `data` = '20-30 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Squirrel Lifespan', `short_name` = 'Avg Squirrel Life', `slug` = 'avg-squirrel-life', `data` = '5-10 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Tiger Lifespan', `short_name` = 'Avg Tiger Life', `slug` = 'avg-tiger-life', `data` = '8-10 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Turtle Lifespan', `short_name` = 'Avg Turtle Life', `slug` = 'avg-turtle-life', `data` = '10-80 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);
    

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Tortoise Lifespan', `short_name` = 'Avg Tortoise Life', `slug` = 'avg-tortoise-life', `data` = '80-150 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Whale Lifespan', `short_name` = 'Avg Whale Life', `slug` = 'avg-whale-life', `data` = '40-70 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Wolf Lifespan', `short_name` = 'Avg Wolf Life', `slug` = 'avg-wolf-life', `data` = '5-12 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);
    

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Worm Lifespan', `short_name` = 'Avg Worm Life', `slug` = 'avg-worm-life', `data` = '4-8 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);


    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = 'Average Zebra Lifespan', `short_name` = 'Avg Zebra Life', `slug` = 'avg-zebra-life', `data` = '20-30 yrs', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id);
};
    

// Down
$down = function($db) {
    $sql = <<<'SQL'
SELECT `id` FROM `categories` WHERE `slug` = 'lifespan' LIMIT 1;
SQL;
    $results = $db->query($sql);

    if(count($results)) {
        $category_id = $results[0]['id'];

        $sql = <<<'SQL'
DELETE FROM `numbers` WHERE `category_id` = ?;
SQL;
        $db->query($sql, $category_id);

        $sql = <<<'SQL'
DELETE FROM `categories` WHERE `id` = ?;
SQL;
        $db->query($sql, $category_id);
    }
};
