(function () {
    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    function initQuantityButtons() {
        document.querySelectorAll('form.cart .quantity').forEach(function (wrap) {
            var input = wrap.querySelector('input.qty');
            if (!input || wrap.querySelector('.corporate-qty-btn')) return;

            var step = parseFloat(input.getAttribute('step')) || 1;
            var min  = input.hasAttribute('min') && input.getAttribute('min') !== ''
                       ? parseFloat(input.getAttribute('min')) : 0;
            var max  = input.hasAttribute('max') && input.getAttribute('max') !== ''
                       ? parseFloat(input.getAttribute('max')) : Infinity;

            var minus = document.createElement('button');
            minus.type = 'button';
            minus.className = 'corporate-qty-btn corporate-qty-btn--minus';
            minus.setAttribute('aria-label', 'Decrease quantity');
            minus.textContent = '−';

            var plus = document.createElement('button');
            plus.type = 'button';
            plus.className = 'corporate-qty-btn corporate-qty-btn--plus';
            plus.setAttribute('aria-label', 'Increase quantity');
            plus.textContent = '+';

            wrap.insertBefore(minus, input);
            wrap.appendChild(plus);

            function clamp(v) {
                if (isNaN(v)) v = min || 0;
                if (v < min) v = min;
                if (v > max) v = max;
                return v;
            }

            minus.addEventListener('click', function () {
                input.value = clamp((parseFloat(input.value) || 0) - step);
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            plus.addEventListener('click', function () {
                input.value = clamp((parseFloat(input.value) || 0) + step);
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    }

    function getNoticeBar() {
        var bar = document.querySelector('.woocommerce-notice-bar .container');
        return bar || null;
    }

    function showNotice(productName) {
        var bar = getNoticeBar();
        if (!bar) return;
        var html = '<div class="woocommerce-message" role="alert">«' + productName + '» додано до кошика.</div>';
        bar.innerHTML = html;
    }

    function initAjaxAddToCart() {
        var forms = document.querySelectorAll('form.cart');
        if (!forms.length || !window.wc_add_to_cart_params) return;

        forms.forEach(function (form) {
            if (form.classList.contains('variations_form') || form.classList.contains('grouped_form')) return;

            form.addEventListener('submit', function (e) {
                var btn        = form.querySelector('button.single_add_to_cart_button, button[name="add-to-cart"]');
                var qtyInput   = form.querySelector('input.qty');
                var idInputEl  = form.querySelector('input[name="add-to-cart"]');
                var productId  = (btn && btn.value) || (idInputEl && idInputEl.value) || null;

                if (!productId) return;
                e.preventDefault();

                if (btn) {
                    btn.classList.remove('added');
                    btn.classList.add('corporate-btn-loading');
                    btn.disabled = true;
                }

                var ajaxUrl = window.wc_add_to_cart_params.wc_ajax_url
                    .toString()
                    .replace('%%endpoint%%', 'add_to_cart');

                var body = new URLSearchParams();
                body.append('product_id', productId);
                body.append('quantity', qtyInput ? qtyInput.value : 1);

                fetch(ajaxUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: body.toString(),
                })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (btn) {
                        btn.classList.remove('corporate-btn-loading');
                        btn.disabled = false;
                    }
                    if (!res) return;
                    if (res.error && res.product_url) {
                        window.location = res.product_url;
                        return;
                    }
                    if (res.fragments) {
                        Object.keys(res.fragments).forEach(function (selector) {
                            document.querySelectorAll(selector).forEach(function (el) {
                                el.outerHTML = res.fragments[selector];
                            });
                        });
                    }
                    if (window.jQuery) {
                        window.jQuery(document.body).trigger('added_to_cart', [
                            res.fragments, res.cart_hash, window.jQuery(),
                        ]);
                    }
                    var titleEl = document.querySelector('.product_title');
                    var name = titleEl ? titleEl.textContent.trim() : 'Товар';
                    showNotice(name);
                })
                .catch(function () {
                    if (btn) {
                        btn.classList.remove('corporate-btn-loading');
                        btn.disabled = false;
                    }
                });
            });
        });
    }

    ready(function () {
        initQuantityButtons();
        initAjaxAddToCart();
    });
})();
