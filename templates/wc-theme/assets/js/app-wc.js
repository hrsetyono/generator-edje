(function($) {
'use strict';

$(document).ready(start);
$(document).on('page:load', start);
$(window).load(startOnLoad);

function start() {
  myShop.init();
  myAccount.init();
}

// functions that needs to run only after everything loads
function startOnLoad() {
}


var myShop = {
  init: function() {
    $('.woocommerce-info, .woocommerce-message, .woocommerce-error').on( 'click', this.closeToast );
  },

  closeToast: function() {
    $(this).hide();
  }
};


var myAccount = {
  init: function() {
    // toggle between Register and Login form
    $('.h-toggle-form').on( 'click', this.toggleForm );
  },

  toggleForm: function() {
    $(this).closest( '.u-columns' ).find( '.u-column1, .u-column2' ).toggle();
  },
};


})( jQuery );
