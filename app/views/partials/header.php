        <header>
            <div class="container">
                <h2><a href="/"><?php esc(ao()->env('APP_NAME')) ?></a></h2>
                <nav>
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/pricing">Pricing</a></li>
<?php if(false): ?>
                        <li><a href="/docs">Docs</a></li>
                        <li><a href="/blog">Blog</a></li>
<?php endif; ?>
                        <li><a href="/contact">Contact</a></li>
                        <?php if($user): ?>
                        <li><a href="<?php url(ao()->env('APP_PRIVATE_HOME')); ?>">Account</a></li>
                        <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout').submit();">Logout</a></li>
                        <?php else: ?>
                        <li><a href="/login">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </header>
