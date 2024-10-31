<?php
/**
 * This class a widget displaying a calendar of book reviews.
 *
 * @link       https://wzymedia.com
 * @since      1.5.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 */

/**
 * This was inspired by a plugin found http://pippinsplugins.com
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Calendar extends WP_Widget {


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.5.0
	 * @version 1.0.0
	 */
	public function __construct() {

		$this->set_widget_options();

		// Create the widget.
		parent::__construct(
			'rcno-reviews-calendar',
			__( 'Rcno Review Calendar', 'recencio-book-reviews' ),
			$this->widget_options,
			$this->control_options
		);
	}

	private function set_widget_options() {

		// Set up the widget options.
		$this->widget_options = array(
			'classname'   => 'rcno-review-calendar',
			'description' => esc_html__( 'A widget displaying a calendar of book reviews', 'recencio-book-reviews' ),
		);

		// Set up the widget control options.
		$this->control_options = array(
			'width'  => 325,
			'height' => 350,
		);

	}

	/**
	 * Register our review calendar widget if enabled in plugin settings.
	 */
	public function rcno_register_review_calendar_widget() {
		if ( Rcno_Reviews_Option::get_option( 'rcno_show_review_calendar_widget' ) ) {
			register_widget( 'Rcno_Reviews_Calendar' );
		}
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		$title            = isset( $instance['title'] ) ? $instance['title'] : '';
		$posttype_enabled = isset( $instance['posttype_enabled'] ) ? $instance['posttype_enabled'] : '';

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
	?>
        <div class="widget_calendar">
            <div id="calendar_wrap">
				<?php
                    if ( ! $posttype_enabled ) {
					    ucc_get_calendar( array( 'rcno_review' ) );
				    } else {
					    ucc_get_calendar( array(), true, true, true  );
                    }
                ?>
            </div>
        </div>
		<?php echo $args['after_widget']; ?>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                     = $old_instance;
		$instance['title']            = strip_tags( $new_instance['title'] );
		$instance['posttype_enabled'] = isset( $new_instance['posttype_enabled'] ) ? $new_instance['posttype_enabled'] : false;
		//$instance['posttype']         = $new_instance['posttype'];

		return $instance;
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$title            = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$posttype_enabled = isset( $instance['posttype_enabled'] ) ? esc_attr( $instance['posttype_enabled'] ) : false;
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>"
                   type="text" value="<?php echo $title; ?>"/>
        </p>
        <p>
            <input id="<?php echo $this->get_field_id( 'posttype_enabled' ); ?>" name="<?php echo $this->get_field_name( 'posttype_enabled' ); ?>"
                   type="checkbox" value="1" <?php checked( '1', $posttype_enabled ); ?>/>
            <label for="<?php echo $this->get_field_id( 'posttype_enabled' ); ?>"><?php _e( 'Show regular posts?', 'recencio-book-reviews' ); ?></label>
        </p>
		<?php
	}
}

/** ucc_get_calendar() :: Extends get_calendar() by including custom post types.
 * Derived from get_calendar() code in /wp-includes/general-template.php.
 *
 * @param array $post_types
 * @param bool  $initial
 * @param bool  $echo
 * @param bool  $regular_posts
 *
 * @return mixed|string|void
 */
function ucc_get_calendar( array $post_types, $initial = true, $echo = true, $regular_posts = false ) {
	global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;
	$custom_post_type = get_post_type_object( 'rcno_review' );

	if ( empty( $post_types ) || ! is_array( $post_types ) ) {
		$args     = array(
			'public'   => true,
			'_builtin' => false,
		);
		$output   = 'names';
		$operator = 'and';

		$post_types = get_post_types( $args, $output, $operator );
		$post_types = array_merge( $post_types, array( 'post' ) );
	} else {
		/* Trust but verify. */
		$my_post_types = array();
		foreach ( $post_types as $post_type ) {
			if ( post_type_exists( $post_type ) ) {
				$my_post_types[] = $post_type;
			}
		}
		$post_types = $my_post_types;
	}
	$post_types_key = implode( '', $post_types );
	$post_types     = "'" . implode( "' , '", $post_types ) . "'";

	$cache = array();
	$key   = md5( $m . $monthnum . $year . $post_types_key );
	if ( $cache = wp_cache_get( 'get_calendar', 'calendar' ) ) {
		if ( is_array( $cache ) && isset( $cache[ $key ] ) ) {
			remove_filter( 'get_calendar', 'ucc_get_calendar_filter' );
			$output = apply_filters( 'get_calendar', $cache[ $key ] );
			add_filter( 'get_calendar', 'ucc_get_calendar_filter' );
			if ( $echo ) {
				echo $output;

				return;
			} else {
				return $output;
			}
		}
	}

	if ( ! is_array( $cache ) ) {
		$cache = array();
	}

	// Quick check. If we have no posts at all, abort!
	if ( ! $posts ) {
		$sql     = "SELECT ID FROM $wpdb->posts WHERE post_type IN ( $post_types ) AND post_status = 'publish' LIMIT 1";
		$gotsome = $wpdb->get_var( $sql );
		if ( ! $gotsome ) {
			$cache[ $key ] = '';
			wp_cache_set( 'get_calendar', $cache, 'calendar' );

			return;
		}
	}

	if ( isset( $_GET[ 'w' ] ) ) {
		$w = '' . (int) $_GET[ 'w' ];
	}

	// week_begins = 0 stands for Sunday
	$week_begins = (int) get_option( 'start_of_week' );

	// Let's figure out when we are
	if ( ! empty( $monthnum ) && ! empty( $year ) ) {
		$thismonth = '' . zeroise( (int) $monthnum, 2 );
		$thisyear  = '' . (int) $year;
	} elseif ( ! empty( $w ) ) {
		// We need to get the month from MySQL
		$thisyear  = '' . (int) substr( $m, 0, 4 );
		$d         = ( ( $w - 1 ) * 7 ) + 6; //it seems MySQL's weeks disagree with PHP's.
		$thismonth = $wpdb->get_var( "SELECT DATE_FORMAT( ( DATE_ADD( '${thisyear}0101' , INTERVAL $d DAY ) ) , '%m' ) " );
	} elseif ( ! empty( $m ) ) {
		$thisyear = '' . (int) substr( $m, 0, 4 );
		if ( strlen( $m ) < 6 ) {
			$thismonth = '01';
		} else {
			$thismonth = '' . zeroise( (int) substr( $m, 4, 2 ), 2 );
		}
	} else {
		$thisyear  = gmdate( 'Y', current_time( 'timestamp' ) );
		$thismonth = gmdate( 'm', current_time( 'timestamp' ) );
	}

	$unixmonth = mktime( 0, 0, 0, $thismonth, 1, $thisyear );

	// Get the next and previous month and year with at least one post
	$previous = $wpdb->get_row( "SELECT DISTINCT MONTH( post_date ) AS month , YEAR( post_date ) AS year
    FROM $wpdb->posts
    WHERE post_date < '$thisyear-$thismonth-01'
    AND post_type IN ( $post_types ) AND post_status = 'publish'
      ORDER BY post_date DESC
      LIMIT 1" );
	$next     = $wpdb->get_row( "SELECT DISTINCT MONTH( post_date ) AS month, YEAR( post_date ) AS year
    FROM $wpdb->posts
    WHERE post_date > '$thisyear-$thismonth-01'
    AND MONTH( post_date ) != MONTH( '$thisyear-$thismonth-01' )
    AND post_type IN ( $post_types ) AND post_status = 'publish'
      ORDER  BY post_date ASC
      LIMIT 1" );

	/* translators: Calendar caption: 1: month name, 2: 4-digit year */
	$calendar_caption = _x( '%1$s %2$s', 'calendar caption' );
	$calendar_output  = '<table id="wp-calendar" summary="' . esc_attr__( 'Calendar' ) . '">
  <caption>' . sprintf( $calendar_caption, $wp_locale->get_month( $thismonth ), date( 'Y', $unixmonth ) ) . '</caption>
  <thead>
  <tr>';

	$myweek = array();

	for ( $wdcount = 0; $wdcount <= 6; $wdcount ++ ) {
		$myweek[] = $wp_locale->get_weekday( ( $wdcount + $week_begins ) % 7 );
	}

	foreach ( $myweek as $wd ) {
		$day_name        = ( true === $initial ) ? $wp_locale->get_weekday_initial( $wd ) : $wp_locale->get_weekday_abbrev( $wd );
		$wd              = esc_attr( $wd );
		$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	$calendar_output .= '
  </tr>
  </thead>

  <tfoot>
  <tr>';


	$next_month_link = '';
	$prev_month_link = '';

	if ( $regular_posts && $previous ) {
		$prev_month_link = get_month_link( $previous->year , $previous->month );
	} elseif ( null !== $custom_post_type && null !== $previous ) {
		$prev_month_link = get_site_url() . '/' . $custom_post_type->has_archive . '/' . $previous->year . '/' . $previous->month . '/';
    }

    if ( $regular_posts && $next ) {
	    $next_month_link = get_month_link( $next->year , $next->month );
    } elseif ( null !== $custom_post_type && null !== $next ) {
	    $next_month_link = get_site_url() . '/' . $custom_post_type->has_archive . '/' . $next->year . '/' . $next->month . '/';
    }

	if ( $previous ) {
		$calendar_output .= "\n\t\t" . '<td colspan="3" id="prev"><a href="' . $prev_month_link . '" title="' . sprintf( __( 'View reviews for %1$s %2$s' ), $wp_locale->get_month( $previous->month ), date( 'Y', mktime( 0, 0, 0, $previous->month, 1, $previous->year ) ) ) . '">&laquo; ' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $previous->month ) ) . '</a></td>';
	} else {
		$calendar_output .= "\n\t\t" . '<td colspan="3" id="prev" class="pad">&nbsp;</td>';
	}

	$calendar_output .= "\n\t\t" . '<td class="pad">&nbsp;</td>';

	if ( $next ) {
		$calendar_output .= "\n\t\t" . '<td colspan="3" id="next"><a href="' . $next_month_link . '" title="' . esc_attr( sprintf( __( 'View reviews for %1$s %2$s' ), $wp_locale->get_month( $next->month ), date( 'Y', mktime( 0, 0, 0, $next->month, 1, $next->year ) ) ) ) . '">' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $next->month ) ) . ' &raquo;</a></td>';
	} else {
		$calendar_output .= "\n\t\t" . '<td colspan="3" id="next" class="pad">&nbsp;</td>';
	}

	$calendar_output .= '
  </tr>
  </tfoot>

  <tbody>
  <tr>';

	// Get days with posts
	$dayswithposts = $wpdb->get_results( "SELECT DISTINCT DAYOFMONTH( post_date )
    FROM $wpdb->posts WHERE MONTH( post_date ) = '$thismonth'
    AND YEAR( post_date ) = '$thisyear'
    AND post_type IN ( $post_types ) AND post_status = 'publish'
    AND post_date < '" . current_time( 'mysql' ) . '\'', ARRAY_N );
	if ( $dayswithposts ) {
		foreach ( (array) $dayswithposts as $daywith ) {
			$daywithpost[] = $daywith[ 0 ];
		}
	} else {
		$daywithpost = array();
	}

	if ( strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'MSIE' ) !== false || stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'camino' ) !== false || stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'safari' ) !== false ) {
		$ak_title_separator = "\n";
	} else {
		$ak_title_separator = ', ';
	}

	$ak_titles_for_day = array();
	$ak_post_titles    = $wpdb->get_results( "SELECT ID, post_title, DAYOFMONTH( post_date ) as dom "
	                                         . "FROM $wpdb->posts "
	                                         . "WHERE YEAR( post_date ) = '$thisyear' "
	                                         . "AND MONTH( post_date ) = '$thismonth' "
	                                         . "AND post_date < '" . current_time( 'mysql' ) . "' "
	                                         . "AND post_type IN ( $post_types ) AND post_status = 'publish'"
	);
	if ( $ak_post_titles ) {
		foreach ( (array) $ak_post_titles as $ak_post_title ) {

			$post_title = esc_attr( apply_filters( 'the_title', $ak_post_title->post_title, $ak_post_title->ID ) );

			if ( empty( $ak_titles_for_day[ 'day_' . $ak_post_title->dom ] ) ) {
				$ak_titles_for_day[ 'day_' . $ak_post_title->dom ] = '';
			}
			if ( empty( $ak_titles_for_day[ "$ak_post_title->dom" ] ) ) // first one
			{
				$ak_titles_for_day[ "$ak_post_title->dom" ] = $post_title;
			} else {
				$ak_titles_for_day[ "$ak_post_title->dom" ] .= $ak_title_separator . $post_title;
			}
		}
	}

	// See how much we should pad in the beginning
	$pad = calendar_week_mod( date( 'w', $unixmonth ) - $week_begins );
	if ( (int) $pad !== 0 ) {
		$calendar_output .= "\n\t\t" . '<td colspan="' . esc_attr( $pad ) . '" class="pad">&nbsp;</td>';
	}

	$daysinmonth = (int) date( 't', $unixmonth );
	for ( $day = 1; $day <= $daysinmonth; ++ $day ) {
		if ( isset( $newrow ) && $newrow ) {
			$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
		}
		$newrow = false;

		if ( $day == gmdate( 'j', current_time( 'timestamp' ) ) && $thismonth == gmdate( 'm', current_time( 'timestamp' ) ) && $thisyear == gmdate( 'Y', current_time( 'timestamp' ) ) ) {
			$calendar_output .= '<td id="today">';
		} else {
			$calendar_output .= '<td>';
		}

		$post_day_link = '';
		if ( $regular_posts ) {
			$post_day_link = get_day_link( $thisyear , $thismonth , $day );
		} elseif ( null !== $custom_post_type && $day ) {
			$post_day_link = get_site_url() . '/' . $custom_post_type->has_archive . '/' . $thisyear . '/' . $thismonth . '/' . $day . '/';
		}

		if ( in_array( $day, $daywithpost, false ) ) { // any posts today?
		    $calendar_output .= '<a href="' . $post_day_link . "\" title=\"" . esc_attr( $ak_titles_for_day[ $day ] ) . "\">$day</a>";
		} else {
			$calendar_output .= $day;
		}
		$calendar_output .= '</td>';

		if ( 6 === (int) calendar_week_mod( date( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins ) ) {
			$newrow = true;
		}
	}

	$pad = 7 - calendar_week_mod( date( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins );
	if ( (int) $pad !== 0 && (int) $pad !== 7 ) {
		$calendar_output .= "\n\t\t" . '<td class="pad" colspan="' . esc_attr( $pad ) . '">&nbsp;</td>';
	}

	$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

	$cache[ $key ] = $calendar_output;
	wp_cache_set( 'get_calendar', $cache, 'calendar' );

	remove_filter( 'get_calendar', 'ucc_get_calendar_filter' );
	$output = apply_filters( 'get_calendar', $calendar_output );
	add_filter( 'get_calendar', 'ucc_get_calendar_filter' );

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

function ucc_get_calendar_filter( $content ) {
	return ucc_get_calendar( array(), '', false );
}

add_filter( 'get_calendar', 'ucc_get_calendar_filter', 10, 2 );
