<?php

/**
 * Author: wzyMedia
 * Template Name: Rcno Default
 * Rcno Default Template - the excerpt
 */

	// Get the review ID.
	if ( isset( $GLOBALS['review_id'] ) && $GLOBALS['review_id'] !== '' ) {
		$review_id = $GLOBALS['review_id'];
	} else {
		$review_id = get_post()->ID;
	}

	$plugin_name = 'recencio-book-reviews';
	$version     = '1.0.0';

	$review = new Rcno_Template_Tags( $plugin_name, $version );

?>

<?php

	/**
	 * Displaying the book review title is normally done by the theme as post_title().
	 * However, if the review is embedded, we need to do it here.
	 */
	$review->the_rcno_review_title( $review_id );

	if ( $review->is_review_embedded() ) {
		$review->the_better_rcno_book_review_excerpt( $review_id );
	} else {
		$review->the_rcno_book_review_content( $review_id );
	}

	echo '<!--- Recencio Book Reviews --->';

?>
