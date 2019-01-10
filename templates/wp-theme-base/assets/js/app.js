(function($) {
'use strict';

$(document).ready( start );
$(document).on( 'page:load', start );
$(window).load( startOnLoad );

function start() {
  myApp.init();
  myNav.init();
}

// functions that needs to run only after everything loads
function startOnLoad() {
}


///// GENERAL LISTENERS

var myApp = {
  init() {
    var self = this;
    self.slider();
    // self.lightbox();
  },

  /*
    hSLider Example
    - Open the JS file to see the documentation.
  */
  slider() {
    var instance = $('.wp-block-gallery').hSlider({
      index: 0,
      arrows: true,
      dots: true,
      itemsPerSlide: 2,
    });

    // What "instance" object can do: https://github.com/electerious/basicSlider#instance-api
  },

  /*
    hLightbox Example
    - Open the JS file to see the documentation.
  */
  lightbox() {
    $('.wp-block-gallery a').hLightbox({
      closeButton: true,
      onOpen: (i) => {
        console.log('lightbox opened');
      },
      onClose: (i) => {
        console.log('lightbox closed');
      }
    });
  }
};


///// NAVIGATION

var myNav = {
  init() {
    var self = this;
    self.mobileNav();
    self.dialogNav();

    $(document).on( 'click', self.closeNav );
  },

  /*
    Toggle mobile nav
  */
  mobileNav() {
    var self = this;
    $('#nav-toggle').on( 'click', toggle );
    $('.nav-items').on( 'click', self.preventClose );
    

    function toggle( e ) {
      e.stopPropagation();
      $('body').removeClass( 'dialog-is-active' ).toggleClass( 'nav-is-active' );
    }
  },


  /*
    Toggle dialog nav

    <a data-dialog="my-dialog-id"> Click me </a>
    <div id="my-dialog-id" class="dialog">
      ...
    </div>
  */
  dialogNav() {
    var self = this;
    $( document.body ).on( 'click', '[data-dialog]', toggle );
    $( document.body ).on( 'click', '.dialog', self.preventClose );

    //
    function toggle( e ) {
      e.preventDefault();
      e.stopPropagation();
      var $target = $( '#' + $(this).data( 'dialog' ) );

      $('.dialog--active').not( $target ).removeClass( 'dialog--active' ); // close other nav dialog
      $('body').removeClass( 'nav-is-active' ).toggleClass( 'dialog-is-active' ); // close main menu, toggle dialog menu
      $target.toggleClass( 'dialog--active' ); // toggle selected dialog menu
    }
  },

  // Close all nav when clicking outside
  closeNav( e ) {
    $('.dialog--active').removeClass( 'dialog--active' );
    $('body').removeClass( 'nav-is-active  dialog-is-active' );
  },


  // Prevent nav closed when clicking this part
  preventClose( e ) {
    e.stopPropagation();
  }
};

// Browser compatibility, leave this untouched
if('registerElement' in document) { document.createElement( 'h-grid' ); document.createElement( 'h-tile' ); }
})( jQuery );
