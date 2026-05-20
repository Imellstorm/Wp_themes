<?php
/**
 * Corporate / Octopus theme functions
 */

/**
 * Prevent WordPress.org from offering updates for this theme
 * (the directory slug "corporate" matches a theme on wp.org repo —
 *  without this filter, WP would try to overwrite our theme with that one)
 */
add_filter( 'http_request_args', function ( $r, $url ) {
    if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check' ) ) {
        return $r;
    }
    if ( ! isset( $r['body']['themes'] ) ) {
        return $r;
    }
    $themes = json_decode( $r['body']['themes'] );
    if ( isset( $themes->themes->corporate ) ) {
        unset( $themes->themes->corporate );
    }
    $r['body']['themes'] = wp_json_encode( $themes );
    return $r;
}, 5, 2 );

/**
 * Hide this theme from the "Available updates" list in WP admin
 */
add_filter( 'site_transient_update_themes', function ( $value ) {
    if ( isset( $value->response['corporate'] ) ) {
        unset( $value->response['corporate'] );
    }
    return $value;
} );

function corporate_setup() {
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
add_action( 'after_setup_theme', 'corporate_setup' );

function corporate_scripts() {
    wp_enqueue_style(
        'corporate-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap',
        array(),
        null
    );
    wp_enqueue_style( 'corporate-style', get_stylesheet_uri(), array(), '1.1.9' );
    wp_enqueue_script( 'corporate-faq', get_template_directory_uri() . '/assets/js/faq.js', array(), '1.0.4', true );

    if ( class_exists( 'WooCommerce' ) && is_front_page() ) {
        wp_enqueue_script( 'wc-add-to-cart' );
        wp_enqueue_script( 'wc-cart-fragments' );
    }

    // Override WC block strings (cart/checkout) — inline attach to wp-i18n
    if ( class_exists( 'WooCommerce' ) && function_exists( 'is_cart' )
         && ( is_cart() || is_checkout() || is_account_page() ) ) {
        if ( ! wp_script_is( 'wp-i18n', 'enqueued' ) && ! wp_script_is( 'wp-i18n', 'registered' ) ) {
            wp_enqueue_script( 'wp-i18n' );
        }
        $js_file = get_template_directory() . '/assets/js/wc-i18n.js';
        if ( file_exists( $js_file ) ) {
            wp_add_inline_script( 'wp-i18n', file_get_contents( $js_file ), 'after' );
        }
    }

    if ( class_exists( 'WooCommerce' ) && function_exists( 'is_product' ) && is_product() ) {
        wp_enqueue_script( 'wc-add-to-cart' );
        wp_enqueue_script( 'wc-cart-fragments' );
        wp_enqueue_script(
            'corporate-single-product',
            get_template_directory_uri() . '/assets/js/single-product.js',
            array( 'wc-add-to-cart' ),
            '1.0.1',
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'corporate_scripts' );

add_filter( 'pre_option_woocommerce_enable_ajax_add_to_cart', function () { return 'yes'; } );

function corporate_cart_count_fragment( $fragments ) {
    ob_start(); ?>
    <span class="cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
    <?php
    $fragments['span.cart-count'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'corporate_cart_count_fragment' );

function corporate_woocommerce_single_product_cleanup() {
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_all_notices', 10 );
    remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
}
add_action( 'init', 'corporate_woocommerce_single_product_cleanup' );

function corporate_remove_reviews_tab( $tabs ) {
    unset( $tabs['reviews'] );
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'corporate_remove_reviews_tab', 98 );

function corporate_remove_view_cart_link( $message, $products ) {
    return wp_strip_all_tags( preg_replace( '/<a [^>]*class="[^"]*button[^"]*wc-forward[^"]*"[^>]*>.*?<\/a>/is', '', $message ) );
}
add_filter( 'wc_add_to_cart_message_html', 'corporate_remove_view_cart_link', 10, 2 );

function corporate_description_under_cart() {
    global $product;
    if ( ! $product instanceof WC_Product ) return;
    $desc = $product->get_description();
    if ( ! $desc ) return;
    echo '<div class="corporate-product-description">';
    echo '<div class="corporate-product-description__content">' . wp_kses_post( wpautop( $desc ) ) . '</div>';
    echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'corporate_description_under_cart', 35 );

function corporate_buy_now_redirect( $url ) {
    if ( isset( $_GET['buy_now'] ) ) return wc_get_checkout_url();
    return $url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'corporate_buy_now_redirect' );

/**
 * My Account menu labels — Ukrainian + remove Downloads
 */
function corporate_wc_account_menu_items( $items ) {
    unset( $items['downloads'] );

    $map = array(
        'dashboard'       => 'Огляд',
        'orders'          => 'Замовлення',
        'edit-address'    => 'Адреси',
        'payment-methods' => 'Способи оплати',
        'edit-account'    => 'Дані облікового запису',
        'customer-logout' => 'Вийти',
    );
    foreach ( $map as $key => $label ) {
        if ( isset( $items[ $key ] ) ) {
            $items[ $key ] = $label;
        }
    }
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'corporate_wc_account_menu_items', 999 );

add_filter( 'woocommerce_my_account_my_address_description', function () {
    return 'Ці адреси за замовчуванням будуть використовуватися на сторінці оформлення замовлення.';
} );

/**
 * Translate WC strings to Ukrainian via gettext
 */
function corporate_wc_translations( $translated, $text, $domain ) {
    if ( ! in_array( $domain, array( 'woocommerce', 'default' ), true ) ) {
        return $translated;
    }

    static $strings = null;
    if ( $strings === null ) {
        $strings = array(
            'Cart' => 'Кошик',
            'Your cart is currently empty!' => 'Ваш кошик зараз порожній!',
            'Your cart is currently empty.' => 'Ваш кошик зараз порожній.',
            'Return to shop' => 'Повернутися до магазину',
            'Product' => 'Товар',
            'Price' => 'Ціна',
            'Quantity' => 'Кількість',
            'Subtotal' => 'Сума',
            'Total' => 'Усього',
            'Cart totals' => 'Підсумок кошика',
            'Update cart' => 'Оновити кошик',
            'Apply coupon' => 'Застосувати купон',
            'Coupon code' => 'Промокод',
            'Proceed to checkout' => 'Оформити замовлення',
            'New in store' => 'Новинки в магазині',
            'Remove this item' => 'Видалити',
            'Shipping' => 'Доставка',
            'Add to cart' => 'В кошик',
            'View cart' => 'Переглянути кошик',
            'Checkout' => 'Оформлення замовлення',
            'Billing details' => 'Платіжні дані',
            'Shipping details' => 'Дані доставки',
            'Order summary' => 'Підсумок замовлення',
            'Your order' => 'Ваше замовлення',
            'Place order' => 'Підтвердити замовлення',
            'First name' => "Ім'я",
            'Last name' => 'Прізвище',
            'Email address' => 'Електронна пошта',
            'Phone' => 'Телефон',
            'Country / Region' => 'Країна / Регіон',
            'Street address' => 'Вулиця та будинок',
            'Town / City' => 'Місто',
            'State / County' => 'Область',
            'Postcode / ZIP' => 'Поштовий індекс',
            'Order notes' => 'Примітки до замовлення',
            'My account' => 'Мій кабінет',
            'Login' => 'Вхід',
            'Register' => 'Реєстрація',
            'Username or email address' => "Ім'я користувача або email",
            'Username or email' => "Ім'я користувача або email",
            'Password' => 'Пароль',
            'Lost your password?' => 'Забули пароль?',
            'Remember me' => "Запам'ятати мене",
            'Log in' => 'Увійти',
            'Logout' => 'Вийти',
            'Log out' => 'Вийти',
            'Dashboard' => 'Огляд',
            'Orders' => 'Замовлення',
            'Addresses' => 'Адреси',
            'Account details' => 'Дані облікового запису',
            'Edit address' => 'Редагувати адресу',
            'No order has been made yet.' => 'Замовлень ще немає.',
            'Browse products' => 'Переглянути товари',
            'Recent orders' => 'Останні замовлення',
            'Order' => 'Замовлення',
            'Date' => 'Дата',
            'Status' => 'Статус',
            'View' => 'Переглянути',
            'Actions' => 'Дії',
            'Pay' => 'Оплатити',
            'Cancel' => 'Скасувати',
            'Billing address' => 'Платіжна адреса',
            'Shipping address' => 'Адреса доставки',
            'You have not set up this type of address yet.' => 'Ви ще не задали цей тип адреси.',
            'Add' => 'Додати',
            'Edit' => 'Редагувати',
            'Save address' => 'Зберегти адресу',
            'Save changes' => 'Зберегти зміни',
            'Display name' => "Ім'я для відображення",
            'Password change' => 'Зміна пароля',
            'Read more' => 'Детальніше',
            'Sale!' => 'Знижка!',
            'Out of stock' => 'Немає в наявності',
            'In stock' => 'В наявності',
            'Description' => 'Опис',
            'No products were found matching your selection.' => 'Не знайдено товарів за вашим запитом.',
        );
    }

    return isset( $strings[ $text ] ) ? $strings[ $text ] : $translated;
}
add_filter( 'gettext', 'corporate_wc_translations', 20, 3 );
add_filter( 'ngettext', 'corporate_wc_translations', 20, 3 );

function corporate_translate_wc_page_titles( $title, $id = 0 ) {
    if ( is_admin() || ! function_exists( 'wc_get_page_id' ) ) return $title;

    $cart_id     = wc_get_page_id( 'cart' );
    $checkout_id = wc_get_page_id( 'checkout' );
    $myacc_id    = wc_get_page_id( 'myaccount' );

    $map = array();
    if ( $cart_id > 0 )     $map[ $cart_id ]     = 'Кошик';
    if ( $checkout_id > 0 ) $map[ $checkout_id ] = 'Оформлення замовлення';
    if ( $myacc_id > 0 )    $map[ $myacc_id ]    = 'Мій кабінет';

    if ( $id && isset( $map[ $id ] ) ) return $map[ $id ];
    return $title;
}
add_filter( 'the_title', 'corporate_translate_wc_page_titles', 10, 2 );

/**
 * Customizer
 */
function corporate_customize_register( $wp_customize ) {
    $wp_customize->add_panel( 'corporate_panel', array(
        'title'    => __( 'Corporate Page', 'corporate' ),
        'priority' => 30,
    ) );

    // Hero
    $wp_customize->add_section( 'corporate_hero', array( 'title' => 'Hero Section', 'panel' => 'corporate_panel' ) );

    $wp_customize->add_setting( 'corporate_hero_image', array( 'type' => 'option', 'sanitize_callback' => 'absint' ) );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'corporate_hero_image', array(
        'label' => 'Hero Background Image', 'section' => 'corporate_hero', 'mime_type' => 'image',
    ) ) );

    $wp_customize->add_setting( 'corporate_hero_eyebrow', array( 'type' => 'option', 'default' => 'Octopus security', 'sanitize_callback' => 'sanitize_text_field' ) );
    $wp_customize->add_control( 'corporate_hero_eyebrow', array( 'label' => 'Hero Eyebrow', 'section' => 'corporate_hero', 'type' => 'text' ) );

    $wp_customize->add_setting( 'corporate_hero_title', array( 'type' => 'option', 'default' => 'Lorem Ipsum Dolor Sit Amet Consectetur', 'sanitize_callback' => 'sanitize_text_field' ) );
    $wp_customize->add_control( 'corporate_hero_title', array( 'label' => 'Hero Title (H1)', 'section' => 'corporate_hero', 'type' => 'text' ) );

    $wp_customize->add_setting( 'corporate_hero_subtitle', array( 'type' => 'option', 'default' => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.', 'sanitize_callback' => 'sanitize_text_field' ) );
    $wp_customize->add_control( 'corporate_hero_subtitle', array( 'label' => 'Hero Subtitle (H3)', 'section' => 'corporate_hero', 'type' => 'textarea' ) );

    // About
    $wp_customize->add_section( 'corporate_about', array( 'title' => 'About Section', 'panel' => 'corporate_panel' ) );

    $wp_customize->add_setting( 'corporate_about_title', array( 'type' => 'option', 'default' => 'Lorem Ipsum Dolor Sit Amet', 'sanitize_callback' => 'sanitize_text_field' ) );
    $wp_customize->add_control( 'corporate_about_title', array( 'label' => 'About Title (H2)', 'section' => 'corporate_about', 'type' => 'text' ) );

    for ( $i = 1; $i <= 5; $i++ ) {
        $wp_customize->add_setting( "corporate_about_point_{$i}_title", array( 'type' => 'option', 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
        $wp_customize->add_control( "corporate_about_point_{$i}_title", array( 'label' => sprintf( 'Point %d — Title', $i ), 'section' => 'corporate_about', 'type' => 'text' ) );

        $wp_customize->add_setting( "corporate_about_point_{$i}_text", array( 'type' => 'option', 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
        $wp_customize->add_control( "corporate_about_point_{$i}_text", array( 'label' => sprintf( 'Point %d — Description', $i ), 'section' => 'corporate_about', 'type' => 'textarea' ) );
    }

    // Products
    $wp_customize->add_section( 'corporate_products', array( 'title' => 'Products Section', 'panel' => 'corporate_panel' ) );

    $wp_customize->add_setting( 'corporate_products_title', array( 'type' => 'option', 'default' => 'Наші товари', 'sanitize_callback' => 'sanitize_text_field' ) );
    $wp_customize->add_control( 'corporate_products_title', array( 'label' => 'Products Section Title', 'section' => 'corporate_products', 'type' => 'text' ) );

    $wp_customize->add_setting( 'corporate_products_count', array( 'type' => 'option', 'default' => 6, 'sanitize_callback' => 'absint' ) );
    $wp_customize->add_control( 'corporate_products_count', array(
        'label' => 'Number of Products', 'section' => 'corporate_products', 'type' => 'number',
        'input_attrs' => array( 'min' => 1, 'max' => 20 ),
    ) );

    // FAQ
    $wp_customize->add_section( 'corporate_faq', array( 'title' => 'FAQ Section', 'panel' => 'corporate_panel' ) );

    for ( $i = 1; $i <= 5; $i++ ) {
        $wp_customize->add_setting( "corporate_faq_{$i}_question", array( 'type' => 'option', 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
        $wp_customize->add_control( "corporate_faq_{$i}_question", array( 'label' => sprintf( 'FAQ %d — Question', $i ), 'section' => 'corporate_faq', 'type' => 'text' ) );

        $wp_customize->add_setting( "corporate_faq_{$i}_answer", array( 'type' => 'option', 'default' => '', 'sanitize_callback' => 'wp_kses_post' ) );
        $wp_customize->add_control( "corporate_faq_{$i}_answer", array( 'label' => sprintf( 'FAQ %d — Answer', $i ), 'section' => 'corporate_faq', 'type' => 'textarea' ) );
    }
}
add_action( 'customize_register', 'corporate_customize_register' );

/**
 * Export/Import admin page
 */
function corporate_export_import_menu() {
    add_theme_page( 'Corporate Export/Import', 'Export/Import', 'manage_options', 'corporate-export-import', 'corporate_export_import_page' );
}
add_action( 'admin_menu', 'corporate_export_import_menu' );

function corporate_maybe_export() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
    if ( empty( $_GET['page'] ) || $_GET['page'] !== 'corporate-export-import' ) return;
    if ( empty( $_POST['corporate_action'] ) || $_POST['corporate_action'] !== 'export' ) return;
    if ( ! wp_verify_nonce( $_POST['_wpnonce_export'] ?? '', 'corporate_export' ) ) return;
    corporate_handle_export();
}
add_action( 'admin_init', 'corporate_maybe_export' );

function corporate_get_option_keys() {
    $keys = array( 'corporate_hero_image', 'corporate_hero_eyebrow', 'corporate_hero_title', 'corporate_hero_subtitle', 'corporate_about_title', 'corporate_products_title', 'corporate_products_count' );
    for ( $i = 1; $i <= 5; $i++ ) {
        $keys[] = "corporate_about_point_{$i}_title";
        $keys[] = "corporate_about_point_{$i}_text";
        $keys[] = "corporate_faq_{$i}_question";
        $keys[] = "corporate_faq_{$i}_answer";
    }
    return $keys;
}

function corporate_handle_export() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $keys = corporate_get_option_keys();
    $data = array();

    foreach ( $keys as $key ) {
        $val = get_option( $key, '' );
        if ( $key === 'corporate_hero_image' && $val ) {
            $data[ $key ] = array( 'id' => (int) $val, 'url' => wp_get_attachment_url( (int) $val ) ?: '' );
        } else {
            $data[ $key ] = $val;
        }
    }

    header( 'Content-Type: application/json' );
    header( 'Content-Disposition: attachment; filename="corporate-settings-' . date( 'Y-m-d' ) . '.json"' );
    echo wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    exit;
}

function corporate_handle_import() {
    if ( empty( $_FILES['corporate_import_file']['tmp_name'] ) ) return array( 'error', 'No file uploaded.' );
    $json = file_get_contents( $_FILES['corporate_import_file']['tmp_name'] );
    $data = json_decode( $json, true );
    if ( ! is_array( $data ) ) return array( 'error', 'Invalid JSON file.' );

    $keys = corporate_get_option_keys();
    $updated = 0;
    $img_skipped = array();

    foreach ( $keys as $key ) {
        if ( ! array_key_exists( $key, $data ) ) continue;
        $val = $data[ $key ];

        if ( $key === 'corporate_hero_image' ) {
            if ( is_array( $val ) ) {
                $url = $val['url'] ?? '';
                if ( ! $url ) { update_option( $key, '' ); $updated++; continue; }
                $existing_id = attachment_url_to_postid( $url );
                if ( $existing_id ) { update_option( $key, $existing_id ); $updated++; continue; }
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';
                $tmp = download_url( $url, 10 );
                if ( is_wp_error( $tmp ) ) { $img_skipped[] = $key; continue; }
                $file_array = array( 'name' => basename( wp_parse_url( $url, PHP_URL_PATH ) ), 'tmp_name' => $tmp );
                $new_id = media_handle_sideload( $file_array, 0 );
                if ( is_wp_error( $new_id ) ) { $img_skipped[] = $key; continue; }
                update_option( $key, $new_id );
                $updated++;
            }
            continue;
        }

        update_option( $key, $val );
        $updated++;
    }

    $msg = sprintf( 'Imported %d settings successfully.', $updated );
    if ( ! empty( $img_skipped ) ) $msg .= ' Skipped images: ' . implode( ', ', $img_skipped );
    return array( 'success', $msg );
}

function corporate_export_import_page() {
    $notice = '';
    $notice_type = 'success';

    if ( isset( $_POST['corporate_action'] ) && $_POST['corporate_action'] === 'import'
         && wp_verify_nonce( $_POST['_wpnonce_import'] ?? '', 'corporate_import' ) ) {
        $result = corporate_handle_import();
        $notice_type = $result[0] === 'error' ? 'error' : 'success';
        $notice = $result[1];
    }
    ?>
    <div class="wrap">
        <h1>Corporate — Export / Import</h1>
        <?php if ( $notice ) : ?>
            <div class="notice notice-<?php echo esc_attr( $notice_type ); ?> is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
        <?php endif; ?>

        <div style="display:flex;gap:40px;margin-top:20px;">
            <div style="flex:1;background:#fff;padding:24px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2>Export Settings</h2>
                <p>Download all Corporate settings as a JSON file. Images are exported as URLs.</p>
                <form method="post">
                    <?php wp_nonce_field( 'corporate_export', '_wpnonce_export' ); ?>
                    <input type="hidden" name="corporate_action" value="export">
                    <p><button type="submit" class="button button-primary">Download JSON</button></p>
                </form>
            </div>

            <div style="flex:1;background:#fff;padding:24px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2>Import Settings</h2>
                <p>Upload a previously exported JSON file.</p>
                <form method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field( 'corporate_import', '_wpnonce_import' ); ?>
                    <input type="hidden" name="corporate_action" value="import">
                    <p><input type="file" name="corporate_import_file" accept=".json"></p>
                    <p><button type="submit" class="button button-primary">Import JSON</button></p>
                </form>
            </div>
        </div>
    </div>
    <?php
}
