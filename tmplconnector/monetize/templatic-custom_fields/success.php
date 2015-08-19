<?php
/*
 * success page after successful submission.
 */
$order_id = $_REQUEST['pid'];
global $page_title,$wpdb;

/* add background color and image set in customizer */
add_action('wp_head','show_background_color');
if(!function_exists('show_background_color'))
{
	function show_background_color()
	{
	/* Get the background image. */
		$image = get_background_image();
		/* If there's an image, just call the normal WordPress callback. We won't do anything here. */
		if ( !empty( $image ) ) {
			_custom_background_cb();
			return;
		}
		/* Get the background color. */
		$color = get_background_color();
		/* If no background color, return. */
		if ( empty( $color ) )
			return;
		/* Use 'background' instead of 'background-color'. */
		$style = "background: #{$color};";
	?>
		<style type="text/css">
			body.custom-background {
				<?php echo trim( $style );?>
			}
		</style>
	<?php
	}
}
global $wpdb;
if($_REQUEST['pid']){
	$post_type = get_post_type($_REQUEST['pid']);
	$post_type_object = get_post_type_object($post_type);
	$post_type_label = ( @$post_type_object->labels->post_name ) ? @$post_type_object->labels->post_name  :  $post_type_object->labels->singular_name ;
}
if(isset($_REQUEST['renew']) && $_REQUEST['renew']!="")
{
	$page_title = __('Renew Successfully Information',DOMAIN);
}elseif($_REQUEST['action']=='edit'){
	
	$page_title = $post_type_label.' '.__('Updated Successfully',DOMAIN);
	if(function_exists('icl_register_string')){
		$context = get_option('blogname');
		icl_register_string($context,$post_type_label." Updated",$post_type_label." Updated");
		$page_tile = icl_t($context,$post_type_label." Updated",$post_type_label." Updated");
	}
}elseif(isset($_REQUEST['upgrade']) && $_REQUEST['upgrade']!=""){
		if(function_exists('icl_register_string')){
			icl_register_string(DOMAIN,$post_type_label."success",$post_type_label);
		    $post_type_label = icl_t(DOMAIN,$post_type_label."success",$post_type_label);
		}
		$page_title = $post_type_label.' '.__('Upgraded Successfully',DOMAIN);

}else{
	if(function_exists('icl_register_string')){
		icl_register_string(DOMAIN,$post_type_label."success",$post_type_label);
		 $post_type_label = icl_t(DOMAIN,$post_type_label."success",$post_type_label);
	    }
	if($_REQUEST['pid'] && !isset($_REQUEST['action_edit']))
		$page_title = $post_type_label.' '.__('Submitted Successfully',DOMAIN);
	elseif(isset($_REQUEST['action_edit']))
		$page_title = $post_type_label.' '.__('Updated Successfully',DOMAIN);
	else
		$page_title = $post_type_label.' '.__('Thank you for purchasing a subscription plan',DOMAIN);
}
get_header(); 
do_action('templ_before_success_container_breadcrumb');
if(isset($_REQUEST['paydeltype']) && $_REQUEST['paydeltype']=='prebanktransfer' && @$_REQUEST['upgrade'] =='')
{
	/*MAIL SENDING TO CLIENT AND ADMIN START*/
	global $payable_amount,$last_postid,$stripe_options,$wpdb,$monetization,$sql_post_id;
	$transaction_tabel = $wpdb->prefix."transactions";
	$user_id = $wpdb->get_var("select user_id from $transaction_tabel order by trans_id DESC limit 1");
	$user_id = $user_id;
	$sql_transaction = "select max(trans_id) as trans_id from $transaction_tabel where user_id = $user_id and status=0 ";
	$sql_data = $wpdb->get_var($sql_transaction);
	$sql_status_update = $wpdb->query("update $transaction_tabel set status=0 where trans_id=$sql_data");
	$get_post_id = $wpdb->get_var("select post_id from $transaction_tabel where trans_id=$sql_data");
	$tmpdata = get_option('templatic_settings');
	/*$post_default_status = $tmpdata['post_default_status_paid'];*/
	$post_default_status = 'draft'; /* if payment method = prebank transfer no option affected - listing shold be ib draft*/

	$wpdb->query("UPDATE $wpdb->posts SET post_status='".$post_default_status."' where ID = '".$get_post_id."'");
	
	/*$trans_status = $wpdb->query("update $transaction_tabel SET status = 1 where post_id = '".$get_post_id."'");*/
	$pmethod = 'payment_method_'.$_REQUEST['paydeltype'];
	$payment_detail = get_option($pmethod,true);
	$bankname = $payment_detail['payOpts'][0]['value'];
	$account_id = $payment_detail['payOpts'][1]['value'];
	$sql_post_id = $wpdb->get_var("select post_id from $transaction_tabel where user_id = $user_id and trans_id=$sql_data");
	$suc_post = get_post($sql_post_id);
	$payment_date = $wpdb->get_var("select payment_date from $transaction_tabel where user_id = $user_id and trans_id=$sql_data");
	$sql_payable_amt = $wpdb->get_var("select payable_amt from $transaction_tabel where user_id = $user_id and trans_id=$sql_data");
	$payforfeatured_h = $wpdb->get_var("select payforfeatured_h from $transaction_tabel where user_id = $user_id and trans_id=$sql_data");
	$payforfeatured_c = $wpdb->get_var("select payforfeatured_c from $transaction_tabel where user_id = $user_id and trans_id=$sql_data");
	$sql_payable_amt = display_amount_with_currency_plugin(number_format($sql_payable_amt,2));
	$post_title = $suc_post->post_title;
	$post_content = $suc_post->post_content;
	$paid_amount = display_amount_with_currency_plugin(get_post_meta($sql_post_id,'paid_amount',true));
	$user_details = get_userdata( $user_id );
	$first_name = $user_details->user_login;
	$last_name = $user_details->last_name;
	$fromEmail = get_site_emailId_plugin();
	$fromEmailName = get_site_emailName_plugin(); 	
	$toEmail = apply_filters('client_booking_success_email',$user_details->user_email,$_REQUEST['pid']);
	$toEmailName = apply_filters('client_booking_success_name',$first_name,$_REQUEST['pid']);
	$theme_settings = get_option('templatic_settings');
	
	$submiited_id  = $sql_post_id;
	$submitted_link = '<a href="'.get_permalink($sql_post_id).'">'.$suc_post->post_title.'</a>';
	/*	Payment success Mail to client END		*/
	$client_mail_subject =  apply_filters('prebanktransfer_client_subject',$theme_settings['payment_success_email_subject_to_client']);
	$client_mail_content = stripslashes($theme_settings['user_post_submited_success_email_content']);
	
	if(@$client_mail_subject == '')
	{
		$client_mail_subject = __('Thank you for your submission!',DOMAIN);
	}
	if(@$client_mail_content == '')
	{
		$client_mail_content = __("<p>Howdy [#to_name#],</p><p>You have submitted a new listing. Here are some details about it</p><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>",DOMAIN);
	}
	$paymentupdsql = "select option_value from $wpdb->options where option_name='payment_method_".$_REQUEST['paydeltype']."'";
	$paymentupdinfo = $wpdb->get_results($paymentupdsql);
	$paymentInfo = unserialize($paymentupdinfo[0]->option_value);
	$payment_method_name = $paymentInfo['name'];
	$payOpts = $paymentInfo['payOpts'];
	$bankInfo = $payOpts[0]['value'];
	$accountinfo = $payOpts[1]['value'];
	if($tmpdata['post_default_status_paid'] == 'publish')
	{
		$payment_status = __("Approved",DOMAIN);
	}
	else
	{
		$payment_status = __("Pending",DOMAIN);
	}
	$payment_type = $payment_detail['name'];
	$orderId = $sql_post_id?$sql_post_id:mt_rand(100000, 999999);
	$payment_date =  date_i18n(get_option('date_format'),strtotime($payment_date));
	$transaction_details="";
	$transaction_details .= "<br/>\r\n-------------------------------------------------- <br/>\r\n";
	$transaction_details .= __('Payment Details for',DOMAIN).": $post_title <br/>\r\n";
	$transaction_details .= "-------------------------------------------------- <br/>\r\n";
	$transaction_details .= 	__('Status',DOMAIN).": $payment_status <br/>\r\n";
	$transaction_details .=     __('Type',DOMAIN).": $payment_type <br/>\r\n";
	$transaction_details .= 	__('Date',DOMAIN).": $payment_date <br/>\r\n";
	$transaction_details .= 	__('Bank Name',DOMAIN).": $bankInfo <br/>\r\n";
	$transaction_details .= 	__('Account Number',DOMAIN).": $accountinfo <br/>\r\n";
	$transaction_details .= 	__('Reference Number',DOMAIN).": $orderId <br/>\r\n";
	$transaction_details .= "-------------------------------------------------- <br/>\r\n";
	$transaction_details = $transaction_details;
	$client_transaction_mail_content = '<p>'.__('Thank you for your cooperation with us.',DOMAIN).'</p>';
	/*$client_transaction_mail_content .= '<p>You successfully completed your payment by Pre Bank Transfer.</p>';*/
	$client_transaction_mail_content .= "<p>".__('Your submitted id is',DOMAIN)." : ".$sql_post_id."</p>";
	$client_transaction_mail_content .= '<p>'.__('View more detail from',DOMAIN).' <a href="'.get_permalink($sql_post_id).'">'.$suc_post->post_title.'</a></p>';
	
	$search_array = array('[#to_name#]','[#payable_amt#]','[#information_details#]','[#site_name#]','[#admin_email#]','[#user_login#]');
	$replace_array = array($toEmailName,$sql_payable_amt,$transaction_details,$fromEmailName,get_option('admin_email'),$toEmailName);
	
	$client_message = apply_filters('prebanktransfer_client_message',str_replace($search_array,$replace_array,$client_mail_content),$toEmailName,$fromEmailName);
	if(isset($_REQUEST['upgrade']) && $_REQUEST['upgrade']!=''){
	
	}else{
		templ_send_email($fromEmail,$fromEmailName,$toEmail,$toEmailName,$client_mail_subject,$client_message,$extra='');/*/To client email*/
	}
	
	$transaction_details="";
	$transaction_details .= "<br/>\r\n-------------------------------------------------- <br/>\r\n";
	$transaction_details .= __('Payment Details for',DOMAIN).": $post_title <br/>\r\n";
	$transaction_details .= "-------------------------------------------------- <br/>\r\n";
	$transaction_details .= 	__('Status',DOMAIN).": $payment_status <br/>\r\n";
	$transaction_details .=     __('Type',DOMAIN).": $payment_type <br/>\r\n";
	$transaction_details .= 	__('Date',DOMAIN).": $payment_date <br/>\r\n";
	$transaction_details .= 	__('Reference Number',DOMAIN).": $orderId <br/>\r\n";
	$transaction_details .= "-------------------------------------------------- <br/>\r\n";
	/* Check psot dedault status for paid listing is publish then listing and transction will be publish and approve */
	if($tmpdata['post_default_status_paid']=='publish'){
		
		if($payforfeatured_h == 1  && $payforfeatured_c == 1){
			update_post_meta($_REQUEST['pid'], 'featured_c', 'c');
			update_post_meta($_REQUEST['pid'], 'featured_h', 'h');
			update_post_meta($_REQUEST['pid'], 'featured_type', 'both');			
		}elseif($payforfeatured_c == 1){
			update_post_meta($_REQUEST['pid'], 'featured_c', 'c');
			update_post_meta($_REQUEST['pid'], 'featured_type', 'c');
		}elseif($payforfeatured_h == 1){
			update_post_meta($_REQUEST['pid'], 'featured_h', 'h');
			update_post_meta($_REQUEST['pid'], 'featured_type', 'h');
		}else{
			update_post_meta($_REQUEST['pid'], 'featured_type', 'none');	
		}
		
		$wpdb->query("UPDATE $wpdb->posts SET post_status='".$tmpdata['post_default_status_paid']."' where ID = '".$_REQUEST['pid']."'");
		$trans_status = $wpdb->query("update $transaction_tabel SET status = 1 where post_id = ".$_REQUEST['pid']);
		
	}
	/*Payment success Mail to admin START*/
	$admin_mail_subject =  apply_filters('prebanktransfer_admin_subject',__('Submission pending payment',DOMAIN));
	$admin_mail_content = $theme_settings['pre_payment_success_email_content_to_admin'];
	if(@$admin_mail_subject == '')
	{
		$admin_mail_subject = __('Submission pending payment',DOMAIN);
	}
	if(@$admin_mail_content == '')
	{
		$admin_mail_content = "<p>Dear [#to_name#],</p><p>A payment from username [#user_login#] is now pending on a submission or subscription to one of your plans.</p><p>[#transaction_details#]</p><p>Thanks!<br/>[#site_name#]</p>";
	}
	
	$search_array = array('[#to_name#]','[#payable_amt#]','[#transaction_details#]','[#site_name#]','[#admin_email#]','[#user_login#]');
	$replace_array = array($fromEmailName,$sql_payable_amt,$transaction_details,$fromEmailName,get_option('admin_email'),$toEmailName);
	$admin_message = apply_filters('prebanktransfer_admin_message',str_replace($search_array,$replace_array,$admin_mail_content),$fromEmailName,$toEmailName);
	if(isset($_REQUEST['upgrade']) && $_REQUEST['upgrade']!=''){
	
	}else{
		templ_send_email($fromEmail,$fromEmailName,$fromEmail,$fromEmailName,$admin_mail_subject,$admin_message,$extra='');/* To admin email*/
	}
	/*Payment success Mail to admin FINISH*/
}
$amout=get_post_meta($_REQUEST['pid'],'total_price',true);
if($amout=='0' || $amout==''){
	global $wpdb;
	$transaction_tabel = $wpdb->prefix."transactions";
	$tmpdata = get_option('templatic_settings');
	
	if($_SESSION['custom_fields']['last_selected_pkg'])
	{
		$get_last_trans_status = $wpdb->get_var("select status from $transaction_tabel t where post_id='".$_SESSION['custom_fields']['user_last_postid']."' AND (t.package_type is NULL OR t.package_type=0) order by t.trans_id desc");
		if($get_last_trans_status==2){
			$get_last_trans_status=0;
		}
		if(@$get_last_trans_status !='')
			$trans_status = $wpdb->query("update $transaction_tabel SET status = ".$get_last_trans_status." where post_id = ".$_REQUEST['pid']);

	}
	else
	{
		if($tmpdata['post_default_status']=='publish' && !isset($_SESSION['custom_fields']['last_selected_pkg']) && $_SESSION['custom_fields']['last_selected_pkg'] == '' && (!isset($_REQUEST['upgrade']) && $_REQUEST['upgrade'] != 1) && (isset($_REQUEST['pid']) && $_REQUEST['pid'] != '')){
			if($amout == 0 && isset($_REQUEST['renew']) && $_REQUEST['renew'] ==1){
				$post_status = $tmpdata['post_default_status'];
				$post_default_status= ($post_status)? $post_status :  'draft';
			}else{
				$post_default_status= (isset($_REQUEST['pid']) && $_REQUEST['pid']!="")? get_post_status($_REQUEST['pid']):  'publish';
			}
			$trans_status = $wpdb->query("update $transaction_tabel SET status = 1 where post_id = ".$_REQUEST['pid']);
			$wpdb->query("UPDATE $wpdb->posts SET post_status='".$post_default_status."' where ID = '".$_REQUEST['pid']."'");
		}elseif(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
			$trans_status = $wpdb->query("update $transaction_tabel SET status = 0 where post_id = ".$_REQUEST['pid']);
		}
	}	
	
}
global $upload_folder_path,$wpdb;
?>
    <div class="large-9 small-12 columns <?php echo stripslashes(get_option('ptthemes_sidebar_left')); ?>" id="content">
	 <h1 class="page-title"><?php echo $page_title; ?></h1>
     <div class="posted_successful">
	 <?php
		do_action('tevolution_before_submition_success_msg');
		do_action('tevolution_submition_success_msg');
		do_action('tevolution_after_submition_success_msg');
	 ?> 
	</div>
     <?php if(!isset($_REQUEST['upgrade']) && $_REQUEST['upgrade'] =='' && (isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''))
		{
			do_action('tevolution_submition_success_post_content'); 
		}?>
	</div> <!-- content #end -->
<?php 
	if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=""){
		$ptype = $wpdb->get_var("select post_type from $wpdb->posts where $wpdb->posts.ID = '".$_REQUEST['pid']."'");
		$cus_post_type = apply_filters('success_page_sidebar_post_type',$ptype);
	}	
?>
<aside class="sidebar large-3 small-12 columns" id="sidebar-primary">
<?php 
	if(isset($cus_post_type) && $cus_post_type!="" && is_active_sidebar($cus_post_type.'_detail_sidebar')){
		dynamic_sidebar($cus_post_type.'_detail_sidebar');
	}else{ 
		dynamic_sidebar('primary-sidebar');
	}
?>
</aside>

<?php get_footer(); ?>