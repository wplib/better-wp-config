<?php
/**
 * This is configured for use with the WPLib Box local development solution for WordPress.
 *
 * @see https://github.com/wplib/wplib-box
 *
 */
return array(
	'db[name]'    => $_ENV[ 'DB_NAME' ],
	'db[user]'    => $_ENV[ 'DB_USER' ],
	'db[pass]'    => $_ENV[ 'DB_PASSWORD' ],
	'db[host]'    => $_ENV[ 'DB_HOST' ],
	'db[charset]' => $_ENV[ 'DB_CHARSET' ],
	'db[collate]' => $_ENV[ 'DB_COLLATE' ],
);

