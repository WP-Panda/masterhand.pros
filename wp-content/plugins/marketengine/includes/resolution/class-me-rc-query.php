<?php
/**
 * MarketEngine Resolution Center Query
 *
 * @author EngineThemes
 * @package  Includes/Resolution
 * @category Class
 */
class ME_RC_Query
{
    /**
     * The single instance of the class.
     *
     * @var ME_RC_Query
     * @since 1.0
     */
    protected static $_instance = null;

    /**
     * Main ME_RC_Query Instance.
     *
     * Ensures only one instance of ME_RC_Query is loaded or can be loaded.
     *
     * @since 1.0
     * @static
     * @return ME_RC_Query - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
    }
    /**
     * ME_RC_Query Class contructor
     *
     * Initialize hooks to filter query, add enpoint, rewrite rules
     *
     * @since 1.0
     */
    public function __construct()
    {
        add_action('init', array($this, 'add_enpoint'));
        add_filter('query_vars', array($this, 'add_query_vars'));

        add_action('init', array($this, 'rewrite_case_detail_url'));
        add_action('template_redirect', array($this, 'rewrite_templates'));
    }

    /**
     * Add plugin supported enpoint
     * @since 1.0
     */
    public function add_enpoint()
    {
        $option_value = marketengine_option('ep_resolution-center');
        if (!$option_value) {
            $option_value = 'resolution-center';
        }
        add_rewrite_endpoint($option_value, EP_ROOT | EP_PAGES, 'resolution-center');
        add_rewrite_rule('^(.?.+?)/' . $option_value . '/page/?([0-9]{1,})/?$', 'index.php?pagename=$matches[1]&paged=$matches[2]&' . $option_value, 'top');
    }

    /**
     * Add query order-id, keyword
     *
     * @param array $vars WP query var list
     * @since 1.0
     */
    public function add_query_vars($vars)
    {
        $vars[] = 'resolution-center';
        $vars[] = 'case_type';
        $vars[] = 'case_id';

        return $vars;
    }

    /**
     * Rewrite inquiry details url rule
     * @since 1.0
     */
    public function rewrite_case_detail_url()
    {
        $endpoint = trim(marketengine_option('ep_case'));
        $endpoint  = $endpoint ? $endpoint : 'case';
        add_rewrite_rule($endpoint . '/([0-9]+)/?$', 'index.php?case_type=dispute&case_id=$matches[1]', 'top');
    }

    public function rewrite_templates()
    {
        if (get_query_var('case_type')) {
            $current_user_id = get_current_user_id();
            $case_id         = absint( get_query_var('case_id') );
            $case            = marketengine_get_message($case_id);

            if (!$case) {
                return;
            }

            if (current_user_can('manage_options') || $current_user_id == $case->receiver || $current_user_id == $case->sender) {
                add_filter('template_include', array($this, 'include_dispute_template'));
                add_filter('document_title_parts', array($this, 'the_dispute_title'));
                add_filter('body_class', array($this, 'the_dispute_body_class'));
            }
        }
    }

    public function include_dispute_template($template)
    {
        global $wp_query;
        $wp_query->is_404 = 0;
        return ME()->plugin_path() . '/templates/resolution/me-single-case.php';

    }

    public function the_dispute_title($title)
    {
        $title['title'] = sprintf(__("Dispute case #%d", "enginethemes"), get_query_var('case_id'));
        $title['site']  = get_bloginfo('name', 'display');
        return $title;
    }

    public function the_dispute_body_class($classes)
    {
        $classes[] = 'dispute-case-' . absint( get_query_var('case_id') ) . ' single-dispute ';
        return $classes;
    }
}
