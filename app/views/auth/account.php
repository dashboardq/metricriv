<!DOCTYPE html>                
<html>
    <head>                     
        <?php $res->partial('head'); ?>
    </head>
    <body class="<?php $res->pathClass(); ?>">
        <?php $res->partial('header_app'); ?>
        <main>
            <h1>Account</h1>
            <section class="page">

                <?php if(ao()->env('APP_LOGIN_TYPE') == 'db'): ?>
                    <?php $res->html->messages(); ?>
                    <form method="POST">
                        <?php $res->html->text('Full Name', 'name'); ?>

                        <?php $res->html->text('Email'); ?>
                        
                        <div>
                            <a href="/change-password">Change Password</a>
                        </div>

                        <?php $res->html->submit('Update', 'button button_invert'); ?>
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <?php $res->html->text('Email', '', '', '', 'disabled'); ?>
                    </form>
                <?php endif; ?>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
		<?php $res->partial('foot'); ?>
    </body>
</html>

