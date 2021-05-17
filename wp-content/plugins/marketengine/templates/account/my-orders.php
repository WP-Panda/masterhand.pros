<?php
/**
 *	The Template for displaying orders and inquiries of seller.
 * 	This template can be overridden by copying it to yourtheme/marketengine/account/my-orders.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 */
$redirect = marketengine_get_auth_url('orders');
?>
<div class="me-orderlist">
	<div class="marketengine-tabs">
		<ul class="me-tabs">
			<li <?php if(empty($_GET['tab']) || $_GET['tab'] == 'order') { echo 'class="active"'; } ?> >
				<?php $redirect = add_query_arg(array( 'tab' => 'order' ), $redirect); ?>
				<a href="<?php echo $redirect; ?>"><span><?php _e('Orders', 'enginethemes'); ?></span></a>
			</li>
			<li <?php if(!empty($_GET['tab']) && $_GET['tab'] == 'inquiry') { echo 'class="active"'; } ?>>
				<?php $redirect = add_query_arg(array( 'tab' => 'inquiry' ), $redirect); ?>
				<a href="<?php echo $redirect; ?>"><span><?php _e('Inquiries', 'enginethemes'); ?></span></a>
			</li>
		</ul>
		<div class="me-tabs-container">
			<div class="me-tabs-section">
			<?php
				if(empty($_GET['tab']) || $_GET['tab'] == 'order') :
					marketengine_get_template('account/order-list');
				else :
					marketengine_get_template('account/seller-inquiry-list');
				endif;
				?>
			</div>

		</div>
	</div>
</div>