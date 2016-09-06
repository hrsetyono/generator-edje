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
    // sample jquery listener format
    $('.something').on('click', this.doSomething);
    $('.something-2').on('click', this.doSomething2);
  },
  doSomething: function(e) {},
  doSomething2: function(e) {},
};

// Browser compatibility, leave this untouched
if('registerElement' in document) { document.createElement('h-row'); document.createElement('h-column'); }

})(jQuery);
