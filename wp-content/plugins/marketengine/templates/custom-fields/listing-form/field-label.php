<label for="me_cf_number_3" class="me-field-title">
	<?php echo $field['field_title'] ?> 

	<?php if(strpos($field['field_constraint'], 'required') === false) : ?>
	<small><?php _e("(optional)", "enginethemes"); ?></small>
	<?php endif; ?>

	<?php if($field['field_help_text']) : ?>
		<i class="me-help-text icon-me-question-circle" title="<?php echo $field['field_help_text']; ?>"></i>
	<?php endif; ?>
</label>