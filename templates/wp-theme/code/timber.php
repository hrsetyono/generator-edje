<?php
///// TIMBER Global setting /////

class TimberH extends TimberSite {

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
    $context['nav'] = new TimberMenu( 'main-nav' );
    $context['social_nav'] = new TimberMenu( 'social-nav' );

    $context['site'] = $this;
    $context['home_url'] = home_url();
    $context['sidebar'] = Timber::get_widgets( 'my-sidebar' );

    $root = get_template_directory_uri();
    $context['images'] = $root.'/assets/images';
    $context['img'] = $context['images']; // alias
    $context['files'] = $root.'/assets/files';

    // if posts page, single post, or category page, add CATEGORY context
    if( is_home() || is_singular('post') || is_category() ) {
      // get blog menu, if null, get categories instead
      $context['blog_menu'] = has_nav_menu( 'blog-menu' ) ? new TimberMenu( 'blog-menu' ) : null;
      $context['categories'] = is_null( $context['blog_menu'] ) ? Timber::get_terms('category', array('parent' => 0)) : null;
    }

    // if there's option page
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

    /*
      Custom filter sample
      Usage: {{ post.content | my_filter }}
    */
    $twig->addFilter('my_filter', new Twig_Filter_Function(function( $text ) {
      return $text;
    }) );

    return $twig;
  }
}
