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
class ME_Select_Group extends ME_Select{

    function render() {
        $id = $this->_slug ? 'id="'. $this->_slug . '"' : '';
        $option_value = $this->get_value();

        echo '<div class="me-group-field" '.$id.'>';
        $this->label();
        $this->description();
        echo '<span class="me-select-control">';
        echo '<select class="select-field" name="'. $this->_name .'" >';
        echo '<option value="">'. $this->_placeholder .'</option>';

        foreach ($this->_data as $key => $group_value) {
            echo '<optgroup label="'.$group_value['label'].'">';
            foreach ($group_value['options'] as $key => $value) {
                echo '<option '.selected($option_value, $key, false).' value="'. $key .'">' . $value . '</option>';
            }
            echo '</optgroup>';
        }

        echo '</select>';
        echo '</span>';
        echo '</div>';
    }

}