<?php
/**
 * Cart Page
 */
 
global $woocommerce;
?>
<?php $woocommerce->show_messages(); ?>

<form action="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" method="post">
<?php do_action( 'woocommerce_before_cart_table' ); ?>
<table class="shop_table cart" cellspacing="0">
	<thead>
		<tr>
			<th class="product-remove">&nbsp;</th>
			<th class="product-thumbnail">&nbsp;</th>
			<th class="product-name"><span class="nobr"><?php _e('Product Name', 'woocommerce'); ?></span></th>
			<th class="product-price"><span class="nobr"><?php _e('Unit Price', 'woocommerce'); ?></span></th>
			<th class="product-quantity"><?php _e('Quantity', 'woocommerce'); ?></th>
			<th class="product-subtotal"><?php _e('Price', 'woocommerce'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>
		
		<?php
		if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
			foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
				$_product = $values['data'];
				if ( $_product->exists() && $values['quantity'] > 0 ) {
					?>
					<tr>
						<!-- Remove from cart link -->
						<td class="product-remove">
							<?php 
								echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove" title="%s">&times;</a>', esc_url( $woocommerce->cart->get_remove_url( $cart_item_key ) ), __('Remove this item', 'woocommerce') ), $cart_item_key ); 
							?>
						</td>
						
						<!-- The thumbnail -->
						<td class="product-thumbnail">
							<?php 
								printf('<a href="%s">%s</a>', esc_url( get_permalink( apply_filters('woocommerce_in_cart_product_id', $values['product_id'] ) ) ), $_product->get_image() ); 
							?>
						</td>
						
						<!-- Product Name -->
						<td class="product-name">
							<?php 
								printf('<a href="%s">%s</a>', esc_url( get_permalink( apply_filters('woocommerce_in_cart_product_id', $values['product_id'] ) ) ), apply_filters('woocommerce_in_cart_product_title', $_product->get_title(), $_product) );
							
								// Meta data
								echo $woocommerce->cart->get_item_data( $values );
                   				
                   				// Backorder notification
                   				if ( $_product->backorders_require_notification() && $_product->get_total_stock() < 1 ) 
                   					echo '<p class="backorder_notification">' . __('Available on backorder.', 'woocommerce') . '</p>';
							?>
						</td>
						
						<!-- Product price -->
						<td class="product-price">
							<?php 							
								$product_price = ( get_option('woocommerce_display_cart_prices_excluding_tax') == 'yes' ) ? $_product->get_price_excluding_tax() : $_product->get_price();
							
								echo apply_filters('woocommerce_cart_item_price_html', woocommerce_price( $product_price ), $values, $cart_item_key ); 
							?>
						</td>
						
						<!-- Quantity inputs -->
						<td class="product-quantity">
							<?php 
								if ( $_product->is_sold_individually() ) {
									$product_quantity = '1';
								} else {
									$data_min = apply_filters( 'woocommerce_cart_item_data_min', '', $_product );
									$data_max = ( $_product->backorders_allowed() ) ? '' : $_product->get_stock_quantity();
									$data_max = apply_filters( 'woocommerce_cart_item_data_max', $data_max, $_product ); 
									
									$product_quantity = sprintf( '<div class="quantity"><input name="cart[%s][qty]" data-min="%s" data-max="%s" value="%s" size="4" title="Qty" class="input-text qty text" maxlength="12" /></div>', $cart_item_key, $data_min, $data_max, esc_attr( $values['quantity'] ) );
								}
								
								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key ); 					
							?>
						</td>
						
						<!-- Product subtotal -->
						<td class="product-subtotal">
							<?php 
								echo $woocommerce->cart->get_product_subtotal( $_product, $values['quantity'] ); 
							?>
						</td>
					</tr>
					<?php
				}
			}
		}
		
		do_action( 'woocommerce_cart_contents' );
		?>
		<tr>
			<td colspan="6" class="actions">

				<?php if ( get_option( 'woocommerce_enable_coupons' ) == 'yes' ) { ?>
					<div class="coupon">
					
						<label for="coupon_code"><?php _e('Coupon', 'woocommerce'); ?>:</label> <input name="coupon_code" class="input-text" id="coupon_code" value="" /> <input type="submit" class="button" name="apply_coupon" value="<?php _e('Apply Coupon', 'woocommerce'); ?>" />
						
						<?php do_action('woocommerce_cart_coupon'); ?>
						
					</div>
				<?php } ?>

				<?php $woocommerce->nonce_field('cart') ?>
				<input type="submit" class="button" name="update_cart" value="<?php _e('Update Cart', 'woocommerce'); ?>" /> <a href="<?php echo esc_url( $woocommerce->cart->get_checkout_url() ); ?>" class="checkout-button button alt"><?php _e('Proceed to Purchase', 'woocommerce'); ?></a>
				<?php do_action('woocommerce_proceed_to_checkout'); ?>
			</td>
		</tr>
		
		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	</tbody>
</table>
<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>
<div class="cart-collaterals">
	
	<?php do_action('woocommerce_cart_collaterals'); ?>
	
	<?php woocommerce_cart_totals(); ?>
	
	<?php woocommerce_shipping_calculator(); ?>
	
</div>
<script>	
	var temp = 'Shopping Bag / <strong>Order Summary</strong>';
	jQuery(".breadcrumbs").removeClass('breadcrumbs').addClass('checkout_breadcrumbs').html(temp)
</script>