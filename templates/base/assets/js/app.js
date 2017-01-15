(function($) {
'use strict';

$(document).ready(start);
$(document).on('page:load', start);
$(window).load(startOnLoad);

function start() {
  app.init();
  animateOnScroll.init();
}

// functions that needs to run only after everything loads
function startOnLoad() {

}

/////

// ----- GENERAL -----

var app = {
  init: function() {
    this.mobileMenu();
    this.searchMenu();
  },

  // Toggle Mobile menu
  mobileMenu: function() {
    $('.menu-toggle').on('click', open);
    $('.menu-wrapper').on('click', preventClose);
    $(document).on('click', close);

    function open(e) {
      e.stopPropagation();
      $('body').addClass('menu-active');
    }

    function preventClose(e) { e.stopPropagation(); }
    function close(e) { $('body').removeClass('menu-active'); }
  },

  // Toggle Search field
  searchMenu: function() {
    $('#search-toggle').on('click', toggle);

    function toggle(e) {
      e.preventDefault();

      var $form = $(this).closest('form');
      $form.toggleClass('search-active');
    }
  },
};

/*
  ANIMATE ON SCROLL
  - Animate the element when visible, uses [data-animate] attribute

  <div data-animate="fadeInUp">...</div>
*/
var animateOnScroll = {
  init: function() {
    var _this = this;
    if($('[data-animate]').length <= 0) { return false; }

    // Animate elements that already visible
    $("[data-animate]").each(_this.run);

    // Animate elements upon scrolling
    $(window).scroll(function() {
      $("[data-animate]").each(_this.run);
    });
  },

  /*
    Check whether the element is visible on screen. If yes, start animating.

    @param $element DOM
  */
  run: function($element) {
    var $element = $(this);

    var topOfWindow = $(window).scrollTop();
    var threshold = $(window).height() - 50;
    var imagePos = $element.offset().top;

    if (imagePos < topOfWindow + threshold) {
      animate.startAnimating($element);
    }
  },

  /*
    Start the animation with delay, if any

    @param $element DOM
  */
  startAnimating: function($element) {
    var animationData = $element.data("animate");
    var animation = {
      name: animationData.match(/^\S+/),
      delay: animationData.match(/\d+/) || 0,
    };

    var addAnimateClass = function() {
      $element.addClass("animated " + animation.name);
    };

    setTimeout(addAnimateClass, animation.delay);
  },
}

// Browser compatibility, leave this untouched
if('registerElement' in document) { document.createElement('h-row'); document.createElement('h-column'); }

})(jQuery);
