<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'YphsVdlynV9%P~AiDn3U,<pk?dFOO2X#o_be^6Bt2nZJ=DU&#?qAqK^jRxG/y<Qo' );
define( 'SECURE_AUTH_KEY',  '7=#A>;E&TK+]0qW_If,:dPyY*cF}y23WOk[xHWX,8g}OO|d:0pEGN`l:)2`S@uYG' );
define( 'LOGGED_IN_KEY',    'W#9>-H|,F9:3REEJ7wg,9]ok-t*k-y2gy!X7$$Yy$=6[)cJ=^z}[I>J;ASyh&XZI' );
define( 'NONCE_KEY',        'A~v0rFlPWOvi+htl,[&p$yn$O@lDC5b;.o~l3AzU9! WQ/EPnZ*Gx)XQ9-zB~i,u' );
define( 'AUTH_SALT',        'fP@H<,j$5oFC+7sRIgWlA0T-FQ>O:zl*Y%G_(7eb$2?rsSO*-7Aqxc8.;}kV%g@o' );
define( 'SECURE_AUTH_SALT', 'u&E*KJEma7Oc/Wway;Rh$p[Gx[D}?[Z{>Ui^Yrz(@w0Tfr66<neXa5-G@=!h(IKE' );
define( 'LOGGED_IN_SALT',   ':}:03<!BU,i{Nb%_JZDm6@}RX>3@.Z9bMg2Dc$ZLbWS#9!f*hh;Ii2y7iMr@@N7N' );
define( 'NONCE_SALT',       'Az<{r=peIWa4a}wDO[i&#:G!B=I^:z5f=}qwFuKgk_Yl3w@fp{k+FtZ^.r0X~)&u' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
