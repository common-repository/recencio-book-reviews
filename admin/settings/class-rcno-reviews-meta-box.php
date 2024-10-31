<?php

/**
 * Handles building the setting page
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin/settings
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Meta_Box {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The array of settings tabs
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var    array $options_tabs The array of settings tabs
	 */
	private $options_tabs;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 */
	public function __construct( $plugin_name ) {

		$this->plugin_name  = $plugin_name;
		$this->options_tabs = Rcno_Reviews_Settings_Definition::get_tabs();
	}

	/**
	 * Register the meta boxes on settings page.
	 *
	 * @since    1.0.0
	 */
	public function add_meta_boxes() {

		foreach ( $this->options_tabs as $tab_id => $tab_name ) {

			add_meta_box(
				$tab_id,                            // Meta box ID.
				$tab_name,                          // Meta box Title.
				array( $this, 'render_meta_box' ),  // Callback defining the plugin's innards.
				'rcno_reviews_settings_' . $tab_id, // Screen to which to add the meta box.
				'normal'                            // Context.
			);

		} // end foreach
	}

	/**
	 * Print the meta box on settings page.
	 *
	 * @param string $active_tab The active tab.
	 *
	 * @since     1.0.0
	 */
	public function render_meta_box( $active_tab ) {

		require_once plugin_dir_path( __DIR__ ) . 'partials/meta-box-display.php';
	}
}
