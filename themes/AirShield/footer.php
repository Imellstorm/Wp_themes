<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-logo">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-text"><?php bloginfo( 'name' ); ?></a>
            <?php endif; ?>
        </div>
        <div class="footer-copy">
            &copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
