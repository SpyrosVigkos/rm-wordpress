<?php
/**
 * Plugin Name: RM Headless
 * Description: ReelMetrics content model for the Astro site: post types, taxonomies, ACF field groups and REST fields. Works with any theme.
 * Version: 0.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Author: ReelMetrics
 * License: MIT
 * Text Domain: rm-headless
 *
 * @package RMHeadless
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'RM_HEADLESS_VERSION', '0.1.0' );
define( 'RM_HEADLESS_DIR', __DIR__ );

/**
 * The Elementor theme (rm-elementor-theme) still registers the same post types
 * and field groups. Loading both would register everything twice, so the plugin
 * stays passive while that theme is active. Plugins load before the theme, so
 * the check waits for after_setup_theme; init has not fired yet at that point.
 */
function rm_headless_load() {
    if ( defined( 'RM_THEME_VERSION' ) ) {
        add_action( 'admin_notices', 'rm_headless_theme_notice' );
        return;
    }

    $parts = array(
        'team-member',
        'reelcast',
        'reelhot',
        'buying-guide',
        'texts',
        'cpt-notification',
        'cpt-comparison',
        'acf-comparison',
        'admin-rm-auth',
        'rm-auth-rest',
        'preview',
    );
    foreach ( $parts as $part ) {
        require_once RM_HEADLESS_DIR . '/includes/' . $part . '.php';
    }

    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        add_action( 'admin_notices', 'rm_headless_acf_notice' );
    }
}
add_action( 'after_setup_theme', 'rm_headless_load', 0 );

function rm_headless_theme_notice() {
    echo '<div class="notice notice-info"><p>RM Headless is inactive: the ReelMetrics Elementor theme already registers the content model.</p></div>';
}

function rm_headless_acf_notice() {
    echo '<div class="notice notice-error"><p>RM Headless needs Advanced Custom Fields for the episode, ReelHot and text fields.</p></div>';
}
