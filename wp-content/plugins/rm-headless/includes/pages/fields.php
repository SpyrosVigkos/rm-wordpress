<?php
/**
 * A small way to describe a page's fields once and get both the ACF field
 * group (for the edit screen) and the conversion of the saved values into the
 * shape the Astro site renders (for rm/v1/page). Field names are the property
 * names of the site's content types (site/src/content/types.ts).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RM_Missing_Field extends RuntimeException {}

const RM_PARTS_HELP = 'Wrap highlighted words in *asterisks*, bold words in **double asterisks**.';

function rm_f( $kind, $name, array $extra = array() ) {
    return array_merge( array( 'kind' => $kind, 'name' => $name ), $extra );
}
function rm_f_text( $name ) { return rm_f( 'text', $name ); }
function rm_f_textarea( $name ) { return rm_f( 'textarea', $name ); }
function rm_f_parts( $name ) { return rm_f( 'parts', $name ); }
function rm_f_image( $name ) { return rm_f( 'image', $name ); }
function rm_f_file( $name ) { return rm_f( 'file', $name ); }
function rm_f_link( $name ) { return rm_f( 'link', $name ); }
/** A button whose URL may stay empty (ACF's link field drops a link without one). */
function rm_f_link_plain( $name ) { return rm_f( 'link_plain', $name ); }
function rm_f_icon( $name ) { return rm_f( 'icon', $name ); }
function rm_f_bool( $name ) { return rm_f( 'bool', $name ); }
function rm_f_group( $name, array $fields ) { return rm_f( 'group', $name, array( 'fields' => $fields ) ); }
function rm_f_list( $name, array $fields ) { return rm_f( 'list', $name, array( 'fields' => $fields ) ); }

/** A list of single values (strings, images, booleans), stored as rows with one `value` field. */
function rm_f_list_of( $name, array $field ) {
    $field['name'] = 'value';
    return rm_f( 'list_of', $name, array( 'fields' => array( $field ) ) );
}

/** May be left empty; the key is then left out of the site's content. */
function rm_f_optional( array $field ) {
    $field['optional'] = true;
    return $field;
}

/** May be left empty; the site then gets an empty string (e.g. a block without an eyebrow). */
function rm_f_or_empty( array $field ) {
    $field['or_empty'] = true;
    return $field;
}

/** An image whose src, alt, width and height sit directly on the parent object. */
function rm_f_spread( array $field ) {
    $field['spread'] = true;
    return $field;
}

/**
 * "Master your *mix*" → [{text: "Master your ", accent: false}, {text: "mix", accent: true}].
 * Only paired markers count, and a marker must touch its words, so "5* rating" stays text.
 */
function rm_text_parts( $text ) {
    $parts  = array();
    $offset = 0;
    preg_match_all( '/\*\*(?=\S)(.+?)(?<=\S)\*\*|\*(?=[^\s*])(.+?)(?<=[^\s*])\*/s', $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
    foreach ( $matches as $match ) {
        if ( $match[0][1] > $offset ) {
            $parts[] = array( 'text' => substr( $text, $offset, $match[0][1] - $offset ), 'accent' => false );
        }
        if ( isset( $match[2] ) && $match[2][1] >= 0 ) {
            $parts[] = array( 'text' => $match[2][0], 'accent' => true );
        } else {
            $parts[] = array( 'text' => $match[1][0], 'accent' => false, 'strong' => true );
        }
        $offset = $match[0][1] + strlen( $match[0][0] );
    }
    if ( $offset < strlen( $text ) ) {
        $parts[] = array( 'text' => substr( $text, $offset ), 'accent' => false );
    }
    return $parts;
}

function rm_image_out( $id ) {
    $id = (int) $id;
    $src = wp_get_attachment_image_src( $id, 'large' );
    if ( ! $src ) {
        return null;
    }
    return array(
        'src'    => $src[0],
        'alt'    => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
        'width'  => (int) $src[1],
        'height' => (int) $src[2],
    );
}

function rm_is_empty( $value ) {
    return $value === null || $value === '' || $value === false || $value === array();
}

function rm_field_value( array $field, $value, $path ) {
    switch ( $field['kind'] ) {
        case 'text':
        case 'textarea':
        case 'icon':
            return rm_is_empty( $value ) ? null : (string) $value;
        case 'parts':
            return rm_is_empty( $value ) ? null : rm_text_parts( (string) $value );
        case 'image':
            return rm_is_empty( $value ) ? null : rm_image_out( is_array( $value ) ? $value['ID'] : $value );
        case 'file':
            if ( rm_is_empty( $value ) ) {
                return null;
            }
            $url = wp_get_attachment_url( (int) ( is_array( $value ) ? $value['ID'] : $value ) );
            return $url ? $url : null;
        case 'link':
            if ( ! is_array( $value ) || ( rm_is_empty( $value['url'] ?? '' ) && rm_is_empty( $value['title'] ?? '' ) ) ) {
                return null;
            }
            return array( 'label' => (string) ( $value['title'] ?? '' ), 'href' => (string) $value['url'] );
        case 'link_plain':
            if ( ! is_array( $value ) || ( rm_is_empty( $value['url'] ?? '' ) && rm_is_empty( $value['title'] ?? '' ) ) ) {
                return null;
            }
            return array( 'label' => (string) ( $value['title'] ?? '' ), 'href' => (string) ( $value['url'] ?? '' ) );
        case 'bool':
            return (bool) $value;
        case 'group':
            $values = is_array( $value ) ? $value : array();
            if ( ! empty( $field['optional'] ) && count( array_filter( $values, function ( $v ) { return ! rm_is_empty( $v ); } ) ) === 0 ) {
                return null;
            }
            return rm_fields_to_site( $field['fields'], $values, $path );
        case 'list':
            $rows = is_array( $value ) ? array_values( $value ) : array();
            if ( $rows === array() ) {
                return null;
            }
            $out = array();
            foreach ( $rows as $i => $row ) {
                $out[] = rm_fields_to_site( $field['fields'], is_array( $row ) ? $row : array(), "$path.$i" );
            }
            return $out;
        case 'list_of':
            $rows = is_array( $value ) ? array_values( $value ) : array();
            if ( $rows === array() ) {
                return null;
            }
            $out = array();
            foreach ( $rows as $i => $row ) {
                $item = rm_field_value( $field['fields'][0], is_array( $row ) ? ( $row['value'] ?? null ) : null, "$path.$i" );
                if ( $item === null ) {
                    throw new RM_Missing_Field( "$path.$i" );
                }
                $out[] = $item;
            }
            return $out;
    }
    throw new InvalidArgumentException( "Unknown field kind {$field['kind']}" );
}

/**
 * Saved ACF values (get_fields with formatting) → the site's content shape.
 * An empty required field throws RM_Missing_Field with its path, e.g. home.hero.title.
 */
function rm_fields_to_site( array $schema, array $values, $path = '' ) {
    $out = array();
    foreach ( $schema as $field ) {
        $field_path = $path === '' ? $field['name'] : "$path.{$field['name']}";
        $value      = rm_field_value( $field, $values[ $field['name'] ] ?? null, $field_path );
        if ( $value === null && ! empty( $field['or_empty'] ) ) {
            $value = '';
        }
        if ( $value === null ) {
            if ( empty( $field['optional'] ) ) {
                throw new RM_Missing_Field( $field_path );
            }
            continue;
        }
        if ( ! empty( $field['spread'] ) && is_array( $value ) ) {
            $out = array_merge( $out, $value );
            continue;
        }
        $out[ $field['name'] ] = $value;
    }
    return $out;
}

function rm_field_label( $name ) {
    return ucfirst( trim( strtolower( preg_replace( '/([a-z])([A-Z])/', '$1 $2', str_replace( '_', ' ', $name ) ) ) ) );
}

/** The ACF field definitions for a schema; keys are field_rm_<group>_<path>. */
function rm_fields_to_acf( $group, array $schema, $prefix = '' ) {
    $fields = array();
    foreach ( $schema as $field ) {
        $key = 'field_rm_' . $group . '_' . ( $prefix === '' ? '' : $prefix . '_' ) . $field['name'];
        $acf = array(
            'key'      => $key,
            'name'     => $field['name'],
            'label'    => rm_field_label( $field['name'] ),
            'required' => ( empty( $field['optional'] ) && empty( $field['or_empty'] ) && $field['kind'] !== 'bool' ) ? 1 : 0,
        );
        $sub_prefix = ( $prefix === '' ? '' : $prefix . '_' ) . $field['name'];
        switch ( $field['kind'] ) {
            case 'text':
                $acf['type'] = 'text';
                break;
            case 'textarea':
                $acf += array( 'type' => 'textarea', 'rows' => 4, 'new_lines' => '' );
                break;
            case 'parts':
                $acf += array( 'type' => 'textarea', 'rows' => 2, 'new_lines' => '', 'instructions' => RM_PARTS_HELP );
                break;
            case 'image':
                $acf += array( 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium' );
                break;
            case 'file':
                $acf += array( 'type' => 'file', 'return_format' => 'id' );
                break;
            case 'link':
                $acf += array( 'type' => 'link', 'return_format' => 'array' );
                break;
            case 'link_plain':
                $acf += array(
                    'type'       => 'group',
                    'layout'     => 'row',
                    'sub_fields' => array(
                        array( 'key' => $key . '_title', 'name' => 'title', 'label' => 'Label', 'type' => 'text', 'required' => 1 ),
                        array( 'key' => $key . '_url', 'name' => 'url', 'label' => 'Link (optional)', 'type' => 'text', 'required' => 0 ),
                    ),
                );
                break;
            case 'icon':
                $acf += array( 'type' => 'text', 'instructions' => 'Icon name, set by the developers.' );
                break;
            case 'bool':
                $acf += array( 'type' => 'true_false', 'ui' => 1 );
                break;
            case 'group':
                $acf += array( 'type' => 'group', 'layout' => 'block', 'sub_fields' => rm_fields_to_acf( $group, $field['fields'], $sub_prefix ) );
                break;
            case 'list':
            case 'list_of':
                $acf += array(
                    'type'         => 'repeater',
                    'layout'       => $field['kind'] === 'list_of' ? 'table' : 'block',
                    'button_label' => 'Add',
                    'sub_fields'   => rm_fields_to_acf( $group, $field['fields'], $sub_prefix ),
                );
                break;
            default:
                throw new InvalidArgumentException( "Unknown field kind {$field['kind']}" );
        }
        $fields[] = $acf;
    }
    return $fields;
}
