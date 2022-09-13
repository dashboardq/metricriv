<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Connections</title>

        <link rel="preload" href="/assets/fonts/feather.woff2" as="font" crossorigin="anonymous" />
        <link href="/assets/css/normalize.css" rel="stylesheet">
		<link href="/assets/css/base.css" rel="stylesheet">
        <link href="/assets/css/main.css" rel="stylesheet">
        <link href="/assets/css/page.css" rel="stylesheet">
    </head>
    <body>
        <?php $res->partial('header_app'); ?>
        <main>
            <h1>Connections</h1>

            <section class="page">
                <?php $res->html->messages(); ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $item): ?>
                        <tr>
                            <td data-label="Name"><?php esc($item['values']['name']); ?></td>
                            <td data-label="Type"><?php esc($item['category']['name']); ?></td>
                            <td data-label="Created"><?php esc($item['created']); ?></td>
                            <td data-label="Actions">
                                <?php $res->html->a('/connection/edit/' . $item['id'], 'Edit', 'button button_invert'); ?>
                                <?php $res->html->delete('/connection/delete/' . $item['id'], 'Delete', 'button button_invert', 'Deleting a connection will also delete the associated numbers. Are you sure you want to proceed?'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
