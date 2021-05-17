<?php
/**
 * 	The Template for displaying information of user.
 * 	This template can be overridden by copying it to yourtheme/marketengine/user-info.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 */

$display_name = get_the_author_meta('display_name', $author_id);
$location = get_the_author_meta('location', $author_id);
$member_sinced = date_i18n( get_option( 'date_format' ), strtotime(get_the_author_meta( 'user_registered', $author_id )) );
?>
<div class="me-authors me-authors-xs <?php echo !empty($class) ? $class : ''; ?>">
	<span class="me-avatar">
		<?php echo marketengine_get_avatar( $author_id ); ?>
		<b><?php echo esc_html( $display_name ); ?></b>
	</span>
	<ul class="me-author-info">
		<li>
			<span><?php echo __('From:', 'enginethemes'); ?><span><?php echo $location ?></span></span>
		</li>
		<li>
			<span class="pull-left"><?php echo __('Member Since:', 'enginethemes'); ?><span><?php echo $member_sinced; ?></span></span>
		</li>
	</ul>
	<a href="<?php echo get_author_posts_url($author_id); ?>" class="me-view-profile"><?php echo __('View profile', 'enginethemes'); ?></a>
</div>