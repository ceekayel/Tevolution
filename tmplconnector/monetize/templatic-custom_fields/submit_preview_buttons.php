<?php
/*  File show the payment options on preview page */
global $post,$wpdb,$current_user;
$id = $_SESSION['custom_fields']['cur_post_id'];
$permalink = get_permalink( $id );
if(isset($_REQUEST['page']) && $_REQUEST['page']=='preview' && isset($_GET['pid']))
{	
	$is_delet_post=1;
}
if(isset($_REQUEST['page']) && $_REQUEST['page']=='payment' )
{	
	$is_delet_post=1;  /* set to change the message for upgrade post */
}
if(@$current_user->ID =='')
{	
	$_SESSION['category'] = $_REQUEST['category'];
	$_SESSION['custom_fields']['cur_post_id'] = $_POST['cur_post_id'];
	$current_user_id = templ_insertuser_with_listing();	
}
/* code to set the gobackend edit link AND RENEW LINK */
if(strpos($permalink,'?'))
{ 
	 if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=""){ $postid = '&amp;pid='.$_REQUEST['pid']."&action=edit"; }
	 if(isset($_REQUEST['pid']) && $_REQUEST['pid'] !='' && isset($_REQUEST['renew'])){ $postid = '&amp;pid='.$_REQUEST['pid']."&renew=1"; }
	 $gobacklink = $permalink."&backandedit=1&fields=custom_fields".@$postid;
}elseif(isset($_REQUEST['page']) && $_REQUEST['page'] == 'payment'){
	$gobacklink =  $_SESSION['upgrade_post']['upgrade_url']."&backandedit=1&upgpkg=1"; // set go back link for upgrade post
}else{ 
	if(isset($_REQUEST['pid']) && $_REQUEST['pid'] !=''){ $postid = '&amp;pid='.$_REQUEST['pid']."&action=edit"; }
	if(isset($_REQUEST['pid']) && $_REQUEST['pid'] !='' && isset($_REQUEST['renew'])){ $postid = '&amp;pid='.$_REQUEST['pid']."&renew=1"; }
	$gobacklink = $permalink."?backandedit=1&fields=custom_fields".$postid;
}
?>
<!-- Published box code start -->
<div class="published_box">
<?php 
if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'payment'){
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		global $sitepress;
		if(isset($_REQUEST['lang'])){
			$url = get_option( 'siteurl' ).'/?page=upgradenow&lang='.$_REQUEST['lang'];
		}elseif($sitepress->get_current_language()){
	
			if($sitepress->get_default_language() != $sitepress->get_current_language()){
							$url = get_option( 'siteurl' ).'/'.$sitepress->get_current_language().'/?page=upgradenow';
						}else{
							$url = get_option( 'siteurl' ).'/?page=upgradenow';
						}
		}else{
			$url = get_option( 'siteurl' ).'/?page=upgradenow';
		}
	}else{
		$url = get_option( 'siteurl' ).'/?page=upgradenow';
	}
	 
}else{
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		global $sitepress;
		if(isset($_REQUEST['lang'])){
			$url = get_option( 'siteurl' ).'/?page=paynow&lang='.$_REQUEST['lang'];
		}elseif($sitepress->get_current_language()){
			if($sitepress->get_default_language() != $sitepress->get_current_language()){
				$url = get_option( 'siteurl' ).'/'.$sitepress->get_current_language().'/?page=paynow';
			}else{
				$url = get_option( 'siteurl' ).'/?page=paynow';
			}
		}else{
			$url = get_option( 'siteurl' ).'/?page=paynow';
		}
	}else{
		$url = get_option( 'siteurl' ).'/?page=paynow';
	}

}

$form_action_url = tmpl_get_ssl_normal_url($url);
global $monetization;
if(is_active_addons('monetization')){
	$listing_price_pkg = $monetization->templ_get_price_info($_SESSION['custom_fields']['package_select'],$_SESSION['custom_fields']['total_price']);
}

?>
<form method="post" action="<?php echo $form_action_url; ?>" id="payment-form" name="paynow_frm"  >
	<?php 
	global $payable_amount,$alive_days;
	$total_amount = 0;
	if(is_active_addons('monetization'))
	{
		global $monetization;
		if(class_exists('monetization'))
		{
			$total_amount = $monetization->tmpl_get_payable_amount($_POST['package_select'],$_POST['featured_type'],$_POST['category']);
		}
	}
	$payable_amount = @$total_amount;
	$alive_days = $listing_price_pkg[0]['alive_days'];
	if(isset($listing_price_pkg[0]['alive_days'])){
		$alive_days = $listing_price_pkg[0]['alive_days'];
	}else{
		$alive_days = 30;
	}
		
	if(isset($_REQUEST['msg']) == 'nopaymethod')
	{
	  echo '<div class="error_msg"> No Payment Method Selected. </div>';
	}
	/* validate coupon */
	if(isset($_REQUEST['add_coupon']) && $_REQUEST['add_coupon']!='')
	{
		if(is_valid_coupon_plugin($_SESSION['custom_fields']['add_coupon']))
		{ 
			$coupon_amount = get_payable_amount_with_coupon_plugin($payable_amount,$_SESSION['custom_fields']['add_coupon']);
			$post_table = $wpdb->prefix."posts";
			$add_coupon	= "select ID from $post_table where post_title ='".$_SESSION['custom_fields']['add_coupon']."' and post_type ='coupon_code' and post_status='publish'";
			$coupon_id	= $wpdb->get_var($add_coupon);
			$coupondisc = get_post_meta($coupon_id,'coupondisc',true);
			$couponamt 	= get_post_meta($coupon_id,'couponamt',true);
			
			if($coupondisc == 'per' && $coupon_amount == 0 && $couponamt==100  ){
				$payable_amount = $coupon_amount;
			}elseif($coupon_amount > 0 )
			{
				$payable_amount = $coupon_amount;
			}
		}
	}
	/* validate coupon end */
	
	if(((isset($_REQUEST['pid'])=='' && $payable_amount>0) || (isset($_POST['renew']) || isset($_POST['upgrade']) && $payable_amount>0 && isset($_REQUEST['pid'])!='') || $_SESSION['custom_fields']['total_price'] > 0 ) && (isset($_SESSION['custom_fields']['add_coupon']) && $coupon_amount !=0))
	{
		if(!$payable_amount)
		 {
			$payable_amount = $_SESSION['custom_fields']['total_price'];
		 }
		 if($payable_amount==0){
		 	$message = __('You are going to submit post for ',DOMAIN).$alive_days." ".__('days.',DOMAIN);
		 }else if(isset($_REQUEST['page']) && $_REQUEST['page'] =='payment'){ /* message for upgrade post */
		
			$message = __('You are going to upgrade your post with ',DOMAIN)."<span style='color:green;'>".$listing_price_pkg[0]['title']."</span>"." ".__('package which will cost you',DOMAIN)." "."<span style='color:green;'>".display_amount_with_currency_plugin($payable_amount)."</span> ".__('for',DOMAIN)." <span style='color:green;'>".@$alive_days."</span> ".__('days',DOMAIN); 
		}else{
			
			$message = __('You are going to submit a post that will cost you ',DOMAIN).display_amount_with_currency_plugin($payable_amount)." ".__('for',DOMAIN)." ".$alive_days." ".__('days',DOMAIN); 
		}
	}elseif(isset($_SESSION['custom_fields']['add_coupon']) && $_SESSION['custom_fields']['add_coupon'] > 0 && $coupon_amount == 0){
			$message = __('You are going to submit post for ',DOMAIN).$alive_days." ".__('days',DOMAIN); 
	}else
	{
		if(isset($_REQUEST['pid'])=='')
		{
			 if(is_active_addons('monetization')){ 
				if($payable_amount>0){
					$message = __('You are going to submit a post that will cost you ',DOMAIN).display_amount_with_currency_plugin($payable_amount)." ".__('for',DOMAIN)." ".@$alive_days." ".__('days',DOMAIN); 
				}else{
					$message = __("This is the preview of your submitted information. To make any changes, please press the 'Go back and edit' button below.",DOMAIN);
				}
			 }else{
				$message = __("This is the preview of your submitted information. To make any changes, please press the 'Go back and edit' button below.",DOMAIN);
			 }
		}elseif(!$is_delet_post)
		{	
			if(is_active_addons('monetization')){ 
				if($payable_amount>0){
					$message = __('You are going to update a post that will cost you ',DOMAIN).display_amount_with_currency_plugin($payable_amount)." ".__('for',DOMAIN)." ".@$alive_days." ".__('days',DOMAIN); 
				}else{
					$message = __("You are going to update your entry. To make any changes, please press the 'Go back and edit' button below.",DOMAIN);
				}
			 }else{
				$message = __("You are going to update your entry. To make any changes, please press the 'Go back and edit' button below.",DOMAIN);
			 }
			
		}elseif(isset($_REQUEST['page']) && $_REQUEST['page'] =='payment'){ /* message for upgrade post */

			$message = __('You are going to upgrade your post with ',DOMAIN)."<span style='color:green;'>".$listing_price_pkg[0]['title']."</span>"." ".__('package which will cost you',DOMAIN)." "."<span style='color:green;'>".display_amount_with_currency_plugin($payable_amount)."</span> ".__('for',DOMAIN)." <span style='color:green;'>".@$alive_days."</span> ".__('days',DOMAIN); 
		}
	}
	
	if(isset($message) && $message != ""): ?>
		<h5 class="post_message"> <?php echo $message; ?> </h5>
    <?php endif; ?>    
	<?php /* display payment options only when monetization is activated */?>
	<span style="color:red;font-weight:bold;display:block;" id="payment_errors"><?php 
		if(isset($_REQUEST['paypalerror']) && $_REQUEST['paypalerror']=='yes'){
			echo $_SESSION['paypal_errors'];
		}
		if(isset($_REQUEST['eway_error']) && $_REQUEST['eway_error']=='yes'){
			echo $_SESSION['display_message'];
		}
		if(isset($_REQUEST['stripeerror']) && $_REQUEST['stripeerror']=='yes'){
			echo $_SESSION['stripe_errors'];
		}
		if(isset($_REQUEST['psigateerror']) && $_REQUEST['psigateerror']=='yes'){
			echo $_SESSION['psigate_errors'];
		}
		if(isset($_REQUEST['braintreeerror']) && $_REQUEST['braintreeerror']=='yes'){
			echo $_SESSION['braintree_errors'];
		}
		if(isset($_REQUEST['inspire_commerceerror']) && $_REQUEST['inspire_commerceerror']=='yes'){
			echo $_SESSION['inspire_commerce_errors'];
		}
	?></span>
	<?php if((isset($_REQUEST['pid'])=='' && $payable_amount>0) || ((isset($_POST['renew']) || isset($_POST['upgrade'])) && $payable_amount>0 && $_REQUEST['pid']!='') || $_SESSION['custom_fields']['total_price'] > 0)
	{
		if(is_active_addons('monetization') && $payable_amount>0){
			/* Delete option of pay cash on delivery because we removed it. */
				delete_option('payment_method_payondelivery');
			/* Delete option of pay cash on delivery because we removed it. */
			templatic_payment_option_preview_page(); // To display the payment gateways on preview page
		}
	}
	?>
    <input type="hidden" name="paynow" value="1" />
	<input type="hidden" name="pid" value="<?php echo $_POST['pid'];?>" />
	<?php
	if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="" && !isset($_REQUEST['upgrade']))
	{
	?> 
		<input type="submit" name="paynow" value="<?php _e(PRO_UPDATE_BUTTON,DOMAIN);?>" class="btn_input_highlight btn_spacer fr" />
	<?php
	}else
	{ 
		/* pay and publish button show only when monetization is activated */
		if(is_active_addons('monetization') && $payable_amount > 0)
		{ $btn_value = PRO_SUBMIT_BUTTON; }else{ $btn_value = PUBLISH_BUTTON; }
		?>
		<input type='submit' name='paynow' id='paynow'  value='<?php _e($btn_value,DOMAIN); ?>' class='btn_input_highlight btn_spacer fr' />
        <?php
	}
	
	if((isset($_POST['renew']) && $_POST['renew'] == 1) || (isset($_POST['upgrade']) && $_POST['upgrade'] != '')):	
	$submit_page=(isset($_SESSION['custom_fields']['cur_post_id']))? get_permalink($_SESSION['custom_fields']['cur_post_id']) : get_permalink($_POST['cur_post_id']);
	if(isset($_POST['upgrade']) && $_POST['upgrade'] != '')
		$submit_page =  $_SESSION['upgrade_post']['upgrade_url']."&upgpkg=1"; // set cancel link for upgrade post
	?>
        <input type="button" name="Cancel" value="<?php _e(PRO_PREVIEW_CANCEL_BUTTON,DOMAIN);?>" class="btn_input_normal fl" onclick="window.location.href='<?php echo $submit_page;?>'" />
        <a href="<?php echo $gobacklink; ?>" class="btn_input_normal button" ><?php _e('Go back and edit',DOMAIN);?></a>
	<?php else: 
	$submit_page=(isset($_SESSION['custom_fields']['cur_post_id']))? get_permalink($_SESSION['custom_fields']['cur_post_id']) : get_permalink($_POST['cur_post_id']);
	?>
        <input type="button" name="Cancel" value="<?php _e(PRO_PREVIEW_CANCEL_BUTTON,DOMAIN);?>" class="btn_input_normal fl" onclick="window.location.href='<?php echo $submit_page;?>'" />
        <a href="<?php echo $gobacklink; ?>" class="btn_input_normal button" ><?php _e('Go back and edit',DOMAIN);?></a>
	<?php endif; ?>
	 </form>
</div>
<!-- Published box code end -->
