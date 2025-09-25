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

define('WP_ENV', 'local');
define('WP_ENVIRONMENT_TYPE', 'local');
define('DISABLE_GLIDE', true);
define('DISABLED_LOCAL_PLUGINS', [
    ABSPATH . 'wp-content/plugins/ep-cache/ep-cache.php',
    ABSPATH . 'wp-content/plugins/wordfence/wordfence.php',
    ABSPATH . 'wp-content/plugins/mailgun/mailgun.php',
    ABSPATH . 'wp-content/plugins/ewww-image-optimizer/ewww-image-optimizer.php'
]);

// Site url settings
define('WP_SITEURL', 'https://example.com/');
define('WP_HOME', 'https://example.com/');

// Google Analytics (leave empty to disable)
define('GA_ID', '');

// Google Tag Manager (leave empty to disable)
define('GTM_ID', '');

// Limit the number of revisions to keep historically
// Since too many can cause memory issues and aren't
// really necessary.
define('WP_POST_REVISIONS', 50);

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'database_name_here');

/** MySQL database username */
define('DB_USER', 'username_here');

/** MySQL database password */
define('DB_PASSWORD', 'password_here');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

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
define('AUTH_KEY', 'put your unique phrase here');
define('SECURE_AUTH_KEY', 'put your unique phrase here');
define('LOGGED_IN_KEY', 'put your unique phrase here');
define('NONCE_KEY', 'put your unique phrase here');
define('AUTH_SALT', 'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT', 'put your unique phrase here');
define('NONCE_SALT', 'put your unique phrase here');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);
// define('WP_DEBUG_LOG', false);
// define('WP_DEBUG_DISPLAY', true);

define('DISALLOW_FILE_EDIT', true); // disables file editor
define('DISALLOW_FILE_MODS', true); // disables both file editor and installer
define('DISABLE_WP_CRON', true); // should run cron via WP-CLI instead
define('AUTOMATIC_UPDATER_DISABLED', true); // we want to control updates in our codebase
define('WP_AUTO_UPDATE_CORE', false); // we want to control updates in our codebase

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
