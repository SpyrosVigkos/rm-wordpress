<?php
/**
 * ReelMetrics Child Theme Functions
 * Child theme of Hello Elementor
 *
 * @package RMElementorTheme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define theme constants
define( 'RM_THEME_VERSION', '1.0.1' );
define( 'RM_THEME_DIR', get_stylesheet_directory() );
define( 'RM_THEME_URI', get_stylesheet_directory_uri() );

/**
 * Enqueue parent and child theme styles
 */
function rm_child_enqueue_styles() {
    // Parent theme style
    wp_enqueue_style( 'hello-elementor', get_template_directory_uri() . '/style.css' );
    
    // Child theme style
    wp_enqueue_style( 'rm-child-style', get_stylesheet_uri(), array( 'hello-elementor' ), RM_THEME_VERSION );
    
    // Fonts (loaded before typography to ensure availability)
    wp_enqueue_style( 'rm-fonts', RM_THEME_URI . '/assets/css/fonts.css', array(), RM_THEME_VERSION );

    // ReelMetrics Design System
    wp_enqueue_style( 'rm-colors', RM_THEME_URI . '/assets/css/colors.css', array(), RM_THEME_VERSION );
    wp_enqueue_style( 'rm-typography', RM_THEME_URI . '/assets/css/typography.css', array( 'rm-fonts', 'rm-colors' ), RM_THEME_VERSION );
    wp_enqueue_style( 'rm-buttons', RM_THEME_URI . '/assets/css/buttons.css', array( 'rm-colors', 'rm-typography' ), RM_THEME_VERSION );
    wp_enqueue_style( 'rm-navigation', RM_THEME_URI . '/assets/css/navigation.css', array( 'rm-colors', 'rm-typography' ), RM_THEME_VERSION );
    
    // Theme scripts
    wp_enqueue_script( 'rm-theme-script', RM_THEME_URI . '/assets/js/main.js', array( 'jquery' ), RM_THEME_VERSION, true );
    
    // Accordion widget styles and scripts (conditional loading)
    if ( is_page() || is_single() || is_front_page() ) {
        wp_enqueue_style( 'rm-accordion-style', RM_THEME_URI . '/assets/css/accordion.css', array( 'rm-colors', 'rm-typography' ), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/css/accordion.css' ) );
        wp_enqueue_script( 'rm-accordion-script', RM_THEME_URI . '/assets/js/accordion.js', array( 'jquery' ), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/js/accordion.js' ), true );
    }
}
add_action( 'wp_enqueue_scripts', 'rm_child_enqueue_styles' );

/**
 * Theme setup - extends Hello Elementor
 */
function rm_child_theme_setup() {
    // Load text domain for translations
    load_child_theme_textdomain( 'rm-elementor-theme', RM_THEME_DIR . '/languages' );
    
    // Add additional theme support
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 350,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    
    // Register additional menus if needed
    register_nav_menus( array(
        'footer' => __( 'Footer Menu', 'rm-elementor-theme' ),
    ) );
}
add_action( 'after_setup_theme', 'rm_child_theme_setup' );

/**
 * Load Elementor functionality
 */
function rm_init_elementor_support() {
    // Check if Elementor is loaded
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'rm_elementor_missing_notice' );
        return;
    }
    
    // Wait for Elementor to initialize
    add_action( 'elementor/init', 'rm_elementor_init' );
}
add_action( 'after_setup_theme', 'rm_init_elementor_support', 20 );

/**
 * Initialize Elementor integration
 */
function rm_elementor_init() {
    // Load Elementor extensions
    require_once RM_THEME_DIR . '/includes/elementor-global-settings.php';
    require_once RM_THEME_DIR . '/includes/elementor-integration.php';
    require_once RM_THEME_DIR . '/includes/elementor-widgets.php';
}

/**
 * Admin notice for missing Elementor
 */
function rm_elementor_missing_notice() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }
    
    $message = sprintf(
        /* translators: 1: Theme name 2: Elementor plugin name */
        esc_html__( '%1$s requires %2$s plugin to be installed and activated for full functionality.', 'rm-elementor-theme' ),
        '<strong>' . wp_get_theme()->get( 'Name' ) . '</strong>',
        '<strong>' . esc_html__( 'Elementor Page Builder', 'rm-elementor-theme' ) . '</strong>'
    );
    
    printf( '<div class="notice notice-warning"><p>%s</p></div>', $message );
}

/**
 * Check parent theme
 */
function rm_check_parent_theme() {
    $theme = wp_get_theme();
    $parent = $theme->parent();
    
    if ( ! $parent || 'Hello Elementor' !== $parent->get( 'Name' ) ) {
        add_action( 'admin_notices', 'rm_parent_theme_notice' );
    }
}
add_action( 'after_setup_theme', 'rm_check_parent_theme' );

/**
 * Admin notice for missing parent theme
 */
function rm_parent_theme_notice() {
    if ( ! current_user_can( 'install_themes' ) ) {
        return;
    }
    
    $message = sprintf(
        /* translators: 1: Child theme name 2: Parent theme name */
        esc_html__( '%1$s requires %2$s parent theme to be installed.', 'rm-elementor-theme' ),
        '<strong>' . wp_get_theme()->get( 'Name' ) . '</strong>',
        '<strong>Hello Elementor</strong>'
    );
    
    printf( '<div class="notice notice-error"><p>%s</p></div>', $message );
}

/**
 * Custom body classes
 */
function rm_body_classes( $classes ) {
    $classes[] = 'rm-theme';
    $classes[] = 'rm-hello-child';
    
    return $classes;
}
add_filter( 'body_class', 'rm_body_classes' );

/**
 * Override Hello Elementor's typography if needed
 */
function rm_customize_hello_settings() {
    // Add custom CSS to override Hello's defaults with ReelMetrics design system
    add_action( 'wp_head', function() {
        ?>
        <style>
            /* Override Hello Elementor defaults with ReelMetrics design system */
            body {
                font-family: var(--rm-font-primary, 'Helvetica Neue', Helvetica, Arial, sans-serif);
            }
            
            /* Apply ReelMetrics colors globally */
            .elementor-section.elementor-section-boxed > .elementor-container {
                max-width: 1225px;
            }
        </style>
        <?php
    }, 100 );
}
add_action( 'init', 'rm_customize_hello_settings' );

/**
 * Force Elementor to regenerate CSS (temporary - remove after cache is cleared)
 */
function rm_force_elementor_css_regeneration() {
    if ( class_exists( '\Elementor\Plugin' ) ) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
    }
}
// Uncomment the line below temporarily to force CSS regeneration, then comment it back
// add_action( 'init', 'rm_force_elementor_css_regeneration' );