<?php

new My_Shortcode();
new My_Filter();

///// SHORTCODE /////

class My_Shortcode {
  function __construct() {
    add_shortcode('icon', array($this, 'icon') );
    add_shortcode('button', array($this, 'button') );

    add_shortcode('dropcap', array($this, 'dropcap') );
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
    if(preg_match('/<a (.+?)>/', $content, $match) ) {
      return "<span class='button-shortcode'>{$content}</span>";
    } else {
      return "<a class='button'>{$content}</a>";
    }
  }

  function dropcap($atts, $content = null) {
    return '<span class="dropcap-shortcode">' . $content . '</span>';
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
