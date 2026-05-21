<?php
defined( 'ABSPATH' ) || exit;
$customer_id = get_current_user_id();
if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
    $get_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array( 'billing' => 'Платіжна адреса', 'shipping' => 'Адреса доставки' ),
        $customer_id
    );
} else {
    $get_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array( 'billing' => 'Платіжна адреса' ),
        $customer_id
    );
}
?>
<p class="corporate-address-description">
    <?php echo esc_html( apply_filters( 'woocommerce_my_account_my_address_description', 'Ці адреси за замовчуванням будуть використовуватися на сторінці оформлення замовлення.' ) ); ?>
</p>
<div class="woocommerce-Addresses corporate-addresses">
    <?php foreach ( $get_addresses as $name => $address_title ) :
        $address = wc_get_account_formatted_address( $name );
    ?>
        <div class="woocommerce-Address">
            <header class="woocommerce-Address-title title">
                <h2><?php echo esc_html( $address_title ); ?></h2>
                <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit">
                    <?php echo $address ? 'Редагувати' : 'Додати'; ?>
                </a>
            </header>
            <address>
                <?php
                if ( $address ) {
                    echo wp_kses_post( $address );
                } else {
                    echo 'Ви ще не задали цей тип адреси.';
                }
                do_action( 'woocommerce_my_account_after_my_address', $name );
                ?>
            </address>
        </div>
    <?php endforeach; ?>
</div>
