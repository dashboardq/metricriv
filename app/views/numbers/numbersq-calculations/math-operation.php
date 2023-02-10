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
    <body class="page_other <?php echo ($other) ? 'page_other_active' : ''; ?>">
        <?php $res->partial('header_app'); ?>
        <main>
            <h1>Add A Number</h1>

            <section class="page">
                <p class="desc"><a href="<?php esc($back); ?>">&lt; Back</a></p>
                <?php $res->html->messages(); ?>

				<form method="POST">
					<?php $res->html->text('Number Display Name', 'name', $number->data['short_name']); ?>

					<?php $res->html->radios('Update Number Interval', 'interval', $intervals); ?>

                    <hr>

					<?php $res->html->select('Number In Collection', 'number_1', $extras['number_1']); ?>

					<?php $res->html->select('Operation', 'operation', $extras['operations']); ?>

					<?php $res->html->select('Number In Collection', 'number_2', $extras['number_2']); ?>

					<?php $res->html->select('Decimals', 'decimal', $extras['decimals']); ?>

					<?php $res->html->select('Format', 'format', $extras['formats']); ?>

					<?php $res->html->submit('Start Tracking', 'button button_invert'); ?>
				</form>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
