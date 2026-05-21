<?php
defined( 'ABSPATH' ) || exit;
remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
?>
<div class="corporate-empty-cart">
    <div class="corporate-empty-cart__icon" aria-hidden="true">
        <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="40" cy="40" r="34"/>
            <path d="M28 32h.01M52 32h.01"/>
            <path d="M52 54c-3-4-7-6-12-6s-9 2-12 6"/>
        </svg>
    </div>
    <p class="corporate-empty-cart__message">Ваш кошик зараз порожній.</p>
    <?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
        <p class="corporate-empty-cart__cta">
            <a class="btn btn-buy-now" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">Повернутися до магазину</a>
        </p>
    <?php endif; ?>
</div>
