<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

function get_clipboard_js() {

	$screen = apply_filters( 'wpp_is_a_singular', is_singular() );

	if ( ! $screen ) {
		return;
	}

	$url = str_replace( wp_normalize_path( ABSPATH ), home_url( '/' ), wp_normalize_path( __DIR__ ) );

	wp_enqueue_script( 'clipboard', $url . 'clipboard.min.js', [ 'jquery' ], '2.0.8' );
}

add_action( 'wp_enqueue_scripts', 'get_clipboard_js', 10 );


function clipboard_footer_init() {

	$screen = apply_filters( 'wpp_is_a_singular', is_singular() );

	if ( ! $screen ) {
		return;
	}

	?>
    <script>
        jQuery(function ($) {

            function setTooltip(message) {
                $('.wpp-copy-btn').html(message)
            }

            function hideTooltip() {
                setTimeout(function () {
                    $('.wpp-copy-btn').html($('.wpp-copy-btn').attr('data-clipboard-text'))
                }, 1000);
            }

            var clipboard = new ClipboardJS('.wpp-copy-btn');

            clipboard.on('success', function (e) {
                setTooltip('Copied!');
                hideTooltip();
            });

            clipboard.on('error', function (e) {
                setTooltip('Failed!');
                hideTooltip();
            });

            $(document).on('click', '#Capa_1', function (e) {
                $('.wpp-copy-btn').trigger('click')
            });

        });
    </script>

	<?php

}

add_action( 'wp_footer', 'clipboard_footer_init', 1500 );