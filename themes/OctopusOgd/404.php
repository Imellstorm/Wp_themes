<?php
get_header();

$home_url = home_url( '/' );
$shop_url = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : $home_url;

$popular = array();
if ( class_exists( 'WooCommerce' ) ) {
    $popular = wc_get_products( array(
        'limit'   => 3,
        'status'  => 'publish',
        'orderby' => 'rand',
    ) );
}
?>

<main class="section section--404">
    <div class="container">
        <div class="error-404">
            <div class="error-404__bg" aria-hidden="true"></div>

            <div class="error-404__code" aria-hidden="true">
                <span>4</span>
                <span class="error-404__zero">
                    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round">
                        <circle cx="50" cy="50" r="42"/>
                        <path d="M30 38 70 62 M70 38 30 62" stroke-width="4" opacity="0.35"/>
                    </svg>
                </span>
                <span>4</span>
            </div>

            <span class="eyebrow error-404__eyebrow">Помилка</span>
            <h1 class="error-404__title">Сторінку не знайдено</h1>
            <p class="error-404__text">
                Можливо, сторінку було видалено, перенесено або ви ввели неправильну адресу.
                Скористайтеся пошуком або поверніться на головну.
            </p>

            <form class="error-404__search" action="<?php echo esc_url( $home_url ); ?>" method="get" role="search">
                <input type="search" name="s" class="error-404__search-input" placeholder="Пошук товарів..." aria-label="Пошук">
                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <input type="hidden" name="post_type" value="product">
                <?php endif; ?>
                <button type="submit" class="btn btn-buy-now error-404__search-btn">Шукати</button>
            </form>

            <div class="error-404__actions">
                <a href="<?php echo esc_url( $home_url ); ?>" class="btn btn-add-cart">На головну</a>
                <a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-buy-now">До магазину</a>
            </div>
        </div>

        <?php if ( ! empty( $popular ) ) : ?>
            <div class="error-404-popular">
                <h2 class="error-404-popular__title">Можливо, вас зацікавить</h2>
                <div class="products-grid error-404-popular__grid">
                    <?php $idx = 0; foreach ( $popular as $product ) :
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
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
