<?php
/**
 * MarketEngine Inquiry Form
 *
 * @version     1.0.0
 * @package     Includes/Handle-inquiry
 * @category    Classes
 * @author      EngineThemes
 * @since       1.0.0
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME_Inquiry_Form Class
 *
 * Handle submit, ajax inquire listing
 *
 * @version     1.0.0
 * @since       1.0.0
 */
class ME_Inquiry_Form
{
    /**
     * Initialize all hook of inquiry form
     */
    public static function init_hook()
    {
        // parse_request
        add_action('wp_loaded', array(__CLASS__, 'process_start_inquiry'));

        add_action('wp_ajax_get_messages', array(__CLASS__, 'fetch_messages'));
        add_action('wp_ajax_me_send_message', array(__CLASS__, 'send_message'));

        add_action('wp_ajax_get_contact_list', array(__CLASS__, 'fetch_contact_list'));
        add_action('wp_ajax_me-get-buyer-list', array(__CLASS__, 'search_contact'));

        add_filter('the_marketengine_message', array(__CLASS__, 'filter_message'));

        add_action('save_message_message', array(__CLASS__, 'new_message_in_inquiry'), 10, 2);
        add_action('marketengine_after_inquiry_form', array(__CLASS__, 'clear_unread_message_count'));

        add_action('init', array(__CLASS__, 'rewrite_inquiry_detail_url'));
        add_action('template_redirect', array(__CLASS__, 'rewrite_templates'));
    }


    /**
     * Rewrite inquiry details url rule
     * @since 1.0
     */
    public static function  rewrite_inquiry_detail_url()
    {
        $inquiry_endpoint = marketengine_get_endpoint_name('inquiry');
        add_rewrite_rule($inquiry_endpoint . '/([0-9]+)/?$', 'index.php?message_type=inquiry&p=$matches[1]', 'top');
    }

    public static function rewrite_templates()
    {

        if (get_query_var('message_type')) {
            add_filter('template_include', array(__CLASS__, 'include_inquiry_template'));
        }
    }

    public static function include_inquiry_template()
    {
        return ME()->plugin_path() . '/templates/me-single-inquiry.php';
    }

    /**
     * Update inquiry message count
     *
     * @param int $message_ID
     * @param object $message
     */
    public static function new_message_in_inquiry($message_ID, $message)
    {
        if (!$message_ID) {
            return;
        }

        $inquiry_id = $message->post_parent;

        $inquiry         = marketengine_get_message($inquiry_id);
        $current_user_id = get_current_user_id();
        if (!$inquiry || $inquiry->post_type != 'inquiry') {
            return;
        }

        $message_count = marketengine_get_message_field('message_count', $inquiry_id);
        marketengine_update_message(array('post_type' => 'inquiry', 'message_count' => ($message_count + 1), 'ID' => $inquiry_id), true);

        // update message meta
        if ($current_user_id == $inquiry->receiver) {
            $new_message = marketengine_get_message_meta($inquiry_id, '_me_sender_new_message', true);
            marketengine_update_message_meta($inquiry_id, '_me_sender_new_message', absint($new_message) + 1);
        }

        if ($current_user_id == $inquiry->sender) {
            $new_message = marketengine_get_message_meta($inquiry_id, '_me_recevier_new_message', true);
            marketengine_update_message_meta($inquiry_id, '_me_recevier_new_message', absint($new_message) + 1);
        }

    }

    /**
     * Update number of unread message to 0.
     *
     * @param object $inquiry
     */
    public static function clear_unread_message_count($inquiry)
    {
        $current_user_id = get_current_user_id();
        $inquiry_id      = $inquiry->ID;
        // update message meta
        if ($current_user_id == $inquiry->receiver) {
            marketengine_update_message_meta($inquiry_id, '_me_recevier_new_message', 0);
        }

        if ($current_user_id == $inquiry->sender) {
            marketengine_update_message_meta($inquiry_id, '_me_sender_new_message', 0);
        }
    }

    /**
     * Buyer start inquiry listing in listiting details
     */
    public static function process_start_inquiry()
    {
        if (isset($_POST['send_inquiry']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-send-inquiry')) {
            // check user login
            if (!is_user_logged_in()) {
                $redirect = marketengine_get_page_permalink('user_account');
                wp_redirect($redirect);
                exit;
            }

            $redirect = marketengine_get_page_permalink('inquiry');

            $id = marketengine_get_current_inquiry(absint( $_POST['send_inquiry'] ));

            if (!$id) {
                $result = ME_Inquiry_Handle::inquiry($_POST);
                if (!is_wp_error($result)) {
                    $redirect = add_query_arg(array('inquiry_id' => $result), $redirect);
                    wp_redirect($redirect);
                    exit;
                }
            } else {
                $redirect = add_query_arg(array('inquiry_id' => $id), $redirect);
                wp_redirect($redirect);
                exit;
            }
        }
    }

    /**
     * User send message in a inquiry conversation
     */
    public static function send_message()
    {
        // send message in an inquiry
        if (isset($_POST['content']) && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-inquiry-message')) {
            $result = ME_Inquiry_Handle::message($_POST);
            if (is_wp_error($result)) {
                wp_send_json(array('success' => 'false', 'msg' => $result->get_error_message()));
            } else {
                $message = marketengine_get_message($result);
                ob_start();
                marketengine_get_template('inquiry/message-item', array('message' => $message));
                $content = ob_get_clean();
                wp_send_json(array('success' => true, 'content' => $content));
            }
        }
    }

    /**
     * User fetch the older messages
     */
    public static function fetch_messages()
    {
        if (!empty($_GET['parent']) && !empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'me-inquiry-message')) {
            $parent  = marketengine_get_message(absint( $_GET['parent'] ));
            $user_id = get_current_user_id();
            if ($parent->receiver != $user_id && $parent->sender != $user_id) {
                wp_send_json(array('success' => false));
            }
            $messages = marketengine_get_messages(array('post_type' => 'message', 'showposts' => 12, 'post_parent' => absint( $_GET['parent'] ), 'paged' => absint( $_GET['paged'] )));
            $messages = array_reverse($messages);
            ob_start();
            foreach ($messages as $key => $message) {
                marketengine_get_template('inquiry/message-item', array('message' => $message));
            }
            $content = ob_get_clean();

            wp_send_json(array('success' => true, 'data' => $content));
        }
    }

    /**
     * Seller load the inquiry contact list
     */
    public static function fetch_contact_list()
    {
        if (!empty($_GET['listing'])) {
            $user_id = get_current_user_id();
            $listing = get_post(absint( $_GET['listing'] ));

            $paged = absint( $_GET['paged'] );

            if ($listing->post_author != $user_id) {
                wp_send_json(array('success' => false));
            }

            $args = array(
                'paged'       => $paged,
                'post_parent' => $listing->ID,
                'post_type'   => 'inquiry',
                'showposts'   => 12,
                'inquiry_id'  => absint( $_GET['inquiry_id'] ),
                's'           => esc_sql( $_GET['s'] ),
            );
            $contact_list = ME_Inquiry_Handle::get_contact_list($args);

            wp_send_json(array('success' => true, 'data' => $contact_list['content']));
        }
    }

    /**
     * Ajax search buyer contact list
     */
    public static function search_contact()
    {
        if (isset($_GET['s']) && !empty($_GET['listing_id'])) {
            $user_id = get_current_user_id();
            $listing = get_post(absint( $_GET['listing_id'] ));

            $paged = 1;

            if ($listing->post_author != $user_id) {
                wp_send_json(array('success' => false));
            }

            $args = array(
                'paged'       => $paged,
                'post_parent' => $listing->ID,
                'post_type'   => 'inquiry',
                'showposts'   => 12,
                'inquiry_id'  => absint( $_GET['inquiry_id'] ),
                's'           => esc_sql( $_GET['s'] ),
            );
            $contact_list = ME_Inquiry_Handle::get_contact_list($args);

            wp_send_json(
                array(
                    'success'   => true,
                    'data'      => $contact_list['content'],
                    'count_msg' => sprintf(__("%d people in contact list", "enginethemes"), $contact_list['found_posts']),
                )
            );
        }
    }

    /**
     * Filters the message content
     *
     * @param string $content
     * @return string $content
     */
    public static function filter_message($content)
    {
        $content = nl2br(esc_html($content));

        $content = '<p>' . $content . '</p>';
        $url     = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
        $content = preg_replace($url, '<a href="$0" rel="noopener noreferrer" target="_blank"  title="$0">$0</a>', $content);

        $content = do_shortcode($content);
        return $content;
    }

}
ME_Inquiry_Form::init_hook();
