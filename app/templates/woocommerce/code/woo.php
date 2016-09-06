<?php
// Your custom WooCommerce code here

/*
  Set the global scope of Product
*/
function timber_set_product($post) {
  global $product;
  $product = isset($post->product) ? $post->product : get_product($post->ID);
}

remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
