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
 * Render a procedural thumbnail for a post. If the post has a featured
 * image, that wins. Otherwise we emit a random abstract composition keyed
 * on the post — same post → same image (deterministic seed).
 */
function devpoint_thumb_art( $post = null, $args = [] ) {
	$post = get_post( $post );
	if ( $post && has_post_thumbnail( $post ) ) {
		echo get_the_post_thumbnail( $post, $args['size'] ?? 'devpoint-card', [ 'loading' => 'lazy' ] );
		return;
	}

	$cat   = devpoint_primary_category( $post );
	$entry = devpoint_palette_entry_for_term( $cat );
	$tones = [ $entry[0], $entry[1], $entry[2] ];
	$key   = $post ? $post->post_name . '|' . $post->ID : ( $args['key'] ?? (string) wp_rand() );

	// Optional per-post seed offset — bumped by the "Regenerate image" button
	// in the post editor. When present, it re-rolls the procedural art.
	if ( $post ) {
		$offset = get_post_meta( $post->ID, '_devpoint_art_seed', true );
		if ( $offset !== '' ) $key .= '|' . $offset;
	}
	$seed = devpoint_hash( $key );

	devpoint_thumb_art_render_random( $tones, $seed );
}

/**
 * Catalog of all available thumb art styles. The hash bucket is stable
 * because the order is fixed — adding NEW styles at the end won't change
 * which style an existing post lands on.
 */
function devpoint_thumb_styles() {
	return [
		// Original eleven.
		'stack', 'phone', 'rings', 'grid', 'circles', 'layers',
		'bars', 'scatter', 'tree', 'split', 'chart',
		// New patterns from the design board.
		'burst', 'wave', 'stripes', 'orbit', 'mountains',
		'windowMock', 'code', 'checklist', 'donut', 'ribbon',
		'blob', 'isoCube', 'scribble', 'pinned', 'lighthouse',
		'bento', 'globeWire', 'stack3d', 'ticker', 'calendar',
	];
}

/**
 * Pick a style (and occasionally a faint secondary overlay) deterministic
 * to the seed, then render. ~20% of posts get a second pattern mixed in
 * at low opacity — keeps the grid varied without making cards busy.
 */
function devpoint_thumb_art_render_random( $tones, $seed ) {
	$styles = devpoint_thumb_styles();
	$n      = count( $styles );

	$primary = $styles[ devpoint_hash( $seed . '|primary' ) % $n ];

	$overlay = null;
	if ( devpoint_hash( $seed . '|mix' ) % 100 < 20 ) {
		$secondary = $styles[ devpoint_hash( $seed . '|secondary' ) % $n ];
		if ( $secondary !== $primary ) $overlay = $secondary;
	}

	devpoint_thumb_art_render( $primary, $tones, $overlay );
}

/**
 * Wrap the SVG and emit primary (+ optional ghosted overlay) style.
 */
function devpoint_thumb_art_render( $style, $tones, $overlay = null ) {
	list( $c1, $c2, $c3 ) = $tones;
	$common = 'width="100%" height="100%" viewBox="0 0 200 160" preserveAspectRatio="xMidYMid slice"';
	?>
	<div class="thumb-art" style="background: <?php echo esc_attr( $c2 ); ?>;">
		<svg <?php echo $common; ?>>
			<rect width="200" height="160" fill="<?php echo esc_attr( $c2 ); ?>"/>
			<?php devpoint_thumb_emit_style( $style, $tones ); ?>
			<?php if ( $overlay ) : ?>
				<g opacity="0.35"><?php devpoint_thumb_emit_style( $overlay, $tones ); ?></g>
			<?php endif; ?>
		</svg>
	</div>
	<?php
}

/**
 * Emit SVG fragments (no <svg> wrapper, no background rect) for one named
 * style. Coordinate system is 200 × 160. All shapes use the 3-tone palette
 * — $c1 primary, $c2 paper, $c3 accent.
 */
function devpoint_thumb_emit_style( $style, $tones ) {
	list( $c1, $c2, $c3 ) = $tones;

	switch ( $style ) {
		case 'stack': ?>
			<circle cx="160" cy="-10" r="80" fill="<?php echo esc_attr( $c3 ); ?>" opacity="0.7"/>
			<rect x="30" y="40" width="120" height="14" rx="3" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<rect x="30" y="62" width="90"  height="14" rx="3" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.55"/>
			<rect x="30" y="84" width="105" height="14" rx="3" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.3"/>
			<circle cx="155" cy="110" r="22" fill="<?php echo esc_attr( $c1 ); ?>"/>
		<?php break;

		case 'phone': ?>
			<circle cx="40" cy="135" r="50" fill="<?php echo esc_attr( $c3 ); ?>" opacity="0.6"/>
			<rect x="78" y="22" width="64" height="120" rx="12" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<rect x="84" y="32" width="52" height="78" rx="4" fill="<?php echo esc_attr( $c2 ); ?>" opacity="0.95"/>
			<rect x="92" y="44" width="36" height="6" rx="2" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.5"/>
			<rect x="92" y="56" width="28" height="6" rx="2" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.25"/>
			<rect x="92" y="76" width="36" height="20" rx="3" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.15"/>
			<circle cx="110" cy="125" r="6" fill="<?php echo esc_attr( $c2 ); ?>"/>
		<?php break;

		case 'rings': ?>
			<circle cx="100" cy="80" r="62" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.3"/>
			<circle cx="100" cy="80" r="44" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.5"/>
			<circle cx="100" cy="80" r="26" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<circle cx="100" cy="80" r="10" fill="<?php echo esc_attr( $c3 ); ?>"/>
		<?php break;

		case 'grid':
			for ( $i = 0; $i < 4; $i++ ) : for ( $j = 0; $j < 5; $j++ ) :
				$op = ( ( $i + $j ) % 3 === 0 ) ? 0.85 : 0.18;
				$x  = 20 + $j * 34;
				$y  = 18 + $i * 32; ?>
				<rect x="<?php echo $x; ?>" y="<?php echo $y; ?>" width="28" height="26" rx="4" fill="<?php echo esc_attr( $c1 ); ?>" opacity="<?php echo $op; ?>"/>
			<?php endfor; endfor;
			break;

		case 'circles': ?>
			<circle cx="60"  cy="60"  r="50" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.85"/>
			<circle cx="140" cy="100" r="36" fill="<?php echo esc_attr( $c3 ); ?>"/>
			<circle cx="120" cy="50"  r="14" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.5"/>
			<circle cx="170" cy="140" r="10" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.4"/>
		<?php break;

		case 'layers': ?>
			<rect x="36" y="36" width="128" height="92" rx="6" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.25"/>
			<rect x="48" y="52" width="128" height="92" rx="6" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.55"/>
			<rect x="60" y="68" width="128" height="92" rx="6" fill="<?php echo esc_attr( $c1 ); ?>"/>
		<?php break;

		case 'bars':
			$heights = [ 44, 72, 56, 96, 64, 110, 80 ]; ?>
			<?php foreach ( $heights as $i => $h ) : ?>
				<rect x="<?php echo 24 + $i * 22; ?>" y="<?php echo 140 - $h; ?>" width="14" height="<?php echo $h; ?>" rx="3" fill="<?php echo esc_attr( $c1 ); ?>" opacity="<?php echo 0.45 + ( $i % 3 ) * 0.18; ?>"/>
			<?php endforeach; ?>
			<path d="M22 90 Q60 60, 100 70 T180 40" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" stroke-linecap="round"/>
		<?php break;

		case 'scatter':
			for ( $i = 0; $i < 18; $i++ ) :
				$x  = 18 + ( $i * 37 ) % 170;
				$y  = 22 + ( ( $i * 53 ) % 110 );
				$r  = 4 + ( $i % 4 ) * 3;
				$op = 0.25 + ( $i % 3 ) * 0.25; ?>
				<circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="<?php echo $r; ?>" fill="<?php echo esc_attr( $c1 ); ?>" opacity="<?php echo $op; ?>"/>
			<?php endfor; ?>
			<circle cx="120" cy="80" r="28" fill="<?php echo esc_attr( $c3 ); ?>" opacity="0.85"/>
		<?php break;

		case 'tree': ?>
			<line x1="100" y1="30" x2="60"  y2="76"  stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.6"/>
			<line x1="100" y1="30" x2="140" y2="76"  stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.6"/>
			<line x1="60"  y1="76" x2="40"  y2="120" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.45"/>
			<line x1="60"  y1="76" x2="80"  y2="120" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.45"/>
			<line x1="140" y1="76" x2="120" y2="120" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.45"/>
			<line x1="140" y1="76" x2="160" y2="120" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2" opacity="0.45"/>
			<circle cx="100" cy="30"  r="14" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<circle cx="60"  cy="76"  r="10" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.7"/>
			<circle cx="140" cy="76"  r="10" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.7"/>
			<circle cx="40"  cy="120" r="7"  fill="<?php echo esc_attr( $c3 ); ?>"/>
			<circle cx="80"  cy="120" r="7"  fill="<?php echo esc_attr( $c3 ); ?>"/>
			<circle cx="120" cy="120" r="7"  fill="<?php echo esc_attr( $c3 ); ?>"/>
			<circle cx="160" cy="120" r="7"  fill="<?php echo esc_attr( $c3 ); ?>"/>
		<?php break;

		case 'split': ?>
			<rect x="0"   y="0" width="100" height="160" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<circle cx="50"  cy="80" r="26" fill="<?php echo esc_attr( $c3 ); ?>"/>
			<circle cx="150" cy="80" r="26" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<line x1="100" y1="0" x2="100" y2="160" stroke="<?php echo esc_attr( $c3 ); ?>" stroke-width="2"/>
		<?php break;

		case 'chart':
			$xs = [ 20, 60, 100, 140, 180 ];
			$ys = [ 130, 110, 90, 60, 30 ]; ?>
			<path d="M20 130 L60 110 L100 90 L140 60 L180 30 L180 140 L20 140 Z" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.15"/>
			<path d="M20 130 L60 110 L100 90 L140 60 L180 30" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="3" stroke-linecap="round"/>
			<?php foreach ( $xs as $i => $x ) : ?>
				<circle cx="<?php echo $x; ?>" cy="<?php echo $ys[ $i ]; ?>" r="4" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<?php endforeach;
			break;

		case 'burst':
			for ( $i = 0; $i < 12; $i++ ) :
				$ang = $i * M_PI / 6;
				$x1 = round( 100 + cos( $ang ) * 30, 1 );
				$y1 = round( 80 + sin( $ang ) * 30, 1 );
				$x2 = round( 100 + cos( $ang ) * 75, 1 );
				$y2 = round( 80 + sin( $ang ) * 75, 1 ); ?>
				<line x1="<?php echo $x1; ?>" y1="<?php echo $y1; ?>" x2="<?php echo $x2; ?>" y2="<?php echo $y2; ?>" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="3" stroke-linecap="round" opacity="<?php echo $i % 2 ? 0.55 : 0.85; ?>"/>
			<?php endfor; ?>
			<circle cx="100" cy="80" r="22" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<circle cx="100" cy="80" r="8"  fill="<?php echo esc_attr( $c2 ); ?>"/>
		<?php break;

		case 'wave': ?>
			<circle cx="160" cy="35" r="14" fill="<?php echo esc_attr( $c3 ); ?>"/>
			<path d="M0 110 Q 50 85, 100 105 T 200 95 L 200 160 L 0 160 Z" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.45"/>
			<path d="M0 128 Q 50 105, 100 125 T 200 115 L 200 160 L 0 160 Z" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.7"/>
			<path d="M0 145 Q 50 128, 100 140 T 200 135 L 200 160 L 0 160 Z" fill="<?php echo esc_attr( $c1 ); ?>"/>
		<?php break;

		case 'stripes': ?>
			<g transform="rotate(35 100 80)">
				<?php for ( $i = -6; $i <= 12; $i++ ) : ?>
					<rect x="<?php echo -50 + $i * 22; ?>" y="-100" width="11" height="360" fill="<?php echo esc_attr( $c1 ); ?>" opacity="<?php echo $i % 2 ? 0.65 : 0.3; ?>"/>
				<?php endfor; ?>
			</g>
			<circle cx="65" cy="45" r="18" fill="<?php echo esc_attr( $c3 ); ?>"/>
		<?php break;

		case 'orbit':
			$orbits = [ 0, 60, 120 ];
			foreach ( $orbits as $deg ) : ?>
				<ellipse cx="100" cy="80" rx="72" ry="22" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1.5" opacity="0.55" transform="rotate(<?php echo $deg; ?> 100 80)"/>
			<?php endforeach; ?>
			<circle cx="100" cy="80" r="14" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<circle cx="172" cy="80" r="5" fill="<?php echo esc_attr( $c3 ); ?>"/>
			<circle cx="64"  cy="42" r="5" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.8"/>
			<circle cx="136" cy="118" r="5" fill="<?php echo esc_attr( $c3 ); ?>"/>
		<?php break;

		case 'mountains': ?>
			<circle cx="155" cy="38" r="14" fill="<?php echo esc_attr( $c3 ); ?>"/>
			<polygon points="-10,150 50,70 110,150" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.55"/>
			<polygon points="60,150 110,40 170,150" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<polygon points="120,150 165,80 220,150" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.75"/>
			<rect x="0" y="145" width="200" height="15" fill="<?php echo esc_attr( $c1 ); ?>"/>
		<?php break;

		case 'windowMock': ?>
			<rect x="20" y="22" width="160" height="118" rx="8" fill="<?php echo esc_attr( $c2 ); ?>" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1.5" opacity="0.9"/>
			<rect x="20" y="22" width="160" height="20" rx="8" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<rect x="20" y="36" width="160" height="6"  fill="<?php echo esc_attr( $c1 ); ?>"/>
			<circle cx="30" cy="32" r="3" fill="<?php echo esc_attr( $c2 ); ?>"/>
			<circle cx="40" cy="32" r="3" fill="<?php echo esc_attr( $c2 ); ?>"/>
			<circle cx="50" cy="32" r="3" fill="<?php echo esc_attr( $c2 ); ?>"/>
			<rect x="30" y="56" width="100" height="6" rx="2" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.7"/>
			<rect x="30" y="68" width="70"  height="6" rx="2" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.35"/>
			<rect x="30" y="80" width="85"  height="6" rx="2" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.35"/>
			<rect x="30" y="106" width="50" height="20" rx="4" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<rect x="86" y="106" width="50" height="20" rx="4" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.35"/>
		<?php break;

		case 'code':
			$cols    = [ 22, 80, 138 ];
			$widths  = [ 50, 30, 45, 20, 40, 50, 28, 38 ];
			foreach ( $cols as $cx_col ) :
				foreach ( $widths as $i => $w ) :
					$op  = round( 0.3 + ( $i % 3 ) * 0.22, 2 );
					$col = ( $i % 4 === 0 ) ? $c3 : $c1; ?>
					<rect x="<?php echo $cx_col; ?>" y="<?php echo 25 + $i * 14; ?>" width="<?php echo $w; ?>" height="6" rx="2" fill="<?php echo esc_attr( $col ); ?>" opacity="<?php echo $op; ?>"/>
				<?php endforeach;
			endforeach;
			break;

		case 'checklist': ?>
			<rect x="32" y="26" width="136" height="108" rx="8" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1.5"/>
			<?php $rows = [ true, true, true, false, false ];
			foreach ( $rows as $i => $checked ) :
				$y = 42 + $i * 18; ?>
				<?php if ( $checked ) : ?>
					<rect x="44" y="<?php echo $y - 6; ?>" width="12" height="12" rx="2" fill="<?php echo esc_attr( $c1 ); ?>"/>
					<path d="M46 <?php echo $y; ?> L50 <?php echo $y + 3; ?> L55 <?php echo $y - 3; ?>" stroke="<?php echo esc_attr( $c2 ); ?>" stroke-width="2" fill="none" stroke-linecap="round"/>
				<?php else : ?>
					<rect x="44" y="<?php echo $y - 6; ?>" width="12" height="12" rx="2" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1.5"/>
				<?php endif; ?>
				<rect x="64" y="<?php echo $y - 3; ?>" width="<?php echo $checked ? 80 : 60; ?>" height="6" rx="2" fill="<?php echo esc_attr( $c1 ); ?>" opacity="<?php echo $checked ? 0.5 : 0.3; ?>"/>
			<?php endforeach;
			break;

		case 'donut':
			// Circumference of a r=40 circle is 2π·40 ≈ 251.3 — dashing into segments.
			?>
			<circle cx="100" cy="80" r="40" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="22" opacity="0.4"/>
			<circle cx="100" cy="80" r="40" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="22" stroke-dasharray="75 251" transform="rotate(-90 100 80)"/>
			<circle cx="100" cy="80" r="40" fill="none" stroke="<?php echo esc_attr( $c3 ); ?>" stroke-width="22" stroke-dasharray="35 251" transform="rotate(20 100 80)"/>
			<circle cx="100" cy="80" r="22" fill="<?php echo esc_attr( $c2 ); ?>"/>
		<?php break;

		case 'ribbon': ?>
			<path d="M-10 100 C 50 30, 110 150, 210 60" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="22" stroke-linecap="round" opacity="0.9"/>
			<path d="M-10 100 C 50 30, 110 150, 210 60" fill="none" stroke="<?php echo esc_attr( $c3 ); ?>" stroke-width="6"  stroke-linecap="round"/>
			<circle cx="50" cy="55" r="6" fill="<?php echo esc_attr( $c2 ); ?>"/>
			<circle cx="155" cy="98" r="5" fill="<?php echo esc_attr( $c2 ); ?>"/>
		<?php break;

		case 'blob': ?>
			<path d="M 65 50 C 45 30, 115 22, 145 48 C 175 76, 152 132, 105 132 C 55 132, 35 88, 65 50 Z" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<path d="M 65 50 C 45 30, 115 22, 145 48 C 175 76, 152 132, 105 132 C 55 132, 35 88, 65 50 Z" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1" opacity="0.6" transform="translate(8 6)"/>
			<circle cx="88"  cy="78"  r="7" fill="<?php echo esc_attr( $c2 ); ?>"/>
			<circle cx="122" cy="100" r="5" fill="<?php echo esc_attr( $c3 ); ?>"/>
		<?php break;

		case 'isoCube':
			// One cube = top rhombus + left + right faces.
			?>
			<polygon points="105,38 145,58 105,78 65,58" fill="<?php echo esc_attr( $c3 ); ?>"/>
			<polygon points="65,58 105,78 105,120 65,100" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.7"/>
			<polygon points="145,58 105,78 105,120 145,100" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<polygon points="155,90 175,100 155,110 135,100" fill="<?php echo esc_attr( $c3 ); ?>" opacity="0.7"/>
			<polygon points="135,100 155,110 155,135 135,125" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.55"/>
			<polygon points="175,100 155,110 155,135 175,125" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.8"/>
		<?php break;

		case 'scribble':
			for ( $i = 0; $i < 5; $i++ ) :
				$y  = 40 + $i * 22;
				$op = round( 0.4 + $i * 0.15, 2 ); ?>
				<path d="M 18 <?php echo $y; ?> C 60 <?php echo $y - 14; ?>, 120 <?php echo $y + 14; ?>, 182 <?php echo $y; ?>" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="3" stroke-linecap="round" opacity="<?php echo $op; ?>"/>
			<?php endfor;
			break;

		case 'pinned': ?>
			<g transform="rotate(-9 70 80)">
				<rect x="32" y="36" width="68" height="68" fill="<?php echo esc_attr( $c2 ); ?>" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1.5"/>
				<circle cx="42" cy="44" r="3" fill="<?php echo esc_attr( $c1 ); ?>"/>
			</g>
			<g transform="rotate(6 135 90)">
				<rect x="100" y="56" width="68" height="60" fill="<?php echo esc_attr( $c1 ); ?>"/>
				<circle cx="158" cy="64" r="3" fill="<?php echo esc_attr( $c2 ); ?>"/>
			</g>
		<?php break;

		case 'lighthouse': ?>
			<polygon points="100,30 30,46 170,46" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.25"/>
			<polygon points="100,30 70,90 130,90" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.45"/>
			<rect x="92" y="22" width="16" height="18" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<polygon points="92,40 108,40 116,140 84,140" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<rect x="82" y="86" width="36" height="6" fill="<?php echo esc_attr( $c3 ); ?>"/>
			<path d="M 0 145 Q 100 132 200 145 L 200 160 L 0 160 Z" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.5"/>
		<?php break;

		case 'bento': ?>
			<rect x="18" y="20" width="76" height="58" rx="6" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.85"/>
			<rect x="104" y="20" width="42" height="42" rx="6" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.5"/>
			<rect x="156" y="20" width="26" height="58" rx="6" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.4"/>
			<rect x="18" y="88" width="42" height="52" rx="6" fill="<?php echo esc_attr( $c2 ); ?>" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1.5"/>
			<rect x="70" y="88" width="56" height="52" rx="6" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<rect x="136" y="72" width="46" height="68" rx="6" fill="<?php echo esc_attr( $c3 ); ?>"/>
		<?php break;

		case 'globeWire': ?>
			<circle cx="100" cy="80" r="50" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1.5"/>
			<ellipse cx="100" cy="80" rx="50" ry="14" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1" opacity="0.55"/>
			<ellipse cx="100" cy="80" rx="50" ry="30" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1" opacity="0.45"/>
			<ellipse cx="100" cy="80" rx="20" ry="50" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1" opacity="0.55"/>
			<ellipse cx="100" cy="80" rx="38" ry="50" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1" opacity="0.35"/>
			<line x1="100" y1="30" x2="100" y2="130" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1" opacity="0.5"/>
			<line x1="50" y1="80" x2="150" y2="80" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1" opacity="0.5"/>
			<circle cx="130" cy="60" r="3" fill="<?php echo esc_attr( $c3 ); ?>"/>
		<?php break;

		case 'stack3d':
			$layers = [ [ 40, $c1, 0.45 ], [ 60, $c2, 1.0 ], [ 80, $c3, 0.85 ], [ 100, $c1, 0.7 ], [ 120, $c1, 0.95 ] ];
			foreach ( $layers as $L ) :
				list( $cy, $fill, $op ) = $L; ?>
				<polygon points="100,<?php echo $cy - 12; ?> 145,<?php echo $cy; ?> 100,<?php echo $cy + 12; ?> 55,<?php echo $cy; ?>" fill="<?php echo esc_attr( $fill ); ?>" opacity="<?php echo $op; ?>"/>
			<?php endforeach;
			break;

		case 'ticker': ?>
			<rect x="20" y="22" width="44" height="10" rx="2" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.7"/>
			<rect x="20" y="38" width="22" height="6" rx="2" fill="<?php echo esc_attr( $c1 ); ?>" opacity="0.35"/>
			<polyline points="20,95 50,80 80,90 110,65 140,55 170,40 180,48" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
			<polyline points="20,125 50,120 80,128 110,112 140,118 170,108 180,112" fill="none" stroke="<?php echo esc_attr( $c3 ); ?>" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
			<circle cx="170" cy="40" r="4" fill="<?php echo esc_attr( $c1 ); ?>"/>
		<?php break;

		case 'calendar': ?>
			<rect x="38" y="30" width="124" height="108" rx="6" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1.5"/>
			<rect x="38" y="30" width="124" height="18" rx="6" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<rect x="38" y="42" width="124" height="6" fill="<?php echo esc_attr( $c1 ); ?>"/>
			<line x1="56" y1="22" x2="56" y2="34" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2.5" stroke-linecap="round"/>
			<line x1="144" y1="22" x2="144" y2="34" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="2.5" stroke-linecap="round"/>
			<?php for ( $r = 0; $r < 4; $r++ ) : for ( $c = 0; $c < 7; $c++ ) :
				$cx_d = 50 + $c * 16;
				$cy_d = 68 + $r * 18;
				$is_hot = ( $r === 1 && $c === 3 ) || ( $r === 2 && $c === 5 ); ?>
				<?php if ( $is_hot ) : ?>
					<circle cx="<?php echo $cx_d; ?>" cy="<?php echo $cy_d; ?>" r="5" fill="<?php echo esc_attr( $c1 ); ?>"/>
				<?php else : ?>
					<circle cx="<?php echo $cx_d; ?>" cy="<?php echo $cy_d; ?>" r="5" fill="none" stroke="<?php echo esc_attr( $c1 ); ?>" stroke-width="1.2" opacity="0.55"/>
				<?php endif;
			endfor; endfor;
			break;

		default: ?>
			<rect width="200" height="160" fill="<?php echo esc_attr( $c1 ); ?>"/>
		<?php break;
	}
}
