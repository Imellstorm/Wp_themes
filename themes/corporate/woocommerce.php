<?php
get_header();

/* =====================================================
   Single product page — keep WC default rendering
   ===================================================== */
if ( is_singular( 'product' ) ) :
    ?>
    <div class="woocommerce-notice-bar">
        <div class="container">
            <?php if ( function_exists( 'wc_print_notices' ) && wc_notice_count() > 0 ) wc_print_notices(); ?>
        </div>
    </div>

    <main class="section section--woocommerce">
        <div class="container">
            <?php woocommerce_content(); ?>
        </div>
    </main>
    <?php
    get_footer();
    return;
endif;

/* =====================================================
   Detect: is this a product archive view?
   ===================================================== */
$is_product_archive = is_shop()
    || is_product_category()
    || is_product_tag()
    || is_post_type_archive( 'product' )
    || ( is_search() && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'product' );

if ( ! $is_product_archive ) :
    // Fallback for any other WC route — render with simple page layout
    ?>
    <div class="woocommerce-notice-bar">
        <div class="container">
            <?php if ( function_exists( 'wc_print_notices' ) && wc_notice_count() > 0 ) wc_print_notices(); ?>
        </div>
    </div>

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
   Archive view — shop, category, product search
   ===================================================== */
$search_q    = trim( get_search_query() );
$is_search   = $search_q !== '' || is_search();
$is_cat_arch = is_product_category();
$current_cat = $is_cat_arch ? get_queried_object() : null;

// === Filter params ===
$sel_cats = array();
if ( ! empty( $_GET['product_cat'] ) ) {
    $raw = (array) $_GET['product_cat'];
    foreach ( $raw as $slug ) {
        $slug = sanitize_title( $slug );
        if ( $slug ) {
            $sel_cats[] = $slug;
        }
    }
}
if ( $current_cat && ! in_array( $current_cat->slug, $sel_cats, true ) ) {
    $sel_cats[] = $current_cat->slug;
}

$min_price = ( isset( $_GET['min_price'] ) && $_GET['min_price'] !== '' ) ? max( 0, (float) $_GET['min_price'] ) : '';
$max_price = ( isset( $_GET['max_price'] ) && $_GET['max_price'] !== '' ) ? max( 0, (float) $_GET['max_price'] ) : '';

$paged    = max( 1, (int) ( get_query_var( 'paged' ) ?: get_query_var( 'page' ) ?: 1 ) );
$per_page = 12;

// === Build query ===
$args = array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'paged'          => $paged,
);

if ( $search_q ) {
    $args['s'] = $search_q;
}

if ( ! empty( $sel_cats ) ) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $sel_cats,
        ),
    );
}

if ( $min_price !== '' || $max_price !== '' ) {
    $meta = array();
    if ( $min_price !== '' ) {
        $meta[] = array( 'key' => '_price', 'value' => $min_price, 'compare' => '>=', 'type' => 'NUMERIC' );
    }
    if ( $max_price !== '' ) {
        $meta[] = array( 'key' => '_price', 'value' => $max_price, 'compare' => '<=', 'type' => 'NUMERIC' );
    }
    $args['meta_query'] = $meta;
}

$query = new WP_Query( $args );

// === Page meta ===
$shop_url = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

if ( $is_search ) {
    $index_label = '— Пошук';
    $form_action = home_url( '/' );
    $reset_url   = add_query_arg( array(
        's'         => $search_q,
        'post_type' => class_exists( 'WooCommerce' ) ? 'product' : null,
    ), home_url( '/' ) );
} elseif ( $is_cat_arch && $current_cat ) {
    $index_label = '— Категорія';
    $form_action = get_term_link( $current_cat );
    $reset_url   = get_term_link( $current_cat );
} else {
    $index_label = '— Магазин';
    $form_action = $shop_url;
    $reset_url   = $shop_url;
}

$all_cats = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
) );
if ( is_wp_error( $all_cats ) ) {
    $all_cats = array();
}

$has_active_filters = ! empty( $_GET['product_cat'] ) || $min_price !== '' || $max_price !== '';
?>

<div class="woocommerce-notice-bar">
    <div class="container">
        <?php if ( function_exists( 'wc_print_notices' ) && wc_notice_count() > 0 ) wc_print_notices(); ?>
    </div>
</div>

<main class="section section--shop">
    <div class="container">
        <div class="shop-layout">
            <aside class="shop-filters">
                <form method="get" action="<?php echo esc_url( $form_action ); ?>" class="shop-filters-form">
                    <?php if ( $is_search ) : ?>
                        <input type="hidden" name="s" value="<?php echo esc_attr( $search_q ); ?>">
                        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                            <input type="hidden" name="post_type" value="product">
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ( ! empty( $all_cats ) ) : ?>
                        <div class="filter-group">
                            <h3 class="filter-group__title">Категорія</h3>
                            <ul class="filter-cats">
                                <?php foreach ( $all_cats as $cat ) :
                                    $is_current_cat = $current_cat && $current_cat->term_id === $cat->term_id;
                                    $is_checked     = $is_current_cat || in_array( $cat->slug, $sel_cats, true );
                                ?>
                                    <li>
                                        <label class="filter-cat <?php echo $is_current_cat ? 'filter-cat--current' : ''; ?>">
                                            <input type="checkbox" name="product_cat[]" value="<?php echo esc_attr( $cat->slug ); ?>"
                                                <?php checked( $is_checked ); ?>
                                                <?php disabled( $is_current_cat ); ?>>
                                            <span class="filter-cat__name"><?php echo esc_html( $cat->name ); ?></span>
                                            <span class="filter-cat__count"><?php echo intval( $cat->count ); ?></span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="filter-group">
                        <h3 class="filter-group__title">Ціна</h3>
                        <div class="filter-price">
                            <label class="filter-price__field">
                                <span>Від</span>
                                <input type="number" name="min_price" value="<?php echo esc_attr( $min_price ); ?>" min="0" step="1" placeholder="0">
                            </label>
                            <span class="filter-price__sep">—</span>
                            <label class="filter-price__field">
                                <span>До</span>
                                <input type="number" name="max_price" value="<?php echo esc_attr( $max_price ); ?>" min="0" step="1" placeholder="∞">
                            </label>
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-buy-now filter-apply">Застосувати</button>
                        <?php if ( $has_active_filters ) : ?>
                            <a href="<?php echo esc_url( $reset_url ); ?>" class="filter-reset">Скинути фільтри</a>
                        <?php endif; ?>
                    </div>
                </form>
            </aside>

            <div class="shop-main">
                <h1 class="shop-title">
                    <?php if ( $is_search && $search_q ) : ?>
                        <span class="search-query-label">Результати для</span>
                        <span class="search-query">«<?php echo esc_html( $search_q ); ?>»</span>
                    <?php elseif ( $is_cat_arch && $current_cat ) : ?>
                        <?php echo esc_html( $current_cat->name ); ?>
                    <?php else : ?>
                        Магазин
                    <?php endif; ?>
                </h1>
                <div class="shop-meta">
                    <div class="shop-meta__count">
                        <span class="shop-meta__num"><?php echo esc_html( str_pad( (int) $query->found_posts, 3, '0', STR_PAD_LEFT ) ); ?></span>
                        <span class="shop-meta__label">знайдено товарів</span>
                    </div>
                    <?php if ( ! empty( $sel_cats ) || $min_price !== '' || $max_price !== '' ) : ?>
                        <div class="shop-meta__tags">
                            <?php foreach ( $sel_cats as $slug ) :
                                $t = get_term_by( 'slug', $slug, 'product_cat' );
                                if ( ! $t ) continue;
                            ?>
                                <span class="shop-meta__tag"><?php echo esc_html( $t->name ); ?></span>
                            <?php endforeach; ?>
                            <?php if ( $min_price !== '' || $max_price !== '' ) :
                                if ( $min_price !== '' && $max_price !== '' ) {
                                    $price_label = sprintf( '$%d — $%d', $min_price, $max_price );
                                } elseif ( $min_price !== '' ) {
                                    $price_label = sprintf( 'від $%d', $min_price );
                                } else {
                                    $price_label = sprintf( 'до $%d', $max_price );
                                }
                                ?>
                                <span class="shop-meta__tag"><?php echo esc_html( $price_label ); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ( $query->have_posts() ) : ?>
                    <div class="products-grid shop-grid">
                        <?php
                        $idx = ( $paged - 1 ) * $per_page;
                        while ( $query->have_posts() ) : $query->the_post();
                            $product = wc_get_product( get_the_ID() );
                            if ( ! $product ) continue;
                            $idx++;
                            $permalink = get_permalink();
                            $short     = $product->get_short_description();
                            if ( ! $short ) {
                                $short = wp_trim_words( $product->get_description(), 18, '&hellip;' );
                            }
                        ?>
                            <div class="product-card">
                                <a href="<?php echo esc_url( $permalink ); ?>"
                                   class="product-card-link"
                                   aria-label="<?php echo esc_attr( $product->get_name() ); ?>"></a>
                                <div class="product-image">
                                    <span class="product-index">SKU/<?php echo esc_html( str_pad( $idx, 3, '0', STR_PAD_LEFT ) ); ?></span>
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
                                    <div class="product-desc"><?php echo wp_kses_post( wpautop( $short ) ); ?></div>
                                    <div class="product-price"><?php echo $product->get_price_html(); ?></div>
                                    <div class="product-actions">
                                        <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
                                           class="btn btn-add-cart add_to_cart_button ajax_add_to_cart"
                                           data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
                                           data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
                                           data-quantity="1"
                                           rel="nofollow">В кошик</a>
                                        <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>&buy_now=1"
                                           class="btn btn-buy-now">Купити</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <?php
                    $big = 999999999;
                    $pagination = paginate_links( array(
                        'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                        'format'    => '?paged=%#%',
                        'current'   => $paged,
                        'total'     => (int) $query->max_num_pages,
                        'mid_size'  => 2,
                        'prev_text' => '← Назад',
                        'next_text' => 'Далі →',
                        'type'      => 'array',
                    ) );
                    if ( $pagination ) : ?>
                        <nav class="search-pagination" aria-label="Пагінація">
                            <?php foreach ( $pagination as $link ) : ?>
                                <span class="search-pagination__item"><?php echo wp_kses_post( $link ); ?></span>
                            <?php endforeach; ?>
                        </nav>
                    <?php endif;
                    wp_reset_postdata();
                    ?>

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
                            <?php if ( $is_search && $search_q ) : ?>
                                Ми не знайшли товарів за запитом <strong>«<?php echo esc_html( $search_q ); ?>»</strong>.
                                Спробуйте змінити запит або скинути фільтри.
                            <?php else : ?>
                                Спробуйте змінити фільтри або скинути їх.
                            <?php endif; ?>
                        </p>
                        <?php if ( $has_active_filters ) : ?>
                            <a href="<?php echo esc_url( $reset_url ); ?>" class="search-empty__link">Скинути фільтри →</a>
                        <?php else : ?>
                            <a href="<?php echo esc_url( $shop_url ); ?>" class="search-empty__link">Переглянути всі товари →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
