(function($) {
'use strict';

$(document).ready(start);
$(document).on('page:load', start);

function start() {
  app.init();
}

/////

var app = {
  init: function() {
    this.searchMenu();
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

// Browser compatibility, leave this untouched
if('registerElement' in document) { document.createElement('h-row'); document.createElement('h-column'); }

})(jQuery);
