(function($) {
'use strict';

$(document).ready(start);
$(document).on('page:load', start);
$(window).load(startOnLoad);

function start() {
  myApp.init();
  myNav.init();
  myBlog.init();
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


///// BLOG POSTS

var myBlog = {
  init: function() {
    this.commentFormToggle();
  },


  // Open Comment Form and add Placeholder to the textarea
  commentFormToggle: function() {
    // exit if comment form not found
    if( $('#reply-title').length <= 0 ) { return false; }

    var replyTitle = document.getElementById( 'reply-title' ).childNodes;
    var replyTo = replyTitle[1].childNodes[0].nodeValue;
    var placeholder = replyTitle[0].nodeValue + (replyTo ? replyTo : '') + '…';

    $('.comment-form textarea').attr( 'placeholder', placeholder );
    $('.comment-form').on( 'click', activateForm );
    $('.comment-reply-link').on( 'click', activateForm );

    function activateForm(e) {
      var $form = $('.comment-form');
      $form.addClass('active');
      $form.off('click').find('textarea').focus();
    }
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
    $('#nav-toggle').on( 'click', open );
    $('.nav-wrapper').on( 'click', self.preventClose );


    function open( e ) {
      e.stopPropagation();
      self.closeNav(); // close all nav first
      $('body').toggleClass( 'nav-active' );
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
      var isTargetActive = $target.hasClass( 'nav-dialog--active' );

      self.closeNav(); // close all nav first
      isTargetActive ? $target.removeClass( 'nav-dialog--active' ) : $target.addClass( 'nav-dialog--active' );
    }
  },

  // Close all nav when clicking outside
  closeNav: function( e ) {
    $('.nav-dialog--active').removeClass( 'nav-dialog--active' );
    $('body').removeClass( 'nav-active' );
  },


  // Prevent nav closed when clicking this part
  preventClose: function( e ) {
    e.stopPropagation();
  }
};

// Browser compatibility, leave this untouched
if('registerElement' in document) { document.createElement( 'h-row' ); document.createElement( 'h-column' ); }

})( jQuery );
