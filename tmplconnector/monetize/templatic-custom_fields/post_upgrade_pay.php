<?php 
global $wpdb,$last_postid,$payable_amount;

global $current_user;
$payment_details = $_SESSION['upgrade_post'];
$current_user = wp_get_current_user();
$current_user_id = $current_user->ID;
$last_postid = $_REQUEST['pid'];
$post_id = $_SESSION['upgrade_post']['pid'];
$payable_amount = $_SESSION['upgrade_post']['total_price'];


/* Add upgrade request of thr post */
update_post_meta($post_id ,'upgrade_request',1);
update_post_meta($post_id ,'upgrade_data',$_SESSION['upgrade_post']);
update_post_meta($post_id ,'upgrade_method',$_REQUEST['paymentmethod']);
$post_category=$_SESSION['upgrade_post']['category'];

/* fetch package information if monetization is activated */
if(is_active_addons('monetization') && class_exists('monetization')){
	global $monetization;
	$listing_price_info = $monetization->templ_get_price_info($_SESSION['upgrade_post']['package_select'],$_SESSION['upgrade_post']['total_price']);
	$listing_price_info = $listing_price_info[0];
	$payable_amount = $_SESSION['upgrade_post']['total_price'];
	/* calculate total amout with coupon */
	if($_SESSION['upgrade_post']['add_coupon'])
	{
		$payable_amount = get_payable_amount_with_coupon_plugin($payable_amount,$_SESSION['upgrade_post']['add_coupon']);
	}
	global $wpdb;
	$upgrade_data = get_post_meta($_REQUEST['pid'],'upgrade_data',true);
	$paymentmethod = get_post_meta($_REQUEST['pid'],'upgrade_method',true);
	$upgrade_data['total_price'] = $payable_amount;
	
	$post_tax=$_SESSION['upgrade_post']['cur_post_taxonomy'];
	/*wp_delete_object_term_relationships( $post_id, $post_tax );
	foreach($post_category as $_post_category)
	{	
		$post_cat_id=explode(',',$_post_category);	
		if(taxonomy_exists($post_tax)):		
			wp_set_post_terms( $post_id,$post_cat_id[0],$post_tax,true);
		endif;
	}*/
	update_post_meta($post_id ,'upgrade_data',$upgrade_data);
	update_post_meta($post_id ,'paid_amount',$payable_amount);
	update_post_meta($post_id ,'total_price',$payable_amount);
	update_post_meta($post_id ,'package_select',$_SESSION['upgrade_post']['package_select']);
	update_user_meta($current_user_id,'package_selected',$_SESSION['upgrade_post']['package_select']);
	update_user_meta($current_user_id, get_post_type( $post_id ).'_package_select',$_SESSION['upgrade_post']['package_select']);
	/* redirect on preview page if monetization active + no payment method selected */
	if($_REQUEST['pid']=='' && isset($_REQUEST['paymentmethod']) && $_REQUEST['paymentmethod'] == '' && $payable_amount > 0)
	{
		wp_redirect(get_option( 'siteurl' ).'/?page=payment&msg=nopaymethod');
		exit;
	}
}else{
	$payable_amount =0;
}
$cat_display = get_option('templatic-category_type');

if($_POST){

	if($_POST['paynow']){
		$catids_arr = array();
		$my_post = array();
		$upgrade_post = $_SESSION['upgrade_post'];
		$alive_days = $listing_price_info['alive_days'];
		$payment_method = $_REQUEST['paymentmethod'];
		$coupon = @$upgrade_post['add_coupon'];
		$featured_type = @$upgrade_post['featured_type'];
		$pid = $_REQUEST['pid']; /* it will be use when going for RENEW */
		$post_tax = fetch_page_taxonomy($_SESSION['custom_fields']['cur_post_id']);		
		$last_postid = $post_id;
		if($payable_amount <= 0)
		{	
			if($_SESSION['upgrade_post']['last_selected_pkg'] !='')
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
				$post_default_status = 'publish';
			}
		}else
		{
			$post_default_status = 'publish';
		}
		if(is_active_addons('monetization')){
			global $trans_id;
			//$trans_id = get_transaction_detail($_REQUEST['paymentmethod'],$post_id);
			$trans_id = insert_transaction_detail($_REQUEST['paymentmethod'],$post_id,$is_upgrade=1);
					
		} 
		///////ADMIN EMAIL START//////
			$fromEmail = get_site_emailId_plugin();
			$fromEmailName = get_site_emailName_plugin();
			$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
			$admin_email_id = get_option('admin_email');
			$tmpdata = get_option('templatic_settings');
			$email_content =  @stripslashes($tmpdata['admin_post_upgrade_email_content']);
			$email_subject =  @stripslashes($tmpdata['admin_post_upgrade_email_subject']);
			
			$email_content_user =  @stripslashes($tmpdata['client_post_upgrade_email_content']);
			$email_subject_user =  @stripslashes($tmpdata['client_post_upgrade_email_subject']);
			
			
			$mail_post_type_object = '';
			$mail_post_title ='';
			if($post_id){
				$mail_post_type_object = get_post_type_object(get_post_type($post_id));
				$mail_post_title = $mail_post_type_object->labels->menu_name;
			}
			
			if(function_exists('icl_t')){
				icl_register_string(DOMAIN,$mail_post_title,$mail_post_title);
				$mail_post_title = icl_t(DOMAIN,$mail_post_title,$mail_post_title);
			}else{
				$mail_post_title = @$mail_post_title;
			}
			
			
			if(@$email_subject == '')
			{
				$email_subject = __('A New Upgrade Request of ID:#[#post_id#]',DOMAIN);
			}
	
			//$email_subject = __(sprintf('A New Upgrade Request of ID:#%s',$post_id),DOMAIN);
			if(@$email_content == '')
			{
				$email_content = __('<p>Howdy [#to_name#],</p><p>A New Upgrade request has been submited to your site.</p><br/>Here are some details about it.<br/><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>',DOMAIN);
			}
			

			if(@$email_subject_user == '')
			{
				$email_subject_user = __('Payment Pending For Upgrade Request: #[#post_id#]',DOMAIN);
			}
			if(@$email_content_user == '')
			{
				$email_content_user = __("<p>Dear [#to_name#],</p><p>Your [#post_title#] has been updated by you . Here is the information about the [#post_title#]:</p>[#information_details#]<br><p>[#site_name#]</p>",DOMAIN);
			}
			
			$information_details = "<p>".__('ID',DOMAIN)." : ".$post_id."</p>";
			$information_details .= '<p>'.__('View more detail of',DOMAIN).' <a href="'.get_permalink($post_id).'">'.stripslashes($my_post['post_title']).'</a></p>';
			
			
			global $payable_amount;
			if(is_active_addons('monetization') && $payable_amount > 0){
				$information_details .= '<p>'.__('Payment Status: <b>Pending</b>',DOMAIN).'</p>';
				$information_details .= '<p>'.__('Payment Method: ' ,DOMAIN).'<b>'.ucfirst(@$_POST['paymentmethod']).'</b></p>';
			}else{
				$information_details .= '<p>'.__('Payment Status: <b>Success</b>',DOMAIN).'</p>';
			}
			if(isset($_POST['paymentmethod']) && $_POST['paymentmethod'] == 'prebanktransfer')
			{
				$pmethod = 'payment_method_'.$_POST['paymentmethod'];
				$payment_detail = get_option($pmethod,true);
				$bankname = $payment_detail['payOpts'][0]['value'];
				$account_id = $payment_detail['payOpts'][1]['value'];
				$information_details .= '<p>'.__('Bank Name: ',DOMAIN).'<b>'.ucfirst(@$bankname).'</b></p>';
				$information_details .= '<p>'.__('Account Number: ',DOMAIN).'<b>'.@$account_id.'</b></p>';
			}
			$post_type=get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true);
			$show_on_email=get_post_custom_fields_templ_plugin($post_type,$post_category,$post_tax);	
			$suc_post = get_post($post_id);			

				$subject_search_array = array('[#post_id#]');
				$subject_replace_array = array($post_id);
				$email_subject = str_replace($subject_search_array,$subject_replace_array,$email_subject);
				$email_subject_user = str_replace($subject_search_array,$subject_replace_array,$email_subject_user);
				$search_array = array('[#to_name#]','[#information_details#]','[#transaction_details#]','[#site_name#]','[#submited_information_link#]','[#admin_email#]','[#post_type_name#]');
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
				payment_upgrade_response_url(@$_REQUEST['paymentmethod'],$last_postid,'upgrade',@$_REQUEST['pid'],$payable_amount);
			}else{
				$suburl = "&upgrade=upgrade&pid=$last_postid";
				if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
					global $sitepress;
					if(isset($_REQUEST['lang'])){
						$url = get_option('siteurl').'/?page=success&lang='.$_REQUEST['lang'].$suburl;
					}elseif($sitepress->get_current_language()){
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
			}exit;
			
	}
}
?>
