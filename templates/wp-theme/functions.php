<?php

require_once 'code/addon.php';
if( !MyHelper::has_required_plugins() ) { return false; }
require_once 'code/timber.php';

add_action( 'wp_enqueue_scripts', 'my_enqueue_scripts', 100 );
add_action( 'after_setup_theme', 'my_after_setup_theme' );
add_action( 'init', 'my_init' );
add_action( 'widgets_init', 'my_widgets' );

new MyFilter();

/////

/*
  Register all your CSS and JS here
  @action wp_enqueue_scripts 100
*/
function my_enqueue_scripts() {
  $css_dir = get_stylesheet_directory_uri() . '/assets/css';
  $js_dir = get_stylesheet_directory_uri() . '/assets/js';

  // Stylesheet
  wp_enqueue_style( 'my-framework', $css_dir . '/framework.css' );
  wp_enqueue_style( 'my-app', $css_dir . '/app.css' );
  wp_enqueue_style( 'dashicons', get_stylesheet_uri(), 'dashicons' ); // WP native icons

  // JavaScript
  wp_enqueue_script( 'my-photoswipe', $js_dir . '-vendor/photoswipe.min.js', array('jquery'), false, true );
  wp_enqueue_script( 'my-slick', $js_dir . '-vendor/slick.min.js', array('jquery'), false, true );
  wp_enqueue_script( 'my-app', $js_dir . '/app.js', array('jquery'), false, true );
}



/*
  Run after theme is loaded
  @action after_setup_theme
*/
function my_after_setup_theme() {
  $GLOBALS['content_width'] = 600; // Blog width, affect Jetpack Tiled-Gallery size
  add_theme_support( 'widgets' );

  // Jetpack support
  add_theme_support( 'infinite-scroll', array(
    'footer' => false,
    'posts_per_page' => false,
    'render' => function() {
      $context = array( 'posts' => Timber::get_posts() );
      Timber::render( 'partials/_posts.twig', $context );
    },
  ) );

  // Gutenberg support
  add_theme_support( 'align-wide' );

  // Create Nav assignment
  register_nav_menu( 'main-nav', 'Main Nav' );
  register_nav_menu( 'social-nav', 'Social Nav' );
}



/*
  After Wordpress has finished loading but no data has been sent
  @action init 1
*/
function my_init() {
  new MyShortcode();
  new MyTimber();

  /*
    Register Custom Post Type and Taxonomy
    https://github.com/hrsetyono/wp-edje/wiki/Custom-Post-Type
  */
  // H::register_post_type( 'product' );

  // ACF Option page
  if( function_exists( 'acf_add_options_page' ) ) {
    acf_add_options_sub_page(array(
  		'page_title' => 'Theme Options',
  		'parent_slug' => 'themes.php',
  	));
  }
}

/*
  Register widgets
  @action widgets_init
*/
function my_widgets() {
  register_sidebar( array('name' => 'My Sidebar', 'id' => 'my-sidebar') );
}
