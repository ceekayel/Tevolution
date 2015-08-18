<?php
/**
 * Tevolution archive page
 *
**/
get_header(); //Header Portation
do_action('templ_before_container_breadcrumb'); /*do action for display the bradcrumb in between header and container. */
?>
<div id="content" class="contentarea">
	<?php do_action('templ_inside_container_breadcrumb'); /*do action for display the bradcrumn  inside the container. */ ?>
	<?php do_action('templ_before_archive_title');//do action for display before categories title?>              
     <h1 class="page-title">
		<?php echo ucfirst(apply_filters('tevolution_archive_page_title',get_post_type()));?>		
	</h1>
	<?php  do_action('templ_after_archive_title');// do action for display after categories title ?>
     <!--Start loop archive page-->    
     <div id="loop_archive" class="indexlist">
     	<?php if (have_posts()) : 
				while (have_posts()) : the_post(); ?>	
                    	<div class="post <?php templ_post_class();?>">  
                         	<?php do_action('tmpl_before_archive_page_image');           /*do_action before the post image */?>
						<?php do_action('tmpl_archive_page_image');?>  
						<?php do_action('tmpl_after_archive_page_image');           /*do action after the post image */?> 
                         	<div class="entry"> 
                                   <!--start post type title -->
                                   <?php do_action('templ_before_post_title');         /* do action for before the post title.*/ ?>
                                   <?php do_action('templ_post_title');                /* do action for display the single post title */?>
                                   <?php do_action('templ_after_post_title');          /* do action for after the post title.*/?>
                                   <!--end post type title -->
                                   <?php do_action('templ_post_info');                 /*do action for display the post info */ ?>     
                                   <!--Start Post Content -->
                                   <?php do_action('templ_before_post_content');       /* do action for before the post content. */ ?> 
                                  <?php
						    $tmpdata = get_option('templatic_settings');
						    if($tmpdata['listing_hide_excerpt']=='' || !in_array(get_post_type(),$tmpdata['listing_hide_excerpt'])){
								if(function_exists('supreme_prefix')){
									$theme_settings = get_option(supreme_prefix()."_theme_settings");
								}else{
									$theme_settings = get_option("supreme_theme_settings");
								}
								if($theme_settings['supreme_archive_display_excerpt']){
									echo '<div itemprop="description" class="entry-summary">';
									the_excerpt();
									echo '</div>';
								}else{
									echo '<div itemprop="description" class="entry-content">';
									the_content(); 
									echo '</div>';
								}
						    }
							?>
                                   <?php do_action('templ_after_post_content');        /* do action for after the post content. */?>
                                   <!-- End Post Content -->
                                   <!-- Show custom fields where show on listing = yes -->
                                   <?php do_action('templ_listing_custom_field',$htmlvar_name,$pos_title);/*add action for display the listing page custom field */?>
                                   <?php the_taxonomies(array('before'=>'<p class="bottom_line"><span class="i_category">','sep'=>'</span>&nbsp;&nbsp;<span class="i_tag">','after'=>'</span></p>'));?> 
				   		</div>
                         </div>
          	<?php endwhile; ?>
               	 <div id="listpagi">
                         <div class="pagination pagination-position">
                               <?php if(function_exists('pagenavi_plugin')) { pagenavi_plugin(); } ?>
                         </div>
                    </div>
			<?php wp_reset_query();
			else:?>
          	<p class='nodata_msg'><?php _e( 'Apologies, no results were found matching this search criteria.', DOMAIN ); ?></p>              
          <?php endif;?>
     </div>
      <!--End loop archive page -->
</div>
<!--archive sidebar -->
<?php if ( is_active_sidebar( 'primary' )) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar( 'primary' ); ?>		
	</div>
<?php elseif ( is_active_sidebar( 'primary-sidebar') ) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar('primary-sidebar'); ?>
	</div>
<?php endif; ?>
<!--archive sidebar -->
<?php get_footer(); ?>