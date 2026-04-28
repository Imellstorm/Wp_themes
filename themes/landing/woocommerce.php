<?php get_header(); ?>

<div class="woocommerce-notice-bar">
    <div class="container">
        <?php if ( function_exists( 'wc_print_notices' ) && wc_notice_count() > 0 ) wc_print_notices(); ?>
    </div>
</div>

<main class="section section--woocommerce">
    <div class="container">
        <?php woocommerce_content(); ?>
    </div>
</main>

<?php get_footer(); ?>
