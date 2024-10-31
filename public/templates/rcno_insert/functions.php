<?php

/**
 * Registers the stylesheet for the 'Basic' template.
 *
 * @uses wp_enqueue_style
 * @return void
 */

add_action( 'wp_enqueue_scripts', 'rcno_insert_styles' );
add_action( 'wp_enqueue_scripts', 'rcno_insert_scripts' );

function rcno_insert_styles() {
	wp_enqueue_style( 'rcno-insert-style', plugin_dir_url( __FILE__ ) . 'insert-style.css', array(), '1.0.0', 'all' );
}

function rcno_insert_scripts() {
	wp_enqueue_script( 'rcno-insert-script', plugin_dir_url( __FILE__ ) . 'insert-script.js', array( 'jquery' ), '1.0.0', true );
}


function rcno_insert_book_details( $content ) {
	// Get the review ID.
	if ( isset( $GLOBALS['review_id'] ) && $GLOBALS['review_id'] !== '' ) {
		$review_id = $GLOBALS['review_id'];
	} else {
		$review_id = get_the_ID();
	}

	$template = new Rcno_Template_Tags( 'recencio-book-reviews', '1.0.0' );

	if ( is_single() && is_singular( 'rcno_review' ) ) {
		$content_block = explode( '<p>', $content );
		$count         = count( $content_block );

		if ( ! empty( $content_block[1] ) ) {
			$content_block[1] .= $template->get_the_rcno_full_book_details( $review_id, 'medium' );
		}
		for ( $i = 1; $i < $count; $i ++ ) {
			$content_block[ $i ] = '<p>' . $content_block[ $i ];
		}
		$content = implode( '', $content_block );
	}

	return $content;
}

add_filter( 'the_content', 'rcno_insert_book_details', 11 );
