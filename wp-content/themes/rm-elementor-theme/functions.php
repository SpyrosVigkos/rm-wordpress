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

        // Comparison table widget styles
        if ( file_exists( RM_THEME_DIR . '/assets/css/comparison-table.css' ) ) {
            wp_enqueue_style( 'rm-comparison-table', RM_THEME_URI . '/assets/css/comparison-table.css', array( 'rm-colors', 'rm-typography' ), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/css/comparison-table.css' ) );
        }

        // Okta login scripts (registered, enqueued on demand by widget)
        wp_register_script( 'okta-auth-js', 'https://global.oktacdn.com/okta-auth-js/6.9.0/okta-auth-js.min.js', array(), '6.9.0', true );
        if ( file_exists( RM_THEME_DIR . '/assets/js/rm-okta-login.js' ) ) {
            wp_register_script( 'rm-okta-login', RM_THEME_URI . '/assets/js/rm-okta-login.js', array( 'okta-auth-js' ), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/js/rm-okta-login.js' ), true );
            // Localize config for convenience
            $opt = function_exists('rm_okta_get_option') ? rm_okta_get_option() : get_option('rm_okta_auth');
            if ( is_array($opt) ) {
                $payload = array(
                    'oidc_base_url' => $opt['base_url'] ?? '',
                    'oidc_issuer' => $opt['issuer_id'] ?? 'default',
                    'oidc_identifier' => $opt['client_id'] ?? '',
                    'oidc_redirect_uri' => $opt['redirect_uri'] ?? '',
                    'oidc_forgot_password_url' => $opt['forgot_password_url'] ?? '',
                );
                wp_localize_script( 'rm-okta-login', 'RM_OKTA', $payload );
            }
        }

        // Okta login styles (loaded when widget requests)
        if ( file_exists( RM_THEME_DIR . '/assets/css/okta-login.css' ) ) {
            wp_register_style( 'rm-okta-login', RM_THEME_URI . '/assets/css/okta-login.css', array(), RM_THEME_VERSION . '-' . filemtime( RM_THEME_DIR . '/assets/css/okta-login.css' ) );
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

// Register Notification CPT and Admin Migration Tool
require_once RM_THEME_DIR . '/includes/cpt-notification.php';
require_once RM_THEME_DIR . '/includes/admin-notification-migration.php';
// Comparison CPT and ACF registration
require_once RM_THEME_DIR . '/includes/cpt-comparison.php';
require_once RM_THEME_DIR . '/includes/acf-comparison.php';
// RM Auth settings page and REST
require_once RM_THEME_DIR . '/includes/admin-rm-auth.php';
require_once RM_THEME_DIR . '/includes/rm-auth-rest.php';

/**
 * Register CPT: ReelCast Episodes
 * Custom Post Type for podcast episodes
 */
function rm_register_reelcast_cpt() {
    $labels = array(
        'name'               => 'ReelCast Episodes',
        'singular_name'      => 'ReelCast Episode',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New ReelCast Episode',
        'edit_item'          => 'Edit ReelCast Episode',
        'new_item'           => 'New ReelCast Episode',
        'view_item'          => 'View ReelCast Episode',
        'search_items'       => 'Search ReelCast Episodes',
        'not_found'          => 'No episodes found',
        'not_found_in_trash' => 'No episodes found in Trash',
        'menu_name'          => 'ReelCast',
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ),
        'menu_icon'          => 'dashicons-microphone',
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'reelcast' ),
        'hierarchical'       => false,
        'query_var'          => true,
        'can_export'         => true,
        'delete_with_user'   => false,
        'capability_type'    => 'post',
    );
    
    register_post_type( 'reelcast', $args );
}
add_action( 'init', 'rm_register_reelcast_cpt' );

/**
 * Register dedicated taxonomies for ReelCast
 * - reelcast_category (hierarchical)
 * - reelcast_tag (non-hierarchical)
 */
function rm_register_reelcast_taxonomies() {
    // Categories
    $cat_labels = array(
        'name'              => _x( 'ReelCast Categories', 'taxonomy general name', 'rm-elementor-theme' ),
        'singular_name'     => _x( 'ReelCast Category', 'taxonomy singular name', 'rm-elementor-theme' ),
        'search_items'      => __( 'Search Categories', 'rm-elementor-theme' ),
        'all_items'         => __( 'All Categories', 'rm-elementor-theme' ),
        'parent_item'       => __( 'Parent Category', 'rm-elementor-theme' ),
        'parent_item_colon' => __( 'Parent Category:', 'rm-elementor-theme' ),
        'edit_item'         => __( 'Edit Category', 'rm-elementor-theme' ),
        'update_item'       => __( 'Update Category', 'rm-elementor-theme' ),
        'add_new_item'      => __( 'Add New Category', 'rm-elementor-theme' ),
        'new_item_name'     => __( 'New Category Name', 'rm-elementor-theme' ),
        'menu_name'         => __( 'Categories', 'rm-elementor-theme' ),
    );

    register_taxonomy( 'reelcast_category', array( 'reelcast' ), array(
        'hierarchical'      => true,
        'labels'            => $cat_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'reelcast/category' ),
        'show_in_rest'      => true,
    ) );

    // Tags
    $tag_labels = array(
        'name'                       => _x( 'ReelCast Tags', 'taxonomy general name', 'rm-elementor-theme' ),
        'singular_name'              => _x( 'ReelCast Tag', 'taxonomy singular name', 'rm-elementor-theme' ),
        'search_items'               => __( 'Search Tags', 'rm-elementor-theme' ),
        'popular_items'              => __( 'Popular Tags', 'rm-elementor-theme' ),
        'all_items'                  => __( 'All Tags', 'rm-elementor-theme' ),
        'edit_item'                  => __( 'Edit Tag', 'rm-elementor-theme' ),
        'update_item'                => __( 'Update Tag', 'rm-elementor-theme' ),
        'add_new_item'               => __( 'Add New Tag', 'rm-elementor-theme' ),
        'new_item_name'              => __( 'New Tag Name', 'rm-elementor-theme' ),
        'separate_items_with_commas' => __( 'Separate tags with commas', 'rm-elementor-theme' ),
        'add_or_remove_items'        => __( 'Add or remove tags', 'rm-elementor-theme' ),
        'choose_from_most_used'      => __( 'Choose from the most used tags', 'rm-elementor-theme' ),
        'menu_name'                  => __( 'Tags', 'rm-elementor-theme' ),
    );

    register_taxonomy( 'reelcast_tag', array( 'reelcast' ), array(
        'hierarchical'      => false,
        'labels'            => $tag_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'reelcast/tag' ),
        'show_in_rest'      => true,
    ) );

    // Ensure taxonomies are attached to post type (in case of registration order differences)
    register_taxonomy_for_object_type( 'reelcast_category', 'reelcast' );
    register_taxonomy_for_object_type( 'reelcast_tag', 'reelcast' );
}
add_action( 'init', 'rm_register_reelcast_taxonomies', 11 );
/**
 * Register ACF fields for ReelCast Episodes
 * Mirrors the React component props structure from rm-public-pages
 */
function rm_register_reelcast_acf() {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group(array(
            'key' => 'group_reelcast_episode',
            'title' => 'ReelCast Episode Fields',
            'fields' => array(
                // Basic episode information
                array(
                    'key' => 'field_reelcast_subtitle',
                    'label' => 'Subtitle',
                    'name' => 'subtitle',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'Season X, Episode Y',
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_reelcast_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'required' => 1,
                    'rows' => 5,
                    'new_lines' => 'br',
                    'instructions' => 'Episode description for display and SEO',
                ),
                array(
                    'key' => 'field_reelcast_date_published',
                    'label' => 'Date Published',
                    'name' => 'date_published',
                    'type' => 'date_picker',
                    'required' => 1,
                    'display_format' => 'Y-m-d',
                    'return_format' => 'Y-m-d',
                    'first_day' => 1,
                    'wrapper' => array( 'width' => '33' ),
                ),
                array(
                    'key' => 'field_reelcast_season_number',
                    'label' => 'Season Number',
                    'name' => 'season_number',
                    'type' => 'number',
                    'required' => 1,
                    'min' => 1,
                    'wrapper' => array( 'width' => '33' ),
                ),
                array(
                    'key' => 'field_reelcast_episode_number',
                    'label' => 'Episode Number',
                    'name' => 'episode_number',
                    'type' => 'number',
                    'required' => 1,
                    'min' => 1,
                    'wrapper' => array( 'width' => '34' ),
                ),
                
                // Buzzsprout integration
                array(
                    'key' => 'field_reelcast_buzzsprout_id',
                    'label' => 'Buzzsprout Episode ID',
                    'name' => 'buzzsprout_id',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => '14577542-s03e02-delaware-north-with-michael-carruthers',
                    'instructions' => 'Format: digits-episode-slug (used for player embed)',
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_reelcast_buzzsprout_url',
                    'label' => 'Buzzsprout URL',
                    'name' => 'buzzsprout_url',
                    'type' => 'url',
                    'placeholder' => 'https://www.buzzsprout.com/2057836/14577542-...',
                    'wrapper' => array( 'width' => '50' ),
                ),
                
                // Platform URLs
                array(
                    'key' => 'field_reelcast_spotify_url',
                    'label' => 'Spotify URL',
                    'name' => 'spotify_url',
                    'type' => 'url',
                    'placeholder' => 'https://open.spotify.com/show/314iBnXRGGAuttAsIcZeC5',
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_reelcast_apple_podcasts_url',
                    'label' => 'Apple Podcasts URL',
                    'name' => 'apple_podcasts_url',
                    'type' => 'url',
                    'placeholder' => 'https://podcasts.apple.com/us/podcast/reelcast-by-reelmetrics/id1652485625',
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_reelcast_iheart_url',
                    'label' => 'iHeartRadio URL',
                    'name' => 'iheart_url',
                    'type' => 'url',
                    'placeholder' => 'https://iheart.com/podcast/104029260/',
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_reelcast_cdc_url',
                    'label' => 'CDC Gaming URL',
                    'name' => 'cdc_url',
                    'type' => 'url',
                    'placeholder' => 'https://cdcgaming.com/reelmetrics/s03e02-...',
                    'wrapper' => array( 'width' => '50' ),
                ),
                
                // Topics (repeater)
                array(
                    'key' => 'field_reelcast_topics',
                    'label' => 'Topics',
                    'name' => 'topics',
                    'type' => 'repeater',
                    'button_label' => 'Add Topic',
                    'layout' => 'table',
                    'instructions' => 'Key discussion topics covered in this episode',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_reelcast_topic',
                            'label' => 'Topic',
                            'name' => 'topic',
                            'type' => 'text',
                            'required' => 1,
                        ),
                    ),
                ),
                
                // Keywords (repeater)
                array(
                    'key' => 'field_reelcast_keywords',
                    'label' => 'Keywords',
                    'name' => 'keywords',
                    'type' => 'repeater',
                    'button_label' => 'Add Keyword',
                    'layout' => 'table',
                    'instructions' => 'SEO keywords and tags for this episode',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_reelcast_keyword',
                            'label' => 'Keyword',
                            'name' => 'keyword',
                            'type' => 'text',
                            'required' => 1,
                        ),
                    ),
                ),
                
                // Transcript
                array(
                    'key' => 'field_reelcast_transcript',
                    'label' => 'Transcript',
                    'name' => 'transcript',
                    'type' => 'textarea',
                    'rows' => 20,
                    'new_lines' => 'wpautop',
                    'instructions' => 'Full episode transcript. Speaker names followed by colons will be automatically formatted.',
                ),
                
                // Optional social image override
                array(
                    'key' => 'field_reelcast_social_image',
                    'label' => 'Social Image (Optional)',
                    'name' => 'social_image',
                    'type' => 'image',
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'instructions' => 'Override default ReelCast social image for this episode',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'reelcast',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'acf_after_title',
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'active' => true,
            'show_in_rest' => 1,
        ));
    }
}
add_action( 'acf/init', 'rm_register_reelcast_acf' );

/**
 * Shortcode for Buzzsprout player embed
 * Usage: [reelcast_player episode_id="14577542-s03e02-delaware-north-with-michael-carruthers"]
 */
function rm_reelcast_player_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'episode_id' => '',
        'player_size' => 'large', // 'small' or 'large'
    ), $atts );
    
    if ( empty( $atts['episode_id'] ) ) {
        return '<p>Error: episode_id is required for ReelCast player</p>';
    }
    
    $episode_id = sanitize_text_field( $atts['episode_id'] );
    $player_size = sanitize_text_field( $atts['player_size'] );
    $container_id = "buzzsprout-player-{$episode_id}";
    
    // Generate the player HTML and script
    $output = '<div id="' . esc_attr( $container_id ) . '" style="padding: 10px 0;"></div>';
    $output .= '<script type="text/javascript" charset="utf-8" src="https://www.buzzsprout.com/2057836/' . esc_attr( $episode_id ) . '.js?container_id=' . esc_attr( $container_id ) . '&player=' . esc_attr( $player_size ) . '"></script>';
    
    return $output;
}
add_shortcode( 'reelcast_player', 'rm_reelcast_player_shortcode' );

/**
 * Auto-generate reelcast player shortcode for single episode pages
 */
function rm_reelcast_auto_player() {
    if ( is_singular( 'reelcast' ) ) {
        $buzzsprout_id = get_field( 'buzzsprout_id' );
        if ( $buzzsprout_id ) {
            echo do_shortcode( '[reelcast_player episode_id="' . esc_attr( $buzzsprout_id ) . '"]' );
        }
    }
}
// Uncomment to auto-insert player (or use shortcode in Elementor templates)
// add_action( 'elementor/theme/after_do_header', 'rm_reelcast_auto_player' );

/**
 * Helper function to get episode data in React component format
 * Useful for REST API or Elementor dynamic content
 */
function rm_get_reelcast_episode_data( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    if ( get_post_type( $post_id ) !== 'reelcast' ) {
        return false;
    }
    
    $post = get_post( $post_id );
    
    // Get topics array
    $topics_raw = get_field( 'topics', $post_id );
    $topics = array();
    if ( $topics_raw ) {
        foreach ( $topics_raw as $topic ) {
            $topics[] = $topic['topic'];
        }
    }
    
    // Get keywords array
    $keywords_raw = get_field( 'keywords', $post_id );
    $keywords = array();
    if ( $keywords_raw ) {
        foreach ( $keywords_raw as $keyword ) {
            $keywords[] = $keyword['keyword'];
        }
    }
    
    return array(
        'id' => get_field( 'buzzsprout_id', $post_id ),
        'link' => $post->post_name,
        'title' => $post->post_title,
        'subTitle' => get_field( 'subtitle', $post_id ),
        'description' => get_field( 'description', $post_id ),
        'episodeNumber' => (int) get_field( 'episode_number', $post_id ),
        'seasonNumber' => (int) get_field( 'season_number', $post_id ),
        'datePublished' => get_field( 'date_published', $post_id ),
        'topics' => $topics,
        'keywords' => $keywords,
        'buzzSproutLink' => get_field( 'buzzsprout_url', $post_id ),
        'spotifyLink' => get_field( 'spotify_url', $post_id ),
        'applePodcastsLink' => get_field( 'apple_podcasts_url', $post_id ),
        'iHeartRadioLink' => get_field( 'iheart_url', $post_id ),
        'cdcLink' => get_field( 'cdc_url', $post_id ),
        'transcript' => get_field( 'transcript', $post_id ),
        'socialImage' => get_field( 'social_image', $post_id ),
    );
}

/**
 * REST API endpoint for ReelCast episodes (optional)
 * Provides data in React component format
 */
function rm_register_reelcast_rest_fields() {
    register_rest_field( 'reelcast', 'episode_data', array(
        'get_callback' => function( $post_array ) {
            return rm_get_reelcast_episode_data( $post_array['id'] );
        },
        'schema' => array(
            'description' => 'Complete episode data in React component format',
            'type' => 'object',
        ),
    ));
}
add_action( 'rest_api_init', 'rm_register_reelcast_rest_fields' );

// Importer removed per request (CPT and ACF fields kept)

/**
 * ReelCast: Default Featured Image (fallback) via ACF Options
 * - Adds a subpage under ReelCast to set a default image
 * - Provides fallback for featured images on ReelCast posts when none set
 */
function rm_reelcast_register_options_page() {
    if ( function_exists( 'acf_add_options_sub_page' ) ) {
        acf_add_options_sub_page( array(
            'page_title'  => 'ReelCast Settings',
            'menu_title'  => 'Settings',
            'parent_slug' => 'edit.php?post_type=reelcast',
            'menu_slug'   => 'reelcast-settings',
            'capability'  => 'edit_posts',
            'position'    => false,
            'autoload'    => true,
        ) );
    }
}
add_action( 'init', 'rm_reelcast_register_options_page' );

function rm_reelcast_register_options_fields() {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group( array(
            'key' => 'group_reelcast_settings',
            'title' => 'ReelCast Settings',
            'fields' => array(
                array(
                    'key' => 'field_reelcast_default_image',
                    'label' => 'Default ReelCast Image',
                    'name' => 'reelcast_default_image',
                    'type' => 'image',
                    'return_format' => 'id',
                    'preview_size' => 'medium',
                    'instructions' => 'Used as a fallback image for ReelCast posts without a featured image.',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'reelcast-settings',
                    ),
                ),
            ),
            'position' => 'normal',
            'style' => 'default',
            'active' => true,
            'show_in_rest' => 0,
        ) );
    }
}
add_action( 'acf/init', 'rm_reelcast_register_options_fields' );

/**
 * Helper: Get ReelCast image URL with fallbacks (featured → ACF social_image → options default → theme asset)
 */
function rm_get_reelcast_image_url( $post_id = null, $size = 'large' ) {
    $post_id = $post_id ? $post_id : get_the_ID();
    if ( ! $post_id ) return '';

    // 1) Featured image
    $thumb_id = get_post_thumbnail_id( $post_id );
    if ( $thumb_id ) {
        $src = wp_get_attachment_image_url( $thumb_id, $size );
        if ( $src ) return $src;
    }

    // 2) Per-post ACF social_image (URL)
    if ( function_exists( 'get_field' ) ) {
        $social = get_field( 'social_image', $post_id );
        if ( $social ) return $social;
    }

    // 3) Options default (ID)
    if ( function_exists( 'get_field' ) ) {
        $default_id = get_field( 'reelcast_default_image', 'option' );
        if ( $default_id ) {
            $src = wp_get_attachment_image_url( $default_id, $size );
            if ( $src ) return $src;
        }
    }

    // 4) Theme asset fallback (optional)
    $asset = RM_THEME_URI . '/assets/images/reelcast-default.jpg';
    return $asset;
}

/**
 * Filter: provide a thumbnail ID fallback for ReelCast posts using the Options default image
 * This helps Elementor and core functions that rely on _thumbnail_id.
 */
function rm_reelcast_thumbnail_fallback( $value, $object_id, $meta_key, $single ) {
    if ( $meta_key !== '_thumbnail_id' || ! $single ) {
        return $value;
    }
    if ( get_post_type( $object_id ) !== 'reelcast' ) {
        return $value;
    }
    // If a thumbnail is already set, respect it
    if ( ! empty( $value ) ) {
        return $value;
    }
    if ( function_exists( 'get_field' ) ) {
        $default_id = get_field( 'reelcast_default_image', 'option' );
        if ( $default_id ) {
            return (int) $default_id;
        }
    }
    return $value;
}
add_filter( 'get_post_metadata', 'rm_reelcast_thumbnail_fallback', 10, 4 );

/**
 * Filter: when HTML is requested directly for the thumbnail and none exists, render fallback IMG
 */
function rm_reelcast_thumbnail_html_fallback( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
    if ( get_post_type( $post_id ) !== 'reelcast' ) {
        return $html;
    }
    if ( $html ) {
        return $html;
    }
    $src = rm_get_reelcast_image_url( $post_id, $size );
    if ( ! $src ) return $html;
    $alt = esc_attr( get_the_title( $post_id ) );
    $class = isset( $attr['class'] ) ? esc_attr( $attr['class'] ) : 'attachment-' . esc_attr( is_string( $size ) ? $size : 'large' );
    return '<img src="' . esc_url( $src ) . '" class="' . $class . ' rm-reelcast-fallback" alt="' . $alt . '" loading="lazy" decoding="async" />';
}
add_filter( 'post_thumbnail_html', 'rm_reelcast_thumbnail_html_fallback', 10, 5 );

/**
 * Buying Guide: Register CPT and taxonomy (1 CPT + taxonomy model)
 */
function rm_register_buying_guide_types() {
    // CPT: buying_guide_item
    register_post_type( 'buying_guide_item', array(
        'label' => 'Buying Guide Items',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'supports' => array( 'title', 'revisions' ),
        'menu_icon' => 'dashicons-list-view',
        'has_archive' => false,
        'rewrite' => array( 'slug' => 'buying-guide-item' ),
    ) );

    // Taxonomy: buying_guide_category (attached to items)
    register_taxonomy( 'buying_guide_category', array( 'buying_guide_item' ), array(
        'hierarchical' => false,
        'labels' => array(
            'name' => 'Buying Guide Categories',
            'singular_name' => 'Buying Guide Category',
        ),
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'buying-guide-category' ),
        'show_in_rest' => true,
    ) );

    // Ensure taxonomy is attached to CPT even if registration order changes
    register_taxonomy_for_object_type( 'buying_guide_category', 'buying_guide_item' );
}
add_action( 'init', 'rm_register_buying_guide_types' );

/**
 * Buying Guide: ACF groups (items + taxonomy term fields)
 */
function rm_register_buying_guide_acf() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    // Item fields
    acf_add_local_field_group( array(
        'key' => 'group_rm_buying_guide_item',
        'title' => 'Buying Guide Item',
        'fields' => array(
            array(
                'key' => 'field_rm_bg_parameters',
                'label' => 'Parameters (JSON)',
                'name' => 'parameters',
                'type' => 'textarea',
                'new_lines' => 'br',
                'instructions' => 'Paste the JSON string used by the frontend to build report queries.',
            ),
            array(
                'key' => 'field_rm_bg_order',
                'label' => 'Order',
                'name' => 'order',
                'type' => 'number',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_rm_bg_butter_id',
                'label' => 'ButterCMS ID',
                'name' => 'butter_id',
                'type' => 'number',
                'instructions' => 'Optional: original ButterCMS numeric ID for idempotent migrations.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'buying_guide_item',
                ),
            ),
        ),
        'show_in_rest' => 1,
        'position' => 'acf_after_title',
        'style' => 'seamless',
    ) );

    // Taxonomy term fields (category meta)
    acf_add_local_field_group( array(
        'key' => 'group_rm_buying_guide_category',
        'title' => 'Buying Guide Category Fields',
        'fields' => array(
            array(
                'key' => 'field_rm_bg_cat_description',
                'label' => 'Description',
                'name' => 'description',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'media_upload' => 0,
            ),
            array(
                'key' => 'field_rm_bg_cat_order',
                'label' => 'Order',
                'name' => 'order',
                'type' => 'number',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_rm_bg_cat_active',
                'label' => 'Active',
                'name' => 'active',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
                'instructions' => 'Mirrors ButterCMS "published" flag for categories.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'buying_guide_category',
                ),
            ),
        ),
        'show_in_rest' => 1,
        'position' => 'normal',
        'style' => 'default',
    ) );
}
add_action( 'acf/init', 'rm_register_buying_guide_acf' );

/**
 * Buying Guide: Expose taxonomy term ACF on REST responses
 */
function rm_buying_guide_register_rest_fields() {
    register_rest_field( 'buying_guide_category', 'acf', array(
        'get_callback' => function( $term_array ) {
            if ( ! function_exists( 'get_fields' ) ) return null;
            $term_id = isset( $term_array['id'] ) ? (int) $term_array['id'] : 0;
            if ( ! $term_id ) return null;
            // ACF stores term meta under term_{id}
            return get_fields( 'term_' . $term_id ) ?: new stdClass();
        },
        'schema' => array(
            'description' => 'ACF fields for buying_guide_category term',
            'type' => 'object',
        ),
    ) );
}
add_action( 'rest_api_init', 'rm_buying_guide_register_rest_fields' );

/**
 * Texts: Register CPT for site text snippets and ACF fields
 * Mirrors ButterCMS texts: content_key, title, text
 */
function rm_register_texts_cpt() {
    register_post_type( 'rm_text', array(
        'label' => 'Texts',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'supports' => array( 'title', 'revisions' ),
        'menu_icon' => 'dashicons-editor-quote',
        'has_archive' => false,
        'rewrite' => array( 'slug' => 'text' ),
    ) );
}
add_action( 'init', 'rm_register_texts_cpt' );

function rm_register_texts_acf() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    acf_add_local_field_group( array(
        'key' => 'group_rm_text',
        'title' => 'Text Fields',
        'fields' => array(
            array(
                'key' => 'field_rm_text_content_key',
                'label' => 'Content Key',
                'name' => 'content_key',
                'type' => 'text',
                'instructions' => 'Unique key used by frontend to locate the text',
            ),
            array(
                'key' => 'field_rm_text_title',
                'label' => 'Internal Title',
                'name' => 'butter_title',
                'type' => 'text',
                'instructions' => 'Optional: original ButterCMS title',
            ),
            array(
                'key' => 'field_rm_text_text',
                'label' => 'Text',
                'name' => 'text',
                'type' => 'textarea',
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_rm_text_butter_id',
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
                    'value' => 'rm_text',
                ),
            ),
        ),
        'show_in_rest' => 1,
        'position' => 'acf_after_title',
        'style' => 'seamless',
    ) );
}
add_action( 'acf/init', 'rm_register_texts_acf' );
