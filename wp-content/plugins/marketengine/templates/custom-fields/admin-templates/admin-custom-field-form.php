<?php
if(isset($field_obj)) {
	$_POST = $field_obj;
}

$constraint = marketengine_field_attribute_array($_POST);

?>
<div class="me-custom-field">
	<?php marketengine_print_notices(); ?>
	<h2><?php _e('Add New Custom Field', 'enginethemes'); ?></h2>
	<form method="post" id="me-custom-field-form">

		<div class="me-group-field">
			<label for="me-cf-field-name" class="me-title"><?php _e('Field Name', 'enginethemes'); ?></label>
			<span class="me-field-control">
				<input <?php disabled( isset($_POST['field_name']) && !empty($_POST['field_name']) ); ?> data-old-field-name="<?php echo isset($_POST['field_name']) ? esc_attr($_POST['field_name']) : ''; ?>" required id="me-cf-field-name" name="field_name" class="me-input-field " type="text" value="<?php echo isset($_POST['field_name']) ? esc_attr($_POST['field_name']) : ''; ?>">

				<?php if(isset($_REQUEST['view']) && $_REQUEST['view'] == 'edit') : ?>
				<input type="hidden" name="field_name" value="<?php echo isset($_POST['field_name']) ? esc_attr($_POST['field_name']) : ''; ?>">
				<?php endif; ?>
			</span>
		</div>

		<div class="me-group-field">
			<label for="" class="me-title"><?php _e('Field Title', 'enginethemes'); ?></label>
			<span class="me-field-control">
				<input required aria-required="true" id="me-cf-field-title" name="field_title" class="me-input-field " type="text" value="<?php echo isset($_POST['field_title']) ? esc_attr($_POST['field_title']) : ''; ?>">
			</span>
		</div>

		<div class="me-group-field">
			<label for="" class="me-title"><?php _e('Field Type', 'enginethemes'); ?></label>
			<span class="me-select-control">

				<select <?php disabled( isset($_POST['field_name']) && !empty($_POST['field_name']) ); ?> required id="me-choose-field-type" class="select-field" name="field_type">
					<option value=""><?php _e('Choose field type', 'enginethemes'); ?></option>
					<?php
						$field_types = marketengine_list_custom_field_type();
						$field_type = isset($_POST['field_type']) ? $_POST['field_type'] : '';
						if(!empty($field_types)) :
					?>
					<?php foreach ($field_types as $key => $group_value) : ?>
			            <optgroup label="<?php echo $group_value['label']; ?>">
			            <?php foreach ($group_value['options'] as $key => $value) : ?>
			                <option <?php selected($field_type, $key); ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
			            <?php endforeach; ?>
			            </optgroup>
			        <?php endforeach; ?>
			    	<?php endif; ?>
				</select>

			</span>

			<div class="me-field-type-options">
			<?php
				if (isset($_POST['field_type'])) {
					do_action('marketengine_load_cf_input');
				}
			?>
			</div>

		</div>

		<div class="me-group-field">
		<?php
			$checked = (isset($constraint['required']) || !empty($constraint['required'])) ? 'required' : '';
			if($_REQUEST['view'] == 'add') {
				$checked = 'required';
			} else {
				$checked = isset($constraint['required']) ? 'required' : '';
			}
		?>

			<label class="me-title"><?php _e('Required?', 'enginethemes'); ?></label>
			<span class="me-radio-field">
				<label class="me-radio" for="me-field-required-yes"><input id="me-field-required-yes" type="radio" name="field_constraint" value="required" <?php checked($checked, 'required'); ?>><span><?php _e('Yes', 'enginethemes'); ?></span></label>
				<label class="me-radio" for="me-field-required-no"><input id="me-field-required-no" type="radio" name="field_constraint" value="" <?php checked($checked, ''); ?>><span><?php _e('No', 'enginethemes'); ?></span></label>
			</span>
		</div>

		<div class="me-group-field">
			<label for="" class="me-title"><?php _e('Available In Which Categories', 'enginethemes'); ?></label>
			<span class="me-select-control">
				<select required class="select-field" name="field_for_categories[]" id="field_for_categories" multiple="true">

				<?php
					$categories = marketengine_get_listing_categories();
					if(!isset($_POST['field_id'])) {
						if(!isset($_REQUEST['category-id'])) {
							$selected = isset($_POST['field_id']) ? $selected : array_keys($categories);
						} else {
							$selected = array($_REQUEST['category-id']);
						}
					} else {
						$selected = isset($_POST['field_for_categories']) ? $_POST['field_for_categories'] : array();
					}

					foreach ($categories as $key => $value) :
				?>
					<option <?php selected(in_array($key, $selected)); ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
				<?php endforeach; ?>

				</select>
			</span>
		</div>

		<div class="me-group-field">
			<label for="" class="me-title"><?php _e('Help text', 'enginethemes'); ?> <small>(<?php _e('optional', 'enginethemes'); ?>)</small></label>
			<span class="me-subtitle"><?php _e('Help text for fields of post listing form', 'enginethemes'); ?></span>
			<textarea class="me-textarea-field" name="field_help_text"><?php echo isset($_POST['field_help_text']) ? $_POST['field_help_text'] : ''; ?></textarea>
		</div>

		<div class="me-group-field">
			<label for="" class="me-title"><?php _e('Description', 'enginethemes'); ?> <small>(<?php _e('optional', 'enginethemes'); ?>)</small></label>
			<textarea class="me-textarea-field" name="field_description"><?php echo isset($_POST['field_description']) ? $_POST['field_description'] : ''; ?></textarea>
		</div>

		<?php if(isset($_REQUEST['view']) && $_REQUEST['view'] == 'edit') {
			wp_nonce_field( 'me-update_custom_field' );
		} else {
			wp_nonce_field( 'me-insert_custom_field' );
		} ?>

		<input type="submit" class="me-cf-save-btn" name="insert-custom-field" value="<?php _e('Save', 'enginethemes'); ?>"><a href="<?php echo isset($_REQUEST['category-id']) ? add_query_arg('view', 'group-by-category', remove_query_arg('custom-field-id')) : remove_query_arg(array('view', 'custom-field-id')); ?>" class="me-cf-cancel-btn"><?php _e('Cancel', 'enginethemes'); ?></a>

		<input type="hidden" name="redirect" value="<?php echo isset($_REQUEST['category-id']) ? add_query_arg('view', 'group-by-category', remove_query_arg('custom-field-id')) : remove_query_arg(array('view', 'custom-field-id')); ?>">

		<input type="hidden" id="me-cf-current-field-id" value="<?php echo isset($_REQUEST['custom-field-id']) ? $_REQUEST['custom-field-id'] : -1; ?>">
	</form>
</div>