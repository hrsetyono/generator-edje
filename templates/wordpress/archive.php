<?php
$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$query = get_queried_object();

// if infinite scroll not active, add Pagination
if( !class_exists('Jetpack') || !Jetpack::is_module_active('infinite-scroll') || is_paged() ) {
  $context['pagination'] = Timber::get_pagination();
}

// Decide which template to use
$templates = array( 'archive.twig' );

if( is_category() || is_tax() ) {
  $context['term'] = $query;
  array_unshift( $templates, 'archive-' . $query->taxonomy . '.twig' );
}
elseif( is_post_type_archive() ) {
  array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
}
elseif( is_author() ) {
  $context['user'] = new TimberUser( $query->ID );
  array_unshift( $templates, 'archive-author.twig' );
}

Timber::render($templates, $context);
