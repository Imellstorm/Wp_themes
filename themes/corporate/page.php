<?php
get_header();

/* =====================================================
   Empty cart — custom layout
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
                            <a href="<?php echo esc_url( $permalink ); ?>" class="product-card-link" aria-label="<?php echo esc_attr( $product->get_name() ); ?>"></a>
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
   WC pages (cart with items / checkout / account / order received)
   — simple rendering, no extra wrappers
   ===================================================== */
$is_wc_page = function_exists( 'is_cart' ) && (
    is_cart() ||
    is_checkout() ||
    is_account_page() ||
    ( function_exists( 'is_order_received_page' ) && is_order_received_page() )
);

if ( $is_wc_page ) : ?>
    <main class="section">
        <div class="container">
            <?php while ( have_posts() ) : the_post(); ?>
                <h1><?php the_title(); ?></h1>
                <div><?php the_content(); ?></div>
            <?php endwhile; ?>
        </div>
    </main>
    <?php
    get_footer();
    return;
endif;

/* =====================================================
   Legal / standard pages — beautiful long-form layout
   ===================================================== */
$shop_url     = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$delivery_url = home_url( '/dostavka-ta-oplata/' );
$offer_url    = home_url( '/dogovir-publichnoyi-oferty/' );
$delivery_page = get_page_by_path( 'dostavka-ta-oplata' );
if ( $delivery_page ) $delivery_url = get_permalink( $delivery_page );
$offer_page = get_page_by_path( 'dogovir-publichnoyi-oferty' );
if ( $offer_page ) $offer_url = get_permalink( $offer_page );

$current_id = get_queried_object_id();
?>

<main class="section section--legal">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
            <article class="legal-page">
                <header class="legal-page__header">
                    <span class="eyebrow">Документ</span>
                    <h1 class="legal-page__title"><?php the_title(); ?></h1>
                    <p class="legal-page__date">
                        Оновлено:
                        <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>">
                            <?php echo esc_html( get_the_modified_date( 'j F Y' ) ); ?>
                        </time>
                    </p>
                </header>

                <div class="legal-page__content">
                    <?php the_content(); ?>
                </div>

                <footer class="legal-page__footer">
                    <span class="eyebrow">Інші документи</span>
                    <nav class="legal-page__nav" aria-label="Other legal documents">
                        <?php if ( $current_id !== ( $delivery_page ? $delivery_page->ID : 0 ) ) : ?>
                            <a href="<?php echo esc_url( $delivery_url ); ?>" class="legal-page__nav-link">
                                <span class="legal-page__nav-label">Доставка та Оплата</span>
                                <span class="legal-page__nav-arrow">→</span>
                            </a>
                        <?php endif; ?>
                        <?php if ( $current_id !== ( $offer_page ? $offer_page->ID : 0 ) ) : ?>
                            <a href="<?php echo esc_url( $offer_url ); ?>" class="legal-page__nav-link">
                                <span class="legal-page__nav-label">Договір публічної оферти</span>
                                <span class="legal-page__nav-arrow">→</span>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo esc_url( $shop_url ); ?>" class="legal-page__nav-link">
                            <span class="legal-page__nav-label">Перейти до магазину</span>
                            <span class="legal-page__nav-arrow">→</span>
                        </a>
                    </nav>
                </footer>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
