<?php

/**
 * Saving the book review score meta information.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Admin_Review_Score {

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
	 * Add a metabox for the book's review criteria based scoring information.
	 * This feature can disabled via the plugin's setting page.
	 *
	 * @since 1.0.0
	 *
	 * @uses  add_meta_box()
	 *
	 * @return bool
	 */
	public function rcno_book_review_score_metabox() {
		// Disables the review score metabox displaying on review edit screen.
		if ( false === (bool) Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box' ) ) {
			return false;
		}

		add_meta_box(
			'rcno_book_review_score_metabox',
			__( 'Review Score', 'recencio-book-reviews' ),
			array( $this, 'do_rcno_book_review_score_metabox' ),
			'rcno_review',
			'normal',
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
	public function do_rcno_book_review_score_metabox( $review ) {
		include __DIR__ . '/views/rcno-review-score-metabox.php';
	}

	/**
	 * Fetches, sanitizes, delete and saves the book's criteria based scoring information
	 *
	 * @since 1.0.0
	 *
	 * @uses  get_post_meta()
	 * @uses  update_post_meta()
	 * @uses  delete_post_meta()
	 * @uses  wp_verify_nonce()
	 * @uses  sanitize_text_field()
	 *
	 * @param int   $review_id  The post ID of the review post.
	 * @param array $data       The data passed from the post custom metabox.
	 * @param mixed $review     The review object this data is being saved to.
	 *
	 * @return void
	 */
	public function rcno_save_book_review_score_metadata( $review_id, $data, $review = null ) {

		$old = get_post_meta( $review_id, 'rcno_review_score_criteria', true );
		$new = array();

		$labels = isset( $data['label'] ) ? $data['label'] : array();
		$scores = isset( $data['score'] ) ? $data['score'] : array();

		$count = count( $labels );

		for ( $i = 0; $i < $count; $i ++ ) {
			if ( '' !== $labels[ $i ] ) {
				$new[ $i ]['label'] = sanitize_text_field( $labels[ $i ] );
				$new[ $i ]['score'] = sanitize_text_field( $scores[ $i ] );
			}
		}
		if ( ! empty( $new ) && $new !== $old ) {
			update_post_meta( $review_id, 'rcno_review_score_criteria', $new );
		} elseif ( empty( $new ) && $old ) {
			delete_post_meta( $review_id, 'rcno_review_score_criteria', $old );
		}

		// @TODO: Below needs sanitization.
		if ( isset( $data['rcno_review_score_type'] ) ) {
			$review_score_type = $data['rcno_review_score_type'];
			update_post_meta( $review_id, 'rcno_review_score_type', $review_score_type );
		}

		if ( isset( $data['rcno_review_score_position'] ) ) {
			$review_score_position = $data['rcno_review_score_position'];
			update_post_meta( $review_id, 'rcno_review_score_position', $review_score_position );
		}

		if ( isset( $data['rcno_review_score_enable'] ) ) {
			$review_score_enable = $data['rcno_review_score_enable'];
			update_post_meta( $review_id, 'rcno_review_score_enable', $review_score_enable );
		}
	}

}
