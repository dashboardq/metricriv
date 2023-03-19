<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Add A Number</title>

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
            <h1>Add A Number</h1>

            <section class="page">
                <p class="desc"><a href="<?php esc($back); ?>">&lt; Back</a></p>
                <?php $res->html->messages(); ?>

				<form method="POST">
					<?php $res->html->radios('Mailchimp List', 'list_id', $extras['radios']); ?>

                    <hr>

					<?php $res->html->text('Number Display Name', 'name', $number->data['short_name']); ?>

					<?php $res->html->radios('Update Number Interval', 'interval', $intervals); ?>
					<?php $res->html->hidden('priority', 0); ?>

					<?php $res->html->submit('Start Tracking', 'button button_invert'); ?>
				</form>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
