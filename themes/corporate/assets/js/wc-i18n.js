(function () {
    if (typeof wp === 'undefined' || !wp.i18n || !wp.i18n.setLocaleData) {
        return;
    }

    var t = {
        /* Cart block */
        'Cart':                                   ['Кошик'],
        'Cart totals':                            ['Підсумок кошика'],
        'Cart total':                             ['Сума кошика'],
        'Estimated total':                        ['Орієнтовна сума'],
        'Add coupons':                            ['Додати купон'],
        'Add a coupon':                           ['Додати купон'],
        'Coupon code':                            ['Промокод'],
        'Apply':                                  ['Застосувати'],
        'Apply coupon':                           ['Застосувати купон'],
        'Proceed to Checkout':                    ['Оформити замовлення'],
        'Proceed to checkout':                    ['Оформити замовлення'],
        'Product':                                ['Товар'],
        'Total':                                  ['Усього'],
        'Subtotal':                               ['Сума'],
        'Quantity':                               ['Кількість'],
        'Remove item':                            ['Видалити'],
        'Remove from cart':                       ['Видалити з кошика'],
        'New in store':                           ['Новинки в магазині'],
        'Your cart is currently empty!':          ['Ваш кошик зараз порожній!'],
        'Your cart is currently empty.':          ['Ваш кошик зараз порожній.'],
        'Browse store':                           ['Перейти в магазин'],
        'Return to shop':                         ['Повернутися до магазину'],
        'Return to Cart':                         ['Повернутися до кошика'],
        'Continue shopping':                      ['Продовжити покупки'],
        'Shipping':                               ['Доставка'],
        'Taxes':                                  ['Податки'],
        'Discount':                               ['Знижка'],
        'No shipping options available':          ['Немає доступних способів доставки'],
        'Calculated during checkout':             ['Розраховується під час оформлення'],
        'Order summary':                          ['Підсумок замовлення'],
        'Edit':                                   ['Редагувати'],
        'Save':                                   ['Зберегти'],

        /* Checkout block */
        'Checkout':                               ['Оформлення замовлення'],
        'Contact information':                    ['Контактна інформація'],
        'You will receive order confirmation and order updates by email.':
            ['Підтвердження замовлення та оновлення надійдуть на email.'],
        'Email address':                          ['Електронна пошта'],
        'Email':                                  ['Email'],
        'Phone (optional)':                       ['Телефон (необовʼязково)'],
        'Phone':                                  ['Телефон'],
        'Shipping address':                       ['Адреса доставки'],
        'Billing address':                        ['Платіжна адреса'],
        'Use same address for billing':           ['Використати ту саму адресу для оплати'],
        'First name':                             ["Ім'я"],
        'Last name':                              ['Прізвище'],
        'Country/Region':                         ['Країна / Регіон'],
        'Country / Region':                       ['Країна / Регіон'],
        'Address':                                ['Адреса'],
        'City':                                   ['Місто'],
        'State':                                  ['Область'],
        'Postcode':                               ['Поштовий індекс'],
        'Shipping options':                       ['Способи доставки'],
        'Payment options':                        ['Способи оплати'],
        'Order notes':                            ['Примітки до замовлення'],
        'Order notes (optional)':                 ['Примітки до замовлення (необовʼязково)'],
        'Place Order':                            ['Підтвердити замовлення'],
        'Place order':                            ['Підтвердити замовлення'],
        'Your order':                             ['Ваше замовлення'],
        'Date':                                   ['Дата'],
        'Payment method':                         ['Спосіб оплати'],
        'Terms and Conditions':                   ['Умови використання'],
        'Privacy Policy':                         ['Політика конфіденційності'],

        'Cash on delivery':                       ['Оплата при отриманні'],
        'Pay with cash upon delivery.':           ['Оплатіть готівкою при отриманні.'],
        'Direct bank transfer':                   ['Прямий банківський переказ'],
    };

    var localeData = {
        '': {
            domain: 'woocommerce',
            lang: 'uk_UA',
            'plural-forms': 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);'
        }
    };
    Object.keys(t).forEach(function (k) { localeData[k] = t[k]; });

    ['woocommerce', '@woocommerce/blocks', 'woo-gutenberg-products-block', ''].forEach(function (d) {
        wp.i18n.setLocaleData(localeData, d);
    });

    /* Force reload when LAST cart item is removed */
    function isCartPage() {
        return document.body && document.body.classList.contains('woocommerce-cart');
    }

    function dimCart() {
        var el = document.querySelector('.wp-block-woocommerce-cart')
              || document.querySelector('.wc-block-cart')
              || document.querySelector('main');
        if (el) {
            el.style.transition = 'opacity 0.15s ease';
            el.style.opacity = '0.25';
            el.style.pointerEvents = 'none';
        }
    }

    function getFirstCartItemKey() {
        try {
            var store = wp.data.select('wc/store/cart');
            if (!store || typeof store.getCartData !== 'function') return null;
            var data = store.getCartData();
            if (data && Array.isArray(data.items) && data.items.length > 0) {
                return data.items[0].key || null;
            }
        } catch (err) {}
        return null;
    }

    function removeItemAndReload(key) {
        var done = false;
        function finalReload() {
            if (done) return;
            done = true;
            window.location.reload();
        }
        setTimeout(finalReload, 2000);
        if (!key) { finalReload(); return; }

        if (window.wp && wp.apiFetch) {
            wp.apiFetch({
                path: '/wc/store/v1/cart/remove-item',
                method: 'POST',
                data: { key: key }
            }).then(finalReload, finalReload);
        } else {
            fetch('/wp-json/wc/store/v1/cart/remove-item', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ key: key })
            }).then(finalReload, finalReload);
        }
    }

    if (isCartPage()) {
        document.addEventListener('click', function (e) {
            var removeBtn = e.target.closest(
                '.wc-block-cart-item__remove-link, ' +
                '.wc-block-cart-items__row a[aria-label*="Remove" i], ' +
                '.wc-block-cart-items__row button[aria-label*="Remove" i]'
            );
            if (!removeBtn) return;

            var rows = document.querySelectorAll('.wc-block-cart-items__row');
            if (rows.length > 1) return;

            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();

            dimCart();
            removeItemAndReload(getFirstCartItemKey());
        }, true);
    }
})();
