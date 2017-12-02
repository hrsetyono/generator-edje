<?php
///// TIMBER Global setting /////

class MyTimber extends TimberSite {

  function __construct(){
    add_filter( 'timber_context', array( $this, 'add_to_context' ) );
    add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
    parent::__construct();
  }

  /*
    Global Context
    @filter timber_context
  */
  function add_to_context( $context ) {
    global $woocommerce;
    $context['woo'] = $woocommerce;

    $context['nav'] = new TimberMenu( 'main-nav' );
    $context['social_nav'] = new TimberMenu( 'social-nav' );

    $context['site'] = $this;
    $context['home_url'] = home_url();
    $context['shop_url'] = get_permalink( wc_get_page_id( 'shop' ) );
    $context['checkout_url'] = wc_get_checkout_url();

    $context['sidebar'] = Timber::get_widgets( 'my-sidebar' );

    $root = get_template_directory_uri();
    $context['images'] = $root.'/assets/images';
    $context['img'] = $context['images']; // alias
    $context['files'] = $root.'/assets/files';

    // if posts page, single post, or category page, use CATEGORIES as nav
    if( is_home() || is_singular('post') || is_category() ) {
      $context['blog_url'] = get_post_type_archive_link( 'post' );
      $context['blog_nav'] = Timber::get_terms( 'category', array('parent' => 0) );
    }

    // if ACF installed
    if(function_exists( 'acf_add_options_page' )) {
      $context['options'] = get_fields( 'options' );
    }

    return $context;
  }

  /*
    Custom Filter for Twig
    @filter get_twig
  */
  function add_to_twig( $twig ) {
    $twig->addExtension( new Twig_Extension_StringLoader() );

    // Custom filter sample
    // Usage: {{ post.content | my_filter }}
    $twig->addFilter('my_filter', new Twig_Filter_Function(function( $text ) {
      return $text;
    }) );

    // Get category / term permalink
    // Usage: {{ post.categories[0] | get_term_link  }}
    $twig->addFilter('get_term_link', new Twig_Filter_Function(function( $cat_id ) {
      return get_term_link( $cat_id );
    }) );

    return $twig;
  }
}
