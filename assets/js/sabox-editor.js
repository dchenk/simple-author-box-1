(function( $ ) {

  'use strict';

  $( document ).ready( function() {
  	if ( $( '#description' ).length > 0 ) {
  		wp.editor.initialize( 'description', {
	      tinymce: {
	        wpautop: true,
	      },
	      quicktags: true
	    });
  	}
  });

})( jQuery );
