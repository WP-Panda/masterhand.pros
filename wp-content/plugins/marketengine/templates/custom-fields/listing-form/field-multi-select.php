<?php
/**
 * The Template for field multi select form
 *
 * This template can be overridden by copying it to yourtheme/marketengine/custom-fields/listing-form/field-multi-select.php.
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
	<div class="marketengine-input-field">
	    <?php marketengine_get_template('custom-fields/listing-form/field-label', array('field' => $field));  ?>
	    <select name="<?php echo $field['field_name'] ?>[]" id="<?php echo $field['field_name'] ?>" class="me-chosen-select me-cf-chosen" multiple="true"  data-placeholder="<?php echo $field['field_placeholder']; ?>">
	    	<?php foreach ($options as $option) : ?>
	    		<option value="<?php echo $option['value'] ?>" <?php if(in_array($option['value'], (array)$value)) {echo 'selected="true"';} ?>>
	    			<?php echo $option['label']; ?>
	    		</option>
	    	<?php endforeach; ?>
	    </select>
	</div>
</div>