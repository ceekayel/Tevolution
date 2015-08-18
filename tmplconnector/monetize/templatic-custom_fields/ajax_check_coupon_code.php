<?php
require( "../../../../../../wp-load.php");	
global $wpdb;
$post_table = $wpdb->prefix."posts";
$add_coupon = $_REQUEST['add_coupon'];
$add_coupon = "select ID from $post_table where post_title ='".$add_coupon."' and post_type ='coupon_code' and post_status='publish'";
$coupon_id = $wpdb->get_var($add_coupon);
$coupondisc = get_post_meta($coupon_id,'coupondisc',true);
$couponamt = get_post_meta($coupon_id,'couponamt',true);
if($coupondisc == 'per')
{
	$result = "Whooppy!!!You save $couponamt%";
}
if($coupondisc == 'amt')
{
	$price = fetch_currency_with_position($couponamt);
	$result = "Whooppy!!!You save $price";
}
echo $result;exit;
?>