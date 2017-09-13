<?php

$context = Timber::get_context();
// $context['sidebar'] = Timber::get_widgets('shop-sidebar');

// SINGLE PRODUCT
if (is_singular('product') ) {
  $post = Timber::get_post();
  $context['post'] = $post;

  $product = get_product($context['post']->id );
  $context['product'] = $product;
  $context['related_products'] = get_related_products($product, 3);

  Timber::render('woo/single.twig', $context);
}

// CATEGORY or SHOP
else {
  $context['posts'] = Timber::get_posts();

  if (is_product_category() ) {
    $category = get_queried_object();
    $context['title'] = $category->name;
    $context['content'] = wpautop($category->description);
  } else {
    $post = Timber::get_post(get_option('woocommerce_shop_page_id') );
    $context['title'] = $post->title;
    $context['content'] = $post->content;
  }

  Timber::render('woo/shop.twig', $context);
}
