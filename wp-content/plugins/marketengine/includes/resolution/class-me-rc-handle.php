<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * MarketEngine Dispute Form Handle Class
 *
 * @package Includes/Resolution
 * @category Class
 * @version 1.0
 * @since 1.1
 */
class ME_RC_Form_Handle
{
    public static function insert($case_data)
    {
        $sender = get_current_user_id();

        $transaction = marketengine_get_order($case_data['transaction-id']);

        if (!$transaction) {
            return new WP_Error('order_not_found', __('The transaction does not exist.', 'enginethemes'));
        }

        if ($sender != $transaction->post_author) {
            return new WP_Error('permission_denied', __('You can not dispute this transaction.', 'enginethemes'));
        }

        $receiver = $transaction->get_seller();
        if (!$receiver || is_wp_error($receiver)) {
            return new WP_Error('user_not_exists', __('You can not dispute because this user has already remove from system.', 'enginethemes'));
        }

        $error_data = array();

        if (empty($case_data['is_received_item'])) {
            $error_data['invalid_received_item_option'] = __('Did you receive the item?', 'enginethemes');
        }

        if (empty($case_data['dispute_problem'])) {
            $error_data['empty_dispute_problem'] = __('Please tell us your problem.', 'enginethemes');
        }

        if (empty($case_data['dispute_content'])) {
            $error_data['empty_dispute_content'] = __('Please describe something about your problem.', 'enginethemes');
        }

        if (empty($case_data['expect_solution'])) {
            $error_data['empty_expect_solution'] = __('Please choose a resolution you want.', 'enginethemes');
        }

        if (!empty($error_data)) {
            $wp_error = new WP_Error();
            foreach ($error_data as $key => $value) {
                $wp_error->add($key, $value);
            }
            return $wp_error;
        }

        $receiver_id = $receiver->ID;
        $data        = array(
            'post_content' => wp_kses_post($case_data['dispute_content']),
            'post_title'   => 'Dispute transaction #' . $transaction->id,
            'post_type'    => 'dispute',
            'receiver'     => $receiver_id,
            'post_parent'  => $transaction->id,
            'sender'       => $sender,
            'post_status'  => 'me-open',
        );

        $data    = array_merge($data, $case_data);
        $case_id = self::create_dispute($data, $transaction);

        marketengine_dispute_order($transaction->id);
        do_action('marketengine_after_dispute', $transaction->id, $case_id, $transaction);

        return $case_id;
    }

    /**
     * Create a message in dispute
     *
     * @param array $data The message data
     *
     * @return int | WP_Error
     */
    public static function debate($data)
    {
        $case = marketengine_get_message(absint($data['dispute']));
        if (!$case) {
            return new WP_Error('invalid_case', __("Invalid case.", "enginethemes"));
        }

        $sender = get_current_user_id();

        if (!current_user_can('manage_options') && $sender != $case->sender && $sender != $case->receiver) {
            return new WP_Error('permission_denied', __("You can not send message to this case.", "enginethemes"));
        }

        if ($sender == $case->sender) {
            $receiver_id = $case->receiver;
        } else {
            $receiver_id = $case->sender;
        }

        $message_data = array(
            'post_content' => wp_kses_post($data['post_content']),
            'post_title'   => 'Message case #' . $case->ID,
            'post_type'    => 'message',
            'receiver'     => $receiver_id,
            'post_parent'  => $case->ID,
            'sender'       => $sender,
            'post_status'  => 'read',
        );

        if (!empty($data['dispute_file'])) {
            $message_data['dispute_file'] = array_map('absint', $data['dispute_file']);
        }

        return self::create_dispute_message($message_data);
    }

    /**
     * Create a dispute case
     *
     * @param array $data The dispute data
     * @param object $transaction The ME_Order object
     *
     * @return int
     */
    public static function create_dispute($data, $transaction)
    {
        $case_id = marketengine_insert_message($data);
        if ($case_id) {

            marketengine_update_message_meta($case_id, '_case_problem', sanitize_text_field($data['dispute_problem']));
            marketengine_update_message_meta($case_id, '_case_problem_description', sanitize_text_field($data['dispute_content']));
            marketengine_update_message_meta($case_id, '_case_expected_resolution', sanitize_text_field($data['expect_solution']));

            if ($data['is_received_item']) {
                marketengine_update_message_meta($case_id, '_case_is_received_item', sanitize_text_field($data['is_received_item']));
            }

            $data['post_parent'] = $case_id;
            $data['case_id']     = $case_id;
            self::create_dispute_message($data);
            // add revision
            self::add_dispute_revision('me-open', marketengine_get_message($case_id));
            // email seller
            self::new_dispute_notify($data, $transaction);
        }
        return $case_id;
    }

    /**
     * Insert message to dispute case
     * @param array $data Message data
     * @param object $transaction The ME_Order object
     * @return int
     */
    public static function create_dispute_message($data)
    {
        $data['post_type'] = 'message';
        if (!empty($data['dispute_file'])) {
            $data['post_content'] .= "\n";
            $data['post_content'] .= '[me_message_file id=' . join(',', $data['dispute_file']) . ' ]';
        }

        return marketengine_insert_message($data);
    }

    /**
     * Send mail to seller after buyer dispute
     *
     * @param array $data The dispute data
     * @param object $transaction The order buyer request dispute
     */
    public static function new_dispute_notify($data, $transaction)
    {
        $subject = __("There is a dispute for your transaction.", "enginethemes");
        $args    = array(
            'display_name' => get_the_author_meta('display_name', $data['receiver']),
            'buyer_name'   => get_the_author_meta('display_name', $data['sender']),
            'blogname'     => get_bloginfo('blogname'),
            'order_link'   => $transaction->get_order_detail_url(),
            'order'        => $transaction,
            'order_id'     => $transaction->id,
            'dispute_link' => marketengine_rc_dispute_link($data['case_id']),
        );
        // get dispute mail content from template
        ob_start();
        marketengine_get_template('resolution/emails/dispute-email', $args);
        $dispute_mail_content = ob_get_clean();

        $user = get_userdata($data['receiver']);
        /**
         * Filter user dispute email content
         *
         * @param String $dispute_mail_content
         * @param Object $transaction
         *
         * @since 1.1
         */
        $dispute_mail_content = apply_filters('marketengine_dispute_mail_content', $dispute_mail_content, $transaction);
        return wp_mail($user->user_email, $subject, $dispute_mail_content);
    }

    /**
     * Seller request buyer to close dispute
     * @param int $dispute_id
     * @return int | WP_Error
     */
    public static function request_close($dispute_id)
    {
        $dispute = marketengine_get_message(absint($dispute_id));
        if (!$dispute) {
            return new WP_Error('invalid_case', __("Invalid case id.", "enginethemes"));
        }

        if ($dispute->post_status !== 'me-open' || get_current_user_id() != $dispute->receiver) {
            return new WP_Error('permission_denied', __("You can not request to close this case.", "enginethemes"));
        }

        $dispute_id = marketengine_update_message(array('ID' => $dispute_id, 'post_status' => 'me-waiting'));

        // add revision
        self::add_dispute_revision('me-waiting', $dispute);
        self::request_close_notify($dispute);

        return $dispute_id;
    }

    public static function request_close_notify($dispute)
    {
        $subject = __("A new request to close the dispute.", "enginethemes");
        $args    = array(
            'display_name' => get_the_author_meta('display_name', $dispute->sender),
            'seller_name'  => get_the_author_meta('display_name', $dispute->receiver),
            'blogname'     => get_bloginfo('blogname'),
            'dispute_link' => marketengine_rc_dispute_link($dispute->ID),
        );
        // get dispute mail content from template
        ob_start();
        marketengine_get_template('resolution/emails/request-close', $args);
        $request_close_mail_content = ob_get_clean();

        $user = get_userdata($dispute->sender);
        /**
         * Filter user request close dispute email content
         *
         * @param String $request_close_mail_content
         * @param Object $dispute The dispute object
         *
         * @since 1.1
         */
        $request_close_mail_content = apply_filters('marketengine_close_dispute_mail_content', $request_close_mail_content, $dispute);
        return wp_mail($user->user_email, $subject, $request_close_mail_content);
    }

    /**
     * Buyer close the dispute case
     * @param int $case_id The dispute case id
     */
    public static function close($case_id)
    {
        $dispute = marketengine_get_message(absint($case_id));
        if (!$dispute) {
            return new WP_Error('invalid_case', __("Invalid case id.", "enginethemes"));
        }

        if ('me-resolved' === $dispute->post_status || 'me-closed' === $dispute->post_status || get_current_user_id() != $dispute->sender) {
            return new WP_Error('permission_denied', __("You can not close this case.", "enginethemes"));
        }

        $case_id = marketengine_update_message(array('ID' => $case_id, 'post_status' => 'me-closed'));
        // add revision
        self::add_dispute_revision('me-closed', $dispute);
        self::close_notify($dispute);

        marketengine_resolve_order($dispute->post_parent);

        return $case_id;

    }

    public static function close_notify($dispute)
    {
        $subject = __("Your dispute has been closed.", "enginethemes");
        $args    = array(
            'display_name' => get_the_author_meta('display_name', $dispute->receiver),
            'buyer_name'   => get_the_author_meta('display_name', $dispute->sender),
            'blogname'     => get_bloginfo('blogname'),
            'dispute_link' => marketengine_rc_dispute_link($dispute->ID),
        );
        // get dispute mail content from template
        ob_start();
        marketengine_get_template('resolution/emails/close-dispute', $args);
        $close_dispute_mail_content = ob_get_clean();

        $user = get_userdata($dispute->receiver);
        /**
         * Filter user close dispute email content
         *
         * @param String $close_dispute_mail_content
         * @param Object $dispute The dispute object
         *
         * @since 1.1
         */
        $close_dispute_mail_content = apply_filters('marketengine_close_dispute_mail_content', $close_dispute_mail_content, $dispute);
        return wp_mail($user->user_email, $subject, $close_dispute_mail_content);
    }

    public static function escalate($case_data)
    {
        $case = marketengine_get_message(absint($case_data['dispute']));
        if (!$case) {
            return new WP_Error('invalid_case', __("Invalid case id.", "enginethemes"));
        }

        $current_user_id = get_current_user_id();
        if ($current_user_id != $case->sender && $current_user_id != $case->receiver) {
            return new WP_Error('permission_denied', __("You do not have permission to escalate case.", "enginethemes"));
        }

        if (empty($case_data['post_content'])) {
            return new WP_Error('permission_denied', __("The escalte content is required.", "enginethemes"));
        }

        $case_id = marketengine_update_message(array('ID' => $case->ID, 'post_status' => 'me-escalated'));
        marketengine_update_message_meta($case_id, '_escalated_by', $current_user_id);

        self::debate($case_data);
        self::add_dispute_revision('me-escalated', $case);
        
        // gui mail
        if ($case->sender == $current_user_id) {
            self::escalate_notify_seller($case);
        } else {
            self::escalate_notify_buyer($case);
        }

        self::escalate_notify_admin($case, $current_user_id);

        return $case_id;
    }

    public static function escalate_notify_buyer($dispute)
    {
        $subject     = __("Your dispute has been escalated.", "enginethemes");
        $transaction = marketengine_get_order($dispute->post_parent);
        $buyer_id    = $dispute->sender;
        $seller_id   = $dispute->receiver;

        $args = array(
            'display_name' => get_the_author_meta('display_name', $buyer_id),
            'seller_name'  => get_the_author_meta('display_name', $seller_id),
            'blogname'     => get_bloginfo('blogname'),
            'dispute_link' => marketengine_rc_dispute_link($dispute->ID),
            'order_link'   => $transaction->get_order_detail_url(),
            'order'        => $transaction,
            'order_id'     => $transaction->id,
        );
        // get dispute mail content from template
        ob_start();
        marketengine_get_template('resolution/emails/escalate-to-buyer', $args);
        $escalate_buyer_mail_content = ob_get_clean();

        $buyer = get_userdata($buyer_id);
        /**
         * Filter user escalate dispute email to buyer content
         *
         * @param String $escalate_buyer_mail_content
         * @param Object $dispute The dispute object
         *
         * @since 1.1
         */
        $escalate_buyer_mail_content = apply_filters('marketengine_escalate_to_buyer_mail_content', $escalate_buyer_mail_content, $dispute);
        return wp_mail($buyer->user_email, $subject, $escalate_buyer_mail_content);
    }

    public static function escalate_notify_seller($dispute)
    {
        $subject     = __("Your dispute has been escalated.", "enginethemes");
        $transaction = marketengine_get_order($dispute->post_parent);
        $buyer_id    = $dispute->sender;
        $seller_id   = $dispute->receiver;

        $args = array(
            'display_name' => get_the_author_meta('display_name', $seller_id),
            'buyer_name'   => get_the_author_meta('display_name', $buyer_id),
            'blogname'     => get_bloginfo('blogname'),
            'dispute_link' => marketengine_rc_dispute_link($dispute->ID),
            'order_link'   => $transaction->get_order_detail_url(),
            'order'        => $transaction,
            'order_id'     => $transaction->id,
        );
        // get dispute mail content from template
        ob_start();
        marketengine_get_template('resolution/emails/escalate-to-seller', $args);
        $escalate_seller_mail_content = ob_get_clean();

        $seller = get_userdata($seller_id);
        /**
         * Filter user escalate to seller dispute email content
         *
         * @param String $escalate_seller_mail_content
         * @param Object $dispute The dispute object
         *
         * @since 1.1
         */
        $escalate_seller_mail_content = apply_filters('marketengine_escalate_to_seller_mail_content', $escalate_seller_mail_content, $dispute);
        return wp_mail($seller->user_email, $subject, $escalate_seller_mail_content);
    }

    public static function escalate_notify_admin($dispute, $sender)
    {
        $subject     = sprintf(__("Dispute on the transaction #%d has been escalated.", "enginethemes"), $dispute->post_parent);
        $transaction = marketengine_get_order($dispute->post_parent);

        $args = array(
            'display_name' => 'Admin',
            'sender_name'  => get_the_author_meta('display_name', $sender),
            'blogname'     => get_bloginfo('blogname'),
            'dispute_link' => marketengine_rc_dispute_link($dispute->ID),
            'order_link'   => $transaction->get_order_detail_url(),
            'order'        => $transaction,
            'order_id'     => $transaction->id,
        );

        // get dispute mail content from template
        ob_start();
        if ($sender == $dispute->sender) {
            marketengine_get_template('resolution/emails/buyer-escalate-to-admin', $args);
        } else {
            marketengine_get_template('resolution/emails/seller-escalate-to-admin', $args);
        }
        $escalate_admin_mail_content = ob_get_clean();

        $admin_email = get_option('admin_email');
        /**
         * Filter user escalate dispute email content
         *
         * @param String $escalate_dispute_mail_content
         * @param Object $dispute The dispute object
         *
         * @since 1.1
         */
        $escalate_admin_mail_content = apply_filters('marketengine_escalate_to_admin_mail_content', $escalate_admin_mail_content, $dispute);
        return wp_mail($admin_email, $subject, $escalate_admin_mail_content);
    }

    public static function resolve($case_data)
    {
        if (!current_user_can('manage_options')) {
            return new WP_Error('permission_denied', __("You do not have permission to resolve case.", "enginethemes"));
        }
        $dispute = marketengine_get_message($case_data['dispute']);
        if ($dispute->post_status !== 'me-escalated') {
            return new WP_Error('invalid_case', __("You can not resolve this case.", "enginethemes"));
        }

        if (empty($case_data['me-dispute-win'])) {
            return new WP_Error('empty_content', __("You have to specify who is winner.", "enginethemes"));
        }

        if (empty($case_data['arbitrate_content'])) {
            return new WP_Error('empty_content', __("The arbitrate content is required.", "enginethemes"));
        }
        $winner = absint( $case_data['me-dispute-win'] );
        $case_id = marketengine_update_message(array('ID' => $dispute->ID, 'post_status' => 'me-resolved'));
        marketengine_update_message_meta($dispute->ID, '_case_winner', $winner );
        marketengine_update_message_meta($dispute->ID, '_case_arbitrate', sanitize_textarea_field( $case_data['arbitrate_content'] ));

        self::add_dispute_revision('me-resolved', $dispute);
        self::resolve_notify_seller($dispute, $winner);
        self::resolve_notify_buyer($dispute, $winner);

        marketengine_resolve_order($dispute->post_parent);
        return $dispute->ID;
    }

    public static function resolve_notify_buyer($dispute, $winner)
    {
        $subject     = sprintf(__("Resolved: The dispute on your transaction.", "enginethemes"));
        $transaction = marketengine_get_order($dispute->post_parent);

        $args = array(
            'display_name' => get_the_author_meta('display_name', $dispute->sender),
            'sender_name'  => 'Admin',
            'blogname'     => get_bloginfo('blogname'),
            'dispute_link' => marketengine_rc_dispute_link($dispute->ID),
            'order_link'   => $transaction->get_order_detail_url(),
            'order'        => $transaction,
            'order_id'     => $transaction->id,
        );

        if ($winner == $dispute->sender) {
            $args['winner'] = __("you", "enginethemes");
        } else {
            $args['winner'] = get_the_author_meta('display_name', $dispute->receiver);
        }

        ob_start();
        marketengine_get_template('resolution/emails/admin-resolve', $args);
        $resolve_dispute_mail_content = ob_get_clean();

        $buyer = get_userdata($dispute->sender);

        /**
         * Filter user resolve dispute email content
         *
         * @param String $resolve_dispute_mail_content
         * @param Object $dispute The dispute object
         *
         * @since 1.1
         */
        $resolve_dispute_mail_content = apply_filters('marketengine_resolve_to_buyer_mail_content', $resolve_dispute_mail_content, $dispute);
        return wp_mail($buyer->user_email, $subject, $resolve_dispute_mail_content);
    }

    public static function resolve_notify_seller($dispute, $winner)
    {
        $subject     = sprintf(__("Resolved: The dispute on your transaction.", "enginethemes"));
        $transaction = marketengine_get_order($dispute->post_parent);

        $args = array(
            'display_name' => get_the_author_meta('display_name', $dispute->receiver),
            'sender_name'  => 'Admin',
            'blogname'     => get_bloginfo('blogname'),
            'dispute_link' => marketengine_rc_dispute_link($dispute->ID),
            'order_link'   => $transaction->get_order_detail_url(),
            'order'        => $transaction,
            'order_id'     => $transaction->id,
        );

        if ($winner == $dispute->receiver) {
            $args['winnder'] = __("you", "enginethemes");
        } else {
            $args['winner'] = get_the_author_meta('display_name', $dispute->sender);
        }

        ob_start();
        marketengine_get_template('resolution/emails/admin-resolve', $args);
        $resolve_dispute_mail_content = ob_get_clean();

        $seller = get_userdata($dispute->receiver);
        /**
         * Filter user resolve dispute email content
         *
         * @param String $resolve_dispute_mail_content
         * @param Object $dispute The dispute object
         *
         * @since 1.1
         */
        $resolve_dispute_mail_content = apply_filters('marketengine_resolve_to_seller_mail_content', $resolve_dispute_mail_content, $dispute);
        return wp_mail($seller->user_email, $subject, $resolve_dispute_mail_content);
    }

    public static function add_dispute_revision($state, $dispute)
    {
        $sender = get_current_user_id();
        if ($sender == $dispute->receiver) {
            $receiver = $dispute->sender;
        } else {
            $receiver = $dispute->receiver;
        }

        $data = array(
            'post_parent'  => $dispute->ID,
            'post_type'    => 'revision',
            'sender'       => $sender,
            'receiver'     => $receiver,
            'post_status'  => $state,
            'post_title'   => 'Revision #' . $dispute->ID,
            'post_content' => 'Revision #' . $dispute->ID,
        );
        $revision = marketengine_insert_message($data);
    }
}
