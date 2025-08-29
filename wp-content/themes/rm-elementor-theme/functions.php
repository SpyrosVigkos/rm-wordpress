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
define( 'RM_THEME_VERSION', '1.1.6' );
define( 'RM_THEME_DIR', get_stylesheet_directory() );
define( 'RM_THEME_URI', get_stylesheet_directory_uri() );

// (Importer removed) — ButterCMS endpoint no longer needed

/**
 * Enqueue parent and child theme styles
 */
function rm_child_enqueue_styles() {
    // Parent theme style
    wp_enqueue_style( 'hello-elementor', get_template_directory_uri() . '/style.css' );
    
    // Child theme style
    wp_enqueue_style( 'rm-child-style', get_stylesheet_uri(), array( 'hello-elementor' ), RM_THEME_VERSION );
    
    // ReelMetrics Design System
    wp_enqueue_style( 'rm-colors', RM_THEME_URI . '/assets/css/colors.css', array(), RM_THEME_VERSION );
    wp_enqueue_style( 'rm-fonts', RM_THEME_URI . '/assets/css/fonts.css', array(), RM_THEME_VERSION );
    wp_enqueue_style( 'rm-typography', RM_THEME_URI . '/assets/css/typography.css', array( 'rm-colors' ), RM_THEME_VERSION );
    wp_enqueue_style( 'rm-buttons', RM_THEME_URI . '/assets/css/buttons.css', array( 'rm-colors', 'rm-typography' ), RM_THEME_VERSION );
    wp_enqueue_style( 'rm-navigation', RM_THEME_URI . '/assets/css/navigation.css', array( 'rm-colors', 'rm-typography' ), RM_THEME_VERSION );
    
    // Theme scripts
    wp_enqueue_script( 'rm-theme-script', RM_THEME_URI . '/assets/js/main.js', array( 'jquery' ), RM_THEME_VERSION, true );
    
    // Widget styles and scripts (conditional loading)
    if ( is_page() || is_single() || is_front_page() ) {
        // Accordion widget
        wp_enqueue_style( 'rm-accordion-style', RM_THEME_URI . '/assets/css/accordion.css', array( 'rm-colors', 'rm-typography' ), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/css/accordion.css' ) );
        wp_enqueue_script( 'rm-accordion-script', RM_THEME_URI . '/assets/js/accordion.js', array( 'jquery' ), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/js/accordion.js' ), true );
        
        // Timeline widget
        wp_enqueue_style( 'rm-timeline-style', RM_THEME_URI . '/assets/css/timeline.css', array( 'rm-colors', 'rm-typography' ), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/css/timeline.css' ) );
        wp_enqueue_script( 'rm-timeline-script', RM_THEME_URI . '/assets/js/timeline.js', array( 'jquery' ), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/js/timeline.js' ), true );

        // Team widget
        if ( file_exists( RM_THEME_DIR . '/assets/css/team.css' ) ) {
            wp_enqueue_style( 'rm-team-style', RM_THEME_URI . '/assets/css/team.css', array( 'rm-colors', 'rm-typography' ), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/css/team.css' ) );
        }

        // Carousel widget
        if ( file_exists( RM_THEME_DIR . '/assets/css/carousel.css' ) ) {
            wp_enqueue_style( 'rm-carousel-style', RM_THEME_URI . '/assets/css/carousel.css', array( 'rm-colors', 'rm-typography' ), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/css/carousel.css' ) );
        }
        if ( file_exists( RM_THEME_DIR . '/assets/js/carousel.js' ) ) {
            wp_enqueue_script( 'rm-carousel-script', RM_THEME_URI . '/assets/js/carousel.js', array(), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/js/carousel.js' ), true );
        }
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
                font-family: var(--rm-font-primary, 'Work Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif);
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

/**
 * Register CPT: Team Member (CPT UI is installed, but we ensure it's registered programmatically too)
 */
function rm_register_team_member_cpt() {
    $labels = array(
        'name'               => 'Team Members',
        'singular_name'      => 'Team Member',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Team Member',
        'edit_item'          => 'Edit Team Member',
        'new_item'           => 'New Team Member',
        'view_item'          => 'View Team Member',
        'search_items'       => 'Search Team Members',
        'not_found'          => 'No team members found',
        'not_found_in_trash' => 'No team members found in Trash',
        'menu_name'          => 'Team Members',
    );
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'menu_icon'          => 'dashicons-groups',
        'has_archive'        => false,
        'rewrite'            => array( 'slug' => 'team' ),
    );
    register_post_type( 'rm_team_member', $args );
}
add_action( 'init', 'rm_register_team_member_cpt' );

/**
 * Ensure ACF fields exist (name, role, dob, photo). If ACF JSON/GUI exists, this no-ops.
 */
function rm_register_team_member_acf() {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group(array(
            'key' => 'group_rm_team_member',
            'title' => 'Team Member',
            'fields' => array(
                array(
                    'key' => 'field_rm_tm_role',
                    'label' => 'Role',
                    'name' => 'role',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_rm_tm_dob',
                    'label' => 'Date of Birth',
                    'name' => 'dob',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_rm_tm_photo',
                    'label' => 'Photo',
                    'name' => 'photo',
                    'type' => 'image',
                    'return_format' => 'id',
                    'preview_size' => 'medium',
                ),
                array(
                    'key' => 'field_rm_tm_butter_id',
                    'label' => 'ButterCMS ID',
                    'name' => 'butter_id',
                    'type' => 'number',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'rm_team_member',
                    ),
                ),
            )
        ));
    }
}
add_action( 'acf/init', 'rm_register_team_member_acf' );

// Importer removed per request (CPT and ACF fields kept)

