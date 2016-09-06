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

class TimberH extends TimberSite {

  function __construct(){
    add_filter('timber_context', array($this, 'add_to_context') );
    add_filter('get_twig', array($this, 'add_to_twig') );
    parent::__construct();
  }

  /*
    Global Context
  */
  function add_to_context($context) {
    $context['menu'] = new TimberMenu();
    $context['site'] = $this;
    $context['home_url'] = home_url();

    // $context['options'] = get_fields('options');

    $root = get_template_directory_uri();
    $context['images'] = $root.'/assets/images';
    $context['img'] = $context['images']; // alias
    $context['css'] = $root.'/assets/css';
    $context['js'] = $root.'/assets/js';
    $context['files'] = $root.'/assets/files';

    return $context;
  }

  /*
    Extension for Twig
  */
  function add_to_twig($twig) {
    $twig->addExtension(new Twig_Extension_StringLoader() );

    // Parse Markdown
    $twig->addFilter('markdown', new Twig_Filter_Function(function($text) {
      $pd = new Parsedown();
      return $pd->text($text);
    }) );

    // Show all methods available in the object
    $twig->addFilter('methods', new Twig_Filter_Function(function($object) {
      return get_class_methods($object);
    }) );

    return $twig;
  }
}

new TimberH();
