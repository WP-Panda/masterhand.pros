<?php
/**
 * The Template for displaying account menu. This is version for mobile.
 *
 * 	This template can be overridden by copying it to yourtheme/marketengine/account/account-menu.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 */
?>
<?php if( is_user_logged_in() ) : ?>

	<ul class="me-account-list-mobile">
		<li><a href="<?php echo marketengine_get_auth_url( 'listings' ); ?>"><?php echo __('My Listings', 'enginethemes'); ?></a></li>
		<li><a href="<?php echo marketengine_get_auth_url( 'orders' ); ?>"><?php echo __('My Orders', 'enginethemes'); ?></a></li>
		<li><a href="<?php echo marketengine_get_auth_url( 'purchases' ); ?>"><?php echo __('My Purchases', 'enginethemes'); ?></a></li>
		<li><a href="<?php echo marketengine_resolution_center_url(); ?>"><?php echo __('Resolution Center', 'enginethemes'); ?></a></li>
		<li><a href="<?php echo marketengine_get_page_permalink( 'user_account' ); ?>"><?php echo __('My Profile', 'enginethemes'); ?></a></li>
		<li><a href="<?php echo wp_logout_url( get_the_permalink() ); ?>"><?php echo __('Logout', 'enginethemes'); ?></a></li>
	</ul>

<?php else: ?>

	<ul class="me-account-list-mobile">
		<li><a href="<?php echo marketengine_get_page_permalink('user_account'); ?>"><?php echo __('Login', 'enginethemes'); ?></a></li>
		<?php if( get_option('users_can_register') ) : ?>
		<li><a href="<?php echo marketengine_get_auth_url( 'register' ); ?>"><?php echo __('Register', 'enginethemes'); ?></a></li>
		<?php endif; ?>

	</ul>

<?php endif; ?>