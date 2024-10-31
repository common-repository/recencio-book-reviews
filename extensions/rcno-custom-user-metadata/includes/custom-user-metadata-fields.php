<?php
    $active_extensions = Rcno_Reviews_Option::get_option( 'rcno_reviews_active_extensions', array() );
?>
<?php if ( array_key_exists( 'rcno_custom_user_metadata', $active_extensions )
           && '' !== $this->get_setting( 'custom_user_metaboxes' ) ) : ?>
	<?php
		$custom_metadata = explode( ',', $this->get_setting( 'custom_user_metaboxes' ) );
	?>
	<div class="book-general-metabox-2">
		<?php foreach( $custom_metadata as $custom_meta ) : ?>
			<div class="<?php echo 'rcno-' . sanitize_key( $custom_meta ) . '-meta' ?> metabox">
				<label
						for="<?php echo 'rcno_' . sanitize_key( $custom_meta ) . '_meta' ?>"
						class="<?php echo 'rcno_' . sanitize_key( $custom_meta ) . '_meta' ?>"
				>
					<?php echo $custom_meta; ?>
				</label>
				<input
						type="text"
						name="<?php echo 'rcno_' . sanitize_key( $custom_meta ) . '_meta' ?>"
						id="<?php echo 'rcno_' . sanitize_key( $custom_meta ) . '_meta' ?>"
						value="<?php echo get_post_meta( $this->the_review_id(), 'rcno_' . sanitize_key( $custom_meta ) . '_meta', true ); ?>"
						size="20" >

			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>