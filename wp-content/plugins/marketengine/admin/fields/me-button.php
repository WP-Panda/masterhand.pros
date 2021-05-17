<?php
/**
 * Class ME Button
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class ME Button option
 * Render button in admin options
 * 
 * @category Class
 * @package Admin/Options
 *
 * @version 1.0
 */
class ME_Button extends ME_Input{
    /**
     * Contruct the field attribute
     * @param $args The field args
     * @param $options The field option value
     */
    function __construct( $args, $options ) {
        $args = wp_parse_args($args, array('name' => 'option_name', 'description' => '', 'label' => '', 'class' => '', 'cancel' => false));

        $this->_type        = 'button';
        $this->_template    = $args['template'];
        $this->_name        = $args['name'];
        $this->_label       = $args['label'];
        $this->_slug        = $args['slug'];
        $this->_class       = $args['class'];
        $this->_container   = $options;

        $this->_options = $options;
    }
    /**
     * Render button html
     */
    function render() {
        echo '<input class="'.$this->_class.'" id="'.$this->_slug.'" type="submit" name="'.$this->_name.'" value="'.$this->_label.'"/>';

        if($this->_template) {
            foreach( $this->_template as $key => $value) {
                $class = 'ME_'.ucfirst($value['type']);
                $control = new $class($value, $this);
                $control->render();
            }
        }
    }
}
