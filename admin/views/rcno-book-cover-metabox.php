<?php
$cover_src    = get_post_meta( $review->ID, 'rcno_reviews_book_cover_src', true );
$cover_alt    = get_post_meta( $review->ID, 'rcno_reviews_book_cover_alt', true );
$cover_title  = get_post_meta( $review->ID, 'rcno_reviews_book_cover_title', true );
$cover_id     = absint( get_post_meta( $review->ID, 'rcno_reviews_book_cover_id', true ) );
$cover_url    = get_post_meta( $review->ID, 'rcno_reviews_book_cover_url', true );
$gr_cover_url = get_post_meta( $review->ID, 'rcno_reviews_gr_cover_url', true );
?>

<div id="rcno-reviews-book-cover-container" class="hidden rcno-reviews-book-cover-container">
	<img src="<?php echo esc_attr( $cover_src ); ?>"
		alt="<?php echo esc_attr( $cover_alt ); ?>"
		title="<?php echo esc_attr( $cover_title ); ?>"
		data-id="<?php echo esc_attr( $cover_id ); ?>"
		style="width: 100%"/>
	<?php if ( Rcno_Reviews_Option::get_option( 'rcno_show_book_cover_url' ) ) { ?>
		<input type="text" id="rcno-reviews-book-cover-url" name="rcno_reviews_book_cover_url"
			value="<?php echo esc_attr( $cover_url ); ?>"
			placeholder="<?php esc_attr_e( 'Custom URL', 'recencio-book-reviews' ); ?>"
			style="width: 100%;"/>
	<?php } ?>
</div>

<p class="hide-if-no-js">
	<a title="<?php esc_attr_e( 'Add Book Cover', 'recencio-book-reviews' ); ?>" href="javascript:" id="rcno-add-book-cover" class="rcno-reviews-book-cover">
		<?php esc_html_e( 'Add Book Cover', 'recencio-book-reviews' ); ?>
	</a>
</p><!-- .hide-if-no-js -->

<p class="hide-if-no-js hidden">
	<a title="<?php esc_attr_e( 'Remove Book Cover', 'recencio-book-reviews' ); ?>" href="javascript:" id="rcno-remove-book-cover">
		<?php esc_html_e( 'Remove Book Cover', 'recencio-book-reviews' ); ?>
	</a>
</p><!-- .hide-if-no-js -->

<input type="hidden" id="rcno-reviews-book-cover-src" name="rcno_reviews_book_cover_src"
	value="<?php echo esc_attr( $cover_src ); ?>"/>
<input type="hidden" id="rcno-reviews-book-cover-alt" name="rcno_reviews_book_cover_alt"
	value="<?php echo esc_attr( $cover_alt ); ?>"/>
<input type="hidden" id="rcno-reviews-book-cover-title" name="rcno_reviews_book_cover_title"
	value="<?php echo esc_attr( $cover_title ); ?>"/>
<input type="hidden" id="rcno-reviews-book-cover-id" name="rcno_reviews_book_cover_id"
	value="<?php echo esc_attr( $cover_id ); ?>"/>

<?php do_action( 'rcno_book_cover_metabox_end' ); ?>
