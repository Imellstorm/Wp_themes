<?php
/**
 * 404 — not found.
 *
 * @package devpoint
 */

get_header(); ?>

<section class="section">
	<div class="wrap">
		<div class="error-404">
			<div class="big">404</div>
			<h1><?php esc_html_e( 'This page wandered off.', 'devpoint' ); ?></h1>
			<p><?php esc_html_e( 'The link might be broken or the essay moved. Try searching or head back to the index.', 'devpoint' ); ?></p>
			<div class="hero-ctas" style="justify-content:center;">
				<a class="btn btn-primary btn-lg" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Back to devpoint', 'devpoint' ); ?>
					<?php devpoint_the_icon( 'arrow', [ 'size' => 16 ] ); ?>
				</a>
			</div>
			<div style="margin-top:32px;">
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</section>

<?php get_footer();
