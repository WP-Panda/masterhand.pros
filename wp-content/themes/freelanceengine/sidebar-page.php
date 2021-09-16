<?php
/**
 * The Sidebar containing widget area on static page left side
 *
 * @package FreelanceEngine
 * @since   1.0
 */
if ( is_active_sidebar( 'sidebar-page' ) ) : ?>
    <div class="primary-sidebar widget-area" role="complementary">
        <div class="hidden-xs fre-profile-box"><?php dynamic_sidebar( 'sidebar-page' ); ?></div>
    </div>
<?php endif;