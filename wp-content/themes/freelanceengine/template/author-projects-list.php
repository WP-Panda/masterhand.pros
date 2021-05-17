<?php //global $wpdb, $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
//$user_role = ae_user_role($user_ID);
define('NO_RESULT', __('<span class="project-no-results">There are no activities yet.</span>', ET_DOMAIN));
$currency = ae_get_option('currency', array('align' => 'left', 'code' => 'USD', 'icon' => '$'));
?>
<ul class="fre-tabs nav-tabs-my-work">
    <li class="active"><a data-toggle="tab" href="#current-project-tab"><span><?php _e('Current Projects', ET_DOMAIN); ?></span></a>
    </li>
    <li class="next"><a data-toggle="tab" href="#previous-project-tab"><span><?php _e('Previous Projects', ET_DOMAIN); ?></span></a>
    </li>
</ul>
<div class="fre-tab-content">
    <div id="current-project-tab" class="employer-current-project-tab fre-panel-tab active">
        <?php
                         /*       $employer_current_project_query = new WP_Query(
                                    array(
                                        'post_status' => array(
                                            'close',
                                            'disputing',
                                            'publish',
                                            'pending',
                                            'draft',
                                            'reject',
                                            'archive'
                                        ),
                                        'is_author' => true,
                                        'post_type' => PROJECT,
                                        'author' => $user_ID,
                                        'suppress_filters' => true,
                                        'orderby' => 'date',
                                        'order' => 'DESC'
                                    )
                                );

                                $post_object = $ae_post_factory->get(PROJECT);
                                $no_result_current = '';
                                ?>
            <div class="fre-work-project-box">
                <div class="work-project-filter">
                    <form>
                        <div class="row">
                            <div class="col-md-8 col-sm-6 col-xs-12">
                                <div class="fre-input-field">
                                    <label class="fre-field-title"><?php _e('Keyword', ET_DOMAIN); ?></label>
                                    <input type="text" class="search" name="s" placeholder="<?php _e('Search projects by keywords', ET_DOMAIN); ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="fre-input-field">
                                    <label class="fre-field-title"><?php _e('Status', ET_DOMAIN); ?></label>
                                    <select class="fre-chosen-single" name="project_current_status">
                                                            <option value=""><?php _e('All', ET_DOMAIN); ?></option>
                                                            <option value="close"><?php _e('Processing', ET_DOMAIN); ?></option>
                                                            <option value="disputing"><?php _e('Disputed', ET_DOMAIN); ?></option>
                                                            <option value="publish"><?php _e('Active', ET_DOMAIN); ?></option>
                                                            <option value="pending"><?php _e('Pending', ET_DOMAIN); ?></option>
                                                            <option value="draft"><?php _e('Draft', ET_DOMAIN); ?></option>
                                                            <option value="reject"><?php _e('Rejected', ET_DOMAIN); ?></option>
                                                            <option value="archive"><?php _e('Archived', ET_DOMAIN); ?></option>
                                                        </select>
                                </div>
                            </div>
                        </div>
                        <a class="clear-filter work-project-filter-clear secondary-color" href="">
                            <?php _e('Clear all filters', ET_DOMAIN); ?>
                        </a>
                    </form>
                </div>
            </div>
            <div class="fre-work-project-box">
                <div class="current-employer-project">
                    <div class="fre-table">
                        <div class="fre-table-head">
                            <div class="fre-table-col project-title-col">
                                <?php _e('Project Title', ET_DOMAIN); ?>
                            </div>
                            <div class="fre-table-col project-bids-col">
                                <?php _e('Number Bids', ET_DOMAIN); ?>
                            </div>
                            <div class="fre-table-col project-budget-col">
                                <?php _e('Budget', ET_DOMAIN); ?>
                            </div>
                            <div class="fre-table-col project-open-col">
                                <?php _e('Open Date', ET_DOMAIN); ?>
                            </div>
                            <div class="fre-table-col project-status-col">
                                <?php _e('Status', ET_DOMAIN); ?>
                            </div>
                            <div class="fre-table-col project-action-col">
                                <?php _e('Action', ET_DOMAIN); ?>
                            </div>
                        </div>
                        <div class="fre-current-table-rows" style="display: table-row-group;">
                            <?php

                                                if ($employer_current_project_query->have_posts()) {
                                                    $postdata = array();
                                                while ($employer_current_project_query->have_posts()) {
                                                    $employer_current_project_query->the_post();
                                                    $convert = $post_object->convert($post, 'thumbnail');
                                                    $postdata[] = $convert;
                                                    $project_status = $convert->post_status;
                                                    $optionsProject = optionsProject($convert);
                                                    ?>
                                <div class="fre-table-row" <?=$ optionsProject[ 'highlight_project'] ?> >
                                    <div class="fre-table-col project-title-col">
                                        <a class="secondary-color" href="<?php echo $convert->permalink; ?>">
                                            <?php echo $convert->post_title; ?>
                                            <?php //new2
                                                                echo $optionsProject['create_pro_project'];
                                                                echo $optionsProject['priority_in_list_project'];
                                                                echo $optionsProject['urgent_project'];
                                                                echo $optionsProject['hidden_project'];
                                                                //new2?>
                                        </a>
                                    </div>
                                    <div class="fre-table-col project-bids-col">
                                        <?php echo $convert->total_bids; ?>
                                        <span><?php _e('Bids', ET_DOMAIN); ?></span></div>
                                    <div class="fre-table-col project-budget-col">
                                        <span><?php _e('Budget', ET_DOMAIN); ?></span>
                                        <?php echo $convert->budget; ?>
                                    </div>
                                    <div class="fre-table-col project-open-col">
                                        <span><?php _e('Open on', ET_DOMAIN); ?></span>
                                        <?php echo $convert->post_date; ?>
                                    </div>
                                    <div class="fre-table-col project-status-col">
                                        <?php echo $convert->project_status_view; ?>
                                    </div>
                                    <?php
                                                        if ($project_status == 'close') {
                                                            echo '<div class="fre-table-col project-action-col">';
                                                            echo '<a href="' . add_query_arg(array('workspace' => 1), $convert->permalink) . '" target="_blank">' . __('Workspace', ET_DOMAIN) . '</a>';
                                                            echo '</div>';
                                                        } else if ($project_status == 'disputing') {
                                                            echo '<div class="fre-table-col project-action-col">';
                                                            echo '<a href="' . add_query_arg(array('dispute' => 1), $convert->permalink) . '" target="_blank">' . __('Dispute Page', ET_DOMAIN) . '</a>';
                                                            echo '</div>';
                                                        } else if ($project_status == 'publish') {
                                                            echo '<div class="fre-table-col project-action-col">';
                                                            echo '<a class="project-action" data-action="archive" data-project-id="' . $convert->ID . '">' . __('Archive', ET_DOMAIN) . '</a>';
                                                            echo '</div>';
                                                        } else if ($project_status == 'pending') {
                                                            echo '<div class="fre-table-col project-action-col">';
                                                            echo '<a href="' . et_get_page_link('edit-project', array('id' => $convert->ID)) . '" target="_blank">' . __('Edit', ET_DOMAIN) . '</a>';
                                                            echo '</div>';
                                                        } else if ($project_status == 'draft') {
                                                            echo '<div class="fre-table-col project-action-col project-action-two">';
                                                            echo '<a href="' . et_get_page_link('submit-project', array('id' => $convert->ID)) . '" target="_blank">' . __('Edit', ET_DOMAIN) . '</a>';
                                                            echo '<a class="project-action" data-action="delete" data-project-id="' . $convert->ID . '">' . __('Delete', ET_DOMAIN) . '</a>';
                                                            echo '</div>';
                                                        } else if ($project_status == 'reject') {
                                                            echo '<div class="fre-table-col project-action-col">';
                                                            echo '<a href="' . et_get_page_link('edit-project', array('id' => $convert->ID)) . '" target="_blank">' . __('Edit', ET_DOMAIN) . '</a>';
                                                            echo '</div>';
                                                        } else if ($project_status == 'archive') {
                                                            echo '<div class="fre-table-col project-action-col project-action-two">';
                                                            echo '<a href="' . et_get_page_link('submit-project', array('id' => $convert->ID)) . '" target="_blank">' . __('Renew', ET_DOMAIN) . '</a>';
                                                            echo '<a class="project-action" data-action="delete" data-project-id="' . $convert->ID . '">' . __('Delete', ET_DOMAIN) . '</a>';
                                                            echo '</div>';
                                                        }
                                                        ?>
                                </div>
                                <?php } ?>
                                <script type="data/json" id="current_project_post_data">
                                    <?php echo json_encode($postdata); ?>
                                </script>
                                <?php } else {
                                                    $no_result_current = NO_RESULT;
                                                }
                                                ?>
                        </div>
                    </div>
                    <?php
                                        if ($no_result_current != '') {
                                            echo $no_result_current;
                                        }
                                        ?>
                </div>
            </div>
            <div class="fre-paginations paginations-wrapper">
                <div class="paginations">
                    <?php ae_pagination($employer_current_project_query, get_query_var('paged')); ?>
                </div>
            </div>
            <?php
                                wp_reset_postdata();
                                wp_reset_query();
                                ?>
    </div>
    <div id="previous-project-tab" class="employer-previous-project-tab fre-panel-tab">
        <?php
                                $employer_previous_project_query = new WP_Query(
                                    array(
                                        'post_status' => array('complete', 'disputed'),
                                        'is_author' => true,
                                        'post_type' => PROJECT,
                                        'author' => $user_ID,
                                        'suppress_filters' => true,
                                        'orderby' => 'date',
                                        'order' => 'DESC'
                                    )
                                );
                                $post_object = $ae_post_factory->get(PROJECT);
                                $no_result_previous = '';
                                ?>
            <div class="fre-work-project-box">
                <div class="work-project-filter">
                    <form>
                        <div class="row">
                            <div class="col-md-8 col-sm-6 col-xs-12">
                                <div class="fre-input-field">
                                    <label class="fre-field-title"><?php _e('Keyword', ET_DOMAIN); ?></label>
                                    <input type="text" class="search" name="s" placeholder="<?php _e('Search projects by keywords', ET_DOMAIN); ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="fre-input-field">
                                    <label class="fre-field-title"><?php _e('Status', ET_DOMAIN); ?></label>
                                    <select class="fre-chosen-single" name="project_previous_status">
                                                            <option value=""><?php _e('All', ET_DOMAIN); ?></option>
                                                            <option value="complete"><?php _e('Completed', ET_DOMAIN); ?></option>
                                                            <option value="disputed"><?php _e('Resolved', ET_DOMAIN); ?></option>
                                                        </select>
                                </div>
                            </div>
                        </div>
                        <a class="clear-filter work-project-filter-clear secondary-color" href="">
                            <?php _e('Clear all filters', ET_DOMAIN); ?>
                        </a>
                    </form>
                </div>
            </div>
            <div class="fre-work-project-box">
                <div class="previous-employer-project">
                    <div class="fre-table">
                        <div class="fre-table-head">
                            <div class="fre-table-col project-title-col">
                                <?php _e('Project Title', ET_DOMAIN); ?>
                            </div>
                            <div class="fre-table-col project-start-col">
                                <?php _e('Start Date', ET_DOMAIN); ?>
                            </div>
                            <div class="fre-table-col project-bid-col">
                                <?php _e('Bid Won', ET_DOMAIN); ?>
                            </div>
                            <div class="fre-table-col project-status-col">
                                <?php _e('Status', ET_DOMAIN); ?>
                            </div>
                            <div class="fre-table-col project-review-col">
                                <?php _e('Review', ET_DOMAIN); ?>
                            </div>
                        </div>
                        <div class="fre-previous-table-rows" style="display: table-row-group;">
                            <?php
                                                if ($employer_previous_project_query->have_posts()) {
                                                    $postdata = array();
                                                while ($employer_previous_project_query->have_posts()) {
                                                    $employer_previous_project_query->the_post();
                                                    $convert = $post_object->convert($post, 'thumbnail');
                                                    $postdata[] = $convert;
                                                    ?>
                                <div class="fre-table-row">
                                    <div class="fre-table-col project-title-col">
                                        <a class="secondary-color" href="<?php echo $convert->permalink; ?>">
                                            <?php echo $convert->post_title; ?>
                                        </a>
                                    </div>
                                    <div class="fre-table-col project-start-col">
                                        <?php echo $convert->post_date; ?>
                                    </div>
                                    <div class="fre-table-col project-bid-col">
                                        <span><?php _e('Bid won:', ET_DOMAIN); ?></span><b><?php echo $convert->bid_budget_text; ?></b><span><?php echo $convert->bid_won_date; ?></span>
                                    </div>
                                    <div class="fre-table-col project-status-col">
                                        <?php echo $convert->project_status_view; ?>
                                    </div>
                                    <div class="fre-table-col project-review-col">
                                        <?php if (isset($convert->win_disputed) && $convert->win_disputed != '') {
                                                                if ($convert->win_disputed == EMPLOYER) {
                                                                    echo '<i>';
                                                                    _e('Won dispute', ET_DOMAIN);
                                                                    echo '</i>';
                                                                } else {
                                                                    echo '<i>';
                                                                    _e('Lost dispute', ET_DOMAIN);
                                                                    echo '</i>';
                                                                }
                                                            } else {
                                                                if ($convert->rating_score > 0) {
                                                                    echo '<span class="rate-it" data-score="' . $convert->rating_score . '"></span>';
                                                                } else {
                                                                    _e('<i>No rating & review yet.</i>', ET_DOMAIN);
                                                                }
                                                                if (isset($convert->project_comment) && !empty($convert->project_comment)) {
                                                                    echo '<p>' . $convert->project_comment . '</p>';
                                                                }
                                                            } ?>
                                    </div>
                                </div>
                                <?php } ?>
                                <script type="data/json" id="previous_project_post_data">
                                    <?php echo json_encode($postdata); ?>
                                </script>
                                <?php } else {
                                                    $no_result_previous = NO_RESULT;
                                                }
                                                ?>
                        </div>
                    </div>
                    <?php
                                        if ($no_result_previous != '') {
                                            echo $no_result_previous;
                                        }
                                        ?>
                </div>
            </div>
            <div class="fre-paginations paginations-wrapper">
                <div class="paginations">
                    <?php ae_pagination($employer_previous_project_query, get_query_var('paged')); ?>
                </div>
            </div>
            <?php
                                wp_reset_postdata();
                                wp_reset_query();
                                ?>
    </div>

</div>
