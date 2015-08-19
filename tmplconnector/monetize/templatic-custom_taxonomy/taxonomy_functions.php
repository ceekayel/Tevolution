<?php
/*
 Return the collection for category listing page
*/
function listing_fields_collection()
{
	global $wpdb,$post;
	remove_all_actions('posts_where');
	$cus_post_type = get_post_type();
	$args = 
	array( 'post_type' => 'custom_fields',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
	   'relation' => 'AND',
		array(
			'key' => 'post_type_'.$cus_post_type.'',
			'value' => $cus_post_type,
			'compare' => '=',
			'type'=> 'text'
		),
		array(
			'key' => 'show_on_page',
			'value' =>  array('user_side','both_side'),
			'compare' => 'IN'
		),
		array(
			'key' => 'is_active',
			'value' =>  '1',
			'compare' => '='
		),
		array(
			'key' => 'show_on_listing',
			'value' =>  '1',
			'compare' => '='
		)
	),
		'meta_key' => 'sort_order',
		'orderby' => 'meta_value',
		'order' => 'ASC'
	);
	$post_query = null;
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$post_query = new WP_Query($args);
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	return $post_query;
}
/* EOF */

/* This function wil return the custom fields of the post detail page */
function details_field_collection()
{
	global $wpdb,$post,$htmlvar_name;
	remove_all_actions('posts_where');
	remove_all_actions('posts_orderby');
	$cus_post_type = get_post_type();
	$args = 
	array( 'post_type' => 'custom_fields',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
	   'relation' => 'AND',
		array(
			'key' => 'post_type_'.$cus_post_type.'',
			'value' => $cus_post_type,
			'compare' => '=',
			'type'=> 'text'
		),
		array(
			'key' => 'show_on_page',
			'value' =>  array('user_side','both_side'),
			'compare' => 'IN'
		),
		array(
			'key' => 'is_active',
			'value' =>  '1',
			'compare' => '='
		),
		array(
			'key' => 'show_on_detail',
			'value' =>  '1',
			'compare' => '='
		)
	),
		'meta_key' => 'sort_order',
		'orderby' => 'meta_value_num',
		'meta_value_num'=>'sort_order',
		'order' => 'ASC'
	);
	$post_meta_info = null;
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$post_meta_info = new WP_Query($args);
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	return $post_meta_info;
}
/* EOF */

add_action('tmpl_detail_page_custom_fields_collection','detail_fields_colletion');
/*
	Return the collection for detail/single page
*/
function detail_fields_colletion()
{
	global $wpdb,$post,$detail_post_type;
	$detail_post_type = $post->post_type;
	if(isset($_REQUEST['pid']) && $_REQUEST['pid'])
	{
		$cus_post_type = get_post_type($_REQUEST['pid']);
		$PostTypeObject = get_post_type_object($cus_post_type);
		$PostTypeLabelName = $PostTypeObject->labels->name;
		$single_pos_id = $_REQUEST['pid'];
	}
	else
	{	
		$cus_post_type = get_post_type($post->ID);
		$PostTypeObject = get_post_type_object($cus_post_type);
		$PostTypeLabelName = $PostTypeObject->labels->name;
		$single_pos_id = $post->ID;
	}
	$heading_type = fetch_heading_per_post_type($cus_post_type);
	remove_all_actions('posts_where');
	$post_query = null;
	if(count($heading_type) > 0)
	  { 
		foreach($heading_type as $_heading_type)
		 {
			$args = 
			array( 'post_type' => 'custom_fields',
			'posts_per_page' => -1	,
			'post_status' => array('publish'),
			'meta_query' => array(
			   'relation' => 'AND',
				array(
					'key' => 'post_type_'.$cus_post_type.'',
					'value' => $cus_post_type,
					'compare' => '=',
					'type'=> 'text'
				),
				array(
					'key' => 'show_on_page',
					'value' =>  array('admin_side','user_side','both_side'),
					'compare' => 'IN'
				),
				array(
					'key' => 'is_active',
					'value' =>  '1',
					'compare' => '='
				),
				array(
					'key' => 'heading_type',
					'value' =>  $_heading_type,
					'compare' => '='
				),
				array(
					'key' => 'show_on_detail',
					'value' =>  '1',
					'compare' => '='
				)
			),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value_num',
				'meta_value_num'=>'sort_order',
				'order' => 'ASC'				
			);
		$post_query = new WP_Query($args);
		$post_meta_info = $post_query;
		$suc_post = get_post($single_pos_id);
		
				if($post_meta_info->have_posts())
				  {
					echo "<div class='grid02 rc_rightcol clearfix'>";
					echo "<ul class='list'>";					
					  $i=0;
					while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
						$field_type = get_post_meta($post->ID,"ctype",true);
						$style_class = get_post_meta($post->ID,"style_class",true);
						if($i==0)
						{
							if($post->post_name!='post_excerpt' && $post->post_name!='post_content' && $post->post_name!='post_title' && $post->post_name!='post_images' && $post->post_name!='post_category')
							{
								if($_heading_type == "[#taxonomy_name#]"){
									echo "<li><h2 class='custom_field_title'>";_e(ucfirst($PostTypeLabelName),'templatic');echo ' '; _e("Information",'templatic');echo "</h2></li>";
								}else{
									echo "<li><h2 class='custom_field_title'>".$_heading_type."</h2></li>";  
								}	
							}
							$i++;
						}
				
							if(get_post_meta($single_pos_id,$post->post_name,true))
							  { 
								if(get_post_meta($post->ID,"ctype",true) == 'multicheckbox')
								  {
									foreach(get_post_meta($single_pos_id,$post->post_name,true) as $value)
									 {
										$_value .= $value.",";
									 }
									 echo "<li class='".$style_class."'><p class='tevolution_field_title label'>".$post->post_title." : </p> <p class='tevolution_field_title'> ".substr($_value,0,-1)."</p></li>";
								  }else if($field_type =='radio' || $field_type =='select'){
									
										$options = explode(',',get_post_meta($post->ID,"option_values",true));
										$options_title = explode(',',get_post_meta($post->ID,"option_title",true));
							
										for($i=0; $i<= count($options); $i++){
											 $val = $options[$i];
											if(trim($val) == trim(get_post_meta($single_pos_id,$post->post_name,true))){ 
												$val_label = $options_title[$i];
												
											}
										}
										
										if($val_label ==''){ $val_label = get_post_meta($single_pos_id,$post->post_name,true); } /* if title not set then display the value*/
											
										echo "<li class='".$style_class."'><p class='tevolution_field_title label'>".$post->post_title." : </p> <p class='tevolution_field_title'> ".$val_label."</p></li>";

								  
								  }
								else
								 {
									 if(get_post_meta($post->ID,'ctype',true) == 'upload')
									 {
									 	echo "<li class='".$style_class."'><p class='tevolution_field_title label'>".$post->post_title." : </p> <p class='tevolution_field_title'> ".__('Click here to download File','templatic-admin')." <a href=".get_post_meta($single_pos_id,$post->post_name,true).">".__('Download','templatic-admin')."</a></p></li>";
									 }
									 else
									 {
										 echo "<li class='".$style_class."'><p class='tevolution_field_title label'>".$post->post_title." : </p> <p class='tevolution_field_title'> ".get_post_meta($single_pos_id,$post->post_name,true)."</p></li>";
									 }
								 }
							  }							
							if($post->post_name == 'post_excerpt' && $suc_post->post_excerpt!='')
							 {
								$suc_post_excerpt = $suc_post->post_excerpt;
								?>
                                     <li>
                                     <div class="row">
                                        <div class="twelve columns">
                                             <div class="title_space">
                                                 <div class="title-container">
                                                     <h1><?php _e('Post Excerpt','templatic');?></h1>
                                                     <div class="clearfix"></div>
                                                 </div>
                                                 <?php echo $suc_post_excerpt;?>
                                             </div>
                                         </div>
                                     </div>
                                     </li>
                                <?php
							 }
		
							if(get_post_meta($post->ID,"ctype",true) == 'geo_map')
							 {
								$add_str = get_post_meta($single_pos_id,'address',true);
								$geo_latitude = get_post_meta($single_pos_id,'geo_latitude',true);
								$geo_longitude = get_post_meta($single_pos_id,'geo_longitude',true);
								$map_view = get_post_meta($single_pos_id,'map_view',true);								
							 }		 
					endwhile;wp_reset_query();
					echo "</ul>";
					echo "</div>";
				  }		
		   }
	  }
	 else
	  {		
		$args = 
		array( 'post_type' => 'custom_fields',
		'posts_per_page' => -1	,
		'post_status' => array('publish'),
		'meta_query' => array(
		   'relation' => 'AND',
			array(
				'key' => 'post_type_'.$cus_post_type.'',
				'value' => $cus_post_type,
				'compare' => '=',
				'type'=> 'text'
			),
			array(
				'key' => 'is_active',
				'value' =>  '1',
				'compare' => '='
			),
			array(
				'key' => 'show_on_detail',
				'value' =>  '1',
				'compare' => '='
			)
		),
			'meta_key' => 'sort_order',
			'orderby' => 'meta_value',
			'order' => 'ASC'
		);				
		$post_query = new WP_Query($args);
		$post_meta_info = $post_query;
		$suc_post = get_post($single_pos_id);				
		if($post_meta_info->have_posts())
		{	
			$i=0;
			/*Display the post_detail heading only one time also with if any custom field create. */
			while ($post_meta_info->have_posts()) : $post_meta_info->the_post();	
				if($i==0)
				if($post->post_name != 'post_excerpt' && $post->post_name != 'post_content' && $post->post_name != 'post_title' && $post->post_name != 'post_images' && $post->post_name != 'post_category')
				{
					echo '<div class="title-container clearfix">';	
					/*echo '<h1>'.POST_DETAIL.'</h1>';*/
					$CustomFieldHeading = apply_filters('CustomFieldsHeadingTitle',POST_DETAIL);
					
					if(function_exists('icl_register_string')){
						icl_register_string('templatic',$CustomFieldHeading,$CustomFieldHeading);
					}
					
					if(function_exists('icl_t')){
						$CustomFieldHeading1 = icl_t('templatic',$CustomFieldHeading,$CustomFieldHeading);
					}else{
						$CustomFieldHeading1 = __($CustomFieldHeading,'templatic'); 
					}
					echo '<h3>'.$CustomFieldHeading1.'</h3>';
				
					echo '</div>';
					$i++;
				}			
			endwhile;wp_reset_query();	/*Finish this while loop for display POST_DETAIL	  		*/
			  ?>              
		<?php echo "<div class='grid02 rc_rightcol clearfix'>";
                echo "<ul class='list'>";
                if($_heading_type!="")			
                    echo "<h3>".$_heading_type."</h3>";
			
			while ($post_meta_info->have_posts()) : $post_meta_info->the_post();				
					if(get_post_meta($single_pos_id,$post->post_name,true))
					  {
						$style_class = get_post_meta($post->ID,"style_class",true);
						if(get_post_meta($post->ID,"ctype",true) == 'multicheckbox')
						  {
							foreach(get_post_meta($single_pos_id,$post->post_name,true) as $value)
							 {
								$_value .= $value.",";
							 }
							 echo "<li class='".$style_class."'><p class='tevolution_field_title'>".$post->post_title.": </p> <p class='tevolution_field_value'> ".substr($_value,0,-1)."</p></li>";
						  }
						else
						 {
							 echo "<li  class='".$style_class."'><p class='tevolution_field_title'>".$post->post_title.": </p> <p class='tevolution_field_value'> ".get_post_meta($single_pos_id,$post->post_name,true)."</p></li>";
						 }
					  }							
					if($post->post_name == 'post_excerpt' && $suc_post->post_excerpt!="")
					 {
						$suc_post_excerpt = $suc_post->post_excerpt;
						?>
                           <li>
                           <div class="row">
                              <div class="twelve columns">
                                   <div class="title_space">
                                       <div class="title-container">
                                           <h1><?php _e('Post Excerpt');?></h1>
                                           <div class="clearfix"></div>
                                       </div>
                                       <?php echo $suc_post_excerpt;?>
                                   </div>
                               </div>
                           </div>
                           </li>
				  <?php
					 }

					if(get_post_meta($post->ID,"ctype",true) == 'geo_map')
					 {
						$add_str = get_post_meta($single_pos_id,'address',true);
						$geo_latitude = get_post_meta($single_pos_id,'geo_latitude',true);
						$geo_longitude = get_post_meta($single_pos_id,'geo_longitude',true);								
					 }
  
			endwhile;wp_reset_query();
			echo "</ul>";
			echo "</div>";
		  }
	  }
		if(isset($suc_post_con)):
		do_action('templ_before_post_content');/*Add action for before the post content. */?> 
             <div class="row">
                <div class="twelve columns">
                     <div class="title_space">
                         <div class="title-container">
                             <h1><?php _e('Post Description', 'templatic');?></h1>
                          </div>
                         <?php echo $suc_post_con;?>
                     </div>
                 </div>
             </div>
   		<?php do_action('templ_after_post_content'); /*Add Action for after the post content. */
		endif;		
			$tmpdata = get_option('templatic_settings');	
			$show_map='';
			if(isset($tmpdata['map_detail_page']) && $tmpdata['map_detail_page']=='yes')
				$show_map=$tmpdata['map_detail_page'];
			if(isset($add_str) && $add_str != '')
			{
			?>
				<div class="row">
					<div class="title_space">
						<div class="title-container">
							<h1><?php _e('Map','templatic'); ?></h1>
						</div>
						<p><strong><?php _e('Location','templatic'); echo ": ".$add_str;?></strong></p>
					</div>
					<div id="gmap" class="graybox img-pad">
						<?php 						
						if($geo_longitude &&  $geo_latitude ):
								$pimgarr = bdw_get_images_plugin($single_pos_id,'thumb',1);
								$pimg = $pimgarr[0]['file'];
								if(!$pimg):
									$pimg = CUSTOM_FIELDS_URLPATH."images/img_not_available.png";
								endif;	
								$title = $suc_post->post_title;
								$link = get_permalink($suc_post->ID);
								$address = $add_str;
								$srcharr = array("'");
								$replarr = array("\'");
								$title = str_replace($srcharr,$replarr,$title);
								$address = str_replace($srcharr,$replarr,$address);
								require_once (TEMPL_MONETIZE_FOLDER_PATH . 'templatic-custom_fields/preview_map.php');
								$retstr ="";								
								$retstr .= "<div class=\"google-map-info map-image forrent\"><div class=map-inner-wrapper><div class=map-item-info><div class=map-item-img><a href=\"$link\"><img src=\"$pimg\" width=\"192\" height=\"134\" alt=\"\" /></a></div>";
								$retstr .= "<h6><a href=\"\" class=\"ptitle\" style=\"color:#444444;font-size:14px;\"><span>$title</span></a></h6>";
								if($address){$retstr .= "<span style=\"font-size:10px;\">$address</span>";}
								$retstr .= "<p class=\"link-style1\"><a href=\"$plink\" class=\"$title\">$more</a></div></div></div>";
								
								
								
								
								$content_data[] = $retstr;
								preview_address_google_map_plugin($geo_latitude,$geo_longitude,$retstr,$map_view);
							  else:
								if(is_ssl()){
									$url = '//maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q='.$add_str.'&amp;ie=UTF8&amp;z=14&amp;iwloc=A&amp;output=embed';
								}else{
									$url = '//maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q='.$add_str.'&amp;ie=UTF8&amp;z=14&amp;iwloc=A&amp;output=embed';
								}
						?>
								<iframe src="<?php echo $url; ?>" height="358" width="100%" scrolling="no" frameborder="0" ></iframe>
						<?php endif; ?>
					</div>
				</div>
			<?php }

}
/* EOF */

/*
 *  add action for display single post image gallery
 */
add_action('templ_post_single_image','single_post_image_gallery');
/*
 Display the single post image gallery in detail page.
*/
function single_post_image_gallery()
{
	global $post;
	$post_type = get_post_type($post->ID);
	$post_type_object = get_post_type_object($post_type);
	$single_gallery_post_type = $post_type_object->labels->name;
	$post_img = bdw_get_images_plugin($post->ID,'large');
	$post_images = $post_img[0]['file'];
	$attachment_id = $post_img[0]['id'];
	$attach_data = get_post($attachment_id);
	$img_title = $attach_data->post_title;
	$img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
	
	$post_img_thumb = bdw_get_images_plugin($post->ID,'thumbnail'); 
	$post_images_thumb = $post_img_thumb[0]['file'];
	$attachment_id1 = $post_img_thumb[0]['id'];
	$attach_idata = get_post($attachment_id1);
	$post_img_title = $attach_idata->post_title;
	$post_img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
	?>
    <div class="row">
		 <?php if(count($post_images)>0): ?>
             <div class="content_details">
                 <div class="graybox">
                        <img id="replaceimg" src="<?php echo $post_images;?>" alt="<?php echo $img_alt; ?>" title="<?php echo $img_title; ?>" />
                 </div>
             </div>            
         <?php endif; ?>
        <div class="row title_space">
            <?php if(count($post_images)>0): ?>
                <div class="title-container">
                    <h2>
						<?php 
							/*_e(MORE_PHOTOS.' '.$single_gallery_post_type,'templatic') */
							$msg = __("More Photos of",'templatic').' '.$single_gallery_post_type;
							if(function_exists('icl_register_string')){
								icl_register_string('templatic',$msg,$msg);
							}
							if(function_exists('icl_t')){
								$message1 = icl_t('templatic',$msg,$msg);
							}else{
								$message1 = __($msg,'templatic'); 
							}
							echo __($message1,'templatic');
						?>
					</h2>
                 </div>
                <div id="gallery">
                    <ul class="more_photos">
                        <?php for($im=0;$im<count($post_img_thumb);$im++):
							$attachment_id = $post_img_thumb[$im]['id'];
							$attach_data = get_post($attachment_id);
							$img_title = $attach_data->post_title;
						?>
                        <li>
                            <a href="<?php echo $post_img[$im]['file'];?>" title="<?php echo $img_title; ?>">
                                <img src="<?php echo $post_img_thumb[$im]["file"];?>" height="70" width="70"  title="<?php echo $img_title; ?>" alt="<?php echo $img_alt; ?>" />
                           </a>
                        </li>
                        <?php endfor; ?>
                    </ul>
               </div>     
			<?php endif;?>
		 </div>
     </div>    
    <?php
}
/* EOF - display gallery */
/*
 * Add action for display related post
 */
add_action('tmpl_related_post','related_post_by_categories');
/*
 * Function Name: related_post_by_single_post
 * Return : Display the related post from single post
 */
function related_post_by_categories()
{
	global $post,$claimpost,$sitepress;
	$claimpost = $post;	
	$tmpdata = get_option('templatic_settings');
	if(@$tmpdata['related_post_numbers']==0){
		return '';	
	}
	$related_post =  @$tmpdata['related_post'];
	$related_post_numbers =  ( @$tmpdata['related_post_numbers'] ) ? @$tmpdata['related_post_numbers'] : 3;
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));	
	remove_all_actions('posts_where');	
	if($related_post=='tags')
	{		
		 $terms = wp_get_post_terms($post->ID, $taxonomies[1], array("fields" => "ids"));	
		 $postQuery = array(
			'post_type'    => $post->post_type,
			'post_status'  => 'publish',
			'tax_query' => array(                
						array(
							'taxonomy' =>$taxonomies[1],
							'field' => 'ID',
							'terms' => $terms,
							'operator'  => 'IN'
						)            
					 ),
			'posts_per_page'=> apply_filters('tmpl_related_post_per_page',$related_post_numbers),			
			'ignore_sticky_posts'=>1,
			'orderby'      => 'RAND',
			'post__not_in' => array($post->ID)
		);
	}
	else
	{		
		 $terms = wp_get_post_terms($post->ID, $taxonomies[0], array("fields" => "ids"));	
		 $postQuery = array(
			'post_type'    => $post->post_type,
			'post_status'  => 'publish',
			'tax_query' => array(                
						array(
							'taxonomy' =>$taxonomies[0],
							'field' => 'ID',
							'terms' => $terms,
							'operator'  => 'IN'
						)            
					 ),
			'posts_per_page'=> apply_filters('tmpl_related_post_per_page',$related_post_numbers),			
			'ignore_sticky_posts'=>1,
			'orderby'      => 'RAND',
			'post__not_in' => array($post->ID)
		);
	}

	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') && (!empty($tmpdata['related_post_type']) && in_array($post->post_type,$tmpdata['related_post_type']))){
		remove_action( 'parse_query', array( $sitepress, 'parse_query' ) );
		add_filter('posts_where', array($sitepress,'posts_where_filter'),10,2);	
	}
	
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php') && (!empty($tmpdata['related_post_type']) && in_array($post->post_type,$tmpdata['related_post_type']))){
		add_filter('posts_where', 'location_related_posts_where_filter');
	}
	
	$my_query = new wp_query($postQuery);

	if(is_plugin_active('Tevolution-LocationManager/location-manager.php') && (!empty($tmpdata['related_post_type']) && in_array($post->post_type,$tmpdata['related_post_type']))){
		remove_filter('posts_where', 'location_related_posts_where_filter');
	}
	$postcount = count($my_query->posts);
	$posttype_obj = get_post_type_object($post->post_type);
	$type_post = "";
	if($postcount > 1 ){
		$type_post = __("Entries",'templatic');
	}else{
		$type_post = __("Entry",'templatic');
	}
	$post_lable = ($posttype_obj->labels->menu_name) ? $posttype_obj->labels->menu_name : $type_post;
	if( $my_query->have_posts() ) :
	 ?>
     <div class="realated_post clearfix">  
    	 <h3><span><?php _e("Related",'templatic'); echo "&nbsp;".$post_lable;?></span></h3>
		 <ul class="related_post_grid_view clearfix">
         <?php	   
		  while ( $my_query->have_posts() ) : $my_query->the_post();		
			if ( has_post_thumbnail())
			{
				$post_rel_img = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), apply_filters('tevolution_replated_image_size','thumb') );
			}
			else
			{
				$post_rel_img =  bdw_get_images_plugin(get_the_ID(),apply_filters('tevolution_replated_image_size','thumb')); 			
			}
			$title = @$post->post_title;
			$alt = $post->post_title;

                        $is_parent = $post->post_parent;	
                        if($is_parent != 0){
                            $featured = get_post_meta($is_parent,'featured_c',true);
                            $calss_featured=$featured=='c'?'featured_c':'';
                        }else{
                            $featured = get_post_meta($post->ID,'featured_c',true);
								if($featured =='n'){  $featured = get_post_meta(get_the_ID(),'featured_h',true); }
							if($featured =='n'){  $featured = get_post_meta(get_the_ID(),'featured_type',true); }
                            $calss_featured=$featured=='c'?'featured_c':'';
                        }
             
		 ?>
         <li>
			<?php if($featured !='n' && $featured !=''){ echo '<span class="featured"></span>'; } ?>
			<?php if( @$post_rel_img[0] ){ 
				if ( has_post_thumbnail())
				{?>
					<a class="post_img" href="<?php echo get_permalink(get_the_ID());?>"><img  src="<?php echo $post_rel_img[0];?>" alt="<?php echo $alt; ?>" title="<?php echo $title; ?>"  /> </a>
			<?php }
				else { ?>
					<a class="post_img" href="<?php echo get_permalink(get_the_ID());?>"><img  src="<?php echo $post_rel_img[0]['file'];?>" alt="<?php echo $alt; ?>" title="<?php echo $title; ?>"  /> </a>
            <?php }
				}else{ ?>
            	<a class="post_img" href="<?php echo get_permalink(get_the_ID());  ?>"><img src="<?php echo TEMPL_PLUGIN_URL."/tmplconnector/monetize/images/no-image.png"; ?>"   alt="<?php echo $post_img[0]['alt']; ?>" /></a>
            <?php } ?>
         	<h3><a href="<?php echo get_permalink(get_the_ID());?>" > <?php the_title();?> </a></h3>
            <?php 	
			do_action('related_post_before_content');
			if(function_exists('theme_get_settings')){
				if(theme_get_settings('supreme_archive_display_excerpt')){
					the_excerpt();
				}else{
					the_content(); 
				}
			}	
			?>
         </li>
         <?php endwhile;?>
         
         </ul>     
     </div>     
     <?php
	wp_reset_query();
	else:
   		/*echo apply_filters('related_post_not_found',sprintf(__('No Related %s found.','templatic'),$post->post_type));   //uncomment if you want to show this message.*/
	endif;
}
/* EOF - related posts */

/*
	Display the breadcrumb
*/
function the_breadcrumb() {
	if (!is_home()) {
		echo '<div class="breadcrumb"><a href="';
		echo get_option('home');
		echo '">'.__('Home','templatic');
		echo "</a>";
		if (is_category() || is_single() || is_archive()) {
			the_category('title_li=');
			if(is_archive())
			{		
				echo " » ";
				single_cat_title();
			}
			if (is_single()) {
				echo " » ";
				the_title();
			}
		} elseif (is_page()) {
			echo the_title();
		}		
		echo "</div>";
	}	
}
/*
 * Add Action display for single post page next previous pagination before comment
 */
if(!strstr($_SERVER['REQUEST_URI'],'/wp-admin/') && (!isset($_REQUEST['slider_search']) && @$_REQUEST['slider_search'] ==''))
{ 
	add_action('tmpl_single_post_pagination','single_post_pagination');
}
/*
	Display the next and previous  pagination in single post page
*/
function single_post_pagination()
{
	global $post;
	?>
		<div class="pos_navigation clearfix">
			<div class="post_left fl"><?php previous_post_link('%link','<i class="fa fa-angle-left"></i>  %title') ?></div>
			<div class="post_right fr"><?php next_post_link('%link','%title <i class="fa fa-angle-right"></i>' ) ?></div>
		</div>
	<?php
}

/*
 * Add action display post categories and tag before the post comments
 */
add_action('templ_the_taxonomies','category_post_categories_tags'); 
function category_post_categories_tags()
{
	if(get_post_type() != 'post'){	
		/* global $post;		
		the_taxonomies(array('before'=>'<p class="bottom_line"><span class="i_category">','sep'=>'</span>&nbsp;&nbsp;<span class="i_tag">','after'=>'</span></p>')); */
		global $wp_query, $post,$htmlvar_name;
		/* get all the custom fields which select as " Show field on listing page" from back end */	
		
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));	
		$terms = get_the_terms($post->ID, $taxonomies[0]);
		$sep = ",";
		$i = 0;
		$taxonomy_category='';
		if(!empty($terms)){
			foreach($terms as $term)
			{
				
				if($i == ( count($terms) - 1))
				{
					$sep = '';
				}
				elseif($i == ( count($terms) - 2))
				{
					$sep = __(' and ','templatic');
				}
				$term_link = get_term_link( $term, $taxonomies[0] );
				if( is_wp_error( $term_link ) )
					continue;
				$taxonomy_category .= '&nbsp;<a href="' . $term_link . '">' . $term->name . '</a>'.$sep; 
				$i++;
			}
		}
		if(!empty($terms) && (!empty($htmlvar_name['basic_inf']['category']) || !empty($htmlvar_name['category'])))
		{
			echo '<p class="bottom_line"><span class="i_category">';
			echo apply_filters('tmpl_taxonomy_title'.get_post_type(),"<span>".__('Posted In','templatic'))."</span>"; echo " ".$taxonomy_category;
			echo '</span></p>';
		}
		global $post;
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));	
		
		$tag_terms = get_the_terms($post->ID, $taxonomies[1]);
		$sep = ",";
		$i = 0;
		$taxonomy_tag ='';
		if($tag_terms){
		foreach($tag_terms as $term)
		{
			
			if($i == ( count($tag_terms) - 1))
			{
				$sep = '';
			}
			elseif($i == ( count($tag_terms) - 2))
			{
				$sep = __(' and ','templatic');
			}
			$term_link = get_term_link( $term, $taxonomies[1] );
			if( is_wp_error( $term_link ) )
				continue;
			$taxonomy_tag .= '<a href="' . $term_link . '">' . $term->name . '</a>'.$sep; 
			$i++;
		}
		}
		if(!empty($tag_terms) && (!empty($htmlvar_name['basic_inf']['post_tags']) || !empty($htmlvar_name['post_tags'])))
		{
			echo '<p class="bottom_line"><span class="i_category">';
			_e(apply_filters('tmpl_tags_title_'.get_post_type(),'Tagged In'),'templatic'); echo " ".$taxonomy_tag;
			echo '</span></p>';
		}
	}
}

/*
 * Add action display post categories and tag before the post comments
 */
add_action('tmpl_before_comments','single_post_categories_tags'); 
function single_post_categories_tags()
{
	/*global $post;		
	the_taxonomies(array('before'=>'<p class="bottom_line"><span class="i_category">','sep'=>'</span>&nbsp;&nbsp;<span class="i_tag">','after'=>'</span></p>'));*/
	global $post;
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));	
	$terms = get_the_terms($post->ID, $taxonomies[1]);
	$sep = ",";
	$i = 0;
	if($terms){
	foreach($terms as $term)
	{
		
		if($i == ( count($terms) - 1))
		{
			$sep = '';
		}
		elseif($i == ( count($terms) - 2))
		{
			$sep = ' and ';
		}
		$term_link = get_term_link( $term, $taxonomies[1] );
		if( is_wp_error( $term_link ) )
			continue;
		$taxonomy_category .= '<a href="' . $term_link . '">' . $term->name . '</a>'.$sep; 
		$i++;
	}
	}
	if(isset($taxonomy_category) && $taxonomy_category!=''){
		_e('Tagged In','templatic'); echo " ".$taxonomy_category;
	}else{
		echo " ";
	}
}

/*
 * add action for display the post info
 */
add_action('templ_post_info','post_info');
function post_info()
{
	global $post;
	$num_comments = get_comments_number();
	$write_comments='';
	if ( comments_open() ) {
		if ( $num_comments == 0 ) {
			$comments = __('No Comments','templatic');
		} elseif ( $num_comments > 1 ) {
			$comments = $num_comments .' '. __('Comments','templatic');
		} else {
			$comments = __('1 Comment','templatic');
		}
		$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
	}
	?>
    <div class="byline">
		<?php
		$post_type = get_post_type_object( get_post_type() );
		if ( !current_user_can( $post_type->cap->edit_post, get_the_ID() ) ){
			$edit = '';
		}else{
			$edit = '<span class="post_edit"><a class="post-edit-link" href="' . esc_url( get_edit_post_link( get_the_ID() ) ) . '" title="' . sprintf( esc_attr__( 'Edit %1$s', 'templatic' ), $post_type->labels->singular_name ) . '">' . __( 'Edit', 'templatic' ) . '</a></span>';
		}	
		$author = __('Published by','templatic').' <span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a></span>';
		$published = __('On','templatic').' <abbr class="published" title="' . sprintf( get_the_time( esc_attr__( get_option('date_format')) ) ) . '">' . sprintf( get_the_time( esc_attr__( get_option('date_format')) ) ) . '</abbr>';
	    echo sprintf(__('%s %s %s %s','templatic'),$author,$published,$write_comments,$edit);
        ?>
    </div>
    <?php		
}
/* Add action for display the image in taxonomy page */
add_action('tmpl_category_page_image','tmpl_category_page_image');
add_action('tmpl_archive_page_image','tmpl_category_page_image'); 
/*
 * Function Name: tmpl_category_page_image
 */
function tmpl_category_page_image()
{
	global $post;		
	if ( has_post_thumbnail()):
		echo '<a href="'.get_permalink().'" class="event_img">';
		if($featured){echo '<span class="featured_tag">'.__('Featured111',EDOMAIN).'</span>';}
		the_post_thumbnail('event-listing-image'); 
		echo '</a>';
	else:
	$post_img = bdw_get_images_plugin($post->ID,'thumbnail');
	$thumb_img = $post_img[0]['file'];
	$attachment_id = $post_img[0]['id'];
	$attach_data = get_post($attachment_id);
	$img_title = $attach_data->post_title;
	$img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
	?>
    <?php if($thumb_img):?>
						<a href="<?php the_permalink();?>" class="post_img">
							<?php do_action('inside_listing_image'); ?>
							<img src="<?php echo $thumb_img; ?>"  alt="<?php echo $img_alt; ?>" title="<?php echo $img_title; ?>" />
						</a>
    <?php else:?>
					<a href="<?php the_permalink();?>" class="post_img no_image_avail">
						<?php do_action('inside_listing_image'); ?>
						<img src="<?php echo CUSTOM_FIELDS_URLPATH; ?>/images/img_not_available.png" alt="" height="156" width="180"  />
					</a>	
    <?php endif;
	endif;?>
 <?php
}
add_action('templ_taxonomy_content','templ_taxonomy_category_content');
function templ_taxonomy_category_content()
{ 
	global $wp_query,$post,$htmlvar_name;
	$post_type = get_post_type();
	
	if(isset($_REQUEST['custom_post']) && $_REQUEST['custom_post'] !=''){
		$post_type = $_REQUEST['custom_post'];
	}
	/* get all the custom fields which select as " Show field on listing page" from back end */	
	$tmpdata = get_option('templatic_settings');	
	if(@$tmpdata['listing_hide_excerpt']=='' || !in_array($post_type,@$tmpdata['listing_hide_excerpt'])){
		if(function_exists('supreme_prefix')){
			$theme_settings = get_option(supreme_prefix()."_theme_settings");
		}else{
			$theme_settings = get_option("supreme_theme_settings");
		}
		if($theme_settings['supreme_archive_display_excerpt'] && (!empty($htmlvar_name['post_excerpt']) || !empty($htmlvar_name['post_excerpt']) || !empty($htmlvar_name['basic_inf']['post_excerpt'])) ){
			echo '<div itemprop="description" class="entry-summary">';
			if(!function_exists('tevolution_excerpt_length')){	
				if($theme_settings['templatic_excerpt_length']){
					$length = $theme_settings['templatic_excerpt_length'];
				}
				if(function_exists('print_excerpt')){
					echo print_excerpt($length);
				}else{
					the_excerpt();
				}
			}else{
				the_excerpt();
			}
			echo '</div>';
		}elseif(!empty($htmlvar_name['post_content']) || !empty($htmlvar_name['basic_inf']['post_content'])){ 
			echo '<div itemprop="description" class="entry-content">';
			the_content(); 
			echo '</div>';
		}
	}
}
/* filtering for featured listing  start*/
if(!strstr($_SERVER['REQUEST_URI'],'/wp-admin/') && (!isset($_REQUEST['slider_search']) && @$_REQUEST['slider_search'] ==''))
{ 
	add_action('init', 'templ_featured_ordering');
}
function templ_featured_ordering(){
	global $wp_query;
	
		add_filter('posts_orderby', 'feature_filter_order');
		add_filter('posts_where','tmpl_sort_by_character');
		add_filter('pre_get_posts', 'home_page_feature_listing');
		add_filter('posts_orderby', 'home_page_feature_listing_orderby');

}

/*
 * search by alphabetical
 */
function tmpl_sort_by_character($where){
	global $wpdb,$wp_query;	
	if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!=''){
		$where .= "  AND $wpdb->posts.post_title like '".$_REQUEST['sortby']."%'";
	}
	return $where;
}

/* featured posts filter for listing page */
function feature_filter_order($orderby)
{
	global $wpdb,$wp_query;	
	if((is_category() || is_tax() || is_archive()) && $wp_query->tax_query->queries[0]['taxonomy'] != 'product_cat')
	 {
		
		if (isset($_REQUEST['tevolution_sortby']) && ($_REQUEST['tevolution_sortby'] == 'title_asc' || $_REQUEST['tevolution_sortby'] == 'alphabetical'))
		{
			$orderby= "$wpdb->posts.post_title ASC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') ASC";
		}
		elseif (isset($_REQUEST['tevolution_sortby']) && $_REQUEST['tevolution_sortby'] == 'title_desc' )
		{
			$orderby = "$wpdb->posts.post_title DESC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
		}
		elseif (isset($_REQUEST['tevolution_sortby']) && $_REQUEST['tevolution_sortby'] == 'date_asc' )
		{
			$orderby = "$wpdb->posts.post_date ASC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
		}
		elseif (isset($_REQUEST['tevolution_sortby']) && $_REQUEST['tevolution_sortby'] == 'date_desc' )
		{
			$orderby = "$wpdb->posts.post_date DESC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
		}elseif(isset($_REQUEST['tevolution_sortby']) && $_REQUEST['tevolution_sortby'] == 'random' )
		{
			$orderby = " (select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC,rand()";
		}elseif(isset($_REQUEST['tevolution_sortby']) && $_REQUEST['tevolution_sortby'] == 'reviews' )
		{
			$orderby = 'DESC';
			$orderby = " comment_count $orderby,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
		}elseif(isset($_REQUEST['tevolution_sortby']) && $_REQUEST['tevolution_sortby'] == 'rating' )
		{

			$orderby = " (select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key like \"average_rating\") DESC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
		}else
		{
			$orderby = " (SELECT DISTINCT $wpdb->postmeta.meta_value from $wpdb->postmeta where ($wpdb->posts.ID = $wpdb->postmeta.post_id) AND $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC,$wpdb->posts.post_date DESC";
		}
	 }
	 return $orderby;
}

/* fetch featured posts filter for home page */
function home_page_feature_listing( &$query)
{	
	if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] !=''):
		$post_type= @$query->query_vars['post_type'];
	else:
		$post_type='';
	endif;
	if((is_home() || @is_front_page() || get_option('show_on_front') == 'posts') && !is_page()){
		$tmpdata = get_option('templatic_settings');
		$home_listing_type_value = @$tmpdata['home_listing_type_value'];
		
		if(!empty($home_listing_type_value)){
			$attach = array('attachment');
			if(is_array($home_listing_type_value))
				$merge = array_merge($home_listing_type_value,$attach);
				
			if($post_type=='booking_custom_field'):
				$query->set('post_type',$post_type); /* set custom field post type*/
			else:
				$query->set('post_type', @$merge); /* set post type events */
			endif;
		}
		$query->set('post_status',array('publish')); /* set post type events */
	}else{

		remove_action('pre_get_posts', 'home_page_feature_listing');
	}
}

/* sort featured posts filter for home page */
function home_page_feature_listing_orderby($orderby)
{
	global $wpdb,$wp_query;
	if(is_home() || @is_front_page()){		
		$orderby = " (SELECT DISTINCT($wpdb->postmeta.meta_value) from $wpdb->postmeta where ($wpdb->posts.ID = $wpdb->postmeta.post_id) AND $wpdb->postmeta.meta_key = 'featured_h' AND $wpdb->postmeta.meta_value = 'h') DESC,$wpdb->posts.post_date DESC";
	}
	return $orderby;
}
/* filtering for featured listing end*/


add_action('templ_listing_custom_field','templ_custom_field_display',10,2);
function templ_custom_field_display($custom_field,$pos_title)
{
	global $post,$wpdb;		
	?>
     <div class="postmetadata">
        <ul>
		<?php $i=0; 
		
		if(!empty($custom_field)){
			foreach($custom_field as $key=> $_htmlvar_name):
				if($key!='category' && $key!='post_title' && $key!='post_content' && $key!='post_excerpt' && $key!='post_images' ):
					if($_htmlvar_name['type'] == 'multicheckbox' && get_post_meta($post->ID,$key,true) !=''):
						?>
                              <li class="<?php echo $custom_field[$key]['style_class']; ?>"><label><?php echo $_htmlvar_name['label']; ?></label> : <span><?php echo implode(",",get_post_meta($post->ID,$key,true)); ?></span></li>
                              <?php
					endif;
					if($_htmlvar_name['type'] != 'multicheckbox' && get_post_meta($post->ID,$key,true) !=''):
					?>
						<li class="<?php echo $custom_field[$key]['style_class']; ?>"><label><?php echo $_htmlvar_name['label']; ?></label> : <span><?php echo get_post_meta($post->ID,$key,true); ?></span></li>
					<?php
					endif;
				endif;				
			endforeach;			
		}
		?>
        </ul>
     </div>
     <?php	
}

/*
	get the image path 
 */
function get_templ_image($post_id,$size='thumbnail') {

	global $post;
	/*get the thumb image*/	
	$thumbnail = wp_get_attachment_image_src ( get_post_thumbnail_id ( $post_id ), $size ) ;	
	if($thumbnail[0]!='')
	{
		$image_src=$thumbnail[0];		
	}else
	{
		$post_img_thumb = bdw_get_images_plugin($post_id,$size); 
		$image_src = $post_img_thumb[0]['file'];
	}	
	return $image_src;
}


/* return the sorting options and views button*/
function tmpl_archives_sorting_opt(){
	global $wpdb,$wp_query,$sort_post_type;
	
	if(!is_search()){
		$post_type = (get_post_type()!='')? get_post_type() : get_query_var('post_type');
		$sort_post_type = apply_filters('tmpl_tev_sorting_for_'.$post_type,$post_type);
		
	}else{
		/* on search page what happens if user search with multiple post types */
		if(isset($_REQUEST['post_type'])){
			if(is_array($_REQUEST['post_type']) && count($_REQUEST['post_type'])==1){
				$sort_post_type= $_REQUEST['post_type'][0];
			}else{
				$sort_post_type= $_REQUEST['post_type'];
			}
		}
			if(!$cur_post_type){
				$sort_post_type='directory';
			}
		
	}
	
	$templatic_settings=get_option('templatic_settings');
	$googlemap_setting=get_option('city_googlemap_setting');
	
	/*custom post type link */
	$current_posttype = get_post_type();
	
	if(empty($current_posttype)){
		$current_posttype = $wp_query->query['post_type'];
	}
		
	if(!is_tax() && is_archive() && !is_search())
	{
		$current_term = $wp_query->get_queried_object();		
		$permalink = get_post_type_archive_link($current_posttype);
		$permalink=str_replace('&'.$sort_post_type.'_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
	}elseif(is_search()){
		$search_query_str=str_replace('&'.$sort_post_type.'_sortby=alphabetical&sortby='.@$_REQUEST['sortby'],'',$_SERVER['QUERY_STRING']);
		$permalink= site_url()."?".$search_query_str;
	}else{
		$current_term = $wp_query->get_queried_object();
		$permalink=($current_term->slug) ?  get_term_link($current_term->slug, $current_term->taxonomy):'';
		if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!='')
			$permalink=str_replace('&'.$sort_post_type.'_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
		
	}
	
	$post_type= get_post_type_object( get_post_type());
	
	/* get all the request url and con-cat with permalink to get the exact results */
	foreach($_GET as $key=>$val){
		if($key !='' && !strstr($key,'_sortby')){
			$req_uri .= $key."=".$val."&";
		}
	}
	
	/* permalink */
	if(false===strpos($permalink,'?')){
	    $url_glue = '?'.$req_uri;
	}else{
		$url_glue = '&amp;'.$req_uri;	
	}
	
	/* no grid view list view if no results found */
	
	if($wp_query->found_posts!=0){
	?>
	<div class='directory_manager_tab clearfix'>
	<div class="sort_options">
	<?php if(have_posts()!='' && current_theme_supports('tmpl_show_pageviews')): ?>
		<ul class='view_mode viewsbox'>
			<?php if(function_exists('tmpl_wp_is_mobile') && tmpl_wp_is_mobile()){ 
				$templatic_settings['category_googlemap_widget']; if(isset($templatic_settings['category_googlemap_widget']) && $templatic_settings['category_googlemap_widget']=='yes'){
				?>
				<li><a class='switcher last listview  <?php if($templatic_settings['default_page_view']=="listview"){echo 'active';}?>' id='listview' href='#'><?php _e('LIST VIEW','templatic');?></a></li>
				<li><a class='map_icon <?php if($templatic_settings['default_page_view']=="mapview"){echo 'active';}?>' id='locations_map' href='#'><?php _e('MAP','templatic');?></a></li>
			<?php }	
			}else{ ?>
				<li><a class='switcher first gridview <?php if($templatic_settings['default_page_view']=="gridview"){echo 'active';}?>' id='gridview' href='#'><?php _e('GRID VIEW','templatic');?></a></li>
				<li><a class='switcher last listview  <?php if($templatic_settings['default_page_view']=="listview"){echo 'active';}?>' id='listview' href='#'><?php _e('LIST VIEW','templatic');?></a></li>
				<?php $templatic_settings['category_googlemap_widget']; if(isset($templatic_settings['category_googlemap_widget']) && $templatic_settings['category_googlemap_widget']=='yes'):?> 
				<li><a class='map_icon <?php if($templatic_settings['default_page_view']=="mapview"){echo 'active';}?>' id='locations_map' href='#'><?php _e('MAP','templatic');?></a></li>
				<?php endif;
			}
			?>
		</ul>	
	<?php endif;

	if(isset($_GET[$sort_post_type.'_sortby']) && $_GET[$sort_post_type.'_sortby']=='alphabetical'){
		$_SESSION['alphabetical']='1';	
	}else{
		unset($_SESSION['alphabetical']);
	}
	
	if(!empty($templatic_settings['sorting_option'])){

		/* take "directory" as a post type if additional post type is detected */
		$exclude_arr = apply_filters('exclude_sorting_posttypes',array('event','property','classified'));
		if(!in_array(get_post_type(),$exclude_arr)){
			$sort_post_type_name = 'tevolution';
		}	
		else{	
			$sort_post_type_name = get_post_type();
		}
		
		$sel_sort_by = $_REQUEST[$sort_post_type_name.'_sortby'];
		$sel_class = 'selected=selected';
		
	?>
		<div class="tev_sorting_option">
			<form action="<?php if(function_exists('tmpl_directory_full_url')){ echo tmpl_directory_full_url('directory'); } ?>" method="get" id="<?php echo $sort_post_type.'_sortby_frm'; ?>" name="<?php echo $sort_post_type.'_sortby_frm'; ?>">
               <select name="<?php echo $sort_post_type_name.'_sortby'; ?>" id="<?php echo $sort_post_type_name.'_sortby'; ?>" onchange="sort_as_set(this.value)" class="tev_options_sel">
				<option <?php if(!$sel_sort_by){ echo $sel_class; } ?>><?php _e('Sort By','templatic'); ?></option>
				<?php
					do_action('tmpl_before_sortby_title_alphabetical');
					if(!empty($templatic_settings['sorting_option']) && in_array('title_alphabetical',$templatic_settings['sorting_option'])):?>
						<option value="alphabetical" <?php if($sel_sort_by =='alphabetical'){ echo $sel_class; } ?>><?php _e('Alphabetical','templatic');?></option>
				<?php endif;
					do_action('tmpl_after_sortby_title_alphabetical');
					
					do_action('tmpl_before_sortby_title_asc');
					if(!empty($templatic_settings['sorting_option']) && in_array('title_asc',$templatic_settings['sorting_option'])):?>
						<option value="title_asc" <?php if($sel_sort_by =='title_asc'){ echo $sel_class; } ?>><?php _e('Title Ascending','templatic');?></option>
				<?php endif;
					do_action('tmpl_after_sortby_title_asc');
					
					do_action('tmpl_before_sortby_title_desc');
					if(!empty($templatic_settings['sorting_option']) && in_array('title_desc',$templatic_settings['sorting_option'])):?>
						<option value="title_desc" <?php if($sel_sort_by =='title_desc'){ echo $sel_class; } ?>><?php _e('Title Descending','templatic');?></option>
				<?php endif;
					do_action('tmpl_after_sortby_title_desc');
				
					do_action('tmpl_before_sortby_date_asc');
					if(!empty($templatic_settings['sorting_option']) && in_array('date_asc',$templatic_settings['sorting_option'])):?>
						<option value="date_asc" <?php if($sel_sort_by =='date_asc'){ echo $sel_class; } ?>><?php _e('Publish Date Ascending','templatic');?></option>
				<?php endif;
					do_action('tmpl_after_sortby_date_asc');
					
					do_action('tmpl_before_date_desc');
					if(!empty($templatic_settings['sorting_option']) && in_array('date_desc',$templatic_settings['sorting_option'])):?>
						<option value="date_desc" <?php if($sel_sort_by =='date_desc'){ echo $sel_class; } ?>><?php _e('Publish Date Descending','templatic');?></option>
				<?php endif;
					do_action('tmpl_after_sortby_date_desc');
					
					do_action('tmpl_before_sortby_reviews');
					if(!empty($templatic_settings['sorting_option']) && in_array('reviews',$templatic_settings['sorting_option'])):?>
						<option value="reviews" <?php if($sel_sort_by =='reviews'){ echo $sel_class; } ?>><?php _e('Reviews','templatic');?></option>
				<?php endif;
					do_action('tmpl_after_sortby_reviews');
					
					do_action('tmpl_before_sortby_rating');
					if(!empty($templatic_settings['sorting_option']) && in_array('rating',$templatic_settings['sorting_option'])):?>
						<option value="rating" <?php if($sel_sort_by =='rating'){ echo $sel_class; } ?>><?php _e('Rating','templatic');?></option>
				<?php endif;
					do_action('tmpl_after_sortby_rating');
					
					do_action('tmpl_before_sortby_random');
					if(!empty($templatic_settings['sorting_option']) && in_array('random',$templatic_settings['sorting_option'])):?>
						<option value="random" <?php if($sel_sort_by =='random'){ echo $sel_class; } ?>><?php _e('Random','templatic');?></option>
				<?php endif;
					do_action('tmpl_after_sortby_random');
					?>             
			  </select>
			 </form>
             <?php add_action('wp_footer','sorting_option_of_listing'); ?>
		</div>
    <?php
	}

	?>
     	</div><!--END sort_options div -->
    </div><!-- END directory_manager_tab Div -->
	<?php
	}
	
	
	/* On archive and category pages - alphabets order should display even there is no post type pass in argument  */
	$exclude_arr = array('event','property','classified');
	if(isset($_REQUEST['alpha_sort_post_type']) && $_REQUEST['alpha_sort_post_type'] != '')
		$sort_post_type = $_REQUEST['alpha_sort_post_type'];
	if(!in_array($sort_post_type,$exclude_arr))
		$sort_post_type = 'tevolution';
	else	
		$sort_post_type = $sort_post_type;
	if(!$sort_post_type){ $sort_post_type="tevolution"; }
	if((isset($_REQUEST[$sort_post_type.'_sortby']) && $_REQUEST[$sort_post_type.'_sortby']=='alphabetical') || (isset($_SESSION['alphabetical']) && $_SESSION['alphabetical']==1)):
	
	$alphabets = array(__('A','templatic'),__('B','templatic'),__('C','templatic'),__('D','templatic'),__('E','templatic'),__('F','templatic'),__('G','templatic'),__('H','templatic'),__('I','templatic'),__('J','templatic'),__('K','templatic'),__('L','templatic'),__('M','templatic'),__('N','templatic'),__('O','templatic'),__('P','templatic'),__('Q','templatic'),__('R','templatic'),__('S','templatic'),__('T','templatic'),__('U','templatic'),__('V','templatic'),__('W','templatic'),__('X','templatic'),__('Y','templatic'),__('Z','templatic'));
	/*show all result when we click on all in alphabetical sort order*/
	$all = str_replace('?sortby='.$_REQUEST['sortby'].'&','/?',$url_glue);
	?>
    <div id="directory_sort_order_alphabetical" class="sort_order_alphabetical">
	    <ul>
			<li class="<?php echo (!isset($_REQUEST['sortby']))?'active':''?>"><a href="<?php echo remove_query_arg('sortby',$permalink.$all.$sort_post_type.'_sortby=alphabetical');?>"><?php _e('All','templatic');?></a></li>
			<?php
			foreach($alphabets as &$value){ 
				$key = $value;
				$val = strtolower($key);
				?>
				<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby'] == $val)? 'active':''?>"><a href="<?php echo $permalink.$url_glue .$sort_post_type.'_sortby=alphabetical&sortby='.$val.'&alpha_sort_post_type='.$sort_post_type;?>"><?php echo $key; ?></a></li>
				<?php 
			} ?>
	    </ul>
    </div>
    <?php endif;
}
function sorting_option_of_listing()
{
	?>
    <script type="text/javascript" async >
		function sort_as_set(val)
		{
			<?php 
			global $sort_post_type;
			$current_post_type = get_post_type();
			$addons_posttype = tmpl_addon_name(); /* all tevolution addons' post type as key and folter name as a value */
			$exclude_arr = apply_filters('exclude_sorting_posttypes',array('event','property','classified'));
			if(!in_array($current_post_type, $exclude_arr)){
				$sort_post_type = 'tevolution';
			}else{
				$sort_post_type = $current_post_type;
			}
				
			if(function_exists('tmpl_directory_full_url')){ ?>
			if(document.getElementById('<?php echo $sort_post_type; ?>_sortby').value)
			{
				<?php if(strstr(tmpl_directory_full_url($sort_post_type),'?')): ?>
					window.location = '<?php echo tmpl_directory_full_url($sort_post_type); ?>'+'&'+'<?php echo $sort_post_type; ?>'+'_sortby='+val;
				<?php else: ?>
					window.location = '<?php echo tmpl_directory_full_url($sort_post_type); ?>'+'?'+'<?php echo $sort_post_type; ?>'+'='+val;
				<?php endif; ?>
			}
			<?php } ?>
		}
	</script>
    <?php
}

/*
	Return the label of taxonomy for archive page title.
 */
add_filter('tevolution_archive_page_title','tevolution_archive_page_title');
function tevolution_archive_page_title()
{
	global $wp_query;
	$PostTypeObject = get_post_type_object($wp_query->query_vars['post_type']);
	$_PostTypeName = $PostTypeObject->labels->name;
	return $_PostTypeName;
}

/* return the related listings query without HTML */

function tmpl_get_related_posts_query(){ 
	
	global $post,$claimpost,$sitepress;
	$claimpost = $post;	
	$tmpdata = get_option('templatic_settings');
	if(@$tmpdata['related_post_numbers']==0 && $tmpdata['related_post_numbers']!=''){
		return '';	
	}
	$related_post =  @$tmpdata['related_post'];
	$related_post_numbers =  ( @$tmpdata['related_post_numbers'] ) ? @$tmpdata['related_post_numbers'] : 3;
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));	
	remove_all_actions('posts_where');
	
	if($post->ID !=''){
	if($related_post=='tags')
	{		
		 $terms = wp_get_post_terms($post->ID, $taxonomies[1], array("fields" => "ids"));	
		 $postQuery = array(
			'post_type'    => $post->post_type,
			'post_status'  => 'publish',
			'tax_query' => array(                
						array(
							'taxonomy' =>$taxonomies[1],
							'field' => 'ID',
							'terms' => $terms,
							'operator'  => 'IN'
						)            
					 ),
			'posts_per_page'=> apply_filters('tmpl_related_post_per_page',$related_post_numbers),
			'orderby'      => 'RAND',
			'post__not_in' => array($post->ID)
		);
	}
	else
	{		
		 $terms = wp_get_post_terms($post->ID, $taxonomies[0], array("fields" => "ids"));	
		 $postQuery = array(
			'post_type'    => $post->post_type,
			'post_status'  => 'publish',
			'tax_query' => array(                
						array(
							'taxonomy' =>$taxonomies[0],
							'field' => 'ID',
							'terms' => $terms,
							'operator'  => 'IN'
						)            
					 ),
			'posts_per_page'=> apply_filters('tmpl_related_post_per_page',$related_post_numbers),
			'orderby'      => 'RAND',
			'post__not_in' => array($post->ID)
		);
	}
	}
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		remove_action( 'parse_query', array( $sitepress, 'parse_query' ) );
		add_filter('posts_where', array($sitepress,'posts_where_filter'),10,2);	
	}
	
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php') ){
		add_filter('posts_where', 'location_related_posts_where_filter');
	}
	$my_query = new wp_query($postQuery);
	
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
		remove_filter('posts_where', 'location_related_posts_where_filter');
	}
	
	return $my_query;
 }
/*hide sort option while map view is selected.*/
add_action('wp_footer','hide_archive_sort_option');
function hide_archive_sort_option()
{
	global $posts,$wp_query;

	if((is_tax() || is_archive()) && !empty($posts))
	{
	?>
    <script type="text/javascript" async>
		jQuery(window).load(function () {
			if(jQuery('#locations_map').hasClass('active'))
			{
				jQuery('.tev_sorting_option').css('display','none');
				jQuery('#directory_sort_order_alphabetical').css('display','none');
			}
			else
			{
				jQuery('.tev_sorting_option').css('display','');
				jQuery('#directory_sort_order_alphabetical').css('display','');
			}
			jQuery('.viewsbox a.listview').click(function(e){
				jQuery('.tev_sorting_option').css('display','');
				jQuery('#directory_sort_order_alphabetical').css('display','');
			});
			jQuery('.viewsbox a.gridview').click(function(e){
				jQuery('.tev_sorting_option').css('display','');
				jQuery('#directory_sort_order_alphabetical').css('display','');
			});
			jQuery('.viewsbox a#locations_map').click(function(e){
				jQuery('.tev_sorting_option').css('display','none');
				jQuery('#directory_sort_order_alphabetical').css('display','none');
			});
		});
	</script>
    <?php
	}
}


/* Pass the custom post types for feeds */

if(!function_exists('directory_myfeed_request')){
	function directory_myfeed_request($qv) {
		if (isset($qv['feed']))
			$qv['post_type'] = get_post_types();
		return $qv;
	}
}
add_filter('request', 'directory_myfeed_request');

/* category page and search page excerpt content limit */
if(!function_exists('print_excerpt')){
	function print_excerpt($length) { // Max excerpt length. Length is set in characters
		global $post;
                $length = ($length =='')? '50' : $length;
		$tmpdata = get_option('directory_theme_settings');
		$morelink = @$tmpdata['templatic_excerpt_link'];
		if(!empty($morelink))
			$morelink =sprintf(__('<a href="%s" class="more moretag">%s</a>','templatic'),get_permalink(),$morelink);
		else
			$morelink ='<a class="moretag" href="'.get_permalink().'" class="more">'.__('Read more').'...</a>';
		
		$text = $post->post_excerpt;
		if ($text =='') {
			$text = get_the_content($post->ID);
			$text = apply_filters('the_excerpt', $text);
			$text = str_replace(']]>', ']]>', $text);
		}
		$text = strip_shortcodes($text); // optional, recommended
		$text = strip_tags($text); // use ' $text = strip_tags($text,'<p><a>'); ' if you want to keep some tags

		$text = wp_trim_words($text,$length); /* shows perticular words */
		if(reverse_strrchr($text, '.', 1)){
			$excerpt = reverse_strrchr($text, '.', 1)." ".sprintf(__('%s','templatic'),$morelink);
		}else{
			$excerpt = $text." ".sprintf(__('%s','templatic'),$morelink);
		}
		
		if( $excerpt ) {
			echo apply_filters('the_excerpt',$excerpt);
		} else {
			echo apply_filters('the_excerpt',$text);
		}
	}
}
?>