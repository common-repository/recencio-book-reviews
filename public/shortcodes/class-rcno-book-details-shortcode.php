<?php

/**
 * Creates the shortcodes used for book details
 *
 *
 * @since      1.65.0
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Book_Details_Shortcode {

	/**
	 * @var \Rcno_Template_Tags
	 */
	private $template;

	/**
	 * Rcno_Book_Details_Shortcode constructor.
	 *
	 * @since 1.65.0
	 *
	 * @param $plugin_name
	 * @param $version
	 */
	public function __construct( $plugin_name, $version ) {
		$this->template    = new Rcno_Template_Tags( $plugin_name, $version );
	}

	/**
	 * Return the actual shortcode code used the WP.
	 *
	 * @since 1.65.0
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function rcno_do_book_details_shortcode( $atts ) {
		$atts = shortcode_atts(
			[
				'id'      => 0,
				'detail'  => 'description',
				'wrapper' => 'p',
				'label'   => 'false',
			],
			$atts,
			'rcno-book'
		);
		$id     = (int) $atts['id'] ?: get_the_ID();
		$key    = array_search( $atts['detail'], $this->template->meta_keys(), true );
		$label  = 'true' === $atts['label'];

		$output = $this->template->get_the_rcno_book_meta( $id, $key, $atts['wrapper'], $label );

		return do_shortcode( $output );
	}
}
