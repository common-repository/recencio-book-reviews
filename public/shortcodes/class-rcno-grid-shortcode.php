<?php

/**
 * Creates the shortcodes used for Masonry grid.
 *
 * @link       https://wzymedia.com
 * @since      1.24.1
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public/shortcodes
 */

/**
 * Creates the shortcodes used for Masonry grid.
 *
 * @since      1.24.1
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Grid_Shortcode {

	/**
	 * The Rcno_Template_Tags class
	 *
	 * @since    1.24.1
	 * @access   private
	 * @var      Rcno_Template_Tags $template The current version of this plugin.
	 */
	private $template;

	/**
	 * An array of the variables used by the shortcode template
	 *
	 * @since    1.24.1
	 * @access   private
	 * @var      array $variables The current version of this plugin.
	 */
	private $variables;

	/**
	 * Rcno_Isotope_Grid_Shortcode constructor.
	 *
	 * @since 1.24.1
	 * @param $plugin_name
	 * @param $version
	 */
	public function __construct( $plugin_name, $version ) {

		$this->template  = new Rcno_Template_Tags( $plugin_name, $version );
		$this->variables = $this->rcno_get_shortcode_variables();
	}

	/**
	 * Fetches all the variables to be used in template and add them to an array.
	 *
	 * @since 1.24.1
	 * @return array
	 */
	private function rcno_get_shortcode_variables() {
		$variables                      = array();
		$variables['ignore_articles']   = (bool) Rcno_Reviews_Option::get_option( 'rcno_reviews_ignore_articles' );
		$variables['articles_list']     = str_replace( ',', '|', Rcno_Reviews_Option::get_option( 'rcno_reviews_ignored_articles_list', 'The,A,An' ) )
										. '|\d+';
		$variables['custom_taxonomies'] = explode( ',', strtolower( Rcno_Reviews_Option::get_option( 'rcno_taxonomy_selection' ) ) );

		return $variables;
	}

	/**
	 * Return the actual shortcode code content used in WP.
	 *
	 * @since 1.24.1
	 * @param $options
	 * @return string
	 */
	public function do_masonry_grid_shortcode( $options ) {

		// Set default values for options not set explicitly.
		$options = shortcode_atts(
			array(
				'rating'   => 1,
				'taxonomy' => '',
				'terms'    => '',
			),
			$options,
			'rcno-reviews-grid'
		);

		// The actual rendering is done by a special function.
		$output = $this->render_masonry_grid( $options );

		// We are enqueuing the previously registered script.
		wp_enqueue_script( 'macy-masonary-grid' );

		return do_shortcode( $output );
	}

	/**
	 * * Do the render of the shortcode contents via the template file.
	 *
	 * @since 1.24.1
	 * @param $options
	 * @return string
	 */
	public function render_masonry_grid( $options ) {

		// Get an alphabetically ordered list of all reviews.
		$args  = array(
			'post_type'      => 'rcno_review',
			'post_status'    => 'publish',
			'orderby'        => 'post_title',
			'order'          => 'ASC',
			'posts_per_page' => - 1,
		);

		if ( $options['taxonomy'] && $options['terms'] ) {
			$taxonomy = in_array( $options['taxonomy'], array( 'category', 'post_tag' ) )
				? $options['taxonomy'] : 'rcno_' . $options['taxonomy'];
			$terms    = explode( ',', $options['terms'] );

			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'name',
					'terms'    => $terms,
				),
			);
		}

		$query = new WP_Query( $args );
		$posts = $query->posts;

		// Create an empty output variable.
		$out = '';

		if ( $posts && count( $posts ) > 0 ) {

			// Create an empty array to take the book details.
			$books = array();

			// Loop through each post, book title from post-meta and work on book title.
			foreach ( $posts as $book ) {
				$book_details   = get_post_custom( $book->ID );
				$unsorted_title = ! empty( $book_details['rcno_book_title'] ) ? $book_details['rcno_book_title'][0] : '';
				$sorted_title   = $unsorted_title;
				if ( $this->variables['ignore_articles'] ) {
					$sorted_title = preg_replace( '/^(' . $this->variables['articles_list'] . ') (.+)/', '$2, $1', $unsorted_title );
				}
				$books[] = array(
					'ID'             => $book->ID,
					'sorted_title'   => $sorted_title,
					'unsorted_title' => $unsorted_title,
				);
				usort( $books, array( $this, 'compare_titles' ) );
			}

			$out .= '<div id="macy-container" class="rcno-book-grid-index-container rcno-book-grid-index">';

			// Walk through all the books to build alphabet navigation.
			foreach ( $books as $book ) {
				// Add the entry for the post.
				$out .= '<div class="rcno-book-grid-item"><a href="' . get_permalink( $book['ID'] ) . '">';

				// Pick the 'medium' book cover size
				$out .= $this->template->get_the_rcno_book_cover( $book['ID'], 'medium', true, true );

				$out .= '<p>' . $book['unsorted_title'] . '</p>';
				$out .= '</a></div>';
			}
			// Close the last list.
			$out .= '</div>';

			return $out; // This is where everything is printed.

		}

		return esc_html__( 'There are no book reviews to display.', 'recencio-book-reviews' );
	}

	/**
	 * Utility function used by `usort` to sort titles alphabetically
	 *
	 * @since 1.24.1
	 * @param $a
	 * @param $b
	 * @return int
	 */
	protected function sort_by_title( $a, $b ) {

		return strcasecmp( $a['sorted_title'], $b['sorted_title'] );
	}

	/**
	 * Used in 'usort' to sort alphabetically by book title.
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	protected function compare_titles( $a, $b ) {

		return strcasecmp( $a['sorted_title'], $b['sorted_title'] );
	}
}
