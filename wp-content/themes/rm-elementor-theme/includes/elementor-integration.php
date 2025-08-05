<?php
/**
 * Elementor Integration for RM Theme
 * Registers ReelMetrics colors with Elementor
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ReelMetrics colors with Elementor
 */
function rm_elementor_kit_settings( $config ) {
    // Add ReelMetrics color palette
    $rm_colors = [
        // Blurple variations
        [
            '_id' => 'rm_blurple',
            'title' => 'Blurple',
            'color' => '#5E55FC',
        ],
        [
            '_id' => 'rm_blurple_medium',
            'title' => 'Medium Blurple',
            'color' => '#CFCDF2',
        ],
        [
            '_id' => 'rm_blurple_light',
            'title' => 'Light Blurple',
            'color' => '#F1F0F9',
        ],
        // Core colors
        [
            '_id' => 'rm_black',
            'title' => 'Black',
            'color' => '#292929',
        ],
        [
            '_id' => 'rm_white',
            'title' => 'White',
            'color' => '#FFFFFF',
        ],
        [
            '_id' => 'rm_red',
            'title' => 'Red',
            'color' => '#FF3E3E',
        ],
        // Grey scale
        [
            '_id' => 'rm_grey',
            'title' => 'Grey',
            'color' => '#6A6A6A',
        ],
        [
            '_id' => 'rm_grey_medium',
            'title' => 'Medium Grey',
            'color' => '#E9E9E9',
        ],
        [
            '_id' => 'rm_grey_light',
            'title' => 'Light Grey',
            'color' => '#F8F8F8',
        ],
    ];

    // Add colors to Elementor's system colors
    if ( isset( $config['settings']['system_colors'] ) ) {
        $config['settings']['system_colors'] = array_merge( 
            $config['settings']['system_colors'], 
            $rm_colors 
        );
    } else {
        $config['settings']['system_colors'] = $rm_colors;
    }

    return $config;
}
add_filter( 'elementor/kit/export/config', 'rm_elementor_kit_settings' );

/**
 * Add custom color palette to Elementor color picker
 */
function rm_elementor_editor_colors() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        if (typeof elementor !== 'undefined') {
            elementor.on('preview:loaded', function() {
                // Add ReelMetrics colors to color picker
                var rmColors = [
                    '#5E55FC', // Blurple
                    '#CFCDF2', // Medium Blurple
                    '#F1F0F9', // Light Blurple
                    '#292929', // Black
                    '#FFFFFF', // White
                    '#FF3E3E', // Red
                    '#6A6A6A', // Grey
                    '#E9E9E9', // Medium Grey
                    '#F8F8F8'  // Light Grey
                ];
                
                // Store colors for Elementor use
                elementor.config.rm_colors = rmColors;
            });
        }
    });
    </script>
    <?php
}
add_action( 'elementor/editor/after_enqueue_scripts', 'rm_elementor_editor_colors' );

/**
 * Register custom Elementor color controls
 */
function rm_register_elementor_color_schemes() {
    // Register color schemes
    $color_schemes = [
        '1' => [
            'title' => 'ReelMetrics Default',
            'items' => [
                '1' => '#5E55FC', // Primary
                '2' => '#292929', // Secondary  
                '3' => '#6A6A6A', // Text
                '4' => '#FF3E3E', // Accent
            ],
        ],
        '2' => [
            'title' => 'ReelMetrics Light',
            'items' => [
                '1' => '#CFCDF2', // Primary Light
                '2' => '#F8F8F8', // Light Grey
                '3' => '#292929', // Dark Text
                '4' => '#5E55FC', // Accent
            ],
        ],
    ];

    // Store for later use
    update_option( 'rm_elementor_color_schemes', $color_schemes );
}
add_action( 'init', 'rm_register_elementor_color_schemes' );