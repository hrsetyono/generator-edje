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
    // if have anchor inside, add button class
    if(preg_match('/<a (.+?)>/', $content, $match) ) {
      $content = substr_replace($content, ' class="button" ', 3, 0);
      return $content;
    }
    // else, make it into do-nothing button
    else {
      return '<a class="button">' . $content . '</a>';
    }
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
