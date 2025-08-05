<?php
/**
 * Custom Elementor Widgets for ReelMetrics Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ReelMetrics Typography Widget
 */
class RM_Typography_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'rm-typography';
    }

    public function get_title() {
        return __( 'RM Typography', 'rm-elementor-theme' );
    }

    public function get_icon() {
        return 'eicon-text';
    }

    public function get_categories() {
        return [ 'reelmetrics' ];
    }

    protected function _register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'rm-elementor-theme' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'text',
            [
                'label' => __( 'Text', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __( 'Your text here', 'rm-elementor-theme' ),
                'placeholder' => __( 'Type your text here', 'rm-elementor-theme' ),
            ]
        );

        $this->add_control(
            'typography_style',
            [
                'label' => __( 'Typography Style', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'rm-body-text',
                'options' => [
                    'rm-hero-large' => __( 'Hero Large (95px)', 'rm-elementor-theme' ),
                    'rm-hero-medium' => __( 'Hero Medium (88px)', 'rm-elementor-theme' ),
                    'rm-module-title' => __( 'Module Title (60px)', 'rm-elementor-theme' ),
                    'rm-intro-large' => __( 'Intro Large (56px)', 'rm-elementor-theme' ),
                    'rm-header-cta' => __( 'Header CTA (32px)', 'rm-elementor-theme' ),
                    'rm-subheading' => __( 'Subheading (28px)', 'rm-elementor-theme' ),
                    'rm-list-item' => __( 'List Item (24px)', 'rm-elementor-theme' ),
                    'rm-body-large' => __( 'Body Large (20px)', 'rm-elementor-theme' ),
                    'rm-body-text' => __( 'Body Text (18px)', 'rm-elementor-theme' ),
                ],
            ]
        );

        $this->add_control(
            'html_tag',
            [
                'label' => __( 'HTML Tag', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'div',
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __( 'Style', 'rm-elementor-theme' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __( 'Text Color', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rm-typography-widget' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'custom_typography',
                'label' => __( 'Custom Typography', 'rm-elementor-theme' ),
                'selector' => '{{WRAPPER}} .rm-typography-widget',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $html_tag = $settings['html_tag'];
        $typography_class = $settings['typography_style'];
        $text = $settings['text'];

        echo "<{$html_tag} class='rm-typography-widget {$typography_class}'>";
        echo wp_kses_post( $text );
        echo "</{$html_tag}>";
    }
}

/**
 * Register ReelMetrics Widget Category
 */
function rm_add_elementor_widget_categories( $elements_manager ) {
    $elements_manager->add_category(
        'reelmetrics',
        [
            'title' => __( 'ReelMetrics', 'rm-elementor-theme' ),
            'icon' => 'fa fa-plug',
        ]
    );
}
add_action( 'elementor/elements/categories_registered', 'rm_add_elementor_widget_categories' );

/**
 * Register Custom Widgets
 */
function rm_register_elementor_widgets() {
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new RM_Typography_Widget() );
}
add_action( 'elementor/widgets/widgets_registered', 'rm_register_elementor_widgets' );