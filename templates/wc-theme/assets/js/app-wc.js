(function($) {
'use strict';

$(document).ready(start);
$(document).on('page:load', start);
$(window).load(startOnLoad);

function start() {
  myShop.init();
}

// functions that needs to run only after everything loads
function startOnLoad() {
}


var myShop = {
  init: function() {
    $('.woocommerce-message-close').on( 'click', this.closeToast );
  },

  closeToast: function() {
    $(this).closest( '.woocommerce-message, .woocommerce-info' ).hide();
  }
};


})( jQuery );
