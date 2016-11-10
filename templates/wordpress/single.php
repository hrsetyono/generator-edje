<?php
$context = Timber::get_context();
$context['post'] = Timber::get_post();

Timber::render(array('single-' . $post->post_type . '.twig', 'single.twig'), $context);
