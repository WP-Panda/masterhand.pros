<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="fre-menu-freelancer dropdown">
    <a><?php _e( 'Professionals', ET_DOMAIN ); ?></a>
    <ul class="dropdown-menu">
        <li>
            <a href="<?php echo get_post_type_archive_link( PROJECT ); ?>"><?php _e( 'Find Projects', ET_DOMAIN ); ?></a>
        </li>
		<?php if ( fre_check_register() ) { ?>
            <li>
                <a href="<?php echo et_get_page_link( 'register' ) . '?role=freelancer'; ?>"><?php _e( 'Create Profile', ET_DOMAIN ); ?></a>
            </li>
		<?php } ?>
    </ul>
</li>
<li class="fre-menu-employer dropdown">
    <a><?php _e( 'Clients', ET_DOMAIN ); ?></a>
    <ul class="dropdown-menu">
        <li>
            <a href="<?php echo et_get_page_link( 'login' ) . '?ae_redirect_url=' . urlencode( et_get_page_link( 'submit-project' ) ); ?>"><?php _e( 'Post a Project', ET_DOMAIN ); ?></a>
        </li>
        <li>
            <a href="<?php echo get_post_type_archive_link( PROFILE ); ?>"><?php _e( 'Find Professionals', ET_DOMAIN ); ?></a>
        </li>
    </ul>
</li>
