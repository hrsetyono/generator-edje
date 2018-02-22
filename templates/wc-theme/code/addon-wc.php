<?php
/*
  Set the global scope of Product
*/
function timber_set_product( $post ) {
  global $product;
  $product = isset( $post->product ) ? $post->product : wc_get_product( $post->ID );
}


/*
  Functions for SHOP listing page
*/
class MyShop {
  function __construct() {
    // remove default image in product thumb
    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );

    // remove breadcrumb
    add_filter( 'woocommerce_get_breadcrumb', '__return_false' );

    // change the amount of products per page
    add_filter( 'loop_shop_per_page', array($this, 'change_products_per_page') );

    // replace default pagination
    remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
    add_action( 'woocommerce_after_shop_loop', array($this, 'custom_woocommerce_pagination'), 10 );
  }

  /*
    Change Products-per-page
    @filter loop_shop_per_page

    @param int $num - Current number of products per page
  */
  function change_products_per_page( $num ) {
    return get_option( 'posts_per_page' );
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
}

/*
  Controller for Catalog, Single Product, and Shop page
*/
class MyProduct {

  function __construct() {
    // SINGLE PRODUCT
    // Separate some actions from one call
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

    add_action( 'my_output_related_products', 'woocommerce_output_related_products' );
    add_action( 'my_template_single_buy', 'woocommerce_template_single_price', 5 );
    add_action( 'my_template_single_buy', 'woocommerce_template_single_add_to_cart', 10 );

    // TEASE
    add_filter( 'woocommerce_product_single_add_to_cart_text', array($this, 'add_to_cart_text') );
  }

  /*
    Change "Add to Cart" button
    @filter woocommerce_product_single_add_to_cart_text
  */
  function add_to_cart_text() {
    return __( 'Buy Now', 'my' );
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
  Controller for Cart actions and pages
*/
class MyCart {
  function __construct() {
    // remove success message
    add_filter( 'woocommerce_add_success', array( $this, 'remove_add_success' ) );
    add_filter( 'wc_add_to_cart_message_html', array( $this, 'added_to_cart_message' ), null, 2 );

    // replace default cross-sell
    remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
    add_action( 'woocommerce_cart_collaterals', array($this, 'custom_cross_sell_display') );
  }

  /*
    Remove the alert when removing item from cart
    @filter woocommerce_add_success

    @param $message (str) - Default message
    @return str - Modified message
  */
  function remove_add_success( $message ) {
    if( strpos( $message, 'Undo' ) ) {
      return false;
    }

    return $message;
  }

  /*
    Change the alert message after adding to cart
    @filter wc_add_to_cart_message

    @param $message (str) - The default message
    @param $product_id (int) - The product that's just added to cart
    @return str - Modified message
  */
  function added_to_cart_message( $message, $product_id ) {
    $real_message = preg_replace( '/<a\D+a>/', '', $message ); // without <a> tag
    $button = sprintf(
      '<a href="%s" class="button wc-forward">%s</a> ',
      esc_url( wc_get_page_permalink('checkout') ), esc_html__( 'Continue Payment', 'my' )
    );

    return $button . $real_message;
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

/*
  Controller for Cart, Checkout, and Order-Received page
*/
class MyCheckout {
  function __construct() {

  }
}
