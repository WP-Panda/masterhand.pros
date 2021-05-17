<div class="me-order-note">
	<h3><?php _e("Order Notes", "enginethemes"); ?> <small><?php _e("(optional)", "enginethemes"); ?></small></h3>
	<textarea name="customer_note" placeholder="Notes about your order, e.g. special notes for delivery"><?php if(!empty($_POST['note'])) echo esc_attr( $_POST['note'] ); ?></textarea>
</div>