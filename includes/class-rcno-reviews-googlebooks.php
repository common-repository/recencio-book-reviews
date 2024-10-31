<?php

/**
 * Encapsulates the methods related to the Google Books functionality.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 */

/**
 * Encapsulates the methods related to the Google Books functionality.
 *
 * @since      1.0.0
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_GoogleBooks_API {

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
	 * The Google Books API key
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var     string $key Google Books API key stored in the plugin settings table.
	 */
	protected $key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $plugin_name The name of this plugin.
	 * @param   string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->key = Rcno_Reviews_Option::get_option( 'rcno_googlebooks_key', '' );
	}


	/**
	 * Checks if we have enabled and chose Google Books as our external book API.
	 *
	 * @simce 1.0.0
	 *
	 * @param string $hook The global hook variable.
	 *
	 * @return void
	 */
	public function rcno_enqueue_gb_scripts( $hook ) {

		// Disables the enqueuing of the Goodreads script on review edit screen.
		if ( Rcno_Reviews_Option::get_option( 'rcno_enable_googlebooks' )
			&& 'google-books' === Rcno_Reviews_Option::get_option( 'rcno_external_book_api' )
		) {

			global $post;

			if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
				if ( 'rcno_review' === $post->post_type ) {
					wp_enqueue_script( 'googlebooks-script', plugin_dir_url( __FILE__ ) . '../admin/js/rcno-reviews-google-books.js', array( 'jquery' ), $this->version, true );
					wp_localize_script(
						'googlebooks-script',
						'gb_options',
						array(
							'api_key' => $this->key,
						)
					);
				}
			}
		}
	}

}
