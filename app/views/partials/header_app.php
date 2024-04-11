        <header class="app">
            <div class="container">
                <h2><a href="/"><?php esc(ao()->env('APP_NAME')) ?></a></h2>
                <nav>
                    <ul>
                        <?php foreach($follows as $follow): ?>
                        <li><a href="<?php uri($follow->data['collection']['slug']); ?>"><?php esc($follow->data['collection']['title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <ul>
                        <?php ao()->hook('app_header_app_nav_first_child'); ?>
                        <li><a href="/collections">Collections</a></li>
                        <li><a href="/number/add">Add Metric</a></li>
                        <li><a href="/connections">Connections</a></li>
                        <li><a href="/viewers">Viewers</a></li>
                        <li><a href="/settings">Settings</a></li>
                        <?php /*
                        <li class="fade"><a href="/users">Users</a></li>
                        <li class="fade"><a href="/follows">Follows</a></li>
                         */ ?>
                        <li class="fade"><a href="/requests">Feature Requests</a></li>

                        <li class="divider"></li>

                        <?php foreach($additional_links as $link): ?>
                        <li><a href="<?php esc($link['url']); ?>"><?php esc($link['name']); ?></a></li>
                        <?php endforeach; ?>

                        <li class="divider"></li>

                        <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout').submit();">Logout</a></li>
                        <?php ao()->hook('app_header_app_nav_last_child'); ?>
                    </ul>
                </nav>
            </div>
        </header>
