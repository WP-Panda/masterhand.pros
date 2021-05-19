<?php
/**
 * STI admin functions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'STI_Admin' ) ) :

    /**
     * Class for plugin search
     */
    class STI_Admin {

        /**
         * @var STI_Admin The single instance of the class
         */
        protected static $_instance = null;

        /**
         * Main STI_Admin Instance
         *
         * Ensures only one instance of STI_Admin is loaded or can be loaded.
         *
         * @static
         * @return STI_Admin - Main instance
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /*
         * Constructor
         */
        public function __construct() {

            add_action( 'admin_init', array( &$this, 'register_settings' ) );
            add_action( 'admin_menu', array( &$this, 'add_admin_page' ) );
            add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );

            if ( ! STI_Admin_Options::get_settings() ) {
                $default_settings = STI_Admin_Options::get_default_settings();
                update_option( 'sti_settings', $default_settings );
            }

            add_action( 'admin_notices', array( $this, 'admin_notices_local' ) );

            add_filter( 'submenu_file', array( $this, 'submenu_file' ), 10, 2 );

            add_action( 'admin_notices', array( $this, 'display_welcome_header' ), 1 );

        }

        /*
        * Register plugin settings
        */
        public function register_settings() {
            register_setting( 'sti_settings', 'sti_settings' );
        }

        /*
         * Get plugin settings
         */
        public function get_settings() {
            $plugin_options = get_option( 'sti_settings' );
            return $plugin_options;
        }

        /**
         * Add options page
         */
        public function add_admin_page() {
            add_menu_page( esc_html__( 'Share Image Options', 'share-this-image' ), esc_html__( 'Share Images', 'share-this-image' ), 'manage_options', 'sti-options', false, 'dashicons-format-image' );
            add_submenu_page( 'sti-options', __( 'Settings', 'share-this-image' ), __( 'Settings', 'share-this-image' ), 'manage_options', 'sti-options', array( $this, 'display_admin_page' ) );
            add_submenu_page( 'sti-options', __( 'Pro Features', 'share-this-image' ),  __( 'Pro Features', 'share-this-image' ), 'manage_options', admin_url( 'admin.php?page=sti-options&tab=premium' ) );
        }

        /**
         * Generate and display options page
         */
        public function display_admin_page() {

            $options = STI_Admin_Options::options_array();
            $nonce = wp_create_nonce( 'plugin-settings' );

            $tabs = array(
                'general' => __( 'General', 'share-this-image' ),
                'content' => __( 'Content', 'share-this-image' ),
                'premium' => __( 'Premium Version', 'share-this-image' ),
            );

            $current_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_text_field( $_GET['tab'] );

            $tabs_html = '';

            foreach ( $tabs as $name => $label ) {
                $tabs_html .= '<a href="' . admin_url( 'admin.php?page=sti-options&tab=' . $name ) . '" class="nav-tab '.$name.'-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . esc_html( $label ) . '</a>';
            }

            $tabs_html = '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">'.$tabs_html.'</h2>';
            

            if ( isset( $_POST["Submit"] ) && current_user_can( 'manage_options' ) && isset( $_POST["_wpnonce"] ) && wp_verify_nonce( $_POST["_wpnonce"], 'plugin-settings' ) ) {
                STI_Admin_Options::update_settings();
            }

            $sti_options = STI_Admin_Options::get_settings(); ?>


            <div class="wrap">

                <h1 class="sti-title"><?php esc_html_e( 'Share This Image', 'share-this-image' ); ?></h1>

                <?php echo $tabs_html; ?>

                <form action="" name="sti_form" id="sti_form" class="sti_form form-tab-<?php echo $current_tab; ?>" method="post">

                    <?php

                    switch ($current_tab) {
                        case('content'):
                            new STI_Admin_Fields( $options['content'], $sti_options );
                            break;
                        case('premium'):
                            new STI_Admin_Page_Premium();
                            break;
                        default:
                            new STI_Admin_Fields( $options['general'], $sti_options );
                    }

                    ?>

                    <?php //new STI_Admin_Fields( $options, $sti_options ); ?>

                    <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>">

                    <p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'share-this-image' ); ?>" /></p>

                </form>

            </div>

        <?php }

        /**
         * Enqueue admin scripts and styles
         */
        public function admin_enqueue_scripts() {
            if ( isset( $_GET['page'] ) && $_GET['page'] == 'sti-options' ) {
                wp_enqueue_style( 'sti-admin-style', STI_URL . '/assets/css/sti-admin.css', array(), STI_VER );
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'jquery-ui-sortable' );
                wp_enqueue_media();
                wp_enqueue_script( 'sti-admin-js', STI_URL . '/assets/js/admin.js', array('jquery'), STI_VER );
                wp_localize_script( 'sti-admin-js', 'sti_ajax_object', array(
                    'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ),
                ) );
            }
        }

        /*
         * Add admin notice
         */
        public function admin_notices_local() {

            if ( get_option( 'sti-notice-dismiss-local-notice' ) ) {
                return;
            }

            if ( isset( $_GET['page'] ) && $_GET['page'] === 'sti-options' ) { ?>
                <div data-sti-notice="local-notice" class="notice notice-info is-dismissible">
                    <p><?php _e('<strong>Remember:</strong> Plugin won\'t scrap any data if you are using it on your <strong>local server</strong> or if your site disable <strong>search engine indexing</strong>.', 'share-this-image'); ?></p>

                    <p><?php _e('Please test STI plugin on publicly available site.', 'share-this-image'); ?></p>
                </div>
            <?php }

        }

        /*
         * Change current class for premium tab
         */
        public function submenu_file( $submenu_file, $parent_file ) {
            if ( $parent_file === 'sti-options' && isset( $_GET['tab'] ) && $_GET['tab'] === 'premium' ) {
                $submenu_file = admin_url( 'admin.php?page=sti-options&tab=premium' );
            }
            return $submenu_file;
        }

        /*
         * Add welcome notice
         */
        public function display_welcome_header() {

            if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'sti-options' ) {
                return;
            }

            $hide_notice = get_option( 'sti_hide_welcome_notice' );

            if ( ! $hide_notice || $hide_notice === 'true' ) {
                return;
            }

            echo STI_Admin_Meta_Boxes::get_welcome_notice();

        }

    }


endif;

add_action( 'init', 'STI_Admin::instance' );