<?php
/**
 * Provide a meta box view for the settings page
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin/partials
 * @author     wzyMedia <wzy@outlook.com>
 */

/**
 * Meta Box
 *
 * Renders a single meta box.
 *
 * @since       1.0.0
 */
?>

<form action="options.php" method="POST">
	<?php settings_fields( 'rcno_reviews_settings' ); ?>
	<?php do_settings_sections( 'rcno_reviews_settings_' . $active_tab ); ?>
	<?php submit_button(); ?>
    <button class="button rcno-reset-button">Reset Settings</button>
</form>
<br class="clear"/>
