<?php
/**
 * Includes custom field functions files
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

require_once MARKETENGINE_PATH . '/includes/resolution/me-rc-functions.php';
require_once MARKETENGINE_PATH . '/includes/resolution/me-rc-template-functions.php';

require_once MARKETENGINE_PATH . '/includes/resolution/class-me-rc-form.php';
require_once MARKETENGINE_PATH . '/includes/resolution/class-me-rc-handle.php';
require_once MARKETENGINE_PATH . '/includes/resolution/class-me-rc-query.php';

require_once MARKETENGINE_PATH . '/includes/resolution/class-me-case-list.php';

function marketengine_setup_resolution_center() {
	ME_RC_Form::init();
	ME_RC_Query::instance();
}
add_action('after_setup_theme', 'marketengine_setup_resolution_center');