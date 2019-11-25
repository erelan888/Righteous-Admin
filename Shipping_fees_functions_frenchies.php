<?php
// Add additional fees based on shipping class
function woocommerce_fee_based_on_shipping_class() {

  global $woocommerce;
  // Setup an array of shipping classes which correspond to those created in Woocommerce
  $shippingclass_mug_array = array( 'mug' );
  // then we loop through the cart, checking the shipping classes

	foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
      $shipping_class = get_the_terms( $values['product_id'], 'product_shipping_class' );
      
        if ( isset( $shipping_class[0]->slug ) && in_array( $shipping_class[0]->slug, $shippingclass_mug_array ) ) {
              $woocommerce->cart->add_fee( __('Special Packing Fee', 'woocommerce'), 6 ); 
        }
	}
}
add_action( 'woocommerce_cart_calculate_fees', 'woocommerce_fee_based_on_shipping_class' ); 