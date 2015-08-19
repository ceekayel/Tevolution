<?php
/*
	Create user custom fields while registration module activate.
*/
add_action('admin_init','create_default_registration_customfields');
function create_default_registration_customfields()
{
	global $wpdb,$pagenow;
	if((@$_REQUEST['activated'] == 'templatic-login' && @$_REQUEST['true']==1) || (@$_REQUEST['page'] == 'templatic_system_menu' && @$_REQUEST['activated']=='true') || $pagenow=='plugins.php' || $pagenow=='themes.php'){
		
		/* insert two fields of user name and email while activation this meta box */
		global $current_user,$wpdb;
		$postname = 'user_email';
		$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $postname . "'" );
		if(!$postid)
		{
			$args = array(
			  'public' => true,
			  'label'  => 'User Fields'
			);
   			 register_post_type( 'custom_user_field', $args );
			$tmpdata = get_option('templatic_settings');
			$tmpdata['allow_autologin_after_reg'] = 'No';
			update_option('templatic_settings',$tmpdata);
		
			$my_post = array();
			$my_post['post_title'] = 'Email';
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
			$my_post['post_title']  = 'Username';
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
			/* Linked in url custom field */
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

/* Email Settings */
add_action('templatic_general_data_email','registration_email_setting_data',11);
add_action('templatic_general_data_email','legends_email_setting_data',15);
add_action('templatic_general_setting_data','templatic_general_setting_register_data',11);

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
	<td><label class="form-textfield-label"><?php echo __('Registration email to user',ADMINDOMAIN); ?></label></td>

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
					$settings = array(
						'wpautop' => false, /* use wpautop?*/
						'media_buttons' => false, /* show insert/upload button(s)*/
						'textarea_name' => 'registration_success_email_content', /* set the textarea name to something different, square brackets [] can be used here*/
						'textarea_rows' => '7', /* rows="..."*/
						'tabindex' => '',
						'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', /* intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".*/
						'editor_class' => '', /* add extra class(es) to the editor textarea*/
						'teeny' => true, /* output the minimal editor config used in Press This*/
						'dfw' => true, /* replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)*/
						'tinymce' => true, /* load TinyMCE, can be used to pass settings directly to TinyMCE using an array()*/
						'quicktags' => true /* load Quicktags, can be used to pass settings directly to Quicktags using an array()*/
					);	
					if($tmpdata['registration_success_email_content'] != ""){
						$content = stripslashes($tmpdata['registration_success_email_content']);
					}else{
						$content = __('<p>Dear [#user_name#],</p><p>Thank you for registering and welcome to [#site_name#]. You can proceed with logging in to your account.</p><p>Login here: [#site_login_url_link#]</p><p>Username: [#user_login#]</p><p>Password: [#user_password#]</p><p>Feel free to change the password after you login for the first time.</p><p>&nbsp;</p><p>Thanks again for signing up at [#site_name#]</p>',DOMAIN);
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
	<tr class="admin-registration-email">
	<td><label class="form-textfield-label"><?php echo __('Registration email to admin',ADMINDOMAIN); ?></label></td>

	<td>
		<a href="javascript:void(0);" onclick="open_quick_edit('admin-registration-email','edit-admin-registration-email')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a> 
		| 
		<a href="javascript:void(0);" onclick="reset_to_default('admin_registration_success_email_subject','admin_registration_success_email_content','admin-registration-email');"><?php echo __("Reset",ADMINDOMAIN);?></a>
		<span class="spinner" style="margin:2px 18px 0;"></span>
		<span class="qucik_reset"><?php echo __("Data reset",DOMAIN);?></span>
	</td>
	</tr>
	<tr class="edit-admin-registration-email" style="display:none">
	<td width="100%" colspan="3">
		<h4 class="edit-sub-title">Quick Edit</h4>
		<table width="98%" align="left" class="tab-sub-table">
			<tr>
				<td style="line-height:10px">
					<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
				</td>
				<td width="90%" style="line-height:10px">
					<input type="text" name="admin_registration_success_email_subject" id="admin_registration_success_email_subject" value="<?php if(isset($tmpdata['admin_registration_success_email_subject'])){echo $tmpdata['admin_registration_success_email_subject'];}else{echo 'New user registration'; } ?>"/>
				</td>
			</tr>
			<tr>
				<td style="line-height:10px">
					<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
				</td>
				<td width="90%" style="line-height:10px">
					<?php
					$settings =   array(
									'wpautop' => false, 
									'media_buttons' => false, 
									'textarea_name' => 'admin_registration_success_email_content', 
									'textarea_rows' => '7', 
									'tabindex' => '',
									'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', 
									'editor_class' => '',
									'teeny' => true,
									'dfw' => true,
									'tinymce' => true,
									'quicktags' => true
								);	
					if($tmpdata['admin_registration_success_email_content'] != ""){
						$content = stripslashes($tmpdata['admin_registration_success_email_content']);
					}else{
						$content = '<p>Dear admin,</p><p>A new user has registered on your site [#site_name#].</p><p>Login Credentials: [#site_login_url_link#]</p><p>Username: [#user_login#]</p><p>Password: [#user_password#]</p>';
					}
					wp_editor( $content, 'admin_registration_success_email_content', $settings);
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="buttons">
						<div class="inline_update">
						<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
						<a class="button-secondary cancel alignright " href="javascript:void(0);" onclick="open_quick_edit('edit-admin-registration-email','admin-registration-email')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
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
			echo '<div id="legend_notifications">'.templatic_legend_notification().'</div>';
		break;
	}
}
/*
	Create login. register and profile short code page
 */

function register_module_insert_page()
{ 
	global $wpdb;
	/* Tevolution login page */
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
	/* Tevolution Register Page */
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
	/* Tevolution Register Page */
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
/* Registration form settings */

function templatic_general_setting_register_data($column){
	
	$tmpdata = get_option('templatic_settings');
	$logion_id=get_option('tevolution_login');
	if(@$logion_id!=@$tmpdata['tevolution_login'])
	{
		update_option('tevolution_login',$tmpdata['tevolution_login']);	
	}
	
	$register_id=get_option('tevolution_register');
	if(@$register_id!=@$tmpdata['tevolution_register'])
	{
		update_option('tevolution_register',$tmpdata['tevolution_register']);	
	}
	
	$profile_id=get_option('tevolution_profile');
	if(@$profile_id!=@$tmpdata['tevolution_profile'])
	{
		update_option('tevolution_profile',$tmpdata['tevolution_profile']);	
	}
	?>
		<table id="registration_page_setup" class="tmpl-general-settings form-table">
		<tr>
			<td colspan="2"><p class="tevolution_desc"><?php echo sprintf(__('Match your Login, Register and Profile pages below to ensure registration works correctly. These pages were created automatically when Tevolution was activated. If you need to create them manually please open the %s',ADMINDOMAIN),'<a href="http://templatic.com/docs/tevolution-guide/#registration" target= "_blank"> documentation guide</a>')?></p></td>
		</tr>
		<tr>
		<th><label><?php echo __('Allow user to auto login after registration',ADMINDOMAIN);  ?></label></th>
		<td>
			<div class="input-switch"><input type="checkbox" id="allow_autologin_after_reg" name="allow_autologin_after_reg" value="1" <?php if(isset($tmpdata['allow_autologin_after_reg']) && @$tmpdata['allow_autologin_after_reg']==1){?>checked="checked"<?php }?> />
			<label for="allow_autologin_after_reg">&nbsp;<?php echo __('Enable',ADMINDOMAIN);?></label></div>
			<p class="description"><?php echo __('Enabling this option will automatically show the user status as logged in after registering on your site.',ADMINDOMAIN); ?></p>
		</td>
		</tr> 
		<!-- HTML for social loggin -->
		<tr>
		<th><label><?php echo __('Allow user to login from social sites',ADMINDOMAIN);  ?></label></th>
		<td>
			<div class="input_wrap"><label for="allow_facebook_login"><input type="checkbox" id="allow_facebook_login" name="allow_facebook_login" value="1" onclick="return show_social_login(this.id);" <?php if(isset($tmpdata['allow_facebook_login']) && @$tmpdata['allow_facebook_login']==1){?>checked="checked"<?php }?> />&nbsp;<?php echo __('Facebook',ADMINDOMAIN);?></label></div>
			<div id="show_facebook_key" <?php if((!isset($tmpdata['allow_facebook_login']) && @$tmpdata['allow_facebook_login']!=1) || @$tmpdata['allow_facebook_login'] == ''){ ?> style="display:none;" <?php } ?>>
				<?php echo __('App ID',ADMINDOMAIN); ?>
				<input type="text" name="facebook_key" id="facebook_key" placeholder="<?php echo __('Your Facbook App ID here',ADMINDOMAIN); ?>" value="<?php if(isset($tmpdata['facebook_key'])){echo @$tmpdata['facebook_key'];} ?>"/>
				<?php echo __('Secret Key',ADMINDOMAIN); ?>
				<input type="text" name="facebook_secret_key" id="facebook_secret_key" placeholder="<?php echo __('Your Facbook Secret Key here',ADMINDOMAIN); ?>" value="<?php if(isset($tmpdata['facebook_secret_key'])){echo $tmpdata['facebook_secret_key'];} ?>"/>
				<p class="description"><?php echo __('You can create the facebook key from',ADMINDOMAIN);  ?><a href="https://developers.facebook.com/apps"><?php echo __(' here',ADMINDOMAIN); ?></a></p>
			</div>
			<div class="input_wrap"><label for="allow_google_login"><input type="checkbox" id="allow_google_login" name="allow_google_login" value="1" onclick="return show_social_login(this.id);" <?php if(isset($tmpdata['allow_google_login']) && @$tmpdata['allow_google_login']==1){?>checked="checked"<?php }?> />&nbsp;<?php echo __('Google',ADMINDOMAIN);?></label></div>
			<div id="show_google_key" <?php if((!isset($tmpdata['allow_google_login']) && @$tmpdata['allow_google_login']!=1 ) || @$tmpdata['allow_google_login'] == ''){ ?> style="display:none;" <?php } ?>>
				<?php echo __('App ID',ADMINDOMAIN); ?>
				<input type="text" name="google_key" id="google_key" placeholder="<?php echo __('Your google App ID here',ADMINDOMAIN); ?>" value="<?php if(isset($tmpdata['google_key'])){	echo $tmpdata['google_key'];} ?>"/>
				<?php echo __('Secret Key',ADMINDOMAIN); ?>
				<input type="text" name="google_secret_key" id="google_secret_key" placeholder="<?php echo __('Your Google Secret Key here',ADMINDOMAIN); ?>" value="<?php if(isset($tmpdata['google_secret_key'])){echo $tmpdata['google_secret_key'];} ?>"/>
				<p class="description"><?php echo __('You can create the google key from',ADMINDOMAIN);  ?><a href="https://code.google.com/apis/console/"><?php echo __(' here',ADMINDOMAIN); ?></a></p>
			</div>
			<div class="input_wrap"><label for="allow_twitter_login"><input type="checkbox" id="allow_twitter_login" name="allow_twitter_login" value="1" onclick="return show_social_login(this.id);" <?php if(isset($tmpdata['allow_twitter_login']) && $tmpdata['allow_twitter_login']==1){?>checked="checked"<?php }?> />&nbsp;<?php echo __('Twitter',ADMINDOMAIN);?></label></div>
			<div id="show_twitter_key"  <?php if((!isset($tmpdata['allow_twitter_login']) && @$tmpdata['allow_twitter_login']!=1) || @$tmpdata['allow_twitter_login'] == ''){ ?> style="display:none;" <?php } ?>>
				<?php echo __('App ID',ADMINDOMAIN); ?>
				<input type="text" placeholder="<?php echo __('Your Twitter App ID here',ADMINDOMAIN); ?>" name="twitter_key" id="twitter_key" value="<?php if(isset($tmpdata['twitter_key'])){echo $tmpdata['twitter_key'];} ?>"/>
				<?php echo __('Secret Key',ADMINDOMAIN); ?>
				<input type="text" name="twitter_secret_key" placeholder="<?php echo __('Your Twitter Secret Key here',ADMINDOMAIN); ?>" id="twitter_secret_key" value="<?php if(isset($tmpdata['twitter_secret_key'])){echo $tmpdata['twitter_secret_key'];} ?>"/>
				<p class="description"><?php echo __('You can create the twitter key from',ADMINDOMAIN);  ?><a href="https://dev.twitter.com/apps/"><?php echo __(' here',ADMINDOMAIN); ?></a></p>
			</div>
		</td>
		</tr>  
		<tr>
		<th><label><?php echo __('Login Page',ADMINDOMAIN);?></label></th>
		<td>
			<?php $pages = get_pages();
                                                                                        $select_page=$tmpdata['tevolution_login'];
                                                                                          if( $select_page !=''){
                                                                                                    $page = get_page($select_page);
                                                                                                    if(has_shortcode($page->post_content, 'tevolution_login')){
                                                                                                              $sp = 1;
                                                                                                    }else{
                                                                                                              $sp = 0;
                                                                                                    }
                                                                                          }
                                                                      ?>
			<input type="hidden" id="input_tevolution_login" name="input_tevolution_login" value="<?php echo $sp;  ?>">
			<select id="tevolution_login" name="tevolution_login">
				<?php
				if($pages) :
				
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
			<p style="display:none" id="tevolution_login_page" class="description act_success"><?php echo __('Copy this shortcode and paste it in the editor of your selected page to make it work correctly.<br> Shortcode - [tevolution_login] (including square braces)', ADMINDOMAIN); ?></p>
		</td>
		</tr>
		<tr>
		<th><label><?php echo __('Register Page',ADMINDOMAIN);?></label></th>
		<td>
			<?php 
                                                                                          $pages = get_pages();
                                                                                          $select_page=$tmpdata['tevolution_register'];
                                                                                          if( $select_page !=''){
                                                                                                    $page = get_page($select_page);
                                                                                                    if(has_shortcode($page->post_content, 'tevolution_register')){
                                                                                                              $sp = 1;
                                                                                                    }else{
                                                                                                              $sp = 0;
                                                                                                    }
                                                                                          }
                                                                                ?>
                                                                           <input type="hidden" id="input_tevolution_register" name="input_tevolution_register" value="<?php echo $sp; ?>">
			<select id="tevolution_register" name="tevolution_register">
				<?php
				if($pages) :
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
                                                                           <p style="display:none" id="tevolution_register_page" class="description act_success"><?php echo __('Copy this shortcode and paste it in the editor of your selected page to make it work correctly.<br> Shortcode - [tevolution_register] (including square braces)',ADMINDOMAIN); ?></p>
		</td>
		</tr>
		<tr>
		<th><label><?php echo __('Profile Page',ADMINDOMAIN);?></label></th>
		<td>
			<?php $pages = get_pages();
                                                                                          $select_page=$tmpdata['tevolution_profile'];
                                                                                          if( $select_page !=''){
                                                                                                    $page = get_page($select_page);
                                                                                                    if(has_shortcode($page->post_content, 'tevolution_profile')){
                                                                                                              $sp = 1;
                                                                                                    }else{
                                                                                                              $sp = 0;
                                                                                                    }
                                                                                          }
                                                                                ?>
                                                                           <input type="hidden" id="input_tevolution_profile" name="input_tevolution_profile" value="<?php echo $sp; ?>">
			<select id="tevolution_profile" name="tevolution_profile">
				<?php
				if($pages) :
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
                                                                           <p style="display:none" id="tevolution_profile_page" class="description act_success"><?php echo __('Copy this shortcode and paste it in the editor of your selected page to make it work correctly.<br> Shortcode - [tevolution_profile] (including square braces)',ADMINDOMAIN); ?></p>
		</td>
		</tr>
		<tr>
			<td colspan="2">
			<p class="submit" style="clear: both;">
			<input type="submit" name="Submit"  class="button button-primary button-hero" value="<?php echo __('Save All Settings',ADMINDOMAIN);?>" />
			<input type="hidden" name="settings-submit" value="Y" />
			</p>
		</td>
		</tr>
		</table>
	<?php
	/*	 function to hide show the html of social login. */	
	add_action('admin_footer','show_social_login');	
}

/* function to show particular social login key html */
function show_social_login()
{
	?>
	<script>function show_social_login(e){if(e=="allow_facebook_login"){jQuery("#show_facebook_key").toggle()}else if(e=="allow_google_login"){jQuery("#show_google_key").toggle()}else if(e=="allow_twitter_login"){jQuery("#show_twitter_key").toggle()}}</script><?php
}

/* 
this function will add user custom fields on dashboard 
*/

add_action('show_user_profile', 'add_extra_profile_fields'); /* CALL A FUNCTION */
function add_extra_profile_fields( $user )
{
	$user_id = $user->ID;
	fetch_user_registration_fields( 'profile',$user_id ); /* CALL A FUNCTION TO DISPLAY CUSTOM FIELDS */
}
add_action('edit_user_profile', 'add_extra_profile_fields');


/*
 this function will save custom field data displaying on profile page in backend 
*/
add_action('personal_options_update', 'update_extra_profile_fields'); /* CALL A FUNCTION */
/* update user data */
add_action( 'edit_user_profile_update', 'update_extra_profile_fields' ); 
function update_extra_profile_fields( $user_id )
{
	global $upload_folder_path;
		global $form_fields_usermeta;
		fetch_user_custom_fields();
	
		foreach($form_fields_usermeta as $fkey=>$fval)
		{
			$fldkey = "$fkey";
			$fldkey = $_POST["$fkey"];
			update_user_meta($user_id, $fkey, $fldkey); /* User Custom Metadata Here*/
		}
	
}
/* add  'multipart/form-data' in edit profile page backend*/
add_action('admin_footer','modify_form');
function modify_form(){
echo  '<script type="text/javascript">
		jQuery("#your-profile").attr("enctype", "multipart/form-data");
		</script>
  ';
}


/*
	Update user custom fields sorting options 
*/
add_action('wp_ajax_user_customfield_sort','tevolution_user_customfield_sort');
function tevolution_user_customfield_sort(){
	
	$user_id = get_current_user_id();		
	if(isset($_REQUEST['paging_input']) && $_REQUEST['paging_input']!=0 && $_REQUEST['paging_input']!=1){
		$taxonomy_per_page=get_user_meta($user_id,'taxonomy_per_page',true);
		$j =$_REQUEST['paging_input']*$taxonomy_per_page+1;
		$test='';
		$i=$taxonomy_per_page;		
		for($j; $j >= count($_REQUEST['user_field_sort']);$j--){			
			if($_REQUEST['user_field_sort'][$i]!=''){
				update_post_meta($_REQUEST['user_field_sort'][$i],'sort_order',$j);	
			}
			$i--;	
		}
	}else{
		$j=1;
		for($i=0;$i<count($_REQUEST['user_field_sort']);$i++){
			update_post_meta($_REQUEST['user_field_sort'][$i],'sort_order',$j);		
			$j++;
		}
	}	
	exit;
}


/*
	check user name and password while login from submit form.
*/
add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
function ajax_login(){
	header('Content-Type: application/json; charset=utf-8');
    /* First check the nonce, if it fails the function will break*/
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    /* Nonce is checked, get the POST data and sign user on*/
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon( $info, false );
    $package_selected = '';
    $package_type = '';
	$package_id=$_POST['pkg_id'];
	if($package_id!=''){
		$selected_package_type=get_post_meta($package_id,'package_type',true);
		$subscription_as_pay_post=get_post_meta($package_id,'subscription_as_pay_post',true);
		if($subscription_as_pay_post==1){
			$_SESSION['custom_fields']=$_POST;
		}
	}
    $package_selected = get_user_meta($user_signon->data->ID,'package_selected',true);
    $tmpdata = get_option('templatic_settings');
    if(@$package_selected)
    {
		$package_type = get_post_meta($package_selected,'package_type',true);
	}
	$username = ucfirst($user_signon->data->display_name);
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.',DOMAIN)));
    } else {
		echo json_encode(array('loggedin'=>true, 'message'=>sprintf(__('Welcome %s, submit your listing details.',DOMAIN),$username),'package_type'=>$package_type,'selected_package_type'=>$selected_package_type));
    }
    die();
}

?>