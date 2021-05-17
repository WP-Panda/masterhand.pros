( function( $ ) {

	// Update the site title in real time...
	wp.customize( 'blogname', function( value ) {
		value.bind( function( newval ) {
			$( '#site-title a' ).html( newval );
		} );
	} );



	//Update site title color in real time...
	wp.customize( 'bg_header', function( value ) {
		value.bind( function( newval ) {
			$('#main_header').css('background-color', newval );
			console.log(newval);
		} );
	} );
	wp.customize( 'textcolor_header', function( value ) {
		value.bind( function( newval ) {
			$('.fre-menu-main li a,.fre-login>li a').css('color', newval );
		} );
	} );
	//Update site title color in real time...
	wp.customize( 'primary_color', function( value ) {
		value.bind( function( newval ) {
			$('.primary-bg-color').css({'background-color':newval,'border-color': newval} );
			$('.primary-color').css({'color': newval, 'border-color':newval} );

			console.log(newval);
		} );
	} );
	wp.customize( 'secondary_color', function( value ) {
		value.bind( function( newval ) {
			$('.secondary-color').css({'color': newval} );

			console.log(newval);
		} );
	} );

	//Update site background color...
	wp.customize( 'bg_footer', function( value ) {
		value.bind( function( newval ) {
			$('footer').css('background-color', newval );
		} );
	} );

	wp.customize( 'textcolor_footer', function( value ) {
		value.bind( function( newval ) {
			$('footer a, footer, footer h2.widgettitle').css('color', newval );
		} );
	} );

	//Update site link color in real time...
	wp.customize( 'bg_copyright', function( value ) {
		value.bind( function( newval ) {
			$('.copyright-wrapper ').css('background-color', newval );
		} );
	} );

} )( jQuery );
