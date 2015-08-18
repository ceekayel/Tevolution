<?php
/*
NAME : IPN PROCESS FILE FOR PAYPAL
DESCRIPTION : THIS FILE WILL BE CALLED ON SUCCESSFUL PAYMENT VIA PAYPAL. THE CODE MENTIONED IN THIS FILE WILL FETCH THE POSTED EVENT DATA AND ACCORDINGLY IT WILL SEND EMAIL TO THE ADMIN AS WELL AS THE USER.
*/


global $wpdb;

$paypal=get_option('payment_method_paypal');
//$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
$url = 'https://www.paypal.com/cgi-bin/webscr';
$raw_post_data = file_get_contents('php://input');
$raw_post_data='cmd=_notify-validate&'.$raw_post_data;

$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
  $keyval = explode ('=', $keyval); 
  if (count($keyval) == 2){
     $myPost[$keyval[0]] = urldecode($keyval[1]);
	$_POST[$keyval[0]] = urldecode($keyval[1]);
	$new_string.=	$keyval[0]."==".$keyval[1]."&&";
  }
}
 
//new Code
$arg=array('method' => 'POST',
		 'timeout' => 45,
		 'redirection' => 5,
		 'httpversion' => '1.0',
		 'body' => $myPost,
		 'user-agent' => 'WordPress/'. $wp_version .'; '. home_url(),
	);

$response = wp_remote_get($url,$arg );
//Finish New Code

// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
if(!is_wp_error( $response ) && trim($response['body'])=='VERIFIED' && $response['response']['code']==200) {
	global $wpdb,$transection_db_table_name,$current_user;
	$transection_db_table_name=$wpdb->prefix.'transactions';
	$postid = $_POST['custom'];
	$item_name = $_POST['item_name'];
	$txn_id = $_POST['txn_id'];	
	$payment_type = $_POST['payment_type'];
	$payment_date = date_i18n(get_option('date_format'),strtotime($_POST['payment_date']));	
	$sql = "select max(trans_id) as trans_id,status from $transection_db_table_name where post_id = $postid";
	$sql_data = $wpdb->get_row($sql);		
	switch ($_POST['txn_type']){
		case 'subscr_signup':
		case 'subscr_payment':
			if(isset($_POST['payment_status']) && $_POST['payment_status']=='Completed'){				
				$sql = "select max(trans_id) as trans_id,status from $transection_db_table_name where post_id = $postid";
				$sql_data = $wpdb->get_row($sql);				
				$wpdb->query("UPDATE $transection_db_table_name set payment_date='".date("Y-m-d H:i:s")."' where trans_id=$sql_data->trans_id");				
				$wpdb->query("UPDATE $wpdb->posts SET post_status='publish' where ID = ".$postid);
			}
			break;
		case 'recurring_payment':
			switch ($_POST['payment_status'])
			{
				case 'Completed':                       
					$user_id = $current_user->ID;
					$sql = "select max(trans_id) as trans_id,status from $transection_db_table_name where post_id = $postid";
					$sql_data = $wpdb->get_row($sql);
					$wpdb->query("UPDATE $transection_db_table_name set payment_date='".date("Y-m-d H:i:s")."' where trans_id=$sql_data->trans_id");	
					$wpdb->query("UPDATE $wpdb->posts SET post_status='publish' where ID = '".$postid."'");
				break;
				default:
			}
		break; 
	}
	
}else{
	
}
?>