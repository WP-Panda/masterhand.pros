<?php
/**
 * 	The Template for displaying notices for admin.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates/Admin
 * @since 		1.0.1
 * @version     1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>

<?php
$notices = array();
$notices = apply_filters( 'marketengine_admin_notices', $notices );

if( !empty($notices) ) : ?>

	<div class="me-notification-error">
		<i class="icon-me-warning"></i>
		<?php foreach ($notices as $key => $notice) : ?>

			<p><?php echo $notice; ?></p>

		<?php endforeach; ?>

	</div>

<?php endif; ?>