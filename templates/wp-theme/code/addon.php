<?php
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
    add_action( 'after_switch_theme', array($this, 'allow_editor_edit_appearance') );
  }

  /*
    Allow EDITOR to edit Appearance settings like Customize and Menu
  */
  function allow_editor_edit_appearance() {
    $role = get_role( 'editor' );
    $role ? $role->add_cap( 'edit_theme_options' ) : false;
  }
}




///// HELPER /////

class MyHelper {
  /*
    Check activation of required plugins
    @param $plugins mixed - String or array of Class name to check for existence
    @return boolean
  */
  static function has_required_plugins( $plugins = array('H', 'Timber') ) {
    $is_all_installed = true;

    // if any of the plugins doesn't exist, break the loop
    if( is_array( $plugins ) ) {
      foreach( $plugins as $p ) {
        $is_all_installed = class_exists( $p );
        if( !$is_all_installed ) { break; }
      }
    } else {
      $is_all_installed = class_exists( $plugins );
    }



    // show error message if all not installed AND it's admin page
    $is_admin_page = is_admin() && current_user_can('install_plugins');
    if( !$is_all_installed && $is_admin_page ) {
      add_action('admin_notices', function() {
        $text = sprintf(
          __('You need to activate WP Edje and Timber. <a href="%s">Activate now »</a>.', 'my'),
          admin_url('plugins.php')
        );
        echo '<div class="notice notice-error"><p>' . $text . '</p></div>';
      });
    }
    return $is_all_installed;
  }
}
