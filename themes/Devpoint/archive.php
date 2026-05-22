<?php
/**
 * Archive — used for categories, tags, dates, post-type archives.
 * Mirrors category.html / category.jsx structure (head block + listing grid).
 *
 * @package devpoint
 */

get_header();

/* Build a contextual head block. */
$is_category = is_category();
$current     = $is_category ? get_queried_object() : null;
$total       = $GLOBALS['wp_query']->found_posts;

$crumbs = [
	[ 'label' => __( 'Home', 'devpoint' ), 'href' => home_url( '/' ) ],
];
if ( $is_category ) {
	$crumbs[] = [ 'label' => __( 'Categories', 'devpoint' ), 'href' => home_url( '/#all' ) ];
	$crumbs[] = [ 'label' => $current->name ];
} else {
	$crumbs[] = [ 'label' => get_the_archive_title() ];
}
?>

<section class="page-head">
	<div class="wrap">
		<?php devpoint_breadcrumbs( $crumbs ); ?>

		<?php if ( $is_category ) : ?>
			<div class="cat-head">
				<?php devpoint_cat_glyph( $current, [ 'size' => 30, 'stroke' => 1.6 ] ); ?>
				<h1 class="reader-title"><?php echo esc_html( $current->name ); ?></h1>
				<p class="reader-lead">
					<?php
					if ( $current->description ) {
						echo esc_html( $current->description );
					} else {
						/* translators: 1: count, 2: category name */
						printf(
							esc_html( _n( '%1$d essay on %2$s.', '%1$d essays on %2$s.', $total, 'devpoint' ) ),
							$total,
							esc_html( mb_strtolower( $current->name ) )
						);
					}
					?>
				</p>
				<?php
				$other = get_categories( [ 'hide_empty' => true, 'exclude' => [ $current->term_id ] ] );
				if ( $other ) : ?>
					<div class="cat-head-meta">
						<?php foreach ( $other as $c ) : ?>
							<a href="<?php echo esc_url( get_term_link( $c ) ); ?>" class="cat-pill"><?php echo esc_html( $c->name ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="cat-head">
				<h1 class="reader-title"><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
				<?php $desc = get_the_archive_description(); if ( $desc ) : ?>
					<p class="reader-lead"><?php echo wp_kses_post( $desc ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
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
					<?php
					if ( $is_category ) {
						/* translators: %s: category name */
						printf( esc_html__( 'No essays in %s yet', 'devpoint' ), esc_html( $current->name ) );
					} else {
						esc_html_e( 'No essays here yet', 'devpoint' );
					}
					?>
				</div>
				<div style="font-size:14px;"><?php esc_html_e( "We're working on it — check back soon.", 'devpoint' ); ?></div>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer();
