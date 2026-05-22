<?php
/**
 * Fallback template — also used as the blog index when no home.php is loaded.
 *
 * @package devpoint
 */

get_header(); ?>

<section class="page-head">
	<div class="wrap">
		<?php devpoint_breadcrumbs( [
			[ 'label' => __( 'Home', 'devpoint' ), 'href' => home_url( '/' ) ],
			[ 'label' => __( 'Essays', 'devpoint' ) ],
		] ); ?>
		<div class="cat-head">
			<h1 class="reader-title"><?php echo esc_html__( 'All essays', 'devpoint' ); ?></h1>
			<p class="reader-lead">
				<?php
				$count = (int) wp_count_posts()->publish;
				/* translators: %d: essay count */
				printf( esc_html( _n( '%d essay published so far.', '%d essays published so far.', $count, 'devpoint' ) ), $count );
				?>
			</p>
		</div>
	</div>
</section>

<section class="section section-grid">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="latest-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php devpoint_article_card( get_post() ); ?>
				<?php endwhile; ?>
			</div>
			<?php devpoint_pagination(); ?>
		<?php else : ?>
			<div class="empty-state">
				<div class="icon"><?php devpoint_the_icon( 'search', [ 'size' => 22 ] ); ?></div>
				<div style="margin-bottom:4px;color:var(--ink-2);font-weight:500;">
					<?php esc_html_e( 'No essays yet', 'devpoint' ); ?>
				</div>
				<div style="font-size:14px;"><?php esc_html_e( "We're working on it — check back soon.", 'devpoint' ); ?></div>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer();
