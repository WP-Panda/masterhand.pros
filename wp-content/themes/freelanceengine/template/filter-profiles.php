<?php
global $wp_query;

$project_category             = $wp_query->query['project_category'];
$category_profile_selected    = '';
$subcategory_profile_selected = '';

if ( isset( $project_category ) && $project_category != '' ) {
	$taxonomy_project_category    = get_term_by( 'slug', $project_category, 'project_category', ARRAY_A );
	$subcategory_profile_selected = $taxonomy_project_category['slug'];
	if ( ! empty( $taxonomy_project_category['parent'] ) ) {
		$taxonomy_parent_project_category = get_term_by( 'term_id', $taxonomy_project_category['parent'], 'project_category', ARRAY_A );
		$category_profile_selected        = $taxonomy_parent_project_category['slug'];
	} else {
		$category_profile_selected    = $taxonomy_project_category['slug'];
		$subcategory_profile_selected = '';
	}
}
?>
<div class="fre-profile-filter-box">
    <script type="data/json" id="search_data">
        <?php
		$search_data = $_POST;
		echo json_encode( $search_data );
		?>




    </script>
    <div class="profile-filter-header visible-sm visible-xs">
        <a class="profile-filter-title" href=""><?php _e( 'Advanced search', ET_DOMAIN ); ?></a>
    </div>
    <div class="fre-profile-list-filter">
        <form onsubmit="return false;">
            <div class="row">
                <div class="col-md-4">
                    <div class="fre-input-field">
                        <label for="keywords" class="fre-field-title"><?php _e( 'Keyword', ET_DOMAIN ); ?></label>
                        <input class="keyword search" id="s" type="text" name="s"
                               placeholder="<?php _e( 'Search professionals by keyword', ET_DOMAIN ); ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="fre-input-field">
                        <input type="hidden" name="type_filter" value="project_category">
                        <label for="cat"
                               class="fre-field-title"><?php _e( 'Category', ET_DOMAIN ); ?></label>
                        <div class="select_style">
							<?php ae_tax_dropdown( 'project_category', [
								'attr'            => 'data-selected_slug="' . $category_profile_selected . '"',
								//'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="' . __("Select cat", ET_DOMAIN) . '"',
								'show_option_all' => __( "Select category", ET_DOMAIN ),
								'class'           => '',
								//'fre-chosen-single',
								'hide_empty'      => false,
								'hierarchical'    => false,
								'selected'        => $category_profile_selected,
								'id'              => 'cat',
								'value'           => 'slug',
								//'include' => (is_tax()) ? $terms : 'all'
								'parent'          => 0,
								'name'            => 'cat',
							] ); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fre-input-field">
                        <label for="sub"
                               class="fre-field-title"><?php _e( 'Subcategory', ET_DOMAIN ); ?></label>
                        <div class="select_style">
                            <select name="sub" id="sub" data-selected_slug="<?php echo $subcategory_profile_selected ?>">
                                <option value="">Select category first</option>
                            </select>
                        </div>


                    </div>
                </div>

                <div class="clearfix"></div>
				<?php include get_stylesheet_directory() . '/inc/filter-location.php'; ?>
            </div>
            <a class="profile-filter-clear clear-filter secondary-color"
               href=""><?php _e( 'Clear all filters', ET_DOMAIN ); ?></a>
        </form>
    </div>
</div>