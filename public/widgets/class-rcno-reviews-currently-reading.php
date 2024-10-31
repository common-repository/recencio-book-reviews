<?php
/**
 * This class displays the currently reading book on the site's frontend.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 */

/**
 * This class displays the currently reading book.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/public
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Currently_Reading extends \WP_Widget {

	public $widget_options;
	public $control_options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.1.10
	 */
	public function __construct() {

		$this->set_widget_options();

		// Create the widget.
		parent::__construct(
			'rcno-reviews-currently-reading',
			__( 'Rcno Currently Reading', 'recencio-book-reviews' ),
			$this->widget_options,
			$this->control_options
		);
	}

	private function set_widget_options() {

		// Set up the widget options.
		$this->widget_options = array(
			'classname'   => 'current-reading',
			'description' => esc_html__( 'A widget to display your currently reading progress', 'recencio-book-reviews' ),
		);

		// Set up the widget control options.
		$this->control_options = array(
			'width'  => 325,
			'height' => 350,
		);
	}

	/**
	 * Enqueue the necessary scripts and styles if the widget is enabled.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! is_active_widget( false, false, $this->id_base, true ) ) {
		    return;
        }

		wp_enqueue_script( 'rcno-vuejs' );
		wp_enqueue_script( 'rcno-currently-reading', RCNO_PLUGIN_URI . 'public/js/rcno-currently-reading.js', array( 'rcno-vuejs' ), '1.0.0', true );
		wp_localize_script( 'rcno-currently-reading', 'rcno_currently_reading', array(
			'nonce'     => wp_create_nonce( 'wp_rest' ),
			'completed' => __( 'completed', 'recencio-book-reviews' ),
		) );
	}

	/**
	 * Register our currently widget and enqueue the relevant scripts.
	 */
	public function rcno_register_currently_reading_widget() {
		if ( ! Rcno_Reviews_Option::get_option( 'rcno_show_currently_reading_widget' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		register_widget( 'Rcno_Reviews_Currently_Reading' );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 1.1.10
	 *
	 * @param   array   $args
	 * @param   array   $instance
	 * @return  void
	 */
	public function widget( $args, $instance ) {

		// If there is an error, stop and return.
		if ( ! empty( $instance['error'] ) ) {
			return;
		}

		// Output the theme's $before_widget wrapper.
		echo $args['before_widget'];

		// Output the title (if we have any).
		if ( $instance && $instance['title'] ) {
			echo $args['before_title'] . sanitize_text_field( $instance['title'] ) . $args['after_title'];
		}

		$progress    = get_option( 'rcno_currently_reading', array() );
		$most_recent = end( $progress );
		$percentage  = ( isset( $most_recent['num_of_pages'] ) && $most_recent['num_of_pages'] !== 0 )
			? round( ( $most_recent['current_page'] / $most_recent['num_of_pages'] ) * 100 ) : 0;
		?>

		<?php if ( $most_recent && $most_recent['book_title'] && $most_recent['book_author'] ) : ?>

			<div id="currently-reading">
				<currently-reading>
					<div class="rcno-currently-loading">
						<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
					</div>
				</currently-reading>
			</div>

			<template id="reading-template">
				<div id="rcno-currently-reading" :data-source="data_source">
					<div class="rcno-currently-reading-widget-fe">
						<div class="book-cover" :title="completed">
							<?php if ( $most_recent['book_cover'] ) : ?>
								<div class="progress-bar-container">
									<img src="<?php echo $most_recent['book_cover']; ?>" alt="book-cover" />
									<div v-if="is_loading" class="progress-bar foo" style="width: <?php echo $percentage; ?>%"></div>
									<div v-else class="progress-bar" :style="{ width: percentage + '%' }"></div>
								</div>
							<?php else : ?>
								<div class="progress-bar-container">
									<div style="min-height: 100px; background-color: #ecf7f9"></div>
									<div v-if="is_loading" class="progress-bar" style="width: <?php echo $percentage; ?>%"></div>
									<div v-else class="progress-bar" :style="{ width: percentage + '%'}"></div>
								</div>
							<?php endif; ?>
							<span v-if="is_loading"><?php echo sprintf( '%s/%s', $most_recent['current_page'], $most_recent['num_of_pages'] ); ?></span>
							<span v-else>{{ all_updates[curr_index].current_page + '/' + all_updates[curr_index].num_of_pages }}</span>
						</div>
						<div class="book-progress">
							<h3 class="book-title"><?php echo $most_recent['book_title']; ?></h3>
							<p class="book-author"><?php echo sprintf( '%s %s', __( 'by', 'recencio-book-reviews' ), $most_recent['book_author'] ); ?></p>

							<p v-if="is_loading" class="book-comment" key="default"><?php echo $most_recent['progress_comment']; ?></p>
							<p v-else class="book-comment" key="update">{{ all_updates[curr_index].progress_comment }}</p>
						</div>
					</div>
					<div class="book-progress-btn">
						<span :disabled="curr_index == 0" @click="previous"><?php _e( 'Previous', 'recencio-book-reviews' ); ?></span> <> <span :disabled="curr_index == all_updates.length - 1" @click="next"><?php _e( 'Next', 'recencio-book-reviews' ); ?></span>
					</div>
					<div class="rcno-review-coming-soon">
						<p><?php echo sanitize_text_field( $instance['review_coming_soon'] ); ?></p>
					</div>
				</div>				
			</template>

			<style>
				.book-progress-btn span {
						cursor: pointer;
				}
				.book-progress-btn span[disabled] {
						color: #ccc;
				}
				.rcno-currently-loading {
					width: 100%;
					min-height: 150px;
					display: flex;
					justify-content: center;
					align-items: center;
				}
				.lds-ellipsis {
					display: inline-block;
					position: relative;
					width: 64px;
					height: 64px;
				}
				.lds-ellipsis div {
					position: absolute;
					top: 27px;
					width: 11px;
					height: 11px;
					border-radius: 50%;
					background: #f1f1f1;
					animation-timing-function: cubic-bezier(0, 1, 1, 0);
				}
				.lds-ellipsis div:nth-child(1) {
					left: 6px;
					animation: lds-ellipsis1 0.6s infinite;
				}
				.lds-ellipsis div:nth-child(2) {
					left: 6px;
					animation: lds-ellipsis2 0.6s infinite;
				}
				.lds-ellipsis div:nth-child(3) {
					left: 26px;
					animation: lds-ellipsis2 0.6s infinite;
				}
				.lds-ellipsis div:nth-child(4) {
					left: 45px;
					animation: lds-ellipsis3 0.6s infinite;
				}
				@keyframes lds-ellipsis1 {
					0% {
						transform: scale(0);
					}
					100% {
						transform: scale(1);
					}
				}
				@keyframes lds-ellipsis3 {
					0% {
						transform: scale(1);
					}
					100% {
						transform: scale(0);
					}
				}
				@keyframes lds-ellipsis2 {
					0% {
						transform: translate(0, 0);
					}
					100% {
						transform: translate(19px, 0);
					}
				}
			</style>

		<?php else : ?>

			<?php if ( isset( $instance['no_currently_reading'] ) ) : ?>
				<p><?php echo sanitize_text_field( $instance['no_currently_reading'] ); ?></p>
			<?php endif; ?>

		<?php endif; ?>

		<?php
		echo $args['after_widget']; // Close the theme's widget wrapper.
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 1.1.10
	 *
	 * @param   array $old_instance
	 * @param   array $new_instance
	 * @return  array
	 */
	public function update( $new_instance, $old_instance ) {
		// Fill current state with old data to be sure we not loose anything
		$instance = $old_instance;

		// Check and sanitize all inputs.
		$instance['title']                = strip_tags( $new_instance['title'] );
		$instance['no_currently_reading'] = strip_tags( $new_instance['no_currently_reading'] );
		$instance['review_coming_soon']   = strip_tags( $new_instance['review_coming_soon'] );

		// and now we return new values and WordPress do all work for you.
		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 1.1.10
	 *
	 * @param   array $instance
	 * @return  void
	 */
	public function form( $instance ) {

		// Element options.
		$title                = ! empty( $instance['title'] ) ? sanitize_text_field( $instance['title'] ) : '';
		$no_currently_reading = ! empty( $instance['no_currently_reading'] ) ? sanitize_text_field( $instance['no_currently_reading'] ) : esc_html__( 'No currently reading book right now.', 'recencio-book-reviews' );
		$review_coming_soon   = ! empty( $instance['review_coming_soon'] ) ? sanitize_text_field( $instance['review_coming_soon'] ) : esc_html__( 'Review coming soon!', 'recencio-book-reviews' );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?> ">
				<?php _e( 'Title (optional)', 'recencio-book-reviews' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'no_currently_reading' ); ?> ">
				<?php _e( 'No currently reading book', 'recencio-book-reviews' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'no_currently_reading' ); ?>" name="<?php echo $this->get_field_name( 'no_currently_reading' ); ?>" value="<?php echo esc_attr( $no_currently_reading ); ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'review_coming_soon' ); ?> ">
				<?php _e( 'Review coming soon', 'recencio-book-reviews' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'review_coming_soon' ); ?>" name="<?php echo $this->get_field_name( 'review_coming_soon' ); ?>" value="<?php echo esc_attr( $review_coming_soon ); ?>"/>
		</p>

		<?php
	}
}
