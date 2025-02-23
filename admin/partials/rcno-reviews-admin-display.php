<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wzymedia.com
 *
 * @since      1.0.0
 *
 * @var string $active_tab
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin/partials
 * @author     wzyMedia <wzy@outlook.com>
 */

?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?> </h2>

	<?php settings_errors( $this->plugin_name . '-notices' ); ?>

	<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $tabs as $tab_slug => $tab_name ) {

			$tab_url = add_query_arg(
				array(
					'settings-updated' => false,
					'tab'              => $tab_slug,
				)
			);

			$active = $active_tab === $tab_slug ? ' nav-tab-active' : '';

			echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . esc_attr( $active ) . '">';
			echo esc_html( $tab_name );
			echo '</a>';
		}
		?>
	</h2>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder">

			<div id="postbox-container" class="postbox-container">

				<?php do_meta_boxes( 'rcno_reviews_settings_' . $active_tab, 'normal', $active_tab ); ?>

			</div><!-- #postbox-container-->

		</div><!-- #post-body-->

	</div><!-- #poststuff-->
</div><!-- .wrap -->
