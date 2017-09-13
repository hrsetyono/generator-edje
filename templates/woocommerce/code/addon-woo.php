<?php
// Your custom WooCommerce code here

new My_Product();
new My_Cart();
new My_Checkout();

/*
  Set the global scope of Product
*/
function timber_set_product($post) {
  global $product;
  $product = isset($post->product) ? $post->product : wc_get_product($post->ID);
}

/*
  Controller for Catalog, Single Product, and Shop page
*/
class My_Product {

  function __construct() {
    // SINGLE PRODUCT
    // Separate some actions from one call
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

    add_action('my_output_related_products', 'woocommerce_output_related_products');
    add_action('my_template_single_buy', 'woocommerce_template_single_price', 5);
    add_action('my_template_single_buy', 'woocommerce_template_single_add_to_cart', 10);

    // TEASE
    add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'add_to_cart_text') );

    // SHOP
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
  }

  /*
    Change "Add to Cart" button
    @filter woocommerce_product_single_add_to_cart_text
  */
  function add_to_cart_text() {
    return __('Buy Now', 'my');
  }
}


class My_Cart {
  function __construct() {
    // remove success message
    add_filter('woocommerce_add_success', array($this, 'remove_add_success') );
    add_filter('wc_add_to_cart_message', array($this, 'added_to_cart_message'), null, 2);
  }

  /*
    Remove the alert when removing item from cart
    @filter woocommerce_add_success

    @param $message (str) - Default message
    @return str - Modified message
  */
  function remove_add_success($message) {
    if(strpos($message, 'Undo') ) {
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
  function added_to_cart_message($message, $product_id) {
    $real_message = preg_replace('/<a\D+a>/', '', $message); // without <a> tag
    $button = sprintf('<a href="%s" class="button wc-forward">%s</a> ', esc_url(wc_get_page_permalink('checkout')), esc_html__('Continue Payment', 'my') );

    return $button . $real_message;
  }

}

/*
  Controller for Cart, Checkout, and Order-Received page
*/
class My_Checkout {

  function __construct() {

  }
}
