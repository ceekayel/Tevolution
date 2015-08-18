<?php /* ajax calling file to check/validate coupon going to generate by admin ( check its exist or not )*/

require("../../../../../../../wp-load.php");	
global $wpdb;
$post_table = $wpdb->prefix."posts";
$add_coupon = $_REQUEST['add_coupon'];
$coupon_code= $_REQUEST['add_coupon'];
$startdate	= $_REQUEST['startdate'];
$enddate 	= $_REQUEST['enddate'];
$post_id	= $_REQUEST['post_id'];
$subsql= '';
if(isset($post_id) && $post_id !='')
	$subsql =  " and ID != $post_id";
$add_coupon = "select ID from $post_table where post_title ='".$add_coupon."' and post_type ='coupon_code' and post_status='publish' $subsql ";
$coupon_id = $wpdb->get_var($add_coupon);
$coupon_startdate = get_post_meta($coupon_id,'startdate',true);
$coupon_enddate = get_post_meta($coupon_id,'enddate',true);
$result = '0';

$couponcode1 = "select post_title from $post_table where post_title ='".$coupon_code."' and post_type ='coupon_code' and post_status='publish'";
$couponcodeexists = $wpdb->get_var($couponcode1);

if($startdate=='' && $enddate==''){
	$result = "<p>".__('Please select coupon start date and end date.',DOMAIN)."</p>";
	echo trim($result);exit;
}
if (($coupon_startdate <= $startdate &&  $coupon_enddate >= $startdate) || ($coupon_startdate <= $enddate &&  $coupon_enddate >= $enddate))
{
	$result = __("<p>Coupon already exist with the same name and within the same start date and end date period.</p>",DOMAIN);
}elseif($couponcodeexists == $coupon_code){
	$result = __("<p>Coupon already exist with the same name.</p>",DOMAIN);
}
echo trim($result);exit;
?>