<?php

/**
 * Saving the book review 5 star score meta information.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Admin_Review_Rating {

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
	 * Add a metabox for the book's 5 star rating information.
	 *
	 * @since 1.0.0
	 *
	 * @uses  add_meta_box()
	 * @return void
	 */
	public function rcno_book_review_rating_metabox() {

		if ( (bool) Rcno_Reviews_Option::get_option( 'rcno_enable_star_rating_box', false ) ) {

			add_meta_box(
				'rcno_book_review_rating_metabox',
				__( '5 Star Rating', 'recencio-book-reviews' ),
				array( $this, 'do_rcno_book_review_rating_metabox' ),
				'rcno_review',
				'side',
				'high'
			);
		}
	}

	/**
	 * Builds and display the 5 star metabox UI.
	 *
	 * @since 1.0.0
	 *
	 * @param object $review    The current post object.
	 *
	 * @return void
	 */
	public function do_rcno_book_review_rating_metabox( $review ) {
		include __DIR__ . '/views/rcno-review-rating-metabox.php';
	}

	/**
	 * Checks the presence of, sanitizes then saves all the book's 5 star rating.
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
	public function rcno_save_book_review_rating_metadata( $review_id, $data, $review = null ) {

		if ( isset( $data['rcno_admin_rating'] ) ) { //TODO: This needs a nonce check.

			$book_rating = sanitize_text_field( $data['rcno_admin_rating'] );
			update_post_meta( $review_id, 'rcno_admin_rating', $book_rating );
		}
	}

}
