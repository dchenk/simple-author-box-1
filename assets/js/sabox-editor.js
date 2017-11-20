(function( $ ) {

  'use strict';
  var SABox = {};

  $( document ).ready( function() {
    if ( $( '#description' ).length > 0 ) {
      wp.editor.initialize( 'description', {
        tinymce: {
          wpautop: true
        },
        quicktags: true
      } );
    }

    // Add Social Links
    $( '.sabox-add-social-link' ).click( function() {

      if ( undefined === SABox.html ) {
        SABox.html = '<tr> <th> <select name="sabox-social-icons[]">';
        $.each( SABHerlper.socialIcons, function( key, name ) {
          SABox.html = SABox.html + '<option value="' + key + '">' + name + '</option>';
        } );
        SABox.html = SABox.html + '</select></th><td><input name="sabox-social-links[]" type="text" class="regular-text"><td></tr>';
      }

      $( '#sabox-social-table' ).append( SABox.html );

    } );

    // Remove Social Link
    $( '#sabox-social-table' ).on( 'click', '.dashicons-no', function() {
      var row = $( this ).parents( 'tr' );
      row.fadeOut( 'slow', function() {
        row.remove();
      } );
    } );

  } );

})( jQuery );
