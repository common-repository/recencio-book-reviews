<?php

/**
 *
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 */

/**
 * The get_option functionality of the plugin.
 *
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Option {

	/**
	 * Get an option
	 *
	 * Looks to see if the specified setting exists, returns default if not.
	 *
	 * @since    1.0.0
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return    mixed    $value    Value saved / $default if key if not exist
	 */
	public static function get_option( $key, $default = false ) {

		if ( empty( $key ) ) {
			return $default;
		}

		$plugin_options = get_option( 'rcno_reviews_settings', array() );

		$value = isset( $plugin_options[ $key ] ) ? $plugin_options[ $key ] : $default;

		return $value;
	}

	/**
	 * Update an option
	 *
	 * Updates the specified option.
	 * This is for developers to update options outside the settings page.
	 *
	 * WARNING: Hooks and filters will be triggered!!
	 * @TODO  : Trigger hooks & filters, pull requests welcomed
	 *
	 * @since 1.0.0
	 *
	 * @param $key
	 * @param $value
	 * @return true if the option was saved or false if not
	 */
	public static function update_option( $key, $value ) {

		if ( empty( $key ) ) {
			return false;
		}

		// Load the options.
		$plugin_options = get_option( 'rcno_reviews_settings', array() );

		// Update the specified value in the array.
		$plugin_options[ $key ] = $value;

		// Flush rewrite rules on changing options.
		flush_rewrite_rules();

		// Save the options back to the DB.
		return update_option( 'rcno_reviews_settings', $plugin_options );
	}

	/**
	 * Delete an option
	 *
	 * Deletes the specified option.
	 * This is for developers to delete options outside the settings page.
	 *
	 * WARNING: Hooks and filters will be triggered!!
	 * @TODO  : Trigger hooks & filters, pull requests welcomed
	 *
	 * @since 1.0.0
	 *
	 * @param $key
	 * @return true if the option was deleted or false if not
	 */
	public static function delete_option( $key ) {

		if ( empty( $key ) ) {
			return false;
		}

		// Load the options.
		$plugin_options = get_option( 'rcno_reviews_settings', array() );

		// Delete the specified key.
		unset( $plugin_options[ $key ] );

		// Flush rewrite rules on changing options.
		flush_rewrite_rules();

		// Save the options back to the DB.
		return update_option( 'rcno_reviews_settings', $plugin_options );
	}

	public static function delete_all_options() {

		// Delete all the options.
		delete_option( 'rcno_reviews_settings' );
	}

}
