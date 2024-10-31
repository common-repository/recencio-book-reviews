<?php
/**
 * The ISBN metabox view of the plugin.
 *
 * @link       https://wzymedia.com
 * @since      1.0.0
 *
 * @var \WP_Post $review
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin/views
 */


$isbn         = get_post_meta( $review->ID, 'rcno_book_isbn', true );
$external_api = Rcno_Reviews_Option::get_option( 'rcno_external_book_api', '' );

if ( 'google-books' === $external_api ) {
	$external_api_name = 'Google Books®';
} else {
	$external_api_name = 'Goodreads®';
}

?>

<div class="book-isbn-metabox">
    <input type="text" name="rcno_book_isbn" id="rcno_book_isbn" size="20"
           value="<?php echo sanitize_text_field( $isbn ); ?>"/>
	<?php if ( 'no-3rd-party' !== $external_api ) : ?>
        <button href="#" class="button rcno-isbn-fetch <?php echo $external_api; ?>" title="">Fetch <span class="rcno-ajax-loading"></span></button>
	<?php endif; ?>
	<?php wp_nonce_field( 'rcno_save_book_isbn_metadata', 'rcno_isbn_nonce' ); ?>
    <div class="book-isbn-metabox-error">
        <p><?php printf( __( 'Your book was not found on %s', 'recencio-book-reviews' ), $external_api_name ); ?></p>
    </div>
</div>
