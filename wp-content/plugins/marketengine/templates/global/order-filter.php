<?php
/**
 *	The Template for displaying order filter section
 * 	This template can be overridden by copying it to yourtheme/marketengine/account/order-filter.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 */
global $wp;
if ('' === get_option('permalink_structure')) {
    $form_action = remove_query_arg(array('page', 'paged'), add_query_arg($wp->query_string, '', home_url($wp->request)));
} else {
    $form_action = preg_replace('%\/page/[0-9]+%', '', home_url(trailingslashit($wp->request)));
}
$type = isset($type) ? $type : 'transaction';
?>

<div class="me-orderlist-filter">
	<form id="me-transaction-filter-form" action="<?php echo $form_action; ?>">
		<div class="me-row">

			<div class="me-col-md-2">
				<div class="me-order-status-filter">
					<label><?php _e('Status', 'enginethemes'); ?></label>
					<?php do_action( 'marketengine_status_list', $type ); ?>
				</div>
			</div>

			<div class="me-col-md-3">
				<div class="me-order-pick-date-filter">
					<label><?php _e('Date of Order', 'enginethemes'); ?></label>
					<div class="me-order-pick-date">
						<input id="me-order-pick-date-1" name="from_date" type="text" value="<?php echo isset($_GET['from_date']) ? esc_attr( $_GET['from_date'] ) : ''; ?>" placeholder="<?php _e('From date', 'enginethemes'); ?>">
						<input id="me-order-pick-date-2" name="to_date" type="text" value="<?php echo isset($_GET['to_date']) ? esc_attr( $_GET['to_date'] ) : ''; ?>" placeholder="<?php _e('To date', 'enginethemes'); ?>">
					</div>
				</div>
			</div>

			<div class="me-col-md-7">
				<div class="me-order-keyword-filter">
					<label><?php _e('Keyword', 'enginethemes'); ?></label>
					<input type="text" name="keyword" value="<?php echo isset($_GET['keyword']) ? esc_attr( $_GET['keyword'] ) : ''; ?>" placeholder="<?php _e('Order ID, listing name, etc.', 'enginethemes'); ?>">
				</div>
				<div class="me-order-clear-filter">
					<?php $page = ($type === 'order') ? 'orders' : 'purchases'; ?>
					<a href="<?php echo marketengine_get_auth_url($page); ?>"><?php _e('Clear Filter', 'enginethemes'); ?></a>
					<input class="me-order-filter-btn" type="submit" value="<?php _e('FILTER', 'enginethemes'); ?>">
				</div>
			</div>
		</div>
		<input name="tab" type="hidden" value="<?php echo isset($type) ? $type : 'transaction'; ?>">
	</form>
</div>
<?php $nonce = wp_create_nonce('me-export_report'); ?>

<a href="<?php echo add_query_arg( array('export' => 'csv', '_wpnonce' => $nonce, 'tab' => $type)); ?>" target="_blank" rel="noopener noreferrer" class="me-order-export"><i class="icon-me-download"></i><?php _e('Export report', 'enginethemes'); ?></a>
