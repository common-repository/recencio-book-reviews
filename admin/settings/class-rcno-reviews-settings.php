<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin/settings
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Settings {

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
	 * The array of plugin settings.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array $registered_settings The array of plugin settings.
	 */
	private $registered_settings;

	/**
	 * The callback helper to render HTML elements for settings forms.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rcno_Reviews_Callback_Helper $callback Render HTML elements.
	 */
	protected $callback;

	/**
	 * The sanitization helper to sanitize and validate settings.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rcno_Reviews_Sanitization_Helper $sanitization Sanitize and validate settings.
	 */
	protected $sanitization;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param    string                           $plugin_name           The name of this plugin.
	 * @param    Rcno_Reviews_Callback_Helper     $settings_callback     The callback helper for rendering HTML markups
	 * @param    Rcno_Reviews_Sanitization_Helper $settings_sanitization The sanitization helper for sanitizing settings
	 */
	public function __construct( $plugin_name, $settings_callback, $settings_sanitization ) {

		$this->plugin_name         = $plugin_name;
		$this->callback            = $settings_callback;
		$this->sanitization        = $settings_sanitization;
		$this->registered_settings = Rcno_Reviews_Settings_Definition::get_settings();
	}

	/**
	 * A work-around to get i18n to work on the settings page
	 *
	 * @since 1.0.0
	 *
	 * @see https://github.com/wphuman/WordPress-Settings-Module/issues/9
	 *
	 * @return void
	 */
	public function set_settings(){
		$this->registered_settings = Rcno_Reviews_Settings_Definition::get_settings();
	}

	/**
	 * Register all settings sections and fields.
	 *
	 * @since    1.0.0
	 * @return    void
	 */
	public function register_settings() {

		if ( false === get_option( 'rcno_reviews_settings' ) ) {
			add_option( 'rcno_reviews_settings', array(), '', 'yes' );
		}

		foreach ( $this->registered_settings as $tab => $settings ) {

			// add_settings_section( $id, $title, $callback, $page )
			add_settings_section(
				'rcno_reviews_settings_' . $tab,
				__return_null(),
				'__return_false',
				'rcno_reviews_settings_' . $tab
			);

			foreach ( (array) $settings as $key => $option ) {

				$_name = isset( $option['name'] ) ? $option['name'] : $key;

				// add_settings_field( $id, $title, $callback, $page, $section, $args )
				add_settings_field(
					'rcno_reviews_settings[' . $key . ']',
					$_name,
					method_exists( $this->callback, $option['type'] . '_callback' ) ? array(
						$this->callback,
						$option['type'] . '_callback'
					) : array( $this->callback, 'missing_callback' ),
					'rcno_reviews_settings_' . $tab,
					'rcno_reviews_settings_' . $tab,
					array(
						'id'            => $key,
						'desc'          => ! empty( $option['desc'] ) ? $option['desc'] : '',
						'name'          => $_name,
						'section'       => $tab,
						'size'          => isset( $option['size'] ) ? $option['size'] : 'regular',
						'options'       => isset( $option['options'] ) ? $option['options'] : '',
						'std'           => isset( $option['std'] ) ? $option['std'] : '',
						'max'           => isset( $option['max'] ) ? $option['max'] : 999999,
						'min'           => isset( $option['min'] ) ? $option['min'] : 0,
						'step'          => isset( $option['step'] ) ? $option['step'] : 1,
						'class'         => $key . ' rcno-table-' . $option['type'],
						'accept'        => isset( $option['accept'] ) ? $option['accept'] : 'image/*',
						'pattern'       => isset( $option['pattern'] ) ? $option['pattern'] : '{2,}',
						'title'         => isset( $option['title'] ) ? $option['title'] : __( 'Please enter 2 or more lower-case characters.', 'recencio-book-reviews' ),
						'singular_desc' => isset( $option['singular_desc'] ) ? $option['singular_desc'] : __( 'The singular form of the label', 'recencio-book-reviews' ),
						'plural_desc'   => isset( $option['plural_desc'] ) ? $option['plural_desc'] : __( 'The plural form of the label', 'recencio-book-reviews' ),
						'singular_std'  => isset( $option['singular_std'] ) ? $option['singular_std'] : '',
						'plural_std'    => isset( $option['plural_std'] ) ? $option['plural_std'] : '',
					)
				);
			} // end foreach

		} // end foreach

		// Creates our settings in the options table
		register_setting( 'rcno_reviews_settings', 'rcno_reviews_settings', array(
			$this->sanitization,
			'settings_sanitize'
		) );

	}

	/**
	 * Adds a flag to the options table used to
	 * check if we need to flush the rewrite rules
	 *
	 * Usually called by the Settings class after a setting that affects the
	 * rewrite rules has been called
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function flush_permalinks_on_update() {
		update_option( 'rcno_flush_rewrite_rules', true, true );
	}
}
