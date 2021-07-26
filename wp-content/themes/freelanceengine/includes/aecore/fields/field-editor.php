<?php

class AE_editor {

	/**
	 * Field Constructor.
	 *
	 * @param array $field
	 * - id
	 * - name
	 * - placeholder
	 * - readonly
	 * - class
	 * - title
	 * @param $value
	 * @param $parent
	 *
	 * @since AEFramework 1.0.0
	 */
	function __construct( $field = array(), $value = '', $parent ) {

		//parent::__construct( $parent->sections, $parent->args );
		$this->parent = $parent;
		$this->field  = $field;
		$this->value  = $value;

	}

	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since AEFramework 1.0.0
	 */
	function render() {

		$readonly    = isset( $this->field['readonly'] ) ? ' readonly="readonly"' : '';
		$placeholder = ( isset( $this->field['placeholder'] ) && ! is_array( $this->field['placeholder'] ) ) ? ' placeholder="' . esc_attr( $this->field['placeholder'] ) . '" ' : '';

		echo '<textarea id="' . $this->field['id'] . '" name="' . $this->field['name'] . '" ' . $placeholder .
		     ' class="regular-editor editor ' . $this->field['class'] . '"' . $readonly . ' >' . esc_attr( $this->value ) . '</textarea>';

		if ( isset( $this->field['reset'] ) && $this->field['reset'] ) {
			echo '<div style="margin-top: 10px;" class="mail-control-btn"><a href="#" class="reset-default">Reset to default</a></div>';
		}

	}//render

}
