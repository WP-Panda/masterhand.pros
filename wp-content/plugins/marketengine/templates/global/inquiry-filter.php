<?php
/**
 *	The Template for displaying inquiry filter section
 * 	This template can be overridden by copying it to yourtheme/marketengine/global/inquiry-filter.php.
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
?>
<div class="me-order-inquiries-filter">
	<form id="me-transaction-inquiries-filter-form" action="<?php echo $form_action; ?>">
		<div class="me-row">
			<div class="me-col-md-3">
				<div class="me-inquiries-pick-date-filter">
					<label><?php _e('Latest', 'enginethemes'); ?></label>
					<div class="me-inquiries-pick-date">
						<input id="me-inquiries-pick-date-1" name="from_date" type="text" value="<?php echo isset($_GET['from_date']) ? esc_attr( $_GET['from_date'] ) : ''; ?>" placeholder="<?php _e('From date', 'enginethemes'); ?>">
						<input id="me-inquiries-pick-date-2" name="to_date" type="text" value="<?php echo isset($_GET['to_date']) ? esc_attr( $_GET['to_date'] ) : ''; ?>" placeholder="<?php _e('To date', 'enginethemes'); ?>">
					</div>
				</div>
			</div>

			<div class="me-col-md-9">
				<div class="me-inquiries-filter">
					<label><?php _e('Keyword', 'enginethemes'); ?></label>
					<input class="me-inquiries-keyword" type="text" name="keyword" value="<?php echo isset($_GET['keyword']) ? esc_attr( $_GET['keyword'] ) : ''; ?>" placeholder="<?php _e('Listing name, seller name, etc.', 'enginethemes'); ?>">

					<?php //TODO style lai cho nay ?>
				</div>
				<div class="me-inquiries-clear-filter">
					<a href="<?php echo marketengine_get_auth_url($page).'?tab=inquiry'; ?>"><?php _e('Clear Filter'); ?></a>
					<input class="me-inquiries-filter-btn" type="submit" value="<?php _e('FILTER', 'enginethemes'); ?>">
				</div>
			</div>
		</div>
		<input name="tab" type="hidden" value="inquiry">
	</form>
</div>