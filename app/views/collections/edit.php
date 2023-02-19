<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Edit Collection</title>

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
            <h1>Edit Collection</h1>

            <section class="page">
                <p class="desc"><a href="<?php url('/collections'); ?>">&lt; Back</a></p>
                <p><a href="<?php url('/collection/view/' . $item->id); ?>" class="button button_invert">Edit Numbers</a></p>
                <?php $res->html->messages(); ?>

				<form method="POST">
					<?php $res->html->text('Name', 'name', $item->data['name']); ?>

                    <div class="field">
                        <label>Slug</label>
                        <?php foreach($usernames as $username): ?>
                        <label><?php $res->html->radioRaw('username_id', $username['id']); ?><span><?php url(strtolower($username['name'])); ?>/</span><?php $res->html->textRaw('slug', 'slug_' . $username['id']); ?></label>
                        <?php endforeach; ?>
                    </div>

                    <?php $res->html->radios('Visibility', 'visibility', ['Public', 'Private']); ?>

					<?php $res->html->submit('Save', 'button button_invert'); ?>
				</form>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
