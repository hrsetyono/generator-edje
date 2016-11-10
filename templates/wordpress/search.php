<?php
$context = Timber::get_context();
$context['query'] = get_search_query();
$context['posts'] = Timber::get_posts();

Timber::render('search.twig', $context);
