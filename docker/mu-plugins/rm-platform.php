<?php
/**
 * Plugin Name: RM Platform
 * Description: Keeps the image's plugins and theme active, whatever the database says, and turns off what a content-only WordPress does not need.
 *
 * @package RMPlatform
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const RM_PLATFORM_PLUGINS = array(
    'secure-custom-fields/secure-custom-fields.php',
    'rm-headless/rm-headless.php',
);
const RM_PLATFORM_THEME   = 'rm-headless-theme';

// A database copied from another install (the migration) may list other
// plugins or another theme; only what the image ships with is loaded.
add_filter( 'option_active_plugins', function ( $active ) {
    $shipped = array_filter( (array) $active, function ( $plugin ) {
        return is_file( WP_PLUGIN_DIR . '/' . $plugin );
    } );
    return array_values( array_unique( array_merge( RM_PLATFORM_PLUGINS, $shipped ) ) );
} );
add_filter( 'pre_option_template', function () {
    return RM_PLATFORM_THEME;
} );
add_filter( 'pre_option_stylesheet', function () {
    return RM_PLATFORM_THEME;
} );

add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'pings_open', '__return_false' );
add_filter( 'comments_open', '__return_false' );
