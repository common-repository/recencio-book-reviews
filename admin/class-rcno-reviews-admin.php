<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 */



require_once __DIR__ . '/class-rcno-admin-description-meta.php';
require_once __DIR__ . '/class-rcno-admin-isbn.php';
require_once __DIR__ . '/class-rcno-admin-book-cover.php';
require_once __DIR__ . '/class-rcno-admin-general-info.php';
require_once __DIR__ . '/class-rcno-admin-review-score.php';
require_once __DIR__ . '/class-rcno-admin-review-rating.php';
require_once __DIR__ . '/class-rcno-admin-buy-links.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Admin {

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
	 * Instance of the general meta class handling all general information related functions
	 *
	 * @since  1.0.0
	 * @access public
	 * @var Rcno_Admin_Description_Meta $description_meta
	 */
	public $description_meta;

	/**
	 * Instance of the ISBN class handling book ISBN number functions.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var Rcno_Admin_ISBN $book_isbn
	 */
	public $book_isbn;

	/**
	 * Instance of the Book_Cover class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var Rcno_Admin_Book_Cover  $book_cover
	 */
	public $book_cover;

	/**
	 * Instance of the Rcno_Admin_General_Info class handling general book info functions.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    Rcno_Admin_General_Info  $book_general_info
	 */
	public $book_general_info;

	/**
	 * Instance of the Rcno_Admin_Review_Score class handling general book info functions.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    Rcno_Admin_Review_Score  $book_review_score
	 */
	public $book_review_score;

	/**
	 * Instance of the Rcno_Admin_Review_Rating class handling simple 5 star rating functions.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    Rcno_Admin_Review_Rating $book_review_rating
	 */
	public $book_review_rating;

	/**
	 * Instance of the Rcno_Admin_Buy_Links class handling the purchase links.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    Rcno_Admin_Buy_Links $buy_links
	 */
	public $buy_links;

	/**
	 * An array of uncountable words such as sheep.
	 *
	 * @since  1.7.1
	 * @access public
	 * @var    array $uncountable;
	 */
	public $uncountable;

	/**
	 * Check to see if automatic pluralization has been disabled.
	 *
	 * @since  1.9.2
	 * @access public
	 * @var    bool $no_pluralize;
	 */
	public $no_pluralize;

	/**
	 * Initialize the class and set its properties.
	 *
	 * The constructor also imports and initializes the classes related to controlling
	 * the various metaboxes on the review edit screen.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->description_meta   = new Rcno_Admin_Description_Meta( $this->plugin_name, $this->version );
		$this->book_isbn          = new Rcno_Admin_ISBN( $this->plugin_name, $this->version );
		$this->book_cover         = new Rcno_Admin_Book_Cover( $this->version, $this->version );
		$this->book_general_info  = new Rcno_Admin_General_Info( $this->plugin_name, $this->version );
		$this->book_review_score  = new Rcno_Admin_Review_Score( $this->plugin_name, $this->version );
		$this->book_review_rating = new Rcno_Admin_Review_Rating( $this->plugin_name, $this->version );
		$this->buy_links          = new Rcno_Admin_Buy_Links( $this->plugin_name, $this->version );

		$this->uncountable  = explode( ',', Rcno_Reviews_Option::get_option( 'rcno_no_pluralization' ) );
		$this->no_pluralize = Rcno_Reviews_Option::get_option( 'rcno_disable_pluralization', false );
	}

	/**
	 * @param $input
	 *
	 * @return string
	 */
	public function sanitize_string( $input ) {
		$input = strip_tags( $input );
		$input = str_replace( '%', '', $input );

		if ( function_exists( 'mb_strtolower' ) && seems_utf8( $input ) ) {
			$input = mb_strtolower( $input, 'UTF-8' );
		}

		$input = strtolower( $input );
		$input = preg_replace( '/\s+/', '-', $input );
		$input = trim( $input, '-' );

		return $input;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since  1.0.0
	 *
	 * @uses   wp_enqueue_style()
	 * @return void
	 */
	public function enqueue_styles() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $screen && 'rcno_review' === $screen->post_type ) {
			wp_enqueue_style( $this->plugin_name . 'minicolors-css', plugin_dir_url( __FILE__ ) . 'css/minicolors.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-modal', plugin_dir_url( __FILE__ ) . 'css/rcno-reviews-modal.css', $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-selectize', plugin_dir_url( __FILE__ ) . 'css/selectize.default.css', '0.12.4', 'all' );
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rcno-reviews-admin.css', array(), $this->version, 'all' );

		if ( (bool) Rcno_Reviews_Option::get_option( 'rcno_enable_star_rating_box', false ) ) {
			// $star_color = Rcno_Reviews_Option::get_option( 'rcno_star_rating_color', '#CCCCCC' );
			$star_color = '#ffd700'; // This is fixed and not affected by user settings.
			$custom_css = '
				#rcno_book_review_rating_metabox .rcno-rate > input:checked ~ label {
				    color: ' . $star_color . '
				}
				#rcno_book_review_rating_metabox .rcno-rate input[type=radio]:checked + label:before {
				    color: ' . $star_color . '
				}
				#rcno_book_review_rating_metabox .rcno-rate:not(:checked) > label:hover,
				#rcno_book_review_rating_metabox .rcno-rate:not(:checked) > label:hover ~ label {
				    color: ' . $star_color . '
				}
				#rcno_book_review_rating_metabox .rcno-rate > input:checked + label:hover,
				#rcno_book_review_rating_metabox .rcno-rate > input:checked + label:hover ~ label,
				#rcno_book_review_rating_metabox .rcno-rate > input:checked ~ label:hover,
				#rcno_book_review_rating_metabox .rcno-rate > input:checked ~ label:hover ~ label,
				#rcno_book_review_rating_metabox .rcno-rate > label:hover ~ input:checked ~ label {
				    color: ' . $star_color . '
				}
			';
			wp_add_inline_style( $this->plugin_name, $custom_css );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 *
	 * @uses     wp_enqueue_media() For using the builtin media uploader functionality.
	 * @uses     wp_enqueue_style()
	 * @uses     wp_enqueue_script()
	 * @uses     wp_localize_script()
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		global $post;

		$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$review_id = ( null !== $post ) ? $post->ID : '';
		$template  = new Rcno_Template_Tags( $this->plugin_name, $this->version );

		if ( $screen && 'dashboard' === $screen->base ) {
			// Add the media uploader.
			wp_enqueue_media();

			wp_register_script( 'rcno-vuejs', plugin_dir_url( __FILE__ ) . 'js/vue.min.js', array(), '2.5.17', true );
		}

		if ( $screen && 'rcno_review' === $screen->post_type ) {
			// Add the media uploader.
			wp_enqueue_media();

			wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
			wp_enqueue_script( $this->plugin_name . '-minicolors-js', plugin_dir_url( __FILE__ ) . 'js/minicolors.min.js', array( 'jquery' ), '2.2.6', true );
			wp_enqueue_script( 'selectize', plugin_dir_url( __FILE__ ) . 'js/selectize.min.js', array( 'jquery' ), '0.13.5', true );
			wp_enqueue_script( 'xml2json', plugin_dir_url( __FILE__ ) . 'js/xml2json.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'star-rating-svg', plugin_dir_url( __FILE__ ) . 'js/star-rating-svg.js', array( 'jquery' ), '1.2.0', true );
		}

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/rcno-reviews-admin.js',
			( $screen && 'rcno_review' === $screen->post_type ? array( 'jquery', 'selectize', 'xml2json' ) : array( 'jquery' ) ),
			$this->version,
			true
		);

		wp_localize_script(
			$this->plugin_name,
			'my_script_vars',
			array(
				'reviewID'                     => $review_id,
				'ajaxURL'                      => admin_url( 'admin-ajax.php' ),
				'rcno_reset_nonce'             => wp_create_nonce( 'rcno-rest-nonce' ),
				'rcno_settings_download_nonce' => wp_create_nonce( 'rcno-settings-download-nonce' ),
				'rcno_settings_import_nonce'   => wp_create_nonce( 'rcno-settings-import-nonce' ),
				'rcno_gr_remote_get_nonce'     => wp_create_nonce( 'rcno-gr-remote-get-nonce' ),
				'rcno_admin_rating'            => get_post_meta( $review_id, 'rcno_admin_rating', true ),
				'rcno_settings_reset_msg'      => __( 'Your settings have been reset, please reload the page to see them.', 'recencio-book-reviews' ),
				'rcno_book_meta_keys'          => $template->get_rcno_book_meta_keys(),
			)
		);
	}


	/**
	 * Creates the book review custom post type.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 * @uses    register_post_type()
	 *
	 * @return  void
	 */
	public function rcno_review_post_type() {

		$cap_type = 'post';
		$cpt_name = 'rcno_review';
		$plural   = Rcno_Reviews_Option::get_option( 'rcno_review_labels', array( 'singular' => 'Review', 'plural' => 'Reviews' ) )['plural']
			?: __( 'Reviews', 'recencio-book-reviews' );
		$single   = Rcno_Reviews_Option::get_option( 'rcno_review_labels', array( 'singular' => 'Review', 'plural' => 'Reviews' ) )['singular']
			?: __( 'Review', 'recencio-book-reviews' );

		$opts['can_export']           = true;
		$opts['capability_type']      = $cap_type;
		$opts['description']          = '';
		$opts['exclude_from_search']  = false;
		$opts['has_archive']          = $this->sanitize_string(  $plural );
		$opts['hierarchical']         = false;
		$opts['map_meta_cap']         = true;
		$opts['menu_icon']            = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAyMDYuNSAzMTQuNyIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMjA2LjUgMzE0LjciIHhtbDpzcGFjZT0icHJlc2VydmUiPjxnPjxwb2x5Z29uIGZpbGw9IiM4Mjg3OEMiIHBvaW50cz0iMCw4Ny40IDE2Mi41LDMzLjIgMTYyLjUsMTUuNCAiLz48cG9seWdvbiBmaWxsPSIjODI4NzhDIiBwb2ludHM9IjAuNCw4Ny41IDE4Ni4zLDQ4LjggMTg2LjEsMzMuMiAiLz48cG9seWdvbiBmaWxsPSIjODI4NzhDIiBwb2ludHM9IjAuNCw4Ny41IDEzOS41LDE4LjcgMTM5LjUsMCAiLz48Zz48cGF0aCBmaWxsPSIjODI4NzhDIiBkPSJNMC41LDg3Ljd2MjI3bDIwNi0zMS41VjUyLjdMMC41LDg3Ljd6IE0xODUuNSwyNTEuNGwtNDcuMSw2LjZsLTEuMy0wLjhjLTUuOC02LjEtMTMtMTYuNy0yMi0zMy4zYy01LjktMTAuOS0xMC4zLTE4LjEtMTMuMS0yMS41Yy0yLjYtMy4xLTUuMi01LjItNy44LTYuMmMtMS4zLTAuNS00LjYtMS0xMy42LTAuMXY0MS44YzAsNS41LDEuMSw5LjUsMy4zLDExLjZjMi4zLDIuMiw2LjUsMy4zLDEyLjUsMy4zbDMuMiwwdjEwLjRsLTc5LDExdi0xMC42bDMuNS0wLjdjMTAuNy0yLjMsMTUuNS03LjcsMTUuNS0xNy41VjE0NS40YzAtNy4yLTEuNC0xMC4xLTIuNy0xMS4zYy0xLjQtMS4yLTQuNi0yLjgtMTIuOC0zLjJsLTMuNS0wLjF2LTEwLjRsNzUuMS0xMC41YzE5LjEtMi43LDM0LjMtMS42LDQ0LjgsMy44YzExLDUuNywxNi41LDE0LjgsMTYuNSwyNy45YzAsOS4zLTIuOSwxNy45LTguNiwyNS40Yy00LjIsNS42LTkuOCwxMC4zLTE2LjcsMTQuM2MxLjksMS4zLDMuOCwzLDUuNyw1LjFjNC4yLDQuNSwxMC4xLDEzLjQsMTcuOSwyNi45YzcuMywxMi41LDEyLjYsMjAuNSwxNS44LDIzLjVjMi45LDIuNyw2LjYsNC4xLDExLjcsNC4ybDIuNywwLjFWMjUxLjR6Ii8+PHBhdGggZmlsbD0iIzgyODc4QyIgZD0iTTg2LjksMTI1LjdsLTYuNCwwLjl2NTMuOGMwLDAsMi42LTAuNCwzLjgtMC41YzEwLjgtMS41LDE4LjMtNC43LDIzLTkuN2M0LjYtNSw3LTEyLjUsNy0yMi4zQzExNC4yLDEzMC4zLDEwNS41LDEyMy4xLDg2LjksMTI1Ljd6Ii8+PC9nPjwvZz48cG9seWdvbiBmaWxsPSIjOURDMkUwIiBwb2ludHM9IjYyNywxMTMuNCA3ODkuNSw1OS4yIDc4OS41LDQxLjQgIi8+PHBvbHlnb24gZmlsbD0iIzlEQzJFMCIgcG9pbnRzPSI2MjcuNCwxMTMuNSA4MTMuMyw3NC44IDgxMy4xLDU5LjIgIi8+PHBvbHlnb24gZmlsbD0iIzJCNzRBNSIgcG9pbnRzPSI2MjcuNCwxMTMuNSA3NjYuNSw0NC43IDc2Ni41LDI2ICIvPjxnPjxwYXRoIGZpbGw9IiMzMDgyQzYiIGQ9Ik02MjcuNSwxMTMuN3YyMjdsMjA2LTMxLjVWNzguN0w2MjcuNSwxMTMuN3ogTTgxMi41LDI3Ny40bC00Ny4xLDYuNmwtMS4zLTAuOGMtNS44LTYuMS0xMy0xNi43LTIyLTMzLjNjLTUuOS0xMC45LTEwLjMtMTguMS0xMy4xLTIxLjVjLTIuNi0zLjEtNS4yLTUuMi03LjgtNi4yYy0xLjMtMC41LTQuNi0xLTEzLjYtMC4xdjQxLjhjMCw1LjUsMS4xLDkuNSwzLjMsMTEuNmMyLjMsMi4yLDYuNSwzLjMsMTIuNSwzLjNsMy4yLDB2MTAuNGwtNzksMTF2LTEwLjZsMy41LTAuN2MxMC43LTIuMywxNS41LTcuNywxNS41LTE3LjVWMTcxLjRjMC03LjItMS40LTEwLjEtMi43LTExLjNjLTEuNC0xLjItNC42LTIuOC0xMi44LTMuMmwtMy41LTAuMXYtMTAuNGw3NS4xLTEwLjVjMTkuMS0yLjcsMzQuMy0xLjYsNDQuOCwzLjhjMTEsNS43LDE2LjUsMTQuOCwxNi41LDI3LjljMCw5LjMtMi45LDE3LjktOC42LDI1LjRjLTQuMiw1LjYtOS44LDEwLjMtMTYuNywxNC4zYzEuOSwxLjMsMy44LDMsNS43LDUuMWM0LjIsNC41LDEwLjEsMTMuNCwxNy45LDI2LjljNy4zLDEyLjUsMTIuNiwyMC41LDE1LjgsMjMuNWMyLjksMi43LDYuNiw0LjEsMTEuNyw0LjJsMi43LDAuMVYyNzcuNHoiLz48cGF0aCBmaWxsPSIjMzA4MkM2IiBkPSJNNzEzLjksMTUxLjdsLTYuNCwwLjl2NTMuOGMwLDAsMi42LTAuNCwzLjgtMC41YzEwLjgtMS41LDE4LjMtNC43LDIzLTkuN2M0LjYtNSw3LTEyLjUsNy0yMi4zQzc0MS4yLDE1Ni4zLDczMi41LDE0OS4xLDcxMy45LDE1MS43eiIvPjwvZz48L3N2Zz4=';
		$opts['menu_position']        = 5;
		$opts['public']               = true;
		$opts['publicly_querable']    = true;
		$opts['query_var']            = true;
		$opts['register_meta_box_cb'] = '';
		$opts['show_in_admin_bar']    = true;
		$opts['show_in_menu']         = true;
		$opts['show_in_nav_menu']     = true;
		$opts['show_ui']              = true;

		$opts['show_in_rest']          = true;
		$opts['rest_base']             = 'rcno/' . strtolower( $plural );
		$opts['rest_controller_class'] = 'WP_REST_Posts_Controller';

		$opts['supports'] = array(
			'title',
			'editor',
			'thumbnail',
			'excerpt',
			'featured',
			'author',
			'comments',
			'revisions',
			'publicize'
		);

		$opts['taxonomies'] = Rcno_Reviews_Option::get_option( 'rcno_enable_builtin_taxonomy' ) ? array(
			'category',
			'post_tag',
		) : array();

		$opts['capabilities']['delete_others_posts']    = "delete_others_{$cap_type}s";
		$opts['capabilities']['delete_post']            = "delete_{$cap_type}";
		$opts['capabilities']['delete_posts']           = "delete_{$cap_type}s";
		$opts['capabilities']['delete_private_posts']   = "delete_private_{$cap_type}s";
		$opts['capabilities']['delete_published_posts'] = "delete_published_{$cap_type}s";
		$opts['capabilities']['edit_others_posts']      = "edit_others_{$cap_type}s";
		$opts['capabilities']['edit_post']              = "edit_{$cap_type}";
		$opts['capabilities']['edit_posts']             = "edit_{$cap_type}s";
		$opts['capabilities']['edit_private_posts']     = "edit_private_{$cap_type}s";
		$opts['capabilities']['edit_published_posts']   = "edit_published_{$cap_type}s";
		$opts['capabilities']['publish_posts']          = "publish_{$cap_type}s";
		$opts['capabilities']['read_post']              = "read_{$cap_type}";
		$opts['capabilities']['read_private_posts']     = "read_private_{$cap_type}s";

		$opts['labels']['add_new']            = sprintf( __( 'New %1$s', 'recencio-book-reviews' ), $single );
		$opts['labels']['add_new_item']       = sprintf( __( 'Add New %1$s', 'recencio-book-reviews' ), $single );
		$opts['labels']['all_items']          = sprintf( __( 'All %1$s', 'recencio-book-reviews' ), $plural );
		$opts['labels']['edit_item']          = sprintf( __( 'Edit %1$s', 'recencio-book-reviews' ), $single );
		$opts['labels']['menu_name']          = $plural;
		$opts['labels']['name']               = $plural;
		$opts['labels']['name_admin_bar']     = $single;
		$opts['labels']['new_item']           = sprintf( __( 'New %1$s', 'recencio-book-reviews' ), $single );
		$opts['labels']['not_found']          = sprintf( __( 'No %1$s Found', 'recencio-book-reviews' ), $plural );
		$opts['labels']['not_found_in_trash'] = sprintf( __( 'No %1$s Found in Trash', 'recencio-book-reviews' ), $plural );
		$opts['labels']['parent_item_colon']  = sprintf( __( 'Parent %1$s', 'recencio-book-reviews' ), $plural );
		$opts['labels']['search_items']       = sprintf( __( 'Search %1$s', 'recencio-book-reviews' ), $plural );
		$opts['labels']['singular_name']      = $single;
		$opts['labels']['view_item']          = sprintf( __( 'View %1$s', 'recencio-book-reviews' ), $single );

		$opts['rewrite']['ep_mask']    = EP_PERMALINK;
		$opts['rewrite']['feeds']      = true;
		$opts['rewrite']['pages']      = true;
		$opts['rewrite']['slug']       = $this->sanitize_string( $single );
		$opts['rewrite']['with_front'] = false;

		$opts = apply_filters( 'rcno_review_cpt_options', $opts );

		register_post_type( $cpt_name, $opts );
	}

	/**
	 * Creates an array of custom taxonomies.
	 *
	 * @since   1.9.0
	 * @access  public
	 *
	 * @return  array
	 */
	public function rcno_get_custom_taxonomies() {

		$taxonomies        = array();
		$custom_taxonomies = Rcno_Reviews_Option::get_option( 'rcno_taxonomy_selection' );
		$custom_taxonomies = explode( ',', $custom_taxonomies );
		$author            = __( 'Author', 'recencio-book-reviews' );

		if ( ! in_array( $author, $custom_taxonomies, true ) ) {
			// This is book review plugin, the book author taxonomy must always be present.
			$custom_taxonomies = array( $author ) + $custom_taxonomies;
		}

		foreach ( $custom_taxonomies as $key ) {
			$labels['singular'] = Rcno_Pluralize_Helper::singularize( $key );
			$labels['plural']   = Rcno_Pluralize_Helper::pluralize( $key );
			$taxonomies[] = array(
				'tax_settings' => array(
					'settings_key'   => Rcno_Reviews_Option::get_option( 'rcno_' . $this->sanitize_string( $key ) . '_key', $this->sanitize_string( $key ) ),
					'label'          => Rcno_Reviews_Option::get_option( 'rcno_' . $this->sanitize_string( $key ) . '_label', $key ),
					'labels'         => Rcno_Reviews_Option::get_option( 'rcno_' . $this->sanitize_string( $key ) . '_labels', $labels ),
					'slug'           => Rcno_Reviews_Option::get_option( 'rcno_' . $this->sanitize_string( $key ) . '_slug', $this->sanitize_string( $key ) ),
					'hierarchy'      => Rcno_Reviews_Option::get_option( 'rcno_' . $this->sanitize_string( $key ) . '_hierarchical', false ),
					'show_in_table'  => Rcno_Reviews_Option::get_option( 'rcno_' . $this->sanitize_string( $key ) . '_show', false ),
					'show_in_filter' => Rcno_Reviews_Option::get_option( 'rcno_' . $this->sanitize_string( $key ) . '_filter', false ),
					'use_with_posts' => Rcno_Reviews_Option::get_option( 'rcno_' . $this->sanitize_string( $key ) . '_posts', false ),
				),
			);
		}

		return apply_filters( 'rcno_custom_taxonomies', $taxonomies );
	}

	/**
	 * Creates a new course taxonomy for the book review post type.
	 *
	 * We are pluralizing the rewrite slug to prevent clash with builtin author taxonomy
	 * and author custom taxonomy.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @uses    register_taxonomy()
	 *
	 * @return  void
	 */
	public function rcno_custom_taxonomy() {

		$custom_taxonomies = $this->rcno_get_custom_taxonomies();

		foreach ( $custom_taxonomies as $tax ) {

			$plural = ! empty( $tax['tax_settings']['labels']['plural'] )
				? $tax['tax_settings']['labels']['plural']
				: Rcno_Pluralize_Helper::pluralize( implode( ' ', array_map( 'ucfirst', explode( ' ', $tax['tax_settings']['settings_key'] ) ) ) );
			$single = ! empty( $tax['tax_settings']['labels']['singular'] )
				? $tax['tax_settings']['labels']['singular']
				: $tax['tax_settings']['settings_key'];
			$tax_name  = 'rcno_' . implode( '-', explode( ' ', $tax['tax_settings']['settings_key'] ) );
			$cpt_slug  = $this->sanitize_string( Rcno_Reviews_Option::get_option( 'rcno_review_labels', array( 'singular' => 'Review', 'plural' => 'Reviews' ) )['plural']
				?: __( 'Reviews', 'recencio-book-reviews' ) );

			$opts['hierarchical']      = $tax['tax_settings']['hierarchy'];
			$opts['public']            = true;
			$opts['query_var']         = $tax_name;
			$opts['show_admin_column'] = $tax['tax_settings']['show_in_table'];
			$opts['show_in_nav_menus'] = true;
			$opts['show_tag_cloud']    = true;
			$opts['show_ui']           = true;
			$opts['sort']              = '';

			$opts['show_in_rest']          = true;
			$opts['rest_base']             = 'rcno/' . strtolower( $plural );
			$opts['rest_controller_class'] = 'WP_REST_Terms_Controller';

			/**
			 * Note: If you want to ensure that your custom taxonomy behaves like a tag,
			 * you must add the option 'update_count_callback' => '_update_post_term_count'.
			 * Not doing so will result in multiple comma-separated items added at once being saved as a single value,
			 * not as separate values. This can cause undue stress when using get_the_term_list and other term display functions.
			 */
			$opts['update_count_callback'] = $tax['tax_settings']['hierarchy'] ? '_update_post_term_count' : '';

			$opts['capabilities']['assign_terms'] = 'edit_posts';
			$opts['capabilities']['delete_terms'] = 'manage_categories';
			$opts['capabilities']['edit_terms']   = 'manage_categories';
			$opts['capabilities']['manage_terms'] = 'manage_categories';

			$opts['labels']['add_new_item']               = sprintf( __( 'Add New %1$s', 'recencio-book-reviews' ), $single );
			$opts['labels']['add_or_remove_items']        = sprintf( __( 'Add or remove %1$s', 'recencio-book-reviews' ), $plural );
			$opts['labels']['all_items']                  = $plural;
			$opts['labels']['choose_from_most_used']      = sprintf( __( 'Choose from most used %1$s', 'recencio-book-reviews' ), $plural );
			$opts['labels']['edit_item']                  = sprintf( __( 'Edit %1$s', 'recencio-book-reviews' ), $single );
			$opts['labels']['menu_name']                  = $plural;
			$opts['labels']['name']                       = $plural;
			$opts['labels']['new_item_name']              = sprintf( __( 'New %1$s Name', 'recencio-book-reviews' ), $single );
			$opts['labels']['not_found']                  = sprintf( __( 'No %1$s Found', 'recencio-book-reviews' ), $plural );
			$opts['labels']['parent_item']                = sprintf( __( 'Parent %1$s', 'recencio-book-reviews' ), $single );
			$opts['labels']['parent_item_colon']          = sprintf( __( 'Parent %1$s', 'recencio-book-reviews' ), $single );
			$opts['labels']['popular_items']              = sprintf( __( 'Popular %1$s', 'recencio-book-reviews' ), $plural );
			$opts['labels']['search_items']               = sprintf( __( 'Search %1$s', 'recencio-book-reviews' ), $plural );
			$opts['labels']['separate_items_with_commas'] = sprintf( __( 'Separate %1$s with commas', 'recencio-book-reviews' ), $plural );
			$opts['labels']['singular_name']              = $single;
			$opts['labels']['update_item']                = sprintf( __( 'Update %1$s', 'recencio-book-reviews' ), $single );
			$opts['labels']['view_item']                  = sprintf( __( 'View %1$s', 'recencio-book-reviews' ), $single );

			$opts['rewrite']['ep_mask']      = EP_NONE;
			$opts['rewrite']['hierarchical'] = false;
			$opts['rewrite']['slug']         = $cpt_slug . '/' . $this->sanitize_string( $plural );
			$opts['rewrite']['with_front']   = false;

			$opts = apply_filters( 'rcno_review_taxonomy_options', $opts );

			$post_types = $tax['tax_settings']['use_with_posts'] ? ['post', 'rcno_review'] : ['rcno_review'];

			register_taxonomy( $tax_name, apply_filters( 'rcno_taxonomy_posttypes', $post_types, $tax_name ), $opts );
		}
	}

	/**
	 * Creates custom metaboxes to handle the custom taxonomy input fields
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The post object of the current review.
	 * @param array $box The metaboxes attached to the current post object.
	 *
	 * @return void
	 */
	public function rcno_custom_taxonomy_metabox( $post, $box ) {
		include __DIR__ . '/views/rcno-reviews-taxonomies-metabox.php';
	}


	/**
	 * Add the help text found under the "Help" tab on the review edit screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string    $contextual_help
	 * @param int       $screen_id
	 * @param WP_Screen $screen
	 *
	 * @return string
	 */
	public function rcno_add_help_text( $contextual_help, $screen_id, $screen ) {

		if ( 'rcno_review' === $screen->id ) {
			$contextual_help =
				'<p>' . __( 'Things to remember when adding or editing a book review:', 'recencio-book-reviews' ) . '</p>' .
				'<ul>' .
				'<li>' . __( 'Specify the correct genre such as Mystery, or Historic.', 'recencio-book-reviews' ) . '</li>' .
				'<li>' . __( 'Specify the correct writer of the book review. Remember that the Author module refers to you, the author of this book review.', 'recencio-book-reviews' ) . '</li>' .
				'</ul>' .
				'<p>' . __( 'If you want to schedule the book review to be published in the future:', 'recencio-book-reviews' ) . '</p>' .
				'<ul>' .
				'<li>' . __( 'Under the Publish module, click on the Edit link next to Publish.', 'recencio-book-reviews' ) . '</li>' .
				'<li>' . __( 'Change the date to the date to actual publish this article, then click on Ok.', 'recencio-book-reviews' ) . '</li>' .
				'</ul>' .
				'<span><strong>' . __( 'For more information', 'recencio-book-reviews' ) . ': </strong></span>' .
				'<span>' . '<a href="https://wordpress.org/support/plugin/recencio-book-reviews" target="_blank">' . __( 'Support Forums', 'recencio-book-reviews' ) . '</a>' . '</span>';
		} elseif ( 'edit-book' === $screen->id ) {
			$contextual_help =
				'<p>' . __( 'This is the help screen displaying the table of book reviews you have created.', 'recencio-book-reviews' ) . '</p>';
		}

		return $contextual_help;
	}

	/**
	 * Creates the "Help" tab on the review edit screen.
	 *
	 * @since 1.0.0
	 *
	 * @uses  get_current_screen()
	 * @uses  add_help_tab()
	 *
	 * @return void
	 */
	public function rcno_reviews_help_tab() {

		$screen = get_current_screen();

		// Return early if we're not on a book review edit screen.
		if ( $screen && 'rcno_review' === $screen->post_type ) {
			// Setup help tab args.
			$args = array(
				'id'      => 'rcno_reviews_help',
				'title'   => __( 'Reviews Help', 'recencio-book-reviews' ),
				'content' => '<h3>Recencio Book Reviews</h3><p>Help content</p>',
			);

			// Add the help tab.
			$screen->add_help_tab( $args );
		}
	}

	/**
	 * Registers new featured image sizes for the book review post type.
	 *
	 * @since    1.0.0
	 *
	 * @uses     add_image_size()
	 * @uses     add_filter()
	 *
	 * @return  void
	 */
	public function rcno_book_cover_sizes() {

		add_image_size( 'rcno-book-cover-lg', 380, 500, array( 'left', 'top' ) );
		add_image_size( 'rcno-book-cover-sm', 85, 130, array( 'left', 'top' ) );

		add_filter( 'image_size_names_choose', function ( $sizes ) {
			return array_merge( $sizes, array(
				'rcno-book-cover-lg' => __( 'Book Cover LG', 'recencio-book-reviews' ),
				'rcno-book-cover-sm' => __( 'Book Cover SM', 'recencio-book-reviews' ),
			) );
		} );
	}


	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since       1.0.0
	 *
	 * @uses        add_menu_page()
	 * @uses        add_submenu_page()
	 * @uses        remove_menu_page()
	 *
	 * @return      void
	 */
	public function add_plugin_admin_menu() {

		add_menu_page(
			'Recencio ' . __( 'Book Reviews', $this->plugin_name ),
			__( 'Reviews', 'recencio-book-reviews' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' )
		);

		add_submenu_page(
			'edit.php?post_type=rcno_review',
			'Recencio ' . __( 'Book Reviews', $this->plugin_name ),
			__( 'Settings', 'recencio-book-reviews' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' )
		);

		remove_menu_page( $this->plugin_name );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 *
	 * @param array $links The links below the entry on the plugin list field.
	 *
	 * @return array $links
	 */
	public function add_action_links( $links ) {

		$links['settings'] = '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '">' . __( 'Settings',	'recencio-book-reviews' ) . '</a>';

		return $links;
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public function display_plugin_admin_page() {

		$tabs        = Rcno_Reviews_Settings_Definition::get_tabs();
		$default_tab = Rcno_Reviews_Settings_Definition::get_default_tab_slug();
		$active_tab  = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? $_GET['tab'] : $default_tab;

		include_once 'partials/admin-display.php';
	}

	/**
	 * Saves all the data from book review meta boxes
	 *
	 * @since   1.0.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/wp_update_post/#user-contributed-notes
	 *
	 * @uses    wp_is_post_revision()
	 * @uses    remove_action()
	 * @uses    add_action()
	 * @uses    current_user_can()
	 * @uses    update_option()
	 * @uses    wp_update_post()
	 *
	 * @param   int   $review_id post ID of review being saved.
	 * @param   mixed $review    the review post object.
	 *
	 * @return  int|bool
	 */
	public function rcno_save_review( $review_id, $review = null ) {

		if ( ! wp_is_post_revision( $review_id ) ) { // 'save_post' is fired twice, once for revisions, then to save post.

			remove_action( 'save_post', array( $this, 'rcno_save_review' ) );

			$data = $_POST;

			if ( null !== $review && 'rcno_review' === $review->post_type ) {
				$errors = false;

				// Verify if this is an auto save routine. If it is our form has not been submitted, so we don't want to do anything.
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
					return $review_id;
				}

				// Check user permissions.
				if ( ! current_user_can( 'edit_post', $review_id ) ) {
					$errors = 'There was an error saving the review. Insufficient administrator rights.';
				}

				// If we have an error update the error_option and return.
				if ( $errors ) {
					update_option( 'rcno_admin_errors', $errors );

					return $review_id;
				}

				$this->description_meta->rcno_save_book_description_metadata( $review_id, $data, $review );
				$this->book_isbn->rcno_save_book_isbn_metadata( $review_id, $data, $review );
				$this->book_cover->rcno_save_book_cover_metadata( $review_id, $data, $review );
				$this->book_general_info->rcno_save_book_general_info_metadata( $review_id, $data, $review );
				$this->book_review_score->rcno_save_book_review_score_metadata( $review_id, $data, $review );
				$this->book_review_rating->rcno_save_book_review_rating_metadata( $review_id, $data, $review );
				$this->buy_links->rcno_save_book_buy_links_metadata( $review_id, $data, $review );

				wp_update_post( $review );

				add_action( 'save_post', array( $this, 'rcno_save_review' ) );
			}
		}

		return true;
	}

	/**
	 * Function to display any errors in the backend.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function rcno_admin_notice_handler() {
		$screen = get_current_screen();

		if ( $screen && ( 'rcno_review' === $screen->post_type || 'dashboard' === $screen->id ) ) {
			$errors = get_option( 'rcno_admin_errors' );

			if ( $errors ) {
				echo '<div class="error"><p>' . esc_html( $errors ) . '</p></div>';
			}

			// Reset the error option for the next error.
			update_option( 'rcno_admin_errors', false );
		}
	}

	/**
	 * Adds or removes columns from the admin columns.
	 *
	 * The builtin 'author' column is removed to avoid confusion with the
	 * book author of a reviews book.
	 *
	 * @since 1.0.0
	 *
	 * @see   https://stackoverflow.com/a/3354804/3513481
	 * @param array $columns An array of the columns in the admin reviews page.
	 *
	 * @return array
	 */
	public function rcno_add_remove_admin_columns( $columns ) {
		unset( $columns['author'] );

		if ( true ) { // TODO: Add an option to the setting page if requested.
			// Insert the new book cover column after the first column.
			$columns = array_slice( $columns, 0, 1, true)
			           + array( 'book_cover' => __( 'Cover', 'rcno-review' ) )
			           + array_slice( $columns, 1, count( $columns ) - 1, true);
		}

		return $columns;
	}

	/**
	 * Enables the sorting and filtering of the admin columns based on custom taxonomies.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns An array of the columns in the admin reviews page.
	 *
	 * @return array
	 */
	public function rcno_sort_admin_columns( $columns ) {
		$taxonomies = array_diff( get_object_taxonomies( 'rcno_review' ), array( 'category', 'post_tag' ) );

		foreach ( $taxonomies as $taxonomy ) {
			$columns[ 'taxonomy-' . $taxonomy ] = 'taxonomy-' . $taxonomy;
		}

		return $columns;
	}

	/**
	 * Adds the book cover to the admin columns
	 *
	 * @since 1.3.0
	 *
	 * @param array $column_name	The array key and usually the name of the column.
	 * @param array $review_id	    The post ID of each review listed in the admin column.
	 *
	 * @return void
	 */
	public function rcno_add_image_column_content( $column_name, $review_id ) {
		$review        = get_post_custom( $review_id );
		$book_cover    = isset( $review[ 'rcno_reviews_book_cover_src' ][0] ) ? $review[ 'rcno_reviews_book_cover_src' ][0] : '';
		$attachment_id = attachment_url_to_postid( $book_cover );
		$book_src      = wp_get_attachment_image_url( $attachment_id, 'rcno-book-cover-sm' );

		if ( $column_name === 'book_cover' ) {
			if ( $book_src ) {
				echo '<img src="' . $book_src . '" width="50px" />';
			} else {
				echo '<div style="width: 50px; height: 75px; background-color: #f1f1f1"></div>';
			}

		}
	}

	/**
	 * Creates the custom query used to sort admin columns by our custom taxonomies.
	 *
	 * @since 1.0.0
	 *
	 * @param array     $clauses	An array of the SQL statement sent with each WP_Query.
	 * @param WP_Query  $wp_query	The WP WP_Query object.
	 *
	 * @return array
	 */
	public function rcno_sort_admin_columns_by_taxonomy( $clauses, $wp_query ) {

		global $wpdb;

		if ( ! is_post_type_archive( 'rcno_review' ) && ! is_blog_admin() ) {
			return $clauses;
		}

		if ( isset( $wp_query->query['orderby'] )
			 && is_string( $wp_query->query['orderby'])
			 && false !== strpos( $wp_query->query['orderby'], 'taxonomy-rcno_' ) ) {
			$taxonomy = str_replace( 'taxonomy-', '', $wp_query->query['orderby'] );

			$clauses['join']    .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;
			$clauses['where']   .= "AND (taxonomy = '" . $taxonomy . "' OR taxonomy IS NULL)";
			$clauses['groupby'] = 'object_id';
			$clauses['orderby'] = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC)";
			if ( strtoupper( $wp_query->get( 'order' ) ) === 'ASC' ) {
				$clauses['orderby'] .= 'ASC';
			} else {
				$clauses['orderby'] .= 'DESC';
			}
		}

		return $clauses;
	}

	public function rcno_add_taxonomy_to_admin_search_join( $join ) {
		global $pagenow, $wpdb;

		// I want the filter only when performing a search on edit page of Custom Post Type named "rcno_review".
		if ( 'edit.php' === $pagenow  && 'rcno_review' === $_GET['post_type'] && ! empty( $_GET['s'] ) && is_admin() ) {
			$join .= 'LEFT JOIN ' . $wpdb->term_relationships . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->term_relationships . '.post_id ';
		}

		return $join;
	}

	public function rcno_add_taxonomy_to_admin_search_where( $where ) {
		global $pagenow, $wpdb;

		// I want the filter only when performing a search on edit page of Custom Post Type named "rcno_review".
		if ( 'edit.php' === $pagenow && 'rcno_review' === $_GET['post_type'] && ! empty( $_GET['s'] ) && is_admin() ) {
			$where = preg_replace(
				"/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
				"(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->term_relationships . ".term_taxonomy_id LIKE $1)", $where );
		}

		return $where;
	}

	public function rcno_add_taxonomy_to_admin_search_group( $groupby ) {
		global $pagenow, $wpdb;
		if ( is_admin() && $pagenow === 'edit.php' && $_GET['post_type'] === 'rcno_review' && $_GET['s'] !== '' ) {
			$groupby = "$wpdb->posts.ID";
		}

		return $groupby;
	}

	public function rcno_filter_post_type_by_taxonomy() {
		global $typenow;
		$taxonomies = explode( ',', Rcno_Reviews_Option::get_option( 'rcno_taxonomy_selection', array() ) );

		if ( $typenow === 'rcno_review' ) {
			foreach ( $taxonomies as $taxonomy ) {
				$taxonomy = 'rcno_' . $this->sanitize_string( strtolower( $taxonomy ) );
				if ( Rcno_Reviews_Option::get_option( $taxonomy . '_filter' ) ) {
					$selected      = isset( $_GET[ $taxonomy ] ) ? $_GET[ $taxonomy ] : '';
					$info_taxonomy = get_taxonomy( $taxonomy );
					wp_dropdown_categories( array(
						'show_option_all' => sprintf( __( 'All %1$s', 'recencio-book-reviews' ), $info_taxonomy->label ),
						'taxonomy'        => $taxonomy,
						'name'            => $taxonomy,
						'orderby'         => 'name',
						'selected'        => $selected,
						'show_count'      => true,
						'hide_if_empty'   => true,
					) );
				}

			}
		}
	}

	public function rcno_convert_id_to_term_in_query( $query ) {
		global $pagenow;
		$taxonomies = Rcno_Reviews_Option::get_option( 'rcno_taxonomy_selection' );
		$taxonomies = explode( ',', $taxonomies );

		foreach ( $taxonomies as $taxonomy ) {
			$q_vars    = &$query->query_vars;
			$taxonomy = 'rcno_' . $this->sanitize_string( strtolower( $taxonomy ) );
			if ( $pagenow === 'edit.php' && isset( $q_vars['post_type'], $q_vars[ $taxonomy ] )
			     && $q_vars['post_type'] === 'rcno_review'
			     && is_numeric( $q_vars[ $taxonomy ] )
			     && $q_vars[ $taxonomy ] !== 0 ) {
				$term = get_term_by( 'id', $q_vars[ $taxonomy ], $taxonomy );
				if ( $term ) {
					$q_vars[ $taxonomy ] = $term->slug;
				}
			}
		}
	}


	/**
	 * Adds book reviews to the 'Recent Activity' Dashboard widget
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args	An array of the query arguments.
	 *
	 * @return array
	 */
	public function rcno_dashboard_recent_posts_widget( $query_args ) {
		$query_args['post_type'] = 'rcno_review';

		return $query_args;
	}


	/**
	 * Adds book reviews to the 'At a Glance' Dashboard widget
	 *
	 * @since 1.0.0
	 *
	 * @param array $items	The list of items in the widget.
	 *
	 * @return array
	 */
	public function rcno_add_reviews_glance_items( array $items ) {
		$num_reviews = wp_count_posts( 'rcno_review' );

		if ( $num_reviews ) {
			$published = (int) $num_reviews->publish;
			$post_type = get_post_type_object( 'rcno_review' );

			if ( ! $post_type ) {
				return $items;
			}

			$text = $published <= 1 ? $post_type->labels->singular_name : $post_type->labels->name;

			if ( $post_type && current_user_can( $post_type->cap->edit_posts ) ) {
				$items[] = sprintf( '<a class="%1$s-count" href="edit.php?post_type=%1$s">%2$s %3$s</a>', 'rcno_review', $published, $text ) . "\n";
			} else {
				$items[] = sprintf( '<span class="%1$s-count">%2$s</span>', 'rcno_review', $text ) . "\n";
			}
		}

		return $items;
	}

	/**
	 * Book review update messages.
	 *
	 * @since 1.0.0
	 *
	 * @see   /wp-admin/edit-form-advanced.php
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array Amended post update messages with new review update messages.
	 */
	public function rcno_updated_review_messages( $messages ) {
		$review           = get_post();
		$post_type        = get_post_type( $review );
		$post_type_object = get_post_type_object( $post_type );

		$messages['rcno_review'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Review updated.', 'recencio-book-reviews' ),
			2  => __( 'Custom field updated.', 'recencio-book-reviews' ),
			3  => __( 'Custom field deleted.', 'recencio-book-reviews' ),
			4  => __( 'Review updated.', 'recencio-book-reviews' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? __( 'Review restored to revision from ', 'recencio-book-reviews' ) . wp_post_revision_title( (int) $_GET['revision'], false ) : false,
			6  => __( 'Review published.', 'recencio-book-reviews' ),
			7  => __( 'Review saved.', 'recencio-book-reviews' ),
			8  => __( 'Review submitted.', 'recencio-book-reviews' ),
			9  => __( 'Review scheduled for: ', 'recencio-book-reviews' ) . date_i18n( get_option( 'date_format' ), strtotime( $review->post_date ) ),
			10 => __( 'Review draft updated.', 'recencio-book-reviews' ),
		);

		if ( $post_type_object && $post_type_object->publicly_queryable && 'rcno_review' === $post_type ) {
			$permalink = get_permalink( $review->ID );

			$view_link                 = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View review', 'recencio-book-reviews' ) );
			$messages[ $post_type ][1] .= $view_link;
			$messages[ $post_type ][6] .= $view_link;
			$messages[ $post_type ][9] .= $view_link;

			$preview_permalink          = add_query_arg( 'preview', 'true', $permalink );
			$preview_link               = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview review', 'recencio-book-reviews' ) );
			$messages[ $post_type ][8]  .= $preview_link;
			$messages[ $post_type ][10] .= $preview_link;
		}

		return $messages;
	}


	/**
	 * Adds the book reviews post type to AMP, if AMP support is enabled in WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @uses  add_post_type_support()
	 *
	 * @return void
	 */
	public function rcno_add_reviews_cpt_amp() {

		if ( ! defined( 'AMP_QUERY_VAR' ) ) {
			return; // Do not add support if AMP plugin is not detected.
		}

		add_post_type_support( 'rcno_review', AMP_QUERY_VAR );
	}

	/**
	 * Adds the book review CPT to the rewrite rules for date archive.
	 *
	 * @since 1.5.0
	 * @see goo.gl/RYqinL
	 *
	 * @param $wp_rewrite
	 *
	 * @return WP_Rewrite
	 */
	public function rcno_date_archives_rewrite_rules( $wp_rewrite ) {
		$rules             = $this->rcno_generate_date_archives( 'rcno_review', $wp_rewrite );
		$wp_rewrite->rules = array_merge( $rules, $wp_rewrite->rules );

		return $wp_rewrite;
	}

	/**
	 * Generates the rewrite rules for the date archives.
	 *
	 * @since 1.5.0
	 *
	 * @param string $cpt
	 * @param WP_Rewrite $wp_rewrite
	 *
	 * @return array
	 */
	public function rcno_generate_date_archives( $cpt, $wp_rewrite ) {
		$rules = array();

		$post_type    = get_post_type_object( $cpt );

		if ( null === $post_type ) { // If our custom post type is not present fail gracefully.
			return $rules;
		}

		$slug_archive = $post_type->has_archive;
		if ( $slug_archive === false ) {
			return $rules;
		}
		if ( $slug_archive === true ) {
			$slug_archive = $post_type->name;
		}

		$dates = array(
			array(
				'rule' => '([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})',
				'vars' => array( 'year', 'monthnum', 'day' ),
			),
			array(
				'rule' => '([0-9]{4})/([0-9]{1,2})',
				'vars' => array( 'year', 'monthnum' ),
			),
			array(
				'rule' => '([0-9]{4})',
				'vars' => array( 'year' ),
			),
		);

		foreach ( $dates as $data ) {
			$query = 'index.php?post_type=' . $cpt;
			$rule  = Rcno_Pluralize_Helper::pluralize( $slug_archive ) . '/' . $data[ 'rule' ];

			$i = 1;
			foreach ( $data['vars'] as $var ) {
				$query .= '&' . $var . '=' . $wp_rewrite->preg_index( $i );
				$i ++;
			}

			$rules[ $rule . '/?$' ]                               = $query;
			$rules[ $rule . '/feed/(feed|rdf|rss|rss2|atom)/?$' ] = $query . '&feed=' . $wp_rewrite->preg_index( $i );
			$rules[ $rule . '/(feed|rdf|rss|rss2|atom)/?$' ]      = $query . '&feed=' . $wp_rewrite->preg_index( $i );
			$rules[ $rule . '/page/([0-9]{1,})/?$' ]              = $query . '&paged=' . $wp_rewrite->preg_index( $i );
		}

		return $rules;
	}

	/**
	 * Reset plugin option to default settings.
	 *
	 * @since 1.0.0
	 *
	 * @uses  update_option()
	 *
	 * @return void
	 */
	public function reset_all_options() {

		if ( ! wp_verify_nonce( $_POST['reset_nonce'], 'rcno-rest-nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// @TODO: I can refactor this to call the method in the activation class.
		$default_options = array(
			'rcno_review_labels'                    =>
				array(
					'singular' => 'Review',
					'plural'   => 'Reviews',
				),
			'rcno_reviews_on_homepage'              => '1',
			'rcno_reviews_in_rss'                   => '1',
			'rcno_reviews_archive'                  => 'archive_display_excerpt',
			'rcno_taxonomy_selection'               => 'Author,Genre,Series,Publisher',
			'rcno_enable_builtin_taxonomy'          => '1',
			'rcno_author_labels'                    =>
				array(
					'singular' => 'Author',
					'plural'   => 'Authors',
				),
			'rcno_author_show'                      => '1',
			'rcno_author_filter'                    => '1',
			'rcno_genre_labels'                     =>
				array(
					'singular' => 'Genre',
					'plural'   => 'Genres',
				),
			'rcno_genre_show'                       => '1',
			'rcno_genre_filter'                     => '1',
			'rcno_series_labels'                    =>
				array(
					'singular' => 'Series',
					'plural'   => 'Series',
				),
			'rcno_series_show'                      => '1',
			'rcno_series_filter'                    => '1',
			'rcno_publisher_labels'                 =>
				array(
					'singular' => 'Publisher',
					'plural'   => 'Publishers',
				),
			'rcno_publisher_show'                   => '1',
			'rcno_publisher_filter'                 => '1',
			'rcno_show_isbn'                        => '1',
			'rcno_show_isbn13'                      => '1',
			'rcno_show_asin'                        => '1',
			'rcno_show_gr_id'                       => '1',
			'rcno_show_gr_url'                      => '1',
			'rcno_show_illustrator'                 => '1',
			'rcno_show_pub_date'                    => '1',
			'rcno_show_pub_format'                  => '1',
			'rcno_show_pub_edition'                 => '1',
			'rcno_show_series_number'               => '1',
			'rcno_show_page_count'                  => '1',
			'rcno_show_gr_rating'                   => '1',
			'rcno_show_book_cover_url'              => '1',
			'rcno_enable_purchase_links'            => '1',
			'rcno_store_purchase_links_label'       => 'Purchase on:',
			'rcno_store_purchase_links'             => 'Amazon,Barnes & Noble,Kobo',
			'rcno_store_purchase_link_text_color'   => 'rgba(23, 23, 23, 1)',
			'rcno_store_purchase_link_background'   => 'rgba(237, 237, 237, 1)',
			'rcno_enable_star_rating_box'           => '1',
			'rcno_star_rating_color'                => 'rgba(255, 235, 59, 1)',
			'rcno_star_background_color'            => 'rgba(255, 255, 255, 1)',
			'rcno_show_review_score_box'            => '1',
			'rcno_show_review_score_box_background' => 'rgba(240, 240, 240, 1)',
			'rcno_show_review_score_box_accent'     => 'rgba(166, 166, 166, 1)',
			'rcno_show_review_score_box_accent_2'   => 'rgba(59, 59, 59, 1)',
			'rcno_custom_review_score_criteria'     => 'Plot,Characters,World',
			'rcno_enable_comment_ratings'           => '1',
			'rcno_comment_rating_label'             => 'Rate this review',
			'rcno_comment_rating_star_color'        => 'rgba(255, 235, 59, 1)',
			'rcno_show_book_slider_widget'          => '1',
			'rcno_show_book_grid_widget'            => '1',
			'rcno_show_recent_reviews_widget'       => '1',
			'rcno_show_tag_cloud_widget'            => '1',
			'rcno_show_taxonomy_list_widget'        => '1',
			'rcno_show_currently_reading_widget'    => '1',
			'rcno_show_review_calendar_widget'      => '1',
			'rcno_reviews_index_headers'            => '1',
			'rcno_show_book_covers_index'           => '1',
			'rcno_reviews_ignore_articles'          => '1',
			'rcno_reviews_ignored_articles_list'    => 'The,A,An',
			'rcno_reviews_sort_names'               => 'last_name_first_name',
			'rcno_review_template'                  => 'rcno_default',
			'rcno_excerpt_read_more'                => 'Read more',
			'rcno_book_details_meta'                => 'category,post_tag,rcno_author,rcno_genre,rcno_series,rcno_publisher,rcno_book_illustrator,rcno_book_pub_date',
			'rcno_external_book_api'                => 'no-3rd-party',
		);

		// Set the options to the defaults from the '$default_options' array.
		update_option( 'rcno_reviews_settings', $default_options );
		flush_rewrite_rules();
		wp_die();
	}

	/**
	 * Sends the current settings as a JSON array
	 *
	 * @since 1.9.0
	 *
	 * @return void
	 */
	public function rcno_settings_export() {

		if ( empty( $_POST['action'] ) || 'rcno_settings_export' !== $_POST['action'] ) {
			wp_send_json_error( array(
				'message' => 'Invalid post action sent.'
			), 500 );
			return;
		}

		if ( ! wp_verify_nonce( $_POST['settings_download_nonce'], 'rcno-settings-download-nonce' ) ) {
			wp_send_json_error( array(
				'message' => 'Invalid nonce.'
			), 500 );
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => 'Invalid user permissions.'
			), 500 );
			return;
		}

		$settings = get_option( 'rcno_reviews_settings' );
		wp_send_json( $settings );
		wp_die();
	}

	/**
	 * Import plugin settings
	 *
	 * @since 1.9.0
	 *
	 * @return void
	 */
	public function rcno_settings_import() {

		if ( empty( $_POST['action'] ) || 'rcno_settings_import' !== $_POST['action'] ) {
			wp_send_json_error( array(
				'message' => 'Invalid post action sent.'
			), 500 );
			return;
		}

		if ( ! wp_verify_nonce( $_POST['settings_import_nonce'], 'rcno-settings-import-nonce' ) ) {
			wp_send_json_error( array(
				'message' => 'Invalid nonce.'
			), 500 );
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ){
			wp_send_json_error( array(
				'message' => 'Invalid user permissions.'
			), 500 );
			return;
		}

		$settings = stripslashes( $_POST['file_data'] );
		$settings = (array) json_decode( $settings );

		if ( isset( $settings['rcno_settings_version'] ) ) {
			update_option( 'rcno_reviews_settings', $settings );
			wp_send_json_success( array(
				'message' => 'Settings updated.'
			), 200 );
			flush_rewrite_rules();
		} else {
			wp_send_json_error( array(
				'message' => 'The required data was not found.'
			), 500 );
			return;
		}

		wp_die();
	}

	/**
	 * Adds example text site owners can use for their privacy policy text.
	 *
	 * @since 1.16.0
	 *
	 * @return void
	 */
	public function rcno_add_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$content = __( 'When you leave a review on this site, we will store your provided name, email
        address, IP address, review text and review rating to our website. 
        
        Example.com may chose to share your review text, 
        review rating and name with Google for the express purpose of enabling Structured Data 
        markup for our reviews on their search result pages.', 'recencio-book-reviews' );

		wp_add_privacy_policy_content(
			'Recencio Book Reviews',
			wp_kses_post( wpautop( $content, false ) )
		);
	}

	/**
	 * Adds the review score to the information that can be requested in GDPR compliance.
	 *
	 * @since 1.16.0
	 *
	 * @param string    $email_address
	 * @param int       $page
	 *
	 * @return array
	 */
	public function rcno_data_exporter( $email_address, $page = 1 ) {
		$number = 500; // Limit us to avoid timing out
		$page = (int) $page;

		$export_items = array();

		$comments = get_comments(
			array(
				'author_email' => $email_address,
				'number'       => $number,
				'paged'        => $page,
				'order_by'     => 'comment_ID',
				'order'        => 'ASC',
			)
		);

		foreach ( (array) $comments as $comment ) {
			$review_score = get_comment_meta( $comment->comment_ID, 'rcno_review_comment_rating', true );

			// Only add review score data to the export if it has been set.
			if ( 0 !== $review_score ) {
				// Most item IDs should look like postType-postID
				// If you don't have a post, comment or other ID to work with,
				// use a unique value to avoid having this item's export
				// combined in the final report with other items of the same id
				$item_id = "comment-{$comment->comment_ID}";

				// Core group IDs include 'comments', 'posts', etc.
				// But you can add your own group IDs as needed
				$group_id = 'review_score';

				// Optional group label. Core provides these for core groups.
				// If you define your own group, the first exporter to
				// include a label will be used as the group label in the
				// final exported report
				$group_label = __( 'Review Score', 'recencio-book-reviews' );

				// Plugins can add as many items in the item data array as they want
				$data = array(
					array(
						'name' => __( 'Review Score', 'recencio-book-reviews' ),
						'value' => $review_score
					)
				);

				$export_items[] = array(
					'group_id' => $group_id,
					'group_label' => $group_label,
					'item_id' => $item_id,
					'data' => $data,
				);
			}
		}

		// Tell core if we have more comments to work on still
		$done = count( $comments ) < $number;
		return array(
			'data' => $export_items,
			'done' => $done,
		);
	}

	/**
	 * Adds the review score to the information that can be erased in GDPR compliance.
	 *
	 * @since 1.16.0
	 *
	 * @param string    $email_address
	 * @param int       $page
	 *
	 * @return array
	 */
	public function rcno_data_eraser( $email_address, $page = 1 ) {
		$number = 500; // Limit us to avoid timing out
		$page = (int) $page;

		$comments = get_comments(
			array(
				'author_email' => $email_address,
				'number'       => $number,
				'paged'        => $page,
				'order_by'     => 'comment_ID',
				'order'        => 'ASC',
				'include_unapproved' => true
			)
		);

		$items_removed = false;

		foreach ( (array) $comments as $comment ) {
			$review_score  = get_comment_meta( $comment->comment_ID, 'rcno_review_comment_rating', true );

			if ( 0 !== $review_score) {
				update_comment_meta( $comment->comment_ID, 'rcno_review_comment_rating', 0 );
				$items_removed = true;
			}
		}

		// Tell core if we have more comments to work on still
		$done = count( $comments ) < $number;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false, // always false in this example
			'messages'       => array(), // no messages in this example
			'done'           => $done,
		);
	}

	/**
	 * Registers our data exporter.
	 *
	 * @since 1.16.0
	 *
	 * @param array    $exporters
	 *
	 * @return array
	 */
	public function register_rcno_data_exporter( $exporters ) {
		$exporters['recencio-book-reviews'] = array(
			'exporter_friendly_name' => __( 'Recencio Book Reviews Plugin', 'recencio-book-reviews' ),
			'callback' => array( $this, 'rcno_data_exporter' ),
		);
		return $exporters;
	}

	/**
	 * Registers our data eraser.
	 *
	 * @since 1.16.0
	 *
	 * @param array    $erasers
	 *
	 * @return array
	 */
	public function register_rcno_data_eraser( $erasers ) {
		$erasers['recencio-book-reviews'] = array(
			'eraser_friendly_name' => __( 'Recencio Book Reviews Plugin', 'recencio-book-reviews' ),
			'callback'             => array( $this, 'rcno_data_eraser' ),
		);
		return $erasers;
	}

	/**
	 * Adds the `Recencio` page state to posts
	 * containing our shortcodes
	 *
	 * @since 1.0.0
	 *
	 * @param array   $states
	 * @param WP_Post $post
	 *
	 * @return mixed
	 */
	public function rcno_add_page_states( $states, $post ) {

		$rcno_shortcodes = array(
			'rcno-sortable-grid',
			'rcno-reviews-grid',
			'rcno-tax-list',
			'rcno-reviews-index',
			'rcno-reviews'
		);

		foreach ( $rcno_shortcodes as $rcno_shortcode ) {
			if ( has_shortcode( $post->post_content, $rcno_shortcode ) ) {
				$states['recencio'] = 'Recencio'; // No need for i18n as it is a brand name.
			}
		}

		return $states;
	}

	/**
	 * Add option to enable or disable Gutenberg support.
	 *
	 * @since 1.26.0
	 *
	 * @param bool $use_block_editor
	 * @param string $post_type
	 *
	 * @return bool
	 */
	public function rcno_enable_gutenberg_support( $use_block_editor, $post_type ) {

		$enable = Rcno_Reviews_Option::get_option( 'rcno_reviews_in_gutenberg' );

		if ( 'rcno_review' === $post_type ) {
			return $enable;
		}

		return $use_block_editor;
	}

	/**
	 * @return bool|void
	 */
	public function rcno_gr_remote_get( ) {

		check_ajax_referer( 'rcno-gr-remote-get-nonce', 'gr_nonce' );

		if ( empty( $_POST['action'] ) || 'rcno_gr_remote_get' !== $_POST['action'] ) {
			wp_send_json_error( array(
				'message' => 'Invalid post action sent.'
			), 500 );
			return;
		}

		$gr_url = esc_url_raw( $_POST['gr_url'] );
		$data = wp_safe_remote_get( $gr_url, array( 'user-agent' => 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36' ) );

		if ( is_wp_error( $data ) ) {
			wp_send_json_error();
		}

		wp_send_json_success( $data );
	}

	/**
	 * Flush permalinks by checking if updating a setting sets a
	 * `rcno_flush_rewrite_rules` flag to `true` in the `wp_options` table
	 *
	 * @since 1.0.0
	 *
	 * @void
	 */
	public function plugin_settings_update_flush_rewrite() {
		if ( get_option( 'rcno_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			update_option( 'rcno_flush_rewrite_rules', false, true );
		}
	}

	/**
	 * This function runs when WordPress completes its upgrade process
	 * It iterates through each plugin updated to see if ours is included
	 *
	 * @param object $upgrader_object
	 * @param array $options
	 */
	public function rcno_upgrade_completed( $upgrader_object, $options ) {

		// The path to our plugin's main file
		$our_plugin = RCNO_PLUGIN_BASENAME;
		// If an update has taken place and the updated type is plugins and the plugin's element exists
		if ( $options['action'] === 'update' && $options['type'] === 'plugin' && isset( $options['plugins'] ) ) {
			// Iterate through the plugins being updated and check if ours is there
			foreach ( $options['plugins'] as $plugin ) {
				if ( $plugin === $our_plugin ) {
					// Set a transient to record that our plugin has just been updated
					set_transient(
						'rcno_updated',
						array(
							'updated' => true,
							'version' => $upgrader_object->skin->plugin_info['Version']
						)
					);
				}
			}
		}
	}

	/**
	 * Show a notice to anyone who has just updated this plugin
	 * This notice shouldn't display to anyone who has just installed the plugin for the first time
	 */
	public function rcno_display_update_notice() {

		$transient = get_transient( 'rcno_updated' ) ?: null;

		if ( $transient && $transient['updated'] && version_compare( $transient['version'], '1.41.0', '<=' ) ) {
			echo '<div class="notice notice-success">'
				 . __( 'This update has made some changes to how the Recencio Book Reviews plugin handles the generation of public URLs. 
				 Please check the plugin setting page and verify then save the settings on the "General" and "Taxonomy" tabs.', 'recencio-book-reviews' )
				 . '</div>';
		}

		delete_transient( 'rcno_updated' );
	}

    /**
     * Display the review ID in the post action links
     *
     * @param array    $actions
     * @param \WP_Post $review
     *
     * @return array
     */
    public function show_review_id( $actions, $review ) {

        if ( 'rcno_review' === $review->post_type && current_user_can( 'edit_posts' ) ) {
            $actions = array( 'rcno_id' => 'ID: ' .  $review->ID ) + $actions;
        }

        return $actions;
    }

}
