<?php

new My_Shortcode();
new My_Filter();

///// SHORTCODE /////

class My_Shortcode {
  function __construct() {
    add_shortcode('button', array($this, 'button') );

    add_shortcode('row', array($this, 'row') );
    add_shortcode('column', array($this, 'column') );
  }

  /*
    Add button class to the link inside

    [button] link [/button]
  */
  function button($attr, $content = null) {
    // if have anchor inside, add button class
    if(preg_match('/<a (.+?)>/', $content, $match) ) {
      $content = substr_replace($content, ' class="button" ', 3, 0);
    }
    // else, make it into do-nothing button
    else {
      $content = '<a class="button">' . $content . '</a>';
    }

    // return wpautop($content);
    return wpautop($content);
  }

  /*
    Wrap the content with Edje Row syntax, used together with [column] shortcode.
    - Don't add line spacing between shortcode to avoid the <h-row> getting wrapped with <p>. See example below.

    [row][column size="8"]
      Content
    [/column][column size="4"]
      Content
    [/column][/row]
  */
  function row($atts, $content = null) {
    return '<h-row>' . do_shortcode($content) . '</h-row>';
  }

  function column($atts, $content = null) {
    $a = shortcode_atts( array(
      'size' => '12', // default is 12
    ), $atts);

    return '<h-column class="column-shortcode large-' . $a['size'] . '">' . do_shortcode($content) . '</h-column>';
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
