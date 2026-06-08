<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container header-inner">
        <div class="header-logo">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-text"><?php bloginfo( 'name' ); ?></a>
            <?php endif; ?>
        </div>
        <div class="header-right">

            <button class="header-search-toggle" type="button" aria-label="Пошук" aria-expanded="false" aria-controls="header-search-bar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>
            </button>

            <?php
            if ( is_user_logged_in() ) {
                $account_url = class_exists( 'WooCommerce' )
                    ? wc_get_account_endpoint_url( 'dashboard' )
                    : admin_url( 'profile.php' );
                $current_user  = wp_get_current_user();
                $account_label = $current_user->display_name ?: $current_user->user_login;
            } else {
                $account_url = class_exists( 'WooCommerce' )
                    ? wc_get_page_permalink( 'myaccount' )
                    : wp_login_url( home_url( $_SERVER['REQUEST_URI'] ?? '/' ) );
                $account_label = __( 'Увійти', 'corporate' );
            }
            ?>
            <a href="<?php echo esc_url( $account_url ); ?>" class="header-account" aria-label="<?php echo esc_attr( $account_label ); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
                <span><?php echo esc_html( $account_label ); ?></span>
            </a>

            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-btn" aria-label="Кошик">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="square" stroke-linejoin="miter">
                        <path d="M3 5h3l2 12h11l2-9H7"/>
                        <circle cx="9" cy="20" r="1.2"/>
                        <circle cx="18" cy="20" r="1.2"/>
                    </svg>
                    <span>Кошик</span>
                    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>

        <nav class="main-nav" aria-label="<?php esc_attr_e( 'Головне меню', 'corporate' ); ?>">
            <div class="container main-nav-inner">
                <button class="main-nav-toggle" type="button" aria-label="Меню" aria-expanded="false" aria-controls="primary-menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"/>
                        <path d="M3 12h18"/>
                        <path d="M3 18h18"/>
                    </svg>
                    <span>Меню</span>
                </button>
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_id'        => 'primary-menu',
                    'menu_class'     => 'main-nav-menu',
                    'fallback_cb'    => 'corporate_primary_menu_fallback',
                    'depth'          => 2,
                ) );
                ?>
            </div>
        </nav>

    <?php
    $product_categories = array();
    if ( class_exists( 'WooCommerce' ) ) {
        $product_categories = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ) );
        if ( is_wp_error( $product_categories ) ) {
            $product_categories = array();
        }
    }
    $current_cat = isset( $_GET['product_cat'] ) ? sanitize_text_field( wp_unslash( $_GET['product_cat'] ) ) : '';
    ?>
    <div class="header-search-bar" id="header-search-bar" aria-hidden="true">
        <div class="container">
            <div class="header-search-bar-inner">
                <form role="search" method="get" class="header-search-bar-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <?php if ( ! empty( $product_categories ) ) : ?>
                        <div class="header-search-cat-wrap">
                            <select name="product_cat" class="header-search-cat" aria-label="Категорія">
                                <option value="">Всі категорії</option>
                                <?php foreach ( $product_categories as $cat ) : ?>
                                    <option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $current_cat, $cat->slug ); ?>>
                                        <?php echo esc_html( $cat->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <input type="search" name="s" class="header-search-input" placeholder="Пошук..." aria-label="Пошук" value="<?php echo esc_attr( get_search_query() ); ?>">
                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                        <input type="hidden" name="post_type" value="product">
                    <?php endif; ?>
                    <button type="submit" class="header-search-submit" aria-label="Шукати">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5"/>
                        </svg>
                    </button>
                </form>
                <button type="button" class="header-search-close" aria-label="Закрити пошук">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>
