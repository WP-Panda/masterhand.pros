<?php
class ME_Shortcodes_Listing
{
    public static function init_shortcodes()
    {
        add_shortcode('me_post_listing_form', array(__CLASS__, 'post_listing_form'));
        add_shortcode('me_edit_listing_form', array(__CLASS__, 'edit_listing_form'));
        add_shortcode('me_listings', array(__CLASS__, 'the_listing'));
    }
    public static function post_listing_form()
    {
        ob_start();
        marketengine_get_template('post-listing/post-listing');
        $content = ob_get_clean();
        return $content;
    }

    public static function edit_listing_form()
    {
        $user_id = get_current_user_id();

        if (!$user_id) {
            return ME_Shortcodes_Auth::marketengine_login_form();
        }

        ob_start();
        $listing_id = absint(get_query_var('listing-id'));
        $listing    = marketengine_get_listing($listing_id);
        if ($listing) {
            $seller = $listing->get_author() == get_current_user_id();

            if (!$seller) {
                return load_template(get_404_template());
            }

            marketengine_get_template('post-listing/edit-listing', array('listing' => $listing));
            $content = ob_get_clean();
            return $content;
        } else {
            return load_template(get_404_template());
        }
    }

    public static function the_listing()
    {
        ob_start();
        query_posts(array('post_type' => 'listing', 'post_status' => 'publish', 'paged' => get_query_var('page')));
        marketengine_get_template('listing-list');
        $content = ob_get_clean();
        wp_reset_query();
        return $content;
    }

}
ME_Shortcodes_Listing::init_shortcodes();
