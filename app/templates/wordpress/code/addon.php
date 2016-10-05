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
    add_filter('comment_class', array($this, 'comment_class'), null, 4);
  }

  /*
    Modify the 'depth' in comment's classes

    @param $classes (arr) - Array of all class
    @param $class (str) - Comma separated string of all class
    @param $comment_id (int)
    @param $comment (obj) - WP_Comment object

    @return arr - Array of all class
  */
  function comment_class($classes, $class, $comment_id, $comment) {
    // change depth if has parent
    if($comment->comment_parent > 0) {
      $index = array_search('depth-1', $classes);
      $classes[$index] = 'depth-2';
    }

    return $classes;
  }

}
