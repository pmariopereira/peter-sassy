<?php
/**
 * Single Product Image
 */

global $post, $woocommerce;

?>
<div class="images">

	<?php if ( has_post_thumbnail() ) : ?>

		<a itemprop="image" href="<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>" class="zoom" rel="thumbnails" title="<?php echo get_the_title( get_post_thumbnail_id() ); ?>"><?php echo get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) ) ?></a>

	<?php else : ?>
	
		<img src="<?php echo woocommerce_placeholder_img_src(); ?>" alt="Placeholder" />
	
	<?php endif; ?>

	<?php do_action('woocommerce_product_thumbnails'); ?>
	
	<?php global $post, $woocommerce; ?>
		<div class="panel" id="tab-gallery">		
		<?php

			$thumb_id = get_post_thumbnail_id();
			$small_thumbnail_size = apply_filters('single_product_small_thumbnail_size', 'shop_thumbnail');
			$args = array(
				'post_type' 	=> 'attachment',
				'numberposts' 	=> 3,
				'post_status' 	=> null,
				'post_parent' 	=> $post->ID,
				'post__not_in'	=> array($thumb_id),
				'post_mime_type'=> 'image',
				'orderby'		=> 'menu_order',
				'order'			=> 'ASC'
			);
			$attachments = get_posts($args);

			if( $attachments ){
			
			echo '<div id="foo" class="product-gallery-thumbnail">' . "\n";			
				foreach ( $attachments as $attachment ) :					
					if (get_post_meta($attachment->ID, '_woocommerce_exclude_image', true)==1) continue;
					$_post = & get_post( $attachment->ID );
					$url = wp_get_attachment_url($_post->ID);
					$path = get_attached_file($_post->ID);
					$post_title = esc_attr($_post->post_title);
					
					if( file_exists($path) ){
						$image = wip_resize($path, $url, 80, 80, true);						
						echo '<a href="'.$url.'" title="'.$post_title.'" rel="thumbnails" class="zoom" style="margin-left:15px">';
						echo '<img src="'.$image['url'].'" alt="'.$post_title.'"/></a>';
					}

				endforeach;					
			echo '</div>' . "\n";				
			} else {

				print '<p class="no_gallery"><em>' . __('No Galleries', 'wip') . '</em></p>';
			}
		?>

	</div>
</div>