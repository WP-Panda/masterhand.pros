<?php
$parent_cat = absint( $_REQUEST['parent-cat'] );

// $child_categories = get_terms( array('taxonomy' => 'listing_category', 'hide_empty' => false, 'parent' => $parent_cat) );

if(!$parent_cat || empty($child_categories)) : ?>

	<option value=""><?php _e("Select sub category", "enginethemes"); ?></option>

<?php else: ?>

	<option value=""><?php _e("Select sub category", "enginethemes"); ?></option>
	<?php foreach ($child_categories as $key => $child_cat) : ?>
	<option value="<?php echo $child_cat->term_id; ?>"><?php echo $child_cat->name; ?></option>
	<?php endforeach; ?>

<?php endif; ?>