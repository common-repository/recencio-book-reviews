<?php
/**
 * Fetches and add book cover to review
 *
 * @package Rcno_Fetch_Book_Cover
 */

if ( ! class_exists( 'Abstract_Rcno_Extension' ) ) {
	return;
}

/**
 * Class Rcno_Fetch_Book_Cover
 */
class Rcno_Fetch_Book_Cover extends Abstract_Rcno_Extension {

	/**
	 * Rcno_Fetch_Book_Cover constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id       = 'rcno_fetch_book_cover';
		$this->image    = plugin_dir_url( __FILE__ ) . 'assets/images/rcno-fetch-book-cover.png';
		$this->title    = __( 'Fetch Book Cover', 'recencio-book-reviews' );
		$this->desc     = __( 'Automatically downloads and attach a book cover to a book review.', 'recencio-book-reviews' );
		$this->settings = false;
	}

	/**
	 * The post ID of the current review we are attaching to.
	 *
	 * @return int
	 */
	protected function the_review_id() {
		return ! empty( $GLOBALS['review_id'] ) ? (int) $GLOBALS['review_id'] : get_the_ID();
	}

	/**
	 * All methods that we want to be called by the Rcno_Reviews_Extensions class goes here.
	 */
	public function load() {
		$this->add_filters();
		$this->add_actions();
	}

	/**
	 * Add WordPress filters are called here.
	 */
	private function add_filters() {}

	/**
	 * Add WordPress actions are called here.
	 */
	private function add_actions() {
		add_action( 'rcno_book_cover_metabox_end', array( $this, 'add_new_book_cover_input' ) );
		add_action( 'rcno_save_admin_book_cover_metadata', array( $this, 'save_and_attach_cover_data' ) );
	}

	/**
	 * Adds a hidden input to the book cover metabox
	 */
	public function add_new_book_cover_input() {
		$gr_cover_url = get_post_meta( $this->the_review_id(), 'rcno_reviews_gr_cover_url', true );
		echo '<input type="hidden" class="rcno-fetch-book-cover-ext" id="gr-book-cover-url" name="rcno_reviews_gr_cover_url" value="' . esc_attr( $gr_cover_url ) . '"/>';
	}

	/**
	 * Fetches, saves and attach the book cover to our review.
	 */
	public function save_and_attach_cover_data() {

		$data      = $_POST; // phpcs:ignore
		$review_id = $this->the_review_id();

		if ( isset( $data['rcno_reviews_gr_cover_url'] ) ) {

			$new_url = $data['rcno_reviews_gr_cover_url'];
			$old_url = get_post_meta( $review_id, 'rcno_reviews_gr_cover_url', true );

			// If the API returns with a `nophoto` exit early.
			if ( false !== stripos( $data['rcno_reviews_gr_cover_url'], 'nophoto' ) ) {
				return false;
			}

			if ( $old_url === $new_url ) {
				return false;
			}

			if ( '' === $new_url ) {
				return delete_post_meta( $review_id, 'rcno_reviews_gr_cover_url' );
			}

			// Fetch and Store the Image.
			$_filename = ! empty( $data['rcno_book_title'] ) ? $data['rcno_book_title'] : $review_id . '-' . $data['rcno_book_isbn'];
			$image_url = esc_url_raw( $data['rcno_reviews_gr_cover_url'] );
			$file_name = sanitize_file_name( strtolower( $_filename ) ) . '.' . pathinfo( basename( $image_url ), PATHINFO_EXTENSION );
			$get       = wp_remote_get( $image_url, array( 'user-agent' => 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36' ) );
			$type      = wp_remote_retrieve_header( $get, 'content-type' );
			$mirror    = wp_upload_bits( $file_name, null, wp_remote_retrieve_body( $get ) );

			if ( false !== $mirror['error'] ) {
				return false;
			}

			// Attachment options.
			$attachment = array(
				'post_title'     => sanitize_text_field( $data['rcno_book_title'] ),
				'post_mime_type' => $type,
			);

			// Add the image to your media library and set as featured image.
			$attach_id = wp_insert_attachment( $attachment, $mirror['file'], $review_id );
			if ( 0 === $attach_id || is_wp_error( $attach_id ) ) {
				return false;
			}
			$attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );
			wp_update_attachment_metadata( $attach_id, $attach_data );
			update_post_meta( $review_id, 'rcno_reviews_gr_cover_url', $image_url );
			update_post_meta( $review_id, 'rcno_reviews_book_cover_src', $mirror['url'] );
			update_post_meta( $review_id, 'rcno_reviews_book_cover_id', $attach_id );
		}
	}

}
