<?php
// Your custom WooCommerce code here

/*
  Set the global scope of Product
*/
function timber_set_product($post) {
  global $product;
  $product = isset($post->product) ? $post->product : get_product($post->ID);
}

new My_Product();
new My_Order();

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

    add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'add_to_cart_text') ); // change add-to-cart button


    // SHOP
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail'); // remove image from shop view
  }

  function add_to_cart_text() {
    return __('Buy Now', 'my');
  }
}


/*
  Controller for Cart, Checkout, and Order-Received page
*/
class My_Order {

  function __construct() {
    // CHECKOUT
    add_action('woocommerce_checkout_after_customer_details', array($this, 'checkout_after_customer_details') ); // wrap the form
    add_action('woocommerce_checkout_after_order_review', array($this, 'checkout_after_order_review') ); // close the form wrapper

    // CART
    add_filter('woocommerce_add_success', array($this, 'remove_add_success') ); // disable 'remove item from cart' alert
    add_filter('wc_add_to_cart_message', array($this, 'added_to_cart_message'), null, 2); // change 'add item to cart' alert message

    // ORDER
    add_action('woocommerce_order_status_completed_notification', array($this, 'send_invoice_after_order') ); // Send invoice to customer automatically
  }

  /*
    Add wrapper to Checkout Order details so we can float it properly
  */
  function checkout_after_customer_details() {
    echo '<section id="order_details">';
  }
  function checkout_after_order_review() {
    echo '</section>';
  }

  /*
    Remove the alert when removing item from cart

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

    @param $message (str) - The default message
    @param $product_id (int) - The product that's just added to cart

    @return str - Modified message
  */
  function added_to_cart_message($message, $product_id) {
    $real_message = preg_replace('/<a\D+a>/', '', $message); // without <a> tag
    $button = sprintf('<a href="%s" class="button wc-forward">%s</a> ', esc_url(wc_get_page_permalink('checkout')), esc_html__('Continue Payment', 'my') );

    return $button . $real_message;
  }

  /*
    Send invoice to customer automatically after order

    @param $order_id (int) - The new order that's just created
  */
  function send_invoice_after_order($order_id) {
    $email = new WC_Email_Customer_Invoice();
    $email->trigger($order_id);
  }
}
