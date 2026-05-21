<?php
defined( 'ABSPATH' ) || exit;
$allowed_html = array( 'a' => array( 'href' => array() ), 'strong' => array() );
?>
<p class="corporate-account-greeting">
    <?php
    printf(
        wp_kses( 'Вітаємо, %1$s! <a href="%2$s">Вийти</a>', $allowed_html ),
        '<strong>' . esc_html( $current_user->display_name ) . '</strong>',
        esc_url( wc_logout_url() )
    );
    ?>
</p>
<p class="corporate-account-intro">
    <?php
    $orders_url  = esc_url( wc_get_endpoint_url( 'orders' ) );
    $address_url = esc_url( wc_get_endpoint_url( 'edit-address' ) );
    $account_url = esc_url( wc_get_endpoint_url( 'edit-account' ) );
    if ( wc_shipping_enabled() ) {
        printf(
            wp_kses( 'З панелі облікового запису ви можете переглядати <a href="%1$s">останні замовлення</a>, керувати <a href="%2$s">адресами доставки та оплати</a> і <a href="%3$s">редагувати пароль та особисті дані</a>.', $allowed_html ),
            $orders_url, $address_url, $account_url
        );
    } else {
        printf(
            wp_kses( 'З панелі облікового запису ви можете переглядати <a href="%1$s">останні замовлення</a>, керувати <a href="%2$s">платіжною адресою</a> і <a href="%3$s">редагувати пароль та особисті дані</a>.', $allowed_html ),
            $orders_url, $address_url, $account_url
        );
    }
    ?>
</p>
<?php
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
