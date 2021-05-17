<?php
/**
 * MarketEngine Report Functions
 *
 * Functions for MarketEngine Report.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Includes
 * @category 	Functions
 *
 * @since 		1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Retrives list of order data.
 *
 * @param array $args
 * @return array $result
 */
function marketengine_order_report_data( $args ) {
	global $wpdb;
	$defaults = array(
        'from_date' => '2016-1-1',
        'to_date'   => date('Y-m-d', time()),
        'orderby'   => 'post_date',
        'order'     => 'DESC',
        'order_status' => 'any',
        'showposts' => get_option('posts_per_page'),
        'keyword'	=> ''
    );
    $args = wp_parse_args($args, $defaults);

    extract($args);

    if( empty($from_date) ) {
    	$from_date = '1970-1-1';
    }
    if( empty($to_date) ) {
    	$to_date = date('Y-m-d', time());
    }

    $user_info = get_userdata(get_current_user_id());

	$query = "SELECT DISTINCT(P.ID) as order_id,
			P.post_status as status,
			PM.meta_value as amount,
			P.post_date as date_of_order,
			O.order_item_name as listing_title

	 		FROM $wpdb->posts as P
	 		LEFT JOIN $wpdb->marketengine_order_items as O
	 			ON P.ID = O.order_id
	 		LEFT JOIN  $wpdb->postmeta as PM ON  PM.post_id = P.ID AND PM.meta_key = '_order_subtotal'
	 		WHERE P.post_type = 'me_order'
	 		    AND P.ID IN (
	 		        SELECT order_items.order_id
	 		        FROM $wpdb->marketengine_order_items as order_items
	 		        WHERE order_items.order_item_type = 'receiver_item'
	 		        AND order_items.order_item_name = '{$user_info->user_login}'
	 		    )
	 		    AND P.post_date BETWEEN '{$from_date} 0:0:1' AND '{$to_date} 23:59:59'";

	if( empty($order_status) || $order_status == 'any' ) {
		$query .= " AND (";
		$order_status = array( 'me-complete', 'me-closed', 'publish' );
		$order_status = apply_filters( 'marketengine_export-order_status', $order_status );
		foreach($order_status as $key => $status) {
			if($key != 0) {
				$query .= " OR ";
			}
			$query .= "P.post_status = '{$status}'";
		}
		$query .= ")";
	} else {
		$query .= " AND P.post_status = '{$order_status}'";
	}

	if( !empty($keyword) ) {
		$query .= " AND (P.ID IN (
	 		        SELECT order_items.order_id
	 		        FROM $wpdb->marketengine_order_items as order_items
	 		        WHERE order_items.order_item_type = 'listing_item'
	 		        AND order_items.order_item_name LIKE '%{$keyword}%'
	 		        )";
	 	if( is_numeric($keyword) ) {
	 		$query .= " OR
	 		        P.ID IN ({$keyword})";
	 	}
	 	$query .= ")";
	}

	$query .= " GROUP BY P.ID";

    $result = $wpdb->get_results($query);

	return $result;
}

/**
 * Retrives list of transaction data.
 *
 * @param array $args
 * @return array $result
 */
function marketengine_transaction_report_data( $args ) {
	global $wpdb;
	$defaults = array(
        'from_date' => '1970-1-1',
        'to_date'   => date('Y-m-d', time()),
        'orderby'   => 'post_date',
        'order'     => 'DESC',
        'order_status' => 'any',
        'showposts' => get_option('posts_per_page'),
        'keyword'	=> ''
    );
    $args = wp_parse_args($args, $defaults);

    extract($args);

    if( empty($from_date) ) {
    	$from_date = '1970-1-1';
    }
    if( empty($to_date) ) {
    	$to_date = date('Y-m-d', time());
    }

	$user = get_current_user_id();

	$query = "SELECT DISTINCT(P.ID) as transaction_id,
			P.post_status as status,
			PM.meta_value as amount,
			P.post_date as date_of_order,
			O.order_item_name as listing_title

	 		FROM $wpdb->posts as P
	 		LEFT JOIN $wpdb->marketengine_order_items as O
	 			ON P.ID = O.order_id
	 		LEFT JOIN  $wpdb->postmeta as PM ON  PM.post_id = P.ID AND PM.meta_key = '_order_subtotal'
	 		WHERE P.post_type = 'me_order'
	 		    AND P.post_author = {$user}
	 		    AND P.post_date BETWEEN '{$from_date} 0:0:1' AND '{$to_date} 23:59:59'";

	if( empty($order_status) || $order_status == 'any' ) {
		$query .= " AND (";
		$order_status = array( 'me-complete', 'me-pending', 'me-closed', 'publish' );
		$order_status = apply_filters( 'marketengine_export-order_status', $order_status );
		foreach($order_status as $key => $status) {
			if($key != 0) {
				$query .= " OR ";
			}
			$query .= "P.post_status = '{$status}'";
		}
		$query .= ")";
	} else {
		$query .= " AND P.post_status = '{$order_status}'";
	}

	if( !empty($keyword) ) {
		$query .= " AND (P.ID IN (
	 		        SELECT order_items.order_id
	 		        FROM $wpdb->marketengine_order_items as order_items
	 		        WHERE order_items.order_item_type = 'listing_item'
	 		        AND order_items.order_item_name LIKE '%{$keyword}%'
	 		        )";
	 	if( is_numeric($keyword) ) {
	 		$query .= " OR
	 		        P.ID IN ({$keyword})";
	 	}
	 	$query .= ")";
	}

	$query .= " GROUP BY P.ID";

    $result = $wpdb->get_results($query);

	return $result;
}