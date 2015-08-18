<?php
/*
Plugin Name: Tevolution
Plugin URI: http://templatic.com/
Description: Tevolution is a collection of Templatic features to enhance your website.
Version: 2.1.8
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
	define('TEVOLUTION_VERSION','2.1.8');
	@define('PLUGIN_NAME','Tevolution Plugin');
	define('TEVOLUTION_SLUG','Tevolution/templatic.php');
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	require_once(plugin_dir_path( __FILE__ ).'classes/templconnector.class.php' );
	
	//Change apache AllowOverride in overview page
	if(function_exists("is_admin") && is_admin() && @$_REQUEST['page'] == "templatic_system_menu"){
		ini_set("AllowOverride","All");
	}
	
	/* for auto updates */
	if(strstr($_SERVER['REQUEST_URI'],'plugins.php')){
		require_once('wp-updates-plugin.php');
		new WPUpdatesTevolutionUpdater( 'http://templatic.com/updates/api/index.php', plugin_basename(__FILE__) );
	}
	
	// Plugin Folder URL
	define( 'TEVOLUTION_PAGE_TEMPLATES_URL', plugin_dir_url( __FILE__ ) );
	// Plugin Folder Path
	define( 'TEVOLUTION_PAGE_TEMPLATES_DIR', plugin_dir_path( __FILE__ ) );
        
        /*included the class-wp-list-table.php wordpress file*/
        include_once(plugin_dir_path( __FILE__ ).'class-wp-list-table.php');
	
	define('TEMPL_MONETIZE_FOLDER_PATH', plugin_dir_path( __FILE__ ).'tmplconnector/monetize/');
	define('TEMPL_PLUGIN_URL', plugin_dir_url( __FILE__ ));
	define('TT_CUSTOM_USERMETA_FOLDER_PATH',TEMPL_MONETIZE_FOLDER_PATH.'templatic-registration/custom_usermeta/');
	define('TEMPL_PAYMENT_FOLDER_PATH',TEMPL_MONETIZE_FOLDER_PATH.'templatic-monetization/templatic-payment_options/payment/');
	define('MY_PLUGIN_SETTINGS_URL',site_url().'/wp-admin/admin.php?page=templatic_system_menu&activated=true');
	define( 'DOMAIN', 'templatic');  //tevolution* deprecated
	@define( 'ADMINDOMAIN', 'templatic' ); //tevolution* deprecated
	
	$locale = get_locale();
	
	if(is_admin()){
		load_textdomain( ADMINDOMAIN, plugin_dir_path( __FILE__ ).'languages/templatic-admin-'.$locale.'.mo' );
		load_textdomain( DOMAIN, plugin_dir_path( __FILE__ ).'languages/templatic-'.$locale.'.mo' );
	}else{
		load_textdomain( DOMAIN, plugin_dir_path( __FILE__ ).'languages/templatic-'.$locale.'.mo' );
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
		//require_once( plugin_dir_path( __FILE__ ).'classes/main.connector.class.php' );			
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
	   require_once( plugin_dir_path( __FILE__ ).'tmplconnector/shortcodes/shortcode-init.php' );
	   require_once( plugin_dir_path( __FILE__ ).'tmplconnector/taxonomies_permalink/taxonomies_permalink.php' );
	 	
        global $tmplconnector;
		/* remove custom user meta box*/
		function remove_custom_metaboxes() {
			$custom_post_types_args = array();  
			$custom_post_types = get_post_types($custom_post_types_args,'objects');   
			foreach ($custom_post_types as $content_type) 
			{
			 remove_meta_box( 'postcustom' , $content_type->name , 'normal' ); //removes custom fields for page
			}
		}
		add_action( 'admin_menu' , 'remove_custom_metaboxes' );
    }
	
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
						 'post_default_status'			=> 'publish',
						 'post_default_status_paid' 		=> 'draft',
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
				'name' => 'Paypal',
				'key' => 'paypal',
				'isactive' => 1,
				'display_order' => 1,
				'payOpts' => array
					(
						array
							(
								'title' =>  __('Your PayPal email',ADMINDOMAIN),
								'fieldname' => 'merchantid',
								'value' => 'email@example.com',
								'description' =>  __('Example : email@example.com',ADMINDOMAIN)
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
								'title' => __('Bank Information',ADMINDOMAIN),
								'fieldname' => 'bankinfo',
								'value' => 'ICICI Bank',
								'description' => __('Enter the bank name to which you want to transfer payment',ADMINDOMAIN)
							),
						array
							(
								'title' =>  __('Account ID',ADMINDOMAIN),
								'fieldname' => 'bank_accountid',
								'value' => 'AB1234567890',
								'description' =>  __('Enter your bank Account ID',ADMINDOMAIN)
							),
					),
			);
			update_option('payment_method_prebanktransfer',$prebanktransfer_update);
			
			
		}
		/*	Updated default payment gateway option on plugin activation END	*/
		
		
		update_option('myplugin_redirect_on_first_activation', 'true');
		$default_pointers = "wp330_toolbar,wp330_media_uploader,wp330_saving_widgets,wp340_choose_image_from_library,wp340_customize_current_theme_link";
		update_user_meta(get_current_user_id(),'dismissed_wp_pointers',$default_pointers);
		
		
		//Set Default permalink on theme activation: start
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$wp_rewrite->flush_rules();
		if(function_exists('flush_rewrite_rules')){
			flush_rewrite_rules(true);  
		}
		//Set Default permalink on theme activation: end
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
			'post_title' 		=> 'Profile',
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
	}
	function my_plugin_deactivate() { 
		delete_option('myplugin_redirect_on_first_activation');
		
		/*Clear scheduled event on plugin deactivate hook */
		wp_clear_scheduled_hook( 'daily_schedule_expire_session' );
	}
	register_activation_hook(__FILE__, 'my_plugin_activate');
	register_deactivation_hook(__FILE__, 'my_plugin_deactivate');
	


add_action('wp_head', 'recatcha_settings');
function recatcha_settings()
{
	$a = get_option('recaptcha_options');
	?>
<script type="text/javascript"> var upload_single_title = '<?php _e("Upload Image",DOMAIN);?>'; var RecaptchaOptions = { theme : '<?php echo $a['comments_theme']; ?>', lang : '<?php echo $a['recaptcha_language']; ?>', tabindex :'<?php echo $a['comments_tab_index']?>' }; </script>
	<?php
	
	/**
	* Author post delete code
	*/
	if(is_author()){ 
		$delete_auth_post = wp_create_nonce( "auth-delete-post" );
		$currUrl = ( is_ssl() ) ? 'https://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] : 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery(".autor_delete_link").click(function(){
					if( confirm( "<?php _e("Are you really sure want to DELETE this post? Deleted post can not be recovered later.",DOMAIN);?>" ) ){
						jQuery(this).after("<span class='delete_append'><?php _e('Deleting.',DOMAIN);?></span>");
						jQuery(".delete_append").css({"margin":"5px","vertical-align":"sub","font-size":"14px"});
						setTimeout(function() {
						   jQuery(".delete_append").html('<?php _e('Deleting..',DOMAIN);?>');
						}, 700);
						setTimeout(function() {
						   jQuery(".delete_append").html('<?php _e('Deleting...',DOMAIN);?>');
						}, 1400);
						jQuery.ajax({
							url:ajaxUrl,
							type:'POST',
							data:'action=delete_auth_post&security=<?php echo $delete_auth_post; ?>&postId='+ jQuery(this).attr('data-deleteid') + '&currUrl=<?php echo $currUrl;?>',
							success:function(redirect) {	
								window.location.href = redirect;
							},
						});
						
						return false;
					}else{
						return false;
					}
				});
			});
		</script>
<?php
	}
	/**
	* Author post delete code end
	*/
}
/**
* Function: delete_auth_post_function
* Filter: wp_ajax_* and wp_ajax_nopriv_*
* Return: delete current author post
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
 * Function Name: tevolution_update_login
 * Return: update tevolution plugin version after templatic member login
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
}
function tevolution_success_page_title()
{
		echo sprintf(__("%s Submitted Successfully",DOMAIN),ucfirst(get_post_type($_REQUEST['pid'])));
}
/* plugin activation - settings link*/
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),'tevolution_action_links'  );
function tevolution_action_links($links){
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=templatic_settings' ) . '">' . __( 'Settings', DOMAIN ) . '</a>',			
	);
	return array_merge( $plugin_links, $links );
}
function remove_media_library_tab($tabs) {
    unset($tabs['library']);
    return $tabs;
}
add_filter('media_upload_tabs', 'remove_media_library_tab');
/*
 * Function Name: set_texteditor_visual
 * Description: Set visual on texteditor by default.
 */
add_action('wp_head','set_texteditor_visual');
function set_texteditor_visual()
{
	add_filter( 'wp_default_editor', create_function('', 'return "tinymce";') );
}
/* add shortcode */
// init process for registering our button
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
	 
      //Abort early if the user will never see TinyMCE
      if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
           return;
      //Add a callback to regiser our tinymce plugin   
      add_filter("mce_external_plugins", "tevolution_register_tinymce_plugin"); 
      // Add a callback to add our button to the TinyMCE toolbar
      add_filter('mce_buttons', 'tevolution_add_tinymce_shortcode_button');
	
}
//This callback registers our plug-in
function tevolution_register_tinymce_plugin($plugin_array) {
    $plugin_array['tevolution_shortcodes'] = plugin_dir_url( __FILE__ ).'js/shortcodes.js';
    return $plugin_array;
}
//This callback adds our button to the toolbar
function tevolution_add_tinymce_shortcode_button($buttons) {
            //Add the button ID to the $button array
    $buttons[] = "tevolution_shortcodes";
    return $buttons;
}
/*
include font awesome css.
*/
add_action( 'init', 'plugin_css_on_init' ); // include fonts awesome
   
function plugin_css_on_init() {
   /* Register our stylesheet. */
   wp_register_style( 'fontawesomecss', 'https://netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' );
   wp_enqueue_style( 'fontawesomecss' );
}
/*
Name:get_tmpl_plugin_directory
desc: return the plugin directory path
*/
if(!function_exists('get_tmpl_plugin_directory')){
function get_tmpl_plugin_directory() {
	 return trailingslashit(WP_PLUGIN_DIR);
}
}

/* Adds the option to change number of columns on dashboard */

if(!function_exists('tevolution_dashboard_columns')){
function tevolution_dashboard_columns() {
    add_screen_option(
        'layout_columns',
        array(
            'max'     => 4,
            'default' => 2
        )
    );
}
}
add_action( 'admin_head-index.php', 'tevolution_dashboard_columns' );
?>
