(function( $ ) {


	'use strict';
	var context = $( '#sabox-container' );


	context.find( '.saboxfield' ).on( 'change', function() {

		var value = getElementValue( $( this ) );

		var elements = context.find( '.show_if_' + $( this ).attr( 'id' ) );

		if ( value && '0' !== value ) {
			elements.show();
		} else {
			elements.hide();
		}
	} );

	function getElementValue( $element ) {
		var type = $element.attr( 'type' );
		var name = $element.attr( 'name' );

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




	/**
	 * Handle UI tab switching via jQuery instead of relying on CSS only
	 */
	function admin_tab_switching() {

		var nav_tab_selector = '.nav-tab-wrapper a';

		/**
		 * Default tab handling
		 */

		// make the first tab active by default
		$( nav_tab_selector + ':first' ).addClass( 'nav-tab-active' );

		// get the first tab href
		var initial_tab_href = $( nav_tab_selector + ':first' ).attr( 'href' );

		// make all the tabs, except the first one hidden
		$( '.epfw-turn-into-tab' ).each( function( index, value ) {
			if ( '#' + $( this ).attr( 'id' ) !== initial_tab_href ) {
				$( this ).hide();
			}
		} );

		/**
		 * Listen for click events on nav-tab links
		 */
		$( nav_tab_selector ).click( function( event ) {

			$( nav_tab_selector ).removeClass( 'nav-tab-active' ); // remove class from previous selector
			$( this ).addClass( 'nav-tab-active' ).blur(); // add class to currently clicked selector

			var clicked_tab = $( this ).attr( 'href' );

			$( '.epfw-turn-into-tab' ).each( function( index, value ) {
				if ( '#' + $( this ).attr( 'id' ) !== clicked_tab ) {
					$( this ).hide();
				}

				$( clicked_tab ).fadeIn();

			} );

			// prevent default behavior
			event.preventDefault();

		} );
	}

	$( document ).ready( function() {
		var elements = context.find( '.saboxfield' ),
			sliders = context.find( '.sabox-slider' ),
			colorpickers = context.find( '.sabox-color' );


		if ( sliders.length > 0 ) {
			sliders.each( function( $index, $slider ) {
				var input = $( $slider ).parent().find( '.saboxfield' ),
					max = input.data( 'max' ),
					min = input.data( 'min' ),
					step = input.data( 'step' ),
					value = parseInt( input.val(), 10 );

				$( $slider ).slider( {
					value: value,
					min: min,
					max: max,
					step: step,
					slide: function( event, ui ) {
						input.val( ui.value + 'px' ).trigger( 'change' );
					}
				} );
			} );
		}
		if ( colorpickers.length > 0 ) {
			colorpickers.each( function( $index, $colorpicker ) {
				$( $colorpicker ).wpColorPicker();
			} );
		}

		admin_tab_switching();

	} );


})( jQuery );