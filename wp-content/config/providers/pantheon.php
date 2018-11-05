<?php
/**
 * This is configured for use with Pantheon.
 *
 * These settings were derived from Pantheon's WordPress upstream on GitHub:
 *
 *      https://github.com/pantheon-systems/WordPress/blob/default/wp-config.php
 *
 */

$is_https = isset( $_SERVER['HTTP_USER_AGENT_HTTPS'] )
       && 'ON' === $_SERVER['HTTP_USER_AGENT_HTTPS'];
if ( $is_https ) {
	$_SERVER['HTTPS'] = 'on';
}
return array(
	'db[host]'               => "{$_ENV[ 'DB_HOST' ]}:{$_ENV['DB_PORT']}",
	'db[name]'               => $_ENV[ 'DB_NAME' ],
	'db[user]'               => $_ENV[ 'DB_USER' ],
	'db[pass]'               => $_ENV[ 'DB_PASSWORD' ],
	'salt[auth_key]'         => $_ENV[ 'AUTH_KEY' ],
	'salt[secure_auth_key]'  => $_ENV[ 'SECURE_AUTH_KEY' ],
	'salt[logged_in_key]'    => $_ENV[ 'LOGGED_IN_KEY' ],
	'salt[nonce_key]'        => $_ENV[ 'NONCE_KEY' ],
	'salt[auth_salt]'        => $_ENV[ 'AUTH_SALT' ],
	'salt[secure_auth_salt]' => $_ENV[ 'SECURE_AUTH_SALT' ],
	'salt[logged_in_salt]'   => $_ENV[ 'LOGGED_IN_SALT' ],
	'salt[nonce_salt]'       => $_ENV[ 'NONCE_SALT' ],
	'error[reporting]'       => E_ALL ^ E_DEPRECATED,
	'environment[scheme]'    => $is_https ? 'https' : 'https',
	'disallow[file_mods]'    => in_array( $_ENV[ 'PANTHEON_ENVIRONMENT' ], array( 'test', 'live' ) ),
	'defines'                => array(
		'WP_TEMP_DIR' => "{$_SERVER[ 'HOME' ]}/tmp",
	)
);



















