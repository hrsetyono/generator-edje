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
  init: function() {
    $('.sample-div').on( 'click', this.sampleListener );
  },

  sampleListener: function( e ) {
    // do something
  }
};


///// NAVIGATION

var myNav = {
  init: function() {
    var self = this;
    self.mobileNav();
    self.dialogNav();

    $(document).on( 'click', self.closeNav );
  },

  /*
    Toggle mobile nav
  */
  mobileNav: function() {
    var self = this;
    $('#nav-toggle').on( 'click', toggle );
    $('.nav-wrapper').on( 'click', self.preventClose );


    function toggle( e ) {
      e.stopPropagation();
      $('body').removeClass( 'nav-dialog-is-active' ).toggleClass( 'nav-is-active' );
    }
  },


  /*
    Toggle dialog nav

    <a data-nav-toggle="my-dialog-id"> Click me </a>
    <div id="my-dialog-id" class="nav-dialog">
      ...
    </div>
  */
  dialogNav: function() {
    var self = this;
    $('.main-nav').on( 'click', '[data-nav-dialog]', toggle );
    $( document.body ).on( 'click', '.nav-dialog', self.preventClose );

    function toggle( e ) {
      e.preventDefault();
      e.stopPropagation();
      var $target = $( '#' + $(this).data( 'nav-dialog' ) );

      $('.nav-dialog--active').not( $target ).removeClass( 'nav-dialog--active' ); // close other nav dialog
      $('body').removeClass( 'nav-is-active' ).toggleClass( 'nav-dialog-is-active' ); // close main menu, toggle dialog menu
      $target.toggleClass( 'nav-dialog--active' ); // toggle selected dialog menu
     }
  },

  // Close all nav when clicking outside
  closeNav: function( e ) {
    $('.nav-dialog--active').removeClass( 'nav-dialog--active' );
    $('body').removeClass( 'nav-is-active nav-dialog-is-active' );
  },


  // Prevent nav closed when clicking this part
  preventClose: function( e ) {
    e.stopPropagation();
  }
};

// Browser compatibility, leave this untouched
if('registerElement' in document) { document.createElement( 'h-grid' ); document.createElement( 'h-tile' ); }
})( jQuery );
