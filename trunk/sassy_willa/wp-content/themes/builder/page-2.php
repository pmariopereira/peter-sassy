<?php get_header(); ?>
	
	<div id="single-page-title">
		<div class="wrap_940">
		<h1><?php single_post_title(); ?></h1>
		</div>
	</div>
	
	<!-- MAIN SECTION -->
	<div id="main-inner-site">
	<?php do_action('wip_before_content'); ?>
		
	<?php 
			$args = array( 'taxonomy' => 'product_tag' );

			$terms = get_terms('product_tag', $args);

			$count = count($terms); $i=0;
			echo $count;
			if ($count > 0) {
				$term_list = '<p class="my_term-archive">';
				foreach ($terms as $term) {
					$i++;
					$term_list .= '<a href="/term-base/' . $term->slug . '" title="' . sprintf(__('View all post filed under %s', 'my_localization_domain'), $term->name) . '">' . $term->name . '</a>';
					if ($count != $i) $term_list .= ' &middot; '; else $term_list .= '</p>';
				}
				echo $term_list;
			}
	?>
		
	</div>
	<!-- END MAIN SECTION -->
	
<?php get_footer(); ?>