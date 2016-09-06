<?php

new My_Shortcode();

///// SHORTCODE /////

class My_Shortcode {

  function __construct() {
    add_shortcode('icon', array($this, 'icon') );

    add_shortcode('row', array($this, 'row') );
    add_shortcode('column', array($this, 'column') );

    add_filter('the_content', array($this, 'grid_unautop'), 100);
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
    Wrap the content with Edje Row syntaxt, used together with [column] shortcode.
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

  /*
    Fix Paragraph tag wrapping the ROW and COLUMN shortcode
  */
  function grid_unautop($content) {
    $shortcodes = array('h-row', 'h-column');

    // if no h-row
    if(!strpos($content, $shortcodes[0]) ) {
      return $content;
    }

    // trim the extra <p>
    foreach($shortcodes as $sc) {
      $trim_list = array (
        '<p><' . $sc => '<' .$sc,
        '<p></' . $sc => '</' .$sc,
        $sc . '></p>' => $sc . '>',
        $sc . '><br />' => $sc . '>',
      );

      $content = strtr($content, $trim_list);

      // remove the remaining </p> after <h-column class="...">
      preg_match("/(<{$sc}[^<]+)/", $content, $matches); // get without p
      $content = preg_replace("/<{$sc}[^<]+<\/p>/", $matches[0], $content);
    }

    return $content;
  }
}
