<?php
/**
 *	The Template for displaying transactions and inquiries of buyer.
 * 	This template can be overridden by copying it to yourtheme/marketengine/account/my-purchases.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 */
$redirect = marketengine_get_auth_url('purchases');
?>
<div class="me-orderlist">
	<div class="marketengine-tabs">
		<ul class="me-tabs">
			<li <?php if(empty($_GET['tab']) || $_GET['tab'] == 'transaction') { echo 'class="active"'; } ?>>
				<?php $redirect = add_query_arg(array( 'tab' => 'transaction' ), $redirect); ?>
				<a href="<?php echo $redirect; ?>">
					<span><?php echo __('Transactions', 'enginethemes'); ?></span>
				</a>
			</li>
			<li <?php if(!empty($_GET['tab']) && $_GET['tab'] == 'inquiry') { echo 'class="active"'; } ?>>
				<?php $redirect = add_query_arg(array( 'tab' => 'inquiry' ), $redirect); ?>
				<a href="<?php echo $redirect; ?>">
					<span><?php echo __('Inquiries', 'enginethemes'); ?></span>
				</a>
			</li>
		</ul>
		<div class="me-tabs-container">
			<!-- Tabs Orders -->
			<div class="me-tabs-section">

				<?php
					if(empty($_GET['tab']) || $_GET['tab'] == 'transaction') :
						marketengine_get_template('account/transaction-list');
					else :
						marketengine_get_template('account/buyer-inquiry-list');
					endif;
				?>

			</div>
		</div>
	</div>
</div>