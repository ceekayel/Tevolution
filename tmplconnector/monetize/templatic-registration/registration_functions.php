<?php
/*
Name : allow_autologin_after_reg
Description : Redirect on plugin dashboard after activating plugin
*/
function allow_autologin_after_reg()
{
  if (get_option('allow_autologin_after_reg') || get_option('allow_autologin_after_reg') == '')
  { 
	return true; 
  }else{
    return false;
  }
}
define('TT_REGISTRATION_FOLDER_PATH',TEMPL_MONETIZE_FOLDER_PATH.'templatic-registration/');
include_once(TT_REGISTRATION_FOLDER_PATH.'registration_main.php');
/* NAME : FETCH THE CURRENT USER
DESCRIPTION : THIS FUNCTION WILL FETCH THE CURRENT USER */
add_action('admin_init','user_role_assign');
function user_role_assign()
{
	global $current_user;
	$current_user = wp_get_current_user();
}
/* EOF - FETCH THE USER */
function fetch_user_custom_fields(){	
	global $wpdb,$custom_post_meta_db_table_name,$current_user,$form_fields_usermeta;
	
	$args = array(
				'post_type'       => 'custom_user_field',
				'post_status'     => 'publish',
				'numberposts'	   => -1,
				'meta_key'        => 'sort_order',
				'orderby'         => 'meta_value_num',
				'meta_value_num'  => 'sort_order',
				'order'           => 'ASC'
			);
	$custom_metaboxes_fields = get_posts( $args );
	if(isset($custom_metaboxes_fields) && $custom_metaboxes_fields != '')
	{
		$form_fields_usermeta_usermeta = array();
		foreach($custom_metaboxes_fields as $custom_metaboxes)
		{
			$name            = $custom_metaboxes->post_name;
			$site_title      = stripslashes($custom_metaboxes->post_title);
			$type            = get_post_meta($custom_metaboxes->ID,'ctype',true);
			$default_value   = get_post_meta($custom_metaboxes->ID,'default_value',true);
			$is_require      = get_post_meta($custom_metaboxes->ID,'is_require',true);
			$admin_desc      = $custom_metaboxes->post_content;
			$option_values   = get_post_meta($custom_metaboxes->ID,'option_values',true);
			$option_titles   = get_post_meta($custom_metaboxes->ID,'option_titles',true);
			$on_registration = get_post_meta($custom_metaboxes->ID,'on_registration',true);
			$on_profile      = get_post_meta($custom_metaboxes->ID,'on_profile',true);
			$on_author_page  = get_post_meta($custom_metaboxes->ID,'on_author_page',true);
			
			if(is_admin())
			{
				$label      = '<tr><th>'.$site_title.'</th>';
				$outer_st   = '<table class="form-table">';
				$outer_end  = '</table>';
				$tag_st     = '<td>';
				$tag_end    = '<span class="message_note">'.$admin_desc.'</span></td></tr>';
				$tag_before = '';
				$tag_after  = '';
			} else {
				$label      = $site_title;
				$outer_st   = '<div class="form_row clearfix">';
				$outer_end  = '</div>';
				$tag_st     = '';
				$tag_end    = '<span class="message_note">'.$admin_desc.'</span>';
				$tag_before = '';
				$tag_after  = '';
			}
			
			if($type == 'text')
			{
				$form_fields_usermeta[$name] = array("label"		        => $label,
												"type"		   => 'text',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="textfield"',
												"is_require"	   => $is_require,
												"outer_st" 	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
											);
			}
			if($type == 'head')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'head',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="head"',
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
											);
			}
			elseif($type == 'checkbox')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'checkbox',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="checkbox"',
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'textarea')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'textarea',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="textarea"',
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'texteditor')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'texteditor',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="mce"',
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => '<div class="clear">',
												"tag_after"       => '</div>',
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'select')
			{
				//$option_values=explode(",",$option_values );
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'select',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'"',
												"options"	        => $option_values,
												"option_titles"   => $option_titles,
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'radio')
			{
				//$option_values=explode(",",$option_values );
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'radio',
												"default"	        => $default_value,
												"extra"		   => '',
												"options"	        => $option_values,
												"option_titles"   => $option_titles,
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => '',
												"tag_after"       => '',
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'multicheckbox')
			{
				//$option_values=explode(",",$option_values );
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'multicheckbox',
												"default"	        => $default_value,
												"extra"		   => '',
												"options"	        =>  $option_values,
												"option_titles"   => $option_titles,
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => '<div class="form_cat">',
												"tag_after"       => '</div>',
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'date')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'date',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="textfield_date"',
												"is_require"	   => $is_require,
												"outer_st" 	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												//"tag_st"	   => '<img src="'.get_template_directory_uri().'/images/cal.gif" alt="Calendar"  onclick="displayCalendar(document.userform.'.$name.',\'yyyy-mm-dd\',this)" style="cursor: pointer;" align="absmiddle" border="0" class="calendar_img" />',
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'upload')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'upload',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" class="textfield"',
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'head')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'head',
												"outer_st"	   => '<h1 class="form_title">',
												"outer_end"	   => '</h1>',
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page
												);
			}
			elseif($type == 'geo_map')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => '',
												"type"		   => 'geo_map',
												"default"	        => $default_value,
												"extra"		   => '',
												"is_require"	   => $is_require,
												"outer_st"	   => '',
												"outer_end"	   => '',
												"tag_st"	        => '',
												"tag_end"	        => '',
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);		
			}
			elseif($type == 'image_uploader')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => '',
												"type"		   => 'image_uploader',
												"default"	        => $default_value,
												"extra"		   => '',
												"is_require"	   => $is_require,
												"outer_st"	   => '',
												"outer_end"	   => '',
												"tag_st"	        => '',
												"tag_end"	        => '',
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);		
			}
			
				
		}//finish foreach
		
		return $form_fields_usermeta;
	}//finish if condition
	
}
/*
name : add_author_box
description : add action to fetch author page fileds for author page */
add_action('author_box', 'add_author_box');
function add_author_box($content)
{	
	global $current_user,$wp_query,$wpdb;
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$qvar = $wp_query->query_vars;
	$author = $qvar['author'];
	if(isset($_POST['author_custom_post']))
	{	
		update_user_meta( $_POST['author_id'], 'author_custom_post', $_POST['author_custom_post'] ); 
	}
	if(isset($author) && $author !='') :
		$curauth = get_userdata($qvar['author']);
	else :
		$curauth = get_userdata(intval($_REQUEST['author']));
	endif;	
	
		global $form_fields_usermeta;
		/* Fetch the user custom fields */
		$form_fields_usermeta=fetch_user_custom_fields();
		
		$dirinfo = wp_upload_dir();
		$path = $dirinfo['path'];
		$url = $dirinfo['url'];
		$subdir = $dirinfo['subdir'];
		$basedir = $dirinfo['basedir'];
		$baseurl = $dirinfo['baseurl'];
		$profile_page_id=get_option('tevolution_profile');
		if(function_exists('icl_object_id')){
			$profile_page_id = icl_object_id($profile_page_id, 'page', false);
		}
		$profile_url=get_permalink($profile_page_id);
		?>
		
		<div class="author_cont">
		<div class="author_photo">
		<?php 
		
		do_action('tmpl_before_author_photo');
		if($form_fields_usermeta['profile_photo']['on_author_page']){
			if(get_user_meta($curauth->ID,'profile_photo',true) != ""){
				echo '<img src="'.get_user_meta($curauth->ID,'profile_photo',true).'" alt="'.$curauth->display_name.'" title="'.$curauth->display_name.'" />';
			}else{
				echo get_avatar($curauth->ID, apply_filters('tev_gravtar_size',32) ); 
			}
		}
		 
		 
			  $facebook=get_user_meta($curauth->ID,'facebook',true);
			  $twitter=get_user_meta($curauth->ID,'twitter',true);
			  $linkedin=get_user_meta($curauth->ID,'linkedin',true);
			  $email=get_user_meta($curauth->ID,'user_email',true);
			  $google=get_user_meta($curauth->ID,'user_google',true);
			  
			  if($facebook!='' || $twitter!='' || $linkedin!=''):
		   ?>
               <div class="author_social_networks social_media">
                   <ul class="social_media_list">
                   <?php 
				    /* give the author's social media contacts link */
					do_action('tmpl_social_media_list_start');
				    if($facebook!=''):  ?>
                    <li><a href="<?php echo (strstr($facebook,'http://'))?$facebook:'http://'.$facebook; ?>" target="_blank"><i class="fa fa-facebook" title="<?php _e("Facebook",DOMAIN);?>"></i></a></li>
                    <?php endif;
					
					if($twitter):?>
                    <li><a href="<?php echo (strstr($twitter,'http://'))?$twitter:'http://'.$twitter; ?>" target="_blank"><i class="fa fa-twitter" title="<?php _e("Twitter",DOMAIN);?>"></i></a></li>
                    <?php endif;
					
					if($google):?>
                    <li><a href="<?php echo (strstr($google,'http://'))?$google:'http://'.$google; ?>" target="_blank"><i class="fa fa-google-plus" title="<?php _e("Google Plus",DOMAIN);?>"></i></a></li>
                    <?php endif;
					
					if($linkedin):?>
                    <li><a href="<?php echo (strstr($linkedin,'http://'))?$linkedin:'http://'.$linkedin; ?>" target="_blank"><i class="fa fa-linkedin" title="<?php _e("LinkedIn",DOMAIN);?>"></i></a></li>
                    <?php endif;
					if($curauth->user_email && $form_fields_usermeta['user_email']['on_author_page'] == 1) { ?>
					<li><a href="mailto:<?php echo antispambot($curauth->user_email); ?>" target="_blank"><i class="fa fa-envelope-o" title="<?php _e("Contact Me",DOMAIN);?>"></i></a></li>
					<?php } 
					do_action('tmpl_social_media_list_end');
					/* give the author's social media contacts link end */
					?>
                   </ul>
               </div>
          <?php endif;
		   if($current_user->ID == $curauth->ID)
		  { ?>
		  <div class="editProfile"><a href="<?php echo $profile_url; ?>" ><?php _e('Edit Profile',DOMAIN);?> </a> </div>
		  <?php } 
		  do_action('tmpl_after_author_photo');
		  ?>
		</div>
		<div class="right_box">
		<h2><?php echo $curauth->display_name; ?></h2>
		<div class="user_dsb_cf">
		<?php 
			if(get_user_meta($curauth->ID,'Country',true) && $form_fields_usermeta['Country']['on_author_page'] == 1){  ?>
            <p><label><?php _e('Country',DOMAIN); ?>: </label><span><?php echo get_user_meta($curauth->ID,'Country',true); ?></span></p>
		<?php } 
		 if(is_array($form_fields_usermeta) && !empty($form_fields_usermeta)){
			 foreach($form_fields_usermeta as $key=> $_form_fields_usermeta)
			  {
				  if(function_exists('icl_register_string')){
						icl_register_string(DOMAIN,$_form_fields_usermeta['label'],$_form_fields_usermeta['label']);
						$_form_fields_usermeta['label'] = icl_t(DOMAIN,$_form_fields_usermeta['label'],$_form_fields_usermeta['label']);
					}
				if($_form_fields_usermeta['type']=='head' && $_form_fields_usermeta['on_author_page']==1):
					echo '<h2>'. $_form_fields_usermeta['label'].'</h2>';
				endif;
		
				if(get_user_meta($curauth->ID,$key,true) != "" && $key !='facebook' && $key !='display_name' && $key !='user_google' && $key !='twitter' && $key !='twitter' && $key!= 'linkedin' && $key!= 'user_email' && $key!= 'profile_photo' && $key!= 'Country'): 
					if($_form_fields_usermeta['on_author_page']): 
						if($curauth->ID != $current_user->ID && $key == 'user_fname')
						{
							continue;
						}
						if($_form_fields_usermeta['type']=='multicheckbox' || $_form_fields_usermeta['type']=='radio' || $_form_fields_usermeta['type']=='select'):  ?>
							<?php
								$checkbox = '';
								$option_values=explode(",",$_form_fields_usermeta['options']);
								$option_titles=explode(",",$_form_fields_usermeta['option_titles']);
								for($i=0;$i<count($option_titles);$i++){
									if(in_array($option_values[$i],get_user_meta($curauth->ID,$key,true)) || get_user_meta($curauth->ID,$key,true) == $option_values[$i]){
										if($option_titles[$i]!=""){
											$checkbox .= $option_titles[$i].',';
										}else{
											$checkbox .= $option_values[$i].',';
										}
									}
								}								
								?>
								<p><label><?php echo $_form_fields_usermeta['label']; ?>:</label><span><?php echo substr($checkbox,0,-1); ?></span></p>
                         <?php elseif($_form_fields_usermeta['type']=='upload'): ?>
                         	<p><label  style="vertical-align:top;"><?php echo $_form_fields_usermeta['label'].": "; ?></label> <img src="<?php echo get_user_meta($curauth->ID,$key,true);?>" /></p>
					<?php 
						else:
					?>
								<p>
									<label><?php echo $_form_fields_usermeta['label']; ?>:</label>
									<span><?php 
											if( $key == 'url' ){
												echo '<a href="'.get_user_meta($curauth->ID,$key,true).'" title="'.get_user_meta($curauth->ID,$key,true).'">'.get_user_meta($curauth->ID,$key,true).'</a>';
											}else{
												echo stripslashes(get_user_meta($curauth->ID,$key,true)); 
											}
										?>
									</span>
								</p>
								
					<?php endif;// finish check multi checkbox condition					
					endif;// finish the on author page condition				
				endif;//finish key is not blank
			  }
		  }
		  
		 /* get the total post countings */
		 if($curauth->ID): 
			 $posttaxonomy = get_option("templatic_custom_post");
			 $posttaxonomy = implode(',',array_keys($posttaxonomy));

			@$post_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = '" . $curauth->ID . "' AND post_type IN('$posttaxonomy') AND post_status = 'publish'");				

			 ?>
			 <p>
				<label><?php echo _e('Total Listing',DOMAIN);?>: </label><span class="i_agent_others"> <b><?php echo $post_count;?></b></span>
			 </p>
			 <?php
		 endif;
		 
		/* payment type details */
		$posttaxonomy = get_option("templatic_custom_post");

			$price_pkg = get_user_meta($curauth->ID,'package_selected',true);
			$pagd_data = get_post($price_pkg);
			$package_name = $pagd_data->post_title;
			$types = get_post_types();
		
			$ptypes = implode(',',$types);
			$ptypes = explode(',',$ptypes);
			$pkg_post_type = get_post_meta($price_pkg,'package_post_type',true);
			$pkg_post_types = explode(',',$pkg_post_type);
			$pkg_post_type1='';
				for($c=0; $c < count($pkg_post_types); $c++){
					if(in_array($pkg_post_types[$c],$ptypes)){
						$pkg_post_type1 .=ucfirst($pkg_post_types[$c]).",";
					}
				}
			$pkg_type = get_post_meta($price_pkg,'package_type',true);
			$limit_no_post = get_post_meta($price_pkg,'limit_no_post',true);
			
			$submited =get_user_meta($curauth->ID,'total_list_of_post',true);
			if(!$submited)
				$submited =0;
			$remaining = intval($limit_no_post) - intval($submited);
			if($pkg_type == 2 && $current_user->ID != '' && $curauth->ID == $current_user->ID){
				echo "<div class='pkg_info'>";
				
					_e('You have subscribed to',DOMAIN);
					echo " <b>".$package_name."</b> ";
					_e('price package for',DOMAIN);
					echo "<b> ".rtrim($pkg_post_type1,',')." </b>"; 
					_e('Total number of posts:',DOMAIN);
					echo "<b> ".$limit_no_post."</b>, "; 
					_e('Submited:',DOMAIN);
					echo '<b> '.$submited.', </b>';
					_e('Remaining:',DOMAIN);
					echo '<b> '.$remaining.' </b>';
				
				echo ".</div>";
			
			}
		
		 ?>
		</div>
			<div class="clearfix"></div>
           <?php do_action('author_box_content');  ?>
           
		  </div>
		  <?php $i=0;  
				$author_link=apply_filters('templ_login_widget_dashboardlink_filter',get_author_posts_url($curauth->ID));
				if(strpos($author_link, "?"))
					$author_link=apply_filters('templ_login_widget_dashboardlink_filter',get_author_posts_url($curauth->ID))."&";
				else
					$author_link=apply_filters('templ_login_widget_dashboardlink_filter',get_author_posts_url($curauth->ID))."?";
					
					
			$obj = get_post_type_object( 'post' );			
			$activetab=(isset($_REQUEST['custom_post']) && 'post'== $_REQUEST['custom_post']) ?'nav-author-post-tab-active':'';
		  ?>
			<div class="author_post_tabs">
				<div class="author_custom_post_wrapper">
				<ul>  
				<?php
				$posttaxonomy = get_option("templatic_custom_post");		  
				foreach($posttaxonomy as $key=>$_posttaxonomy):					
				?>            	
				<?php   $active_tab=(isset($_REQUEST['custom_post']) && $key==$_REQUEST['custom_post']) ?'nav-author-post-tab-active':'';
						if($active_tab=="" && !isset($_REQUEST['custom_post']))
						{
							if($i==0)
							{
								$active_tab ='nav-author-post-tab-active';						
								$custom_post_type=$key;
								$i++;
							}
						}
						if(function_exists('icl_register_string')){
							icl_register_string(DOMAIN,$_posttaxonomy['label'].'author',$_posttaxonomy['label']);
							$_posttaxonomy['label'] = icl_t(DOMAIN,$_posttaxonomy['label'].'author',$_posttaxonomy['label']);
						}
					?>
					<li><a href="<?php echo $author_link;?>custom_post=<?php  echo $key;?>" class="author_post_tab <?php echo $active_tab;?>"><?php echo $_posttaxonomy['labels']['menu_name']; ?></a></li>                    
				<?php  endforeach; ?>
						<li><a href="<?php echo $author_link;?>custom_post=post" class="author_post_tab <?php echo $activetab;?>"><?php echo $obj->labels->singular_name;?></a></li>				
				<?php do_action('tevolution_author_tab');?>
                </ul>
			 </div>
			</div>        
				<?php				
					global $wp_query;
					if(isset($_REQUEST['custom_post']) && $_REQUEST['custom_post']!="")
						$post_type=$_REQUEST['custom_post'];
					else
						$post_type=$custom_post_type;
					
					$posts_per_page=get_option('posts_per_page');
					//echo $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					$args=array(
							'post_type'  =>$post_type,
							'author'=>$curauth->ID,
							'post_status' => array('publish','draft'),
							'posts_per_page' =>$posts_per_page,
							'paged'=>$paged,
							'order_by'=>'date',
							'order' => 'DESC'
						);						
					query_posts( $args );					
				do_action('tevolution_author_query');	
				?>      
		  <?php  
		  
}

/*
 * Function Name: tevolution_author_pre_get_posts
 * remove tevolution author pre get posts action in author wp_head action
 */
add_action('wp_head','tevolution_author_pre_get_posts');
function tevolution_author_pre_get_posts(){
	if(is_author()){
		remove_action('pre_get_posts','tevolution_author_post');
	}
}
add_action('pre_get_posts','tevolution_author_post');
function tevolution_author_post($query){
	
	if(!is_admin()){
		if((is_author() || (isset($_REQUEST['custom_post']) && $_REQUEST['custom_post']!=''))){
			global $current_user;
			$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
			$i=0;			
			$posttaxonomy = get_option("templatic_custom_post");	
			if(!empty($posttaxonomy)){
				foreach($posttaxonomy as $key=>$_posttaxonomy):
					$active_tab=(isset($_REQUEST['custom_post']) && $key==$_REQUEST['custom_post']) ?'1':'';
					if($active_tab=="" && !isset($_REQUEST['custom_post']))
					{
						if($i==0)
						{
							$custom_post_type=$key;
							$i++;
						}
					}
				endforeach;
				
				if(isset($_REQUEST['custom_post']) && $_REQUEST['custom_post']!="")
					$post_type=$_REQUEST['custom_post'];
				else
					$post_type=$custom_post_type;
				$query->set('post_type',array($post_type));
				if($author->ID == $current_user->ID)
				{
					$query->set('post_status', array('publish','draft','private'));
				}
				else
				{
					$query->set('post_status', array('publish'));
				}
			}
		}
	}	
}
/*
Name : templ_fetch_registration_onsubmit
Desc : fecth login and registration form in submit page template
*/
function templ_fetch_registration_onsubmit(){
	if($_SESSION['custom_fields']['login_type'])
	{
		$user_login_or_not = $_SESSION['custom_fields']['login_type'];
	}
	if((isset($_SESSION['user_email']) && $_SESSION['user_email']!='') || (isset($_REQUEST['backandedit']) && $_REQUEST['backandedit'] == 1))
	{
		$user_login_or_not = 'new_user';
	}
  ?>
	<div id="login_user_meta" <?php if($user_login_or_not=='new_user'){ echo 'style="display:block;"';}else{ echo 'style="display:none;"'; }?> >
		<input type="hidden" name="user_email_already_exist" id="user_email_already_exist" value="<?php if($_SESSION['custom_fields']['user_email_already_exist']) { echo "1"; } ?>" />
		<input type="hidden" name="user_fname_already_exist" id="user_fname_already_exist" value="<?php if($_SESSION['custom_fields']['user_fname_already_exist']) { echo "1"; } ?>" />
		<input type="hidden" name="login_type" id="login_type" value="<?php echo $_SESSION['custom_fields']['login_type']; ?>" />
		<input type="hidden" name="reg_redirect_link" value="<?php echo apply_filters('tevolution_register_redirect_to',@$_SERVER['HTTP_REFERER']);?>" />
	    <?php
			$user_meta_array = user_fields_array();
			display_usermeta_fields($user_meta_array);/* fetch registration form */
			include_once(TT_REGISTRATION_FOLDER_PATH . 'registration_validation.php');
		?>
	</div>
<?php
}
/*
Name : templ_fecth_login_onsubmit
Desc : fecth login form in submit page template
*/
function templ_fecth_login_onsubmit(){ ?>
<p style="display:none;" class="status"></p>
	<div class="login_submit clearfix" id="loginform">
	  <div class="sec_title">
		<h3 class="form_title spacer_none"><?php _e('Login or register',DOMAIN);?></h3>
	  </div>
		<?php 
		
		if($_SESSION['custom_fields']['login_type'])
		{
			$user_login_or_not = $_SESSION['custom_fields']['login_type'];
		}
		if(isset($_REQUEST['usererror'])==1)
		{
			if(isset($_SESSION['userinset_error']))
			{
				for($i=0;$i<count($_SESSION['userinset_error']);$i++)
				{
					echo '<div class="error_msg">'.$_SESSION['userinset_error'][$i].'</div>';
				}
				echo "<br>";
			}
		}
		?>   
	  <?php if(isset($_REQUEST['emsg'])==1): ?>
		<div class="error_msg"><?php _e('Invalid Username/Password.',DOMAIN);?></div>
	  <?php endif; ?>
	  <div class="user_type clearfix">
		<label class="lab1"><?php _e('I am a',DOMAIN);?> </label>
		<label class="radio_lbl"><input name="user_login_or_not" type="radio" value="existing_user" <?php if($user_login_or_not=='existing_user'){ echo 'checked="checked"';}else{ echo 'checked="checked"'; }?> onclick="set_login_registration_frm('existing_user');" /> <?php _e('Existing User',DOMAIN);?> </label>
		<?php 
			$users_can_register = get_option('users_can_register');
			if($users_can_register):
		?>
		<label class="radio_lbl"><input name="user_login_or_not" type="radio" value="new_user" <?php if($user_login_or_not=='new_user'){ echo 'checked="checked"';}?> onclick="set_login_registration_frm('new_user');" /> <?php _e('New User? Register Now',DOMAIN);?> </label>
		<?php endif;?>
	  </div>
	  <form name="loginform" class="sublog_login" <?php if($user_login_or_not=='existing_user' || $user_login_or_not == '' ){ ?> style="display:block;" <?php } else {  ?> style="display:none;" <?php }?>  id="login_user_frm_id" action="" method="post" >
		  <div class="form_row clearfix lab2_cont">
			<label class="lab2"><?php _e('Login',DOMAIN);?><span class="required">*</span></label>
			<input type="text" class="textfield slog_prop " id="user_login" name="log" />
		  </div>
		  
		   <div class="form_row learfix lab2_cont">
			<label class="lab2"><?php _e('Password',DOMAIN);?><span class="required">*</span> </label>
			<input type="password" class="textfield slog_prop" id="user_pass" name="pwd" />
		  </div>
		  
		  <div class="form_row clearfix">
		  <input name="submit" type="submit" value="<?php _e('Login',DOMAIN);?>" class="button_green submit" />
		  </div>
			   <?php do_action('login_form');?>
		  <?php	$login_redirect_link = get_permalink();?>
		  <input type="hidden" name="redirect_to" value="<?php echo $login_redirect_link; ?>" />
		  <input type="hidden" name="testcookie" value="1" />
		  <input type="hidden" name="pagetype" value="<?php echo $login_redirect_link; ?>" />
		  <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
	  </form>
    </div>
<?php
	add_action('wp_footer','submit_form_ajax_login'); // call a function for ajax login.
} 

/*
Name : submit_form_ajax_login
Desc : user login script while submit form
*/
function submit_form_ajax_login()
{
	?>
	<script>
		jQuery(document).ready(function($) {
			var redirecturl = '<?php echo $_SESSION['redirect_to']; ?>';
			// Perform AJAX login on form submit
			$('form#login_user_frm_id').on('submit', function(e){
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: ajaxUrl,
					data: { 
						'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
						'username': $('form#login_user_frm_id #user_login').val(), 
						'password': $('form#login_user_frm_id #user_pass').val(), 
						'security': $('form#login_user_frm_id #security').val() },
					success: function(data){
						if(data.loggedin)
						{
							$('p.status').css('display','block');
							$('p.status').text(data.message);
							if(data.package_type == 2)
							{
								document.location.href = redirecturl;
							}
							$('#loginform').css('display','none');
							$( "#login_user_meta" ).remove();
						}
						else
						{
							$('p.status').css('display','block');
							$('p.status').css('color','red');
							$('p.status').text(data.message);
						}
					}
				});
				e.preventDefault();
			});
		});
	</script>
	<?php
}
/*
Name : ajax_login
Desc : check username and password while login from submit form.
*/
add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
function ajax_login(){
	header('Content-Type: application/json; charset=utf-8');
    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon( $info, false );
    $package_selected = '';
    $package_type = '';
    $package_selected = get_user_meta($user_signon->data->ID,'package_selected',true);
    $tmpdata = get_option('templatic_settings');
    if(@$package_selected)
    {
		$package_type = get_post_meta($package_selected,'package_type',true);
	}
	$username = ucfirst($user_signon->data->display_name);
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.',DOMAIN)));
    } else {
		if($tmpdata['templatic-category_custom_fields'] == 'No')
		{
			echo json_encode(array('loggedin'=>true, 'message'=>sprintf(__('Welcome %s, select your category and add listing details.',DOMAIN),$username),'package_type'=>$package_type));
		}
		elseif($tmpdata['templatic-category_custom_fields'] == 'Yes')
		{
			echo json_encode(array('loggedin'=>true, 'message'=>sprintf(__('Welcome %s, select your category and add listing details on the next page.',DOMAIN),$username),'package_type'=>$package_type));
		}
    }

    die();
}
/*
Name : templ_insertuser_with_listing
Desc : return page to insert user
*/
function templ_insertuser_with_listing(){
	include_once(TEMPL_REGISTRATION_FOLDER_PATH.'single_page_checkout_insertuser.php');	
	return $current_user_id;
}
/*
Name : fetch_user_registration_fields
Desc : return user custom fields for register or profile page.
*/
function fetch_user_registration_fields($validate,$user_id='')
{
	global $form_fields_usermeta,$user_validation_info,$current_user;
	/* Fetch the user custom fields */
	$form_fields_usermeta=fetch_user_custom_fields();
	$user_validation_info = array();	
	if($form_fields_usermeta){
	foreach($form_fields_usermeta as $key=>$val)
	{
		
		if($validate == 'register')
			$validate_form = $val['on_registration'];
		else
			$validate_form = $val['on_profile'];
			
		if($validate_form){
        $str = ''; $fval = '';
        $field_val = $key.'_val';
		
        if(isset($field_val) && $field_val){ $fval = $field_val; }else{ $fval = $val['default']; }
      
        if($val['is_require'])
        {
            $user_validation_info[] = array(
                                       'name'	=> $key,
                                       'espan'	=> $key.'_error',
                                       'type'	=> $val['type'],
                                       'text'	=> $val['label'],
                                       );
        }
		
		if($key)
		{
			if($user_id != '' )
			{
				$fval = get_user_meta($user_id,$key,true);
			}
			else
			{
				$fval = get_user_meta($current_user->ID,$key,true);
			}
		}
		
        if($val['type']=='text')
        {
			if(!(is_templ_wp_admin() && ( $key == 'user_email' || $key == 'user_fname' || $key == 'display_name'))) /* CONDITION FOR EMAIL AND USER NAME FIELD */
			{
				if($key=='user_email')
				{
					$fval=($fval=='')?$current_user->user_email: $fval;
					
				}
				
				if($key=='user_fname')
				{
					if($validate != 'register')
					{					
						$readonly = 'readonly="readonly"';
						$background_color = 'style="background-color:#EEEEEE"';
					}
					$fval=($fval=='')?$current_user->user_login: $fval;
				}
				if($key=='display_name')
				{
					$fval=($fval=='')?$current_user->display_name: $fval;
					
				}
				$str = '<input '.@$readonly.' name="'.$key.'" type="text" '.$val['extra'].' '.@$background_color.' value="'.$fval.'">';
				$readonly = '';
				$background_color = '';
				if($val['is_require'])
				{
					$str .= '<span id="'.$key.'_error"></span>';
				}
			}
        }elseif($val['type']=='hidden')
        {
            $str = '<input name="'.$key.'" type="hidden" '.$val['extra'].' value="'.$fval.'">';	
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='textarea')
        {
            $str = '<textarea name="'.$key.'" '.$val['extra'].'>'.$fval.'</textarea>';	
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='file')
        {
            $str = '<input name="'.$key.'" type="file" '.$val['extra'].' value="'.$fval.'">';
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='include')
        {
            $str = @include_once($val['default']);
        }else
        if($val['type']=='head')
        {
            $str = '';
        }else
        if($val['type']=='date')
        {
			?>
            <script type="text/javascript">	
				jQuery(function(){
				var pickerOpts = {
						showOn: "both",
						dateFormat: 'yy-mm-dd',
						//buttonImage: "<?php echo TEMPL_PLUGIN_URL; ?>css/datepicker/images/cal.png",
						buttonText: '<i class="fa fa-calendar"></i>',
						monthNames: objectL11tmpl.monthNames,
						monthNamesShort: objectL11tmpl.monthNamesShort,
						dayNames: objectL11tmpl.dayNames,
						dayNamesShort: objectL11tmpl.dayNamesShort,
						dayNamesMin: objectL11tmpl.dayNamesMin,
						isRTL: objectL11tmpl.isRTL,
						onChangeMonthYear: function(year, month, inst) {
							jQuery("#<?php echo $key;?>").blur();
						},
						onSelect: function(dateText, inst) {
						   //jQuery("#<?php echo $key;?>").focusin();
							jQuery("#<?php echo $key;?>").blur();
						}
					};	
					jQuery("#<?php echo $key;?>").datepicker(pickerOpts);					
				});
			</script>
            <?php
            $str = '<input name="'.$key.'" id="'.$key.'" type="text" '.$val['extra'].' value="'.$fval.'">';				
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='catselect')
        {
            $term = get_term( (int)$fval, CUSTOM_CATEGORY_TYPE1);
            $str = '<select name="'.$key.'" '.$val['extra'].'>';
            $args = array('taxonomy' => CUSTOM_CATEGORY_TYPE1);
            $all_categories = get_categories($args);
            foreach($all_categories as $key => $cat) 
            {
            
                $seled='';
                if($term->name==$cat->name){ $seled='selected="selected"';}
                $str .= '<option value="'.$cat->name.'" '.$seled.'>'.$cat->name.'</option>';	
            }
            $str .= '</select>';
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='catdropdown')
        {
            $cat_args = array('name' => 'post_category', 'id' => 'post_category_0', 'selected' => $fval, 'class' => 'textfield', 'orderby' => 'name', 'echo' => '0', 'hierarchical' => 1, 'taxonomy'=>CUSTOM_CATEGORY_TYPE1);
            $cat_args['show_option_none'] = __('Select Category',DOMAIN);
            $str .=wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='select')
        {
			 $option_values_arr = explode(',', $val['options']);
			 $option_titles_arr = explode(',',$val['option_titles']);
			 if (function_exists('icl_register_string')) {		
				icl_register_string(DOMAIN, $val['option_titles'].'_'.$key,$val['option_titles']);	
				$option_titles_arr = icl_t(DOMAIN, $val['option_titles'].'_'.$key,$val['option_titles']);
		   }
            $str = '<select name="'.$key.'" '.$val['extra'].'>';
			 $str .= '<option value="" >'.PLEASE_SELECT.'</option>';	
            for($i=0;$i<count($option_values_arr);$i++)
            {
                $seled='';
                
                if($fval==$option_values_arr[$i]){ $seled='selected="selected"';}
                $str .= '<option value="'.$option_values_arr[$i].'" '.$seled.'>'.$option_titles_arr[$i].'</option>';	
            }
            $str .= '</select>';
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='catcheckbox')
        {
            $fval_arr = explode(',',$fval);
            $str .= $val['tag_before'].get_categories_checkboxes_form(CUSTOM_CATEGORY_TYPE1,$fval_arr).$oval.$val['tag_after'];
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='catradio')
        {
            $args = array('taxonomy' => CUSTOM_CATEGORY_TYPE1);
            $all_categories = get_categories($args);
            foreach($all_categories as $key1 => $cat) 
            {
                
                
                    $seled='';
                    if($fval==$cat->term_id){ $seled='checked="checked"';}
                    $str .= $val['tag_before'].'<input name="'.$key.'" type="radio" '.$val['extra'].' value="'.$cat->name.'" '.$seled.'> '.$cat->name.$val['tag_after'].'</div>';
                
            }
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='checkbox')
        {
            if($fval){ $seled='checked="checked"';}
            $str = '<input name="'.$key.'" type="checkbox" '.$val['extra'].' value="1" '.$seled.'>';
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='upload')
        {
			$str = '<input name="'.$key.'" type="file" '.@$val['extra'].' '.@$uclass.' value="'.$fval.'" > ';
			if($fval!=''){
				$str .='<br/><img src="'.$fval.'"  width="125px" height="125px" alt="" />
				<br />
				<input type="hidden" name="prev_upload" value="'.$fval.'" />
				';	
			}
			if($val['is_require'])
			{
				$str .='<span id="'.$key.'_error"></span>';	
			}
        }
        else
        if($val['type']=='radio')
        {
            $options = $val['options'];
		  $option_titles = $val['option_titles'];	
		  if (function_exists('icl_register_string')) {		
				icl_register_string(DOMAIN, $val['option_titles'].'_'.$key,$val['option_titles']);	
				$option_titles = icl_t(DOMAIN, $val['option_titles'].'_'.$key,$val['option_titles']);
		   }
            if($options)
            {
			  $chkcounter = 0;
                $option_values_arr = explode(',',$options);
			 $option_titles_arr = explode(',',$option_titles);
			 $str='<div class="form_cat_left hr_input_radio">';
                for($i=0;$i<count($option_values_arr);$i++)
                {
                    $seled='';
				$chkcounter++;
                    if($fval==$option_values_arr[$i]){$seled='checked="checked"';}
                    $str .= '<div class="form_cat">'.$val['tag_before'].'<label for="'.$key.'_'.$chkcounter.'"><input id="'.$key.'_'.$chkcounter.'" name="'.$key.'" type="radio" '.$val['extra'].'  value="'.$option_values_arr[$i].'" '.$seled.'> '.$option_titles_arr[$i].$val['tag_after']."</label>".'</div>';
                }
                if($val['is_require'])
                {
                    $str .= '<span id="'.$key.'_error"></span>';	
                }
			$str.="</div>";
            }
        }else
        if($val['type']=='multicheckbox')
        {
            $options = $val['options'];
		  $option_titles = $val['option_titles'];		  
		    if (function_exists('icl_register_string')) {		
				icl_register_string(DOMAIN, $val['option_titles'].'_'.$key,$val['option_titles']);	
				$option_titles = icl_t(DOMAIN, $val['option_titles'].'_'.$key,$val['option_titles']);
		   }
            if($options)
            {  
				$chkcounter = 0;
                $option_values_arr = explode(',',$options);
			 $option_titles_arr = explode(',',$option_titles);
			 $str='<div class="form_cat_left hr_input_multicheckbox">';
                for($i=0;$i<count($option_values_arr);$i++)
                {
                    $chkcounter++;
                    $seled='';
           // 		$fval_arr = explode(',',$fval);
					if($fval)
					{
				   		if(in_array($option_values_arr[$i],$fval)){ $seled='checked="checked"';}
					}
                    $str .= $val['tag_before'].'<label for="'.$key.'_'.$chkcounter.'"><input name="'.$key.'[]"  id="'.$key.'_'.$chkcounter.'" type="checkbox" '.$val['extra'].' value="'.$option_values_arr[$i].'" '.$seled.'> '.$option_titles_arr[$i]."</label>".$val['tag_after'];
                }
                if($val['is_require'])
                {
                    $str .= '<span id="'.$key.'_error"></span>';	
                }
			 $str.="</div>";
            }
        }
        else
        if($val['type']=='packageradio')
        {
            $options = $val['options'];
            foreach($options as $okey=>$oval)
            {
                $seled='';
                if($fval==$okey){$seled='checked="checked"';}
                $str .= $val['tag_before'].'<input name="'.$key.'" type="radio" '.$val['extra'].' value="'.$okey.'" '.$seled.'> '.$oval.$val['tag_after'];	
            }
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='geo_map')
        {
            do_action('templ_submit_form_googlemap');	
        }else
        if($val['type']=='image_uploader')
        {
            do_action('templ_submit_form_image_uploader');	
        }
	   
	   if (function_exists('icl_register_string')) {		
			icl_register_string(DOMAIN, $val['type'].'_'.$key,$val['label']);	
			$val['label'] = icl_t(DOMAIN, $val['type'].'_'.$key,$val['label']);
	   }
        if($val['is_require'] && !is_admin())
        {
            $label = '<label>'.$val['label'].' <span class="indicates">*</span> </label>';
        }
		elseif($val['is_require'] && is_admin())
        {
           $label = '<label> <span class="indicates">*</span> </label>';
        }
		elseif(is_admin())
        {
            $label = '';
        }elseif($val['type']=='head'){
		  $label = '<h3>'.$val['label'].'</h3>'; 
	   }else
        {
            $label = '<label>'.$val['label'].'</label>';
        }
		if(!(is_templ_wp_admin() && ( $key == 'user_email' || $key == 'user_fname' || $key == 'description'))) /* CONDITION FOR EMAIL AND USER NAME FIELD */
		{			
			if($val['type']=='texteditor')
			{
				echo $val['outer_st'].$label.$val['tag_st'];
				 echo $val['tag_before'].$val['tag_after'];
            // default settings
					$settings =   array(
						'wpautop' => false, // use wpautop?
						'media_buttons' => false, // show insert/upload button(s)
						'textarea_name' => $key, // set the textarea name to something different, square brackets [] can be used here
						'textarea_rows' => '10', // rows="..."
						'tabindex' => '',
						'editor_css' => '<style>.wp-editor-wrap{width:640px !important;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
						'editor_class' => '', // add extra class(es) to the editor textarea
						'teeny' => false, // output the minimal editor config used in Press This
						'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
						'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
						'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
					);				
					if(isset($fval) && $fval != '') 
					{  $content=$fval; }
					else{$content= $fval; } 				
					wp_editor( $content, $key, $settings);				
			
					if($val['is_require'])
					{
						$str .= '<span id="'.$key.'_error"></span>';	
					}
				echo $str.$val['tag_end'].$val['outer_end'];
			}else{	
				if(is_admin())
					echo $val['outer_st'].$val['label'].$val['tag_st'].$str.$val['tag_end'].$val['outer_end'];
				else
					echo $val['outer_st'].$label.$val['tag_st'].$str.$val['tag_end'].$val['outer_end'];
			}
        }
		}
	}
	}
}
/* NAME : CUSTOMIZE USER DASHBOARD IN BACKEND
DESCRIPTION : THIS FUNCTION WILL ADD USER CUSTOM FIELDS ON DASHBOARD */
add_action('show_user_profile', 'add_extra_profile_fields'); /* CALL A FUNCTION */
function add_extra_profile_fields( $user )
{
	$user_id = $user->ID;
	fetch_user_registration_fields( 'profile',$user_id ); /* CALL A FUNCTION TO DISPLAY CUSTOM FIELDS */
}
add_action('edit_user_profile', 'add_extra_profile_fields');
/* NAME : SAVE CUSTOM FIELDS FROM BACKEND
DESCRIPTION : THIS FUNCTION WILL SAVE CUSTOM FIELD DATA DISPLAYING ON PROFILE PAGE IN BACKEND */
add_action('personal_options_update', 'update_extra_profile_fields'); /* CALL A FUNCTION */
function update_extra_profile_fields( $user_id )
{
	global $upload_folder_path;
		global $form_fields_usermeta;
		fetch_user_custom_fields();
	//	$custom_metaboxes = templ_get_usermeta_plugin();
		foreach($form_fields_usermeta as $fkey=>$fval)
		{
			$fldkey = "$fkey";
			$$fldkey = $_POST["$fkey"];
			if($fval['type']=='upload')
			{
				if($_FILES[$fkey]['name'] && $_FILES[$fkey]['size']>0) {
					$dirinfo = wp_upload_dir();
					$path = $dirinfo['path'];
					$url = $dirinfo['url'];
					$destination_path = $path."/";
					$destination_url = $url."/";
					
					$src = $_FILES[$fkey]['tmp_name'];
					$file_ame = date('Ymdhis')."_".$_FILES[$fkey]['name'];
					$target_file = $destination_path.$file_ame;
					if(move_uploaded_file($_FILES[$fkey]["tmp_name"],$target_file))
					{
						$image_path = $destination_url.$file_ame;
					}else
					{
						$image_path = '';	
					}					
					$_POST[$fkey] = $image_path;
					$fldkey = $image_path;
					update_user_meta($user_id, $fkey, $fldkey);	
				}
				else{
					$_POST[$fkey]=$_POST['prev_upload'];
				}
			}
			else
				update_user_meta($user_id, $fkey, $$fldkey); // User Custom Metadata Here
		}
	/*foreach( $_POST as $key => $val )
	{
		update_user_meta($user_id, $key, $val);
	}		*/
}
add_action( 'edit_user_profile_update', 'update_extra_profile_fields' ); /* UPDATE ANOTHER USER'S DATA */
function modify_form(){
echo  '<script type="text/javascript">
      jQuery("#your-profile").attr("enctype", "multipart/form-data");
        </script>
  ';
}
add_action('admin_footer','modify_form');
/*Convert special character as normal character */
function Unaccent($string)
{
    if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false)
    {
        $string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
    }
    return $string;
}
/*
 * Function Name: get_tevolution_login_permalink
 * Return: login permalink
 */
function get_tevolution_login_permalink(){
	
	$login_page_id=get_option('tevolution_login');
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') && function_exists('icl_object_id')){
		$login_page_id = icl_object_id( $login_page_id, 'page', false, ICL_LANGUAGE_CODE );
	 }
	return get_permalink($login_page_id);
}
/*
 * Function Name: get_tevolution_register_permalink
 * Return: resgiter permalink
 */
function get_tevolution_register_permalink(){
	
	$register_page_id=get_option('tevolution_register');
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') && function_exists('icl_object_id')){									
		$register_page_id = icl_object_id( $register_page_id, 'page', false, ICL_LANGUAGE_CODE );
	 }
	 if($register_page_id !='')
		return get_permalink($register_page_id);
}
/*
 * Function Name: get_tevolution_profile_permalink
 * Return: resgiter permalink
 */
function get_tevolution_profile_permalink(){
	
	$profile_page_id=get_option('tevolution_profile');
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') && function_exists('icl_object_id')){
		$profile_page_id = icl_object_id( $profile_page_id, 'page', false, ICL_LANGUAGE_CODE );
	 }
	return get_permalink($profile_page_id);
}
add_action('wp_ajax_user_customfield_sort','tevolution_user_customfield_sort');
function tevolution_user_customfield_sort(){
	
	$user_id = get_current_user_id();		
	if(isset($_REQUEST['paging_input']) && $_REQUEST['paging_input']!=0 && $_REQUEST['paging_input']!=1){
		$custom_fields_per_page=get_user_meta($user_id,'user_custom_fields_per_page',true);
		$j =$_REQUEST['paging_input']*$custom_fields_per_page+1;
		$test='';
		$i=$custom_fields_per_page;		
		for($j; $j >= count($_REQUEST['user_field_sort']);$j--){			
			if($_REQUEST['user_field_sort'][$i]!=''){
				update_post_meta($_REQUEST['user_field_sort'][$i],'sort_order',$j);	
			}
			$i--;	
		}
	}else{
		$j=1;
		for($i=0;$i<count($_REQUEST['user_field_sort']);$i++){
			update_post_meta($_REQUEST['user_field_sort'][$i],'sort_order',$j);		
			$j++;
		}
	}	
	exit;
}
?>
