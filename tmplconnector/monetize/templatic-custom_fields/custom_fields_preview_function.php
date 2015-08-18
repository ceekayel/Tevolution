<?php
/*
 * function related to preview pop up for submit form.
 */
/* Add Action for display the preview page post image gallery  */
add_action('tmpl_preview_page_gallery','tmpl_preview_detail_page_gallery_display');
function tmpl_preview_detail_page_gallery_display()
{
	if(isset($_REQUEST['imgarr']) && !empty($_REQUEST['imgarr']) &&  @$_REQUEST['pid']=="")
	{
	?>
	<div class="preview-post-images clearfix">
	<?php
	$thumb_img_counter = 0;
	/* gallery begin */	
		
		$single_gallery_post_type=$_POST['submit_post_type'];
		
		$thumb_img_counter = $thumb_img_counter+count($_REQUEST['imgarr']);
		$image_path = get_image_phy_destination_path_plugin();
		$tmppath = "/".$upload_folder_path."tmp/";
		foreach($_REQUEST['imgarr'] as $image_id=>$val):
			$thumb_image = get_template_directory_uri().'/images/tmp/'.$val;
			break;
		endforeach;	
	 ?>
		 <div class="content_details">
			 <div class="graybox">
			 <?php $f=0; foreach($_REQUEST['imgarr'] as $image_id=>$val):
					$curry = date("Y");
					$currm = date("m");
					$src = TEMPLATEPATH.'/images/tmp/'.$val;
					$img_title = pathinfo($val);									 
					
					if($largest_img_arr): 
						foreach($largest_img_arr as $value):
							$name = end(explode("/",$value['file']));
							if($val == $name):	?>
								<img src="<?php echo  $value['file'];?>" alt=""  width="700"/>
							<?php 
							endif;
							endforeach;
					else: ?>
					<img src="<?php echo $thumb_image;?>" alt=""   width="600"/>
				<?php endif;
				if($f==0) break; endforeach;?>
			 </div>
		 </div>             
		 <div id="gallery">
			<h3><?php echo MORE_PHOTOS; echo " ";  echo __($single_gallery_post_type,DOMAIN); ?></h3>
			<ul class="more_photos">
			 <?php
				foreach($_REQUEST['imgarr'] as $image_id=>$val)
				{
					$curry = date("Y");
					$currm = date("m");
					$src = TEMPLATEPATH.'/images/tmp/'.$val;
					$img_title = pathinfo($val);						
					if($val):
					if(file_exists($src)):
							 $thumb_image = get_template_directory_uri().'/images/tmp/'.$val; ?>
							 <li><a href="<?php echo $thumb_image;?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $thumb_image;?>" alt="" height="70" width="70" title="<?php echo $img_title['filename'] ?>" /></a></li>
					<?php else:
							if($largest_img_arr):
							foreach($largest_img_arr as $value):
								$name = end(explode("/",$value['file']));									
								if($val == $name):?>
								<li><a href="<?php echo $value['file']; ?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $value['file'];?>" alt="" height="70" width="70" title="<?php echo $img_title['filename'] ?>" /></a></li>
						<?php
								endif;
							endforeach;
							endif;
						
						endif; 
						
					else: 

					if($thumb_img_arr): 
					
						$thumb_img_counter = $thumb_img_counter+count($thumb_img_arr);
						for($i=0;$i<count($thumb_img_arr);$i++):
							$thumb_image = $large_img_arr[$i];
							
							if(!is_array($thumb_image)):	?>
						  <li><a href="<?php echo $thumb_image;?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $thumb_image;?>" alt="" height="70" width="70" title="<?php echo $img_title['filename'] ?>" /></a></li>
						<?php 
						endif;
						  
						endfor; 
						endif;
					endif; 
				$thumb_img_counter++;
				} ?>
				</ul>
		 </div>
	
	</div>
	<?php }/* gallery end */
}
/*  Finish add action for preview page */

/* add action for display preview detail page fields collection */

add_action('tmpl_preview_page_fields_collection','tmpl_preview_detail_page_fields_collection_display');
function tmpl_preview_detail_page_fields_collection_display($cur_post_type)
{		

	$heading_type = fetch_heading_per_post_type($cur_post_type);
	if(count($heading_type) > 0)
	{
	foreach($heading_type as $_heading_type)
		{
		$post_meta_info = tmpl_show_on_detail($cur_post_type,$_heading_type); /* return fields selected for detail page  */
		}
	}
	else
	{
		$post_meta_info = tmpl_show_on_detail($cur_post_type,''); /* return fields selected for detail page  */
	}
	
	if(empty($post_meta_info))
		$post_meta_info = array();
	
	if($post_meta_info)
	{
		/* display custom fields value */
		do_action('templatic_fields_onpreview',$_POST,$cur_post_type);
	}
	
}

add_action('templatic_fields_onpreview','tmpl_show_custom_fields_onpreview',10,2);

/*
	Display the custom fields on preview page , It shows the fields which are selected as show on detail page "Yes"
*/
function tmpl_show_custom_fields_onpreview($session,$cur_post_type){
	global $wpdb,$post,$upload;
	
	$heading_type = fetch_heading_per_post_type($cur_post_type);
	if(count($heading_type) > 0)
	{
		/* Fetch the custom fields as per a different heading type */
		foreach($heading_type as $_heading_type)		
			$post_meta_info_arr[$_heading_type] = tmpl_show_on_detail($cur_post_type,$_heading_type);
	}
	else
		$post_meta_info_arr[] = tmpl_show_on_detail($cur_post_type,'');
	
	echo "<div class='grid02 rc_rightcol clearfix'>";
	echo "<ul class='list'>";
	if($post_meta_info_arr)
	{	
		foreach($post_meta_info_arr as $key=> $post_meta_info)
		 {
			$activ = fetch_active_heading($key);
			$j=0;
			while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
				if($post->post_name != 'post_content' && $post->post_name != 'post_title' && $post->post_name != 'category' && $post->post_name != 'post_images' && $post->post_name != 'post_excerpt')
				{
					if($j==0){
						if($activ):
							if($key == '[#taxonomy_name#]'):
								echo '<div class="sec_title"><h3>'.$cur_post_type.__(' Information',DOMAIN).'</h3></div>';
							else:
								echo "<li><h3>".$key."</h3></li>";
							endif;
						endif;
						$j++;
					}
					if(isset($session[$post->post_name]) && $session[$post->post_name]!=""){
						if(get_post_meta($post->ID,"ctype",true) == 'multicheckbox')
						{
							foreach($session[$post->post_name] as $value)
							{
								$_value .= $value.",";	 
							}
							echo "<li><p>".$post->post_title.": ".substr($_value,0,-1)."</p></li>"; 
						}else
						{
		
							 echo "<li><p>".$post->post_title.": ".stripslashes($session[$post->post_name])."</p></li>";
						}
					}				
					if(get_post_meta($post->ID,"ctype",true) == 'upload')
					{
						$upload[] = $post->post_name;
					}
				}
			endwhile;
		}
	}
	echo "</ul>";
	echo "</div>";
}

/* Add action for preview map display */
add_action('templ_preview_address_map','templ_preview_address_map_display');

/*
 Display the post preview detail map
 */
function templ_preview_address_map_display()
{	
	$add_str = @$_POST['address'];
	$geo_latitude = $_POST['geo_latitude'];
	$geo_longitude = $_POST['geo_longitude'];
	$map_type=isset($_POST['map_view'])?$_POST['map_view']:'';		 
	if(isset($_POST['address']) && $geo_longitude &&  $geo_latitude)
	{ ?>
        <div class="row clearfix">
			<h3 class="submit_info_section"><span><?php _e('Map',DOMAIN); ?></span></h3>                
			<p><strong><?php _e('Location',DOMAIN); echo $add_str;?>: </strong></p>
			<div id="gmap" class="graybox img-pad">
				<?php  require_once (TEMPL_MONETIZE_FOLDER_PATH . 'templatic-custom_fields/google_map_detail.php');?>
			</div>
		</div>
        <?php		
	}
}
?>