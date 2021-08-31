<?php
/**
 * Validation module.
 *
 * @package Meta Box
 */

/**
 * Validation class.
 */
class WPP_MB_Validation {

	/**
	 * Add hooks when module is loaded.
	 */
	public function __construct() {
		add_action( 'wpp_mb_after', array( $this, 'rules' ) );
		add_action( 'wpp_mb_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Output validation rules of each meta box.
	 * The rules are outputted in [data-rules] attribute of an hidden <script> and will be converted into JSON by JS.
	 *
	 * @param WPPMB_Meta_Box $object Meta Box object.
	 */
	public function rules( WPPMB_Meta_Box $object ) {
		if ( ! empty( $object->meta_box['validation'] ) ) {
			echo '<script type="text/html" class="rwmb-validation-rules" data-rules="' . esc_attr( wp_json_encode( $object->meta_box['validation'] ) ) . '"></script>';
		}
	}

	/**
	 * Enqueue scripts for validation.
	 *
	 * @param WPPMB_Meta_Box $object Meta Box object.
	 */
	public function enqueue( WPPMB_Meta_Box $object ) {
		if ( empty( $object->meta_box['validation'] ) ) {
			return;
		}
		wp_enqueue_script( 'jquery-validation', WPP_MB_JS_URL . 'jquery-validation/jquery.validate.min.js', array( 'jquery' ), '1.15.0', true );
		wp_enqueue_script( 'jquery-validation-additional-methods', WPP_MB_JS_URL . 'jquery-validation/additional-methods.min.js', array( 'jquery-validation' ), '1.15.0', true );
		wp_enqueue_script( 'rwmb-validate', WPP_MB_JS_URL . 'validate.js', array( 'jquery-validation', 'jquery-validation-additional-methods' ), WPP_MB_VER, true );

		WPP_MB_Helpers_Field::localize_script_once(
			'rwmb-validate',
			'rwmbValidate',
			array(
				'summaryMessage' => esc_html__( 'Please correct the errors highlighted below and try again.', 'meta-box' ),
			)
		);
	}
}
