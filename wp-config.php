<?php
/**
 * This is a (proposed) standard configuration framework for WordPress
 *
 * If adopted, this header would start with a DO NOT MODIFY warning.
 *
 * The idea is that environment specific configuration will be moved to
 * other files that can coexist in version control and can be easily  
 * deployed via CI/CD without requiring custom code to deal with each 
 * webhost's different configuration and rules for wp-config.php. 
 * 
 * Defines these constants:
 *
 *      WP_WEBHOST - The name of the host in lowercase, e.g. `dreamhost`, `pantheon`, `wpengine` etc.
 *      WP_ROOT_DIR - Indicates the directory for the root of the WordPress website
 *      WP_ROOT_DOMAIN - The domain sans `.www` on which the site is hosted
 *      WP_SITE_DOMAIN - The domain including `.www`, if applicable
 *      WP_SITE_SUBDIR - Specifies subdirectory where site installed. Defaults to '', could be '/blog', etc.
 *      WP_CORE_PATH - Defines the path to core. Defaults to '', could be '/wp', etc.
 *      WP_CONTENT_PATH - Defines path to wp-content. Defaults to '/wp-content', could be '/app', etc.
 *
 */

/**
 * Get the directory of the root of the website
 * This differs from ABSPATH
 * BETTER if WordPress core could set in /index.php
 */
$backtrace = debug_backtrace();
define( 'WP_ROOT_DIR', dirname( $backtrace[ 2 ][ 'file' ] ) );
unset( $backtrace );

/*
 * Ensure WP CLI works on the codebase. 
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	$_SERVER[ 'HTTP_HOST' ] = 'wp.cli';  // Is there a better way? Read from a file?
} else {
	define( 'WP_CLI', false );
}

/*
 * Ensure $_SERVER[ 'HTTP_HOST' ] is set.
 */
if ( ! isset( $_SERVER[ 'HTTP_HOST' ] ) ) {
	trigger_error( '$_SERVER[ \'HTTP_HOST\' ] not set (server may be misconfigured.)' );
	exit;
}

/**
 * Set a new WP_HOST_NAME constant to identify the host.
 * This is set by the host itself and need not be from
 * a validated list.
 */
define( 'WP_WEBHOST', ! empty( $_ENV[ 'WP_HOST_NAME' ] ) ? 'custom' : $_ENV[ 'WP_HOST_NAME' ] );

/**
 * If a wp-config-{WP_HOST_NAME}.php exists, load it.
 *
 * This allows a webhost to first run their required config
 */
if ( is_file( dirname( __FILE__ ) . '/wp-config-' . WP_WEBHOST . '.php' ) ) {
	require( dirname( __FILE__ ) . '/wp-config-' . WP_WEBHOST . '.php' );
}

/**
 * Set new WP_ROOT_DOMAIN constant with is a domain sans-www.
 * If set by webhost in $_ENV[ 'WP_ROOT_DOMAIN' ] then use that.
 */
if ( ! defined( 'WP_ROOT_DOMAIN' ) ) {
	define( 'WP_ROOT_DOMAIN', empty( $_ENV[ 'WP_ROOT_DOMAIN' ] )
		? preg_replace( '#^www\.(.+)$#', '$1', $_SERVER[ 'HTTP_HOST' ] )
		: $_ENV[ 'WP_ROOT_DOMAIN' ]
	);
}

/**
 * If a wp-config-{WP_ROOT_DOMAIN}.php exists, load it.
 * directories for config overrides
 *
 * Allows the site developer to specify different configurations for
 * different environments and yet still store them side-by-side in the
 * same version control repository.
 */
if ( is_file( dirname( __FILE__ ) . '/wp-config-' . WP_ROOT_DOMAIN . '.php' ) ) {
	require( dirname( __FILE__ ) . '/wp-config-' . WP_ROOT_DOMAIN . '.php' );
}

/*
 * Set WP_CORE_PATH and WP_CONTENT_PATH to allow for different content path configurations
 */
if ( ! defined( 'WP_CORE_PATH' ) ) {
	define( 'WP_CORE_PATH', ! empty( $_ENV[ 'WP_CORE_PATH' ] ) ? '' : $_ENV[ 'WP_CORE_PATH' ] );
}
if ( ! defined( 'WP_CONTENT_PATH' ) ) {
	define( 'WP_CONTENT_PATH', ! empty( $_ENV[ 'WP_CONTENT_PATH' ] ) ? '/wp-content' : $_ENV[ 'WP_CONTENT_PATH' ] );
}

/*
 * Explicitly define the URL scheme.
 *
 * Defaults to https since that is today's best practice, but can be overriden is host and domain config paths
 */
if ( ! defined( 'WP_URL_SCHEME' ) ) {
	define( 'WP_URL_SCHEME', 'https' );
}

/*
 * Grab site domain in case $_SERVER[ 'HTTP_HOST' ] in case later changed.
 *
 * Examples might be `www.example.com`, `example.net`, `docs.example.org`, etc.
 */
if ( ! defined( 'WP_SITE_DOMAIN' ) ) {
	define( 'WP_SITE_DOMAIN', $_SERVER[ 'HTTP_HOST' ] );
}

/*
 * Allow a subdirectory install to be explicitly specified
 *
 * Examples might be `/blog`, `/wordpress`, etc.
 */
if ( ! defined( 'WP_SITE_SUBDIR' ) ) {
	define( 'WP_SITE_SUBDIR', '' );
}

/*
 * Go ahead and predefine these here. Why not?
 */
if ( ! defined( 'WP_HOME' ) ) {
	define( 'WP_HOME', empty( $_ENV[ 'WP_HOME' ] ) ? WP_URL_SCHEME . '://' . WP_SITE_DOMAIN : $_ENV[ 'WP_HOME' ] );
}
if ( ! defined( 'WP_SITEURL' ) ) {
	define( 'WP_SITEURL', empty( $_ENV[ 'WP_SITEURL' ] )
		? WP_URL_SCHEME . '://' . WP_SITE_DOMAIN . WP_SITE_SUBDIR . WP_CORE_PATH
		: $_ENV[ 'WP_SITEURL' ]
	);
}
if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	define( 'WP_CONTENT_DIR', empty( $_ENV[ 'WP_CONTENT_DIR' ] )
		? WP_ROOT_DIR . WP_SITE_SUBDIR . WP_CONTENT_PATH
		: $_ENV[ 'WP_CONTENT_DIR' ]
	);
}
if ( ! defined( 'WP_CONTENT_URL' ) ) {
	define( 'WP_CONTENT_URL', empty( $_ENV[ 'WP_CONTENT_DIR' ] )
		? WP_URL_SCHEME . '://' . WP_SITE_DOMAIN . WP_SITE_SUBDIR . WP_CORE_PATH
		: $_ENV[ 'WP_CONTENT_DIR' ]
	);
}

/*
 * Configure the database credentials.
 *
 * These can be set by the host in "/wp-config-{WP_WEBHOST}.php"
 * or by the developer in "/wp-config-{WP_ROOT_DOMAIN}.php" or
 * set by the webhost to default from $_ENV, or finally default
 * to some "obvious" choices.
 *
 */
if ( ! defined( 'DB_NAME' ) ) {
	define( 'DB_NAME', isset( $_ENV[ 'DB_NAME' ] ) ? $_ENV[ 'DB_NAME' ] : 'wordpress' );
}
if ( ! defined( 'DB_USER' ) ) {
	define( 'DB_USER', isset( $_ENV[ 'DB_USER' ] ) ? $_ENV[ 'DB_USER' ] : 'wordpress' );
}
if ( ! defined( 'DB_PASSWORD' ) ) {
	define( 'DB_PASSWORD', isset( $_ENV[ 'DB_PASSWORD' ] ) ? $_ENV[ 'DB_PASSWORD' ] : 'wordpress' );
}
if ( ! defined( 'DB_HOST' ) ) {
	define( 'DB_HOST', isset( $_ENV[ 'DB_HOST' ] ) ? $_ENV[ 'DB_HOST' ] : 'localhost' );
}
if ( ! defined( 'DB_CHARSET' ) ) {
	define( 'DB_CHARSET', isset( $_ENV[ 'DB_CHARSET' ] ) ? $_ENV[ 'DB_CHARSET' ] : 'utf8' );
}
if ( ! defined( 'DB_COLLATE' ) ) {
	define( 'DB_COLLATE', isset( $_ENV[ 'DB_COLLATE' ] ) ? $_ENV[ 'DB_COLLATE' ] : '' );
}

/*
 * Configure the optional "switches" in WordPress core.
 * @TODO Add most (all?) other optional constants defined in CORE here.
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

if ( ! defined( 'WP_SCRIPT_DEBUG' ) ) {
	define( 'WP_SCRIPT_DEBUG', false );
}

if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', false );
}

/*
 * Set the default table prefix is not already set
 */
if ( ! isset( $table_prefix ) ) {
	$table_prefix = 'wp_';
}

/**
 * Search for a wp-salt-{HTTP_HOST}.php and if not found
 * then wp-salt.php and if not found then define insecure keys.
 *
 * Random Salt Generator:
 * https://api.wordpress.org/secret-key/1.1/salt/
 */
if ( is_file( dirname( __FILE__ ) . '/wp-salt-' . WP_ROOT_DOMAIN . '.php' ) ) {
	require( dirname( __FILE__ ) . '/wp-salt-' . WP_ROOT_DOMAIN . '.php' );
} if ( is_file( dirname( __FILE__ ) . '/wp-salt.php' ) ) {
	require( dirname( __FILE__ ) . '/wp-salt.php' );
} else {
	define( 'AUTH_KEY',         'Insecure' );
	define( 'SECURE_AUTH_KEY',  'Insecure' );
	define( 'LOGGED_IN_KEY',    'Insecure' );
	define( 'NONCE_KEY',        'Insecure' );
	define( 'AUTH_SALT',        'Insecure' );
	define( 'SECURE_AUTH_SALT', 'Insecure' );
	define( 'LOGGED_IN_SALT',   'Insecure' );
	define( 'NONCE_SALT',       'Insecure' );
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . WP_CORE_PATH . '/' );
}

require_once( ABSPATH . 'wp-settings.php' );
