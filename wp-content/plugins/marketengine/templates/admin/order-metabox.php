<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// item list
// buyer: name, paypal, account, status
// receiver list: name, paypal account, status
global $post;
$order = marketengine_get_order($post);
$billing_address = $order->get_address( 'billing' );

$note = esc_html( $order->post_excerpt );

$listing_items = $order->get_listing_items();
$listing_item = array_pop($listing_items);
$listing_obj = marketengine_get_listing($listing_item['ID']);

$receiver_items = marketengine_get_order_items($post->ID, 'receiver_item');
$receiver_item = array_pop($receiver_items);

$commission_items = marketengine_get_order_items($post->ID, 'commission_item');
$commission_item = array_pop($commission_items);
?>
<style type="text/css">
	#post-body.columns-2 #postbox-container-1 {
		display: none;
	}
	#poststuff #post-body.columns-2 {
		margin-right: 0;
	}
	.hndle  {
		/* display: none; */
	}
	#post-body-content {
		margin: 0;
	}
	.me-order-preview {
		/* box-shadow: 0 2px 15px 0 #f5f5f5; */
		border-top:  1px solid #ededed;
		padding-top: 10px;
		padding-bottom: 10px;
	}
	.me-order-preview:first-of-type {
		border: none;
	}
	#poststuff h2 {
		font-size: 20px;
	}
	.me-order-preview > h2 {
		font-size: 18px !important;
		font-weight: 700 !important;
	}
	.order-details, .me-orderbill-info, .me-ordernotes-info, .me-order-items, .me-receiver-item {
		padding: 0 12px;
		color: #333;
	}
	.me-order-items table {
		width: 100%;
		color: #333;
	}
	.me-order-items table th {
		font-size: 14px;
	    font-weight: bold;
	    padding-bottom: 4px;
	}
	.order-details p {
		margin: 10px 0;
	}
	.me-order-items td {
		padding: 10px 0;
	}
	.me-order-items tbody tr:last-of-type {
		/* border-top: 1px solid #ededed; */
	}
	.me-order-items tbody tr:last-of-type td{
		font-size: 16px;
		font-weight: 700;
		/* border-top: 1px solid #ededed; */
	}	
	.order-details label {
	    cursor: default;
	    font-size: 14px;
	    font-weight: bold;
	    min-width: 100px;
	    display: inline-block;
	}
	.me-orderbill-info p > span {
		font-size: 14px;
	    font-weight: bold;
	    min-width: 100px;
	    display: inline-block;
	}
	.pull-left {
		float: left;
	}
	.clearfix {
		clear : both;
	}
	.me-receiver-item table {
		width: 100%;
		color:  #333;
	}
	.me-receiver-item td {
		padding: 8px 0 0;
		font-size: 14px;
	}
	.me-receiver-item table thead th {
		font-size: 14px;
	    font-weight: bold;
	    /* border-bottom: 1px solid #ededed; */
	    padding-bottom: 4px;
	}
	.me-order-items th, .me-receiver-item th {
		text-align: left;
		/* width: 40%; */
		line-height: 28px;
	}
	.me-receiver-item th {
	 	width: 25%;
	}
	.me-order-items img {
		width: 50px;
		height: 50px;
		float: left;
		margin-right: 15px; 
	}
	.me-order-items img + span {
		display: block;
		padding-left: 65px;
	}
	.me-order-items a {
		color: #0073aa;
	}
	.page-title-action {
		display: none;
	}
	.handlediv.button-link {
		display: none !important;
	}

	.me-order-items .me-item-price {
		text-align: right;
	}
	
</style>
<div class="me-order-preview">
	<h2><?php printf(__( "Order #%d details" , "enginethemes" ), $order->ID); ?></h2>
	<div class="order-details">
		<p>
			<label><?php _e("ID:", "enginethemes"); ?></label>
			<?php printf(__( "#%d" , "enginethemes" ), $order->ID) ?>
		</p>
		<p>
			<label><?php _e("Order date:", "enginethemes"); ?></label>
			<?php echo get_the_date(); ?>
		</p>
		<p>
			<label><?php _e("Order status", "enginethemes"); ?></label>
			<?php echo marketengine_get_order_status_label($order->post_status); ?>
		</p>
		<p>
			<label><?php _e("Buyer:", "enginethemes"); ?></label>
			<?php echo get_the_author_meta( 'display_name', $order->post_author ); ?>
		</p>
	</div>
</div>

<div class="me-order-preview">
	<h2><?php _e( 'Billed to:', 'enginethemes' ); ?></h2>
	<div class="me-orderbill-info">
		<p><?php marketengine_print_buyer_information( $billing_address ); ?></p>
	</div>
</div>


<?php if($note) : ?>
	<div class="me-order-preview">
		<h2><?php echo __( 'Order Notes:', 'enginethemes' ); ?></h2>
		<div class="me-ordernotes-info">

			<p class=""><?php echo nl2br(esc_attr($note)); ?></p>

		</div>
	</div>
<?php endif;?>

<div class="me-order-preview">
	<h2><?php _e("Order Item", "enginethemes"); ?></h2>
	<div class="me-order-items">
		<table>
			<thead>
				<tr>
					<th><?php _e("Listing", "enginethemes"); ?></th>
					<th><?php _e("Price", "enginethemes"); ?></th>
					<th><?php _e("Units", "enginethemes"); ?></th>
					<th class="me-item-price"><?php _e("Total price", "enginethemes"); ?></th>
				</tr>
			</thead>
			<?php
				$listing = $listing_item['ID'];
				$unit = ($listing_item['qty']) ? $listing_item['qty'][0] : 1;
			?>
			<tbody>
				<tr>
					<td>
						<a href="<?php echo get_permalink( $listing_obj->ID ); ?>">
							<?php echo get_the_post_thumbnail($listing_obj->ID); ?>
							<span><?php echo esc_html(get_the_title($listing_obj->ID)); ?></span>
						</a>
					</td>
					<td><?php echo marketengine_price_html( $listing_item['price'] ); ?></td>
					<td><?php echo $unit ?></td>
					<td class="me-item-price"><?php echo marketengine_price_html($listing_item['price'] * $unit); ?></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td><?php _e("Total amount:", "enginethemes"); ?></td>
					<td class="me-item-price"><?php echo marketengine_price_html($listing_item['price'] * $unit); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="me-order-preview">
	<h2><?php _e("Payment Info", "enginethemes"); ?></h2>
	<div class="me-receiver-item">
		<table>
			<thead>
				<tr>
					<th><?php _e("Receiver Name", "enginethemes"); ?></th>
					<th><?php _e("Paypal Email", "enginethemes"); ?></th>
					<th><?php _e("Amount", "enginethemes"); ?></th>
				</tr>
			</thead>
		  	<?php if(!empty($receiver_item)) : ?>
			  	<?php
					$receiver_name = $receiver_item->order_item_name;
					$receiver = get_user_by( 'login', $receiver_name );
				?>
			<tbody>
				<tr>
					<td><?php echo get_the_author_meta( 'display_name', $receiver->ID ); ?></td>
					<td><?php echo marketengine_get_order_item_meta($receiver_item->order_item_id, '_receive_email', true); ?></td>
					<td><?php echo marketengine_price_html(!marketengine_get_order_item_meta($receiver_item->order_item_id, '_amount', true)); ?></td>
				</tr>
			  	<?php endif; ?>

			  	<?php if(!empty($commission_item)) : ?>
			  	<tr>
				    <td><?php _e("Commision", "enginethemes"); ?></td>
				    <td><?php echo marketengine_get_order_item_meta($commission_item->order_item_id, '_receive_email', true); ?></td>
				    <td><?php echo marketengine_price_html(!marketengine_get_order_item_meta($commission_item->order_item_id, '_amount', true)); ?></td>
			  	</tr>
			  	<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

