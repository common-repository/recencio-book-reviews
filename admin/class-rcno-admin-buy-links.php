<?php

/**
 * Handles the creation, storage and sanitation of buy links for the reviewed book.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Admin_Buy_Links {

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
	public function rcno_book_buy_links_metabox() {

		// Disables the purchase links metabox displaying on review edit screen.
		if ( false === (bool) Rcno_Reviews_Option::get_option( 'rcno_enable_purchase_links' ) ) {
			return false;
		}

		add_meta_box(
			'rcno_book_buy_links_metabox',
			__( 'Purchase Links', 'recencio-book-reviews' ),
			array( $this, 'do_rcno_book_buy_links_metabox' ),
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
	public function do_rcno_book_buy_links_metabox( $review ) {
		include __DIR__ . '/views/rcno-buy-links-metabox.php';
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
	 * @uses  esc_url()
	 *
	 * @param int   $review_id The post ID of the review post.
	 * @param array $data      The data passed from the post custom metabox.
	 * @param mixed $review     The review object this data is being saved to.
	 *
	 * @return void
	 */
	public function rcno_save_book_buy_links_metadata( $review_id, $data, $review = null ) {

		if ( isset( $data['rcno_buy_links_nonce'] ) && wp_verify_nonce( $data['rcno_buy_links_nonce'], 'rcno_buy_links_meta_box_nonce' ) ) {

			$old = get_post_meta( $review_id, 'rcno_review_buy_links', true );
			$new = array();

			$stores = isset( $data['store'] ) ? $data['store'] : array();
			$links  = isset( $data['link'] ) ? $data['link'] : array();

			$count = count( $stores );

			for ( $i = 0; $i < $count; $i ++ ) {
				if ( '' !== $links[ $i ] ) { // Don't save an item if a link is not provided.
					$new[ $i ]['store'] = sanitize_text_field( $stores[ $i ] );
					$new[ $i ]['link']  = esc_url( $links[ $i ] );
				}
			}

			if ( ! empty( $new ) && $new !== $old ) {
				update_post_meta( $review_id, 'rcno_review_buy_links', $new );
			} elseif ( empty( $new ) && $old ) {
				delete_post_meta( $review_id, 'rcno_review_buy_links', $old );
			}
		}
	}
	
}
