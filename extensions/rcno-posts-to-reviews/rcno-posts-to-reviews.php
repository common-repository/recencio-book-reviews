<?php

if ( ! class_exists( 'Abstract_Rcno_Extension' ) ) {
	return;
}

/**
 * Class Rcno_Posts_To_Reviews
 */
class Rcno_Posts_To_Reviews extends Abstract_Rcno_Extension {

	/**
	 * Rcno_Custom_User_Metadata constructor.
	 */
	public function __construct() {
		$this->id       = 'rcno_posts_to_reviews';
		$this->image    = plugin_dir_url( __FILE__ ) . 'assets/images/rcno-posts-to-reviews.png';
		$this->title    = __( 'Convert Posts to Reviews', 'recencio-book-reviews' );
		$this->desc     = __( 'Converts WordPress posts to Recencio book reviews and Recencio book reviews to WordPress posts.', 'recencio-book-reviews' );
		$this->settings = false;
	}

	/**
	 * All methods that we want to be called by the Rcno_Reviews_Extensions class goes here.
	 */
	public function load() {
		$this->add_filters();
		$this->add_actions();
		return true;
	}

	/**
	 * Add WordPress filters are called here.
	 */
	private function add_filters() {
		add_filter( 'post_row_actions', array( $this, 'add_link' ), 10, 2 );
		add_filter( 'bulk_actions-edit-post', array( $this, 'post_bulk_actions' ) );
		add_filter( 'bulk_actions-edit-rcno_review', array( $this, 'review_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-edit-post', array( $this, 'handle_post_bulk_actions' ), 10, 3 );
		add_filter( 'handle_bulk_actions-edit-rcno_review', array( $this, 'handle_post_bulk_actions' ), 10, 3 );
	}

	/**
	 * Add WordPress actions are called here.
	 */
	private function add_actions() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'bulk_action_notices' ) );
		add_action( 'rcno_extensions_settings_page_footer', array( $this, 'render_settings_page' ) );
		add_action( 'wp_ajax_convert_post_review', array( $this, 'convert_post_review' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'admin_script' ) );
	}

	/**
	 * Adds a link to the WP admin posts table
	 *
	 * @since 1.39.0
	 *
	 * @param array $actions An array of row action links
	 * @param WP_Post $post  The post object
	 *
	 * @return array
	 */
	public function add_link( $actions, $post ) {

		$post_id = $post->ID;
		$nonce   = wp_create_nonce( 'convert-to-review' );

		// Check for the default type.
		// You can check if the current user has some custom rights.
		if ( ( 'post' === $post->post_type ) && current_user_can( 'edit_post', $post->ID ) ) {

			// Add the new 'Convert' quick link.
			$actions = array_merge( $actions, array(
				'convert' => sprintf( '<a href="#" class="convert-to-review" data-post-id="%1$s" data-nonce="%4$s" data-post-type="%5$s" title="%3$s">%2$s</a>',
					$post_id,
					__( 'Convert', 'recencio-book-reviews' ),
					__( 'Convert to Review', 'recencio-book-reviews' ),
					$nonce,
					$post->post_type
				)
			) );
		}

		if ( ( 'rcno_review' === $post->post_type ) && current_user_can( 'edit_post', $post->ID ) ) {

			// Add the new 'Convert' quick link.
			$actions = array_merge( $actions, array(
				'convert' => sprintf( '<a href="#" class="convert-to-post" data-post-id="%1$s" data-nonce="%4$s" data-post-type="%5$s" title="%3$s">%2$s</a>',
					$post_id,
					__( 'Convert', 'recencio-book-reviews' ),
					__( 'Convert to Post', 'recencio-book-reviews' ),
					$nonce,
					$post->post_type
				)
			) );
		}

		return $actions;
	}

	/**
	 * Handles the AJAX request
	 *
	 * @since 1.39.0
	 *
	 * @uses check_admin_referer()
	 * @uses set_post_type()
	 * @uses wp_send_json_success()
	 * @uses wp_send_json_error()
	 *
	 * @return void
	 */
	public function convert_post_review() {

		check_admin_referer( 'convert-to-review', 'nonce' );

		$post_id   = ! empty( $_POST['postID'] ) ? (int) $_POST['postID'] : 0;
		$post_type = ! empty( $_POST['postType'] ) ? sanitize_text_field( $_POST['postType'] ) : null;

		if ( $post_id && 'post' === $post_type ) {
			$result = set_post_type( $post_id, 'rcno_review' );

			if ( $result ) {
				wp_send_json_success();
			}
		}

		if ( $post_id && 'rcno_review' === $post_type ) {
			$result = set_post_type( $post_id, 'post' );

			if ( $result ) {
				wp_send_json_success();
			}
		}

		wp_send_json_error();
	}

	/**
	 * Adds our script on the WP admin pages for posts and rcno_reviews
	 *
	 * @since 1.39.0
	 *
	 * @uses get_current_screen()
	 *
	 * @return void
	 */
	public function admin_script() {

		$screen = get_current_screen();

		if ( $screen && ( 'edit-rcno_review' === $screen->id || 'edit-post' === $screen->id ) ) { ?>
			<script>
			  window.jQuery(document).ready(function($) {

				$('.convert-to-review, .convert-to-post').on('click', function(e) {
				  e.preventDefault();

				  var postID = $(this).data('post-id');
				  var postType = $(this).data('post-type');
				  var nonce = $(this).data('nonce');

				  $.ajax({
					method: 'POST',
					url: window.ajaxurl,
					data: {
					  action: 'convert_post_review',
					  nonce,
					  postID,
					  postType,
					},
				  }).done(function(res) {
					if (res.success === true) {
					  $('tr#post-' + postID).hide();
					}
				  }).fail(function(res) {
					console.log(res);
				  });

				});

			  });
			</script>
		<?php }
	}

	/**
	 * Adds the 'Convert to Review' option in the
	 * bulk actions dropdown on the WP admin posts table
	 *
	 * @since 1.39.0
	 *
	 * @param array $bulk_array A list of items in the bulk actions dropdown
	 *
	 * @return array
	 */
	public function post_bulk_actions( $bulk_array ) {

		$bulk_array['convert_to_review'] = __( 'Convert to Review', 'recencio-book-reviews' );

		return $bulk_array;
	}

	/**
	 * Adds the 'Convert to Post' option in the
	 * bulk actions dropdown on the WP admin posts table
	 *
	 * @since 1.39.0
	 *
	 * @param array $bulk_array A list of items in the bulk actions dropdown
	 *
	 * @return array
	 */
	public function review_bulk_actions( $bulk_array ) {

		$bulk_array['convert_to_post'] = __( 'Convert to Post', 'recencio-book-reviews' );

		return $bulk_array;
	}

	/**
	 * Handles converting or selection of posts to reviews
	 * and vice-versa
	 *
	 * @since 1.39.0
	 *
	 * @uses set_post_type()
	 *
	 * @param string $redirect
	 * @param string $do_action
	 * @param array  $object_ids
	 *
	 * @return string
	 */
	public function handle_post_bulk_actions( $redirect, $do_action, $object_ids ) {

		if ( 'convert_to_review' === $do_action ) {

			foreach ( $object_ids as $post_id ) {
				set_post_type( $post_id, 'rcno_review' );
			}

			$redirect = add_query_arg(
				'converted_to_review',
				count( $object_ids ),
				$redirect
			);
		}

		if ( 'convert_to_post' === $do_action ) {

			foreach ( $object_ids as $post_id ) {
				set_post_type( $post_id, 'post' );
			}

			$redirect = add_query_arg(
				'converted_to_post',
				count( $object_ids ),
				$redirect
			);
		}

		return $redirect;
	}

	/**
	 * Adds a updated message to the WP admin screen
	 *
	 * @since 1.39.0
	 *
	 * @return void
	 */
	public function bulk_action_notices() {

		$out = '';
		$converted_to_reviews = ! empty( $_REQUEST['converted_to_review'] ) ? (int) $_REQUEST['converted_to_review'] : null;
		$converted_to_posts   = ! empty( $_REQUEST['converted_to_post'] ) ? (int) $_REQUEST['converted_to_post'] : null;

		if ( $converted_to_reviews ) {
			$out .= '<div id="message" class="updated fade notice is-dismissible"><p>';
			$out .= sprintf(
				_n( '%s post converted to a review.', '%s posts converted to reviews.', $converted_to_reviews, 'recencio-book-reviews' ),
				number_format_i18n( $converted_to_reviews )
			);
			$out .= '</p></div>';
		}

		if ( $converted_to_posts ) {
			$out .= '<div id="message" class="updated fade notice is-dismissible"><p>';
			$out .= sprintf(
				_n( '%s review converted to a post.', '%s reviews converted to posts.', $converted_to_posts, 'recencio-book-reviews' ),
				number_format_i18n( $converted_to_posts )
			);
			$out .= '</p></div>';
		}

		echo $out;
	}

	/**
	 * Registers the settings to be stored to the WP Options table.
	 */
	public function register_settings() {
		register_setting( 'rcno-posts-to-reviews', 'rcno_posts_to_reviews_options', array(
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
	 * Looks to see if the specified setting exists, returns default if not.
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	protected function get_setting( $key, $default = '' ) {

		if ( empty( $key ) ) {
			return $default;
		}

		$settings = get_option( 'rcno_posts_to_reviews_options', array() );
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

