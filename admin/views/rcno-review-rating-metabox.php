<?php
$book_rating = get_post_meta( $review->ID, 'rcno_admin_rating', true );
?>

<div id="rcno-star-rating" class="rcno-star-rating"></div>
<input type="hidden" id="rcno_admin_rating" name="rcno_admin_rating" value="<?php echo ( ! empty( $book_rating ) ? esc_attr( $book_rating ) : '' ); ?> "/>
