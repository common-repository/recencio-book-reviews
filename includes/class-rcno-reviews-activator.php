<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Activator {

	/**
	 * Short Description. (use period).
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		( new Rcno_Reviews_Admin( RCNO_PLUGIN_NAME, RCNO_PLUGIN_VER ) )->rcno_review_post_type();

		flush_rewrite_rules();
	}

	/**
	 * Sets the plugins default options.
	 *
	 * @return void
	 */
	public static function setup_rcno_settings() {
		$previous_version = get_option( 'rcno_reviews_settings' );

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

		if ( ! $previous_version ) {
			update_option( 'rcno_reviews_settings', $default_options );
		}
	}

}
