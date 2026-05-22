<?php
/**
 * devpoint theme — bootstrap, enqueues, helpers.
 *
 * @package devpoint
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DEVPOINT_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/icons.php';
require_once get_template_directory() . '/inc/thumb-art.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/ajax-load-more.php';
if ( is_admin() ) {
	require_once get_template_directory() . '/inc/seed.php';
	require_once get_template_directory() . '/inc/admin-regen-art.php';
}

/**
 * Theme setup.
 */
function devpoint_setup() {
	load_theme_textdomain( 'devpoint', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'custom-logo', [
		'height'      => 64,
		'width'       => 64,
		'flex-width'  => true,
		'flex-height' => true,
	] );

	add_image_size( 'devpoint-card', 800, 500, true );
	add_image_size( 'devpoint-hero', 1600, 900, true );

	register_nav_menus( [
		'primary' => __( 'Primary menu (drawer & footer)', 'devpoint' ),
		'footer'  => __( 'Footer — Read column', 'devpoint' ),
	] );
}
add_action( 'after_setup_theme', 'devpoint_setup' );

/**
 * Sidebars for the footer subscribe area override etc.
 */
function devpoint_widgets_init() {
	register_sidebar( [
		'name'          => __( 'Footer — Brand blurb', 'devpoint' ),
		'id'            => 'footer-blurb',
		'description'   => __( 'Optional text below the brand mark in the footer.', 'devpoint' ),
		'before_widget' => '<div class="ftr-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	] );
}
add_action( 'widgets_init', 'devpoint_widgets_init' );

/**
 * Enqueue styles & scripts.
 */
function devpoint_assets() {
	$theme_uri = get_template_directory_uri();
	$ver       = DEVPOINT_VERSION;

	wp_enqueue_style(
		'devpoint-fonts',
		'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,300;12..96,400;12..96,500;12..96,600;12..96,700&family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap',
		[],
		null
	);

	wp_enqueue_style( 'devpoint-base', $theme_uri . '/assets/css/base.css', [ 'devpoint-fonts' ], $ver );
	wp_enqueue_style( 'devpoint-post', $theme_uri . '/assets/css/post.css', [ 'devpoint-base' ], $ver );
	wp_enqueue_style( 'devpoint',      get_stylesheet_uri(), [ 'devpoint-post' ], $ver );

	wp_enqueue_script( 'devpoint-main', $theme_uri . '/assets/js/main.js', [], $ver, true );
	wp_localize_script( 'devpoint-main', 'DevpointSearch', [
		'restUrl' => esc_url_raw( rest_url( 'wp/v2/posts' ) ),
		'home'    => esc_url_raw( home_url( '/' ) ),
		'strings' => [
			'placeholder' => __( 'Search essays, categories, authors…', 'devpoint' ),
			'noResults'   => __( 'No matches. Try a broader term.', 'devpoint' ),
			'suggested'   => __( 'Suggested for you', 'devpoint' ),
			'resultOne'   => __( '1 result', 'devpoint' ),
			'resultMany'  => __( '%d results', 'devpoint' ),
			'minRead'     => __( 'min read', 'devpoint' ),
		],
	] );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'devpoint_assets' );

/**
 * Body classes used by the design.
 */
function devpoint_body_classes( $classes ) {
	if ( is_singular( 'post' ) ) $classes[] = 'is-post';
	if ( is_home() || is_front_page() ) $classes[] = 'is-home';
	return $classes;
}
add_filter( 'body_class', 'devpoint_body_classes' );

/**
 * Excerpt tweaks — match the design's tight 4-line preview.
 */
function devpoint_excerpt_length( $len ) { return 28; }
function devpoint_excerpt_more( $more ) { return '…'; }
add_filter( 'excerpt_length', 'devpoint_excerpt_length' );
add_filter( 'excerpt_more',   'devpoint_excerpt_more'  );

/**
 * Estimated reading time in minutes.
 */
function devpoint_read_minutes( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) return 1;
	$words = str_word_count( wp_strip_all_tags( (string) $post->post_content ) );
	return max( 1, (int) round( $words / 220 ) );
}

/**
 * Pagination using a card-style numbered list.
 */
function devpoint_pagination() {
	$links = paginate_links( [
		'type'      => 'array',
		'mid_size'  => 1,
		'prev_text' => '&larr;',
		'next_text' => '&rarr;',
	] );
	if ( ! $links ) return;
	echo '<nav class="devpoint-pagination" aria-label="' . esc_attr__( 'Posts navigation', 'devpoint' ) . '">';
	echo implode( '', $links );
	echo '</nav>';
}
