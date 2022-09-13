
    $name = '{{number_name}}';
    $short_name = '{{number_short_name}}';
    $number_slug = '{{number_slug}}';
    $premium_level = {{number_premium_level}};
    $user_ids = '{{number_user_ids}}';
    $needs_connection = {{number_needs_connection}}; // 0 = no connection needed, 1 = api key based connection, 2 = oauth based connection

    $sql = <<<'SQL'
INSERT INTO `numbers` SET `category_id` = ?, `name` = ?, `short_name` = ?, `slug` = ?, `user_ids` = ?, `needs_connection` = ?, `data` = '', `created_at` = NOW(), `updated_at` = NOW();
SQL;
    $db->query($sql, $category_id, $name, $short_name, $number_slug, $user_ids, $needs_connection);

