<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Feature Requests</title>

        <link rel="preload" href="/assets/fonts/feather.woff2" as="font" crossorigin="anonymous" />
        <link href="/assets/css/normalize.css" rel="stylesheet">
		<link href="/assets/css/base.css" rel="stylesheet">
        <link href="/assets/css/main.css?cache-date=2022-08-17" rel="stylesheet">
        <link href="/assets/css/page.css" rel="stylesheet">
    </head>
    <body class="page_requests">
        <?php $res->partial('header_app'); ?>
        <main>
            <h1>Feature Requests</h1>

            <section class="page">
                <div class="notice -notice">
                    <p>This feature request section does not currently have an email notification system so you will need to check back from time to time to see any updates.</p>
                </div>
                <?php $res->html->messages(); ?>
                <table>
                    <thead>
                        <tr>
                            <th>Score</th>
                            <th>Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $item): ?>
                        <tr>
                            <td data-label="Score"><?php esc(number_format($item['score'])); ?></td>
                            <td data-label="Title"><a href="/request/view/<?php esc($item['id']); ?>"><?php esc($item['title']); ?></a></td>
                            <td data-label="Actions">
                                <?php if($req->user_id): ?>
                                <form action="<?php esc('/request/vote/' . $item['id']); ?>" method="POST">
                                <?php $res->html->hidden('direction', 'up'); ?>
                                <?php $res->html->submit('Vote Up', 'button button_invert'); ?>
                                </form>

                                <form action="<?php esc('/request/vote/' . $item['id']); ?>" method="POST">
                                <?php $res->html->hidden('direction', 'down'); ?>
                                <?php $res->html->submit('Vote Down', 'button button_invert'); ?>
                                </form>
                                <?php else: ?>
                                <?php $res->html->a('/login', 'Login', 'button button_invert'); ?>
                                <?php endif; ?>

                                <?php //if($item['author_id'] == $req->user_id): ?>
                                <?php //$res->html->delete('/request/delete/' . $item['id'], '', 'button button_invert'); ?>
                                <?php //endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2"></th>
                            <th colspan="1"><a href="/request/add" class="button button_invert">Add Feature Request</a></th>
                        </tr>
                    </tfoot>
                </table>
            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
