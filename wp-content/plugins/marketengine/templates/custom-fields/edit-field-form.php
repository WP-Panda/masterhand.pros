<?php
/**
 * Render edit listing custom field form
 * This template can be overridden by copying it to yourtheme/marketengine/custom-fields/edit-field-form.php.
 * 
 * @package     MarketEngine/Templates
 * @version     1.0.1
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if($fields) :
?>
<div class="marketengine-custom-field">
	<?php foreach ($fields as $field) : ?>

		<?php 
			$value = marketengine_field($field['field_name'], $listing, array('fields' => 'ids'));
			marketengine_get_template('custom-fields/listing-form/field-'. $field['field_type'], array('field' => $field, 'value' => $value)); 
		?>

	<?php endforeach; ?>

</div>
<?php endif; ?>
