<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
?>
<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
    <label class="fre-field-title"><?php _e( 'Password', ET_DOMAIN ); ?></label>
    <a href="#" class="change-password">
		<?php _e( '******', ET_DOMAIN ); ?>
    </a>

	<?php if ( function_exists( 'fre_credit_add_request_secure_code' ) ) {
		$fre_credit_secure_code = ae_get_option( 'fre_credit_secure_code' );
		if ( ! empty( $fre_credit_secure_code ) ) {
			?>
            <ul class="fre-secure-code">
                <li>
                    <span><?php _e( "Secure code", ET_DOMAIN ) ?></span>
                </li>
				<?php do_action( 'fre-profile-after-list-setting' ); ?>
            </ul>
		<?php }
	} ?>
</div>