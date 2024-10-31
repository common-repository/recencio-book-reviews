<?php

/**
 * Render a list of all book reviews.
 */

$template        = new Rcno_Template_Tags( 'recencio-book-reviews', '1.0.0' );
$ignore_articles = Rcno_Reviews_Option::get_option( 'rcno_reviews_ignore_articles' );
$articles_list   = explode( ',', Rcno_Reviews_Option::get_option( 'rcno_reviews_ignored_articles_list', 'The,A,An' ) );
$articles_list   = implode( '|', $articles_list ) . '|\d+'; // @TODO: Figure out a better way to handle this.

// Create an empty output variable.
$out = '';

if ( $posts && count( $posts ) > 0 ) {
	// Create an index i to compare the number in the list and check for first and last item.
	$i = 0;

	// Create an empty array to take the book details.
	$books = array();

	// Used in 'usort' to sort alphabetically by book title.
	function cmp( $a, $b ) {
		return strcasecmp( $a['sorted_title'][0], $b['sorted_title'][0] );
	}

	// Loop through each post, book title from post-meta and work on book title.
	foreach ( $posts as $book ) {
		$book_details = get_post_custom( $book->ID );
		$unsorted_title = $book_details['rcno_book_title'][0];
		$sorted_title = $unsorted_title;
		if ( $ignore_articles ) {
			$sorted_title = preg_replace( '/^('. $articles_list .') (.+)/', '$2, $1', $book_details['rcno_book_title'] );
		}
		$books[] = array(
			'ID'    => $book->ID,
			'sorted_title' => $sorted_title,
			'unsorted_title' => $unsorted_title,
		);
		usort( $books, 'cmp' );
	}

	$out .= '<div id="macy-container" class="rcno-book-grid-index-container rcno-book-grid-index">';

	// Walk through all the books to build alphabet navigation.
	foreach ( $books as $book ) {

		// Add the entry for the post.
		$out .= '<div class="rcno-book-grid-item"><a href="' . get_permalink( $book['ID'] ) . '">';

		// Pick the 'medium' book cover size
		$out .= $template->get_the_rcno_book_cover( $book['ID'], 'medium', true, true );

		$out .= '<p>' . $book['unsorted_title'] . '</p>';
		$out .= '</a></div>';

		// Increment the counter.
		$i ++;
	}
	// Close the last list.
	$out .= '</div>';

	echo $out; // This is where everything is printed.

} else {
	// No book reviews.
	esc_html_e( 'There are no book reviews to display.', 'recencio-book-reviews' );
}
