<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?php esc($title); ?></title>

        <link rel="preload" href="/assets/fonts/feather.woff2" as="font" crossorigin="anonymous" />
        <link href="/assets/css/normalize.css" rel="stylesheet">
		<link href="/assets/css/base.css" rel="stylesheet">
        <link href="/assets/css/main.css" rel="stylesheet">
        <link href="/assets/css/page.css" rel="stylesheet">
        <link href="/assets/css/numbers.css?cache_date=2023-02-23" rel="stylesheet">
    </head>
    <body>
        <?php if($user): ?>
        <?php $res->partial('header_app'); ?>
        <?php else: ?>
        <?php $res->partial('header_button'); ?>
        <?php endif; ?>
        <main>
            <?php if($midnight): ?>
            <div class="notice error"><p>Between midnight and 1:00 am, the labels and numbers may not match or may not be accurate while everything is processed for the new day.</p></div>
            <?php endif; ?>
			<section class="numbers">
                <?php foreach($list as $item): ?>
                <?php if(isset($item->data['values']['type']) && $item->data['values']['type'] == 'hide'): ?>
                <?php break; ?>
                <?php elseif(isset($item->data['values']['type']) && $item->data['values']['type'] == 'header'): ?>
                <h2><?php esc($item->data['title']); ?></h2>
                <?php elseif(isset($item->data['values']['type']) && $item->data['values']['type'] == 'newline'): ?>
                <div class="newline"></div>
                <?php else: ?>
                <div class="number">
                    <h2><?php esc($item->data['values']['number']); ?></h2>
                    <label><?php esc($item->data['title']); ?></label>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
