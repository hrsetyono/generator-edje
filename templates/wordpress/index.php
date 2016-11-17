<?php
$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$context['post'] = array_shift($context['posts']); // latest post

// if infinite scroll not active, add Pagination
if(!class_exists('Jetpack') || !Jetpack::is_module_active('infinite-scroll') ) {
  $context['pagination'] = Timber::get_pagination();
}

Timber::render('index.twig', $context);
