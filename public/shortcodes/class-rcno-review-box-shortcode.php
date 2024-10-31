<?php

/**
 * Creates the shortcodes used for book review box.
 *
 * @link       https://wzymedia.com
 * @since      1.8.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 */

/**
 * Creates the shortcodes used for book review box.
 *
 *
 * @since      1.8.0
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Review_Box_Shortcode {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.8.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.8.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin template tags.
	 *
	 * @since    1.8.0
	 * @access   public
	 * @var      Rcno_Template_Tags $template
	 */
	public $template;

	/**
	 * The help text for the shortcode.
	 *
	 * @since    1.8.0
	 * @access   public
	 * @var      string $help_text
	 */
	public $help_text;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      1.8.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->rcno_set_help_text();

		$this->template = new Rcno_Template_Tags( $this->plugin_name, $this->version );
	}

	/**
	 * Do the shortcode 'rcno-book-list' and render a list of all books
	 * in a custom taxonomy.
	 *
	 * @since 1.8.0
	 *
	 * @param   mixed $atts
	 * @return  string
	 */
	public function rcno_do_review_box_shortcode( $atts ) {

		$review_id = isset( $GLOBALS['review_id'] ) ? (int) $GLOBALS['review_id'] : get_the_ID();

		// Set default values for options not set explicitly.
		$atts = shortcode_atts( array(
			'id'        => $review_id,
		), $atts, 'rcno-review-box' );

		// The actual rendering is done inside the Rcno_Template_Tags class.
		$output = $this->template->get_the_rcno_review_box( $atts['id'] );

		return do_shortcode( $output );
	}

	/**
	 * Creates the help text for the 'rcno-book-list' shortcode.
	 *
	 * @since 1.8.0
	 * @return void
	 */
	public function rcno_set_help_text() {

		$this->help_text = '<h4>' . __( 'The Review Box shortcode', 'recencio-book-reviews' ) . '</h4>';
		$this->help_text .= '<p>';
		$this->help_text .= __( 'This shortcode display the book review score box in reviews or regular posts. This shortcode only needs the ID parameter if it is inserted into a regular post. If the shortcode is used in a regular post, the ID of the review it is attached to is needed.',
								'recencio-book-reviews' );
		$this->help_text .= '</p>';

		$this->help_text .= '<code>' . '[rcno-review-box]' . '</code> ' . __('The default shortcode.', 'recencio-book-reviews');

		$this->help_text .= '<ul>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . 'id' . '</code> ' . __( 'The review ID, defaults to the review post.', 'recencio-book-reviews' );
		$this->help_text .= '</li>';

		$this->help_text .= '</ul>';

		$this->help_text .= '<p>';
		$this->help_text .= __( 'Examples of the review box shortcode', 'recencio-book-reviews' ) . ':';
		$this->help_text .= '</p>';

		$this->help_text .= '<ul>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . '[rcno-review-box id=656]' . '</code> ';
		$this->help_text .= '</li>';

		$this->help_text .= '</ul>';
	}

	/**
	 * Returns the help text for the 'rcno-book-list' shortcode.
	 *
	 * @since 1.8.0
	 * @return string
	 */
	public function rcno_get_help_text() {

		return $this->help_text;
	}
}
