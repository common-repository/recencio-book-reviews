<?php

/**
 * Creates the shortcodes used for book reviews.
 *
 * @link       https://wzymedia.com
 * @since      1.8.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 */

/**
 * Creates the shortcodes used for book reviews.
 *
 *
 * @since      1.8.0
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Book_List_Shortcode {

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
		$this->rcno_set_help_text();
		$this->template = new Rcno_Template_Tags( $plugin_name, $version );
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
	public function rcno_do_book_list_shortcode( $atts ) {

		$review_id = isset( $GLOBALS['review_id'] ) ? (int) $GLOBALS['review_id'] : get_the_ID();

		// Set default values for options not set explicitly.
		$atts = shortcode_atts( array(
			'id'        => $review_id,
			'taxonomy'  => 'series',
			'number'    => true,
			'header'    => __( 'Books in this series', 'recencio-book-reviews' ) . ': ',
		), $atts, 'rcno-book-list' );

		// The actual rendering is done inside the Rcno_Template_Tags class.
		$output = $this->template->get_the_rcno_book_list( $atts['id'], $atts['taxonomy'], $atts['number'], $atts['header'] );

		return do_shortcode( $output );
	}

	/**
	 * Creates the help text for the 'rcno-book-list' shortcode.
	 *
	 * @since 1.8.0
	 * @return void
	 */
	public function rcno_set_help_text() {

		$this->help_text = '<h4>' . __( 'The Book List shortcode', 'recencio-book-reviews' ) . '</h4>';
		$this->help_text .= '<p>';
		$this->help_text .= __( 'This shortcode creates a graphical list of books belonging to a particular custom taxonomy. 
								If the chosen custom taxonomy only has 1 reviewed book, nothing will be displayed when rendering the content of this shortcode on the frontend. 
								',
								'recencio-book-reviews' );
		$this->help_text .= '</p>';

		$this->help_text .= '<code>' . '[rcno-book-list]' . '</code> ' . __('The default shortcode.', 'recencio-book-reviews');
		$this->help_text .= '<p>';

		$this->help_text .= __( 'The shortcode uses a couple of parameters that can be changed or left at the default values. 
								If the default values are being used, then it is not necessary to include them in the shortcode. The default values are listed below: ',
								'recencio-book-reviews' );
		$this->help_text .= '</p>';

		$this->help_text .= '<ul>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . 'id' . '</code> ' . __( 'The post/review ID, defaults to the current review post.', 'recencio-book-reviews' );
		$this->help_text .= '</li>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . 'taxonomy' . '</code> ' . __( 'The custom taxonomy to display the book list for, defaults to the the "series" taxonomy.',
																	'recencio-book-reviews' );
		$this->help_text .= '</li>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . 'number' . '</code> ' . __( 'Whether to display the book series number. Defaults to "yes", use 0 for "no".',
																	'recencio-book-reviews');
		$this->help_text .= '</li>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . 'header' . '</code> ' . __( 'The header text for the book list, defaults to "Books in this series:"',
																	'recencio-book-reviews' );
		$this->help_text .= '</li>';

		$this->help_text .= '</ul>';

		$this->help_text .= '<p>';
		$this->help_text .= __( 'Examples of the book list shortcode:', 'recencio-book-reviews' );
		$this->help_text .= '</p>';

		$this->help_text .= '<ul>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . '[rcno-book-list taxonomy="publisher" number=0 header="Books by this publisher:"]' . '</code> ';
		$this->help_text .= '</li>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . '[rcno-book-list taxonomy="author" header="Other books by this author:"]' . '</code> ';
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
