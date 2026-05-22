<?php
/**
 * Blog index — used both for the WordPress posts page and the static front page
 * (front-page.php is just an alias of this template).
 *
 * Sections: Featured → Categories → Latest with category filters.
 *
 * @package devpoint
 */

get_header();

/* ── Featured (top-4) ────────────────────────────────────────────────────── */
/* Sticky posts come first, then the most recent published posts fill in. */
$sticky = get_option( 'sticky_posts', [] );
$featured_q = new WP_Query( [
	'posts_per_page'      => 4,
	'post__in'            => $sticky ? $sticky : null,
	'ignore_sticky_posts' => true,
	'orderby'             => $sticky ? 'post__in' : 'date',
	'order'               => 'DESC',
] );
if ( ! $featured_q->have_posts() ) {
	$featured_q = new WP_Query( [
		'posts_per_page'      => 4,
		'ignore_sticky_posts' => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
	] );
}
$featured_ids = wp_list_pluck( $featured_q->posts, 'ID' );

/* ── Latest (9) + categories for filter chips. Excludes featured to avoid dupes. */
$active_cat_slug = isset( $_GET['cat'] ) ? sanitize_title( wp_unslash( $_GET['cat'] ) ) : 'all';
$latest_args = [
	'posts_per_page'      => 9,
	'ignore_sticky_posts' => true,
	'post__not_in'        => $featured_ids,
];
if ( $active_cat_slug && $active_cat_slug !== 'all' ) {
	$latest_args['category_name'] = $active_cat_slug;
}
$latest_q = new WP_Query( $latest_args );

$categories = get_categories( [ 'hide_empty' => true ] );
$all_count  = wp_count_posts()->publish;
?>

<?php if ( $featured_q->have_posts() ) : ?>
<section class="section">
	<div class="wrap">
		<div class="section-h">
			<div>
				<div class="eyebrow"><?php esc_html_e( "Editor's picks", 'devpoint' ); ?></div>
				<h2><?php echo wp_kses_post( __( 'The four <em>most-read</em> essays', 'devpoint' ) ); ?></h2>
			</div>
			<div class="right"><span><?php esc_html_e( 'Hand-picked from the archive', 'devpoint' ); ?></span></div>
		</div>
		<div class="featured-cards">
			<?php while ( $featured_q->have_posts() ) : $featured_q->the_post(); ?>
				<?php devpoint_article_card( get_post() ); ?>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( ! empty( $categories ) ) : ?>
<section class="section">
	<div class="wrap">
		<div class="section-h">
			<div>
				<div class="eyebrow"><?php esc_html_e( 'Browse by topic', 'devpoint' ); ?></div>
				<h2><?php echo wp_kses_post( __( 'Pick a <em>thread</em> to follow', 'devpoint' ) ); ?></h2>
			</div>
		</div>
		<div class="cat-grid">
			<?php foreach ( $categories as $c ) : ?>
				<a class="cat-tile" href="<?php echo esc_url( get_term_link( $c ) ); ?>">
					<?php devpoint_cat_glyph( $c ); ?>
					<div>
						<div class="cat-name"><?php echo esc_html( $c->name ); ?></div>
						<div class="cat-count"><span><?php
							/* translators: %d: essay count */
							printf( esc_html( _n( '%d essay', '%d essays', $c->count, 'devpoint' ) ), $c->count );
						?></span></div>
					</div>
					<span class="cat-arrow"><?php devpoint_the_icon( 'arrow', [ 'size' => 14 ] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="section" id="all">
	<div class="wrap">
		<div class="section-h">
			<div>
				<div class="eyebrow"><?php esc_html_e( 'Fresh off the press', 'devpoint' ); ?></div>
				<h2><?php echo wp_kses_post( __( 'Latest <em>essays</em>', 'devpoint' ) ); ?></h2>
			</div>
		</div>

		<div class="filter-bar" role="tablist">
			<a class="filter-chip<?php echo $active_cat_slug === 'all' ? ' active' : ''; ?>"
			   href="<?php echo esc_url( add_query_arg( 'cat', 'all', home_url( '/' ) ) ); ?>#all">
				<?php esc_html_e( 'All', 'devpoint' ); ?> <span style="opacity:.6;">· <?php echo (int) $all_count; ?></span>
			</a>
			<?php foreach ( $categories as $c ) : ?>
				<a class="filter-chip<?php echo $active_cat_slug === $c->slug ? ' active' : ''; ?>"
				   href="<?php echo esc_url( add_query_arg( 'cat', $c->slug, home_url( '/' ) ) ); ?>#all">
					<?php echo esc_html( $c->name ); ?>
				</a>
			<?php endforeach; ?>
		</div>

		<?php if ( $latest_q->have_posts() ) : ?>
			<div class="latest-grid">
				<?php while ( $latest_q->have_posts() ) : $latest_q->the_post(); ?>
					<?php devpoint_article_card( get_post() ); ?>
				<?php endwhile; ?>
			</div>
			<?php if ( $latest_q->max_num_pages > 1 ) : ?>
				<div class="load-more">
					<a class="btn btn-ghost btn-lg" href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ?: home_url( '/?paged=2' ) ); ?>">
						<?php esc_html_e( 'Load more essays', 'devpoint' ); ?>
						<?php devpoint_the_icon( 'chevron', [ 'size' => 16, 'class' => 'rot-90' ] ); ?>
					</a>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<div class="empty-state">
				<div class="icon"><?php devpoint_the_icon( 'search', [ 'size' => 22 ] ); ?></div>
				<div style="margin-bottom:4px;color:var(--ink-2);font-weight:500;">
					<?php esc_html_e( 'No essays in this category yet', 'devpoint' ); ?>
				</div>
				<div style="font-size:14px;"><?php esc_html_e( "We're working on it — check back soon.", 'devpoint' ); ?></div>
			</div>
		<?php endif; wp_reset_postdata(); ?>
	</div>
</section>

<?php
get_footer();
