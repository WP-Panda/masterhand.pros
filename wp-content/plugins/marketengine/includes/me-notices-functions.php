<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Marketengine Add Notice
 *
 * Add notice to session manager
 *
 * @see Class ME_Session
 * @param string $message
 * @param string $notice_type successs|error|warning
 *
 * @since 1.0
 *
 * @return void
 */
function marketengine_add_notice($message, $notice_type = 'success') {
    if (!did_action('init')) {
        _doing_it_wrong(__FUNCTION__, __('This function should not be called before wordpress init.', 'enginethemes'), '1.0');
        return;
    }
    $me_notices = ME()->session->get('marketengine_notices', array());
    /**
     * marketengine_add_$notice_type
     * filter notice message
     * @param String $message
     * @since 1.0
     */
    $message = apply_filters('marketengine_add_' . $notice_type, $message);
    $me_notices[$notice_type][] = $message;
    ME()->session->set('marketengine_notices', $me_notices);
}
/**
 * ME Get all Notices
 *
 * Retrieve all user notices
 *
 * @since 1.0
 *
 * @see Class ME_Session
 * @return array Notices
 */
function marketengine_get_notices($notice_type = '') {
    if (!did_action('init')) {
        _doing_it_wrong(__FUNCTION__, __('This function should not be called before wordpress init.', 'enginethemes'), '1.0');
        return;
    }
    $me_notices = ME()->session->get('marketengine_notices', array());
    if (empty($notice_type)) {
        $notices = $me_notices;
    } elseif (isset($me_notices[$notice_type])) {
        $notices = $me_notices[$notice_type];
    } else {
        $notices = array();
    }
    return apply_filters('marketengine_get_notices', $notices);
}
/**
 * Marketengine Empty Notice
 *
 * Clear the notices
 *
 * @since 1.0
 *
 * @return void
 */
function marketengine_empty_notices() {
    if (!did_action('init')) {
        _doing_it_wrong(__FUNCTION__, __('This function should not be called before wordpress init.', 'enginethemes'), '1.0');
        return;
    }
    $me_notices = ME()->session->set('marketengine_notices', array());
}
/**
 * MarketEngine add WP_Error message to error notice
 *
 * Get message in WP_Error object and save notice
 *
 * @since 1.0
 *
 * @param WP_Error $errors Wordpress WP_Error object
 * @return void
 */
function marketengine_wp_error_to_notices($wp_error) {
    if (!did_action('init')) {
        _doing_it_wrong(__FUNCTION__, __('This function should not be called before wordpress init.', 'enginethemes'), '1.0');
        return;
    }
    if (is_wp_error($wp_error) && $wp_error->get_error_messages()) {
        foreach ($wp_error->get_error_messages() as $key => $message) {
            marketengine_add_notice($message, 'error');
        }
    }
}

/**
 * MarketEngine print notices
 *
 * Parse the notices to HTML
 *
 * @since 1.0
 *
 * @param string $notice_type The notice type want to show
 * @return void
 */
function marketengine_print_notices($notice_type = 'all') {
    if (!did_action('init')) {
        _doing_it_wrong(__FUNCTION__, __('This function should not be called before wordpress init.', 'enginethemes'), '1.0');
        return;
    }
    marketengine_get_template('notices/' . $notice_type);
    marketengine_empty_notices();
}