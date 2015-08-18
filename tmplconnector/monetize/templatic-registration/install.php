<?php
global $wp_query,$wpdb,$wp_rewrite,$post;
define('TEMPL_REGISTRATION_FOLDER_PATH',TEMPL_MONETIZE_FOLDER_PATH.'templatic-registration/');
/* conditions for activation of login wizard */
if((isset($_REQUEST['activated']) && $_REQUEST['activated'] == 'templatic-login') && (isset($_REQUEST['true']) && $_REQUEST['true']==1)){
	update_option('templatic-login','Active');
}elseif((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'templatic-login') && (isset($_REQUEST['true']) && $_REQUEST['true']==0)){
	
	$tmpdata = get_option('templatic_settings');	
	delete_option('templatic-login');
	/* delete two fields of user name and email while deavtivation this meta box */
	$postname = 'user_fname';
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
	wp_delete_post($postid);
	$postname = 'user_email';
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
	wp_delete_post($postid);
	$display_name = 'display_name';
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $display_name . "'" );
	wp_delete_post($postid);
	$postname = 'facebook';
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
	wp_delete_post($postid);
	$postname = 'twitter';
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
	wp_delete_post($postid);
	$postname = 'linkedin';
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
	wp_delete_post($postid);
	$postname = 'description';
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
	wp_delete_post($postid);
	$postname = 'profile_photo';
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
	wp_delete_post($postid);
	$postname = 'url';
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
	wp_delete_post($postid);
	$postname = 'user_phone';
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
	wp_delete_post($postid);
	/*Delete the tevolution register module related page */
	//remove tevolution_login
	wp_delete_post($tmpdata['tevolution_login'],true);
	$tevolution_login = array_search($tmpdata['tevolution_login'], $tmpdata);
	unset($tmpdata[$tevolution_login]);
	delete_option('tevolution_login');
	//remove tevolution_register
	wp_delete_post($tmpdata['tevolution_register'],true);
	$tevolution_register = array_search($tmpdata['tevolution_register'], $tmpdata);
	unset($tmpdata[$tevolution_register]);
	delete_option('tevolution_register');
	
	//remove tevolution profile page
	wp_delete_post($tmpdata['tevolution_profile'],true);
	$tevolution_profile = array_search($tmpdata['tevolution_profile'], $tmpdata);
	unset($tmpdata[$tevolution_profile]);
	delete_option('tevolution_profile');
	
	update_option('templatic_settings',$tmpdata);
}
/*
*Name:create_default_registration_customfields
*Description: create user custom fields while registration module activate.
*/
add_action('admin_init','create_default_registration_customfields');
function create_default_registration_customfields()
{
	if((@$_REQUEST['activated'] == 'templatic-login' && @$_REQUEST['true']==1) || (@$_REQUEST['page'] == 'templatic_system_menu' && @$_REQUEST['activated']=='true')){
		$args = array(
      'public' => true,
      'label'  => 'User Fields'
    );
    register_post_type( 'custom_user_field', $args );
		$tmpdata = get_option('templatic_settings');
		$tmpdata['allow_autologin_after_reg'] = 'No';
		update_option('templatic_settings',$tmpdata);
		/* insert two fields of user name and email while activation this meta box */
		global $current_user,$wpdb;
		$postname = 'user_email';
		$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
		if(!$postid)
		{
			$my_post = array();
			$my_post['post_title'] = 'E-mail';
			$my_post['post_name'] = 'user_email';
			$my_post['post_content'] = '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;		
			$my_post['post_type'] = 'custom_user_field';
			$custom = array("ctype"		     => 'text',
						 "sort_order" 		=> '1',
						 "on_registration"	=> '1',
						 "on_profile"		=> '1',
						 "option_values"	=> '',
						 "is_require"		=> '1',
						 "on_author_page"	=> '1'
						);
			$last_postid = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
			}
			foreach($custom as $key=>$val)
			{				
				update_post_meta($last_postid, $key, $val);
			}
		}
		$postname = 'user_fname';
		$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
		if(!$postid)
		{
			/* User Name custom field */
			$my_post = array();
			$my_post['post_title']  = 'User name';
			$my_post['post_name']   = 'user_fname';
			$my_post['post_content']= '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type']   = 'custom_user_field';
			$custom = array("ctype"		     => 'text',
						 "sort_order" 		=> '2',
						 "on_registration"	=> '1',
						 "on_profile"		=> '1',
						 "option_values"	=> '',
						 "is_require"		=> '1',
						 "on_author_page"	=> '1'
						);
			$last_postid = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
			}
			foreach($custom as $key=>$val)
			{				
				update_post_meta($last_postid, $key, $val);
			}
		}
		$display_name = 'display_name';
		$display_namepostid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $display_name . "'" );
		if(!$display_namepostid)
		{
			/* website url custom field */
			$my_post = array();
			$my_post['post_title']  = 'Display Name';
			$my_post['post_name']   = 'display_name';
			$my_post['post_content']= '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type']   = 'custom_user_field';
			$custom = array("ctype"		     => 'text',
						 "sort_order" 		=> '3',
						 "on_registration"	=> '0',
						 "on_profile"		=> '1',
						 "option_values"	=> '',
						 "is_require"		=> '0',
						 "on_author_page"	=> '1'
						);
			$last_postid = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
			}
			foreach($custom as $key=>$val)
			{				
				update_post_meta($last_postid, $key, $val);
			}
		}
		$user_google = 'user_google';
		$user_google_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $user_google . "'" );
		if(!$user_google_id)
		{
			/* website url custom field */
			$my_post = array();
			$my_post['post_title']  = 'Google+';
			$my_post['post_name']   = 'user_google';
			$my_post['post_content']= '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type']   = 'custom_user_field';
			$custom = array("ctype"		     => 'text',
						 "sort_order" 		=> '3',
						 "on_registration"	=> '0',
						 "on_profile"		=> '1',
						 "option_values"	=> '',
						 "is_require"		=> '0',
						 "on_author_page"	=> '1'
						);
			$last_postid = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
			}
			foreach($custom as $key=>$val)
			{				
				update_post_meta($last_postid, $key, $val);
			}
		}			
		$website = 'url';
		$websitepostid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $website . "'" );
		if(!$websitepostid)
		{
			/* website url custom field */
			$my_post = array();
			$my_post['post_title']  = 'Website';
			$my_post['post_name']   = 'url';
			$my_post['post_content']= '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type']   = 'custom_user_field';
			$custom = array("ctype"		     => 'text',
						 "sort_order" 		=> '4',
						 "on_registration"	=> '0',
						 "on_profile"		=> '1',
						 "option_values"	=> '',
						 "is_require"		=> '0',
						 "on_author_page"	=> '1'
						);
			$last_postid = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
			}
			foreach($custom as $key=>$val)
			{				
				update_post_meta($last_postid, $key, $val);
			}
		}
		$user_phone = 'user_phone';
		$user_phonepostid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $user_phone . "'" );
		if(!$user_phonepostid)
		{
			/* website url custom field */
			$my_post = array();
			$my_post['post_title']  = 'Phone';
			$my_post['post_name']   = 'user_phone';
			$my_post['post_content']= '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type']   = 'custom_user_field';
			$custom = array("ctype"		     => 'text',
						 "sort_order" 		=> '5',
						 "on_registration"	=> '0',
						 "on_profile"		=> '1',
						 "option_values"	=> '',
						 "is_require"		=> '0',
						 "on_author_page"	=> '1'
						);
			$last_postid = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
			}
			foreach($custom as $key=>$val)
			{				
				update_post_meta($last_postid, $key, $val);
			}
		}
		
		$facebook = 'facebook';
		$facebookpostid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $facebook . "'" );
		if(!$facebookpostid)
		{
			/* Facebook url custom field */
			$my_post = array();
			$my_post['post_title']  = 'Facebook';
			$my_post['post_name']   = 'facebook';
			$my_post['post_content']= '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type']   = 'custom_user_field';
			$custom = array("ctype"		     => 'text',
						 "sort_order" 		=> '6',
						 "on_registration"	=> '0',
						 "on_profile"		=> '1',
						 "option_values"	=> '',
						 "is_require"		=> '0',
						 "on_author_page"	=> '1'
						);
			$last_postid = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
			}
			foreach($custom as $key=>$val)
			{				
				update_post_meta($last_postid, $key, $val);
			}
		}
		
		$twitter = 'twitter';
		$twitterpostid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $twitter . "'" );
		if(!$twitterpostid)
		{
			/* Twitter url custom field */
			$my_post = array();
			$my_post['post_title']  = 'Twitter';
			$my_post['post_name']   = 'twitter';
			$my_post['post_content']= '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type']   = 'custom_user_field';
			$custom = array("ctype"		     => 'text',
						 "sort_order" 		=> '7',
						 "on_registration"	=> '0',
						 "on_profile"		=> '1',
						 "option_values"	=> '',
						 "is_require"		=> '0',
						 "on_author_page"	=> '1'
						);
			$last_postid = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
			}
			foreach($custom as $key=>$val)
			{				
				update_post_meta($last_postid, $key, $val);
			}
		}
		$linkedin = 'linkedin';
		$linkedinpostid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $linkedin . "'" );
		if(!$linkedinpostid)
		{	
			/* Linkedin url custom field */
			$my_post = array();
			$my_post['post_title']  = 'LinkedIn';
			$my_post['post_name']   = 'linkedin';
			$my_post['post_content']= '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type']   = 'custom_user_field';
			$custom = array("ctype"		     => 'text',
						 "sort_order" 		=> '8',
						 "on_registration"	=> '0',
						 "on_profile"		=> '1',
						 "option_values"	=> '',
						 "is_require"		=> '0',
						 "on_author_page"	=> '1'
						);
			$last_postid = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
			}
			foreach($custom as $key=>$val)
			{				
				update_post_meta($last_postid, $key, $val);
			}
		}
		$description = 'description';
		$descriptionpostid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $description . "'" );
		if(!$descriptionpostid)
		{
			/* Author Biography custom field */
			$my_post = array();
			$my_post['post_title']  = 'Author Biography';
			$my_post['post_name']   = 'description';
			$my_post['post_content']= '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type']   = 'custom_user_field';
			$custom = array("ctype"		     => 'texteditor',
						 "sort_order" 		=> '9',
						 "on_registration"	=> '0',
						 "on_profile"		=> '1',
						 "option_values"	=> '',
						 "is_require"		=> '0',
						 "on_author_page"	=> '1'
						);
			$last_postid = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
			}
			foreach($custom as $key=>$val)
			{				
				update_post_meta($last_postid, $key, $val);
			}
		}
		$profile_photo = 'profile_photo';
		$profile_photopostid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $profile_photo . "'" );
		if(!$profile_photopostid)
		{	
			/* Linkedin url custom field */
			$my_post = array();
			$my_post['post_title']  = 'Profile Photo';
			$my_post['post_name']   = 'profile_photo';
			$my_post['post_content']= '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type']   = 'custom_user_field';
			$custom = array("ctype"		     => 'upload',
						 "sort_order" 		=> '10',
						 "on_registration"	=> '0',
						 "on_profile"		=> '1',
						 "option_values"	=> '',
						 "is_require"		=> '0',
						 "on_author_page"	=> '1'
						);
			$last_postid = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
			}
			foreach($custom as $key=>$val)
			{				
				update_post_meta($last_postid, $key, $val);
			}
		}
			global $wp_post_types;
			if ( isset( $wp_post_types[ 'custom_user_field' ] ) ) {
				unset( $wp_post_types[ 'custom_user_field' ] );
			}
		/*Register Module auto install page like login, register, profile*/
		add_action('admin_init','register_module_insert_page',100);		
		/*end Register Module auto install page like login, register, profile*/
	}
}
if(is_active_addons('templatic-login')){
	add_action('templ_add_admin_menu_', 'templ_add_subadmin_menu',12);
	
	if(file_exists(TEMPL_REGISTRATION_FOLDER_PATH . 'registration_functions.php'))
	{
		/* Registeration module related constant variable */
		define(TT_CUSTOM_USERMETA_FOLDER_PATH, TEMPL_REGISTRATION_FOLDER_PATH.'custom_usermeta/');		
		define('NEW_PW_TEXT',__('New Password',DOMAIN));
		define('CONFIRM_NEW_PW_TEXT',__('Confirm New Password',DOMAIN));
		define('EDIT_PROFILE_UPDATE_BUTTON',__('Update',DOMAIN));
		define('GET_NEW_PW_TEXT',__('Get New Password',DOMAIN));
		define('ABOUT_TEXT',__('About you',DOMAIN));
		define('YR_WEBSITE_TEXT',__('Your Website',DOMAIN));
		define('ABOUT_U_TEXT',__('Provide brief information about yourself',DOMAIN));
		
		/**--below are the main file which will work with registration -**/
		include_once(TEMPL_REGISTRATION_FOLDER_PATH . 'registration_functions.php');		
		include_once(TEMPL_REGISTRATION_FOLDER_PATH . 'login_box_widget.php');
		include_once(TEMPL_REGISTRATION_FOLDER_PATH . 'shortcodes_registration.php');
	}
	
	add_action( 'after_setup_theme', 'theme_login_setup',11 );
	add_action('login_form','sfc_register_add_login_button');
	add_action('wp_head','tmpl_reg_js',12);
	add_filter('templatic_general_settings_tab', 'registration_email_setting',13);
	add_action('templatic_general_data','registration_email_setting_data',11);
	add_action('templatic_general_data','legends_email_setting_data',15);
	add_action('templatic_general_setting_data','templatic_general_setting_register_data',11);
	
}

/*
name:templ_add_subadmin_menu
description: coading to add submenu under main menu--**/
	
function templ_add_subadmin_menu()
{ 
	$menu_title1 = __('Profile Fields Setup',ADMINDOMAIN);
	$hook = add_submenu_page('templatic_system_menu', $menu_title1,$menu_title1, 'administrator', 'user_custom_fields', 'my_user_plugin_function');
	add_action( "load-$hook", 'add_screen_options_user_custom_fields' ); /* CALL A FUNCTION TO ADD SCREEN OPTIONS */	
}

/*
 * Function Name: add_screen_options_user_custom_fields
 * return: display the screen option in profile fields setip page
 */
function add_screen_options_user_custom_fields()
{
	$option = 'per_page';
	$args = array('label'   => 'User custom fields',
			    'default' => 10,
		         'option'  => 'user_custom_fields_per_page'
			);
	add_screen_option( $option, $args ); /* ADD SCREEN OPTION */
}

function theme_login_setup(){
	add_filter('wp_nav_menu_items', 'filter_my_theme_nav_bars', 10, 2);
}

function filter_my_theme_nav_bars($items, $args) {
	global $current_user;	
	//login url
	$login_url=get_tevolution_login_permalink();	
	//register url	
	$register_url=get_tevolution_register_permalink();
	
	/*Primary Menu location */
	/* Check the condition for theme menu location promart, footer andsecondory */
	if($args->theme_location == 'primary' || $args->theme_location == 'footer' || $args->theme_location == 'secondory')
	{
		if($current_user->ID){
			$loginlink = '<li class="home' . ((is_home())? ' ' : '') . '"><a href="' .wp_logout_url(home_url()). '">' . __('Log out',DOMAIN) . '</a></li>'; 
		}else{
			$loginlink = '<li class="home' . (($_REQUEST['ptype']=='login')? ' current_page_item' : '') . '"><a href="' .$login_url . '">' . __('Login',DOMAIN) . '</a></li>'; 
		}
		if($current_user->ID){
			$reglink = '<li class="home' . ((is_home())? ' ' : '') . '"><a href="' . get_author_posts_url($current_user->ID) . '">' . $current_user->display_name . '</a></li>'; 
		}else{
			$users_can_register = get_option('users_can_register');
			if($users_can_register){				
				$reglink = '<li class="home' . (($_REQUEST['ptype']=='register')? ' current_page_item' : '') . '"><a href="' .$register_url . '">' . __('Register',DOMAIN) . '</a></li>';
			}
		}
		$items = $items. $loginlink.$reglink ;
	} 		
    return $items;
}

/*
name : my_user_plugin_function
description :Function to insert file for add/edit/delete options for custom fields BOF */
function my_user_plugin_function(){
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'addnew'){
		include (TEMPL_REGISTRATION_FOLDER_PATH . "admin_custom_usermeta_edit.php");
	}else{
		include (TEMPL_REGISTRATION_FOLDER_PATH . "admin_custom_usermeta_list.php");
	}
}
/**-- Function to insert file for add/edit/delete options for custom fields EOF --**/

function sfc_register_add_login_button() {
	if(isset($_REQUEST['ptype']) && $_REQUEST['ptype']!=''){		
		echo '<p><fb:login-button v="2" registration-url="'.site_url('wp-login.php?action=register', 'login').'" scope="email,user_website" onlogin="window.location.reload();" /></p>';
	}
}

function tmpl_reg_js(){
	global $wp_query,$post;
	// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object	
    if(!empty($post)):
		$is_tevolution_submit_form = get_post_meta( $post->ID, 'is_tevolution_submit_form', TRUE );
		else:
		$is_tevolution_submit_form='';
	endif;
	/* include only for pages and registration page */
	$login_page_id=get_option('tevolution_login');
	$register_page_id=get_option('tevolution_register');
	$profile_page_id=get_option('tevolution_profile');
	
	if(!empty($post) && @$post->ID == $login_page_id || @$post->ID == $register_page_id || @$post->ID == $profile_page_id || @$is_tevolution_submit_form==1){
		include_once(TEMPL_REGISTRATION_FOLDER_PATH . 'registration_js.php');
	}
}
	
/*
 * Add Filter for create the general setting sub tab for email setting
 */	
function registration_email_setting($tabs ) {			
	$tabs['email']=__('Email Settings',ADMINDOMAIN);
	return $tabs;
}	
/*
 * Create email setting data action
 */
function registration_email_setting_data($column)
{
	$tmpdata = get_option('templatic_settings');		
	switch($column)
	{
		case 'email':	
			?>
			<tr class="registration-email alternate">
				<td><label class="form-textfield-label"><?php echo __('After registration email',ADMINDOMAIN); ?></label></td>
			   
				<td>
					<a href="javascript:void(0);" onclick="open_quick_edit('registration-email','edit-registration-email')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a> 
					| 
					<a href="javascript:void(0);" onclick="reset_to_default('registration_success_email_subject','registration_success_email_content','registration-email');"><?php echo __("Reset",ADMINDOMAIN);?></a>
					<span class="spinner" style="margin:2px 18px 0;"></span>
					<span class="qucik_reset"><?php echo __("Data reset",DOMAIN);?></span>
				</td>
			</tr>
			<tr class="edit-registration-email alternate" style="display:none">
				<td width="100%" colspan="3">
					<h4 class="edit-sub-title">Quick Edit</h4>
					<table width="98%" align="left" class="tab-sub-table">
						<tr>
							<td style="line-height:10px">
								<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
							</td>
							<td width="90%" style="line-height:10px">
								<input type="text" name="registration_success_email_subject" id="registration_success_email_subject" value="<?php if(isset($tmpdata['registration_success_email_subject'])){echo $tmpdata['registration_success_email_subject'];}else{echo 'Thank you for registering!'; } ?>"/>
							</td>
						</tr>
						<tr>
							<td style="line-height:10px">
								<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
							</td>
							<td width="90%" style="line-height:10px">
								<?php
								$settings =   array(
												'wpautop' => false, // use wpautop?
												'media_buttons' => false, // show insert/upload button(s)
												'textarea_name' => 'registration_success_email_content', // set the textarea name to something different, square brackets [] can be used here
												'textarea_rows' => '7', // rows="..."
												'tabindex' => '',
												'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
												'editor_class' => '', // add extra class(es) to the editor textarea
												'teeny' => true, // output the minimal editor config used in Press This
												'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
												'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
												'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
											);	
								if($tmpdata['registration_success_email_content'] != ""){
									$content = stripslashes($tmpdata['registration_success_email_content']);
								}else{
									$content = '<p>Dear [#user_name#],</p><p>Thank you for registering and welcome to [#site_name#]. You can proceed with logging in to your account.</p><p>Login here: [#site_login_url_link#]</p><p>Username: [#user_login#]</p><p>Password: [#user_password#]</p><p>Feel free to change the password after you login for the first time.</p><p>&nbsp;</p><p>Thanks again for signing up at [#site_name#]</p>';
								}
								wp_editor( $content, 'registration_success_email_content', $settings);
							?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="buttons">
									<div class="inline_update">
									<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
									<a class="button-secondary cancel alignright " href="javascript:void(0);" onclick="open_quick_edit('edit-registration-email','registration-email')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
									<span class="save_error" style="display:none"></span><span class="spinner"></span>
									</div>
								</div>	
							</td>
						</tr>
					</table>
				</td>
			</tr>
   <?php
		break;		
	}
}
/*
 * Create email setting data action
 */	
function legends_email_setting_data($column)
{
	$tmpdata = get_option('templatic_settings');		
	switch($column)
	{
		case 'email':	
			echo '<div>'.templatic_legend_notification().'</div>';
			break;
	}
}




function templatic_general_setting_register_data($column){
	
	$tmpdata = get_option('templatic_settings');
	$logion_id=get_option('tevolution_login');
	if(@$logion_id!=@$tmpdata['tevolution_login'])
	{
		update_option('tevolution_login',$tmpdata['tevolution_login']);	
	}
	//register page
	$register_id=get_option('tevolution_register');
	if(@$register_id!=@$tmpdata['tevolution_register'])
	{
		update_option('tevolution_register',$tmpdata['tevolution_register']);	
	}
	//profile page
	$profile_id=get_option('tevolution_profile');
	if(@$profile_id!=@$tmpdata['tevolution_profile'])
	{
		update_option('tevolution_profile',$tmpdata['tevolution_profile']);	
	}
	?>
		<tr id="registration_page_setup">
			<th colspan="2"><div class="tevo_sub_title"><?php echo __('Registration options',ADMINDOMAIN);?></div> <p class="tevolution_desc"><?php echo sprintf(__('Match your Login, Register and Profile pages below to ensure registration works correctly. These pages were created automatically when Tevolution was activated. If you need to create them manually please open the %s',ADMINDOMAIN),'<a href="http://templatic.com/docs/tevolution-guide/#registration" target= "_blank"> documentation guide</a>')?></p></th>
		</tr>
		 <tr>
			<th><label><?php echo __('Allow user to auto login after registration',ADMINDOMAIN);  ?></label></th>
			<td>
			   <div class="input_wrap"><label for="allow_autologin_after_reg"><input type="checkbox" id="allow_autologin_after_reg" name="allow_autologin_after_reg" value="1" <?php if(isset($tmpdata['allow_autologin_after_reg']) && $tmpdata['allow_autologin_after_reg']==1){?>checked="checked"<?php }?> />&nbsp;<?php echo __('Enable',ADMINDOMAIN);?></label></div>
				<p class="description"><?php echo __('Enabling this option will automatically show the user status as logged in after registering on your site.',ADMINDOMAIN); ?></p>
			</td>
		 </tr>  
		 <tr>
			<th><label><?php echo __('Login Page',ADMINDOMAIN);?></label></th>
			<td>
				<?php $pages = get_pages();?>
				<select id="tevolution_login" name="tevolution_login">
					<?php
					if($pages) :
					$select_page=$tmpdata['tevolution_login'];
						foreach ( $pages as $page ) {
							$selected=($select_page==$page->ID)?'selected="selected"':'';
							$option = '<option value="' . $page->ID . '" ' . $selected . '>';
							$option .= $page->post_title;
							$option .= '</option>';
							echo $option;
						}
					else :
						echo '<option>' . __('No pages found', ADMINDOMAIN) . '</option>';
					endif;
					?>
				</select> 
				<div style="display:none" id="tevolution_login_page" class="description act_success  tevolution_highlight"><?php echo __('Copy this shortcode and paste it in the editor of your selected page to make it work correctly.<br> Shortcode - [tevolution_login] (including square braces)', ADMINDOMAIN); ?></div>
			</td>
		 </tr>
		  <tr>
			<th><label><?php echo __('Register Page',ADMINDOMAIN);?></label></th>
			<td>
				<?php $pages = get_pages();?>
				<select id="tevolution_register" name="tevolution_register">
					<?php
					if($pages) :
					$select_page=$tmpdata['tevolution_register'];
						foreach ( $pages as $page ) {
							$selected=($select_page==$page->ID)?'selected="selected"':'';
							$option = '<option value="' . $page->ID . '" ' . $selected . '>';
							$option .= $page->post_title;
							$option .= '</option>';
							echo $option;
						}
					else :
						echo '<option>' . __('No pages found', ADMINDOMAIN) . '</option>';
					endif;
					?>
				</select> <div style="display:none" id="tevolution_register_page" class="description act_success  tevolution_highlight"><?php echo __('Copy this shortcode and paste it in the editor of your selected page to make it work correctly.<br> Shortcode - [tevolution_register] (including square braces)',ADMINDOMAIN); ?></div>
			</td>
		 </tr>
		  <tr>
			<th><label><?php echo __('Profile Page',ADMINDOMAIN);?></label></th>
			<td>
				<?php $pages = get_pages();?>
				<select id="tevolution_profile" name="tevolution_profile">
					<?php
					if($pages) :
					$select_page=$tmpdata['tevolution_profile'];
						foreach ( $pages as $page ) {
							$selected=($select_page==$page->ID)?'selected="selected"':'';
							$option = '<option value="' . $page->ID . '" ' . $selected . '>';
							$option .= $page->post_title;
							$option .= '</option>';
							echo $option;
						}
					else :
						echo '<option>' . __('No pages found', ADMINDOMAIN) . '</option>';
					endif;
					?>
				</select> <div style="display:none" id="tevolution_profile_page" class="description act_success tevolution_highlight"><?php echo __('Copy this shortcode and paste it in the editor of your selected page to make it work correctly.<br> Shortcode - [tevolution_profile] (including square braces)',ADMINDOMAIN); ?></div>
			</td>
		 </tr>
	<?php			
}
	

/*
 * Function Name: register_module_insert_page
 * Return: create login. register and profile shortcode page
 */

function register_module_insert_page()
{ 
	global $wpdb;
	/*Tevolution login page */
	$templatic_settings=get_option('templatic_settings');
	
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
}
?>
