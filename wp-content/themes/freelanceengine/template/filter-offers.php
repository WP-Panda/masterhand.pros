<?php
global $wp_query;

?>
<div class="fre-project-filter-box">
    <script type="data/json" id="search_data">
        <?php
        $search_data = $_POST;
        echo json_encode($search_data);
        ?>

    </script>
    <div class="project-filter-header visible-sm visible-xs">
        <a class="project-filter-title" href=""><?php _e('Advanced search', ET_DOMAIN); ?></a>
    </div>
    <div class="fre-offer-list-filter">
        <form onsubmit="return false;">
            <div class="row">
                <div class="col-md-4">
                    <div class="fre-input-field">
                        <label for="keywords" class="fre-field-title"><?php _e('Keyword', ET_DOMAIN); ?></label>
                        <input class="keyword search" id="s" type="text" name="s"
                               placeholder="<?php _e('Search by keyword', ET_DOMAIN); ?>">
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php include get_stylesheet_directory() . '/inc/filter-location.php'; ?>
            </div>
            <a class="project-filter-clear clear-filter secondary-color"
               href=""><?php _e('Clear all filters', ET_DOMAIN); ?></a>
        </form>
    </div>
</div>

