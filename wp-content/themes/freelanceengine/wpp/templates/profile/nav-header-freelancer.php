<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="fre-menu-freelancer dropdown-empty">
    <a href="<?php echo et_get_page_link( "my-project" ); ?>"><?php _e( 'My project', ET_DOMAIN ); ?></a>
</li>
<li class="fre-menu-employer dropdown-empty">
    <a href="<?php echo get_post_type_archive_link( PROJECT ); ?>"><?php _e( 'Projects', ET_DOMAIN ); ?></a>
</li>
