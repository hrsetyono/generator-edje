<?php
/*
  Check activation of required plugins

  @param boolean $show_message - Whether to show error message or not
  @return boolean
*/
function has_required_plugins($show_message = true) {
  $plugins_class = array('H', 'Timber');

  // check if all installed
  $all_installed = false;
  foreach($plugins_class as $c) {
    $all_installed = class_exists($c);

    if(! $all_installed) { break; } // if one is false, end loop
  }

  // show error message if all not installed AND it's admin page
  if(! $all_installed && $show_message && is_admin() && current_user_can('install_plugins') ) {
    $text = 'Please activate required plugins <a href="' . admin_url('plugins.php?s=timber') . '">here</a>.';

    add_action('admin_notices', function() use ($text) {
      echo '<div class="notice notice-error"><p>' . $text . '</p></div>';
    });
  }

  return $all_installed;
}

if(!has_required_plugins(false) ) { return false; }


///// TIMBER Global setting /////


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
    $context['site'] = $this;
    $context['home_url'] = home_url();
    $context['sidebar'] = Timber::get_widgets('my-sidebar');

    $root = get_template_directory_uri();
    $context['images'] = $root.'/assets/images';
    $context['img'] = $context['images']; // alias
    $context['files'] = $root.'/assets/files';

    // if posts page, single post, or category page, add CATEGORY context
    if(is_home() || is_single() || is_category() ) {
      // get blog menu, if null, get categories instead
      $context['blog_menu'] = has_nav_menu('blog-menu') ? new TimberMenu('blog-menu') : null;
      $context['categories'] = is_null($context['blog_menu']) ? Timber::get_terms('category', array('parent' => 0)) : null;
    }

    // if there's option page
    if(function_exists('acf_add_options_page') ) {
      $context['options'] = get_fields('options');
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
