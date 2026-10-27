/**
 * The block editor's autosave (which Preview triggers) sends the title and
 * body but not the ACF fields, so a preview would show the saved fields.
 * This adds the current field values to the autosave request; preview.php
 * stores them on the autosave revision, never on the post itself.
 */
( function ( wp, acf, $ ) {
	if ( ! wp || ! wp.apiFetch || ! acf ) {
		return;
	}
	var AUTOSAVE = /\/wp\/v2\/[a-z_-]+\/\d+\/autosaves(\?|$)/;
	var FORMS = '.metabox-location-normal, .metabox-location-side, .metabox-location-advanced';

	wp.apiFetch.use( function ( options, next ) {
		var target = options.path || options.url || '';
		if ( options.method === 'POST' && AUTOSAVE.test( target ) ) {
			var fields = acf.serialize( $( FORMS ), 'acf' );
			if ( fields && Object.keys( fields ).length ) {
				options = Object.assign( {}, options, {
					data: Object.assign( {}, options.data, { rm_acf: fields } ),
				} );
			}
		}
		return next( options );
	} );
} )( window.wp, window.acf, window.jQuery );
