<?php
/**
 * The Template for field textarea input form
 *
 * This template can be overridden by copying it to yourtheme/marketengine/custom-fields/listing-form/field-textarea.php.
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
<div class="marketengine-group-field">
	<div class="marketengine-textarea-field">
	    <?php marketengine_get_template('custom-fields/listing-form/field-label', array('field' => $field));  ?>
	    <textarea <?php //echo marketengine_field_attribute($field); ?> id="<?php echo $field['field_name'] ?>" placeholder="<?php echo $field['field_placeholder'] ?>" name="<?php echo $field['field_name'] ?>"><?php echo $value; ?></textarea>
	</div>
</div>