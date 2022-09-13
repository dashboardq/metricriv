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
                <?php ao()->hook('app_html_numbers_page'); ?>
                <p>
                You can view your numbers here:<br>
                <?php foreach($collections as $collection): ?>
                    <a href="<?php esc(ao()->env('APP_SITE') . $collection->data['slug']); ?>"><?php esc(ao()->env('APP_SITE') . $collection->data['slug']); ?></a><br>
                <?php endforeach; ?>
                </p>
                <?php $res->html->messages(); ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Visibility</th>
                            <th>Category</th>
                            <th>Connection</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $item): ?>
                        <tr>
                            <td data-label="Name"><a href="<?php uri($item['collection']['slug']); ?>"><?php esc($item['title']); ?></a></td>
                            <td data-label="Visibility"><a href="<?php uri($item['collection']['slug']); ?>"><?php esc($item['collection']['name']); ?></a></td>
                            <td data-label="Category"><?php esc($item['category']['name']); ?></td>
                            <td data-label="Connection"><?php esc($item['connection']['values']['name'] ?? ''); ?></td>
                            <td data-label="Status"><?php esc($item['status']); ?></td>
                            <td data-label="Last Updated"><?php esc($item['updated']); ?></td>
                            <td data-label="Actions">
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
                    <tfoot>
                        <tr>
                            <th><a href="/number/add" class="button button_invert">Add Number</a></th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                </table>
            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
