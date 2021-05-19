<?php
/**
 * STI shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'STI_Shortcodes' ) ) :

    /**
     * Class for main plugin functions
     */
    class STI_Shortcodes {

        /**
         * @var STI_Shortcodes The single instance of the class
         */
        protected static $_instance = null;

        /**
         * Main STI_Shortcodes Instance
         * @static
         * @return STI_Shortcodes - Main instance
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Setup actions and filters for all things settings
         */
        public function __construct() {

            add_shortcode( 'sti_image', array( $this, 'shortcode' ) );
            add_shortcode( 'sti_buttons', array( $this, 'buttons_shortcode' ) );

        }

        /*
         * Shortcode for image sharing
         */
        public function shortcode( $atts = array() ) {

            extract( shortcode_atts( array(
                'image'           => '',
                'shared_image'    => '',
                'shared_url'      => '',
                'shared_title'    => '',
                'shared_desc'     => '',
            ), $atts ) );


            $params_string = '';
            $output = '';

            $params = array(
                'data-media'   => $shared_image,
                'data-url'     => $shared_url,
                'data-title'   => $shared_title,
                'data-summary' => $shared_desc,
            );

            foreach( $params as $key => $value ) {
                if ( $value ) {
                    $params_string .= $key . '="' . esc_attr( strip_shortcodes( $value ) ) . '" ';
                }
            }

            if ( $image ) {
                $output = '<img src="' . esc_url( $image ) . '" ' . $params_string . '>';
            }

            return apply_filters( 'sti_shortcode_output', $output, $atts );

        }

        /*
         * Shortcode for sharing buttons
         */
        public function buttons_shortcode( $atts = array() ) {

            extract( shortcode_atts( array(
                'image'       => '',
                'url'         => '',
                'title'       => '',
                'description' => '',
                'buttons'     => 'facebook, twitter, linkedin',
            ), $atts ) );

            $params_string = '';
            $output = '';

            $post_thumbnail = get_the_post_thumbnail_url( null, 'full' );
            $yoast_wpseo_opengraph = get_post_meta( get_queried_object_id(), '_yoast_wpseo_opengraph-image', true );

            $params = array(
                'data-media'   => $image ? $image : ( $post_thumbnail ? $post_thumbnail : ( $yoast_wpseo_opengraph ? $yoast_wpseo_opengraph : false ) ),
                'data-url'     => $url ? $url : get_permalink(),
                'data-title'   => $title ? $title : get_the_title(),
                'data-summary' => $description ? $description : get_the_excerpt(),
            );

            /**
             * Filter buttons shortcode paramems
             * @since 1.37
             * @param array $params
             * @param array $atts
             */
            $params = apply_filters( 'sti_buttons_shortcode_params', $params, $atts );

            if ( ! $params['data-media'] ) {
                $id = get_queried_object_id();
                if ( $id ) {
                    $image = get_the_post_thumbnail_url( $id, 'full' );
                    if ( $image ) {
                        $params['data-media'] = $image;
                    }
                }
            }

            if ( ! $params['data-media'] && ( ! isset( $_REQUEST['action'] ) || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] !== 'edit' ) )
                && ( ! isset( $_REQUEST['context'] ) || ( isset( $_REQUEST['context'] ) && $_REQUEST['context'] !== 'edit' ) )
            ) {
                return '';
            }

            foreach( $params as $key => $value ) {
                if ( $value ) {
                    $params_string .= $key . '="' . esc_attr( strip_shortcodes( $value ) ) . '" ';
                }
            }

            $buttons_array = array_map( 'trim', explode( ',', $buttons ) );

            if ( $buttons_array ) {

                $output .= '<div class="sti-container" ' . $params_string . '>';

                    $output .= '<div class="sti sti-top style-flat-small sti-shortcode">';
                        $output .= '<div class="sti-share-box">';

                        foreach ( $buttons_array as $button ) {
                            $output .= '<div class="sti-btn sti-' . $button . '-btn" data-network="' . $button . '" rel="nofollow">';
                            $output .= STI_Admin_Helpers::get_svg( $button );
                            $output .= '</div>';
                        }

                        $output .= '</div>';
                    $output .= '</div>';

                $output .= '</div>';

            }

            return apply_filters( 'sti_buttons_shortcode_output', $output, $atts );

        }

    }


endif;


STI_Shortcodes::instance();


if ( ! function_exists( 'sti_sharing_buttons' ) ) {

    /**
     * Return sharing buttons
     * @return string
     */
    function sti_sharing_buttons( $echo = true, $args = array() ) {

        $buttons = STI_Shortcodes::instance()->buttons_shortcode( $args );

        if ( $echo ) {
            echo $buttons;
        } else {
            return $buttons;
        }

    }

}