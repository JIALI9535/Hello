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
define('DB_NAME', 'jiali');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         '.v0{ssT8E{9XA3Oczk:8;/lwDaW.5F %D|`,b|M/.8QwPL:?&#so2$uXHOzO;G}4');
define('SECURE_AUTH_KEY',  'F>kyF=%~I~uuHy6e:r?~Fm8IvwF^KB|W`0n:U?;cyNe5Z=?>=A#Z1W_O4@?^I(4^');
define('LOGGED_IN_KEY',    'b}f/*9$xhH/4#h=}l&85wMA!7mY1w3JjQy2QQaomYw?;hQoy|/4~0{V1PaU^5z7c');
define('NONCE_KEY',        'avhCdoj0OwT|-BuXA?6j*)rjp;S7H09M6hlO^T0u!~Ll(?0xpo{=/aB&dl0|!qhB');
define('AUTH_SALT',        ':T&~:vED N0v{]A#@GBV#3O3FCb6E4TiI,R{{|mEL<ct?=OkC:hI3{qC*6R-N ]#');
define('SECURE_AUTH_SALT', '0{.6KaYe9L+UZ^7?FW).~[[Jj-e@!0a/t}oz[#WW(&.jxeS6apGw*^Df&HwPq[%b');
define('LOGGED_IN_SALT',   'yYvWGO[kFKQ. *|+OJEcvGAKq/N%L6Of9JWP_fr)Y9`B(CH.-W=>`Bk:vrGuJ`xR');
define('NONCE_SALT',       '({Ph/LzEuw9RJDm922}>SMbFipoD2q[00.Wm%t~:9B`r)NaZv=z-Q-TQul,Xm2GW');

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
