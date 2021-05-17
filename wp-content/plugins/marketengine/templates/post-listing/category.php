<?php
$listing_type_categories = marketengine_get_listing_type_categories();
$selected_cat = empty($_POST['parent_cat']) ? $selected_cat : absint( $_POST['parent_cat'] );
$listing_type_categories['all'][] = $selected_cat;

$parent_categories = get_terms(array('taxonomy' => 'listing_category', 'hide_empty' => false, 'parent' => 0, 'include' => $listing_type_categories['all']));

if ($selected_cat) {
    $child_cats = get_terms(array('taxonomy' => 'listing_category', 'hide_empty' => false, 'parent' => $selected_cat));
}
$selected_sub_cat = empty($_POST['sub_cat']) ? $selected_sub_cat : absint( $_POST['sub_cat'] );
if(!isset($editing)) $editing = false;
?>

<?php do_action('marketengine_before_post_listing_category_form');?>

<div class="marketengine-post-step active select-category">
	<div class="marketengine-group-field" id="me-parent-cat-container">
		<div class="marketengine-select-field">
		    <label class="me-field-title"><?php _e("Category", "enginethemes");?></label>
		    <select <?php disabled($editing); ?> class="select-category me-parent-category me-chosen-select" name="parent_cat">
		    	<option value=""><?php _e("Select your category", "enginethemes");?></option>
		    	<?php foreach ($parent_categories as $key => $parent_cat): ?>
			    	<option value="<?php echo $parent_cat->term_id; ?>" <?php selected($selected_cat, $parent_cat->term_id);?> >
			    		<?php echo $parent_cat->name; ?>
			    	</option>
		    	<?php endforeach;?>
		    </select>
		</div>
	</div>
	<div class="marketengine-group-field" id="me-sub-cat-container">
		<div class="marketengine-select-field">
		    <label class="me-field-title"><?php _e("Sub-category", "enginethemes");?></label>
		    <select <?php disabled( empty($child_cats) || $editing ); ?> class="select-category me-sub-category me-chosen-select" name="sub_cat">
		    	<option value=""><?php _e("Select sub category", "enginethemes");?></option>
		    	<?php if (!empty($child_cats)) : ?>
		    	<?php foreach ($child_cats as $key => $sub_cat): ?>
			    	<option value="<?php echo $sub_cat->term_id; ?>" <?php selected($selected_sub_cat, $sub_cat->term_id);?> >
			    		<?php echo $sub_cat->name; ?>
			    	</option>
		    	<?php endforeach;?>
		    	<?php endif; ?>
		    </select>
		</div>
	</div>

<?php if($editing) : ?>
	<input type="hidden" name="parent_cat" value="<?php echo $selected_cat; ?>">
	<input type="hidden" name="sub_cat" value="<?php echo $selected_sub_cat; ?>">
<?php endif; ?>
</div>

<?php do_action('marketengine_after_post_listing_category_form');?>