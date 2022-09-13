<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Add Feature Request</title>

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
            <h1>Add Feature Request</h1>

            <section class="page">
                <?php $res->html->messages(); ?>

                <?php if(!$req->user): ?>
                <div class="notice -error">
                    <p>In order to add a feature request, you need to be logged in.</p>
                </div>
                <?php else: ?>
				<form method="POST">
					<?php $res->html->text('Title'); ?>

					<?php $res->html->textarea('Message'); ?>

					<?php $res->html->submit('Save', 'button button_invert'); ?>
				</form>
                <?php endif; ?>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
