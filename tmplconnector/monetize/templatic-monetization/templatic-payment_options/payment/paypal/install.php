<?php
/*
 * insert option for paypal in database while plugin activation
 */
$paymentmethodname = 'paypal'; 
if($_REQUEST['install']==$paymentmethodname)
{
	$paymethodinfo = array();
	$payOpts = array();
	$payOpts[] = array(
					"title"			=>	__('Your PayPal Email','templatic-admin'),
					"fieldname"		=>	"merchantid",
					"value"			=>	"email@example.com",
					"description"	=>	__('Example','templatic-admin').__(": email@example.com",'templatic-admin')
					);
	$paymethodinfo = array(
						"name" 		=> __('PayPal','templatic-admin'),
						"key" 		=> $paymentmethodname,
						"isactive"	=>	'1', /* 1->display,0->hide*/
						"display_order"=>'1',
						"payOpts"	=>	$payOpts,
						);
	
	update_option("payment_method_$paymentmethodname", $paymethodinfo );
	$install_message = __("Payment Method integrated successfully",'templatic-admin');
	$option_id = $wpdb->get_var("select option_id from $wpdb->options where option_name like \"payment_method_$paymentmethodname\"");
	wp_redirect("admin.php?page=monetization&tab=payment_options");
}elseif($_REQUEST['uninstall']==$paymentmethodname)
{
	delete_option("payment_method_$paymentmethodname");
	$install_message = __("this payment method cannot deleted because it is fix, you can deactive it",'templatic-admin');
}
?>