<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME Html select input tag
 *
 * @since 1.0
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 */
class ME_Select extends ME_Input{
    function __construct( $args, $options ) {
        $args = wp_parse_args($args, array('name' => 'option_name', 'description' => '', 'label' => ''));

        $this->_type        = 'select';
        $this->_name        = $args['name'];
        $this->_label       = $args['label'];
        $this->_description = $args['description'];
        $this->_slug        = $args['slug'];
        $this->_is_multiple = isset($args['is_multiple']) ? 'multiple="multiple"' : '';
        $this->_placeholder = isset($args['placeholder']) ? $args['placeholder'] : __('Select...', 'enginethemes');
        $this->_data        = isset($args['data']) ? $args['data'] : array();
        $this->_container   = $options;

        $this->_options = $options;
    }

    function render() {
        $id = $this->_slug ? 'id="'. $this->_slug . '"' : '';
        $option_value = $this->get_value();

        echo '<div class="me-group-field" '.$id.'>';
        $this->label();
        $this->description();
        echo '<span class="me-select-control">';
        echo '<select class="select-field" name="'. $this->_name .'" '. $this->_is_multiple .'>';
        echo '<option value="">'. $this->_placeholder .'</option>';
        foreach ($this->_data as $key => $value) {
            echo '<option '.selected($option_value, $key, false).' value="'. $key .'">' . $value . '</option>';
            $selected = '';
        }
        echo '</select>';
        echo '</span>';
        echo '</div>';
    }

}