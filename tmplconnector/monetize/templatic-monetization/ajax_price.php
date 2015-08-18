<?php
define( 'DOING_AJAX', true );
require("../../../../../../wp-load.php");
if(isset($_REQUEST['pkid'])){
$packid = $_REQUEST['pkid']; }else{
	$pxid = 1;
}
$pckage_id='';
if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=""){
	$pckage_id=get_post_meta($_REQUEST['pid'],'package_select',true);	
}

$pckid = $_REQUEST['pckid'];
$post_type = $_REQUEST['post_type'];
$taxonomy = $_REQUEST['taxonomy'];
$all_cat_id = str_replace('|',',',$_REQUEST['pckid']);
global  $price_db_table_name,$wpdb ;
	if($packid != "")
	{
	$pricesql = $wpdb->get_row("select * from $wpdb->posts where ID='".$packid."'"); 
	$homelist = get_post_meta($packid,'feature_amount',true);
	if(!$homelist){ $homelist =0; }
	$catlist =  get_post_meta($packid,'feature_cat_amount',true);
	if(!$catlist){ $catlist =0; }
	$bothlist = $catlist + $homelist;
	$packprice = get_post_meta($packid,'package_amount',true);
	$is_featured = get_post_meta($packid,'is_featured',true);
	$alive_days = get_post_meta($packid,'validity',true);
	$none = 0;
	if(is_plugin_active('Tevolution-FieldsMonetization/fields_monetization.php'))
	{
		$category_can_select = get_post_meta($packid,'category_can_select',true);
	}
	if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php'))
	{
		$comment_mederation_amount = get_post_meta($packid,'comment_mederation_amount',true);
		if(!$comment_mederation_amount){ $comment_mederation_amount = 0; }
		$can_author_mederate = get_post_meta($packid,'can_author_mederate',true);
		if(is_plugin_active('Tevolution-FieldsMonetization/fields_monetization.php'))
		{
			$priceof = array($homelist,$catlist,$bothlist,$none,$packprice,$is_featured,$alive_days,$can_author_mederate,$comment_mederation_amount,$category_can_select);
		}
		else
		{
			$priceof = array($homelist,$catlist,$bothlist,$none,$packprice,$is_featured,$alive_days,$can_author_mederate,$comment_mederation_amount);
		}
		$rawrsize = sizeof($priceof);
	}
	else
	{
		if(is_plugin_active('Tevolution-FieldsMonetization/fields_monetization.php'))
		{
			$priceof = array($homelist,$catlist,$bothlist,$none,$packprice,$is_featured,$alive_days,$category_can_select);
		}
		else
		{
			$priceof = array($homelist,$catlist,$bothlist,$none,$packprice,$is_featured,$alive_days);
		}
		$rawrsize = sizeof($priceof);
	}
	$returnstring = "";
	
	//go through the array, using a unique identifier to mark the start of each new record
	for($i=0;$i<$rawrsize;$i++)
	{
		
		$returnstring .= $priceof[$i];
		$returnstring .= '###RAWR###';
	}
	
	echo $returnstring;
	}
	if(isset($_REQUEST['pckid'])) {
		$pckid = $_REQUEST['pckid'];
		$edit_id ='';
		global $monetization;
		if($pckid != "" && (isset($_REQUEST['is_backend']) && $_REQUEST['is_backend']==1 )){
			$monetization->fetch_monetization_packages_back_end($pckage_id,'ajax_packages_checkbox',$post_type,$taxonomy,$all_cat_id);
		}else if($pckid != ""){
			$monetization->fetch_monetization_packages_front_end($pckage_id,'ajax_packages_checkbox',$post_type,$taxonomy,$all_cat_id);
		}  
	}
?>
