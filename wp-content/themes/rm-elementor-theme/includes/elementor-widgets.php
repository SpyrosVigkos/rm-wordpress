<?php
/**
 * Custom Elementor Widgets for ReelMetrics Theme
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

    protected function register_controls() {
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

    protected function register_controls() {
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
 * ReelMetrics Navigation Widget
 */
class RM_Navigation_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'rm-navigation';
    }

    public function get_title() {
        return __( 'RM Navigation', 'rm-elementor-theme' );
    }

    public function get_icon() {
        return 'eicon-nav-menu';
    }

    public function get_categories() {
        return [ 'reelmetrics' ];
    }

    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Navigation', 'rm-elementor-theme' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'logo_image',
            [
                'label' => __( 'Logo', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'menu_location',
            [
                'label' => __( 'Menu Location', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'primary',
                'options' => [
                    'primary' => __( 'Primary Menu', 'rm-elementor-theme' ),
                    'footer' => __( 'Footer Menu', 'rm-elementor-theme' ),
                ],
            ]
        );

        $this->add_control(
            'show_login',
            [
                'label' => __( 'Show Login Button', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'rm-elementor-theme' ),
                'label_off' => __( 'Hide', 'rm-elementor-theme' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'login_text',
            [
                'label' => __( 'Login Button Text', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Login', 'rm-elementor-theme' ),
                'condition' => [
                    'show_login' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'login_link',
            [
                'label' => __( 'Login Link', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-login-url.com', 'rm-elementor-theme' ),
                'default' => [
                    'url' => '#login',
                ],
                'condition' => [
                    'show_login' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'mobile_cta_primary',
            [
                'label' => __( 'Mobile CTA Primary', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Get Started for Free', 'rm-elementor-theme' ),
            ]
        );

        $this->add_control(
            'mobile_cta_primary_link',
            [
                'label' => __( 'Mobile CTA Primary Link', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'rm-elementor-theme' ),
                'default' => [
                    'url' => '#signup',
                ],
            ]
        );

        $this->add_control(
            'mobile_cta_secondary',
            [
                'label' => __( 'Mobile CTA Secondary', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Book a Demo', 'rm-elementor-theme' ),
            ]
        );

        $this->add_control(
            'mobile_cta_secondary_link',
            [
                'label' => __( 'Mobile CTA Secondary Link', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'rm-elementor-theme' ),
                'default' => [
                    'url' => '#demo',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <nav class="rm-navigation">
            <div class="rm-nav-bar">
                <!-- Logo -->
                <div class="rm-nav-logo">
                    <?php if ( ! empty( $settings['logo_image']['url'] ) ) : ?>
                        <img src="<?php echo esc_url( $settings['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                    <?php else : ?>
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                    <?php endif; ?>
                </div>

                <!-- Desktop Navigation Menu -->
                <div class="rm-nav-menu">
                    <div class="rm-nav-links">
                        <?php
                        // Check if menu exists
                        if ( has_nav_menu( $settings['menu_location'] ) ) {
                            wp_nav_menu( array(
                                'theme_location' => $settings['menu_location'],
                                'menu_class'     => 'rm-nav-wp-menu',
                                'container'      => false,
                                'fallback_cb'    => false,
                                'walker'         => new RM_Nav_Walker(),
                            ) );
                        } else {
                            // Display default menu items
                            ?>
                            <ul class="rm-nav-wp-menu rm-nav-default">
                                <li><a href="#products" class="rm-nav-link">Products</a></li>
                                <li><a href="#pricing" class="rm-nav-link">Pricing</a></li>
                                <li><a href="#resources" class="rm-nav-link">Resources</a></li>
                                <li><a href="#about" class="rm-nav-link">About</a></li>
                                <li><a href="#contact" class="rm-nav-link">Contact</a></li>
                            </ul>
                            <?php
                        }
                        ?>
                    </div>

                    <?php if ( $settings['show_login'] === 'yes' ) : ?>
                        <a href="<?php echo esc_url( $settings['login_link']['url'] ); ?>" 
                           class="rm-nav-login"
                           <?php if ( $settings['login_link']['is_external'] ) echo 'target="_blank"'; ?>
                           <?php if ( $settings['login_link']['nofollow'] ) echo 'rel="nofollow"'; ?>>
                            <?php echo esc_html( $settings['login_text'] ); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Toggle -->
                <div class="rm-nav-mobile-toggle" onclick="rmToggleMobileMenu()">
                    <span class="rm-nav-mobile-text">Menu</span>
                    <div class="rm-nav-mobile-icon"></div>
                </div>
            </div>

            <!-- Mobile Menu Overlay -->
            <div class="rm-nav-mobile-overlay" id="rm-mobile-overlay">
                <div class="rm-nav-mobile-close" onclick="rmToggleMobileMenu()">
                    <span class="rm-nav-mobile-close-text">Close</span>
                    <div class="rm-nav-mobile-close-icon"></div>
                </div>

                <div class="rm-nav-mobile-content">
                    <!-- Mobile Navigation Links -->
                    <div class="rm-nav-mobile-links">
                        <?php
                        // Check if menu exists
                        if ( has_nav_menu( $settings['menu_location'] ) ) {
                            wp_nav_menu( array(
                                'theme_location' => $settings['menu_location'],
                                'menu_class'     => 'rm-nav-mobile-menu',
                                'container'      => false,
                                'fallback_cb'    => false,
                                'walker'         => new RM_Mobile_Nav_Walker(),
                            ) );
                        } else {
                            // Display default mobile menu items
                            ?>
                            <div class="rm-nav-mobile-menu rm-nav-mobile-default">
                                <a href="#products" class="rm-nav-mobile-link" onclick="rmToggleMobileMenu()">Products</a>
                                <a href="#pricing" class="rm-nav-mobile-link" onclick="rmToggleMobileMenu()">Pricing</a>
                                <a href="#resources" class="rm-nav-mobile-link" onclick="rmToggleMobileMenu()">Resources</a>
                                <a href="#about" class="rm-nav-mobile-link" onclick="rmToggleMobileMenu()">About</a>
                                <a href="#contact" class="rm-nav-mobile-link" onclick="rmToggleMobileMenu()">Contact</a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <?php if ( $settings['show_login'] === 'yes' ) : ?>
                        <a href="<?php echo esc_url( $settings['login_link']['url'] ); ?>" 
                           class="rm-nav-mobile-login"
                           <?php if ( $settings['login_link']['is_external'] ) echo 'target="_blank"'; ?>
                           <?php if ( $settings['login_link']['nofollow'] ) echo 'rel="nofollow"'; ?>>
                            <?php echo esc_html( $settings['login_text'] ); ?>
                        </a>
                    <?php endif; ?>

                    <!-- Mobile CTA Buttons -->
                    <div class="rm-nav-mobile-buttons">
                        <a href="<?php echo esc_url( $settings['mobile_cta_primary_link']['url'] ); ?>" 
                           class="rm-nav-mobile-cta-primary"
                           <?php if ( $settings['mobile_cta_primary_link']['is_external'] ) echo 'target="_blank"'; ?>
                           <?php if ( $settings['mobile_cta_primary_link']['nofollow'] ) echo 'rel="nofollow"'; ?>>
                            <?php echo esc_html( $settings['mobile_cta_primary'] ); ?>
                        </a>
                        
                        <a href="<?php echo esc_url( $settings['mobile_cta_secondary_link']['url'] ); ?>" 
                           class="rm-nav-mobile-cta-secondary"
                           <?php if ( $settings['mobile_cta_secondary_link']['is_external'] ) echo 'target="_blank"'; ?>
                           <?php if ( $settings['mobile_cta_secondary_link']['nofollow'] ) echo 'rel="nofollow"'; ?>>
                            <?php echo esc_html( $settings['mobile_cta_secondary'] ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        <?php
    }
}

/**
 * Custom Walker for Desktop Navigation
 */
class RM_Nav_Walker extends Walker_Nav_Menu {
    
    function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent</ul>\n";
    }

    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
        
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
        
        $attributes = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        
        $item_output = isset( $args->before ) ? $args->before : '';
        $item_output .= '<a class="rm-nav-link"' . $attributes . '>';
        $item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( isset( $args->link_after ) ? $args->link_after : '' );
        $item_output .= '</a>';
        $item_output .= isset( $args->after ) ? $args->after : '';
        
        $output .= $indent . $item_output;
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "\n";
    }
}

/**
 * Custom Walker for Mobile Navigation
 */
class RM_Mobile_Nav_Walker extends Walker_Nav_Menu {
    
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        $attributes = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        
        $item_output = '<a class="rm-nav-mobile-link"' . $attributes . ' onclick="rmToggleMobileMenu()">';
        $item_output .= apply_filters( 'the_title', $item->title, $item->ID );
        $item_output .= '</a>';
        
        $output .= $indent . $item_output . "\n";
    }
}

/**
 * Register Custom Widgets
 */
function rm_register_elementor_widgets( $widgets_manager ) {
    $widgets_manager->register( new RM_Typography_Widget() );
    $widgets_manager->register( new RM_Button_Widget() );
    $widgets_manager->register( new RM_Navigation_Widget() );
}
add_action( 'elementor/widgets/register', 'rm_register_elementor_widgets' );