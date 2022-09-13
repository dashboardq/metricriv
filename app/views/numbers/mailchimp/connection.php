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
                <p>Please connect your account with Mailchimp below by entering your API key.</p>
                <?php $res->html->messages(); ?>

				<form method="POST">
                    <?php if(count($radios)): ?>
					<?php $res->html->radios('Choose Connection To Use', 'connection_id', $radios); ?>
                    <hr>
                    <?php else: ?>
					<?php $res->html->hidden('connection_id', 0); ?>
                    <?php endif; ?>

					<?php $res->html->text('Connection Nickname (for easy selection in the future)', 'name', 'Main'); ?>

					<?php $res->html->text('Mailchimp API Key', 'api_key', '', '', 'autocomplete="off"'); ?>

					<?php $res->html->submit('Continue', 'button button_invert'); ?>
				</form>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
