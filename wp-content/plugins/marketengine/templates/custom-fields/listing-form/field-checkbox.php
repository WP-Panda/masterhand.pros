<?php
/**
 * The Template for field checkbox form
 *
 * This template can be overridden by copying it to yourtheme/marketengine/custom-fields/listing-form/field-checkbox.php.
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
$options = marketengine_cf_get_field_options($field['field_name']);
if(empty($options)) return;
?>
<div class="marketengine-group-field">
	<div class="marketengine-checkbox-field">
	    <?php marketengine_get_template('custom-fields/listing-form/field-label', array('field' => $field));  ?>
	    <?php foreach ($options as $option) : ?>
		    <div class="me-checkbox">
		    	<label for="<?php echo $field['field_name'] ?>-<?php echo $option['value']; ?>">
	    			<input name="<?php echo $field['field_name'] ?>[]" value="<?php echo $option['value']; ?>" id="<?php echo $field['field_name'] ?>-<?php echo $option['value']; ?>" type="checkbox" <?php if(in_array($option['value'], (array)$value)) {echo 'checked="true"';} ?>>
	    			<span><?php echo $option['label']; ?></span>
	    		</label>	
		    </div>
	    <?php endforeach; ?>
	</div>
</div>