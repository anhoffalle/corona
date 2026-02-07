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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'corona');

/** MySQL database username */
define('DB_USER', 'corona');

/** MySQL database password */
define('DB_PASSWORD', 'gP8nS0gO2h');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'UjGm8puhZ*E)vjMLPhpR7ksobsHr#STl3uzAV0zzyavDDJ@YSIZ^e2DC(o%BJm7^');
define('SECURE_AUTH_KEY',  'S(laA(6U20mc)9GOn1g%eNN7Gie%KAF1vP!*dmukcj#)VFiU^V1R0J@JrN(VYW3w');
define('LOGGED_IN_KEY',    'z6r1Cy*c9!JYbysZ^U938g#7#s(l(W7KbUvS6w96#Qvl5BqfT6w2JmNNB&uRhdZl');
define('NONCE_KEY',        'P6jPfD1bQqFMsTMOWWr7DPDMKIhms)RFQ#G&YmP7qeaJByYG7E90PC%p%YZ1ilf7');
define('AUTH_SALT',        'Jcz00)f^Q@OheYbuFzBrS3W2HK3y(9uDRAgZYviHnVBv#7%gZ(jPxAmNTD10e%4(');
define('SECURE_AUTH_SALT', 'npW*DkNXZ!He%yu@TcanYgHR&Y^lz6TfmBCGThq)7YDXgEYD^1j(7owkV&wHMW^m');
define('LOGGED_IN_SALT',   'wZ@lMR24hsp%eB@&cODseHsfh*lgW2a6F(#OGhii00FGe6XD@Y&bhGP2fTsPF0!c');
define('NONCE_SALT',       'QJjeuGC24L(MeH6I3V0NN0YYoCk39iuwiArUTyK5oAy08mtirq(ttE*4aUUa7H44');
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

// Оптимизации для локальной разработки
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');
define('DISABLE_WP_CRON', true);  // Отключаем WP-Cron для ускорения
define('WP_POST_REVISIONS', 3);   // Ограничиваем ревизии

/* That's all, stop editing! Happy blogging. */



/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

define( 'WP_ALLOW_MULTISITE', true );

define ('FS_METHOD', 'direct');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
