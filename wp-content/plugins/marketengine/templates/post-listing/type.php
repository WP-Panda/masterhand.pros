<?php
/**
 * The template for displaying listing type on post listing page.
 *
 * This template can be override by copying it to yourtheme/marketengine/post-listing/listing-type.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 *
 * @since 		1.0.0
 *
 * @version 	1.0.0
 *
 */
$listing_types = marketengine_get_listing_types();
$selected_listing_type = empty($_POST['listing_type']) ? $selected_listing_type : esc_attr( $_POST['listing_type'] );

if (!empty($_POST['meta_input']['contact_email'])) {$contact_email = esc_attr($_POST['meta_input']['contact_email']);}
if (!empty($_POST['meta_input']['listing_price'])) {$price =  esc_attr($_POST['meta_input']['listing_price']);}
if (!empty($_POST['meta_input']['pricing_unit'])) {$unit =  esc_attr($_POST['meta_input']['pricing_unit']);}
if (isset($_POST['meta_input']['listing_price']) && empty($_POST['meta_input']['listing_price']) ) {
	$price = '';
}
if (!isset($editing)) $editing = false;
?>

<?php do_action('marketengine_before_post_listing_type_form'); ?>

<div class="marketengine-group-field" id="listing-type-container">

	<?php do_action('marketengine_post_listing_type_form_start'); ?>

	<div class="marketengine-select-field">
	    <label class="me-field-title"><?php _e("Listing Type", "enginethemes"); ?></label>
	    <select <?php disabled( $editing); ?> class="listing-type me-chosen-select" name="listing_type" id="listing-type-select">
	    	<?php foreach ($listing_types as $type => $name) : ?>
	    		<option value="<?php echo $type ?>" <?php selected( $selected_listing_type, $type) ?> <?php disabled(!marketengine_is_listing_type_available($type)); ?> >
	    			<?php echo $name; ?>
	    		</option>
	    	<?php endforeach; ?>
	    </select>
	</div>

	<?php do_action('marketengine_post_listing_type_form_end'); ?>

</div>
<div class="marketengine-<?php echo $selected_listing_type; ?>">

	<?php do_action('marketengine_post_listing_type_form_fields_start'); ?>

	<div class="me-row me-pricing-container listing-type-info" id="purchasion-type-field" <?php if($selected_listing_type !='purchasion') echo 'style="display:none";'; ?> >

		<?php do_action('marketengine_post_listing_price_form_start'); ?>

		<div class="me-col-md-6">
			<div class="marketengine-group-field">
				<div class="marketengine-input-field">
				    <label class="me-field-title"><?php _e("Price", "enginethemes"); ?></label>
				    <input type="text" name="meta_input[listing_price]" placeholder="<?php echo marketengine_option('payment-currency-sign'); ?>" class="required me-input-price" value="<?php echo $price; ?>">
				</div>
			</div>
		</div>

		<div class="me-col-md-6">
			<div class="marketengine-group-field">
				<div class="marketengine-select-field">
				    <label class="me-field-title"><?php _e("Pricing Unit", "enginethemes"); ?></label>
				    <select class="pricing-unit me-chosen-select" name="meta_input[pricing_unit]">
				    	<option value="none" <?php if(!$unit) echo 'selected'; ?>><?php _e("None", "enginethemes"); ?></option>
				    	<option value="per_unit" <?php if($unit == 'per_unit') echo 'selected'; ?> ><?php _e("Per Unit", "enginethemes"); ?></option>
				    	<option value="per_hour" <?php if($unit == 'per_hour') echo 'selected'; ?> ><?php _e("Per Hour", "enginethemes"); ?></option>
				    </select>
				</div>
			</div>
		</div>

		<?php do_action('marketengine_post_listing_price_form_end'); ?>

	</div>

	<?php do_action('marketengine_post_listing_type_form_fields'); ?>

	<?php do_action('marketengine_post_listing_type_form_fields_end'); ?>

	<?php if($editing) : ?>
		<input type="hidden" name="listing_type" value="<?php echo $selected_listing_type; ?>">
	<?php endif; ?>

</div>
<?php do_action('marketengine_after_post_listing_type_form'); ?>