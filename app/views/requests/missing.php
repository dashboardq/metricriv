<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Missing Feature</title>

        <link rel="preload" href="/assets/fonts/feather.woff2" as="font" crossorigin="anonymous" />
        <link href="/assets/css/normalize.css" rel="stylesheet">
		<link href="/assets/css/base.css" rel="stylesheet">
        <link href="/assets/css/main.css" rel="stylesheet">
        <link href="/assets/css/page.css" rel="stylesheet">
        <link href="/assets/css/form.css" rel="stylesheet">
    </head>
    <body>
        <?php $res->partial('header_app'); ?>
        <main>
            <h1>Missing Feature</h1>

            <section class="page">
                <?php $res->html->messages(); ?>

                <?php if(ao()->hook('app_html_missing_p', true)): ?>
                <h3>Public Feature</h3>
                <p>If the feature you are requesting would be beneficial to other users (like a service that lots of people use), please add a new item to the <a href="/requests">Feature Request</a> page or upvote a feature that has already been submitted.</p>
                <?php endif; ?>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
