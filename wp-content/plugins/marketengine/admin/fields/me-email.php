<?php
/**
 * ME Html email input tag
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME Html email input tag
 *
 * @since 1.0
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 */
class ME_Email extends ME_Input {
    /**
     * Field contructor
     *
     * @param array $args Input attribute
     * @param mix $options Input option value
     * 
     */
    public function __construct($args, $options) {
        $args = wp_parse_args($args, array('name' => 'option_name', 'description' => '', 'label' => ''));

        $this->_type        = 'email';
        $this->_name        = $args['name'];
        $this->_label       = $args['label'];
        $this->_description = $args['description'];
        $this->_slug        = $args['slug'];
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
        echo '<span class="me-field-control"><input type="email" name="'.$this->_name.'" class="me-input-field" value="' . $this->get_value() . '" placeholder="'.$this->_placeholder.'" /></span>';
        echo '</div>';
    }
}

