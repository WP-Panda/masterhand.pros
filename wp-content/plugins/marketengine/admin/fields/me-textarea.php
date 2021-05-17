<?php
/**
 * MarketEngine Input HTML
 *
 * @author EngineThemes
 * @since 1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * class ME_Textarea
 *
 * ME Html textarea tag
 *
 * @since 1.0
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 */
class ME_Textarea extends ME_Input {
    /**
     * Class constructor.
     *
     * Initialize value.
     *
     * @param array $args
     * @param object $options
     *
     * @since 1.0.0
     */
    function __construct( $args, $options ) {
        $args = wp_parse_args($args, array('name' => 'option_name', 'description' => '', 'label' => ''));

        $this->_type        = 'textarea';
        $this->_name        = $args['name'];
        $this->_label       = $args['label'];
        $this->_description = $args['description'];
        $this->_slug        = $args['slug'];
        $this->_container   = $options;

        $this->_options = $options;
    }

    /**
     * Renders html
     *
     * @since 1.0.0
     */
    function render() {
        $id = $this->_slug ? 'id="'.$this->_slug.'"' : '';
        echo '<div class="me-group-field" '.$id.'>';
        $this->label();
        $this->description();
        echo '<textarea name="' . $this->_name .'" class="me-textarea-field" >'. $this->get_value() . '</textarea>';
        echo '</div>';
    }

}
