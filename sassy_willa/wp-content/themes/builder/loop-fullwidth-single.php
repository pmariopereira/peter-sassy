<?php
/**
 * Template part for single blog (fullwidth layout),
 * @since version 2.0
 * @author webinpixels
 * @package The_Builder
 */
global $post;
?>

<div class="wrap_940">

<?php
if( have_posts()):
while (have_posts()): the_post();
?>
	<div class="single-blog-entry">

	<h1 class="single-blog-title"><?php the_title(); ?></h1>
	<span class="meta-blog-lists"><?php print __('By', 'wip'); ?> <?php the_author_posts_link(); ?>
				&ndash;
				<?php printf( __('On %1$s', 'wip'), get_the_time('F d, Y', $post->ID) ); ?>
				&ndash;
				<?php print __('In', 'wip'); ?> <?php the_category(', '); ?>
				<?php print __('With', 'wip'); ?> <?php comments_popup_link( __('No comment', 'wip'), __('1 Comment', 'wip'), __('% Comments', 'wip') ); ?>
				</span>
	
	<?php
	#if user set the featured images for this post!
	if( has_post_thumbnail() && wip_get_attached_file($post->ID) ){
		echo '<div class="single-full-blog-thumbnail">' . "\n";
		echo '<img src="'. wip_print_autoresize( wip_get_attached_file($post->ID), get_thumbOri($post->ID, 'full'), 940, 9999, false ) .'" alt="'. the_title_attribute('echo=0') .'"/>';
		echo '</div>' . "\n";
	}
	?>
	
	<div class="single-blog-entry-content">
	
	<?php the_content(); ?>

		<div class="clear"></div>
	</div>

	<?php 
		if( get_option('bd_blog_related_off') !== '0' ) get_template_part('blog', 'fullwidth-related'); 
	?>


	<?php comments_template( '', true ); ?>

	</div>

<?php
endwhile;
endif;
wp_reset_query();
?>
</div><!-- end .wrap_940 -->