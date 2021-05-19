<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'STI_Admin_Options' ) ) :

    /**
     * Class for plugin admin options methods
     */
    class STI_Admin_Options {

        /*
         * Get default settings values
         * @param string $tab Tab name
		 * @return array
         */
        static public function get_default_settings( $tab = false ) {

            $options = self::options_array( $tab );
            $default_settings = array();

            foreach ( $options as $section_name => $section ) {
                foreach ($section as $values) {

                    if ($values['type'] === 'heading') {
                        continue;
                    }

                    if ($values['type'] === 'checkbox') {
                        foreach ($values['choices'] as $key => $val) {
                            $default_settings[$values['id']][$key] = (string) sanitize_text_field( $values['value'][$key] );
                        }
                        continue;
                    }

                    if ( $values['type'] === 'textarea' && isset( $values['allow_tags'] ) ) {
                        $default_settings[$values['id']] = (string) addslashes( wp_kses( stripslashes( $values['value'] ), STI_Admin_Helpers::get_kses( $values['allow_tags'] ) ) );
                        continue;
                    }

                    if ( $values['type'] === 'sortable_table' ) {
                        foreach ( $values['choices'] as $key => $opts_arr ) {
                            foreach( $opts_arr as $opt_name => $opt_val ) {
                                if ( $opt_name === 'name' ) continue;
                                $default_settings[$values['id']][$key][$opt_name] = (string) sanitize_text_field( $opt_val );
                            }
                        }
                        continue;
                    }

                    $default_settings[$values['id']] = (string) sanitize_text_field( $values['value'] );

                }
            }

            return $default_settings;

        }

        /*
         * Update plugin settings
         */
        static public function update_settings() {

            $options = self::options_array();
            $settings = self::get_settings();
            $current_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_text_field( $_GET['tab'] );

            foreach ( $options[$current_tab] as $values ) {

                if ( $values['type'] === 'heading' ) {
                    continue;
                }

                if ( $values['type'] === 'checkbox' ) {
                    foreach ( $values['choices'] as $key => $val ) {
                        $settings[$values['id']][$key] = (string) sanitize_text_field( $_POST[ $values['id'] ][$key] );
                    }
                    continue;
                }

                if ( $values['type'] === 'textarea' && isset( $values['allow_tags'] ) ) {
                    $settings[ $values['id'] ] = (string) addslashes( wp_kses( stripslashes( $_POST[ $values['id'] ] ), STI_Admin_Helpers::get_kses( $values['allow_tags'] ) ) );
                    continue;
                }

                if ( $values['type'] === 'sortable_table' ) {

                    $table_keys = array_map( 'sanitize_text_field', array_keys( $_POST[ $values['id'] ] ) );
                    $sorted_table = array_merge( array_flip( $table_keys ), $values['choices'] );
                    $table_options = array();

                    foreach ( $sorted_table as $key => $opts_arr ) {
                        foreach( $opts_arr as $opt_name => $opt_val ) {
                            if ( $opt_name === 'name' ) continue;
                            $table_options[$key][$opt_name] = isset( $_POST[ $values['id'] ][$key][$opt_name] ) ? (string) sanitize_text_field( $_POST[ $values['id'] ][$key][$opt_name] ) : 'false';
                        }
                    }

                    $settings[$values['id']] = $table_options;

                    continue;
                }

                $settings[ $values['id'] ] = (string) sanitize_text_field( $_POST[ $values['id'] ] );

            }

            update_option( 'sti_settings', $settings );

        }

        /*
         * Get plugin settings
         * @return array
         */
        static public function get_settings() {
            $plugin_options = get_option( 'sti_settings' );
            return $plugin_options;
        }

        /*
         * Options array that generate settings page
         *
         * @param string $tab Tab name
         * @return array
         */
        static public function options_array( $tab = false ) {

            $options = self::include_options();
            $options_arr = array();

            /**
             * Filter the array of plugin options
             * @since 1.31
             * @param array $options Array of options
             */
            $options = apply_filters( 'sti_all_options', $options );

            foreach ( $options as $tab_name => $tab_options ) {

                if ( $tab && $tab !== $tab_name ) {
                    continue;
                }

                $options_arr[$tab_name] = $tab_options;

            }

            return $options_arr;

        }

        /*
         * Include options array
         * @return array
         */
        static public function include_options() {

            $options = array();

            $options['general'][] = array(
                "name"    => __( "What to Share", "share-this-image" ),
                "desc"    => '',
                "type"    => "heading"
            );

            $options['general'][] = array(
                "name"  => __( "Selector", "share-this-image" ),
                "desc"  => __( "Selectors for images. Separate several selectors with commas.", "share-this-image" ),
                "id"    => "selector",
                "value" => 'img',
                "type"  => "text"
            );

            $options['general'][] = array(
                "name"    => __( "Display Settings", "share-this-image" ),
                "desc"    => '',
                "type"    => "heading"
            );

            $options['general'][] = array(
                "name"  => __( "Sharing buttons", "share-this-image" ),
                "desc"  => __( "Enable or disable sharing buttons for desktop and mobile. Drag & drop to change the order.", "share-this-image" ),
                "id"    => "buttons",
                "value" => array(),
                "type"  => "sortable_table",
                'choices' => array(
                    "facebook" => array(
                        'name'    => __( "Facebook", "share-this-image" ),
                        'desktop' => 'true',
                        'mobile'  => 'true'
                    ),
                    "twitter" => array(
                        'name'    => __( "Twitter", "share-this-image" ),
                        'desktop' => 'true',
                        'mobile'  => 'true'
                    ),
                    "linkedin" => array(
                        'name'    => __( "LinkedIn", "share-this-image" ),
                        'desktop' => 'true',
                        'mobile'  => 'true'
                    ),
                    "pinterest" => array(
                        'name'    => __( "Pinterest", "share-this-image" ),
                        'desktop' => 'true',
                        'mobile'  => 'true'
                    ),
                    "messenger" => array(
                        'name'    => __( "Messenger", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                    "whatsapp" => array(
                        'name'    => __( "WhatsApp", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                    "telegram" => array(
                        'name'    => __( "Telegram", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                    "tumblr" => array(
                        'name'    => __( "Tumblr", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                    "reddit" => array(
                        'name'    => __( "Reddit", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                    "digg" => array(
                        'name'    => __( "Digg", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                    "delicious" => array(
                        'name'    => __( "Delicious", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                    "vkontakte" => array(
                        'name'    => __( "Vkontakte", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                    "odnoklassniki" => array(
                        'name'    => __( "Odnoklassniki", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                )
            );

            $options['general'][] = array(
                "name"  => __( "Buttons position", "share-this-image" ),
                "desc"  => __( "Choose sharing buttons position.", "share-this-image" ) . '<br>' .
                    __( "NOTE: Enabling some positions can cause problems with images inside sliders, galleries, etc.", "share-this-image" ),
                "id"    => "position",
                "value" => 'image_hover',
                "type"  => "radio",
                'choices' => array(
                    'image'       => __( 'On image ( always show )', 'share-this-image' ),
                    'image_hover' => __( 'On image ( show on mouse enter )', 'share-this-image' ),
                )
            );

            $options['general'][] = array(
                "name"  => __( "Minimal width", "share-this-image" ),
                "desc"  => __( "Minimum width of image in pixels to use for sharing.", "share-this-image" ),
                "id"    => "minWidth",
                "value" => '150',
                "type"  => "number"
            );

            $options['general'][] = array(
                "name"  => __( "Minimal height", "share-this-image" ),
                "desc"  => __( "Minimum height of image in pixels to use for sharing.", "share-this-image" ),
                "id"    => "minHeight",
                "value" => '150',
                "type"  => "number"
            );

            $options['general'][] = array(
                "name"  => __( "Facebook app id", "share-this-image" ),
                "desc"  => __( "Required for FB Messenger sharing. Read more", "share-this-image" ) . ' <a href="https://share-this-image.com/guide/facebook-app-id/" target="_blank">' . __( 'here.', 'share-this-image' ) . '</a>' ,
                "id"    => "fb_app",
                "value" => '',
                "type"  => "text"
            );

            $options['general'][] = array(
                "name"  => __( "Twitter via", "share-this-image" ),
                "desc"  => __( "Set twitters 'via' property.", "share-this-image" ),
                "id"    => "twitter_via",
                "value" => '',
                "type"  => "text"
            );

            $options['general'][] = array(
                "name"  => __( "Enable on mobile?", "share-this-image" ),
                "desc"  => __( "Enable image sharing on mobile devices", "share-this-image" ),
                "id"    => "on_mobile",
                "value" => 'true',
                "type"  => "radio",
                'choices' => array(
                    'true' => __( 'On', 'share-this-image' ),
                    'false' => __( 'Off', 'share-this-image' )
                )
            );

            $options['general'][] = array(
                "name"  => __( "Use intermediate page.", "share-this-image" ),
                "desc"  => __( "If you have some problems with redirection from social networks to page with sharing image try to switch Off this option.", "share-this-image" ) . '</br>' .
                    __( "But before apply it need to be tested to ensure that all work's fine.", 'share-this-image' ),
                "id"    => "sharer",
                "value" => 'true',
                "type"  => "radio",
                'choices' => array(
                    'true'  => __( 'On', 'share-this-image' ),
                    'false' => __( 'Off', 'share-this-image' )
                )
            );

            $options['general'][] = array(
                "name"  => __( "Google Analytics", "share-this-image" ),
                "desc"  => __( "Use google analytics to track social buttons clicks. Need google analytics to be installed on your site.", "share-this-image" ) .
                    '<br>' . __( "Will be send the event with category - 'STI click', action - 'social button name' and label of value of image URL.", "share-this-image" ) .
                    ' <a href="https://share-this-image.com/guide/google-analytics/" target="_blank">' . __( 'More info', 'share-this-image' ) . '</a>' ,
                "id"    => "use_analytics",
                "value" => 'false',
                "type"  => "radio",
                'choices' => array(
                    'true'  => __( 'On', 'share-this-image' ),
                    'false' => __( 'Off', 'share-this-image' )
                )
            );

            $options['content'][] = array(
                "name"    => "",
                "desc"    => sprintf( __( 'Plugin has %s special rules %s how to choose what title and description to use for sharing.', 'share-this-image' ), '<a href="https://share-this-image.com/guide/customize-content/" target="_blank">', '</a>' ) . '<br>' .
                    __( 'There is different sources that plugin look in step by step searching for content according to priority of this sources.', 'share-this-image' ) . '<br>' .
                    __( 'Also with PRO plugin version you can change priority of this sources or even disable/enable some of them.', 'share-this-image' ) . '<br>' . '<br>' .
                    __( "For title: 'data-title attribute' -> 'image title attribute' -> 'default title option' -> 'page title'", "share-this-image" ) . '<br>' .
                    __( "For description: 'data-summary attribute' -> 'image caption' -> 'image alt attribute' -> 'default description option'", "share-this-image" ) . '<br>' . '<br>' .
                    sprintf( __( "It is also possible to create your fully unique title and description with help of special %s variables and conditions %s.", "share-this-image" ), '<a href="https://share-this-image.com/guide/customize-content/" target="_blank">', '</a>' ),
                "type"    => "heading"
            );

            $options['content'][] = array(
                "name"    => __( "Default Content", "share-this-image" ),
                "desc"    => '',
                "type"    => "heading"
            );

            $options['content'][] = array(
                "name"  => __( "Default Title", "share-this-image" ),
                "desc"  => __( "Content for 'Default Title' source.", "share-this-image" ),
                "id"    => "title",
                "value" => '',
                "type"  => "text"
            );

            $options['content'][] = array(
                "name"  => __( "Default Description", "share-this-image" ),
                "desc"  => __( "Content for 'Default Description' source.", "share-this-image" ),
                "id"    => "summary",
                "value" => '',
                "type"  => "textarea",
                'allow_tags' => array( 'a', 'br', 'em', 'strong', 'b', 'code', 'blockquote', 'p', 'i' )
            );

            return $options;

        }

    }

endif;