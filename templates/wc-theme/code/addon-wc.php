<?php
/*
  Set the global scope of Product
*/
function timber_set_product( $post ) {
  global $product;
  $product = isset( $post->product ) ? $post->product : wc_get_product( $post->ID );
}


/*
  Functions for SHOP or CATALOG page
*/
class MyShop {
  function __construct() {
    // disable woocommerce CSS
    add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

    // remove breadcrumb
    add_filter( 'woocommerce_get_breadcrumb', '__return_false' );

    // remove default image in product thumb
    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );

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

  /////

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
    // Move UPSELL and RELATED products to bottom
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

    // Move DESCRIPTION to center panel
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs' );

    // move TITLE and SHARING to top
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
    add_action( 'woocommerce_before_single_product', 'woocommerce_template_single_title' );
    add_action( 'woocommerce_before_single_product', 'woocommerce_template_single_sharing' );

    // move the PRICE and VARIATION to right-sidebar
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    add_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_price' );
    add_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_add_to_cart' );
  }

  /////
}


/*
  Functions for CART actions and pages
*/
class MyCart {
  function __construct() {
    // remove alert when removing item from cart
    add_filter( 'woocommerce_add_success', array($this, 'disable_alert_remove_cart') );


    // Add close button in message
    add_filter( 'woocommerce_add_to_cart_validation', array($this, 'add_close_button_in_message') );
    add_filter( 'wc_add_to_cart_message_html', array($this, 'add_close_button_in_message') );
    add_filter( 'woocommerce_add_error', array($this, 'add_close_button_in_message') );

    // change the button in alert to go straight to checkout
    add_filter( 'wc_add_to_cart_message_html', array($this, 'added_to_cart_message'), null, 2 );

    // Cart navigation widget
    add_filter( 'woocommerce_add_to_cart_fragments', array($this, 'update_cart_widget_fragment') );

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
  function disable_alert_remove_cart( $message ) {
    if( strpos( $message, 'Undo' ) ) {
      return false;
    }

    return $message;
  }

  /*
    Add X button to dismiss the message

    @filter woocommerce_add_to_cart_validation
    @filter wc_add_to_cart_message
  */
  function add_close_button_in_message( $message ) {
    $button = '<span class="woocommerce-message-close">×</span>';
    return $button . $message;
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
