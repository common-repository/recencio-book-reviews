<?php
/**
 * The general book info metabox view of the plugin.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin/views
 */
?>

<div class="book-general-info-metaboxes">

	<div class="book-general-metabox-1">

		<?php if ( true === true ) : ?>
			<div class="book-title metabox">
				<?php $book_title = get_post_meta( $review->ID, 'rcno_book_title', true ); ?>
				<label class="rcno_book_title_label"
					   for="rcno_book_title"><?php esc_attr_e( 'Title', 'recencio-book-reviews' ) ?></label>
				<input type="text" name="rcno_book_title" id="rcno_book_title"
					   value="<?php echo esc_attr( $book_title ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_title_metadata', 'rcno_general_title_nonce' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_illustrator' ) ) : ?>
			<div class="illustrator-info metabox">
				<?php $illustrator = get_post_meta( $review->ID, 'rcno_book_illustrator', true ); ?>
				<label class="rcno_book_illustrator_label"
					   for="rcno_book_illustrator"><?php esc_attr_e( 'Illustrator', 'recencio-book-reviews' ) ?></label>
				<input type="text" name="rcno_book_illustrator" id="rcno_book_illustrator" size="20"
					   value="<?php echo esc_attr( $illustrator ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_illustrator_metadata', 'rcno_general_illustrator_nonce' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_pub_date' ) ) : ?>
			<div class="publication-date metabox">
				<?php $pub_date = get_post_meta( $review->ID, 'rcno_book_pub_date', true ); ?>
				<label class="rcno_book_publication_date"
					   for="rcno_book_pub_date"><?php esc_attr_e( 'Publication Date', 'recencio-book-reviews' ) ?></label>
				<input type="text" name="rcno_book_pub_date" id="rcno_book_pub_date" size="20"
					   value="<?php echo esc_attr( $pub_date ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_pub_date_metadata', 'rcno_general_pub_date_nonce' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_pub_format' ) ) : ?>
			<div class="publication-format metabox">
				<?php $pub_format = get_post_meta( $review->ID, 'rcno_book_pub_format', true ); ?>
				<label class="rcno_book_publication_format"
					   for="rcno_book_pub_format"><?php esc_attr_e( 'Publication Format', 'recencio-book-reviews' ) ?></label>
				<input type="text" name="rcno_book_pub_format" id="rcno_book_pub_format" size="20"
					   value="<?php echo esc_attr( $pub_format ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_pub_format_metadata', 'rcno_general_pub_format_nonce' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_pub_edition' ) ) : ?>
			<div class="publication-edition metabox">
				<?php $pub_edition = get_post_meta( $review->ID, 'rcno_book_pub_edition', true ); ?>
				<label class="rcno_book_publication_edition"
					   for="rcno_book_pub_edition"><?php esc_attr_e( 'Edition', 'recencio-book-reviews' ) ?></label>
				<input type="text" name="rcno_book_pub_edition" id="rcno_book_pub_edition" size="20"
					   value="<?php echo esc_attr( $pub_edition ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_pub_edition_metadata', 'rcno_general_pub_edition_nonce' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_series_number' ) ) : ?>
            <div class="publication-series-number metabox">
				<?php $series_number = get_post_meta( $review->ID, 'rcno_book_series_number', true ); ?>
                <label class="rcno_book_series_number"
                       for="rcno_book_series_number"><?php esc_attr_e( 'Series Number', 'recencio-book-reviews' ) ?></label>
                <input type="text" name="rcno_book_series_number" id="rcno_book_series_number" size="20"
                       value="<?php echo esc_attr( $series_number ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_series_number_metadata', 'rcno_general_series_number_nonce' ); ?>
            </div>
		<?php endif; ?>

		<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_page_count' ) ) : ?>
			<div class="publication-page-count metabox">
				<?php $page_count = get_post_meta( $review->ID, 'rcno_book_page_count', true ); ?>
				<label class="rcno_book_page_count"
					   for="rcno_book_page_count"><?php esc_attr_e( 'Page Count', 'recencio-book-reviews' ) ?></label>
				<input type="text" name="rcno_book_page_count" id="rcno_book_page_count" size="20"
					   value="<?php echo esc_attr( $page_count ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_page_count_metadata', 'rcno_general_page_count_nonce' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_gr_id' ) ) : ?>
			<div class="publication-gr-id metabox">
				<?php $gr_id = get_post_meta( $review->ID, 'rcno_book_gr_id', true ); ?>
				<label class="rcno_book_gr_id"
					   for="rcno_book_gr_id"><?php esc_attr_e( 'GoodReads ID', 'recencio-book-reviews' ) ?></label>
				<input type="text" name="rcno_book_gr_id" id="rcno_book_gr_id" size="20"
					   value="<?php echo esc_attr( $gr_id ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_gr_id_metadata', 'rcno_general_gr_id_nonce' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_isbn13' ) ) : ?>
			<div class="publication-isbn13 metabox">
				<?php $isbn13 = get_post_meta( $review->ID, 'rcno_book_isbn13', true ); ?>
				<label class="rcno_book_isbn13" for="rcno_book_isbn13"><?php esc_attr_e( 'ISBN13', 'recencio-book-reviews' ) ?></label>
				<input type="text" name="rcno_book_isbn13" id="rcno_book_isbn13" size="20"
					   value="<?php echo esc_attr( $isbn13 ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_isbn13_metadata', 'rcno_general_isbn13_nonce' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_asin' ) ) : ?>
			<div class="publication-asin metabox">
				<?php $asin = get_post_meta( $review->ID, 'rcno_book_asin', true ); ?>
				<label class="rcno_book_asin" for="rcno_book_asin"><?php esc_attr_e( 'ASIN', 'recencio-book-reviews' ) ?></label>
				<input type="text" name="rcno_book_asin" id="rcno_book_asin" size="20"
					   value="<?php echo esc_attr( $asin ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_asin_metadata', 'rcno_general_asin_nonce' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_gr_rating' ) ) : ?>
			<div class="publication-gr-review metabox">
				<?php $gr_review = get_post_meta( $review->ID, 'rcno_book_gr_review', true ); ?>
				<label class="rcno_book_gr_review"
					   for="rcno_book_gr_review"><?php esc_attr_e( 'Average', 'recencio-book-reviews' ) ?></label>
				<input type="text" name="rcno_book_gr_review" id="rcno_book_gr_review" size="20"
					   value="<?php echo esc_attr( $gr_review ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_gr_review_metadata', 'rcno_general_gr_review_nonce' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_gr_url' ) ) : ?>
			<div class="publication-gr-url metabox">
				<?php $gr_url = get_post_meta( $review->ID, 'rcno_book_gr_url', true ); ?>
				<label class="rcno_book_gr_url"
					   for="rcno_book_gr_url"><?php esc_attr_e( 'Book URL', 'recencio-book-reviews' ) ?></label>
				<input type="text" name="rcno_book_gr_url" id="rcno_book_gr_url" size="20"
					   value="<?php echo esc_attr( $gr_url ); ?>"/>
				<?php wp_nonce_field( 'rcno_save_book_gr_url_metadata', 'rcno_general_gr_url_nonce' ); ?>
			</div>
		<?php endif; ?>

	</div>

    <?php do_action( 'rcno_after_general_info_metabox' ) ?>

</div>
