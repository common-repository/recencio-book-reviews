<?php

/**
 * The shortcodes used for `[rcno-table]` shortcode
 *
 * @link       https://wzymedia.com
 * @since      1.49.0
 *
 * @package    Rcno_Reviews
 */

/**
 * Creates the shortcodes used for `[rcno-table]` shortcode
 *
 * @since      1.49.0
 *
 * @package    Rcno_Reviews
 *
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Table_Shortcode {

	/**
	 * An array of the variables used by the shortcode template
	 *
	 * @since 1.49.0
	 *
	 * @var   array $options The shortcode options
	 */
    public $options;

    /**
	 * Rcno_Isotope_Grid_Shortcode constructor.
	 *
	 * @since 1.49.0
     *
	 * @param $plugin_name
     *
	 * @param $version
	 */
	public function __construct( $plugin_name, $version ) {

	}

	/**
	 * Return the actual shortcode code used the WP.
	 *
	 * @since 1.49.0
	 *
	 * @param $options
	 *
	 * @return string
	 */
	public function rcno_do_table_shortcode( $options ) {

		// Set default values for options not set explicitly.
		$this->options = shortcode_atts(
			array(
				'sort'    => 1,
				'search'  => 1,
				'limit'   => 10,
				'orderby' => 'title',
				'order'   => 'DESC',
				'columns' => 'title,isbn,author,genre,year,publisher,rating',
				'links'   => 'title,author,genre,publisher,isbn',
			),
			$options,
			'rcno-table'
		);

		// The actual rendering is done by a special function.
		$output = $this->rcno_render_table( $this->options );

		// We are enqueuing the previously registered script.
		wp_enqueue_script( 'rcno-gridjs' );
		wp_enqueue_script( 'rcno-table' );
		wp_enqueue_style( 'rcno-table-theme' );
		wp_enqueue_script( 'rcno-meter-discrete' );

        wp_add_inline_script( 'rcno-table', 'window.rcno = window.rcno || {}; rcno.rcnoTableShortcodeOptions = ' . wp_json_encode( $this->options ) );

		return do_shortcode( $output );
	}

	/**
	 * * Do the render of the shortcode contents via the template file.
	 *
	 * @since 1.49.0
     *
	 * @param $options
	 *
	 * @return string
	 */
	public function rcno_render_table( $options ) {

		$reviews = array();
		// Get an alphabetically ordered list of all reviews.
		$args  = array(
			'post_type'              => 'rcno_review',
			'post_status'            => 'publish',
			'orderby'                => $options['orderby'],
			'order'                  => $options['order'],
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		$posts = ( new WP_Query( $args ) )->posts;

		foreach ( $posts as $post ) {
			$_reviews['ID'] = $post->ID;
			$_reviews['title'] = $post->post_title;
			$_reviews['URL'] = get_the_permalink( $post->ID );
			$_reviews['meta'] = $this->get_the_review_meta( $post->ID );
			$_reviews['terms'] = $this->get_the_review_terms( $post->ID );

			$reviews[] = $_reviews;
		}

        wp_reset_postdata();
		wp_add_inline_script( 'rcno-table', 'window.rcno = window.rcno || {}; rcno.rcnoTableShortcodeData = ' . wp_json_encode( $reviews ) );
		ob_start();
		?>
		<div id="rcno-table"></div>
		<script>
          window.rcno = window.rcno || {};
          rcno.rcnoTableShortcodeOptions = <?php echo wp_json_encode( $this->options ); ?>;
          rcno.rcnoTableShortcodeData = <?php echo wp_json_encode( $reviews ); ?>;
		</script>
	<?php
		return ob_get_clean();
	}

	/**
	 * Fetches the custom meta that is attached to a recipe post
	 *
	 * @since 1.49.0
	 *
	 * @uses \get_post_meta()
	 *
	 * @param int $review_id The post ID of a review
	 *
	 * @return array
	 */
	public function get_the_review_meta( $review_id ) {

		$metadata = array();
		$data = get_post_meta( $review_id );

		if ( ! $data ) {
			return $metadata;
		}

		foreach ( $data as $key => $value ) {
			$metadata[ $key ] = maybe_unserialize( $value[0] );
		}

		// Remove data not related to our recipe.
		$metadata = array_filter(
			$metadata,
			static function( $key ) {
				return false !== strpos( $key, 'rcno' );
			},
			ARRAY_FILTER_USE_KEY
		);

		return apply_filters( 'rcno_review_metadata', $metadata, $review_id );
	}

	/**
	 * @param int $review_id The post ID of a review
	 *
	 * @return array
	 */
	public function get_the_review_terms( $review_id ) {
		$terms      = array();
		$taxonomies = explode( ',', Rcno_Reviews_Option::get_option( 'rcno_taxonomy_selection' ) );
		$taxonomies[] = 'category';
		$taxonomies[] = 'post_tag';

		foreach ( $taxonomies as $taxonomy ) {
			$t =  get_the_terms( $review_id, 'rcno_' . strtolower( $taxonomy ) );

			if ( false === $t || is_wp_error( $t ) ) {
				$t =  get_the_terms( $review_id, strtolower( $taxonomy ) );
			}

			if ( is_array( $t ) ) {
				foreach ( $t as $tax ) {
					// $terms[] = array( $taxonomy => array( $tax->name => get_term_link( $tax, $tax->taxonomy ) ) );
					//$terms[][strtolower( $taxonomy )] = $tax->name;
					//$terms[][strtolower( $taxonomy ) . '_link'] = get_term_link( $tax, $tax->taxonomy );
					$terms[] = array(
						strtolower( $taxonomy ) => $tax->name,
						'link' => get_term_link( $tax, $tax->taxonomy ),
					);
				}
			}
		}

		return $terms;
	}

}
