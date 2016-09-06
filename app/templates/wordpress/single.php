<?php
$context = Timber::get_context();
$context['post'] = Timber::get_post();
$context['comment_form'] = TimberHelper::get_comment_form();

Timber::render(array('single-' . $post->post_type . '.twig', 'single.twig'), $context);
