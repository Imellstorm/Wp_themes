<?php
/**
 * devpoint — AJAX endpoint for the "Load more essays" button on the
 * home page. Returns the next page of article cards as HTML, plus
 * pagination metadata so the JS can hide the button when done.
 *
 * @package devpoint
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function devpoint_ajax_load_more_essays() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'devpoint_load_more' ) ) {
		wp_send_json_error( [ 'message' => 'Invalid nonce' ], 403 );
	}

	$page = isset( $_POST['page'] ) ? max( 2, (int) $_POST['page'] ) : 2;
	$cat  = isset( $_POST['cat'] )  ? sanitize_title( wp_unslash( $_POST['cat'] ) ) : 'all';

	$args = [
		'posts_per_page'      => 9,
		'ignore_sticky_posts' => true,
		'paged'               => $page,
	];
	if ( $cat && $cat !== 'all' ) {
		$args['category_name'] = $cat;
	}

	$q = new WP_Query( $args );

	ob_start();
	while ( $q->have_posts() ) :
		$q->the_post();
		devpoint_article_card( get_post() );
	endwhile;
	wp_reset_postdata();
	$html = ob_get_clean();

	wp_send_json_success( [
		'html'      => $html,
		'has_more'  => $page < (int) $q->max_num_pages,
		'next_page' => $page + 1,
	] );
}
add_action( 'wp_ajax_devpoint_load_more_essays',        'devpoint_ajax_load_more_essays' );
add_action( 'wp_ajax_nopriv_devpoint_load_more_essays', 'devpoint_ajax_load_more_essays' );
