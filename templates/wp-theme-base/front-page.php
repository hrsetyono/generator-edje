<?php
$context = Timber::get_context();

if( is_home() ) {
  $context['posts'] = Timber::get_posts();
  Timber::render( 'index.twig', $context );
} else {
  $context['post'] = Timber::get_post();
  Timber::render( 'front-page.twig', $context );
}
