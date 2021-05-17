<?php
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );
define( 'WP_CACHE', false );
//define('SAVEQUERIES', true);
/** Enable W3 Total Cache */
 // Added by W3 Total Cache
// set_time_limit(300);
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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
//define('DB_NAME', 'masterha_wp100');
define('DB_NAME', 'masterha_wp100');

/** MySQL database username */
//define('DB_USER', 'masterha_wp100');
define('DB_USER', 'masterha_wp100');

/** MySQL database password */
//define('DB_PASSWORD', 'zs$uqiW0#5qD');
define('DB_PASSWORD', 'zs$uqiW0#5qD');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'ocrup4pdvj73qcyc1jm0bafzgwuctbzr5h5s71iwcmsgtqrmammma5eu3tcco4ya');
define('SECURE_AUTH_KEY',  'bjbiqallsukooy8ho9sxuur3xiq6tgv0nbvcm2quhy4nv026shqihrhndxht3k2w');
define('LOGGED_IN_KEY',    'axpqbcxibxe6dbf9ppbtadi1eluzmjjjhkkndhyym6k8r3jf84xbwgusqfehhsjg');
define('NONCE_KEY',        't7saw5yqcfc1ellcjhsyh2pb2gxrxdocsbfcb8alzlc6gjcfxprbkuzvtsjnkreh');
define('AUTH_SALT',        'be7dmxskapfftqwcudvpungbcpzujhhaz6k3vlr550i9gmu7h7jzkiuk8ke29m4p');
define('SECURE_AUTH_SALT', '0qzg2h0vy1bgsrjvmxjta9wpctgnxqmzadbsaati1kiqecoc9vdhwi4w16ulyubj');
define('LOGGED_IN_SALT',   '1aphi1eedpci2wv4d2wdce1erwiyhieqajgzgfi94tfl4imrjaplpluji7zdi5re');
define('NONCE_SALT',       'ctmo4qmo9bnydsft81y9pxmcxitcmvqilerhkofvkykew4uh86zwtzjw3ogrgsqo');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', true);

# define('RECOVERY_MODE_EMAIL', 'masterhand.pro@gmail.com');
/**
 * Disable revisions
 */
define('WP_POST_REVISIONS', false);


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
ini_set( 'upload_max_size' , '6M' );
ini_set( 'post_max_size', '6M');

define( 'ALTERNATE_WP_CRON', false );
define( 'DISABLE_WP_CRON', false );