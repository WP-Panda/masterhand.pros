<?php
$address_fields = array(
	'first_name' => __("First name", "enginethemes"),
	'last_name' => __("Last name", "enginethemes"),
	'email' => __("Email address", "enginethemes"),
	'phone' => __("Phone", "enginethemes"),
	'country' => __("Country", "enginethemes"),
	'town' => __("Town/City", "enginethemes"),
	'address' => __("Street address", "enginethemes"),
	'postcode' => __("Post code", "enginethemes")
);
$billing_fields = apply_filters('marketengine_billing_fields', $address_fields);
$shipping_fields = apply_filters('marketengine_shipping_fields', $address_fields);
?>
<div class="me-billing-shipping">
	<h3 class="me-title-bill-ship"><?php _e("Billing Details", "enginethemes"); ?></h3>
	<div class="me-switch-billing">

		<?php marketengine_print_notices(); ?>
		
		<!-- billing info -->
		<div class="me-billing-address">
			<?php /* <h4><?php _e("My billing address", "enginethemes"); ?></h4> */ ?>
			<div class="me-row">
				<div class="me-col-md-6">
					<div class="marketengine-input-field">
						<label class="me-field-title"><?php _e("First name", "enginethemes"); ?></label>
						<input type="text" name="billing_info[first_name]" value="<?php if(!empty($_POST['billing_info']['first_name'])) echo esc_attr( $_POST['billing_info']['first_name'] ); ?>">
					</div>
				</div>
				<div class="me-col-md-6">
					<div class="marketengine-input-field">
						<label class="me-field-title"><?php _e("Last name", "enginethemes"); ?></label>
						<input type="text" name="billing_info[last_name]" value="<?php if(!empty($_POST['billing_info']['last_name'])) echo esc_attr( $_POST['billing_info']['last_name'] ); ?>">
					</div>
				</div>
			</div>
			<div class="me-row">
				<div class="me-col-md-6">
					<div class="marketengine-input-field">
						<label class="me-field-title"><?php _e("Email address", "enginethemes"); ?></label>
						<input type="text" name="billing_info[email]" value="<?php if(!empty($_POST['billing_info']['email'])) echo esc_attr( $_POST['billing_info']['email'] ); ?>">
					</div>
				</div>
				<div class="me-col-md-6">
					<div class="marketengine-input-field">
						<label class="me-field-title"><?php _e("Phone", "enginethemes"); ?></label>
						<input type="text" name="billing_info[phone]" value="<?php if(!empty($_POST['billing_info']['phone'])) echo esc_attr( $_POST['billing_info']['phone'] ); ?>">
					</div>
				</div>
			</div>
			<div class="me-row">
				<div class="me-col-md-6">
					<div class="marketengine-input-field">
						<label class="me-field-title"><?php _e("Country", "enginethemes"); ?></label>
						<input type="text" name="billing_info[country]" value="<?php if(!empty($_POST['billing_info']['country'])) echo esc_attr( $_POST['billing_info']['country'] ); ?>">
					</div>
				</div>
				<div class="me-col-md-6">
					<div class="marketengine-input-field">
						<label class="me-field-title"><?php _e("Town/City", "enginethemes"); ?></label>
						<input type="text" name="billing_info[city]" value="<?php if(!empty($_POST['billing_info']['city'])) echo esc_attr( $_POST['billing_info']['city'] ); ?>">
					</div>
				</div>
			</div>
			<div class="me-row">
				<div class="me-col-md-6">
					<div class="marketengine-input-field">
						<label class="me-field-title"><?php _e("Street address", "enginethemes"); ?></label>
						<input type="text" name="billing_info[address]" value="<?php if(!empty($_POST['billing_info']['address'])) echo esc_attr( $_POST['billing_info']['address'] ); ?>">
					</div>
				</div>
				<div class="me-col-md-6">
					<div class="marketengine-input-field">
						<label class="me-field-title"><?php _e("Post code", "enginethemes"); ?> <small><?php _e("(optional)", "enginethemes"); ?></small></label>
						<input type="text" name="billing_info[postcode]" value="<?php if(!empty($_POST['billing_info']['postcode'])) echo esc_attr( $_POST['billing_info']['postcode'] ); ?>">
					</div>
				</div>
			</div>

		</div>
	</div>
</div>