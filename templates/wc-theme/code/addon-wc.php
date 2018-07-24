<?php
/*
  Set the global scope of Product
*/
function timber_set_product( $post ) {
  global $product;
  $product = isset( $post->product ) ? $post->product : wc_get_product( $post->ID );
}


class MyShop {
  function construct() {
    // replace default pagination
    remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
    add_action( 'woocommerce_after_shop_loop', array($this, 'custom_woocommerce_pagination'), 10 );
  }

  /*
    Change the HTML markup of WC Pagination
    @action woocommerce_after_shop_loop

    @param array $args
  */
  function custom_woocommerce_pagination() {
    if( wc_get_loop_prop( 'total' ) <= 0 ) {
      return false;
    }

    $context = array(
      'pagination' => Timber::get_pagination()
    );
    Timber::render( '/partials/_pagination.twig', $context );
  }

  /*
    Get categories data to be displayed as Thumbnail in SHOP page

    @param int $parent_id - Parent category ID. Default is 0.
  */
  static function get_category_thumbnails( $parent_id = 0 ) {
    $raw_cats = woocommerce_get_product_subcategories( $parent_id );

    // get extra data for category
    $parsed_cats = array_map( function( $c ) {
      // get thumbnail image
      $thumb_id = get_woocommerce_term_meta( $c->term_id, 'thumbnail_id', true );
      $image = wp_get_attachment_image_src( $thumb_id, 'medium' );
      $c->image = $image ? $image[0] : wc_placeholder_img_src();

      // get permalink
      $c->link = get_term_link( $c->term_id, 'product_cat' );

      return $c;
    }, $raw_cats );

    return $parsed_cats;
  }

  /*
    Get WC_Product data from posts and embed it

    @param $posts (arr)
    @return (arr) - Posts with embedded Product data
  */
  static function get_products( $posts ) {
    $post_ids = array_reduce($posts, function( $result, $p ) {
      $result[] = $p->id;
      return $result;
    }, array() );

    $products = wc_get_products(array(
      'include' => $post_ids,
      'orderby' => 'post__in',
      'posts_per_page' => wc_get_loop_prop( 'total' )
    ) );

    $posts = array_map( function( $p, $index ) use ( $products ) {
      $p->product = $products[$index];
      return $p;
    }, $posts, array_keys( $posts ) );

    return $posts;
  }
}

/*
  Functions for SINGLE PRODUCT page
*/
class MyProduct {
  function __construct() {
    // ....
  }
}


/*
  Functions for CART actions and pages
*/
class MyCart {
  function __construct() {
    // Cart navigation widget
    add_filter( 'woocommerce_add_to_cart_fragments', array($this, 'update_cart_widget_fragment') );

    // replace default cross-sell
    remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
    add_action( 'woocommerce_cart_collaterals', array($this, 'custom_cross_sell_display') );
  }

  /*
    Update Cart Widget whenever we add new item to cart via AJAX
    @filter woocommerce_add_to_cart_fragments

    @param $fragments arr - HTML to be applied to widget
  */
  function update_cart_widget_fragment( $fragments ) {
    ob_start();
    global $woocommerce;
    $context = array( 'woo' => $woocommerce );

    Timber::render( 'woo/_cart-button.twig', $context );
    $fragments['.cart-button'] = ob_get_clean();

    return $fragments;
  }

  /*
    Custom cross sell
    @action woocommerce_cart_collaterals
  */
  function custom_cross_sell_display( ) {
    $products = Timber::get_posts( WC()->cart->get_cross_sells() );

    if( $products ) {
      $context = array(
        'products' => $products,
      );

      Timber::render( 'woo/_cart-cross-sells.twig', $context );
    }
  }

}
