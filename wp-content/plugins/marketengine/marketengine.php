<?php
/*
Plugin Name: MarketEngine
Plugin URI: www.enginethemes.com
Description: A free WordPress plugin that allows you to build a multi vendor marketplace platform for any niche. Let anyone open a store to sell their products/services on your site in minutes, then earn commissions from each transaction happened in your ecommerce marketplace.
Version: 1.1
Author: EngineThemes team
Author URI: https://enginethemes.com
Domain Path: enginethemes
Tags: wordpress-plugin, ecommerce-marketplace, multi-vendors, shopping, selling, offering, store, payment-gateways, checkout, listings, seller, monetize money
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * MarketEngine Core Class
 *
 * @author      EngineThemes
 * @package     MarketEngine
 * @category    Classes
 *
 * @since       1.0.0
 * @version     1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('MarketEngine')) :

/**
 * Main MarketEngine Class
 *
 * Includes files, initialize and define all of MarketEngine parts.
 *
 * @package MarketEngine
 * @category Classes
 * @author EngineThemes
 */

class MarketEngine
{
    /**
     * The single instance of the class.
     *
     * @var MarketEngine
     * @since 1.0
     */
    protected static $_instance = null;

    /**
     * The string of plugin version.
     *
     * @var version
     * @since 1.0
     */
    public $version = '1.1';
    /**
     * The object of current user data
     *
     * @var current_user
     * @since 1.0
     */
    public $current_user;

    /**
     * The listing factory object
     * @var listing_factory
     * @since 1.0
     */
    public $listing_factory;
    /**
     * Main MarketEngine Instance.
     *
     * Ensures only one instance of MarketEngine is loaded or can be loaded.
     *
     * @since 1.0
     * @static
     * @see ME()
     * @return MarketEngine - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * MarketEngine Class contructor
     *
     * Defines constants, includes files, initialize hooks.
     *
     * @since 1.0
     */

    public function __construct()
    {
        // TODO: init alot of thing here
        $this->define();
        $this->include_files();
        $this->init_hooks();
        /**
         * Fires after the plugin is loaded.
         *
         * @since 1.0.0
         */

        $this->listing_factory = ME_Listing_Factory::instance();

        do_action('marketengine_loaded');
    }

    /**
     * Defines constants.
     *
     * Defines path of MarketEngine plugin.
     *
     * @since 1.0
     */

    private function define()
    {
        if (!defined('MARKETENGINE_PATH')) {
            define('MARKETENGINE_PATH', dirname(__FILE__));
        }

        if (!defined('MARKETENGINE_URL')) {
            define('MARKETENGINE_URL', plugin_dir_url(__FILE__));
        }
    }

    /**
     * Includes file in core
     *
     * @since 1.0
     */

    private function include_files()
    {
        require_once MARKETENGINE_PATH . '/includes/class-me-autoloader.php';

        require_once MARKETENGINE_PATH . '/admin/index.php';
        require_once MARKETENGINE_PATH . '/includes/custom-fields/index.php';

        require_once MARKETENGINE_PATH . '/includes/resolution/index.php';

        require_once MARKETENGINE_PATH . '/includes/class-me-install.php';
        require_once MARKETENGINE_PATH . '/includes/class-me-session.php';
        require_once MARKETENGINE_PATH . '/includes/class-me-validator.php';
        require_once MARKETENGINE_PATH . '/includes/class-me-post-types.php';
        require_once MARKETENGINE_PATH . '/includes/class-me-query.php';
        require_once MARKETENGINE_PATH . '/includes/class-me-template-loader.php';
        require_once MARKETENGINE_PATH . '/includes/class-me-order.php';
        require_once MARKETENGINE_PATH . '/includes/class-me-shipping.php';
        require_once MARKETENGINE_PATH . '/includes/class-me-options.php';

        require_once MARKETENGINE_PATH . '/includes/class-me-schedule.php';
        require_once MARKETENGINE_PATH . '/includes/users/class-me-user.php';
        require_once MARKETENGINE_PATH . '/includes/users/class-me-user-seller.php';

        require_once MARKETENGINE_PATH . '/includes/users/class-me-user-seller.php';

        require_once MARKETENGINE_PATH . '/includes/class-me-message-query.php';
        require_once MARKETENGINE_PATH . '/includes/class-me-conversation.php';
        require_once MARKETENGINE_PATH . '/includes/class-me-csv-export.php';

        require_once MARKETENGINE_PATH . '/includes/me-notices-functions.php';
        require_once MARKETENGINE_PATH . '/includes/me-template-functions.php';
        require_once MARKETENGINE_PATH . '/includes/me-email-functions.php';
        require_once MARKETENGINE_PATH . '/includes/me-report-functions.php';
        require_once MARKETENGINE_PATH . '/includes/me-listing-functions.php';
        require_once MARKETENGINE_PATH . '/includes/me-order-functions.php';
        require_once MARKETENGINE_PATH . '/includes/me-payment-functions.php';
        require_once MARKETENGINE_PATH . '/includes/me-cart-functions.php';
        require_once MARKETENGINE_PATH . '/includes/me-conversation-functions.php';
        require_once MARKETENGINE_PATH . '/includes/me-user-functions.php';
        require_once MARKETENGINE_PATH . '/includes/me-helper-functions.php';
        require_once MARKETENGINE_PATH . '/includes/me-widgets.php';

        require_once MARKETENGINE_PATH . '/includes/abstracts/class-abstract-form.php';

        require_once MARKETENGINE_PATH . '/includes/handle-options/class-me-options-handle.php';

        require_once MARKETENGINE_PATH . '/includes/handle-authentication/class-me-authentication-form.php';
        require_once MARKETENGINE_PATH . '/includes/handle-authentication/class-me-authentication.php';

        require_once MARKETENGINE_PATH . '/includes/handle-listings/class-me-listing-handle.php';
        require_once MARKETENGINE_PATH . '/includes/handle-listings/class-me-listing-status-handle.php';
        require_once MARKETENGINE_PATH . '/includes/handle-listings/class-me-listing-handle-form.php';

        require_once MARKETENGINE_PATH . '/includes/handle-upload/class-me-upload-handle.php';

        require_once MARKETENGINE_PATH . '/includes/listings/class-me-listing-factory.php';
        require_once MARKETENGINE_PATH . '/includes/listings/class-me-listing.php';
        require_once MARKETENGINE_PATH . '/includes/listings/class-me-listing-purchasion.php';
        require_once MARKETENGINE_PATH . '/includes/listings/class-me-listing-contact.php';

        require_once MARKETENGINE_PATH . '/includes/gateways/class-me-payment.php';
        require_once MARKETENGINE_PATH . '/includes/gateways/class-me-ppadaptive.php';

        require_once MARKETENGINE_PATH . '/includes/handle-checkout/class-me-checkout-handle.php';
        require_once MARKETENGINE_PATH . '/includes/handle-checkout/class-me-checkout-form.php';

        require_once MARKETENGINE_PATH . '/includes/handle-inquiry/class-me-inquiry-handle.php';
        require_once MARKETENGINE_PATH . '/includes/handle-inquiry/class-me-inquiry-form.php';

        require_once MARKETENGINE_PATH . '/includes/shortcodes/class-me-shortcodes-auth.php';
        require_once MARKETENGINE_PATH . '/includes/shortcodes/class-me-shortcodes-listing.php';
        require_once MARKETENGINE_PATH . '/includes/shortcodes/class-me-shortcodes-transaction.php';
    }

    /**
     * Add action hooks
     *
     * @since 1.0
     */

    private function init_hooks()
    {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'add_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'add_admin_scripts'));

        add_action('init', array($this, 'wpdb_table_fix'), 0);
    }

    /**
     * Initialize session, post type, taxonomies, navigation menu
     */
    public function init()
    {
        $this->session = ME_Session::instance();

        ME_Post_Types::register_post_type();
        ME_Post_Types::register_taxonomies();

        // ME_Auto_Update::get_instance( $this->version, 'update_path', plugin_basename(__FILE__) );

        register_nav_menu('category-menu', __('Category Menu', 'enginethemes'));
    }

    /**
     * Creates data tables for storing order and message data.
     */
    public function wpdb_table_fix()
    {
        global $wpdb;
        $wpdb->marketengine_order_itemmeta = $wpdb->prefix . 'marketengine_order_itemmeta';
        $wpdb->marketengine_order_items    = $wpdb->prefix . 'marketengine_order_items';

        $wpdb->marketengine_message_itemmeta = $wpdb->prefix . 'marketengine_message_itemmeta';
        $wpdb->marketengine_message_item     = $wpdb->prefix . 'marketengine_message_item';

        $wpdb->marketengine_custom_fields = $wpdb->prefix . 'marketengine_custom_fields';
        $wpdb->marketengine_fields_relationship = $wpdb->prefix . 'marketengine_fields_relationship';

        $wpdb->tables[] = 'marketengine_order_itemmeta';
        $wpdb->tables[] = 'marketengine_order_items';

        $wpdb->tables[] = 'marketengine_message_itemmeta';
        $wpdb->tables[] = 'marketengine_message_item';

        $wpdb->tables[] = 'marketengine_custom_fields';
    }

    /**
     * Add javascript for front-end
     */
    public function add_scripts()
    {
        $develop_src = false;
        // $develop_src = TRUE;

        if (!defined('MARKETENGINE_SCRIPT_DEBUG')) {
            define('MARKETENGINE_SCRIPT_DEBUG', $develop_src);
        }

        $suffix     = MARKETENGINE_SCRIPT_DEBUG ? '' : '.min';
        $dev_suffix = $develop_src ? '' : '.min';

        wp_enqueue_style('me_layout', $this->plugin_url() . '/assets/css/marketengine-layout.css');
        wp_enqueue_style('magnific_popup_css', $this->plugin_url() . '/assets/css/magnific-popup.css');
        wp_enqueue_style('me_font_icon', $this->plugin_url() . '/assets/css/marketengine-font-icon.css');

        wp_enqueue_script(array('jquery', 'plupload-all'));
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-slider');  
        wp_enqueue_script('jquery-ui-tooltip');
        wp_enqueue_script('jquery-ui-sortable'); 

        if(MARKETENGINE_SCRIPT_DEBUG) {
            wp_enqueue_script('muploader.js', $this->plugin_url() . "/assets/js/muploader$suffix.js", array('jquery', 'plupload-all'), $this->version, true);

            // lib
            wp_enqueue_script('magnific_popup', $this->plugin_url() . "/assets/js/jquery.magnific-popup.min.js", array('jquery'), $this->version, true);
            wp_enqueue_script('owl-carousel-js', $this->plugin_url() . "/assets/js/owl.carousel.min.js", array('jquery'), $this->version, true);
            wp_enqueue_script('raty.js', $this->plugin_url() . "/assets/js/jquery.raty$suffix.js", array('jquery'), $this->version, true);

            wp_enqueue_script('user_profile', $this->plugin_url() . "/assets/js/user-profile$suffix.js", array('jquery'), $this->version, true);
            wp_enqueue_script('tag_box', $this->plugin_url() . "/assets/js/tag-box$suffix.js", array('jquery', 'suggest'), $this->version, true);
            wp_enqueue_script('post_listing', $this->plugin_url() . "/assets/js/post-listing$suffix.js", array('jquery', 'tag_box'), $this->version, true);

            wp_enqueue_script('me.sliderthumbs', $this->plugin_url() . "/assets/js/me.sliderthumbs$suffix.js", array('jquery'), $this->version, true);
            wp_enqueue_script('script.js', $this->plugin_url() . "/assets/js/script$suffix.js", array('jquery'), $this->version, true);
            wp_enqueue_script('message.js', $this->plugin_url() . "/assets/js/message$suffix.js", array('jquery'), $this->version, true);
            wp_enqueue_script('index', $this->plugin_url() . "/assets/js/index$suffix.js", array('jquery', 'message.js'), $this->version, true);
            wp_enqueue_script('my-listings.js', $this->plugin_url() . "/assets/js/my-listings$suffix.js", array('jquery'), $this->version, true);
            wp_enqueue_script('listing-review', $this->plugin_url() . "/assets/js/listing-review$suffix.js", array('jquery'), $this->version, true);

            wp_enqueue_script( 'dispute', $this->plugin_url() . "/assets/js/dispute$suffix.js", array('jquery'), $this->version, true );

            wp_localize_script(
                'post_listing',
                'me_globals',
                array(
                    'ajaxurl'   => admin_url('admin-ajax.php'),
                    'limitFile' => __("Exceed number of allowed file upload. Max file upload is ", "enginethemes"),
                    'date_format' => get_option( 'date_format' ),
                )
            );

        } else {

            wp_enqueue_script('me-vendor', $this->plugin_url() . "/assets/js/me.vendor.js", array('jquery', 'plupload-all', 'suggest'), $this->version, true);
            
            wp_localize_script(
                'me-vendor',
                'me_globals',
                array(
                    'ajaxurl'   => admin_url('admin-ajax.php'),
                    'limitFile' => __("Exceed number of allowed file upload. Max file upload is ", "enginethemes"),
                    'date_format' => get_option( 'date_format' ),
                )
            );
        }


        $max_files   = apply_filters('marketengine_plupload_max_files', 2);
        $post_params = array(
            "_wpnonce" => wp_create_nonce('media-form'),
            "short"    => "1",
            "action"   => 'me-upload-file',
        );
        wp_localize_script(
            'plupload',
            'plupload_opt',
            array(
                'max_file_size'       => (wp_max_upload_size() / (1024 * 1024)) . 'mb',
                'url'                 => admin_url('admin-ajax.php'),
                'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
                'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
                'max_files'           => $max_files,
                'filters'             => array(
                    array(
                        'title'      => __('Image Files', "enginethemes"),
                        'extensions' => 'jpg,jpeg,gif,png',
                    ),
                ),
                'runtimes'            => 'html5,gears,flash,silverlight,browserplus,html4',
                'multipart_params'    => $post_params,
            )
        );
    }

    /**
     * Add javascript for admin area
     */
    public function add_admin_scripts()
    {
        $develop_src = true;

        if (!defined('MARKETENGINE_SCRIPT_DEBUG')) {
            define('MARKETENGINE_SCRIPT_DEBUG', $develop_src);
        }

        $suffix     = MARKETENGINE_SCRIPT_DEBUG ? '' : '.min';
        $dev_suffix = $develop_src ? '' : '.min';
        wp_enqueue_style('me-option-css', $this->plugin_url() . '/assets/admin/menu.css');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-slider');  
        wp_enqueue_script('jquery-ui-tooltip');
        wp_enqueue_script('jquery-ui-sortable'); 
        wp_enqueue_script('jquery-validation', $this->plugin_url() . "/assets/js/jquery.validate.min.js", array('jquery'), $this->version, true);
    }

    /**
     * Get the plugin url.
     * @return string
     */
    public function plugin_url()
    {
        return untrailingslashit(plugins_url('/', __FILE__));
    }

    /**
     * Get the plugin path.
     * @return string
     */
    public function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(__FILE__));
    }

    /**
     * Get the template path.
     * @return string
     */
    public function template_path()
    {
        return apply_filters('marketengine_template_path', 'marketengine/');
    }

    /**
     *  Gets the current user information.
     *  @return object
     */
    public function get_current_user()
    {
        global $current_user;
        if (null === $this->current_user && $current_user) {
            $this->current_user = new ME_User($current_user);
        }
        return $this->current_user;
    }

}
endif;
/**
 * Main MarketEngine Instance.
 */
function ME()
{
    return MarketEngine::instance();
}

$GLOBALS['marketengine'] = ME();
