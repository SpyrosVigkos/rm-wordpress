<?php
/**
 * Preview: the editor's Preview button opens the Astro preview server instead
 * of the theme. The link carries a signed token that expires after ten
 * minutes; the preview server sends it back to GET rm/v1/preview/<id>, which
 * answers with the latest autosave (unsaved title and ACF fields) in the same
 * shape as the wp/v2 REST item. No WordPress login is needed on the Astro side.
 *
 * Off unless RM_PREVIEW_URL is defined in wp-config.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const RM_HEADLESS_PREVIEW_TYPES = array( 'reelcast' );
const RM_HEADLESS_PREVIEW_TTL   = 600;

function rm_headless_preview_base() {
    return defined( 'RM_PREVIEW_URL' ) ? untrailingslashit( RM_PREVIEW_URL ) : '';
}

function rm_headless_preview_token( $post_id, $expires ) {
    return $expires . '.' . hash_hmac( 'sha256', $post_id . '|' . $expires, wp_salt( 'auth' ) );
}

function rm_headless_preview_token_valid( $post_id, $token ) {
    $parts = explode( '.', (string) $token, 2 );
    if ( count( $parts ) !== 2 || ! ctype_digit( $parts[0] ) || (int) $parts[0] < time() ) {
        return false;
    }
    return hash_equals( rm_headless_preview_token( $post_id, (int) $parts[0] ), (string) $token );
}

function rm_headless_preview_link( $link, $post ) {
    $base = rm_headless_preview_base();
    if ( $base === '' || ! in_array( $post->post_type, RM_HEADLESS_PREVIEW_TYPES, true ) ) {
        return $link;
    }
    $token = rm_headless_preview_token( $post->ID, time() + RM_HEADLESS_PREVIEW_TTL );
    return add_query_arg( 'token', rawurlencode( $token ), $base . '/preview/' . $post->post_type . '/' . $post->ID );
}
add_filter( 'preview_post_link', 'rm_headless_preview_link', 10, 2 );

/**
 * The block editor does not always use preview_post_link: for drafts, and for
 * published posts without an autosave yet, it opens the post URL with
 * ?preview=true. WordPress has checked the user may preview by the time the
 * template loads, so that request is sent on to the preview server here.
 */
function rm_headless_preview_redirect() {
    if ( ! is_preview() || rm_headless_preview_base() === '' ) {
        return;
    }
    $post = get_queried_object();
    if ( ! $post instanceof WP_Post || ! in_array( $post->post_type, RM_HEADLESS_PREVIEW_TYPES, true ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post->ID ) ) {
        return;
    }
    nocache_headers();
    wp_redirect( rm_headless_preview_link( '', $post ), 302, 'RM Headless' );
    exit;
}
add_action( 'template_redirect', 'rm_headless_preview_redirect' );

function rm_headless_preview_editor_script() {
    $screen = get_current_screen();
    if ( rm_headless_preview_base() === '' || ! $screen || ! in_array( $screen->post_type, RM_HEADLESS_PREVIEW_TYPES, true ) ) {
        return;
    }
    wp_enqueue_script(
        'rm-headless-preview-editor',
        plugins_url( 'preview-editor.js', __FILE__ ),
        array( 'wp-api-fetch', 'acf-input', 'jquery' ),
        RM_HEADLESS_VERSION,
        true
    );
}
add_action( 'enqueue_block_editor_assets', 'rm_headless_preview_editor_script' );

/**
 * Saves the ACF values sent with an autosave (see preview-editor.js) onto the
 * revision that autosave created. For a draft WordPress autosaves the draft
 * itself, and the fields go there too, as a normal save would.
 */
function rm_headless_preview_save_fields( $response, $handler, WP_REST_Request $request ) {
    if ( $request->get_method() !== 'POST' || is_wp_error( $response ) || ! function_exists( 'acf_save_post' ) ) {
        return $response;
    }
    if ( ! preg_match( '#^/wp/v2/([a-z_-]+)/(\d+)/autosaves$#', $request->get_route(), $route ) ) {
        return $response;
    }
    $values = $request->get_param( 'rm_acf' );
    $post   = get_post( (int) $route[2] );
    if ( ! is_array( $values ) || ! $post || ! in_array( $post->post_type, RM_HEADLESS_PREVIEW_TYPES, true ) ) {
        return $response;
    }
    if ( ! current_user_can( 'edit_post', $post->ID ) ) {
        return $response;
    }
    $data      = $response instanceof WP_REST_Response ? $response->get_data() : (array) $response;
    $target_id = isset( $data['id'] ) ? (int) $data['id'] : 0;
    $target    = $target_id ? get_post( $target_id ) : null;
    $is_draft  = $target && $target->ID === $post->ID && $post->post_status !== 'publish';
    $is_copy   = $target && (int) $target->post_parent === $post->ID && wp_is_post_autosave( $target );
    if ( $is_draft || $is_copy ) {
        acf_save_post( $target->ID, wp_unslash( $values ) );
    }
    return $response;
}
add_filter( 'rest_request_after_callbacks', 'rm_headless_preview_save_fields', 10, 3 );

function rm_headless_preview_source( $post ) {
    $autosave = wp_get_post_autosave( $post->ID );
    if ( $autosave && strtotime( $autosave->post_modified_gmt ) >= strtotime( $post->post_modified_gmt ) ) {
        return $autosave;
    }
    return $post;
}

function rm_headless_preview_response( WP_REST_Request $request ) {
    $post = get_post( (int) $request['id'] );
    if ( ! $post || ! in_array( $post->post_type, RM_HEADLESS_PREVIEW_TYPES, true ) ) {
        return new WP_Error( 'rm_preview_not_found', 'No such post', array( 'status' => 404 ) );
    }

    $source = rm_headless_preview_source( $post );
    $fields = function_exists( 'get_fields' ) ? ( get_fields( $post->ID ) ?: array() ) : array();
    if ( $source->ID !== $post->ID && function_exists( 'get_fields' ) ) {
        $fields = array_merge( $fields, get_fields( $source->ID ) ?: array() );
    }

    $thumbnail = get_post_thumbnail_id( $post->ID );
    $media     = $thumbnail ? array( array( 'source_url' => wp_get_attachment_url( $thumbnail ) ) ) : array();

    $response = rest_ensure_response( array(
        'id'        => $post->ID,
        'date'      => $post->post_date,
        'status'    => $post->post_status,
        'slug'      => $post->post_name !== '' ? $post->post_name : sanitize_title( $source->post_title ),
        'title'     => array( 'rendered' => get_the_title( $source ) ),
        'acf'       => $fields,
        '_embedded' => array( 'wp:featuredmedia' => $media ),
    ) );
    $response->header( 'Cache-Control', 'no-store' );
    return $response;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'rm/v1', '/preview/(?P<id>\d+)', array(
        'methods'             => 'GET',
        'callback'            => 'rm_headless_preview_response',
        'permission_callback' => function ( WP_REST_Request $request ) {
            return rm_headless_preview_token_valid( (int) $request['id'], $request->get_param( 'token' ) );
        },
    ) );
} );
