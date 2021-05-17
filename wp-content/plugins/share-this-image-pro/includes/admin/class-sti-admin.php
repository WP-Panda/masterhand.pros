<?php
/**
 * STI admin functions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'STI_PRO_Admin' ) ) :

    /**
     * Class for plugin search
     */
    class STI_PRO_Admin {

        /**
         * @var STI_PRO_Admin The single instance of the class
         */
        protected static $_instance = null;

        /**
         * Main STI_PRO_Admin Instance
         *
         * Ensures only one instance of STI_PRO_Admin is loaded or can be loaded.
         *
         * @static
         * @return STI_PRO_Admin - Main instance
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
                update_option( 'sti_pro_settings', $default_settings );
            }

            add_action( 'admin_notices', array( $this, 'admin_notices_local' ) );

        }

        /*
        * Register plugin settings
        */
        public function register_settings() {
            register_setting( 'sti_pro_settings', 'sti_pro_settings' );
        }

        /**
         * Add options page
         */
        public function add_admin_page() {
            add_menu_page( esc_html__( 'Share Image Options', 'share-this-image' ), esc_html__( 'Share Images', 'share-this-image' ), 'manage_options', 'sti-options', array( &$this, 'display_admin_page' ), 'dashicons-format-image' );
        }

        /**
         * Generate and display options page
         */
        public function display_admin_page() {

            $options = STI_Admin_Options::options_array();
            $nonce = wp_create_nonce( 'plugin-settings' );

            $tabs = array(
                'general' => esc_html__( 'General', 'share-this-image' ),
                'view'    => esc_html__( 'Styling', 'share-this-image' ),
                'content' => esc_html__( 'Content', 'share-this-image' ),
            );

            $current_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_text_field( $_GET['tab'] );

            $tabs_html = '';

            foreach ( $tabs as $name => $label ) {
                $tabs_html .= '<a href="' . admin_url( 'admin.php?page=sti-options&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
            }

            $tabs_html = '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">'.$tabs_html.'</h2>';
            

            if ( isset( $_POST["Submit"] ) && current_user_can( 'manage_options' ) && isset( $_POST["_wpnonce"] ) && wp_verify_nonce( $_POST["_wpnonce"], 'plugin-settings' ) ) {
                STI_Admin_Options::update_settings();
            }

            $sti_options = STI_Admin_Options::get_settings(); ?>


            <div class="wrap">

                <h1 class="sti-title"><?php esc_html_e( 'Share This Image', 'share-this-image' ); ?></h1>

                <?php echo $tabs_html; ?>

                <form action="" name="sti_form" id="sti_form" method="post">

                    <?php

                    switch ($current_tab) {
                        case('view'):
                            new STI_PRO_Admin_Fields( $options['view'], $sti_options );
                            break;
                        case('content'):
                            new STI_PRO_Admin_Fields( $options['content'], $sti_options );
                            break;
                        default:
                            new STI_PRO_Admin_Fields( $options['general'], $sti_options );
                    }

                    ?>

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
                wp_enqueue_style( 'sti-admin-style', STI_PRO_URL . '/assets/css/sti-admin.css', array(), STI_PRO_VER );
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'jquery-ui-sortable' );
                wp_enqueue_media();
                wp_enqueue_script( 'sti-admin-js', STI_PRO_URL . '/assets/js/admin.js', array('jquery'), STI_PRO_VER );
                wp_localize_script( 'sti-admin-js', 'sti_ajax_object', array(
                    'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ),
                ) );
            }
        }

        /*
         * Add admin notice
         */
        public function admin_notices_local() {

            if ( get_option( 'sti-pro-notice-dismiss-local-notice' ) ) {
                return;
            }

            if ( isset( $_GET['page'] ) && $_GET['page'] === 'sti-options' ) { ?>
                <div data-sti-notice="local-notice" class="notice notice-info is-dismissible">
                    <p><?php _e('<strong>Remember:</strong> Plugin won\'t scrap any data if you are using it on your <strong>local server</strong> or if your site disable <strong>search engine indexing</strong>.', 'share-this-image'); ?></p>

                    <p><?php esc_html_e('Please test STI plugin on publicly available site.', 'share-this-image'); ?></p>
                </div>
            <?php }

        }

    }


endif;

add_action( 'init', 'STI_PRO_Admin::instance' );