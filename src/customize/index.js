/**
 * File customize.js.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */
( function( $, wp ) {
    // Bail if customize is not available.
    if ( ! wp.customize ) {
        return;
    }

	// Custom CSS for BuddyPress areas.
	wp.customize( 'bp_nouveau_appearance[meilleur_copain_custom_css]', function( value ) {
        value.bind( function( newValue ) {
            var currentStyle = $( '#bp-nouveau-inline-css' ).html(),
                toReplace = currentStyle.match( /\/\* meilleur-copain \*\/$([\s\S]*?)^\/\* meilleur-copain \*\//m );

            if ( toReplace && toReplace[1] ) {
                $( '#bp-nouveau-inline-css' ).html( currentStyle.replace( toReplace[1], '\n' + newValue + '\n' ) );
            }
		} );
    } );

} )( jQuery, window.wp || {} );
