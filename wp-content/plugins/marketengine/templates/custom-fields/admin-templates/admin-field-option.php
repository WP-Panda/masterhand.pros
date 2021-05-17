<?php
	$options_str = '';
	if(isset($field_name)) {
		$options = marketengine_cf_get_field_options($field_name);
		$options_str = marketengine_field_option_to_string($options);
	}
?>
<div class="me-group-field">
	<label class="me-title"><?php _e('Option','enginethemes'); ?></label>
	<p><?php _e('Enter each option on a new line. Each line should follow the rule: <b>Key : Value</b>', 'enginethemes'); ?></p>
	<p><?php _e('Example: option_1 : Option 1', 'enginethemes'); ?></p>
	<span class="me-field-control">
		<textarea required class="me-textarea-field" name="field_options" placeholder="<?php _e('Your option here', 'enginethemes'); ?>"><?php echo $options_str; ?></textarea>
	</span>
</div>