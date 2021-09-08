<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="fre-menu-employer dropdown">
    <a><?php _e( 'Projects', ET_DOMAIN ); ?></a>
    <ul class="dropdown-menu">
        <li>
            <a href="<?php echo et_get_page_link( "my-project" ); ?>"><?php _e( 'All Projects Posted', ET_DOMAIN ); ?></a>
        </li>
        <li>
            <a href="<?php echo et_get_page_link( 'submit-project' ); ?>"><?php _e( 'Post a Project', ET_DOMAIN ); ?></a>
        </li>
    </ul>
</li>
<li class="fre-menu-employer dropdown-empty">
    <a href="<?php echo get_post_type_archive_link( PROFILE ); ?>"><?php _e( 'Professionals', ET_DOMAIN ); ?></a>
</li>