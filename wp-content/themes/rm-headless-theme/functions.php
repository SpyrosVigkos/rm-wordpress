<?php
/**
 * Featured images stay editable (and in REST) without the Elementor theme.
 *
 * @package RMHeadlessTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'after_setup_theme', function () {
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
} );
