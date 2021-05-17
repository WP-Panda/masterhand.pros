<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'STI_Updater' ) ) :

    /**
     * Class for pro plugin updates
     */
    class STI_Updater {

        /**
         * The plugin current version
         * @var string
         */
        public $current_version;

        /**
         * The plugin remote update path
         * @var string
         */
        public $update_path;

        /**
         * Plugin Slug (plugin_directory/plugin_file.php)
         * @var string
         */
        public $plugin_slug;

        /**
         * Plugin name (plugin_file)
         * @var string
         */
        public $slug;

        /**
         * Name for transient info value
         * @var string
         */
        public $transient_name_info;

        /**
         * Initialize a new instance of the WordPress Auto-Update class
         * @param array $conf Config
         */
        function __construct( $conf ) {

            // Set the class public variables
            $this->current_version     = $conf['current_version'];
            $this->update_path         = $conf['update_path'];
            $this->plugin_slug         = $conf['plugin_slug'];
            $this->slug                = $conf['slug'];
            $this->transient_name_info = $conf['transient_name'];

            add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

            add_filter( 'plugins_api', array( $this, 'check_info' ), 10, 3 );

        }

        /**
         * Add our self-hosted autoupdate plugin to the filter transient
         *
         * @param $transient
         * @return object $ transient
         */
        public function check_update( $transient ) {

            if ( empty( $transient->checked ) ) {
                return $transient;
            }

            // Get the remote version
            $remote_version = $this->getRemote_version();

            // If a newer version is available, add the update
            if ( version_compare( $this->current_version, $remote_version, '<' ) ) {

                // Get the remote information
                $information = $this->getRemote_information();

                if ( ! $information ) {
                    return $transient;
                }

                delete_transient( $this->transient_name_info );

                $obj = new stdClass();
                $obj->slug = $this->slug;
                $obj->new_version = $remote_version;
                $obj->url = $information->homepage;

                if ( $information->download_url ) {
                    $obj->package = $information->download_url;
                }

                $transient->response[$this->plugin_slug] = $obj;

            }

            return $transient;

        }

        /**
         * Add self-hosted description to the filter
         *
         * @param boolean $false
         * @param array $action
         * @param object $arg
         * @return bool|object
         */
        public function check_info( $false, $action, $arg ) {

            if ( property_exists( $arg, 'slug' ) && $arg->slug && $arg->slug === $this->slug ) {

                $information = $this->getRemote_information();

                return $information;

            }

            return false;

        }

        /*
         * Get plugin metadata
         */
        public function get_plugin_info() {

            $information = get_transient( $this->transient_name_info );

            if ( false === $information ) {

                $information = $this->getRemote_information();

                set_transient( $this->transient_name_info, $information, 60 * 60 );

            }

            return $information;

        }

        /**
         * Return the remote version
         * @return string $remote_version
         */
        public function getRemote_version() {

            $request = wp_remote_post( $this->update_path, array( 'timeout' => 30, 'sslverify' => true, 'body' => array(
                'action' => 'version',
                'slug' => $this->slug,
                'installed_version' => $this->current_version,
                'license' => STI_PRO()->license->get_license_key(),
            ) ) );

            if ( ! is_wp_error($request) || wp_remote_retrieve_response_code( $request ) === 200 ) {
                return $request['body'];
            }

            return false;

        }

        /**
         * Get information about the remote version
         * @return bool|object
         */
        public function getRemote_information() {

            $request = wp_remote_post( $this->update_path, array( 'timeout' => 30, 'sslverify' => true, 'body' => array(
                'action' => 'get_metadata',
                'slug' => $this->slug,
                'installed_version' => $this->current_version,
                'license' => STI_PRO()->license->get_license_key(),
            ) ) );

            if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
                return unserialize($request['body']);
            }

            return false;

        }

        /**
         * Return the status of the plugin licensing
         * @param string $license_key
         * @return boolean $remote_license
         */
        public function get_remote_license( $license_key = '' ) {

            $request = wp_remote_post( $this->update_path, array( 'timeout' => 30, 'sslverify' => true, 'body' => array(
                'action' => 'license',
                'slug' => $this->slug,
                'license' => $license_key,
                'installed_version' => $this->current_version
            ) ) );

            if ( ! is_wp_error( $request ) && wp_remote_retrieve_response_code( $request ) === 200 ) {
                return true;
            }

            return false;

        }

    }


endif;