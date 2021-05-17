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

            update_option( 'sti_pro_settings', $settings );

        }

        /*
         * Get plugin settings
         * @return array
         */
        static public function get_settings() {
            $plugin_options = get_option( 'sti_pro_settings' );
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
             * @since 1.46
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
                "name"  => __( "Black list", "share-this-image" ),
                "desc"  => __( "The image classes and ids that the plugin will filter out. Separate several selectors with commas.", "share-this-image" ),
                "id"    => "dontshow",
                "value" => '.dontshow',
                "type"  => "text"
            );

            $options['general'][] = array(
                "name"  => __( "Exclude Pages", "share-this-image" ),
                "desc"  => __( "Add pages ids to exclude all images inside them from sharing.", "share-this-image" ),
                "id"    => "exclude_id",
                "value" => '',
                "type"  => "number_add"
            );


            $options['general'][] = array(
                "name"    => __( "Display Settings", "share-this-image" ),
                "desc"    => '',
                "type"    => "heading"
            );

            $options['general'][] = array(
                "name"  => __( "Share buttons", "share-this-image" ),
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
                    "link" => array(
                        'name'    => __( "Link", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                    "email" => array(
                        'name'    => __( "Email", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                    "download" => array(
                        'name'    => __( "Download", "share-this-image" ),
                        'desktop' => 'false',
                        'mobile'  => 'false'
                    ),
                )
            );

            $options['general'][] = array(
                "name"  => __( "Minimal width", "share-this-image" ),
                "desc"  => __( "Minimum width of image in pixels to use for sharing.", "share-this-image" ),
                "id"    => "minWidth",
                "value" => '200',
                "type"  => "number"
            );

            $options['general'][] = array(
                "name"  => __( "Minimal height", "share-this-image" ),
                "desc"  => __( "Minimum height of image in pixels to use for sharing.", "share-this-image" ),
                "id"    => "minHeight",
                "value" => '200',
                "type"  => "number"
            );

            $options['general'][] = array(
                "name"  => __( "Twitter via", "share-this-image" ),
                "desc"  => __( "Set twitters 'via' property.", "share-this-image" ),
                "id"    => "twitter_via",
                "value" => '',
                "type"  => "text"
            );

            $options['general'][] = array(
                "name"  => __( "Enable auto scroll?", "share-this-image" ),
                "desc"  => __( "If you don't want to scroll your visitors to the relevant shared image set this option to Off", "share-this-image" ),
                "id"    => "scroll",
                "value" => 'true',
                "type"  => "radio",
                'choices' => array(
                    'true' => __( 'On', 'share-this-image' ),
                    'false' => __( 'Off', 'share-this-image' )
                )
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
                "name"  => __( "Always show?", "share-this-image" ),
                "desc"  => __( "Show sharing buttons only on hover or make them all visible on page load.", "share-this-image" ) . '<br>' .
                    __( "NOTE: Enabling this option can cause some problems with images inside sliders, galleries, etc.", "share-this-image" ),
                "id"    => "always_show",
                "value" => 'false',
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
                    '<br>' . __( "Will be send the event with category - 'STI click', action - 'social button name' and label of value of image URL.", "share-this-image" ),
                "id"    => "use_analytics",
                "value" => 'false',
                "type"  => "radio",
                'choices' => array(
                    'true'  => __( 'On', 'share-this-image' ),
                    'false' => __( 'Off', 'share-this-image' )
                )
            );

            $options['content'][] = array(
                "name"    => __( "Content Sources", "share-this-image" ),
                "desc"    => sprintf( __( 'Plugin has %s special rules %s how to choose what title and description to use for sharing.', 'share-this-image' ), '<a href="https://share-this-image.com/guide/customize-content/" target="_blank">', '</a>' ) . '<br>' .
                    __( 'There is different sources that plugin look in step by step searching for content according to priority of this sources.', 'share-this-image' ) . '<br>' .
                    __( 'In this section you can change content sources priorities or even disable/enable some of them.', 'share-this-image' ),
                "type"    => "heading"
            );

            $options['content'][] = array(
                "name"  => __( "Title Source", "share-this-image" ),
                "desc"  => __( "Title source: Drag&drop sources order to change priority, or exclude by moving to deactivated sources.", "share-this-image" ),
                "id"    => "title_source",
                "value" => "data_title,title,default_title,document_title",
                "choices" => array(
                    "data_title"     => __( "data-title attribute", "share-this-image" ),
                    "data_summary"   => __( "data-summary attribute", "share-this-image" ),
                    "title"          => __( "title attribute", "share-this-image" ),
                    "alt"            => __( "alt attribute", "share-this-image" ),
                    "caption"        => __( "image caption", "share-this-image" ),
                    "default_title"  => __( "custom title", "share-this-image" ),
                    "default_desc"   => __( "custom description", "share-this-image" ),
                    "document_title" => __( "document title", "share-this-image" )
                ),
                "type"  => "sortable"
            );

            $options['content'][] = array(
                "name"  => __( "Description Source", "share-this-image" ),
                "desc"  => __( "Description source: Drag&drop sources order to change priority, or exclude by moving to deactivated sources.", "share-this-image" ),
                "id"    => "desc_source",
                "value" => "data_summary,caption,alt,default_desc",
                "choices" => array(
                    "data_title"     => __( "data-title attribute", "share-this-image" ),
                    "data_summary"   => __( "data-summary attribute", "share-this-image" ),
                    "title"          => __( "title attribute", "share-this-image" ),
                    "alt"            => __( "alt attribute", "share-this-image" ),
                    "caption"        => __( "image caption", "share-this-image" ),
                    "default_title"  => __( "custom title", "share-this-image" ),
                    "default_desc"   => __( "custom description", "share-this-image" ),
                    "document_title" => __( "document title", "share-this-image" )
                ),
                "type"  => "sortable"
            );

            $options['content'][] = array(
                "name"  => __( "URL Source", "share-this-image" ),
                "desc"  => __( "URL source: Drag&drop sources order to change priority, or exclude by moving to deactivated sources.", "share-this-image" ),
                "id"    => "url_source",
                "value" => "data_url,page_url",
                "choices" => array(
                    "data_url"    => __( "data_Url attribute", "share-this-image" ),
                    "page_url"    => __( "current page URL", "share-this-image" ),
                    "default_url" => __( "custom URL", "share-this-image" ),
                    "image_url"   => __( "shared image URL", "share-this-image" ),
                ),
                "type"  => "sortable"
            );

            $options['content'][] = array(
                "name"    => __( "Custom Content", "share-this-image" ),
                "desc"    => __( 'Content for "Custom Title", "Custom Description" and "Custom URL" sources that was selected above.', "share-this-image" ) . '<br>' .
                    __( "Note: source must be active and have high priority otherwise its won't be using for sharing.", "share-this-image" ) . '<br><br>' .
                    __( 'It is possible to customize content with following special variables:', "share-this-image" ) . '<br>' .
                    "<strong>{{data_title_attr}}</strong> , <strong>{{data_summary_attr}}</strong> , <strong>{{data_url_attr}}</strong> , <strong>{{title_attr}}</strong> , <strong>{{alt_attr}}</strong><br>" .
                    "<strong>{{page_link}}</strong> , <strong>{{document_title}}</strong> , <strong>{{img_caption}}</strong> , <strong>{{image_link}}</strong>, <strong>{{wp_page_title}}</strong><br><br>" .
                    __( 'Also you can use condition rules like below. Nesting is not allowed.', "share-this-image" ) . '<br>' .
                    "<strong>{{if img_caption}}</strong><br> Caption - <strong>{{img_caption}}</strong><br><strong>{{endif}}</strong><br><strong>{{if !data_title_attr AND title_attr}}</strong><br>Title - <strong>{{title_attr}}</strong><br><strong>{{endif}}</strong>",
                "type"    => "heading"
            );

            $options['content'][] = array(
                "name"  => __( "Custom Title", "share-this-image" ),
                "desc"  => __( "Content for 'Custom Title' source.", "share-this-image" ),
                "id"    => "title",
                "value" => '',
                "type"  => "textarea"
            );

            $options['content'][] = array(
                "name"  => __( "Custom Description", "share-this-image" ),
                "desc"  => __( "Content for 'Custom Description' source.", "share-this-image" ),
                "id"    => "summary",
                "value" => '',
                "type"  => "textarea"
            );

            $options['content'][] = array(
                "name"  => __( "Custom URL", "share-this-image" ),
                "desc"  => __( "Content for 'Custom URL' source.", "share-this-image" ),
                "id"    => "url",
                "value" => '',
                "type"  => "textarea"
            );

            $options['content'][] = array(
                "name"  => __( "Email Template", "share-this-image" ),
                "desc"  => __( "For email subject and body you can customize sharing content.", "share-this-image") . '<br>' .
                    __( "It is possible to use same special variables and conditions that was use above for custom content.", "share-this-image" ),
                "type"  => "heading"
            );

            $options['content'][] = array(
                "name"  => __( "Subject", "share-this-image" ),
                "desc"  => __( "Set default email subject.", "share-this-image" ),
                "id"    => "email_subject",
                "value" => '',
                "type"  => "textarea"
            );

            $options['content'][] = array(
                "name"  => __( "Content", "share-this-image" ),
                "desc"  => __( "Set default email content.", "share-this-image" ),
                "id"    => "email_body",
                "value" => '',
                "type"  => "textarea"
            );


            $options['view'][] = array(
                "name"  => __( "Buttons style", "share-this-image" ),
                "desc"  => __( "Choose one of predefined styles for share buttons.", "share-this-image" ),
                "id"    => "style",
                "value" => 'flat-small',
                "type"  => "radio-image",
                'choices' => array(
                    'flat-small' => 'style1.png',
                    'flat'       => 'style2.png',
                    'box'        => 'style3.png',
                    'circle'     => 'style4.png',
                    'square'     => 'style5.png',
                )
            );

            $options['view'][] = array(
                "name"  => __( "Align by plane x", "share-this-image" ),
                "desc"  => __( "Align of share box by x coordinate.", "share-this-image" ),
                "id"    => "align_x",
                "value" => 'left',
                "type"  => "radio",
                'choices' => array(
                    'left'  => __( 'Left', 'share-this-image' ),
                    'right' => __( 'Right', 'share-this-image' )
                )
            );

            $options['view'][] = array(
                "name"  => __( "Align by plane y", "share-this-image" ),
                "desc"  => __( "Align of share box by y coordinate.", "share-this-image" ),
                "id"    => "align_y",
                "value" => 'top',
                "type"  => "radio",
                'choices' => array(
                    'top'    => __( 'Top', 'share-this-image' ),
                    'bottom' => __( 'Bottom', 'share-this-image' )
                )
            );

            $options['view'][] = array(
                "name"  => __( "Offset by plane x", "share-this-image" ),
                "desc"  => __( "Offset of share box by x coordinate. In pixels.", "share-this-image" ),
                "id"    => "offset_x",
                "value" => '0',
                "type"  => "number"
            );

            $options['view'][] = array(
                "name"  => __( "Offset by plane y", "share-this-image" ),
                "desc"  => __( "Offset of share box by y coordinate. In pixels.", "share-this-image" ),
                "id"    => "offset_y",
                "value" => '0',
                "type"  => "number"
            );

            $options['view'][] = array(
                "name"  => __( "Orientation", "share-this-image" ),
                "desc"  => __( "Vertical or horizontal orientation of share box.", "share-this-image" ),
                "id"    => "orientation",
                "value" => 'vertical',
                "type"  => "radio",
                'choices' => array(
                    'vertical'   => __( 'Vertical', 'share-this-image' ),
                    'horizontal' => __( 'Horizontal', 'share-this-image' )
                )
            );

            return $options;

        }

    }

endif;