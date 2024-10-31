<div id="<?php echo esc_attr( $this->id ) ?>" class="settings-page wrap modal slide" aria-hidden="true">
	<div class="modal__overlay" tabindex="-1" data-micromodal-close>
		<div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="settings-page-wrap-title" >
			<header class="modal__header">
				<h2 id="settings-page-wrap-title" class="modal__title">
					<?php printf( '%s %s', $this->title, __( 'Settings', 'recencio-book-reviews' ) ); ?>
				</h2>
				<button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
			</header>

			<div id="settings-page-wrap-content" class="modal__content">
				<form method="post" action="options.php">
					<?php settings_fields( 'rcno-custom-user-metadata' ); ?>
					<?php do_settings_sections( 'rcno-custom-user-metadata' ); ?>
					<div class="settings-form">

                        <label for="settings-form-text-field">
                            <?php _e( 'Custom User Metaboxes', 'recencio-book-reviews' ); ?>
                        </label>
                        <input type="text"
                               name="rcno_custom_user_metadata_options[custom_user_metaboxes]"
                               class="regular-text"
                               id="settings-form-text-field"
                               value="<?php echo esc_attr( $this->get_setting( 'custom_user_metaboxes' ) ); ?>"
                        />
                        <p>
                            <?php _e( 'Please visit the "Templates" tab on the Recencio setting page,
                             to add your new metadata entries to the frontend', 'recencio-book-reviews' ); ?>
                        </p>

					</div>
					<footer class="modal__footer">
						<?php submit_button(); ?>
					</footer>
				</form>
			</div>

		</div>
	</div>
</div>

<style>
    .settings-form {
        width: 550px;
        margin: 2em 0 0 0;
    }

    .settings-form label {
        display: block;
    }

    .settings-form p {
        margin: 0;
    }

    input#settings-form-text-field {
        width: 100%;
    }
</style>

<script>
  (function($) {
    $(function() {
      $('#settings-form-text-field').selectize({
        create: true,
        plugins: ['remove_button', 'restore_on_backspace', 'drag_drop'],
      });
    });
  })(jQuery);
</script>
