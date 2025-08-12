<?php
/**
 * Elementor Global Settings Integration
 * Properly injects ReelMetrics colors and typography into Elementor's global system
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Exit if Elementor is not active
if ( ! did_action( 'elementor/loaded' ) ) {
    return;
}

/**
 * Override Elementor's default kit settings
 */
add_filter( 'elementor/kit/export/manifest-data', function( $manifest_data ) {
    // Add ReelMetrics colors
    $manifest_data['site-settings']['system_colors'] = [
        [
            '_id' => 'primary',
            'title' => 'Primary - Blurple',
            'color' => '#5E55FC',
        ],
        [
            '_id' => 'secondary', 
            'title' => 'Secondary - Black',
            'color' => '#292929',
        ],
        [
            '_id' => 'text',
            'title' => 'Text - Black',
            'color' => '#292929',
        ],
        [
            '_id' => 'accent',
            'title' => 'Accent - Red',
            'color' => '#FF3E3E',
        ],
    ];
    
    // Add custom colors
    $manifest_data['site-settings']['custom_colors'] = [
        [
            '_id' => 'rm_blurple_medium',
            'title' => 'Blurple Medium',
            'color' => '#CFCDF2',
        ],
        [
            '_id' => 'rm_blurple_light',
            'title' => 'Blurple Light', 
            'color' => '#F1F0F9',
        ],
        [
            '_id' => 'rm_grey_light',
            'title' => 'Grey Light',
            'color' => '#D0D0D0',
        ],
        [
            '_id' => 'rm_grey_dark',
            'title' => 'Grey Dark',
            'color' => '#525252',
        ],
    ];
    
    return $manifest_data;
});

/**
 * Update active kit with ReelMetrics settings
 */
add_action( 'elementor/kit/after_save', function( $kit, $data ) {
    $kit_id = $kit->get_id();
    
    // Get current settings
    $meta_key = '_elementor_page_settings';
    $settings = get_post_meta( $kit_id, $meta_key, true );
    
    if ( ! is_array( $settings ) ) {
        $settings = [];
    }
    
    // Update System Colors
    $settings['system_colors'] = [
        [
            '_id' => 'primary',
            'title' => 'Primary - Blurple',
            'color' => '#5E55FC',
        ],
        [
            '_id' => 'secondary',
            'title' => 'Secondary - Black', 
            'color' => '#292929',
        ],
        [
            '_id' => 'text',
            'title' => 'Text - Black',
            'color' => '#292929',
        ],
        [
            '_id' => 'accent',
            'title' => 'Accent - Red',
            'color' => '#FF3E3E',
        ],
    ];
    
    // Update Custom Colors
    if ( ! isset( $settings['custom_colors'] ) ) {
        $settings['custom_colors'] = [];
    }
    
    // Add ReelMetrics custom colors
    $rm_custom_colors = [
        [
            '_id' => uniqid(),
            'title' => 'RM Blurple Medium',
            'color' => '#CFCDF2',
        ],
        [
            '_id' => uniqid(),
            'title' => 'RM Blurple Light',
            'color' => '#F1F0F9',
        ],
        [
            '_id' => uniqid(),
            'title' => 'RM Grey Light',
            'color' => '#D0D0D0',
        ],
        [
            '_id' => uniqid(),
            'title' => 'RM Grey Dark',
            'color' => '#525252',
        ],
        [
            '_id' => uniqid(),
            'title' => 'RM White',
            'color' => '#FFFFFF',
        ],
    ];
    
    foreach ( $rm_custom_colors as $color ) {
        $color_exists = false;
        foreach ( $settings['custom_colors'] as $existing_color ) {
            if ( $existing_color['title'] === $color['title'] ) {
                $color_exists = true;
                break;
            }
        }
        if ( ! $color_exists ) {
            $settings['custom_colors'][] = $color;
        }
    }
    
    // Update Typography Settings
    $settings['system_typography'] = [
        [
            '_id' => 'primary',
            'title' => 'Primary',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Helvetica Neue',
            'typography_font_weight' => '400',
        ],
        [
            '_id' => 'secondary',
            'title' => 'Secondary', 
            'typography_typography' => 'custom',
            'typography_font_family' => 'Helvetica Neue',
            'typography_font_weight' => '700',
        ],
        [
            '_id' => 'text',
            'title' => 'Text',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Helvetica Neue',
            'typography_font_weight' => '400',
        ],
        [
            '_id' => 'accent',
            'title' => 'Accent',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Helvetica Neue',
            'typography_font_weight' => '700',
        ],
    ];
    
    // Save updated settings
    update_post_meta( $kit_id, $meta_key, $settings );
    
}, 10, 2 );

/**
 * Force update kit on theme activation
 */
add_action( 'after_switch_theme', function() {
    // Get the active kit
    $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();
    
    if ( ! $kit ) {
        return;
    }
    
    $kit_id = $kit->get_id();
    $meta_key = '_elementor_page_settings';
    $settings = get_post_meta( $kit_id, $meta_key, true );
    
    if ( ! is_array( $settings ) ) {
        $settings = [];
    }
    
    // Force ReelMetrics colors
    $settings['system_colors'] = [
        [
            '_id' => 'primary',
            'title' => 'Primary - Blurple',
            'color' => '#5E55FC',
        ],
        [
            '_id' => 'secondary',
            'title' => 'Secondary - Black',
            'color' => '#292929',
        ],
        [
            '_id' => 'text',
            'title' => 'Text - Black',
            'color' => '#292929',
        ],
        [
            '_id' => 'accent',
            'title' => 'Accent - Red',
            'color' => '#FF3E3E',
        ],
    ];
    
    update_post_meta( $kit_id, $meta_key, $settings );
    
    // Clear Elementor cache
    \Elementor\Plugin::$instance->files_manager->clear_cache();
});