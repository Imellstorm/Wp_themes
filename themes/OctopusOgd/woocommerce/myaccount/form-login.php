<?php
defined( 'ABSPATH' ) || exit;
do_action( 'woocommerce_before_customer_login_form' );
?>
<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
<div class="u-columns col2-set corporate-auth" id="customer_login">
    <div class="u-column1 col-1">
<?php endif; ?>

        <h2>Вхід</h2>
        <form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>
            <?php do_action( 'woocommerce_login_form_start' ); ?>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="username">Ім'я користувача або email <span class="required" aria-hidden="true">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="password">Пароль <span class="required" aria-hidden="true">*</span></label>
                <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
            </p>
            <?php do_action( 'woocommerce_login_form' ); ?>
            <p class="form-row">
                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                    <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
                    <span>Запам'ятати мене</span>
                </label>
                <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                <button type="submit" class="woocommerce-button button btn btn-buy-now" name="login" value="Увійти">Увійти</button>
            </p>
            <p class="woocommerce-LostPassword lost_password">
                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Забули пароль?</a>
            </p>
            <?php do_action( 'woocommerce_login_form_end' ); ?>
        </form>

<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
    </div>
    <div class="u-column2 col-2">
        <h2>Реєстрація</h2>
        <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
            <?php do_action( 'woocommerce_register_form_start' ); ?>
            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_username">Ім'я користувача <span class="required" aria-hidden="true">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" />
                </p>
            <?php endif; ?>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="reg_email">Електронна пошта <span class="required" aria-hidden="true">*</span></label>
                <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" />
            </p>
            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_password">Пароль <span class="required" aria-hidden="true">*</span></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
                </p>
            <?php else : ?>
                <p>Посилання для встановлення пароля надійде на вашу електронну пошту.</p>
            <?php endif; ?>
            <?php do_action( 'woocommerce_register_form' ); ?>
            <p class="woocommerce-form-row form-row">
                <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                <button type="submit" class="woocommerce-Button woocommerce-button button btn btn-buy-now" name="register" value="Зареєструватися">Зареєструватися</button>
            </p>
            <?php do_action( 'woocommerce_register_form_end' ); ?>
        </form>
    </div>
</div>
<?php endif; ?>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
