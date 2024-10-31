<?php
/*
Author: wzyMedia
Author Mail: kemory@wzymedia.com
Author URL: https://wzymedia.com
Layout Name: Rcno Simple
Version: 1.0.0
Description: The simple book review layout.
*/


// Get the review ID.
if ( isset( $GLOBALS['review_id'] ) && $GLOBALS['review_id'] !== '' ) {
	$review_id = $GLOBALS['review_id'];
} else {
	$review_id = get_post()->ID;
}

$plugin_name = 'recencio-book-reviews';
$version     = '1.0.0';

$template              = new Rcno_Template_Tags( $plugin_name, $version );
$review_score_enable   = (bool) get_post_meta( $review_id, 'rcno_review_score_enable', true );
$review_score_position = get_post_meta( $review_id, 'rcno_review_score_position', true );

?>

<?php $template->the_rcno_review_title( $review_id ) ?>

<?php do_action( 'before_rcno_book_review' ); ?>

<div class="rcno-book-info">
	<div class="review-content">

		<?php if ( true === $review_score_enable && 'top' === $review_score_position ) : ?>

			<div class="review-box-container">
				<?php $template->the_rcno_review_box( $review_id ); ?>
			</div>

			<?php do_action( 'after_rcno_review_score_box' ); ?>

		<?php endif; ?>

		<div class="book-cover-container">
			<div class="book-cover">
				<?php
				// Prints the book cover.
				$template->the_rcno_book_cover( $review_id, 'full' );

				// Prints the book's private 5 star rating.
				$template->the_rcno_admin_book_rating( $review_id );

				do_action( 'after_rcno_book_cover' );
				?>
			</div>
			<div class="book-info">
				<?php
				// Prints the book cover and book's meta data.
				$template->the_rcno_book_meta( $review_id, 'rcno_book_title', 'h2', false );

				$template->the_rcno_taxonomy_terms( $review_id, 'rcno_author', true );
				$template->the_rcno_taxonomy_terms( $review_id, 'rcno_genre', true );
				$template->the_rcno_taxonomy_terms( $review_id, 'rcno_series', true );
				$template->the_rcno_taxonomy_terms( $review_id, 'rcno_publisher', true );

				$template->the_rcno_book_meta( $review_id, 'rcno_book_pub_date', 'p', true );
				$template->the_rcno_book_meta( $review_id, 'rcno_book_pub_format', 'p', true );
				$template->the_rcno_book_meta( $review_id, 'rcno_book_isbn', 'p', true );

				do_action( 'after_rcno_book_information' );
				?>
			</div>
		</div>

		<?php
		// Prints the book review content.
		$template->the_rcno_book_review_content( $review_id );
		do_action( 'after_rcno_review_content' );

		// Prints the book purchase links.
		if ( ! apply_filters( 'rcno_book_purchase_links_in_details', false, $review_id ) ) {
			$template->the_rcno_book_purchase_links( $review_id, true );
		}
		do_action( 'after_rcno_purchase_links' );
		?>

		<?php if ( true === $review_score_enable && 'bottom' === $review_score_position ) : ?>

			<div class="review-box-container">
				<?php $template->the_rcno_review_box( $review_id ); ?>
			</div>

			<?php do_action( 'after_rcno_review_score_box' ); ?>

		<?php endif; ?>

	</div>

	<?php
	// Prints the review and book's metadata in the JSON+LD format content.
	$template->the_rcno_book_schema_data( $review_id );
	$template->the_rcno_review_schema_data( $review_id );
	echo '<!--- Recencio Book Reviews --->';
	?>

</div>

<?php do_action( 'after_rcno_book_review' ); ?>
