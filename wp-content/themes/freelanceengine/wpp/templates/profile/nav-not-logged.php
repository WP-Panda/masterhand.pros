<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="col-sm-12 col-md-12 col-lg-2 col-xs-12 fre-account-wrap">
    <div class="fre-login-wrap">
        <ul class="fre-login row">
            <li class="col-sm-12 col-md-12 col-lg-6">
                <a href="<?php echo et_get_page_link( "login" ) ?>"><?php _e( 'Login', ET_DOMAIN ); ?></a>
            </li>
			<?php if ( fre_check_register() ) { ?>
                <li class="col-sm-12 col-md-12 col-lg-6">
                    <a href="<?php echo et_get_page_link( "register" ) ?>"><?php _e( 'Sign up', ET_DOMAIN ); ?></a>
                </li>
			<?php } ?>
        </ul>
    </div>
</div>