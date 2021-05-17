<?php
/**
 * The templates for displaying custom field manage page
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 *
 * @since 		1.0.1
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$categories = marketengine_get_listing_categories();

?>

<div class="me-custom-field">
	<h2><?php _e('List of Custom Field', 'enginethemes'); ?></h2>
	<?php marketengine_print_notices(); ?>

	<?php marketengine_get_template('custom-fields/admin-templates/category-select', array('categories' => $categories )); ?>

	<a class="me-add-custom-field-btn" href="<?php echo add_query_arg('view', 'add'); ?>"><?php _e('Add New Custom Field', 'enginethemes'); ?></a>
	<?php if(isset($_REQUEST['view']) && $_REQUEST['view'] == 'group-by-category') : ?>
	<p><?php _e('Drag & drop to arrange custom field order.', 'enginethemes'); ?></p>
	<?php endif; ?>

	<div class="me-custom-field-list">
		<ul id="me-cf-list-sortable" class="me-cf-list">
			<?php marketengine_get_template('custom-fields/admin-templates/table-header'); ?>

			<?php
			if(isset($_REQUEST['view']) && $_REQUEST['view'] == 'group-by-category') {
				$customfields = marketengine_cf_get_fields($_REQUEST['category-id']);
				marketengine_get_template('custom-fields/admin-templates/field-list-by-category', array('customfields' => $customfields ));
			} else {
				$customfields = marketengine_cf_fields_query($_REQUEST);
				marketengine_get_template('custom-fields/admin-templates/field-list', array('customfields' => $customfields ));
			}
			?>
		</ul>
	</div>

	<?php if( !isset($_REQUEST['view']) ) : ?>
	<div class="me-pagination-wrap">
		<span class="me-paginations">
		<?php marketengine_cf_pagination( $customfields ); ?>
		</span>
	</div>
	<?php endif; ?>
</div>