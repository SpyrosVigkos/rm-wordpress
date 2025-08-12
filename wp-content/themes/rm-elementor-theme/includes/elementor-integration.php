<?php
/**
 * Elementor Integration for RM Theme
 * Registers ReelMetrics colors and typography with Elementor Global Settings
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
 * Register ReelMetrics Global Colors
 */
add_action( 'elementor/kits/register_tabs', function( $kits_manager ) {
    // Register colors in Site Settings
    $kits_manager->register_tab( 'colors-picker', \Elementor\Core\Kits\Documents\Tabs\Colors_And_Typography::class );
}, 1, 40 );

/**
 * Add ReelMetrics colors to Elementor's color system
 */
add_action( 'elementor/editor/after_enqueue_scripts', function() {
    wp_add_inline_script( 
        'elementor-editor',
        "
        jQuery( window ).on( 'elementor:init', function() {
            // Add ReelMetrics colors to Elementor's color picker
            elementor.modules.controls.Color.addControlView('ReelMetricsColors', {
                onReady: function() {
                    this.ui.picker.wpColorPicker('option', 'palettes', [
                        '#5E55FC', // Blurple
                        '#CFCDF2', // Medium Blurple
                        '#F1F0F9', // Light Blurple
                        '#292929', // Black
                        '#FFFFFF', // White
                        '#FF3E3E', // Red
                        '#D0D0D0', // Light Grey
                        '#525252'  // Dark Grey
                    ]);
                }
            });
        });
        "
    );
});

/**
 * Register Global Colors via Kit
 */
add_filter( 'elementor/kits/raw_kit_settings', function( $kit_settings, $kit_id ) {
    // Add ReelMetrics colors to the kit
    if ( ! isset( $kit_settings['system_colors'] ) ) {
        $kit_settings['system_colors'] = [];
    }
    
    // ReelMetrics Global Colors
    $rm_colors = [
        [
            '_id' => 'rm_primary',
            'title' => 'RM Blurple',
            'color' => '#5E55FC',
        ],
        [
            '_id' => 'rm_primary_medium',
            'title' => 'RM Blurple Medium',
            'color' => '#CFCDF2',
        ],
        [
            '_id' => 'rm_primary_light',
            'title' => 'RM Blurple Light',
            'color' => '#F1F0F9',
        ],
        [
            '_id' => 'rm_text',
            'title' => 'RM Black',
            'color' => '#292929',
        ],
        [
            '_id' => 'rm_accent',
            'title' => 'RM Red',
            'color' => '#FF3E3E',
        ],
        [
            '_id' => 'rm_grey_light',
            'title' => 'RM Light Grey',
            'color' => '#D0D0D0',
        ],
        [
            '_id' => 'rm_grey_dark',
            'title' => 'RM Dark Grey',
            'color' => '#525252',
        ],
    ];
    
    // Merge with existing colors
    $kit_settings['system_colors'] = array_merge( $kit_settings['system_colors'], $rm_colors );
    
    return $kit_settings;
}, 10, 2 );

/**
 * Register Global Typography
 */
add_filter( 'elementor/kits/raw_kit_settings', function( $kit_settings, $kit_id ) {
    // Add ReelMetrics typography to the kit
    if ( ! isset( $kit_settings['system_typography'] ) ) {
        $kit_settings['system_typography'] = [];
    }
    
    // ReelMetrics Global Typography
    $rm_typography = [
        [
            '_id' => 'rm_hero_large',
            'title' => 'RM Hero Large',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Helvetica Neue',
            'typography_font_size' => [
                'unit' => 'px',
                'size' => 95,
                'sizes' => []
            ],
            'typography_font_size_mobile' => [
                'unit' => 'px',
                'size' => 88,
                'sizes' => []
            ],
            'typography_font_weight' => '700',
            'typography_line_height' => [
                'unit' => '%',
                'size' => 110,
                'sizes' => []
            ],
        ],
        [
            '_id' => 'rm_intro_large',
            'title' => 'RM Intro Large',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Helvetica Neue',
            'typography_font_size' => [
                'unit' => 'px',
                'size' => 56,
                'sizes' => []
            ],
            'typography_font_size_mobile' => [
                'unit' => 'px',
                'size' => 60,
                'sizes' => []
            ],
            'typography_font_weight' => '700',
            'typography_line_height' => [
                'unit' => '%',
                'size' => 110,
                'sizes' => []
            ],
        ],
        [
            '_id' => 'rm_subheading',
            'title' => 'RM Subheading',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Helvetica Neue',
            'typography_font_size' => [
                'unit' => 'px',
                'size' => 28,
                'sizes' => []
            ],
            'typography_font_size_mobile' => [
                'unit' => 'px',
                'size' => 32,
                'sizes' => []
            ],
            'typography_font_weight' => '400',
            'typography_line_height' => [
                'unit' => '%',
                'size' => 110,
                'sizes' => []
            ],
        ],
        [
            '_id' => 'rm_body_large',
            'title' => 'RM Body Large',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Helvetica Neue',
            'typography_font_size' => [
                'unit' => 'px',
                'size' => 20,
                'sizes' => []
            ],
            'typography_font_size_mobile' => [
                'unit' => 'px',
                'size' => 24,
                'sizes' => []
            ],
            'typography_font_weight' => '400',
            'typography_line_height' => [
                'unit' => '%',
                'size' => 130,
                'sizes' => []
            ],
        ],
        [
            '_id' => 'rm_body_text',
            'title' => 'RM Body Text',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Helvetica Neue',
            'typography_font_size' => [
                'unit' => 'px',
                'size' => 16,
                'sizes' => []
            ],
            'typography_font_size_mobile' => [
                'unit' => 'px',
                'size' => 18,
                'sizes' => []
            ],
            'typography_font_weight' => '400',
            'typography_line_height' => [
                'unit' => '%',
                'size' => 130,
                'sizes' => []
            ],
        ],
    ];
    
    // Merge with existing typography
    $kit_settings['system_typography'] = array_merge( $kit_settings['system_typography'], $rm_typography );
    
    // Set default font
    $kit_settings['default_generic_fonts'] = 'Sans-serif';
    $kit_settings['body_typography_typography'] = 'custom';
    $kit_settings['body_typography_font_family'] = 'Helvetica Neue';
    
    return $kit_settings;
}, 10, 2 );

/**
 * Add to Elementor's Site Settings panel
 */
add_action( 'elementor/documents/register_controls', function( $document ) {
    if ( ! $document instanceof \Elementor\Core\Kits\Documents\Kit ) {
        return;
    }

    // Add notice about ReelMetrics design system
    $document->start_controls_section(
        'rm_design_system',
        [
            'label' => __( 'ReelMetrics Design System', 'rm-elementor-theme' ),
            'tab' => 'global-colors',
        ]
    );

    $document->add_control(
        'rm_colors_notice',
        [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => '<div style="padding: 12px; background: #f1f0f9; border-left: 3px solid #5E55FC; color: #292929;">
                <strong>ReelMetrics Colors Active:</strong><br/>
                • Blurple (#5E55FC)<br/>
                • Black (#292929)<br/>
                • Red (#FF3E3E)<br/>
                • Grey variations<br/><br/>
                Use these in any color picker or select "Global Colors" to see them.
            </div>',
        ]
    );

    $document->end_controls_section();

    // Add typography notice
    $document->start_controls_section(
        'rm_typography_system',
        [
            'label' => __( 'ReelMetrics Typography', 'rm-elementor-theme' ),
            'tab' => 'global-typography',
        ]
    );

    $document->add_control(
        'rm_typography_notice',
        [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => '<div style="padding: 12px; background: #f1f0f9; border-left: 3px solid #5E55FC; color: #292929;">
                <strong>ReelMetrics Typography Active:</strong><br/>
                • Hero Large (95px / 88px mobile)<br/>
                • Intro Large (56px / 60px mobile)<br/>
                • Subheading (28px / 32px mobile)<br/>
                • Body Large (20px / 24px mobile)<br/>
                • Body Text (16px / 18px mobile)<br/><br/>
                Font: Helvetica Neue
            </div>',
        ]
    );

    $document->end_controls_section();
}, 10 );

/**
 * Add custom controls to existing Elementor widgets
 */
add_action( 'elementor/element/heading/section_title/before_section_end', function( $element, $args ) {
    $element->add_control(
        'rm_typography_preset',
        [
            'label' => __( 'ReelMetrics Typography', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '' => __( 'Default', 'rm-elementor-theme' ),
                'rm-hero-large' => __( 'Hero Large (95px, 400)', 'rm-elementor-theme' ),
                'rm-hero-subtitle' => __( 'Hero Subtitle (32px, 300)', 'rm-elementor-theme' ),
                'rm-intro-large' => __( 'Intro Large (56px)', 'rm-elementor-theme' ),
                'rm-module-title' => __( 'Module Title (32px)', 'rm-elementor-theme' ),
                'rm-subheading' => __( 'Subheading (32px)', 'rm-elementor-theme' ),
            ],
            'prefix_class' => 'elementor-heading-',
            'separator' => 'before',
        ]
    );
}, 10, 2 );

add_action( 'elementor/element/text-editor/section_editor/before_section_end', function( $element, $args ) {
    $element->add_control(
        'rm_typography_preset',
        [
            'label' => __( 'ReelMetrics Typography', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '' => __( 'Default', 'rm-elementor-theme' ),
                'rm-body-large' => __( 'Body Large', 'rm-elementor-theme' ),
                'rm-body-text' => __( 'Body Text', 'rm-elementor-theme' ),
                'rm-subheading' => __( 'Subheading', 'rm-elementor-theme' ),
            ],
            'prefix_class' => '',
            'separator' => 'before',
        ]
    );
}, 10, 2 );

add_action( 'elementor/element/button/section_button/before_section_end', function( $element, $args ) {
    $element->add_control(
        'rm_button_style',
        [
            'label' => __( 'ReelMetrics Button Style', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '' => __( 'Default', 'rm-elementor-theme' ),
                'rm-btn-primary' => __( 'Primary (Blurple)', 'rm-elementor-theme' ),
                'rm-btn-secondary' => __( 'Secondary (Black)', 'rm-elementor-theme' ),
                'rm-btn-cta-primary' => __( 'CTA Primary', 'rm-elementor-theme' ),
                'rm-btn-cta-secondary' => __( 'CTA Secondary', 'rm-elementor-theme' ),
            ],
            'prefix_class' => 'elementor-button-',
            'separator' => 'before',
            'render_type' => 'template',
        ]
    );
}, 10, 2 );

/**
 * Enqueue frontend styles for Elementor-specific classes
 */
add_action( 'elementor/frontend/after_enqueue_styles', function() {
    wp_add_inline_style( 'elementor-frontend', '
        /* ReelMetrics Typography Classes for Headings */
        .elementor-heading-rm-hero-large .elementor-heading-title { 
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            font-size: 95px !important;
            font-weight: 400 !important;
            line-height: 105% !important;
        }
        @media (max-width: 768px) {
            .elementor-heading-rm-hero-large .elementor-heading-title {
                font-size: 50px !important;
                line-height: 105% !important;
            }
        }
        
        .elementor-heading-rm-hero-subtitle .elementor-heading-title {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            font-size: 32px !important;
            font-weight: 300 !important;
            line-height: 130% !important;
        }
        @media (max-width: 768px) {
            .elementor-heading-rm-hero-subtitle .elementor-heading-title {
                font-size: 18px !important;
                line-height: 120% !important;
            }
        }
        
        .elementor-heading-rm-intro-large .elementor-heading-title { 
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            font-size: 56px !important;
            font-weight: 700 !important;
            line-height: 110% !important;
        }
        @media (max-width: 768px) {
            .elementor-heading-rm-intro-large .elementor-heading-title {
                font-size: 60px !important;
            }
        }
        
        .elementor-heading-rm-module-title .elementor-heading-title {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            font-size: 32px !important;
            font-weight: 700 !important;
            line-height: 110% !important;
        }
        
        .elementor-heading-rm-subheading .elementor-heading-title { 
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            font-size: 32px !important;
            font-weight: 300 !important;
            line-height: 130% !important;
        }
        @media (max-width: 768px) {
            .elementor-heading-rm-subheading .elementor-heading-title {
                font-size: 32px !important;
            }
        }
        
        /* ReelMetrics Typography Classes for Text Editor */
        .rm-body-large .elementor-text-editor { 
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            font-size: 20px !important;
            font-weight: 400 !important;
            line-height: 130% !important;
        }
        @media (max-width: 768px) {
            .rm-body-large .elementor-text-editor {
                font-size: 24px !important;
            }
        }
        
        .rm-body-text .elementor-text-editor { 
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            font-size: 16px !important;
            font-weight: 400 !important;
            line-height: 130% !important;
        }
        @media (max-width: 768px) {
            .rm-body-text .elementor-text-editor {
                font-size: 18px !important;
            }
        }
        
        /* ReelMetrics Button Classes */
        .elementor-button-rm-btn-primary .elementor-button,
        .rm-btn-primary {
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 8px !important;
            background-color: var(--rm-blurple, #5E55FC) !important;
            color: white !important;
            border-radius: 80px !important;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            font-size: 20px !important;
            font-weight: 400 !important;
            line-height: 130% !important;
            padding: 25px 30px !important;
            min-height: 60px !important;
            border: none !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
        }
        .elementor-button-rm-btn-primary .elementor-button:hover,
        .rm-btn-primary:hover {
            background: linear-gradient(0deg, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), #5E55FC !important;
        }
        @media (max-width: 768px) {
            .elementor-button-rm-btn-primary .elementor-button,
            .rm-btn-primary {
                font-size: 16px !important;
                padding: 25px 24px !important;
                min-height: 52px !important;
            }
        }
        
        .elementor-button-rm-btn-secondary .elementor-button,
        .rm-btn-secondary {
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 8px !important;
            background-color: var(--rm-black, #292929) !important;
            color: white !important;
            border-radius: 80px !important;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            font-size: 20px !important;
            font-weight: 400 !important;
            line-height: 130% !important;
            padding: 25px 30px !important;
            min-height: 60px !important;
            border: none !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
        }
        .elementor-button-rm-btn-secondary .elementor-button:hover,
        .rm-btn-secondary:hover {
            background: linear-gradient(0deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.2)), #292929 !important;
        }
        @media (max-width: 768px) {
            .elementor-button-rm-btn-secondary .elementor-button,
            .rm-btn-secondary {
                font-size: 16px !important;
                padding: 25px 24px !important;
                min-height: 52px !important;
            }
        }
        
        .elementor-button-rm-btn-cta-primary .elementor-button,
        .rm-btn-cta-primary {
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 8px !important;
            background-color: var(--rm-blurple, #5E55FC) !important;
            color: white !important;
            border-radius: 80px !important;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            font-size: 20px !important;
            font-weight: 400 !important;
            line-height: 130% !important;
            padding: 25px 30px !important;
            min-height: 60px !important;
            border: none !important;
            text-decoration: none !important;
        }
        @media (max-width: 768px) {
            .elementor-button-rm-btn-cta-primary .elementor-button,
            .rm-btn-cta-primary {
                font-size: 16px !important;
                padding: 25px 24px !important;
                min-height: 52px !important;
            }
        }
        
        .elementor-button-rm-btn-cta-secondary .elementor-button,
        .rm-btn-cta-secondary {
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 8px !important;
            background-color: var(--rm-white, #FFFFFF) !important;
            color: var(--rm-black, #292929) !important;
            border-radius: 80px !important;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            font-size: 20px !important;
            font-weight: 400 !important;
            line-height: 130% !important;
            padding: 25px 30px !important;
            min-height: 60px !important;
            border: none !important;
            text-decoration: none !important;
        }
        .elementor-button-rm-btn-cta-secondary .elementor-button:hover,
        .rm-btn-cta-secondary:hover {
            background: linear-gradient(0deg, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), #FFFFFF !important;
        }
        @media (max-width: 768px) {
            .elementor-button-rm-btn-cta-secondary .elementor-button,
            .rm-btn-cta-secondary {
                font-size: 16px !important;
                padding: 25px 24px !important;
                min-height: 52px !important;
            }
        }
    ' );
} );

/**
 * Register CDN-served Helvetica Neue in Elementor's font dropdown
 * Maps likely family names from the CDN to a custom "ReelMetrics" group
 */
add_filter( 'elementor/fonts/groups', function( $groups ) {
    $groups['rm'] = 'ReelMetrics';
    return $groups;
}, 10, 1 );

add_filter( 'elementor/fonts/additional_fonts', function( $fonts ) {
    // Register the main generic family name only
    $fonts['Helvetica Neue'] = 'rm';
    return $fonts;
}, 10, 1 );