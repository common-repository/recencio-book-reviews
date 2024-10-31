<?php
/**
 * Define the plugin's extension functionality
 *
 *
 * @link       https://wzymedia.com
 * @since      1.14.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 */

/**
 * Define the plugin's extension functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.14.0
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Extensions {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.14.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.14.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.14.0
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->rcno_include_extensions();
	}

	/**
	 * Includes the files needed for our extension classes. Then we instantiate
	 * the class and run the 'add_extension' method via a filter.
	 *
	 * @since 1.19.0
	 * @return void
	 */
	private function rcno_include_extensions() {
		$dirs = array_filter( glob( RCNO_EXT_DIR . '*' ), 'is_dir' );
		foreach ( $dirs as $ext ) {
			$file = $ext . DIRECTORY_SEPARATOR . basename( $ext ) . '.php';
			if ( is_file( $file ) ) {
				include $file;

				// Break folder name into an array on '-', uppercase 1st letter, then combine using '_'.
				$class     = implode( '_', array_map( 'ucfirst', explode( '-', basename( $ext ) ) ) );
				$extension = new $class();
				add_filter( 'rcno_reviews_extensions', array( $extension, 'add_extension' ) );
			}
		}
	}

	/**
	 * Get all the registered extensions through a filter.
	 *
	 * @since 1.14.0
	 * @return array
	 */
	public function rcno_get_extensions() {
		return (array) apply_filters( 'rcno_reviews_extensions', array() );
	}

	/**
	 * Get all activated extensions.
	 *
	 * @since 1.14.0
	 * @return array
	 */
	public function rcno_get_active_extensions() {
		return (array) Rcno_Reviews_Option::get_option( 'rcno_reviews_active_extensions', array() );
	}

	/**
	 * Enable the 'Extensions' menu option on the settings page
	 *
	 * @since 1.14.0
	 * @return void
	 */
	public function rcno_add_extensions_page() {

		add_submenu_page(
			'edit.php?post_type=rcno_review',
			'Recencio ' . __( 'Extensions', 'recencio-book-reviews' ),
			__( 'Extensions', 'recencio-book-reviews' ),
			'manage_options',
			'rcno_extensions',
			array( $this, 'rcno_render_extensions_page' )
		);
	}

	/**
	 * Render the content of the extension page
	 *
	 * @since 1.14.0
	 *
	 * @return void
	 */
	public function rcno_render_extensions_page() {

		// Get all extensions.
		$all_extensions = $this->rcno_get_extensions();

		// Get active extensions.
		$active_extensions = $this->rcno_get_active_extensions();
		?>
		<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<h4><?php esc_html_e( 'All Recencio Review Extensions. Choose which you want to use, then enable it.', 'recencio-book-reviews' ); ?></h4>

			<?php if ( 0 === count( $all_extensions ) ) : ?>
				<div class="wp-list-table widefat plugin-install">
					<h2 style="text-align: center; margin: 5em 0 0 0; font-size: 30px">
						<?php esc_html_e( 'No extensions are installed or enabled.', 'recencio-book-reviews' ); ?>
					</h2>
				</div>
			<?php endif; ?>

			<div class="wp-list-table widefat plugin-install">
				<div id="the-list">
					<?php
					if ( $all_extensions ) {
						foreach ( $all_extensions as $slug => $class ) {
							if ( ! class_exists( $class ) ) {
								continue;
							}
							// Instantiate each extension.
							$extension_object = new $class();
							// We will use this object to get the title, description and image of the extension.
							?>
							<div class="plugin-card plugin-card-<?php echo esc_attr( $slug ); ?>">
								<div class="plugin-card-top">
									<div class="name column-name">
										<h3>
											<?php echo esc_html( $extension_object->title ); ?>
											<img src="<?php echo esc_attr( $extension_object->image ); ?>"
												class="plugin-icon"
												alt="<?php echo esc_attr( $extension_object->id ); ?>">
										</h3>
									</div>
									<div class="desc column-description">
										<p><?php echo esc_html( $extension_object->desc ); ?></p>
									</div>
								</div>
								<div class="plugin-card-bottom">
									<?php
									// Use the `buttons` method from our Abstract class to create the buttons
									// Can be overwritten by each integration if needed.
									$extension_object->buttons( $active_extensions );
									?>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
		</div>
		<?php
		do_action( 'rcno_extensions_settings_page_footer' );
	}

	/**
	 * Loads the needed scripts
	 *
	 * @since 1.14.0
	 *
	 * @param string $hook_suffix The current admin page's hook.
	 *
	 * @return mixed
	 */
	public function rcno_extension_admin_scripts( $hook_suffix ) {

		if ( 'rcno_review_page_rcno_extensions' !== $hook_suffix ) {
			return false;
		}

		wp_enqueue_script( 'rcno-micromodal-script', RCNO_PLUGIN_URI . 'admin/js/micromodal.min.js', array(), '0.3.2', true );
		wp_enqueue_script( 'rcno-extensions-admin-script', RCNO_PLUGIN_URI . 'admin/js/rcno-extension-admin.js',
			array( 'jquery' ), $this->version, true );
		wp_localize_script(
			'rcno-extensions-admin-script',
			'rcno_extension_admin',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'rcno-extension-admin-nonce' ),
				'text'     => array(
					'activate'   => __( 'Enable', 'recencio-book-reviews' ),
					'deactivate' => __( 'Disable', 'recencio-book-reviews' ),
				),
			)
		);
		wp_enqueue_style( 'rcno-micromodal-styles', RCNO_PLUGIN_URI . 'admin/css/micromodal.css', array(), $this->version );

		return true;
	}

	/**
	 * Activating the Extension through AJAX
	 *
	 * @return void
	 */
	public function rcno_activate_extension_ajax() {

		// Check if there is a nonce and if it is, verify it. Otherwise throw an error.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'rcno-extension-admin-nonce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Something went wrong!', 'recencio-book-reviews' ) ) );
			die();
		}

		// If we don't have an extension id, don't process any further.
		if ( ! isset( $_POST['extension'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No extension data was sent', 'recencio-book-reviews' ) ) );
			die();
		}
		// The extension to activate.
		$extension         = sanitize_text_field( wp_unslash( $_POST['extension'] ) );
		$active_extensions = $this->rcno_get_active_extensions();
		// If that extension is already active, don't process it further.
		// If the extension is not active yet, let's try to activate it.
		if ( ! isset( $active_extensions[ $extension ] ) ) {
			// Let's get all the registered extensions.
			$extensions = $this->rcno_get_extensions();
			// Check if we have that extensions registered.
			if ( isset( $extensions[ $extension ] ) ) {
				// Put it in the active extensions array.
				$active_extensions[ $extension ] = $extensions[ $extension ];
				// Trigger an action so some plugins can also process some data here.
				do_action( 'rcno_reviews' . $extension . '_extension_activated' );
				// Update the active extensions.
				Rcno_Reviews_Option::update_option( 'rcno_reviews_active_extensions', $active_extensions );
				wp_send_json_success( array( 'message' => __( 'Enabled', 'recencio-book-reviews' ) ) );
				die();
			}
		} else {
			// Our extension is already active.
			wp_send_json_success( array( 'message' => __( 'Already enabled', 'recencio-book-reviews' ) ) );
			die();
		}
		// Extension might not be registered.
		wp_send_json_error( array( 'message' => __( 'Nothing happened', 'recencio-book-reviews' ) ) );
		die();
	}

	/**
	 * Deactivating the Integration through AJAX
	 *
	 * @return void
	 */
	public function rcno_deactivate_extension_ajax() {
		// Check if there is a nonce and if it is, verify it. Otherwise, throw an error.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'rcno-extension-admin-nonce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Something went wrong!', 'recencio-book-reviews' ) ) );
			die();
		}
		// If we don't have an extension id, don't process any further.
		if ( ! isset( $_POST['extension'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No extension data sent', 'recencio-book-reviews' ) ) );
			die();
		}
		// The extension to activate.
		$extension         = sanitize_text_field( wp_unslash( $_POST['extension'] ) );
		$active_extensions = $this->rcno_get_active_extensions();
		// If that extension is already deactivated, don't process it further.
		// If the extension is active, let's try to deactivate it.
		if ( isset( $active_extensions[ $extension ] ) ) {
			// Remove the extension from the active extensions.
			unset( $active_extensions[ $extension ] );
			do_action( 'rcno_reviews' . $extension . '_extension_deactivated' );
			// Update the active extensions.
			Rcno_Reviews_Option::update_option( 'rcno_reviews_active_extensions', $active_extensions );
			wp_send_json_success( array( 'message' => __( 'Disabled', 'recencio-book-reviews' ) ) );
			die();
		}

		wp_send_json_error( array( 'message' => __( 'Not enabled', 'recencio-book-reviews' ) ) );
		die();
	}

	/**
	 * Loads all our active extension
	 *
	 * @since 1.14.0
	 *
	 * @return void
	 */
	public function rcno_load_extensions() {
		$active_extensions = $this->rcno_get_active_extensions();
		if ( $active_extensions ) {
			foreach ( $active_extensions as $slug => $extension ) {
				if ( ! class_exists( $extension ) ) {
					continue;
				}
				$extension = new $extension();
				$extension->load();
			}
		}
	}

}
