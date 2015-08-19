<?php
/**
 * Tevolution single custom post type template
 *
**/
get_header(); /* Header Portation */
do_action('templ_before_container_breadcrumb'); /*do action for display the bradcrumb in between header and container. */
?>
<!-- start content part-->
<div id="content" role="main" class="large-9 small-12 columns">	
	<?php do_action('templ_inside_container_breadcrumb'); /*do action for display the bradcrumn  inside the container. */ ?>
	<?php while ( have_posts() ) : the_post(); ?>
     	<div>  
          	<!--start post type title -->
     		<?php do_action('templ_before_post_title');         /* do action for before the post title.*/ ?>
		   
               <?php do_action('templ_post_title');                /* do action for display the single post title */
					
					do_action('tevolution_display_rating',get_the_ID());
				 
					do_action('templ_after_post_title');          /* do action for after the post title.*/?>
     		<!--end post type title -->
			<?php do_action('templ_post_info');                 /*do action for display the post info */ ?>
			<!--Code start for single captcha -->   
            <?php 
			  $tmpdata = get_option('templatic_settings');
			  $display = (isset($tmpdata['user_verification_page']))?$tmpdata['user_verification_page']:array();
			  $captcha_set = array();
			  $captcha_dis = '';
			  if(count($display) > 0){
				  foreach($display as $_display){
					  if($_display == 'claim' || $_display == 'emaitofrd' || $_display == 'sendinquiry'){ 						 
						 $captcha_dis = $_display;
						 break;
					   }
				   }
			   }
			    $recaptcha = get_option("recaptcha_options");
			   global $current_user;
			 ?>
               
            <div id="myrecap" style="display:none;"><?php if($recaptcha['show_in_comments']!= 1 || $current_user->ID != ''){ templ_captcha_integrate($captcha_dis); }?></div> 
            <input type="hidden" id="owner_frm" name="owner_frm" value=""  />
            <div id="claim_ship"></div>
          
            
            <!--Code end for single captcha -->
            
			<!--Start Post Image -->
			<?php do_action('templ_before_post_image');         /* do action for before the post title.*/ 
			
			do_action('templ_post_single_image');         /* do action for display the single post title */
			
				do_action('templ_after_post_image');          /* do action for after the post title.*/?>
			<!--End  Post Image -->           


			<!--Start Post Content -->
			<?php do_action('templ_before_post_content');       /* do action for before the post content. */ ?> 

			<div itemprop="description" class="entry-content">
			<?php do_action('templ_post_single_content');       /*do action for single post content */	?>
			</div><!-- end .entry-content -->

			<?php do_action('templ_after_post_content');        /* do action for after the post content. */?>
			<!-- End Post Content -->

     		<!--Custom field collection do action -->
     		<?php do_action('tmpl_detail_page_custom_fields_collection');  ?>
     		</div>
	<?php endwhile; /* end of the loop.  */
	
	wp_reset_query(); 
	
	do_action('tmpl_single_post_pagination'); /* add action for display the next previous pagination */ 
	
	do_action('tmpl_before_comments'); /* add action for display before the post comments. */
	
	do_action( 'after_entry' );
	
	do_action( 'for_comments' );
	
	do_action('tmpl_after_comments'); /*Add action for display after the post comments. */
	
	add_action('wp_footer','tmpl_load_script_infooter',200);
	global $post;
	$tmpdata = get_option('templatic_settings');
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php') ){
	if((!empty($tmpdata['related_post_type']) && in_array($post->post_type,$tmpdata['related_post_type'])))
	{
		do_action('tmpl_related_post'); /*add action for display the related post list. */
	}
	}else
	{
	do_action('tmpl_related_post'); /*add action for display the related post list. */
	}?>
</div><!-- #content -->
<!--single post type sidebar -->
<?php if ( is_active_sidebar( get_post_type().'_detail_sidebar' ) ) : ?>
	<aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
		<?php dynamic_sidebar( get_post_type().'_detail_sidebar' ); ?>		
	</aside>
	<?php
elseif ( is_active_sidebar( 'primary-sidebar') ) : ?>
	<aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
		<?php dynamic_sidebar('primary-sidebar'); ?>
	</aside>
<?php endif; ?>
<!--end single post type sidebar -->
<!-- end  content part-->
<?php get_footer(); 

/* Load script in footer */
function tmpl_load_script_infooter(){ ?>
	<script type="text/javascript" async >
	var RECAPTCHA_COMMENT = 0;
	<?php
		  $recaptcha = get_option("recaptcha_options");
		if($recaptcha['show_in_comments']!= 1 || $current_user->ID != ''){ ?>
			jQuery('#owner_frm').val(jQuery('#myrecap').html());
	<?php 	} else{ ?> RECAPTCHA_COMMENT = <?php echo $recaptcha['show_in_comments']; ?>; <?php } ?>
	</script><?php
}
?>