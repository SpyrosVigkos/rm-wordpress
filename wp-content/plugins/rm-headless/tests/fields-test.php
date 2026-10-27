<?php
/**
 * Checks for includes/pages/fields.php. Run inside the container:
 *   wp eval-file wp-content/plugins/rm-headless/tests/fields-test.php
 */

$rm_failures = 0;
function rm_assert_same( $actual, $expected, $name ) {
    global $rm_failures;
    if ( $actual === $expected ) {
        echo "ok   $name\n";
        return;
    }
    $rm_failures++;
    echo "FAIL $name\n  expected " . wp_json_encode( $expected ) . "\n  actual   " . wp_json_encode( $actual ) . "\n";
}
function rm_assert_throws( $fn, $message, $name ) {
    global $rm_failures;
    try {
        $fn();
    } catch ( Throwable $e ) {
        if ( strpos( $e->getMessage(), $message ) !== false ) {
            echo "ok   $name\n";
            return;
        }
        $rm_failures++;
        echo "FAIL $name\n  message " . $e->getMessage() . "\n";
        return;
    }
    $rm_failures++;
    echo "FAIL $name\n  nothing thrown\n";
}

rm_assert_same( rm_text_parts( 'Master your *mix*' ), array( array( 'text' => 'Master your ', 'accent' => false ), array( 'text' => 'mix', 'accent' => true ) ), 'splits accent' );
rm_assert_same( rm_text_parts( 'a **b** c' )[1], array( 'text' => 'b', 'accent' => false, 'strong' => true ), 'splits strong' );
rm_assert_same( rm_text_parts( 'Rated 5* by teams' ), array( array( 'text' => 'Rated 5* by teams', 'accent' => false ) ), 'keeps a lone asterisk' );
rm_assert_same( rm_text_parts( 'Toll Free +1 (855) FOR-REEL [367-7335]' ), array( array( 'text' => 'Toll Free +1 (855) FOR-REEL [367-7335]', 'accent' => false ) ), 'keeps plain text' );
rm_assert_same( rm_text_parts( '*Data* driven' )[0], array( 'text' => 'Data', 'accent' => true ), 'accent at the start' );

rm_assert_same(
    rm_fields_to_site( array( rm_f_link( 'button' ) ), array( 'button' => array( 'title' => 'Book', 'url' => '/contact', 'target' => '' ) ) ),
    array( 'button' => array( 'label' => 'Book', 'href' => '/contact' ) ),
    'maps links'
);
rm_assert_same(
    rm_fields_to_site( array( rm_f_list( 'features', array( rm_f_text( 'title' ), rm_f_parts( 'text' ) ) ) ), array( 'features' => array( array( 'title' => 'A', 'text' => '*B*' ) ) ) ),
    array( 'features' => array( array( 'title' => 'A', 'text' => array( array( 'text' => 'B', 'accent' => true ) ) ) ) ),
    'maps repeaters'
);
rm_assert_same(
    rm_fields_to_site( array( rm_f_list_of( 'tabs', rm_f_text( 'value' ) ) ), array( 'tabs' => array( array( 'value' => 'One' ), array( 'value' => 'Two' ) ) ) ),
    array( 'tabs' => array( 'One', 'Two' ) ),
    'maps lists of values'
);
rm_assert_same(
    rm_fields_to_site( array( rm_f_group( 'cta', array( rm_f_optional( rm_f_text( 'href' ) ), rm_f_text( 'label' ) ) ) ), array( 'cta' => array( 'href' => '', 'label' => 'Go' ) ) ),
    array( 'cta' => array( 'label' => 'Go' ) ),
    'leaves out empty optional fields'
);
rm_assert_same(
    rm_fields_to_site( array( rm_f_bool( 'included' ) ), array( 'included' => false ) ),
    array( 'included' => false ),
    'keeps false booleans'
);
rm_assert_throws(
    function () {
        rm_fields_to_site( array( rm_f_group( 'hero', array( rm_f_text( 'title' ) ) ) ), array( 'hero' => array( 'title' => '' ) ), 'home' );
    },
    'home.hero.title',
    'names the empty required field'
);

rm_assert_same(
    rm_fields_to_site( array( rm_f_link_plain( 'button' ) ), array( 'button' => array( 'title' => 'Contact sales', 'url' => '' ) ) ),
    array( 'button' => array( 'label' => 'Contact sales', 'href' => '' ) ),
    'keeps a button without a link'
);
rm_assert_same(
    rm_fields_to_site( array( rm_f_or_empty( rm_f_text( 'eyebrow' ) ) ), array( 'eyebrow' => '' ) ),
    array( 'eyebrow' => '' ),
    'gives empty strings where allowed'
);
rm_assert_same(
    rm_fields_to_site( array( rm_f_optional( rm_f_group( 'cta', array( rm_f_text( 'label' ) ) ) ) ), array( 'cta' => array( 'label' => '' ) ) ),
    array(),
    'leaves out an empty optional group'
);

$acf = rm_fields_to_acf( 'home', array( rm_f_list( 'features', array( rm_f_text( 'title' ) ) ), rm_f_bool( 'on' ) ) );
rm_assert_same( $acf[0]['type'], 'repeater', 'builds repeaters' );
rm_assert_same( $acf[0]['sub_fields'][0]['key'], 'field_rm_home_features_title', 'keys follow the path' );
rm_assert_same( $acf[1]['required'], 0, 'booleans are never required' );

echo $rm_failures === 0 ? "all passed\n" : "$rm_failures failed\n";
