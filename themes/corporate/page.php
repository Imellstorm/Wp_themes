<?php
get_header();

/* =====================================================
   Cart page — when EMPTY, fully render our own layout
   ===================================================== */
$is_wc_cart_empty = function_exists( 'is_cart' )
    && is_cart()
    && WC()->cart
    && WC()->cart->is_empty();

if ( $is_wc_cart_empty ) :
    $shop_url = wc_get_page_permalink( 'shop' );
    $new_products = wc_get_products( array(
        'limit'   => 4,
        'status'  => 'publish',
        'orderby' => 'date',
        'order'   => 'DESC',
    ) );
    ?>
    <div class="woocommerce-notice-bar">
        <div class="container">
            <?php if ( function_exists( 'wc_print_notices' ) && wc_notice_count() > 0 ) wc_print_notices(); ?>
        </div>
    </div>

    <main class="section section--wc-page section--empty-cart">
        <div class="container">
            <header class="wc-page-header">
                <h1 class="wc-page-title">Кошик</h1>
            </header>

            <div class="empty-cart">
                <div class="empty-cart__icon" aria-hidden="true">
                    <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="40" cy="40" r="34"/>
                        <path d="M28 32h.01M52 32h.01"/>
                        <path d="M52 54c-3-4-7-6-12-6s-9 2-12 6"/>
                    </svg>
                </div>
                <p class="empty-cart__message">Ваш кошик зараз порожній</p>
                <p class="empty-cart__sub">Перегляньте наш каталог і знайдіть щось цікаве.</p>
                <a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-buy-now empty-cart__cta">Повернутися до магазину</a>
            </div>

            <?php if ( $new_products ) : ?>
                <h2 class="empty-cart-heading">Новинки в магазині</h2>
                <div class="products-grid empty-cart-products">
                    <?php $idx = 0; foreach ( $new_products as $product ) :
                        $idx++;
                        $permalink  = get_permalink( $product->get_id() );
                        $short_desc = $product->get_short_description();
                        if ( ! $short_desc ) {
                            $short_desc = wp_trim_words( $product->get_description(), 14, '&hellip;' );
                        }
                    ?>
                        <div class="product-card">
                            <a href="<?php echo esc_url( $permalink ); ?>"
                               class="product-card-link"
                               aria-label="<?php echo esc_attr( $product->get_name() ); ?>"></a>
                            <div class="product-image">
                                <span class="product-index">SKU/<?php echo esc_html( str_pad( $idx, 3, '0', STR_PAD_LEFT ) ); ?></span>
                                <?php if ( $product->get_image_id() ) : ?>
                                    <?php echo wp_get_attachment_image( $product->get_image_id(), 'large', false, array( 'alt' => esc_attr( $product->get_name() ) ) ); ?>
                                <?php else : ?>
                                    <div class="product-placeholder"></div>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?php echo esc_html( $product->get_name() ); ?></h3>
                                <div class="product-desc"><?php echo wp_kses_post( wpautop( $short_desc ) ); ?></div>
                                <div class="product-price"><?php echo $product->get_price_html(); ?></div>
                                <div class="product-actions">
                                    <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
                                       class="btn btn-add-cart add_to_cart_button ajax_add_to_cart"
                                       data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
                                       data-quantity="1"
                                       rel="nofollow">В кошик</a>
                                    <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>&buy_now=1" class="btn btn-buy-now">Купити</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php
    get_footer();
    return;
endif;

/* =====================================================
   All other pages — simple, untouched rendering
   ===================================================== */
?>
<main class="section">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
            <h1><?php the_title(); ?></h1>
            <div><?php the_content(); ?></div>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
