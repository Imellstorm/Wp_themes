<?php
/**
 * Search form used by get_search_form().
 *
 * @package devpoint
 */
?>
<form role="search" method="get" class="search-form ftr-sub-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="devpoint-s"><?php esc_html_e( 'Search for:', 'devpoint' ); ?></label>
	<input type="search" id="devpoint-s" name="s" value="<?php echo esc_attr( get_search_query() ); ?>"
	       placeholder="<?php esc_attr_e( 'Search essays…', 'devpoint' ); ?>" />
	<button type="submit"><?php esc_html_e( 'Search', 'devpoint' ); ?></button>
</form>
