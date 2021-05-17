<?php
/**
 * The template for displaying listing control block.
 *
 * This template can be override by copying it to yourtheme/marketengine/single-listing/control.php
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @since       1.0.1
 * @version     1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$listing_type = $listing->get_listing_type();
$is_owner = $listing->post_author == get_current_user_id();
$listing_status = get_post_status_object($listing->post_status);
?>
<div class="me-status-action">
<?php
	if($listing_type) :
		marketengine_get_template('single-listing/status', array('listing' => $listing) );
		if( $is_owner ) :
			marketengine_get_template('single-listing/control-action', array('listing_type' => $listing_type , 'listing' => $listing, 'listing_status' => $listing_status) );
		else :
			marketengine_get_template('single-listing/'. $listing_type , array('listing' => $listing));
		endif;
	endif;
?>
</div>