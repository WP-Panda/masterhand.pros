<?php
/**
 * The template for displaying the form to send message.
 *
 * This template can be override by copying it to
 * yourtheme/marketengine/inquiry/send-message-form.php.
 *
 * @author EngineThemes
 * @package MarketEngine/Templates
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<form method="post" id="send-message">
	<div class="me-mc-container" id="me-mc-container"></div>
	<textarea id="me-message-content" class="required me-message-content" required name="content" placeholder="<?php _e("Type your message here", "enginethemes");?>"></textarea>
	<div class="upload-container">
		<span id="me-message-send-btn" class="me-message-send-btn"><i class="icon-me-attach"></i></span>
	</div>

	<?php wp_nonce_field('me-inquiry-message', '_msg_wpnonce');?>
	<?php wp_nonce_field('marketengine', '_msg_file_nonce');?>
	
	<input type="hidden" name="inquiry_listing" value="<?php echo $listing->get_id(); ?>" />
	<input type="hidden" name="inquiry_id" value="<?php echo $inquiry->ID; ?>" />
</form>