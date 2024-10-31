<?php
/**
 * Abstract Extension Class from which all others should be done
 */
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

abstract class Abstract_Rcno_Extension {

	/**
	 * Extension ID
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Active Indicator
	 *
	 * @var boolean
	 */
	public $active = false;
	/**
	 * Integration Image
	 *
	 * @var string
	 */
	public $image = '';
	/**
	 * Integration Title
	 *
	 * @var string
	 */
	public $title = '';
	/**
	 * Integration Description
	 *
	 * @var string
	 */
	public $desc = '';

	/**
	 * Integration Settings page
	 *
	 * @var bool
	 */
	public $settings = false;

	/**
	 * Load method used to create hooks to extend or apply new features
	 * This method will be called only on active extensions
	 */
	public function load() {
	}


	/**
	 * Buttons to be shown on the Extensions screen
	 *
	 * @param  array $extension Array of active extension.
	 *
	 * @return void
	 */
	public function buttons( $extension ) {

		$extension = (array) $extension; // There is an edge case where this is an object; we need an array.

		if ( ! isset( $extension[ $this->id ] ) ) { ?>
            <button type="button" data-extension="<?php echo $this->id; ?>"
                    class="button button-primary button-extension-activate">
                <?php _e( 'Enable', 'recencio-book-reviews' ); ?>
            </button>
			<?php if ( $this->settings ) { ?>
                <button type="button" data-extension="<?php echo $this->id; ?>"
                        class="button button-primary <?php echo $this->id; ?>-settings"
                        data-micromodal-trigger="<?php echo $this->id; ?>"
                        style="display: none">
					<?php _e( 'Settings', 'recencio-book-reviews' ); ?>
                </button>
			<?php } ?>
		<?php } else { ?>
            <button type="button" data-extension="<?php echo $this->id; ?>"
                    class="button button-default button-extension-deactivate">
                <?php _e( 'Disable', 'recencio-book-reviews' ); ?>
            </button>
            <?php if ( $this->settings ) { ?>
                <button type="button" data-extension="<?php echo $this->id; ?>"
                        class="button button-primary <?php echo $this->id; ?>-settings"
                        data-micromodal-trigger="<?php echo $this->id; ?>">
                    <?php _e( 'Settings', 'recencio-book-reviews' ); ?>
                </button>
            <?php } ?>
		<?php }	?>

		<?php
	}

	/**
	 * Adds the extension to the list of registered extensions.
	 *
	 * @param array $extensions The currently registered extensions.
	 *
	 * @return array
	 */
	public function add_extension( $extensions ) {
		$extensions[ $this->id ] = get_class( $this );
		return $extensions;
	}
}
