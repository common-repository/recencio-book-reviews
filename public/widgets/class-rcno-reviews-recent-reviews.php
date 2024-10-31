<?php
/**
 * This class displays a list of recent book reviews.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 */

/**
 * This class displays a list of recent book reviews.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Recent_Reviews extends WP_Widget {

	public $widget_options;
	public $control_options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		$this->set_widget_options();

		// Create the widget.
		parent::__construct(
			'rcno-reviews-recent-reviews',
			__( 'Rcno Recent Reviews', 'recencio-book-reviews' ),
			$this->widget_options,
			$this->control_options
		);

	}

	private function set_widget_options() {

		// Set up the widget options.
		$this->widget_options = array(
			'classname'   => 'recent-reviews',
			'description' => esc_html__( 'A widget to display the most recent book reviews.', 'recencio-book-reviews' ),
		);

		// Set up the widget control options.
		$this->control_options = array(
			'width'  => 325,
			'height' => 350,
		);
	}

	/**
	 * Register our widget, un-register the builtin widget.
	 */
	public function rcno_register_recent_reviews_widget() {
		if ( ! Rcno_Reviews_Option::get_option( 'rcno_show_recent_reviews_widget' ) ) {
			return false;
		}

		register_widget( 'Rcno_Reviews_Recent_Reviews' );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @uses mb_substr()
	 * @see https://stackoverflow.com/questions/9087502/php-substr-function-with-utf-8-leaves-marks-at-the-end
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @since 1.0.0
	 */
	public function widget( $args, $instance ) {

		// If there is an error, stop and return.
		if ( ! empty( $instance['error'] ) ) {
			return;
		}

		// Output the theme's $before_widget wrapper.
		echo $args['before_widget'];

		// Output the title (if we have any).
		if ( isset( $instance['title'] ) ) {
			echo $args['before_title'] . sanitize_text_field( $instance['title'] ) . $args['after_title'];
		}

		// Begin frontend output.
		$out         = '';
		$char_count  = ! empty( $instance['char_count'] ) ? (int) $instance['char_count'] : 150;
		$review_info = ! empty( $instance['review_info'] ) ? $instance['review_info'] : 'truncated';
		$cover_size  = ! empty( $instance['cover_size'] ) ? $instance['cover_size'] : 'rcno-book-cover-sm';
		$query_args  = array(
			'post_type'      => ( ! empty( $instance['regular_posts'] ) ) ? array( 'post', 'rcno_review' ) : 'rcno_review',
			'posts_per_page' => ! empty( $instance['review_count'] ) ? (int) $instance['review_count'] : 5,
		);
		$reviews     = get_posts( $query_args );
		$template    = new Rcno_Template_Tags( 'recencio-book-reviews', '1.0.0' );

		foreach ( $reviews as $review ) {
			$out .= '<div class="rcno-recent-review">';
			$out .= '<div class="widget-book-cover">';
			$out .= '<a href="' . get_the_permalink( $review->ID ) . '">';
			$out .= $template->get_the_rcno_book_cover( $review->ID, $cover_size );
			$out .= '</a>';
			$out .= $template->get_the_rcno_admin_book_rating( $review->ID );
			$out .= '</div>';
			$out .=	'<div class="widget-book-details">';
			$out .= '<a href="' . get_the_permalink( $review->ID ) . '">';
			$out .= '<h3>' . $review->post_title . '</h3>';
			$out .= '</a>';
			$out .= $template->get_the_rcno_taxonomy_terms( $review->ID, 'rcno_author', true );
			$out .= $template->get_the_rcno_book_meta( $review->ID, 'rcno_book_publisher', 'div', true );

			if ( 'synopsis' === $review_info ) {
				if ( apply_filters( 'rcno_recent_reviews_skip_sanitization', false ) ) {
					$content = $template->get_the_rcno_book_description( $review->ID, 200 );
				} else {
					$content = '<p>' . mb_substr( wp_strip_all_tags( strip_shortcodes( $template->get_the_rcno_book_description( $review->ID, 200 ) ), true ), 0, $char_count ) .'</p>';
				}

				$out .= apply_filters( 'rcno_recent_reviews_content', $content, $review->ID );
			}

			if ( 'excerpt' === $review_info ) {
				if ( apply_filters( 'rcno_recent_reviews_skip_sanitization', false ) ) {
					$content = $template->get_the_rcno_book_review_excerpt( $review->ID, 5000 );
				} else {
					$content = '<p>' . mb_substr( wp_strip_all_tags( strip_shortcodes( $template->get_the_rcno_book_review_excerpt( $review->ID, 5000 ) ), true ), 0, $char_count ) . '</p>';
				}

				$out .= apply_filters( 'rcno_recent_reviews_content', $content, $review->ID );
			}

			if ( 'truncated' === $review_info ) {
				if ( apply_filters( 'rcno_recent_reviews_skip_sanitization', false ) ) {
					$content = $template->get_the_rcno_book_review_content( $review->ID );
				} else {
					$content = '<p>' . mb_substr( wp_strip_all_tags( strip_shortcodes( $template->get_the_rcno_book_review_content( $review->ID ) ), true ), 0, $char_count ) . '</p>';
				}

				$out .= apply_filters( 'rcno_recent_reviews_content', $content, $review->ID );
			}

			$out .= '<div class="clear"></div>';
			$out .= '</div>';
			$out .=	'</div>';
		}

		// Print out output.
		echo $out;

		// Close the theme's widget wrapper.
		echo $args['after_widget'];
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param object $new_instance
	 * @param object $old_instance
	 *
	 * @return object
	 */
	public function update( $new_instance, $old_instance ) {

		// Fill current state with old data to be sure we not loose anything
		$instance = $old_instance;

		// Check and sanitize all inputs.
		$instance['title']         = strip_tags( $new_instance['title'] );
		$instance['review_count']  = (int) $new_instance['review_count'];
		$instance['char_count']    = ! empty( $new_instance['char_count'] ) ? (int) $new_instance['char_count'] : 150;
		$instance['regular_posts'] = ! empty( $new_instance['regular_posts'] ) && (bool) $new_instance['regular_posts'];
		$instance['review_info']   = ! empty( $new_instance['review_info'] ) ? $new_instance['review_info'] : 'truncated';
		$instance['cover_size']    = ! empty( $new_instance['cover_size'] ) ? $new_instance['cover_size'] : 'rcno-book-cover-sm';

		// Now we return new values and WordPress do all work for you.
		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 0.8.0
	 *
	 * @param object $instance
	 *
	 * @return void
	 *
	 */
	public function form( $instance ) {
		// Set up the default form values.
		$defaults = array(
			'title'         => '',
			'review_count'  => 5,
			'char_count'    => 150,
			'regular_posts' => false,
			'review_info'   => 'truncated',
            'cover_size'    => 'rcno-book-cover-sm' // 'rcno-book-cover-lg'
		);

		// Merge the user-selected arguments with the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		// Element options.
		$title         = sanitize_text_field( $instance['title'] );
		$review_count  = (int) $instance['review_count'];
		$char_count    = (int) $instance['char_count'];
		$regular_posts = (bool) $instance['regular_posts'];
		$review_info   = array(
			'truncated'  => esc_attr__( 'Review content', 'recencio-book-reviews' ),
			'excerpt' => esc_attr__( 'Review excerpt', 'recencio-book-reviews' ),
			'synopsis'  => esc_attr__( 'Book synopsis', 'recencio-book-reviews' ),
		);
        $cover_size  = array(
	        'rcno-book-cover-sm' => esc_attr__( 'Small', 'recencio-book-reviews' ),
	        'rcno-book-cover-lg' => esc_attr__( 'Large', 'recencio-book-reviews' ),
        );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?> ">
				<?php _e( 'Title (optional)', 'recencio-book-reviews' ); ?>:
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
				name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'review_count' ); ?>">
				<?php _e( 'Number of Reviews', 'recencio-book-reviews' ); ?>:
			</label>
			<input type="number" class="widefat" id="<?php echo $this->get_field_id( 'review_count' ); ?>"
				name="<?php echo $this->get_field_name( 'review_count' ); ?>"
				value="<?php echo esc_attr( $review_count ); ?>"
				style="width:50px;" min="1" max="100" pattern="[0-9]"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'char_count' ); ?>">
				<?php _e( 'Character count', 'recencio-book-reviews' ); ?>:
			</label>
			<input type="number" class="widefat" id="<?php echo $this->get_field_id( 'char_count' ); ?>"
				   name="<?php echo $this->get_field_name( 'char_count' ); ?>"
				   value="<?php echo esc_attr( $char_count ); ?>"
				   style="width:70px;" min="50" max="1000" pattern="[0-9]"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'regular_posts' ); ?>">
				<?php _e( 'Show regular posts', 'recencio-book-reviews' ); ?>:
			</label>
			<input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'regular_posts' ); ?>"
				name="<?php echo $this->get_field_name( 'regular_posts' ); ?>"
				value="1" <?php checked( '1', $regular_posts ); ?> />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'review_info' ); ?>">
				<?php _e( 'Display text', 'recencio-book-reviews' ); ?>:
			</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'review_info' ); ?>"
					name="<?php echo $this->get_field_name( 'review_info' ); ?>" style="width:140px">
				<?php foreach ( $review_info as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['review_info'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>

        <p>
            <label for="<?php echo $this->get_field_id( 'cover_size' ); ?>">
				<?php _e( 'Cover size', 'recencio-book-reviews' ); ?>:
            </label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'cover_size' ); ?>"
                    name="<?php echo $this->get_field_name( 'cover_size' ); ?>" style="width:140px">
				<?php foreach ( $cover_size as $option_value => $option_label ) { ?>
                    <option value="<?php echo $option_value; ?>" <?php selected( $instance['cover_size'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
            </select>
        </p>

		<?php
	}
}
