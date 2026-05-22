<?php
/**
 * devpoint — SVG icon library + the big hero illustration.
 * Mirrors window.Icons / window.HeroArt from the static design.
 *
 * @package devpoint
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Return SVG markup for one of the design's named icons.
 *
 * @param string $name  Icon slug.
 * @param array  $args  { size, stroke, fill, class, title }
 */
function devpoint_icon( $name, $args = [] ) {
	$args = wp_parse_args( $args, [
		'size'   => 18,
		'stroke' => 1.7,
		'fill'   => 'none',
		'class'  => '',
		'title'  => '',
	] );

	$paths = [
		'search'    => 'M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16ZM21 21l-4.3-4.3',
		'menu'      => 'M4 7h16M4 12h16M4 17h16',
		'close'     => 'M6 6l12 12M18 6L6 18',
		'arrow'     => 'M5 12h14M13 6l6 6-6 6',
		'arrowUp'   => 'M7 17L17 7M9 7h8v8',
		'chevron'   => 'M9 6l6 6-6 6',
		'check'     => 'M5 12l5 5L20 7',
		'spark'     => 'M12 3v4M12 17v4M3 12h4M17 12h4M6 6l3 3M15 15l3 3M6 18l3-3M15 9l3-3',
		'globe'     => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20ZM2 12h20M12 2c2.5 3 4 6.5 4 10s-1.5 7-4 10c-2.5-3-4-6.5-4-10s1.5-7 4-10Z',
		'phone'     => 'M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2ZM11 18h2',
		'briefcase' => 'M3 8h18v12H3zM9 8V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v3M3 13h18',
		'clipboard' => 'M9 4h6a1 1 0 0 1 1 1v1h3v15H5V6h3V5a1 1 0 0 1 1-1ZM9 11h6M9 15h4',
		'trophy'    => 'M7 4h10v4a5 5 0 0 1-10 0V4ZM3 4h4v3a3 3 0 0 1-3 3M21 4h-4v3a3 3 0 0 0 3 3M9 14h6v3H9zM7 21h10',
		'bookmark'  => 'M6 3h12v18l-6-4-6 4V3Z',
		'clock'     => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20ZM12 7v5l3 2',
		'wa'        => 'M20 12a8 8 0 1 1-15.3 3.2L4 20l4.9-1.3A8 8 0 0 1 20 12ZM9 9c.2-.5.5-.5.8-.5.2 0 .5 0 .8.4l.6 1.3a.6.6 0 0 1 0 .6l-.4.5c-.1.1-.2.3-.1.5.3.7 1 1.4 1.7 1.7.2.1.4 0 .5-.1l.5-.5c.1-.1.4-.2.6-.1l1.2.6c.5.2.5.5.5.7 0 .3-.3 1.1-.5 1.3-.3.3-1.4.7-2.4.3-.9-.4-2-1.2-2.7-1.9-.7-.7-1.5-1.8-1.9-2.7-.4-1 0-2 .3-2.4Z',
		'sun'       => 'M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10ZM12 2v2M12 20v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M2 12h2M20 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4',
	];
	$fillIcons = [
		'twitter'   => 'M22 5.8a8.2 8.2 0 0 1-2.4.7 4.2 4.2 0 0 0 1.8-2.3 8.2 8.2 0 0 1-2.6 1A4.1 4.1 0 0 0 11.5 9a11.7 11.7 0 0 1-8.5-4.3 4.1 4.1 0 0 0 1.3 5.5A4 4 0 0 1 2.4 9v.1A4.1 4.1 0 0 0 5.7 13a4.1 4.1 0 0 1-1.9.1 4.1 4.1 0 0 0 3.8 2.9A8.3 8.3 0 0 1 2 17.6 11.7 11.7 0 0 0 8.3 19.5c7.5 0 11.7-6.3 11.7-11.7v-.5A8.4 8.4 0 0 0 22 5.8Z',
		'linkedin'  => 'M5 4a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM3 7h4v14H3zM10 7h4v2a4 4 0 0 1 7 3v9h-4v-9a2 2 0 0 0-4 0v9h-4V7Z',
		'github'    => 'M12 2a10 10 0 0 0-3.2 19.5c.5.1.7-.2.7-.5v-2c-2.8.6-3.4-1.2-3.4-1.2-.5-1.2-1.1-1.5-1.1-1.5-.9-.6.1-.6.1-.6 1 .1 1.5 1 1.5 1 .9 1.5 2.3 1.1 2.9.9.1-.7.4-1.1.6-1.4-2.2-.2-4.6-1.1-4.6-5 0-1.1.4-2 1-2.7-.1-.3-.4-1.3.1-2.7 0 0 .8-.3 2.7 1a9.5 9.5 0 0 1 5 0c1.9-1.3 2.7-1 2.7-1 .5 1.4.2 2.4.1 2.7.6.7 1 1.6 1 2.7 0 3.9-2.4 4.8-4.6 5 .4.3.7.9.7 1.8v2.7c0 .3.2.6.7.5A10 10 0 0 0 12 2Z',
		'insta'     => 'M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5ZM12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8ZM17.5 5.5h.01',
	];

	$is_fill = isset( $fillIcons[ $name ] );
	$d       = $is_fill ? $fillIcons[ $name ] : ( $paths[ $name ] ?? null );
	if ( ! $d ) return '';

	$size   = (int) $args['size'];
	$stroke = floatval( $args['stroke'] );
	$class  = trim( 'devpoint-icon ' . $args['class'] );

	$attrs  = sprintf(
		'width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="%2$s" stroke="%3$s" stroke-width="%4$s" stroke-linecap="round" stroke-linejoin="round" class="%5$s" aria-hidden="true"',
		$size,
		$is_fill ? 'currentColor' : esc_attr( $args['fill'] ),
		$is_fill ? 'none' : 'currentColor',
		esc_attr( (string) $stroke ),
		esc_attr( $class )
	);

	$title = $args['title'] ? '<title>' . esc_html( $args['title'] ) . '</title>' : '';
	return '<svg ' . $attrs . '>' . $title . '<path d="' . esc_attr( $d ) . '"/></svg>';
}

/**
 * Echo helper.
 */
function devpoint_the_icon( $name, $args = [] ) {
	echo devpoint_icon( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * The big hero illustration from icons.jsx → HeroArt.
 */
function devpoint_hero_art() {
	?>
	<svg viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
		<defs>
			<radialGradient id="dp-hg1" cx="40%" cy="35%" r="60%">
				<stop offset="0%" stop-color="#F4D8C8" />
				<stop offset="100%" stop-color="#D67A52" />
			</radialGradient>
			<linearGradient id="dp-hg2" x1="0" y1="0" x2="1" y2="1">
				<stop offset="0%" stop-color="#5D8A6A" />
				<stop offset="100%" stop-color="#3F6E81" />
			</linearGradient>
			<pattern id="dp-dots" x="0" y="0" width="14" height="14" patternUnits="userSpaceOnUse">
				<circle cx="2" cy="2" r="1.2" fill="#1E1916" opacity="0.18" />
			</pattern>
		</defs>
		<circle cx="380" cy="220" r="160" fill="url(#dp-hg1)" />
		<path d="M120,420 Q60,360 110,300 Q150,250 100,200 Q60,150 130,110 Q200,80 240,150 Q280,220 320,200 Q400,180 380,260 Q360,360 280,400 Q200,440 120,420 Z" fill="url(#dp-dots)" />
		<path d="M100,440 Q200,520 320,500 Q440,480 460,400 Q480,330 400,330 Q320,330 280,400 Q240,470 160,470 Q120,470 100,440 Z" fill="url(#dp-hg2)" opacity="0.85" />
		<circle cx="180" cy="180" r="80" fill="none" stroke="#F5C56B" stroke-width="20" stroke-dasharray="180 50" stroke-linecap="round" transform="rotate(-30 180 180)" />
		<path d="M460,440 a90,90 0 0 1 -180,0 z" fill="#8B5E83" />
		<circle cx="120" cy="120" r="10" fill="#1E1916" />
		<circle cx="500" cy="120" r="14" fill="#F5C56B" />
		<rect x="460" y="320" width="60" height="60" rx="14" fill="#1E1916" transform="rotate(15 490 350)" />
		<g transform="translate(330,140)">
			<path d="M0,-20 L4,-4 L20,0 L4,4 L0,20 L-4,4 L-20,0 L-4,-4 Z" fill="#FBF7EF" stroke="#1E1916" stroke-width="2" />
		</g>
	</svg>
	<?php
}

/**
 * Category glyph — colored rounded box with an icon inside, matching CatGlyph.
 *
 * @param WP_Term|null $term
 * @param array        $args { size }
 */
function devpoint_cat_glyph( $term, $args = [] ) {
	$args = wp_parse_args( $args, [ 'size' => 20, 'stroke' => 1.8 ] );
	$color = devpoint_category_color( $term );
	$glyph = devpoint_category_glyph( $term );
	?>
	<div class="cat-glyph" style="background: <?php echo esc_attr( $color ); ?>;">
		<?php devpoint_the_icon( $glyph, [ 'size' => $args['size'], 'stroke' => $args['stroke'] ] ); ?>
	</div>
	<?php
}
