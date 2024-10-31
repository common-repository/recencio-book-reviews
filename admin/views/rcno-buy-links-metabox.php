<?php

$buy_links = get_post_meta( $review->ID, 'rcno_review_buy_links', true );

$_stores = Rcno_Reviews_Option::get_option( 'rcno_store_purchase_links' );
$_stores = explode( ',', $_stores );

$stores = array();
foreach ( $_stores as $store ) {
	$stores[ sanitize_title( $store ) ] = $store;
}

wp_nonce_field( 'rcno_buy_links_meta_box_nonce', 'rcno_buy_links_nonce' );

?>

<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$('#add-link').on('click', function () {
			var row = $('.empty-buy-links.screen-reader-text').clone(true);
			row.removeClass('empty-buy-links screen-reader-text');
			row.insertBefore('#repeatable-fieldset-two tbody>tr:last');
			return false;
		});

		$('.remove-link').on('click', function () {
			$(this).parents('tr').remove();
			return false;
		});
	});
</script>

<div class="review-buy-links-container">

	<div class="review-buy-links-table" style="width:74%; display:inline-block">

		<table id="repeatable-fieldset-two" width="100%">

			<thead>
			<tr>
				<th width="25%"><?php _e( 'Store', 'recencio-book-reviews' ); ?></th>
				<th width="65%"><?php _e( 'Link', 'recencio-book-reviews' ); ?></th>
				<th width="10%"></th>
			</tr>
			</thead>
			<tbody>
			<?php

			if ( $buy_links ) :

				foreach ( $buy_links as $field ) {
					?>
					<tr>
						<td>
							<select name="store[]" class="buy-links" style="width: 100%">
								<?php foreach ( $stores as $key => $value ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $field['store'] ); ?>><?php echo esc_attr( $value ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>

						<td><input type="text" class="widefat" name="link[]" value="<?php if ( '' !== $field['link'] ) {
								echo esc_html( $field['link'] );
							} ?>"/>
                        </td>
						<td><a class="button remove-link" href="#"><?php _e( 'Remove', 'recencio-book-reviews' ); ?></a></td>
					</tr>
					<?php
				}
			else :
				// show a blank one
				?>
				<tr>
					<td>
						<select name="store[]" title="Store" style="width: 100%">
							<?php foreach ( $stores as $key => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td><input type="text" class="widefat" name="link[]" title="Purchase Link"/></td>
					<td><a class="button remove-link" href="#"><?php _e( 'Remove', 'recencio-book-reviews' ); ?></a></td>
				</tr>
				<tr>
					<td>
						<select name="store[]" title="Store" style="width: 100%">
							<?php foreach ( $stores as $key => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td><input type="text" class="widefat" name="link[]"/></td>
					<td><a class="button remove-link" href="#"><?php _e( 'Remove', 'recencio-book-reviews' ); ?></a></td>
				</tr>
				<tr>
					<td>
						<select name="store[]" title="Store" style="width: 100%">
							<?php foreach ( $stores as $key => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td><input type="text" class="widefat" name="link[]"/></td>
					<td><a class="button remove-link" href="#"><?php _e( 'Remove', 'recencio-book-reviews' ); ?></a></td>
				</tr>
			<?php endif; ?>

			<!-- empty hidden one for jQuery -->
			<tr class="empty-buy-links screen-reader-text">
				<td>
					<select name="store[]" title="Store" style="width: 100%">
						<?php foreach ( $stores as $key => $value ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $value ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td><input type="text" class="widefat" name="link[]"/></td>
				<td><a class="button remove-link" href="#"><?php _e( 'Remove', 'recencio-book-reviews' ); ?></a></td>
			</tr>
			</tbody>

		</table>
		<p style="margin:0;font-size:10px;text-align:right;"><?php _e( 'Select and enter your affiliate purchase links here.', 'recencio-book-reviews' ); ?></p>
		<p><a id="add-link" class="button button-primary" href="#"><?php _e( 'Add New Link', 'recencio-book-reviews' ); ?></a></p>

	</div>

</div>
