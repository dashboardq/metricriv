<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Choose Category</title>

        <link rel="preload" href="/assets/fonts/feather.woff2" as="font" crossorigin="anonymous" />
        <link href="/assets/css/normalize.css" rel="stylesheet">
		<link href="/assets/css/base.css" rel="stylesheet">
        <link href="/assets/css/main.css" rel="stylesheet">
        <link href="/assets/css/page.css" rel="stylesheet">
        <link href="/assets/css/form.css" rel="stylesheet">
    </head>
    <body class="add_number add_number_category">
        <?php $res->partial('header_app'); ?>
        <main>
            <h1>Choose Category</h1>

            <section class="page">
                <p class="desc"><a href="<?php esc($back); ?>">&lt; Back</a></p>
                <p>Please select a category below.</p>
                <?php $res->html->messages(); ?>
                
                <?php $disabled_count = 0; ?>
                <ul class="selections">
                    <li><a href="/missing">I'm not seeing a service I need...</a></li>
                    <?php foreach($list as $item): ?>
                    <?php if(
                        $item['user_ids'] 
                        && in_array($req->user_id, explode(',', $item['user_ids']))
                        && $item['premium_level'] <= $restriction['premium_level']
                    ): ?>
                    <li><a href="<?php esc($req->path . '/' . $item['slug']); ?>"><?php esc($item['name']); ?></a></li>
                    <?php elseif(
                        !$item['user_ids']
                        && $item['premium_level'] > $restriction['premium_level']
                    ): ?>
                    <?php $disabled_count++; ?>
                    <li class="disabled"><span><?php esc($item['name']); ?>*</span></li>
                    <?php elseif(!$item['user_ids']): ?>
                    <li><a href="<?php esc($req->path . '/' . $item['slug']); ?>"><?php esc($item['name']); ?></a></li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <?php if($disabled_count): ?>
                <p>* This is a premium category which has additional costs to process. Please upgrade your plan to access this category.</p>
                <?php endif; ?>
            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
