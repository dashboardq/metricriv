<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Add A Number</title>

        <link rel="preload" href="/assets/fonts/feather.woff2" as="font" crossorigin="anonymous" />
        <link href="/assets/css/normalize.css" rel="stylesheet">
		<link href="/assets/css/base.css" rel="stylesheet">
        <link href="/assets/css/main.css" rel="stylesheet">
        <link href="/assets/css/page.css" rel="stylesheet">
        <link href="/assets/css/form.css" rel="stylesheet">
    </head>
    <body class="page_other <?php echo ($other) ? 'page_other_active' : ''; ?>">
        <?php $res->partial('header_app'); ?>
        <main>
            <h1>Add A Number</h1>

            <section class="page">
                <?php $res->html->messages(); ?>

				<form method="POST">
                    <?php 
                        $periods = [];
                        $periods[] = ['label' => 'All', 'value' => 'all'];
                        $periods[] = ['label' => 'Today', 'value' => '1d_0d'];
                        $periods[] = ['label' => 'Yesterday', 'value' => '1d_1d'];
                        $periods[] = ['label' => 'Two Days Ago', 'value' => '1d_2d'];
                        $periods[] = ['label' => 'Current Week To Date', 'value' => '1w_0d'];
                        $periods[] = ['label' => 'Last Week', 'value' => '1w_1w'];
                        $periods[] = ['label' => 'Two Weeks Ago', 'value' => '1w_2w'];
                        $periods[] = ['label' => 'Current Month To Date', 'value' => '1m_0d'];
                        $periods[] = ['label' => 'Last Month', 'value' => '1m_1m'];
                        $periods[] = ['label' => 'Two Months Ago', 'value' => '1m_2m'];
                        $periods[] = ['label' => 'Current Year To Date', 'value' => 'y1_0d'];
                        $periods[] = ['label' => 'Last Year', 'value' => '1y_1y'];
                        $periods[] = ['label' => 'Last Year Today', 'value' => '1d_1y'];
                        $periods[] = ['label' => 'Last Year Yesterday', 'value' => '1d_1y1d'];
                        $periods[] = ['label' => 'Last Year Two Days Ago', 'value' => '1d_1y2d'];
                        $periods[] = ['label' => 'Last Year This Week', 'value' => '1w_1y'];
                        $periods[] = ['label' => 'Last Year Last Week', 'value' => '1w_1y1w'];
                        $periods[] = ['label' => 'Last Year Two Weeks Ago', 'value' => '1w_1y2w'];
                        $periods[] = ['label' => 'Last Year This Month', 'value' => '1m_1y'];
                        $periods[] = ['label' => 'Last Year Last Month', 'value' => '1m_1y1m'];
                        $periods[] = ['label' => 'Last Year Two Months Ago', 'value' => '1m_1y2m'];
                        $periods[] = ['label' => 'Other', 'value' => 'other'];
                    ?>
					<?php $res->html->radios('Tracking Period', 'period', $periods); ?>

                    <?php 
                        $increments = [];
                        for($i = 0; $i < 1000; $i++) {
                            $increments[] = ['label' => $i];
                        }
                    ?>
                    <div class="field other">
                        <label>How Long Ago</label>
                        <?php $res->html->selectRaw('years_ago', $increments); ?> Years
                        <?php $res->html->selectRaw('months_ago', $increments); ?> Months
                        <?php $res->html->selectRaw('weeks_ago', $increments); ?> Weeks
                        <?php $res->html->selectRaw('days_ago', $increments); ?> Days
                    </div>

                    <div class="field other">
                        <label>Range</label>
                        <?php $res->html->selectRaw('years_range', $increments); ?> Years
                        <?php $res->html->selectRaw('months_range', $increments); ?> Months
                        <?php $res->html->selectRaw('weeks_range', $increments); ?> Weeks
                        <?php $res->html->selectRaw('days_range', $increments); ?> Days
                    </div>

                    <hr>

					<?php $res->html->text('Number Display Name', 'name', $number->data['short_name']); ?>

					<?php $res->html->radios('Update Number Interval', 'interval', $intervals); ?>

					<?php $res->html->submit('Start Tracking', 'button button_invert'); ?>
				</form>

            </section>
        </main>
		<?php $res->partial('footer'); ?>
    </body>
</html>
