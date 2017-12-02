<?php

require_once 'code/addon.php';
if( !MyHelper::has_required_plugins() ) { return false; }
require_once 'code/timber.php';
require_once 'code/addon-wc.php';

add_action( 'wp_enqueue_scripts', 'my_enqueue_script', 100 );
add_action( 'after_setup_theme', 'my_after_load_theme' );
add_action( 'after_switch_theme', 'my_after_activate_theme' );
add_action( 'init', 'my_init', 1 );
add_action( 'widgets_init', 'my_widgets' );

/////

/*
  Register all your CSS and JS here
*/
function my_enqueue_script() {
  $css_dir = get_stylesheet_directory_uri() . '/assets/css';
  $js_dir = get_stylesheet_directory_uri() . '/assets/js';

  // Stylesheet
  wp_enqueue_style( 'my-framework', $css_dir . '/framework.css' );
  wp_enqueue_style( 'my-app', $css_dir . '/app.css' );
  wp_enqueue_style( 'dashicons', get_stylesheet_uri(), 'dashicons' ); // WP native icons

  // JavaScript
  wp_enqueue_script( 'my-fastclick', $js_dir . '/vendor/fastclick.min.js', false, false, true );
  wp_enqueue_script( 'my-fancybox', $js_dir . '/vendor/fancybox.min.js', array('jquery'), false, true );
  wp_enqueue_script( 'my-slick', $js_dir . '/vendor/slick.min.js', array('jquery'), false, true );
  wp_enqueue_script( 'my-app', $js_dir . '/app.js', array('jquery'), false, true );

  // Enable comment's reply form
  if( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}



/*
  Run after theme is loaded
*/
function my_after_load_theme() {
  $GLOBALS['content_width'] = 600; // Blog article's width

  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'menus' );
  add_theme_support( 'custom-logo' );
  add_theme_support( 'title_tag' );
  add_theme_support( 'widgets' );
  add_theme_support( 'html5', array('search-form', 'comment-form', 'gallery', 'caption') );
  add_theme_support( 'jetpack-responsive-videos' );
  add_theme_support( 'automatic-feed-links' );
  add_theme_support( 'woocommerce' );

  add_theme_support( 'infinite-scroll', array(
    'footer' => false,
    'render' => function() {
      $context = array( 'posts' => Timber::get_posts() );
      Timber::render( 'partials/_posts.twig', $context );
    },
    'posts_per_page' => false
  ) );

  add_post_type_support( 'page', 'excerpt' ); // allow page to have excerpt


  // Create Nav assignment
  register_nav_menu( 'main-nav', 'Main Nav' );
  register_nav_menu( 'social-nav', 'Social Nav' );
}



/*
  Run only after this theme is activated
*/
function my_after_activate_theme() {
  // Allow EDITOR and SHOP MANAGER to edit appearances
  $role = get_role( 'editor' );
  $role ? $role->add_cap( 'edit_theme_options' ) : false;

  $role = get_role( 'shop_manager' );
  $role ? $role->add_cap( 'edit_theme_options' ) : false;
}



/*
  After Wordpress has finished loading but no data has been sent
*/
function my_init() {
  new MyShortcode();
  new MyFilter();
  new MyTimber();

  new MyProduct();
  new MyCart();
  new MyCheckout();

  /*
    Register Custom Post Type and Taxonomy
    https://github.com/hrsetyono/edje-wp/wiki/Custom-Post-Type
  */
  // H::register_post_type( 'product' );
  // H::remove_menu( array( 'Comments', 'Media' ) );


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
*/
function my_widgets() {
  register_sidebar( array('name' => 'My Sidebar', 'id' => 'my-sidebar') );
}
