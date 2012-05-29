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
			//$args_post_tag = array( 'taxonomy' => 'post_tag' );
			
			//$terms_post_tag = get_terms('post_tag', $args_post_tag);
			
			$args_product_tag = array( 'taxonomy' => 'product_tag' );

			$terms_product_tag = get_terms('product_tag', $args_product_tag);			
			$count = count($terms); $i=0;
			$characterTags = array('0-9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
			
			$arrayTags = array();
			
			foreach($terms_product_tag as $term)
			{				
				foreach($characterTags as $key=>$value)
				{					
					$name = strtoupper(substr($term->name,0,1));
					if($name >'0' && $name <'9'){
						$arrayTags[strtoupper('0-9')][] = $term;
						break;					
					}
					if(strtoupper($value) == $name)
					{
						$arrayTags[strtoupper($value)][] = $term;
						break;
					}
				}
			}									
	?>
		<div id="heading_tag">
			GO TO
			<?php foreach($characterTags as $character): ?>				
				<a href="#focus_tag_<?php echo $character; ?>"><?php echo $character; ?></a>
			<?php endforeach; ?>
		</div>
		<div id="content_tag">
			<?php $i = 0; ?>
			<?php foreach($characterTags as $key=>$value): ?>
				
				<?php $i++;?>
				<?php if($i == 1): ?>
				<div class="items first"> 
				<?php elseif($i == 4): $i = 0;?>			
				<div class="items fast"> 
				<?php else: ?>
				<div class="items"> 
				<?php endif; ?>
					<h3><a href="#" id="focus_tag_<?php echo $value; ?>"><?php echo $value; ?></a></h3>					
				<?php if(count($arrayTags[$value]) != 0): ?>
					<div class="sub_items"> 
					<?php foreach($arrayTags[$value] as $obj): ?>				
						<a href="<?php echo get_home_url(); ?>/shop/product-tag/<?php echo $obj->slug; ?>"><?php echo $obj->name; ?></a> <br>
					<?php endforeach; ?>
					</div>
				<?php endif; ?>
				</div> 
			<?php endforeach; ?>
		</div>
		
	</div>
	<!-- END MAIN SECTION -->
	<div class="clear"></div>
<?php get_footer(); ?>

<script>
	jQuery("a[id^='focus_tag_']").click(function(){	
		var p = jQuery(this).parent().next();		
		//jQuery(p).slideUp("slow");
		if(!jQuery(p).is(":hidden"))
			jQuery(p).slideUp("slow");	
		else
			jQuery(p).slideDown("slow");
		return false;
	});	
</script>