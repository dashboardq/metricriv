        <header>
            <div class="container">
                <h1><a href="/">NumbersQ</a></h1>
                <nav>
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/pricing">Pricing</a></li>
                        <li><a href="/contact">Contact</a></li>
                        <?php if($user): ?>
                        <li><a href="/numbers">Account</a></li>
                        <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout').submit();">Logout</a></li>
                        <?php else: ?>
                        <li><a href="/login">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </header>
