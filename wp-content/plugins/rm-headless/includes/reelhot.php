<?php
/**
 * ReelHot indexes: post type, ACF fields, settings and image fallback.
 * Copied from the rm-elementor-theme functions.php; keep the two in step
 * until the theme no longer registers it.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ReelHot Indexes - CPT, ACF, Options, and image fallback
 */
function rm_register_reelhot_cpt() {
    $labels = array(
        'name'               => 'ReelHot Indexes',
        'singular_name'      => 'ReelHot Index',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New ReelHot Index',
        'edit_item'          => 'Edit ReelHot Index',
        'new_item'           => 'New ReelHot Index',
        'view_item'          => 'View ReelHot Index',
        'search_items'       => 'Search ReelHot Indexes',
        'not_found'          => 'No items found',
        'not_found_in_trash' => 'No items found in Trash',
        'menu_name'          => 'ReelHot',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'thumbnail', 'excerpt' ),
        'menu_icon'          => 'dashicons-chart-line',
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'reelhot' ),
        'hierarchical'       => false,
        'query_var'          => true,
        'can_export'         => true,
        'capability_type'    => 'post',
    );

    register_post_type( 'reelhot', $args );
}
add_action( 'init', 'rm_register_reelhot_cpt' );

function rm_register_reelhot_acf() {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group(array(
            'key' => 'group_reelhot_index',
            'title' => 'ReelHot Index Fields',
            'fields' => array(
                array(
                    'key' => 'field_reelhot_index_date',
                    'label' => 'Index Date',
                    'name' => 'index_date',
                    'type' => 'date_picker',
                    'display_format' => 'Y-m-d',
                    'return_format' => 'Y-m-d',
                    'first_day' => 1,
                    'required' => 1,
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_reelhot_external_url',
                    'label' => 'External URL',
                    'name' => 'external_url',
                    'type' => 'url',
                    'required' => 1,
                    'wrapper' => array( 'width' => '50' ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'reelhot',
                    ),
                ),
            ),
            'position' => 'acf_after_title',
            'style' => 'seamless',
            'active' => true,
            'show_in_rest' => 1,
        ));
    }
}
add_action( 'acf/init', 'rm_register_reelhot_acf' );

function rm_reelhot_register_options_page() {
    if ( function_exists( 'acf_add_options_sub_page' ) ) {
        acf_add_options_sub_page( array(
            'page_title'  => 'ReelHot Settings',
            'menu_title'  => 'Settings',
            'parent_slug' => 'edit.php?post_type=reelhot',
            'menu_slug'   => 'reelhot-settings',
            'capability'  => 'edit_posts',
            'autoload'    => true,
        ) );
    }
}
add_action( 'init', 'rm_reelhot_register_options_page' );

function rm_reelhot_register_options_fields() {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group( array(
            'key' => 'group_reelhot_settings',
            'title' => 'ReelHot Settings',
            'fields' => array(
                array(
                    'key' => 'field_reelhot_default_image',
                    'label' => 'Default ReelHot Image',
                    'name' => 'reelhot_default_image',
                    'type' => 'image',
                    'return_format' => 'id',
                    'preview_size' => 'medium',
                    'instructions' => 'Fallback image for ReelHot posts without a featured image.',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'reelhot-settings',
                    ),
                ),
            ),
            'position' => 'normal',
            'style' => 'default',
            'active' => true,
        ) );
    }
}
add_action( 'acf/init', 'rm_reelhot_register_options_fields' );

function rm_get_reelhot_image_url( $post_id = null, $size = 'large' ) {
    $post_id = $post_id ? $post_id : get_the_ID();
    if ( ! $post_id ) return '';

    $thumb_id = get_post_thumbnail_id( $post_id );
    if ( $thumb_id ) {
        $src = wp_get_attachment_image_url( $thumb_id, $size );
        if ( $src ) return $src;
    }
    if ( function_exists( 'get_field' ) ) {
        $default_id = get_field( 'reelhot_default_image', 'option' );
        if ( $default_id ) {
            $src = wp_get_attachment_image_url( $default_id, $size );
            if ( $src ) return $src;
        }
    }
    return '';
}

function rm_reelhot_thumbnail_fallback( $value, $object_id, $meta_key, $single ) {
    if ( $meta_key !== '_thumbnail_id' || ! $single ) return $value;
    if ( get_post_type( $object_id ) !== 'reelhot' ) return $value;
    remove_filter( 'get_post_metadata', 'rm_reelhot_thumbnail_fallback', 10 );
    $stored_id = get_post_meta( $object_id, '_thumbnail_id', true );
    add_filter( 'get_post_metadata', 'rm_reelhot_thumbnail_fallback', 10, 4 );
    if ( ! empty( $stored_id ) ) return $value;
    if ( function_exists( 'get_field' ) ) {
        $default_id = get_field( 'reelhot_default_image', 'option' );
        if ( $default_id ) return (int) $default_id;
    }
    return $value;
}
add_filter( 'get_post_metadata', 'rm_reelhot_thumbnail_fallback', 10, 4 );

function rm_reelhot_thumbnail_html_fallback( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
    if ( get_post_type( $post_id ) !== 'reelhot' ) return $html;
    if ( $html ) return $html;
    $src = rm_get_reelhot_image_url( $post_id, $size );
    if ( ! $src ) return $html;
    $alt = esc_attr( get_the_title( $post_id ) );
    $class = isset( $attr['class'] ) ? esc_attr( $attr['class'] ) : 'attachment-' . esc_attr( is_string( $size ) ? $size : 'large' );
    return '<img src="' . esc_url( $src ) . '" class="' . $class . ' rm-reelhot-fallback" alt="' . $alt . '" loading="lazy" decoding="async" />';
}
add_filter( 'post_thumbnail_html', 'rm_reelhot_thumbnail_html_fallback', 10, 5 );
