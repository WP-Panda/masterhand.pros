<?php
/**
 * Class ME Query
 *
 * Filter & sort the listing query, setup site enpoint, custom order post link
 *
 * @category Class
 * @package Includes/Query
 * @version 1.0
 */
class ME_Query
{
    /**
     * The single instance of the class.
     *
     * @var ME_Query
     * @since 1.0
     */
    protected static $_instance = null;

    /**
     * Main ME_Query Instance.
     *
     * Ensures only one instance of ME_Query is loaded or can be loaded.
     *
     * @since 1.0
     * @static
     * @return ME_Query - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * ME_Query Class contructor
     *
     * Initialize hooks to filter query, add enpoint, rewrite rules
     *
     * @since 1.0
     */
    public function __construct()
    {
        add_action('init', array($this, 'init_endpoint'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_filter('post_type_link', array($this, 'custom_order_link'), 1, 3);

        add_action('pre_get_posts', array($this, 'filter_pre_get_posts'));
    }

    /**
     * Load the enpoints name
     *
     * Retrieve the enpoint list, if the value is not set get the default
     *
     * @since 1.0
     * @return array of endpoints
     */
    private function load_endpoints_name()
    {
        $endpoint_arr = marketengine_default_endpoints();
        foreach ($endpoint_arr as $key => $value) {
            $option_value = marketengine_option('ep_' . $key);
            if (isset($option_value) && !empty($option_value) && $option_value != $value) {
                $endpoint_arr[$key] = $option_value;
            }
        }
        return $endpoint_arr;
    }

    /**
     * Hook to action init setup site enpoint
     * @since 1.0
     */
    public function init_endpoint()
    {
        $this->add_enpoint();

        $this->rewrite_payment_flow_url();

        $this->rewrite_user_account_url();

        $this->rewrite_edit_listing_url();

        $this->rewrite_order_detail_url();
    }

    /**
     * Add plugin supported enpoint
     * @since 1.0
     */
    private function add_enpoint()
    {
        $endpoint_arr = $this->load_endpoints_name();
        foreach ($endpoint_arr as $key => $value) {
            add_rewrite_endpoint($value, EP_ROOT | EP_PAGES, str_replace('_', '-', $key));
        }
    }

    /**
     * Rewrite page flow page url.
     * - confirm order page
     * - cancel order page
     * - checkout page
     *
     * @since 1.0
     */
    private function rewrite_payment_flow_url()
    {
        $rewrite_args = array(
            array(
                'page_id'       => marketengine_get_option_page_id('confirm_order'),
                'endpoint_name' => marketengine_get_endpoint_name('order-id'),
                'query_var'     => 'order-id',
            ),

            array(
                'page_id'       => marketengine_get_option_page_id('cancel_order'),
                'endpoint_name' => marketengine_get_endpoint_name('order-id'),
                'query_var'     => 'order-id',
            ),
            array(
                'page_id'       => marketengine_get_option_page_id('marketengine_checkout'),
                'endpoint_name' => marketengine_get_endpoint_name('pay'),
                'query_var'     => 'pay',
            ),
        );
        foreach ($rewrite_args as $key => $value) {
            if ($value['page_id'] > -1) {
                $page = get_post($value['page_id']);
                add_rewrite_rule('^/' . $page->post_name . '/' . $value['endpoint_name'] . '/([^/]*)/?', 'index.php?page_id=' . $value['page_id'] . '&' . $value['query_var'] . '=$matches[1]', 'top');
            }
        }
    }

    /**
     * Rewrite user account url rule
     * @since 1.0
     */
    private function rewrite_user_account_url()
    {
        $endpoints = array('orders', 'purchases', 'listings');
        foreach ($endpoints as $endpoint) {
            add_rewrite_rule('^(.?.+?)/' . marketengine_get_endpoint_name($endpoint) . '/page/?([0-9]{1,})/?$', 'index.php?pagename=$matches[1]&paged=$matches[2]&' . $endpoint, 'top');
        }
    }

    /**
     * Rewrite edit listing url rule
     * @since 1.0
     */
    private function rewrite_edit_listing_url()
    {
        $edit_listing_page = marketengine_get_option_page_id('edit_listing');
        if ($edit_listing_page > -1) {
            $page = get_post($edit_listing_page);
            add_rewrite_rule('^/' . $page->post_name . '/' . marketengine_get_endpoint_name('listing_id') . '/?([0-9]{1,})/?$', 'index.php?page_id=' . $edit_listing_page . '&listing_id' . '=$matches[1]', 'top');
        }
    }

    /**
     * Rewrite order details url rule
     * @since 1.0
     */
    private function rewrite_order_detail_url()
    {
        $order_endpoint = marketengine_get_endpoint_name('order_id');
        add_rewrite_rule($order_endpoint . '/([0-9]+)/?$', 'index.php?post_type=me_order&p=$matches[1]', 'top');
    }

    /**
     * Filters order detail url.
     *
     * @param       string $post_link The post link
     * @param       object $post The post object
     * @return      string $post_link Return filter order link if is order post 
     *                                else return normal post link
     *
     * @since       1.0.0
     * @version     1.0.0
     */
    public function custom_order_link($post_link, $post = 0)
    {
        if ($post->post_type == 'me_order') {
            if (get_option('permalink_structure')) {
                $pos       = strrpos($post_link, '%/');
                $post_link = substr($post_link, 0, $pos + 1);
            }
            return str_replace('%post_id%', $post->ID, $post_link);
        }
        return $post_link;
    }

    /**
     * Filter wordpress pre get posts to control listing query
     *  - only load published listing in archive listing page, author page
     *  - exclude listing from wordpress search page
     *  - filter listing by price, keyword, listing type
     *  - order listing by price asc | desc
     *
     * @param WP_Query $query The wordpress main wp_query object
     * @since 1.0.0
     */
    public function filter_pre_get_posts($query)
    {
        // Only affect the main query
        if (!$query->is_main_query()) {
            return;
        }

        if (is_archive('listing') && !is_admin()) {
            $query->set('post_status', 'publish');
        }

        if ($query->is_author()) {
            $query->set('post_type', 'listing');
            $query->set('post_status', 'publish');
        }

        global $wp_post_types;
        if (is_search()) {
            $wp_post_types['listing']->exclude_from_search = true;
        }

        return $this->filter_listing_query($query);

    }

    /**
     * Filter, sort listing
     *
     * @param WP_Query $query The wordpress main wp_query object
     * @since 1.0.0
     */
    public function filter_listing_query($query)
    {
        if (!$query->is_post_type_archive('listing') && !$query->is_tax(get_object_taxonomies('listing'))) {
            return $query;
        }

        $query = $this->sort_listing_query($query);
        $query = $this->filter_price_query($query);
        $query = $this->filter_listing_type_query($query);
        $query = $this->filter_search_query($query);
    }
    /**
     * Filter query listing by price
     * 
     * @param WP_Query $query The wordpress main wp_query object
     * @since 1.0
     */
    public function filter_price_query($query)
    {
        if (!empty($_GET['price-min']) && !empty($_GET['price-max'])) {
            $min_price                                       = esc_sql( $_GET['price-min'] );
            $max_price                                       = esc_sql( $_GET['price-max'] );
            $query->query_vars['meta_query']['filter_price'] = array(
                'key'     => 'listing_price',
                'value'   => array($min_price, $max_price),
                'type'    => 'numeric',
                'compare' => 'BETWEEN',
            );

            $query->query_vars['meta_query']['type'] = array(
                'key'     => '_me_listing_type',
                'value'   => 'purchasion',
                'compare' => '=',
            );

            $query->query_vars['meta_query']['relation'] = 'AND';

        }
        return $query;
    }

    /**
     * Filter query listing by listing type
     * 
     * @param WP_Query $query The wordpress main wp_query object
     * @since 1.0
     */
    public function filter_listing_type_query($query)
    {
        if (!empty($_GET['type'])) {
            $query->query_vars['meta_query']['filter_type'] = array(
                'key'     => '_me_listing_type',
                'value'   => esc_sql( $_GET['type'] ),
                'compare' => '=',
            );
        }
        return $query;
    }

    /**
     * Filter query listing by keyword
     * 
     * @param WP_Query $query The wordpress main wp_query object
     * @since 1.0
     */
    public function filter_search_query($query)
    {
        if (!empty($_GET['keyword'])) {
            $query->query_vars['s'] = esc_sql( $_GET['keyword'] );
        }
        return $query;
    }

    /**
     * Sort the listing
     * 
     * @param WP_Query $query The wordpress main wp_query object
     * @since 1.0
     */
    public function sort_listing_query($query)
    {
        if (empty($_GET['orderby'])) {
            return $query;
        }
        switch ($_GET['orderby']) {
            case 'date':
                $query->set('orderby', 'date');
                break;
            case 'price':
                $query = $this->sort_by_price($query, 'asc');
                break;
            case 'price-desc':
                $query = $this->sort_by_price($query, 'desc');
                break;
            case 'rating':
                $query->set('meta_key', '_me_rating');
                $query->set('orderby', 'meta_value_num');
                $query->set('order', 'desc');
        }
        return $query;
    }

    /**
     * Sort the listing by price
     * 
     * @param WP_Query $query The wordpress main wp_query object
     * @param string $asc sort asc or desc
     * @since 1.0
     */
    public function sort_by_price($query, $asc = 'asc')
    {
        $query->set('meta_key', 'listing_price');
        $meta_query = array(
            'relation'     => 'AND',
            'filter_price' => array(
                'key' => 'listing_price',
            ),
            'filter_type'  => array(
                'key'     => '_me_listing_type',
                'value'   => 'purchasion',
                'compare' => '=',
            ),
        );
        $query->set('meta_query', $meta_query);
        $query->set('orderby', 'meta_value_num');
        $query->set('order', $asc);
        return $query;
    }

    /**
     * Add query order-id, keyword
     * 
     * @param array $vars WP query var list
     * @since 1.0
     */
    public function add_query_vars($vars)
    {
        $vars[] = 'order-id';
        $vars[] = 'message_type';
        $vars[] = 'keyword';

        return $vars;
    }
}

ME_Query::instance();
