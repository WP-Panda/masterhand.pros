<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ME_Conversation {
    public $id;

    public $sender;

    public $receiver;

    /**
     *
     */
    public function __construct() {

    }

    public function add_message() {

    }

    public function delete_message() {

    }
}

final class ME_Message {
    /**
     * Message ID.
     *
     * @var int
     */
    public $ID;

    /**
     * ID of sender.
     *
     * A numeric string, for compatibility reasons.
     *
     * @var string
     */
    public $sender = 0;

    /**
     * ID of receiver.
     *
     * A numeric string, for compatibility reasons.
     *
     * @var string
     */
    public $receiver = 0;

    /**
     * The message's local publication time.
     *
     * @var string
     */
    public $post_date = '0000-00-00 00:00:00';

    /**
     * The message's GMT publication time.
     *
     * @var string
     */
    public $post_date_gmt = '0000-00-00 00:00:00';

    /**
     * The message's content.
     *
     * @var string
     */
    public $post_content = '';

    /**
     * The message's title.
     *
     * @var string
     */
    public $post_title = '';

    /**
     * The message's excerpt.
     *
     * @var string
     */
    public $post_excerpt = '';

    /**
     * The message's status.
     *
     * @var string
     */
    public $post_status = 'sent';

    /**
     * The message's password in plain text.
     *
     * @var string
     */
    public $post_password = '';

    /**
     * The message's slug.
     *
     * @var string
     */
    public $post_name = '';

    /**
     * The message's local modified time.
     *
     * @var string
     */
    public $post_modified = '0000-00-00 00:00:00';

    /**
     * The message's GMT modified time.
     *
     * @var string
     */
    public $post_modified_gmt = '0000-00-00 00:00:00';

    /**
     * A utility DB field for message content.
     *
     *
     * @var string
     */
    public $post_content_filtered = '';

    /**
     * ID of a message's parent message.
     *
     * @var int
     */
    public $post_parent = 0;

    /**
     * The unique identifier for a message, not necessarily a URL, used as the feed GUID.
     *
     * @var string
     */
    public $guid = '';

    /**
     * The message's type, like post or page.
     *
     * @var string
     */
    public $post_type = 'post';

    /**
     * Stores the message object's sanitization level.
     *
     * Does not correspond to a DB field.
     *
     * @var string
     */
    public $filter;

    /**
     * Retrieve ME_Message instance.
     *
     * @static
     * @access public
     *
     * @global wpdb $wpdb WordPress database abstraction object.
     *
     * @param int $message_id Message ID.
     * @return ME_Message|false Message object, false otherwise.
     */
    public static function get_instance($message_id) {
        global $wpdb;

        $message_id = (int) $message_id;
        if (!$message_id) {
            return false;
        }

        $_message = wp_cache_get($message_id, 'messages');

        if (!$_message) {
            $message_table = $wpdb->prefix . 'marketengine_message_item';
            $_message = $wpdb->get_row($wpdb->prepare("SELECT * FROM $message_table WHERE ID = %d LIMIT 1", $message_id));

            if (!$_message) {
                return false;
            }

            $_message = sanitize_post($_message, 'raw');
            wp_cache_add($_message->ID, $_message, 'messages');
        } elseif (empty($_message->filter)) {
            $_message = sanitize_post($_message, 'raw');
        }

        return new ME_Message($_message);
    }

    /**
     * Constructor.
     *
     * @param WP_Post|object $post Post object.
     */
    public function __construct($post) {
        foreach (get_object_vars($post) as $key => $value) {
            $this->$key = $value;
        }

    }

    /**
     * Isset-er.
     *
     * @param string $key Property to check if set.
     * @return bool
     */
    public function __isset($key) {
        if ('ancestors' == $key) {
            return true;
        }

        return metadata_exists('post', $this->ID, $key);
    }

    /**
     * Getter.
     *
     * @param string $key Key to get.
     * @return mixed
     */
    public function __get($key) {
        // Rest of the values need filtering.
        if ('ancestors' == $key) {
            $value = get_post_ancestors($this);
        } else {
            $value = get_post_meta($this->ID, $key, true);
        }

        if ($this->filter) {
            $value = sanitize_post_field($key, $value, $this->ID, $this->filter);
        }

        return $value;
    }

    /**
     * {@Missing Summary}
     *
     * @param string $filter Filter.
     * @return self|array|bool|object|WP_Post
     */
    public function filter($filter) {
        if ($this->filter == $filter) {
            return $this;
        }

        if ($filter == 'raw') {
            return self::get_instance($this->ID);
        }

        return sanitize_post($this, $filter);
    }

    /**
     * Convert object to array.
     *
     * @return array Object as array.
     */
    public function to_array() {
        $post = get_object_vars($this);
        return $post;
    }
}