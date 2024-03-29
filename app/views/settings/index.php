<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Settings</title>

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
            <h1>Settings</h1>

            <section class="page">
                <?php $res->html->messages(); ?>

                <p>Please enter your settings below.</p>

				<form method="POST">
					<?php $res->html->select('Timezone', 'timezone', $timezones); ?>
					<?php $res->html->select('Start Of Week', 'week_start', $days); ?>

					<?php $res->html->submit('Save', 'button button_invert'); ?>
				</form>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
