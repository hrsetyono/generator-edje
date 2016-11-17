<?php
/*
  Check activation of required plugins
  @param boolean $show_message - Whether to show error message or not
  @return boolean
*/
function has_required_plugins($show_message = true) {
  $plugins_class = array('H', 'Timber');

  $pass = array_reduce($plugins_class, function($result, $c) {
    // if result already false, always return false
    return ($result) ? class_exists($c) : false;
  }, true);

  // show error message if not pass
  if(!$pass && $show_message) {
    $text = 'TIMBER or EDJE WP is not activated. Please <a href="' . admin_url('plugins.php#timber') . '">visit here</a> to active it.';

    if(is_admin() && current_user_can('install_plugins') ) {
      add_action('admin_notices', function() use ($text) {
        echo '<div class="notice notice-error"><p>' . $text . '</p></div>';
      });
    }
  }

  return $pass;
}
if(!has_required_plugins(false) ) { return false; }

// ------------------------
// Timber Global setting
// ------------------------

new TimberH();
class TimberH extends TimberSite {

  function __construct(){
    add_filter('timber_context', array($this, 'add_to_context') );
    add_filter('get_twig', array($this, 'add_to_twig') );

    parent::__construct();
  }

  /*
    Global Context

    @filter timber_context
  */
  function add_to_context($context) {
    $context['menu'] = new TimberMenu('top-menu');
    $context['logo'] = new TimberImage(get_theme_mod('custom_logo') );

    $context['site'] = $this;
    $context['home_url'] = home_url();

    // $context['options'] = get_fields('options');

    $root = get_template_directory_uri();
    $context['images'] = $root.'/assets/images';
    $context['img'] = $context['images']; // alias
    $context['css'] = $root.'/assets/css';
    $context['js'] = $root.'/assets/js';
    $context['files'] = $root.'/assets/files';

    // if posts page, single post, or category page
    if(is_home() || is_single() || is_category() ) {
      // get blog menu, if null, get categories instead
      $context['blog_menu'] = has_nav_menu('blog-menu') ? new TimberMenu('blog-menu') : null;
      $context['categories'] = is_null($context['blog_menu']) ? Timber::get_terms('category', array('parent' => 0)) : null;
    }

    return $context;
  }

  /*
    Custom Filter for Twig

    @filter get_twig
  */
  function add_to_twig($twig) {
    $twig->addExtension(new Twig_Extension_StringLoader() );

    // Example
    $twig->addFilter('myfilter', new Twig_Filter_Function(function($text) {
      return $text;
    }) );

    return $twig;
  }
}
