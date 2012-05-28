<?php
/**
 * Reviews Tab
 */
 
global $woocommerce, $post,$product;

if ($product->get_attribute("size-and-fit")) : ?>
	<div class="panel entry-content" id="tab-size-fit">
	
		<?php $heading = apply_filters('woocommerce_product_description_heading', __('Size and Fit', 'woocommerce')); ?>
		
		<h2><?php echo $heading; ?></h2>
		
		<?php 
			echo $product->get_attribute("size-and-fit");
		?>
	
	</div>
<?php endif; ?>