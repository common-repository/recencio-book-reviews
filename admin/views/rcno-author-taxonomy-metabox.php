<?php
	$term_id = 'rcno_author' === $taxonomy ? null : $taxonomy->term_id;
	$author_url = get_term_meta( $term_id, 'rcno_author_taxonomy_url', true );
?>

<div class="rcno-taxonomy-metabox form-field">
	<label for="rcno_author_taxonomy_url"><?php esc_html_e( 'Author\'s URL', 'recencio-book-reviews' ); ?></label>
	<input type="text" name="rcno_author_taxonomy_url" id="rcno_author_taxonomy_url" size="20"
		   value="<?php echo esc_url( $author_url ); ?>"/>
	<?php wp_nonce_field( 'rcno_save_author_taxonomy', 'rcno_aut_tax_url_nonce' ); ?>
	<p><?php esc_html_e( 'Please provide a link pointing to the author\'s official website or fan page.', 'recencio-book-reviews' );
	?></p>
</div>
