<?php
/**
 * devpoint — reusable view fragments. PHP versions of the design's JSX
 * components: Avatar, CardMeta, ArticleCard, Breadcrumbs.
 *
 * @package devpoint
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Two-letter initials from a name. Mirrors the JS initials() helper.
 */
function devpoint_initials( $name ) {
	$parts = preg_split( '/\s+/', trim( (string) $name ) );
	$letters = '';
	foreach ( array_slice( $parts, 0, 2 ) as $p ) {
		if ( $p !== '' ) $letters .= mb_strtoupper( mb_substr( $p, 0, 1 ) );
	}
	return $letters ?: '·';
}

/**
 * Deterministic accent color for an author (so each author gets a stable hue).
 */
function devpoint_author_color( $user_id ) {
	$palette = [ '#D67A52', '#5D8A6A', '#8B5E83', '#3F6E81', '#E0A248', '#1E1916' ];
	return $palette[ devpoint_hash( 'u' . $user_id ) % count( $palette ) ];
}

/**
 * Avatar pill (colored circle with initials).
 *
 * @param int|WP_User $user_id
 * @param int         $size
 */
function devpoint_avatar( $user_id, $size = 22 ) {
	if ( $user_id instanceof WP_User ) {
		$user    = $user_id;
		$user_id = $user->ID;
	} else {
		$user = get_user_by( 'id', (int) $user_id );
	}
	if ( ! $user ) return '';

	$initials = devpoint_initials( $user->display_name );
	$color    = devpoint_author_color( $user_id );
	$style    = sprintf(
		'background:%s;width:%dpx;height:%dpx;font-size:%dpx;',
		esc_attr( $color ),
		$size,
		$size,
		(int) round( $size * 0.45 )
	);
	return '<span class="avatar" style="' . $style . '">' . esc_html( $initials ) . '</span>';
}

/**
 * Card meta strip: author + read-time.
 *
 * @param WP_Post|null $post
 */
function devpoint_card_meta( $post = null ) {
	$post   = get_post( $post );
	if ( ! $post ) return;
	$author = get_userdata( (int) $post->post_author );
	$mins   = devpoint_read_minutes( $post );
	?>
	<div class="card-meta">
		<?php if ( $author ) : ?>
			<a class="author-tag" href="<?php echo esc_url( get_author_posts_url( $author->ID ) ); ?>"
			   onclick="event.stopPropagation();">
				<?php echo devpoint_avatar( $author, 22 ); // phpcs:ignore ?>
				<span class="author-name"><?php echo esc_html( $author->display_name ); ?></span>
			</a>
		<?php endif; ?>
		<span class="meta-time"><?php
			/* translators: %d: minutes */
			printf( esc_html__( '%d min read', 'devpoint' ), $mins ); ?></span>
	</div>
	<?php
}

/**
 * One article card (clickable). Mirrors <ArticleCard> from app.jsx.
 *
 * @param WP_Post|null $post
 */
function devpoint_article_card( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) return;

	$cat       = devpoint_primary_category( $post );
	$cat_url   = $cat ? get_term_link( $cat ) : '#';
	$cat_name  = $cat ? $cat->name : __( 'Essay', 'devpoint' );
	$permalink = get_permalink( $post );
	$title     = get_the_title( $post );
	$excerpt   = get_the_excerpt( $post );
	$date      = mysql2date( get_option( 'date_format', 'M j, Y' ), $post->post_date );
	?>
	<article class="card">
		<div class="card-thumb">
			<?php devpoint_thumb_art( $post ); ?>
			<a class="card-cat" href="<?php echo esc_url( $cat_url ); ?>">
				<?php echo esc_html( $cat_name ); ?>
			</a>
			<span class="card-date"><?php echo esc_html( $date ); ?></span>
		</div>
		<h3 title="<?php echo esc_attr( $title ); ?>">
			<a class="card-link" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
		</h3>
		<p title="<?php echo esc_attr( wp_strip_all_tags( $excerpt ) ); ?>">
			<?php echo esc_html( wp_strip_all_tags( $excerpt ) ); ?>
		</p>
		<?php devpoint_card_meta( $post ); ?>
	</article>
	<?php
}

/**
 * Breadcrumbs.
 *
 * @param array $items Each item: [ 'label' => '', 'href' => '' (optional) ]
 */
function devpoint_breadcrumbs( $items ) {
	if ( empty( $items ) ) return;
	$total = count( $items );
	?>
	<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'devpoint' ); ?>">
		<ol>
			<?php foreach ( $items as $i => $it ) : ?>
				<li>
					<?php if ( ! empty( $it['href'] ) ) : ?>
						<a href="<?php echo esc_url( $it['href'] ); ?>"><?php echo esc_html( $it['label'] ); ?></a>
					<?php else : ?>
						<span aria-current="page" title="<?php echo esc_attr( $it['label'] ); ?>">
							<?php echo esc_html( $it['label'] ); ?>
						</span>
					<?php endif; ?>
					<?php if ( $i < $total - 1 ) : ?>
						<span class="crumb-sep" aria-hidden="true">/</span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

/**
 * The contact CTA section — used at the bottom of most templates.
 */
function devpoint_contact_cta() {
	$wa_url    = apply_filters( 'devpoint_whatsapp_url',    'https://wa.me/15551234567' );
	$email     = apply_filters( 'devpoint_contact_email',   'hello@ogd.solutions' );
	$blurb_h   = apply_filters( 'devpoint_cta_heading',     __( 'If you would need any development feel <em>free to contact me</em>.', 'devpoint' ) );
	$blurb_p   = apply_filters( 'devpoint_cta_body',        __( "I'm Bram, founder of OGD Solutions — the team behind devpoint. We design and build websites, mobile apps and internal tools. Drop a line on WhatsApp or email and let's talk.", 'devpoint' ) );
	$eyebrow   = apply_filters( 'devpoint_cta_eyebrow',     __( 'Work with us', 'devpoint' ) );
	$btn_label = apply_filters( 'devpoint_cta_button',      __( 'Message on WhatsApp', 'devpoint' ) );
	?>
	<section class="section section-newsletter">
		<div class="wrap">
			<div class="newsletter">
				<div class="newsletter-inner">
					<div>
						<div class="nl-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
						<h2><?php echo wp_kses_post( $blurb_h ); ?></h2>
					</div>
					<div class="contact-actions">
						<a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener" class="btn btn-cta btn-lg">
							<?php devpoint_the_icon( 'wa', [ 'size' => 18 ] ); ?>
							<?php echo esc_html( $btn_label ); ?>
						</a>
						<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="contact-secondary">
							<?php devpoint_the_icon( 'arrow', [ 'size' => 14 ] ); ?>
							<?php echo esc_html( $email ); ?>
						</a>
						<p class="contact-blurb"><?php echo esc_html( $blurb_p ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
}
