<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<ul class="nav nav-justify-content-center" id="Tabs" role="tablist">
    <li class="nav-item active">
        <a class="nav-link" id="rating-tab" data-toggle="tab" href="#rating" role="tab"
           aria-controls="rating" aria-selected="true">
			<?php _e("Rating", ET_DOMAIN ); ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab"
           aria-controls="reviews" aria-selected="false">
			<?php _e("Reviews", ET_DOMAIN ); ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="settings-tab" data-toggle="tab" href="#settings" role="tab"
           aria-controls="settings" aria-selected="false">
			<?php _e("Settings", ET_DOMAIN ); ?>
        </a>
    </li>
</ul>