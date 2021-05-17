<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME Toggle Option HTML Form
 * 
 * @since 1.0
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 */
class ME_Switch extends ME_Input{
    function __construct( $args, $options ) {
        $args = wp_parse_args($args, array('name' => 'option_name', 'description' => '', 'label' => ''));

        $this->_type        = 'switch';
        $this->_name        = $args['name'];
        $this->_label       = $args['label'];
        $this->_description = $args['description'];
        $this->_slug        = $args['slug'];
        $this->_checked     = isset($args['checked']) ? $args['checked'] : '';
        $this->_text        = isset($args['text']) ? $args['text'] : array();
        $this->_container   = $options;

        $this->_options = $options;
    }

    function render() {
        $enable_text = __("Enable", 'enginethemes');
        $disable_text = __("Disable", 'enginethemes');
        if(!empty($this->_text)) {
            $enable_text = $this->_text[0];
            $disable_text = $this->_text[1];
        }

        $id = $this->_slug ? 'id="'. $this->_slug . '"' : '';
        $value = $this->get_value();

        echo '<div class="me-group-field" '.$id.'>';
        $this->label();
        $this->description();
        echo '<label class="me-switch">
                <input type="checkbox" name="'.$this->_name.'" ' . $value . '>
                <div class="me-switch-slider">
                    <span class="me-enable">'. $enable_text .'</span>
                    <span class="me-disable">'. $disable_text .'</span>
                </div>
            </label>';
        echo '</div>';
    }
}
