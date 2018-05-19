<?php
$context = Timber::get_context();

// SINGLE PRODUCT
if( is_singular( 'product' ) ) {
  $context['post'] = Timber::get_post();

  // get detailed product info
  $product = wc_get_product( $context['post']->id );
  $context['product'] = $product;

  // get related products
  $related_limit = wc_get_loop_prop( 'columns' );
  $related_ids = wc_get_related_products( $context['post']->id, $related_limit );
  $context['related_products'] =  Timber::get_posts(  $related_ids );

  Timber::render( 'woo/single.twig', $context );
}

// if SHOP or CATEGORY page
else {
  $display_mode = woocommerce_get_loop_display_mode();
  $parent_term_id = 0;

  // if CATEGORY page
  if( is_product_category() || is_product_tag() ) {
    $term_raw = get_queried_object();
    $term = new TimberTerm( $term_raw->term_id );
    $parent_term_id = $term_raw->term_id;

    $context['title'] = $term->name;
    $context['content'] = wpautop( $term->description );
  }
  // if SHOP page
  else {
    $post = Timber::get_post( get_option( 'woocommerce_shop_page_id' ) );

    $context['title'] = $post->title;
    $context['content'] = $post->content;
  }


  // if display products
  if( $display_mode === 'both' || $display_mode === 'products' ) {
    $posts = Timber::get_posts();
    $context['products'] =  MyShop::get_products( $posts );
  }

  // if display categories
  if( $display_mode === 'both' || $display_mode === 'subcategories' ) {
    $context['categories'] = MyShop::get_category_thumbnails( $parent_term_id );
  }

  // disable pagination
  if( $display_mode === 'subcategories' ) {
    wc_set_loop_prop( 'total', 0 );
  }

  Timber::render( 'woo/shop.twig', $context );
}
