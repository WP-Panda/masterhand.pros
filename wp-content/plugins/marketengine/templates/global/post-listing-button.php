<?php
/**
 *	Post listing button
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<a href="<?php echo marketengine_get_page_permalink('post_listing') ?>" class="me-post-listing"><i class="icon-me-add-circle"></i><span class=""><?php echo __('Post a Listing', 'enginethemes'); ?></span></a>