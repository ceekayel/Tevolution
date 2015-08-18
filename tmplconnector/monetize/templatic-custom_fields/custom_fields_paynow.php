<?php 
ob_start();
if(isset($_SESSION['file_info']) &&($_SESSION['file_info']=='' || empty($_SESSION['file_info'][0]))){
	$_SESSION['file_info']= explode(',',$_SESSION['custom_fields']['imgarr']);
}
global $wpdb,$last_postid,$payable_amount;
global $current_user;
$current_user = wp_get_current_user();
$current_user_id = $current_user->ID;

/* fetch package information if monetization is activated */
if(is_active_addons('monetization') && class_exists('monetization')){
	global $monetization;
	$listing_price_info = $monetization->templ_get_price_info($_SESSION['custom_fields']['package_select'],$_SESSION['custom_fields']['total_price']);
	$listing_price_info = $listing_price_info[0];
	$payable_amount = $_SESSION['custom_fields']['total_price'];
	/* calculate total amout with coupon */
	if($_SESSION['custom_fields']['add_coupon'])
	{
		$payable_amount = get_payable_amount_with_coupon_plugin($payable_amount,$_SESSION['custom_fields']['add_coupon']);
	}
	/* redirect on preview page if monetization active + no payment method selected */
	if($_REQUEST['pid']=='' && isset($_REQUEST['paymentmethod']) && $_REQUEST['paymentmethod'] == '' && $payable_amount > 0)
	{
		wp_redirect(get_option( 'siteurl' ).'/?page=preview&msg=nopaymethod');
		exit;
	}
}else{
	$payable_amount =0;
}
$cat_display = get_option('templatic-category_type');
if($_POST)
{
	if($_POST['paynow'])
	{  
		$custom_fields = $_SESSION['custom_fields'];
		$custom = array();
		$post_title = stripslashes($custom_fields['post_title']);
		$description = @$custom_fields['post_content'];
		$post_excerpt = $custom_fields['post_excerpt'];
		$post_tags = $custom_fields['post_tags'];
		$catids_arr = array();
		$my_post = array();
		$alive_days = $listing_price_info['alive_days'];
		$payment_method = $_REQUEST['paymentmethod'];
		$coupon = @$custom_fields['add_coupon'];
		$featured_type = @$custom_fields['featured_type'];
		$pid = $_REQUEST['pid']; /* it will be use when going for RENEW */
		
		if($payable_amount <= 0)
		{	
			if($_SESSION['custom_fields']['last_selected_pkg'] !='')
			{
				global $monetization;
				$post_default_status = $monetization->templ_get_packaget_post_status($current_user->ID, get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true));
				if($post_default_status =='recurring'){
					$post = get_post($custom_fields['cur_post_id']);
					
					$post_default_status = $monetization->templ_get_packaget_post_status($current_user->ID, $post->post_parent,'submit_post_type',true);
					if($post_default_status =='trash'){
						$post_default_status ='draft';
					}
				}
			}else{
				if($payment_method  =='prebanktransfer'){
					$post_default_status = 'draft';
				}else{
					$post_default_status = fetch_posts_default_status();
				}
			}
		}else
		{
			$post_default_status = 'draft';
		}	
	
		if( @$_REQUEST['pid']){
			$post_default_status = get_post_status($_REQUEST['pid']);
		}else{
			$post_default_status = $post_default_status;
		}
		
		$my_post['post_status'] = $post_default_status;
		
		if($current_user_id)
		{
			$my_post['post_author'] = $current_user_id;
		}
		$my_post['post_title'] = $post_title;
		$my_post['post_name'] = $post_title;
		$my_post['post_content'] = $description;
		$my_post['post_category'] = $custom_fields['category'];
		$my_post['tags_input'] = apply_filters('tevolution_post_tags',$post_tags,$_SESSION);
		$my_post['post_excerpt'] = $post_excerpt;
		$my_post['post_type'] = get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true);
		/* Here array separated by category id and price amount */
		if($_SESSION['category'])
		{
			$category_arr = $_SESSION['category'];
			foreach($category_arr as $_category_arr)
			 {
				$category[] = explode(",",$_category_arr);
			 }
			foreach($category as $_category)
			 {
				 $post_category[] = $_category[0];
				 $category_price[] = $_category[1];
			 }
		}
		
		/*Set the post per subscription limite post count on user meta table  */
		if($_REQUEST['pid'] =='')
		{
				$submit_post_type = get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true);
				$package_post=get_post_meta($_SESSION['custom_fields']['package_select'],'limit_no_post',true);
				//$user_limit_post=get_user_meta($current_user_id,$submit_post_type.'_list_of_post',true);
				$user_limit_post=get_user_meta($current_user_id,'total_list_of_post',true);
				
				$package_selected=get_user_meta($current_user_id,'package_selected',true);//fetch last package selected price packageid
				
				// check last price package id with current price package id
				if($package_post > $user_limit_post && $package_selected==$_SESSION['custom_fields']['package_select'])
				{
					//$limit_post=get_user_meta($current_user_id,$submit_post_type.'_list_of_post',true);				
					$limit_post=get_user_meta($current_user_id,'total_list_of_post',true);				
					update_user_meta($current_user_id,$submit_post_type.'_list_of_post',$limit_post+1);
					update_user_meta($current_user_id,'total_list_of_post',$limit_post+1);
					update_user_meta($current_user_id,$submit_post_type.'_package_select',$_SESSION['custom_fields']['package_select']);
					update_user_meta($current_user_id,'package_selected',$_SESSION['custom_fields']['package_select']);
				}else
				{
					update_user_meta($current_user_id,'package_selected',$_SESSION['custom_fields']['package_select']);
					update_user_meta($current_user_id,$submit_post_type.'_package_select',$_SESSION['custom_fields']['package_select']);
					//update_user_meta($current_user_id,$submit_post_type.'_list_of_post',1);
					update_user_meta($current_user_id,'total_list_of_post',1);
					update_user_meta($current_user_id,$submit_post_type.'_list_of_post',1);
				}			
		}
		
		/*Finish post per subscription limite post count on user meta table  */
		if(isset($_REQUEST['pid']) && $_REQUEST['pid'] != '')
		{
			do_action('update_post_before_submit',$_REQUEST['pid']); /*add action to do any changes before update the post.*/
			if($custom_fields['renew'])
			{
				if($post_status==''){
					$post_status ='publish';
				}
				$my_post['post_date'] = date('Y-m-d H:i:s');
				$my_post['post_status'] = $post_default_status;
				$my_post['ID'] = $_REQUEST['pid'];
				$my_post['comment_status'] = 'open';				
				$last_postid = wp_insert_post($my_post);
				update_post_meta($last_postid,'stripe_cancelled',0);
				/* Finish the place geo_latitude and geo_longitude in postcodes table*/
				if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
					if(function_exists('wpml_insert_templ_post'))
						wpml_insert_templ_post($last_postid,$my_post['post_type']); /* insert post in language */
				}				
				$post_tax = fetch_page_taxonomy($_SESSION['custom_fields']['cur_post_id']);
				wp_set_post_terms( $last_postid,'',$post_tax,false);
				if($post_category){
				foreach($post_category as $_post_category)
				 {
					if(taxonomy_exists($post_tax)):
						wp_set_post_terms( $last_postid,$_post_category,$post_tax,true);
					endif;
				 }
				}
				
				
				/*Being Insert Post tag*/
				 $taxonomies = get_object_taxonomies( (object) array( 'post_type' => $my_post['post_type'],'public'   => true, '_builtin' => true ));		
				 if($my_post['tags_input']!=""){
					wp_set_post_terms($last_postid,$my_post['tags_input'],$taxonomies[1]);	 
				 }
				 /*End insert post tag */
				
				foreach($custom_fields as $key=>$val)
				{
					if($key != 'category' && $key != 'post_title' && $key != 'post_content' && $key != 'imgarr' && $key != 'Update' && $key != 'post_excerpt')
					  {
						if($key=='recurrence_bydays')
						{
							$val=implode(',',$val);
							update_post_meta($last_postid, $key, $val);
						}
						else
						{
							update_post_meta($last_postid, $key, $val);
						}
					  }
				}
			
				if(isset($_SESSION['upload_file']) && $_SESSION['upload_file']!="")
				{
					foreach($_SESSION['upload_file'] as $key=> $valfile)
					{
						update_post_meta($last_postid, $key, $valfile);
					}
				}
			}
			else
			{ /* Condtion for Edit post */
				if( @$_REQUEST['pid']){
					$post_default_status = get_post_status($_REQUEST['pid']);
				}else{
					$post_default_status = 'publish';
				}
				$my_post['ID'] = $_REQUEST['pid'];
				$my_post['post_title'] = stripslashes($custom_fields['post_title']);
				$my_post['post_name'] = $custom_fields['post_title'];
				$my_post['post_content'] = $custom_fields['post_content'];
				$my_post['post_excerpt'] = $custom_fields['post_excerpt'];
				$my_post['post_type'] = get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true);
				$my_post['post_status'] = $post_default_status;
				$my_post['comment_status'] = 'open';				
				
				$last_postid = wp_insert_post( $my_post );
				
				/* Finish the place geo_latitude and geo_longitude in postcodes table*/
				if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
					if(function_exists('wpml_insert_templ_post'))
						wpml_insert_templ_post($post_id,$my_post['post_type']); /* insert post in language */
				}
				
				/*Being Insert Post tag*/
				 $taxonomies = get_object_taxonomies( (object) array( 'post_type' => $my_post['post_type'],'public'   => true, '_builtin' => true ));		
				 if($my_post['tags_input']!=""){
					wp_set_post_terms($last_postid,$my_post['tags_input'],$taxonomies[1]);	 
				 }
				 /*End insert post tag */
				$post_category = wp_get_post_terms($last_postid, $taxonomies[0], array("fields" => "ids"));
				
				$post_tax = $taxonomies[0];
				
				foreach($custom_fields as $key=>$val)
				{
					if($key != 'category' && $key != 'post_title' && $key != 'post_content' && $key != 'imgarr' && $key != 'Update' && $key != 'post_excerpt' && $key != 'alive_days')
					  {
						if($key=='recurrence_bydays')
						{
							$val=implode(',',$val);
							update_post_meta($last_postid, $key, $val);
						}
						else
						{
							update_post_meta($last_postid, $key, $val);
						}
					  }
				}
			
				if(isset($_SESSION['upload_file']) && $_SESSION['upload_file']!="")
				{
					foreach($_SESSION['upload_file'] as $key=> $valfile)
					{
						update_post_meta($last_postid, $key, $valfile);
					}
				}
			}
		}else
		{ 			
			$my_post['comment_status'] = 'open';			
			$last_postid = wp_insert_post($my_post); //Insert the post into the database			
			$post_tax = fetch_page_taxonomy($_SESSION['custom_fields']['cur_post_id']);			
			
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,$my_post['post_type']); /* insert post in language */
			}			
			if($post_category){
			foreach($post_category as $_post_category)
			 {
				if(taxonomy_exists($post_tax)):
					wp_set_post_terms( $last_postid,$_post_category,$post_tax,true);
				endif;
			 }
			 }
			 /*Being Insert Post tag*/
			 $taxonomies = get_object_taxonomies( (object) array( 'post_type' => $my_post['post_type'],'public'   => true, '_builtin' => true ));		
			 if($my_post['tags_input']!=""){
				wp_set_post_terms($last_postid,$my_post['tags_input'],$taxonomies[1]);	 
			 }
			 /*End insert post tag */
			 
			 
			 
			/* insert custom fields */
			foreach($custom_fields as $key=>$val)
			{
				if($key != 'category' && $key != 'post_title' && $key != 'post_content' && $key != 'imgarr' && $key != 'Update' && $key != 'post_excerpt')
				  {
					  if($key=='recurrence_bydays')
						{
							$val=implode(',',$val);
							update_post_meta($last_postid, $key, $val);
						}else
						{
							update_post_meta($last_postid, $key, $val);
						}
				  }
			} 
			if(isset($_SESSION['upload_file']) && $_SESSION['upload_file'] !=''){
					foreach($_SESSION['upload_file'] as $key=> $valfile)
					{
						update_post_meta($last_postid, $key, $valfile);
					} 
			}
		}
		if(class_exists('monetization')){
			if($custom_fields['renew'] || !$custom_fields['pid'])
			{
				global $monetization;
				$monetize_settings = $monetization->templ_set_price_info($last_postid,$pid,$payable_amount,$alive_days,$payment_method,$coupon,$featured_type);
			}
		}
		if(is_active_addons('monetization')){
			global $trans_id;
			if($_SESSION['custom_fields']['action'] != 'edit')
			{
				$trans_id = insert_transaction_detail($_REQUEST['paymentmethod'],$last_postid);
			}
			
		} 
		if(isset($_SESSION["file_info"]) && $_SESSION['file_info']!="")
		{
			$menu_order = 0;
			foreach($_SESSION["file_info"] as $image_id=>$val)
			{
				//$src = get_image_tmp_phy_path().$image_id.'.jpg';
				$src = TEMPLATEPATH."/images/tmp/".$val;
				if(file_exists($src) && $val != '')
				{
					$menu_order++;
					$dest_path = get_image_phy_destination_path_plugin().$val;
					$original_size = get_image_size_plugin($src);
					$thumb_info = image_resize_custom_plugin($src,$dest_path,get_option('thumbnail_size_w'),get_option('thumbnail_size_h'));
					$medium_info = image_resize_custom_plugin($src,$dest_path,get_option('medium_size_w'),get_option('medium_size_h'));
					$post_img = move_original_image_file_plugin($src,$dest_path);
					$post_img['post_status'] = 'attachment';
					$post_img['post_parent'] = $last_postid;
					$post_img['post_type'] = 'attachment';
					$post_img['post_mime_type'] = 'image/jpeg';
					$post_img['menu_order'] = $menu_order;
					$dirinfo = wp_upload_dir();		
					$path = $dirinfo['path'];
					$url = $dirinfo['url'];
					$subdir = $dirinfo['subdir'];
					$basedir = $dirinfo['basedir'];
					$baseurl = $dirinfo['baseurl'];	
					 $wp_filetype = wp_check_filetype(basename($val), null );
					$attachment = array(
						 'guid' => $baseurl.$subdir."/"._wp_relative_upload_path( $val ),
						 'post_mime_type' => $wp_filetype['type'],
						 'post_title' => preg_replace('/\.[^.]+$/', '', basename($val)),
						 'post_content' => '',
						 'post_status' => 'inherit',
						 'menu_order' => $menu_order
					  );		
					//$last_postimage_id = wp_insert_post( $post_img ); // Insert the post into the database
		
					  $img_attachment = substr($subdir."/".$val,1);
					  $attach_id = wp_insert_attachment( $attachment, $img_attachment, $last_postid );
					 
					  require_once(ABSPATH . 'wp-admin/includes/image.php');					 
					  $upload_img_path=$basedir.$subdir."/"._wp_relative_upload_path( $val);
					  $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_img_path );					
					  wp_update_attachment_metadata( $attach_id, $attach_data );
					
				}
			}
		}
		do_action('process_coupons',$last_postid);
		if(!$_REQUEST['pid']){
		update_post_meta($last_postid, 'remote_ip',getenv('REMOTE_ADDR'));
		update_post_meta($last_postid,'ip_status',@$_SESSION['custom_fields']['ip_status']);
		}
	  /* Code for update menu for images */
	  
	  if($_REQUEST['pid'])
		  {
			$j = 1;
			foreach($_SESSION["file_info"] as $arrVal)
			 {
				$expName = array_slice(explode(".",$arrVal),0,1);
				$wpdb->query('update '.$wpdb->posts.' set  menu_order = "'.$j.'" where post_name = "'.$expName[0].'"  and post_parent = "'.$_REQUEST['pid'].'"');
				$j++;	
			 }
		  }
	/* End Code for update menu for images */
		///////ADMIN EMAIL START//////
			$fromEmail = get_site_emailId_plugin();
			$fromEmailName = get_site_emailName_plugin();
			$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
			$admin_email_id = get_option('admin_email');
			$tmpdata = get_option('templatic_settings');
			$email_content =  @stripslashes($tmpdata['post_submited_success_email_content']);
			$email_subject =  @stripslashes($tmpdata['post_submited_success_email_subject']);
			
			$email_content_user =  @stripslashes($tmpdata['user_post_submited_success_email_content']);
			$email_subject_user =  @stripslashes($tmpdata['user_post_submited_success_email_subject']);
			
			
			$mail_post_type_object = '';
			$mail_post_title ='';
			if($last_postid){
				$mail_post_type_object = get_post_type_object(get_post_type($last_postid));
				$mail_post_title = $mail_post_type_object->labels->menu_name;
			}
			
			if(function_exists('icl_t')){
				icl_register_string(DOMAIN,$mail_post_title,$mail_post_title);
				$mail_post_title = icl_t(DOMAIN,$mail_post_title,$mail_post_title);
			}else{
				$mail_post_title = @$mail_post_title;
			}
			
			if(!$email_subject){
				$email_subject = __('A new post has been submited on your site',DOMAIN);
			}
			if($_REQUEST['pid']){
				$update_listing_notification_subject=$tmpdata['update_listing_notification_subject'];
				$email_subject = str_replace(array('[#post_type#]','[#submition_Id#]'),array($mail_post_title,$last_postid),$update_listing_notification_subject);
			}
			if(isset($_SESSION['custom_fields']['renew'])){
				$email_subject = __(sprintf('%s renew of ID:#%s',$mail_post_title,$last_postid));
			}
			if(!$email_content){
				$email_content = __('<p>Howdy [#to_name#],</p><p>A new post has been submited on your site. Here are some details about it</p><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>',DOMAIN);
			}
			if($_REQUEST['pid'] ){				
				$email_content = __(sprintf('<p>Dear [#to_name#],</p>
				<p>%s has been updated on your site. Here is the information about the %s:</p>
				[#information_details#]
				<br>
				<p>[#site_name#]</p>',$mail_post_title,$mail_post_title),DOMAIN);
			}
			if(isset($_SESSION['custom_fields']['renew'])){
				$email_content = __(sprintf('<p>Dear [#to_name#],</p>
				<p>%s has been renew on your site. Here is the information about the %s:</p>
				[#information_details#]
				<br>
				<p>[#site_name#]</p>',$mail_post_title,$mail_post_title),DOMAIN);
				
			}				
			
			if(!$email_subject_user){
				$email_subject_user = __(sprintf('New %s listing of ID:#%s',$mail_post_title,$last_postid),DOMAIN);	
			}
			if($_REQUEST['pid']){
				$update_listing_notification_subject=$tmpdata['update_listing_notification_subject'];
				$email_subject_user = str_replace(array('[#post_type#]','[#submition_Id#]'),array($mail_post_title,$last_postid),$update_listing_notification_subject);
			}
			if(isset($_SESSION['custom_fields']['renew']))
			{
				$update_listing_notification_subject=$tmpdata['renew_listing_notification_subject'];
				$email_subject_user = str_replace(array('[#post_type#]','[#submition_Id#]'),array($mail_post_title,$last_postid),$update_listing_notification_subject);
				
			}	
			if(!$email_content_user)
			{
				$email_content_user = __("<p>Hello [#to_name#]</p><p>Here's some info about your payment...</p><p>[#information_details#]</p><p>If you'll have any questions about this payment please send an email to [#admin_email#]</p><p>Thanks!,<br/>[#site_name#]</p>",DOMAIN);
			}
			if($_REQUEST['pid'])
			{
				$email_content_user=$tmpdata['update_listing_notification_content'];				
			}
			if(isset($_SESSION['custom_fields']['renew']))
			{
				$email_content_user=$tmpdata['renew_listing_notification_content'];
				
			}	
			$information_details = "<p>".__('ID',DOMAIN)." : ".$last_postid."</p>";
			$information_details .= '<p>'.__('View more detail of',DOMAIN).' <a href="'.get_permalink($last_postid).'">'.stripslashes($my_post['post_title']).'</a></p>';
			global $payable_amount;
			if(is_active_addons('monetization') && $payable_amount > 0){
				$information_details .= '<p>'.__('Payment Status: <b>Pending</b>',DOMAIN).'</p>';
				$information_details .= '<p>'.__('Payment Method: <b>'.ucfirst(@$_POST['paymentmethod']).'</b>',DOMAIN).'</p>';
			}else{
				$information_details .= '<p>'.__('Payment Status: <b>Success</b>',DOMAIN).'</p>';
			}	
			$post_type=get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true);
			$show_on_email=get_post_custom_fields_templ_plugin($post_type,$post_category,$post_tax);	
			$suc_post = get_post($last_postid);			

			$information_details='<style type="text/css">
					.cust_feild_details {
						max-width: 800px;
						}
						
					.cust_feild_details li  {
						border-bottom: 1px solid #ccc;
						padding: 8px;
						list-style: none;
						}
						
					.cust_feild_details li label {
						display: inline-block;
						vertical-align: top;
						width: 180px;
						}
			</style>';
			if($show_on_email)
			{
				$information_details.='<ul class="cust_feild_details">';
				foreach($show_on_email as $key=>$val)
				{		
					if($key == 'category')
					{
						$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $suc_post->post_type,'public'   => true, '_builtin' => true ));	
						
						$category_name = wp_get_post_terms($last_postid, $taxonomies[0]);
						if($category_name)
						{
							$_value = '';
							
							foreach($category_name as $value)
							 {
								$_value .= $value->name.",";
							 }
							 $information_details.= "<li><label>".__(sprintf('%s Category',$mail_post_title)).": </label> ".substr($_value,0,-1)."</li>";
						}
					}
					if($key=='post_title' && $val['show_in_email'])
					{
						$information_details.= '<li><label>'.$val['label'].' :</label>'.$my_post['post_title'].'</li>';
					}
					if($key=='post_content' && $val['show_in_email'] && $my_post['post_content']!='')
					{
						$information_details.= '<li><label>'.$val['label'].' :</label>'.$my_post['post_content'].'</li>';
					}
					if($key=='post_excerpt' && $val['show_in_email'] && $my_post['post_excerpt']!='')
					{
						$information_details.= '<li><label>'.$val['label'].' :</label>'.$my_post['post_excerpt'].'</li>';
					}
					
					if($val['type'] == 'multicheckbox' && get_post_meta($last_postid,$val['htmlvar_name'],true) !='' && $val['show_in_email']=='1')
					{
						$information_details.='<li><label>'.$val['label'].' :</label> '. apply_filters('tevolution_submited_email', implode(",",get_post_meta($last_postid,$val['htmlvar_name'],true)),$val['htmlvar_name']).'</li>';
					}elseif($val['type']=='upload' && get_post_meta($last_postid,$val['htmlvar_name'],true) !='' && $val['show_in_email']=='1'){
						
						$value=apply_filters('tevolution_submited_email',get_post_meta($last_postid,$val['htmlvar_name'],true),$val['htmlvar_name']);
						$information_details.= '<li><label>'.$val['label'].' :</label> <img src="'.$value.'" width="200"></li>';
					}else{					
						if($val['show_in_email']=='1' && get_post_meta($last_postid,$val['htmlvar_name'],true)!="")
						{
							$information_details.= '<li><label>'.$val['label'].' :</label> '.apply_filters('tevolution_submited_email',get_post_meta($last_postid,$val['htmlvar_name'],true),$val['htmlvar_name']).'</li>';
						}
					}
					
				}
				if(get_post_meta($last_postid,'package_select',true))
				{
						$package_name = get_post(get_post_meta($last_postid,'package_select',true));
						 $information_details.= "<li><h4>".__('Price Package Information',DOMAIN)."</h4></li>";
						 $information_details.= "<li><label>".__('Package Type',DOMAIN).": </label>".$package_name->post_title."</li>";
					 
				}
				if(get_post_meta($last_postid,'alive_days',true))
				{
					 $information_details.= "<li><label>".__('Validity',DOMAIN).": </label> ".get_post_meta($last_postid,'alive_days',true).' '.__('Days',DOMAIN)."</li>";
				}
				if(get_user_meta($suc_post->post_author,'list_of_post',true))
				{
					 $information_details.= "<li><label>".__('Submited number of posts',DOMAIN).": </label> ".get_user_meta($suc_post->post_author,'list_of_post',true)."</li>";
				}
				if(get_post_meta(get_post_meta($last_postid,'package_select',true),'recurring',true))
				{
					$package_name = get_post(get_post_meta($last_postid,'package_select',true));
					 $information_details.= "<li><label>".__('Recurring Charges',DOMAIN).": </label> ".fetch_currency_with_position(get_post_meta($last_postid,'paid_amount',true))."</li>";
				}
				$information_details.='</ul>';
			}
			
			$search_array = array('[#to_name#]','[#information_details#]','[#transaction_details#]','[#site_name#]','[#submited_information_link#]','[#admin_email#]','[#post_type#]');
			$uinfo = get_userdata($current_user_id);
			$user_fname = $uinfo->display_name;
			$user_email = $uinfo->user_email;
			$link = get_permalink($last_postid);
			$replace_array_admin = array($fromEmailName,$information_details,$information_details,$store_name,'',get_option('admin_email'),$mail_post_title);
			$replace_array_client =  array($user_fname,$information_details,$information_details,$store_name,$link,get_option('admin_email'),$mail_post_title);
			$email_content_admin = str_replace($search_array,$replace_array_admin,$email_content);
			$email_content_client = str_replace($search_array,$replace_array_client,$email_content_user);
			templ_send_email($fromEmail,$fromEmailName,$fromEmail,$fromEmailName,$email_subject,$email_content_admin,$extra='');///To admin email			
			templ_send_email($fromEmail,$fromEmailName,$user_email,$user_fname,$email_subject_user,$email_content_client,$extra='');//to client email			
		
		//////ADMIN EMAIL END////////
		if(is_active_addons('monetization') && ($payable_amount != '' || $payable_amount >= 0) && @$_REQUEST['paymentmethod']){
			payment_menthod_response_url(@$_REQUEST['paymentmethod'],$last_postid,@$custom_fields['renew'],@$_REQUEST['pid'],$payable_amount);
		}else{
			$suburl = "&pid=$last_postid";
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				global $sitepress;
				if(isset($_REQUEST['lang'])){
					$url = get_option('siteurl').'/?page=success&lang='.$_REQUEST['lang'].$suburl;
				}elseif($sitepress->get_current_language()){
					$url = get_option( 'siteurl' ).'/'.$sitepress->get_current_language().'/?page=success'.$suburl;
						if($sitepress->get_default_language() != $sitepress->get_current_language()){
							$url = get_option( 'siteurl' ).'/'.$sitepress->get_current_language().'/?page=success'.$suburl;
						}else{
							$url = get_option( 'siteurl' ).'/?page=success'.$suburl;
						}
				}else{
					$url = get_option('siteurl').'/?page=success'.$suburl;
				}
			}else{
				$url = get_option('siteurl').'/?page=success'.$suburl;
			}
			wp_redirect($url);
		}
	}
}
?>
