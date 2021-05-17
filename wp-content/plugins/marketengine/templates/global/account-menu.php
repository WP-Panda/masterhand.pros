<?php
/**
 * This template can be overridden by copying it to yourtheme/marketengine/account/account-menu.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 *
 * @since 		1.0.0
 *
 * @version     1.0.0
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

?>
<?php if( is_user_logged_in() ) : ?>
<nav class="me-menu-account">
	<span class="me-my-account"><?php echo get_the_author_meta('display_name', get_current_user_id()); ?></span>
	<ul class="me-account-list">
		<li><a href="<?php echo marketengine_get_auth_url( 'listings' ); ?>"><?php echo __('My Listings', 'enginethemes'); ?></a></li>
		<li><a href="<?php echo marketengine_get_auth_url( 'orders' ); ?>"><?php echo __('My Orders', 'enginethemes'); ?></a></li>
		<li><a href="<?php echo marketengine_get_auth_url( 'purchases' ); ?>"><?php echo __('My Purchases', 'enginethemes'); ?></a></li>
		<li><a href="<?php echo marketengine_resolution_center_url(); ?>"><?php echo __('Resolution Center', 'enginethemes'); ?></a></li>
		<li><a href="<?php echo marketengine_get_page_permalink( 'user_account' ); ?>"><?php echo __('My Profile', 'enginethemes'); ?></a></li>
		<li><a href="<?php echo wp_logout_url( get_the_permalink() ); ?>"><?php echo __('Logout', 'enginethemes'); ?></a></li>
	</ul>
</nav>
<?php else: ?>
	<nav class="me-menu-account">
		<span class="me-my-account"><?php echo __('MY ACCOUNT', 'enginethemes'); ?></span>
		<ul class="me-account-list">
			<li><a href="<?php echo marketengine_get_page_permalink('user_account'); ?>"><?php echo __('Login', 'enginethemes'); ?></a></li>
			<?php if( get_option('users_can_register') ) : ?>
			<li><a href="<?php echo marketengine_get_auth_url( 'register' ); ?>"><?php echo __('Register', 'enginethemes'); ?></a></li>
			<?php endif; ?>

		</ul>
	</nav>
<?php endif; ?>