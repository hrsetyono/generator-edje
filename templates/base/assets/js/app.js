(function($) {
'use strict';

$(document).ready(start);
$(document).on('page:load', start);
$(window).load(startOnLoad);

function start() {
  myApp.init();
  myNav.init();
}

// functions that needs to run only after everything loads
function startOnLoad() {
}


/////


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
      $('body').toggleClass( 'nav-is-active' );
    }
  },

  // Close all nav when clicking outside
  closeNav: function( e ) {
    $('body').removeClass( 'nav-is-active' );
  },

  // Prevent nav closed when clicking this part
  preventClose: function( e ) {
    e.stopPropagation();
  }
};


// Browser compatibility, leave this untouched
if('registerElement' in document) { document.createElement( 'h-row' ); document.createElement( 'h-column' ); }

})( jQuery );
