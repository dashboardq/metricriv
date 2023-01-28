        <footer>
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> NumbersQ</p>
                <nav>
                    <ul>
                        <li><a href="/terms">Terms of Service</a></li>
                        <li><a href="/privacy">Privacy Policy</a></li>
                    </ul>
                </nav>
            </div>
        </footer>
        <script src="/assets/js/ajax.js"></script>
        <script src="/assets/js/main.js"></script>
        <?php if($user): ?>
        <form id="logout" action="/logout" method="POST" class="hidden">
        </form>
        <?php endif; ?>

        <?php echo ao()->env('APP_ANALYTICS'); ?>
