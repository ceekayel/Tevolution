<?php
/*
 * update the data if paymnent is successful after upgrade
 */
add_action('init','tevolution_post_upgrade_content',20);
function tevolution_post_upgrade_content()
{
	if(isset($_GET['pid']) && $_GET['pid'] !=''){
		$catids_arr = array();
		$my_post = array();
		$pid = $_REQUEST['pid']; /* it will be use when going for RENEW */
		$upgrade_post = get_post_meta($pid,'upgrade_data',true);
		$upgrade_post['package_select']=$upgrade_post['pkg_id'];
		$last_postid = $pid;
		$alive_days = $upgrade_post['alive_days'];
		$payment_method = $_REQUEST['pmethod'];
		$coupon = @$upgrade_post['add_coupon'];
		$featured_type = @$upgrade_post['featured_type'];
		$payable_amount = @$upgrade_post['total_price'];
		$post_tax = fetch_page_taxonomy($upgrade_post['cur_post_id']);
		/* Here array separated by category id and price amount */
		if($upgrade_post['category'])
		{
			$category_arr = $upgrade_post['category'];
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
	
		
		$submit_post_type = get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true);
		$package_post=get_post_meta($upgrade_post['package_select'],'limit_no_post',true);
		/*$user_limit_post=get_user_meta($current_user_id,$submit_post_type.'_list_of_post',true);*/
		$user_limit_post=get_user_meta($current_user_id,'total_list_of_post',true);
	
			/*$limit_post=get_user_meta($current_user_id,$submit_post_type.'_list_of_post',true);*/
		
			update_post_meta($last_postid,'package_select',$upgrade_post['package_select']);				
			update_post_meta($last_postid,'paid_amount',$upgrade_post['total_price']);				
			$limit_post=get_user_meta($current_user_id,'total_list_of_post',true);				
			update_user_meta($current_user_id,$submit_post_type.'_list_of_post',$limit_post+1);
			update_user_meta($current_user_id,'total_list_of_post',$limit_post+1);
			update_user_meta($current_user_id,$submit_post_type.'_package_select',$upgrade_post['package_select']);
			update_user_meta($current_user_id,'package_selected',$upgrade_post['package_select']);
			
		foreach($upgrade_post as $key=>$val)
		{ 
			if($key != 'category' && $key != 'paid_amount' && $key != 'post_title' && $key != 'post_content' && $key != 'imgarr' && $key != 'Update' && $key != 'post_excerpt' && $key != 'alive_days')
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
		/* set post categories start */
		wp_set_post_terms( $last_postid,'',$post_tax,false);
		if($post_category){
			foreach($post_category as $_post_category)
			{ 
				if(taxonomy_exists($post_tax)):
					wp_set_post_terms( $last_postid,$_post_category,$post_tax,true);
				endif;
			}
		} 
		/* set post categories end */
	 
		/* Condition for Edit post */
	
		if(class_exists('monetization')){	
			global $monetization;
			$monetize_settings = $monetization->templ_get_price_info($upgrade_post['package_select']);
			update_post_meta($last_postid,'alive_days',$monetize_settings[0]['alive_days']);
		}
				
		if(isset($_REQUEST['paydeltype']) && $_REQUEST['paydeltype']=='prebanktransfer')
		{
			$post_default_status = get_post_status($_REQUEST['pid']);
			$wpdb->query("UPDATE $wpdb->posts SET post_status='".$post_default_status."' where ID = '".$get_post_id."'");
		}
		/* End Code for update menu for images */
		/* ADMIN EMAIL START */
		$fromEmail = get_site_emailId_plugin();
		$fromEmailName = get_site_emailName_plugin();
		$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
		$admin_email_id = get_option('admin_email');
		$tmpdata = get_option('templatic_settings');
		$email_content =  @stripslashes($tmpdata['post_submited_success_email_content']);
		$email_subject =  @stripslashes($tmpdata['post_submited_success_email_subject']);
		
		$email_content_user =  @stripslashes($tmpdata['payment_success_email_content_to_client']);
		$email_subject_user =  @stripslashes($tmpdata['payment_success_email_subject_to_client']);
		
		
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
		if($_REQUEST['pid']){
			$email_subject = __(sprintf('%s updated of ID:#%s',$mail_post_title,$last_postid),DOMAIN);
		}
		if(isset($upgrade_post['renew'])){
			$email_subject = __(sprintf('%s renew of ID:#%s',$mail_post_title,$last_postid),DOMAIN);
		}
		if(!$email_content){
			$email_content = __('<p>Dear [#to_name#],</p><p>A new submission has been made on your site with the details below.</p><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>',DOMAIN);
		}
		if($_REQUEST['pid'] ){
			$email_content = __(sprintf('<p>Dear [#to_name#],</p>
			<p>%s has been updated on your site. Here is the information about the %s:</p>
			[#information_details#]
			<br>
			<p>[#site_name#]</p>',$mail_post_title,$mail_post_title));
		}
		if(isset($_SESSION['upgrade_post']['renew'])){
			$email_content = __(sprintf('<p>Dear [#to_name#],</p>
			<p>%s has been renew on your site. Here is the information about the %s:</p>
			[#information_details#]
			<br>
			<p>[#site_name#]</p>',$mail_post_title,$mail_post_title));
			
		}				
		
		if(!$email_subject_user){
			$email_subject_user = __(sprintf('New %s listing of ID:#%s',$mail_post_title,$last_postid));	
		}
		if($_REQUEST['pid']){
			$email_subject_user = __(sprintf('%s updated of ID:#%s',$mail_post_title,$last_postid));
		}
		if(isset($_SESSION['upgrade_post']['renew']))
		{
			$email_subject_user = __(sprintf('%s renew of ID:#%s',$mail_post_title,$last_postid));
			
		}	
		if(!$email_content_user)
		{
			$email_content_user = __("<p>Hello [#to_name#],</p><p>Your submission has been approved! You can see the listing here:</p><p>[#transaction_details#]</p><p>If you'll have any questions about this please send an email to [#admin_email#]</p><p>Thanks!,<br/>[#site_name#]</p>",DOMAIN);
		}
		if($_REQUEST['pid'])
		{
			$email_content_user = __(sprintf('<p>Dear [#to_name#],</p><p>Your %s has been updated by you . Here is the information about the %s:</p>[#information_details#]<br><p>[#site_name#]</p>',$mail_post_title,$mail_post_title),DOMAIN);
		}
		if(isset($_SESSION['upgrade_post']['renew']))
		{
			$email_content_user = __(sprintf('<p>Dear [#to_name#],</p><p>Your %s has been renew by you . Here is the information about the %s:</p>[#information_details#]<br><p>[#site_name#]</p>',$mail_post_title,$mail_post_title),DOMAIN);
			
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
		$post_type=get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true);
		$show_on_email=get_post_custom_fields_templ_plugin($post_type,$post_category,$post_tax);	
		$suc_post = get_post($last_postid);
			
			
			
	}
}
?>