<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Render order dispute button link on desktop
 * @since 1.1
 */
function marketengine_rc_dispute_button($transaction) {
	$dispute_time_limit = $transaction->get_dispute_time_limit() ;
	if ( $transaction->post_author == get_current_user_id() && 'me-pending' !== $transaction->post_status && $dispute_time_limit) {
		marketengine_get_template('resolution/order/dispute-button', array('transaction' => $transaction, 'dispute_time_limit' => $dispute_time_limit));
	}

	if('me-resolved' === $transaction->post_status) {
		$case = new ME_Message_Query(array('post_type' => 'dispute', 'post_parent' => $transaction->id));
		$case = array_pop($case->posts);
		$case_id = $case->ID;
		marketengine_get_template('resolution/order/resolution-link', array('transaction' => $transaction , 'case' => $case_id));
	}

}
add_action( 'marketengine_order_extra_content', 'marketengine_rc_dispute_button', 11);

/**
 * Load the resolution link template
 * @param object $transaction The me order object
 */
function marketengine_rc_center_link($transaction) {
	if ('me-disputed' === $transaction->post_status ) {
		$case = new ME_Message_Query(array('post_type' => 'dispute', 'post_parent' => $transaction->id));
		$case = array_pop($case->posts);
		$case_id = $case->ID;
		marketengine_get_template('resolution/order/resolution-link', array('transaction' => $transaction , 'case' => $case_id));
	}
}
/**
 * Render order resolution center link
 * @since 1.1
 */
function marketengine_rc_center_desktop_link($transaction) {
	marketengine_rc_center_link($transaction);
}
add_action( 'marketengine_order_extra_content', 'marketengine_rc_center_desktop_link', 11);

/**
 * Render order dispute button link on mobile
 * @since 1.1
 */
function marketengine_rc_mobile_dispute_button($transaction) {
	$dispute_time_limit = $transaction->get_dispute_time_limit() ;
	if ( $transaction->post_author == get_current_user_id() && 'me-pending' !== $transaction->post_status && $dispute_time_limit) {
		echo '<div class="me-visible-sm me-visible-xs">';
		marketengine_get_template('resolution/order/dispute-button', array('transaction' => $transaction, 'dispute_time_limit' => $dispute_time_limit));
		echo '</div>';
	}
}
// add_action( 'marketengine_order_extra_end', 'marketengine_rc_mobile_dispute_button', 11);

/**
 * Render order resolution center link
 * @since 1.1
 */
function marketengine_rc_center_mobile_link($transaction) {
	marketengine_rc_center_link($transaction);
}

/**
 * Transaction dispute form
 *
 * @param string $action The action dispute user send
 * @param object $transaction The current transaction user want to dispute
 *
 * @since 1.1
 */
function marketengine_transaction_dispute_form($action, $transaction)
{
    if ('dispute' === $action) {
        marketengine_get_template('resolution/form/dispute-form', array('transaction' => $transaction));
    }
}
add_action('marketengine_order_details_action', 'marketengine_transaction_dispute_form', 10, 2);

/**
 * Add a dispute to the transaction breadcrum
 * @since 1.1
 */
function marketengine_transaction_dispute_breadcrumb() {
    if(!empty($_GET['action']) && 'dispute' == $_GET['action'] ) : ?>
        <li><a href="#"><?php _e("Dispute", "enginethemes"); ?></a></li>
    <?php endif; 
}
add_action( 'marketengine_order_breadcrumb_end', 'marketengine_transaction_dispute_breadcrumb' );

/**
 * Filter the user account title when user access resolution center list
 *
 * @param string $title The current page title
 *
 * @since 1.1
 */
function marketengine_rc_account_title($title, $id) {
	if (is_page() && $id === marketengine_get_option_page_id('user_account')) {
        global $wp_query;
        if (isset($wp_query->query_vars['resolution-center'])) {
            return __('Resolution Center', 'enginethemes');
        }
    }
    return $title;
}
add_filter( 'the_title', 'marketengine_rc_account_title', 10, 2 );

/**
 * Replace account title when user access resolution center
 * @param array $title The title parts array
 * @since 1.1
 */
function marketengine_rc_account_document_title($title){
	global $post;
	if (is_page() && $post->ID === marketengine_get_option_page_id('user_account')) {
        global $wp_query;
        if (isset($wp_query->query_vars['resolution-center'])) {
            $title['title'] =  __('Resolution Center', 'enginethemes');
        }
    }
    return $title;
}
add_filter('document_title_parts', 'marketengine_rc_account_document_title');


function marketengine_transaction_dispute_title($title) {
    if(!empty($_GET['action']) && $_GET['action'] == 'dispute') {
        return __("Dispute", "enginethemes");
    }
    return $title;
}
add_filter('marketengine_transaction_title', 'marketengine_transaction_dispute_title');
