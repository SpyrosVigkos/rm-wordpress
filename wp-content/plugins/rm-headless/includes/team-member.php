<?php
/**
 * Team members: post type and ACF fields.
 * Copied from the rm-elementor-theme functions.php; keep the two in step
 * until the theme no longer registers it.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register CPT: Team Member (CPT UI is installed, but we ensure it's registered programmatically too)
 */
function rm_register_team_member_cpt() {
    $labels = array(
        'name'               => 'Team Members',
        'singular_name'      => 'Team Member',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Team Member',
        'edit_item'          => 'Edit Team Member',
        'new_item'           => 'New Team Member',
        'view_item'          => 'View Team Member',
        'search_items'       => 'Search Team Members',
        'not_found'          => 'No team members found',
        'not_found_in_trash' => 'No team members found in Trash',
        'menu_name'          => 'Team Members',
    );
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'menu_icon'          => 'dashicons-groups',
        'has_archive'        => false,
        'rewrite'            => array( 'slug' => 'team' ),
    );
    register_post_type( 'rm_team_member', $args );
}
add_action( 'init', 'rm_register_team_member_cpt' );

/**
 * Ensure ACF fields exist (name, role, dob, photo). If ACF JSON/GUI exists, this no-ops.
 */
function rm_register_team_member_acf() {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group(array(
            'key' => 'group_rm_team_member',
            'title' => 'Team Member',
            'fields' => array(
                array(
                    'key' => 'field_rm_tm_role',
                    'label' => 'Role',
                    'name' => 'role',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_rm_tm_dob',
                    'label' => 'Date of Birth',
                    'name' => 'dob',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_rm_tm_photo',
                    'label' => 'Photo',
                    'name' => 'photo',
                    'type' => 'image',
                    'return_format' => 'id',
                    'preview_size' => 'medium',
                ),
                array(
                    'key' => 'field_rm_tm_butter_id',
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
                        'value' => 'rm_team_member',
                    ),
                ),
            )
        ));
    }
}
add_action( 'acf/init', 'rm_register_team_member_acf' );
