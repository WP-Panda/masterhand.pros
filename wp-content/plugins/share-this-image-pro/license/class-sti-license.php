<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'STI_License' ) ) :

    /**
     * Class for pro plugin updates
     */
    class STI_License {

        /**
         * Plugin license config
         * @var string
         */
        private $conf;

        /**
         * Plugin license key
         * @var string
         */
        private $license_key;

        /**
         * Plugin updater object
         * @var object
         */
        public $updater;

        /**
         * Initialize a new instance of the WordPress license class
         * @param string $current_version
         * @param string $update_path
         * @param string $plugin_slug
         */
        function __construct( $current_version, $update_path, $plugin_slug ) {

            // Set the class public variables
            list ($t1, $t2) = explode('/', $plugin_slug);
            $slug = str_replace('.php', '', $t2);

            $this->conf = array(
                'current_version' => $current_version,
                'update_path'     => $update_path,
                'plugin_slug'     => $plugin_slug,
                'slug'            => $slug,
                'transient_name'  => str_replace( '-', '_', $slug ) . '_info'
            );

            $this->includes();
            $this->init();

            add_action( 'wp_ajax_wpunit-sti-ajax-actions', array( $this, 'ajax_actions' ) );

        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        private function includes() {
            include_once( 'class-sti-updates.php' );
            include_once( 'class-sti-license-page.php' );
        }

        /*
         * Init plugin classes
         */
        private function init() {
            $this->updater = new STI_Updater( $this->conf );

            if ( is_admin() ) {
                add_action( 'in_plugin_update_message-' . $this->conf['plugin_slug'], array( $this, 'modify_plugin_update_message' ), 10, 2 );
            }

        }

        /**
         * Displays an update message for plugin list screens.
         */
        function modify_plugin_update_message( $plugin_data, $response ) {

            if ( $this->get_license_key() ) return;

            echo '<br />' . sprintf( esc_html__('To enable updates, please enter your license key on the %s updates %s page. If you don\'t have a licence key, please visit  %s plugin page %s.', 'share-this-image'), '<a href="'.admin_url('admin.php?page=sti-options-updates').'">', '</a>', '<a href="https://share-this-image.com/" target="_blank">', '</a>' );

        }

        /**
         * License ajax actions
         *
         * @return string $response
         */
        public function ajax_actions() {

            $action_type = $_POST['type'];
            $response = '';

            if ( $action_type === 'verify-license' ) {

                $license_key = $_POST['license'];

                $license_response = $this->updater->get_remote_license( $license_key );

                if ( $license_response ) {
                    $response = 'valid';
                    $this->update_license_key( $license_key );
                    $this->remove_transient();
                } else {
                    $response = 'invalid';
                }

            }

            if ( $action_type === 'deactivate-license' ) {

                $this->remove_license_key();

                $this->remove_transient();

                $response = 'deactivated';

            }

            wp_send_json_success( $response );

        }

        /*
         * Get currently active license key
         */
        public function get_license_key() {
            $option_name = $this->get_license_option_name();
            return get_option( $option_name );
        }

        /*
         * Update currently active license key
         * @param string $license_key New license key
         */
        private function update_license_key( $license_key ) {
            $option_name = $this->get_license_option_name();
            update_option( $option_name, $license_key );
        }

        /*
         * Remove currently active license key
         */
        private function remove_license_key() {
            $option_name = $this->get_license_option_name();
            return delete_option( $option_name );
        }

        /*
         * Get option name for license key
         */
        public function get_license_option_name() {
            return trim( str_replace( '-', '_', $this->conf['slug'] ) );
        }

        /*
         * Remove plugin transient data
         */
        private function remove_transient() {
            if ( function_exists( 'wp_clean_plugins_cache' ) ) {
                wp_clean_plugins_cache();
            }
            delete_transient( $this->conf['transient_name'] );
        }

    }

endif;