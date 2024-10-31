<?php

/**
 * Rcno_Reviews Callback Helper Class
 *
 * The callback functions of the settings page
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin/settings
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Reviews_Callback_Helper {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string $plugin_name The name of this plugin.
	 */
	public function __construct( $plugin_name ) {

		$this->plugin_name = $plugin_name;

	}

	private function get_id_attribute( $id ) {
		return ' id="rcno_reviews_settings[' . $id . ']" ';
	}

	private function get_name_attribute( $name ) {
		return ' name="rcno_reviews_settings[' . $name . ']" ';
	}

	private function get_id_and_name_attributes( $field_key ) {
		return $this->get_id_attribute( $field_key ) . $this->get_name_attribute( $field_key );
	}

	private function get_label_for( $id, $desc ) {
		return '<label for="rcno_reviews_settings[' . $id . ']"> ' . $desc . '</label>';
	}

	/**
	 * Missing Callback
	 *
	 * If a function is missing for settings callbacks alert the user.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function missing_callback( $args ) {
		printf( __( 'The callback function used for <strong>%s</strong> setting is missing.', $this->plugin_name ), $args['id'] );
	}

	/**
	 * Spacer Callback
	 *
	 * Renders an empty table row used for spacing.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function spacer_callback( $args ) {
		echo '';
	}

	/**
	 * Header Callback
	 *
	 * Renders the header.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function header_callback( $args ) {
		echo '<hr/>';
	}

	/**
	 * Instructions Callback
	 *
	 * Renders the instructions.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function instruction_callback( $args ) {

		$html = '';
		$html .= '<p>';
		$html .= $args['desc'];
		$html .= '</p>';

		echo $html;
	}

	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function checkbox_callback( $args ) {

		$value   = Rcno_Reviews_Option::get_option( $args['id'] );
		$checked = isset( $value ) ? checked( 1, $value, false ) : '';

		$html = '<input type="checkbox" ';
		$html .= $this->get_id_and_name_attributes( $args['id'] );
		$html .= 'value="1" ' . $checked . '/>';

		$html .= '<br />';
		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function multicheck_callback( $args ) {

		if ( empty( $args['options'] ) ) {
			printf( __( 'Options for <strong>%s</strong> multicheck is missing.', $this->plugin_name ), $args['id'] );

			return;
		}

		$old_values = Rcno_Reviews_Option::get_option( $args['id'], array() );

		$html = '';

		foreach ( $args['options'] as $field_key => $option ) {

			if ( isset( $old_values[ $field_key ] ) ) {
				$enabled = $option;
			} else {
				$enabled = null;
			}

			$checked = checked( $option, $enabled, false );

			$html .= '<input type="checkbox" ';
			$html .= $this->get_id_and_name_attributes( $args['id'] . '][' . $field_key );
			$html .= ' value="' . $option . '" ' . $checked . '/> ';

			$html .= $this->get_label_for( $args['id'] . '][' . $field_key, $option );
			$html .= '<br/>';
		}

		$html .= '<p class="description">' . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function radio_callback( $args ) {

		if ( empty( $args['options'] ) ) {
			printf( __( 'Options for <strong>%s</strong> radio is missing.', $this->plugin_name ), $args['id'] );

			return;
		}

		$old_value = Rcno_Reviews_Option::get_option( $args['id'] );

		$html = '';

		foreach ( $args['options'] as $field_key => $option ) {

			if ( ! empty( $old_value ) ) {
				$checked = checked( $field_key, $old_value, false );
			} else {
				$checked = checked( $args['std'], $field_key, false );
			}

			$html .= '<input type="radio"';
			$html .= $this->get_name_attribute( $args['id'] );
			$html .= $this->get_id_attribute( $args['id'] . '][' . $field_key );
			$html .= ' value="' . $field_key . '" ' . $checked . '/> ';

			$html .= $this->get_label_for( $args['id'] . '][' . $field_key, $option );
			$html .= '<br/>';

		}

		$html .= '<p class="description">' . $args['desc'] . '</p>';
		echo $html;
	}

	/**
	 * Template Callback
	 *
	 * Renders book review template selection option.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function template_callback( $args ) {

		if ( empty( $args['options'] ) ) {
			printf( __( 'Options for <strong>%s</strong> radio is missing.', $this->plugin_name ), $args['id'] );

			return;
		}

		$old_value = Rcno_Reviews_Option::get_option( $args['id'] );

		$html = '';

		foreach ( $args['options'] as $field_key => $option ) {

			if ( ! empty( $old_value ) ) {
				$checked = checked( $field_key, $old_value, false );
			} else {
				$checked = checked( $args['std'], $field_key, false );
			}

			$html .= '<div class="template-options">';
			$html .= '<input type="radio"';
			$html .= $this->get_name_attribute( $args['id'] );
			$html .= $this->get_id_attribute( $args['id'] . '][' . $field_key );
			$html .= ' value="' . $field_key . '" ' . $checked . '/> ';

			$html .= '<label for="rcno_reviews_settings[' . $args['id'] . '][' . $field_key . ']">';
			$html .= '<div class="label-container">';
			$html .= ' <img src="' . $option['screenshot'] . '"';
			$html .= ' class="template-label-image';
			$html .= $checked ? ' checked' : '';
			$html .= '"/>';

			$html .= '<div class="template-label-info">';
			$html .= '<div class="template-label-p">';
			$html .= ' <p>Title: ' . $option['title'] . '</p>';
			$html .= ' <p>Author: ' . $option['author'] . '</p>';
			$html .= ' <p>Version: ' . $option['version'] . '</p>';
			$html .= '</div>';
			$html .= '</div>';

			$html .= '</div>';
			$html .= '</label>';
			$html .= '<br/>';
			$html .= '</div>';
		}

		echo $html;
	}

	/**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function text_callback( $args ) {

		$this->input_type_callback( 'text', $args );
	}

	/**
	 * Color Picker Callback
	 *
	 * Renders text fields.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function color_callback( $args ) {

		$value = Rcno_Reviews_Option::get_option( $args['id'], $args['std'] );

		$html = '<input type="text" ';
		$html .= $this->get_id_and_name_attributes( $args['id'] );
		$html .= 'class="' . $args['size'] . '-text ' . 'rcno-color-input' . '"';
		$html .= 'value="' . esc_attr( stripslashes( $value ) ) . '"/>';

		$html .= '<br />';
		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * Email Callback
	 *
	 * Renders email fields.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function email_callback( $args ) {

		$this->input_type_callback( 'email', $args );
	}

	/**
	 * Url Callback
	 *
	 * Renders url fields.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function url_callback( $args ) {

		$this->input_type_callback( 'url', $args );
	}

	/**
	 * Password Callback
	 *
	 * Renders password fields.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function password_callback( $args ) {

		$this->input_type_callback( 'password', $args );
	}

	/**
	 * Input Type Callback
	 *
	 * Renders input type fields.
	 *
	 * @since    1.0.0
	 *
	 * @param    string $type Input Type.
	 * @param    array  $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	private function input_type_callback( $type, $args ) {

		$value = Rcno_Reviews_Option::get_option( $args['id'], $args['std'] );

		$html = '<input type="' . $type . '" ';
		$html .= $this->get_id_and_name_attributes( $args['id'] );
		$html .= 'class="' . $args['size'] . '-text ' . 'rcno-' . $type . '-input' . '"';
		$html .= 'value="' . esc_attr( stripslashes( $value ) ) . '"/>';

		$html .= '<br />';
		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function number_callback( $args ) {

		$value = Rcno_Reviews_Option::get_option( $args['id'] );


		$html = '<input type="number" ';
		$html .= $this->get_id_and_name_attributes( $args['id'] );
		$html .= 'class="' . $args['size'] . '-text" ';
		$html .= 'step="' . $args['step'] . '" ';
		$html .= 'max="' . $args['max'] . '" ';
		$html .= 'min="' . $args['min'] . '" ';
		$html .= 'value="' . $value . '"/>';

		$html .= '<br />';
		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * Textarea Callback
	 *
	 * Renders textarea fields.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function textarea_callback( $args ) {

		$value = Rcno_Reviews_Option::get_option( $args['id'], $args['std'] );


		$html = '<textarea ';
		$html .= 'class="' . $args['size'] . '-text" ';
		$html .= 'cols="50" rows="5" ';
		$html .= $this->get_id_and_name_attributes( $args['id'] ) . '>';
		$html .= esc_textarea( stripslashes( $value ) );
		$html .= '</textarea>';

		$html .= '<br />';
		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * CSS Box Callback
	 *
	 * Renders textarea fields.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function cssbox_callback( $args ) {

		$value = Rcno_Reviews_Option::get_option( $args['id'], $args['std'] );

		$html = '<textarea ';
		$html .= 'class="rcno-css-box" ';
		$html .= 'cols="100" rows="10" ';
		$html .= $this->get_id_and_name_attributes( $args['id'] ) . '>';
		$html .= esc_textarea( stripslashes( $value ) );
		$html .= '</textarea>';

		$html .= '<br />';
		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function select_callback( $args ) {

		$value = Rcno_Reviews_Option::get_option( $args['id'] );

		$html = '<select ' . $this->get_id_and_name_attributes( $args['id'] ) . '/>';

		foreach ( (array) $args['options'] as $option => $option_name ) {
			$selected = selected( $option, $value, false );
			$html     .= '<option value="' . $option . '" ' . $selected . '>' . $option_name . '</option>';
		}

		$html .= '</select>';
		$html .= '<br />';
		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * Multiple Select Callback
	 *
	 * Renders the multiple select fields.
	 *
	 * @since    1.11.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function multi_select_callback( $args ) {

		$values = Rcno_Reviews_Option::get_option( $args['id'] );

		$html = '<select ' . $this->get_id_attribute( $args['id'] ) . ' name="rcno_reviews_settings[' . $args['id'] . '][]" ' .  ' multiple />';

		foreach ( (array) $args['options'] as $option => $option_name ) {
			if ( in_array( $option, $values, true ) ) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$html     .= '<option value="' . $option . '" ' . $selected . '>' . $option_name . '</option>';
		}

		$html .= '</select>';
		$html .= '<br />';
		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * Rich Editor Callback
	 *
	 * Renders rich editor fields.
	 *
	 * @since    1.0.0
	 *
	 * @param   array   $args        Arguments passed by the setting.
	 *
	 * @global  int     $wp_version  WordPress version number
	 */
	public function rich_editor_callback( $args ) {
		global $wp_version;

		$value = Rcno_Reviews_Option::get_option( $args['id'] );

		if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
			ob_start();
			wp_editor( stripslashes( $value ), 'rcno_reviews_settings_' . $args['id'], array( 'textarea_name' => 'rcno_reviews_settings[' . $args['id'] . ']' ) );
			$html = ob_get_clean();
		} else {
			$html = '<textarea' . $this->get_id_and_name_attributes( $args['id'] ) . 'class="' . $args['size'] . '-text" rows="10" >' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
		}

		$html .= '<br/>';
		$html .= $this->get_label_for( $args['id'], $args['desc'] );
		$html .= '<br/>';

		echo $html;
	}

	/**
	 * Upload Callback
	 *
	 * Renders upload fields.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $args Arguments passed by the setting.
	 *
	 * @return  void
	 */
	public function upload_callback( $args ) {

		$value = Rcno_Reviews_Option::get_option( $args['id'] );

		$html = '<input type="text" ';
		$html .= $this->get_id_and_name_attributes( $args['id'] );
		$html .= 'class="' . $args['size'] . '-text ' . 'rcno_reviews_upload_field" ';
		$html .= ' value="' . esc_attr( stripslashes( $value ) ) . '"/>';

		$html .= '<span>&nbsp;<input type="button" id="' . $args['id'] . '" 
		class="' . 'rcno_reviews_settings_upload_button ' . $args['id'] . ' button-secondary" 
		value="' . __( 'Upload File', 'recencio-book-reviews' ) . '"/></span><br>';

		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * Download Callback
	 *
	 * Renders the settings download button.
	 *
	 * @since   1.9.0
	 *
	 * @param   array $args Arguments passed by the setting.
	 *
	 * @return  void
	 */
	public function download_callback( $args ) {

		$html = '<input type="button" id="' . $args['id'] . '" 
		class="' . 'rcno_reviews_settings_download_button ' . $args['id'] . ' button-secondary" 
		value="' . __( 'Download File', 'recencio-book-reviews' ) . '"/><br>';

		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * File Callback
	 *
	 * Renders the settings download button.
	 *
	 * @since   1.9.0
	 *
	 * @param   array $args Arguments passed by the setting.
	 *
	 * @return  void
	 */
	public function file_callback( $args ) {

		$html = '<input type="file" id="' . $args['id'] . '" 
		class="' . 'rcno_reviews_settings_file_button ' . $args['id'] . ' file-button" 
		value="' . __( 'Download File', 'recencio-book-reviews' ) . '" accept="' . $args['accept'] . '" /><br>';

		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * Slug Callback
	 *
	 * Renders slug field used to collect the CPT name.
	 *
	 * @since    1.9.0
	 *
	 * @param    array $args Arguments passed by the setting.
	 *
	 * @return    void
	 */
	public function slug_callback( $args ) {

		//$this->input_type_callback( 'text', $args );

		$value = Rcno_Reviews_Option::get_option( $args['id'], $args['std'] );

		$html = '<input type="text" ';
		$html .= $this->get_id_and_name_attributes( $args['id'] );
		$html .= 'class="' . $args['size'] . '-text ' . 'rcno-slug-input' . '"';
		$html .= 'value="' . esc_attr( stripslashes( $value ) ) . '" ';
		$html .= 'pattern="' . $args['pattern'] . '" ';
		$html .= 'title="' . $args['title'] . '" ';
		$html .= '/>';

		$html .= '<br />';
		$html .= $this->get_label_for( $args['id'], $args['desc'] );

		echo $html;
	}

	/**
	 * Labels Callback
	 *
	 * Renders 2 text fields to record the singular and plural forms of
	 * a label
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments passed by the setting.
	 *
	 * @return void
	 */
	public function labels_callback( $args ) {

		$values = Rcno_Reviews_Option::get_option( $args['id'], array() );

		$html  = '<div class="rcno inline-row">';
		$html .= '<div class="rcno inline-inputs">';
		$html .= '<input type="text" ';
		$html .= $this->get_id_and_name_attributes( $args['id'] . '][' . 'singular' );
		$html .= 'class="rcno text-' . $args['size'] . '" ';
		$html .= 'value="' . esc_attr( stripslashes( ! empty( $values['singular'] ) ? $values['singular'] : $args['singular_std'] ) ) . '"/>';
		$html .= '<br/>';
		$html .= '<label class="rcno-settings-label" for="rcno_reviews_settings[' . $args['id'] . '][singular]"> ' . $args['singular_desc'] . '</label>';
		$html .= '</div>';

		$html .= '<div class="rcno inline-inputs">';
		$html .= '<input type="text" ';
		$html .= $this->get_id_and_name_attributes( $args['id'] . '][' . 'plural' );
		$html .= 'class="rcno text-' . $args['size'] . '" ';
		$html .= 'value="' . esc_attr( stripslashes( ! empty( $values['plural'] ) ? $values['plural'] : $args['plural_std'] ) ) . '"/>';
		$html .= '<br/>';
		$html .= '<label class="rcno-settings-label" for="rcno_reviews_settings[' . $args['id'] . '][plural]"> ' . $args['plural_desc'] . '</label>';
		$html .= '</div>';
		$html .= '</div>';

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
