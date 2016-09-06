<?php // Requirement : WP 4.1 and PHP 5.3

require_once 'code/timber.php';
require_once 'code/woo.php';

if(!has_required_plugins() ) { return false; }

add_action('init', 'my_post_type');
add_action('after_setup_theme', 'my_theme_support');
add_action('wp_enqueue_scripts', 'my_enqueue_script');

// ACF OPTION page
// if( function_exists('acf_add_options_page') ) {
//   acf_add_options_page();
// }

/////

/*
  Register Custom Post Type and Taxonomy
  https://github.com/hrsetyono/edje-wp/wiki/Custom-Post-Type
*/
function my_post_type() {
  // H::register_post_type('product');
}

/*
  Feature supported by this theme
*/
function my_theme_support() {
  add_theme_support('post-thumbnails');
  add_theme_support('menus');

  add_theme_support('html5');
  add_theme_support('title_tag');
  add_post_type_support('page', 'excerpt');

  add_theme_support('woocommerce');
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
  wp_enqueue_script('my-app', $js_dir . '/app.js', array('jquery'), false, true);
}
