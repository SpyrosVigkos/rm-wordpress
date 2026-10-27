<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register Notification CPT and attach same taxonomies as default posts
add_action('init', function() {
    $labels = array(
        'name'               => 'Notifications',
        'singular_name'      => 'Notification',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Notification',
        'edit_item'          => 'Edit Notification',
        'new_item'           => 'New Notification',
        'view_item'          => 'View Notification',
        'search_items'       => 'Search Notifications',
        'not_found'          => 'No notifications found',
        'not_found_in_trash' => 'No notifications found in Trash',
        'menu_name'          => 'Notifications',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author' ),
        'menu_icon'          => 'dashicons-megaphone',
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'notifications' ),
        'taxonomies'         => get_object_taxonomies( 'post' ),
    );

    register_post_type( 'notification', $args );

    // Ensure all 'post' taxonomies are explicitly attached as well
    foreach ( get_object_taxonomies( 'post' ) as $tax ) {
        register_taxonomy_for_object_type( $tax, 'notification' );
    }
}, 10 );

// One-time rewrite flush after first deployment
add_action('init', function() {
    if ( get_option( 'rm_flush_rewrite_notifications_done' ) ) {
        return;
    }
    flush_rewrite_rules();
    update_option( 'rm_flush_rewrite_notifications_done', 1 );
}, 99 );


