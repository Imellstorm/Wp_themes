<?php
/**
 * Generic page template — narrow column, same reading typography as posts.
 *
 * @package devpoint
 */

get_header();

while ( have_posts() ) : the_post(); ?>
	<article class="post-page">
		<div class="wrap post-head-wrap">
			<?php devpoint_breadcrumbs( [
				[ 'label' => __( 'Home', 'devpoint' ), 'href' => home_url( '/' ) ],
				[ 'label' => get_the_title() ],
			] ); ?>
			<h1 class="reader-title post-title"><?php the_title(); ?></h1>
		</div>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="wrap post-hero-wrap">
				<div class="reader-hero post-hero">
					<?php the_post_thumbnail( 'devpoint-hero' ); ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="wrap post-body-wrap">
			<div class="reader-content post-content">
				<?php the_content(); ?>
				<?php wp_link_pages(); ?>
			</div>
		</div>
	</article>

	<?php if ( comments_open() || get_comments_number() ) comments_template(); ?>
<?php endwhile;

get_footer();
