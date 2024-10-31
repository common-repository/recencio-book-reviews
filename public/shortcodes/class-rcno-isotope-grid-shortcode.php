<?php

/**
 * Creates the shortcodes used for isotope grid.
 *
 * @link       https://wzymedia.com
 * @since      1.12.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public/shortcodes
 */

/**
 * Creates the shortcodes used for isotope grid.
 *
 * @since      1.12.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/includes
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Isotope_Grid_Shortcode {

	/**
	 * The Rcno_Template_Tags class
	 *
	 * @since    1.12.0
	 * @access   private
	 * @var      Rcno_Template_Tags $template The current version of this plugin.
	 */
	private $template;

	/**
	 * An array of the variables used by the shortcode template
	 *
	 * @since    1.12.0
	 * @access   private
	 * @var      array $variables The current version of this plugin.
	 */
	private $variables;
    private $options;

    /**
	 * Rcno_Isotope_Grid_Shortcode constructor.
	 *
	 * @since 1.12.0
	 * @param $plugin_name
	 * @param $version
	 */
	public function __construct( $plugin_name, $version ) {

		$this->template  = new Rcno_Template_Tags( $plugin_name, $version );
		$this->variables = $this->rcno_get_shortcode_variables();
	}

	/**
	 * Fetches all the variables to be used in template and add them to an array.
	 *
	 * @since 1.12.0
	 * @return array
	 */
	private function rcno_get_shortcode_variables() {
		$variables                      = array();
		$variables['ignore_articles']   = (bool) Rcno_Reviews_Option::get_option( 'rcno_reviews_ignore_articles' );
		$variables['articles_list']     = str_replace( ',', '|', Rcno_Reviews_Option::get_option( 'rcno_reviews_ignored_articles_list', 'The,A,An' ) ) . '|\d+';
		$variables['custom_taxonomies'] = explode( ',', strtolower( Rcno_Reviews_Option::get_option( 'rcno_taxonomy_selection' ) ) );
		return $variables;
	}

	/**
	 * Return the actual shortcode code used the WP.
	 *
	 * @since 1.12.0
	 * @param $options
	 * @return string
	 */
	public function rcno_do_isotope_grid_shortcode( $options ) {

		// Set default values for options not set explicitly.
		$this->options = shortcode_atts(
			array(
				'selectors' => 1,
				'search'    => 1,
				'width'     => 85,
				'height'    => 130,
				'exclude'   => '',
				'rating'    => 1,
				'category'  => 0,
				'count'     => 50,
				'orderby'   => 'title',
				'order'     => 'DESC',
			),
			$options,
			'rcno-sortable-grid'
		);

		// The actual rendering is done by a special function.
		$output = $this->rcno_render_isotope_grid( $this->options );

		// We are enqueuing the previously registered script.
		wp_enqueue_script( 'rcno-images-loaded' );
		wp_enqueue_script( 'rcno-isotope-grid' );

        $this->options['nonce']   = wp_create_nonce( 'rcno-isotope' );
        $this->options['ajaxURL'] = admin_url( 'admin-ajax.php' );

        wp_add_inline_script( 'rcno-isotope-grid', 'const rcnoIsotopeVars = ' . wp_json_encode( $this->options ) );

		return do_shortcode( $output );
	}

	/**
	 * * Do the render of the shortcode contents via the template file.
	 *
	 * @since 1.12.0
     *
	 * @param $options
	 * @return string
	 */
	public function rcno_render_isotope_grid( $options ) {

		// Get an alphabetically ordered list of all reviews.
		$args  = array(
			'post_type'      => 'rcno_review',
			'post_status'    => 'publish',
			'orderby'        => $options['orderby'],
			'order'          => $options['order'],
			'posts_per_page' => (int) $options['count'],
			'fields'         => 'ids', // Only get post IDs
		);

		$posts = ( new WP_Query( $args ) )->posts;

        wp_reset_postdata();

		// This will check for a template file inside the theme folder.
		$template_path = get_query_template( 'rcno-isotope-grid' );

		// If no such file exists, use the default template for the shortcode.
		if ( empty( $template_path ) ) {
			$template_path = __DIR__ . '/layouts/rcno-isotope-html.php';
		}

		ob_start();

		include $template_path;

		// Render the content using that file.
		$content = ob_get_contents();

		// Finish rendering.
		if ( ob_get_contents() ) {
			ob_end_clean();
		}

		// Return the rendered content.
		return $content;
	}

	/**
	 * Utility function used by `usort` to sort titles alphabetically
	 *
	 * @since 1.12.0
	 * @param $a
	 * @param $b
	 * @return int
	 */
	protected function sort_by_title( $a, $b ) {
		return strcasecmp( $a['sorted_title'][0], $b['sorted_title'][0] );
	}

    /**
     * Sends more recipes via AJAX
     *
     * @since 2.0.0
     *
     * @return string
     */
    public function more_filtered_reviews() {
        //  Nonce check
        /*if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'rpr-filterable' ) ) {
            wp_die( __( 'Failed security check', 'recipepress-reloaded' ) );
        }*/

        $output = '';
        $args = array(
            'post_type' => 'rcno_review',
            'posts_per_page' => isset( $_POST['count'] ) ? (int) $_POST['count'] : 20,
            'orderby' => isset( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'date',
            'order'   => isset( $_POST['order'] ) ? sanitize_text_field( $_POST['order'] ) : 'DESC',
            'post_status' => 'publish'
        );

        $pagenum = isset( $_POST['page'] ) ? (int) $_POST['page'] : 1;
        $args['offset'] = $pagenum > 1 ? $args['posts_per_page'] * ( $pagenum - 1 ) : 0;

        $reviews = new \WP_Query( $args );

        if ( $reviews->have_posts() ) {

            $width  = isset( $_POST['width'] ) ? (int) $_POST['width'] : 85;
            $height = isset( $_POST['height'] ) ? (int) $_POST['height'] : 120;

            foreach( $reviews->posts as $review ) {
                $output .= '<div class="rcno-isotope-grid-item ';
                $output .= sanitize_title( $this->template->get_the_rcno_book_meta( $review->ID, 'rcno_book_title', '', false ) ) . ' ';
                $output .= implode( ' ', $this->template->get_rcno_review_html_classes( $review->ID ) ) . '" ';
                $output .= 'style="width:' . $width . 'px; height:' . $height . 'px;"';
                $output .= '>';
                if ( isset( $_POST['rating'] ) && (int) $_POST['rating'] ) {
                    $output .= $this->template->get_the_rcno_admin_book_rating( $review->ID );
                }
                $output .= '<a href="' . get_permalink( $review->ID ) . '">';
                // $size 'thumbnail', 'medium', 'full', 'rcno-book-cover-sm', 'rcno-book-cover-lg'.
                if ( $width > 85 ) {
                    $output .= $this->template->get_the_rcno_book_cover( $review->ID, 'rcno-book-cover-lg' );
                } else {
                    $output .= $this->template->get_the_rcno_book_cover( $review->ID, 'rcno-book-cover-sm' );
                }
                $output .= '</a>';
                $output .= '</div>';
            }

            wp_reset_postdata();
        }

        echo $output;

        wp_die();
    }
}
