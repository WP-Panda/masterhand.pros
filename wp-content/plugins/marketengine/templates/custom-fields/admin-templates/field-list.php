<?php
	if($customfields['found_posts']) :
		foreach($customfields['fields'] as $key => $field) :
			extract($field);
			$affected_cats_name = marketengine_cf_get_field_categories($field_id, true);
?>

	<li class="me-cf-item">
		<div class="me-cf-row">
			<div class="me-cf-row-wrap">
				<div class="me-cf-col-title"><?php echo esc_attr($field_name); ?></div>
				<div class="me-cf-col-title"><?php echo esc_attr($field_title); ?></div>
				<div class="me-cf-col-number">
					<?php echo $count; ?>
					<div class="me-cf-action">
						<a class="me-cf-show" href="" title="<?php _e('Show\Hide custom field', 'enginethemes'); ?>"><span><i class="icon-me-eye"></i><i class="icon-me-eye-slash"></i></span></a>
						<a class="me-cf-edit" href="<?php echo add_query_arg(array('view' => 'edit', 'custom-field-id' => $field_id)); ?>" title="<?php _e('Edit custom field', 'enginethemes'); ?>"><i class="icon-me-edit-pad"></i></a>
						<a class="me-cf-remove" href="<?php echo add_query_arg(array('action' => 'delete-custom-field', '_wp_nonce' => wp_create_nonce('delete-custom-field'), 'custom-field-id' => $field_id)); ?>" title="<?php _e('Delete custom field', 'enginethemes'); ?>"><i class="icon-me-trash"></i></a>
					</div>
				</div>
			</div>
		</div>
		<div class="me-cf-row-content">
			<table>
				<tr>
					<td><span><?php _e('Field type:', 'enginethemes'); ?></span></td>
					<td><?php echo marketengine_get_field_type_label($field_type); ?></td>
				</tr>

				<?php do_action('marketengine_load_inputs_for_view', $field); ?>
				<tr>
					<td><span><?php _e('Required:', 'enginethemes'); ?></span></td>
					<td><?php echo (strpos($field_constraint, 'equired')) ? __('Yes', 'enginethemes') : __('No', 'enginethemes') ; ?></td>
				</tr>
				<tr>
					<td><span><?php _e('Available in:', 'enginethemes'); ?></span></td>
					<td><?php echo $affected_cats_name; ?></td>
				</tr>
				<tr>
					<td><span><?php _e('Help text:', 'enginethemes'); ?></span></td>
					<td></span><?php echo $field_help_text ? $field_help_text : 'N/A'; ?></td>
				</tr>
				<tr>
					<td><span><?php _e('Description:', 'enginethemes'); ?></span></td>
					<td><?php echo $field_description ? $field_description : 'N/A'; ?></td>
				</tr>

			
			</table>
		</div>
	</li>

	<?php endforeach; ?>
<?php else : ?>
	<?php marketengine_get_template('custom-fields/admin-templates/field-list-no-fields'); ?>
<?php endif; ?>