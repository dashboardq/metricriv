<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Viewers</title>

        <link rel="preload" href="/assets/fonts/feather.woff2" as="font" crossorigin="anonymous" />
        <link href="/assets/css/normalize.css" rel="stylesheet">
		<link href="/assets/css/base.css" rel="stylesheet">
        <link href="/assets/css/main.css" rel="stylesheet">
        <link href="/assets/css/page.css" rel="stylesheet">
    </head>
    <body>
        <?php $res->partial('header_app'); ?>
        <main>
            <h1>Viewers</h1>

            <section class="page">
                <?php $res->html->messages(); ?>

                <p>Adding additional viewers is a premium feature. When you add a viewer, you are allowing that user to view or edit your collections.</p>
                <p><a href="/viewer/add" class="button button_invert">Add Viewer</a></p>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Type</th>
                            <th>Collection</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $item): ?>
                        <tr>
                            <td data-label="Username"><?php esc($item['username']); ?></td>
                            <td data-label="Type"><?php esc($item['type_fmt']); ?></td>
                            <td data-label="Collection"><?php esc($item['collection']['name']); ?></td>
                            <td data-label="Created"><?php esc($item['created']); ?></td>
                            <td data-label="Actions">
                                <?php $res->html->a('/viewer/edit/' . $item['id'], 'Edit', 'button button_invert'); ?>
                                <?php $res->html->delete('/viewer/delete/' . $item['id'], 'Delete', 'button button_invert', 'Deleting a viewer will remove access to the collection. Are you sure you want to proceed?'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(count($list) == 0): ?>
                        <tr>
                            <td data-label="Details" colspan="5">No additional viewers have been added.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
