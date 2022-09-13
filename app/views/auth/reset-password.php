<!DOCTYPE html>                
<html>
    <head>                     
        <?php $res->partial('head'); ?>
    </head>
    <body class="<?php $res->pathClass(); ?>">
        <?php $res->partial('header'); ?>
        <main>
            <section class="box">
                <?php $res->html->messages(); ?>

                <section class="page reset_password">
                    <h2><?php esc($title); ?></h2>
                    <form method="POST">
                        <?php $res->html->hidden('user_id', $user_id); ?>
                        <?php $res->html->hidden('token', $token); ?>

                        <?php $res->html->password('New Password'); ?>

                        <?php $res->html->submit('Submit', 'button button_invert'); ?>
                    </form>
                </section>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
		<?php $res->partial('foot'); ?>
    </body>
</html>

