<?php

$context = Timber::get_context();

// SINGLE PRODUCT
if( is_singular( 'product' ) ) {
  $post = Timber::get_post();
  $context['post'] = $post;

  $product = wc_get_product( $context['post']->id );
  $context['product'] = $product;

  Timber::render( 'woo/single.twig', $context );
}

// CATEGORY or SHOP
else {
  $posts = Timber::get_posts();
  $context['posts'] = MyProduct::get_products( $posts );

  // if category / tag page
  if( is_product_category() || is_product_tag() ) {
    $term_raw = get_queried_object();
    $term = new TimberTerm( $term_raw->term_id );

    $context['title'] = $term->name;
    $context['content'] = wpautop( $term->description );
  }
  // if shop page
  else {
    $post = Timber::get_post( get_option( 'woocommerce_shop_page_id' ) );
    $context['title'] = $post->title;
    $context['content'] = $post->content;
  }

  Timber::render( 'woo/shop.twig', $context );
}
