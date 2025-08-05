<?php
/**
 * RM Elementor Theme Functions
 *
 * @package RMElementorTheme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define theme constants
define( 'RM_THEME_VERSION', '1.0.0' );
define( 'RM_THEME_DIR', get_template_directory() );
define( 'RM_THEME_URI', get_template_directory_uri() );

/**
 * Theme setup
 */
function rm_theme_setup() {
    // Add theme support
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
    
    // Register navigation menus
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'rm-elementor-theme' ),
        'footer'  => __( 'Footer Menu', 'rm-elementor-theme' ),
    ) );
}
add_action( 'after_setup_theme', 'rm_theme_setup' );

/**
 * Enqueue scripts and styles
 */
function rm_theme_scripts() {
    // Theme stylesheet
    wp_enqueue_style( 'rm-theme-style', get_stylesheet_uri(), array(), RM_THEME_VERSION );
    
    // ReelMetrics Design System
    wp_enqueue_style( 'rm-colors', RM_THEME_URI . '/assets/css/colors.css', array(), RM_THEME_VERSION );
    wp_enqueue_style( 'rm-typography', RM_THEME_URI . '/assets/css/typography.css', array( 'rm-colors' ), RM_THEME_VERSION );
    wp_enqueue_style( 'rm-buttons', RM_THEME_URI . '/assets/css/buttons.css', array( 'rm-colors', 'rm-typography' ), RM_THEME_VERSION );
    
    // Theme scripts
    wp_enqueue_script( 'rm-theme-script', RM_THEME_URI . '/assets/js/main.js', array( 'jquery' ), RM_THEME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'rm_theme_scripts' );

/**
 * Register widget areas
 */
function rm_theme_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'rm-elementor-theme' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Add widgets here.', 'rm-elementor-theme' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'rm_theme_widgets_init' );

// Include custom Elementor functionality
if ( defined( 'ELEMENTOR_VERSION' ) ) {
    require_once RM_THEME_DIR . '/includes/elementor-integration.php';
    require_once RM_THEME_DIR . '/includes/elementor-widgets.php';
}