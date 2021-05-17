<?php
/**
 * The template for displaying status of listing.
 *
 * This template can be override by copying it to yourtheme/marketengine/single-listing/status.php
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @since       1.0.0
 * @version     1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$listing_status = get_post_status_object($listing->post_status);
$is_owner = $listing->post_author == get_current_user_id();
?>

<?php if( $is_owner || current_user_can( 'manage_options' ) ) : ?>
<div class="me-status">
	<div class="me-label-<?php echo str_replace('me-', '', $listing_status->name); ?>">
		<span><?php echo ucfirst($listing_status->label); ?></span>
	</div>
</div>
<?php endif; ?>