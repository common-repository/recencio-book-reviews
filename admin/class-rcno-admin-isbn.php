<?php

/**
 * Saving the book ISBN meta information.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Admin_ISBN {

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
	 * Add a metabox for the book's ISBN information.
	 *
	 * If the option has been disabled on the plugin setting page, return early with a false
	 * and don't do anything.
	 *
	 * @since 1.0.0
	 *
	 * @uses  add_meta_box
	 * @return bool
	 */
	public function rcno_book_isbn_metabox() {

		// Disables the ISBN metabox displaying on review edit screen.
		if ( false === (bool) Rcno_Reviews_Option::get_option( 'rcno_show_isbn' ) ) {
			return false;
		}

		add_meta_box(
			'rcno_book_isbn_metabox',
			__( 'ISBN', 'recencio-book-reviews' ),
			array( $this, 'do_rcno_book_isbn_metabox' ),
			'rcno_review',
			'side',
			'high'
		);

		return true;
	}

	/**
	 * Builds and display the metabox UI.
	 *
	 * @since 1.0.0
	 *
	 * @param object $review    The current post object.
	 *
	 * @return void
	 */
	public function do_rcno_book_isbn_metabox( $review ) {
		include __DIR__ . '/views/rcno-reviews-isbn-metabox.php';
	}

	/**
	 * Check the presence of, sanitizes then saves book's ISBN.
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
	public function rcno_save_book_isbn_metadata( $review_id, $data, $review = null ) {

		// Saving description not only to the post_meta field but also to excerpt and content.
		if ( isset( $data['rcno_book_isbn'] ) && wp_verify_nonce( $data['rcno_isbn_nonce'], 'rcno_save_book_isbn_metadata' ) ) {

			$book_isbn = sanitize_text_field( $data['rcno_book_isbn'] );
			update_post_meta( $review_id, 'rcno_book_isbn', $book_isbn );
		}
	}

}
