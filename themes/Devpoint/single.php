<?php
/**
 * Single post — devpoint reading-room layout.
 * Mirrors post.html / post.jsx: breadcrumbs, title, lead, byline, hero,
 * dropcap body, CTA, related grid.
 *
 * @package devpoint
 */

get_header();

while ( have_posts() ) : the_post();
	$post    = get_post();
	$cat     = devpoint_primary_category( $post );
	$author  = get_userdata( (int) $post->post_author );
	$mins    = devpoint_read_minutes( $post );
	$excerpt = get_the_excerpt();
	$date    = get_the_date();

	$crumbs = [ [ 'label' => __( 'Home', 'devpoint' ), 'href' => home_url( '/' ) ] ];
	if ( $cat ) {
		$crumbs[] = [ 'label' => $cat->name, 'href' => get_term_link( $cat ) ];
	}
	$crumbs[] = [ 'label' => get_the_title() ];
	?>
	<div class="post-progress" data-post-progress style="width:0%"></div>

	<article class="post-page">
		<div class="wrap post-head-wrap">
			<?php devpoint_breadcrumbs( $crumbs ); ?>
			<h1 class="reader-title post-title"><?php the_title(); ?></h1>
			<?php if ( $excerpt ) : ?>
				<p class="reader-lead post-lead"><?php echo esc_html( wp_strip_all_tags( $excerpt ) ); ?></p>
			<?php endif; ?>
			<div class="reader-byline post-byline">
				<?php if ( $author ) : ?>
					<a href="<?php echo esc_url( get_author_posts_url( $author->ID ) ); ?>" style="display:flex;align-items:center;gap:14px;color:inherit;">
						<?php echo devpoint_avatar( $author, 44 ); // phpcs:ignore ?>
						<div>
							<div><b><?php echo esc_html( $author->display_name ); ?></b>
								<?php $role = get_the_author_meta( 'description', $author->ID );
								if ( $role ) : ?> · <span style="color:var(--ink-3);"><?php echo esc_html( wp_trim_words( $role, 4, '' ) ); ?></span><?php endif; ?>
							</div>
							<div style="color:var(--ink-3);font-size:13px;">
								<?php echo esc_html( $date ); ?>
								<span class="reader-meta-sep">·</span>
								<?php /* translators: %d: minutes */
								printf( esc_html__( '%d min read', 'devpoint' ), $mins ); ?>
							</div>
						</div>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<div class="wrap post-hero-wrap">
			<div class="reader-hero post-hero">
				<?php devpoint_thumb_art( $post ); ?>
			</div>
		</div>

		<?php
			ob_start();
			the_content();
			$raw_content = ob_get_clean();
			$processed   = devpoint_inject_toc( $raw_content );
		?>

		<?php if ( ! empty( $processed['toc'] ) ) : ?>
			<aside class="post-toc" id="post-toc" aria-label="<?php esc_attr_e( 'On this page', 'devpoint' ); ?>">
				<div class="post-toc-label"><?php esc_html_e( 'On this page', 'devpoint' ); ?></div>
				<ol class="post-toc-list">
					<?php foreach ( $processed['toc'] as $item ) : ?>
						<li class="post-toc-item lvl-<?php echo (int) $item['level']; ?>">
							<a href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a>
						</li>
					<?php endforeach; ?>
				</ol>
			</aside>
			<script>
			(function(){
				var toc = document.getElementById('post-toc');
				if (!toc) return;

				// Anchor the TOC below the hero image — until the hero
				// scrolls past the top of the viewport, at which point the
				// TOC sticks at MIN_TOP px.
				var hero    = document.querySelector('.post-hero-wrap') || document.querySelector('.post-hero');
				var MIN_TOP = 110;
				var GAP     = 24;
				var rafId   = 0;

				function syncTop() {
					rafId = 0;
					if (!hero) return;
					var bottom = hero.getBoundingClientRect().bottom;
					var top    = bottom + GAP;
					if (top < MIN_TOP) top = MIN_TOP;
					toc.style.top = top + 'px';
				}
				function schedule() {
					if (rafId) return;
					rafId = requestAnimationFrame(syncTop);
				}
				syncTop();
				window.addEventListener('scroll', schedule, { passive: true });
				window.addEventListener('resize', schedule);

				// Highlight the section the reader is currently on.
				if (!('IntersectionObserver' in window)) return;
				var links = Array.prototype.slice.call(toc.querySelectorAll('a[href^="#"]'));
				var map = {};
				links.forEach(function(a){
					var id = decodeURIComponent(a.getAttribute('href').slice(1));
					var el = document.getElementById(id);
					if (el) map[id] = a;
				});
				var ids = Object.keys(map);
				if (!ids.length) return;
				var setActive = function(id){
					links.forEach(function(a){ a.parentElement.classList.remove('active'); });
					if (map[id]) map[id].parentElement.classList.add('active');
				};
				var io = new IntersectionObserver(function(entries){
					entries.forEach(function(e){
						if (e.isIntersecting) setActive(e.target.id);
					});
				}, { rootMargin: '-20% 0px -70% 0px' });
				ids.forEach(function(id){ io.observe(document.getElementById(id)); });
			})();
			</script>
		<?php endif; ?>

		<div class="wrap post-body-wrap">
			<div class="reader-content post-content">
				<?php echo $processed['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — already filtered by the_content ?>
				<?php wp_link_pages( [
					'before' => '<div class="post-pagination">' . esc_html__( 'Pages:', 'devpoint' ),
					'after'  => '</div>',
				] ); ?>
			</div>

			<div class="reader-cta post-cta">
				<h3><?php esc_html_e( 'Enjoyed this essay?', 'devpoint' ); ?></h3>
				<p><?php
					/* translators: %s: site name */
					printf(
						esc_html__( '%s is published by the team at OGD Solutions, where we design and build sites and apps for a living.', 'devpoint' ),
						esc_html( get_bloginfo( 'name' ) )
					);
				?></p>
			</div>
		</div>
	</article>

	<?php
	/* ── Related ─────────────────────────────────────────────────────────── */
	$related_args = [
		'posts_per_page'      => 2,
		'post__not_in'        => [ $post->ID ],
		'ignore_sticky_posts' => true,
	];
	if ( $cat ) $related_args['category__in'] = [ $cat->term_id ];

	$related_q = new WP_Query( $related_args );
	if ( $related_q->have_posts() ) : ?>
		<section class="section post-related-section">
			<div class="wrap">
				<div class="section-h">
					<div>
						<div class="eyebrow"><?php esc_html_e( 'Keep reading', 'devpoint' ); ?></div>
						<h2><?php
							if ( $cat ) {
								printf( /* translators: %s: category */ wp_kses_post( __( 'More in <em>%s</em>', 'devpoint' ) ), esc_html( $cat->name ) );
							} else {
								esc_html_e( 'More essays', 'devpoint' );
							}
						?></h2>
					</div>
				</div>
				<div class="latest-grid post-related-grid">
					<?php while ( $related_q->have_posts() ) : $related_q->the_post(); ?>
						<?php devpoint_article_card( get_post() ); ?>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( comments_open() || get_comments_number() ) : ?>
		<?php comments_template(); ?>
	<?php endif; ?>

<?php endwhile;
get_footer();
