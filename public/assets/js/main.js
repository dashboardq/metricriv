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
        } else if(e.target.classList.contains('sort_up') || e.target.closest('.sort_up')) {
            console.log('clickDocument: sort_up');
            console.log(e.target);
            moveUp(e.target);
        } else if(e.target.classList.contains('sort_down') || e.target.closest('.sort_down')) {
            console.log('clickDocument: sort_down');
            console.log(e.target);
            moveDown(e.target);
            prevent = true;
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

    function droppedDocument(e) {
        var $table = e.target;
        orderChanged($table);
    }

    function moveDown($target) {
        console.log('moveDown');
        var $table = $target.closest('table');
        var $tbody = $target.closest('tbody');
        var $tr = $target.closest('tr');
        var $next = $tr.nextElementSibling;
        if($next) {
            $tr.before($next);
            orderChanged($table);
        }
    }

    function moveUp($target) {
        console.log('moveUp');
        var $table = $target.closest('table');
        var $tbody = $target.closest('tbody');
        var $tr = $target.closest('tr');
        var $prev = $tr.previousElementSibling;
        if($prev) {
            $tr.after($prev);
            orderChanged($table);
        }
    }

    function orderChanged($table) {
        var ids;
        var list;
        var url;
        $tr = $table.querySelectorAll('[data-id]');

        url = $table.getAttribute('data-action');

        list = [];
        $tr.forEach(function($item) {
            list.push($item.getAttribute('data-id'));
        });

        list.reverse();
        ids = list.join(',');
        args = {};
        args.ids = ids;

        ajax.post(url, args, function(err, data) {
            //console.log(data);
        });
    }

    function init() {
        document.addEventListener('click', clickDocument);

        document.addEventListener('change', changeDocument);

        document.addEventListener('dropped', droppedDocument);

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

