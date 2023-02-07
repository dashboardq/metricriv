(function() {

    function changeDocument(e) {
        console.log(e);
        if(e.target.matches('input[type=radio]') && e.target.value == 'other') {
            document.querySelector('body').classList.add('page_other_active');
        } else if(e.target.matches('input[type=radio]') && e.target.value != 'other' && e.target.name == 'period') {
            document.querySelector('body').classList.remove('page_other_active');
        }

    }

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

        document.addEventListener('change', changeDocument);

        // Fix Chrome not adding page_other_active on back button.
        setTimeout(function() {
            var $option = document.querySelector('[name=period]:checked');
            if($option && $option.value && $option.value == 'other') {
                document.querySelector('body').classList.add('page_other_active');
            }
        }, 1);
    }

    ajax.ready(init);
})();

