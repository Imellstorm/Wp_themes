<?php
get_header();

global $wp_query;
$query_str    = get_search_query();
$result_count = (int) $wp_query->found_posts;
$shop_url     = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

$active_cat = '';
if ( ! empty( $_GET['product_cat'] ) ) {
    $active_cat_slug = sanitize_text_field( wp_unslash( $_GET['product_cat'] ) );
    $term            = get_term_by( 'slug', $active_cat_slug, 'product_cat' );
    if ( $term && ! is_wp_error( $term ) ) {
        $active_cat = $term->name;
    }
}
?>

<div class="woocommerce-notice-bar">
    <div class="container">
        <?php if ( function_exists( 'wc_print_notices' ) && wc_notice_count() > 0 ) wc_print_notices(); ?>
    </div>
</div>

<main class="section section--search">
    <div class="container">
        <div class="section-header">
            <div class="section-header__index">— Пошук</div>
            <h2 class="section-title">
                <?php if ( $query_str ) : ?>
                    <span class="search-query-label">Результати для</span>
                    <span class="search-query">&laquo;<?php echo esc_html( $query_str ); ?>&raquo;</span>
                <?php else : ?>
                    Усі результати
                <?php endif; ?>
            </h2>
        </div>

        <div class="search-meta">
            <div class="search-meta__count">
                <span class="search-meta__num"><?php echo esc_html( str_pad( $result_count, 3, '0', STR_PAD_LEFT ) ); ?></span>
                <span class="search-meta__label">знайдено результатів</span>
            </div>
        </div>

        <?php if ( have_posts() ) : ?>
            <div class="products-grid search-results-grid">
                <?php
                $idx = 0;
                while ( have_posts() ) : the_post();
                    $post_id  = get_the_ID();
                    $is_prod  = ( 'product' === get_post_type( $post_id ) && class_exists( 'WooCommerce' ) );
                    $permalink = get_permalink( $post_id );

                    if ( $is_prod ) :
                        $product = wc_get_product( $post_id );
                        if ( ! $product ) continue;
                        $short = $product->get_short_description();
                        if ( ! $short ) {
                            $short = wp_trim_words( $product->get_description(), 18, '&hellip;' );
                        }
                        $idx++;
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
                            <div class="product-desc"><?php echo wp_kses_post( wpautop( $short ) ); ?></div>
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
                <?php else : ?>
                    <article class="product-card product-card--post">
                        <a href="<?php echo esc_url( $permalink ); ?>" class="product-card-link" aria-label="<?php echo esc_attr( get_the_title() ); ?>"></a>
                        <div class="product-info">
                            <h3 class="product-name"><?php the_title(); ?></h3>
                            <div class="product-desc"><?php echo wp_kses_post( wpautop( wp_trim_words( get_the_excerpt(), 24, '&hellip;' ) ) ); ?></div>
                            <div class="product-actions">
                                <a href="<?php echo esc_url( $permalink ); ?>" class="btn btn-add-cart">Читати далі</a>
                            </div>
                        </div>
                    </article>
                <?php endif; ?>
                <?php endwhile; ?>
            </div>

        <?php else : ?>

            <div class="search-empty">
                <div class="search-empty__icon" aria-hidden="true">
                    <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="34" cy="34" r="22"/>
                        <path d="m52 52 18 18"/>
                    </svg>
                </div>
                <h3 class="search-empty__title">Нічого не знайдено</h3>
                <p class="search-empty__text">
                    <?php if ( $query_str ) : ?>
                        Ми не знайшли нічого за запитом <strong>&laquo;<?php echo esc_html( $query_str ); ?>&raquo;</strong>.
                    <?php else : ?>
                        Введіть запит для початку пошуку.
                    <?php endif; ?>
                </p>
                <form role="search" method="get" class="search-empty__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="search" name="s" class="search-empty__input" placeholder="Пошук..." value="<?php echo esc_attr( $query_str ); ?>">
                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                        <input type="hidden" name="post_type" value="product">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-buy-now">Шукати</button>
                </form>
                <a href="<?php echo esc_url( $shop_url ); ?>" class="search-empty__link">Переглянути всі товари →</a>
            </div>

        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
