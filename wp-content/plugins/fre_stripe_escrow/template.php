<?php
/**
* Card form template
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function fre_stripe_card_form(){
?>
	<div class="fre-input-field">
		<label class="fre-field-title"><?php _e("Stripe's email", ET_DOMAIN);?></label>
		<input name="stripe_email" tabindex="19" id="stripe_email" type="text" size="20"  data-stripe="email" class="bg-default-input not_empty" placeholder="youremail@gmail.com" />
	</div>


	<div class="fre-input-field name_card">
		<label class="fre-field-title" for="name_card"><?php _e('Name on card',ET_DOMAIN);?></label>
		<input tabindex="23" name="name_card" id="name_card"  data-stripe="name" class="bg-default-input not_empty" type="text" />
	</div>


	<div class="row" style="margin-bottom: 30px">
		<div class="col-sm-7">
			<div class="fre-input-field fld-wrap">
				<label class="fre-field-title"><?php _e('Card number', ET_DOMAIN);?></label>
				<input name="stripe_number" tabindex="20" id="stripe_number" type="text" size="20"  data-stripe="number" class="bg-default-input not_empty" placeholder="&bull;&bull;&bull;&bull;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&bull;&bull;&bull;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&bull;&bull;&bull;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&bull;&bull;&bull;" />
			</div>
		</div>
		<div class="col-sm-5">
			<div class="fre-input-field">
				<label class="fre-field-title"><?php _e('Expiry date', ET_DOMAIN);?></label>
				<div class="row">
					<div class="col-xs-6" style="padding-right:5px;">
						<input tabindex="21" type="text" size="2" data-stripe="exp-month" placeholder="MM"  class="bg-default-input not_empty" id="exp_month" name="exp_month"/>
					</div>
					<div class="col-xs-6" style="padding-left:5px;">
						<input tabindex="22" type="text" size="4" data-stripe="exp-year" placeholder="YY"  class="bg-default-input not_empty" id="exp_year" name="exp_year" />
					</div>
				</div>
		  	</div>
		</div>
	</div>

	<div class="fre-input-field" style="margin-bottom: 0">
		<label class="fre-field-title"><?php _e('Card code', ET_DOMAIN);?></label>
		<input tabindex="24" type="text" size="3"  data-stripe="cvc" class="bg-default-input not_empty input-cvc " placeholder="CVC" id="cvc" name="cvc"/>
	</div>
<?php }
/**
* Update stripe card button
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function fre_update_stripe_button() { ?>
		<span class="update-stripe-container">
			<a href="#" class="btn-update-stripe">
				<?php
				global $user_ID;
				$ae_escrow_stripe = AE_Escrow_Stripe::getInstance();
				$ae_escrow_stripe->init();
				if( $ae_escrow_stripe->ae_get_stripe_user_id($user_ID) ){
					_e('Change Stripe account', ET_DOMAIN );
				}
				else{
					_e('Update Stripe information', ET_DOMAIN);
				} ?>
			</a>
        </span>
<?php }
/**
* Stripe card modal
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function fre_update_stripe_info_modal(){ ?>
<div class="modal fade" id="stripe_escrow_modal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button  style="z-index:1000;" data-dismiss="modal" class="close">
					<i class="fa fa-times"></i>
				</button>
				<div class="info slogan">
	      			<h4 class="modal-title"><span class="plan_name"><?php _e("Update Stripe Acount", ET_DOMAIN); ?></span></h4>
	    		</div>
			</div>
			<div class="modal-body">
				<form class="modal-form fre-modal-form" id="stripe_form" novalidate="novalidate" autocomplete="on" data-ajax="false">
					<?php fre_stripe_card_form(); ?>
					<div class="fre-form-btn">
						<button class="fre-normal-btn btn-submit" type="submit"  id="submit_stripe"> <?php _e('Update',ET_DOMAIN);?> </button>
						<span class="fre-form-close" data-dismiss="modal">Cancel</span>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
}
/**
* The field for users update their stripe account
* @param string $html of user escrow field
* @return string $html
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function ae_stripe_recipient_field( $html ){
	$ae_escrow_stripe = AE_Escrow_Stripe::getInstance();
	$ae_escrow_stripe->init();
	global $user_ID;
	if( $ae_escrow_stripe->is_use_stripe_escrow() ) {
		ob_start();
		if( ae_user_role($user_ID) == FREELANCER ) {
			 $ae_escrow_stripe->ae_stripe_connect();
		}else{
			fre_update_stripe_button();
		}
		$html = ob_get_clean();
	}
	echo $html;
}
add_action('ae_escrow_stripe_user_field', 'ae_stripe_recipient_field');
/**
* stripe email field
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function ae_stripe_email(){
	global $user_ID;
	?>
	<div class="form-group">
		<div class="form-group-control">
			<label><?php _e('Your Stripe Account', ET_DOMAIN) ?></label>
			<input type="stripe_email" class="form-control" id="stripe_email" name="stripe_email" value="<?php echo get_user_meta( $user_ID, 'stripe_email', true ); ?>" placeholder="<?php _e('Enter your Stripe email', ET_DOMAIN) ?>">
		</div>
	</div>
	<div class="clearfix"></div>
<?php }
/**
* Notification
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function ae_stripe_escrow_notification( $msg = '' ){ ?>
<script type="text/javascript" id="user-confirm">
	(function ($ , Views, Models) {
		$(document).ready(function(){
			<?php if( !empty($msg) ){?>
				var msg = "<?php echo $msg;?>";
		<?php } else { ?>
			var msg = "<?php _e('Your account has been successfully connected to Stripe!',ET_DOMAIN); ?>";
		<?php } ?>
			alert(msg);
			window.location.href = "<?php echo et_get_page_link('profile'); ?>"
		});
	})(jQuery, window.Views, window.Models);
</script>
<?php }
/**
* disable paypal field
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
add_filter('ae_escrow_recipient_field_html', 'ae_escrow_stripe_field_html');
function ae_escrow_stripe_field_html( $html ){
	$ae_escrow_stripe = AE_Escrow_Stripe::getInstance();
	if( $ae_escrow_stripe->is_use_stripe_escrow() ){
		return '';
	}
	return $html;
}