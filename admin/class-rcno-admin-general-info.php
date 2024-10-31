<?php

/**
 * Saving the general book meta information.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Admin_General_Info {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $plugin_name     The ID of this plugin.
	 * @param   string $version         The current version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Adda a metabox for the book general meta information, such as title,
	 * illustrator, page count, etc
	 *
	 * @since 1.0.0
	 *
	 * @uses  add_meta_meta_box()
	 * @return void
	 */
	public function rcno_book_general_info_metabox() {

		add_meta_box(
			'rcno_book_general_info_metabox',
			__( 'General Information', 'recencio-book-reviews' ),
			array( $this, 'do_rcno_book_general_info_metabox' ),
			'rcno_review',
			'normal',
			'high'
		);
	}

	/**
	 * Creates the metaboxes UI.
	 *
	 * @since 1.0.0
	 *
	 * @param object $review    The current post object.
	 *
	 * @return void
	 */
	public function do_rcno_book_general_info_metabox( $review ) {
		include __DIR__ . '/views/rcno-general-info-metabox.php';
	}

	/**
	 * Checks the presence of, sanitizes then saves the book data on the review edit screen.
	 * Also runs a nonce security check
	 *
	 * @since 1.0.0
	 *
	 * @uses  update_post_meta()
	 * @uses  wp_verify_nonce()
	 * @uses  sanitize_text_field()
	 *
	 * @param int   $review_id The post ID of the review post.
	 * @param array $data      The data passed from the post custom metabox.
	 * @param mixed $review     The review object this data is being saved to.
	 *
	 * @return void
	 */
	public function rcno_save_book_general_info_metadata( $review_id, $data, $review = null ) {


		// Saving book title post_meta field.
		if ( isset( $data['rcno_book_title'] )
		     && wp_verify_nonce( $data['rcno_general_title_nonce'], 'rcno_save_book_title_metadata' )
		) {
			$book_title = sanitize_text_field( $data['rcno_book_title'] );
			update_post_meta( $review_id, 'rcno_book_title', $book_title );
		}

		// Saving book illustrator post_meta field.
		if ( isset( $data['rcno_book_illustrator'] )
		     && wp_verify_nonce( $data['rcno_general_illustrator_nonce'], 'rcno_save_book_illustrator_metadata' )
		) {
			$book_illustrator = sanitize_text_field( $data['rcno_book_illustrator'] );
			update_post_meta( $review_id, 'rcno_book_illustrator', $book_illustrator );
		}

		// Saving book published date post_meta field.
		if ( isset( $data['rcno_book_pub_date'] )
		     && wp_verify_nonce( $data['rcno_general_pub_date_nonce'], 'rcno_save_book_pub_date_metadata' )
		) {
			$book_pub_date = sanitize_text_field( $data['rcno_book_pub_date'] );
			update_post_meta( $review_id, 'rcno_book_pub_date', $book_pub_date );
		}

		// Saving book published format post_meta field.
		if ( isset( $data['rcno_book_pub_format'] )
		     && wp_verify_nonce( $data['rcno_general_pub_format_nonce'], 'rcno_save_book_pub_format_metadata' )
		) {
			$book_pub_format = sanitize_text_field( $data['rcno_book_pub_format'] );
			update_post_meta( $review_id, 'rcno_book_pub_format', $book_pub_format );
		}

		// Saving book published edition post_meta field.
		if ( isset( $data['rcno_book_pub_edition'] )
		     && wp_verify_nonce( $data['rcno_general_pub_edition_nonce'], 'rcno_save_book_pub_edition_metadata' )
		) {
			$book_pub_edition = sanitize_text_field( $data['rcno_book_pub_edition'] );
			update_post_meta( $review_id, 'rcno_book_pub_edition', $book_pub_edition );
		}

		// Saving book page count post_meta field.
		if ( isset( $data['rcno_book_series_number'] )
		     && wp_verify_nonce( $data['rcno_general_series_number_nonce'], 'rcno_save_book_series_number_metadata' )
		) {
			$book_series_number = sanitize_text_field( $data['rcno_book_series_number'] );
			update_post_meta( $review_id, 'rcno_book_series_number', $book_series_number );
		}

		// Saving book page count post_meta field.
		if ( isset( $data['rcno_book_page_count'] )
		     && wp_verify_nonce( $data['rcno_general_page_count_nonce'], 'rcno_save_book_page_count_metadata' )
		) {
			$book_page_count = sanitize_text_field( $data['rcno_book_page_count'] );
			update_post_meta( $review_id, 'rcno_book_page_count', $book_page_count );
		}

		// Saving book Gr review average post_meta field.
		if ( isset( $data['rcno_book_gr_review'] )
		     && wp_verify_nonce( $data['rcno_general_gr_review_nonce'], 'rcno_save_book_gr_review_metadata' )
		) {
			$book_gr_review = sanitize_text_field( $data['rcno_book_gr_review'] );
			update_post_meta( $review_id, 'rcno_book_gr_review', $book_gr_review );
		}

		// Saving book Gr ID post_meta field.
		if ( isset( $data['rcno_book_gr_id'] )
		     && wp_verify_nonce( $data['rcno_general_gr_id_nonce'], 'rcno_save_book_gr_id_metadata' )
		) {
			$book_gr_id = sanitize_text_field( $data['rcno_book_gr_id'] );
			update_post_meta( $review_id, 'rcno_book_gr_id', $book_gr_id );
		}

		// Saving book ISBN13 post_meta field.
		if ( isset( $data['rcno_book_isbn13'] )
		     && wp_verify_nonce( $data['rcno_general_isbn13_nonce'], 'rcno_save_book_isbn13_metadata' )
		) {
			$book_isbn13 = sanitize_text_field( $data['rcno_book_isbn13'] );
			update_post_meta( $review_id, 'rcno_book_isbn13', $book_isbn13 );
		}

		// Saving book ASIN post_meta field.
		if ( isset( $data['rcno_book_asin'] )
		     && wp_verify_nonce( $data['rcno_general_asin_nonce'], 'rcno_save_book_asin_metadata' )
		) {
			$book_asin = sanitize_text_field( $data['rcno_book_asin'] );
			update_post_meta( $review_id, 'rcno_book_asin', $book_asin );
		}

		// Saving book Gr URL post_meta field.
		if ( isset( $data['rcno_book_gr_url'] )
		     && wp_verify_nonce( $data['rcno_general_gr_url_nonce'], 'rcno_save_book_gr_url_metadata' )
		) {
			$book_gr_url = sanitize_text_field( $data['rcno_book_gr_url'] );
			update_post_meta( $review_id, 'rcno_book_gr_url', $book_gr_url );
		}

		do_action( 'rcno_save_admin_general_info_metadata' );
	}

}
