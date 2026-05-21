<?php
defined( 'ABSPATH' ) || exit;
$page_title = ( 'billing' === $load_address ) ? 'Платіжна адреса' : 'Адреса доставки';
do_action( 'woocommerce_before_edit_account_address_form' );
?>
<?php if ( ! $load_address ) : ?>
    <?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>
    <form method="post" novalidate class="corporate-edit-address">
        <h2><?php echo esc_html( apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ) ); ?></h2>
        <div class="woocommerce-address-fields">
            <?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>
            <div class="woocommerce-address-fields__field-wrapper">
                <?php
                foreach ( $address as $key => $field ) {
                    woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
                }
                ?>
            </div>
            <?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>
            <p>
                <button type="submit" class="button btn btn-buy-now" name="save_address" value="Зберегти адресу">Зберегти адресу</button>
                <?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
                <input type="hidden" name="action" value="edit_address" />
            </p>
        </div>
    </form>
<?php endif; ?>
<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
