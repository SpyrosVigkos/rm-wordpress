<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Register ACF fields for Comparison CPT
add_action('acf/init', function() {
    if ( ! function_exists('acf_add_local_field_group') ) { return; }

    acf_add_local_field_group(array(
        'key' => 'group_rm_comparison',
        'title' => 'Comparison',
        'fields' => array(
            array(
                'key' => 'field_rm_cmp_plans',
                'label' => 'Plans',
                'name' => 'plans',
                'type' => 'repeater',
                'min' => 2,
                'layout' => 'row',
                'sub_fields' => array(
                    array(
                        'key' => 'field_rm_cmp_plan_title',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_rm_cmp_plan_subtitle',
                        'label' => 'Subtitle / Price',
                        'name' => 'subtitle',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_rm_cmp_plan_highlight',
                        'label' => 'Highlight Column',
                        'name' => 'highlight',
                        'type' => 'true_false',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_rm_cmp_plan_badge',
                        'label' => 'Badge',
                        'name' => 'badge',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_rm_cmp_plan_key',
                        'label' => 'Plan Key',
                        'name' => 'plan_key',
                        'type' => 'text',
                        'wrapper' => array('width' => 0),
                        'readonly' => 1,
                        'instructions' => 'Auto-generated stable key',
                    ),
                ),
            ),
            array(
                'key' => 'field_rm_cmp_groups',
                'label' => 'Feature Groups',
                'name' => 'feature_groups',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_rm_cmp_group_title',
                        'label' => 'Group Title',
                        'name' => 'group_title',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_rm_cmp_features',
                        'label' => 'Features',
                        'name' => 'features',
                        'type' => 'repeater',
                        'layout' => 'row',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_rm_cmp_feature_title',
                                'label' => 'Feature Title',
                                'name' => 'feature_title',
                                'type' => 'text',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_rm_cmp_feature_note',
                                'label' => 'Note',
                                'name' => 'note',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_rm_cmp_feature_availability',
                                'label' => 'Available in Plans',
                                'name' => 'availability',
                                'type' => 'checkbox',
                                'choices' => array(),
                                'layout' => 'horizontal',
                                'return_format' => 'value',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'comparison',
                ),
            ),
        ),
    ));
});

// Populate availability choices with plans from this post
add_filter('acf/load_field/key=field_rm_cmp_feature_availability', function($field){
    $post_id = acf_get_form_data('post_id');
    if ( ! $post_id ) { $post_id = get_the_ID(); }
    $field['choices'] = array();
    if ( $post_id ) {
        $plans = get_field('plans', $post_id);
        if ( is_array($plans) ) {
            foreach ( $plans as $p ) {
                $key = ! empty($p['plan_key']) ? $p['plan_key'] : sanitize_title($p['title'] ?? '');
                if ( $key ) {
                    $field['choices'][$key] = (string)($p['title'] ?? $key);
                }
            }
        }
    }
    return $field;
});

// Ensure every plan has a stable plan_key
add_action('acf/save_post', function($post_id){
    if ( get_post_type($post_id) !== 'comparison' ) { return; }
    $plans = get_field('plans', $post_id);
    if ( ! is_array($plans) ) { return; }
    $changed = false;
    foreach ( $plans as $i => $p ) {
        if ( empty($p['plan_key']) ) {
            $plans[$i]['plan_key'] = uniqid('plan_', true);
            $changed = true;
        }
    }
    if ( $changed ) {
        update_field('field_rm_cmp_plans', $plans, $post_id);
    }
}, 20);


