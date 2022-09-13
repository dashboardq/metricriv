<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Numbers Being Tracked</title>

        <link rel="preload" href="/assets/fonts/feather.woff2" as="font" crossorigin="anonymous" />
        <link href="/assets/css/normalize.css" rel="stylesheet">
		<link href="/assets/css/base.css" rel="stylesheet">
        <link href="/assets/css/main.css" rel="stylesheet">
        <link href="/assets/css/page.css" rel="stylesheet">
    </head>
    <body>
        <?php $res->partial('header_app'); ?>
        <main>
            <h1>Numbers Being Tracked</h1>

            <section class="page">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Group</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php foreach($list as $item): ?>
                            <td><?php esc($item['name']); ?></td>
                            <td><?php esc($item['group']); ?></td>
                            <td><?php esc($item['status']); ?></td>
                            <td><?php esc($item['last_updated']); ?></td>
                            <td>
                                <form action="/number/delete/<?php esc($item['id']); ?>" method="POST">
                                    <?php $res->html->submit('Delete', 'button button_invert'); ?>
                                </form>
                            </td>
                            <?php endforeach; ?>
                    </tbody>
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><a href="/number/add" class="button button_invert">Add Number</a></th>
                        </tr>
                    </thead>
                </table>
            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
