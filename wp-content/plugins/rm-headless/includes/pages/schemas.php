<?php
/**
 * The fields of each fixed page. Every schema mirrors a content type in the
 * Astro site (site/src/content/types.ts); keep the two in step.
 * Composed elsewhere, not edited here: About's team (rm_team_member posts),
 * the ReelHot entries on Publications (reelhot posts) and the latest insights
 * in the footer (notification posts).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** GetStartedContent */
function rm_schema_get_started( $name ) {
    return rm_f_group( $name, array(
        rm_f_or_empty( rm_f_text( 'eyebrow' ) ),
        rm_f_text( 'title' ),
        rm_f_textarea( 'text' ),
        rm_f_list( 'cards', array( rm_f_text( 'title' ), rm_f_textarea( 'text' ), rm_f_link( 'button' ) ) ),
    ) );
}

/** ContactItem */
function rm_schema_contact_items( $name ) {
    return rm_f_list( $name, array( rm_f_icon( 'icon' ), rm_f_parts( 'label' ), rm_f_optional( rm_f_text( 'href' ) ) ) );
}

/** ProductShowcase */
function rm_schema_showcase( $name ) {
    return rm_f_group( $name, array(
        rm_f_parts( 'title' ),
        rm_f_textarea( 'text' ),
        rm_f_list( 'items', array( rm_f_text( 'title' ), rm_f_textarea( 'text' ) ) ),
        rm_f_image( 'image' ),
    ) );
}

/** Page key → slug of its WordPress page and its fields. */
function rm_page_schemas() {
    return array(
        'home'         => array(
            'slug'   => 'home',
            'schema' => array(
                rm_f_group( 'hero', array( rm_f_text( 'title' ), rm_f_textarea( 'text' ), rm_f_list_of( 'buttons', rm_f_link( 'value' ) ) ) ),
                rm_f_group( 'heroBackground', array( rm_f_file( 'video' ), rm_f_optional( rm_f_file( 'poster' ) ) ) ),
                rm_f_parts( 'intro' ),
                rm_f_text( 'featuresTitle' ),
                rm_f_list( 'features', array( rm_f_text( 'eyebrow' ), rm_f_text( 'title' ), rm_f_parts( 'text' ), rm_f_link( 'link' ), rm_f_image( 'image' ) ) ),
                rm_f_text( 'compatibleTitle' ),
                rm_f_list_of( 'logos', rm_f_image( 'value' ) ),
                rm_f_list( 'testimonials', array(
                    rm_f_parts( 'quote' ),
                    rm_f_text( 'name' ),
                    rm_f_text( 'role' ),
                    rm_f_image( 'image' ),
                    rm_f_optional( rm_f_group( 'cta', array( rm_f_text( 'label' ), rm_f_optional( rm_f_text( 'href' ) ) ) ) ),
                ) ),
                rm_schema_get_started( 'getStarted' ),
            ),
        ),
        'services'     => array(
            'slug'   => 'services',
            'schema' => array(
                rm_f_group( 'hero', array( rm_f_parts( 'title' ), rm_f_textarea( 'text' ), rm_f_image( 'image' ) ) ),
                rm_f_parts( 'practicesTitle' ),
                rm_f_list( 'practices', array( rm_f_text( 'eyebrow' ), rm_f_text( 'title' ), rm_f_textarea( 'text' ), rm_f_image( 'image' ) ) ),
                rm_f_parts( 'timelineTitle' ),
                rm_f_list( 'steps', array( rm_f_text( 'title' ), rm_f_textarea( 'text' ), rm_f_optional( rm_f_image( 'image' ) ) ) ),
                rm_schema_get_started( 'getStarted' ),
            ),
        ),
        'product'      => array(
            'slug'   => 'product-page',
            'schema' => array(
                rm_f_group( 'hero', array( rm_f_parts( 'title' ), rm_f_textarea( 'text' ) ) ),
                rm_f_list( 'analytics', array( rm_f_text( 'title' ), rm_f_textarea( 'text' ), rm_f_image( 'icon' ) ) ),
                rm_schema_showcase( 'demand' ),
                rm_schema_showcase( 'supply' ),
                rm_f_text( 'compatibleTitle' ),
                rm_f_list_of( 'logos', rm_f_image( 'value' ) ),
                rm_f_text( 'plansTitle' ),
                rm_f_list( 'plans', array(
                    rm_f_text( 'badge' ),
                    rm_f_text( 'name' ),
                    rm_f_text( 'fee' ),
                    rm_f_textarea( 'description' ),
                    rm_f_link_plain( 'button' ),
                    rm_f_list( 'features', array( rm_f_text( 'text' ), rm_f_image( 'icon' ), rm_f_bool( 'included' ) ) ),
                ) ),
                rm_f_group( 'comparison', array(
                    rm_f_text( 'label' ),
                    rm_f_text( 'caption' ),
                    rm_f_list( 'plans', array( rm_f_text( 'title' ), rm_f_text( 'subtitle' ) ) ),
                    rm_f_list( 'groups', array(
                        rm_f_text( 'title' ),
                        rm_f_list( 'rows', array( rm_f_text( 'feature' ), rm_f_list_of( 'values', rm_f_bool( 'value' ) ) ) ),
                    ) ),
                ) ),
                rm_schema_get_started( 'getStarted' ),
            ),
        ),
        'about'        => array(
            'slug'   => 'about',
            'schema' => array(
                rm_f_group( 'intro', array( rm_f_text( 'title' ), rm_f_textarea( 'text' ), rm_f_image( 'image' ) ) ),
                rm_f_group( 'founding', array( rm_f_parts( 'title' ), rm_f_textarea( 'text' ) ) ),
                rm_f_list( 'carousel', array( rm_f_spread( rm_f_image( 'value' ) ), rm_f_optional( rm_f_text( 'caption' ) ) ) ),
                rm_f_textarea( 'leiden' ),
                rm_f_parts( 'teamTitle' ),
                rm_schema_get_started( 'getStarted' ),
            ),
        ),
        'contact'      => array(
            'slug'   => 'contact',
            'schema' => array(
                rm_f_parts( 'title' ),
                rm_f_parts( 'intro' ),
                rm_f_image( 'image' ),
                rm_f_text( 'reachOutTitle' ),
                rm_schema_contact_items( 'reachOut' ),
                rm_schema_contact_items( 'registry' ),
                rm_f_text( 'addressTitle' ),
                rm_f_group( 'address', array( rm_f_icon( 'icon' ), rm_f_parts( 'label' ), rm_f_text( 'href' ) ) ),
                rm_f_group( 'map', array( rm_f_text( 'title' ), rm_f_text( 'href' ) ) ),
            ),
        ),
        'careers'      => array(
            'slug'   => 'careers',
            'schema' => array(
                rm_f_group( 'hero', array( rm_f_parts( 'title' ), rm_f_textarea( 'text' ), rm_f_list_of( 'buttons', rm_f_link( 'value' ) ), rm_f_image( 'image' ) ) ),
                rm_f_list( 'perks', array( rm_f_text( 'label' ), rm_f_icon( 'icon' ) ) ),
                rm_f_group( 'offer', array( rm_f_text( 'title' ), rm_f_textarea( 'text' ) ) ),
                rm_f_text( 'openingsTitle' ),
                rm_f_list_of( 'gallery', rm_f_image( 'value' ) ),
                rm_f_group( 'digs', array( rm_f_text( 'title' ), rm_f_textarea( 'text' ), rm_f_image( 'image' ) ) ),
                rm_f_group( 'demo', array( rm_f_text( 'title' ), rm_f_textarea( 'text' ), rm_f_link( 'button' ) ) ),
                rm_f_group( 'queries', array(
                    rm_f_text( 'title' ),
                    rm_f_text( 'id' ),
                    rm_schema_contact_items( 'address' ),
                    rm_schema_contact_items( 'phones' ),
                    rm_schema_contact_items( 'email' ),
                    rm_schema_contact_items( 'registry' ),
                ) ),
            ),
        ),
        'publications' => array(
            'slug'   => 'publications',
            'schema' => array(
                rm_f_parts( 'title' ),
                rm_f_textarea( 'intro' ),
                rm_f_list_of( 'tabs', rm_f_text( 'value' ) ),
                rm_f_group( 'reelcast', array( rm_f_text( 'eyebrow' ), rm_f_text( 'button' ), rm_f_image( 'image' ) ) ),
                rm_f_group( 'reelhot', array( rm_f_text( 'button' ) ) ),
                rm_schema_get_started( 'getStarted' ),
            ),
        ),
        'site'         => array(
            'slug'   => 'site',
            'schema' => array(
                rm_f_group( 'layout', array(
                    rm_f_image( 'logo' ),
                    rm_f_image( 'footerLogo' ),
                    rm_f_list_of( 'nav', rm_f_link( 'value' ) ),
                    rm_f_list_of( 'footerLinks', rm_f_link( 'value' ) ),
                    rm_f_list_of( 'social', rm_f_link( 'value' ) ),
                ) ),
                rm_f_group( 'episodePage', array( rm_schema_get_started( 'hearFromYou' ), rm_schema_get_started( 'getStarted' ) ) ),
            ),
        ),
    );
}
