<?php
/**
 * devpoint — procedural thumbnail SVG generator + category color palette.
 * Mirrors window.ThumbArt from icons.jsx.
 *
 * @package devpoint
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The fixed palette the mock used.
 */
function devpoint_palette() {
	return [
		[ '#D67A52', '#F4D8C8', '#FBF7EF', 'globe',     'web'     ], // terracotta
		[ '#5D8A6A', '#D5E2D2', '#FBF7EF', 'phone',     'mobile'  ], // sage
		[ '#8B5E83', '#E7D2E2', '#FBF7EF', 'briefcase', 'biz'     ], // plum
		[ '#E0A248', '#F8E5C2', '#FBF7EF', 'clipboard', 'pm'      ], // butter-deep
		[ '#3F6E81', '#C8DAE0', '#FBF7EF', 'trophy',    'cases'   ], // steel
	];
}

/**
 * Deterministic integer hash of a string (sdbm-style).
 */
function devpoint_hash( $s ) {
	$h = 0;
	$len = strlen( (string) $s );
	for ( $i = 0; $i < $len; $i++ ) {
		$h = ( ord( $s[ $i ] ) + ( $h << 6 ) + ( $h << 16 ) - $h ) & 0x7fffffff;
	}
	return $h;
}

/**
 * Pick a palette entry for a category term — prefer matching the slug,
 * else fall back to a deterministic hash bucket.
 */
function devpoint_palette_entry_for_term( $term ) {
	$palette = devpoint_palette();
	if ( $term && ! is_wp_error( $term ) ) {
		foreach ( $palette as $entry ) {
			if ( $entry[4] === $term->slug ) return $entry;
		}
		return $palette[ devpoint_hash( $term->slug ) % count( $palette ) ];
	}
	return $palette[0];
}

function devpoint_category_color( $term ) {
	$e = devpoint_palette_entry_for_term( $term );
	return $e[0];
}
function devpoint_category_glyph( $term ) {
	$e = devpoint_palette_entry_for_term( $term );
	return $e[3];
}

/**
 * Return the primary category term for a post.
 */
function devpoint_primary_category( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) return null;
	$terms = get_the_category( $post->ID );
	if ( empty( $terms ) ) return null;
	return $terms[0];
}

/**
 * Available thumb art styles, in a fixed order so the hash bucket is stable.
 */
function devpoint_thumb_styles() {
	return [ 'stack', 'phone', 'rings', 'grid', 'circles', 'layers', 'bars', 'scatter', 'tree', 'split', 'chart' ];
}

/**
 * Render a procedural thumbnail for a post. If the post has a featured
 * image, that wins. Otherwise we emit an SVG keyed on the post slug.
 */
function devpoint_thumb_art( $post = null, $args = [] ) {
	$post = get_post( $post );
	if ( $post && has_post_thumbnail( $post ) ) {
		echo get_the_post_thumbnail( $post, $args['size'] ?? 'devpoint-card', [ 'loading' => 'lazy' ] );
		return;
	}

	$cat     = devpoint_primary_category( $post );
	$entry   = devpoint_palette_entry_for_term( $cat );
	$tones   = [ $entry[0], $entry[1], $entry[2] ];
	$styles  = devpoint_thumb_styles();
	$key     = $post ? $post->post_name . '|' . $post->ID : ( $args['key'] ?? wp_rand() );
	$style   = $styles[ devpoint_hash( $key ) % count( $styles ) ];

	devpoint_thumb_art_render( $style, $tones );
}

/**
 * Low-level renderer — given a style name and 3 tones, output the SVG block.
 */
function devpoint_thumb_art_render( $style, $tones ) {
	list( $c1, $c2, $c3 ) = $tones;
	$common = 'width="100%" height="100%" viewBox="0 0 200 160" preserveAspectRatio="xMidYMid slice"';
	?>
	<div class="thumb-art" style="background: <?php echo esc_attr( $c2 ); ?>;">
		<svg <?php echo $common; ?>>
			<?php
			switch ( $style ) :
				case 'stack': ?>
					<rect width="200" height="160" fill="<?php echo esc_attr( $c2 ); ?>" />
					<circle cx="160" cy="-10" r="80" fill="<?php echo esc_attr( $c3 ); ?>" opacity="0.7" />
					<rect x="30" y="40" width="120" height="14" rx="3" fill="<?php echo esc_attr( $c1 ); ?>" />
					<rect x="30" y="62" width="90"  height="14" rx="3" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.55" />
					<rect x="30" y="84" width="105" height="14" rx="3" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.3" />
					<circle cx="155" cy="110" r="22" fill="<?php echo esc_attr( $c1 ); ?>" />
				<?php break;

				case 'phone': ?>
					<rect width="200" height="160" fill="<?php echo esc_attr( $c2 ); ?>" />
					<circle cx="40" cy="135" r="50" fill="<?php echo esc_attr( $c3 ); ?>" opacity="0.6" />
					<rect x="78" y="22" width="64" height="120" rx="12" fill="<?php echo esc_attr( $c1 ); ?>" />
					<rect x="84" y="32" width="52" height="78" rx="4" fill="<?php echo esc_attr( $c2 ); ?>" opacity="0.95" />
					<rect x="92" y="44" width="36" height="6" rx="2" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.5" />
					<rect x="92" y="56" width="28" height="6" rx="2" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.25" />
					<rect x="92" y="76" width="36" height="20" rx="3" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.15" />
					<circle cx="110" cy="125" r="6" fill="<?php echo esc_attr( $c2 ); ?>" />
				<?php break;

				case 'rings': ?>
					<rect width="200" height="160" fill="<?php echo esc_attr( $c2 ); ?>" />
					<circle cx="100" cy="80" r="62" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.3" />
					<circle cx="100" cy="80" r="44" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.5" />
					<circle cx="100" cy="80" r="26" fill="<?php echo esc_attr( $c1 ); ?>" />
					<circle cx="100" cy="80" r="10" fill="<?php echo esc_attr( $c3 ); ?>" />
				<?php break;

				case 'grid': ?>
					<rect width="200" height="160" fill="<?php echo esc_attr( $c2 ); ?>" />
					<?php for ( $i = 0; $i < 4; $i++ ) : for ( $j = 0; $j < 5; $j++ ) :
						$op = ( ( $i + $j ) % 3 === 0 ) ? 0.85 : 0.18;
						$x  = 20 + $j * 34;
						$y  = 18 + $i * 32; ?>
						<rect x="<?php echo $x; ?>" y="<?php echo $y; ?>" width="28" height="26" rx="4" fill="<?php echo esc_attr( $c1 ); ?>" opacity="<?php echo $op; ?>" />
					<?php endfor; endfor; ?>
				<?php break;

				case 'circles': ?>
					<rect width="200" height="160" fill="<?php echo esc_attr( $c2 ); ?>" />
					<circle cx="60"  cy="60"  r="50" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.85" />
					<circle cx="140" cy="100" r="36" fill="<?php echo esc_attr( $c3 ); ?>" />
					<circle cx="120" cy="50"  r="14" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.5" />
					<circle cx="170" cy="140" r="10" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.4" />
				<?php break;

				case 'layers': ?>
					<rect width="200" height="160" fill="<?php echo esc_attr( $c2 ); ?>" />
					<rect x="36" y="36" width="128" height="92" rx="6" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.25" />
					<rect x="48" y="52" width="128" height="92" rx="6" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.55" />
					<rect x="60" y="68" width="128" height="92" rx="6" fill="<?php echo esc_attr( $c1 ); ?>" />
				<?php break;

				case 'bars':
					$heights = [ 44, 72, 56, 96, 64, 110, 80 ]; ?>
					<rect width="200" height="160" fill="<?php echo esc_attr( $c2 ); ?>" />
					<?php foreach ( $heights as $i => $h ) : ?>
						<rect x="<?php echo 24 + $i * 22; ?>" y="<?php echo 140 - $h; ?>" width="14" height="<?php echo $h; ?>" rx="3" fill="<?php echo esc_attr( $c1 ); ?>" opacity="<?php echo 0.45 + ( $i % 3 ) * 0.18; ?>" />
					<?php endforeach; ?>
					<path d="M22 90 Q60 60, 100 70 T180 40" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" stroke-linecap="round" />
				<?php break;

				case 'scatter': ?>
					<rect width="200" height="160" fill="<?php echo esc_attr( $c2 ); ?>" />
					<?php for ( $i = 0; $i < 18; $i++ ) :
						$x = 18 + ( $i * 37 ) % 170;
						$y = 22 + ( ( $i * 53 ) % 110 );
						$r = 4 + ( $i % 4 ) * 3;
						$op = 0.25 + ( $i % 3 ) * 0.25; ?>
						<circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="<?php echo $r; ?>" fill="<?php echo esc_attr( $c1 ); ?>" opacity="<?php echo $op; ?>" />
					<?php endfor; ?>
					<circle cx="120" cy="80" r="28" fill="<?php echo esc_attr( $c3 ); ?>" opacity="0.85" />
				<?php break;

				case 'tree': ?>
					<rect width="200" height="160" fill="<?php echo esc_attr( $c2 ); ?>" />
					<line x1="100" y1="30" x2="60"  y2="76"  stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.6" />
					<line x1="100" y1="30" x2="140" y2="76"  stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.6" />
					<line x1="60"  y1="76" x2="40"  y2="120" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.45" />
					<line x1="60"  y1="76" x2="80"  y2="120" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.45" />
					<line x1="140" y1="76" x2="120" y2="120" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.45" />
					<line x1="140" y1="76" x2="160" y2="120" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.45" />
					<circle cx="100" cy="30"  r="14" fill="<?php echo esc_attr( $c1 ); ?>" />
					<circle cx="60"  cy="76"  r="10" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.7" />
					<circle cx="140" cy="76"  r="10" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.7" />
					<circle cx="40"  cy="120" r="7"  fill="<?php echo esc_attr( $c3 ); ?>" />
					<circle cx="80"  cy="120" r="7"  fill="<?php echo esc_attr( $c3 ); ?>" />
					<circle cx="120" cy="120" r="7"  fill="<?php echo esc_attr( $c3 ); ?>" />
					<circle cx="160" cy="120" r="7"  fill="<?php echo esc_attr( $c3 ); ?>" />
				<?php break;

				case 'split': ?>
					<rect x="0"   y="0" width="100" height="160" fill="<?php echo esc_attr( $c1 ); ?>" />
					<rect x="100" y="0" width="100" height="160" fill="<?php echo esc_attr( $c2 ); ?>" />
					<circle cx="50"  cy="80" r="26" fill="<?php echo esc_attr( $c3 ); ?>" />
					<circle cx="150" cy="80" r="26" fill="<?php echo esc_attr( $c1 ); ?>" />
					<line x1="100" y1="0" x2="100" y2="160" stroke="<?php echo esc_attr( $c3 ); ?>" stroke-width="2" />
				<?php break;

				case 'chart':
					$xs = [ 20, 60, 100, 140, 180 ];
					$ys = [ 130, 110, 90, 60, 30 ]; ?>
					<rect width="200" height="160" fill="<?php echo esc_attr( $c2 ); ?>" />
					<path d="M20 130 L60 110 L100 90 L140 60 L180 30" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="3" stroke-linecap="round" />
					<path d="M20 130 L60 110 L100 90 L140 60 L180 30 L180 140 L20 140 Z" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.15" />
					<?php foreach ( $xs as $i => $x ) : ?>
						<circle cx="<?php echo $x; ?>" cy="<?php echo $ys[ $i ]; ?>" r="4" fill="<?php echo esc_attr( $c1 ); ?>" />
					<?php endforeach; ?>
				<?php break;

				default: ?>
					<rect width="200" height="160" fill="<?php echo esc_attr( $c1 ); ?>" />
				<?php break;
			endswitch; ?>
		</svg>
	</div>
	<?php
}
