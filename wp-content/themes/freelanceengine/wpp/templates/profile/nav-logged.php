<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
?>
<div class="col-lg-2 col-md-12 col-sm-12 col-xs-12 fre-account-wrap dropdown">

    <a class="fre-notification dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
        <i class="fa fa-bell-o" aria-hidden="true"></i>
		<?php
		$notify_number = wpp_user_have_notify();
		if ( ! empty( $notify_number ) ) {
			printf( '<span class="trigger-overlay trigger-notification-2 circle-new">%s</span>', $notify_number );
		} ?>
    </a>

	<?php wpp_user_notification( $user_ID, 1, 5 ); ?>
    <div class="fre-account dropdown">

        <div class="fre-account-info dropdown-toggle" data-toggle="dropdown">
            <span class="hamburger-menu">
                 <?php echo get_avatar( $user_ID ); ?>
                <div class="hamburger hamburger--elastic"
                     tabindex="0"
                     aria-label="Menu"
                     role="button"
                     aria-controls="navigation">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </span>
        </div>

        <ul class="dropdown-menu">
            <li>
                <a href="<?php echo et_get_page_link( "profile" ) ?>">
					<?php _e( 'My profile', ET_DOMAIN ); ?>
                </a>
            </li>
			<?php if ( wpp_fre_is_freelancer() ) { ?>
                <li>
                    <a href="/my-adverts/">
						<?php _e( 'My Special Offers', ET_DOMAIN ); ?>
                    </a>
                </li>
			<?php } else { ?>
                <li>
                    <a href="/special-offers/">
						<?php _e( 'Special Offers', ET_DOMAIN ); ?>
                    </a>
                </li>
			<?php }
			do_action( 'fre_header_before_notify' ); ?>
            <li>
                <a href="<?php echo wp_logout_url(); ?>">
					<?php _e( 'Logout', ET_DOMAIN ); ?>
                </a>
            </li>
        </ul>

    </div>
</div>