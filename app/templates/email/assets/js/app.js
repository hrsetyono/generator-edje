// Add cellpadding and cellspacing attribute
// This is just for testing purposes. The grunt task will automatically add this to all table
(function(){
  var tables = document.querySelectorAll("table");
  for(var i = 0, len = tables.length; i < len; i++) {
    tables[i].setAttribute("cellpadding", "0");
    tables[i].setAttribute("cellspacing", "0");
  }
})();