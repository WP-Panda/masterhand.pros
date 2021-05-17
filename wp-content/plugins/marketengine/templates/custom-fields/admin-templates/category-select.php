<div class="me-cf-by-category">
	<label>
		<span><?php _e('Group by', 'enginethemes'); ?></span>
		<?php $link = remove_query_arg('paged'); ?>
		<select name="" id="" onchange="window.location.href=this.value;">
			<option value="<?php echo marketengine_custom_field_page_url(); ?>"><?php _e('All category', 'enginethemes'); ?></option>

		<?php foreach($categories as $key => $category) : ?>
			<option <?php selected(isset($_REQUEST['category-id']) && $key==$_REQUEST['category-id']); ?> value="<?php echo add_query_arg(array('view' => 'group-by-category', 'category-id' => $key), $link); ?>"><?php echo $category; ?></option>
		<?php endforeach; ?>
		</select>
	</label>
</div>