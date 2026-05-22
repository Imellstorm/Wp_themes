<?php
/**
 * Footer for devpoint — contact CTA, brand block, subscribe, footer-base.
 *
 * @package devpoint
 */
?>
</div><!-- /#content -->

<?php devpoint_contact_cta(); ?>

<footer class="ftr">
	<div class="wrap">
		<div class="ftr-grid">
			<div>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand">
					<span class="brand-mark"><?php echo esc_html( mb_strtolower( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ) ); ?></span>
					<span><?php bloginfo( 'name' ); ?>
						<small><?php echo esc_html( apply_filters( 'devpoint_footer_brand_tagline', __( 'by OGD Solutions', 'devpoint' ) ) ); ?></small>
					</span>
				</a>
				<?php if ( is_active_sidebar( 'footer-blurb' ) ) : ?>
					<?php dynamic_sidebar( 'footer-blurb' ); ?>
				<?php else : ?>
					<p class="ftr-brand-blurb">
						<?php echo esc_html( get_bloginfo( 'description' ) ?: __( 'A reading-room about shipping websites, apps and the businesses around them.', 'devpoint' ) ); ?>
					</p>
				<?php endif; ?>
			</div>

			<div class="ftr-subscribe">
				<h4><?php esc_html_e( 'Subscribe to the newsletter', 'devpoint' ); ?></h4>
				<form class="ftr-sub-form" data-subscribe-form action="#" method="post" novalidate>
					<input type="email" inputmode="email" name="email"
					       placeholder="<?php esc_attr_e( 'you@yourcompany.com', 'devpoint' ); ?>" required />
					<button type="submit"><?php esc_html_e( 'Subscribe', 'devpoint' ); ?></button>
				</form>
				<div class="ftr-sub-success" hidden>
					<?php devpoint_the_icon( 'check', [ 'size' => 16 ] ); ?>
					<span data-subscribe-ok-text><?php esc_html_e( 'Thanks — check your inbox for confirmation.', 'devpoint' ); ?></span>
				</div>
				<div class="ftr-sub-err" hidden data-subscribe-err></div>
			</div>

			<div class="ftr-col">
				<h4><?php esc_html_e( 'Read', 'devpoint' ); ?></h4>
				<?php if ( has_nav_menu( 'footer' ) ) :
					wp_nav_menu( [
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => '',
						'depth'          => 1,
						'fallback_cb'    => false,
					] );
				else : ?>
					<ul>
						<li><a href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ?: home_url( '/' ) ); ?>"><?php esc_html_e( 'Latest essays', 'devpoint' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/feed/' ) ); ?>"><?php esc_html_e( 'RSS feed', 'devpoint' ); ?></a></li>
					</ul>
				<?php endif; ?>
			</div>
		</div>

		<div class="ftr-base">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.</span>
			<?php if ( has_nav_menu( 'primary' ) ) :
				wp_nav_menu( [
					'theme_location' => 'primary',
					'container'      => 'nav',
					'menu_class'     => '',
					'depth'          => 1,
					'fallback_cb'    => false,
				] );
			endif; ?>
		</div>
	</div>
</footer>

<div class="search-overlay" data-search-overlay hidden>
	<div class="search-panel" data-search-panel role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search', 'devpoint' ); ?>">
		<div class="search-input-row">
			<?php devpoint_the_icon( 'search', [ 'size' => 18 ] ); ?>
			<input type="search" data-search-input placeholder="<?php esc_attr_e( 'Search essays, categories, authors…', 'devpoint' ); ?>" />
			<span class="esc">esc</span>
		</div>
		<div class="search-results" data-search-results></div>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
