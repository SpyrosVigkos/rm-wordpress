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
 * RM Okta Login Widget
 */
class RM_Okta_Login extends \Elementor\Widget_Base {
    public function get_name() { return 'rm_okta_login'; }
    public function get_title() { return __( 'RM Okta Login', 'rm-elementor-theme' ); }
    public function get_icon() { return 'eicon-lock-user'; }
    public function get_categories() { return [ 'reelmetrics' ]; }
    public function get_style_depends() { return [ 'rm-okta-login' ]; }
    public function get_script_depends() { return [ 'okta-auth-js', 'rm-okta-login' ]; }

    protected function register_controls() {
        $this->start_controls_section('content',[ 'label'=>__('Content','rm-elementor-theme'), 'tab'=>\Elementor\Controls_Manager::TAB_CONTENT ]);
        $this->add_control('headline',[ 'label'=>__('Headline','rm-elementor-theme'), 'type'=>\Elementor\Controls_Manager::TEXT, 'default'=>'' ]);
        $this->add_control('body',[ 'label'=>__('Body','rm-elementor-theme'), 'type'=>\Elementor\Controls_Manager::TEXTAREA, 'default'=>'' ]);
        $this->add_control('form_id',[ 'label'=>__('Form ID','rm-elementor-theme'), 'type'=>\Elementor\Controls_Manager::TEXT, 'default'=>'loginFormPopup', 'description'=>__('Used with Elementor Popup "Open By Selector" (e.g. a[href="#loginFormPopup"]).','rm-elementor-theme') ]);
        $this->end_controls_section();
    }

    protected function render(){
        $s = $this->get_settings_for_display();
        echo '<div class="rm-okta-login-wrap">';
        if (!empty($s['headline'])) echo '<div class="rm-okta-headline">'.esc_html($s['headline']).'</div>';
        if (!empty($s['body'])) echo '<div class="rm-okta-body">'.esc_html($s['body']).'</div>';
        $form_id = !empty($s['form_id']) ? sanitize_html_class($s['form_id']) : '';
        $id_attr = $form_id ? ' id="'.esc_attr($form_id).'"' : '';
        echo '<form'.$id_attr.' class="rm-okta-login">';
        echo '<div class="form-group"><label>E-mail<input type="email" placeholder="E-mail" required></label></div>';
        echo '<div class="form-group"><label>Password<input type="password" placeholder="Password" required></label></div>';
        echo '<div class="rm-okta-error" style="color:#c00;min-height:20px"></div>';
        echo '<div class="rm-okta-actions">';
        echo '<a class="rm-okta-forgot" href="#" target="_blank" rel="noopener">I forgot my password</a>';
        echo '<button type="submit" class="button">LOGIN</button>';
        echo '</div>';
        echo '<div class="rm-okta-request-wrap"><strong><a class="rm-okta-request" href="https://operators.reelmetrics.com/member_request" target="_blank" rel="noopener">Request Access</a></strong> if your organization is already subscribed to ReelMetrics.</div>';
        echo '</form>';
        echo '</div>';
    }
}

/**
 * RM Comparison Table Widget
 */
class RM_Comparison_Table extends \Elementor\Widget_Base {
    public function get_name() { return 'rm_comparison_table'; }
    public function get_title() { return __( 'RM Comparison Table', 'rm-elementor-theme' ); }
    public function get_icon() { return 'eicon-table'; }
    public function get_categories() { return [ 'reelmetrics' ]; }
    public function get_style_depends() { return [ 'rm-comparison-table' ]; }

    protected function register_controls() {
        $this->start_controls_section('content',[ 'label'=>__('Content','rm-elementor-theme'), 'tab'=>\Elementor\Controls_Manager::TAB_CONTENT ]);
        $this->add_control('comparison_id',[ 'label'=>__('Comparison Post ID','rm-elementor-theme'), 'type'=>\Elementor\Controls_Manager::NUMBER ]);
        $this->end_controls_section();
    }

    protected function render() {
        $id = intval( $this->get_settings_for_display('comparison_id') );
        if ( ! $id ) { echo '<div>Comparison not selected.</div>'; return; }
        $plans = function_exists('get_field') ? ( get_field('plans',$id) ?: [] ) : [];
        if ( ! $plans ) { echo '<div>No plans configured.</div>'; return; }
        $groups = function_exists('get_field') ? ( get_field('feature_groups',$id) ?: [] ) : [];

        $plan_keys = array_map(function($p){ return !empty($p['plan_key']) ? $p['plan_key'] : sanitize_title($p['title']??''); }, $plans);
        echo '<div class="rm-cmptable" style="--rm-cmp-cols: '.count($plans).'; --rm-color-blurple: #5E55FC; --rm-gray-divider: #e9e9e9;" role="table" aria-label="Plan comparison">';

        // Header
        echo '<div class="rm-cmptable__row rm-cmptable__row--head" role="row">';
        echo '<div class="rm-cmptable__cell rm-cmptable__cell--stub" role="columnheader"></div>';
        foreach ($plans as $p){
            $hl = !empty($p['highlight']) ? ' is-highlight' : '';
            echo '<div class="rm-cmptable__cell rm-cmptable__cell--head'.$hl.'" role="columnheader">';
            if (!empty($p['badge'])) echo '<div class="rm-cmptable__badge">'.esc_html($p['badge']).'</div>';
            echo '<div class="rm-cmptable__plan-title">'.esc_html($p['title']??'').'</div>';
            if (!empty($p['subtitle'])) echo '<div class="rm-cmptable__plan-subtitle">'.esc_html($p['subtitle']).'</div>';
            echo '</div>';
        }
        echo '</div>';

        foreach ( $groups as $g ) {
            echo '<div class="rm-cmptable__group-title" role="rowgroup">'.esc_html($g['group_title']??'').'</div>';
            foreach ( ($g['features']??[]) as $f ) {
                $avail = array_filter( (array)($f['availability']??[]) );
                echo '<div class="rm-cmptable__row" role="row">';
                echo '<div class="rm-cmptable__cell rm-cmptable__cell--feature" role="rowheader">'.esc_html($f['feature_title']??'');
                if (!empty($f['note'])) echo '<span class="rm-cmptable__note">'.esc_html($f['note']).'</span>';
                echo '</div>';
                foreach ($plan_keys as $k) {
                    $yes = in_array($k,$avail,true);
                    echo '<div class="rm-cmptable__cell rm-cmptable__cell--value'.($yes?' is-yes':' is-no').'" role="cell">';
                    echo $yes ? '<span class="rm-cmptable__icon rm-cmptable__icon--yes" aria-hidden="true"></span><span class="sr-only">Yes</span>' : '<span class="rm-cmptable__icon rm-cmptable__icon--no" aria-hidden="true"></span><span class="sr-only">No</span>';
                    echo '</div>';
                }
                echo '</div>';
            }
        }
        echo '</div>';
    }
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
 * ReelMetrics Accordion Widget
 */
class RM_Accordion_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'rm-accordion';
    }

    public function get_title() {
        return __( 'RM Accordion', 'rm-elementor-theme' );
    }

    public function get_icon() {
        return 'eicon-accordion';
    }

    public function get_categories() {
        return [ 'reelmetrics' ];
    }

    public function get_script_depends() {
        return [ 'rm-accordion-script' ];
    }

    public function get_style_depends() {
        return [ 'rm-accordion-style' ];
    }

    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Accordion Items', 'rm-elementor-theme' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => __( 'Title', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Pick the best titles', 'rm-elementor-theme' ),
                'placeholder' => __( 'Type your title here', 'rm-elementor-theme' ),
            ]
        );

        $repeater->add_control(
            'content',
            [
                'label' => __( 'Content', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __( 'At $25k a pop, you want to make sure that you purchase only the most reliable cabinets with the deepest, strongest libraries. With ReelMetrics Cabinets, we keep you abreast of everything hardware, including detailed specs, performance & installed base comparisons, & comprehensive listings of all compatible titles (including performance scores) by hardware platform.', 'rm-elementor-theme' ),
                'placeholder' => __( 'Type your content here', 'rm-elementor-theme' ),
            ]
        );

        $repeater->add_control(
            'closed_width',
            [
                'label' => __( 'Closed Width', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 600,
                        'step' => 10,
                    ],
                    '%' => [
                        'min' => 20,
                        'max' => 100,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 287,
                ],
                'description' => __( 'Set custom width for this item when closed', 'rm-elementor-theme' ),
            ]
        );

        $this->add_control(
            'accordion_items',
            [
                'label' => __( 'Accordion Items', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title' => __( 'Pick the best cabinets', 'rm-elementor-theme' ),
                        'content' => __( 'At $25k a pop, you want to make sure that you purchase only the most reliable cabinets with the deepest, strongest libraries. With ReelMetrics Cabinets, we keep you abreast of everything hardware, including detailed specs, performance & installed base comparisons, & comprehensive listings of all compatible titles (including performance scores) by hardware platform.', 'rm-elementor-theme' ),
                    ],
                    [
                        'title' => __( 'Pick the best titles', 'rm-elementor-theme' ),
                        'content' => __( 'Content for the second accordion item goes here.', 'rm-elementor-theme' ),
                    ],
                    [
                        'title' => __( 'Optimize configurations', 'rm-elementor-theme' ),
                        'content' => __( 'Content for the third accordion item goes here.', 'rm-elementor-theme' ),
                    ],
                ],
                'title_field' => '{{{ title }}}',
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
            'animation_duration',
            [
                'label' => __( 'Animation Duration (ms)', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'ms' ],
                'range' => [
                    'ms' => [
                        'min' => 200,
                        'max' => 1000,
                        'step' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'ms',
                    'size' => 400,
                ],
            ]
        );

        $this->add_control(
            'gap_between_items',
            [
                'label' => __( 'Gap Between Items', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .rm-accordion-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $animation_duration = $settings['animation_duration']['size'];
        
        if ( empty( $settings['accordion_items'] ) ) {
            return;
        }
        ?>
        <div class="rm-accordion" data-animation-duration="<?php echo esc_attr( $animation_duration ); ?>">
            <?php foreach ( $settings['accordion_items'] as $index => $item ) : 
                $closed_width = $item['closed_width']['size'] . $item['closed_width']['unit'];
            ?>
                <div class="rm-accordion-item" data-index="<?php echo esc_attr( $index ); ?>" data-closed-width="<?php echo esc_attr( $closed_width ); ?>">
                    <div class="rm-accordion-header" style="width: <?php echo esc_attr( $closed_width ); ?>;">
                        <h3 class="rm-accordion-title"><?php echo esc_html( $item['title'] ); ?></h3>
                        <div class="rm-accordion-toggle">
                            <div class="rm-accordion-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="rm-accordion-content" style="width: <?php echo esc_attr( $closed_width ); ?>;">
                        <div class="rm-accordion-content-inner">
                            <!-- Header inside content for open state -->
                            <div class="rm-accordion-content-header">
                                <div class="rm-accordion-content-header-inner">
                                    <h3 class="rm-accordion-content-title"><?php echo esc_html( $item['title'] ); ?></h3>
                                    <div class="rm-accordion-content-toggle">
                                        <div class="rm-accordion-content-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="rm-accordion-separator"></div>
                            </div>
                            <div class="rm-accordion-text">
                                <?php echo wp_kses_post( $item['content'] ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}

/**
 * ReelMetrics Timeline/Stepper Widget
 */
class RM_Timeline_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'rm-timeline';
    }

    public function get_title() {
        return __( 'RM Timeline/Stepper', 'rm-elementor-theme' );
    }

    public function get_icon() {
        return 'eicon-time-line';
    }

    public function get_categories() {
        return [ 'reelmetrics' ];
    }

    public function get_script_depends() {
        return [ 'rm-timeline-script' ];
    }

    public function get_style_depends() {
        return [ 'rm-timeline-style' ];
    }

    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Timeline Steps', 'rm-elementor-theme' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'step_image',
            [
                'label' => __( 'Step Image', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'step_title',
            [
                'label' => __( 'Step Title', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Data loading and cleansing', 'rm-elementor-theme' ),
                'placeholder' => __( 'Enter step title', 'rm-elementor-theme' ),
            ]
        );

        $repeater->add_control(
            'step_description',
            [
                'label' => __( 'Step Description', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __( 'We start by getting your data squeaky clean, so you can trust every insight.', 'rm-elementor-theme' ),
                'placeholder' => __( 'Enter step description', 'rm-elementor-theme' ),
            ]
        );

        $repeater->add_control(
            'step_button_text',
            [
                'label' => __( 'Button Text', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Learn more', 'rm-elementor-theme' ),
                'placeholder' => __( 'Enter button text', 'rm-elementor-theme' ),
            ]
        );

        $repeater->add_control(
            'step_button_link',
            [
                'label' => __( 'Button Link', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'rm-elementor-theme' ),
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->add_control(
            'timeline_steps',
            [
                'label' => __( 'Timeline Steps', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'step_title' => __( 'Data loading and cleansing', 'rm-elementor-theme' ),
                        'step_description' => __( 'We start by getting your data squeaky clean, so you can trust every insight.', 'rm-elementor-theme' ),
                        'step_button_text' => __( 'Learn more', 'rm-elementor-theme' ),
                    ],
                    [
                        'step_title' => __( 'Benchmarking', 'rm-elementor-theme' ),
                        'step_description' => __( 'We review inventory and activities to set a baseline.', 'rm-elementor-theme' ),
                        'step_button_text' => __( 'Learn more', 'rm-elementor-theme' ),
                    ],
                    [
                        'step_title' => __( 'Floor and demand mapping', 'rm-elementor-theme' ),
                        'step_description' => __( 'We review inventory and activities to set a baseline.', 'rm-elementor-theme' ),
                        'step_button_text' => __( 'Learn more', 'rm-elementor-theme' ),
                    ],
                ],
                'title_field' => '{{{ step_title }}}',
            ]
        );

        $this->end_controls_section();

        // Layout Settings
        $this->start_controls_section(
            'layout_section',
            [
                'label' => __( 'Layout Settings', 'rm-elementor-theme' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'sticky_offset',
            [
                'label' => __( 'Sticky Offset (px)', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 100,
                ],
                'description' => __( 'Offset from top when image becomes sticky', 'rm-elementor-theme' ),
            ]
        );

        $this->add_control(
            'animation_speed',
            [
                'label' => __( 'Animation Speed (ms)', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'ms' ],
                'range' => [
                    'ms' => [
                        'min' => 200,
                        'max' => 1000,
                        'step' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'ms',
                    'size' => 500,
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $sticky_offset = $settings['sticky_offset']['size'];
        $animation_speed = $settings['animation_speed']['size'];
        
        if ( empty( $settings['timeline_steps'] ) ) {
            return;
        }
        ?>
        <div class="rm-timeline" data-sticky-offset="<?php echo esc_attr( $sticky_offset ); ?>" data-animation-speed="<?php echo esc_attr( $animation_speed ); ?>">
            <!-- Sticky Image Container -->
            <div class="rm-timeline-image-container" aria-hidden="true">
                <div class="rm-timeline-image-sticky" role="img" aria-label="<?php echo esc_attr( $settings['timeline_steps'][0]['step_title'] ?? 'Timeline image' ); ?>">
                    <?php foreach ( $settings['timeline_steps'] as $index => $step ) : ?>
                        <div class="rm-timeline-image <?php echo $index === 0 ? 'is-active' : ''; ?>" data-step="<?php echo esc_attr( $index ); ?>">
                            <?php if ( ! empty( $step['step_image']['url'] ) ) : ?>
                                <img 
                                    src="<?php echo esc_url( $step['step_image']['url'] ); ?>" 
                                    alt="<?php echo esc_attr( $step['step_title'] ); ?>"
                                    loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                                    decoding="async"
                                >
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Timeline Steps -->
            <ol class="rm-timeline-steps rm-timeline__list" role="list">
                <?php foreach ( $settings['timeline_steps'] as $index => $step ) : 
                    $is_active = $index === 0;
                ?>
                    <li 
                        class="rm-timeline-step <?php echo $is_active ? 'is-active step-one' : ''; ?>" 
                        data-step="<?php echo esc_attr( $index ); ?>"
                        <?php echo $is_active ? 'aria-current="step"' : ''; ?>
                        tabindex="0"
                    >
                        <div class="rm-timeline-step-number" aria-hidden="true">
                            <span class="rm-step-number"><?php echo esc_html( $index + 1 ); ?></span>
                        </div>
                        <div class="rm-timeline-step-content">
                            <h3 class="rm-timeline-step-title"><?php echo esc_html( $step['step_title'] ); ?></h3>
                            <p class="rm-timeline-step-description"><?php echo esc_html( $step['step_description'] ); ?></p>
                            <?php if ( ! empty( $step['step_button_text'] ) ) : ?>
                                <a href="<?php echo esc_url( $step['step_button_link']['url'] ); ?>" 
                                   class="rm-timeline-step-button"
                                   <?php if ( $step['step_button_link']['is_external'] ) echo 'target="_blank"'; ?>
                                   <?php if ( $step['step_button_link']['nofollow'] ) echo 'rel="nofollow"'; ?>>
                                    <?php echo esc_html( $step['step_button_text'] ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <?php
    }
}

/**
 * ReelCast Transcript Formatter Widget
 * - Accepts a raw transcript (textarea, supports Dynamic Tags e.g., ACF field)
 * - Detects speaker lines by pattern (e.g., "Nick Hogan:") and renders them bold
 * - Splits non-speaker text into paragraphs of ~N words (default 20)
 * - Wraps output with a configurable CSS class (e.g., body text small)
 */
class RM_Reelcast_Transcript_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'rm-reelcast-transcript'; }
    public function get_title() { return __( 'RM ReelCast Transcript', 'rm-elementor-theme' ); }
    public function get_icon() { return 'eicon-editor-list'; }
    public function get_categories() { return [ 'reelmetrics' ]; }

    protected function register_controls() {
        // Content
        $this->start_controls_section('content', [ 'label' => __( 'Transcript', 'rm-elementor-theme' ), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ]);

        $this->add_control('transcript_text', [
            'label' => __( 'Transcript (supports Dynamic Tags)', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'dynamic' => [ 'active' => true ],
            'rows' => 20,
            'placeholder' => __( 'Paste the episode transcript or bind an ACF field', 'rm-elementor-theme' ),
        ]);

        $this->add_control('words_per_paragraph', [
            'label' => __( 'Words per Paragraph', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 5,
            'max' => 80,
            'step' => 1,
            'default' => 20,
        ]);

        $this->add_control('speaker_regex', [
            'label' => __( 'Speaker Pattern (regex)', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '^[\\w\\s\\.\\\']+:',
            'description' => __( 'Lines matching this pattern will be treated as speaker labels and rendered bold', 'rm-elementor-theme' ),
        ]);

        $this->add_control('wrapper_class', [
            'label' => __( 'Wrapper CSS Class', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'rm-transcript rm-body-text-small',
        ]);

        $this->end_controls_section();

        // Style
        $this->start_controls_section('style', [ 'label' => __( 'Style', 'rm-elementor-theme' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]);
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name' => 'typography',
            'selector' => '{{WRAPPER}} .rm-transcript',
        ]);
        $this->add_control('text_color', [
            'label' => __( 'Text Color', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .rm-transcript' => 'color: {{VALUE}};' ],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $text = isset($s['transcript_text']) && is_string($s['transcript_text']) ? $s['transcript_text'] : '';

        // Fallback to ACF field when used on a ReelCast single page and no bound text provided
        if ( empty($text) && function_exists('get_field') && is_singular('reelcast') ) {
            $acf_text = get_field('transcript');
            if ( is_string($acf_text) ) { $text = $acf_text; }
        }

        if ( trim($text) === '' ) { return; }

        // Normalize HTML-ish transcripts coming from external systems
        $normalized = $text;
        // Convert common break tags to newlines
        $normalized = str_ireplace(["<br>", "<br/>", "<br />"], "\n", $normalized);
        // Convert common closing tags to paragraph breaks
        $normalized = str_ireplace(["</p>", "</div>", "</li>"], "\n\n", $normalized);
        // Strip opening paragraph and other tags while leaving text
        $normalized = preg_replace('/<p[^>]*>/i', '', $normalized);
        // Remove any remaining HTML tags
        $normalized = wp_strip_all_tags( $normalized );
        // Decode HTML entities to plain text
        $normalized = html_entity_decode( $normalized, ENT_QUOTES | ENT_HTML5, get_bloginfo('charset') ?: 'UTF-8' );
        // Normalize CRLF/CR to LF and collapse excessive blank lines
        $normalized = preg_replace("/\r\n?|\n/", "\n", $normalized);
        $normalized = preg_replace("/\n{3,}/", "\n\n", $normalized);

        $words_per = max(5, intval($s['words_per_paragraph']));
        $pattern_body = ( isset($s['speaker_regex']) && is_string($s['speaker_regex']) && $s['speaker_regex'] !== '' )
            ? $s['speaker_regex']
            : '^[\\w\\s\\.\\\']+:';
        $pattern = '/' . $pattern_body . '/u';
        $wrapper_class = $s['wrapper_class'] ?: 'rm-transcript';

        // Split lines
        $lines = preg_split("/(\r?\n)/", $normalized);
        if ( ! is_array($lines) ) { $lines = [$text]; }

        echo '<div class="' . esc_attr($wrapper_class) . '" role="article" aria-label="Episode transcript">';
        echo '<div class="rm-transcript__inner">';

        $buffer_words = [];

        $flush_paragraphs = function() use (&$buffer_words, $words_per) {
            if (empty($buffer_words)) return '';
            $html = '';
            while (count($buffer_words) > 0) {
                $chunk = array_splice($buffer_words, 0, $words_per);
                $html .= '<p class="rm-transcript__paragraph">' . esc_html(implode(' ', $chunk)) . '</p>';
            }
            return $html;
        };

        foreach ($lines as $raw_line) {
            $line = trim($raw_line);
            if ($line === '') { // paragraph break
                echo $flush_paragraphs();
                continue;
            }

            if ( preg_match($pattern, $line) ) {
                // Flush previous paragraph chunks before a new speaker
                echo $flush_paragraphs();
                echo '<p class="rm-transcript__speaker"><strong>' . esc_html($line) . '</strong></p>';
                continue;
            }

            // Accumulate words into buffer
            $words = preg_split('/\s+/', $line);
            if (is_array($words)) {
                foreach ($words as $w) { if ($w !== '') { $buffer_words[] = $w; } }
            }
        }

        // Flush any remaining buffered words
        echo $flush_paragraphs();

        echo '</div></div>';
    }
}

/**
 * Register Custom Widgets
 */
function rm_register_elementor_widgets( $widgets_manager ) {
    $widgets_manager->register( new RM_Typography_Widget() );
    $widgets_manager->register( new RM_Button_Widget() );
    $widgets_manager->register( new RM_Navigation_Widget() );
    $widgets_manager->register( new RM_Accordion_Widget() );
    $widgets_manager->register( new RM_Timeline_Widget() );
    $widgets_manager->register( new RM_Reelcast_Transcript_Widget() );
    $widgets_manager->register( new RM_Topics_List_Widget() );
    $widgets_manager->register( new RM_Team_Widget() );
    $widgets_manager->register( new RM_Carousel_Widget() );
    $widgets_manager->register( new RM_Buzzsprout_Widget() );
    if ( class_exists('RM_Comparison_Table') ) { $widgets_manager->register( new RM_Comparison_Table() ); }
    if ( class_exists('RM_Okta_Login') ) { $widgets_manager->register( new RM_Okta_Login() ); }
}
add_action( 'elementor/widgets/register', 'rm_register_elementor_widgets' );

/**
 * ReelMetrics Team Grid Widget
 */
class RM_Team_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'rm-team';
    }

    public function get_title() {
        return __( 'RM Team Grid', 'rm-elementor-theme' );
    }

    public function get_icon() {
        return 'eicon-person';
    }

    public function get_categories() {
        return [ 'reelmetrics' ];
    }

    public function get_style_depends() {
        return [ 'rm-team-style' ];
    }

    protected function register_controls() {
        // Query section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'rm-elementor-theme' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __( 'Number of items', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min' => -1,
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __( 'Order', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'ASC',
                'options' => [ 'ASC' => 'ASC', 'DESC' => 'DESC' ],
            ]
        );

        $this->end_controls_section();

        // Style section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __( 'Styles', 'rm-elementor-theme' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'card_radius',
            [
                'label' => __( 'Card Radius', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [ 'px' => [ 'min' => 0, 'max' => 80, 'step' => 1 ] ],
                'default' => [ 'size' => 40, 'unit' => 'px' ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'name_typo',
                'label' => __( 'Name Typography', 'rm-elementor-theme' ),
                'selector' => '{{WRAPPER}} .rm-team-card__name',
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label' => __( 'Name Color', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [ '{{WRAPPER}} .rm-team-card__name' => 'color: {{VALUE}};' ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'role_typo',
                'label' => __( 'Role Typography', 'rm-elementor-theme' ),
                'selector' => '{{WRAPPER}} .rm-team-card__role',
            ]
        );

        $this->add_control(
            'role_color',
            [
                'label' => __( 'Role Color', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [ '{{WRAPPER}} .rm-team-card__role' => 'color: {{VALUE}};' ],
            ]
        );

        $this->add_control(
            'grid_gap',
            [
                'label' => __( 'Grid Gap', 'rm-elementor-theme' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [ 'px' => [ 'min' => 0, 'max' => 60, 'step' => 1 ] ],
                'default' => [ 'size' => 36, 'unit' => 'px' ],
                'selectors' => [ '{{WRAPPER}} .rm-team-grid' => 'gap: {{SIZE}}{{UNIT}};' ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $ppp = ! empty( $settings['posts_per_page'] ) ? intval( $settings['posts_per_page'] ) : -1;
        $order = $settings['order'] === 'DESC' ? 'DESC' : 'ASC';

        $q = new \WP_Query([
            'post_type' => 'rm_team_member',
            'posts_per_page' => $ppp,
            'orderby' => 'menu_order title',
            'order' => $order,
            'post_status' => 'publish',
            'no_found_rows' => true,
        ]);

        echo '<div class="rm-team">';
        echo '<div class="rm-team-grid">';
        if ( $q->have_posts() ) {
            while ( $q->have_posts() ) { $q->the_post();
                $post_id = get_the_ID();
                $name = get_the_title();
                $role = function_exists('get_field') ? ( get_field('role', $post_id) ?: '' ) : get_post_meta($post_id, 'role', true);
                $img_id = get_post_thumbnail_id( $post_id );
                $img = $img_id ? wp_get_attachment_image( $img_id, 'large', false, array('class' => 'rm-team-card__img-el', 'loading' => 'lazy') ) : '';
                echo '<article class="rm-team-card">';
                echo '<div class="rm-team-card__image">' . $img . '</div>';
                echo '<div class="rm-team-card__footer">';
                echo '<div class="rm-team-card__text">';
                echo '<h3 class="rm-team-card__name">' . esc_html( $name ) . '</h3>';
                if ( $role ) {
                    echo '<p class="rm-team-card__role">' . esc_html( $role ) . '</p>';
                }
                echo '</div>';
                echo '</div>';
                echo '</article>';
            }
            wp_reset_postdata();
        }
        echo '</div></div>';
    }
}

/**
 * RM Carousel Widget
 */
class RM_Carousel_Widget extends \Elementor\Widget_Base {
    public function get_name() { return 'rm-carousel'; }
    public function get_title() { return __( 'RM Carousel', 'rm-elementor-theme' ); }
    public function get_icon() { return 'eicon-slider-3d'; }
    public function get_categories() { return ['reelmetrics']; }
    public function get_style_depends() { return ['rm-carousel-style']; }
    public function get_script_depends() { return ['rm-carousel-script']; }

    protected function register_controls() {
        $this->start_controls_section('content', [ 'label' => __( 'Slides', 'rm-elementor-theme' ) ]);
        $rep = new \Elementor\Repeater();
        $rep->add_control('image', [ 'label' => __( 'Image', 'rm-elementor-theme' ), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()] ]);
        $rep->add_control('caption', [ 'label' => __( 'Caption (optional)', 'rm-elementor-theme' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ]);
        $this->add_control('slides', [ 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $rep->get_controls(), 'default' => [ ['caption' => 'Subscription of the image'] ] ]);
        $this->end_controls_section();

        $this->start_controls_section('style', [ 'label' => __( 'Style', 'rm-elementor-theme' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]);
        $this->add_control('radius', [ 'label' => __( 'Image Radius', 'rm-elementor-theme' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px'=>['min'=>0,'max'=>80]], 'default' => ['size'=>40,'unit'=>'px'], 'selectors' => [ '{{WRAPPER}} .rm-carousel__image' => 'border-radius: {{SIZE}}{{UNIT}};' ] ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        if ( empty( $s['slides'] ) ) return;
        $uid = 'rmc_' . wp_generate_password( 6, false, false );
        echo '<div class="rm-carousel" role="region" aria-label="Image carousel">';
        echo '<div class="rm-carousel__viewport" id="' . esc_attr( $uid ) . '" tabindex="0" aria-roledescription="carousel" aria-live="polite">';
        echo '<div class="rm-carousel__track" role="listbox">';
        foreach ( $s['slides'] as $slide ) {
            echo '<figure class="rm-carousel__slide" role="option" aria-selected="false">';
            if ( ! empty( $slide['image']['url'] ) ) {
                echo '<img class="rm-carousel__image" src="' . esc_url( $slide['image']['url'] ) . '" alt="' . esc_attr( $slide['caption'] ) . '" loading="lazy" decoding="async" />';
            }
            if ( ! empty( $slide['caption'] ) ) {
                echo '<figcaption class="rm-carousel__caption">' . esc_html( $slide['caption'] ) . '</figcaption>';
            }
            echo '</figure>';
        }
        echo '</div></div>'; // track, viewport
        
        // Caption and controls container for mobile layout
        echo '<div class="rm-carousel__footer">';
        echo '<div class="rm-carousel__caption-display"></div>'; // Will be populated by JS
        echo '<div class="rm-carousel__controls">';
        echo '<button class="rm-carousel__btn" type="button" aria-label="Previous" aria-controls="' . esc_attr( $uid ) . '" data-dir="prev">&#8592;</button>';
        echo '<button class="rm-carousel__btn" type="button" aria-label="Next" aria-controls="' . esc_attr( $uid ) . '" data-dir="next">&#8594;</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

/**
 * RM Buzzsprout Player Widget
 * - Pulls Buzzsprout episode ID from ACF (reelcast) or manual input
 * - Renders the Buzzsprout embed (small/large player)
 */
class RM_Buzzsprout_Widget extends \Elementor\Widget_Base {
    public function get_name() { return 'rm-buzzsprout-player'; }
    public function get_title() { return __( 'RM Buzzsprout Player', 'rm-elementor-theme' ); }
    public function get_icon() { return 'eicon-play'; }
    public function get_categories() { return [ 'reelmetrics' ]; }

    protected function register_controls() {
        $this->start_controls_section('source', [ 'label' => __( 'Source', 'rm-elementor-theme' ) ]);

        $this->add_control('use_acf', [
            'label' => __( 'Load episode from ACF (reelcast)', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => __( 'Yes', 'rm-elementor-theme' ),
            'label_off' => __( 'No', 'rm-elementor-theme' ),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('episode_id', [
            'label' => __( 'Episode ID (e.g., 17757499-s04e07-... )', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => '17757499-s04e07-professor-anthony-lucas-part-one',
            'condition' => [ 'use_acf!' => 'yes' ]
        ]);

        $this->add_control('podcast_id', [
            'label' => __( 'Podcast ID', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '2057836',
        ]);

        $this->add_control('player_size', [
            'label' => __( 'Player Size', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'small',
            'options' => [ 'small' => 'Small', 'large' => 'Large' ],
        ]);

        $this->add_control('use_episodes_segment', [
            'label' => __( 'Use /episodes/ path segment', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('advanced', [ 'label' => __( 'Advanced', 'rm-elementor-theme' ) ]);
        $this->add_control('container_prefix', [
            'label' => __( 'Container ID Prefix', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'buzzsprout-player-',
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        // Get episode id
        $episode_full_id = '';
        if ( $s['use_acf'] === 'yes' && function_exists('get_field') ) {
            $episode_full_id = (string) get_field('buzzsprout_id');
        }
        if ( ! $episode_full_id && ! empty( $s['episode_id'] ) ) {
            $episode_full_id = (string) $s['episode_id'];
        }
        $episode_full_id = trim( $episode_full_id );
        if ( $episode_full_id === '' ) return;

        // Extract numeric id for container (first digits before hyphen)
        $numeric_id = '';
        if ( preg_match('/^(\d+)/', $episode_full_id, $m) ) {
            $numeric_id = $m[1];
        } else {
            // Fallback: strip non-digits to build a safe id
            $numeric_id = preg_replace('/\D+/', '', $episode_full_id);
            if ( $numeric_id === '' ) {
                $numeric_id = substr( md5( $episode_full_id ), 0, 8 );
            }
        }

        $podcast_id = trim( (string) $s['podcast_id'] );
        if ( $podcast_id === '' ) { $podcast_id = '2057836'; }
        $player_size = in_array( $s['player_size'], [ 'small', 'large' ], true ) ? $s['player_size'] : 'small';
        $container_prefix = $s['container_prefix'] !== '' ? $s['container_prefix'] : 'buzzsprout-player-';
        $container_id = $container_prefix . $numeric_id;
        $use_episodes = ($s['use_episodes_segment'] === 'yes');

        $base = 'https://www.buzzsprout.com/' . rawurlencode( $podcast_id ) . '/';
        $path = ($use_episodes ? 'episodes/' : '') . rawurlencode( $episode_full_id ) . '.js';
        $src = $base . $path . '?container_id=' . rawurlencode( $container_id ) . '&player=' . rawurlencode( $player_size );

        echo '<div id="' . esc_attr( $container_id ) . '"></div>';
        echo '<script src="' . esc_url( $src ) . '" type="text/javascript" charset="utf-8"></script>';
    }
}

/**
 * RM Topics List (Two-Column) Widget
 */
class RM_Topics_List_Widget extends \Elementor\Widget_Base {
    public function get_name() { return 'rm-topics-list'; }
    public function get_title() { return __( 'RM Topics List', 'rm-elementor-theme' ); }
    public function get_icon() { return 'eicon-bullet-list'; }
    public function get_categories() { return [ 'reelmetrics' ]; }

    protected function register_controls() {
        // Source controls
        $this->start_controls_section('content', [ 'label' => __( 'Content', 'rm-elementor-theme' ) ]);

        $this->add_control('use_acf', [
            'label' => __( 'Load from ACF (reelcast → topics)', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => __( 'Yes', 'rm-elementor-theme' ),
            'label_off' => __( 'No', 'rm-elementor-theme' ),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $rep = new \Elementor\Repeater();
        $rep->add_control('text', [ 'label' => __( 'Topic', 'rm-elementor-theme' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'Topic text', 'rm-elementor-theme' ) ]);
        $this->add_control('items', [
            'label' => __( 'Manual Topics', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $rep->get_controls(),
            'default' => [],
            'title_field' => '{{{ text }}}',
            'condition' => [ 'use_acf!' => 'yes' ]
        ]);

        $this->end_controls_section();

        // Icon controls
        $this->start_controls_section('icon', [ 'label' => __( 'Icon', 'rm-elementor-theme' ) ]);
        $this->add_control('use_custom_icon', [
            'label' => __( 'Use custom image icon', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);
        $this->add_control('icon_media', [
            'label' => __( 'Icon Image', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'condition' => [ 'use_custom_icon' => 'yes' ],
        ]);
        $this->add_control('icon_color', [
            'label' => __( 'Icon Color', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#5E55FC',
            'condition' => [ 'use_custom_icon!' => 'yes' ],
        ]);
        $this->add_control('icon_size', [
            'label' => __( 'Icon Size (px)', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range' => [ 'px' => [ 'min' => 4, 'max' => 24, 'step' => 1 ] ],
            'default' => [ 'size' => 8, 'unit' => 'px' ],
            'condition' => [ 'use_custom_icon!' => 'yes' ],
        ]);
        $this->end_controls_section();

        // Style controls
        $this->start_controls_section('style', [ 'label' => __( 'Style', 'rm-elementor-theme' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]);
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name' => 'text_typo',
            'selector' => '{{WRAPPER}} .rm-topics__text',
        ]);
        $this->add_control('text_color', [
            'label' => __( 'Text Color', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .rm-topics__text' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control('row_gap', [
            'label' => __( 'Row Gap (px)', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range' => [ 'px' => [ 'min' => 0, 'max' => 40, 'step' => 1 ] ],
            'default' => [ 'size' => 8, 'unit' => 'px' ],
        ]);
        $this->add_control('column_gap', [
            'label' => __( 'Column Gap (px)', 'rm-elementor-theme' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range' => [ 'px' => [ 'min' => 0, 'max' => 80, 'step' => 1 ] ],
            'default' => [ 'size' => 36, 'unit' => 'px' ],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        // Resolve topics
        $topics = [];
        if ( $s['use_acf'] === 'yes' && function_exists('get_field') && is_singular('reelcast') ) {
            $raw = get_field('topics');
            if ( is_array($raw) ) {
                foreach ($raw as $row) {
                    if ( isset($row['topic']) && $row['topic'] !== '' ) {
                        $topics[] = $row['topic'];
                    }
                }
            }
        }
        if ( empty($topics) && ! empty($s['items']) && is_array($s['items']) ) {
            foreach ($s['items'] as $it) {
                if ( ! empty($it['text']) ) { $topics[] = $it['text']; }
            }
        }
        if ( empty($topics) ) { return; }

        $count = count($topics);
        $left_count = (int)ceil($count / 2);
        $left = array_slice($topics, 0, $left_count);
        $right = array_slice($topics, $left_count);

        $row_gap = isset($s['row_gap']['size']) ? (int)$s['row_gap']['size'] : 8;
        $col_gap = isset($s['column_gap']['size']) ? (int)$s['column_gap']['size'] : 36;
        $icon_size = isset($s['icon_size']['size']) ? (int)$s['icon_size']['size'] : 8;
        $icon_color = isset($s['icon_color']) && $s['icon_color'] ? $s['icon_color'] : '#5E55FC';
        $use_img = ($s['use_custom_icon'] === 'yes' && ! empty($s['icon_media']['url']));
        $icon_url = $use_img ? $s['icon_media']['url'] : '';

        // Inline styles for layout and icon
        $uid = 'rmt_' . wp_generate_password(6, false, false);
        echo '<style>
            #' . esc_attr($uid) . ' .rm-topics__cols{display:grid;grid-template-columns:1fr 1fr;gap:' . esc_attr($col_gap) . 'px;}
            #' . esc_attr($uid) . ' .rm-topics__item{display:flex;align-items:center;gap:10px;margin:0 0 ' . esc_attr($row_gap) . 'px 0;}
            #' . esc_attr($uid) . ' .rm-topics__icon{width:' . ($use_img ? $icon_size : $icon_size) . 'px;height:' . ($use_img ? $icon_size : $icon_size) . 'px;display:inline-flex;align-items:center;justify-content:center}
            #' . esc_attr($uid) . ' .rm-topics__diamond{width:' . esc_attr($icon_size) . 'px;height:' . esc_attr($icon_size) . 'px;background:' . esc_attr($icon_color) . ';transform:rotate(-45deg)}
            @media (max-width: 768px){#' . esc_attr($uid) . ' .rm-topics__cols{grid-template-columns:1fr}}
        </style>';

        echo '<div id="' . esc_attr($uid) . '" class="rm-topics rm-body-text-small" role="list">';
        echo '<div class="rm-topics__cols">';

        $render_col = function($arr) use ($use_img, $icon_url) {
            echo '<ul class="rm-topics__col">';
            foreach ($arr as $t) {
                echo '<li class="rm-topics__item">';
                echo '<span class="rm-topics__icon">';
                if ($use_img) {
                    echo '<img src="' . esc_url($icon_url) . '" alt="" loading="lazy" decoding="async" />';
                } else {
                    echo '<span class="rm-topics__diamond" aria-hidden="true"></span>';
                }
                echo '</span>';
                echo '<span class="rm-topics__text">' . esc_html($t) . '</span>';
                echo '</li>';
            }
            echo '</ul>';
        };

        $render_col($left);
        $render_col($right);

        echo '</div></div>';
    }
}