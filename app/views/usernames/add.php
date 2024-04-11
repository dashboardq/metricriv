<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Add Username</title>

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
            <h1>Add Username</h1>

            <section class="page">
                <p>Please choose a username. The username will be used to access your numbers. For example:</p>
                <ul>
                    <li><?php esc(ao()->env('APP_SITE')) ?>/<strong>Your_Username_Here</strong> (Your public numbers)</li>
                    <li><?php esc(ao()->env('APP_SITE')) ?>/<strong>Your_Username_Here</strong>/private (Your private numbers)</li>
                </ul>

                <?php $res->html->messages(); ?>
                <form action="/username/create" method="POST">
                    <?php $res->html->text('Username', 'name'); ?>
                    <?php $res->html->submit('Add', 'button button_invert'); ?>
                </form>
            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
