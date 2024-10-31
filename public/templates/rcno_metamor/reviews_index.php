<?php

/**
 * Render a list of all book reviews.
 */

$template        = new Rcno_Template_Tags( 'recencio-book-reviews', '1.0.0' );
$ignore_articles = Rcno_Reviews_Option::get_option( 'rcno_reviews_ignore_articles' );
$articles_list   = explode( ',', Rcno_Reviews_Option::get_option( 'rcno_reviews_ignored_articles_list', 'The,A,An' ) );
$articles_list   = implode( '|', $articles_list ) . '|\d+'; // @TODO: Figure out a better way to handle this.
$index_headers   = Rcno_Reviews_Option::get_option( 'rcno_reviews_index_headers' );

// Create an empty output variable.
$out = '';

if ( $posts && count( $posts ) > 0 ) {
	// Create an index i to compare the number in the list and check for first and last item.
	$i = 0;

	// Create an empty array to take with the first letters of all headlines.
	$letters = array();

	// Create an empty array to take the book details.
	$books = array();

	/* Used in 'usort' to sort alphabetically by book title. */
	function cmp( $a, $b ) {
		return strcasecmp( $a['title'], $b['title'] );
	}

	// Loop through each post, book title from post-meta and work on book title.
	foreach ( $posts as $book ) {
		$book_details = get_post_custom( $book->ID );
		$book_title   = $book_details['rcno_book_title'];
		if ( $ignore_articles ) {
			$book_title = preg_replace( '/^(' . $articles_list . ') (.+)/', '$2, $1', $book_details['rcno_book_title'] ); // phpcs:ignore
		}
		$books[] = array(
			'ID'    => $book->ID,
			'title' => $book_title[0],
		);
		usort( $books, 'cmp' );
	}

	// Walk through all the books to build alphabet navigation.
	foreach ( $books as $book ) {

		if ( $index_headers && ( (bool) $headers ) ) { // Add first letter headlines for easier navigation.

			if ( '' === $book['title'] ) {
				echo '<p>' . sprintf( __( 'There is a missing book title on review ID %d', 'recencio-book-reviews' ), $book['ID'] ) . '</p>';
			}

			// Get the first letter (without special chars).
			$first_letter = remove_accents( $book['title'] )[0];

			// Check if we've already had a headline.
			if ( ! in_array( $first_letter, $letters, true ) ) {
				// Close list of proceeding group.
				if ( 0 !== $i ) {
					$out .= '</ul>';
				}
				// Create a headline.
				$out .= '<h2><a class="rcno-toplink" href="#top">&uarr;</a><a name="' . $first_letter . '"></a>';
				$out .= strtoupper( $first_letter );
				$out .= '</h2>';

				// Start new list.
				$out .= '<ul class="rcno-taxlist rcno-taxlist-book-covers">';

				// Add the letter to the list.
				$letters[] = $first_letter;
			}
		} else {
			// Start list before first item.
			if ( 0 === $i ) {
				$out .= '<ul class="rcno-taxlist rcno-taxlist-book-covers">';
			}
		}

		// Add the entry for the post.
		$out .= '<li><a href="' . get_permalink( $book['ID'] ) . '">';

		if ( Rcno_Reviews_Option::get_option( 'rcno_show_book_covers_index', false ) ) {
			$out .= $template->get_the_rcno_book_cover( $book['ID'], 'rcno-book-cover-sm', true, false );
		}

		$out .= '<p>' . $book['title'] . '</p>';
		$out .= '</a></li>';

		// increment the counter.
		$i ++;
	}
	// Close the last list.
	$out .= '</ul>';

	// Output the rendered list.
	echo '<a name="top"></a>';
	$template->the_rcno_alphabet_nav_bar( $letters );
	echo $out; // phpcs:ignore
	$template->the_rcno_alphabet_nav_bar( $letters );
} else {
	// No book reviews.
	esc_html_e( 'There are no book reviews to display.', 'recencio-book-reviews' );
}
