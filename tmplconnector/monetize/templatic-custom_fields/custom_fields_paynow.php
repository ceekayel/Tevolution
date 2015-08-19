<?php 
/*
 * save the submit form data into database
*/

$tmpdata = get_option('templatic_settings');
/*if(in_array('submit',$tmpdata['user_verification_page']) && !$_SESSION['gotit']){
	return;
}else{
	unset($_SESSION['gotit']);
}*/
unset($_SESSION['custom_fields']);
if(($_POST['submit_post_type'] && $_POST['submit_post_type']!="")  && (isset( $_POST['submit_form_nonce_field'] ) || wp_verify_nonce( $_POST['submit_form_nonce_field'], 'submit_form_action' )) )
{
	
	$_SESSION['pament_done'] = 1;
	global $wpdb,$last_postid,$payable_amount,$current_user,$trans_id;
	
	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;
	/* new register user section */
	if(!$current_user->ID && isset($_REQUEST['user_email']) && $_REQUEST['user_email']!='' && isset($_REQUEST['user_fname']) && $_REQUEST['user_fname']!=""){		
		$current_user_id = templ_insertuser_with_listing();	
		$current_user=get_userdata( $current_user_id );
	}
	/* END new register user section */	
	
	/* Get the package id from submited post id if post is editable*/
	if(isset($_POST['pid']) && $_POST['pid']!='' && isset($_POST['action']) && $_POST['action']=='edit'){
		$_POST['pkg_id']=get_post_meta($_POST['pid'],'package_select',true);
	}

	/* fetch package information if monetization is activated */
	if(class_exists('monetization') && isset($_POST['pkg_id']) && $_POST['pkg_id']!=''){
		global $monetization;
		$listing_price_info = $monetization->templ_get_price_info($_POST['pkg_id']);	
		
		$subscription_as_pay_post=$listing_price_info[0]['subscription_as_pay_post'];
		/* Get the selected package price */
		$package_price=$listing_price_info[0]['price'];
		$is_package=1;
		$featured_home_price = $featured_cat_price = $is_featured_h=$is_featured_c=0;;
		/*Price package featured calculation */
			/* Get the featured home price*/
		if($listing_price_info[0]['is_home_page_featured']==1 && $listing_price_info[0]['is_home_featured']!='1' && isset($_POST['featured_h']) && $_POST['featured_h']!=''){
			$featured_home_price=$listing_price_info[0]['feature_amount'];
			$is_featured_h=1;
		}elseif($listing_price_info[0]['is_home_featured']==1){
			$_POST['featured_h']='1';
			$is_featured_h=1;
		}
		
		 /* Get the featured category price */
		if($listing_price_info[0]['is_category_page_featured']==1 && $listing_price_info[0]['is_category_featured']!='1' && isset($_POST['featured_c']) && $_POST['featured_c']!=''){
			$featured_cat_price=$listing_price_info[0]['feature_cat_amount'];
			$is_featured_c=1;
		}elseif($listing_price_info[0]['is_category_featured']==1){
			$_POST['featured_c']='1';
			$is_featured_c=1;
		}		
		/* End Price package featured calculation*/		
		
		/* Calculation for price package + featued home price + featured category price  */
		$payable_amount = $package_price+$featured_home_price+$featured_cat_price;
		
		/* Package Alive days */
		$alive_days=$listing_price_info[0]['alive_days'];		
		
		
		$package_selected=get_user_meta($current_user_id,'package_selected',true);
		/* Get the submited list of post and selected package number of limit post*/
		$user_limit_post=get_user_meta($current_user_id,$_POST['submit_post_type'].'_list_of_post',true); 
		$user_limit_post=($user_limit_post!="")?$user_limit_post:'0';
		$package_limit=get_post_meta($_POST['pkg_id'],'limit_no_post',true);		
		/*Get the user last transaction  */
		if(($payable_amount > 0 && $listing_price_info[0]['package_type']==2 && $subscription_as_pay_post!=1 && ($package_selected!=$_POST['pkg_id'] || $package_limit <= $user_limit_post )&& $_POST['pid']=='' && (@$_REQUEST['package_free_submission'] == '' || @$_REQUEST['package_free_submission'] <=0 )) || ($payable_amount > 0 && $listing_price_info[0]['package_type']==2 && $subscription_as_pay_post!=1 && @$_REQUEST['upgrade'] == 'upgrade'))
		{	
			$submit_post_type = $_POST['submit_post_type'];			
			$payable_amount=$package_price;
			/* Check coupon code amount */
			if(isset($_POST['add_coupon']) && $_POST['add_coupon'] && function_exists('tmpl_payable_amount_after_add_coupon')){
				$payable_amount =tmpl_payable_amount_after_add_coupon($payable_amount,$_POST['add_coupon']);
			}
			$_POST['paid_amount']=$_POST['payable_amount']=$payable_amount;
			$_POST['package_select']=$_POST['pkg_id'];
			$_POST['alive_days'] = $alive_days;			
			
			
			if(@$_POST['upgrade'] == 'upgrade')
			{
				update_user_meta($current_user_id,'upgrade',$_POST['upgrade']);
				update_user_meta($current_user_id,$submit_post_type.'_package_select',$_POST['package_select']);
				update_user_meta($current_user_id,'package_selected',$_POST['package_select']);
			}
			else
			{
				update_user_meta($current_user_id,'package_selected',$_POST['package_select']);
				update_user_meta($current_user_id,$submit_post_type.'_package_select',$_POST['package_select']);				
				update_user_meta($current_user_id,'total_list_of_post',0);
				update_user_meta($current_user_id,$submit_post_type.'_list_of_post',0);
			}
			$trans_id = insert_transaction_detail($_POST['paymentmethod'],$last_postid,0,1);	
			insert_update_users_packageperlist(0,$_POST,$trans_id);
			payment_menthod_response_url(@$_POST['paymentmethod'],$last_postid,'','',$payable_amount);
			exit;
		}
		/* user select pay per submited post subscription then set payablem amount with price package price+ featured price */
		elseif($listing_price_info[0]['package_type']==2 && $subscription_as_pay_post==1){
		$transaction_tabel = $wpdb->prefix . "transactions";
		$transaction_status = $wpdb->get_results("SELECT status,package_id FROM $transaction_tabel where package_id = ".$_POST['pkg_id']." AND user_id=".$current_user->ID." order by trans_id DESC LIMIT 1");
		$trans_status=$transaction_status[0]->status;
		
			if(($trans_status == 0 || $trans_status == '' || $trans_status == 2 || $_REQUEST['total_price']>0)  && ((($package_limit <= $user_limit_post  || $user_limit_post==0 )) || (get_user_meta($current_user_id,'package_free_submission_completed',true) == 'completed' &&  $user_limit_post==0 ))){			
				$payable_amount = $package_price+$featured_home_price+$featured_cat_price;
				$is_package=1;
			}
			elseif((($package_limit <= $user_limit_post  || $user_limit_post==0 )) || (get_user_meta($current_user_id,'package_free_submission_completed',true) == 'completed' &&  $user_limit_post==0 ))
			{
				$payable_amount = $package_price+$featured_home_price+$featured_cat_price;
				$is_package=1;
			}else{
				$payable_amount = $featured_home_price+$featured_cat_price;
				$is_package=0;
			}
		}
		/* Check existing package type is pay per subscription then exclude package price package */
		elseif($listing_price_info[0]['package_type']==2 && $_POST['pid']==''){
			$payable_amount = $featured_home_price+$featured_cat_price;
			$is_package=0;
		}
		/*set payable amount on Edit Post Type  */
		if(isset($_POST['pid']) && $_POST['pid']!='' && isset($_POST['action']) && $_POST['action']=='edit'){
			$payable_amount = $featured_home_price+$featured_cat_price;
			$is_package=0;
		}
		
		
	}else{
		$payable_amount =0;
		$is_package=$is_featured_h=$is_featured_c=0;
	}	
	
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $_POST['submit_post_type'],'public'   => true, '_builtin' => true ));
	$taxonomy = $taxonomies[0];		
	$categories = get_terms( $taxonomy,$args = array('hide_empty' => false) ) ;
	$is_category=$category_price=0;
	/* Category price calculation from selected category  */
	if(isset($_POST['category']) && $_POST['category']!=''){
		foreach($_POST['category'] as $value){
			$category=explode(',',$value);				
			$category_id[]=$category[0];
		}
		/*Get the category price as per selected category */
		foreach($categories as $category){
			if(in_array($category->term_id,$category_id)){
				$category_price+=$category->term_price;				
			}
		}
		/*Check category price not zero for insert catrgory entry in transaction table */
		if($category_price!=0){
			$is_category=1;
		}
	}
	/* Finish category price calculation*/
	
	$exclude_post=apply_filters('submit_exclude_post',array('category','post_title','post_content','imgarr','Update','post_excerpt','post_tags','selectall','submitted','submit_post_type','action','pid'),$_POST);
	
	
	/* Get the total payable amount */
	if(isset($_REQUEST['package_free_submission']) && $_REQUEST['package_free_submission'] > 0 && get_user_meta($current_user_id,'package_free_submission_completed',true) != 'completed' && isset($_REQUEST['upgrade']) && $_REQUEST['upgrade'] == '')
	{ 
		$_POST['paid_amount']=$_POST['payable_amount']=$payable_amount=0;
		$_POST['package_select']=$_POST['pkg_id'];
		$_POST['alive_days'] = $alive_days;
		$is_package=1;
	}
	elseif(isset($_POST['pid']) && $_POST['pid']!='' && isset($_POST['action']) && $_REQUEST['action']=='edit'){
		
		$_POST['paid_amount']=$_POST['payable_amount']=$payable_amount=$payable_amount+$category_price;
		
	}else{		
	
		$_POST['paid_amount']=$_POST['payable_amount']=$payable_amount=$payable_amount+$category_price;
		$_POST['package_select']=$_POST['pkg_id'];
		$_POST['alive_days'] = $alive_days;
	}
	
	
	/* Check coupon code amount */
	if(isset($_POST['add_coupon']) && $_POST['add_coupon'] && function_exists('tmpl_payable_amount_after_add_coupon')){
		$_POST['paid_amount']=$_POST['payable_amount']=$payable_amount =tmpl_payable_amount_after_add_coupon($payable_amount,$_POST['add_coupon']);		
	}
	
	$pid = $_POST['pid']; /* it will be use when going for RENEW */		
	$custom_fields = $_POST;
	$custom = array();
	$post_title = stripslashes($_POST['post_title']);
	$description = @$_POST['post_content'];
	$post_excerpt = $_POST['post_excerpt'];
	$post_tags = $_POST['post_tags'];
	$post_type = $_POST['submit_post_type'];
	$catids_arr = array();
	$my_post = array();		
	
	$payment_method = $_POST['paymentmethod'];
	$coupon = @$custom_fields['add_coupon'];
		
		
		
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
	
	/*Post Status */
	$post_default_status= (isset($_POST['pid']) && $_POST['pid']!="")? get_post_status($_POST['pid']):  $post_default_status;
	if(isset($_REQUEST['renew']) && $_REQUEST['renew'] == 1 && isset($_REQUEST['pid']) && $_REQUEST['pid'] != '' && $payable_amount > 0)
		$my_post['post_status'] = 'draft';
	else	
		$my_post['post_status'] = $post_default_status;
	/*Post author */		
	$my_post['post_author']=($current_user_id)?$current_user_id: 0;
	/* Post Title */
	$my_post['post_title'] = $post_title;
	/*Post Content */
	$my_post['post_content'] = $description;
	/*Post Excerpt */
	$my_post['post_excerpt'] = $post_excerpt;
	/*Post Category */
	$my_post['post_category'] = $category_id;
	/*Post tags input */
	$my_post['tags_input'] = apply_filters('tevolution_post_tags',$post_tags,$_POST);
	/*Post type */
	$my_post['post_type'] = $_POST['submit_post_type'];
	/*Post Name*/
	$my_post['post_name']=sanitize_title($post_title);
	
	/* Set Post Id for update inserted post*/
	if(isset($_POST['pid']) && $_POST['pid']!=''){
		 /*add action to do any changes before update the post.*/
		do_action('update_post_before_submit',$_POST['pid']);
		
		$my_post['ID'] = $_POST['pid'];	
		/*added condition to not to update post date while edit a post.*/
		if(isset($_POST['action']) && $_POST['action']=='edit')
		{
			$post_data = get_post($_POST['pid']);
			$my_post['post_date'] = $post_data->post_date;
		}
	}
	
	$default_comment_status = get_option('default_comment_status'); /* default get comment status for new posts */
	
	$my_post['comment_status'] = $default_comment_status;
	/* Insert the post into the database */
	/* added condition for spam entry */
	if($my_post['post_category']){
		$last_postid = wp_insert_post($my_post);
	}
	/* Finish the place geo_latitude and geo_longitude in postcodes table*/
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		if(function_exists('wpml_insert_templ_post'))
			wpml_insert_templ_post($last_postid,$my_post['post_type']); /* insert post in language */
	}	
	/*Insert Post selected category */
	if($my_post['post_category']){
		if(!isset($_POST['action_edit']))
			wp_set_post_terms( $last_postid,'',$taxonomy,false);
			
		
		if(isset($_POST['action_edit'])){
			wp_set_post_terms( $last_postid,'',$taxonomy,false);
				foreach($my_post['post_category'] as $_post_category){	
					wp_set_post_terms( $last_postid,$_post_category,$taxonomy,true);					
				}
		}else{
			foreach($my_post['post_category'] as $_post_category){					
				wp_set_post_terms( $last_postid,$_post_category,$taxonomy,true);					
			}
		}
	 }
	 /*Being Insert Post tag*/			 
	 if($my_post['tags_input']!=""){
		wp_set_post_terms($last_postid,$my_post['tags_input'],$taxonomies[1]);	 
	 }
	/*End insert post tag */
	
	if($payable_amount == 0)
	{
		$custom_fields['paymentmethod'] = 'Free';
	}
	/* insert/update custom fields */
	foreach($custom_fields as $key=>$val)
	{
		/* Check submitted key in exclude post array*/
		if(!in_array($key,$exclude_post))
		{			
			if($key=='recurrence_bydays'){
				
				$val=implode(',',$val);
				update_post_meta($last_postid, $key, trim($val));
				
			}else{
				$value=(is_array($val))?$val:trim($val);
				if($value!=""){
					update_post_meta($last_postid, $key, $value);
				}
				else
				{
					update_post_meta($last_postid, $key, '');
				}
			}
		}
	}	
	/*End insert/update custom fields */

	/* Set post featured type in post meta table */
	//if(@$payable_amount <= 0 )
	{
		if(isset($_POST['featured_c']) && $_POST['featured_c']!='' && isset($_POST['featured_h']) && $_POST['featured_h']!=''){
			update_post_meta($last_postid, 'featured_c', 'c');
			update_post_meta($last_postid, 'featured_h', 'h');
			update_post_meta($last_postid, 'featured_type', 'both');			
		}elseif(isset($_POST['featured_c']) && $_POST['featured_c']!=''){
			update_post_meta($last_postid, 'featured_c', 'c');
			update_post_meta($last_postid, 'featured_h', 'n');
			update_post_meta($last_postid, 'featured_type', 'c');
		}elseif(isset($_POST['featured_h']) && $_POST['featured_h']!=''){
			update_post_meta($last_postid, 'featured_h', 'h');
			update_post_meta($last_postid, 'featured_c', 'n');
			update_post_meta($last_postid, 'featured_type', 'h');
		}else if(!isset($_POST['action_edit'])){
			update_post_meta($last_postid, 'featured_h', 'n');
			update_post_meta($last_postid, 'featured_c', 'n');
			update_post_meta($last_postid, 'featured_type', 'none');	
		}
	}
	/*else
	{
		update_post_meta($last_postid, 'featured_h', 'n');
		update_post_meta($last_postid, 'featured_c', 'n');
		update_post_meta($last_postid, 'featured_type', 'none');	
	}*/
	/*End post featured type in post meta table */
	
	/*Set the post per subscription limit post count on user meta table  */
	if(@$_POST['pid'] =='')
	{
			$submit_post_type = $post_type;
			$package_post=get_post_meta($_POST['package_select'],'limit_no_post',true);			
			$user_limit_post=get_user_meta($current_user_id,$submit_post_type.'_list_of_post',true);
			
			$package_selected=get_user_meta($current_user_id,'package_selected',true);/*fetch last package selected price packageid*/
			/* check last price package id with current price package id */
			$package_free_submission = get_user_meta($current_user_id,'package_free_submission',true);	
			if($package_post > $user_limit_post && $package_selected==$_POST['package_select'])
			{
				/*$limit_post=get_user_meta($current_user_id,$submit_post_type.'_list_of_post',true); */
				$limit_post=get_user_meta($current_user_id,'total_list_of_post',true);
				$limit_submit_post=get_user_meta($current_user_id,$submit_post_type.'_list_of_post',true);
				
				$package_free_submission = get_user_meta($current_user_id,'package_free_submission',true);
				if(get_post_meta($_POST['package_select'],'package_type',true) == 2){
					update_user_meta($current_user_id,$submit_post_type.'_list_of_post',$limit_submit_post+1);
					update_user_meta($current_user_id,'total_list_of_post',$limit_post+1);
					update_user_meta($current_user_id,$submit_post_type.'_package_select',$_POST['package_select']);
				}
				update_user_meta($current_user_id,'package_selected',$_POST['package_select']);
				
				if(isset($_REQUEST['package_free_submission']) &&  @$_REQUEST['package_free_submission'] >0 && get_user_meta($current_user_id,'package_free_submission_completed',true) != 'completed')
				{
					update_user_meta($current_user_id,'package_free_submission',$package_free_submission+1);
				}
				
				if(get_user_meta($current_user_id,'package_free_submission',true) == get_post_meta($_POST['package_select'],'subscription_days_free_trail',true) && get_user_meta($current_user_id,'package_free_submission_completed',true) != 'completed' && get_post_meta($_POST['package_select'],'subscription_days_free_trail',true) != '')
				{
					update_user_meta($current_user_id,'package_free_submission_completed','completed');
					$package_select = get_user_meta($current_user_id,'package_selected',true);
					$package_post_type = get_post_meta($package_select,'package_post_type',true);
					$package_post_type = explode(",",$package_post_type);
					foreach($package_post_type as $post_type)
					{
						update_user_meta($current_user_id,$post_type.'_list_of_post',0);
						update_user_meta($current_user_id,'total_list_of_post',0);
						update_user_meta($current_user_id,$post_type.'_package_select',0);
					}
					/*2014-10-11 rest set post limi in user table */
					update_user_meta($current_user_id,$submit_post_type.'_list_of_post',0);
					update_user_meta($current_user_id,'total_list_of_post',0);
					update_user_meta($current_user_id,$submit_post_type.'_package_select',0);
					update_user_meta($current_user_id,'package_selected',0);
				}
			}else
			{				
				update_user_meta($current_user_id,'package_selected',$_POST['package_select']);
				if(get_post_meta($_POST['package_select'],'package_type',true) == 2){
					update_user_meta($current_user_id,$submit_post_type.'_package_select',$_POST['package_select']);				
					update_user_meta($current_user_id,'total_list_of_post',1);
					update_user_meta($current_user_id,$submit_post_type.'_list_of_post',1);
				}
				
				if(isset($_REQUEST['package_free_submission']) &&  @$_REQUEST['package_free_submission'] >0 && get_user_meta($current_user_id,'package_free_submission_completed',true) != 'completed')
				{
					update_user_meta($current_user_id,'package_free_submission',$package_free_submission+1);
				}
				
				if(get_user_meta($current_user_id,'package_free_submission',true) == get_post_meta($_POST['package_select'],'subscription_days_free_trail',true) && get_user_meta($current_user_id,'package_free_submission_completed',true) != 'completed'  && get_post_meta($_POST['package_select'],'subscription_days_free_trail',true) != '')
				{
					update_user_meta($current_user_id,'package_free_submission_completed','completed');
					$package_select = get_user_meta($current_user_id,'package_selected',true);
					$package_post_type = get_post_meta($package_select,'package_post_type',true);
					$package_post_type = explode(",",$package_post_type);
					foreach($package_post_type as $post_type)
					{
						update_user_meta($current_user_id,$post_type.'_list_of_post',0);
						update_user_meta($current_user_id,'total_list_of_post',0);
						update_user_meta($current_user_id,$post_type.'_package_select',0);
					}
					/*2014-10-11 rest set post limi in user table */
					update_user_meta($current_user_id,$submit_post_type.'_list_of_post',0);
					update_user_meta($current_user_id,'total_list_of_post',0);
					update_user_meta($current_user_id,$submit_post_type.'_package_select',0);
					update_user_meta($current_user_id,'package_selected',0);
				}
			}			
	}
	
	/*Finish post per subscription limite post count on user meta table  */
		

	/* Its Needed  */

	if($payable_amount >= 0)
	{
		if($payable_amount == 0)
		{
			$_POST['paymentmethod'] = 'Free';
		}
		$trans_id = insert_transaction_detail($_POST['paymentmethod'],$last_postid,0,$is_package,$is_featured_h,$is_featured_c,$is_category);
		insert_update_users_packageperlist($last_postid,$_POST,$trans_id);				
	}

	/* Its Needed  */
		
		if(isset($_POST["imgarr"]) && $_POST["imgarr"]!="")
		{
			$uploaddir = TEMPLATEPATH."/images/tmp/";
			
			$dirinfo = wp_upload_dir();
			$path = $dirinfo['path'];
			$url = $dirinfo['url'];
			$subdir = $dirinfo['subdir'];
			$basedir = $dirinfo['basedir'];
			$baseurl = $dirinfo['baseurl'];
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$post_images=explode(',',$_POST['imgarr']);
			$menu_order=0;
			foreach ($post_images as $image) {
				if($image!=""){
					$upload_img_path=$uploaddir._wp_relative_upload_path( $image );
					$wp_filetype = wp_check_filetype(basename($image), null );
					$dest=$path.'/'._wp_relative_upload_path( $image);
					if(file_exists($upload_img_path)){
						copy($upload_img_path, $dest);						
						unlink($upload_img_path);
						$attachment = array('guid' => $url.'/'._wp_relative_upload_path( $image ),
										'post_mime_type' => $wp_filetype['type'],
										'post_title' => preg_replace('/\.[^.]+$/', '', basename($image)),
										'post_content' => '',
										'post_status' => 'inherit',
										'menu_order' => $menu_order++,
									);						
						$img_attachment=substr($subdir.'/'.$image,1);
						
						$attach_id = wp_insert_attachment( $attachment, $img_attachment, $last_postid );
						$upload_img_path=$path.'/'._wp_relative_upload_path( $image );
						$attach_data = wp_generate_attachment_metadata( $attach_id, $upload_img_path );
						wp_update_attachment_metadata( $attach_id, $attach_data );
					}
				}/* finish for each loop */
				
			}/* finish the for each loop */
			
		}
		do_action('process_coupons',$last_postid);
		if(!$_POST['pid']){
			update_post_meta($last_postid, 'remote_ip',getenv('REMOTE_ADDR'));
			update_post_meta($last_postid,'ip_status',$_POST['ip_status']);
			update_post_meta($last_postid,'publish_date',date('Y-m-d h:i:s'));
		}
	  /* Code for update menu for images */
	  
	  if($_POST['pid'] &&  isset($_POST["imgarr"]) && $_POST["imgarr"]!="")
	  {
			$j = 1;
			$post_images=explode(',',$_POST['imgarr']);
			foreach($post_images as $arrVal)
			{
				if($arrVal!=""){
					$expName = array_slice(explode(".",$arrVal),0,1);
					$wpdb->query('update '.$wpdb->posts.' set  menu_order = "'.$j.'" where post_name = "'.$expName[0].'"  and post_parent = "'.$_POST['pid'].'"');
					$j++;
				}
			}
	  }
	/* End Code for update menu for images */		
	
	
	/* ADMIN EMAIL START */
	$fromEmail = get_site_emailId_plugin();
	$fromEmailName = get_site_emailName_plugin();		
	$store_name = '<a href="'.home_url().'">'.get_option('blogname').'</a>';
	$admin_email_id = get_option('admin_email');
	
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
		$email_subject = __('A new post has been submitted on your site',DOMAIN);
	}
	if($_POST['pid']){
		$update_listing_notification_subject=$tmpdata['update_listing_notification_subject'];
		if(!$update_listing_notification_subject)
		{
			$update_listing_notification_subject = '[#post_type#] ID #[#submition_Id#] has been updated';
		}
		$email_subject = str_replace(array('[#post_type#]','[#submition_Id#]'),array($mail_post_title,$last_postid),$update_listing_notification_subject);
	}
	if(isset($_POST['renew'])){
		$email_subject = __(sprintf('%s renew of ID:#%s',$mail_post_title,$last_postid));
	}
	if(!$email_content){
		$email_content = __('<p>Dear [#to_name#],</p><p>A new submission has been made on your site with the details below.</p><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>',DOMAIN);
	}
	if($_POST['pid'] ){				
		$email_content = __(sprintf('<p>Dear [#to_name#],</p>
		<p>%s has been updated on your site. Here is the information about the %s:</p>
		[#information_details#]
		<br>
		<p>[#site_name#]</p>',$mail_post_title,$mail_post_title),DOMAIN);
		$email_content=$tmpdata['update_listing_notification_content'];	
		if(!$email_content)
			$email_content = "<p>Dear [#to_name#],</p><p>[#post_type#] ID #[#submition_Id#] has been updated on your site.</p><p>You can review it again by clicking on its title in this email or through your admin dashboard.</p>[#information_details#]<br><p>[#site_name#]</p>";
	}
	if(isset($_POST['renew'])){
		$email_content = __(sprintf('<p>Dear [#to_name#],</p>
		<p>%s has been renew on your site. Here is the information about the %s:</p>
		[#information_details#]
		<br>
		<p>[#site_name#]</p>',$mail_post_title,$mail_post_title),DOMAIN);
		
	}
	
	if(!$email_subject_user){
		$email_subject_user = __('Details about the listing you have submitted on [#site_title#]',DOMAIN);	
	}
	if($_POST['pid']){
		$update_listing_notification_subject=$tmpdata['update_listing_notification_subject'];
		if(!$update_listing_notification_subject)
			$update_listing_notification_subject = "[#post_type#] ID #[#submition_Id#] has been updated";
		$email_subject_user = str_replace(array('[#post_type#]','[#submition_Id#]'),array($mail_post_title,$last_postid),$update_listing_notification_subject);
		$post_updated_link=get_permalink($_POST['pid']);
		
	}
	if(isset($_POST['renew']))
	{
		$update_listing_notification_subject=$tmpdata['renew_listing_notification_subject'];
		if(!$update_listing_notification_subject){
			$update_listing_notification_subject = __('[#post_type#] renew of ID:#[#submition_Id#]',DOMAIN);	
		}
		$email_subject_user = str_replace(array('[#post_type#]','[#submition_Id#]'),array($mail_post_title,$last_postid),$update_listing_notification_subject);
		
	}
	if(@$_POST['pid'] == ''){
		$email_subject_user = str_replace(array('[#site_title#]'),array(get_option('blogname')),$email_subject_user);
	}
	if(!$email_content_user)
	{
		$email_content_user = __("<p>Howdy [#to_name#],</p><p>You have submitted a new listing. Here are some details about it,</p><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>",DOMAIN);
	}
	if($_POST['pid'])
	{
		$email_content_user=$tmpdata['update_listing_notification_content'];	
		if(!$email_content_user)
			$email_content_user = "<p>Dear [#to_name#],</p><p>[#post_type#] ID #[#submition_Id#] has been updated on your site.</p><p>You can review it again by clicking on its title in this email or through your admin dashboard.</p>[#information_details#]<br><p>[#site_name#]</p>";
	}
	if(isset($_POST['renew']))
	{
		$email_content_user=$tmpdata['renew_listing_notification_content'];
		if(!$email_content_user){
			$email_content_user = __('<p>Dear [#to_name#],</p><p>Your [#post_type#] has been renewed by you . Here is the information about the [#post_type#]:</p><p>[#information_details#]</p><p>[#site_name#]</p>',DOMAIN);	
		}
		
	}	
	$information_details = "<p>".__('ID',DOMAIN)." : ".$last_postid."</p>";
	$information_details .= '<p>'.__('View more detail of',DOMAIN).' <a href="'.get_permalink($last_postid).'">'.stripslashes($my_post['post_title']).'</a></p>';
	global $payable_amount;
	if($payable_amount > 0){
		$information_details .= '<p>'.__('Payment Status: <b>Pending</b>',DOMAIN).'</p>';
		$information_details .= '<p>'.__('Payment Method: <b>'.ucfirst(@$_POST['paymentmethod']).'</b>',DOMAIN).'</p>';
	}else{
		$information_details .= '<p>'.__('Payment Status: <b>Success</b>',DOMAIN).'</p>';
	}
	/* Get the custom fields for send via email  */
	remove_all_actions('posts_where');
	wp_reset_query();
	wp_reset_postdata();
	if(isset($_REQUEST['renew']) && $_REQUEST['renew'] == 1){
		$submit_post_type = $_POST['submit_post_type'];
	}
	$show_on_email=get_post_custom_fields_templ_plugin($submit_post_type,'',$taxonomy);
	$suc_post = get_post($last_postid);			

	$information_details='<style type="text/css">
		.cust_feild_details {
					max-width: 800px;
					padding:0;
					}
					
			.cust_feild_details li  {
					border-bottom: 1px solid #ccc;
					list-style: none;
					}
					
			.cust_feild_details li label {
					display: inline-block;
					vertical-align: top;
					width: 180px;
					float: left;
					}
			.cust_feild_details li .extra_textcontent{
					 display: inline-block;
	</style>';	
	
	if($show_on_email)
	{
		$information_details.='<ul class="cust_feild_details">';
		/*Submitted Post Title */
		if($_POST['pid']){

			if(get_post_status( $_POST['pid'] ) == 'draft')
				$post_title = $my_post['post_title'];
			else
				$post_title = '<a href="'.get_permalink($_POST['pid']).'">'.$my_post['post_title'].'</a>';
				
			$information_details.= '<li><label>'.__('Title',DOMAIN).': </label>'.$post_title.'</li>';
		}else{
			if(get_post_status( $last_postid ) == 'draft')
				$post_title = $my_post['post_title'];
			else
				$post_title = '<a href="'.get_permalink($last_postid).'">'.$my_post['post_title'].'</a>';
			$information_details.= '<li><label>'.__('Title',DOMAIN).': </label>'.$post_title.'</li>';
		}
		/* Submitted Post category */
		$category_name = wp_get_post_terms($last_postid, $taxonomy);
		if($category_name)
		{
			$_value = '';			
			foreach($category_name as $value)
			{
				$_value .= $value->name.",";
			}
			$information_details.= "<li><label>".sprintf(__('%s Category',DOMAIN),$mail_post_title).": </label> ".substr($_value,0,-1)."</li>";
		}
		
		foreach($show_on_email as $key=>$val)
		{
			$title_arr = array();
			$value_arr = array();
			$final_arr = array();
			$value2 = '';
			
			if($val['show_in_email']!='1'){
				continue;
			}
			if($key=='post_content' && $val['show_in_email'] && $my_post['post_content']!='')
			{
				$information_details.= '<li><label>'.$val['label'].': </label>'.$my_post['post_content'].'</li>';
			}
			if($key=='post_excerpt' && $val['show_in_email'] && $my_post['post_excerpt']!='')
			{
				$information_details.= '<li><label>'.$val['label'].': </label>'.$my_post['post_excerpt'].'</li>';
			}
			
			if(($val['ctype'] == 'multicheckbox') && get_post_meta($last_postid,$val['htmlvar_name'],true) !='' && $val['show_in_email']=='1')
			{
				$title_arr = explode(',',$val['option_title']);
				/* get values array */
				$value_arr = explode(',',$val['option_values']);
				/* combine  title as a key and values as value in array */
				$final_arr = @array_combine($title_arr,$value_arr);
				/* get value from database which is in array form */
				$value2 = get_post_meta($last_postid,$val['htmlvar_name'],true);
				/* get the common array value by comparing database valued array and combined array */
				
				$value = array_intersect($final_arr,$value2);
				if(!empty($value))
				{
					$value1 = array();
					foreach($value as $key=>$val1)
					{
						$value1 []= $key;
					}
				}				
				$information_details.='<li><label>'.$val['label'].': </label> '. apply_filters('tevolution_submited_email', implode(",",$value1),$val['htmlvar_name']).'</li>';
			}elseif(($val['ctype'] == 'radio') && get_post_meta($last_postid,$val['htmlvar_name'],true) !='' && $val['show_in_email']=='1')
			{
				$title_arr = explode(',',$val['option_title']);
				/* get values array */
				$value_arr = explode(',',$val['option_values']);
				/* combine  title as a key and values as value in array */
				$final_arr = @array_combine($title_arr,$value_arr);
				/* get value from database which is in array form */
				$value2 = get_post_meta($last_postid,$val['htmlvar_name'],true);
				/* get the common array value by comparing database valued array and combined array */
					
				$value_radio = @array_search($value2,$final_arr);
								
				$information_details.='<li><label>'.$val['label'].': </label> '. apply_filters('tevolution_submited_email',$value_radio).'</li>';
			}elseif($val['ctype']=='upload' && get_post_meta($last_postid,$val['htmlvar_name'],true) !='' && $val['show_in_email']=='1'){
				
				$value=apply_filters('tevolution_submited_email',get_post_meta($last_postid,$val['htmlvar_name'],true),$val['htmlvar_name']);
				$information_details.= '<li><label>'.$val['label'].': </label> <img src="'.$value.'" width="200"></li>';
			}elseif($val['ctype']=='oembed_video' && get_post_meta($last_postid,$val['htmlvar_name'],true) !='' && $val['show_in_email']=='1'){
				$embed_video = wp_oembed_get( get_post_meta($last_postid,$val['htmlvar_name'],true));            
				if($embed_video!=""){
					$video = $embed_video;
				}else{
					$video = get_post_meta($last_postid,$val['htmlvar_name'],true);
				}	
				$value=apply_filters('tevolution_submited_email',$video,$val['htmlvar_name']);
				$information_details.= '<li><label>'.$val['label'].': </label> '.$value.'</li>';
			}
			else{					
				if($val['show_in_email']=='1' && get_post_meta($last_postid,$val['htmlvar_name'],true)!="")
				{
					if($val['ctype'] == 'texteditor')
						$information_details.= '<li><label>'.$val['label'].' :</label><div class="extra_textcontent">'.apply_filters('tevolution_submited_email',get_post_meta($last_postid,$val['htmlvar_name'],true),$val['htmlvar_name']).'</div></li>';
					else	
						$information_details.= '<li><label>'.$val['label'].' :</label> '.apply_filters('tevolution_submited_email',get_post_meta($last_postid,$val['htmlvar_name'],true),$val['htmlvar_name']).'</li>';
				}
			}
			
		}			
		/* get the package information */
		if(get_post_meta($last_postid,'package_select',true))
		{
			$package_name = get_post(get_post_meta($last_postid,'package_select',true));
			$information_details.= "<li><h4>".__('Price Package Information',DOMAIN)."</h4></li>";
			$information_details.= "<li><label>".__('Package Name',DOMAIN).": </label>".$package_name->post_title."</li>";
			$information_details.= "<li><label>".__('Total Price',DOMAIN).": </label>".fetch_currency_with_position(get_post_meta($last_postid,'total_price',true))."</li>";				 
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
	
	
	$search_array = array('[#to_name#]','[#information_details#]','[#transaction_details#]','[#site_name#]','[#submited_information_link#]','[#admin_email#]','[#post_type#]','[#submition_Id#]');
	$uinfo = get_userdata($current_user_id);
	$user_fname = $uinfo->display_name;
	$user_email = $uinfo->user_email;
	$link = get_permalink($last_postid);
	$replace_array_admin = array($fromEmailName,$information_details,$information_details,$store_name,'',get_option('admin_email'),$mail_post_title,$last_postid);
	$replace_array_client =  array($user_fname,$information_details,$information_details,$store_name,$link,get_option('admin_email'),$mail_post_title,$last_postid);
	$email_content_admin = str_replace($search_array,$replace_array_admin,$email_content);
	$email_content_client = str_replace($search_array,$replace_array_client,$email_content_user);

	if($my_post['post_category']){
		templ_send_email($fromEmail,$fromEmailName,$fromEmail,$fromEmailName,$email_subject,$email_content_admin,$extra='');/*To admin email	*/
		templ_send_email($fromEmail,$fromEmailName,$user_email,$user_fname,$email_subject_user,$email_content_client,$extra='');/* to client email*/
	}

	/* ADMIN EMAIL END */
	if(($payable_amount != '' && $payable_amount > 0) && @$_POST['paymentmethod']){
		payment_menthod_response_url(@$_POST['paymentmethod'],$last_postid,@$custom_fields['renew'],@$_POST['pid'],$payable_amount);
	}else{
		if(isset($_POST['action']) && $_POST['action']=='edit'){
			$suburl = "&pid=$last_postid&action=edit";
		}elseif(isset($_POST['renew']) && $_POST['renew']==1){
			$suburl = "&pid=$last_postid&renew=1";
		}else{
			$suburl = "&pid=$last_postid";
		}
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
		
}else{ echo 'cheating huhhhhhh......'; }/* End submit post type submission	*/
?>