<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'sassy_willa');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'MEBSpH<3qXr%+rv&3cxOFJ]-b 9R#.(ucH{rm#/Opm&!9W1e[@`Mhh2W[j;zn*=o');
define('SECURE_AUTH_KEY',  '7|(*p-<bW^?:T_fHeGN*f1sn9<O7lqV^Log#V.DdW&[oa|` zt7cAXh>3W1UD+RI');
define('LOGGED_IN_KEY',    ',V8[D q(}ARXl/m>dM~6Q0p|<_x{po=oSyi;@Sv7EHIel+!O=O8oZs;ZXT-)_c~z');
define('NONCE_KEY',        'U)C5[mP9wkbMtB]b{9bT-wmN~O)-@o$fbFzv^yMc-i)[9.!hKq`op$.cbc=8X{Ep');
define('AUTH_SALT',        'cxr)3Nu4s@B!c@!w18BY[^yI{Q,j;%dx H0r!&BVy+s.bT3Q7?47rc0Gt>[>fM!u');
define('SECURE_AUTH_SALT', 'mFIf*k3v8f0YxJLxSO4/.lk!M#hhSdr&;eY`>J?GqmT9~aY3UwtQ$*[HwS:VU&$a');
define('LOGGED_IN_SALT',   'NJcP^#/3up2r!N,7-^)^$MrD7/ *KNwe*+;QPi>m P=G?yc#agu(2$6%Y?tF14HT');
define('NONCE_SALT',       'JR3D.DU(x`R1Qs&vpnqG$GZLI40c`$S=hDMEeX3D|pHtO/B(.=@ZQdc;^2]S2N-}');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'sw_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
