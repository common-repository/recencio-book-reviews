<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wzymedia.com
 * @since             1.0.0
 * @package           Rcno_Reviews
 *
 * @wordpress-plugin
 * Plugin Name:       Recencio Book Reviews
 * Plugin URI:        https://recencio.com
 * Description:       A powerful and very flexible tool to manage your blog’s book review collection. Designed with the book reviewer in mind.
 * Version:           1.66.0
 * Author:            wzy Media
 * Author URI:        https://wzymedia.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       recencio-book-reviews
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined( 'WPINC' ) || exit;

const RCNO_PLUGIN_VER = '1.66.0';
const RCNO_PLUGIN_NAME = 'recencio-book-reviews';

// Define our constants.
define( 'RCNO_PLUGIN_FILE', plugin_basename( __FILE__ ) );
define( 'RCNO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'RCNO_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'RCNO_EXT_DIR', plugin_dir_path( __FILE__ ) . 'extensions/' );
define( 'RCNO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rcno-reviews-activator.php
 */
function activate_rcno_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rcno-reviews-activator.php';
	Rcno_Reviews_Activator::activate();
	Rcno_Reviews_Activator::setup_rcno_settings();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rcno-reviews-deactivator.php
 */
function deactivate_rcno_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rcno-reviews-deactivator.php';
	Rcno_Reviews_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rcno_reviews' );
register_deactivation_hook( __FILE__, 'deactivate_rcno_reviews' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rcno-reviews.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rcno_reviews() {
	$plugin = new Rcno_Reviews();
	$plugin->run();
}

run_rcno_reviews();

/**
 * Add the `Rcno_Template_Tags` class to globals for 3rd party ease of use
 *
 * @since 1.62.0
 *
 * @return \Rcno_Template_Tags
 */
$GLOBALS['RcnoTemplate'] = new Rcno_Template_Tags( RCNO_PLUGIN_NAME, RCNO_PLUGIN_VER );
