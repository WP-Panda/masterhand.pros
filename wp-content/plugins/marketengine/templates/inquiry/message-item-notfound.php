<?php
/**
 * The templates for displaying notice when no messages be found.
 * This template can be overridden by copying it to yourtheme/marketengine/inquiry/message-item-notfound.php.
 *
 * @package     MarketEngine/Templates
 * @since 		1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
$user_data = get_userdata($author);
?>
<li class="me-inquires-no-conversation">
	<div class="me-inquires-avatar-seller">
		<?php echo marketengine_get_avatar( $author ); ?>
		<b><?php echo esc_html( $user_data->display_name ); ?></b>
		<p><?php _e("Send your first message to start the conversation.", "enginethemes"); ?></p>
	</div>
</li>