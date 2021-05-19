<?php
/**
 * Orion Woo-commerce Setting Template
 *
 * @package Orion SMS OTP verification
 */
$text_domain = 'orion-sms-orion-sms-otp-verification';

function ihs_text_for_translation( $text ) {
	$text_domain = 'orion-sms-orion-sms-otp-verification';
	return __( $text, $text_domain );
}
?>
<div class="wrap orion-otp-mega-wrapper">
	<div class="jumbotron">
		<h6 class="mb-0 text-white lh-100">Orion SMS Plugin Woocommerce Settings <i class="fab fa-product-hunt"></i>ro</h6>
		<small><?php echo __( 'by', $text_domain ); ?> Imran Sayed, Smit Patadiya</small>
	</div>

	<!--Plugin Description-->
	<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
		<h6 class="border-bottom border-gray pb-2 mb-0"><i class="fa fa-info-circle" aria-hidden="true"></i> <?php echo __( 'Description', $text_domain ); ?></h6>
		<div class="media text-muted pt-3">
			<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
				<ul>
					<li><?php echo __( 'You get to verify the user’s mobile number using mobile OTP on WOO-COMMERCE checkout page before the user can place the order.This will verify the authenticity of the mobile number', $text_domain ) ?></li>
					<li><?php echo __( 'You also have an option to send and receive Order SMS at the time of Order Processing, Order Cancelled, Order on Hold, Order Completed. Your custom messages that you write here will automatically go when the order status changes. You can also trigger the SMS, when you change the status of the order from woo-commerce order page', $text_domain ) ?></li>
				</ul>
			</div>
		</div>
	</div>

	<!--Form-->
	<form method="post" action="options.php">
		<?php settings_fields( 'ihs-otp-woo-plugin-settings-group' ); ?>
		<?php do_settings_sections( 'ihs-otp-woo-plugin-settings-group' ); ?>


		<!--1. API Configuration-->
		<!--Heading-->
		<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 bg-purple rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
			<div class="lh-100 ihs-admin-head-cont">
				<h6 class="mb-0 text-white lh-100"><?php echo ihs_text_for_translation( 'Api Configuration' )?></h6>
				<small><?php echo ihs_text_for_translation( 'Api settings required for plugin to function' )?></small>
			</div>
		</div>
		<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
			<p class="border-bottom border-gray pb-2 mb-0"><?php echo ihs_text_for_translation( 'You can get the Auth Key from MSG91.' ); ?>
				<?php echo ihs_get_tell_me_how_link( 'Tell me how', 'https://youtu.be/od7f82A7RMw?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf' )?>
			</p>
			<!--Auth Key Input Field-->
			<!--Api Type-->
			<div class="media text-muted pt-3">
				<?php $api_type_text = __('Select API Type', $text_domain); ?>
				<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="fas fa-key ihs-my-icons"></i></div>
				<?php echo ihs_get_api_type( 'API TYPE', 'ihs_api_type', true, true, $api_type_text ); ?>
			</div>
			<!--TWILLIO KEYS-->
			<h6 class="border-bottom border-gray pb-3 pt-3 mb-0">
				<?php echo __( 'Twilio API Configuration ', $text_domain ); ?>
				<?php echo ihs_get_tell_me_how_link( 'Tell me how', 'https://youtu.be/hne6x-8nbA0' )?>
			</h6>
			<div class="ihs-twilio-keys">
				<!--Twilio Api Key Input Field-->
				<div class="media text-muted pt-3">
					<?php $tooltip_text = ihs_text_for_translation( 'Get the api key from Twilio' ) ?>
					<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-key" aria-hidden="true"></i></div>
					<?php echo ihs_get_text_input( 'TWILIO API KEY', 'ihs_twilio_api_key',
						'text', false, '', true, $tooltip_text ); ?>
				</div>
				<!--Twilio SID Key Input Field-->
				<div class="media text-muted pt-3">
					<?php $tooltip_text = ihs_text_for_translation( 'Get the sid key from Twilio, for order SMS' ) ?>
					<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-key" aria-hidden="true"></i></div>
					<?php echo ihs_get_text_input( 'TWILIO SID KEY', 'ihs_twilio_sid_key',
						'text', false, '', true, $tooltip_text ); ?>
				</div>
				<!--Twilio AUTH Token Input Field-->
				<div class="media text-muted pt-3">
					<?php $tooltip_text = ihs_text_for_translation( 'Get the auth token from Twilio, for order SMS' ) ?>
					<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-key" aria-hidden="true"></i></div>
					<?php echo ihs_get_text_input( 'TWILIO AUTH TOKEN', 'ihs_twilio_auth_token',
						'text', false, '', true, $tooltip_text ); ?>
				</div>
				<!--Twilio PHONE Number Input Field-->
				<div class="media text-muted pt-3">
					<?php $tooltip_text = ihs_text_for_translation( 'Get the Twilio phone number you purchased at twilio.com/console with + sign, for order SMS' ) ?>
					<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-key" aria-hidden="true"></i></div>
					<?php echo ihs_get_text_input( 'TWILIO PHONE NUMBER', 'ihs_twilio_phone_number',
						'text', false, '', true, $tooltip_text ); ?>
				</div>
			</div>
			<!--MSG91 KEYS-->
			<h6 class="border-bottom border-gray pb-3 pt-3 mb-0">
				<?php echo __( 'MSG91 API Configuration ', $text_domain ); ?>
				<?php echo ihs_get_tell_me_how_link( 'Tell me how', 'https://youtu.be/od7f82A7RMw?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf' )?>
			</h6>
			<div class="ihs-msg91-keys">
				<!--MSG91 Api Key Input Field-->
				<div class="media text-muted pt-3">
					<?php $tooltip_text = ihs_text_for_translation( 'Get the auth key from MSG91' ) ?>
					<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-key" aria-hidden="true"></i></div>
					<?php echo ihs_get_text_input( 'MSG91 AUTH KEY', 'ihs_otp_auth_key',
						'text', false, '', true, $tooltip_text ); ?>
				</div>
				<!--Sender's ID-->
				<div class="media text-muted pt-3">
					<?php $tooltip_text = ihs_text_for_translation( 'e.g. IBAZAR' ) ?>
					<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="fa fa-id-badge" aria-hidden="true"></i></div>
					<?php echo ihs_get_text_input( 'SENDER\'S ID ( 6 characters )', 'ihs_otp_woo_sender_id', 'text',
						false, '', true, $tooltip_text, 6 ); ?>
				</div>
				<!--Route-->
				<div class="media text-muted pt-3">
					<?php $route_text = ihs_text_for_translation( 'Select the MSG91 Route' ); ?>
					<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="fas fa-map-signs ihs-my-icons"></i></div>
					<?php echo ihs_get_route_drop_down( 'ROUTE', 'ihs_woo_mgs_route', false, true, $route_text ); ?>
				</div>
			</div>

			<!--Mobile No length-->
			<div class="media text-muted pt-3">
				<?php $tooltip_text = ihs_text_for_translation( 'How many digits excluding country code? For e.g. for India enter 10' ) ?>
				<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-phone-square" aria-hidden="true"></i></div>
				<?php echo ihs_get_text_input( 'MOBILE NO LENGTH', 'ihs_woo_mobile_length', 'text',
					false, '', true, $tooltip_text, 2 ); ?>
			</div>
			<!--Country Code-->
			<div class="media text-muted pt-3">
				<?php $label = ihs_text_for_translation( 'COUNTRY CODE' ) ?>
				<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="fa fa-globe" aria-hidden="true"></i></div>
				<?php echo ihs_get_text_input( $label, 'ihs_otp_woo_country_code', 'select' ); ?>
			</div>
			<!--Rating-->
			<?php echo ihs_get_rate_us_content(); ?>
		</div>

		<!--2. WooCommerce Checkout Form Settings-->
		<!--Heading-->
		<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 ihs-bg-blue rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
			<div class="lh-100 ihs-admin-head-cont">
				<h6 class="mb-0 text-white lh-100"><?php echo ihs_text_for_translation( 'Woocomerce Checkout Settings' ); ?></h6>
				<small><?php echo ihs_text_for_translation( 'Woocommerce Checkout Form Settings' ); ?></small>
			</div>
		</div>
		<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
			<h6 class="border-bottom border-gray pb-2 mb-0"><?php echo ihs_text_for_translation( 'Form Settings' ); ?>
				<?php echo ihs_get_tell_me_how_link( 'Tell me how', 'https://youtu.be/3EX1p05pEv0?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf' )?>
			</h6>
			<!--Contact form Selector Hidden for woo-commerce settings-->
			<div class="media text-muted pt-3 d-none">
				<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="ihs-my-icons fab fa-wpforms" aria-hidden="true"></i></div>
				<?php $tooltip_text = ihs_text_for_translation( 'Please enter a unique bodyclassname followed by classname or id name parent div of the form element. Please prefix a . (dot) for class name and # for ID before the selector' ); ?>
				<?php echo ihs_get_text_input( 'CHECKOUT FORM SELECTOR',
					'ihs_otp_woo_form_selector', 'text', false,
					'e.g .bodyclassname #divclassname', true,
					$tooltip_text ); ?>
			</div>
			<!--Place Order Submit Btn Selector-->
			<div class="media text-muted pt-3">
				<?php $tooltip_text = ihs_text_for_translation( 'Please enter a unique body classname followed by submit button ( place order btn ) id or classname. The two selectors need to be separated by space. Also prefix a . (dot) for class name and # for an ID' ); ?>
				<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="ihs-my-icons fab fa-wpforms" aria-hidden="true"></i></div>
				<?php echo ihs_get_text_input( 'PLACE ORDER SELECTOR', 'ihs_otp_woo_submit_btn-selector',
					'text', true, 'e.g #place_order',
					true, $tooltip_text, '', '#place_order'); ?>
			</div>
			<!--New Mobile Input field and pre-existing One-->
			<?php echo ihs_get_woo_mobile_input_fields( 'ihs_otp_woo_mobile_input_required', 'ihs_otp_woo_mobile_input_name' );?>

			<!--OTP template-->
			<?php $textarea_placeholder = 'Your One Time Password is {OTP}. This OTP is valid for today and please don\'t share this OTP with anyone for security'; ?>
			<div class="media text-muted pt-3">
				<?php $tooltip_text = ihs_text_for_translation( 'Please make sure you follow the format given in placeholder along with OTP' ); ?>
				<div class="ihs-input-icon ihs_otp_template_textarea ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-envelope" aria-hidden="true"></i></div>
				<?php echo ihs_get_text_input( 'OTP TEMPLATE', 'ihs_otp_woo_msg_template',
					'textarea', true, $textarea_placeholder,
					true, $tooltip_text); ?>
			</div>
			<!--Rating-->
			<?php echo ihs_get_rate_us_content(); ?>
		</div>

		<!--2. WooCommerce Order SMS Form Settings-->
		<!--Heading-->
		<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 ihs-bg-pink rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
			<div class="lh-100 ihs-admin-head-cont">
				<h6 class="mb-0 text-white lh-100"><?php echo ihs_text_for_translation( 'Woocomerce Order SMS Settings' )?></h6>
				<small><?php echo ihs_text_for_translation( 'SMS on Pending Order, Failed Order, Order on Hold, Order Processing, Completed Order, Refunded Order, Cancelled Order' )?> </small>
			</div>
		</div>
		<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
			<h6 class="border-bottom border-gray pb-2 mb-0"><?php echo ihs_text_for_translation( 'Form Settings' ); ?>
				<?php echo ihs_get_tell_me_how_link( 'Tell me how', 'https://youtu.be/3EX1p05pEv0?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf' )?>
			</h6>
			<!--Admin Mobile No-->
			<div class="media text-muted pt-3">
				<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-phone-square" aria-hidden="true"></i></div>
				<?php echo ihs_get_text_input( 'ADMIN MOBILE NO', 'ihs_admin_mob_no', 'text',
					false, 'XXXXXXXXXX ( WITHOUT COUNTRY CODE ) ', true, 'Please enter admin\'s number to get Order SMS', 13 ); ?>
			</div>
			<!--Order SMS-->
			<?php echo ihs_order_sms_checkbox_template( 'PENDING', 'ihs_order_pending' );?>
			<?php echo ihs_order_sms_checkbox_template( 'FAILED', 'ihs_order_failed' );?>
			<?php echo ihs_order_sms_checkbox_template( 'HOLD', 'ihs_order_hold' );?>
			<?php echo ihs_order_sms_checkbox_template( 'PROCESSING', 'ihs_order_processing' );?>
			<?php echo ihs_order_sms_checkbox_template( 'COMPLETED', 'ihs_order_completed' );?>
			<?php echo ihs_order_sms_checkbox_template( 'REFUNDED', 'ihs_order_refunded' );?>
			<?php echo ihs_order_sms_checkbox_template( 'CANCELLED', 'ihs_order_cancelled' );?>

			<!--Order SMS template-->
			<?php echo ihs_order_sms_template( 'PENDING', 'ihs_order_pending_template', '{order_status}: Dear {billing_name}! Order #{order_id} is pending. Your order total is {order_total}' ) ?>
			<?php echo ihs_order_sms_template( 'FAILED', 'ihs_order_failed_template', '{order_status}: Dear {billing_name}! Order #{order_id} is failed. Your order total is {order_total}' ) ?>
			<?php echo ihs_order_sms_template( 'HOLD', 'ihs_order_hold_template', '{order_status}: Dear {billing_name}! Order #{order_id} is on hold. Your order total is {order_total}' ) ?>
			<?php echo ihs_order_sms_template( 'PROCESSING', 'ihs_order_processing_template', '{order_status}: Dear {billing_name}! Order #{order_id} is under processing. Your order total is {order_total}' ) ?>
			<?php echo ihs_order_sms_template( 'COMPLETED', 'ihs_order_completed_template', '{order_status}: Dear {billing_name}! Order #{order_id} is complete. Your order total is {order_total}' ) ?>
			<?php echo ihs_order_sms_template( 'REFUNDED', 'ihs_order_refunded_template', '{order_status}: Dear {billing_name}! Order #{order_id} is refund is processed. Your order total was {order_total}' ) ?>
			<?php echo ihs_order_sms_template( 'CANCELLED', 'ihs_order_cancelled_template', '{order_status}: Dear {billing_name}! Order #{order_id} is cancelled. Your order total was {order_total}' ) ?>

			<!--Rating-->
			<?php echo ihs_get_rate_us_content(); ?>
		</div>

		<!--Submit Button-->
		<?php submit_button(); ?>
	</form>

	<!--1- Tutorial Section-->
	<!--Heading-->
	<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 ihs-bg-light-purple rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
		<div class="lh-100 ihs-admin-head-cont">
			<h6 class="mb-0 text-white lh-100"><?php echo __( 'How to use the Plugin?', $text_domain ); ?></h6>
			<small><?php echo __( 'Watch below demo tutorials to have a better understanding', $text_domain ); ?></small>
		</div>
	</div>
	<div class="">
		<div class="row">
			<div class="col-md-4 col-sm-6 col-12">
				<?php $description = __( 'The Plugin Now Supports both Twilio and msg91', $text_domain ); ?>
				<?php ihs_get_video_cards( 'New feature | Twilio Support', $description, 'https://www.youtube.com/embed/YnqsWA3Ccuc' ); ?>
			</div>
			<div class="col-md-4 col-sm-6 col-12">
				<?php $description = __( 'How to get API Key | SID | Auth Token | Twilio Phone No', $text_domain ); ?>
				<?php ihs_get_video_cards( 'Generate Twilio API Key | SID | Auth Token | Twilio Phone No', $description, 'https://www.youtube.com/embed/hne6x-8nbA0' ); ?>
			</div>
		</div>
	</div>
</div>