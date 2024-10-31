<?php
/*
Author: wzyMedia
Author Mail: kemory@wzymedia.com
Author URL: https://wzymedia.com
Layout Name: Rcno Metamor
Version: 1.0.0
Description: The metamorph layout.
*/


// Get the review ID.
if ( isset( $GLOBALS['review_id'] ) && $GLOBALS['review_id'] !== '' ) {
	$review_id = $GLOBALS['review_id'];
} else {
	$review_id = get_post()->ID;
}

$plugin_name = 'recencio-book-reviews';
$version     = '1.0.0';

$review_score_enable   = (bool) get_post_meta( $review_id, 'rcno_review_score_enable', true );
$review_score_position = get_post_meta( $review_id, 'rcno_review_score_position', true );

$template = new Rcno_Template_Tags( $plugin_name, $version );
$ratings  = new Rcno_Reviews_Public_Rating( $plugin_name, $version );

?>

<?php $template->the_rcno_review_title( $review_id ); ?>

<?php do_action( 'before_rcno_book_review' ); ?>

<div class="rcno-metamor-template">
	<div class="rcno-book-cover">
		<?php $template->the_rcno_book_cover( $review_id, 'full' ); ?>
	</div>

	<div class="rcno-book-meta">
		<?php
			$template->the_rcno_book_meta( $review_id, 'rcno_book_title', 'h2', false );
			$template->the_rcno_taxonomy_terms( $review_id, 'rcno_author' );
		?>
		<div>
			<?php
			$template->the_rcno_taxonomy_terms( $review_id, 'rcno_series' );
			$template->the_rcno_book_meta( $review_id, 'rcno_book_series_number', '', false );
			?>
		</div>

		<?php
			$template->the_rcno_taxonomy_terms( $review_id, 'rcno_publisher' );
		?>
		<?php
			$template->the_rcno_taxonomy_terms( $review_id, 'rcno_genre' );
		?>
		<?php
			$template->the_rcno_admin_book_rating( $review_id );
		?>
	</div>
	<div class="rcno-book-synopsis">
		<h3><?php echo apply_filters( 'rcno_metamor_summary', __( 'Summary', 'recencio-book-reviews' ) ); ?></h3>
		<?php
			$template->the_rcno_book_description( $review_id );
		?>
	</div>

	<div class="rcno-book-review">
		<h3><?php echo apply_filters( 'rcno_metamor_summary', __( 'Review', 'recencio-book-reviews' ) ); ?></h3>
		<?php
		$template->the_rcno_book_review_content( $review_id );
		if ( ! apply_filters( 'rcno_book_purchase_links_in_details', false, $review_id ) ) {
			$template->the_rcno_book_purchase_links( $review_id, true );
		}
		$template->the_rcno_book_schema_data( $review_id );
		$template->the_rcno_review_schema_data( $review_id );
		echo '<!--- Recencio Book Reviews --->';
		?>
	</div>
</div>

<?php do_action( 'after_rcno_book_review' ); ?>
