<!DOCTYPE html>                
<html>
    <head>                     
        <meta charset="utf-8">     
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title><?php echo htmlspecialchars($title); ?></title>

        <link rel="preload" href="/assets/fonts/feather.woff2" as="font" crossorigin="anonymous" />
        <link href="/assets/css/normalize.css" rel="stylesheet">
        <link href="/assets/css/base.css" rel="stylesheet">
        <link href="/assets/css/main.css" rel="stylesheet">
        <link href="/assets/css/page.css" rel="stylesheet">
        <link href="/assets/css/form.css" rel="stylesheet">
    </head>
    <body class="page_home">
        <header>
            <div class="container">
                <h1><a href="/"><?php echo htmlspecialchars($app_name); ?></a></h1>
            </div>
        </header>
        <main>
            <h1>Error</h1>
            <section class="page">
                <p>There appears to be a problem with the server. If this problem persists, please contact support.</p> 
            </section>
        </main>
        <footer>
            <div class="container">
                <p>&copy; <?php echo htmlspecialchars(date('Y') . ' ' . $app_name); ?></p>
                <nav>
                    <ul>
                        <li><a href="/terms">Terms of Service</a></li>
                        <li><a href="/privacy">Privacy Policy</a></li>
                    </ul>
                </nav>
            </div>
        </footer>
    </body>
</html>
