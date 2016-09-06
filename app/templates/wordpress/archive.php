<?php
$context = Timber::get_context();
$context['posts'] = Timber::get_posts();

$context['title'] = 'Archive';
$templates = array('archive.twig');

// set title and template
if (is_tag() ) {
  $context['title'] = single_tag_title('', false);
}
else if (is_category() ){
  $context['title'] = single_cat_title('', false);
  array_unshift($templates, 'archive-' . get_query_var('cat') . '.twig');
}
else if (is_post_type_archive() ){
  $context['title'] = post_type_archive_title('', false);
  array_unshift($templates, 'archive-' . get_post_type() . '.twig');
}

Timber::render($templates, $context);
