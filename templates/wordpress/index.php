<?php
$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$context['pagination'] = Timber::get_pagination();

$context['categories'] = Timber::get_terms('category');

Timber::render('index.twig', $context);
