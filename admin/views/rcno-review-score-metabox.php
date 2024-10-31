<?php
/**
 * @var \WP_Post $recipe The recipe post object.
 */

$review_score_criteria = get_post_meta( $review->ID, 'rcno_review_score_criteria', true );
$review_score_type     = get_post_meta( $review->ID, 'rcno_review_score_type', true );
$review_score_position = get_post_meta( $review->ID, 'rcno_review_score_position', true );
$review_score_enable   = get_post_meta( $review->ID, 'rcno_review_score_enable', true );
$use_custom_criteria   = Rcno_Reviews_Option::get_option( 'rcno_enable_custom_review_score_criteria' );
$list_custom_criteria  = explode( ',', Rcno_Reviews_Option::get_option( 'rcno_custom_review_score_criteria' ) );

wp_nonce_field( 'rcno_repeatable_meta_box_nonce', 'rcno_repeatable_meta_box_nonce' );
?>

<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$('#add-row').on('click', function () {
			var row = $('.empty-row.screen-reader-text').clone(true);
			row.removeClass('empty-row screen-reader-text');
			row.insertBefore('#repeatable-fieldset-one tbody>tr:last');
			return false;
		});

		$('.remove-row').on('click', function () {
			$(this).parents('tr').remove();
			return false;
		});
	});
</script>

<div class="review-score-metabox-container">

	<div class="review-score-options" style="width:25%; display:inline-block">
		<div class="review-score-rating-enable">
			<label for="rcno_review_score_enable"><?php _e( 'Enable Rating Box', 'recencio-book-reviews' ); ?></label>
			<input type="hidden" name="rcno_review_score_enable" value="0">
			<!-- The need for this is weird -->
			<input type="checkbox" id="rcno_review_score_enable" name="rcno_review_score_enable"
				value="1" <?php checked( $review_score_enable, '1', true ); ?>>
		</div>

		<div class="review-score-rating-type">
			<label for="rcno_review_score_type"><?php _e( 'Rating Type', 'recencio-book-reviews' ); ?></label>
			<select id="rcno_review_score_type" name="rcno_review_score_type">
				<option value="number" <?php selected( $review_score_type, 'number' ); ?>>
					<?php esc_html_e( 'Number', 'recencio-book-reviews' ); ?>
				</option>
				<option value="letter" <?php selected( $review_score_type, 'letter' ); ?>>
					<?php esc_html_e( 'Letter', 'recencio-book-reviews' ); ?>
				</option>
				<option value="stars" <?php selected( $review_score_type, 'stars' ); ?>>
					<?php esc_html_e( 'Stars', 'recencio-book-reviews' ); ?>
				</option>
			</select>
		</div>

		<div class="review-score-rating-position">
			<label for="rcno_review_score_position"><?php _e( 'Rating Box Position', 'recencio-book-reviews' ); ?></label>
			<select id="rcno_review_score_position" name="rcno_review_score_position">
				<option value="top" <?php selected( $review_score_position, 'top' ); ?>>
					<?php esc_html_e( 'Top', 'recencio-book-reviews' ); ?>
				</option>
				<option value="bottom" <?php selected( $review_score_position, 'bottom' ); ?>>
					<?php esc_html_e( 'Bottom', 'recencio-book-reviews' ); ?>
				</option>
			</select>
		</div>
	</div>

	<div class="review-score-table" style="width:74%; display:inline-block">

		<table id="repeatable-fieldset-one" width="100%">

			<thead>
			<tr>
				<th width="40%"><?php _e( 'Label', 'recencio-book-reviews' ); ?></th>
				<th width="40%"><?php _e( 'Score', 'recencio-book-reviews' ); ?></th>
				<th width="8%"></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( $review_score_criteria ) : ?>

				<?php if ( $use_custom_criteria && $list_custom_criteria ) : ?>

					<?php foreach ( $review_score_criteria as $field ) : ?>
						<tr>
							<td><input type="text" class="widefat" name="label[]"
									value="<?php echo ( '' !== $field['label'] ) ? esc_attr( $field['label'] ) : ''; ?> " readonly /></td>

							<td><input type="number" min="0" max="5" step="0.1" class="widefat" name="score[]"
									value="<?php echo ( '' !== $field['score'] ) ? esc_attr( $field['score'] ) : ''; ?>"/></td>
						</tr>
					<?php endforeach; ?>

				<?php else : ?>

						<?php foreach ( $review_score_criteria as $field ) : ?>
							<tr>
								<td><input type="text" class="widefat" name="label[]"
										value="<?php echo ( '' !== $field['label'] ) ? esc_attr( $field['label'] ) : ''; ?>"/></td>

								<td><input type="number" min="0" max="5" step="0.1" class="widefat" name="score[]"
										value="<?php echo ( '' !== $field['score'] ) ? esc_attr( $field['score'] ) : ''; ?>"/></td>

								<td><a class="button remove-row" href="#"><?php _e( 'Remove', 'recencio-book-reviews' ); ?></a></td>
							</tr>
						<?php endforeach; ?>

				<?php endif ?>

			<?php elseif ( $use_custom_criteria && $list_custom_criteria ) : ?>

				<?php foreach ( $list_custom_criteria as $criteria ) : ?>
					<tr>
						<td><input type="text" class="widefat" name="label[]"
								value="<?php echo esc_attr( $criteria ); ?>" readonly /></td>

						<td><input type="number" min="0" max="5" step="0.1" class="widefat" name="score[]"
								value="" /></td>

					</tr>
				<?php endforeach; ?>

			<?php else : ?>
				<tr>
					<td><input type="text" class="widefat" name="label[]" placeholder="Plot"/></td>
					<td><input type="number" min="0" max="5" step="0.1" class="widefat" name="score[]"
							placeholder="4.2"/></td>
					<td><a class="button remove-row" href="#"><?php _e( 'Remove', 'recencio-book-reviews' ); ?></a></td>
				</tr>
				<tr>
					<td><input type="text" class="widefat" name="label[]" placeholder="Characters"/></td>
					<td><input type="number" min="0" max="5" step="0.1" class="widefat" name="score[]"
							placeholder="3.5"/></td>
					<td><a class="button remove-row" href="#"><?php _e( 'Remove', 'recencio-book-reviews' ); ?></a></td>
				</tr>
				<tr>
					<td><input type="text" class="widefat" name="label[]" placeholder="Pacing"/></td>
					<td><input type="number" min="0" max="5" step="0.1" class="widefat" name="score[]"
							placeholder="2.8"/></td>
					<td><a class="button remove-row" href="#"><?php _e( 'Remove', 'recencio-book-reviews' ); ?></a></td>
				</tr>
			<?php endif; ?>

			<!-- empty hidden one for jQuery -->
			<tr class="empty-row screen-reader-text">
				<td><input type="text" class="widefat" name="label[]"/></td>
				<td><input type="number" min="0" max="5" step="0.1" class="widefat" name="score[]"/></td>
				<td><a class="button remove-row" href="#"><?php _e( 'Remove', 'recencio-book-reviews' ); ?></a></td>
			</tr>
			</tbody>

		</table>
		<p style="margin:0;font-size:10px;text-align:right;"><?php _e( 'Label: The name of criteria | Score: A number value between 0 - 5, in increments of 0.1', 'recencio-book-reviews' ); ?></p>
		<?php if ( ! $use_custom_criteria ) : ?>
			<p><a id="add-row" class="button button-primary" href="#"><?php _e( 'Add Criteria', 'recencio-book-reviews' ); ?></a></p>
		<?php endif; ?>

	</div>

</div>
