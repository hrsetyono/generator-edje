(function($) {
'use strict';

$(document).ready(start);
$(document).on('page:load', start);

function start() {
  app.init();
  woo.init();
}

/////

// ----- GENERAL -----

var app = {
  init: function() {
    this.mobileMenu();
    this.searchMenu();
    this.pagination();
  },

  // Toggle Mobile menu
  mobileMenu: function() {
    $('#menu-toggle').on('click', open);
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

  // Handle pagination dropdown
  pagination: function() {
    if($('#pagination').length <= 0) { return false; }

    $('#pagination').on('change', onChange);

    function onChange(e) {
      var url = $(this).val();
      window.location = url;
    }
  }
};

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
