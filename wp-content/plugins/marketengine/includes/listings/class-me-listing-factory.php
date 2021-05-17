<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ME_Listing_Factory
{
    /**
     * The single instance of the class.
     *
     * @var ME_Listing_Factory
     * @since 1.0
     */
    protected static $_instance = null;

    /**
     * Main ME_Listing_Factory Instance.
     *
     * Ensures only one instance of ME_Listing_Factory is loaded or can be loaded.
     *
     * @since 1.0
     * @return ME_Listing_Factory - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * Get Me Listing
     *
     * Retrive ME Listing instan
     *
     * @since 1.0
     *
     * @param Int|WP_Post $the_listing
     * @param string $listing_type
     *
     * @return ME_Listing| false
     */
    public function get_listing($the_listing = false, $listing_type = '')
    {
        if (!($the_listing instanceof WP_Post)) {
            return false;
        }

        if($the_listing->post_type !== 'listing') {
            return false;
        }

        $listing_class = $this->get_listing_class($the_listing);
        if (!class_exists($listing_class)) {
            $listing_class = 'ME_Listing';
        }

        return new $listing_class($the_listing);
    }
    /**
     * Get Listing Class Name From Type
     *
     * Retrieve ME Listing Class Name base on Listing Type
     *
     * @since 1.0
     *
     * @param string $type the listing type name
     * @return string Class Name
     */
    public function get_listing_class_name_from_type($type)
    {
        return 'ME_Listing_' . ucfirst($type);
    }

    public function get_listing_class($the_listing, $type = '')
    {
        if (empty($type)) {
            $type = $this->get_listing_type($the_listing);
        }
        return $this->get_listing_class_name_from_type($type);
    }

    public function get_listing_type($the_listing)
    {
        $type = get_post_meta($the_listing->ID, '_me_listing_type', true);
        return apply_filters('marketengine_get_listing_type', $type, $the_listing);
    }
}
