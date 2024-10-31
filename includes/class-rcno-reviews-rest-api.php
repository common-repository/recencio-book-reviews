<?php

class Rcno_Reviews_Rest_API {

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
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object $template An instance of the Rcno_Template_Tags class.
	 */
	private $template;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Enables or disables support for reviews in the WordPress REST API.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function rcno_enable_rest_support() {

		// Disables the ISBN metabox displaying on review edit screen.
		if ( ! Rcno_Reviews_Option::get_option( 'rcno_reviews_in_rest', true ) ) {
			return;
		}

		$this->get_template_tags();
		$this->rcno_reviews_rest_support();
		$this->rcno_reviews_taxonomy_rest_support();
	}

	/**
	 * Creates an instance of the Rcno_Template_Tags object and assigns to a class property
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_template_tags() {
		$this->template = new Rcno_Template_Tags( $this->plugin_name, $this->version );
	}


	/**
	 * Add the book review custom post type to the REST API.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function rcno_reviews_rest_support() {
		global $wp_post_types;

		$post_type_name = 'rcno_review';
		if ( isset( $wp_post_types[ $post_type_name ] ) ) {
			$wp_post_types[ $post_type_name ]->show_in_rest          = true;
			$wp_post_types[ $post_type_name ]->rest_base             = 'rcno/' . sanitize_title_with_dashes( $wp_post_types['rcno_review']->labels->singular_name );
			$wp_post_types[ $post_type_name ]->rest_controller_class = 'WP_REST_Posts_Controller';
		}
	}


	/**
	 * Add the book review taxonomies to the REST API.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function rcno_reviews_taxonomy_rest_support() {
		global $wp_taxonomies;

		$taxonomies = explode( ',', Rcno_Reviews_Option::get_option( 'rcno_taxonomy_selection' ) );
		$tax_name   = array();

		foreach ( $taxonomies as $tax ) {
			$tax = sanitize_title_with_dashes( $tax );
			$tax_name[ $tax ] = 'rcno_' . $tax;
		}

		foreach ( $tax_name as $key => $value ) {
			if ( isset( $wp_taxonomies[ $value ] ) ) {
				$wp_taxonomies[ $value ]->show_in_rest          = true;
				$wp_taxonomies[ $value ]->rest_base             = 'rcno/' . $key;
				$wp_taxonomies[ $value ]->rest_controller_class = 'WP_REST_Terms_Controller';
			}
		}
	}


	/**
	 * Registers REST fields for our custom meta fields.
	 *
	 * @since '1.0.0
	 *
	 * @uses 'register_rest_field'
	 *
	 * @return void
	 */
	public function rcno_register_rest_fields() {

		register_rest_field( 'rcno_review', 'book_ISBN', array(
			'get_callback'    => function ( $object ) {
				return get_post_meta( $object['id'], 'rcno_book_isbn', true );
			},
			'update_callback' => 'null',
			'schema'          => null,
		) );

		register_rest_field( 'rcno_review', 'book_ISBN13', array(
			'get_callback'    => function ( $object ) {
				return get_post_meta( $object['id'], 'rcno_book_isbn13', true );
			},
			'update_callback' => 'null',
			'schema'          => null,
		) );

		register_rest_field( 'rcno_review', 'book_ASIN', array(
			'get_callback'    => function ( $object ) {
				return get_post_meta( $object['id'], 'rcno_book_asin', true );
			},
			'update_callback' => 'null',
			'schema'          => null,
		) );

		register_rest_field( 'rcno_review', 'book_title', array(
			'get_callback'    => function ( $object ) {
				return get_post_meta( $object['id'], 'rcno_book_title', true );
			},
			'update_callback' => 'null',
			'schema'          => null,
		) );

		register_rest_field( 'rcno_review', 'book_author', array(
			'get_callback'    => function ( $object ) {
				return wp_strip_all_tags( $this->template->get_the_rcno_taxonomy_terms( $object['id'], 'rcno_author', false, ', ' ) );
			},
			'update_callback' => 'null',
			'schema'          => null,
		) );

		register_rest_field( 'rcno_review', 'book_genre', array(
			'get_callback'    => function ( $object ) {
				return wp_strip_all_tags( $this->template->get_the_rcno_taxonomy_terms( $object['id'], 'rcno_genre', false, ', ' ) );
			},
			'update_callback' => 'null',
			'schema'          => null,
		) );

		register_rest_field( 'rcno_review', 'book_series', array(
			'get_callback'    => function ( $object ) {
				return wp_strip_all_tags( $this->template->get_the_rcno_taxonomy_terms( $object['id'], 'rcno_series', false, ', ' ) );
			},
			'update_callback' => 'null',
			'schema'          => null,
		) );

		register_rest_field( 'rcno_review', 'book_publisher', array(
			'get_callback'    => function ( $object ) {
				return wp_strip_all_tags( $this->template->get_the_rcno_taxonomy_terms( $object['id'], 'rcno_publisher', false, ', ' ) );
			},
			'update_callback' => 'null',
			'schema'          => null,
		) );

		register_rest_field( 'rcno_review', 'book_published_date', array(
			'get_callback'    => function ( $object ) {
				return date( 'c', strtotime( get_post_meta( $object['id'], 'rcno_book_pub_date', true ) ) );
			},
			'update_callback' => 'null',
			'schema'          => null,
		) );

		register_rest_field( 'rcno_review', 'book_page_count', array(
			'get_callback'    => function ( $object ) {
				return get_post_meta( $object['id'], 'rcno_book_page_count', true );
			},
			'update_callback' => 'null',
			'schema'          => null,
		) );

		register_rest_field( 'rcno_review', 'book_cover', array(
			'get_callback'    => function ( $object ) {
				return $this->template->get_the_rcno_book_cover( $object['id'], false );
			},
			'update_callback' => 'null',
			'schema'          => null,
		) );

	}
}
