(function( $ ) {

  'use strict';
  var context = $( '#sabox-cotnainer' );
  context.find( '.saboxfield' ).change( function() {
    var value = getElemetValue( $( this ) ),
        elements = context.find( '.show_if_' + $( this ).attr( 'id' ) );
    if ( value && '0' !== value ) {
      elements.show();
    } else {
      elements.hide();
    }
  } );

  function getElemetValue( $element ) {
    var type = $element.attr( 'type' );

    if ( 'checkbox' === type ) {
      if ( $element.is( ':checked' ) ) {
        return 1;
      } else {
        return 0;
      }
    } else {
      return $element.val();
    }
  }

  $( document ).ready( function() {
    var elements = context.find( '.saboxfield' ),
        sliders = context.find( '.sabox-slider' ),
        colorpickers = context.find( '.sabox-color' );

    elements.each( function( $index, $element ) {
      var element = $( $element ),
          value = getElemetValue( element ),
          elements = context.find( '.show_if_' + element.attr( 'id' ) );
      if ( value && '0' !== value ) {
        elements.removeClass( 'hide' );
      } else {
        elements.addClass( 'hide' );
      }
    } );
    if ( sliders.length > 0 ) {
      sliders.each( function( $index, $slider ) {
        var input = $( $slider ).parent().find( '.saboxfield' ),
            max = input.data( 'max' ),
            min = input.data( 'min' ),
            step = input.data( 'step' ),
            value = input.val(),
            handle = $( $slider ).parent().find( '.custom-handle' );

        $( $slider ).slider( {
          value: value,
          min: min,
          max: max,
          step: step,
          slide: function( event, ui ) {
            input.val( ui.value ).trigger( 'change' );
            handle.text( ui.value );
          },
          create: function() {
            handle.text( $( this ).slider( 'value' ) );
          }
        } );
      } );
    }
    if ( colorpickers.length > 0 ) {
      colorpickers.each( function( $index, $colorpicker ) {
        $( $colorpicker ).wpColorPicker();
      } );
    }

  } );

})( jQuery );
