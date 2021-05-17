<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * class ME_Textbox
 * 
 * ME Html textbox input tag
 *
 * @since 1.0
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 */
class ME_Textbox extends ME_Input {
    public function __construct($args, $options) {
        $args = wp_parse_args($args, array('name' => 'option_name', 'description' => '', 'label' => ''));

        $this->_type        = 'textbox';
        $this->_name        = $args['name'];
        $this->_label       = $args['label'];
        $this->_description = $args['description'];
        $this->_slug        = $args['slug'];
        $this->_class = (empty($args['class_name'])) ? '' : $args['class_name'];
        $this->_placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        $this->_isform      = isset($args['isform']);
        $this->_container   = $options;

        $this->_options = $options;

    }

    public function render() {
        $id = $this->_slug ? 'id="'.$this->_slug.'"' : '';

        echo '<div class="me-group-field" '.$id.'>';
        $this->label();
        $this->description();
        echo '<span class="me-field-control"><input type="text" name="'.$this->_name.'" class="me-input-field ' .$this->_class. '" value="' . $this->get_value() . '" placeholder="'.$this->_placeholder.'" /></span>';
        echo '</div>';
    }
}

