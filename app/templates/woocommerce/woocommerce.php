<?php

$context = Timber::get_context();
$context['sidebar'] = Timber::get_widgets('shop-sidebar');

// SINGLE PRODUCT
if (is_singular('product') ) {
  $post = Timber::get_post();
  $context['post'] = $post;

  $product = $context['post']->product;
  $context['product'] = $product;

  Timber::render('woo/single.twig', $context);
}

// CATEGORY or SHOP
else {
  $context['posts'] = Timber::get_posts();

  if (is_product_category() ) {
    $queried_object = get_queried_object();
    $term_id = $queried_object->term_id;
    $context['category'] = get_term($term_id, 'product_cat');
    $context['title'] = single_term_title('', false);
  }

  Timber::render('woo/shop.twig', $context);
}
