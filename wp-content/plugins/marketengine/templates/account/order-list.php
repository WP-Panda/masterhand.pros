<?php
/**
 *	The Template for displaying list of orders that seller received.
 * 	This template can be overridden by copying it to yourtheme/marketengine/account/order-list.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 */

$args = array(
	'post_type' => 'me_order',
	'paged' 	=> absint( get_query_var('paged') )
);
$type = 'order';
$request = array_map('esc_sql', $_GET);
$args = array_merge(apply_filters( 'marketengine_filter_order', $request, $type ), $args);

$all_order_args = json_encode( array_merge(apply_filters( 'marketengine_filter_order', $request, $type ), array('post_type' => 'me_order', 'posts_per_page' => -1) ) );

$query = new WP_Query($args);

?>
<!--Mobile-->
<div class="me-orderlist-filter-tabs">
	<span><?php echo __('Filter', 'enginethemes'); ?></span>
	<span><?php echo __('Filter list', 'enginethemes'); ?></span>
</div>
<!--/Mobile-->
<?php marketengine_get_template('global/order-filter', array('type' => $type)); ?>


<?php if( $query->have_posts() ) : ?>
	<div class="me-table me-orderlist-table">
		<div class="me-table-rhead">
			<div class="me-table-col me-order-id"><?php _e("ORDER ID", "enginethemes"); ?></div>
			<div class="me-table-col me-order-status"><?php _e("STATUS", "enginethemes"); ?></div>
			<div class="me-table-col me-order-amount"><?php _e("AMOUNT", "enginethemes"); ?></div>
			<div class="me-table-col me-order-date"><?php _e("DATE OF ORDER", "enginethemes"); ?></div>
			<div class="me-table-col me-order-listing"><?php _e("LISTING", "enginethemes"); ?></div>
		</div>
		<?php
			while($query->have_posts()) : $query->the_post(); ?>

		<?php
			$order = new ME_Order( get_the_ID() );
			$order_total = $order->get_total();

			$order_listing = marketengine_get_order_items( get_the_ID() );
			$listing_item = marketengine_get_order_items(get_the_ID(), 'listing_item');
		?>
			<div class="me-table-row">
				<div class="me-table-col me-order-id"><a href="<?php the_permalink(); ?>">#<?php the_ID(); ?></a></div>
				<div class="me-table-col me-order-status">
					<?php marketengine_print_order_status( get_post_status( get_the_ID()) ); ?>
				</div>
				<div class="me-table-col me-order-amount"><?php echo marketengine_price_html($order_total); ?></div>
				<div class="me-table-col me-order-date"><?php echo get_the_date(get_option('date_format'), get_the_ID()); ?></div>
				<div class="me-table-col me-order-listing">
					<div class="me-order-listing-info">
						<p><?php echo isset($order_listing[0]) ? esc_html($order_listing[0]->order_item_name) : '' ?></p>
					</div>
				</div>
			</div>

		<?php endwhile; ?>
	</div>

	<div class="me-paginations">
		<?php marketengine_paginate_link( $query ); ?>
	</div>

<?php else : ?>
	<div class="me-table me-table-empty me-orderlist-table">
		<div class="me-table-rhead">
			<div class="me-table-col me-order-id"><?php _e("ORDER ID", "enginethemes"); ?></div>
			<div class="me-table-col me-order-status"><?php _e("STATUS", "enginethemes"); ?></div>
			<div class="me-table-col me-order-amount"><?php _e("AMOUNT", "enginethemes"); ?></div>
			<div class="me-table-col me-order-date"><?php _e("DATE OF ORDER", "enginethemes"); ?></div>
			<div class="me-table-col me-order-listing"><?php _e("LISTING", "enginethemes"); ?></div>
		</div>
	</div>
	<div class="me-table-empty-none">
		<span><?php _e('There are no conversations yet.', 'enginethemes'); ?></span>
	</div>
<?php
	endif;
	wp_reset_postdata();
?>
<script type="text/json" id="all_order_query">
	<?php echo $all_order_args; ?>
</script>