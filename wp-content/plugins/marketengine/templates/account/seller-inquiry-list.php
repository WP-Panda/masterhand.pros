<?php
/**
 *	The Template for displaying list of inquiries that seller received.
 * 	This template can be overridden by copying it to yourtheme/marketengine/account/seller-inquiry-list.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 */

$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$args = array(
	'post_type'		=> 'inquiry',
	'paged'			=> $paged,
	'receiver'		=> get_current_user_id(),
	'orderby' => 'modified'
);

$role = 'receiver';

$request = array_map('esc_sql', $_GET);
$args = array_merge(apply_filters( 'marketengine_filter_inquiry', $request, $role ), $args);
$query = new ME_Message_Query($args);

?>
<!-- Tabs Inquiries -->
<div class="me-tabs-section">
	<!--Mobile-->
	<div class="me-inquiries-filter-tabs">
		<span><?php echo __('Filter', 'enginethemes'); ?></span>
		<span><?php echo __('Filter list', 'enginethemes'); ?></span>
	</div>
	<!--/Mobile-->
	<?php marketengine_get_template('global/inquiry-filter', array('page' => 'orders') ); ?>

	
	<?php if( $query->have_posts() ) : ?>
		<div class="me-table me-order-inquiries-table">
			<div class="me-table-rhead">
				<div class="me-table-col me-order-buyer"><?php echo __('BUYER', 'enginethemes'); ?></div>
				<div class="me-table-col me-order-status"><?php echo __('STATUS', 'enginethemes'); ?></div>
				<div class="me-table-col me-order-listing"><?php _e("LISTING", "enginethemes"); ?></div>
				<div class="me-table-col me-order-date-contact"><?php echo __('DATE OF CONTACT', 'enginethemes'); ?></div>
			</div>
			<?php
				foreach( $query->posts as $inquiry ) :
					$listing = marketengine_get_listing($inquiry->post_parent);
					$new_message = marketengine_get_message_meta($inquiry->ID, '_me_recevier_new_message', true);
			?>

			<div class="me-table-row">
				<div class="me-table-col me-order-buyer">
					<div class="me-order-listing-info">
						<p><a href="<?php echo marketengine_inquiry_permalink($inquiry->ID); ?>"><?php echo get_the_author_meta( 'display_name', $inquiry->sender ); ?></a></p>
					</div>
				</div>

				<?php if($listing && $listing->is_available()) : ?>
					<?php if($new_message > 0) : ?>
						<div class="me-table-col me-order-status me-unread">
							<i class="icon-me-reply"></i><?php printf(__("%d unread", "enginethemes"), $new_message); ?>
						</div>
					<?php else : ?>
						<div class="me-table-col me-order-status me-read"><?php _e("read", "enginethemes"); ?></div>
					<?php endif; ?>
				<?php endif; ?>

				<?php marketengine_get_template('account/archived-listing-notice', array('listing_obj' => $listing)); ?>

				<div class="me-table-col me-order-listing"><?php echo $listing ? esc_html($listing->get_title()) : __('Deleted listing', 'enginethemes'); ?></div>
				<div class="me-table-col me-order-date-contact"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $inquiry->post_modified ) ); ?></div>
			</div>

			<?php
				endforeach;
			?>
		</div>

		<div class="me-paginations">
			<?php marketengine_paginate_link($query); ?>
		</div>

	<?php else : ?>
		
		<div class="me-table me-table-empty me-order-inquiries-table">
			<div class="me-table-rhead">
				<div class="me-table-col me-order-buyer"><?php echo __('BUYER', 'enginethemes'); ?></div>
				<div class="me-table-col me-order-status"><?php echo __('STATUS', 'enginethemes'); ?></div>
				<div class="me-table-col me-order-listing"><?php _e("LISTING", "enginethemes"); ?></div>
				<div class="me-table-col me-order-date-contact"><?php echo __('DATE OF CONTACT', 'enginethemes'); ?></div>
			</div>
		</div>
		<div class="me-table-empty-none">
			<span><?php _e('There are no conversations yet.', 'enginethemes'); ?></span>
		</div>

	<?php endif; ?>

</div>