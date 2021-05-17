<?php
/**
 * Class ME Multiselect
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class ME_MultiSelect
 *
 * Render Multiselect option field
 *
 * @category Class
 * @package Admin/Options
 * @since 1.0
 *
 * @version 1.0
 */
class ME_MultiSelect extends ME_Input
{
    public function __construct($args, $options)
    {
        $args = wp_parse_args($args, array('name' => 'option_name', 'description' => '', 'label' => '', 'note' => '', 'icon_note' => ''));

        $this->_type        = 'multiselect';
        $this->_name        = $args['name'];
        $this->_label       = $args['label'];
        $this->_description = $args['description'];
        $this->_note        = $args['note'];
        $this->_icon_note   = $args['icon_note'];
        $this->_slug        = $args['slug'];
        $this->_data        = isset($args['data']) ? $args['data'] : array();
        $this->_container   = $options;

        $this->_options = $options;
    }

    public function render()
    {
        $id           = $this->_slug ? 'id="' . $this->_slug . '"' : '';
        $option_value = $this->get_value() ? $this->get_value() : array() ;
        echo '<div class="me-group-field" ' . $id . '>';
        $this->label();
        $this->description();
        echo '<div class="me-select-control">';
        echo '<select multiple class="select-field" name="' . $this->_name . '">';
        foreach ($this->_data as $key => $value) {
            // $selected = in_array($key, $option_value) ? 'selected="selected"' : '';
            echo '<option ' . selected(in_array($key, $option_value), true, false) . ' value="' . $key . '">' . $value . '</option>';
        }
        echo '</select>';
        echo '</div>';
        if ($this->_note) {
            echo '<p class="me-field-note">';
            echo $this->_icon_note;
            echo $this->_note;
            echo '</p>';
        }
        echo '</div>';
    }

}
