<?php
	/**
	 * The base configuration for WordPress
	 *
	 * The wp-config.php creation script uses this file during the
	 * installation. You don't have to use the web site, you can
	 * copy this file to "wp-config.php" and fill in the values.
	 *
	 * This file contains the following configurations:
	 *
	 * * MySQL settings
	 * * Secret keys
	 * * Database table prefix
	 * * ABSPATH
	 *
	 * @link    https://wordpress.org/support/article/editing-wp-config-php/
	 *
	 * @package WordPress
	 */

	require_once 'configs/base.php';

	require_once 'configs/salts.php';

	require_once 'configs/debug.php';


	/* That's all, stop editing! Happy publishing. */

	/** Absolute path to the WordPress directory. */
	if ( ! defined( 'ABSPATH' ) ) {
		define( 'ABSPATH', __DIR__ . '/' );
	}

	/** Sets up WordPress vars and included files. */
	require_once ABSPATH . 'wp-settings.php';

	require_once 'configs/fixes.php';