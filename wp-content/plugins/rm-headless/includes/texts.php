<?php
/**
 * Texts: post type and ACF fields.
 * Copied from the rm-elementor-theme functions.php; keep the two in step
 * until the theme no longer registers it.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Texts: Register CPT for site text snippets and ACF fields
 * Mirrors ButterCMS texts: content_key, title, text
 */
function rm_register_texts_cpt() {
    register_post_type( 'rm_text', array(
        'label' => 'Texts',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'supports' => array( 'title', 'revisions' ),
        'menu_icon' => 'dashicons-editor-quote',
        'has_archive' => false,
        'rewrite' => array( 'slug' => 'text' ),
    ) );
}
add_action( 'init', 'rm_register_texts_cpt' );

function rm_register_texts_acf() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    acf_add_local_field_group( array(
        'key' => 'group_rm_text',
        'title' => 'Text Fields',
        'fields' => array(
            array(
                'key' => 'field_rm_text_content_key',
                'label' => 'Content Key',
                'name' => 'content_key',
                'type' => 'text',
                'instructions' => 'Unique key used by frontend to locate the text',
            ),
            array(
                'key' => 'field_rm_text_title',
                'label' => 'Internal Title',
                'name' => 'butter_title',
                'type' => 'text',
                'instructions' => 'Optional: original ButterCMS title',
            ),
            array(
                'key' => 'field_rm_text_text',
                'label' => 'Text',
                'name' => 'text',
                'type' => 'textarea',
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_rm_text_butter_id',
                'label' => 'ButterCMS ID',
                'name' => 'butter_id',
                'type' => 'number',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'rm_text',
                ),
            ),
        ),
        'show_in_rest' => 1,
        'position' => 'acf_after_title',
        'style' => 'seamless',
    ) );
}
add_action( 'acf/init', 'rm_register_texts_acf' );
