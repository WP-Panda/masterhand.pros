<?php
/**
 * MarketEngine Resolution Center Functions
 *
 * @author     EngineThemes
 * @package     Includes/Resolution
 * @category     Functions
 *
 * @version     1.0.0
 * @since         1.1.0
 */

/**
 * Returns the url of resolution center list
 *
 * @since     1.1.0
 * @version 1.0.0
 */
function marketengine_resolution_center_url()
{
    global $wp_rewrite;
    $url = marketengine_get_page_permalink('user_account');

    if (!$url) {
        return home_url();
    }

    $endpoint = marketengine_option('ep_resolution-center');
    $endpoint = $endpoint ? $endpoint : 'resolution-center';

    if ($wp_rewrite->using_permalinks()) {
        $url = trailingslashit($url) . $endpoint;
    } else {
        $url = add_query_arg($endpoint, $endpoint, $url);
    }
    return $url;
}

/**
 * Returns the url of dispute link
 *
 * @param int $case_id The dispute case id
 * @return string The case link
 * @since     1.1.0
 * @version 1.0.0
 */
function marketengine_rc_dispute_link($case_id) {
    global $wp_rewrite;
    $endpoint = trim(marketengine_option('ep_case'));
    $endpoint  = !empty($endpoint) ? $endpoint : 'case';
    if($wp_rewrite->using_permalinks()) {
        return home_url( $endpoint .'/'. $case_id );    
    }else {
        return add_query_arg(array('case_type' => 'dispute', 'case_id' => $case_id), home_url());
    }
    
}

/**
 * Returns dispute case statuses.
 *
 * @return array $statuses
 *
 * @since     1.1.0
 * @version 1.0.0
 */
function marketengine_dispute_statuses()
{
    $statuses = array(
        'me-open'      => __('Open', 'enginethemes'),
        'me-waiting'   => __('Waiting', 'enginethemes'),
        'me-escalated' => __('Escalated', 'enginethemes'),
        'me-closed'    => __('Closed', 'enginethemes'),
        'me-resolved'  => __('Resolved', 'enginethemes'),
    );

    return apply_filters('marketengine_dispute_statuses', $statuses);
}

/**
 * Returns the label of a dispute case status.
 *
 * @param     string $status_name
 * @return     string status label
 *
 * @since     1.1.0
 * @version 1.0.0
 */
function marketengine_dispute_status_label($status_name)
{
    $statuses = marketengine_dispute_statuses();
    return $status_name ? $statuses[$status_name] : '';
}

/**
 * Returns dispute problem options.
 *
 * @return     array $problems
 *
 * @since     1.1.0
 * @version 1.0.0
 */
function marketengine_rc_dispute_problems()
{
    $problems = array(
        'problem-1' => __('I no longer want this item.', 'enginethemes'),
        'problem-2' => __('I was charged more than I expected.', 'enginethemes'),
        'problem-3' => __('Item not as described.', 'enginethemes'),
        'problem-4' => __('Item damaged during shipping.', 'enginethemes'),
        'problem-5' => __('Missing parts or pieces.', 'enginethemes'),
        'problem-6' => __('Item expired.', 'enginethemes'),
    );

    return apply_filters('marketengine_rc_dispute_problems', $problems);
}

/**
 * Returns the label of a problem.
 *
 * @param     string $problem_name
 * @return     string problem label
 *
 * @since     1.1.0
 * @version 1.0.0
 */
function marketengine_rc_dispute_problem_text($case_id)
{
    $problems     = marketengine_rc_dispute_problems();
    $problem_name = marketengine_get_message_meta($case_id, '_case_problem', true);
    return $problem_name ? $problems[$problem_name] : '';
}

/**
 * MarketEngine user received item expected solution
 *
 * @since     1.1.0
 */
function marketengine_rc_item_received_expected_solutions()
{

    $solution = array(
        'partial-refund' => array(
            'label'       => __('Get refund only', 'enginethemes'),
            'description' => __('(keep the item and negotiate a partial refund with the seller)', 'enginethemes'),
        ),
        'return-item'    => array(
            'label'       => __('Return &amp; get refund', 'enginethemes'),
            'description' => __('(return the item and request a full refund)', 'enginethemes'),
        ),
        'item-replaced'  => array(
            'label'       => __('Get item replaced', 'enginethemes'),
            'description' => __('(get a replaced item without refund)', 'enginethemes'),
        ),
    );

    return apply_filters('marketengine_rc_yes_expected_solutions', $solution);
}

/**
 * MarketEngine user not received item expected solution
 *
 * @since     1.1.0
 */
function marketengine_rc_item_not_received_expected_solutions() {
    return apply_filters('marketengine_rc_no_expected_solutions',array(
        'full-refund'    => array(
            'label'       => __('Get full refund', 'enginethemes'),
            'description' => __('(request the money back for item not received)', 'enginethemes'),
        ),
        'receive-item'   => array(
            'label'       => __('Get the item', 'enginethemes'),
            'description' => __('(request the item shipped)', 'enginethemes'),
        )
    ));
}

/**
 * Retrieve case expected solution label
 * @param int $case_id The case id
 * @return string
 * @since 1.1
 */
function marketengine_rc_case_expected_solution_label($case_id)
{
    $problems     = array_merge(marketengine_rc_item_received_expected_solutions(), marketengine_rc_item_not_received_expected_solutions() );
    $problem_name = marketengine_get_message_meta($case_id, '_case_expected_resolution', true);
    return $problem_name ? $problems[$problem_name]['label'] : '';
}

/**
 * Returns the dispute case query.
 *
 * @since     1.1.0
 * @version 1.0.0
 */
function marketengine_rc_dispute_case_query($query)
{
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    $args  = array(
        'post_type' => 'dispute',
        'paged'     => $paged,
        'sender'    => get_current_user_id(),
        'receiver'  => get_current_user_id(),
    );

    $args = wp_parse_args( $query, $args );

    $args  = array_merge(apply_filters('marketengine_filter_dispute_case', $query), $args); 
    $query = new ME_Message_Query($args);
    
    return $query;
}

function marketengine_dispute_case_id_by_user($user)
{
    global $wpdb;
    $query = "SELECT $wpdb->marketengine_message_item.ID
        FROM $wpdb->marketengine_message_item
        LEFT JOIN $wpdb->users
        ON ($wpdb->marketengine_message_item.sender = $wpdb->users.ID
        	OR $wpdb->marketengine_message_item.receiver = $wpdb->users.ID)
        WHERE $wpdb->marketengine_message_item.post_type = 'dispute'
        AND $wpdb->users.display_name LIKE '%{$user}%'";

    $results = $wpdb->get_col($query);

    return $results;
}

function marketengine_filter_dispute_case($query)
{
    $args = array();
    if (!empty($query['status']) && $query['status'] !== 'any') {
        $args['post_status'] = $query['status'];
    }

    if (isset($query['from_date']) || isset($query['to_date'])) {
        $before = $after = '';
        if (isset($query['from_date']) && !empty($query['from_date'])) {
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $query['from_date'])) {
                $after = $query['from_date'] . ' 0:0:1';
            } else {
                $args['post__in'][] = -1;
                return $args;
            }
        }

        if (isset($query['to_date']) && !empty($query['to_date'])) {
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $query['to_date'])) {
                $before = $query['to_date'] . ' 23:59:59';
            } else {
                $args['post__in'][] = -1;
                return $args;
            }
        }

        $args['date_query'] = array(
            array(
                'after'  => $after,
                'before' => $before,
            ),
        );
    }

    if (!empty($query['keyword'])) {
        $case_id = is_numeric($query['keyword']) ? $query['keyword'] : '';

        $ids = marketengine_dispute_case_id_by_user($query['keyword']);
        if ($case_id) {
            $ids = array_merge($ids, array($case_id));
        }

        if (empty($ids)) {
            $args['post__in'][] = -1;
        } else {
            $args['post__in'] = $ids;
        }
    }

    return $args;
}
add_filter('marketengine_filter_dispute_case', 'marketengine_filter_dispute_case');

function marketengine_dispute_case_filter_form_action()
{
    global $wp;
    if ('' === get_option('permalink_structure')) {
        $form_action = remove_query_arg(array('page', 'paged'), add_query_arg($wp->query_string, '', home_url($wp->request)));
    } else {
        $form_action = preg_replace('%\/page/[0-9]+%', '', home_url(trailingslashit($wp->request)));
    }
    echo $form_action;
    return $form_action;
}