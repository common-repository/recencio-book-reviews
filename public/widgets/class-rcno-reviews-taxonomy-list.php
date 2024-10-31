<?php
/**
 * This class replaces the builtin WP Taxonomy List widget.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 */

/**
 * This class replaces the builtin WP Tag Cloud widget.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Taxonomy_List extends WP_Widget {

	public $widget_options;
	public $control_options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 */
	public function __construct() {

		$this->set_widget_options();

		// Create the widget.
		parent::__construct(
			'rcno-reviews-taxonomy-list',
			__( 'Rcno Taxonomy List', 'recencio-book-reviews' ),
			$this->widget_options,
			$this->control_options
		);

	}

	private function set_widget_options() {

		// Set up the widget options.
		$this->widget_options = array(
			'classname'   => 'taxonomy_list',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your book review taxonomies.', 'recencio-book-reviews' )
		);

		// Set up the widget control options.
		$this->control_options = array(
			'width'  => 325,
			'height' => 350
		);

	}

	/**
	 * Register our widget, un-register the builtin widget.
	 */
	public function rcno_register_taxonomy_list_widget() {
		if ( false === (bool) Rcno_Reviews_Option::get_option( 'rcno_show_taxonomy_list_widget' ) ) {
			return false;
		}
		register_widget( 'Rcno_Reviews_Taxonomy_List' );

		return true;
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {

		// If there is an error, stop and return.
		if ( ! empty( $instance['error'] ) ) {
			return;
		}

		/* Output the theme's $before_widget wrapper. */
		echo $args['before_widget'];

		/**
		 * Output the title (if we have any)
		 */
		if ( $instance['title'] ) {
			echo $args['before_title'] . sanitize_text_field( $instance['title'] ) . $args['after_title'];
		}

		if ( empty( $instance['taxonomy'] ) ) {
			return;
		}

		/**
		 * Put together the list of terms
		 */
		$terms = get_terms(
			$instance['taxonomy'],
			array(
				'orderby'    => $instance['order_by'],
				'order'      => $instance['order'],
				'hide_empty' => $instance['hide_empty'],
				'number'     => $instance['item_count'],
			)
		);

		if ( ! is_wp_error( $terms ) && count( $terms ) > 0 ) {
			echo '<ul class="taglist">';
			foreach ( $terms as $term ) {
				echo '<li>';
				echo '<a href="' . esc_url_raw( get_term_link( $term, $instance['taxonomy'] ) ) . '">';
				echo $term->name;

				if ( true === $instance['show_count'] ) {
					echo ' ';
					echo sanitize_text_field( $instance['before_count'] );
					echo sanitize_text_field( $term->count );
					echo sanitize_text_field( $instance['after_count'] );
				}
				echo '</a>';
				echo '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p class="tag-list tag-list-warning">' . __( 'No terms found', 'recencio-book-reviews' ) . '</p>';
		}

		/**
		 *  Close the theme's widget wrapper.
		 */
		echo $args['after_widget'];
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		// Fill current state with old data to be sure we not loose anything
		$instance = $old_instance;

		// Set the instance to the new instance.
		//$instance = $new_instance;

		// Check and sanitize all inputs.
		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['taxonomy']     = strip_tags( $new_instance['taxonomy'] );
		$instance['item_count']   = absint( $new_instance['item_count'] );
		$instance['order_by']     = strip_tags( $new_instance['order_by'] );
		$instance['order']        = strip_tags( $new_instance['order'] );
		$instance['show_count']   = isset( $new_instance['show_count'] ) ? (bool) $new_instance['show_count'] : false;
		$instance['before_count'] = strip_tags( $new_instance['before_count'] );
		$instance['after_count']  = strip_tags( $new_instance['after_count'] );
		$instance['hide_empty']   = isset( $new_instance['hide_empty'] ) ? (bool) $new_instance['hide_empty'] : false;
		//$instance['exclude']        = trim( $new_instance['exclude'] );

		// and now we return new values and wordpress do all work for you
		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @param array $instance Current settings.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		// Set up the default form values.
		$defaults = array(
			'title'        => '',
			'taxonomy'     => 'rcno_genre',
			'item_count'   => 0,
			'order_by'     => 'name',
			'order'        => 'ASC',
			'show_count'   => false,
			'before_count' => ' ( ',
			'after_count'  => ' ) ',
			'hide_empty'   => false,
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		/* element options. */
		$title        = sanitize_text_field( $instance['title'] );
		$taxonomy     = sanitize_key( $instance['taxonomy'] );
		$item_count   = sanitize_text_field( $instance['item_count'] );
		$order_by     = sanitize_key( $instance['order_by'] );
		$order        = sanitize_sql_orderby( $instance['order'] );
		$show_count   = isset( $instance['show_count'] ) ? (bool) $instance['show_count'] : false;
		$before_count = sanitize_text_field( $instance['before_count'] );
		$after_count  = sanitize_text_field( $instance['after_count'] );
		$hide_empty   = isset( $instance['hide_empty'] ) ? (bool) $instance['hide_empty'] : false;
		$taxonomies   = get_taxonomies( array( 'show_tagcloud' => true, '_builtin' => false ), 'objects' );
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?> ">
				<?php _e( 'Title (optional)', 'recencio-book-reviews' ); ?>
            </label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ) ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>">
				<?php _e( 'Taxonomy to display', 'recencio-book-reviews' ); ?>
            </label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>"
                    name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" size="4" multiple="false">
				<?php foreach ( $taxonomies as $tax ) { ?>
                    <option value="<?php echo $tax->name; ?>" <?php selected( in_array( $tax->name, (array) $taxonomy, true ) ); ?>><?php echo $tax->labels->singular_name; ?></option>
				<?php } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'item_count' ); ?>">
				<?php _e( 'Displayed taxonomy count:', 'recencio-book-reviews' ); ?>
            </label>
            <input type="number" class="widefat" id="<?php echo $this->get_field_id( 'item_count' ); ?>"
                   name="<?php echo $this->get_field_name( 'item_count' ); ?>"
                   value="<?php echo esc_attr( $item_count ); ?>"
                   style="width:50px;" min="1" max="100"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'order_by' ); ?>">
				<?php _e( 'Order items by', 'recencio-book-reviews' ); ?>
            </label>
            <select id="<?php echo $this->get_field_id( 'order_by' ); ?>"
                    name="<?php echo $this->get_field_name( 'order_by' ); ?>" class="widefat" style="width:100px;">
                <option value="name" <?php echo selected( $order_by, 'name', false ) ?> >
					<?php _e( 'Name', 'recencio-book-reviews' ); ?>
                </option>
                <option value="count" <?php echo selected( $order_by, 'count', false ); ?> >
					<?php _e( 'Count', 'recencio-book-reviews' ); ?>
                </option>
            </select>
            <label for="<?php echo $this->get_field_id( 'order' ); ?>">
            </label>
            <select id="<?php echo $this->get_field_id( 'order' ); ?>"
                    name="<?php echo $this->get_field_name( 'order' ); ?>"
                    class="widefat" style="width:100px;">'
                <option value="asc" <?php echo selected( $order, 'asc', false ); ?> >
					<?php echo 'ASC'; ?>
                </option>
                <option value="desc" <?php echo selected( $order, 'desc', false ); ?> >
					<?php echo 'DESC'; ?>
                </option>
            </select>
        </p>
        <p>
            <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'show_count' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_count' ); ?>" <?php checked( $show_count ); ?> />
            <label for="<?php echo $this->get_field_id( 'show_count' ); ?>">
				<?php _e( 'Show count with', 'recencio-book-reviews' ); ?>
            </label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'before_count' ); ?>"
                   name="<?php echo $this->get_field_name( 'before_count' ); ?>"
                   value="<?php echo esc_attr( $before_count ); ?>" style="width:20px;"/>
            <label for="<?php echo $this->get_field_id( 'before_count' ); ?>">
				<?php _e( 'before and', 'recencio-book-reviews' ); ?>
            </label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'after_count' ); ?>"
                   name="<?php echo $this->get_field_name( 'after_count' ); ?>"
                   value="<?php echo esc_attr( $after_count ); ?>" style="width:20px;"/>
            <label for="<?php echo $this->get_field_id( 'after_count' ); ?>">
				<?php _e( 'behind', 'recencio-book-reviews' ); ?>
            </label>
        </p>
        <p>
            <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'hide_empty' ); ?>"
                   name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" <?php checked( $hide_empty ); ?> />
            <label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>">
				<?php _e( 'Hide empty terms?', 'recencio-book-reviews' ); ?>
            </label>
        </p>
		<?php
	}
}
