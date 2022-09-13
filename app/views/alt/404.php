<!DOCTYPE html>                
<html>
    <head>                     
        <?php $res->partial('head'); ?>
    </head>
    <body class="<?php $res->pathClass(); ?>">
        <?php $res->partial('header'); ?>
        <main>
            <h1><?php esc($title); ?></h1>
            <section class="page">
				<?php $res->html->messages(); ?>
                <p>There was a problem accessing the requested page.</p> 
            </section>
        </main>
		<?php $res->partial('footer'); ?>
		<?php $res->partial('foot'); ?>
    </body>
</html>

