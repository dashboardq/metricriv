<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Add A Connection</title>

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
            <h1>Add A Connection</h1>

            <section class="page">
                <p class="desc"><a href="<?php esc($back); ?>">&lt; Back</a></p>
                <p>Please connect your account with Github below.</p>
                <?php $res->html->messages(); ?>

                <?php if(count($radios)): ?>
				<form method="POST">
					<?php $res->html->radios('Choose Connection To Use', 'connection_id', $radios); ?>
					<?php $res->html->submit('Continue', 'button button_invert'); ?>
				</form>
                <br>
                <?php endif; ?>

				<form action="/oauth/github/start" method="POST">
					<?php $res->html->hidden('path', $req->path); ?>
					<?php $res->html->submit('Create New Github Connection', 'button button_invert'); ?>
				</form>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
