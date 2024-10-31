<?php
/**
 * The shortcode overlay view (aka the dialog itself) to insert book review shortcodes.
 *
 * @since      1.0.0
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */
?>

<div id="rcno-modal-backdrop-scr" style="display: none"></div>
<div id="rcno-modal-wrap-scr" class="wp-core-ui search-panel-visible" style="display: none">
    <form id="rcno-modal-form-scr" tabindex="-1">
        <div id="rcno-modal-title-scr">
			<?php _e( 'Insert book review', 'recencio-book-reviews' ) ?>
            <button type="button" id="rcno-modal-close-scr"><span
                        class="screen-reader-text"><?php _e( 'Close' ); ?></span></button>
        </div>
        <div id="rcno-modal-panel-scr">
            <input id="review-id-field" type="hidden" name="reviewid"/>
            <input id="review-title-field" type="hidden" name="reviewtitle"/>

            <p class="howto"><?php _e( 'Choose the review you want to include from the list below or search for it.', 'recencio-book-reviews' ); ?></p>

            <div class="link-search-wrapper">
                <label>
                    <span class="search-label"><?php _e( 'Search' ); ?></span>
                    <input type="search" id="rcno-search-field" class="link-search-field" autocomplete="off"/>
                    <span class="spinner"></span>
                </label>
            </div>

            <div id="rcno-search-results" class="query-results" tabindex="0">
                <ul></ul>
                <div class="river-waiting">
                    <span class="spinner"></span>
                </div>
            </div>
            <div id="rcno-most-recent-results" class="query-results" tabindex="0">
                <div class="query-notice" id="query-notice-message">
                    <em class="query-notice-default"><?php _e( 'No search term specified. Showing recent items.' ); ?></em>
                    <em class="query-notice-hint screen-reader-text"><?php _e( 'Search or use up and down arrow keys to select an item.' ); ?></em>
                </div>
                <ul></ul>
                <div class="river-waiting">
                    <span class="spinner"></span>
                </div>
            </div>
            <!--<a id="rcno-modal-scr-options-link"><i class="fa fa-caret-right"></i><?php _e( "Display options", 'recencio-book-reviews' ); ?> </a>-->
            <div id="rcno-modal-scr-options-panel">
                <b><?php _e( "Display options:", 'reviewpress-reloaded' ); ?></b>
                <ul id="rcno-modal-scr-options-list">
                    <li>
                        <input type="checkbox" id="rcno-embed-excerpt" name="embed-excerpt" value="embed-excerpt"/>
                        <label for="rcno-embed-excerpt"><span><?php _e( 'Embed excerpt only', 'recencio-book-reviews' ); ?></span></label>
                    </li>
                    <li>
                        <input type="checkbox" id="rcno-embed-nodesc" name="embed-nodesc" value="embed-nodesc"/>
                        <label for="rcno-embed-nodesc"><span><?php _e( 'Embed <b>without</b> description', 'recencio-book-reviews' ); ?></span></label>
                    </li>
                </ul>
            </div>

            <div id="rcno-modal-scr-new-review-panel">
				<?php printf(
				/* Translators: 1: Link tag opening and icon  2: Link tag closure */
					__( '%1sCreate a new review.%2s (This will open a new tab and you will need to return here for including the review)', 'recencio-book-reviews' ),
					'<a href="' . admin_url() . '/post-new.php?post_type=rcno_review" target="_new" >' . '<i class="fa fa-plus-circle"></i>&nbsp;',
					'</a>'
				);
				?>
            </div>
        </div>

        <div class="submitbox">
            <div id="rcno-modal-cancel-scr">
                <a class="submitdelete deletion" href="#"><?php _e( 'Cancel' ); ?></a>
            </div>
            <div id="rcno-modal-update-scr">
                <input type="submit" value="<?php esc_attr_e( 'Include Shortcut', 'recencio-book-reviews' ); ?>"
                       class="button button-primary" id="rcno-modal-submit-scr" name="rcno-link-submit">
            </div>
        </div>
    </form>
</div>
