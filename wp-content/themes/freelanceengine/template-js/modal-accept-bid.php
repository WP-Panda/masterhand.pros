<div class="modal fade designed-modal" id="acceptance_project" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content text-center">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"></button>
				<?php _e("Bid acceptance confirmation", ET_DOMAIN) ?>
			</div>
			<div class="modal-body">
				<form role="form" id="escrow_bid" class="fre-modal-form">
					<div class="escrow-info fre-accept-bid-info">
		            	<!-- bid info content here -->
	                </div>

	            </form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!-- MODAL BID acceptance PROJECT-->
<script type="text/template" id="bid-info-template">
	<p>
		<?php _e( 'You are about to accept this bid for' , ET_DOMAIN ); ?> <strong>{{=budget}}</strong>
		<?php _e( 'This bid acceptance<br> requires the payment below' , ET_DOMAIN ); ?>
	</p>

	<p class="accept-bid-budget">
		<span><?php _e( 'Bid budget' , ET_DOMAIN ); ?></span>
		<span>{{= budget }}</span>
	</p>

	<# if(commission){ #>
	<p class="accept-bid-commision">
		<span><?php _e( 'Commission' , ET_DOMAIN ); ?></span>
		<span>{{= commission }}</span>
	</p>
	<# } #>

	<p class="accept-bid-amount">
		<span><?php _e( 'Total amount' , ET_DOMAIN ); ?></span>
		<span>{{=total}}</span>
	</p>

	<?php
	if(ae_get_option('user_credit_system') && class_exists('FRE_Credit')):
        if( is_use_credit_escrow()):
	?>
		<div class="info-credit-balance">
			<p><?php _e("Your credit balance: ", ET_DOMAIN);?><strong class="credit-balance">{{= available_balance}}</strong></p>
			<p class="notice_credit"></p>
		</div>
	<?php
		endif;
	endif;
	?>
	<?php  do_action('fre_after_accept_bid_infor'); ?>
	<# if(accept_bid){ #>
		<div class="fre-form-btn">
			<button type="submit" class="fre-submit-btn btn-left fre-normal-btn">
	            <?php _e('Accept Bid', ET_DOMAIN) ?>
	        </button>
	        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e('Cancel',ET_DOMAIN);?></span>
		</div>


	<# }else{ #>
		<div class="fre-form-btn">
			<a class="fre-normal-btn btn-buy-credit" href="#"><?php _e("Buy credit", ET_DOMAIN);?></a>
			<span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel',ET_DOMAIN );?></span>
		</div>
	<# } #>
</script>