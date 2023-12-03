<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Numbers: <?php esc($collection->data['title']); ?></title>

        <link rel="preload" href="/assets/fonts/feather.woff2" as="font" crossorigin="anonymous" />
        <link href="/assets/css/normalize.css" rel="stylesheet">
		<link href="/assets/css/base.css" rel="stylesheet">
        <link href="/assets/css/main.css" rel="stylesheet">
        <link href="/assets/css/page.css" rel="stylesheet">
    </head>
    <body>
        <?php $res->partial('header_app'); ?>
        <main>
            <h1>Numbers: <?php esc($collection->data['title']); ?></h1>

            <section class="page">
                <p class="desc"><a href="<?php url('/collections'); ?>">&lt; Back</a></p>
                <?php ao()->hook('app_html_numbers_page'); ?>
                <p>
                You can view your numbers here:<br>
                <a href="<?php esc(ao()->env('APP_SITE') . $collection->data['slug']); ?>"><?php esc(ao()->env('APP_SITE') . $collection->data['slug']); ?></a><br>
                </p>
                <?php $res->html->messages(); ?>
                <p><a href="<?php url('/number/add/' . $collection->data['id']); ?>" class="button button_invert">Add Number</a> <a href="<?php url('/collection/sort-order/' . $collection->data['id']); ?>" class="button button_invert">Edit Sort Order</a></p>
                <table class="draggable" data-action="<?php url('/ajax/collection/sort/' . $collection->id); ?>">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Connection</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $item): ?>
                        <tr draggable="true" data-id="<?php esc($item['id']); ?>">
                            <td data-label="Sort">
                                <button class="button button_invert sort_drag"><img src="/assets/images/tabler-icons/selector-ffffff.svg" alt="Drag Sort" draggable="false" /></button>
                                <button class="button button_invert sort_up sort_item"><img src="/assets/images/tabler-icons/chevron-up-ffffff.svg" alt="Sort Up" draggable="false" /></button>
                                <button class="button button_invert sort_down sort_item"><img src="/assets/images/tabler-icons/chevron-down-ffffff.svg" alt="Sort Down" draggable="false" /></button>
                            </td>
                            <td data-label="Name"><a href="<?php url($collection->data['slug']); ?>"><?php esc($item['title']); ?></a></td>
                            <td data-label="Category"><?php esc($item['category']['name']); ?></td>
                            <td data-label="Type"><?php esc($item['number']['short_name']); ?></td>
                            <td data-label="Connection"><?php esc($item['connection']['values']['name'] ?? ''); ?></td>
                            <td data-label="Status">
                                <?php if($item['check_interval'] != 'static'): ?>
                                <?php esc($item['status']); ?>
                                <?php endif; ?>
                            </td>
                            <td data-label="Last Updated">
                                <?php if($item['check_interval'] != 'static'): ?>
                                <?php esc($item['updated']); ?>
                                <?php endif; ?>
                            </td>
                            <td data-label="Actions">
                                <?php $res->html->link('/number/edit/' . $item['id'], 'Edit', 'button button_invert'); ?>
                                
                                <?php $res->html->post('/number/copy/' . $item['id'], 'Copy', 'button button_invert'); ?>
                                <?php $res->html->delete('/number/delete/' . $item['id'], 'Delete', 'button button_invert'); ?>

<?php /*
                                <form action="/number/delete/<?php esc($item['id']); ?>" method="POST">
                                    <?php $res->html->submit('Delete', 'button button_invert'); ?>
                                </form>
*/ ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
		<?php $res->partial('footer'); ?>
        <script src="/assets/js/drag-tr.js"></script>
    </body>
</html>
