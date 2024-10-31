<?php
/**
 * The shortcode overlay view (aka the dialog itself) to insert listings shortcodes.
 *
 * @since      1.0.0
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */
?>
<div id="rcno-modal-backdrop-scl" style="display: none"></div>

<div id="rcno-modal-wrap-scl" class="wp-core-ui search-panel-visible" style="display: none">
	<form id="rcno-modal-form-scl" tabindex="-1">
		<div id="rcno-modal-title-scl">
			<?php esc_html_e( 'Insert book review listing', 'recencio-book-reviews' ) ?>
			<button type="button" id="rcno-modal-close-scl"><span
						class="screen-reader-text"><?php echo esc_html( 'Close' ); ?></span></button>
		</div>
		<div id="rcno-modal-panel-scl">
			<ul id="rcno-modal-scl-mode">
				<li>
					<input type="radio" selected="selected" value="rcno-tax-list" id="rcno-modal-scl-mode-tax"
						   name="rcno-modal-scl-mode"/>
					<label for="rcno-modal-scl-mode-tax"><b><?php esc_html_e( 'Embed taxonomy index', 'recencio-book-reviews' ); ?></b></label>
					<div id="rcno-taxonomy-panel">
						<label><span><?php esc_html_e( 'Taxonomy', 'recencio-book-reviews' ); ?></span></label>
						<select id="review-taxonomy">
							<?php if ( Rcno_Reviews_Option::get_option( 'rcno_taxonomy_selection' ) ) {
								$custom_taxonomies = Rcno_Reviews_Option::get_option( 'rcno_taxonomy_selection' );
								$keys              = explode( ',', $custom_taxonomies );
								foreach ( $keys as $key ) { ?>
									<option value="<?php echo 'rcno_' . strtolower( $key ) ?>"><?php echo esc_html(
									        $key ); ?></option>
								<?php }
							} ?>
						</select>
					</div>
				</li>
				<li>
					<input type="radio" value="rcno-reviews-index" name="rcno-modal-scl-mode"
						   id="rcno-modal-scl-mode-ind"/>
					<label for="rcno-modal-scl-mode-ind"><b><?php esc_html_e( 'Embed book review index', 'recencio-book-reviews' ); ?></b></label>
				</li>
                <li>
                    <input type="radio" value="rcno-reviews-grid" name="rcno-modal-scl-mode"
                           id="rcno-modal-scl-mode-grid"/>
                    <label for="rcno-modal-scl-mode-grid"><b><?php esc_html_e( 'Embed book review grid', 'recencio-book-reviews' ); ?></b></label>
                </li>
                <li>
                    <input type="radio" value="rcno-reviews-isotope" name="rcno-modal-scl-mode"
                           id="rcno-modal-scl-mode-isotope"/>
                    <label for="rcno-modal-scl-mode-isotope"><b><?php esc_html_e( 'Embed sortable book review grid', 'recencio-book-reviews' ); ?></b></label>
                </li>
			</ul>
		</div>
		<div class="submitbox">
			<div id="rcno-modal-cancel-scl">
				<a class="submitdelete deletion" href="#"><?php esc_html_e( 'Cancel', 'recencio-book-reviews' ); ?></a>
			</div>
			<div id="rcno-modal-update-scl">
				<input type="submit" class="button button-primary" id="rcno-modal-submit-scl"
					   name="rcno-modal-submit-scl" value="<?php esc_attr_e( 'Insert', 'recencio-book-reviews' ); ?>">
			</div>
		</div>
	</form>
</div>
