<?php

new My_Shortcode();
new My_Filter();

///// SHORTCODE /////

class My_Shortcode {
  function __construct() {
    add_shortcode('icon', array($this, 'icon') );
    add_shortcode('button', array($this, 'button') );
  }

  /*
    Create a ligature icon powered by Material Icons
    Get the name list here: https://design.google.com/icons/

    [icon] name [/icon]
  */
  function icon($attr, $content = null) {
    return '<i class="icon">' . $content . '</i>';
  }

  /*
    Add button class to the link inside

    [button] link [/button]
  */
  function button($attr, $content = null) {
    return '<span class="button">' . $content . '</span>';
    return $content;
  }
}

///// FILTER /////

class My_Filter {
  function __construct() {
    add_filter('sample_filter', array($this, 'my_sample_filter') );
  }

  function my_sample_filter() {
    // do something
  }
}
