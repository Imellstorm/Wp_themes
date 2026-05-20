<?php
$shop_url     = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$delivery_url = home_url( '/dostavka-ta-oplata/' );
$offer_url    = home_url( '/dogovir-publichnoyi-oferty/' );

$delivery_page = get_page_by_path( 'dostavka-ta-oplata' );
if ( $delivery_page ) {
    $delivery_url = get_permalink( $delivery_page );
}
$offer_page = get_page_by_path( 'dogovir-publichnoyi-oferty' );
if ( $offer_page ) {
    $offer_url = get_permalink( $offer_page );
}
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-inner">
            <div class="footer-logo">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-text"><?php bloginfo( 'name' ); ?></a>
                <?php endif; ?>
                <span class="footer-brand">Octopus security</span>
            </div>
            <nav class="footer-nav" aria-label="Footer">
                <a href="<?php echo esc_url( $shop_url ); ?>" class="footer-link">Магазин</a>
                <a href="<?php echo esc_url( $delivery_url ); ?>" class="footer-link">Доставка та Оплата</a>
                <a href="<?php echo esc_url( $offer_url ); ?>" class="footer-link">Договір публічної оферти</a>
            </nav>
        </div>
        <div class="footer-copy">
            &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. Всі права захищені.
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
