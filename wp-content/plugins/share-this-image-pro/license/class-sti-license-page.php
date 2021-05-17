<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'STI_License_Page' ) ) :

    /**
     * Class for pro plugin license manager
     */
    class STI_License_Page {

        /**
         * @var STI_License_Page The single instance of the class
         */
        protected static $_instance = null;

        /**
         * @var STI_License_Page License key
         */
        private $license_key = false;

        /**
         * Main STI_License_Page Instance
         *
         * Ensures only one instance of STI_License_Page is loaded or can be loaded.
         *
         * @static
         * @return STI_License_Page - Main instance
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Constructor
         */
        function __construct() {

            $this->license_key = STI_PRO()->license->get_license_key();

            add_action( 'admin_menu', array( $this, 'add_admin_page' ) );

            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

        }

        /*
         * Add options page
         */
        public function add_admin_page() {
            add_submenu_page( 'sti-options', esc_html__('Updates','share-this-image'), esc_html__('Updates','share-this-image'), 'manage_options', 'sti-options-updates', array( $this,'updates_page' ) );
        }

        /*
         * License page content
         */
        public function updates_page() {

            echo '<div class="wrap">';

                echo '<h1></h1>';
                echo '<h1>' . esc_html__( 'Updates', 'share-this-image' ) . '</h1>';

                echo '<div class="sti-box-wrap">';

                    $this->license_block();

                    $this->info_block();

                echo '</div>';

            echo '</div>';

        }

        /*
         * Block with license info
         */
        private function license_block() {

            $license_key = $this->license_key ? $this->license_key : '';
            $valid_class = $this->license_key ? 'valid' : '';
            $deactivate_input = $this->license_key ? ' disabled' : '';

            $btn_html = $this->license_key ?
                '<button id="activate-license" data-is-active="active" class="button button-primary">' . esc_html__( 'Deactivate License', 'share-this-image' ) . '</button>' :
                '<button id="activate-license" data-is-active="inactive" class="button button-primary">' . esc_html__( 'Activate License', 'share-this-image' ) . '</button>';


            echo '<div class="sti-box">';

                echo '<div class="title">';
                    echo '<h3>' . esc_html__( 'License Information', 'share-this-image' ) . '</h3>';
                echo '</div>';

                echo '<div class="inner">';

                    echo '<p>';
                        echo sprintf( esc_html__( 'To unlock updates, please enter your license key below. It is equal to your purchase order ID. If you don\'t have a licence key, please visit %s.', 'share-this-image' ), '<a target="_blank" href="https://share-this-image.com/">' . esc_html__( 'plugin page', 'share-this-image' ) . '</a>' );
                    echo '</p>';

                    echo '<form data-license-form action="" name="sti_form" id="sti_form" method="post" class="'. $valid_class .'">';

                        echo '<table class="form-table">';
                            echo '<tbody>';

                                echo '<tr valign="top">';

                                    echo '<th scope="row">'. esc_html__('License Key', 'share-this-image' ) . '</th>';

                                    echo '<td>';
                                        echo '<input' . $deactivate_input . ' type="text" name="license_key" class="regular-text" value="'.$license_key.'">';
                                    echo '</td>';

                                    echo '<td>';
                                        echo '<div class="sti-license-btn-wrap">';
                                            echo $btn_html;
                                            echo '<div class="sti-loader"></div>';
                                        echo '</div>';
                                    echo '</td>';

                                echo '</tr>';

                            echo '</tbody>';
                        echo '</table>';

                        echo '<div class="license-error">' . esc_html__( 'Sorry, your license key is not valid.', 'share-this-image' ) . '</div>';
                        echo '<div class="license-valid">' . esc_html__( 'Your license key is active.', 'share-this-image' ) . '</div>';

                    echo '</form>';

                echo '</div>';

            echo '</div>';

        }

        /*
         * Block with plugin info
         */
        private function info_block() {

            $plugin_info = STI_PRO()->license->updater->get_plugin_info();
            $license_key = $this->license_key ? $this->license_key : '';

            if ( ! $plugin_info ) {
                echo esc_html__( 'Something goes wrong while getting plugin data.', 'share-this-image' );
                return;
            }

            $plugin_latest_version = $plugin_info->new_version;

            if ( version_compare( STI_PRO_VER, $plugin_latest_version, '<' ) ) {

                if ( $license_key ) {
                    $plugin_update_available = esc_html__( 'Yes', 'share-this-image' ) . '<a class="button button-primary sti-update" href="' . admin_url('plugins.php?s=Share+This+Image+Pro') . '">' . esc_html__( 'Update Plugin', 'share-this-image' ) . '</a>';
                } else {
                    $plugin_update_available = esc_html__( 'Yes', 'share-this-image' ) . '<a class="button sti-update" disabled="disabled" href="#">' . esc_html__( 'Please enter your license key to unlock updates', 'share-this-image' ) . '</a>';
                }

                $plugin_changelog = $plugin_info->sections['changelog'];

                if ( $plugin_changelog ) {

                    preg_match( '/(<h4[\S\s]*?)<h4>'.STI_PRO_VER.'<\/h4>/i', $plugin_changelog, $matches );

                    if ( $matches && isset( $matches[1] ) ) {
                        $plugin_changelog = $matches[1];
                    }

                }

            } else {
                $plugin_update_available = esc_html__( 'No', 'share-this-image' );
                $plugin_changelog ='';
            }


            // Update info
            echo '<div class="sti-box">';

                echo '<div class="title">';
                    echo '<h3>' . esc_html__( 'Update Information', 'share-this-image' ) . '</h3>';
                echo '</div>';

                echo '<div class="inner">';

                    echo '<table class="form-table">';
                        echo '<tbody>';

                            echo '<tr valign="top">';

                                echo '<th scope="row">'. esc_html__('Current Version', 'share-this-image' ) . '</th>';

                                echo '<td>';
                                    echo STI_PRO_VER;
                                echo '</td>';

                            echo '</tr>';

                            echo '<tr valign="top">';

                                echo '<th scope="row">'. esc_html__('Latest Version', 'share-this-image' ) . '</th>';

                                echo '<td>';
                                    echo $plugin_latest_version;
                                echo '</td>';

                            echo '</tr>';

                            echo '<tr valign="top">';

                                echo '<th scope="row">'. esc_html__('Update Available', 'share-this-image' ) . '</th>';

                                echo '<td>';
                                    echo $plugin_update_available;
                                echo '</td>';

                            echo '</tr>';

                            echo '<tr valign="top">';

                                echo '<th scope="row">'. esc_html__('Changelog', 'share-this-image' ) . '</th>';

                                echo '<td>';

                                    echo $plugin_changelog;

                                    echo '<a href="https://share-this-image.com/guide/premium-version/" target="_blank">' . esc_html__( 'View all changelog', 'share-this-image' ) . '</a>';

                                echo '</td>';

                            echo '</tr>';

                        echo '</tbody>';
                    echo '</table>';

                echo '</div>';

            echo '</div>';

        }

        /*
         * Enqueue admin scripts and styles
         */
        public function admin_enqueue_scripts() {

            if ( isset( $_GET['page'] ) && $_GET['page'] == 'sti-options-updates' ) {

                wp_enqueue_style( 'plugin-admin-updates-style', STI_PRO_URL . '/license/assets/css/admin-updates.css' );

                wp_enqueue_script( 'jquery' );

                wp_enqueue_script( 'sti-admin-updates', STI_PRO_URL . '/license/assets/js/admin-updates.js', array('jquery') );

            }

        }

    }


endif;

add_action( 'init', 'STI_License_Page::instance' );