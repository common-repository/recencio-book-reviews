<?php

/**
 * Creates the shortcodes used for book reviews.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 */

/**
 * Creates the shortcodes used for book reviews.
 *
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */

include RCNO_PLUGIN_PATH . 'public/shortcodes/class-rcno-book-list-shortcode.php';
include RCNO_PLUGIN_PATH . 'public/shortcodes/class-rcno-review-box-shortcode.php';
include RCNO_PLUGIN_PATH . 'public/shortcodes/class-rcno-score-box-shortcode.php';
include RCNO_PLUGIN_PATH . 'public/shortcodes/class-rcno-purchase-links-shortcode.php';
include RCNO_PLUGIN_PATH . 'public/shortcodes/class-rcno-isotope-grid-shortcode.php';
include RCNO_PLUGIN_PATH . 'public/shortcodes/class-rcno-grid-shortcode.php';
include RCNO_PLUGIN_PATH . 'public/shortcodes/class-rcno-book-listing-shortcode.php';
include RCNO_PLUGIN_PATH . 'public/shortcodes/class-rcno-table-shortcode.php';
include RCNO_PLUGIN_PATH . 'public/shortcodes/class-rcno-book-details-shortcode.php';

class Rcno_Reviews_Shortcodes {

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
	 * The object contain the book list shortcode object.
	 *
	 * @since    1.8.0
	 * @access   public
	 * @var      Rcno_Book_List_Shortcode $book_list The class instance.
	 */
	public $book_list;

	/**
	 * The object contain the book review box shortcode object.
	 *
	 * @since    1.8.0
	 * @access   public
	 * @var      Rcno_Review_Box_Shortcode $review_box The class instance.
	 */
	public $review_box;

	/**
	 * The object contain the review score shortcode object.
	 *
	 * @since    1.34.0
	 * @access   public
	 * @var      Rcno_Score_Box_Shortcode $score_box The class instance.
	 */
	public $score_box;

	/**
	 * The object contain the purchase links shortcode object.
	 *
	 * @since    1.8.0
	 * @access   public
	 * @var      Rcno_Purchase_Links_Shortcode $purchase_links The class instance.
	 */
	public $purchase_links;

	/**
	 * The object contain the purchase links shortcode object.
	 *
	 * @since    1.12.0
	 * @access   public
	 * @var      Rcno_Isotope_Grid_Shortcode $isotope_grid The class instance.
	 */
	public $isotope_grid;

	/**
	 * The object contain the purchase links shortcode object.
	 *
	 * @since    1.12.0
	 * @access   public
	 * @var      Rcno_Grid_Shortcode $masonry_grid The class instance.
	 */
	public $masonry_grid;

	/**
	 * The object contain the purchase links shortcode object.
	 *
	 * @since    1.12.0
	 * @access   public
	 * @var      Rcno_Book_Listing_Shortcode $book_listing The class instance.
	 */
	public $book_listing;


	/**
	 * The object.
	 *
	 * @since    1.49.0
	 * @access   public
	 * @var      Rcno_Table_Shortcode $book_table The class instance.
	 */
	public $book_table;

	/**
	 * @var \Rcno_Book_Details_Shortcode
	 */
	public $book_details;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->book_list      = new Rcno_Book_List_Shortcode( $plugin_name, $version );
		$this->review_box     = new Rcno_Review_Box_Shortcode( $plugin_name, $version );
		$this->score_box      = new Rcno_Score_Box_Shortcode( $plugin_name, $version );
		$this->purchase_links = new Rcno_Purchase_Links_Shortcode( $plugin_name, $version );
		$this->isotope_grid   = new Rcno_Isotope_Grid_Shortcode( $plugin_name, $version );
		$this->masonry_grid   = new Rcno_Grid_Shortcode( $plugin_name, $version );
		$this->book_listing   = new Rcno_Book_Listing_Shortcode( $plugin_name, $version );
		$this->book_table     = new Rcno_Table_Shortcode( $plugin_name, $version );
		$this->book_details   = new Rcno_Book_Details_Shortcode( $plugin_name, $version );
	}

	public function register_shortcode_styles() {

		$this->book_listing->register_styles();
	}

	/**
	 * Render an embedded review by evaluating the rcno-review shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $options The shortcode options.
	 *
	 * @return string
	 */
	public function rcno_do_review_shortcode( $options ) {

		$plugin_public = new Rcno_Reviews_Public( $this->plugin_name, $this->version );

		if ( 'rcno_review' === get_post_type() ) {
			return '<p style="background-color: red; color: white; font-weight: 600; text-align: center;">Unable to display a review shortcode inside a review post</p>';
		}

		/**
		 * Set default values for options not set explicitly.
		 */
		$options = shortcode_atts(
			array(
				'id'      => 'n/a',
				'excerpt' => 0,
				'nodesc'  => 0,
			),
			$options
		);

		/**
		 * Define variable for the post object
		 */
		$review_post = null;


		if ( 'n/a' !== $options['id'] ) {

			// Get random review.
			if ( 'random' === $options['id'] ) {

				$posts = get_posts(
					array(
						'post_type' => 'rcno_review',
						'nopaging'  => true,
					)
				);

				$review_post = $posts[ array_rand( $posts ) ];
			} else {

				// Get post by id.
				$review_post = get_post( (int) $options['id'] );
			}

			if ( null !== $review_post && 'rcno_review' === $review_post->post_type ) {
				$output               = '';
				$review               = get_post_custom( $review_post->ID );
				$GLOBALS['review_id'] = $review_post->ID; // Set review ID for retrieval in embedded reviews.

				if ( 1 === (int) $options['nodesc'] ) {
					// Embed without description.
					$templ  = new Rcno_Template_Tags( 'recencio-book-reviews', '1.0.0' );
					$output = $templ->get_the_rcno_full_book_details( (int) $options['id'] );
				} elseif ( 0 === (int) $options['excerpt'] ) {
					// Embed complete review.
					$output = $plugin_public->rcno_render_review_content( $review_post );
				} elseif ( 1 === (int) $options['excerpt'] ) {
					// Embed excerpt only.
					$output = $plugin_public->rcno_render_review_excerpt( $review_post );
				}
			} else {
				$output = '';
			}

			return do_shortcode( $output );
		}

		return '';
	}


	/**
	 * Do the shortcode 'rcno-taxlist' and render a list of all terms of a given
	 * taxonomy
	 *
	 * @since 1.0.0
	 *
	 * @param  mixed $options The shortcode options.
	 *
	 * @return string
	 */
	public function rcno_do_taxlist_shortcode( $options ) {

		$plugin_public = new Rcno_Reviews_Public( $this->plugin_name, $this->version );

		// Set default values for options not set explicitly.
		$options = shortcode_atts(
			array(
				'headers' => 1,
				'tax'     => 'n/a',
				'count'   => 0,
			),
			$options
		);

		// The actual rendering is done by a special function.
		$output = $plugin_public->rcno_render_taxlist( $options['tax'], $options['headers'], $options['count'] );

		return do_shortcode( $output );
	}


	/**
	 * Do the shortcode 'rcno-index' and render a list of all reviews
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $options The shortcode options.
	 *
	 * @return string
	 */
	public function rcno_do_reviews_index_shortcode( $options ) {

		$plugin_public = new Rcno_Reviews_Public( $this->plugin_name, $this->version );

		// Set default values for options not set explicitly.
		$options = shortcode_atts(
			array(
				'headers'  => 1,
				'category' => null,
				'width'    => 85,
				'height'   => 130,
			),
			$options
		);

		// The actual rendering is done by a special function.
		$output = $plugin_public->rcno_render_review_index( $options );

		return do_shortcode( $output );
	}

	/**
	 * Do the shortcode 'rcno-book-series' and render a list of all books
	 * in a series.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $options The shortcode options.
	 *
	 * @return string
	 */
	public function rcno_do_book_series_shortcode( $options ) {

		// @TODO: To be deprecated
		$template  = new Rcno_Template_Tags( $this->plugin_name, $this->version );
		$review_id = get_the_ID();

		// Set default values for options not set explicitly.
		$options = shortcode_atts(
			array(
				'review_id' => $review_id,
				'taxonomy'  => 'rcno_series',
				'number'    => true,
				'header'    => __( 'Books in this series', 'recencio-book-reviews' ),
			),
			$options
		);

		// The actual rendering is done by a special function.
		$output = $template->get_the_rcno_books_in_series( $options['review_id'], $options['taxonomy'], $options['number'], $options['header'] );

		return do_shortcode( $output );
	}

	/**
	 * *********************** SHORTCODE FOR REVIEW *****************************
	 */

	/**
	 * Add a button for the shortcode dialog above the editor just as "Add Media"
	 *
	 * @param string $editor_id The post editor's ID.
	 *
	 * @return void
	 */
	public function rcno_add_review_button_scr( $editor_id = 'content' ) {
		global $post_type;

		if ( ! in_array( $post_type, array( 'page', 'post' ), true ) ) {
			return;
		}

		printf(
			'<a href="#" id="rcno-add-review-button" class="rcno-icon button" data-editor="%s" title="%s">%s</a>',
			esc_attr( $editor_id ),
			esc_attr__( 'Add Review', 'recencio-book-reviews' ),
			esc_html__( 'Add Review', 'recencio-book-reviews' )
		);
	}

	/**
	 * Function to load the modal overlay in the footer
	 *
	 * @global string $post_type
	 *
	 * @return void
	 */
	public function rcno_load_in_admin_footer_scr() {
		global $post_type;

		if ( ! in_array( $post_type, array( 'page', 'post' ), true ) ) {
			return;
		}

		require plugin_dir_path( __DIR__ ) . 'admin/views/rcno-reviews-modal.php';
	}

	/**
	 * Function to load the scripts needed for the ajax part in shortcode dialog
	 *
	 * @global string $post_type
	 *
	 * @param string $hook The post editors admin screen hook.
	 *
	 * @return void
	 */
	public function rcno_load_ajax_scripts_scr( $hook ) {
		global $post_type;

		// Only load on pages where it is necessary.
		if ( ! in_array( $post_type, array( 'page', 'post' ), true ) ) {
			return;
		}

		wp_enqueue_script( 'rcno_ajax_scr', plugin_dir_url( __DIR__ ) . 'admin/js/rcno_ajax_scr.js', array( 'jquery' ), $this->version, true );
		wp_localize_script(
			'rcno_ajax_scr',
			'rcno_vars',
			array(
				'rcno_ajax_nonce' => wp_create_nonce( 'rcno-ajax-nonce' ),
			)
		);
		wp_localize_script(
			'rcno_ajax_scr',
			'rcnoReviewsScL10n',
			array(
				'noTitle' => __( 'No title', 'recencio-book-reviews' ),
				'review'  => __( 'Review', 'recencio-book-reviews' ),
				'save'    => __( 'Insert', 'recencio-book-reviews' ),
				'update'  => __( 'Insert', 'recencio-book-reviews' ),
			)
		);
	}

	/**
	 * Process the data from the shortcode include dialog
	 */
	public function rcno_process_ajax_scr() {
		check_ajax_referer( 'rcno-ajax-nonce', 'rcno_ajax_nonce' );

		$args = array();

		if ( isset( $_POST['search'] ) ) {
			$args['s'] = sanitize_text_field( wp_unslash( $_POST['search'] ) );
		} else {
			$args['s'] = '';
		}

		$args['pagenum'] = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

		$query           = array(
			'posts_per_page' => 10,
		);
		$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

		$reviews = get_posts(
			array(
				's'              => $args['s'],
				'post_type'      => 'rcno_review',
				'posts_per_page' => $query['posts_per_page'],
				'offset'         => $query['offset'],
				'orderby'        => 'post_date',
			)
		);

		$json = array();

		foreach ( $reviews as $review ) {
			$json[] = array(
				'id'    => $review->ID,
				'title' => $review->post_title,
			);
		}

		wp_send_json( $json );
	}


	/**
	 * ********************** SHORTCODE FOR REVIEW LISTINGS ****************************
	 */

	/**
	 * Add a button for the shortcode dialog above the editor just as "Add Media"
	 *
	 * @param string $editor_id The post editors ID.
	 *
	 * @return string
	 */
	public function rcno_add_button_scl( $editor_id = 'content' ) {
		global $post_type;
		if ( 'page' !== $post_type ) {
			return;
		}

		printf(
			'<a href="#" id="rcno-add-listings-button" class="rcno-icon button" data-editor="%s" title="%s">%s</a>',
			esc_attr( $editor_id ),
			esc_attr__( 'Add Listing', 'recencio-book-reviews' ),
			esc_html__( 'Add Listing', 'recencio-book-reviews' )
		);
	}

	/**
	 * Function to load the modal overlay in the footer
	 *
	 * @global string $post_type
	 *
	 * @return void
	 */
	public function rcno_load_in_admin_footer_scl() {
		global $post_type;
		if ( 'page' !== $post_type ) {
			return;
		}

		require plugin_dir_path( __DIR__ ) . 'admin/views/rcno-reviews-listings-modal.php';
	}

	/**
	 * Function to load the scripts needed for the ajax part in shortcode dialog
	 *
	 * @global string $post_type
	 *
	 * @param string $hook The post editors admin screen hook.
	 *
	 * @return void
	 */
	public function rcno_load_ajax_scripts_scl( $hook ) {
		global $post_type;

		// Only load on pages where it is necessary.
		if ( 'page' !== $post_type ) {
			return;
		}

		wp_enqueue_script( 'rcno_ajax_scl', plugin_dir_url( __DIR__ ) . 'admin/js/rcno_ajax_scl.js', array( 'jquery' ), $this->version, true );
		wp_localize_script(
			'rcno_ajax_scl',
			'rcno_vars',
			array(
				'rcno_ajax_nonce' => wp_create_nonce( 'rcno-ajax-nonce' ),
			)
		);
		wp_localize_script(
			'rcno_ajax_scl',
			'rcnoListingsScL10n',
			array(
				'noTitle' => __( 'No title', 'recencio-book-reviews' ),
				'review'  => __( 'Review', 'recencio-book-reviews' ),
				'save'    => __( 'Insert', 'recencio-book-reviews' ),
				'update'  => __( 'Insert', 'recencio-book-reviews' ),
			)
		);
	}

	/**
	 * Creates the 'Shortcodes' tab on the review edit screen.
	 *
	 * @since 1.8.0
	 *
	 * @uses  get_current_screen()
	 * @uses  add_help_tab()
	 *
	 * @return void
	 */
	public function rcno_reviews_shortcodes_tab() {

		$screen     = get_current_screen();
		$help_text  = '<h3>' . __( 'Shortcodes Help', 'recencio-book-reviews' ) . '</h3>';
		$help_text .= $this->book_list->rcno_get_help_text();
		$help_text .= $this->review_box->rcno_get_help_text();
		$help_text .= $this->score_box->rcno_get_help_text();
		$help_text .= $this->purchase_links->rcno_get_help_text();
		$help_text .= $this->book_listing->rcno_get_help_text();

		if ( null !== $screen ) {

			// Setup help tab args.
			$args = array(
				'id'      => 'rcno_reviews_shortcodes_help',
				'title'   => __( 'Shortcodes', 'recencio-book-reviews' ),
				'content' => $help_text,
			);

			// Add the help tab.
			$screen->add_help_tab( $args );
		}
	}

}
