<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Edit Number</title>

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
            <h1>Edit Number</h1>

            <section class="page">
                <p class="desc"><a href="<?php url('/collection/view/' . $tracking->data['collection_id']); ?>">&lt; Back</a></p>
                <?php $res->html->messages(); ?>

				<form method="POST">
					<?php $res->html->text('Name', 'name'); ?>

					<?php if(in_array($res->fields['check_interval'], ['5 minutes', '1 hour'])): ?>
                        <?php $res->html->radios('Update Number Interval', 'check_interval', $checks); ?>
                        <?php $res->html->select('Hourly Update Target Minute', 'target_interval', $targets); ?>
					<?php endif; ?>

					<?php $res->html->submit('Save', 'button button_invert'); ?>
				</form>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
