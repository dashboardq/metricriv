(function() {

    function clickDocument(e) {
        var el;
        var offset;
        var prevent = false;

        if(e.target.matches('nav ul') && e.target.closest('nav').classList.contains('active')) {
            e.target.closest('nav').classList.remove('active');
        } else if(e.target.closest('nav') && !e.target.closest('nav').classList.contains('active')) {
            e.target.closest('nav').classList.add('active');
        } else if(e.target.classList.contains('scroll')) {
            el = document.querySelector(e.target.getAttribute('data-target'));
            window.scrollTo({
                top: ajax.offset(el),
                behavior: 'smooth'
            });
            prevent = true;
        }

        if(prevent) {
            e.preventDefault();
        }
    }

    function init() {
        document.addEventListener('click', clickDocument);
    }

    ajax.ready(init);
})();

