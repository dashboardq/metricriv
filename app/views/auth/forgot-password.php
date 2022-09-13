<!DOCTYPE html>                
<html>
    <head>                     
        <?php $res->partial('head'); ?>
    </head>
    <body class="<?php $res->pathClass(); ?>">
        <?php $res->partial('header'); ?>
        <main>
            <section class="box">

                <section class="page forgot_password">
                    <h2><?php esc($title); ?></h2>
                    <?php $res->html->messages(); ?>
                    <form method="POST">
                        <p>Please enter your email below to reset your password.</p>
                        <?php $res->html->text('Email'); ?>

                        <?php $res->html->submit('Submit', 'button button_invert'); ?>
                        
                        <div>
                            <a href="/login">&lt; Back to login</a>
                        </div>
                    </form>
                </section>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
		<?php $res->partial('foot'); ?>
    </body>
</html>

