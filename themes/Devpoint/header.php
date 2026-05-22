<?php
/**
 * Header for devpoint.
 *
 * @package devpoint
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'devpoint' ); ?></a>

<div class="hdr-shell" data-hdr>
	<div class="wrap hdr hdr-simple">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
				<span class="brand-mark"><?php echo esc_html( mb_strtolower( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ) ); ?></span>
				<span><?php bloginfo( 'name' ); ?></span>
			<?php endif; ?>
		</a>

		<button class="search-box-desk hdr-search" type="button" data-search-open aria-label="<?php esc_attr_e( 'Open search', 'devpoint' ); ?>">
			<?php devpoint_the_icon( 'search', [ 'size' => 16 ] ); ?>
			<input placeholder="<?php esc_attr_e( 'Search articles…', 'devpoint' ); ?>" readonly tabindex="-1" />
			<kbd>⌘K</kbd>
		</button>
		<button class="icon-btn hdr-search-mobile" type="button" data-search-open aria-label="<?php esc_attr_e( 'Open search', 'devpoint' ); ?>">
			<?php devpoint_the_icon( 'search', [ 'size' => 18 ] ); ?>
		</button>
	</div>
</div>

<div id="content">
