<?php

/**
 * Creates the shortcodes used for book review score box.
 *
 * @link       https://wzymedia.com
 * @since      1.8.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 */

/**
 * Creates the shortcodes used for book review score box.
 *
 *
 * @since      1.8.0
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Score_Box_Shortcode {

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
	public function rcno_do_score_box_shortcode( $atts ) {

		$review_id = isset( $GLOBALS['review_id'] ) ? (int) $GLOBALS['review_id'] : get_the_ID();

		// Set default values for options not set explicitly.
		$atts = shortcode_atts( array(
			'id'        => $review_id,
		), $atts, 'rcno-score-box' );

		$output = $this->get_the_rcno_score_box( $atts );

		return do_shortcode( $output );
	}

	/**
	 * @param array $atts The shortcode attributes.
	 *
	 * @return string
	 */
	public function get_the_rcno_score_box( $atts ) {

		// If we are on the homepage don't display this.
		if ( ! is_single()  ) {
			return false;
		}

		$background = Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box_background' );
		$accent     = Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box_accent' );
		$accent_2   = Rcno_Reviews_Option::get_option( 'rcno_show_review_score_box_accent_2' );

		$rating_type     = get_post_meta( $atts['id'], 'rcno_review_score_type', true );
		$rating_criteria = get_post_meta( $atts['id'], 'rcno_review_score_criteria', true );

		if ( '' === $rating_criteria ) {
			return false; // We are not doing anything if a review score has not been set.
		}

		$rating_criteria_count = count( $rating_criteria );
		$score_array           = array();

		foreach ( (array) $rating_criteria as $criteria ) {
			$score_array[] = $criteria['score'];
		}


		$final_score = array_sum( $score_array );
		$final_score /= $rating_criteria_count; // $final_score / $rating_criteria_count
		$final_score = number_format( $final_score, 1, '.', '' );


		$output = '';
		$output .= '<div id="rcno-score-box" style="background:' . esc_attr( $background ) . '">';
		$output .= '<div class="review-summary">';
		$output .= '<div class="overall-score" style="background:' . esc_attr( $accent ) . ';" title="' . sprintf( '%s - %s out of 5', __( 'Overall Score', 'recencio-book-reviews' ),
				$final_score ) . '">';
		$output .= '<span class="overall">' . $this->template->rcno_calc_review_score( $final_score, $rating_type, true ) . '</span>';
		$output .= '<span class="overall-text" style="background: ' . $accent_2 . ';">' . __( 'Overall Score', 'recencio-book-reviews' ) . '</span>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '<ul>';

		foreach ( (array) $rating_criteria as $criteria ) {
			$percentage_score = ( $criteria['score'] / 5 ) * 100;

			if ( $criteria['label'] ) {
				$output .= '<li>';
			}
			$output .= '<div class="rcno-score-bar-container">';
			$output .= '<div class="review-score-bar" style="width:' . esc_attr( $percentage_score ) . '%; background:' . esc_attr( $accent ) . '">';
			$output .= '<span class="score-bar" title="' . sprintf( '%s - %s out of 5', $criteria['label'], $criteria['score']  ) . '">' . $criteria['label'] . '</span>';
			$output .= '</div>';
			$output .= '<span class="right">';
			$output .= $this->template->rcno_calc_review_score( $criteria['score'], $rating_type, true );
			$output .= '</span>';
			$output .= '</div>';
			$output .= '</li>';

		}
		$output .= '</ul>';
		$output .= '<div style="clear: both"></div>';
		$output .= '</div><!-- End #score-box -->';

		return $output;
	}

	/**
	 * Creates the help text for the 'rcno-book-list' shortcode.
	 *
	 * @since 1.8.0
	 * @return void
	 */
	public function rcno_set_help_text() {

		$this->help_text = '<h4>' . __( 'The Review Score shortcode', 'recencio-book-reviews' ) . '</h4>';
		$this->help_text .= '<p>';
		$this->help_text .= __( 'This shortcode display the book review score box in reviews or regular posts. This shortcode only needs the ID parameter if it is inserted into a regular post. If the shortcode is used in a regular post, the ID of the review it is attached to is needed.',
								'recencio-book-reviews' );
		$this->help_text .= '</p>';

		$this->help_text .= '<code>' . '[rcno-score-box]' . '</code> ' . __('The default shortcode.', 'recencio-book-reviews');

		$this->help_text .= '<ul>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . 'id' . '</code> ' . __( 'The review ID, defaults to the review post.', 'recencio-book-reviews' );
		$this->help_text .= '</li>';

		$this->help_text .= '</ul>';

		$this->help_text .= '<p>';
		$this->help_text .= __( 'Examples of the review score box shortcode', 'recencio-book-reviews' ) . ':';
		$this->help_text .= '</p>';

		$this->help_text .= '<ul>';

		$this->help_text .= '<li>';
		$this->help_text .= '<code>' . '[rcno-score-box id=776]' . '</code> ';
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
