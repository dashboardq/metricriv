        <div class="overlay processing" hidden>
            <div class="loading"><span></span></div>
        </div>

        <div class="overlay modal" hidden>
            <div class="box">
                <h2>Error</h2>
                <div class="content"></div>
                <button class="_close" aria-label="Close">&times;</button>
            </div>
        </div>

        <?php if($user): ?>
        <form id="logout" action="/logout" method="POST" class="hidden"></form>
        <?php endif; ?>
