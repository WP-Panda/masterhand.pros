<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class ME_Multi_Field
 * 
 * A group of multi option fields
 * 
 * @category Class
 * @package Admin/Options
 * @since 1.0
 * 
 * @version 1.0
 */
class ME_Multi_Field extends ME_Input{
    function __construct( $args, $options ) {
        $args = wp_parse_args($args, array('name' => 'option_name', 'description' => '', 'label' => ''));

        $this->_type        = 'multi-field';
        $this->_slug        = $args['slug'];
        $this->_name        = $args['name'];
        $this->_label       = $args['label'];
        $this->_description = $args['description'];
        $this->_template 	= $args['template'];
        $this->_class       = isset($args['class']) ? $args['class'] : '';
        $this->_container   = $options;

        $this->_options = $options;
    }

    function render() {
        echo '<div '.$this->get_id().' class="me-group">';
        echo '<div  class="me-group-field '.$this->_class.'">';
        $this->label();
        $this->description();
        foreach( $this->_template as $template ){
        	$class = 'ME_' . ucfirst($template['type']);
            $template['isform'] = false;
        	$field = new $class($template, $this->_options);
        	$field->render();
        }
        echo '</div>';
        echo '</div>';
    }

}