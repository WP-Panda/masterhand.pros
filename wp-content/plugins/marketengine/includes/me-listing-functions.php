<?php
/**
 * Related to Listing Functions
 * @package Listing
 * @category Function
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get the listing object from wordpress post
 * @param object|int $post The wp_post id or object
 *
 * @package Includes/Listing
 * @category Function
 *
 * @since 1.0
 * 
 * @return ME_Listing | null Return ME_Listing object if post->post_type is listing, if not return null
 */
function marketengine_get_listing($post = null) {
    if (null === $post) {
        global $post;
    }

    if (is_numeric($post)) {
        $post = get_post($post);
    }

    return ME_Listing_Factory::instance()->get_listing($post);
}
/**
 * Retrieve supported listing types
 * 
 * @package Includes/Listing
 * @category Function
 * 
 * @since 1.0
 * @return array Array of listing type
 */
function marketengine_get_listing_types() {
    $purchasion_title = marketengine_option('purchasion-title');
    $contact_title = marketengine_option('contact-title');
    $listing_types = array(
        'purchasion' => $purchasion_title ? $purchasion_title : __("Selling", "enginethemes"),
        'contact'    => $contact_title ? $contact_title : __("Offering", "enginethemes"),
    );
    return apply_filters('marketengine_get_listing_types', $listing_types);
}

/**
 * Retrieve listing type label
 * @param string The listing type keyword
 *
 * @package Includes/Listing
 * @category Function
 * 
 * @since 1.0
 * @return string
 */
function marketengine_get_listing_type_label($type) {
    $types = marketengine_get_listing_types();
    return $types[$type];
}

/**
 * MarketEngine Get Listing Type Categories
 * 
 * Retrieve the categories list supported in each listing type
 *
 * @package Includes/Listing
 * @category Function
 * 
 * @since 1.0
 * @return array Array of category id the listing type support
 */
function marketengine_get_listing_type_categories() {
    $purchase_cats = marketengine_option('purchasion-available', array());
    $contact_cats  = marketengine_option('contact-available', array());
    $categories = array(
        'contact' => empty($contact_cats) ? array() : (array)$contact_cats,
        'purchasion' => empty($purchase_cats) ? array() : (array)$purchase_cats
    );
    $categories['all'] = array_merge($categories['contact'], $categories['purchasion']);
    /**
     * Filter MarketEngine Support Listing Type Categories List
     * @param array $categories
     * @since 1.0.1
     */
    return apply_filters('marketengine_listing_type_categories', $categories);
}

/**
 * Function me is listing type available
 * Check the listing type is available in a category
 * 
 * @param string $listing_type The listing type name
 * @param int $cat The category id. If the cat is not set, get the $_POST['parent_cat']
 *
 * @return bool
 */
function marketengine_is_listing_type_available($listing_type, $cat = 0) {
    if(!$cat && !empty($_POST['parent_cat'])) {
        $cat = absint( $_POST['parent_cat'] );
    }
    if($cat == '') return true;

    $categories = marketengine_get_listing_type_categories();
    return in_array($cat, $categories[$listing_type]);
}

/**
 * MarketEngine Get Listing Status List
 *
 * Retrieve marketengine listing status list
 *
 * @package Includes/Listing
 * @category Function
 * 
 * @since 1.0
 * @return array
 */
function marketengine_listings_status_list() {
    $listing_status = array(
        'publish'     => __("Published", "enginethemes"),
        // 'me-pending'  => __("Pending", "enginethemes"),
        'me-archived' => __("Archived", "enginethemes"),
        // 'draft'       => __("Draft", "enginethemes"),
        // 'me-paused'   => __("Paused", "enginethemes"),
    );
    return apply_filters('marketengine_listing_status_list', $listing_status);
}

/**
 * MarketEngine get rating score user had rated for listing
 *
 * @package Includes/Listing
 * @category Function
 * 
 * @since 1.0
 * @return int The rating score
 */
function marketengine_get_user_rate_listing_score($listing_id, $user_id) {
    $args = array(
        'post_id'        => $listing_id,
        'type'           => 'review',
        'number'         => 1,
        'comment_parent' => 0,
    );

    if($user_id) {
        $args['user_id'] = $user_id;
    }

    $comments = get_comments($args);

    if(!empty($comments)) {
        return get_comment_meta( $comments[0]->comment_ID, '_me_rating_score', true );
    }
    return 0;
}

/**
 * MarketEngine get listing categories
 *
 * @param array $args {
 *     Optional. Array or string of arguments to get terms.
 *
 *     @type string|array $taxonomy               Taxonomy name, or array of taxonomies, to which results should
 *                                                be limited.
 *     @type string       $orderby                Field(s) to order terms by. Accepts term fields ('name', 'slug',
 *                                                'term_group', 'term_id', 'id', 'description'), 'count' for term
 *                                                taxonomy count, 'include' to match the 'order' of the $include param,
 *                                                'meta_value', 'meta_value_num', the value of `$meta_key`, the array
 *                                                keys of `$meta_query`, or 'none' to omit the ORDER BY clause.
 *                                                Defaults to 'name'.
 *     @type string       $order                  Whether to order terms in ascending or descending order.
 *                                                Accepts 'ASC' (ascending) or 'DESC' (descending).
 *                                                Default 'ASC'.
 *     @type bool|int     $hide_empty             Whether to hide terms not assigned to any posts. Accepts
 *                                                1|true or 0|false. Default 1|true.
 *     @type array|string $include                Array or comma/space-separated string of term ids to include.
 *                                                Default empty array.
 *     @type array|string $exclude                Array or comma/space-separated string of term ids to exclude.
 *                                                If $include is non-empty, $exclude is ignored.
 *                                                Default empty array.
 *     @type array|string $exclude_tree           Array or comma/space-separated string of term ids to exclude
 *                                                along with all of their descendant terms. If $include is
 *                                                non-empty, $exclude_tree is ignored. Default empty array.
 *     @type int|string   $number                 Maximum number of terms to return. Accepts ''|0 (all) or any
 *                                                positive number. Default ''|0 (all).
 *     @type int          $offset                 The number by which to offset the terms query. Default empty.
 *     @type string       $fields                 Term fields to query for. Accepts 'all' (returns an array of complete
 *                                                term objects), 'ids' (returns an array of ids), 'id=>parent' (returns
 *                                                an associative array with ids as keys, parent term IDs as values),
 *                                                'names' (returns an array of term names), 'count' (returns the number
 *                                                of matching terms), 'id=>name' (returns an associative array with ids
 *                                                as keys, term names as values), or 'id=>slug' (returns an associative
 *                                                array with ids as keys, term slugs as values). Default 'all'.
 *     @type string|array $name                   Optional. Name or array of names to return term(s) for. Default empty.
 *     @type string|array $slug                   Optional. Slug or array of slugs to return term(s) for. Default empty.
 *     @type bool         $hierarchical           Whether to include terms that have non-empty descendants (even
 *                                                if $hide_empty is set to true). Default true.
 *     @type string       $search                 Search criteria to match terms. Will be SQL-formatted with
 *                                                wildcards before and after. Default empty.
 *     @type string       $name__like             Retrieve terms with criteria by which a term is LIKE $name__like.
 *                                                Default empty.
 *     @type string       $description__like      Retrieve terms where the description is LIKE $description__like.
 *                                                Default empty.
 *     @type bool         $pad_counts             Whether to pad the quantity of a term's children in the quantity
 *                                                of each term's "count" object variable. Default false.
 *     @type string       $get                    Whether to return terms regardless of ancestry or whether the terms
 *                                                are empty. Accepts 'all' or empty (disabled). Default empty.
 *     @type int          $child_of               Term ID to retrieve child terms of. If multiple taxonomies
 *                                                are passed, $child_of is ignored. Default 0.
 *     @type int|string   $parent                 Parent term ID to retrieve direct-child terms of. Default empty.
 *     @type bool         $childless              True to limit results to terms that have no children. This parameter
 *                                                has no effect on non-hierarchical taxonomies. Default false.
 *     @type string       $cache_domain           Unique cache key to be produced when this query is stored in an
 *                                                object cache. Default is 'core'.
 *     @type bool         $update_term_meta_cache Whether to prime meta caches for matched terms. Default true.
 *     @type array        $meta_query             Meta query clauses to limit retrieved terms by.
 *                                                See `WP_Meta_Query`. Default empty.
 *     @type string       $meta_key               Limit terms to those matching a specific metadata key. Can be used in
 *                                                conjunction with `$meta_value`.
 *     @type string       $meta_value             Limit terms to those matching a specific metadata value. Usually used
 *                                                in conjunction with `$meta_key`.
 * }
 * @see get_terms
 * @package Includes/Listing
 * @category Function
 * 
 * @since 1.0
 * @return array ({$term_id} => {$name})
 */
function marketengine_get_listing_categories($args = array('parent' => 0 , 'hide_empty' => false))
{
    $result   = array();
    $termlist = get_terms('listing_category', $args );

    foreach ($termlist as $term) {
        $result[$term->term_id] =  $term->name;
    }

    return $result;
}

function marketengine_filter_order_count_result( $order_count ) {
    $temp = array();
    foreach( $order_count as $key => $value) {
        $temp[$value->status] = $value->count;
    }

    return $temp;
}