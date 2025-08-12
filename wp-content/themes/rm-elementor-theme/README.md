# ReelMetrics Elementor Theme

A professional WordPress child theme extending Hello Elementor with the complete ReelMetrics design system.

## 🎨 Features

- **Complete Design System**: Blurple color palette, Helvetica Neue typography
- **Elementor Integration**: Custom widgets, global colors, and typography
- **CDN Fonts**: Helvetica Neue loaded from CDN with fallbacks
- **Responsive Design**: Mobile-first approach with proper breakpoints
- **Custom Components**: Buttons, navigation, typography widgets

## 📋 Requirements

- **WordPress**: 6.0 or higher
- **PHP**: 7.4 or higher
- **Parent Theme**: Hello Elementor
- **Plugin**: Elementor Page Builder

## 🚀 Installation

1. **Install Hello Elementor parent theme**
2. **Install and activate Elementor plugin**
3. **Upload this theme to** `/wp-content/themes/`
4. **Activate the theme** in WordPress admin
5. **Go to** Elementor → Tools → Regenerate Files & Data
6. **Clear all caches**

## 🎯 Usage

### Typography

- Use ReelMetrics typography classes: `.rm-hero-large`, `.rm-body-text`, etc.
- In Elementor: Select "ReelMetrics → Helvetica Neue" from font dropdown
- Global typography presets available in Elementor Site Settings

### Colors

- CSS variables: `--rm-blurple`, `--rm-black`, `--rm-red`, etc.
- Elementor global colors automatically configured
- ReelMetrics color palette available in all color pickers

### Widgets

Custom Elementor widgets in "ReelMetrics" category:

- **RM Typography**: Pre-styled text with design system typography
- **RM Button**: Branded buttons with proper styling
- **RM Navigation**: Complete navigation with mobile menu

## 🎨 Design System

### Colors

- **Primary**: #5E55FC (Blurple)
- **Secondary**: #292929 (Black)
- **Accent**: #FF3E3E (Red)
- **Text**: #292929 (Black)
- **Background**: #FFFFFF (White)

### Typography Scale

- **Hero Large**: 95px (50px mobile)
- **Intro Large**: 56px (26px mobile)
- **Subheading**: 28px (22px mobile)
- **Body Large**: 20px (18px mobile)
- **Body Text**: 18px (18px mobile)

### Font Weights

- **Light**: 300
- **Regular**: 400
- **Medium**: 500
- **Bold**: 700

## 📁 File Structure

```
rm-elementor-theme/
├── functions.php           # Main theme functions
├── style.css              # Theme header and basic styles
├── assets/
│   ├── css/
│   │   ├── fonts.css       # CDN font loading
│   │   ├── colors.css      # Color system
│   │   ├── typography.css  # Typography system
│   │   ├── buttons.css     # Button components
│   │   └── navigation.css  # Navigation components
│   ├── js/
│   │   └── main.js         # Theme JavaScript
│   └── scss/               # Source SCSS files
├── includes/
│   ├── elementor-integration.php    # Elementor hooks and filters
│   ├── elementor-global-settings.php # Global color/typography injection
│   └── elementor-widgets.php        # Custom widgets
└── templates/              # Custom page templates
```

## 🔧 Customization

### Adding New Colors

1. Add CSS variable to `assets/css/colors.css`
2. Register in Elementor via `includes/elementor-integration.php`

### Adding Typography Styles

1. Add class to `assets/css/typography.css`
2. Register in Elementor global typography

### Custom Widgets

1. Create widget class in `includes/elementor-widgets.php`
2. Register in `rm_register_elementor_widgets()`

## 📊 Performance

- **CDN Fonts**: Fast loading from external CDN
- **Optimized CSS**: Modular stylesheets with proper dependencies
- **Minimal JS**: Only essential JavaScript included
- **Caching Friendly**: Proper versioning for cache busting

## 🐛 Troubleshooting

### Fonts Not Loading

1. Check CDN accessibility: `https://fonts.cdnfonts.com/css/helvetica-neue-55`
2. Clear Elementor cache: Tools → Regenerate Files & Data
3. Verify font appears in Elementor typography dropdown

### Colors Not Appearing

1. Clear all caches (WordPress, Elementor, plugins)
2. Check Elementor Site Settings → Global Colors
3. Verify CSS variables in browser dev tools

### Widgets Missing

1. Ensure Elementor plugin is active
2. Check "ReelMetrics" category in widget panel
3. Clear Elementor cache

## 📝 Changelog

### v1.0.0

- Initial release
- Complete ReelMetrics design system
- Elementor integration
- Custom widgets and components
- CDN font loading

## 📄 License

MIT License - See LICENSE file for details

## 🤝 Support

For theme support and customization, contact the ReelMetrics development team.

---

_ReelMetrics Elementor Theme - Professional WordPress theme with complete design system integration._
