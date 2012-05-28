<?php
/**
 * Reviews Tab
 */
 
global $woocommerce, $post,$product;

if ( $product->get_attribute("shipping_details")) : ?>
	<div class="panel entry-content" id="tab-shipping-details">
	
		<?php $heading = apply_filters('woocommerce_product_description_heading', __('Shipping Details', 'woocommerce')); ?>
		
		<h2><?php echo $heading; ?></h2>
		
		<?php 
			
			echo $product->get_attribute("shipping_details");
		?>
	
	</div>
<?php endif; ?>