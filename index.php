<?php
/**
 * This is the standard index.php for when using Better WP-Config.
 *
 * Replace the index.php for your WordPress installation with this one.
 *
 * @see https://github.com/wplib/better-wp-config
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/**
 * This loads configuration using Better WP Config
 */
require( __DIR__ . '/better-wp-config.php' );

/** This loads the WordPress Environment and Template */
require( wp_config()->dirs->core . '/wp-blog-header.php' );

