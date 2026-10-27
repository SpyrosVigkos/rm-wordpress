<?php
/**
 * Every setting comes from the environment, so the same image runs locally
 * and in Kubernetes. A variable NAME can also be given as NAME_FILE, the path
 * of a file holding the value (mounted secrets). See docker/README.md.
 */

function rm_env( $name, $default = null ) {
    $value = getenv( $name );
    if ( $value !== false && $value !== '' ) {
        return $value;
    }
    $file = getenv( $name . '_FILE' );
    if ( $file !== false && $file !== '' && is_readable( $file ) ) {
        return rtrim( file_get_contents( $file ), "\r\n" );
    }
    return $default;
}

function rm_env_required( $name ) {
    $value = rm_env( $name );
    if ( $value === null ) {
        error_log( "wp-config: $name is not set" );
        http_response_code( 503 );
        exit( 'This site is not configured.' );
    }
    return $value;
}

define( 'DB_NAME', rm_env( 'WORDPRESS_DB_NAME', 'wordpress' ) );
define( 'DB_USER', rm_env( 'WORDPRESS_DB_USER', 'wordpress' ) );
define( 'DB_PASSWORD', rm_env_required( 'WORDPRESS_DB_PASSWORD' ) );
define( 'DB_HOST', rm_env( 'WORDPRESS_DB_HOST', 'db' ) );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );
$table_prefix = rm_env( 'WORDPRESS_TABLE_PREFIX', 'wp_' );

foreach ( array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' ) as $key ) {
    define( $key, rm_env_required( 'WORDPRESS_' . $key ) );
}

// The public address of this WordPress, e.g. https://cms.reelmetrics.com.
$rm_home = rm_env( 'WP_HOME' );
if ( $rm_home !== null ) {
    define( 'WP_HOME', rtrim( $rm_home, '/' ) );
    define( 'WP_SITEURL', rtrim( $rm_home, '/' ) );
}

// TLS ends at the ingress.
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && strpos( $_SERVER['HTTP_X_FORWARDED_PROTO'], 'https' ) !== false ) {
    $_SERVER['HTTPS'] = 'on';
}

define( 'WP_ENVIRONMENT_TYPE', rm_env( 'WP_ENVIRONMENT_TYPE', 'production' ) );
define( 'WP_DEBUG', rm_env( 'WORDPRESS_DEBUG' ) === '1' );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG_LOG', '/dev/stderr' );

// Code only changes with a new image: no installs, updates or editing from wp-admin.
define( 'DISALLOW_FILE_MODS', true );
define( 'DISALLOW_FILE_EDIT', true );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );

define( 'WP_POST_REVISIONS', (int) rm_env( 'WP_POST_REVISIONS', '20' ) );

// The Astro preview server; see the rm-headless plugin.
$rm_preview = rm_env( 'RM_PREVIEW_URL' );
if ( $rm_preview !== null ) {
    define( 'RM_PREVIEW_URL', $rm_preview );
}

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
