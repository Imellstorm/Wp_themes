<?php
/**
 * devpoint — "Regenerate image" meta box on the post editor.
 *
 * Re-rolls the procedural thumbnail by bumping a per-post seed offset
 * stored in `_devpoint_art_seed` post meta. Featured images, when set,
 * take precedence — so the button is a no-op visually in that case.
 *
 * @package devpoint
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the side-panel meta box on the post screen.
 */
function devpoint_register_art_metabox() {
	add_meta_box(
		'devpoint_art_regen',
		__( 'Thumbnail art', 'devpoint' ),
		'devpoint_render_art_metabox',
		'post',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'devpoint_register_art_metabox' );

/**
 * Render the meta box body — inline preview + "Regenerate image" button.
 */
function devpoint_render_art_metabox( $post ) {
	$nonce      = wp_create_nonce( 'devpoint_regen_art_' . $post->ID );
	$has_thumb  = has_post_thumbnail( $post );
	$ajax_url   = admin_url( 'admin-ajax.php' );

	ob_start();
	devpoint_thumb_art( $post );
	$preview_html = ob_get_clean();
	?>
	<style>
		.devpoint-art-preview { aspect-ratio: 16 / 10; border-radius: 6px; overflow: hidden; background: #f4f0e8; border: 1px solid #ddd; margin-bottom: 10px; }
		.devpoint-art-preview > * { width: 100%; height: 100%; display: block; object-fit: cover; }
		.devpoint-art-actions { display: flex; align-items: center; gap: 8px; }
		.devpoint-art-status { font-size: 12px; color: #46b450; }
		.devpoint-art-note { color: #666; font-size: 12px; margin: 8px 0 0; }
	</style>

	<div class="devpoint-art-preview" id="devpoint-art-preview"><?php echo $preview_html; ?></div>

	<div class="devpoint-art-actions">
		<button type="button" class="button button-secondary" id="devpoint-regen-art"
		        data-post-id="<?php echo (int) $post->ID; ?>"
		        data-nonce="<?php echo esc_attr( $nonce ); ?>"
		        data-ajax="<?php echo esc_attr( $ajax_url ); ?>">
			<?php esc_html_e( 'Regenerate image', 'devpoint' ); ?>
		</button>
		<span class="devpoint-art-status" id="devpoint-art-status" style="display:none;">✓</span>
	</div>

	<?php if ( $has_thumb ) : ?>
		<p class="devpoint-art-note">
			<?php esc_html_e( 'A featured image is set and will override the procedural art on the site.', 'devpoint' ); ?>
		</p>
	<?php endif; ?>

	<script>
	(function(){
		var btn    = document.getElementById('devpoint-regen-art');
		var status = document.getElementById('devpoint-art-status');
		var preview = document.getElementById('devpoint-art-preview');
		if (!btn) return;
		btn.addEventListener('click', function(){
			btn.disabled = true;
			status.style.display = 'none';
			var fd = new FormData();
			fd.append('action', 'devpoint_regen_art');
			fd.append('post_id', btn.dataset.postId);
			fd.append('nonce', btn.dataset.nonce);
			fetch(btn.dataset.ajax, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function(r){ return r.json(); })
				.then(function(j){
					btn.disabled = false;
					if (j && j.success && j.data && j.data.html) {
						preview.innerHTML = j.data.html;
						status.textContent = '✓ Regenerated';
						status.style.display = 'inline';
						setTimeout(function(){ status.style.display = 'none'; }, 2000);
					} else {
						alert('Failed to regenerate.');
					}
				})
				.catch(function(){
					btn.disabled = false;
					alert('Network error.');
				});
		});
	})();
	</script>
	<?php
}

/**
 * AJAX handler — bumps the seed offset and returns the freshly rendered art.
 */
function devpoint_ajax_regen_art() {
	$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
	$nonce   = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	if ( ! $post_id || ! wp_verify_nonce( $nonce, 'devpoint_regen_art_' . $post_id ) ) {
		wp_send_json_error( [ 'message' => 'Invalid nonce' ], 403 );
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( [ 'message' => 'Forbidden' ], 403 );
	}

	update_post_meta( $post_id, '_devpoint_art_seed', wp_generate_password( 12, false, false ) );

	ob_start();
	devpoint_thumb_art( $post_id );
	$html = ob_get_clean();

	wp_send_json_success( [ 'html' => $html ] );
}
add_action( 'wp_ajax_devpoint_regen_art', 'devpoint_ajax_regen_art' );
