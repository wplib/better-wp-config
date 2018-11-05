<?php

/**
 * This is the standard wp-config for when using Better WP-Config.
 *
 * Nothing more is needed.
 *
 * @see https://github.com/wplib/better-wp-config
 *
 */

if ( ! class_exists( 'WP_Config', false ) ) {
	require( __DIR__ . '/better-wp-config.php' );
}

require_once( wp_config()->dirs->core . 'wp-settings.php' );