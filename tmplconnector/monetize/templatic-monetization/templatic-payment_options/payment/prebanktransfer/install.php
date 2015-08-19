<?php
/*
 * insert option for prebanktransfer in database while plugin activation
 */
$paymentmethodname = 'prebanktransfer'; 
if($_REQUEST['install']==$paymentmethodname)
{
	$paymethodinfo = array();
	$payOpts = array();
	$payOpts[] = array(
					"title"			=>	__("Bank Information",'templatic-admin'),
					"fieldname"		=>	"bankinfo",
					"value"			=>	"ICICI Bank",
					"description"	=>	__('Enter the bank name to which you want to transfer payment','templatic-admin')
					);
	$payOpts[] = array(
					"title"			=>	__("Account ID",'templatic-admin'),
					"fieldname"		=>	"bank_accountid",
					"value"			=>	"AB1234567890",
					"description"	=>	__('Enter your bank Account ID','templatic-admin'),
					);
					
	$paymethodinfo = array(
						"name" 		=> __('Pre Bank Transfer','templatic-admin'),
						"key" 		=> $paymentmethodname,
						"isactive"	=>	'1', /* 1->display,0->hide */
						"display_order"=>'6',
						"payOpts"	=>	$payOpts,
						);
	
	update_option("payment_method_$paymentmethodname", $paymethodinfo );
	$install_message = __("Payment Method integrated successfully", 'templatic-admin');
	$option_id = $wpdb->get_var("select option_id from $wpdb->options where option_name like \"payment_method_$paymentmethodname\"");
	wp_redirect("admin.php?page=monetization&tab=payment_options");
}elseif($_REQUEST['uninstall']==$paymentmethodname)
{
	delete_option("payment_method_$paymentmethodname");
	$install_message = __("Payment Method deleted successfully",'templatic-admin');
}
?>