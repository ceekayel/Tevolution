<?php
/*
 * export transaction in csv
 */
header('Content-Description: File Transfer');
header("Content-type: application/force-download");
header('Content-Disposition: inline; filename="transaction.csv"');
require("../../../../../../wp-load.php");
session_start();
global $wpdb,$current_user,$transection_db_table_name,$qry_string;
echo __("Title,Payment package,Paid On,Billing Name,Payment Method,Amount",'templatic-admin')."\r\n";
$transinfo = $wpdb->get_results($_SESSION['query_string']);
$totamt=0;
if($transinfo)
{

	foreach($transinfo as $priceinfoObj)
	{
		$totamt = $totamt + $priceinfoObj->payable_amt;
		$post_title =	iconv("UTF-8", "ISO-8859-1//IGNORE", $priceinfoObj->post_title);
		$package_info  =	get_post($priceinfoObj->package_id);
		$pay_pkg =	$package_info->post_title;
		$payment_date =$priceinfoObj->payment_date;
		$billing_name =$priceinfoObj->billing_name;
		$payment_method =$priceinfoObj->payment_method;
		$paid_amount =fetch_currency_with_position($priceinfoObj->payable_amt);
	
		echo "$post_title,$pay_pkg,$payment_date,$billing_name,$payment_method,$paid_amount\r";
 }
echo " , , , ,Total Amount :, ".fetch_currency_with_position($totamt)."\r\n";

}else
{
echo __("No record available",'templatic-admin');

}?>