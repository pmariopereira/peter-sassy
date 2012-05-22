<?php get_header(); ?>
	
	<!-- MAIN SECTION -->
	<div id="main-inner-site">
	<?php do_action('wip_before_content'); ?>
	
	<?php
	global $post; 
	if( get_post_meta( $post->ID, '_bd_post_layout', true) == 'fullwidth' ){
		get_template_part('loop', 'fullwidth-single'); 
	} else {
		get_template_part('loop', 'single'); 
	}
	?>
		
	</div>
	<!-- END MAIN SECTION -->

<?php get_footer(); ?>