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
                <p class="desc"><a href="<?php url('/collection/view/' . $collection->id); ?>">&lt; Back</a></p>

                <p>The Sort Order is in descending order and the values are automatically readjusted to increments of 10 to allow for space to easily move numbers around.</p> 

                <?php $res->html->messages(); ?>
                <form method="POST">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Sort Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($list as $item): ?>
                            <tr>
                                <td data-label="Name"><a href="<?php url($collection->data['slug']); ?>"><?php esc($item['title']); ?></a></td>
                                <td data-label="Category"><?php esc($item['category']['name']); ?></td>
                                <td data-label="Sort Order">
                                    <?php $res->html->hidden('ids[]', $item['id']); ?>
                                    <?php $res->html->textRaw('Sort Order', 'sort_orders[]', $item['priority']); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="2"></td>
                                <td data-label="Action">
                                    <?php $res->html->submit('Update', 'button button_invert'); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </section>
        </main>
		<?php $res->partial('footer'); ?>
        <script src="/assets/js/drag-tr.js"></script>
    </body>
</html>
