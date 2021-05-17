<?php
/**
 * Versions capability
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'STI_PRO_Versions' ) ) :

    /**
     * Class for plugin search
     */
    class STI_PRO_Versions {

        /**
         * Return a singleton instance of the current class
         *
         * @return object
         */
        public static function factory() {
            static $instance = false;

            if ( ! $instance ) {
                $instance = new self();
                $instance->setup();
            }

            return $instance;
        }

        /**
         * Placeholder
         */
        public function __construct() {}

        /**
         * Setup actions and filters for all things settings
         */
        public function setup() {

            $current_version = get_option( 'sti_pro_plugin_ver' );
            
            if ( $current_version ) {

                if ( version_compare( $current_version, '1.33', '<' ) ) {

                    $settings = get_option( 'sti_pro_settings' );

                    if ( $settings ) {
                        if ( strpos( $settings['primary_menu'], 'google') !== false) {
                            $settings['primary_menu'] = str_replace( array( 'google,', 'google' ), '', $settings['primary_menu'] );
                            update_option( 'sti_pro_settings', $settings );
                        }

                    }

                }

                if ( version_compare( $current_version, '1.39', '<' ) ) {

                    $settings = get_option( 'sti_pro_settings' );

                    if ( $settings ) {
                        if ( ! isset( $settings['url_source'] ) ) {
                            $settings['url_source'] = 'data_url,page_url';
                            update_option( 'sti_pro_settings', $settings );
                        }
                        if ( ! isset( $settings['url'] ) ) {
                            $settings['url'] = '';
                            update_option( 'sti_pro_settings', $settings );
                        }
                    }

                }

                if ( version_compare( $current_version, '1.43', '<' ) ) {

                    $settings = get_option( 'sti_pro_settings' );

                    if ( $settings ) {
                        if ( ! isset( $settings['use_analytics'] ) ) {
                            $settings['use_analytics'] = 'false';
                            update_option( 'sti_pro_settings', $settings );
                        }
                    }

                }

                if ( version_compare( $current_version, '1.44', '<' ) ) {

                    $settings = get_option( 'sti_pro_settings' );

                    if ( $settings ) {

                        if ( ! isset( $settings['buttons'] ) ) {

                            $primary_menu_array = explode( ',', $settings['primary_menu'] );
                            $options_array = STI_Admin_Options::options_array();
                            $buttons = array();
                            foreach( $options_array['general'] as $def_option ) {
                                if ( isset( $def_option['id'] ) && $def_option['id'] === 'buttons' && isset( $def_option['choices'] ) ) {
                                    $sorted_table = array_merge( array_flip( $primary_menu_array ), $def_option['choices'] );
                                    foreach( $sorted_table as $choice_key => $choice_arr ) {
                                        foreach( $choice_arr as $opt_name => $opt_val ) {
                                            if ( $opt_name === 'name' ) continue;
                                            $buttons[$choice_key][$opt_name] = in_array( $choice_key, $primary_menu_array ) ? 'true' : 'false';
                                        }
                                    }
                                }
                            }

                            $settings['buttons'] = $buttons;
                            update_option( 'sti_pro_settings', $settings );

                        }

                    }

                }

            }

            update_option( 'sti_pro_plugin_ver', STI_PRO_VER );

        }

    }


endif;

add_action( 'admin_init', 'STI_PRO_Versions::factory' );