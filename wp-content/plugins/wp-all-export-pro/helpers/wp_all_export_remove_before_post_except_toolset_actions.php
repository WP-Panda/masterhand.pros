<?php

function wp_all_export_remove_before_post_except_toolset_actions()
{
    // Extract preserved actions
    $actionsToPreserve = [];

    if(class_exists('WPCF_Post_Types') && class_exists('WPToolset_Forms_Bootstrap') && class_exists('Toolset_Wp_Query_Adjustments_M2m')) {

        global $wp_filter;

        foreach($wp_filter['pre_get_posts']->callbacks as $priority => $callback) {
            foreach($callback as $callbackItem) {
                if(is_array($callbackItem['function'])) {
                    if ($callbackItem['function'][0] instanceof WPCF_Post_Types
                        || $callbackItem['function'][0] instanceof WPToolset_Forms_Bootstrap
                        || $callbackItem['function'][0] instanceof Toolset_Wp_Query_Adjustments_M2m) {
                        $actionsToPreserve[] = $callbackItem['function'];
                    }
                }

            }

        }
    }

    // Remove all actions
    remove_all_actions('pre_get_posts');

    // Add preserved actions back
    foreach ($actionsToPreserve as $priority => $action) {
        add_action('pre_get_posts', $action, $priority);
    }

}