<?php /* ajax calling file to check/validate coupon going to add by user from front end*/
require("../../../../../../../wp-load.php");	
global $wpdb;
if(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
{
	global  $sitepress;
	$sitepress->switch_lang($_REQUEST['language']);
}
$post_table = $wpdb->prefix."posts";
$add_coupon = $_REQUEST['add_coupon'];
$add_coupon	= "select ID from $post_table where post_title ='".$add_coupon."' and post_type ='coupon_code' and post_status='publish'";
$coupon_id	= $wpdb->get_var($add_coupon);
$coupondisc = get_post_meta($coupon_id,'coupondisc',true);
$couponamt 	= get_post_meta($coupon_id,'couponamt',true);
$start_date = strtotime(get_post_meta($coupon_id,'startdate',true));
$end_date 	= strtotime(get_post_meta($coupon_id,'enddate',true));
$todays_date = strtotime(date("Y-m-d"));
$total_price = $_REQUEST['total_price'];
if ($start_date <= $todays_date && $end_date >= $todays_date)
{
	if($coupondisc == 'per')
	{
		if($total_price > 0)
		{
			$price = (($total_price * $couponamt)/100);
			$price = $total_price - $price;
			if($price > 0 || $couponamt==100)
			{
				if($price <=0){
					$result = sprintf(__('Coupon added successfully.',DOMAIN));
				}else{
					$result = sprintf(__('Thanks for using coupon! Now just pay %s for your submission',DOMAIN),display_amount_with_currency_plugin($price));
				}
			}
		}
		
	}
	if($coupondisc == 'amt')
	{
		if($total_price > 0)
		{
			$price = $total_price - $couponamt;
			if($price > 0)
			{
				$result = sprintf(__('Thanks for using coupon! Now just pay %s for your submission',DOMAIN),display_amount_with_currency_plugin($price));
			}
		}
		
	}
}
echo $result;exit;
?>