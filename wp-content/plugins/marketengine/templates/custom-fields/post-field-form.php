<?php
/**
 * The Template for post listing custom field form
 *
 * This template can be overridden by copying it to yourtheme/marketengine/custom-fields/post-field-form.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 *
 * @since 		1.0.1
 *
 * @version     1.0.0
 *
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

?>
<div class="marketengine-custom-field">
<?php
if (!empty($_POST['parent_cat'])):
    $fields = marketengine_cf_get_fields($_POST['parent_cat']);
    if (!empty($fields)) {
        foreach ($fields as $field):
            $field_name = $field['field_name'];
            $value      = !empty($_POST[$field_name]) ? $_POST[$field_name] : '';
            if(is_array($value)) {
            	$value = array_map('esc_html', $value);
            }else {
            	$value = esc_html( $value );
            }
            marketengine_get_template('custom-fields/listing-form/field-' . $field['field_type'], array('field' => $field, 'value' => $value));
        endforeach;
    }
endif;
?>
</div>