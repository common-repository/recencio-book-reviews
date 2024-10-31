<?php
/**
 * This class displays a grid of recent book reviews.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 */

/**
 * This class displays a grid of recent book reviews.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Book_Grid extends WP_Widget {

	public $widget_options;
	public $control_options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function __construct() {

		$this->set_widget_options();

		// Create the widget.
		parent::__construct(
			'rcno-reviews-book-grid',
			__( 'Rcno Book Grid', 'recencio-book-reviews' ),
			$this->widget_options,
			$this->control_options
		);

	}

	private function set_widget_options() {

		// Set up the widget options.
		$this->widget_options = array(
			'classname'   => 'book-grid',
			'description' => esc_html__( 'A widget to display a grid of reviewed books', 'recencio-book-reviews' ),
		);

		// Set up the widget control options.
		$this->control_options = array(
			'width'  => 325,
			'height' => 350,
		);

	}

	/**
	 * Enqueue the necessary scripts and styles if the widget is enabled.
	 */
	public function enqueue_scripts() {

	}

	/**
	 * Register our book slider widget and enqueue the relevant scripts.
	 */
	public function rcno_register_book_grid_widget() {
		if ( (bool) Rcno_Reviews_Option::get_option( 'rcno_show_book_grid_widget' ) ) {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			register_widget( 'Rcno_Reviews_Book_Grid' );
		}
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 0.6.0
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		// If there is an error, stop and return.
		if ( ! empty( $instance['error'] ) ) {
			return;
		}

		// Output the theme's $before_widget wrapper.
		echo $args['before_widget'];

		// Output the title (if we have any).
		if ( $instance && $instance[ 'title' ] ) {
			echo $args['before_title'] . sanitize_text_field( $instance[ 'title' ] ) . $args['after_title'];
		}

		// Begin frontend output.
		$query_args = array(
			'post_type'      => 'rcno_review',
			'posts_per_page' => isset( $instance[ 'review_count' ] ) ? absint( $instance[ 'review_count' ] ) : 9,
			'orderby'        => isset( $instance[ 'order' ] ) ? sanitize_text_field( $instance[ 'order' ] ) : 'date',
		);

		$recent_books = new WP_Query( $query_args );

		if ( $recent_books->have_posts() ) { ?>

			<div class="rcno-book-grid-container">

				<?php while ( $recent_books->have_posts() ) {
					$recent_books->the_post(); ?>

					<?php
					$review_id = get_the_ID();
					$review    = new Rcno_Template_Tags( 'recencio-book-reviews', '1.0.0' );
					?>
					<div class="book-grid-item">
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
							<?php $review->the_rcno_book_cover( $review_id, 'rcno-book-cover-sm' ); ?>
						</a>
					</div>

				<?php } ?>
			</div>

		<?php }

		wp_reset_postdata();

		// Close the theme's widget wrapper.
		echo $args['after_widget'];
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 0.8.0
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
		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['review_count'] = absint( $new_instance['review_count'] );
		$instance['order']        = strip_tags( $new_instance['order'] );

		// and now we return new values and wordpress do all work for you.
		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 0.8.0
	 *
	 * @param object $instance
	 */
	public function form( $instance ) {

		// Set up the default form values.
		$defaults = array(
			'title'        => '',
			'review_count' => 9,
			'order'        => 'rand',
		);

		// Merge the user-selected arguments with the defaults.
		$instance = wp_parse_args( (array) $instance, $defaults );

		// element options.
		$title        = sanitize_text_field( $instance[ 'title' ] );
		$review_count = absint( $instance[ 'review_count' ] );
		$order        = array(
			'title' => esc_attr__( 'Title', 'recencio-book-reviews' ),
			'date'  => esc_attr__( 'Date', 'recencio-book-reviews' ),
			'rand'  => esc_attr__( 'Random', 'recencio-book-reviews' ),
		);

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?> ">
				<?php _e( 'Title (optional)', 'recencio-book-reviews' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
				   name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ) ?>"/>
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
			<label for="<?php echo $this->get_field_id( 'order' ); ?>">
				<?php _e( 'Order', 'recencio-book-reviews' ); ?>:
			</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>"
					name="<?php echo $this->get_field_name( 'order' ); ?>" style="width:100px">
				<?php foreach ( $order as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance[ 'order' ], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>

		<?php
	}
}
