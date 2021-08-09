<?php
	/**
	 * Уыедомления
	 */
	#https://kamranahmed.info/toast
	function wpp_fr_notification_assets() {

		wp_enqueue_script( 'toast', wpp_fr()->plugin_url() . '/wpp-extention/wpp-notification/jquery-toast/jquery.toast.min.js', [ 'jquery' ], '1.3.2', true );
		wp_enqueue_style( 'toast', wpp_fr()->plugin_url() . '/wpp-extention/wpp-notification/jquery-toast/jquery.toast.min.css', [], '1.3.2', 'all' );

		#wp_enqueue_script( 'toast-test', wpp_fr()->plugin_url() . '/assets/test/test-notification.js', [ 'toast' ], '1.3.2', true );

	}

	add_action( 'admin_enqueue_scripts', 'wpp_fr_notification_assets' );