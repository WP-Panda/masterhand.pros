<?php
	/**
	 * For developers: WordPress debugging mode.
	 *
	 * Change this to true to enable the display of notices during development.
	 * It is strongly recommended that plugin and theme developers use WP_DEBUG
	 * in their development environments.
	 *
	 * For information on other constants that can be used for debugging,
	 * visit the documentation.
	 *
	 * @link https://wordpress.org/support/article/debugging-in-wordpress/
	 */
	if ( WPP_SEVER_NAME === 'local' ) {
		// Включить отладку WP_DEBUG
		define( 'WP_DEBUG', true );

		// Включить журнал /wp-content/debug.log
		define( 'WP_DEBUG_LOG', __DIR__ . '/debug/php-errors-' . date( 'd-m-y' ) . '.log' );
		#define( 'WP_DEBUG_LOG', true );

		// Отключить вывод на экран
		define( 'WP_DEBUG_DISPLAY', false );
		@ini_set( 'display_errors', 0 );

		// Использовать версии JS и CSS для разработчика (при тестировании изменений в них)
		define( 'SCRIPT_DEBUG', true );
	} else {
		define( 'WP_DEBUG', false );
	}
