<?php
defined( 'ABSPATH' ) || exit;
do_action( 'woocommerce_before_lost_password_form' );
?>
<form method="post" class="woocommerce-ResetPassword lost_reset_password corporate-lost-password">
    <p>Забули пароль? Введіть ім'я користувача або email — на вашу пошту надійде посилання для створення нового пароля.</p>
    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
        <label for="user_login">Ім'я користувача або email <span class="required" aria-hidden="true">*</span></label>
        <input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" autocomplete="username" required aria-required="true" />
    </p>
    <div class="clear"></div>
    <?php do_action( 'woocommerce_lostpassword_form' ); ?>
    <p class="woocommerce-form-row form-row">
        <input type="hidden" name="wc_reset_password" value="true" />
        <button type="submit" class="woocommerce-Button button btn btn-buy-now" value="Скинути пароль">Скинути пароль</button>
    </p>
    <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
</form>
<?php do_action( 'woocommerce_after_lost_password_form' );
