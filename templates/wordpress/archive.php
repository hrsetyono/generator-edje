<?php
$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$query = get_queried_object();

// if infinite scroll not active, add Pagination
if(!class_exists('Jetpack') || !Jetpack::is_module_active('infinite-scroll') ) {
  $context['pagination'] = Timber::get_pagination();
}

// Decide which template to use
$templates = array('archive.twig');

if(is_category() ) {
  $context['term'] = $query;
  array_unshift($templates, 'archive-' . get_query_var('cat') . '.twig');
}
else if(is_post_type_archive() ) {
  array_unshift($templates, 'archive-' . get_post_type() . '.twig');
}
else if(is_author() ) {
  $context['user'] = $query;
  array_unshift($templates, 'archive-author.twig');
}

Timber::render($templates, $context);
