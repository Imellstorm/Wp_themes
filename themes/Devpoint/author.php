<?php
/**
 * Author profile — mirrors author.html / author.jsx.
 *
 * @package devpoint
 */

get_header();

$author = get_queried_object();
if ( ! $author instanceof WP_User ) {
	$author = get_user_by( 'id', get_query_var( 'author' ) );
}
$total = $GLOBALS['wp_query']->found_posts;
$bio   = $author ? get_the_author_meta( 'description', $author->ID ) : '';
$role  = $author ? get_the_author_meta( 'devpoint_role', $author->ID ) : '';
?>

<section class="page-head">
	<div class="wrap">
		<?php devpoint_breadcrumbs( [
			[ 'label' => __( 'Home', 'devpoint' ), 'href' => home_url( '/' ) ],
			[ 'label' => __( 'Authors', 'devpoint' ) ],
			[ 'label' => $author ? $author->display_name : __( 'Unknown', 'devpoint' ) ],
		] ); ?>

		<?php if ( $author ) : ?>
			<div class="author-head">
				<div class="author-head-avatar" style="background:<?php echo esc_attr( devpoint_author_color( $author->ID ) ); ?>;">
					<?php echo esc_html( devpoint_initials( $author->display_name ) ); ?>
				</div>
				<div class="author-head-body">
					<?php if ( $role ) : ?>
						<div class="eyebrow"><?php echo esc_html( $role ); ?></div>
					<?php endif; ?>
					<h1 class="reader-title"><?php echo esc_html( $author->display_name ); ?></h1>
					<?php if ( $bio ) : ?>
						<p class="reader-lead"><?php echo esc_html( $bio ); ?></p>
					<?php endif; ?>
					<div class="author-head-meta">
						<span><?php
							/* translators: %d: essay count */
							printf( esc_html( _n( '%d essay published', '%d essays published', $total, 'devpoint' ) ), $total );
						?></span>
					</div>
				</div>
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
					if ( $author ) {
						/* translators: %s: author */
						printf( esc_html__( "%s hasn't published yet", 'devpoint' ), esc_html( $author->display_name ) );
					} else {
						esc_html_e( 'No essays here yet', 'devpoint' );
					}
					?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer();
