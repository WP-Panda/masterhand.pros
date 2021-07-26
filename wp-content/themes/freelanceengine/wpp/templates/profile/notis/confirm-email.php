<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
if ( ( ! empty( $user_confirm_email ) && 'confirm' !== $user_confirm_email ) || empty( $user_confirm_email ) ) { ?>
    <div class="notice-first-login blue">
        <p>
            <i class="fa fa-warning"></i>
			<?php _e( 'Please confirm your email to activate your account', WPP_TEXT_DOMAIN ); ?>
            <a class="request-confirm fre-submit-btn btn-right"><?php _e( 'Activate Account', WPP_TEXT_DOMAIN ); ?></a>
        </p>
    </div>
<?php }