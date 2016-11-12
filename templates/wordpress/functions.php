<?php // Requirement : WP 4.1 and PHP 5.3

require_once 'code/timber.php';
if(!has_required_plugins() ) { return false; }
require_once 'code/addon.php';

add_action('init', 'my_post_type', 1);
add_action('widgets_init', 'my_widgets');
add_action('after_setup_theme', 'my_theme_support');
add_action('wp_enqueue_scripts', 'my_enqueue_script');

// ACF OPTION page
// if( function_exists('acf_add_options_page') ) {
//   acf_add_options_page();
// }

// Allow EDTIOR to edit Theme and Menu
$role_object = get_role('editor');
$role_object->add_cap('edit_theme_options');

/////

/*
  Register Custom Post Type and Taxonomy
  https://github.com/hrsetyono/edje-wp/wiki/Custom-Post-Type
*/
function my_post_type() {
  // H::register_post_type('product');
  // H::remove_menu(array('Comments', 'Media') );
}

/*
  Register widgets
*/
function my_widgets() {
  register_sidebar(array(
    'name' => 'Blog Sidebar',
    'id' => 'my-sidebar'
  ));
}

/*
  Feature supported by this theme
*/
function my_theme_support() {
  add_theme_support('post-thumbnails');
  add_theme_support('menus');

  add_theme_support('custom-logo');
  add_theme_support('jetpack-responsive-videos');

  add_theme_support('html5');
  add_theme_support('title_tag');
  add_post_type_support('page', 'excerpt');
}

/*
  Register all your CSS and JS here
*/
function my_enqueue_script() {
  $css_dir = get_template_directory_uri() . '/assets/css';
  $js_dir = get_template_directory_uri() . '/assets/js';

  // Stylesheet
  wp_enqueue_style('my-framework', $css_dir . '/framework.css');
  wp_enqueue_style('my-app', $css_dir . '/app.css');

  // JavaScript
  wp_enqueue_script('my-fastclick', $js_dir . '/vendor/fastclick.min.js', array(), false, true);
  wp_enqueue_script('my-fancybox', $js_dir . '/vendor/fancybox.min.js', array('jquery'), false, true);
  wp_enqueue_script('my-slick', $js_dir . '/vendor/slick.min.js', array('jquery'), false, true);
  wp_enqueue_script('my-app', $js_dir . '/app.js', array('jquery'), false, true);
}
