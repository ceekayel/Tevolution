<?php 
$order_id = $_REQUEST['pid'];
global $page_title,$wpdb;

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
			<?php echo trim( $style );
			?>
		</style>
	<?php
	}
}
if($_REQUEST['pid']){
	$post_type = get_post_type($_REQUEST['pid']);
	$post_type_object = get_post_type_object($post_type);
	$post_type_label = ( @$post_type_object->labels->post_name ) ? @$post_type_object->labels->post_name  :  $post_type_object->labels->singular_name ;
}
if(isset($_REQUEST['renew']) && $_REQUEST['renew']!="")
{
	$page_title = RENEW_SUCCESS_TITLE;
}elseif($_SESSION['custom_fields']['action']=='edit'){
	
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
	$page_title = $post_type_label.' '.__('Submitted Successfully',DOMAIN);
}
get_header(); 
do_action('templ_before_success_container_breadcrumb');
if(isset($_REQUEST['paydeltype']) && $_REQUEST['paydeltype']=='prebanktransfer' && @$_REQUEST['upgrade'] =='')
{
	//MAIL SENDING TO CLIENT AND ADMIN START
	global $payable_amount,$last_postid,$stripe_options,$wpdb,$monetization,$sql_post_id;
	$transaction_tabel = $wpdb->prefix."transactions";
	$user_id = $wpdb->get_var("select user_id from $transaction_tabel order by trans_id DESC limit 1");
	$user_id = $user_id;
	$sql_transaction = "select max(trans_id) as trans_id from $transaction_tabel where user_id = $user_id and status=0 ";
	$sql_data = $wpdb->get_var($sql_transaction);
	$sql_status_update = $wpdb->query("update $transaction_tabel set status=0 where trans_id=$sql_data");
	$get_post_id = $wpdb->get_var("select post_id from $transaction_tabel where trans_id=$sql_data");
	$tmpdata = get_option('templatic_settings');
	//$post_default_status = $tmpdata['post_default_status_paid'];
	$post_default_status = 'draft'; /* if payment method = prebank transfer no option affected - listing shold be ib draft*/
	$wpdb->query("UPDATE $wpdb->posts SET post_status='".$post_default_status."' where ID = '".$get_post_id."'");
	//$trans_status = $wpdb->query("update $transaction_tabel SET status = 1 where post_id = '".$get_post_id."'");
	$pmethod = 'payment_method_'.$_REQUEST['paydeltype'];
	$payment_detail = get_option($pmethod,true);
	$bankname = $payment_detail['payOpts'][0]['value'];
	$account_id = $payment_detail['payOpts'][1]['value'];
	$sql_post_id = $wpdb->get_var("select post_id from $transaction_tabel where user_id = $user_id and trans_id=$sql_data");
	$suc_post = get_post($sql_post_id);
	$payment_date = $wpdb->get_var("select payment_date from $transaction_tabel where user_id = $user_id and trans_id=$sql_data");
	$sql_payable_amt = $wpdb->get_var("select payable_amt from $transaction_tabel where user_id = $user_id and trans_id=$sql_data");
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
	//	Payment success Mail to client END		
	$client_mail_subject =  apply_filters('prebanktransfer_client_subject',$theme_settings['payment_success_email_subject_to_client']);
	$client_mail_content = stripslashes($theme_settings['post_pre_bank_trasfer_msg_content']);
	
	$client_transaction_mail_content = '<p>'.__('Thank you for your cooperation with us.',DOMAIN).'</p>';
	//$client_transaction_mail_content .= '<p>You successfully completed your payment by Pre Bank Transfer.</p>';
	$client_transaction_mail_content .= "<p>".__('Your submitted id is',DOMAIN)." : ".$sql_post_id."</p>";
	$client_transaction_mail_content .= '<p>'.__('View more detail from',DOMAIN).' <a href="'.get_permalink($sql_post_id).'">'.$suc_post->post_title.'</a></p>';
	
	$search_array = array('[#payable_amt#]','[#bank_name#]','[#account_number#]','[#submition_Id#]','[#submited_information_link#]','[#site_name#]','[#admin_email#]');
	$replace_array = array($sql_payable_amt,$bankname,$account_id,$submiited_id,$submitted_link,$fromEmailName,get_option('admin_email'));
	
	$client_message = apply_filters('prebanktransfer_client_message',str_replace($search_array,$replace_array,$client_mail_content),$toEmailName,$fromEmailName);
	if(isset($_REQUEST['upgrade']) && $_REQUEST['upgrade']!=''){
	
	}else{
		templ_send_email($fromEmail,$fromEmailName,$toEmail,$toEmailName,$client_mail_subject,$client_message,$extra='');///To client email
	}
	//Payment success Mail to admin START
	$admin_mail_subject =  apply_filters('prebanktransfer_admin_subject',__('Pending payment through Pre bank transfer',DOMAIN));
	$admin_mail_content = $theme_settings['pre_payment_success_email_content_to_admin'];
	
	$payment_status = __("Pending",DOMAIN);
	$payment_type = $payment_detail['name'];
	$payment_date =  date_i18n(get_option('date_format'),strtotime($payment_date));
	$transaction_details="";
	$transaction_details .= "<br/>\r\n-------------------------------------------------- <br/>\r\n";
	$transaction_details .= __('Payment Details for',DOMAIN).": $post_title <br/>\r\n";
	$transaction_details .= "-------------------------------------------------- <br/>\r\n";
	$transaction_details .= 	__('Status',DOMAIN).": $payment_status <br/>\r\n";
	$transaction_details .=     __('Type',DOMAIN).": $payment_type <br/>\r\n";
	$transaction_details .= 	__('Date',DOMAIN).": $payment_date <br/>\r\n";
	$transaction_details .= "-------------------------------------------------- <br/>\r\n";
	$transaction_details = $transaction_details;
	
	$search_array = array('[#to_name#]','[#payable_amt#]','[#transaction_details#]','[#site_name#]','[#admin_email#]','[#user_login#]');
	$replace_array = array($fromEmailName,$sql_payable_amt,$transaction_details,$fromEmailName,get_option('admin_email'),$toEmailName);
	$admin_message = apply_filters('prebanktransfer_admin_message',str_replace($search_array,$replace_array,$admin_mail_content),$fromEmailName,$toEmailName);
	if(isset($_REQUEST['upgrade']) && $_REQUEST['upgrade']!=''){
	
	}else{
		templ_send_email($fromEmail,$fromEmailName,$fromEmail,$fromEmailName,$admin_mail_subject,$admin_message,$extra='');///To admin email
	}
	//Payment success Mail to admin FINISH
}
$amout=get_post_meta($_REQUEST['pid'],'total_price',true);
if($amout=='0' || $amout==''){
	global $wpdb;
	$transaction_tabel = $wpdb->prefix."transactions";
	$tmpdata = get_option('templatic_settings');
	
	if($_SESSION['custom_fields']['last_selected_pkg'])
	{
		$get_last_trans_status = $wpdb->get_var("select status from $transaction_tabel t where post_id='".$_SESSION['custom_fields']['user_last_postid']."' AND t.package_type is NULL order by t.trans_id desc");
		if($get_last_trans_status==2){
			$get_last_trans_status=0;
		}
		if(@$get_last_trans_status !='')
			$trans_status = $wpdb->query("update $transaction_tabel SET status = ".$get_last_trans_status." where post_id = ".$_REQUEST['pid']);

	}
	else
	{
		if($tmpdata['post_default_status']=='publish' && !isset($_SESSION['custom_fields']['last_selected_pkg']) && $_SESSION['custom_fields']['last_selected_pkg'] == '' && (!isset($_REQUEST['upgrade']) && $_REQUEST['upgrade'] != 1)){
			$trans_status = $wpdb->query("update $transaction_tabel SET status = 1 where post_id = ".$_REQUEST['pid']);
			$wpdb->query("UPDATE $wpdb->posts SET post_status='publish' where ID = '".$_REQUEST['pid']."'");
		}else{
			$trans_status = $wpdb->query("update $transaction_tabel SET status = 0 where post_id = ".$_REQUEST['pid']);
		}
	}	
	
}
global $upload_folder_path,$wpdb;
?>
    <div class="site-content <?php echo stripslashes(get_option('ptthemes_sidebar_left')); ?>" id="content">
	 <h1 class="page-title"><?php echo $page_title; ?></h1>
     <div class="posted_successful">
	 <?php
		do_action('tevolution_submition_success_msg');
	 ?> 
	</div>
     <?php if(!isset($_REQUEST['upgrade']) && $_REQUEST['upgrade'] =='')
		do_action('tevolution_submition_success_post_content'); ?>
	</div> <!-- content #end -->
<?php 
	if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=""){
		$ptype = $wpdb->get_var("select post_type from $wpdb->posts where $wpdb->posts.ID = '".$_REQUEST['pid']."'");
		$cus_post_type = apply_filters('success_page_sidebar_post_type',$ptype);
	}	
?>
<div class="sidebar" id="sidebar-primary">
<?php 
	if(isset($cus_post_type) && $cus_post_type!="" && is_active_sidebar($cus_post_type.'_detail_sidebar')){
		dynamic_sidebar($cus_post_type.'_detail_sidebar');
	}else{ 
		dynamic_sidebar('primary-sidebar');
	}
?>
</div>
<?php
	unset($_SESSION['category']);
	unset($_SESSION['custom_fields']);
	if( !empty( $_SESSION["file_info"] ) ){
		foreach( $_SESSION["file_info"] as $image_id=>$val ){
			if(file_exists(TEMPLATEPATH."/images/tmp/".$val) && $val!='')
				unlink(TEMPLATEPATH."/images/tmp/".$val);
		}
	}
	unset($_SESSION['upload_file']);
	unset($_SESSION['file_info']);
	unset($_SESSION['templ_file_info']);
get_footer(); ?>
