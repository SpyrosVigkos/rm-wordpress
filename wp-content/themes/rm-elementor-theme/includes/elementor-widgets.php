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
 * ReelMetrics Button Widget
 */
class RM_Button_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'rm-button';
    }

    public function get_title() {
        return __( 'RM Button', 'rm-elementor-theme' );
    }

    public function get_icon() {
        return 'eicon-button';
    }

    public function get_categories() {
        return [ 'reelmetrics' ];
    }

    protected function _register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Button', 'rm-elementor-theme' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'text',
            [
                'label' => __( 'Text', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Click here', 'rm-elementor-theme' ),
                'placeholder' => __( 'Click here', 'rm-elementor-theme' ),
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __( 'Link', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'rm-elementor-theme' ),
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->add_control(
            'button_style',
            [
                'label' => __( 'Button Style', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'rm-btn-primary',
                'options' => [
                    'rm-btn-primary' => __( 'Primary', 'rm-elementor-theme' ),
                    'rm-btn-secondary' => __( 'Secondary', 'rm-elementor-theme' ),
                    'rm-btn-outline' => __( 'Outline', 'rm-elementor-theme' ),
                    'rm-cta' => __( 'CTA (Figma Spec)', 'rm-elementor-theme' ),
                ],
            ]
        );

        $this->add_control(
            'button_size',
            [
                'label' => __( 'Size', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'rm-btn-large',
                'options' => [
                    'rm-btn-large' => __( 'Large (60px)', 'rm-elementor-theme' ),
                    'rm-btn-medium' => __( 'Medium (52px)', 'rm-elementor-theme' ),
                    'rm-btn-small' => __( 'Small (44px)', 'rm-elementor-theme' ),
                ],
                'condition' => [
                    'button_style!' => 'rm-cta',
                ],
            ]
        );

        $this->add_control(
            'selected_icon',
            [
                'label' => __( 'Icon', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => '',
                    'library' => 'solid',
                ],
            ]
        );

        $this->add_control(
            'icon_position',
            [
                'label' => __( 'Icon Position', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'right',
                'options' => [
                    'left' => __( 'Before', 'rm-elementor-theme' ),
                    'right' => __( 'After', 'rm-elementor-theme' ),
                ],
                'condition' => [
                    'selected_icon[value]!' => '',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'wrapper', 'class', 'rm-button-wrapper' );

        // Build button classes
        $button_classes = ['rm-btn'];
        $button_classes[] = $settings['button_style'];
        
        if ( $settings['button_style'] !== 'rm-cta' ) {
            $button_classes[] = $settings['button_size'];
        }

        if ( ! empty( $settings['selected_icon']['value'] ) ) {
            $button_classes[] = 'rm-btn-icon';
            $button_classes[] = 'rm-btn-icon-' . $settings['icon_position'];
        }

        $this->add_render_attribute( 'button', 'class', $button_classes );

        if ( ! empty( $settings['link']['url'] ) ) {
            $this->add_link_attributes( 'button', $settings['link'] );
        }

        ?>
        <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
            <a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
                <?php if ( ! empty( $settings['selected_icon']['value'] ) && $settings['icon_position'] === 'left' ) : ?>
                    <span class="rm-btn-icon-element">
                        <?php \Elementor\Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    </span>
                <?php endif; ?>

                <span class="rm-btn-text"><?php echo esc_html( $settings['text'] ); ?></span>

                <?php if ( ! empty( $settings['selected_icon']['value'] ) && $settings['icon_position'] === 'right' ) : ?>
                    <span class="rm-btn-icon-element">
                        <?php \Elementor\Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>
        <?php
    }
}

/**
 * Register Custom Widgets
 */
function rm_register_elementor_widgets() {
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new RM_Typography_Widget() );
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new RM_Button_Widget() );
}
add_action( 'elementor/widgets/widgets_registered', 'rm_register_elementor_widgets' );