<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Collections</title>

        <link rel="preload" href="/assets/fonts/feather.woff2" as="font" crossorigin="anonymous" />
        <link href="/assets/css/normalize.css" rel="stylesheet">
		<link href="/assets/css/base.css" rel="stylesheet">
        <link href="/assets/css/main.css" rel="stylesheet">
        <link href="/assets/css/page.css" rel="stylesheet">
    </head>
    <body class="<?php $res->pathClass(); ?>">
        <?php $res->partial('header_app'); ?>
        <main>
            <h1>Collections</h1>

            <section class="page">
                <?php ao()->hook('app_html_collections_page'); ?>
                <?php $res->html->messages(); ?>
                <p><a href="/collection/add" class="button button_invert">Add Collection</a></p>

                <h2>Your Collections</h2>
                <p>A collection is a group of numbers that are displayed on one page.</p>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>URL</th>
                            <th>Visibility</th>
                            <th>Numbers</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($owns as $item): ?>
                        <tr>
                            <td data-label="Name"><a href="<?php url('/collection/view/' . $item['id']); ?>"><?php esc($item['title']); ?></a></td>
                            <td data-label="URL"><a href="<?php url($item['slug']); ?>"><?php url($item['slug']); ?></a></td>
                            <td data-label="Visibility"><?php esc($item['private'] ? 'Private' : 'Public'); ?></td>
                            <td data-label="Numbers"><?php esc($item['numbers']); ?></td>
                            <td data-label="Actions">
                                <?php $res->html->link('/number/add/' . $item['id'], 'Add Number', 'button button_invert'); ?>
                                <?php $res->html->link('/collection/edit/' . $item['id'], 'Edit', 'button button_invert'); ?>
                                <?php $res->html->delete('/collection/delete/' . $item['id'], 'Delete', 'button button_invert'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if(count($edits)): ?>
                <h2>Editable Collections</h2>
                <p></p>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>URL</th>
                            <th>Visibility</th>
                            <th>Numbers</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($edits as $item): ?>
                        <tr>
                            <td data-label="Name"><a href="<?php url('/collection/view/' . $item['id']); ?>"><?php esc($item['title']); ?></a></td>
                            <td data-label="URL"><a href="<?php url($item['slug']); ?>"><?php url($item['slug']); ?></a></td>
                            <td data-label="Visibility"><?php esc($item['private'] ? 'Private' : 'Public'); ?></td>
                            <td data-label="Numbers"><?php esc($item['numbers']); ?></td>
                            <td data-label="Actions">
                                <?php $res->html->link('/number/add/' . $item['id'], 'Add Number', 'button button_invert'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

                <?php if(count($views)): ?>
                <h2>Viewable Collections</h2>
                <p></p>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>URL</th>
                            <th>Visibility</th>
                            <th>Numbers</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($views as $item): ?>
                        <tr>
                            <td data-label="Name"><?php esc($item['title']); ?></td>
                            <td data-label="URL"><a href="<?php url($item['slug']); ?>"><?php url($item['slug']); ?></a></td>
                            <td data-label="Visibility"><?php esc($item['private'] ? 'Private' : 'Public'); ?></td>
                            <td data-label="Numbers"><?php esc($item['numbers']); ?></td>
                            <td data-label="Actions">
                                No Actions Available
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
