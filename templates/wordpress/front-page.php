<?php
$context = Timber::get_context();

if( is_home() ) {
  $context['posts'] = Timber::get_posts();
  
  // if infinite scroll not active, add Pagination
  if(!class_exists('Jetpack') || !Jetpack::is_module_active('infinite-scroll') || is_paged() ) {
    $context['pagination'] = Timber::get_pagination();
  }

  Timber::render( 'index.twig', $context );
} else {
  $context['post'] = Timber::get_post();
  Timber::render( 'front-page.twig', $context );
}
