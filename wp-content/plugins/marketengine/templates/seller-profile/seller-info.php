<?php
/**
 * 	The Template for displaying information of seller.
 * 	This template can be overridden by copying it to yourtheme/marketengine/seller-profile/seller-info.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 */

$display_name = get_the_author_meta('display_name', $user_id);
$location = get_the_author_meta('location', $user_id);
$member_sinced = date_i18n( get_option( 'date_format' ), strtotime(get_the_author_meta( 'user_registered', $user_id )) );
?>
<div class="me-authors me-authors-xs">
	<span class="me-avatar">
		<?php echo marketengine_get_avatar( $user_id ); ?>
		<b><?php echo $display_name ?></b>
	</span>
	<ul class="me-author-info">
		<li>
			<span class="pull-left"><?php _e('From:', 'enginethemes'); ?></span>
			<b class="pull-right"><?php echo $location; ?></b>
		</li>
		<?php /*
		<li>
			<span class="pull-left">Language:</span>
			<b class="pull-right">Vietnam</b>
		</li>
		*/ ?>
		<li>
			<span class="pull-left"><?php _e('Member Since:', 'enginethemes'); ?></span>
			<b class="pull-right"><?php echo $member_sinced; ?></b>
		</li>
	</ul>
	<?php /* <input type="button" class="me-contact-btn" value="<?php _e('CONTACT', 'enginethemes'); ?>"> */ ?>
</div>