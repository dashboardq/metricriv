<!DOCTYPE html>                
<html>
    <head>                     
        <?php $res->partial('head'); ?>
    </head>
    <body class="<?php $res->pathClass(); ?>">
        <?php $res->partial('header_app'); ?>
        <main>
            <h1><?php esc($title); ?></h1>
            <section class="page">
                <p class="desc"><a href="/account">&lt; Back</a></p>

                <?php if(ao()->env('APP_LOGIN_TYPE') == 'db'): ?>
                    <?php $res->html->messages(); ?>
                    <form method="POST">
                        <?php $res->html->password('Old Password'); ?>

                        <?php $res->html->password('New Password'); ?>

                        <?php $res->html->submit('Update', 'button button_invert'); ?>
                    </form>
                <?php else: ?>
                    <p>The password cannot be changed on the current system.</p>
                <?php endif; ?>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
		<?php $res->partial('foot'); ?>
    </body>
</html>

