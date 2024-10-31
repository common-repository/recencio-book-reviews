<?php

if ( ! class_exists( 'Abstract_Rcno_Extension' ) ) {
	return;
}

/**
 * Class Rcno_Custom_User_Metadata
 */
class Rcno_Custom_User_Metadata extends Abstract_Rcno_Extension {

	/**
	 * Rcno_Custom_User_Metadata constructor.
	 */
	public function __construct() {
		$this->id       = 'rcno_custom_user_metadata';
		$this->image    = plugin_dir_url( __FILE__ ) . 'assets/images/rcno-custom-user-metadata.png';
		$this->title    = __( 'Custom User Metadata', 'recencio-book-reviews' );
		$this->desc     = __( 'Add your own custom metadata fields to the book details section of a reviewed book. Please remember to enable the new fields on the frontend.', 'recencio-book-reviews' );
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
		$this->add_shortcodes();
		return true;
	}

	/**
	 * Add WordPress shortcodes are called here.
	 */
	private function add_shortcodes() {
		// TODO: Implement a shortcode to display custom meta
		// add_shortcode( 'rcno_custom_meta', array( $this, 'display_shortcode' ) );
	}

	/**
	 * Add WordPress filters are called here.
	 */
	private function add_filters() {
		if ( '' !== $this->get_setting( 'custom_user_metaboxes' ) ) {
			add_filter( 'rcno_all_book_meta_keys', array( $this, 'add_custom_meta_to_settings' ) );
		}
	}

	/**
	 * Add WordPress actions are called here.
	 */
	private function add_actions() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'rcno_extensions_settings_page_footer', array( $this, 'render_settings_page' ) );
		add_action( 'rcno_after_general_info_metabox', array( $this, 'add_custom_metabox_to_general_info' ) );
		add_action( 'rcno_save_admin_general_info_metadata', array( $this, 'save_custom_metabox_to_general_info' ) );
	}

	/**
	 * The share buttons HTML markup we are adding to book reviews
	 */
	public function add_custom_metabox_to_general_info() {

		$screen = get_current_screen();
		if ( 'rcno_review' !== ( null !== $screen ? $screen->id : '' ) ) {
			return false;
		}

		include __DIR__ . '/includes/custom-user-metadata-fields.php';
	}

	/**
	 * Saves the new custom metabox data
	 *
	 * @return void
	 */
	public function save_custom_metabox_to_general_info() {
		$data            = $_POST;
		$custom_metadata = explode( ',', $this->get_setting( 'custom_user_metaboxes' ) );

		foreach ( $custom_metadata as $custom_meta ) {
			$index = 'rcno_' . sanitize_key( $custom_meta ) . '_meta';
			if ( isset( $data[ $index ] ) ) {
				$custom_data = sanitize_text_field( $data[ $index ] );
				update_post_meta( $this->the_review_id(), $index, $custom_data );
			}
		}
	}

	/**
	 * Display our shortcodes
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function display_shortcode( $atts ) {
		$out  = '';
		$key  = $index = 'rcno_' . sanitize_key( $atts['name'] ) . '_meta';
		$data = get_post_meta( $this->the_review_id(), $key, true );
		$atts = shortcode_atts(
			array(
				'name' => '',
				'tag'  => 'p'
			),
			$atts,
			'rcno_custom_meta'
		);

		if ( $atts['name'] ) {
			$out .= '<div class="rcno">';
			$out .= '<' . $atts['tag'] . '>';
			$out .= esc_html( $data ?: 'NO DATA FOUND' );
			$out .= '</' . $atts['tag'] . '>';
			$out .= '</div>';
		}

		return $out;
	}

	/**
	 * Registers the settings to be stored to the WP Options table.
	 */
	public function register_settings() {
		register_setting( 'rcno-custom-user-metadata', 'rcno_custom_user_metadata_options', array(
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
	 * @param array $meta   All the book's custom metadata
	 *
	 * @return array
	 */
	public function add_custom_meta_to_settings( $meta ) {

		$custom_metadata = explode( ',', $this->get_setting( 'custom_user_metaboxes' ) );

		foreach ( $custom_metadata as $custom_meta ) {
			$index = 'rcno_' . sanitize_key( $custom_meta ) . '_meta';
			$meta[$index] = $custom_meta;
		}

		return $meta;
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

		$settings = get_option( 'rcno_custom_user_metadata_options', array() );
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

