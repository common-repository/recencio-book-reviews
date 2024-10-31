<?php
/**
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * The Settings definition of the plugin.
 *
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin/settings
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Settings_Definition {

	public static $template;

	public function __construct() {

	}

	/**
	 * @param $input
	 *
	 * @return string
	 */
	public static function sanitize_string( $input ) {
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
	 * [apply_tab_slug_filters description]
	 *
	 * @param  array $default_settings [description]
	 *
	 * @return array                   [description]
	 */
	private static function apply_tab_slug_filters( $default_settings ) {

		$extended_settings[] = array();
		$extended_tabs       = self::get_tabs();

		foreach ( $extended_tabs as $tab_slug => $tab_desc ) {

			$options = isset( $default_settings[ $tab_slug ] ) ? $default_settings[ $tab_slug ] : array();

			$extended_settings[ $tab_slug ] = apply_filters( 'rcno_reviews_settings_' . $tab_slug, $options );
		}

		return $extended_settings;
	}

	/**
	 * [get_default_tab_slug description]
	 *
	 * @return string
	 */
	public static function get_default_tab_slug() {
		return key( self::get_tabs() );
	}

	/**
	 * Retrieve settings tabs
	 *
	 * @since    1.0.0
	 * @return   array    $tabs    Settings tabs
	 */
	public static function get_tabs() {

		$tabs = array();
		$tabs[ 'general_tab' ]    = __( 'General', 'recencio-book-reviews' );
		$tabs[ 'taxonomy_tab' ]   = __( 'Taxonomies', 'recencio-book-reviews' );
		$tabs[ 'components_tab' ] = __( 'Components', 'recencio-book-reviews' );
		$tabs[ 'extras_tab' ]     = __( 'Extras', 'recencio-book-reviews' );
		$tabs[ 'templates_tab' ]  = __( 'Templates', 'recencio-book-reviews' );
		$tabs[ 'advanced_tab' ]   = __( 'Advanced', 'recencio-book-reviews' );

		return apply_filters( 'rcno_reviews_settings_tabs', $tabs );
	}


	/**
	 * 'Whitelisted' Rcno_Reviews settings, filters are provided for each settings
	 * section to allow extensions and other plugins to add their own settings
	 *
	 *
	 * @since    1.0.0
	 * @return    mixed    $value    Value saved / $default if key if not exist
	 */
	public static function get_settings() {

		require_once 'class-rcno-reviews-get-templates.php';
		self::$template = new Rcno_Template_Tags( RCNO_PLUGIN_NAME, RCNO_PLUGIN_VER );

		// $settings[] = array();

		$settings = array(
			// General tab.
			'general_tab'    => array(
				'general_tab_header_0'          => array(
					'name' => '<strong>' . __( 'General Settings', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'spacer_0'                      => array(
					'name' => '',
					'type' => 'spacer',
				),
				'rcno_review_labels'         => array(
					'name'          => __( 'Labels', 'recipepress-reloaded' ),
					'singular_std'  => __( 'Review', 'recipepress-reloaded' ),
					'plural_std'    => __( 'Reviews', 'recipepress-reloaded' ),
					'singular_desc' => __( 'The singular form of the label', 'recipepress-reloaded' ),
					'plural_desc'   => __( 'The plural form of the label', 'recipepress-reloaded' ),
					'type'          => 'labels',
					'size' => '25',
				),
				'rcno_review_slug_instructions' => array(
					'name' => '',
					'desc' => sprintf(
						__( 'Your book reviews will be located at <a href="%1$s">%1$s</a> and a single book review will be located at <a href="%2$s">%2$s</a> as an example.', 'recencio-book-reviews' ),
						get_site_url( null, 'reviews/' ),
						get_site_url( null, 'review/a-sample-book-review' )
					),
					'type' => 'instruction',
				),
				'spacer_1'                      => array(
					'name' => '',
					'type' => 'spacer',
				),
				'rcno_reviews_on_homepage'      => array(
					'name' => __( 'Reviews on homepage?', 'recencio-book-reviews' ),
					'desc' => __( 'Should book reviews be shown on homepage?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_reviews_in_rss'           => array(
					'name' => __( 'Reviews in RSS?', 'recencio-book-reviews' ),
					'desc' => __( 'Should book reviews be shown in the RSS feed.?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_reviews_in_rest'          => array(
					'name' => __( 'Reviews in REST API?', 'recencio-book-reviews' ),
					'desc' => __( 'Enables basic support for reviews in the WordPress REST API.', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_reviews_archive'          => array(
					'name'    => __( 'Reviews archive page', 'recencio-book-reviews' ),
					'desc'    => __( 'Show full content or excerpt on archive pages.', 'recencio-book-reviews' ),
					'options' => array(
						'archive_display_full'    => __( 'The entire book review', 'recencio-book-reviews' ),
						'archive_display_excerpt' => __( 'Only excerpt of the review', 'recencio-book-reviews' ),
					),
					'type'    => 'radio',
				),
				'rcno_reviews_in_gutenberg'     => array(
					'name' => __( 'Gutenberg support', 'recencio-book-reviews' ),
					'desc' => __( 'Enables support for the new Gutenberg post editor.', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'spacer_2'                      => array(
					'name' => '',
					'type' => 'spacer',
				),

			),

			// Taxonomies tab.
			'taxonomy_tab'   => array(
				'taxonomy_tab_header_0'        => array(
					'name' => '<strong>' . __( 'Review Taxonomies', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'spacer_0'                     => array(
					'name' => '',
					'type' => 'spacer',
				),
				'rcno_taxonomy_selection'      => array(
					'name' => __( 'Taxonomy Selection', 'recencio-book-reviews' ),
					'desc' => __( 'Create additional book review taxonomies here. Save the settings, then edit the labels in the corresponding section below.', 'recencio-book-reviews' ),
					'std'  => 'Author',
					'type' => 'text',
				),
				'spacer_1'                     => array(
					'name' => '',
					'type' => 'spacer',
				),
				'taxonomy_tab_header_1'        => array(
					'name' => '<strong>' . __( 'Builtin Taxonomies', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'spacer_2'                     => array(
					'name' => '',
					'type' => 'spacer',
				),
				'rcno_enable_builtin_taxonomy' => array(
					'name' => __( 'Default WP Taxonomy', 'recencio-book-reviews' ),
					'desc' => __( 'Enable the builtin \'category\' and \'tags\' taxonomies.', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'spacer_3'                     => array(
					'name' => '',
					'type' => 'spacer',
				),
			),

			//Components tab.
			'components_tab' => array(
				'components_tab_header_0'               => array(
					'name' => '<strong>' . __( 'Book Review Components', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_show_isbn'                        => array(
					'name' => __( 'ISBN', 'recencio-book-reviews' ),
					'desc' => __( 'Show the ISBN field for book reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_isbn13'                      => array(
					'name' => __( 'ISBN13', 'recencio-book-reviews' ),
					'desc' => __( 'Show the ISBN13 field for book reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_asin'                        => array(
					'name' => __( 'ASIN', 'recencio-book-reviews' ),
					'desc' => __( 'Show the ASIN field for book reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_gr_id'                       => array(
					'name' => __( 'Goodreads ID', 'recencio-book-reviews' ),
					'desc' => __( 'Show the Goodreads book ID field for book reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_gr_url'                      => array(
					'name' => __( 'Book URL', 'recencio-book-reviews' ),
					'desc' => __( 'Show the book\'s URL field for book reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_illustrator'                 => array(
					'name' => __( 'Illustrator', 'recencio-book-reviews' ),
					'desc' => __( 'Show the book\'s illustrator for reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_pub_date'                    => array(
					'name' => __( 'Published Date', 'recencio-book-reviews' ),
					'desc' => __( 'Show the book\'s published date for reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_pub_format'                  => array(
					'name' => __( 'Published Format', 'recencio-book-reviews' ),
					'desc' => __( 'Show the book\'s published format for reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_pub_edition'                 => array(
					'name' => __( 'Published Edition', 'recencio-book-reviews' ),
					'desc' => __( 'Show the book\'s published edition for reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_series_number'                  => array(
					'name' => __( 'Series Number', 'recencio-book-reviews' ),
					'desc' => __( 'Show the book\'s number in the series for reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_page_count'                  => array(
					'name' => __( 'Page Count', 'recencio-book-reviews' ),
					'desc' => __( 'Show the book\'s page count for reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_gr_rating'                   => array(
					'name' => __( 'Goodreads Rating', 'recencio-book-reviews' ),
					'desc' => __( 'Show the book\'s Goodreads rating for reviews?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_book_cover_url'                   => array(
					'name' => __( 'Book Cover Custom URL', 'recencio-book-reviews' ),
					'desc' => __( 'Show the custom book cover URL field?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'spacer2'                               => array(
					'name' => '',
					'type' => 'spacer',
				),

				// Purchase Links.
				'components_tab_header_3'               => array(
					'name' => '<strong>' . __( 'Book Purchase Links', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_enable_purchase_links'            => array(
					'name' => __( 'Enable Purchase Links', 'recencio-book-reviews' ),
					'desc' => __( 'Enable the use of book purchase links', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_store_purchase_links_label'       => array(
					'name' => __( 'Links Label', 'recencio-book-reviews' ),
					'desc' => __( 'Enter the label shown before purchase links.', 'recencio-book-reviews' ),
					'std'  => 'Purchase on: ',
					'type' => 'text',
				),
				'rcno_store_purchase_links'             => array(
					'name' => __( 'Store/Shop', 'recencio-book-reviews' ),
					'desc' => __( 'Enter the name of stores to purchase books from.', 'recencio-book-reviews' ),
					'std'  => 'Amazon,Barnes & Noble,Kobo',
					'type' => 'text',
				),
				'rcno_store_purchase_link_text_color'   => array(
					'name' => __( 'Text Color', 'recencio-book-reviews' ),
					'desc' => __( 'Text color for the purchase button.', 'recencio-book-reviews' ),
					'type' => 'color',
				),
				'rcno_store_purchase_link_background'   => array(
					'name' => __( 'Background Color', 'recencio-book-reviews' ),
					'desc' => __( 'Background color for the purchase button.', 'recencio-book-reviews' ),
					'type' => 'color',
				),
				'spacer'                                => array(
					'name' => '',
					'type' => 'spacer',
				),

				// 5-Star Rating box.
				'components_tab_header_4'               => array(
					'name' => '<strong>' . __( 'Book 5 Star Rating Box', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_enable_star_rating_box'           => array(
					'name' => __( '5 Star Rating', 'recencio-book-reviews' ),
					'desc' => __( 'Enable the 5 star rating box?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_star_rating_color'                => array(
					'name' => __( 'Star Rating Color', 'recencio-book-reviews' ),
					'desc' => __( 'The color of the 5 star rating.', 'recencio-book-reviews' ),
					'type' => 'color',
				),
				'rcno_star_background_color'            => array(
					'name' => __( 'Star Background Color', 'recencio-book-reviews' ),
					'desc' => __( 'Background color for the 5 star rating. (Delete existing value for transparent background)',
						'recencio-book-reviews' ),
					'type' => 'color',
				),
				'spacer5'                               => array(
					'name' => '',
					'type' => 'spacer',
				),


				// Review score box.
				'components_tab_header_2'               => array(
					'name' => '<strong>' . __( 'Book Review Score Box', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_show_review_score_box'            => array(
					'name' => __( 'Review Score Box', 'recencio-book-reviews' ),
					'desc' => __( 'Enable the review score box?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_review_score_box_background' => array(
					'name' => __( 'Background Color', 'recencio-book-reviews' ),
					'desc' => __( 'Background Color for the review score box?', 'recencio-book-reviews' ),
					'type' => 'color',
				),
				'rcno_show_review_score_box_accent'     => array(
					'name' => __( 'Accent Color 1', 'recencio-book-reviews' ),
					'desc' => __( 'The first accent color for the review score box?', 'recencio-book-reviews' ),
					'type' => 'color',
				),
				'rcno_show_review_score_box_accent_2'   => array(
					'name' => __( 'Accent Color 2', 'recencio-book-reviews' ),
					'desc' => __( 'The second accent color for the review score box?', 'recencio-book-reviews' ),
					'type' => 'color',
				),
				'spacer-0' => array(
					'name' => '',
					'type' => 'spacer',
				),
				'rcno_enable_custom_review_score_criteria'            => array(
					'name' => __( 'Fixed Review Criteria', 'recencio-book-reviews' ),
					'desc' => __( 'Enable the use of fixed custom review criteria fields', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_custom_review_score_criteria'             => array(
					'name' => __( 'Custom Criteria List', 'recencio-book-reviews' ),
					'desc' => __( 'List of custom fields to use for review criteria.', 'recencio-book-reviews' ),
					'std'  => 'Plot,Characters,World',
					'type' => 'text',
				),
				'spacer-1' => array(
					'name' => '',
					'type' => 'spacer',
				),


			),

			// Extras tab.
			'extras_tab'     => array(

				// Comment 5 star ratings.
				'extras_tab_header_0'             => array(
					'name' => '<strong>' . __( 'Reader Comment Rating', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_enable_comment_ratings'     => array(
					'name' => __( 'Comment Ratings', 'recencio-book-reviews' ),
					'desc' => __( 'Enable reader submitted ratings in the comment form', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_comment_rating_label'       => array(
					'name' => __( 'Rating Label', 'recencio-book-reviews' ),
					'desc' => __( 'Enter the label before the comment 5 star rating field', 'recencio-book-reviews' ),
					'std'  => __( 'Rate this review', 'recencio-book-reviews' ),
					'type' => 'text',
				),
				'rcno_comment_rating_star_color'  => array(
					'name' => __( 'Comment Star Color', 'recencio-book-reviews' ),
					'desc' => __( 'Background color for the reader comment 5 star rating', 'recencio-book-reviews' ),
					'type' => 'color',
				),
				'spacer-6'                        => array(
					'name' => '',
					'type' => 'spacer',
				),

				// Custom widgets.
				'extras_tab_header_1'             => array(
					'name' => '<strong>' . __( 'Book Review Widgets', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_show_book_slider_widget'    => array(
					'name' => __( 'Book Slider', 'recencio-book-reviews' ),
					'desc' => __( 'Use the Rcno Book Slider widget?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_book_grid_widget'      => array(
					'name' => __( 'Book Grid', 'recencio-book-reviews' ),
					'desc' => __( 'Use the Rcno Book Grid widget?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_recent_reviews_widget' => array(
					'name' => __( 'Recent Reviews', 'recencio-book-reviews' ),
					'desc' => __( 'Use the Rcno Recent Reviews widget?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_tag_cloud_widget'      => array(
					'name' => __( 'Tag Cloud', 'recencio-book-reviews' ),
					'desc' => __( 'Use the Rcno Tag Cloud widget?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_taxonomy_list_widget'  => array(
					'name' => __( 'Taxonomy List', 'recencio-book-reviews' ),
					'desc' => __( 'Use the Rcno Taxonomy List widget?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_currently_reading_widget'  => array(
					'name' => __( 'Currently Reading', 'recencio-book-reviews' ),
					'desc' => __( 'Use the Rcno Currently Reading widget?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_review_calendar_widget'  => array(
					'name' => __( 'Review Calendar', 'recencio-book-reviews' ),
					'desc' => __( 'Use the Rcno Review Calendar widget?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'spacer-7'                        => array(
					'name' => '',
					'type' => 'spacer',
				),
				'extras_tab_header_2'             => array(
					'name' => '<strong>' . __( 'Index Pages', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_reviews_index_headers'      => array(
					'name' => __( 'Navigation headers', 'recencio-book-reviews' ),
					'desc' => __( 'Display navigation header on index page?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_show_book_covers_index'      => array(
					'name' => __( 'Book covers', 'recencio-book-reviews' ),
					'desc' => __( 'Book covers on review index page?', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_reviews_ignore_articles'          => array(
					'name' => __( 'Ignore articles', 'recencio-book-reviews' ),
					'desc' => __( 'Ignore articles when sorting titles. (e.g. "The", "A", "An")', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_reviews_ignored_articles_list'          => array(
					'name' => __( 'Article List', 'recencio-book-reviews' ),
					'desc' => __( 'The list of articles to ignore in your language', 'recencio-book-reviews' ),
					'type' => 'text',
					'std'  => 'The,A,An'
				),
				'rcno_reviews_sort_names'          => array(
					'name'    => __( 'Sort author names', 'recencio-book-reviews' ),
					'desc'    => __( 'Sort names by first-name or last-name', 'recencio-book-reviews' ),
					'options' => array(
						'last_name_first_name' => __( 'LastName, FirstName', 'recencio-book-reviews' ),
						'first_name_last_name' => __( 'FirstName LastName', 'recencio-book-reviews' ),
					),
					'type'    => 'radio',
				),
				'spacer-7-1'                        => array(
					'name' => '',
					'type' => 'spacer',
				),

			),

			// Templates tab.
			'templates_tab'  => array(

				'templates_tab_header_0'  => array(
					'name' => '<strong>' . __( 'Review Templates', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_review_template'    => array(
					'name'    => __( 'Book review template', 'recencio-book-reviews' ),
					'desc'    => __( 'Select how you want your book reviews to look.', 'recencio-book-reviews' ),
					'options' => layout_list(),
					'type'    => 'template',
				),
				'rcno_default_cover'      => array(
					'name' => __( 'Default Book Cover', 'recencio-book-reviews' ),
					'desc' => __( 'The default image to use when a book cover isn\'t uploaded', 'recencio-book-reviews' ),
					'type' => 'upload',
				),
				'rcno_excerpt_read_more'  => array(
					'name' => __( 'Read more text', 'recencio-book-reviews' ),
					'desc' => __( 'The review excerpt \'Read more\' tag.', 'recencio-book-reviews' ),
					'std'  => 'Read more',
					'type' => 'text',
				),
				'rcno_excerpt_word_count' => array(
					'name' => __( 'Review excerpt word count', 'recencio-book-reviews' ),
					'desc' => __( 'Max: 500, Min: 20', 'recencio-book-reviews' ),
					'max'  => 500,
					'min'  => 20,
					'step' => 1,
					'type' => 'number',
				),
				'rcno_reviews_embedded_title_links'          => array(
					'name' => __( 'Embedded Review Links', 'recencio-book-reviews' ),
					'desc' => __( 'Enable clickable title links on embedded book reviews.', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_book_details_meta'  => array(
					'name' => __( 'Book Detail Items', 'recencio-book-reviews' ),
					'desc' => __( 'Book details that should appear in book reviews. You also rearrange their order here', 'recencio-book-reviews' ),
					'std'  => implode( ',', self::$template->get_rcno_book_meta_keys( 'keys', 8 ) ),
					'type' => 'text',
				),
				'spacer-8'                => array(
					'name' => '',
					'type' => 'spacer',
				),
				 // Custom stylesheet.
				'templates_tab_header_1'  => array(
					'name' => '<strong>' . __( 'Reviews Custom Styling', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_custom_styling'                   => array(
					'name' => __( 'Custom styles', 'recencio-book-reviews' ),
					'desc' => __( 'Add your custom CSS here to fine-tune the look of your book reviews', 'recencio-book-reviews' ),
					'type' => 'cssbox',
				),
				'spacer-9'                => array(
					'name' => '',
					'type' => 'spacer',
				),

			),
			'advanced_tab'   => array(

				'advanced_tab_header_0'  => array(
					'name' => '<strong>' . __( 'Book APIs', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_external_book_api' => array(
					'name'    => __( 'External Book API', 'recencio-book-reviews' ),
					'desc'    => __( 'Select which 3rd party API is used to fetch book data.' . '<br />' . 'The corresponding option must also be enabled below.', 'recencio-book-reviews' ),
					'options' => array(
						'no-3rd-party' => __( 'None', 'recencio-book-reviews' ),
						'google-books' => __( 'Google Books® API', 'recencio-book-reviews' ),
						'good-reads'   => __( 'Goodreads® API', 'recencio-book-reviews' ),
					),
					'type'    => 'radio',
				),
				'spacer-8'               => array(
					'name' => '',
					'type' => 'spacer',
				),

				'advanced_tab_header_1'   => array(
					'name' => '<strong>' . __( 'Google Books® Settings', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_enable_googlebooks' => array(
					'name' => __( 'Enable Google Books®', 'recencio-book-reviews' ),
					'desc' => __( 'Enable the external Google Books® API access.', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_googlebooks_key'    => array(
					'name' => __( 'Google Books® API Key', 'recencio-book-reviews' ),
					'desc' => __( 'Available at: ', 'recencio-book-reviews' ) . 'https://console.developers.google.com/apis/credentials',
					'type' => 'password',
				),
				'spacer-9'                => array(
					'name' => '',
					'type' => 'spacer',
				),

				'advanced_tab_header_2' => array(
					'name' => '<strong>' . __( 'Goodreads® Settings', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_enable_goodreads' => array(
					'name' => __( 'Enable Goodreads®', 'recencio-book-reviews' ),
					'desc' => __( 'Enable the external Goodreads® API access.', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_goodreads_key'    => array(
					'name' => __( 'Goodreads® API Key', 'recencio-book-reviews' ),
					'desc' => __( 'Available at: ', 'recencio-book-reviews' ) . 'https://www.goodreads.com/api/keys',
					'type' => 'password',
				),
				'rcno_goodreads_secret' => array(
					'name' => __( 'Goodreads® API Secret', 'recencio-book-reviews' ),
					'desc' => __( 'Available at: ', 'recencio-book-reviews' ) . 'https://www.goodreads.com/api/keys',
					'type' => 'password',
				),
				'spacer-10'             => array(
					'name' => '',
					'type' => 'spacer',
				),

				'advanced_tab_header_5' => array(
					'name' => '<strong>' . __( 'JSON-LD Schemas', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_disable_review_schema' => array(
					'name' => __( 'Disable Review Schema', 'recencio-book-reviews' ),
					'desc' => __( 'Disable the output of the review JSON-LD schema markup', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'rcno_disable_book_schema' => array(
					'name' => __( 'Disable Book Schema', 'recencio-book-reviews' ),
					'desc' => __( 'Disable the output of the book JSON-LD schema markup', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'spacer-13'             => array(
					'name' => '',
					'type' => 'spacer',
				),

				'advanced_tab_header_3' => array(
					'name' => '<strong>' . __( 'Uncountable Words', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_no_pluralization'      => array(
					'name' => __( 'Word List', 'recencio-book-reviews' ),
					'desc' => __( 'Create a list of words that have no plural or singular form e.g. "sheep, fish"', 'recencio-book-reviews' ),
					'type' => 'text',
				),
				'rcno_disable_pluralization' => array(
					'name' => __( 'Disable Pluralization', 'recencio-book-reviews' ),
					'desc' => __( 'Disable the automatic pluralization of words used in the settings', 'recencio-book-reviews' ),
					'type' => 'checkbox',
				),
				'spacer-11'             => array(
					'name' => '',
					'type' => 'spacer',
				),

				'advanced_tab_header_4' => array(
					'name' => '<strong>' . __( 'Import/Export Settings', 'recencio-book-reviews' ) . '</strong>',
					'type' => 'header',
				),
				'rcno_settings_import'      => array(
					'name' => __( 'Import Settings', 'recencio-book-reviews' ),
					'desc' => __( 'Import your previously saved plugin settings.', 'recencio-book-reviews' ),
					'type' => 'file',
					'accept' => '.json',
				),
				'rcno_settings_export'      => array(
					'name' => __( 'Export Settings', 'recencio-book-reviews' ),
					'desc' => __( 'Save the plugin settings to a file on your computer.', 'recencio-book-reviews' ),
					'type' => 'download',
				),
				'spacer-12'             => array(
					'name' => '',
					'type' => 'spacer',
				),

			),


		);

		// Adds each custom taxonomy's options via a foreach loop
		$custom_taxonomies = new Rcno_Reviews_Admin( RCNO_PLUGIN_NAME, RCNO_PLUGIN_VER );
		$_custom_taxonomies = $custom_taxonomies->rcno_get_custom_taxonomies();
		foreach (  $_custom_taxonomies as $tax ) {
			foreach ( self::taxonomy_options( $tax['tax_settings'] ) as $key => $value ) {
				$settings['taxonomy_tab'][ strtolower( $key ) ] = $value;
			}
		}

		return self::apply_tab_slug_filters( $settings );
	}

	/**
	 * Handles the custom taxonomy section
	 *
	 * @param array $tax
	 *
	 * @return array
	 */
	public static function taxonomy_options( $tax ) {

		$opts = array(
			'rcno_' . self::sanitize_string( $tax['settings_key'] ) . '_header' => array(
				'name' => '<strong>' . $tax['label'] . '</strong>',
				'type' => 'header',
			),
			'spacer_' . $tax['settings_key'] . '_0' => array(
				'name' => '',
				'type' => 'spacer',
			),
			'rcno_' . self::sanitize_string( $tax['settings_key'] ) . '_labels' => array(
				'name'          => __( 'Labels', 'recencio-book-reviews' ),
				'singular_std'  => ! empty( $tax['labels']['singular'] ) ? $tax['labels']['singular'] : Rcno_Pluralize_Helper::singularize( $tax['label'] ),
				'plural_std'    => ! empty( $tax['labels']['plural'] ) ? $tax['labels']['plural'] : Rcno_Pluralize_Helper::pluralize( $tax['label'] ),
				'singular_desc' => sprintf( __( 'The singular form of the <b>"%1$s"</b> taxonomy label', 'recencio-book-reviews' ), Rcno_Pluralize_Helper::singularize( $tax['label'] ) ),
				'plural_desc'   => sprintf( __( 'The plural form of the <b>"%1$s"</b> taxonomy label', 'recencio-book-reviews' ), Rcno_Pluralize_Helper::pluralize( $tax['label']) ),
				'type'          => 'labels',
				'size'          => '25',
			),
			'rcno_' . self::sanitize_string( $tax['settings_key'] ) . '_hierarchical' => array(
				'name' => __( 'Hierarchical', 'recencio-book-reviews' ),
				'desc' => __( 'Is this custom taxonomy hierarchical?', 'recencio-book-reviews' ),
				'type' => 'checkbox',
			),
			'rcno_' . self::sanitize_string( $tax['settings_key'] ) . '_show' => array(
				'name' => __( 'Show in table', 'recencio-book-reviews' ),
				'desc' => __( 'Show this custom taxonomy on the admin table', 'recencio-book-reviews' ),
				'type' => 'checkbox',
			),
			'rcno_' . self::sanitize_string( $tax['settings_key'] ) . '_filter' => array(
				'name' => __( 'Show filter', 'recencio-book-reviews' ),
				'desc' => __( 'Show a drop-down filter for this taxonomy on the admin table', 'recencio-book-reviews' ),
				'type' => 'checkbox',
			),
			'rcno_' . self::sanitize_string( $tax['settings_key'] ) . '_posts' => array(
				'name' => __( 'Use with posts', 'recencio-book-reviews' ),
				'desc' => __( 'Use this taxonomy with regular WordPress posts', 'recencio-book-reviews' ),
				'type' => 'checkbox',
			),
			'spacer_' . self::sanitize_string( $tax['settings_key'] ) . '_1' => array(
				'name' => '',
				'type' => 'spacer',
			),
		);

		return $opts;
	}

}
