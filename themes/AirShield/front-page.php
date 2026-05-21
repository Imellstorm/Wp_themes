<?php
get_header();

$hero_image_id  = get_option( 'landing_hero_image' );
$hero_url       = $hero_image_id ? wp_get_attachment_image_url( $hero_image_id, 'full' ) : '';
$hero_title     = get_option( 'landing_hero_title', 'Lorem Ipsum Dolor Sit Amet Consectetur' );
$hero_subtitle  = get_option( 'landing_hero_subtitle', 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.' );

$about_image_id = get_option( 'landing_about_image' );
$about_url      = $about_image_id ? wp_get_attachment_image_url( $about_image_id, 'full' ) : '';
$about_title    = get_option( 'landing_about_title', 'Lorem Ipsum Dolor Sit Amet' );

$products_title = get_option( 'landing_products_title', 'Our Products' );
$products_count = get_option( 'landing_products_count', 5 );

$about_defaults = array(
    array( 'Consectetur Adipiscing Elit', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' ),
    array( 'Ut Enim Ad Minim Veniam', 'Quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit.' ),
    array( 'Duis Aute Irure Dolor', 'In reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.' ),
    array( 'Excepteur Sint Occaecat', 'Sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem.' ),
    array( 'Nemo Enim Ipsam Voluptatem', 'Quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.' ),
);

$faq_defaults = array(
    array( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit?', 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.' ),
    array( 'Ut enim ad minim veniam, quis nostrud exercitation?', 'Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.' ),
    array( 'Duis aute irure dolor in reprehenderit in voluptate?', 'Velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.' ),
    array( 'Excepteur sint occaecat cupidatat non proident?', 'Sunt in culpa qui officia deserunt mollit anim id est laborum. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.' ),
    array( 'Nemo enim ipsam voluptatem quia voluptas sit aspernatur?', 'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit. Sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.' ),
);
?>

<!-- ============ HERO ============ -->
<section class="hero" <?php if ( $hero_url ) echo 'style="background-image:url(' . esc_url( $hero_url ) . ')"'; ?>>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <h1><?php echo esc_html( $hero_title ); ?></h1>
        <h3><?php echo esc_html( $hero_subtitle ); ?></h3>
    </div>
</section>

<!-- ============ PRODUCTS ============ -->
<?php if ( class_exists( 'WooCommerce' ) ) : ?>
<section class="section products-section" id="products">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html( $products_title ); ?></h2>
        <?php
        $products = wc_get_products( array(
            'limit'   => $products_count,
            'status'  => 'publish',
            'orderby' => 'date',
            'order'   => 'ASC',
        ) );

        if ( $products ) : ?>
        <div class="products-grid">
            <?php foreach ( $products as $product ) :
                $permalink   = get_permalink( $product->get_id() );
                $short_desc  = $product->get_short_description();
                if ( ! $short_desc ) {
                    $short_desc = wp_trim_words( $product->get_description(), 24, '&hellip;' );
                }
            ?>
            <div class="product-card">
                <a href="<?php echo esc_url( $permalink ); ?>"
                   class="product-card-link"
                   aria-label="<?php echo esc_attr( $product->get_name() ); ?>"></a>
                <div class="product-image">
                    <?php if ( $product->get_image_id() ) : ?>
                        <?php echo wp_get_attachment_image(
                            $product->get_image_id(),
                            'large',
                            false,
                            array(
                                'alt'   => esc_attr( $product->get_name() ),
                                'sizes' => '(max-width: 768px) 100vw, 220px',
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
                            Add to Cart
                        </a>
                        <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>&buy_now=1"
                           class="btn btn-buy-now">
                            Buy Now
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
        <?php if ( $about_url ) : ?>
            <div class="about-image">
                <img src="<?php echo esc_url( $about_url ); ?>" alt="About us">
            </div>
        <?php endif; ?>
        <h2 class="about-title"><?php echo esc_html( $about_title ); ?></h2>
        <ul class="about-points">
            <?php for ( $i = 1; $i <= 5; $i++ ) :
                $pt = get_option( "landing_about_point_{$i}_title", $about_defaults[ $i - 1 ][0] );
                $pd = get_option( "landing_about_point_{$i}_text", $about_defaults[ $i - 1 ][1] );
                if ( ! $pt && ! $pd ) continue;
            ?>
            <li>
                <span class="point-number"><?php echo str_pad( $i, 2, '0', STR_PAD_LEFT ); ?></span>
                <div>
                    <strong><?php echo esc_html( $pt ); ?></strong>
                    <p><?php echo esc_html( $pd ); ?></p>
                </div>
            </li>
            <?php endfor; ?>
        </ul>
    </div>
</section>

<!-- ============ FAQ ============ -->
<section class="section faq-section" id="faq">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div class="faq-list">
            <?php for ( $i = 1; $i <= 5; $i++ ) :
                $q = get_option( "landing_faq_{$i}_question", $faq_defaults[ $i - 1 ][0] );
                $a = get_option( "landing_faq_{$i}_answer", $faq_defaults[ $i - 1 ][1] );
                if ( ! $q && ! $a ) continue;
            ?>
            <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                    <span><?php echo esc_html( $q ); ?></span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 8l5 5 5-5"/>
                    </svg>
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
