<?php
require("../../../../../../wp-load.php");
//set transaction order approve from pending.
if($_REQUEST['post_id'] !=""){
$my_post['ID'] = $_REQUEST['post_id'];
$my_post['post_status'] = 'publish';
wp_update_post( $my_post );
}
if($_REQUEST['post_id'] !=""){
global $wpdb,$transection_db_table_name;
$transection_db_table_name = $wpdb->prefix . "transactions";
$pid = $_REQUEST['post_id'];
$trans_status = $wpdb->query("update $transection_db_table_name SET status = 1 where post_id = '".$pid."'");
}
$result = '';
if(isset($_REQUEST['trans_id']) && $_REQUEST['trans_id']!='')
	$result = "<span style='color:green; font-weight:normal;'>Approved</span>";
else
	$result = "<span style='color:green;'>".APPROVED_TEXT."</span>";
echo $result;exit;
?>