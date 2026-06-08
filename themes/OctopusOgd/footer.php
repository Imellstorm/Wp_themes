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
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'footer',
                    'container'      => false,
                    'menu_class'     => 'footer-nav-menu',
                    'depth'          => 1,
                    'fallback_cb'    => 'corporate_footer_menu_fallback',
                ) );
                ?>
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
