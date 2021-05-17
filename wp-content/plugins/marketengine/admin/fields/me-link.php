<?php
/**
 * Class ME Link Tag
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class ME Link Tag
 * Render <a> tag in admin options
 *
 * @category Class
 * @package Admin/Options
 *
 * @version 1.0
 */
class ME_Link extends ME_Input{
    /**
     * Contruct the field attribute
     * @param $args The field args
     * @param $options The field option value
     */
    function __construct( $args, $options ) {
        $args = wp_parse_args($args, array('name' => 'option_name', 'description' => '', 'label' => '', 'class' => '', 'url' => ''));

        $this->_type        = 'link';
        $this->_name        = $args['name'];
        $this->_label       = $args['label'];
        $this->_slug        = $args['slug'];
        $this->_class       = $args['class'];
        $this->_url         = $args['url'];
        $this->_container   = $options;

        $this->_options = $options;
    }
    /**
     * Render button html
     */
    function render() {
        echo '<a href="'.$this->_url.'" class="'.$this->_class.'">'.$this->_label.'</a>';
    }
}
