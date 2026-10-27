<?php
/**
 * GET rm/v1/page/<key> and rm/v1/site: a fixed page's content in the shape the
 * Astro site renders, plus the parts composed from other post types: the team
 * on About (alphabetical, as on staging), the 6 newest ReelHot entries on
 * Publications and the 4 newest notifications in the footer.
 * GET rm/v1/media-id?file=YYYY/MM/name.ext: the attachment for an uploads path,
 * for the seed script (needs upload_files).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const RM_LATEST_INSIGHTS     = 4;
const RM_PUBLICATIONS_REELHOT = 6;

function rm_page_post( $key ) {
    $schemas = rm_page_schemas();
    if ( ! isset( $schemas[ $key ] ) ) {
        return null;
    }
    $page = get_page_by_path( $schemas[ $key ]['slug'], OBJECT, 'page' );
    return $page ?: null;
}

/** Fields of $source_id (the page, or its autosave in a preview) as site content. */
function rm_page_content( $key, $source_id ) {
    $schemas = rm_page_schemas();
    $values  = function_exists( 'get_fields' ) ? ( get_fields( $source_id ) ?: array() ) : array();
    $content = rm_fields_to_site( $schemas[ $key ]['schema'], $values, $key );
    return rm_compose_page( $key, $content );
}

function rm_published( $type, array $args = array() ) {
    return get_posts( array_merge( array(
        'post_type'        => $type,
        'post_status'      => 'publish',
        'numberposts'      => -1,
        'suppress_filters' => false,
    ), $args ) );
}

function rm_compose_page( $key, array $content ) {
    if ( $key === 'about' ) {
        $content['team'] = array_map( function ( $member ) {
            $photo = function_exists( 'get_field' ) ? get_field( 'photo', $member->ID, false ) : 0;
            $photo = $photo ? $photo : get_post_thumbnail_id( $member->ID );
            return array(
                'name'  => get_the_title( $member ),
                'role'  => (string) ( function_exists( 'get_field' ) ? get_field( 'role', $member->ID ) : '' ),
                'photo' => rm_image_out( $photo ) ?: array( 'src' => '', 'alt' => '' ),
            );
        }, rm_published( 'rm_team_member', array( 'orderby' => 'title', 'order' => 'ASC' ) ) );
    }
    if ( $key === 'publications' ) {
        $content['reelhot']['entries'] = array_map( function ( $entry ) {
            return array(
                'title' => get_the_title( $entry ),
                'href'  => '/reelhot/' . $entry->post_name,
                'image' => rm_image_out( get_post_thumbnail_id( $entry->ID ) ) ?: array( 'src' => '', 'alt' => '' ),
            );
        }, rm_published( 'reelhot', array( 'orderby' => 'date', 'order' => 'DESC', 'numberposts' => RM_PUBLICATIONS_REELHOT ) ) );
    }
    if ( $key === 'site' ) {
        $content['layout']['latestInsights'] = array_map( function ( $notification ) {
            return array( 'label' => html_entity_decode( get_the_title( $notification ), ENT_QUOTES ), 'href' => '/notifications/' . $notification->post_name );
        }, rm_published( 'notification', array( 'orderby' => 'date', 'order' => 'DESC', 'numberposts' => RM_LATEST_INSIGHTS ) ) );
    }
    return $content;
}

function rm_page_envelope( $key, WP_Post $page, $source_id ) {
    return array(
        'key'      => $key,
        'slug'     => $page->post_name,
        'modified' => get_post_modified_time( 'c', true, $page ),
        'content'  => rm_page_content( $key, $source_id ),
    );
}

function rm_page_response( $key ) {
    $page = rm_page_post( $key );
    if ( ! $page || ( $key !== 'site' && $page->post_status !== 'publish' ) ) {
        return new WP_Error( 'rm_page_not_found', "No published page for $key", array( 'status' => 404 ) );
    }
    try {
        return rest_ensure_response( rm_page_envelope( $key, $page, $page->ID ) );
    } catch ( RM_Missing_Field $e ) {
        return new WP_Error( 'rm_missing_field', $e->getMessage(), array( 'status' => 500 ) );
    }
}

/** The page key for a page post, or null when it is not a fixed page. */
function rm_page_key( WP_Post $post ) {
    foreach ( rm_page_schemas() as $key => $page ) {
        if ( $page['slug'] === $post->post_name ) {
            return $key;
        }
    }
    return null;
}

function rm_media_id( $file ) {
    global $wpdb;
    $file = ltrim( (string) $file, '/' );
    $candidates = array( $file, preg_replace( '/-\d+x\d+(\.[a-z0-9]+)$/i', '$1', $file ), preg_replace( '/-scaled(\.[a-z0-9]+)$/i', '$1', $file ) );
    foreach ( array_unique( $candidates ) as $candidate ) {
        $id = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND ( meta_value = %s OR meta_value = %s ) LIMIT 1",
            $candidate,
            preg_replace( '/(\.[a-z0-9]+)$/i', '-scaled$1', $candidate )
        ) );
        if ( $id ) {
            return $id;
        }
    }
    return 0;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'rm/v1', '/page/(?P<key>[a-z_-]+)', array(
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function ( WP_REST_Request $request ) {
            return $request['key'] === 'site'
                ? new WP_Error( 'rm_page_not_found', 'Use rm/v1/site', array( 'status' => 404 ) )
                : rm_page_response( $request['key'] );
        },
    ) );
    register_rest_route( 'rm/v1', '/site', array(
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function () {
            return rm_page_response( 'site' );
        },
    ) );
    register_rest_route( 'rm/v1', '/media-id', array(
        'methods'             => 'GET',
        'permission_callback' => function () {
            return current_user_can( 'upload_files' );
        },
        'callback'            => function ( WP_REST_Request $request ) {
            $id = rm_media_id( $request->get_param( 'file' ) );
            return $id ? array( 'id' => $id ) : new WP_Error( 'rm_media_not_found', 'Not in the media library', array( 'status' => 404 ) );
        },
    ) );
} );
