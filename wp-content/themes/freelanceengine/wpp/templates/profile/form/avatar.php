<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
?>
<div class="col-lg-2 col-md-4 col-sm-12 col-xs-12 employer-info-avatar avatar-profile-page">
    <span class="employer-avatar img-avatar image"><?php echo get_avatar( $user_ID, 125 ) ?></span>
    <a href="#" id="user_avatar_browse_button">
		<?php _e( 'Change Photo', WPP_TEXT_DOMAIN ) ?>
    </a>
</div>