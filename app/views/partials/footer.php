        <footer>
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> <?php esc(ao()->env('APP_NAME')) ?></p>
                <nav>
                    <ul>
                        <li><a href="/terms">Terms of Service</a></li>
                        <li><a href="/privacy">Privacy Policy</a></li>
                        <li><a href="https://github.com/dashboardq/metricriv">Open Source Code</a></li>
                    </ul>
                </nav>
            </div>
        </footer>
        <script src="/assets/js/ajax.js?cache-date=<?php esc($cache_date); ?>"></script>
        <script src="/assets/js/main.js?cache-date=<?php esc($cache_date); ?>"></script>

        <?php echo ao()->env('APP_ANALYTICS'); ?>
