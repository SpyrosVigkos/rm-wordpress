<?php
/**
 * WordPress only holds content here; visitors use the Astro site.
 * Front-end requests get a short page instead of a themed one.
 *
 * @package RMHeadlessTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

status_header( 200 );
nocache_headers();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="robots" content="noindex, nofollow">
<title><?php bloginfo( 'name' ); ?></title>
</head>
<body>
<p>This WordPress only holds content for reelmetrics.com.</p>
<p><a href="<?php echo esc_url( admin_url() ); ?>">Edit content</a> &middot; <a href="<?php echo esc_url( rest_url() ); ?>">REST API</a></p>
</body>
</html>
