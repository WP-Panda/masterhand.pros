<?php

/**
 * MarketEngine Dispute Form Class
 *
 * @author         EngineThemes
 * @package         Includes/Resolution
 */
class ME_RC_Form
{
    public static function init()
    {
        add_action('wp_loaded', array(__CLASS__, 'dispute'));
        add_action('wp_loaded', array(__CLASS__, 'request_close'));
        add_action('wp_loaded', array(__CLASS__, 'close'));
        add_action('wp_loaded', array(__CLASS__, 'escalate'));
        add_action('wp_loaded', array(__CLASS__, 'resolve'));

        add_action('wp_ajax_me-dispute-debate', array(__CLASS__, 'debate'));
        add_action('wp_ajax_get_messages', array(__CLASS__, 'fetch_messages'));
    }

    public static function dispute()
    {
        if (isset($_POST['me-open-dispute-case']) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-open_dispute_case')) {

            $case = ME_RC_Form_Handle::insert($_POST);

            if (is_wp_error($case)) {
                marketengine_wp_error_to_notices($case);
            } else {
                $redirect = marketengine_rc_dispute_link($case);
                wp_redirect($redirect);
                exit;
            }
        }
    }

    public static function request_close()
    {
        if (!empty($_GET['request-close']) && !empty($_GET['wpnonce']) && wp_verify_nonce($_GET['wpnonce'], 'me-request_close_dispute')) {
            $case = ME_RC_Form_Handle::request_close(absint($_GET['request-close']));
            if (is_wp_error($case)) {
                marketengine_wp_error_to_notices($case);
            }
            wp_redirect(marketengine_rc_dispute_link(absint($_GET['request-close'])));
            exit;
        }
    }

    public static function close()
    {
        if (!empty($_GET['close']) && !empty($_GET['wpnonce']) && wp_verify_nonce($_GET['wpnonce'], 'me-close_dispute')) {
            $case = ME_RC_Form_Handle::close($_GET['close']);
            if (is_wp_error($case)) {
                marketengine_wp_error_to_notices($case);
                wp_redirect(marketengine_rc_dispute_link($_GET['close']));
                exit;
            } else {
                wp_redirect(marketengine_rc_dispute_link($case));
                exit;
            }
        }
    }

    public static function escalate()
    {
        if (!empty($_POST['dispute']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-escalate_case')) {
            $case = ME_RC_Form_Handle::escalate($_POST);
            if (is_wp_error($case)) {
                marketengine_wp_error_to_notices($case);
            } else {
                wp_redirect(marketengine_rc_dispute_link($case));
                exit;
            }

        }
    }

    public static function resolve()
    {
        if (!empty($_POST['dispute']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'marketengine_arbitrate-dispute')) {
            $case = ME_RC_Form_Handle::resolve($_POST);
            if (is_wp_error($case)) {
                marketengine_wp_error_to_notices($case);
            } else {
                wp_redirect(marketengine_rc_dispute_link($case));
                exit;
            }

        }
    }

    public static function debate()
    {
        if (!empty($_REQUEST['dispute']) && !empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'me-debate')) {
            $message = ME_RC_Form_Handle::debate($_REQUEST);
            if (!is_wp_error($case)) {
                $message = marketengine_get_message($message);
                ob_start();
                marketengine_get_template('resolution/message-item', array('message' => $message));
                $content = ob_get_clean();
                wp_send_json(array('success' => true, 'html' => $content));
            } else {
                wp_send_json(array('success' => false));
            }
        }
    }

    /**
     * User fetch the older messages
     */
    public static function fetch_messages()
    {
        if (!empty($_GET['parent']) && !empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'me-debate')) {
            $parent = marketengine_get_message(absint($_GET['parent']));

            $user_id = get_current_user_id();
            if ($parent->receiver != $user_id && $parent->sender != $user_id) {
                wp_send_json(array('success' => false));
            }
            $messages = marketengine_get_messages(array('post_type' => array('message', 'revision'), 'showposts' => 12, 'post_parent' => $parent->ID, 'paged' => absint($_GET['paged'])));
            $messages = array_reverse($messages);

            ob_start();
            foreach ($messages as $key => $message) {
                marketengine_get_template('resolution/' . $message->post_type . '-item', array('message' => $message));
            }
            $content = ob_get_clean();

            wp_send_json(array('success' => true, 'data' => $content));
        }
    }
}
