<?php
/**
 * The methods used to render the review components on the frontend.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 */

/**
 * The methods used to render the review components on the frontend.
 *
 * Defines all the methods used to render the various components of a review.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Template_Tags {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Returns an instance of the \Rcno_Reviews_Public_Rating class
	 *
	 * @string 1.62.0
	 *
	 * @return \Rcno_Reviews_Public_Rating
	 */
	public function ratings() {

		return new Rcno_Reviews_Public_Rating( $this->plugin_name, $this->version );
	}

	/**
	 * Our default meta keys
	 *
	 * @since 1.62.0
	 *
	 * @return array
	 */
	public function meta_keys() {

		return apply_filters(
			'rcno_all_book_meta_keys',
			array(
				'rcno_book_illustrator'   => apply_filters( 'rcno_book_illustrator', __( 'Illustrator', 'recencio-book-reviews' ) ),
				'rcno_book_pub_date'      => apply_filters( 'rcno_book_pub_date', __( 'Published', 'recencio-book-reviews' ) ),
				'rcno_book_pub_format'    => apply_filters( 'rcno_book_pub_format', __( 'Format', 'recencio-book-reviews' ) ),
				'rcno_book_pub_edition'   => apply_filters( 'rcno_book_pub_edition', __( 'Edition', 'recencio-book-reviews' ) ),
				'rcno_book_page_count'    => apply_filters( 'rcno_book_page_count', __( 'Page Count', 'recencio-book-reviews' ) ),
				'rcno_book_series_number' => apply_filters( 'rcno_book_series_number', __( 'Series Number', 'recencio-book-reviews' ) ),
				'rcno_book_gr_review'     => apply_filters( 'rcno_book_gr_review', __( 'Goodreads Rating', 'recencio-book-reviews' ) ),
				'rcno_book_gr_id'         => apply_filters( 'rcno_book_gr_id', __( 'Goodreads ID', 'recencio-book-reviews' ) ),
				'rcno_book_isbn13'        => apply_filters( 'rcno_book_isbn13', __( 'ISBN13', 'recencio-book-reviews' ) ),
				'rcno_book_isbn'          => apply_filters( 'rcno_book_isbn', __( 'ISBN', 'recencio-book-reviews' ) ),
				'rcno_book_asin'          => apply_filters( 'rcno_book_asin', __( 'ASIN', 'recencio-book-reviews' ) ),
				'rcno_book_gr_url'        => apply_filters( 'rcno_book_gr_url', __( 'Book URL', 'recencio-book-reviews' ) ),
				'rcno_book_title'         => apply_filters( 'rcno_book_title', __( 'Title', 'recencio-book-reviews' ) ),
				'rcno_book_description'   => apply_filters( 'rcno_book_description', __( 'Book Description/Synopsis', 'recencio-book-reviews' ) ),
			)
		);
	}

	/**
	 * Includes the 'functions file' to be used by the book review template.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function include_functions_file() {

		// Get the layout chosen.
		$layout = Rcno_Reviews_Option::get_option( 'rcno_review_template', 'rcno_default' );

		$external_path = get_stylesheet_directory() . '/rcno_templates/' . $layout . '/functions.php';
		$local_path    = plugin_dir_path( __FILE__ ) . 'templates/' . $layout . '/functions.php';

		if ( file_exists( $external_path ) ) {
			include_once $external_path;
		} elseif ( file_exists( $local_path ) ) {
			include_once $local_path;
		} else {
			include_once plugin_dir_path( __FILE__ ) . 'templates/rcno_default/functions.php';
		}
	}

	/**
	 * @param $input
	 *
	 * @return string
	 */
	public function sanitize_string( $input ) {
		$input = strip_tags( $input );
		$input = str_replace( '%', '', $input );

		if ( function_exists( 'mb_strtolower' ) && seems_utf8( $input ) ) {
			$input = mb_strtolower( $input, 'UTF-8' );
		}

		$input = strtolower( $input );
		$input = preg_replace( '/\s+/', '-', $input );
		$input = trim( $input, '-' );

		return $input;
	}

	/**
	 * Retrieves a list of all the metadata keys and taxonomy slugs
	 * for a review.
	 *
	 * @since 1.11.0
	 *
	 * @param string   $out Output are keys, values or both.
	 * @param  int     $limit Output are keys, values or both.
	 *
	 * @return array
	 */
	public function get_rcno_book_meta_keys( $out = 'all', $limit = 100 ) {

		$meta_data = array();
		$taxonomies = array();

		$built_taxonomies = array();
		if ( Rcno_Reviews_Option::get_option( 'rcno_enable_builtin_taxonomy', false )) {
			$built_taxonomies = array(
				'category' => __( 'Category', 'recencio-book-reviews' ),
				'post_tag' => __( 'Tag', 'recencio-book-reviews' ),
			);
		}

		$meta_keys         = $this->meta_keys();
		$custom_taxonomies = Rcno_Reviews_Option::get_option( 'rcno_taxonomy_selection' );
		$custom_taxonomies = explode( ',', $custom_taxonomies );

		foreach ( $custom_taxonomies as $taxonomy ) {
			$taxonomies[ 'rcno_' . $this->sanitize_string( $taxonomy ) ] = $taxonomy;
		}

		if ( 'all' === $out ) {
			$meta_data = array_slice( array_merge( $built_taxonomies, $taxonomies, $meta_keys ), 0, $limit );
		}
		if ( 'keys' === $out  ) {
			$meta_data = array_slice( array_keys( array_merge( $built_taxonomies, $taxonomies, $meta_keys ) ), 0, $limit );
		}
		if ( 'values' === $out ) {
			$meta_data = array_slice( array_values( array_merge( $built_taxonomies, $taxonomies, $meta_keys ) ), 0, $limit );
		}

		return $meta_data;
	}

	/******************************************************************************
	 * FULL BOOK DETAILS TEMPLATE TAGS
	 ******************************************************************************/
	/**
	 * Generates the book details box.
	 *
	 * @since 1.0.0
	 *
	 * @param int	 $review_id	The current review's post ID.
	 * @param string $size		The required size of the book cover.
	 *
	 * @return string
	 */
	public function get_the_rcno_full_book_details( $review_id, $size = 'medium' ) {

		$default_meta   = implode( ',', $this->get_rcno_book_meta_keys( 'keys', 8 ) );
		$selected_meta  = explode( ',', Rcno_Reviews_Option::get_option( 'rcno_book_details_meta', $default_meta ) );
		$book_meta_keys = $this->get_rcno_book_meta_keys( 'keys' );

		if ( ! $selected_meta ) {
			return false;
		}

		$out = '<div class="rcno-full-book">';
		$out .= '<div class="rcno-full-book-cover">';
		$out .= $this->get_the_rcno_book_cover( $review_id, $size );

		$out .= $this->get_the_rcno_admin_book_rating( $review_id );
		$out .= '</div>';

		$out .= '<div class="rcno-full-book-content">';
		$out .= '<div class="rcno-full-book-details">';

		foreach ( $selected_meta as $key ) {
			if ( in_array( $key, $book_meta_keys, true )) {
				$out .= $this->get_the_rcno_taxonomy_terms( $review_id, $key, true );
				$out .= $this->get_the_rcno_book_meta( $review_id, $key, 'div' );
			}
		}

		if ( apply_filters( 'rcno_book_purchase_links_in_details', false, $review_id ) ) {
			$out .= $this->get_the_rcno_book_purchase_links( $review_id, true );
		}

		$out .= apply_filters( 'rcno_full_book_details', null, $review_id );

		$out .= '</div>';

		$out .= '<div class="rcno-full-book-description">';
		$out .= $this->get_the_rcno_book_description( $review_id );
		$out .= '</div>';

		$out .= '</div>';
		$out .= '</div>';

		return $out;
	}

	/**
	 * Prints the book details box.
	 *
	 * @since 1.0.0
	 *
	 * @param int	 $review_id	The current review's post ID.
	 * @param string $size		The required size of the book cover.
	 *
	 * @return void
	 */
	public function the_rcno_full_book_details( $review_id, $size = 'medium' ) {
		echo $this->get_the_rcno_full_book_details( $review_id, $size );
	}

	/** ****************************************************************************
	 * ADMIN RATING TEMPLATE TAGS
	 *******************************************************************************/

	/**
	 * Generates the private 5-star rating given by the reviewer.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The current review's post ID.
	 * @param bool $display	    Display the stars or return value.
	 *
	 * @return float|string
	 */
	public function get_the_rcno_admin_book_rating( $review_id, $display = true ) {

		$output     = '';
		$review     = get_post_custom( $review_id );
		$rating     = isset( $review['rcno_admin_rating'] ) ? $review['rcno_admin_rating'][0] : 0;
		$background = Rcno_Reviews_Option::get_option( 'rcno_star_background_color', 'transparent' );

		if ( false === $display) {
			return (float) $rating;
		}

		if ( $rating && Rcno_Reviews_Option::get_option( 'rcno_enable_star_rating_box' ) ) {
			$output .= '<div class="rcno-admin-rating" style="background: ' . $background . '">';
			$output .= '<div class="stars rating-' . $review_id . '" ';
			$output .= 'data-review-id="' . $review_id . '" ';
			$output .= 'data-review-rating="' . esc_attr( $rating ) . '" ';
			$output .= 'title="' . esc_attr( $rating ) . ' ' . __( 'out of 5 stars', 'recencio-book-reviews' ) . '">';
			$output .= '</div>';
			$output .= '</div>';
		}

		return $output;
	}

	/**
	 * Prints the private 5-star rating given by the reviewer.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The current review's post ID.
	 * @param bool $display	    Display the stars or return value.
	 *
	 * @return void
	 */
	public function the_rcno_admin_book_rating( $review_id, $display = true ) {
		echo $this->get_the_rcno_admin_book_rating( $review_id, $display );
	}



	/** ****************************************************************************
	 * BOOK COVER TEMPLATE TAGS
	 *******************************************************************************/

	/**
	 * Generates the book cover, or a default book cover if none is uploaded.
	 *
	 * @since 1.0.0
	 *
	 * @todo Replace `hw_string` with `wp_img_tag_add_width_and_height_attr()` in the future as this function
	 *       was introduced in WP 5.5
	 *
	 * @param int    $review_id	The current review's post ID.
	 * @param string $size      'thumbnail', 'medium', 'large', 'full', 'rcno-book-cover-sm', 'rcno-book-cover-lg'.
	 * @param bool   $wrapper	Whether to add the image URL to an `img` HTML tag.
	 * @param bool   $original	Whether to use the original uploaded image.
	 *
	 * @return bool|string
	 */
	public function get_the_rcno_book_cover( $review_id, $size = 'full', $wrapper = true, $original = false ) {

		$review        = get_post_custom( $review_id );
		$custom_url    = ! empty( $review['rcno_reviews_book_cover_url'] ) ? $review['rcno_reviews_book_cover_url'][0] : '';
		$attachment_id = isset( $review['rcno_reviews_book_cover_id'] ) ? $review['rcno_reviews_book_cover_id'][0] : 0;
		$size          = apply_filters( 'rcno_book_cover_size', $size );
		$hw_string     = '';

		if ( ! isset( $review['rcno_reviews_book_cover_src'] ) ) {
			return false;
		}

		$og_book_src = $review['rcno_reviews_book_cover_src'][0];
		$book_src    = $og_book_src;

		if ( ! $original ) {
			if ( 0 === $attachment_id ) {
				$attachment_id = $this->better_attachment_url_to_post_id( $og_book_src );
			}
			$image     = wp_get_attachment_image_src( $attachment_id, $size );
			$book_src  = $image ? $image[0] : false;
			$hw_string = $image ? image_hwstring( $image[1], $image[2] ) : '';
		}

		if ( ! $book_src ) {
			$no_cover = Rcno_Reviews_Option::get_option( 'rcno_default_cover', plugin_dir_url( __FILE__ ) . 'images/no-cover.jpg' );
			$book_src = apply_filters( 'rcno_no_book_cover_image', $no_cover, $review_id, $attachment_id );
		}

		if ( ! $wrapper ) {
			return $book_src;
		}

		// Use the book title because most users won't edit it from the file upload screen.
		$book_title = ! empty( $review['rcno_book_title'] ) ? $review['rcno_book_title'][0] : '';
		$book_alt   = ! empty( $review['rcno_reviews_book_cover_alt'][0] ) ?
		$review['rcno_reviews_book_cover_alt'][0] : $book_title;

		$book_title = $book_title ? esc_attr( $book_title ) : __( 'Book Title Unavailable', 'recencio-book-reviews' );
		$book_alt   = $book_alt ? esc_attr( $book_alt ) : __( 'no title has been provided for this book', 'recencio-book-reviews' );

		$book_cover_url = apply_filters( 'rcno_book_cover_url', esc_attr( $book_src ?: $og_book_src ), $review_id );

		$cover = '<img ' . $hw_string . ' src="' . $book_cover_url . '" ';
		$cover .= 'title="' . $book_title . '" ';
		$cover .= 'alt="' . $book_alt . '" ';
		$cover .= 'class="rcno-book-cover ' . $size . '" ';
		$cover .= 'data-rcno-attachment-id="' . (int) $attachment_id . '"';
		$cover .= '>';

		$cover = wp_image_add_srcset_and_sizes( $cover, wp_get_attachment_metadata( $attachment_id ), $attachment_id );

		$out = $cover;

		if ( $custom_url && apply_filters( 'rcno_book_cover_enable_custom_link', is_single() ) ) {
			$out = '<a href="' . esc_url( $custom_url ) . '" ';
			$out .= 'target="_blank" rel="' . implode( ' ', apply_filters( 'rcno_custom_link_alt_tags', array( 'nofollow', 'noopener' ) ) ) . '" ';
			$out .= '/>';
			$out .= $cover;
			$out .= '</a>';
		}

		return apply_filters( 'rcno_book_cover', $out, $book_cover_url, $attachment_id, $review_id );
	}

	/**
	 * Prints the book cover, or a default book cover if none is uploaded.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $review_id		The current review's post ID.
	 * @param string $size 'thumbnail', 'medium', 'full', 'rcno-book-cover-sm', 'rcno-book-cover-lg'.
	 * @param bool   $wrapper	Whether to wrap the image URL in the 'img' HTML tag.
	 * @param bool   $original	Whether to use the original uploaded image.
	 *
	 * @return void
	 */
	public function the_rcno_book_cover( $review_id, $size = 'full', $wrapper = true, $original = false ) {
		echo $this->get_the_rcno_book_cover( $review_id, $size, $wrapper, $original );
	}


	/** ****************************************************************************
	 * TAXONOMY RELATED TEMPLATE TAGS
	 *******************************************************************************/

	/**
	 * Creates the taxonomy headline.
	 *
	 * @param string $taxonomy The custom taxonomy to print the header tags for.
	 *
	 * @return string
	 */
	private function get_the_rcno_taxonomy_headline( $taxonomy ) {

		// Get the taxonomy.
		$tax = get_taxonomy( $taxonomy );
		$out = '';

		/**
		 * Add a H3 heading for embedded reviews or a H2
		 * heading for a standalone review.
		 */
		if ( $this->is_review_embedded() ) {
			$out .= '<h3>' . $tax->labels->name . '</h3>';
		} else {
			$out .= '<h2>' . $tax->labels->name . '</h2>';
		}

		return $out;
	}

	/**
	 * Print the taxonomy headline.
	 *
	 * @param string $taxonomy The custom taxonomy to print the header tags for.
	 *
	 * @return void
	 */
	public function the_rcno_taxonomy_headline( $taxonomy ) {
		echo $this->get_the_rcno_taxonomy_headline( $taxonomy );
	}


	/**
	 * Returns the list of terms per taxonomy along with labels.
	 *
	 * @param int 	 $review_id	The current review's post ID.
	 * @param string $taxonomy	The custom taxonomy to print the HTML tags for.
	 * @param bool   $label		Whether not to print the taxonomy label.
	 * @param string $sep		The separator used for multiple taxonomies.
	 * @param bool   $link		Whether not to create linkable taxonomy terms.
	 *
	 * @return null|string
	 */
	public function get_the_rcno_taxonomy_terms( $review_id, $taxonomy, $label = false, $sep = ', ', $link = true ) {

		$out = '';

		$terms = get_the_term_list( $review_id, $taxonomy, '<span class="rcno-tax-term">', $sep, '</span>' );
		$tax   = get_taxonomy( $taxonomy );

		if ( false === $tax || false === $terms || is_wp_error( $terms ) ) {
			return null;
		}

		$counts    = get_the_terms( $review_id, $taxonomy );
		$tax_label = $tax->labels->name;

		if ( count( $counts ) === 1 ) { // If we have only 1 term use the singular label name.
			$tax_label = $tax->labels->singular_name;
		}

		if ( ( ! $link && ! is_wp_error( $terms ) ) || apply_filters( 'rcno_taxonomy_strip_tags', false, $tax ) ) {
			$terms = strip_tags( $terms );
		}

		$prefix = '';

		if ( $label ) {
			$prefix = '<span class="rcno-tax-name">' . apply_filters( 'rcno_taxonomy_label', $tax_label ) . ': </span>';
		}

		if ( $terms && ! is_wp_error( $terms ) ) {
			$out .= sprintf(
				'<div class="rcno-term-list">%1s%2s</div>',
				$prefix,
				$terms
			);
			$out .= '';
		}

		return $out;
	}

	/**
	 * Prints taxonomy terms.
	 *
	 * @param int 	 $review_id	The current review's post ID.
	 * @param string $taxonomy	The custom taxonomy to print the HTML tags for.
	 * @param bool   $label		Whether not to print the taxonomy label.
	 * @param string $sep		The separator used for multiple taxonomies.
	 * @param bool   $link		Whether not to create linkable taxonomy terms.
	 *
	 * @return void
	 */
	public function the_rcno_taxonomy_terms( $review_id, $taxonomy, $label = false, $sep = ', ', $link = true  ) {
		echo $this->get_the_rcno_taxonomy_terms( $review_id, $taxonomy, $label, $sep, $link );
	}

	/**
	 * Generates a list of all the taxonomy and terms attached to a review post.
	 *
	 * @param int    $review_id The current review's post ID.
	 * @param bool   $label     Whether not to print the taxonomy label.
	 * @param string $sep       The separator used for multiple taxonomies.
	 *
	 * @return string
	 */
	private function get_the_rcno_taxonomy_list( $review_id, $label, $sep ) {

		$out = '';
		$custom_taxonomies = Rcno_Reviews_Option::get_option( 'rcno_taxonomy_selection' );
		$custom_taxonomies = explode( ',', $custom_taxonomies );

		foreach ( $custom_taxonomies as $tax_value ) {
			$tax = 'rcno_' . strtolower( $tax_value );
			$out .= $this->get_the_rcno_taxonomy_terms( $review_id, $tax, $label, $sep );
		}

		return $out;
	}

	/**
	 * Prints a list of all the taxonomy and terms attached to a review post.
	 *
	 * @param int    $review_id The current review's post ID.
	 * @param bool   $label     Whether not to print the taxonomy label.
	 * @param string $sep       The separator used for multiple taxonomies.
	 *
	 * @return void
	 */
	public function the_rcno_taxonomy_list( $review_id, $label, $sep ) {
		echo $this->get_the_rcno_taxonomy_list( $review_id, $label, $sep );
	}


	/** ****************************************************************************
	 * TAXONOMY ITEMS
	 *******************************************************************************/

	/**
	 * Creates a space separated list of all taxonomy terms for review.
	 *
	 * This is useful for adding CSS class names for all taxonomy terms attached to a review,
	 * E.g. 'elise-kova steam-punk loom-saga'
	 *
	 * @param int       $review_id  The current review's post ID.
	 * @param array     $taxonomies  The current review's post ID.
	 * @param string    $output     The returned output type.
	 *
	 * @return mixed
	 */
	public function get_the_rcno_taxonomy_items( $review_id, array $taxonomies, $output = 'slug' ) {

		$out = array();
		$taxonomy_values = array();

		foreach ( $taxonomies as $taxonomy ) {
			$tax_slug = 'rcno_' . strtolower( $taxonomy );
			$taxonomy_data = wp_get_post_terms( $review_id, $tax_slug );
			if ( ! empty( $taxonomy_data ) && ! is_wp_error( $taxonomy_data ) ) {
				$taxonomy_values[] = (array) $taxonomy_data;
			}
		}

		if ( 'slug' === $output ) {
			foreach ( $taxonomy_values as $tax_val ) {
				$out[] = $tax_val[0]->slug;
				if ( count( $tax_val ) > 1 ) {
					foreach ( (array) $tax_val as $val ) {
						$out[] = $val->slug;
					}
				}
			}
			return $out;
		}

		return $out;
	}

	/** ****************************************************************************
	 * REVIEW TITLE
	 *******************************************************************************/

	/**
	 * Get the review title normally handle by WP, this is for embedded reviews.
	 *
	 * @since 1.9.1
	 *
	 * @param int    $review_id The current review's post ID.
	 *
	 * @return string
	 */
	public function get_the_rcno_review_title( $review_id ) {

		$out       = '';
		$clickable = Rcno_Reviews_Option::get_option( 'rcno_reviews_embedded_title_links', false );

		if ( $this->is_review_embedded() ) {
			if ( $clickable ) {
				$out .= '<a class="rcno-review-title-link" ';
				$out .= 'href="' . get_the_permalink( $review_id ) . '" >';
				$out .= '<h2 class="rcno-review-title">';
				$out .= get_the_title( $review_id );
				$out .= '</h2>';
				$out .= '</a>';
			} else {
				$out .= '<h2 class="rcno-review-title">';
				$out .= get_the_title( $review_id );
				$out .= '</h2>';
			}
		}

		return $out;
	}

	/**
	 * Prints the review title normally handle by WP, this is for embedded reviews.
	 *
	 * @since 1.9.1
	 *
	 * @param int    $review_id The current review's post ID.
	 *
	 * @return void
	 */
	public function the_rcno_review_title( $review_id ) {
		echo $this->get_the_rcno_review_title( $review_id );
	}


	/** ****************************************************************************
	 * REVIEW BOOK DESCRIPTION TEMPLATE TAGS
	 *******************************************************************************/

	/**
	 * Renders the book description. An empty string if description is empty.
	 *
	 * @param int $review_id	The current review's post ID.
	 * @param int $word_count	The length of the book description.
	 * @param bool $strip_tags	Whether to run the content through wp_trim_words
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_the_rcno_book_description( $review_id, $word_count = 75, $strip_tags = true ) {

		$out              = '';
		$review           = get_post_custom( $review_id );
		$book_description = isset( $review['rcno_book_description'] ) ? $review['rcno_book_description'][0] : '';

		if ( apply_filters( 'rcno_book_description_strip_tags', $strip_tags ) ) {
			$book_description = wp_trim_words( $book_description, apply_filters( 'rcno_book_description_word_count', $word_count ) );
		}

		// Render the description only if it is not empty.
		if ( '' !==  $book_description ) {
			$out .= '<div class="rcno-book-description">';
			$out .= apply_filters( 'rcno_book_description', $book_description, $review_id );
			$out .= '</div>';
		}

		return $out;
	}

	/**
	 * Prints the book's description.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The current review's post ID.
	 *
	 * @return void
	 */
	public function the_rcno_book_description( $review_id ) {
		echo $this->get_the_rcno_book_description( $review_id );
	}

	/** ****************************************************************************
	 * REVIEW BOOK REVIEW EXCERPT TEMPLATE TAGS
	 *******************************************************************************/
	/**
	 * Gets the provide excerpt for a the book review.
	 *
	 * @since 1.0.0
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_the_excerpt/#user-contributed-notes
	 *
	 * @param int $review_id	The current review's post ID.
	 * @param int $length		The maximum character length of the excerpt.
	 *
	 * @return string
	 */
	public function get_the_rcno_book_review_excerpt( $review_id, $length = 200 ) {

		// setup_postdata( $review_id ); // See note by Mikko Saari.

		$review  = get_post( $review_id );
		$excerpt = $review->post_excerpt ?: wp_strip_all_tags( strip_shortcodes( $review->post_content ) );
		$length ++;

		if ( mb_strlen( $excerpt ) > $length ) {
			$sub_ex   = mb_substr( $excerpt, 0, $length - 5 );
			$ex_words = explode( ' ', $sub_ex );
			$ex_cut   = - mb_strlen( $ex_words[ count( $ex_words ) - 1 ] );
			if ( $ex_cut < 0 ) {
				return mb_substr( $sub_ex, 0, $ex_cut ) . '...';
			}

			return $sub_ex . '...';

		}

		return $excerpt;
	}

	/**
	* Gets a better version of excerpt of a review by ID
	*
	* @since 1.11.0
	*
	* @param    $review_id  int     The ID or object of the post to get the excerpt of
	* @param    $length     int     The length of the excerpt in words
	* @param    $tags       string  The allowed HTML tags. These will not be stripped out
	* @param    $extra      string  Text to append to the end of the excerpt
	*
	* @return string
	*/
	public function get_the_better_rcno_book_review_excerpt( $review_id, $length = 35, $tags = '<a><em><strong>', $extra = '...' ) {

		$review = get_post( $review_id );

		if ( ! $review ) {
			return '';
		}

		if ( has_excerpt( $review->ID ) ) {

			return apply_filters( 'rcno_content', $review->post_excerpt );
		}

		$review_excerpt = $review->post_content;
		$review_excerpt = strip_shortcodes( strip_tags( $review_excerpt, $tags ) );
		$review_excerpt = preg_split( '/\b/', $review_excerpt, $length * 2 + 1 );
		$excerpt_waste  = array_pop( $review_excerpt );
		$review_excerpt = implode( $review_excerpt );
		$review_excerpt .= $extra;

		return apply_filters( 'rcno_content', $review_excerpt );
	}

	/**
	 * Prints a better version of excerpt of a review by ID
	 *
	 * @since 1.11.0
	 *
	 * @param    $review_id  int     The ID or object of the post to get the excerpt of
	 * @param    $length     int     The length of the excerpt in words
	 * @param    $tags       string  The allowed HTML tags. These will not be stripped out
	 * @param    $extra      string  Text to append to the end of the excerpt
	 *
	 * @return void
	 */
	public function the_better_rcno_book_review_excerpt( $review_id, $length = 35, $tags = '<a><em><strong>', $extra = '...' ) {
		echo $this->get_the_better_rcno_book_review_excerpt( $review_id, $length, $tags, $extra );
	}

	/**
	 * Prints the provide excerpt for a book review.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The current review's post ID.
	 * @param int $length		The maximum character length of the excerpt.
	 *
	 * @return void
	 */
	public function the_rcno_book_review_excerpt( $review_id, $length = 200 ) {
		echo $this->get_the_rcno_book_review_excerpt( $review_id, $length );
	}


	/** ****************************************************************************
	 * REVIEW BOOK REVIEW CONTENT TEMPLATE TAGS
	 *******************************************************************************/

	/**
	 * Generates the markup for the review content of a book review.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The current review's post ID.
	 *
	 * @return string
	 */
	public function get_the_rcno_book_review_content( $review_id ) {

		$read_more = Rcno_Reviews_Option::get_option( 'rcno_excerpt_read_more' );

		$out  = '<div class="rcno-book-review-content">';
		// Using the `the_content` was causing to many issues, so we created our own filter.
		$out .= apply_filters( 'rcno_content', get_the_content( $read_more, false, $review_id) );
		$out .= '</div>';

		return $out;
	}

	/**
	 * Prints out the review content of a book review.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The current review's post ID.
	 *
	 * @return void
	 */
	public function the_rcno_book_review_content( $review_id ) {
		echo $this->get_the_rcno_book_review_content( $review_id );
	}



	/** ****************************************************************************
	 * REVIEW BOOK META TEMPLATE TAGS
	 *******************************************************************************/

	/**
	 * Generated the required markup for the requested stored book metadata,
	 * accessed via specific meta-keys.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $review_id		The current review's post ID.
	 * @param string $meta_key		The predefined meta key we are requesting.
	 * @param string $wrapper		The predefined HTML tag to wrap our content.
	 * @param bool   $label			Whether not to show a label.
	 *
	 * @return string|null
	 */
	public function get_the_rcno_book_meta( $review_id, $meta_key = '', $wrapper = '', $label = true ) {

		$review    = get_post_custom( (int) $review_id );
		$meta_keys = $this->meta_keys();
		$url       = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';

		$wrappers = array(
			'',
			'span',
			'div',
			'p',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
		);

		if ( '' === $meta_key || ! array_key_exists( $meta_key, $meta_keys ) || ! in_array( $wrapper, $wrappers, true ) ) {
			return '';
		}

		if ( isset( $review[ $meta_key ] ) ) {
			if ( '' !== $review[ $meta_key ][0] ) {
				$out = '';
				if ( '' === $wrapper ) {
					$out .= '';
				} else {
					$out .= '<' . $wrapper . ' class="' . sanitize_html_class( $meta_key ) . '">';
				}

				if ( $label ) {
					$out .= '<span class="rcno-meta-key">' . $meta_keys[ $meta_key ] . ': ' . '</span>';
				}

				/**
				 * Set this filter to 'false' if we want to skip converting link text
				 * to anchor tags. Useful for using URLs for book cover image.
				*/
				if ( apply_filters( 'rcno_skip_url_conversion', true ) ) {
					if ( false !== stripos( $review[ $meta_key ][0], 'goodreads' ) ) {
						$out .= '<span class="rcno-meta-value">'
								. '<a href="' . sanitize_text_field( $review[ $meta_key ][0] ) . '" target="_blank" rel="noopener">'
								. __( 'GoodReads.com', 'recencio-book-reviews' )
								. '</a>'
								. '</span>';
					} elseif( false !== stripos( $review[ $meta_key ][0], 'books.google' ) ) {
						$out .= '<span class="rcno-meta-value">'
								. '<a href="' . sanitize_text_field( $review[ $meta_key ][0] ) . '" target="_blank" rel="noopener">'
								. __( 'Google Books', 'recencio-book-reviews' )
								. '</a>'
								. '</span>';
					} else {
						$out .= '<span class="rcno-meta-value">'
								. preg_replace(
									$url,
									'<a href="http$2://$4" target="_blank" rel="noopener">$0</a>',
									sanitize_text_field( $review[ $meta_key ][0] )
								)
								. '</span>';
					}

				} elseif( '' !== $wrapper ) {
					$out .= '<span class="rcno-meta-value">' . sanitize_text_field( $review[ $meta_key ][0] ) . '</span>';
				} else {
					$out .= sanitize_text_field( $review[ $meta_key ][0] );
				}

				if ( '' === $wrapper ) {
					$out .= '';
				} else {
					$out .= '</' . $wrapper . '>';
				}

				return $out;
			}

			return '';
		}

		return '';
	}


	/**
	 * Prints out the requested stored book metadata, accessed via specific meta-keys.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $review_id		The current review's post ID.
	 * @param string $meta_key		The predefined meta key we are requesting.
	 * @param string $wrapper		The predefined HTML tag to wrap our content.
	 * @param bool   $label			Whether not to show a label.
	 *
	 * @return void
	 */
	public function the_rcno_book_meta( $review_id, $meta_key, $wrapper, $label ) {
		echo $this->get_the_rcno_book_meta( $review_id, $meta_key, $wrapper, $label );
	}

	/** ****************************************************************************
	 * REVIEW BOOK PURCHASE LINKS TEMPLATE TAGS
	 *******************************************************************************/

	/**
	 * Generates the and escapes book purchase links.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $review_id     The post ID of the book review.
	 * @param bool $label         Displays the label before the purchase links.
	 *
	 * @return string
	 */
	public function get_the_rcno_book_purchase_links( $review_id, $label = false ) {

		// If we are on the homepage don't display this.
		if ( ! is_singular( array( 'post', 'page', 'rcno_review' ) ) ) {
			return false;
		}

		// Disables the purchase links displaying on reviews.
		if ( ! Rcno_Reviews_Option::get_option( 'rcno_enable_purchase_links' ) ) {
			return false;
		}

		$purchase_links = get_post_meta( $review_id, 'rcno_review_buy_links', true );

		if ( ! $purchase_links ) {
			return false;
		}

		$links_label = Rcno_Reviews_Option::get_option( 'rcno_store_purchase_links_label' );
		$background  = Rcno_Reviews_Option::get_option( 'rcno_store_purchase_link_background' );
		$txt_color   = Rcno_Reviews_Option::get_option( 'rcno_store_purchase_link_text_color' );
		$dflt_stores = explode( ',', Rcno_Reviews_Option::get_option( 'rcno_store_purchase_links', '' ) );

		$stores = array();
		foreach ( $dflt_stores as $store ) {
			$stores[ sanitize_title( $store ) ] = $store;
		}

		$links = '<div class="rcno-purchase-links-container">';

		if ( $label ) {
			$links .= '<span class="buy-link-label">' . esc_html( $links_label ) . '</span> ';
		}

		foreach ( (array) $purchase_links as $value ) {
			$links .= '<a href="' . esc_url( $value['link'] ) . '" class="rcno-purchase-links '
			          . sanitize_html_class( $value['store'] ) . '"' . ' style="background:' . $background . '; color:' . $txt_color . '"' . 'target="_blank" rel="noopener nofollow"' . ' >';
			$links .= esc_html( $stores[ $value['store'] ] );
			$links .= '</a> ';
		}
		$links .= '</div>';

		return $links;
	}

	/**
	 * Prints the book purchase links.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $review_id     The post ID of the book review.
	 * @param bool $label         Displays the a label before the purchase links.
	 *
	 * @return void
	 */
	public function the_rcno_book_purchase_links( $review_id, $label = false ) {
		echo $this->get_the_rcno_book_purchase_links( $review_id, $label );
	}


	/** ****************************************************************************
	 * REVIEW BOOKS IN THIS SERIES
	 *******************************************************************************/
	/**
	 * Generates a list of books in the 'series' taxonomy.
	 *
	 * @since 1.7.0
	 *
	 * @param int       $review_id      The post ID of the book review.
	 * @param string    $taxonomy       The taxonomy to fetch reviews for.
	 * @param bool      $number         Display the book series number.
	 * @param mixed     $header         The header title.
	 *
	 * @return string
	 */
	public function get_the_rcno_books_in_series( $review_id, $taxonomy, $number, $header ) { // @TODO: To be deprecated

		// If we are on not on a single review don't display this.
		if ( ! is_single()  ) {
			return false;
		}

		$out = '';
		$book_data = array();

		$tax = get_the_terms( $review_id, $taxonomy );

		if ( false === $tax || is_wp_error( $tax ) ) {
			return false;
		}

		$custom_args = array(
			'post_type' => 'rcno_review',
			'meta_key'  => 'rcno_book_series_number',
			'orderby'   => 'meta_value_num',
			'order'     => 'ASC',
			'tax_query' => array(
				array(
					'taxonomy' => $tax[0]->taxonomy,
					'field'    => 'slug',
					'terms'    => $tax[0]->slug,
				),
			),
			'posts_per_page' => 50,
		);

		$data = new WP_Query( $custom_args );

		if ( $data->have_posts() ) {
			while ( $data->have_posts() ) : $data->the_post();
				$book_data[]   = array(
					'ID'    => get_the_ID(),
					'title' => get_the_title(),
					'link'  => get_the_permalink(),
				);
			endwhile;
		}
		wp_reset_postdata();

		if ( ! empty( $book_data ) ) {

			// Set header to a false value to use the default string.
			$header = ! empty( $header ) ? $header : __( 'Books in this series', 'recencio-book-reviews' ) . ': ';

			$out .= '<div class="rcno-book-series-container">';
			$out .= '<h5>' . esc_attr( $header ) . '</h5>';
			$out .= '<div class="rcno-book-series-wrapper">';
			foreach ( $book_data as $_book_data ) {
				if ( $_book_data[ 'ID' ] !== $review_id ) {
					$out .= '<div class="rcno-book-series-book">';
					$out .= '<a href="' . $_book_data[ 'link' ] . '">';
					$out .= $this->get_the_rcno_book_cover( $_book_data['ID'], 'rcno-book-cover-sm' );
					$out .= '</a>';
					if ( $number ) {
						$out .= $this->get_the_rcno_book_meta( $_book_data['ID'], 'rcno_book_series_number', 'span', false );
					}
					$out .= '</div>';
				}
			}
			$out .= '</div>';
			$out .= '</div>';

		}

		return $out;
	}

	/**
	 * Prints a list of books in the 'series' taxonomy.
	 *
	 * @since 1.7.0
	 *
	 * @param int       $review_id     The post ID of the book review.
	 * @param string    $taxonomy      The taxonomy to fetch reviews for.
	 * @param boolean   $number        Display the book number
	 * @param string    $header        The header title.
	 *
	 * @return void
	 */
	public function the_rcno_books_in_series( $review_id, $taxonomy = 'rcno_series', $number = true, $header ) {
		echo $this->get_the_rcno_books_in_series( $review_id, $taxonomy, $number, $header );
	}


	/** ****************************************************************************
	 * REVIEW BOOK BOOK LIST
	 *******************************************************************************/

	/**
	 * Generates a list of books in a given taxonomy.
	 *
	 * @since 1.8.0
	 *
	 * @param int       $review_id      The post ID of the book review.
	 * @param string    $taxonomy       The taxonomy to fetch reviews for.
	 * @param bool      $number         Display the book series number.
	 * @param mixed     $header         The header title.
	 *
	 * @return string
	 */
	public function get_the_rcno_book_list( $review_id, $taxonomy, $number, $header ) {

		// If we are on not on a single review don't display this.
		if ( ! is_single()  ) {
			return false;
		}

		// This shortcode should only work inside a book review post.
		if ( 'rcno_review' !== get_post_type( $review_id )  ) {
			$out = '<div class="rcno-books-error">';
			$out .= '<p>';
			$out .= __( 'This shortcode will only work inside a book review', 'recencio-book-reviews' );
			$out .= '</p>';
			$out .= '</div>';

			return $out;
		}

		$out = '';
		$book_data = array();

		$tax = get_the_terms( $review_id, 'rcno_' . $taxonomy );

		if ( false === $tax || is_wp_error( $tax ) ) {
			$out .= '<div class="rcno-books-error">';
			$out .= '<p>';
			$out .= sprintf( __( 'No entries found for the "%s" taxonomy.', 'recencio-book-reviews' ), $taxonomy );
			$out .= '</p>';
			$out .= '</div>';

			return $out;
		}

		if ( false === ( $data = get_transient( 'rcno_book_list_' . $review_id ) ) ) {
			$custom_args = array(
				'post_type'      => 'rcno_review',
				'order'          => 'ASC',
				'tax_query'      => array(
					array(
						'taxonomy' => $tax[ 0 ]->taxonomy,
						'field'    => 'slug',
						'terms'    => $tax[ 0 ]->slug,
					),
				),
				'rcno_book_list' => 30,
			);

			$data = new WP_Query( $custom_args );

			set_transient( 'rcno_book_list_' . $review_id, $data, 24 * HOUR_IN_SECONDS );
		}

		if ( $data->have_posts() ) {
			while ( $data->have_posts() ) : $data->the_post();
				$series_num  = strip_tags( $this->get_the_rcno_book_meta( get_the_ID(), 'rcno_book_series_number', '', false ) );
				$series      = get_the_terms( get_the_ID(), apply_filters( 'rcno_group_books_by_taxonomy', 'rcno_series' ));
				$book_data[] = array(
					'ID'         => get_the_ID(),
					'title'      => get_the_title(),
					'link'       => get_the_permalink(),
					'series_num' => $series_num ?: 0,
					'series'     => is_array( $series ) ? $series[0]->slug : '',
				);
				usort( $book_data, array( $this, 'series_integer_cmp' ) ); // Group books by series number,
				usort( $book_data, array( $this, 'series_string_cmp' ) );  // then by series slug.
			endwhile;
		}
		wp_reset_postdata();

		// If we only have 1 book in the series no need to print anything.
		if ( ! empty( $book_data ) && count( $book_data ) > 1 ) {

			// Set header to a false value to use the default string.
			$header = ! empty( $header ) ? $header : __( 'Books in this series', 'recencio-book-reviews' ) . ': ';

			$out .= '<div class="rcno-book-series-container">';
			$out .= '<h5>' . esc_attr( $header ) . '</h5>';
			$out .= '<div class="rcno-book-series-wrapper">';
			foreach ( $book_data as $_book_data ) {
				if ( $_book_data['ID'] !== $review_id ) {
					$out .= '<div class="rcno-book-series-book">';
					$out .= '<a href="' . esc_url( $_book_data['link'] ) . '">';
					$out .= $this->get_the_rcno_book_cover( $_book_data['ID'], 'rcno-book-cover-sm' );
					$out .= '</a>';
					if ( $number ) {
						$out .= $this->get_the_rcno_book_meta( $_book_data['ID'], 'rcno_book_series_number', 'span', false );
					}
					$out .= '</div>';
				}
			}
			$out .= '</div>';
			$out .= '</div>';

		}

		return $out;
	}

	/**
	 * Prints a list of books in a given taxonomy.
	 *
	 * @since 1.8.0
	 *
	 * @param int       $review_id      The post ID of the book review.
	 * @param string    $taxonomy       The taxonomy to fetch reviews for.
	 * @param bool      $number         Display the book series number.
	 * @param mixed     $header         The header title.
	 *
	 * @return void
	 */
	public function the_rcno_book_list( $review_id, $taxonomy = 'series', $number = true, $header = null ) {
		echo $this->get_the_rcno_book_list( $review_id, $taxonomy, $number, $header );
	}


	/** ****************************************************************************
	 * REVIEW BOOK REVIEW SCORE TEMPLATE TAGS
	 *******************************************************************************/

	/**
	 * Calculates the review scores.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $num       The review score number rating.
	 * @param string $type      The selected review type.
	 * @param bool   $stars     Whether to display the review scores as stars.
	 *
	 * @return string
	 */
	public function rcno_calc_review_score( $num, $type, $stars = false ) {

		$color = Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box_accent_2', '#ffd700' );

		$output = '';

		switch ( $type ) {

			case 'stars' :

				if ( false !== $stars ) {
					if ( $num <= 1 ) {
						$output = '<span class="badge-star" title="1 star" style="color:' . $color . '"><span>★</span><span>☆</span><span>☆</span><span>☆</span><span>☆</span></span>';
					}
					if ( $num > 1 && $num <= 2 ) {
						$output = '<span class="badge-star" title="2 stars" style="color:' . $color . '"><span>★</span><span>★</span><span>☆</span><span>☆</span><span>☆</span></span>';
					}
					if ( $num > 2 && $num <= 3 ) {
						$output = '<span class="badge-star" title="3 stars" style="color:' . $color . '"><span>★</span><span>★</span><span>★</span><span>☆</span><span>☆</span></span>';
					}
					if ( $num > 3 && $num <= 4 ) {
						$output = '<span class="badge-star" title="4 stars" style="color:' . $color . '"><span>★</span><span>★</span><span>★</span><span>★</span><span>☆</span></span>';
					}
					if ( $num > 4 && $num <= 5 ) {
						$output = '<span class="badge-star" title="5 stars" style="color:' . $color . '"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></span>';
					}
				} else {
					$output = $num;
				}

				break;

			case 'letter' :
				if ( $num <= 1 ) {
					$output = 'E';
				}
				if ( $num > 1 && $num <= 2 ) {
					$output = 'D';
				}
				if ( $num > 2 && $num <= 3 ) {
					$output = 'C';
				}
				if ( $num > 3 && $num <= 4 ) {
					$output = 'B';
				}
				if ( $num > 4 && $num <= 5 ) {
					$output = 'A';
				}
				break;

			case 'number';
				$output = number_format( $num, 1 );
				break;
		}

		return $output;
	}


	/**
	 * Creates the review box for the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param  int $review_id	The current review's post ID.
	 *
	 * @return string|false
	 */
	public function get_the_rcno_review_box( $review_id ) {

		// If we are on the homepage don't display this.
		if ( ! is_single()  ) {
			return false;
		}

		// Disables the review score box displaying on frontend book reviews.
		if ( ! Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box' ) ) {
			return false;
		}

		$background = Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box_background' );
		$accent     = Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box_accent' );
		$accent_2   = Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box_accent_2' );

		$rating_type     = get_post_meta( $review_id, 'rcno_review_score_type', true );
		$rating_criteria = get_post_meta( $review_id, 'rcno_review_score_criteria', true );

		if ( '' === $rating_criteria ) {
			return false; // We are not doing anything if a review score has not been set.
		}

		$rating_criteria_count = count( $rating_criteria );
		$review_summary        = $this->get_the_rcno_book_review_excerpt( $review_id );
		$review_box_title      = strip_tags( $this->get_the_rcno_book_meta( $review_id, 'rcno_book_title', '', false ) );

		$score_array = array();

		foreach ( (array) $rating_criteria as $criteria ) {
			$score_array[] = $criteria['score'];
		}


		$final_score = array_sum( $score_array );
		$final_score /= $rating_criteria_count ?: 1; // $final_score / $rating_criteria_count
		$final_score = number_format( $final_score, 1, '.', '' );


		$output = '';
		$output .= '<div id="rcno-review-score-box" style="background:' . esc_attr( $background ) . '">';
		$output .= '<div class="review-summary">';
		$output .= '<div class="overall-score" style="background:' . esc_attr( $accent ) . ';">';
		$output .= '<span class="overall">' . $this->rcno_calc_review_score( $final_score, $rating_type, true ) . '</span>';
		$output .= '<span class="overall-text" style="background: ' . $accent_2 . ';">' . __( 'Overall Score', 'recencio-book-reviews' ) . '</span>';
		$output .= '</div>';
		$output .= '<div class="review-text">';
		$output .= '<h2 class="review-title">' . $review_box_title . '</h2>';
		$output .= '<p>' . $review_summary . '</p>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '<ul>';
		foreach ( (array) $rating_criteria as $criteria ) {
			$percentage_score = ( $criteria['score'] / 5 ) * 100;

			if ( $criteria['label'] ) {
				$output .= '<li>';
			}
			$output .= '<div class="rcno-review-score-bar-container">';
			$output .= '<div class="review-score-bar" style="width:' . esc_attr( $percentage_score ) . '%; background:' . esc_attr( $accent ) . '">';
			$output .= '<span class="score-bar">' . $criteria['label'] . '</span>';
			$output .= '</div>';
			$output .= '<span class="right">';
			$output .= $this->rcno_calc_review_score( $criteria['score'], $rating_type, true );
			$output .= '</span>';
			$output .= '</div>';
			$output .= '</li>';

		}
		$output .= '</ul>';

		$output .= '</div><!-- End #review-box -->';

		return $output;
	}

	/**
	 * Prints the review box on the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The current review's post ID.
	 *
	 * @return void
	 */
	public function the_rcno_review_box( $review_id ) {
		echo $this->get_the_rcno_review_box( $review_id );
	}


	/**
	 * Creates the review badge for the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The current review's post ID.
	 *
	 * @return string|null|false
	 */
	private function rcno_the_review_badge( $review_id ) {

		// If we are on the homepage don't display this.
		if ( ! is_single()  ) {
			return false;
		}

		$background = Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box_background' );
		$accent     = Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box_accent' );

		// Disables the review score badge displaying on frontend book reviews.
		if ( ! Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box' ) ) {
			return false;
		}

		$rating_type     = get_post_meta( $review_id, 'rcno_review_score_type', true );
		$rating_criteria = get_post_meta( $review_id, 'rcno_review_score_criteria', true );

		if ( '' === $rating_criteria ) {
			return null; // We are not doing anything if a review score has not been set.
		}

		$rating_criteria_count = count( $rating_criteria );

		$out      = '';
		$score_array = array();

		if ( $rating_criteria ) {
			foreach ( (array) $rating_criteria as $criteria ) {
				$score_array[] = $criteria['score'];
			}
		}

		$final_score = array_sum( $score_array );
		$final_score /= $rating_criteria_count; // $final_score / $rating_criteria_count
		$final_score = number_format( $final_score, 1, '.', '' );

		$out .= '<div class="rcno-review-badge review-badge-' . esc_attr( $rating_type ) . '" ';
		$out .= 'style="background-color: ' . esc_attr( $accent ) . '"';
		$out .= '>';
		$out .= '<div class="score">';
		$out .= $this->rcno_calc_review_score( $final_score, $rating_type, true );
		$out .= '</div>';
		$out .= '<p>' . __( 'Review Score', 'recencio-book-reviews' ) . '</p>';
		$out .= '</div>';

		return $out;
	}

	/**
	 * Prints the review badge on the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The current review's post ID.
	 *
	 * @return void
	 */
	public function rcno_print_review_badge( $review_id ) {
		echo $this->rcno_the_review_badge( $review_id );
	}


	/** ****************************************************************************
	 * REVIEW BOOK REVIEW SCHEME DATA TAGS
	 *******************************************************************************/

	/**
	 * Gets the review criteria scores even if the box is disabled.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The current review's post ID.
	 *
	 * @return array
	 */
	public function rcno_get_review_score( $review_id ) {
		$review_scores = get_post_meta( $review_id, 'rcno_review_score_criteria', true );
		$scores        = array();

		if ( $review_scores ) {
			foreach ( (array) $review_scores as $score ) {
				if ( '' === $score['score'] ) {
					continue;
				}
				$scores[] = $score['score'];
			}
		}

		return $scores;
	}

	/**
	 * Generates the markup for the 'Book' schema type in the JSON+LD format.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The post ID of the current review.
	 *
	 * @return string
	 */
	public function get_the_rcno_book_schema_data( $review_id ) {

		$data = array();

		$book_title      = strip_tags( $this->get_the_rcno_book_meta( $review_id, 'rcno_book_title', '', false ) );
		$book_fmt        = strip_tags( $this->get_the_rcno_book_meta( $review_id, 'rcno_book_pub_format', '', false ) );
		$book_author     = $this->get_the_rcno_taxonomy_terms( $review_id, 'rcno_author' ) ? wp_strip_all_tags( $this->get_the_rcno_taxonomy_terms( $review_id, 'rcno_author' ) ) : '';
		$book_review_url = get_post_permalink( $review_id );
		$book_pub_date   = strtotime( strip_tags( $this->get_the_rcno_book_meta( $review_id, 'rcno_book_pub_date', '', false ) ) );
		$book_genre      = $this->get_the_rcno_taxonomy_terms( $review_id, 'rcno_genre' ) ? wp_strip_all_tags( $this->get_the_rcno_taxonomy_terms( $review_id, 'rcno_genre' ) ) : '';
		$book_publisher  = $this->get_the_rcno_taxonomy_terms( $review_id, 'rcno_publisher' ) ? wp_strip_all_tags( $this->get_the_rcno_taxonomy_terms( $review_id, 'rcno_publisher' ) ) : '';
		$book_isbn       = strip_tags( $this->get_the_rcno_book_meta( $review_id, 'rcno_book_isbn', '', false ) );
		$book_edtn       = strip_tags( $this->get_the_rcno_book_meta( $review_id, 'rcno_book_pub_edition', '', false ) );
		$book_pc         = strip_tags( $this->get_the_rcno_book_meta( $review_id, 'rcno_book_page_count', '', false ) );
		$book_ext_url    = strip_tags( $this->get_the_rcno_book_meta( $review_id, 'rcno_book_gr_url', '', false ) );
		$thumbnail       = $this->get_the_rcno_book_cover( $review_id, 'full', false, false );

		$book_aut_url = '';
		$author_terms = get_the_terms( $review_id, 'rcno_author' );
		if ( $author_terms && ! is_wp_error( $author_terms ) ) {
			$book_aut_url = get_term_meta( $author_terms[0]->term_id, 'rcno_author_taxonomy_url', true );
		}

		if ( '' === $book_aut_url ) {
			$book_aut_url = 'https://www.goodreads.com/book/author/' . str_replace( ' ', '+', $book_author );
		}

		$data['@context'] = 'http://schema.org';
		$data['@type']    = 'Book';
		$data['name']     = $book_title;
		$data['author'] = array(
			'@type' => 'Person',
			'name' => $book_author,
			'sameAs' => $book_aut_url,
		);
		$data['url'] = $book_review_url;
		$data['sameAs'] = $book_ext_url;
		$data['datePublished'] = date( 'c', $book_pub_date );
		$data['genre'] = $book_genre;
		$data['publisher'] = $book_publisher;
		$data['workExample'][] = array(
				'@type' => 'Book',
				'isbn'  => $book_isbn,
				'bookEdition' => $book_edtn,
				'bookFormat' => 'http://schema.org/' . $book_fmt,
				'numberOfPages' => (int) $book_pc,
		);

		$data['thumbnailUrl'] = $thumbnail;

		$data = apply_filters( 'rcno_book_schema_data_filter', $data, $review_id );

		return wp_json_encode( $data );
	}

	/**
	 * Prints the 'Book' schema type in the JSON+LD format.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The post ID of the current review.
	 *
	 * @return false|void
	 */
	public function the_rcno_book_schema_data( $review_id ) {

		// If we are on the homepage don't display this.
		if ( ! is_single() || Rcno_Reviews_Option::get_option( 'rcno_disable_book_schema', false ) ) {
			return false;
		}

		echo '<script type="application/ld+json">' . $this->get_the_rcno_book_schema_data( $review_id ) . '</script>';
	}


	/**
	 * Generates the markup for the 'Review' schema type in the JSON+LD format.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The post ID of the current review.
	 *
	 * @return string
	 */
	public function get_the_rcno_review_schema_data( $review_id ) {

		$data = array();

		$reviewer     = get_the_author();
		$reviewer_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
		$review_url   = get_post_permalink( $review_id );
		$review_date  = get_the_date( 'c', $review_id );
		$site_name    = get_bloginfo( 'name' );
		$site_url     = get_bloginfo( 'url' );
		$description  = $this->get_the_rcno_book_review_excerpt( $review_id );
		$language     = get_bloginfo( 'language' );
		$book_name    = strip_tags( $this->get_the_rcno_book_meta( $review_id, 'rcno_book_title', '', false ) );
		$book_isbn    = strip_tags( $this->get_the_rcno_book_meta( $review_id, 'rcno_book_isbn', '', false ) );
		$book_author  = $this->get_the_rcno_taxonomy_terms( $review_id, 'rcno_author' ) ? wp_strip_all_tags( $this->get_the_rcno_taxonomy_terms( $review_id, 'rcno_author' ) ) : '';
		$thumbnail    = $this->get_the_rcno_book_cover( $review_id, 'full', false, false );

		$book_aut_url = '';
		$author_terms = get_the_terms( $review_id, 'rcno_author' );
		if ( $author_terms && ! is_wp_error( $author_terms ) ) {
			$book_aut_url = get_term_meta( $author_terms[0]->term_id, 'rcno_author_taxonomy_url', true );
		}

		if ( '' === $book_aut_url ) {
			$book_aut_url = 'https://www.goodreads.com/book/author/' . str_replace( ' ', '+', $book_author );
		}

		$bk_pub_date  = strtotime( strip_tags( $this->get_the_rcno_book_meta( $review_id, 'rcno_book_pub_date', '', false ) ) );
		$priv_score   = $this->rcno_get_review_score( $review_id );
		$pub_rating   = new Rcno_Reviews_Public_Rating( $this->plugin_name, $this->version );
		$admin_rating = $this->get_the_rcno_admin_book_rating( $review_id, false );

		$data['@context']      = 'http://schema.org';
		$data['@type']         = 'Review';
		$data['author']        = array(
			'@type'  => 'Person',
			'name'   => $reviewer,
			'sameAs' => array(
				$reviewer_url,
			),
		);
		$data['url']           = $review_url;
		$data['datePublished'] = $review_date;
		$data['publisher']     = array(
			'@type'  => 'Organization',
			'name'   => $site_name,
			'sameAs' => $site_url,
		);
		$data['description'] = $description;
		$data['inLanguage'] = $language;
		$data['itemReviewed'] = array(
			'@type' => 'Book',
			'name'  => $book_name,
			'isbn'  => $book_isbn,
			'author' => array(
				'@type'  => 'Person',
				'name'   => $book_author,
				'sameAs' => $book_aut_url,
			),
			'datePublished' => date( 'c', $bk_pub_date ),
		);
		if ( $pub_rating->rcno_rating_info( 'count' ) > 0 ) {
			$data['itemReviewed']['aggregateRating'] = array(
				'@type'         => 'AggregateRating',
				'worstRating'   => 1,
				'bestRating'    => 5,
				'ratingValue'   => $pub_rating->rcno_rating_info( 'avg' ),
				'ratingCount'   => $pub_rating->rcno_rating_info( 'count' ),
			);
		}
		if ( $priv_score ) {
			$data['reviewRating'] = array(
				'@type'       => 'Rating',
				'worstRating' => 1,
				'bestRating'  => 5,
				'ratingValue' => number_format( array_sum( $priv_score ) / count( $priv_score ), 1 ),
			);
		} else {
			$data['reviewRating'] = array(
				'@type'       => 'Rating',
				'worstRating' => 1,
				'bestRating'  => 5,
				'ratingValue' => $admin_rating < 1 ? 1 : $admin_rating,
			);
		}

		$data['thumbnailUrl'] = $thumbnail;

		$data = apply_filters( 'rcno_review_schema_data_filter', $data, $review_id );

		return wp_json_encode( $data );
	}

	/**
	 * Prints the 'Review' schema type in the JSON+LD format.
	 *
	 * @since 1.0.0
	 *
	 * @param int $review_id	The post ID of the current review.
	 *
	 * @return void
	 */
	public function the_rcno_review_schema_data( $review_id ) {

		// If we are on the homepage don't display this.
		if ( ! is_single() || Rcno_Reviews_Option::get_option( 'rcno_disable_review_schema', false ) ) {
			return;
		}

		echo '<script type="application/ld+json">' . $this->get_the_rcno_review_schema_data( $review_id ) . '</script>';
	}


	/**
	 * Generates a navigation bar of letters of the alphabet.
	 *
	 * @since 1.0.0
	 *
	 * @param array $letters	An array of the first letter of all taxonomy terms with reviews.
	 *
	 * @return string
	 */
	public function get_the_rcno_alphabet_nav_bar( array $letters ) {

		// An array with the (complete) alphabet.
		$alphabet = range( 'A', 'Z' );

		$out = '';

		// Start the list.
		$out .= '<ul class="rcno-alphabet-navigation">';

		foreach ( $alphabet as $letter ) {
			// loop through the alphabet.
			if ( $letters ) {
				if ( in_array( $letter, $letters, true ) ) {
					// active letter, so we should set a link in the nav menu.
					$out .= '<li class="active"><a href="#' . $letter . '">' . $letter . '</a></li>';
				} else {
					// inactive letter, no link.
					$out .= '<li class="inactive">' . $letter . '</li>';
				}
			} else {
				// each letter active.
				$out .= '<li class="active"><a href="#' . $letter . '">' . $letter . '</a></li>';
			}
		}

		// End the list.
		$out .= '</ul>';

		// return the rendered nav bar.
		return $out;
	}

	/**
	 * Prints a navigation bar of letters of the alphabet.
	 *
	 * @since 1.0.0
	 *
	 * @param array $letters	An array of the first letter of all taxonomy terms with reviews.
	 *
	 * @return void
	 */
	public function the_rcno_alphabet_nav_bar( $letters ) {
		echo $this->get_the_rcno_alphabet_nav_bar( $letters );
	}


	/**
	 * Check if the review is embedded in a post, page or another custom post type.
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	public function is_review_embedded() {
		return 'rcno_review' !== get_post_type();
	}

	public function series_integer_cmp( $a, $b ) {
		return $a['series_num'] - $b['series_num'];
	}

	public function series_string_cmp( $a, $b ) {
		return strcasecmp( $a['series'], $b['series'] );
	}

	/**
	 * @param $url
	 *
	 * @return bool|int|mixed
	 */
	public function better_attachment_url_to_post_id( $url ){

		$id = wp_cache_get( 'better_attachment_url_to_post_id_' . md5( $url ) );

		if ( false === $id ){
			$id = attachment_url_to_postid( $url );
			if ( empty( $id ) ){
				wp_cache_set( 'better_attachment_url_to_post_id_' . md5( $url ),
					'not_found',
					'default',
					12 * HOUR_IN_SECONDS + mt_rand( 0, 4 * HOUR_IN_SECONDS )
				);
			}else {
				wp_cache_set(
					'better_attachment_url_to_post_id_' . md5( $url ),
					$id,
					'default',
					24 * HOUR_IN_SECONDS + mt_rand( 0, 12 * HOUR_IN_SECONDS )
				);
			}
		} else if( 'not_found' === $id ){
			return false;
		}
		return $id;
	}

	/**
	 * This generate an array of terms for each taxonomy attached to a specific term.
	 *
	 * This function is based on the WP `get_body_class` function.
	 *
	 * @since 1.12.0
	 *
	 * @see https://goo.gl/QrCqnd
	 * @param int $review_id The post ID of the current review.
	 *
	 * @return array
	 */
	public function get_rcno_review_html_classes( $review_id ) {

		$review = get_post( $review_id );

		if ( null === $review ) {
			return array();
		}

		$classes   = array();
		$classes[] = 'review-' . $review->ID;

		// All public taxonomies.
		$taxonomies = get_taxonomies( array( 'public' => true ) );
		foreach ( (array) $taxonomies as $taxonomy ) {
			if ( is_object_in_taxonomy( $review->post_type, $taxonomy ) ) {
				foreach ( (array) get_the_terms( $review->ID, $taxonomy ) as $term ) {
					if ( empty( $term->slug ) ) {
						continue;
					}
					$term_class = sanitize_html_class( $term->slug, $term->term_id );
					$classes[] = $term_class;
				}
			}
		}
		$classes[] = round( $this->get_the_rcno_admin_book_rating( $review_id, false ) );
		$classes   = apply_filters( 'rcno_review_html_classes', $classes );

		return array_unique( array_map( 'esc_attr', $classes ) );
	}

}
