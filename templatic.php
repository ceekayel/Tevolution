<?php
/*
Plugin Name: Tevolution
Plugin URI: http://templatic.com/docs/tevolution-guide/
Description: Tevolution is a collection of Templatic features to enhance your website.
Version: 2.2.11
Author: Templatic
Author URI: http://templatic.com/
*/
ob_start();
if (defined('WP_DEBUG') and WP_DEBUG == true){
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}
define('PLUGIN_FOLDER_NAME','Tevolution');
define('TEVOLUTION_VERSION','2.2.11');
@define('PLUGIN_NAME','Tevolution Plugin');
define('TEVOLUTION_SLUG','Tevolution/templatic.php');

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once(plugin_dir_path( __FILE__ ).'classes/templconnector.class.php' );

/* Plugin Folder URL*/
define( 'TEVOLUTION_PAGE_TEMPLATES_URL', plugin_dir_url( __FILE__ ) );
/* Plugin Folder Path*/
define( 'TEVOLUTION_PAGE_TEMPLATES_DIR', plugin_dir_path( __FILE__ ) );

/*included the class-wp-list-table.php wordpress file*/
include_once(plugin_dir_path( __FILE__ ).'class-wp-list-table.php');

define('TEMPL_MONETIZE_FOLDER_PATH', plugin_dir_path( __FILE__ ).'tmplconnector/monetize/');
define('TEMPL_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('TT_CUSTOM_USERMETA_FOLDER_PATH',TEMPL_MONETIZE_FOLDER_PATH.'templatic-registration/custom_usermeta/');
define('TEMPL_PAYMENT_FOLDER_PATH',TEMPL_MONETIZE_FOLDER_PATH.'templatic-monetization/templatic-payment_options/payment/');
define('MY_PLUGIN_SETTINGS_URL',site_url().'/wp-admin/admin.php?page=templatic_system_menu&activated=true');
//if(!defined('DOMAIN'))
	//define('DOMAIN', 'templatic');  /*tevolution* deprecated*/
//if(!defined(''templatic-admin''))
	//define( ''templatic-admin'', 'templatic-admin' ); /*tevolution* deprecated*/


$locale = get_locale();
if(is_admin()){
	load_textdomain( 'templatic-admin', plugin_dir_path( __FILE__ ).'languages/templatic-admin-'.$locale.'.mo' );
	load_textdomain( 'templatic', plugin_dir_path( __FILE__ ).'languages/templatic-'.$locale.'.mo' );
}else{
	load_textdomain( 'templatic' , plugin_dir_path( __FILE__ ).'languages/templatic-'.$locale.'.mo' );
}

global $templatic,$wpdb,$tevolutions_icon;
$tevolutions_icon = array('event,listing');
$wpdb->query("set sql_big_selects=1");
if(class_exists('templatic')){
	$templatic = new Templatic( __FILE__ );
	global $templatic;
}
if ( ! class_exists( 'Templatic_connector' ) ) {
	require_once( plugin_dir_path( __FILE__ ).'classes/templconnector.class.php' );
	$templconnector = new Templatic_connector( __FILE__ );
	global $templconnector;
}
if ( apply_filters( 'tmplconnector_enable', true ) == true ) {
	if(!function_exists('wp_get_current_user')) {
		include(ABSPATH . "wp-includes/pluggable.php"); 
	}
	$file = dirname(__FILE__);
	$content_dir = explode("/",WP_CONTENT_DIR);
	$file = substr($file,0,stripos($file, $content_dir[1]));

   require_once( plugin_dir_path( __FILE__ ).'tmplconnector/templatic-connector.php' );
   require_once( plugin_dir_path( __FILE__ ).'tmplconnector/tevolution_page_templates.php' );
   require_once( plugin_dir_path( __FILE__ ).'tmplconnector/tevolution_ajax_results.php' );
   require_once( plugin_dir_path( __FILE__ ).'tmplconnector/shortcodes/shortcode-init.php' );
   if(!strstr($_SERVER['REQUEST_URI'],'plugin-install.php')  ){
   		require_once( plugin_dir_path( __FILE__ ).'tmplconnector/taxonomies_permalink/taxonomies_permalink.php' );
   }
	
	global $tmplconnector;
	/* remove custom user meta box*/
	function remove_custom_metaboxes() {
		$custom_post_types_args = array();
		$custom_post_types = get_post_types($custom_post_types_args,'objects');
		foreach ($custom_post_types as $content_type){
			remove_meta_box( 'postcustom' , $content_type->name , 'normal' ); /*removes custom fields for page*/
		}
	}
	add_action( 'admin_menu' , 'remove_custom_metaboxes' );
}

/*Change apache AllowOverride in overview page*/
if(function_exists("is_admin") && is_admin() && @$_REQUEST['page'] == "templatic_system_menu"){
	ini_set("AllowOverride","All");
}

/* for auto updates */
if(strstr($_SERVER['REQUEST_URI'],'plugins.php') || strstr($_SERVER['REQUEST_URI'],'update.php') || strstr($_SERVER['REQUEST_URI'],'update-core.php') ){
	require_once('wp-updates-plugin.php');
	new WPUpdatesTevolutionUpdater( 'http://templatic.com/updates/api/index.php', plugin_basename(__FILE__) );
}

/*show message while site receives an fatal error.Same function you can find it in directory theme*/
if(!function_exists('tmpl_fatalErrorHandler')){
	/**
	 * Handling fatal error
	 *
	 * @return void
	 */
	function tmpl_fatalErrorHandler()
	{
		/* Getting last error */
		if(get_option('tmpl_chk_fatal_error_onupdate') !='done'){
		$error = error_get_last();
		
		# Checking if last error is a fatal error 
		if($error['type'] === E_ERROR)
		{
			$wp_plugins = get_plugins();
			$phpversion = phpversion();
			$weprefer = 5.3;
			if(version_compare($phpversion,$weprefer,'<')){
				$message1 .= "Your PHP version is not compatible update it to 5.3 or 5.3+";
			}else{
				$message1 ='';
			}
			$i = 0;
			/* get all active plug ins of templatic */
			foreach ( (array)$wp_plugins as $plugin_file => $plugin_data ) {
				if(is_plugin_active($plugin_file) || is_plugin_active_for_network( $plugin_file )){
					if($plugin_data['Author'] =='Templatic')
					{
						$plugins[$plugin_file] =  $plugin_data;
					}
				}
			}
			$theme_data = wp_get_theme();
		
			$messaeg = '';
			$message .= "<div class='tmpl_addon_message'>";
			$message .= "<p>".__("Oops! Site seems to be in trouble. We find some 'Templatic' Add-ons installed but not updated on your site. If any of the add-on is having older version, please download it from <a href='http://templatic.com/members'>members</a> area and <a href='http://templatic.com/docs/how-to-manage-and-handle-theme-updates/'>update</a> it manually. If this wan't work go to wp-config.php file located in root of WordPress installation. Change define('WP_DEBUG',false) to define('WP_DEBUG',true) and submit the ticket with error in <a href='http://templatic.com/docs/submit-a-ticket/'>helpdesk</a>.",'templatic')."</p>";
			
			$message .= $message1;
			?>
			<style>.dump_http{ display:none; }</style>
			<?php
			$response = wp_remote_get("http://templatic.com/updates/api/index.php?action=package_details");
			
			$responde_encode = json_decode($response['body']);
			$i =0;
			$message .= "<ul>";
			foreach($plugins as $key => $val){
				$keys = $responde_encode->$key->versions;
				foreach($keys as $k =>$v){
					$new_version =  $k;
				}
				
				if(version_compare($val['Version'], $new_version,'<')){
					$style ="style=color:red;";
					$message .= "<li><span class='tplugin_name'>".$val['Name']."</span> | <span class='tversion'>".$val['Version']."</span> | <span class='tlatest_version' $style>".$new_version."</span></li>";
					$i++;
				}else{
					$style ='';
				}
				
					
			}
			if($i >=1){
				if(!in_array('Tevolution/templatic.php',$plugins))
				{
					$message .= "<li><span class='tplugin_name'> It also seems that the base system ( Tevolution ) of all this add-ons and themes is not activated. Activate it Or If its want work upload it manually.</li>";
				}
			}
			
			$message .= "</ul>";
			$message .= "</div>";
			
			echo $message;
			/* Getting last error */
			
			$error = error_get_last();
			unset($plugins);
			update_option('tmpl_chk_fatal_error_onupdate','done');
		}
		}
	}
	 
	# Registering shut-down function
	register_shutdown_function('tmpl_fatalErrorHandler');
}

/* set tevolution settings while plugin activation */
function my_plugin_activate() {
	update_option('templatic-login','Active');
	/*set templatic settings option */
	$templatic_settings=get_option('templatic_settings');
	$settings=array(
					 'templatic_view_counter' 		=> 'Yes',
					 'default_page_view'               => 'listview',
					 'templatic_image_size'            => '50000',
					 'facebook_share_detail_page'      => 'yes',
					 'google_share_detail_page'        => 'yes',
					 'twitter_share_detail_page'       => 'yes',
					 'pintrest_detail_page'            => 'yes',
					 'related_post' 				=> 'categories',
					 'php_mail'					=> 'php_mail',
					 'templatic-category_custom_fields'=> 'No',
					 'templatic-category_type'         => 'checkbox',
					 'tev_accept_term_condition'       => 1,						 
					 'listing_email_notification' 	=> 5,
					 'templatin_rating' 			=> 'yes',
					 'post_default_status'			=> 'draft',
					 'post_default_status_paid' 		=> 'publish',
					 'send_to_frnd'   				=> 'send_to_frnd',
					 'send_inquiry'   				=> 'send_inquiry',
					 'allow_autologin_after_reg' 		=> '1',
					 'templatic-current_tab'			=> 'current',
					 'templatic-sort_order'			=> 'published',
					 'pippoint_effects'                => 'click',
					 'sorting_type'                    => 'select',
					 'sorting_option'                  => array('title_alphabetical','title_asc','title_desc','date_asc','date_desc','reviews','rating','random','stdate_low_high','stdate_high_low'),    
					 'templatic_widgets' 			=> array( 'templatic_browse_by_categories','templatic_browse_by_tag','templatic_aboust_us')
					);
		
		if(empty($templatic_settings))
		{
			update_option('templatic_settings',$settings);	
		}else{
			update_option('templatic_settings',array_merge($templatic_settings,$settings));
		}
		/* finish the templatic settings option */
	
	/*	Updated default payment gateway option on plugin activation START	*/
	if(!get_option('payment_method_paypal')){
		$paypal_update = array(
			'name' => 'PayPal',
			'key' => 'paypal',
			'isactive' => 1,
			'display_order' => 1,
			'payOpts' => array
				(
					array
						(
							'title' =>  __('Your PayPal Email','templatic-admin'),
							'fieldname' => 'merchantid',
							'value' => 'email@example.com',
							'description' =>  __('Example: email@example.com','templatic-admin')
						),
				),			
		);
		update_option('payment_method_paypal',$paypal_update);
	}
	if(!get_option('payment_method_prebanktransfer')){
		$prebanktransfer_update = array(
			'name' => 'Pre Bank Transfer',
			'key' => 'prebanktransfer',
			'isactive' => 1,
			'display_order' => 6,
			'payOpts' => array
				(
					array
						(
							'title' => __('Bank Information','templatic-admin'),
							'fieldname' => 'bankinfo',
							'value' => 'ICICI Bank',
							'description' => __('Enter the bank name to which you want to transfer payment','templatic-admin')
						),
					array
						(
							'title' =>  __('Account ID','templatic-admin'),
							'fieldname' => 'bank_accountid',
							'value' => 'AB1234567890',
							'description' =>  __('Enter your bank Account ID','templatic-admin')
						),
				),
		);
		update_option('payment_method_prebanktransfer',$prebanktransfer_update);
	}
	/*	Updated default payment gateway option on plugin activation END	*/
	
	update_option('myplugin_redirect_on_first_activation', 'true');
	$default_pointers = "wp330_toolbar,wp330_media_uploader,wp330_saving_widgets,wp340_choose_image_from_library,wp340_customize_current_theme_link";
	update_user_meta(get_current_user_id(),'dismissed_wp_pointers',$default_pointers);	
	
	/*Set Default permalink on theme activation: start*/
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	$wp_rewrite->flush_rules();
	if(function_exists('flush_rewrite_rules')){
		flush_rewrite_rules(true);  
	}
	/*Set Default permalink on theme activation: end*/
	/*Tevolution login page */
	global $wpdb;
	$templatic_settings=get_option('templatic_settings');
	if(!$templatic_settings)
	{
		$templatic_settings = array();
	}
	$login_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = 'login'" );
	if($login_id=='')
	{	
		$login_data = array(
		'post_status' 		=> 'publish',
		'post_type' 		=> 'page',
		'post_author' 		=> 1,
		'post_name' 		=> 'login',
		'post_title' 		=> 'Login',
		'post_content' 		=> '[tevolution_login][tevolution_register]',
		'post_parent' 		=> 0,
		'comment_status' 	=> 'closed'
		);
		$login_id = wp_insert_post( $login_data );
		update_post_meta($login_id,'_wp_page_template','default');
		
		$tmpdata['tevolution_login'] = $login_id;
		$templatic_settings=array_merge($templatic_settings,$tmpdata);			
		update_option('templatic_settings',$templatic_settings);
		update_option('tevolution_login',$login_id);
	
	}
	/*Tevolution Register Page */
	$register_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = 'register'" );
	if($register_id=='')
	{	
		$register_data = array(
		'post_status' 		=> 'publish',
		'post_type' 		=> 'page',
		'post_author' 		=> 1,
		'post_name' 		=> 'register',
		'post_title' 		=> 'Register',
		'post_content' 		=> '[tevolution_register]',
		'post_parent' 		=> 0,
		'comment_status' 	=> 'closed'
		);
		$register_id = wp_insert_post( $register_data );
		update_post_meta($register_id,'_wp_page_template','default');
		$tmpdata['tevolution_register'] = $register_id;
		$templatic_settings=array_merge($templatic_settings,$tmpdata);			   
		update_option('templatic_settings',$templatic_settings);
		update_option('tevolution_register',$register_id);
	}
	/*Tevolution Register Page */
	$profile_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = 'profile'" );
	if($profile_id=='')
	{	
		$profile_data = array(
		'post_status' 		=> 'publish',
		'post_type' 		=> 'page',
		'post_author' 		=> 1,
		'post_name' 		=> 'profile',
		'post_title' 		=> 'Edit Profile',
		'post_content' 		=> '[tevolution_profile]',
		'post_parent' 		=> 0,
		'comment_status' 	=> 'closed'
		);
		$profile_id = wp_insert_post( $profile_data );
		update_post_meta($profile_id,'_wp_page_template','default');
		$tmpdata['tevolution_profile'] = $profile_id;
		$templatic_settings=array_merge($templatic_settings,$tmpdata);
		update_option('templatic_settings',$templatic_settings);
		update_option('tevolution_profile',$profile_id);
	}
	
	update_option('tevolution_cache_disable',1);
	wp_schedule_event( time(), 'daily', 'daily_schedule_expire_session');
        
        /* Set On anyone can register at the time of plugin activate */
        update_option('users_can_register',1);
        }
/* function called while plugin deactivation */
function my_plugin_deactivate() {
	delete_option('myplugin_redirect_on_first_activation');	
	/*Clear scheduled event on plugin deactivate hook */
	wp_clear_scheduled_hook( 'daily_schedule_expire_session' );
}
/* set tevolution settings while plugin activation */
register_activation_hook(__FILE__, 'my_plugin_activate');
/* delete the option while plugin deactivation*/
register_deactivation_hook(__FILE__, 'my_plugin_deactivate');

/**
* To delete current author post
*/
add_action( 'wp_ajax_delete_auth_post', 'delete_auth_post_function' );
add_action( 'wp_ajax_nopriv_delete_auth_post', 'delete_auth_post_function' );
if( !function_exists( 'delete_auth_post_function' ) ){
	function delete_auth_post_function(){
		check_ajax_referer( 'auth-delete-post', 'security' );
		global $current_user;
		get_currentuserinfo();
		$post_authr = get_post( @$_POST['postId'] );
		if( $post_authr->post_author == $current_user->ID ){
			wp_delete_post( $_POST['postId'], true );
			echo $_REQUEST['currUrl'];
		}
		die;
	}
}
/*
 * Update tevolution plugin version after templatic member login
 */
add_action('wp_ajax_tevolution','tevolution_update_login');
function tevolution_update_login()
{
	check_ajax_referer( 'tevolution', '_ajax_nonce' );
	$plugin_dir = rtrim( plugin_dir_path(__FILE__), '/' );
	require_once( $plugin_dir .  '/templatic_login.php' );
	exit;
}
/* remove wp autoupdates */
add_action('admin_init','templatic_wpup_changes',20);
function templatic_wpup_changes(){
	remove_action( 'after_plugin_row_Tevolution/templatic.php', 'wp_plugin_update_row' ,10, 2 );
}

/* success page title */
if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'success' )
{
	add_filter( 'wp_title', 'tevolution_success_page_title' );
}elseif(isset($_REQUEST['ptype']) && $_REQUEST['ptype'] == 'cancel'){
	add_filter( 'wp_title', 'tevolution_cancel_page_title' );
}
function tevolution_success_page_title()
{
	$post_type = get_post_type($_REQUEST['pid']);
	
	$post_type_object = get_post_type_object($post_type);
	
	$post_type_label = ( @$post_type_object->labels->post_name ) ? @$post_type_object->labels->post_name  :  $post_type_object->labels->singular_name ;

	echo $post_type_label.' '.__('Submitted Successfully','templatic');
}

function tevolution_cancel_page_title()
{
	_e('Payment Cancelled','templatic');
}
/* plug-in activation - settings link*/
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),'tevolution_action_links'  );
function tevolution_action_links($links){
	$plugin_links = array('<a href="' . admin_url( 'admin.php?page=templatic_settings' ) . '">' . __( 'Settings', 'templatic' ) . '</a>');
	return array_merge( $plugin_links, $links );
}
/* add shortcode */
/* init process for registering our button*/
if(isset($_REQUEST['post']) && $_REQUEST['post'] !=''){
	$post = get_post($_REQUEST['post']);
	$post_type = $post->post_type;
}else{
	$post_type = @$_REQUEST['post_type'];
}
if(isset($post_type) && $post_type == 'page'){ 
	add_action('init', 'tevolution_shortcode_button_init');
}
function tevolution_shortcode_button_init() {
  global $pagenow; 
  /*Abort early if the user will never see TinyMCE*/
  if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
	   return;
  /*Add a callback to regiser our tinymce plugin   */
  add_filter("mce_external_plugins", "tevolution_register_tinymce_plugin");
  /* Add a callback to add our button to the TinyMCE toolbar*/
  add_filter('mce_buttons', 'tevolution_add_tinymce_shortcode_button');	
}
/*This callback registers our plug-in*/
function tevolution_register_tinymce_plugin($plugin_array) {
    $plugin_array['tevolution_shortcodes'] = plugin_dir_url( __FILE__ ).'js/shortcodes.js';
    return $plugin_array;
}
/*This callback adds our button to the toolbar*/
function tevolution_add_tinymce_shortcode_button($buttons) {
	/*Add the button ID to the $button array*/
    $buttons[] = "tevolution_shortcodes";
    return $buttons;
}
/*Remove 2012 Mobile Javascript*/
function de_script() {
    wp_dequeue_style( 'dashicons-css' );
}
add_action( 'init', 'de_script', 100 );	
/*
	Return the plugin directory path
*/
if(!function_exists('get_tmpl_plugin_directory')){
function get_tmpl_plugin_directory() {
	 return trailingslashit(WP_PLUGIN_DIR);
}
}
/* Adds the option to change number of columns on dashboard */

if(!function_exists('tevolution_dashboard_columns')){
	function tevolution_dashboard_columns() {
		add_screen_option('layout_columns',array('max'     => 4,'default' => 2));
	}
}
add_action( 'admin_head-index.php', 'tevolution_dashboard_columns' );

/*
 * Subscriber user also edit his/her own draft on front-end
 */
add_action( 'admin_init', 'tmpl_tevolution_enable_view_drafts');
function tmpl_tevolution_enable_view_drafts() {

	if(get_option('tmpl_assign_user_role') !='done'){
		if(!is_admin()){
			$role = get_role( 'subscriber' ); 
			$role->add_cap( 'read_private_posts' ); 
			$role->add_cap( 'edit_posts' );
		}else{
			$role = get_role( 'subscriber' );
			$role->remove_cap( 'read_private_posts' );
			$role->remove_cap( 'edit_posts' );
		}
		update_option('tmpl_assign_user_role','done');
	}
}
/*Remove advance search page short code */
add_action('admin_init','remove_advance_search_shortcode_pages');
function remove_advance_search_shortcode_pages(){
	
	if(!get_option('remove_advance_search_shortcode_pages')){
		global $wpdb;
		/*Delete advance search page */
		$post_content = $wpdb->query("delete  FROM $wpdb->posts WHERE $wpdb->posts.post_content like  '%[advance_search_page%' and $wpdb->posts.post_type = 'page'");
		update_option('remove_advance_search_shortcode_pages',1);
	}
}

/* Provide REST api compatibility with tevolution and directory */
if( is_plugin_active( 'json-rest-api/plugin.php' ) ) {
	/* Include main api file for tevolution	 */
	include_once( dirname( __FILE__ ) . '/api/tevolution-wp-json-api.php' );
		
	$wp_json_city = new Tevolution_wp_json_api( $server );
	add_filter( 'json_endpoints', array( $wp_json_city, 'register_routes' ), 0 );
	add_filter( 'json_prepare_city', array( $wp_json_city, 'add_post_type_data' ), 10, 3 );
}

/* user useages curl responce */

$site_info_tracking_allow = get_option("tmpl_site_info_tracking");
$get_usages_date = get_option('tmpl_usages_last_date');
if($site_info_tracking_allow == 1 && empty($get_usages_date) || (time() > $get_usages_date)){
    
    /* set time to schedule usage report */
    //$next_schedule_date = strtotime("+15 seconds");
    //$next_schedule_date = strtotime("+15 minutes");
    $next_schedule_date = strtotime("+15 days");
    update_option('tmpl_usages_last_date',$next_schedule_date);
    include_once( dirname( __FILE__ ) . '/api/usages-report.php' );
}
?>