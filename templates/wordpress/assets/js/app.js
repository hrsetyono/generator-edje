(function($) {
'use strict';

$(document).ready(start);
$(document).on('page:load', start);
$(window).load(startOnLoad);

function start() {
  app.init();
  woo.init();

  animateOnScroll.init();
  responsiveTable.init();
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

    this.commentFormToggle();
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

  commentFormToggle: function() {
    var replyTitle = document.getElementById('reply-title').childNodes;
    var replyTo = replyTitle[1].childNodes[0].nodeValue;
    var placeholder = replyTitle[0].nodeValue + (replyTo ? replyTo : '') + '…';

    $('.comment-form textarea')
      .attr('placeholder', placeholder)
      .on('focus', activateForm);

    function activateForm(e) {
      $(this).closest('.comment-form').addClass('active');
    }
  }
};

/*
  RESPONSIVE TABLE
  - Freeze the first column on mobile.
  - Demo: http://codepen.io/hrsetyono/pen/KaKzzZ
*/

var responsiveTable = {
  init: function() {
    $(".post-content table").each(this.setup);
  },

  setup: function() {
    var $t = $(this);

    // no need to responsify if only three columns
    if($t.find('tr:first-child td, tr:first-child th').length <= 3) {
      return false;
    }

    // wrap large table and clone it to small one
    $t.wrap('<div class="table-large"></div>');
    var $tSmall = $t.clone().insertAfter($t.closest('.table-large') ).wrap('<div class="table-small"></div>');

    // mark the neighboring rowspan
    var $spannedCols = $tSmall.find('[rowspan]');
    $spannedCols.each(markSpannedColumn);

    // remove width attribute that can cause formatting issue
    $tSmall.find('[width]').removeAttr('width');

    // create pinned table
    var $tSmall2 = $tSmall.clone();
    $tSmall2.insertAfter($tSmall);

    // wrap both the small tables
    $tSmall.wrap('<div class="table-scrollable"></div>');
    $tSmall2.wrap('<div class="table-pinned"></div>');

    /////

    function markSpannedColumn() {
      var $cell = $(this);
      var num = $cell.attr('rowspan') - 1;

      $cell.closest('tr').nextAll('tr:lt(' + num + ')')
        .find('th:first-child, td:first-child').addClass('spanned');
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


// ----- WOOCOMMERCE -----

var woo = {
  init: function() {
    this.cartMenu();
  },

  // Toggle Cart dialog menu
  cartMenu: function() {
    if($('.cart-dialog').length == 0) { return false; }

    $('#menu-cart').on('click', toggle);
    $('.cart-dialog').on('click', preventClose);
    $(document).on('click', close);

    function toggle(e) {
      e.stopPropagation();
      $('.cart-dialog').toggleClass('cart-active');
    }

    function preventClose(e) { e.stopPropagation(); }
    function close(e) { $('.cart-dialog').removeClass('cart-active'); }
  }
}

// Browser compatibility, leave this untouched
if('registerElement' in document) { document.createElement('h-row'); document.createElement('h-column'); }

})(jQuery);
