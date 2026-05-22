<?php
/**
 * Comments — uses the design's typography for headings.
 *
 * @package devpoint
 */

if ( post_password_required() ) return;
?>
<section class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$count = get_comments_number();
			if ( '1' === $count ) {
				esc_html_e( 'One response', 'devpoint' );
			} else {
				/* translators: %s: comment count */
				printf( esc_html( _nx( '%s response', '%s responses', (int) $count, 'comments title', 'devpoint' ) ), number_format_i18n( (int) $count ) );
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php wp_list_comments( [ 'style' => 'ol', 'avatar_size' => 40, 'short_ping' => true ] ); ?>
		</ol>

		<?php the_comments_pagination( [
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
		] ); ?>

	<?php endif;

	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'devpoint' ); ?></p>
	<?php endif;

	comment_form( [
		'title_reply'        => __( 'Leave a response', 'devpoint' ),
		'title_reply_to'     => __( 'Reply to %s', 'devpoint' ),
		'cancel_reply_link'  => __( 'Cancel', 'devpoint' ),
		'label_submit'       => __( 'Post comment', 'devpoint' ),
		'comment_notes_before' => '',
	] );
	?>
</section>
