<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Register Comparison CPT for plan/feature tables
add_action('init', function() {
    $labels = array(
        'name'               => 'Comparisons',
        'singular_name'      => 'Comparison',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Comparison',
        'edit_item'          => 'Edit Comparison',
        'new_item'           => 'New Comparison',
        'view_item'          => 'View Comparison',
        'search_items'       => 'Search Comparisons',
        'not_found'          => 'No comparisons found',
        'not_found_in_trash' => 'No comparisons found in Trash',
        'menu_name'          => 'Comparisons',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'revisions' ),
        'menu_icon'          => 'dashicons-table-col-after',
        'has_archive'        => false,
        'rewrite'            => array( 'slug' => 'comparison' ),
    );

    register_post_type( 'comparison', $args );
}, 10 );


