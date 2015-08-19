<?php
/*
 * send response to paypal as from submit form
 */
global $trans_id;
define('PAYPAL_MSG',__('Processing for PayPal, Please wait ....',DOMAIN));
$paymentOpts = templatic_get_payment_options($_REQUEST['paymentmethod']);
/* get all settings in pay pal */
$paypal_options = get_option('payment_method_paypal');
$merchantid = $paymentOpts['merchantid'];

if($_REQUEST['page'] == 'upgradenow'){
	$suburl ="&upgrade=pkg";
}

/* Wpml language plugin wise url change in return url, cancle url and notify url */
if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
	global $sitepress;
	if(isset($_REQUEST['lang'])){
		$url = site_url().'/?page=paynow&lang='.$_REQUEST['lang'];
	}elseif($sitepress->get_current_language()){
		
		if($sitepress->get_default_language() != $sitepress->get_current_language()){
			$url = site_url().'/'.$sitepress->get_current_language();
		}else{
			$url = site_url();
		}	
	}else{
		$url = site_url();
	}
	
	if(strstr($url,'?'))
	{
		$returnUrl = apply_filters('tmpl_returnUrl',$url."&ptype=return&pmethod=paypal&trans_id=".$trans_id.$suburl);
		$cancel_return = apply_filters('tmpl_cancel_return',$url."&ptype=cancel&pmethod=paypal&trans_id=".$trans_id);
		$notify_url = apply_filters('tmpl_notify_url',$url."&ptype=notifyurl&pmethod=paypal&trans_id=".$trans_id);
	}else
	{ 
		$returnUrl = apply_filters('tmpl_returnUrl',$url."?ptype=return&pmethod=paypal&trans_id=".$trans_id.$suburl);
		$cancel_return = apply_filters('tmpl_cancel_return',$url."?ptype=cancel&pmethod=paypal&trans_id=".$trans_id);
		$notify_url = apply_filters('tmpl_notify_url',$url."?ptype=notifyurl&pmethod=paypal&trans_id=".$trans_id);
	}	
}else{
	$returnUrl = apply_filters('tmpl_returnUrl',site_url("/")."?ptype=return&pmethod=paypal&trans_id=".$trans_id.$suburl);
	$cancel_return = apply_filters('tmpl_cancel_return',site_url("/")."?ptype=cancel&pmethod=paypal&trans_id=".$trans_id);
	$notify_url = apply_filters('tmpl_notify_url',site_url("/")."?ptype=notifyurl&pmethod=paypal&trans_id=".$trans_id);
}

$currency_code = templatic_get_currency_type();
global $payable_amount,$post_title,$last_postid;
$payable_amount = number_format((float)$payable_amount, 2, '.', ''); /* shows 2 desimal points as per paypals price forlmat */
$post = get_post($last_postid);
$post_title = apply_filters('tmpl_trans_title',$post->post_title);
$user_info = apply_filters('tmpl_trans_user_info',get_userdata($post->post_author));
$address1 = apply_filters('tmpl_trans_address1',get_post_meta($post->post_author,'address'));
$address2 = apply_filters('tmpl_trans_address2',get_post_meta($post->post_author,'area'));
$country = apply_filters('tmpl_trans_country',get_post_meta($post->post_author,'add_country'));
$state = apply_filters('tmpl_trans_state',get_post_meta($post->post_author,'add_state'));
$city = apply_filters('tmpl_trans_state',get_post_meta($post->post_author,'add_city'));

if($_REQUEST['page'] == 'upgradenow' || $_REQUEST['post_viewer_package']){
	$price_package_id=$_REQUEST['pkg_id'];
}
else{
	$price_package_id=get_post_meta($last_postid,'package_select',true);
}

$package_amount=get_post_meta($price_package_id,'package_amount',true);
$validity=get_post_meta($price_package_id,'validity',true);
$validity_per=get_post_meta($price_package_id,'validity_per',true);
$recurring=get_post_meta($price_package_id,'recurring',true);
$billing_num=get_post_meta($price_package_id,'billing_num',true);
$billing_per=get_post_meta($price_package_id,'billing_per',true);
$billing_cycle=get_post_meta($price_package_id,'billing_cycle',true);
$first_free_trail_period=get_post_meta($price_package_id,'first_free_trail_period',true);
if($recurring==1){
	$c=$billing_num;
	if($billing_per=='M'){
		$rec_type=sprintf('%d Month', $c);
		$cycle= 'Month';
	}elseif($billing_per=='D'){
		$rec_type=sprintf('%d Week', $c/7);
		$cycle= 'Week';
	}else{
		$rec_type=sprintf('%d Year', $c);
		$cycle= 'Year';
	}
				
	$c_recurrence=$rec_type;
	/*$c_duration='FOREVER';*/
	$c_duration=$billing_cycle.' '.$cycle;	
	
}

/* set url according to paypal mode selected in payment setting */
if($paypal_options['paypal_mode'] == 1){ /* if test mode */
	$action_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
}else{ /* if live mode */
	$action_url = 'https://www.paypal.com/cgi-bin/webscr';
}
?>
<form name="frm_payment_method" action="<?php echo $action_url;?>" method="post">
<input type="hidden" name="business" value="<?php echo $merchantid;?>"/>
<input type="hidden" name="address1" value="<?php echo $address1[0]; ?>" >
<input type="hidden" name="address2" value="<?php echo $address2[0]; ?>" >
<input type="hidden" name="first_name" value="<?php if($user_info->first_name){ echo $user_info->first_name; }else{ echo $user_info->user_login; } ?>">
<input type="hidden" name="middle_name" value="<?php echo $user_info->middle_name;; ?>" >
<input type="hidden" name="last_name" value="<?php echo $user_info->last_name;; ?>" >
<input type="hidden" name="lc" value="<?php echo ""; ?>" >
<input type="hidden" name="country" value="<?php echo $country[0]; ?>" >
<input type="hidden" name="state" value="<?php echo $state[0]; ?>" >
<input type="hidden" name="city" value="<?php echo $city[0]; ?>" >
<?php if($recurring == '1') { ?>
	<?php if($first_free_trail_period==1):?>
    <input type="hidden" name="a1" value="0">    
    <input type="hidden" name="t1" value="<?php echo $billing_per;?>">
    <input type="hidden" name="p1" value="<?php echo $billing_num;?>">
    <?php endif;?>

<input type="hidden" name="amount" value="<?php echo $payable_amount;?>" />
<input type="hidden" name="a3" value="<?php echo $payable_amount;?>" />
<input type="hidden" name="t3" value="<?php echo $billing_per;?>"/>
<input type="hidden" name="p3" value="<?php echo $billing_num;?>"/>
<input type="hidden" name="srt" value="<?php echo $billing_cycle;?>"/>
<input type="hidden" name="src" value="1" />
<input type="hidden" name="sra" value="1" />
<input type="hidden" name="return" value="<?php echo $returnUrl;?>&pid=<?php echo $last_postid;?>&trans_id=<?php echo $trans_id; ?>"/>
<input type="hidden" name="cancel_return" value="<?php echo $cancel_return;?>&pid=<?php echo $last_postid;?>&trans_id=<?php echo $trans_id; ?>"/>
<input type="hidden" name="notify_url" value="<?php echo $notify_url;?>"/>
<input type="hidden" name="txn_type" value="subscr_cancel"/>
<input type="hidden" name="cmd" value="_xclick-subscriptions"/>
<?php }  else { ?>
<input type="hidden" name="amount" value="<?php echo $payable_amount;?>"/>
<input type="hidden" name="return" value="<?php echo $returnUrl;?>&pid=<?php echo $last_postid;?>&trans_id=<?php echo $trans_id; ?>"/>
<input type="hidden" name="cancel_return" value="<?php echo $cancel_return;?>&pid=<?php echo $last_postid;?>&trans_id=<?php echo $trans_id; ?>"/>
<input type="hidden" name="notify_url" value="<?php echo $notify_url;?>"/>
<input type="hidden" name="cmd" value="_xclick"/>
<?php }?>
<input type="hidden" name="item_name" value="<?php echo $post_title;?>"/>
<input type="hidden" name="business" value="<?php echo $merchantid;?>"/>
<input type="hidden" name="currency_code" value="<?php echo $currency_code;?>"/>
<input type="hidden" name="custom"  value="<?php echo $last_postid;?>"  />
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="rm" value="2">
</form>
<div class="wrapper" >
<div class="clearfix container_message payment_processing_msg" style=" width:100%;text-align:center; height: 100%; margin-top: -10px; position: relative; top: 50%;">
	<h2 class="head2"><?php _e(PAYPAL_MSG);?></h2>
 </div>
</div>
<script type="text/javascript" async>
setTimeout("document.frm_payment_method.submit()",50); 
</script> <?php exit;?>