<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Edit Viewer</title>

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
            <h1>Edit Viewer</h1>

            <section class="page">
                <p class="desc"><a href="<?php url('/viewers'); ?>">&lt; Back</a></p>
                <?php $res->html->messages(); ?>

				<form method="POST">
					<?php $res->html->text('Username', 'username'); ?>

                    <?php $res->html->radios('Collection', 'collection_id', $collections); ?>

                    <?php $res->html->radios('Type', 'type', [
                        ['label' => 'View', 'value' => 'viewer'],
                        ['label' => 'View & Edit', 'value' => 'editor'],
                    ]); ?>

					<?php $res->html->submit('Update Viewer', 'button button_invert'); ?>
				</form>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
