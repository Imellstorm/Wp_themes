<?php
get_header();

$hero_image_id  = get_option( 'corporate_hero_image' );
$hero_url       = $hero_image_id ? wp_get_attachment_image_url( $hero_image_id, 'full' ) : '';
$hero_eyebrow   = get_option( 'corporate_hero_eyebrow', 'Octopus security' );
$hero_title     = get_option( 'corporate_hero_title', 'Lorem Ipsum Dolor Sit Amet Consectetur' );
$hero_subtitle  = get_option( 'corporate_hero_subtitle', 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.' );

$about_title = get_option( 'corporate_about_title', 'Lorem Ipsum Dolor Sit Amet' );

$products_title = get_option( 'corporate_products_title', 'Наші товари' );
$products_count = get_option( 'corporate_products_count', 6 );

$about_defaults = array(
    array( 'Consectetur Adipiscing Elit', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' ),
    array( 'Ut Enim Ad Minim Veniam', 'Quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit.' ),
    array( 'Duis Aute Irure Dolor', 'In reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.' ),
    array( 'Excepteur Sint Occaecat', 'Sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem.' ),
    array( 'Nemo Enim Ipsam Voluptatem', 'Quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.' ),
);

$faq_defaults = array(
    array( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit?', 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.' ),
    array( 'Ut enim ad minim veniam, quis nostrud exercitation?', 'Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.' ),
    array( 'Duis aute irure dolor in reprehenderit in voluptate?', 'Velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.' ),
    array( 'Excepteur sint occaecat cupidatat non proident?', 'Sunt in culpa qui officia deserunt mollit anim id est laborum. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.' ),
    array( 'Nemo enim ipsam voluptatem quia voluptas sit aspernatur?', 'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.' ),
);
?>

<!-- ============ HERO ============ -->
<section class="hero">
    <?php if ( $hero_url ) : ?>
        <div class="hero-bg" style="background-image:url(<?php echo esc_url( $hero_url ); ?>)"></div>
    <?php endif; ?>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="hero-main">
            <span class="eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></span>
            <h1><?php echo esc_html( $hero_title ); ?></h1>
            <h3><?php echo esc_html( $hero_subtitle ); ?></h3>
        </div>
        <?php
        $hero_shop_url     = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
        $hero_delivery_url = home_url( '/dostavka-ta-oplata/' );
        $hero_offer_url    = home_url( '/dogovir-publichnoyi-oferty/' );
        $hero_delivery_page = get_page_by_path( 'dostavka-ta-oplata' );
        if ( $hero_delivery_page ) {
            $hero_delivery_url = get_permalink( $hero_delivery_page );
        }
        $hero_offer_page = get_page_by_path( 'dogovir-publichnoyi-oferty' );
        if ( $hero_offer_page ) {
            $hero_offer_url = get_permalink( $hero_offer_page );
        }
        ?>
        <nav class="hero-nav" aria-label="Primary">
            <a href="<?php echo esc_url( $hero_shop_url ); ?>" class="hero-nav-link">Магазин</a>
            <a href="<?php echo esc_url( $hero_delivery_url ); ?>" class="hero-nav-link">Доставка та Оплата</a>
            <a href="<?php echo esc_url( $hero_offer_url ); ?>" class="hero-nav-link">Договір публічної оферти</a>
        </nav>
    </div>
</section>

<!-- ============ PRODUCTS ============ -->
<?php if ( class_exists( 'WooCommerce' ) ) : ?>
<section class="section products-section" id="products">
    <div class="container">
        <div class="section-header">
            <div class="section-header__index">— 01 / Каталог</div>
            <h2 class="section-title"><?php echo esc_html( $products_title ); ?></h2>
        </div>
        <?php
        $products = wc_get_products( array(
            'limit'   => $products_count,
            'status'  => 'publish',
            'orderby' => 'date',
            'order'   => 'ASC',
        ) );

        if ( $products ) : ?>
        <div class="products-grid">
            <?php foreach ( $products as $idx => $product ) :
                $permalink  = get_permalink( $product->get_id() );
                $short_desc = $product->get_short_description();
                if ( ! $short_desc ) {
                    $short_desc = wp_trim_words( $product->get_description(), 18, '&hellip;' );
                }
            ?>
            <div class="product-card">
                <a href="<?php echo esc_url( $permalink ); ?>"
                   class="product-card-link"
                   aria-label="<?php echo esc_attr( $product->get_name() ); ?>"></a>
                <div class="product-image">
                    <span class="product-index">SKU/<?php echo esc_html( str_pad( $idx + 1, 3, '0', STR_PAD_LEFT ) ); ?></span>
                    <?php if ( $product->get_image_id() ) : ?>
                        <?php echo wp_get_attachment_image(
                            $product->get_image_id(),
                            'large',
                            false,
                            array(
                                'alt'   => esc_attr( $product->get_name() ),
                                'sizes' => '(max-width: 768px) 100vw, 400px',
                            )
                        ); ?>
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
                           data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
                           data-quantity="1"
                           rel="nofollow">
                            В кошик
                        </a>
                        <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>&buy_now=1"
                           class="btn btn-buy-now">
                            Купити
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- ============ ABOUT ============ -->
<section class="section section--alt about-section" id="about">
    <div class="container">
        <div class="section-header">
            <div class="section-header__index">— 02 / Про нас</div>
            <h2 class="section-title"><?php echo esc_html( $about_title ); ?></h2>
        </div>
        <ul class="about-points">
            <?php for ( $i = 1; $i <= 5; $i++ ) :
                $pt = get_option( "corporate_about_point_{$i}_title", $about_defaults[ $i - 1 ][0] );
                $pd = get_option( "corporate_about_point_{$i}_text", $about_defaults[ $i - 1 ][1] );
                if ( ! $pt && ! $pd ) continue;
            ?>
            <li>
                <span class="point-number">/ <?php echo str_pad( $i, 2, '0', STR_PAD_LEFT ); ?></span>
                <div>
                    <strong><?php echo esc_html( $pt ); ?></strong>
                    <p><?php echo esc_html( $pd ); ?></p>
                </div>
            </li>
            <?php endfor; ?>
        </ul>
    </div>
</section>

<!-- ============ CTA / FEATURE ============ -->
<section class="section section--cta-wrap">
    <div class="container">
        <div class="cta-block">
            <div class="cta-block__content">
                <span class="eyebrow">Lorem ipsum</span>
                <h2 class="cta-title">Lorem Ipsum Dolor Sit Amet Consectetur</h2>
                <p class="cta-subtitle">Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============ FAQ ============ -->
<section class="section faq-section" id="faq">
    <div class="container">
        <div class="section-header">
            <div class="section-header__index">— 03 / Питання</div>
            <h2 class="section-title">Часті запитання</h2>
        </div>
        <div class="faq-list">
            <?php for ( $i = 1; $i <= 5; $i++ ) :
                $q = get_option( "corporate_faq_{$i}_question", $faq_defaults[ $i - 1 ][0] );
                $a = get_option( "corporate_faq_{$i}_answer", $faq_defaults[ $i - 1 ][1] );
                if ( ! $q && ! $a ) continue;
            ?>
            <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                    <span class="faq-question__index">Q.<?php echo str_pad( $i, 2, '0', STR_PAD_LEFT ); ?></span>
                    <span><?php echo esc_html( $q ); ?></span>
                    <span class="faq-icon" aria-hidden="true"></span>
                </button>
                <div class="faq-answer">
                    <p><?php echo wp_kses_post( $a ); ?></p>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
