<?php
$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$context['post'] = array_shift($context['posts']); // latest post
$context['pagination'] = Timber::get_pagination();

$context['term'] = get_queried_object();

$templates = array('archive.twig');

// set different template
if(is_category() ) {
  $context['categories'] = Timber::get_terms('category');
  array_unshift($templates, 'archive-' . get_query_var('cat') . '.twig');
}
else if (is_post_type_archive() ){
  array_unshift($templates, 'archive-' . get_post_type() . '.twig');
}

Timber::render($templates, $context);
