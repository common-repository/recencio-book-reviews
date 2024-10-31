<?php
/**
 * The public-facing comments rating system of the plugin.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 */

/**
 * The public-facing comments rating system of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Public_Rating {

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
	 * The enable public comment setting.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      bool   $enable_rating
	 */
	private $enable_rating;

	/**
	 * The public rating label, stored in the settings.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string   $enable_rating
	 */
	private $ratings_label;

	/**
	 * The background color of the comment rating stars.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string   $star_color
	 */
	private $star_color;

	/**
	 * The comment rating provided by a site visitor.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $rating.
	 */
	private $rating;

	/**
	 * The total count of comments that have provide with a rating, per review post.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $comment_count.
	 */
	private $comment_count;

	/**
	 * The minimum rating value provided by a site visitor, per review post.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $min_rating.
	 */
	private $min_rating;

	/**
	 * The maximum rating value provided by a site visitor, per review post.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $max_rating.
	 */
	private $max_rating;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->enable_rating = (bool) Rcno_Reviews_Option::get_option( 'rcno_enable_comment_ratings', false );
		$this->ratings_label = (string) Rcno_Reviews_Option::get_option( 'rcno_comment_rating_label' );
		$this->star_color    = (string) Rcno_Reviews_Option::get_option( 'rcno_comment_rating_star_color', '#CCCCCC' );
	}

	/**
	 * Enqueues the public facing stylesheet for the comment ratings.
	 */
	public function rcno_enqueue_public_ratings_styles() {

		if ( $this->enable_rating && is_singular( 'rcno_review' ) ) {
			wp_enqueue_style( 'rcno-public-ratings-styles', plugin_dir_url( __FILE__ ) . 'css/rcno-reviews-public-ratings.css', array(), $this->version, 'all' );

			$custom_css = '
				.rating .whole .l, .rating .whole .r {
				    background-color: ' . $this->star_color . '
				}
				.rating .half .l, .rating .half .r {
				    background-color: ' . $this->star_color . '
				}
				.rating .rover .l, .rating .rover .r {
				    background-color: ' . $this->star_color . '
				}
			';

			wp_add_inline_style( 'rcno-public-ratings-styles', $custom_css );
		}

	}


	/**
	 * Enqueues the public facing scripts for the comment ratings.
	 */
	public function rcno_enqueue_public_ratings_scripts() {

		if ( $this->enable_rating && is_singular( 'rcno_review' ) ) {
		wp_enqueue_script( 'rcno-public-ratings-scripts', plugin_dir_url( __FILE__ ) . 'js/rcno-reviews-public-ratings-script.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( 'rcno-public-ratings-scripts', 'rcno_public_object',
			array(
				'public_ajax_url'      => admin_url( 'admin-ajax.php' ),
				'public_ratings_nonce' => wp_create_nonce( 'rcno-ajax-public-ratings-nonce' ),
			) );
		}

	}


	/**
	 * Saves the comment rating data on the 'comment_post' WP hook
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function rcno_comment_post( $id ) {

		if ( ! isset( $_POST['comment_karma'] ) ) {
			return;
		}

		$comment_karma = absint( $_POST['comment_karma'] );

		if ( $comment_karma > 5 ) {
			$comment_karma = 5;
		} elseif ( $comment_karma <= 0 ) {
			$comment_karma = 1;
		} else {
			$comment_karma = absint( $comment_karma );
		}

		update_comment_meta( $id, 'rcno_review_comment_rating', $comment_karma );
	}


	/**
	 * Display the star rating inside the comment form.
	 *
	 * @return string|bool
	 */
	public function rcno_comment_ratings_form() {

		if ( $this->enable_rating && is_singular( 'rcno_review' ) ) {

			$star = '<li class="empty"><span class="l"></span><span class="r"></span></li>';

			return printf(
				'<div class="rating-container"><p class="rating-label">%s</p><ul class="rating form-rating">%s</ul></div>',
				$this->ratings_label,
				str_repeat( $star, 5 )
			);
		}

		return false;
	}


	/**
	 * If a user submits a comment without leaving a rating
	 * use AJAX to send rating.
	 * @return void
	 */
	public function rcno_rate_review() {

		check_ajax_referer( 'rcno-ajax-public-ratings-nonce', 'security_nonce', true );

		$user = '';

		$comment_ID      = isset( $_POST['comment_ID'] ) ? (int) $_POST['comment_ID'] : '' ;
		$comment_post_ID = isset( $_POST['comment_post_ID'] ) ? (int) $_POST['comment_post_ID'] : '';
		$comment_karma   = isset( $_POST['rating'] ) ? (int) $_POST['rating'] : '' ;

		$comment_author_cookie     = $_COOKIE[ 'comment_author_' . COOKIEHASH ];
		$comment_author_e_cookie   = $_COOKIE[ 'comment_author_email_' . COOKIEHASH ];
		$comment_author_url_cookie = $_COOKIE[ 'comment_author_url_' . COOKIEHASH ];


		if ( is_user_logged_in() ) {
			$user               = wp_get_current_user();
			$user->display_name = $user->user_login;
		}

		if ( is_user_logged_in() ) {
			$comment_author = esc_sql( $user->display_name );
		} elseif ( null !== $comment_author_cookie ) {
			$comment_author = $comment_author_cookie;
		} else {
			$comment_author = '';
		}

		if ( is_user_logged_in() ) {
			$comment_author_email = esc_sql( $user->user_email );
		} elseif ( null !== $comment_author_e_cookie ) {
			$comment_author_email = $comment_author_e_cookie;
		} else {
			$comment_author_email = '';
		}

		if ( is_user_logged_in() ) {
			$comment_author_url = esc_sql( $user->user_url );
		} elseif ( null !== $comment_author_url_cookie ) {
			$comment_author_url = $comment_author_url_cookie;
		} else {
			$comment_author_url = '';
		}

		if ( empty( $comment_author ) || empty( $comment_author_email ) ) {
			wp_die( __( 'I don\'t know who you are', 'recencio-book-reviews' ) );
		}

		update_comment_meta( $comment_ID, 'rcno_review_comment_rating', $comment_karma );

		wp_die();
	}


	/**
	 * Returns the current user either by 'wp_get_current_user' or stored cookie
	 *
	 * @return string
	 */
	private function rcno_current_user() {
		global $current_user;

		if ( is_user_logged_in() ) {
			wp_get_current_user();

			return $current_user->user_login;
		}

		return $_COOKIE[ 'comment_author_' . COOKIEHASH ];
	}


	/**
	 * Is this user known.
	 *
	 * @return bool
	 */
	private function rcno_ratings_user_is_known() {
		return is_user_logged_in() || ! empty( $_COOKIE[ 'comment_author_' . COOKIEHASH ] );
	}


	/**
	 * Calculates the raw review score from the comment metadata.
	 *
	 * @param string $query
	 *
	 * @return bool|float|int
	 */
	public function rcno_rating_info( $query ) {

		// Get the review ID.
		$review_id = ! empty( $GLOBALS['review_id'] ) ? (int) $GLOBALS['review_id'] : get_the_ID();

		switch ( $query ) {

			case 'avg':
				$avg = $this->count_ratings_info( $review_id );
				if ( null !== $avg ) {
					return $this->rating = array_sum( $avg ) / count( $avg );
				}
				return 0;

			case 'count':
				$count = $this->count_ratings_info( $review_id );
				if ( null !== $count ) {
					return $this->comment_count = count( $count );
				}
				return 0;

			case 'min':
				$min = $this->count_ratings_info( $review_id );
				if ( null !== $min ) {
					return $this->min_rating = (int) min( $min );
				}
				return 0;

			case 'max':
				$max = $this->count_ratings_info( $review_id );
				if ( null !== $max ) {
					return $this->max_rating = (int) max( $max );
				}
				return 0;

			default:
				return false;
		}
	}


	/**
	 * Does the retrieval of public comment scores from the comment meta table.
	 *
	 * @param int $review_id
	 *
	 * @return array
	 */
	private function count_ratings_info( $review_id ) {

		$comments = get_comments( array(
			'post_id'  => (int) $review_id,
			'meta_key' => 'rcno_review_comment_rating',
		) );

		$comment_ids  = array();
		$karma_scores = array();

		foreach ( $comments as $comment ) {
			$comment_ids[] = $comment->comment_ID;
		}

		foreach ( $comment_ids as $value ) {
			$karma_scores[] = get_comment_meta( $value, 'rcno_review_comment_rating', true );
		}

		return $karma_scores ?: null;
	}


	/**
	 * Does the actual rendering of the star rating.
	 *
	 * @param int  $id
	 * @param bool $is_comment
	 *
	 * @return string
	 */
	public function rate_calculate( $id = 0, $is_comment = false ) {

		$post_id     = (int) $id > 0 ? $id : get_the_ID();
		$previous_id = 0;

		if ( $is_comment ) {
			$c            = $GLOBALS['comment'];
			$this->rating = (float) get_comment_meta( $c->comment_ID, 'rcno_review_comment_rating', true );
			$previous_id  = (int) $c->comment_ID;
		} else {
			$this->rating = (float) $this->rcno_rating_info( 'avg' );
		}

		$this->rating = number_format( $this->rating, 1, '.', '' );

		if ( $this->rating === 0.0 ) {
			$coerced_rating = 0.0;
		} elseif ( ( $this->rating * 10 ) % 5 !== 0 ) {
			$coerced_rating = round( $this->rating * 2.0, 0 ) / 2.0;
		} else {
			$coerced_rating = $this->rating;
		}

		$stars   = array( 0, 1, 2, 3, 4, 5, 6 );
		$classes = array( 'rating' );
		$format  = '<li class="%s"><span class="l"></span><span class="r"></span></li>';

		for ( $i = 1; $i <= 5; $i ++ ) {
			if ( $i <= $coerced_rating ) {
				$stars[ $i ] = sprintf( $format, 'whole' );
			} elseif ( $i - 0.5 === $coerced_rating ) {
				$stars[ $i ] = sprintf( $format, 'half' );
			} else {
				$stars[ $i ] = sprintf( $format, 'empty' );
			}
		}

		$user_meta = array();

		if ( $this->rcno_ratings_user_is_known() ) {
			if ( $is_comment ) {
				if ( (float) $this->rating === 0.0 ) {
					if ( $this->rcno_current_user() === $c->comment_author ) {
						$classes[] = 'needs-rating';
					}
				}
			}

			$user_meta[] = sprintf( 'data-id="%d"', $post_id );
			if ( $previous_id > 0 ) {
				$user_meta[] = sprintf( 'data-comment-id="%d"', $previous_id );
			}
		}

		if ( $this->rating !== 0.0 ) {
			$stars[0] = sprintf(
				'<div class="star-ratings"><ul data-rating="%01.1f" class="%s" %s>',
				$this->rating,
				implode( ' ', $classes ),
				implode( ' ', $user_meta )
			);
			$stars[6] = '</ul></div>';
		}

		return implode( '', $stars );
	}

	/**
	 * Displays the review rating.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function the_rating( $id = 0 ) {
		echo $this->rate_calculate( $id );
	}

	/**
	 * Displays the comment rating
	 * @return string
	 */
	public function the_comment_rating() {
		global $comment;

		return $this->rate_calculate( $comment->comment_post_ID, true );
	}


	/**
	 * Add the star rating above the displayed comment.
	 *
	 * @param $comment
	 *
	 * @return string|bool
	 */
	public function rcno_display_comment_rating( $comment ) {

		if ( $this->enable_rating && is_singular( 'rcno_review' ) && ! is_comment_feed() ) {
			$out = $this->the_comment_rating();
			$out .= $comment;

			return $out;
		}

		return $comment;
	}

}
