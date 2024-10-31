<?php

if ( ! class_exists( 'Abstract_Rcno_Extension' ) ) {
	return;
}

class Rcno_Social_Media_Sharing extends Abstract_Rcno_Extension {

	public function __construct() {
		$this->id       = 'rcno_social_media_sharing';
		$this->image    = plugin_dir_url( __FILE__ ) . 'assets/images/icon.png';
		$this->title    = __( 'Social Media Sharing', 'recencio-book-reviews' );
		$this->desc     = __( 'Simple, lightweight social sharing buttons for your book reviews. 
		Just select the social networks you would to share to, where the buttons should show up and save your settings.', 'recencio-book-reviews' );
		$this->settings = true;
	}

	/**
	 * The post ID of the current review we are attaching to.
	 *
	 * @return int
	 */
	public function the_review_id() {
		if ( isset( $GLOBALS['review_id'] ) && '' !== $GLOBALS['review_id'] ) {
			return $GLOBALS['review_id'];
		}
		return get_post()->ID;
	}

	/**
	 * All methods that we want to be called by the Rcno_Reviews_Extensions class goes here.
	 */
	public function load() {
		$this->add_filters();
		$this->add_actions();
		$this->add_shortcodes();
		return true;
	}

	/**
	 * Add WordPress filters are called here.
	 */
	private function add_shortcodes() {
		add_shortcode( 'rcno-sharing', array( $this, 'display_shortcode' ) );
	}

	/**
	 * Add WordPress filters are called here.
	 */
	private function add_filters() {

	}

	/**
	 * Add WordPress actions are called here.
	 */
	private function add_actions() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 99 );
		add_action( 'rcno_extensions_settings_page_footer', array( $this, 'render_settings_page' ) );
		add_action( 'template_redirect', array( $this, 'add_share_buttons_to_reviews' ) );
	}

	/**
	 * The share buttons HTML markup we are adding to book reviews
	 */
	public function add_share_buttons_to_reviews() {

		if ( is_home() && ! $this->get_setting( 'display_on_homepage', false ) ) {
			return false;
		}

		if ( 1 === (int) $this->get_setting( 'buttons_position' ) || 3 === (int) $this->get_setting( 'buttons_position' ) ) {
			add_action( 'before_rcno_book_review', function () {
				include __DIR__ . '/includes/share-buttons.php';
			});
		}

		if ( 2 === (int) $this->get_setting( 'buttons_position' ) || 3 === (int) $this->get_setting( 'buttons_position' ) ) {
			add_action( 'after_rcno_book_review', function () {
				include __DIR__ . '/includes/share-buttons.php';
			});
		}

		return true;
	}

	/**
	 * Displays our share button's shortcode
	 *
	 * @return bool|string
	 */
	public function display_shortcode() {
		if ( 4 === (int) $this->get_setting( 'buttons_position' ) ) {
			ob_start();
			include_once __DIR__ . '/includes/share-buttons.php';
			return ob_get_clean();
		}
		return false;
	}

	/**
	 * Frontend assets for the share button
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'share-buttons-styles', plugin_dir_url( __FILE__ ) . 'assets/css/button-styles.css', array(), '1.0.0' );
	}

	/**
	 * WP admin assets for the share button
	 */
	public function enqueue_admin_assets() {
		$screen = get_current_screen();
		if ( null !== $screen && 'rcno_review_page_rcno_extensions' === $screen->id ) {
			wp_enqueue_script( 'share-buttons-scripts',
				plugin_dir_url( __FILE__ ) . 'assets/js/button-scripts.js', array( 'jquery', 'rcno-micromodal-script' ), '1.0.0', true );
		}
	}

	/**
	 * Registers the settings to be stored to the WP Options table.
	 */
	public function register_settings() {
		register_setting( 'rcno-social-media-sharing', 'rcno_social_media_sharing_options', array(
			'sanitize_callback' => array( $this, 'sanitize_settings' ),
		) );
	}

	/**
	 * The hidden markup the is rendered by the Thickbox modal window.
	 */
	public function render_settings_page() {
		include __DIR__ . '/includes/settings-page.php';
	}

	/**
	 * Looks to see if the specified setting exists, returns default if not.
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_setting( $key, $default = '' ) {

		if ( empty( $key ) ) {
			return $default;
		}

		$settings = get_option( 'rcno_social_media_sharing_options', array() );
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Sanitize the settings being saved by this extension.
	 *
	 * @param array $settings The settings array for the extension.
	 *
	 * @return array
	 */
	public function sanitize_settings( array $settings ) {
		foreach ( $settings as $key => $value ) {
			$settings[ $key ] = sanitize_text_field( $value );
		}
		return $settings;
	}

}



