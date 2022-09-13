window.ajax = {};
(function() {
    // Offset on the scroll amount (add some padding to the scroll).
    var offset_amount = 32;
    // Default the flash_time to immediately remove. Use CSS for any animation transitions.
    var flash_time = 1;

    function ajax(element) {
        var form = element;
        var url = form.action;
        var data = new FormData(form);
        var error_selector = form.getAttribute('data-ajax-error');
        var error_target = form.querySelector(error_selector);
        var new_el;
        var els;
        var button = form.querySelector('[type=submit]');
        if(button) {
            button.disabled = true;
        }

        form.classList.add('ajax_loading');

        post(url, data, function(err, data) {
            if(err) {
                if(error_target) {
                    new_el = replaceWith(error_target, el(data));
                }

                form.querySelectorAll('.error').forEach(function(el) {
                    el.classList.remove('error');
                });

                if(error_target && new_el.querySelectorAll) {
                    els = new_el.querySelectorAll('[data-error]');
                    els.forEach(function(el) {
                        form.querySelector('[name=' + el.getAttribute('data-error') + ']').classList.add('error');
                    });

                    window.scrollTo({
                        top: offset(new_el) - offset_amount,
                        behavior: 'smooth'
                    });
                }
            } else {
                new_el = replaceWith(form, el(data));
            }

            if(button) {
                button.disabled = false;
            }
            form.classList.remove('ajax_loading');
            if(new_el && new_el.classList) {
                new_el.classList.add('ajax_flash');
                setTimeout(function() {
                    new_el.classList.remove('ajax_flash');
                }, flash_time);
            }
        });
    }

    function el(str) {
        var temp = document.createElement('div');
        temp.innerHTML = str;
        return temp.childNodes[0];
    }

    function get(url, cb) {
        var request = new XMLHttpRequest();
        request.open('GET', url, true);

        request.onload = function() {
            if (request.status >= 200 && request.status < 400) {
                var data = request.responseText;
                cb(null, data);
            } else {
                cb(request.status);

            }
        };

        request.onerror = function() {
            cb('Error');
        };

        request.send();
    }

    function init() {
        document.addEventListener('submit', submitDocument);
    }

    function offset(el) {
        var offset = el.getBoundingClientRect().top + window.pageYOffset;
        return offset;
    }

    function post(url, data, cb) {
        var request = new XMLHttpRequest();
        request.open('POST', url, true);

        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        data = new URLSearchParams(data).toString();

        request.onload = function() {
            if (request.status >= 200 && request.status < 400) {
                var data = request.responseText;
                cb(null, data);
            } else {
                cb(request.status, request.responseText);
            }
        };

        request.onerror = function() {
            cb('Error');
        };

        request.send(data);
    }

    function ready(fn) {
        // see if DOM is already available
        if (document.readyState === "complete" || document.readyState === "interactive") {
            // call on next available tick
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

    function replaceWith(old_el, new_el) {
        var parent = old_el.parentNode;
        parent.replaceChild(new_el, old_el);

        return new_el;
    }

    function submitDocument(e) {
        var prevent = false;

        if(e.target.classList.contains('ajax')) {
            ajax(e.target);
            prevent = true;
        }

        if(prevent) {
            e.preventDefault();
        }
    }

    window.ajax.el = el;
    window.ajax.ready = ready;
    window.ajax.offset = offset;
    window.ajax.replaceWith = replaceWith;

    window.ajax.ready(init);
})();

