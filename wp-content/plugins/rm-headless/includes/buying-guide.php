<?php
/**
 * Buying guide: post type, taxonomy, ACF fields and term ACF in REST.
 * Copied from the rm-elementor-theme functions.php; keep the two in step
 * until the theme no longer registers it.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Buying Guide: Register CPT and taxonomy (1 CPT + taxonomy model)
 */
function rm_register_buying_guide_types() {
    // CPT: buying_guide_item
    register_post_type( 'buying_guide_item', array(
        'label' => 'Buying Guide Items',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'supports' => array( 'title', 'revisions' ),
        'menu_icon' => 'dashicons-list-view',
        'has_archive' => false,
        'rewrite' => array( 'slug' => 'buying-guide-item' ),
    ) );

    // Taxonomy: buying_guide_category (attached to items)
    register_taxonomy( 'buying_guide_category', array( 'buying_guide_item' ), array(
        'hierarchical' => false,
        'labels' => array(
            'name' => 'Buying Guide Categories',
            'singular_name' => 'Buying Guide Category',
        ),
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'buying-guide-category' ),
        'show_in_rest' => true,
    ) );

    // Ensure taxonomy is attached to CPT even if registration order changes
    register_taxonomy_for_object_type( 'buying_guide_category', 'buying_guide_item' );
}
add_action( 'init', 'rm_register_buying_guide_types' );

/**
 * Buying Guide: ACF groups (items + taxonomy term fields)
 */
function rm_register_buying_guide_acf() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    // Item fields
    acf_add_local_field_group( array(
        'key' => 'group_rm_buying_guide_item',
        'title' => 'Buying Guide Item',
        'fields' => array(
            array(
                'key' => 'field_rm_bg_parameters',
                'label' => 'Parameters (JSON)',
                'name' => 'parameters',
                'type' => 'textarea',
                'new_lines' => 'br',
                'instructions' => 'Paste the JSON string used by the frontend to build report queries.',
            ),
            array(
                'key' => 'field_rm_bg_order',
                'label' => 'Order',
                'name' => 'order',
                'type' => 'number',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_rm_bg_butter_id',
                'label' => 'ButterCMS ID',
                'name' => 'butter_id',
                'type' => 'number',
                'instructions' => 'Optional: original ButterCMS numeric ID for idempotent migrations.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'buying_guide_item',
                ),
            ),
        ),
        'show_in_rest' => 1,
        'position' => 'acf_after_title',
        'style' => 'seamless',
    ) );

    // Taxonomy term fields (category meta)
    acf_add_local_field_group( array(
        'key' => 'group_rm_buying_guide_category',
        'title' => 'Buying Guide Category Fields',
        'fields' => array(
            array(
                'key' => 'field_rm_bg_cat_description',
                'label' => 'Description',
                'name' => 'description',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'media_upload' => 0,
            ),
            array(
                'key' => 'field_rm_bg_cat_order',
                'label' => 'Order',
                'name' => 'order',
                'type' => 'number',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_rm_bg_cat_active',
                'label' => 'Active',
                'name' => 'active',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
                'instructions' => 'Mirrors ButterCMS "published" flag for categories.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'buying_guide_category',
                ),
            ),
        ),
        'show_in_rest' => 1,
        'position' => 'normal',
        'style' => 'default',
    ) );
}
add_action( 'acf/init', 'rm_register_buying_guide_acf' );

/**
 * Buying Guide: Expose taxonomy term ACF on REST responses
 */
function rm_buying_guide_register_rest_fields() {
    register_rest_field( 'buying_guide_category', 'acf', array(
        'get_callback' => function( $term_array ) {
            if ( ! function_exists( 'get_fields' ) ) return null;
            $term_id = isset( $term_array['id'] ) ? (int) $term_array['id'] : 0;
            if ( ! $term_id ) return null;
            // ACF stores term meta under term_{id}
            return get_fields( 'term_' . $term_id ) ?: new stdClass();
        },
        'schema' => array(
            'description' => 'ACF fields for buying_guide_category term',
            'type' => 'object',
        ),
    ) );
}
add_action( 'rest_api_init', 'rm_buying_guide_register_rest_fields' );
