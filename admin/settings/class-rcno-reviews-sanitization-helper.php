<?php

/**
 * Rcno_Reviews Sanitization Helper Class
 *
 * The callback functions of the settings page
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin/settings
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Sanitization_Helper {

	/**
	 * The ID of this plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var    string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The array of plugin settings.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var    array $registered_settings The array of plugin settings.
	 */
	private $registered_settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var    string $plugin_name The name of this plugin.
	 * @var    string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name ) {

		$this->plugin_name = $plugin_name;

		$this->registered_settings = Rcno_Reviews_Settings_Definition::get_settings();

		add_filter( 'rcno_reviews_settings_sanitize_text', array( $this, 'sanitize_text_field' ) );
		add_filter( 'rcno_reviews_settings_sanitize_slug', array( $this, 'sanitize_slug_field' ) );
		add_filter( 'rcno_reviews_settings_sanitize_email', array( $this, 'sanitize_email_field' ) );
		add_filter( 'rcno_reviews_settings_sanitize_checkbox', array( $this, 'sanitize_checkbox_field' ) );
		add_filter( 'rcno_reviews_settings_sanitize_url', array( $this, 'sanitize_url_field' ) );
		add_filter( 'rcno_reviews_settings_sanitize_cssbox', array( $this, 'sanitize_cssbox_field' ) );
		add_filter( 'rcno_reviews_settings_sanitize_labels', array( $this, 'sanitize_labels_field' ) );
	}

	/**
	 * Settings Sanitization
	 *
	 * Adds a settings error (for the updated message)
	 * At some point this will validate input.
	 *
	 * Note: All sanitized settings will be saved.
	 * Thus, no error messages will be produced.
	 *
	 * Filters in order:
	 * - plugin_name_settings_sanitize_<tab_slug>
	 * - plugin_name_settings_sanitize_<type>
	 * - plugin_name_settings_sanitize
	 * - plugin_name_settings_on_change_<tab_slug>
	 * - plugin_name_settings_on_change_<field_key>
	 *
	 * @since    1.0.0
	 *
	 * @param    array $input The value inputted in the field.
	 *
	 * @return    mixed        $input        Sanitizied value
	 */
	public function settings_sanitize( $input = array() ) {

		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		parse_str( $_POST['_wp_http_referer'], $referrer );
		$tab = isset( $referrer['tab'] ) ? $referrer['tab'] : Rcno_Reviews_Settings_Definition::get_default_tab_slug();

		// Tab filter.
		$input = apply_filters( 'rcno_reviews_settings_sanitize_' . $tab, $input );

		// Trigger action hook for general settings update for $tab.
		$this->do_settings_on_change_hook( $input, $tab );

		// Loop through each setting being saved and pass it through a sanitization filter.
		foreach ( $input as $key => $value ) {
			$new_value     = $value; // set value of $value in $new_value.
			$input[ $key ] = $this->apply_type_filter( $input, $tab, $key );
			$input[ $key ] = $this->apply_general_filter( $input, $key );
			$this->do_settings_on_key_change_hook( $key, $new_value );

		}

		add_settings_error( $this->plugin_name . '-notices', $this->plugin_name, __( 'Settings updated.', $this->plugin_name ), 'updated' );

		return $this->get_output( $tab, $input );
	}

	private function apply_type_filter( $input, $tab, $key ) {

		// Get the setting type (checkbox, select, etc).
		$type = isset( $this->registered_settings[ $tab ][ $key ]['type'] ) ? $this->registered_settings[ $tab ][ $key ]['type'] : false;

		if ( false === $type ) {
			return $input[ $key ];
		}

		return apply_filters( 'rcno_reviews_settings_sanitize_' . $type, $input[ $key ], $key );
	}

	private function apply_general_filter( $input, $key ) {

		return apply_filters( 'rcno_reviews_settings_sanitize', $input[ $key ], $key );
	}

	// Key specific on change hook.
	private function do_settings_on_key_change_hook( $key, $new_value ) {

		$old_plugin_settings = get_option( 'rcno_reviews_settings' );
		//checks if value is saved already in $old_plugin_settings.
		if ( isset( $old_plugin_settings[ $key ] ) && $old_plugin_settings[ $key ] !== $new_value ) {

			do_action( 'rcno_reviews_settings_on_change_' . $key, $new_value, $old_plugin_settings[ $key ] );

		}
	}

	// Tab specific on change hook (only if a value has changed).
	private function do_settings_on_change_hook( $new_values, $tab ) {

		$old_plugin_settings = get_option( 'rcno_reviews_settings' );
		$changed             = false;

		foreach ( $new_values as $key => $new_value ) {

			if ( isset( $old_plugin_settings[ $key ] ) && $old_plugin_settings[ $key ] !== $new_value ) {
				$changed = true;
			}
		}

		if ( $changed ) {

			do_action( 'rcno_reviews_settings_on_change_' . $tab, $new_values, $old_plugin_settings );

		}
	}

	private function not_empty_or_zero( $var ) {
		return ( ! empty( $var ) || '0' == $var );
	}

	// Loop through the whitelist and unset any that are empty for the tab being saved.
	private function get_output( $tab, $input ) {

		$old_plugin_settings = get_option( 'rcno_reviews_settings' );

		if ( ! is_array( $old_plugin_settings ) ) {
			$old_plugin_settings = array();
		}

		// Remove empty elements.
		$input = array_filter( $input, array( $this, 'not_empty_or_zero' ) );
		foreach ( $this->registered_settings[ $tab ] as $key => $_value ) {

			if ( ! isset( $input[ $key ] ) ) {
				$this->do_settings_on_key_change_hook( $key, null );
				if ( isset( $old_plugin_settings[ $key ] ) ) {
					unset( $old_plugin_settings[ $key ] );
				}
			}
		}

		// Overwrite the old values with new sanitized ones.
		return array_merge( $old_plugin_settings, $input );
	}

	/**
	 * Sanitize text fields
	 *
	 * @since    1.0.0
	 *
	 * @param    array $input The field value.
	 *
	 * @return    string        $input        Sanitized value
	 */
	public function sanitize_text_field( $input ) {

		return sanitize_text_field( $input );
	}

	/**
	 * Sanitize slug fields
	 *
	 * @since    1.9.0
	 *
	 * @param    array $input The field value.
	 *
	 * @return    string        $input        Sanitized value
	 */
	public function sanitize_slug_field( $input ) {

		return sanitize_title( $input );
	}

	/**
	 * Sanitize email fields
	 *
	 * @since    1.0.0
	 *
	 * @param    array $input The field value.
	 *
	 * @return    string    $sanitizes_email    Sanitizied email, return empty string if not is_email()
	 */
	public function sanitize_email_field( $input ) {

		$sanitizes_email = sanitize_email( $input );

		if ( ! is_email( $sanitizes_email ) ) {
			$sanitizes_email = __return_empty_string();
		}

		return $sanitizes_email;
	}

	/**
	 * Sanitize checkbox fields
	 * From WordPress SEO by Yoast class-wpsep-options.php
	 *
	 * @since    1.0.0
	 *
	 * @param    array $input The field value.
	 *
	 * @return    string        $input        '1' if true, empty string otherwise
	 */
	public function sanitize_checkbox_field( $input ) {

		$true = array(
			'1',
			'true',
			'True',
			'TRUE',
			'y',
			'Y',
			'yes',
			'Yes',
			'YES',
			'on',
			'On',
			'ON',
		);

		// String.
		if ( is_string( $input ) ) {

			$input = trim( $input );

			if ( in_array( $input, $true, true ) ) {
				return '1';
			}
		}

		// Boolean.
		if ( is_bool( $input ) && $input ) {
			return '1';
		}

		// Integer.
		if ( is_int( $input ) && 1 === $input ) {
			return '1';
		}

		// Float.
		if ( is_float( $input ) && ! is_nan( $input ) && (float) 1 === $input ) {
			return '1';
		}

		return __return_empty_string();
	}

	/**
	 * Sanitize a url for saving to the database
	 * Not to be confused with the old native WP function
	 * From WordPress SEO by Yoast class-wpsep-options.php
	 *
	 * @since    1.0.0
	 *
	 * @param    string $input
	 * @param    array  $allowed_protocols
	 *
	 * @return    string        URL that safe to use in database queries, redirects and HTTP requests.
	 */
	public function sanitize_url_field( $input, $allowed_protocols = array( 'http', 'https' ) ) {

		return esc_url_raw( sanitize_text_field( rawurldecode( $input ) ), $allowed_protocols );

	}

	/**
	 * Sanitize label fields
	 *
	 * @since 1.9.0
	 *
	 * @param array $input The field value.
	 *
	 * @return array $input Sanitized value
	 */
	public function sanitize_labels_field( $input ) {

		if ( is_array( $input) ) {
			return array_map( 'sanitize_text_field', $input );
		}

		return $input;
	}

	/**
	 * Sanitize user provided CSS using the CSSTidy library
	 *
	 * @see https://wordpress.stackexchange.com/questions/53970/sanitize-user-entered-css.
	 *
	 * @since    1.0.0
	 *
	 * @param    string $input The user provided option being saved.
	 *
	 * @return    string        Parsed and sanitized CSS string.
	 */
	public function sanitize_cssbox_field( $input ) {

		require_once 'class-rcno-reviews-css-tidy.php';

		$csstidy = new CSSTidy();
		$csstidy->set_cfg( 'remove_bslash', false );
		$csstidy->set_cfg( 'compress_colors', false );
		$csstidy->set_cfg( 'compress_font-weight', false );
		$csstidy->set_cfg( 'discard_invalid_properties', true );
		$csstidy->set_cfg( 'merge_selectors', false );
		$csstidy->set_cfg( 'remove_last_;', false );
		$csstidy->set_cfg( 'css_level', 'CSS3.0' );
		$csstidy->set_cfg( 'template', dirname( __FILE__ ) . '/csstidy/wordpress-standard.tpl' );
		$input = preg_replace( '/\\\\([0-9a-fA-F]{4})/', '\\\\\\\\$1', $input );
		$input = wp_kses_split( $input, array(), array() );
		$csstidy->parse( $input );
		$input = $csstidy->print->plain();

		return $input;
	}

}
