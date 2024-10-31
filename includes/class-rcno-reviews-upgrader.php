<?php

/**
 * Fired during plugin load.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines functions that need to run to update aspects on the plugin.
 *
 * @since      1.12.0
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Upgrader {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.12.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.12.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.12.0
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Call the require upgrade methods here.
	 *
	 * @since 1.12.0
	 * @param Plugin_Upgrader   $upgrader_object
	 * @param array     $options
	 */
	public function rcno_upgrade_function( $upgrader_object, $options ) {

		if ( $options[ 'action' ] === 'update' && $options[ 'type' ] === 'plugin' && ! empty( $options['plugins'] ) ) {
			foreach ( (array) $options['plugins'] as $each_plugin ) {
				if ( $each_plugin === RCNO_PLUGIN_FILE ) {
					$this->add_book_cover_id_to_reviews();
				}
			}
		}
	}

	/**
	 * Adds the book cover attachment ID to the post meta of a review.
	 *
	 * @since 1.12.0
	 * @return bool|null
	 */
	private function add_book_cover_id_to_reviews() {

		$old_options = get_option( 'rcno_reviews_settings', array() );
		if ( isset( $old_options['book_cover_ids_updated'] ) && 1 === $old_options['book_cover_ids_updated'] ) {
			return null;
		}

		$review_ids = get_posts(array(
			'post_type'   => 'rcno_review',
			'fields'          => 'ids',
			'posts_per_page'  => -1
		));

		foreach ( $review_ids as $review_id ) {
			$review = get_post_custom( $review_id );
			if ( ! isset( $review['rcno_reviews_book_cover_id'] ) && '' !== $review['rcno_reviews_book_cover_src'][0] ) {
				$attachment_id = attachment_url_to_postid( $review['rcno_reviews_book_cover_src'][0] );
				update_post_meta( $review_id, 'rcno_reviews_book_cover_id', $attachment_id );
			}
		}

		$new_options = get_option( 'rcno_reviews_settings', array() );
		$new_options['book_cover_ids_updated'] = 1;
		return update_option( 'rcno_reviews_settings', $new_options );
	}
}