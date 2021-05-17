<?php

/**
 * ME Html number input tag
 *
 * @author EngineThemes
 * @since 1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME Html number input tag
 *
 * @since 1.0
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 */
class ME_Number extends ME_Input
{
    /**
     * Class constructor
     *
     * @param array $args array of values.
     * @param object $options
     *
     * @since 1.0.0
     */
    public function __construct($args, $options)
    {
        $args = wp_parse_args($args, array('name' => 'option_name', 'description' => '', 'label' => ''));

        $this->_type        = 'number';
        $this->_name        = $args['name'];
        $this->_label       = $args['label'];
        $this->_slug        = $args['slug'];

        $this->_description = $args['description'];
        $this->_placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        $this->_isform      = isset($args['isform']);
        $this->_class       = (empty($args['class_name'])) ? '' : $args['class_name'];
        $this->_attributes  = isset($args['attributes']) ? $args['attributes'] : array();
        $this->_container   = $options;

        $this->_options = $options;

        $this->_default_value = '';
        if (isset($args['default'])) {
            $this->_default_value = $args['default'];
        }
    }

    /**
     * Renders html
     *
     * @since 1.0.0
     */
    public function render()
    {
        $id         = $this->_slug ? 'id="' . $this->_slug . '"' : '';
        $attributes = '';
        foreach ($this->_attributes as $key => $value) {
            $attributes = $key . '="' . $value . '"';
        }

        echo '<div class="me-group-field" ' . $id . '>';
        $this->label();
        $this->description();
        echo '<span class="me-field-control"><input type="number" ' . $attributes . ' name="' . $this->_name . '" class="me-input-field ' . $this->_class . '" value="' . $this->get_value() . '" placeholder="' . $this->_placeholder . '" /></span>';
        echo '</div>';

        if($this->_default_value !== false) : ?>
            <script type="text/javascript">
                jQuery('#<?php echo $this->_slug ?> input').blur(function(event) {
                    var $target = jQuery(event.currentTarget);
                    if (!$target.val()) {
                        $target.val('<?php echo $this->_default_value ?>');
                    }
                });
            </script>
        <?php
        endif;

    }
}
