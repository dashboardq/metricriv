<!DOCTYPE html>                
<html>
    <head>                     
        <?php $res->partial('head'); ?>
        <link href="/assets/css/pricing.css?cache-date=2022-12-30" rel="stylesheet">
    </head>
    <body class="<?php $res->pathClass(); ?>">
        <?php $res->partial('header'); ?>
        <main>
            <div class="container">
                <h1>Pricing</h1>
                <ul class="cards">
                    <li class="card">
                        <h2>Free</h2>
                        <h3>$0</h3>
                        <ul>
                            <li>Up to <strong>100</strong> public numbers</li>
                            <li>Up to <strong>2</strong> private numbers</li>
                            <li>Unlimited public viewers</li>
                            <li>$5/mo for each private viewer</li>
                            <li>Updated every <strong>hour</strong></li>
                            <li>No credit card needed</li>
                        </ul>
                        <a href="/login">Get Started</a>
                    </li>
                    <li class="card highlight">
                        <h2>Basic</h2>
                        <h3>$5/mo</h3>
                        <ul>
                            <li>Up to <strong>100</strong> public numbers</li>
                            <li>Up to <strong>100</strong> private numbers</li>
                            <li>Unlimited public viewers</li>
                            <li>$5/mo for each private viewer</li>
                            <li>Updated every <strong>hour</strong></li>
                            <li>Priced per month</li>
                            <li>Includes premium numbers</li>
                        </ul>
                        <a href="/login">Get Started</a>
                    </li>
                    <li class="card">
                        <h2>Intermediate</h2>
                        <h3>$12/mo</h3>
                        <ul>
                            <li>Up to <strong>500</strong> public numbers</li>
                            <li>Up to <strong>500</strong> private numbers</li>
                            <li>Unlimited public viewers</li>
                            <li>$5/mo for each private viewer</li>
                            <li>Updated every <strong>hour</strong></li>
                            <li>Priced per month</li>
                            <li>Includes premium numbers</li>
                            <li>Receive a <strong>daily email report</strong></li>
                            <li>Process <strong>webhooks</strong></li>
                        </ul>
                        <a href="/login">Get Started</a>
                    </li>
                    <li class="card">
                        <h2>Advanced</h2>
                        <h3>$24/mo</h3>
                        <ul>
                            <li>Up to <strong>1,000</strong> public numbers</li>
                            <li>Up to <strong>1,000</strong> private numbers</li>
                            <li>Unlimited public viewers</li>
                            <li>$5/mo for each private viewer</li>
                            <li>Updated every <strong>5 minutes</strong></li>
                            <li>Priced per month</li>
                            <li>Includes premium numbers</li>
                            <li>Receive a <strong>daily email report</strong></li>
                            <li>Process <strong>webhooks</strong></li>
                            <li>Pull numbers <strong>from email</strong></li>
                        </ul>
                        <a href="/login">Get Started</a>
                    </li>
                    <li class="card">
                        <h2>Custom</h2>
                        <h3>Get In Touch</h3>
                        <ul>
                            <li>If you are not seeing a plan that meets your needs, please feel free to reach out.</li>
                        </ul>
                        <a href="/login">Get Started</a>
                    </li>
                </ul>
            </div>
        </main>
		<?php $res->partial('footer'); ?>
		<?php $res->partial('foot'); ?>
    </body>
</html>
