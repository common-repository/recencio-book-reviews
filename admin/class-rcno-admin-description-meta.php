<?php

/**
 * Saving the general meta information.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Admin_Description_Meta {

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
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $plugin_name     The ID of this plugin.
	 * @param   string $version         The current version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Adds a metabox for the book's description/synopsis
	 *
	 * @since 1.0.0
	 *
	 * @uses  add_meta_meta_box()
	 * @return void
	 */
	public function rcno_book_description_metabox() {
		// Add editor metabox for description.
		add_meta_box(
			'rcno_book_description_metabox',
			__( 'Book Description/Synopsis', 'recencio-book-reviews' ),
			array( $this, 'do_rcno_book_description_metabox' ),
			'rcno_review',
			'normal',
			'high'
		);
	}


	/**
	 * Creates the book description metabox using WordPress wp_editor.
	 *
	 * @since 1.0.0
	 *
	 * @see   wp_editor()
	 *
	 * @param object $review The current post object.
	 *
	 * @return void
	 */
	public function do_rcno_book_description_metabox( $review ) {

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		$description              = get_post_meta( $review->ID, 'rcno_book_description', true );
		$options                  = array(
			'textarea_rows' => 8,
		);
		$options['media_buttons'] = false;
		$options['teeny']         = true;
		$options['quicktags']     = false;
		$options['tinymce']       = ! ( $screen && $screen->is_block_editor );

		wp_editor( $description, 'rcno_book_description', $options );
	}

	/**
	 * Checks the presence of, sanitize then saves the book's description/synopsis data.
	 *
	 * @since 1.0.0
	 *
	 * @uses  update_post_meta()
	 * @uses  sanitize_post_field()
	 *
	 * @param int   $review_id The post ID of the review post.
	 * @param array $data      The data passed from the post custom metabox.
	 * @param mixed $review     The review object this data is being saved to.
	 *
	 * @return void
	 */
	public function rcno_save_book_description_metadata( $review_id, $data, $review = null ) {

		if ( isset( $data['rcno_book_description'] ) ) {

			$book_description = sanitize_post_field( 'rcno_book_description', $data['rcno_book_description'], $review_id );
			update_post_meta( $review_id, 'rcno_book_description', $book_description );
		}
	}

}
