<?php
/**
 * Render a list of all terms of this taxonomy
 */

$template        = new Rcno_Template_Tags( 'recencio-book-reviews', '1.0.0' );
$ignore_articles = Rcno_Reviews_Option::get_option( 'rcno_reviews_ignore_articles' );
$sort_name       = Rcno_Reviews_Option::get_option( 'rcno_reviews_sort_names' );
$index_headers   = Rcno_Reviews_Option::get_option( 'rcno_reviews_index_headers' );
$book_covers     = Rcno_Reviews_Option::get_option( 'rcno_show_book_covers_index', false );

$string_cmp = function ( $a, $b ) {
	return strcasecmp( $a['name'], $b['name'] );
};

$integer_cmp = function ( $a, $b ) {
	return $a['series'] - $b['series'];
};

// Create an empty output variable.
$out = '';

if ( $terms && ! is_wp_error( $terms ) ) {
	if ( count( $terms ) > 0 ) {

		if ( 'rcno_author' === $taxonomy && 'last_name_first_name' === $sort_name ) {
			// If we are looking at authors, move the first name to last and separate by a comma.
			foreach ( $terms as $_term ) {
				$_term_name = $_term->name;
				$_term_name = preg_replace( '/^(.+) (.+)/', '$2, $1', $_term_name );

				$_term->name = $_term_name;
			}
		}

		if ( 'rcno_series' === $taxonomy && $ignore_articles ) {
			// If we are looking at book series, move all articles to the and of string and separate by a comma.
			foreach ( $terms as $_term ) {
				$_term_name = $_term->name;
				$_term_name = preg_replace( '/^(A|An|The) (.+)/', '$2, $1', $_term_name );

				$_term->name = $_term_name;
			}
		}

		foreach ( $terms as $key => $value ) {
			// Form the terms available we are only taking the 'name' and 'ID' and doing a natural sort.
			$_terms[] = array(
				'ID'       => $value->term_id,
				'name'     => $value->name,
				'count'    => $value->count,
				'slug'     => $value->slug,
				'taxonomy' => $value->taxonomy,
			);
			usort( $_terms, $string_cmp );
		}

		// Create an index i to compare the number in the list and check for first and last item.
		$i = 0;

		// Create an empty array to take with the first letters of all headlines.
		$letters = array();

		// Walk through all the terms to build alphabet navigation.
		foreach ( $_terms as $value ) {

			$all_titles  = array();
			$all_ids     = array();
			$review_data = array();
			// Get term meta data.
			$term_meta = get_term_meta( $value['ID'] );

			$title = ucfirst( $value['name'] );

			$custom_args = array(
				'post_type'      => 'rcno_review',
				'tax_query'      => array(
					array(
						'taxonomy' => $value['taxonomy'],
						'field'    => 'slug',
						'terms'    => $value['slug'],
					),
				),
				'posts_per_page' => 100,
			);

			$query = new WP_Query( $custom_args );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) : $query->the_post();
					$series        = $template->get_the_rcno_book_meta( get_the_ID(), 'rcno_book_series_number', '', false );
					$review_data[] = array(
						'ID'     => get_the_ID(),
						'title'  => get_the_title(),
						'link'   => get_the_permalink(),
						'series' => null !== $series ? (int) $series : 0,
					);
					usort( $review_data, $integer_cmp );
				endwhile;
			}

			wp_reset_postdata();

			if ( (bool) $headers ) { // Add first letter headlines for easier navigation.

				// Get the first letter (without special chars).
				$first_letter = remove_accents( $title )[0];

				// Check if we've already had a headline.
				if ( ! in_array( $first_letter, $letters, true ) ) {
					// Close list of proceeding group.
					if ( 0 !== $i ) {
						$out .= '</div><!--- xxxxx --->';
						$out .= '</div><!--- .rcno-tax-wrapper --->';
					}
					// Create a headline.
					$out .= '<div class="rcno-tax-wrapper">';
					$out .= '<h2><a class="rcno-toplink" href="#top">&uarr;</a><a name="' . $first_letter . '"></a>';
					$out .= strtoupper( $first_letter );
					$out .= '</h2>';

					// Start new list.
					$out .= '<div class="rcno-taxlist">';

					// Add the letter to the list.
					$letters[] = $first_letter;
				}
			} else {
				// Start list before first item.
				if ( 0 === $i ) {
					$out .= '<div class="rcno-taxlist">';
				}
			}

			// Add the entry for the term.
			$out .= '<div class="rcno-tax-name">';
			$out .= '<a href="' . get_term_link( $value['ID'] ) . '">';
			$out .= $title;
			$out .= $count ? '<span class="rcno-tax-count"> (' . count( $review_data ) . ')</span>' : '';
			$out .= '</a>';

			$out .= '<div class="' . ( empty( $book_covers ) ? 'titles-container' : 'books-container' ) . '">';
			foreach ( $review_data as $_data ) {

				if ( $book_covers ) {
					$out .= '<div class="book-cover-container">';
					$out .= '<a href="' . $_data['link'] . '">';
					$out .= $template->get_the_rcno_book_cover( $_data['ID'], 'rcno-book-cover-sm' );
					$out .= '</a>';
					$out .= '</div>';
				} else {
					$out .= '<div class="book-title-container">';
					$out .= '<a href="' . $_data['link'] . '">';
					$out .= $template->get_the_rcno_book_meta( $_data['ID'], 'rcno_book_title', 'p', false );
					$out .= '</a>';
					$out .= '</div>';
				}
			}
			$out .= '</div><!--- .books-container--->';
			$out .= '</div><!--- .rcno-tax-name --->';

			// Increment the counter.
			$i ++;

		}
		// Close the last list.
		$out .= '</div>';
		$out .= '</div>';

		// Output the rendered list.
		echo '<a name="top"></a>';
		$template->the_rcno_alphabet_nav_bar( $letters );
		echo '<div class="rcno-tax-container">';
		echo $out;
		echo '</div>';
		$template->the_rcno_alphabet_nav_bar( $letters );

	} else {
		// No terms in this taxonomy.
		esc_html_e( 'There are no terms in this taxonomy.', 'recencio-book-reviews' );
	}

} else {
	// Error: no taxonomy set.
	esc_html_e( 'Error: No taxonomy set for this list!', 'recencio-book-reviews' );
}
