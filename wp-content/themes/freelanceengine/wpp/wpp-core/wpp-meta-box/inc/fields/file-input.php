<?php
/**
 * The file input field which allows users to enter a file URL or select it from the Media Library.
 *
 * @package Meta Box
 */

/**
 * File input field class which uses an input for file URL.
 */
class WPP_MB_File_Input_Field extends WPP_MB_Input_Field {
	/**
	 * Enqueue scripts and styles.
	 */
	public static function admin_enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_style( 'rwmb-file-input', WPP_MB_CSS_URL . 'file-input.css', array(), WPP_MB_VER );
		wp_enqueue_script( 'rwmb-file-input', WPP_MB_JS_URL . 'file-input.js', array( 'jquery' ), WPP_MB_VER, true );
		WPP_MB_Helpers_Field::localize_script_once(
			'rwmb-file-input',
			'rwmbFileInput',
			array(
				'frameTitle' => esc_html__( 'Select File', 'meta-box' ),
			)
		);
	}

	/**
	 * Get field HTML.
	 *
	 * @param mixed $meta  Meta value.
	 * @param array $field Field parameters.
	 *
	 * @return string
	 */
	public static function html( $meta, $field ) {
		$attributes = self::get_attributes( $field, $meta );
		return sprintf(
			'<input %s>
			<a href="#" class="rwmb-file-input-select button">%s</a>
			<a href="#" class="rwmb-file-input-remove button %s">%s</a>',
			self::render_attributes( $attributes ),
			esc_html__( 'Select', 'meta-box' ),
			$meta ? '' : 'hidden',
			esc_html__( 'Remove', 'meta-box' )
		);
	}

	/**
	 * Get the attributes for a field.
	 *
	 * @param array $field Field parameters.
	 * @param mixed $value Meta value.
	 * @return array
	 */
	public static function get_attributes( $field, $value = null ) {
		$attributes         = parent::get_attributes( $field, $value );
		$attributes['type'] = 'text';

		return $attributes;
	}
}
