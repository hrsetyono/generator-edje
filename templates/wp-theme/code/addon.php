<?php

///// HELPER /////

class MyHelper {
  /*
    Check activation of required plugins
    @return boolean
  */
  static function has_required_plugins() {
    $is_all_installed = class_exists( 'H' ) && class_exists( 'Timber' );
    $is_admin_page = is_admin() && current_user_can('install_plugins');
    // show error message if all not installed AND it's admin page
    if( !$is_all_installed && $is_admin_page ) {
      $text = 'Please activate required plugins <a href="' . admin_url('plugins.php?s=timber') . '">here</a>.';
      add_action('admin_notices', function() use ( $text ) {
        echo '<div class="notice notice-error"><p>' . $text . '</p></div>';
      });
    }
    return $is_all_installed;
  }
}


///// SHORTCODE /////

class MyShortcode {
  function __construct() {
    add_shortcode( 'button', array($this, 'button') );
  }

  /*
    Add button class to the link inside
    Usage: [button] link [/button]
  */
  function button($attr, $content = null) {
    // if have anchor inside, add button class
    if(preg_match( '/<a (.+?)>/', $content, $match ) ) {
      $content = substr_replace( $content, ' class="button" ', 3, 0 );
    }
    // else, make it into do-nothing button
    else {
      $content = '<a class="button">' . $content . '</a>';
    }

    return wpautop( $content );
  }
}

///// FILTER /////

class MyFilter {
  function __construct() {
    add_filter( 'filter_name', array($this, 'my_filter') );
  }

  function my_filter() {

  }
}
