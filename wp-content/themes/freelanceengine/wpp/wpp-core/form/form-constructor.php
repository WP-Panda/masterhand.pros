<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class WPP_Form_Constructor {

	public $setting_args = [];

	public function __construct( $args = [] ) {
		$this->setting_args = $args;
	}

	function parse_data() {
		foreach ( $this->setting_args as $one_field ) :

			$data = wp_parse_args( $one_field, $this->get_default() );

			extract( $data );


			if ( empty( $id ) ) {
				continue;
			}


			ob_start();
			wpp_get_template_part( 'wpp/wpp-core/form/templates/' . $type );
			$wrap = ob_get_clean();

			$field = '';


			if ( 'editor' === $type ) {
				ob_start();
				wp_editor( $value, $id, ae_editor_settings() );
				$field = ob_get_clean();
			}

			printf( $wrap, $wrap_class, $label_class, $label, $input_class, $value, $id, $placeholder, $field );

		endforeach;
	}

	function get_default() {

		$default = [
			'id'          => false,
			'wrap_class'  => 'col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field',
			'label_class' => 'fre-field-title',
			'type'        => 'text',
			'label'       => false,
			'value'       => false,
			'input_class' => 'wpp-form-input',
			'placeholder' => false,
		];

		return $default;
	}


}