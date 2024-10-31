<?php


if ( ! class_exists( 'Abstract_Rcno_Extension' ) ) {
	return;
}

class Rcno_Author_Box extends Abstract_Rcno_Extension {

	public function __construct() {
		$this->id       = 'rcno_author_box';
		$this->image    = plugin_dir_url( __FILE__ ) . 'assets/images/rcno-author-box-icon.png';
		$this->title    = __( 'Author Box', 'recencio-book-reviews' );
		$this->desc     = __( 'Adds a review author box below your published book reviews. Please visit your website\'s user profile page to add your social media profile information.', 'recencio-book-reviews' );
		$this->settings = true;
	}

	/**
	 * The post ID of the current review we are attaching to.
	 *
	 * @return int
	 */
	protected function the_review_id() {

		return ! empty( $GLOBALS['review_id'] ) ? $GLOBALS['review_id'] : get_post()->ID;
	}

	/**
	 * All methods that we want to be called by the Rcno_Reviews_Extensions class goes here.
	 */
	public function load() {
		$this->add_filters();
		$this->add_actions();
		return true;
	}

	/**
	 * Add WordPress filters are called here.
	 */
	private function add_filters() {
		add_filter( 'user_contactmethods', array( $this, 'add_social_media_fields' ) );
	}

	/**
	 * Add WordPress actions are called here.
	 */
	private function add_actions() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'rcno_extensions_settings_page_footer', array( $this, 'render_settings_page' ) );
		add_action( 'template_redirect', array( $this, 'add_author_box_to_reviews' ) );
	}

	/**
	 * The share buttons HTML markup we are adding to book reviews
	 */
	public function add_author_box_to_reviews() {
		if ( ! is_singular( 'rcno_review' ) ) {
			return false;
		}

		add_action( 'after_rcno_book_review', function () {
			include __DIR__ . '/includes/author-box.php';
		});

		return true;
	}

	/**
	 * Frontend assets for the share button
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'rcno-social-media-icons', plugin_dir_url( __FILE__ ) . 'assets/css/rcno-social-media-icons.css',
			array(), '1.0.0' );
		wp_enqueue_style( 'rcno-author-box-styles', plugin_dir_url( __FILE__ ) . 'assets/css/rcno-author-box.css',
			array(), '1.0.0' );
	}

	/**
	 * WP admin assets for the share button
	 */
	public function enqueue_admin_assets() {
		$screen = get_current_screen();
		if ( 'rcno_review_page_rcno_extensions' !== ( null !== $screen ? $screen->id : '' ) ) {
			return false;
		}
		wp_enqueue_script( 'rcno-author-box-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/rcno-author-box-scripts.js',
			array('jquery', 'rcno-micromodal-script'), '1.0.0', true );
	}

	/**
	 * Registers the settings to be stored to the WP Options table.
	 */
	public function register_settings() {
		register_setting( 'rcno-author-box', 'rcno_author_box_options', array(
			'sanitize_callback' => array( $this, 'sanitize_settings' )
		) );
	}

	/**
	 * The hidden markup the is rendered by the Thickbox modal window.
	 */
	public function render_settings_page() {
		include __DIR__ . '/includes/settings-page.php';
	}


	/**
	 * Adds new social media profile fields to edit user screen
	 *
	 * @param array $profile_fields
	 *
	 * @return array
	 */
	public function add_social_media_fields( $profile_fields ) {
		// Add new fields
		$profile_fields['facebook'] = 'Facebook URL';
		$profile_fields['twitter']  = 'Twitter Username';
		$profile_fields['google']   = 'Google+ URL';
		$profile_fields['linkedin'] = 'LinkedIn URL';
		$profile_fields['tumblr']   = 'Tumblr URL';

		return $profile_fields;
	}

	/**
	 * Looks to see if the specified setting exists, returns default if not.
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	protected function get_setting( $key, $default = '' ) {

		if ( empty( $key ) ) {
			return $default;
		}

		$settings = get_option( 'rcno_author_box_options', array() );
		return ! empty( $settings[ $key ] ) ? $settings[ $key ] : $default;
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

