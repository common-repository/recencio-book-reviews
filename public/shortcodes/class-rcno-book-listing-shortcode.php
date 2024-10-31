<?php

/**
 * Creates the shortcodes used for book listing.
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
class Rcno_Book_Listing_Shortcode {

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

	public function register_styles(  ) {

		wp_register_style( 'rcno-book-listing', RCNO_PLUGIN_URI . 'public/css/rcno-book-listing.css', array(), $this->version );
	}

	/**
	 * Do the shortcode 'rcno-book-listing' and render a list of books
	 *
	 * @since 1.8.0
	 *
	 * @param   mixed $atts
	 * @return  string
	 */
	public function rcno_do_book_catalogue_shortcode( $atts ) {

		// Set default values for options not set explicitly.
		$atts = shortcode_atts( array(
			'ids'       => '',
			'wordcount' => 30,
			'heading'    => '',
			'readmore'  => __( 'Read more', 'recencio-book-reviews' ),
		), $atts, 'rcno-book-listing' );

		// The actual rendering is done inside the Rcno_Template_Tags class.
		$output = $this->render_shortcode_content( $atts );

		wp_enqueue_style( 'rcno-book-listing' );

		return do_shortcode( $output );
	}

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public function render_shortcode_content( $atts ) {

		// If we are on not on a post or page, don't display this.
		if ( ! is_singular( array( 'post', 'page' ) ) ) {
			return '<!-- must be single post or page -->';
		}

		$out  = '';
		$ids  = explode( ',', $atts['ids'] );

		$out .= '' !== $atts['heading'] ? sprintf( '<h3>%s</h3>', $atts['heading'] ) : '';

		foreach ( $ids as $id ) {

			$book = get_post( $id );

			if ( null === $book || 'rcno_review' !== $book->post_type ) {
				continue;
			}

			$book_title   = $this->template->get_the_rcno_book_meta( $book->ID, 'rcno_book_title', '', false );
			$book_author  = $this->template->get_the_rcno_taxonomy_terms( $book->ID, 'rcno_author', true, ', ', true );
			$publisher    = $this->template->get_the_rcno_taxonomy_terms( $book->ID, 'rcno_publisher', true, ', ', true );
			$description  = $this->template->get_the_rcno_book_description( $book->ID, $atts['wordcount'], true );

			$out .= '<div class="rcno-book-listing-item">';
			$out .= '<div class="rcno-book-listing-cover">';
			$out .= $this->template->get_the_rcno_book_cover( $book->ID );
			$out .= '</div>';
			$out .= '<div class="rcno-book-listing-description">';
			$out .= $book_title ? sprintf( '<h3>%s</h3>', $book_title ) : '<!-- missing title -->';
			$out .= $book_author ?: '<!-- missing author -->';
			$out .= $publisher ?: '<!-- missing publisher -->';
			$out .= $description ?: '<!-- missing description -->';

			if ( '' !== $atts['readmore'] ) {
				$out .= '<a rel="noopener" class="rcno-book-listing-more" href="' . get_the_permalink( $book->ID ) . '">';
				$out .= esc_html( $atts['readmore'] );
				$out .= '</a>';
			}

			$out .= '</div>';
			$out .= '</div>';
		}

		return $out;
	}

	/**
	 * Creates the help text for the 'rcno-book-list' shortcode.
	 *
	 * @since 1.8.0
	 * @return void
	 */
	public function rcno_set_help_text() {

		$this->help_text = '<h4>' . __( 'The Book Listing shortcode', 'recencio-book-reviews' ) . '</h4>';
		$this->help_text .= '<p>';
		$this->help_text .= __( 'This shortcode creates a list of books based on the comma separated list of reviews provided in the "ids" parameter.', 'recencio-book-reviews' );
		$this->help_text .= '</p>';

		$this->help_text .= '<code>' . '[rcno-book-listing ids=\'\']' . '</code> ' . __('The default shortcode.', 'recencio-book-reviews');
		$this->help_text .= '<p>';

		$this->help_text .= __( 'The shortcode uses a couple of parameters that can be changed or left at the default values. 
								If the default values are being used, then it is not necessary to include them in the shortcode. The default values are listed below: ',
								'recencio-book-reviews' );
		$this->help_text .= '</p>';

		$this->help_text .= '<ul>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . 'ids' . '</code> ' . __( 'This a list of review IDs, separated by commas', 'recencio-book-reviews' );
		$this->help_text .= '</li>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . 'wordcount' . '</code> ' . __( 'The word count of the book description being displayed. Defaults to 30', 'recencio-book-reviews' );
		$this->help_text .= '</li>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . 'heading' . '</code> ' . __( 'Adds a heading to the listing of book. Defaults to empty string (not shown)', 'recencio-book-reviews');
		$this->help_text .= '</li>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . 'readmore' . '</code> ' . __( 'The text displayed in the "read more" button. Defaults to "Read more"', 'recencio-book-reviews' );
		$this->help_text .= '</li>';

		$this->help_text .= '</ul>';

		$this->help_text .= '<p>';
		$this->help_text .= __( 'Examples of the book listing shortcode:', 'recencio-book-reviews' );
		$this->help_text .= '</p>';

		$this->help_text .= '<ul>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . '[rcno-book-listing ids="352,5557,695,4333" wc=50 heading="Best sellers this week"]' . '</code> ';
		$this->help_text .= '</li>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . '[rcno-book-listing ids="8976,334,765" readmore="Read review"]' . '</code> ';
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
