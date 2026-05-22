<?php
/**
 * Search results.
 *
 * @package devpoint
 */

get_header();
$query = get_search_query();
$total = $GLOBALS['wp_query']->found_posts;
?>

<section class="page-head">
	<div class="wrap">
		<?php devpoint_breadcrumbs( [
			[ 'label' => __( 'Home', 'devpoint' ), 'href' => home_url( '/' ) ],
			[ 'label' => __( 'Search', 'devpoint' ) ],
		] ); ?>
		<div class="cat-head">
			<h1 class="reader-title">
				<?php /* translators: %s: query */
				printf( esc_html__( 'Search: %s', 'devpoint' ), '<em>' . esc_html( $query ) . '</em>' ); ?>
			</h1>
			<p class="reader-lead">
				<?php /* translators: %d: result count */
				printf( esc_html( _n( '%d match.', '%d matches.', $total, 'devpoint' ) ), $total ); ?>
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
					<?php esc_html_e( 'No matches. Try a broader term.', 'devpoint' ); ?>
				</div>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer();
