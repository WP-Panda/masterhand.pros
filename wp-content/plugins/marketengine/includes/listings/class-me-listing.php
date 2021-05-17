<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ME_Listing
{
    /**
     * Post ID.
     *
     * @var int
     */
    public $ID;

    /**
     * ID of post author.
     *
     * A numeric string, for compatibility reasons.
     *
     * @var string
     */
    public $post_author = 0;

    /**
     * The post's local publication time.
     *
     * @var string
     */
    public $post_date = '0000-00-00 00:00:00';

    /**
     * The post's GMT publication time.
     *
     * @var string
     */
    public $post_date_gmt = '0000-00-00 00:00:00';

    /**
     * The post's content.
     *
     * @var string
     */
    public $post_content = '';

    /**
     * The post's title.
     *
     * @var string
     */
    public $post_title = '';

    /**
     * The post's excerpt.
     *
     * @var string
     */
    public $post_excerpt = '';

    /**
     * The post's status.
     *
     * @var string
     */
    public $post_status = 'publish';

    /**
     * Whether comments are allowed.
     *
     * @var string
     */
    public $comment_status = 'open';

    /**
     * Whether pings are allowed.
     *
     * @var string
     */
    public $ping_status = 'open';

    /**
     * The post's password in plain text.
     *
     * @var string
     */
    public $post_password = '';

    /**
     * The post's slug.
     *
     * @var string
     */
    public $post_name = '';

    /**
     * URLs queued to be pinged.
     *
     * @var string
     */
    public $to_ping = '';

    /**
     * URLs that have been pinged.
     *
     * @var string
     */
    public $pinged = '';

    /**
     * The post's local modified time.
     *
     * @var string
     */
    public $post_modified = '0000-00-00 00:00:00';

    /**
     * The post's GMT modified time.
     *
     * @var string
     */
    public $post_modified_gmt = '0000-00-00 00:00:00';

    /**
     * A utility DB field for post content.
     *
     *
     * @var string
     */
    public $post_content_filtered = '';

    /**
     * ID of a post's parent post.
     *
     * @var int
     */
    public $post_parent = 0;

    /**
     * The unique identifier for a post, not necessarily a URL, used as the feed GUID.
     *
     * @var string
     */
    public $guid = '';

    /**
     * A field used for ordering posts.
     *
     * @var int
     */
    public $menu_order = 0;

    /**
     * The post's type, like post or page.
     *
     * @var string
     */
    public $post_type = 'post';

    /**
     * An attachment's mime type.
     *
     * @var string
     */
    public $post_mime_type = '';

    /**
     * Cached comment count.
     *
     * A numeric string, for compatibility reasons.
     *
     * @var string
     */
    public $comment_count = 0;

    /**
     * Stores the post object's sanitization level.
     *
     * Does not correspond to a DB field.
     *
     * @var string
     */
    public $filter;

    /**
     * Retrieve ME_listing instance.
     *
     * @static
     * @access public
     *
     * @global wpdb $wpdb WordPress database abstraction object.
     *
     * @param int $listing_id Listing ID.
     * @return ME_Listing|false Listing object, false otherwise.
     */
    public static function get_instance($listing_id)
    {
        global $wpdb;

        $listing_id = (int) $listing_id;
        if (!$listing_id) {
            return false;
        }

        $_listing = wp_cache_get($listing_id, 'posts');

        if (!$_listing) {
            $_listing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID = %d LIMIT 1", $listing_id));

            if (!$_listing) {
                return false;
            }

            $_listing = sanitize_post($_listing, 'raw');
            wp_cache_add($_listing->ID, $_listing, 'posts');
        } elseif (empty($_listing->filter)) {
            $_listing = sanitize_post($_listing, 'raw');
        }

        return new ME_Listing($_listing);
    }

    /**
     * Constructor.
     *
     * @param ME_Listing|object $post Post object.
     */
    public function __construct($post)
    {
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
    public function __isset($key)
    {
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
    public function __get($key)
    {
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
    public function filter($filter)
    {
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
    public function to_array()
    {
        $post = get_object_vars($this);
        return $post;
    }

    public function get_id()
    {
        return $this->ID;
    }

    public function get_permalink($leavename = false, $alternate_link = '#')
    {
        if ($this->get_author() == get_current_user_id() || $this->is_available()) {
            return get_the_permalink($this->ID, $leavename);
        } else {
            return $alternate_link;
        }

    }

    public function get_author()
    {
        return $this->post_author;
    }

    public function get_title()
    {
        return get_the_title($this->ID);
    }

    public function get_description()
    {
        return $this->post_content;
    }

    public function get_short_description($length = 40)
    {
        $content = get_post_field('post_content', $this->ID, 'display');
        $content = apply_filters('the_content', $content);
        return marketengine_trim_words($content, $length);
    }

    public function get_listing_thumbnail($size = 'post-thumbnail', $attr = '')
    {
        return get_the_post_thumbnail($this->ID, $size, $attr);
    }

    public function get_listing_type()
    {
        return get_post_meta($this->ID, '_me_listing_type', true);
    }

    /**
     * Retrieve the number of listing's reviews
     *
     * @since 1.0
     * @return int
     */
    public function get_review_count()
    {
        return absint(get_post_meta($this->ID, '_me_reviews_count', true));
    }

    /**
     * Retrieve the details of listing's reviews
     *
     * @since 1.0
     * @return int
     */
    public function get_review_count_details()
    {
        $details = get_post_meta($this->ID, '_me_review_count_details', true);
        return wp_parse_args($details, array('1_star' => 0, '2_star' => 0, '3_star' => 0, '4_star' => 0, '5_star' => 0));
    }

    /**
     * Retrieve the listing rating score
     *
     * @since 1.0
     * @return float
     */
    public function get_review_score()
    {
        return get_post_meta($this->ID, '_rating_score', true);
    }

    /**
     * Retrieve the number of product's orders
     *
     * @since 1.0
     * @return int
     */
    public function get_order_count()
    {
        ME_Listing_Handle::update_order_count($this->ID);
        return absint(get_post_meta($this->ID, '_me_order_count', true));
    }

    public function get_inquiry_count()
    {
        ME_Listing_Handle::update_inquiry_count($this->ID);
        return absint(get_post_meta($this->ID, '_me_inquiry_count', true));
    }

    public function get_gallery()
    {
        $gallery = get_post_meta($this->ID, '_me_listing_gallery', true);
        return $gallery;
    }

    public function get_featured_image()
    {
        return get_post_meta($this->ID, '_thumbnail_id', true);
    }

    /**
     * Retrieve listing galleries
     *
     * @since 1.0
     * @return array
     */
    public function get_galleries()
    {
        $gallery      = get_post_meta($this->ID, '_me_listing_gallery', true);
        $thumbnail_id = get_post_meta($this->ID, '_thumbnail_id', true);

        if (empty($gallery)) {
            $gallery = array();
        }

        if ($thumbnail_id && !in_array($thumbnail_id, $gallery)) {
            array_unshift($gallery, $thumbnail_id);
        }

        return $gallery;
    }

    /**
     * Make sure the listing is available for sale
     *
     * @since 1.0
     * @return bool
     */
    public function is_available()
    {
        $is_available = ('listing' == $this->post_type && $this->post_status == 'publish');
        return apply_filters('marketengine_lisitng_is_available', $is_available, $this->ID);
    }

    public function get_edit_url()
    {
        $page = marketengine_get_page_permalink('user_account');
        $edit = marketengine_get_endpoint_name('edit-listing');
        return $page . $edit . '/' . $this->id;
    }

    /**
     * Check listing is allowed rating or not
     *
     * @since 1.0
     *
     * @return bool
     */
    public function allow_rating()
    {
        return 'contact' != $this->get_listing_type();
    }
}
