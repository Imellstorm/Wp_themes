<?php
/**
 * Landing theme functions
 */

/**
 * Theme setup
 */
function landing_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array(
        'height'      => 50,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'landing_setup' );

/**
 * Enqueue styles and scripts
 */
function landing_scripts() {
    wp_enqueue_style( 'landing-style', get_stylesheet_uri(), array(), '1.0.6' );
    wp_enqueue_script( 'landing-faq', get_template_directory_uri() . '/assets/js/faq.js', array(), '1.0.0', true );

    if ( class_exists( 'WooCommerce' ) && is_front_page() ) {
        wp_enqueue_script( 'wc-add-to-cart' );
        wp_enqueue_script( 'wc-cart-fragments' );
    }

    if ( class_exists( 'WooCommerce' ) && function_exists( 'is_product' ) && is_product() ) {
        // wc-add-to-cart provides window.wc_add_to_cart_params (wc_ajax_url, cart_url etc.)
        wp_enqueue_script( 'wc-add-to-cart' );
        wp_enqueue_script( 'wc-cart-fragments' );
        wp_enqueue_script(
            'landing-single-product',
            get_template_directory_uri() . '/assets/js/single-product.js',
            array( 'wc-add-to-cart' ),
            '1.0.3',
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'landing_scripts' );

/**
 * Force WooCommerce AJAX add-to-cart on regardless of admin setting
 */
add_filter( 'pre_option_woocommerce_enable_ajax_add_to_cart', function () { return 'yes'; } );

/**
 * Refresh the header cart count after AJAX add-to-cart
 */
function landing_cart_count_fragment( $fragments ) {
    ob_start(); ?>
    <span class="cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
    <?php
    $fragments['span.cart-count'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'landing_cart_count_fragment' );

/**
 * Single product page — strip elements not used by the landing site
 */
function landing_woocommerce_single_product_cleanup() {
    // Remove "Related products" block
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
    // Remove product meta block (categories, tags, SKU) — landing has no taxonomy
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    // Remove the tabs section — Description is moved inline below the Add-to-cart button
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

    // Notices are rendered manually inside the .woocommerce-notice-bar above the section.
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_all_notices', 10 );
    remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
}
add_action( 'init', 'landing_woocommerce_single_product_cleanup' );

/**
 * Remove the "Reviews" tab from single product pages
 */
function landing_remove_reviews_tab( $tabs ) {
    unset( $tabs['reviews'] );
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'landing_remove_reviews_tab', 98 );

/**
 * Strip the "View cart" link from the add-to-cart success notice
 */
function landing_remove_view_cart_link( $message, $products ) {
    return wp_strip_all_tags( preg_replace( '/<a [^>]*class="[^"]*button[^"]*wc-forward[^"]*"[^>]*>.*?<\/a>/is', '', $message ) );
}
add_filter( 'wc_add_to_cart_message_html', 'landing_remove_view_cart_link', 10, 2 );

/**
 * Render full product description inside the summary, right under the
 * Add-to-cart button (priority 30 is the cart form, so we use 35).
 */
function landing_description_under_cart() {
    global $product;
    if ( ! $product instanceof WC_Product ) {
        return;
    }
    $desc = $product->get_description();
    if ( ! $desc ) {
        return;
    }
    echo '<div class="landing-product-description">';
    echo '<div class="landing-product-description__content">' . wp_kses_post( wpautop( $desc ) ) . '</div>';
    echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'landing_description_under_cart', 35 );

/**
 * Buy Now redirect — add to cart then go straight to checkout
 */
function landing_buy_now_redirect( $url ) {
    if ( isset( $_GET['buy_now'] ) ) {
        return wc_get_checkout_url();
    }
    return $url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'landing_buy_now_redirect' );

/**
 * Customizer — Landing Page settings
 */
function landing_customize_register( $wp_customize ) {

    // Panel
    $wp_customize->add_panel( 'landing_panel', array(
        'title'    => __( 'Landing Page', 'landing' ),
        'priority' => 30,
    ) );

    // === Hero Section ===
    $wp_customize->add_section( 'landing_hero', array(
        'title' => __( 'Hero Section', 'landing' ),
        'panel' => 'landing_panel',
    ) );

    $wp_customize->add_setting( 'landing_hero_image', array(
        'type'              => 'option',
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'landing_hero_image', array(
        'label'     => __( 'Hero Background Image', 'landing' ),
        'section'   => 'landing_hero',
        'mime_type' => 'image',
    ) ) );

    $wp_customize->add_setting( 'landing_hero_title', array(
        'type'              => 'option',
        'default'           => 'Lorem Ipsum Dolor Sit Amet Consectetur',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'landing_hero_title', array(
        'label'   => __( 'Hero Title (H1)', 'landing' ),
        'section' => 'landing_hero',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'landing_hero_subtitle', array(
        'type'              => 'option',
        'default'           => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'landing_hero_subtitle', array(
        'label'   => __( 'Hero Subtitle (H3)', 'landing' ),
        'section' => 'landing_hero',
        'type'    => 'textarea',
    ) );

    // === About Section ===
    $wp_customize->add_section( 'landing_about', array(
        'title' => __( 'About Section', 'landing' ),
        'panel' => 'landing_panel',
    ) );

    $wp_customize->add_setting( 'landing_about_image', array(
        'type'              => 'option',
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'landing_about_image', array(
        'label'     => __( 'About Image', 'landing' ),
        'section'   => 'landing_about',
        'mime_type' => 'image',
    ) ) );

    $wp_customize->add_setting( 'landing_about_title', array(
        'type'              => 'option',
        'default'           => 'Lorem Ipsum Dolor Sit Amet',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'landing_about_title', array(
        'label'   => __( 'About Title (H2)', 'landing' ),
        'section' => 'landing_about',
        'type'    => 'text',
    ) );

    for ( $i = 1; $i <= 5; $i++ ) {
        $wp_customize->add_setting( "landing_about_point_{$i}_title", array(
            'type'              => 'option',
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "landing_about_point_{$i}_title", array(
            'label'   => sprintf( __( 'Point %d — Title', 'landing' ), $i ),
            'section' => 'landing_about',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( "landing_about_point_{$i}_text", array(
            'type'              => 'option',
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "landing_about_point_{$i}_text", array(
            'label'   => sprintf( __( 'Point %d — Description', 'landing' ), $i ),
            'section' => 'landing_about',
            'type'    => 'textarea',
        ) );
    }

    // === Products Section ===
    $wp_customize->add_section( 'landing_products', array(
        'title' => __( 'Products Section', 'landing' ),
        'panel' => 'landing_panel',
    ) );

    $wp_customize->add_setting( 'landing_products_title', array(
        'type'              => 'option',
        'default'           => 'Our Products',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'landing_products_title', array(
        'label'   => __( 'Products Section Title', 'landing' ),
        'section' => 'landing_products',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'landing_products_count', array(
        'type'              => 'option',
        'default'           => 5,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'landing_products_count', array(
        'label'   => __( 'Number of Products', 'landing' ),
        'section' => 'landing_products',
        'type'    => 'number',
        'input_attrs' => array( 'min' => 1, 'max' => 20 ),
    ) );

    // === FAQ Section ===
    $wp_customize->add_section( 'landing_faq', array(
        'title' => __( 'FAQ Section', 'landing' ),
        'panel' => 'landing_panel',
    ) );

    for ( $i = 1; $i <= 5; $i++ ) {
        $wp_customize->add_setting( "landing_faq_{$i}_question", array(
            'type'              => 'option',
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "landing_faq_{$i}_question", array(
            'label'   => sprintf( __( 'FAQ %d — Question', 'landing' ), $i ),
            'section' => 'landing_faq',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( "landing_faq_{$i}_answer", array(
            'type'              => 'option',
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        ) );
        $wp_customize->add_control( "landing_faq_{$i}_answer", array(
            'label'   => sprintf( __( 'FAQ %d — Answer', 'landing' ), $i ),
            'section' => 'landing_faq',
            'type'    => 'textarea',
        ) );
    }
}
add_action( 'customize_register', 'landing_customize_register' );

/**
 * Export/Import — admin page for Landing Page settings
 */
function landing_export_import_menu() {
    add_theme_page(
        __( 'Landing Export/Import', 'landing' ),
        __( 'Export/Import', 'landing' ),
        'manage_options',
        'landing-export-import',
        'landing_export_import_page'
    );
}
add_action( 'admin_menu', 'landing_export_import_menu' );

/**
 * Handle export early, before any HTML output
 */
function landing_maybe_export() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( empty( $_GET['page'] ) || $_GET['page'] !== 'landing-export-import' ) {
        return;
    }
    if ( empty( $_POST['landing_action'] ) || $_POST['landing_action'] !== 'export' ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['_wpnonce_export'] ?? '', 'landing_export' ) ) {
        return;
    }
    landing_handle_export(); // calls exit
}
add_action( 'admin_init', 'landing_maybe_export' );

function landing_get_option_keys() {
    $keys = array(
        'landing_hero_image',
        'landing_hero_title',
        'landing_hero_subtitle',
        'landing_about_image',
        'landing_about_title',
        'landing_products_title',
        'landing_products_count',
    );
    for ( $i = 1; $i <= 5; $i++ ) {
        $keys[] = "landing_about_point_{$i}_title";
        $keys[] = "landing_about_point_{$i}_text";
        $keys[] = "landing_faq_{$i}_question";
        $keys[] = "landing_faq_{$i}_answer";
    }
    return $keys;
}

function landing_handle_export() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $keys = landing_get_option_keys();
    $data = array();

    foreach ( $keys as $key ) {
        $val = get_option( $key, '' );
        // For image fields, export the URL instead of attachment ID
        if ( in_array( $key, array( 'landing_hero_image', 'landing_about_image' ), true ) && $val ) {
            $data[ $key ] = array(
                'id'  => (int) $val,
                'url' => wp_get_attachment_url( (int) $val ) ?: '',
            );
        } else {
            $data[ $key ] = $val;
        }
    }

    header( 'Content-Type: application/json' );
    header( 'Content-Disposition: attachment; filename="landing-settings-' . date( 'Y-m-d' ) . '.json"' );
    echo wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    exit;
}

function landing_handle_import() {
    if ( empty( $_FILES['landing_import_file']['tmp_name'] ) ) {
        return array( 'error', 'No file uploaded.' );
    }

    $json = file_get_contents( $_FILES['landing_import_file']['tmp_name'] );
    $data = json_decode( $json, true );

    if ( ! is_array( $data ) ) {
        return array( 'error', 'Invalid JSON file.' );
    }

    $keys         = landing_get_option_keys();
    $updated      = 0;
    $img_skipped  = array();

    foreach ( $keys as $key ) {
        if ( ! array_key_exists( $key, $data ) ) {
            continue;
        }

        $val = $data[ $key ];

        // Image fields
        if ( in_array( $key, array( 'landing_hero_image', 'landing_about_image' ), true ) ) {
            if ( is_array( $val ) ) {
                $url = $val['url'] ?? '';
                if ( ! $url ) {
                    update_option( $key, '' );
                    $updated++;
                    continue;
                }

                // Check if attachment already exists by URL
                $existing_id = attachment_url_to_postid( $url );
                if ( $existing_id ) {
                    update_option( $key, $existing_id );
                    $updated++;
                    continue;
                }

                // Try to sideload
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';
                $tmp = download_url( $url, 10 );
                if ( is_wp_error( $tmp ) ) {
                    $img_skipped[] = $key . ' (' . $tmp->get_error_message() . ')';
                    continue;
                }
                $file_array = array(
                    'name'     => basename( wp_parse_url( $url, PHP_URL_PATH ) ),
                    'tmp_name' => $tmp,
                );
                $new_id = media_handle_sideload( $file_array, 0 );
                if ( is_wp_error( $new_id ) ) {
                    $img_skipped[] = $key . ' (' . $new_id->get_error_message() . ')';
                    continue;
                }
                update_option( $key, $new_id );
                $updated++;
            }
            continue;
        }

        // Text fields
        update_option( $key, $val );
        $updated++;
    }

    $msg = sprintf( 'Imported %d settings successfully.', $updated );
    if ( ! empty( $img_skipped ) ) {
        $msg .= ' Skipped images: ' . implode( ', ', $img_skipped ) . ' — upload them manually via Customize.';
    }

    return array( 'success', $msg );
}

function landing_export_import_page() {
    $notice      = '';
    $notice_type = 'success';

    if ( isset( $_POST['landing_action'] ) && $_POST['landing_action'] === 'import'
         && wp_verify_nonce( $_POST['_wpnonce_import'] ?? '', 'landing_import' ) ) {
        $result      = landing_handle_import();
        $notice_type = $result[0] === 'error' ? 'error' : 'success';
        $notice      = $result[1];
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Landing Page — Export / Import', 'landing' ); ?></h1>

        <?php if ( $notice ) : ?>
            <div class="notice notice-<?php echo esc_attr( $notice_type ); ?> is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
        <?php endif; ?>

        <div style="display:flex;gap:40px;margin-top:20px;">

            <!-- Export -->
            <div style="flex:1;background:#fff;padding:24px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2><?php esc_html_e( 'Export Settings', 'landing' ); ?></h2>
                <p><?php esc_html_e( 'Download all Landing Page settings as a JSON file. Images are exported as URLs.', 'landing' ); ?></p>
                <form method="post">
                    <?php wp_nonce_field( 'landing_export', '_wpnonce_export' ); ?>
                    <input type="hidden" name="landing_action" value="export">
                    <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Download JSON', 'landing' ); ?></button></p>
                </form>
            </div>

            <!-- Import -->
            <div style="flex:1;background:#fff;padding:24px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2><?php esc_html_e( 'Import Settings', 'landing' ); ?></h2>
                <p><?php esc_html_e( 'Upload a previously exported JSON file. Images will be downloaded to the media library automatically.', 'landing' ); ?></p>
                <form method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field( 'landing_import', '_wpnonce_import' ); ?>
                    <input type="hidden" name="landing_action" value="import">
                    <p><input type="file" name="landing_import_file" accept=".json"></p>
                    <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Import JSON', 'landing' ); ?></button></p>
                </form>
            </div>

        </div>
    </div>
    <?php
}
