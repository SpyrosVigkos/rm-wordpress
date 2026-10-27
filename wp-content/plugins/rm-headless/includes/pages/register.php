<?php
/**
 * Attaches each page's field group to the page with its slug. The Site page
 * holds site-wide content; it is private, so only rm/v1/site exposes it.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function rm_page_id( $slug ) {
    $page = get_page_by_path( $slug, OBJECT, 'page' );
    return $page ? (int) $page->ID : 0;
}

function rm_site_page_id() {
    $id = rm_page_id( 'site' );
    if ( $id ) {
        return $id;
    }
    return (int) wp_insert_post( array(
        'post_type'   => 'page',
        'post_status' => 'private',
        'post_title'  => 'Site',
        'post_name'   => 'site',
    ) );
}

function rm_register_page_groups() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }
    foreach ( rm_page_schemas() as $key => $page ) {
        $id = $key === 'site' ? rm_site_page_id() : rm_page_id( $page['slug'] );
        if ( ! $id ) {
            continue;
        }
        acf_add_local_field_group( array(
            'key'            => 'group_rm_page_' . $key,
            'title'          => ucfirst( $key ) . ' page',
            'fields'         => rm_fields_to_acf( $key, $page['schema'] ),
            'location'       => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $id ) ) ),
            'position'       => 'acf_after_title',
            'hide_on_screen' => array( 'the_content', 'excerpt', 'discussion', 'comments', 'featured_image' ),
            'show_in_rest'   => 1,
        ) );
    }
}
add_action( 'acf/init', 'rm_register_page_groups' );
