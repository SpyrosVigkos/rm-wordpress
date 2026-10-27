<?php
/**
 * ReelCast episodes: post type, taxonomies, ACF fields, REST episode_data, settings and image fallback.
 * Copied from the rm-elementor-theme functions.php; keep the two in step
 * until the theme no longer registers it.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register CPT: ReelCast Episodes
 * Custom Post Type for podcast episodes
 */
function rm_register_reelcast_cpt() {
    $labels = array(
        'name'               => 'ReelCast Episodes',
        'singular_name'      => 'ReelCast Episode',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New ReelCast Episode',
        'edit_item'          => 'Edit ReelCast Episode',
        'new_item'           => 'New ReelCast Episode',
        'view_item'          => 'View ReelCast Episode',
        'search_items'       => 'Search ReelCast Episodes',
        'not_found'          => 'No episodes found',
        'not_found_in_trash' => 'No episodes found in Trash',
        'menu_name'          => 'ReelCast',
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ),
        'menu_icon'          => 'dashicons-microphone',
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'reelcast' ),
        'hierarchical'       => false,
        'query_var'          => true,
        'can_export'         => true,
        'delete_with_user'   => false,
        'capability_type'    => 'post',
    );
    
    register_post_type( 'reelcast', $args );
}
add_action( 'init', 'rm_register_reelcast_cpt' );

/**
 * Register dedicated taxonomies for ReelCast
 * - reelcast_category (hierarchical)
 * - reelcast_tag (non-hierarchical)
 */
function rm_register_reelcast_taxonomies() {
    // Categories
    $cat_labels = array(
        'name'              => _x( 'ReelCast Categories', 'taxonomy general name', 'rm-elementor-theme' ),
        'singular_name'     => _x( 'ReelCast Category', 'taxonomy singular name', 'rm-elementor-theme' ),
        'search_items'      => __( 'Search Categories', 'rm-elementor-theme' ),
        'all_items'         => __( 'All Categories', 'rm-elementor-theme' ),
        'parent_item'       => __( 'Parent Category', 'rm-elementor-theme' ),
        'parent_item_colon' => __( 'Parent Category:', 'rm-elementor-theme' ),
        'edit_item'         => __( 'Edit Category', 'rm-elementor-theme' ),
        'update_item'       => __( 'Update Category', 'rm-elementor-theme' ),
        'add_new_item'      => __( 'Add New Category', 'rm-elementor-theme' ),
        'new_item_name'     => __( 'New Category Name', 'rm-elementor-theme' ),
        'menu_name'         => __( 'Categories', 'rm-elementor-theme' ),
    );

    register_taxonomy( 'reelcast_category', array( 'reelcast' ), array(
        'hierarchical'      => true,
        'labels'            => $cat_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'reelcast/category' ),
        'show_in_rest'      => true,
    ) );

    // Tags
    $tag_labels = array(
        'name'                       => _x( 'ReelCast Tags', 'taxonomy general name', 'rm-elementor-theme' ),
        'singular_name'              => _x( 'ReelCast Tag', 'taxonomy singular name', 'rm-elementor-theme' ),
        'search_items'               => __( 'Search Tags', 'rm-elementor-theme' ),
        'popular_items'              => __( 'Popular Tags', 'rm-elementor-theme' ),
        'all_items'                  => __( 'All Tags', 'rm-elementor-theme' ),
        'edit_item'                  => __( 'Edit Tag', 'rm-elementor-theme' ),
        'update_item'                => __( 'Update Tag', 'rm-elementor-theme' ),
        'add_new_item'               => __( 'Add New Tag', 'rm-elementor-theme' ),
        'new_item_name'              => __( 'New Tag Name', 'rm-elementor-theme' ),
        'separate_items_with_commas' => __( 'Separate tags with commas', 'rm-elementor-theme' ),
        'add_or_remove_items'        => __( 'Add or remove tags', 'rm-elementor-theme' ),
        'choose_from_most_used'      => __( 'Choose from the most used tags', 'rm-elementor-theme' ),
        'menu_name'                  => __( 'Tags', 'rm-elementor-theme' ),
    );

    register_taxonomy( 'reelcast_tag', array( 'reelcast' ), array(
        'hierarchical'      => false,
        'labels'            => $tag_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'reelcast/tag' ),
        'show_in_rest'      => true,
    ) );

    // Ensure taxonomies are attached to post type (in case of registration order differences)
    register_taxonomy_for_object_type( 'reelcast_category', 'reelcast' );
    register_taxonomy_for_object_type( 'reelcast_tag', 'reelcast' );
}
add_action( 'init', 'rm_register_reelcast_taxonomies', 11 );
/**
 * Register ACF fields for ReelCast Episodes
 * Mirrors the React component props structure from rm-public-pages
 */
function rm_register_reelcast_acf() {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group(array(
            'key' => 'group_reelcast_episode',
            'title' => 'ReelCast Episode Fields',
            'fields' => array(
                // Basic episode information
                array(
                    'key' => 'field_reelcast_subtitle',
                    'label' => 'Subtitle',
                    'name' => 'subtitle',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'Season X, Episode Y',
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_reelcast_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'required' => 1,
                    'rows' => 5,
                    'new_lines' => 'br',
                    'instructions' => 'Episode description for display and SEO',
                ),
                array(
                    'key' => 'field_reelcast_date_published',
                    'label' => 'Date Published',
                    'name' => 'date_published',
                    'type' => 'date_picker',
                    'required' => 1,
                    'display_format' => 'Y-m-d',
                    'return_format' => 'Y-m-d',
                    'first_day' => 1,
                    'wrapper' => array( 'width' => '33' ),
                ),
                array(
                    'key' => 'field_reelcast_season_number',
                    'label' => 'Season Number',
                    'name' => 'season_number',
                    'type' => 'number',
                    'required' => 1,
                    'min' => 1,
                    'wrapper' => array( 'width' => '33' ),
                ),
                array(
                    'key' => 'field_reelcast_episode_number',
                    'label' => 'Episode Number',
                    'name' => 'episode_number',
                    'type' => 'number',
                    'required' => 1,
                    'min' => 1,
                    'wrapper' => array( 'width' => '34' ),
                ),
                
                // Buzzsprout integration
                array(
                    'key' => 'field_reelcast_buzzsprout_id',
                    'label' => 'Buzzsprout Episode ID',
                    'name' => 'buzzsprout_id',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => '14577542-s03e02-delaware-north-with-michael-carruthers',
                    'instructions' => 'Format: digits-episode-slug (used for player embed)',
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_reelcast_buzzsprout_url',
                    'label' => 'Buzzsprout URL',
                    'name' => 'buzzsprout_url',
                    'type' => 'url',
                    'placeholder' => 'https://www.buzzsprout.com/2057836/14577542-...',
                    'wrapper' => array( 'width' => '50' ),
                ),
                
                // Platform URLs
                array(
                    'key' => 'field_reelcast_spotify_url',
                    'label' => 'Spotify URL',
                    'name' => 'spotify_url',
                    'type' => 'url',
                    'placeholder' => 'https://open.spotify.com/show/314iBnXRGGAuttAsIcZeC5',
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_reelcast_apple_podcasts_url',
                    'label' => 'Apple Podcasts URL',
                    'name' => 'apple_podcasts_url',
                    'type' => 'url',
                    'placeholder' => 'https://podcasts.apple.com/us/podcast/reelcast-by-reelmetrics/id1652485625',
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_reelcast_iheart_url',
                    'label' => 'iHeartRadio URL',
                    'name' => 'iheart_url',
                    'type' => 'url',
                    'placeholder' => 'https://iheart.com/podcast/104029260/',
                    'wrapper' => array( 'width' => '50' ),
                ),
                array(
                    'key' => 'field_reelcast_cdc_url',
                    'label' => 'CDC Gaming URL',
                    'name' => 'cdc_url',
                    'type' => 'url',
                    'placeholder' => 'https://cdcgaming.com/reelmetrics/s03e02-...',
                    'wrapper' => array( 'width' => '50' ),
                ),
                
                // Topics (repeater)
                array(
                    'key' => 'field_reelcast_topics',
                    'label' => 'Topics',
                    'name' => 'topics',
                    'type' => 'repeater',
                    'button_label' => 'Add Topic',
                    'layout' => 'table',
                    'instructions' => 'Key discussion topics covered in this episode',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_reelcast_topic',
                            'label' => 'Topic',
                            'name' => 'topic',
                            'type' => 'text',
                            'required' => 1,
                        ),
                    ),
                ),
                
                // Keywords (repeater)
                array(
                    'key' => 'field_reelcast_keywords',
                    'label' => 'Keywords',
                    'name' => 'keywords',
                    'type' => 'repeater',
                    'button_label' => 'Add Keyword',
                    'layout' => 'table',
                    'instructions' => 'SEO keywords and tags for this episode',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_reelcast_keyword',
                            'label' => 'Keyword',
                            'name' => 'keyword',
                            'type' => 'text',
                            'required' => 1,
                        ),
                    ),
                ),
                
                // Transcript
                array(
                    'key' => 'field_reelcast_transcript',
                    'label' => 'Transcript',
                    'name' => 'transcript',
                    'type' => 'textarea',
                    'rows' => 20,
                    'new_lines' => 'wpautop',
                    'instructions' => 'Full episode transcript. Speaker names followed by colons will be automatically formatted.',
                ),
                
                // Optional social image override
                array(
                    'key' => 'field_reelcast_social_image',
                    'label' => 'Social Image (Optional)',
                    'name' => 'social_image',
                    'type' => 'image',
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'instructions' => 'Override default ReelCast social image for this episode',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'reelcast',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'acf_after_title',
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'active' => true,
            'show_in_rest' => 1,
        ));
    }
}
add_action( 'acf/init', 'rm_register_reelcast_acf' );

/**
 * Helper function to get episode data in React component format
 * Useful for REST API or Elementor dynamic content
 */
function rm_get_reelcast_episode_data( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    if ( get_post_type( $post_id ) !== 'reelcast' ) {
        return false;
    }
    
    $post = get_post( $post_id );
    
    // Get topics array
    $topics_raw = get_field( 'topics', $post_id );
    $topics = array();
    if ( $topics_raw ) {
        foreach ( $topics_raw as $topic ) {
            $topics[] = $topic['topic'];
        }
    }
    
    // Get keywords array
    $keywords_raw = get_field( 'keywords', $post_id );
    $keywords = array();
    if ( $keywords_raw ) {
        foreach ( $keywords_raw as $keyword ) {
            $keywords[] = $keyword['keyword'];
        }
    }
    
    return array(
        'id' => get_field( 'buzzsprout_id', $post_id ),
        'link' => $post->post_name,
        'title' => $post->post_title,
        'subTitle' => get_field( 'subtitle', $post_id ),
        'description' => get_field( 'description', $post_id ),
        'episodeNumber' => (int) get_field( 'episode_number', $post_id ),
        'seasonNumber' => (int) get_field( 'season_number', $post_id ),
        'datePublished' => get_field( 'date_published', $post_id ),
        'topics' => $topics,
        'keywords' => $keywords,
        'buzzSproutLink' => get_field( 'buzzsprout_url', $post_id ),
        'spotifyLink' => get_field( 'spotify_url', $post_id ),
        'applePodcastsLink' => get_field( 'apple_podcasts_url', $post_id ),
        'iHeartRadioLink' => get_field( 'iheart_url', $post_id ),
        'cdcLink' => get_field( 'cdc_url', $post_id ),
        'transcript' => get_field( 'transcript', $post_id ),
        'socialImage' => get_field( 'social_image', $post_id ),
    );
}

/**
 * REST API endpoint for ReelCast episodes (optional)
 * Provides data in React component format
 */
function rm_register_reelcast_rest_fields() {
    register_rest_field( 'reelcast', 'episode_data', array(
        'get_callback' => function( $post_array ) {
            return rm_get_reelcast_episode_data( $post_array['id'] );
        },
        'schema' => array(
            'description' => 'Complete episode data in React component format',
            'type' => 'object',
        ),
    ));
}
add_action( 'rest_api_init', 'rm_register_reelcast_rest_fields' );

/**
 * ReelCast: Default Featured Image (fallback) via ACF Options
 * - Adds a subpage under ReelCast to set a default image
 * - Provides fallback for featured images on ReelCast posts when none set
 */
function rm_reelcast_register_options_page() {
    if ( function_exists( 'acf_add_options_sub_page' ) ) {
        acf_add_options_sub_page( array(
            'page_title'  => 'ReelCast Settings',
            'menu_title'  => 'Settings',
            'parent_slug' => 'edit.php?post_type=reelcast',
            'menu_slug'   => 'reelcast-settings',
            'capability'  => 'edit_posts',
            'position'    => false,
            'autoload'    => true,
        ) );
    }
}
add_action( 'init', 'rm_reelcast_register_options_page' );

function rm_reelcast_register_options_fields() {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group( array(
            'key' => 'group_reelcast_settings',
            'title' => 'ReelCast Settings',
            'fields' => array(
                array(
                    'key' => 'field_reelcast_default_image',
                    'label' => 'Default ReelCast Image',
                    'name' => 'reelcast_default_image',
                    'type' => 'image',
                    'return_format' => 'id',
                    'preview_size' => 'medium',
                    'instructions' => 'Used as a fallback image for ReelCast posts without a featured image.',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'reelcast-settings',
                    ),
                ),
            ),
            'position' => 'normal',
            'style' => 'default',
            'active' => true,
            'show_in_rest' => 0,
        ) );
    }
}
add_action( 'acf/init', 'rm_reelcast_register_options_fields' );

/**
 * Helper: Get ReelCast image URL with fallbacks (featured → ACF social_image → options default → theme asset)
 */
function rm_get_reelcast_image_url( $post_id = null, $size = 'large' ) {
    $post_id = $post_id ? $post_id : get_the_ID();
    if ( ! $post_id ) return '';

    // 1) Featured image
    $thumb_id = get_post_thumbnail_id( $post_id );
    if ( $thumb_id ) {
        $src = wp_get_attachment_image_url( $thumb_id, $size );
        if ( $src ) return $src;
    }

    // 2) Per-post ACF social_image (URL)
    if ( function_exists( 'get_field' ) ) {
        $social = get_field( 'social_image', $post_id );
        if ( $social ) return $social;
    }

    // 3) Options default (ID)
    if ( function_exists( 'get_field' ) ) {
        $default_id = get_field( 'reelcast_default_image', 'option' );
        if ( $default_id ) {
            $src = wp_get_attachment_image_url( $default_id, $size );
            if ( $src ) return $src;
        }
    }

    // 4) No theme asset here: the headless site has none, so callers get an empty string.
    return '';
}

/**
 * Filter: provide a thumbnail ID fallback for ReelCast posts using the Options default image
 * This helps Elementor and core functions that rely on _thumbnail_id.
 */
function rm_reelcast_thumbnail_fallback( $value, $object_id, $meta_key, $single ) {
    if ( $meta_key !== '_thumbnail_id' || ! $single ) {
        return $value;
    }
    if ( get_post_type( $object_id ) !== 'reelcast' ) {
        return $value;
    }
    // If a thumbnail is already set, respect it. $value is the short-circuit null, so read the stored meta.
    remove_filter( 'get_post_metadata', 'rm_reelcast_thumbnail_fallback', 10 );
    $stored_id = get_post_meta( $object_id, '_thumbnail_id', true );
    add_filter( 'get_post_metadata', 'rm_reelcast_thumbnail_fallback', 10, 4 );
    if ( ! empty( $stored_id ) ) {
        return $value;
    }
    if ( function_exists( 'get_field' ) ) {
        $default_id = get_field( 'reelcast_default_image', 'option' );
        if ( $default_id ) {
            return (int) $default_id;
        }
    }
    return $value;
}
add_filter( 'get_post_metadata', 'rm_reelcast_thumbnail_fallback', 10, 4 );

/**
 * Filter: when HTML is requested directly for the thumbnail and none exists, render fallback IMG
 */
function rm_reelcast_thumbnail_html_fallback( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
    if ( get_post_type( $post_id ) !== 'reelcast' ) {
        return $html;
    }
    if ( $html ) {
        return $html;
    }
    $src = rm_get_reelcast_image_url( $post_id, $size );
    if ( ! $src ) return $html;
    $alt = esc_attr( get_the_title( $post_id ) );
    $class = isset( $attr['class'] ) ? esc_attr( $attr['class'] ) : 'attachment-' . esc_attr( is_string( $size ) ? $size : 'large' );
    return '<img src="' . esc_url( $src ) . '" class="' . $class . ' rm-reelcast-fallback" alt="' . $alt . '" loading="lazy" decoding="async" />';
}
add_filter( 'post_thumbnail_html', 'rm_reelcast_thumbnail_html_fallback', 10, 5 );
