(function( $ ) {

  'use strict';
  var SAB = {};

  SAB.form = {

    init: function( callback ) {
      var popup = $( '#sabox-add-user' ),
          form = popup.find( 'form' ),
          self = this;
      self.form = form;
      self.popup = popup;
      self.callback = callback;

      // Initialize editor
      wp.editor.initialize( 'sabox-description', {
        tinymce: {
          wpautop: true
        },
        quicktags: true
      } );

      // Get content
      self.editor = window.tinymce.get( 'sabox-description' );

      self.form.on( 'click', '#sabox-submit', function( evt ) {
        evt.preventDefault();
        self.submit();
      } );
      self.form.on( 'click', '.sabox-add-social-link', function() {
        self.addSocialLink();
      } );
      self.popup.on( 'click', '.close-popup', function() {
        self.popup.hide();
        self.callback( {} );
      } );

      self.form.on( 'click', '.social-link-item .dashicons-no', function() {
        var item = $( this ).parents( '.social-link-item' );
        item.fadeOut( 'slow', function() {
          item.remove();
        } );
      } );
    },

    clearInputs: function() {
      var self = this,
          socialLinkContainers = $( self.form ).find( '.sabox-media-links .social-link-item' ),
          inputs = $( self.form ).find( '.sabox-input' ),
          select = $( self.form ).find( '.sabox-select' );

      if ( socialLinkContainers.length > 1 ) {
        socialLinkContainers.not( ':first' ).remove();
      }

      inputs.val( '' );
      select.val( 'addthis' );
      self.editor.setContent( '' );

    },

    submit: function() {
      var self = this,
          form = $( self.form ),
          formData,
          error = false;

      if ( undefined !== self.editor ) {
        self.editor.save();
      }

      formData = form.serializeArray();

      if ( '' === form.find( '#sabox-username' ).val() ) {
        form.find( '#sabox-username' ).addClass( 'error' );
        error = true;
      }

      if ( '' === form.find( '#sabox-email' ).val() ) {
        form.find( '#sabox-email' ).addClass( 'error' );
        error = true;
      }

      if ( error ) {
        return;
      }

      formData.push( { 'name': 'action', 'value': 'sabox_create_user' } );

      $.post( SABHerlper.ajaxurl, formData, function( response ) {

        if ( 'ok' === response.status ) {
          self.callback( { 'value': response.user_id, 'text': response.user_name } );
          self.popup.hide();
        }
      }, 'json' );

    },

    addSocialLink: function() {
      var self = this;

      if ( undefined === SAB.html ) {
        SAB.html = '<div class="social-link-item"><select class="sabox-select" name="sabox-user[social-platform][]">';
        $.each( SABHerlper.socialIcons, function( key, name ) {
          SAB.html = SAB.html + '<option value="' + key + '">' + name + '</option>';
        } );
        SAB.html = SAB.html +
            '</select><div class="social-link-container"><input type="text" class="sabox-input" name="sabox-user[social-links][]" value="" placeholder="Social link ..."><span class="dashicons dashicons-no"></span></div></div>';
      }

      $( self.form ).find( '.sabox-media-links' ).append( SAB.html );

    },

    show: function( name, callback ) {
      var self = this;

      if ( undefined === self.popup ) {
        self.init( callback );
      }

      self.clearInputs();

      if ( '' !== name ) {
        self.form.find( '#sabox-username' ).val( name );
      }

      self.popup.show();

    }

  };

  SAB.selectize = {
    init: function() {
      var self = this;
      this.authorsContainer = $( '#sab-coauthors' );
      this.selectize = $( '#sabox-co-authors' ).selectize( {
        'placeholder': 'Choose Guest Author',
        create: function( input, callback ) {
          SAB.form.show( input, callback );
        },
        onItemAdd: function( value, $item ) {
          var html = '<div class="sab-co-author"><input type="hidden" name="sabox-coauthors[]" value="' + value + '"><span>' + $( $item ).text() +
              '</span><span class="dashicons dashicons-no"></span></div>';
          self.authorsContainer.append( html );
          self.selectize[ 0 ].selectize.clear( true );
        }
      } );
      self.selectize[ 0 ].selectize.clear( true );
    }
  };

  $( document ).ready( function() {

    SAB.selectize.init();

    $( '#sab-coauthors' ).on( 'click', '.dashicons-no', function() {
      var item = $( this ).parents( '.sab-co-author' );
      item.fadeOut( 'slow', function() {
        item.remove();
      } );
    } );

  } );

})( jQuery );
