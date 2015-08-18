<?php
/**
 * Tevolution Category taxonomy page
 *
**/
get_header(); //Header Portation
if ( is_active_sidebar( 'after_'.get_query_var( 'taxonomy' ).'_header') ) : ?>
	<div id="listing_google_map" class="listing_google_map">
		<div class="map_sidebar">
		<div class="top_banner_section_in clearfix">
		<?php dynamic_sidebar('after_'.get_query_var( 'taxonomy' ).'_header'); ?>
		</div>
		</div>
	</div>     
<?php endif;
do_action('templ_before_container_breadcrumb'); /*do action for display the bradcrumb in between header and container. */ ?>
	
<div id="content" class="contentarea">
	<?php do_action('templ_inside_container_breadcrumb'); /*do action for display the bradcrumn  inside the container. */ ?>
	<?php do_action('templ_before_categories_title');//do action for display before categories title?>
     <h1 class="loop-title"><?php single_cat_title(); ?></h1>
	<?php do_action('templ_after_categories_title');// do action for display after categories title 
    do_action('templ_before_categories_description');// do action for display after categories title 
    if ( category_description() ) : // Show an optional category description ?>
          <div class="archive-meta"><?php echo category_description(); ?></div>
    <?php endif; 
	do_action('tevolution_before_subcategory');
     
     do_action('tevolution_subcategory');
     
    do_action('tevolution_after_subcategory');
	
    do_action('templ_after_categories_description');// do action for display after categories title
	
	?>
     <!--Start loop taxonomy page-->
     <div id="loop_taxonomy" class="indexlist">
     	<?php if (have_posts()) : 
				while (have_posts()) : the_post(); ?>	
                    	<div class="post <?php templ_post_class();?> clearfix">  
                         	<?php do_action('tmpl_before_category_page_image');           /*do_action before the post image */?>
						<?php do_action('tmpl_category_page_image');?>  
						<?php do_action('tmpl_after_category_page_image');           /*do action after the post image */?> 
                         	<div class="entry"> 
                                   <!--start post type title -->
                                   <?php do_action('templ_before_post_title');         /* do action for before the post title.*/
                                   do_action('templ_post_title');                /* do action for display the single post title */
                                   do_action('templ_after_post_title');          /* do action for after the post title.*/ ?>
                                   <!--end post type title -->
                                   <?php do_action('templ_post_info');                 /*do action for display the post info */ ?>     
                                   <!--Start Post Content -->
                                   <?php do_action('templ_before_post_content');       /* do action for before the post content. */  
                                   do_action('templ_taxonomy_content');	
                                   do_action('templ_after_post_content');        /* do action for after the post content. */?>
                                   <!-- End Post Content -->
                                   <!-- Show custom fields where show on listing = yes -->
                                   <?php do_action('templ_listing_custom_field',$htmlvar_name,$pos_title);/*add action for display the listing page custom field */
                                   do_action('templ_the_taxonomies');   ?> 
				   		</div>
                         </div>
          	<?php endwhile;
				wp_reset_query(); 
			else:?>
          	<p class='nodata_msg'><?php _e( 'Apologies, no results were found matching this search criteria.', DOMAIN ); ?></p>              
          <?php endif;?>
     </div>
     <div id="listpagi">
          <div class="pagination pagination-position">
                <?php if(function_exists('pagenavi_plugin')) { pagenavi_plugin(); } ?>
          </div>
     </div>
      <!--End loop taxonomy page -->
</div>
<!--taxonomy  sidebar -->
<?php if ( is_active_sidebar( get_query_var( 'taxonomy' ).'_listing_sidebar') ) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar(get_query_var( 'taxonomy' ).'_listing_sidebar'); ?>
	</div>
<?php 
elseif ( is_active_sidebar( 'primary-sidebar') ) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar('primary-sidebar'); ?>
	</div>
<?php endif; ?>
<!--end taxonomy sidebar -->
<?php get_footer(); ?>
