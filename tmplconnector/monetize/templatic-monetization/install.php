<?php
global $wp_query,$wpdb,$wp_rewrite;
define('TEMPL_MONETIZATION_PATH',TEMPL_MONETIZE_FOLDER_PATH . "templatic-monetization/"); 
/* ACTIVATING PRICE PACKAGES */
if( (isset($_REQUEST['activated']) && $_REQUEST['activated'] == 'monetization') && ($_REQUEST['true'] && $_REQUEST['true'] == 1) || (isset($_REQUEST['activated']) && $_REQUEST['activated']=='true') )
{
	update_option('monetization','Active');
	update_option('currency_symbol','$');
	update_option('currency_code','USD');
	update_option('currency_pos','1');
	add_action('admin_init','test_function');
	function test_function(){
		require_once(TEMPL_MONETIZATION_PATH.'add_dummy_packages.php');
	}
}
else if( (isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'monetization') && (isset($_REQUEST['true']) && $_REQUEST['true'] == 0 ))
{
	delete_option('monetization');
}
/* EOF - PRICE PACKAGES ACTIVATION */

/* CODE TO CREATE AN ADMIN SUBPAGE MENU FOR PRICE PACKAGES */
if(is_active_addons('monetization'))
{
	/* INCLUDING A LANGUAGE FILE */
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-monetization/language.php'))
	{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-monetization/language.php");
	}
	/* INCLUDING A FUNCTIONS FILE */
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-monetization/price_package_functions.php'))
	{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-monetization/price_package_functions.php");
	}
	
	add_action('templ_add_admin_menu_', 'add_subpage_monetization',13); /* ADD HOOK */
	add_action('admin_head','add_farbtastic_style_script');
	
	if(file_exists(TEMPL_MONETIZATION_PATH."templatic-payment_options/payment_functions.php"))
		include(TEMPL_MONETIZATION_PATH."templatic-payment_options/payment_functions.php");
	if(file_exists(TEMPL_MONETIZATION_PATH."templatic-manage_coupon/install.php"))
		include(TEMPL_MONETIZATION_PATH."templatic-manage_coupon/install.php");
		
		
	add_action('admin_head','templ_add_pkg_js');
	add_action('wp_head','templ_add_pkg_js');
	add_filter('set-screen-option', 'package_table_set_option', 10, 3);
	add_action('admin_init','transactions_table_create');
	
	/* EOF - CREATE SUB PAGE MENU */
}

function add_subpage_monetization()
{
	$page_title = __('Monetization',ADMINDOMAIN); /* DEFINE PAGE TITLE AND MENU TITLE */
	$transcation_title = __('Transactions',ADMINDOMAIN); /* DEFINE PAGE TITLE AND MENU TITLE */
	/* CREATING A SUB PAGE MENU TO TEMPLATIC SYSTEM */
	add_submenu_page('templatic_system_menu', "",   '<span class="tevolution-menu-separator" style="display:block; 1px -5px;  padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>',"administrator", "admin.php?page=templatic_system_menu"  );
	$hook = add_submenu_page('templatic_system_menu',$page_title,$page_title,'administrator', 'monetization', 'add_monetization');

	add_action( "load-$hook", 'add_screen_options' ); /* CALL A FUNCTION TO ADD SCREEN OPTIONS */
	$hook_transaction = add_submenu_page('templatic_system_menu',$transcation_title,$transcation_title,'administrator', 'transcation', 'add_transcation');
	do_action('templatic_monetizations_menu');// add additional admin menu after  monetization and transcation menu
	add_action( "load-$hook_transaction", 'add_screen_options_transaction' ); /* CALL A FUNCTION TO ADD SCREEN OPTIONS */
	
}

/*
 * Function Name: add_screen_options
 * Return: display the screen option in Monetization menu page 
 */

function add_screen_options()
{
	$option = 'per_page';
	$args = array('label'   => 'Show record per page for monetization',
			    'default' => 10,
			    'option'  => 'package_per_page'
		);
	add_screen_option( $option, $args ); /* ADD SCREEN OPTION */
}

/*
 * Function Name: add_screen_options_transaction
 * return: display the screen option in transaction menu page
 */
function add_screen_options_transaction()
{
	$option = 'per_page';
	$args = array( 'label'   => 'Transaction',
				'default' => 10,
				'option'  => 'transaction_per_page'
		);
	add_screen_option( $option, $args ); /* ADD SCREEN OPTION */
}

/*
 * Function Name: add_farbtastic_style_script
 * return: include wordpress farbtastic script and style for choose color picker
 */
function add_farbtastic_style_script()
{
	wp_enqueue_script( 'farbtastic' );
	wp_enqueue_style( 'farbtastic' );
}
/* FUNCTION CALLED ON SUB PAGE MENU HOOK */
function add_monetization()
{
	include(TEMPL_MONETIZATION_PATH."templatic_monetization.php");
}
/* FUNCTION CALLED ON SUB PAGE MENU HOOK */
function add_transcation()
{
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'transcation' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
		include(TEMPL_MONETIZATION_PATH."templatic_transaction_detail_report.php");
	elseif(isset($_REQUEST['page']) && $_REQUEST['page'] == 'transcation')
		include(TEMPL_MONETIZATION_PATH."templatic_transaction_report.php");
}
/*
Name :payment_option_plugin_function 
desc : Function to insert file for add/edit/delete options for payment options/gateway settings BOF 
*/
function payment_option_plugin_function(){
	if((isset($_GET['tab']) && $_REQUEST['tab'] == 'payment_options') && (!isset($_GET['payact']) && @$_GET['payact']=='')){
		templ_payment_methods();
	}else if((isset($_GET['payact']) && $_GET['payact']=='setting') && (isset($_GET['id']) && $_GET['id'] != '')){
		include (TEMPL_MONETIZATION_PATH."templatic-payment_options/admin_paymethods_add.php");
	}
}
/* Function to insert file for add/edit/delete options for custom fields EOF --**/
/*
Name :payment_option_plugin_function 
desc : Function to insert file for add/edit/delete options for payment options/gateway settings BOF 
*/
function manage_coupon_plugin_function(){
	if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'manage_coupon'){
		manage_coupon_function();
	}
}
/* Function to insert file for add/edit/delete options for custom fields EOF --**/
/*
Name: templ_add_pkg_js
desc : return the script for fetching price packages
*/
function templ_add_pkg_js(){
	global $wp_query,$pagenow,$post;
	// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object	
	if($post)
		$is_tevolution_submit_form = get_post_meta( @$post->ID, 'is_tevolution_submit_form', true );
		$is_tevolution_upgrade_form = get_post_meta(@$post->ID, 'is_tevolution_upgrade_form', true );
		$is_frontend_submit_form = get_post_meta(@$post->ID, 'is_frontend_submit_form', true );
	if((is_page() &&  ($is_tevolution_upgrade_form==1 || $is_tevolution_submit_form==1 || $is_frontend_submit_form==1)) ||(is_admin() && ($pagenow=='post.php' || $pagenow== 'post-new.php'))){
		include(TEMPL_MONETIZE_FOLDER_PATH.'templatic-monetization/price_package_js.php'); 
	}
}

/* NAME : FILTER FOR SCREEN OPTIONS
DESCRIPTION : THIS FUNCTION WILL FILTER DATA ACCORDING TO SCREEN OPTIONS */
function package_table_set_option($status, $option, $value)
{
    return $value;
}

/*
 * Function Name: transactions_table_create
 * Create the transactions table
 */
function transactions_table_create(){
	global $wpdb,$pagenow;	
	/*transaction table BOF*/
	if($pagenow=='index.php' || $pagenow=='plugins.php' || (isset($_REQUEST['page']) && ($_REQUEST['page']=='templatic_system_menu' || $_REQUEST['page']=='transcation' || $_REQUEST['page']=='monetization'))){
		
		$transection_db_table_name = $wpdb->prefix . "transactions";
		if($wpdb->get_var("SHOW TABLES LIKE \"$transection_db_table_name\"") != $transection_db_table_name)
		{
			$transaction_table = 'CREATE TABLE IF NOT EXISTS `'.$transection_db_table_name.'` (
			`trans_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`post_id` bigint(20) NOT NULL,
			`post_title` varchar(255) NOT NULL,
			`status` int(2) NOT NULL,
			`payment_method` varchar(255) NOT NULL,
			`payable_amt` float(25,5) NOT NULL,
			`payment_date` datetime NOT NULL,
			`paypal_transection_id` varchar(255) NOT NULL,
			`user_name` varchar(255) NOT NULL,
			`pay_email` varchar(255) NOT NULL,
			`billing_name` varchar(255) NOT NULL,
			`billing_add` text NOT NULL,
			`package_id` int(10) NOT NULL DEFAULT 0,
			PRIMARY KEY (`trans_id`)
			)DEFAULT CHARSET=utf8';
			$wpdb->query($transaction_table);	
		}
		
		$field_check = $wpdb->get_var("SHOW COLUMNS FROM $transection_db_table_name LIKE 'package_id'");		
		if('package_id' != $field_check){
			$wpdb->query("ALTER TABLE $transection_db_table_name ADD package_id int(10) NOT NULL DEFAULT '0'");
		}
		$field_check = $wpdb->get_var("SHOW COLUMNS FROM $transection_db_table_name LIKE 'package_type'");		
		if('package_type' != $field_check){
			$wpdb->query("ALTER TABLE $transection_db_table_name ADD package_type VARCHAR(255) NULL DEFAULT NULL");
		}
		/*transaction table EOF*/
	}
}
?>