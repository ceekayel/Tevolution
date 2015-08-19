<?php
/*
 * Registration Module short codes
 *
 */
 
 /*
 * function to register user from social sites.
 */
 add_action('init','tmpl_social_login');
 function tmpl_social_login()
 {
	 $uri = array();

	if( isset( $_GET['route'] ) ){ 		
		$array_tmp_uri = preg_split('[\\/]', $_GET['route'], -1, PREG_SPLIT_NO_EMPTY);
		 $uri['controller'] = @ $array_tmp_uri[0];
		 $uri['method']     = @ $array_tmp_uri[1];
		 $uri['var']        = @ $array_tmp_uri[2];
	
	
	/*Session set if user login on submit form */
	if(isset($_POST['submit_post_type']) && $_POST['submit_post_type']!=''){
		$_SESSION['custom_fields']=$_POST;
	}

	$hybridauth_config = TT_REGISTRATION_FOLDER_PATH.'hybridauth/config.php';
	require_once( TT_REGISTRATION_FOLDER_PATH."hybridauth/Hybrid/Auth.php" );
	$hybridauth = new Hybrid_Auth( $hybridauth_config );

	/* try to authenticate the selected $provider */
	$provider =  $uri['var'];
	if($provider == 'twitter')
	{
		$_SESSION['twitter_login'] = 'twitter_login';
	}
	$adapter = $hybridauth->authenticate( $provider );
	$user_profile = $adapter->getUserProfile();	
	if($user_profile->email == '')
	{
		$user_id = username_exists(str_replace(" ","",$user_profile->firstName).str_replace(" ","",$user_profile->lastName));/*check user already exists or not*/
	}
	else
	{
		$user_id = email_exists($user_profile->email);/*check user already exists or not*/
	}
	$user_data = get_userdata ($user_id);
	/*if exists than logged him/her */
	if ( $user_id )
	{
		wp_clear_auth_cookie ();
		wp_set_auth_cookie ($user_data->ID, true);
		$user_data->user_login;
		do_action ('wp_login', $user_data->user_login, $user_data);
		
		if($user_data->ID!=''){
			$return_url=(isset($_REQUEST['redirect_id']) && $_REQUEST['redirect_id']!='') ? get_permalink($_REQUEST['redirect_id']) :  get_author_posts_url( $user_data->ID );
			$redirect_url= $return_url;
			wp_redirect($redirect_url);
			exit;
		}
	}
	else
	{
		/*Name Filter*/
		$user_full_name = apply_filters ('tmpl_social_login_filter_new_user_login', $user_profile->displayName);
		
		/*Image Filter*/
		$user_image = apply_filters ('tmpl_social_login_filter_new_user_image', $user_profile->photoURL);
		
		/*firstName Filter*/
		$user_first_name = apply_filters ('tmpl_social_login_filter_new_user_first_name', $user_profile->firstName);
		
		/*lastName Filter*/
		$user_last_name = apply_filters ('tmpl_social_login_filter_new_user_last_name', $user_profile->lastName);
		
		/*websiteUrl Filter*/
		$user_website = apply_filters ('tmpl_social_login_filter_new_user_website_url', $user_profile->webSiteURL);
		
		/*identifier Filter*/
		$user_identity_provider = apply_filters ('tmpl_social_login_filter_new_user_identifier', $user_profile->identifier);
		
		/*Email Filter*/
		$user_email = apply_filters ('tmpl_social_login_filter_new_user_email', $user_profile->email);

		$user_login = apply_filters ('tmpl_social_login_filter_new_user_firstName', str_replace(" ","",$user_profile->firstName)).apply_filters ('tmpl_social_login_filter_new_user_lastName',  str_replace(" ","",$user_profile->lastName));
		
		/*Setup the user's password*/
		$user_password = wp_generate_password ();
		$user_password = apply_filters ('tmpl_social_login_filter_new_user_password', $user_password);

		/*Setup the user's role*/
		$user_role = get_option ('default_role');
		$user_role = apply_filters ('tmpl_social_login_filter_new_user_role', $user_role);
		
		$creds = array();
		$creds['user_login'] = $user_email;
		$creds['user_password'] = $user_password;
		$creds['remember'] = true;
		/*Build user data*/
		$user_fields = array (
			'user_login' => $user_login,
			'display_name' => (!empty ($user_full_name) ? $user_full_name : $user_login),
			'user_email' => $user_email,
			'first_name' => $user_first_name,
			'last_name' => $user_last_name,
			'user_url' => $user_website,
			'user_pass' => $user_password,
			'role' => $user_role
		);

		/*Filter for user_data*/
		$user_fields = apply_filters ('tmpl_social_login_filter_new_user_fields', $user_fields);

		/*Hook before adding the user*/
		do_action ('tmpl_social_login_action_before_user_insert', $user_fields, $user_identity_provider);

		/* Create a new user */
		$user_id = wp_insert_user ($user_fields);
		
		if (is_numeric ($user_id) )
		{
			/* Save user meta-data */
			update_user_meta ($user_id, 'tmpl_social_login_user_token', $user_token);
			update_user_meta ($user_id, 'tmpl_social_login_identity_provider', $user_identity_provider);
			update_user_meta ($user_id, 'profile_photo', $user_image);
			$user_info = get_userdata($user_id);
			$user_login = $user_info->user_login;
			$user_pass = $user_password;
			$tmpdata = get_option('templatic_settings');
			$subject = stripslashes($tmpdata['registration_success_email_subject']);
			$client_message = stripslashes($tmpdata['registration_success_email_content']);
			$fromEmail = get_site_emailId_plugin();
			$fromEmailName = get_site_emailName_plugin();	
			$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
			if($subject=="" && $client_message=="")
			{
				$client_message = __('[SUBJECT-STR]Thank you for registering![SUBJECT-END]<p>Dear [#user_name#],</p><p>Thank you for registering and welcome to [#site_name#]. You can proceed with logging in to your account.</p><p>Login here: [#site_login_url_link#]</p><p>Username: [#user_login#]</p><p>Password: [#user_password#]</p><p>Feel free to change the password after you login for the first time.</p><p>&nbsp;</p><p>Thanks again for signing up at [#site_name#]</p>',DOMAIN);
				$filecontent_arr1 = explode('[SUBJECT-STR]',$client_message);
				$filecontent_arr2 = explode('[SUBJECT-END]',$filecontent_arr1[1]);
				$subject = $filecontent_arr2[0];
				if($subject == '')
				{
					$subject = __("Thank you for registering!",DOMAIN);
				}
				
				$client_message = $filecontent_arr2[1];
			}
			if(strstr(get_tevolution_login_permalink(),'?'))
			{
				$login_url_link=get_tevolution_login_permalink().'&akey='.$activation_key;
			}else{
				$login_url_link=get_tevolution_login_permalink().'?akey='.$activation_key;
			}
			
			$store_login_link = '<a href="'.$login_url_link.'">'.$login_url_link.'</a>';
			$store_login = sprintf(__('<a href="'.$login_url_link.'">'.'here'.'</a>',DOMAIN));
		
			/* customer email */
			$search_array = array('[#user_name#]','[#user_login#]','[#user_password#]','[#site_name#]','[#site_login_url#]','[#site_login_url_link#]');
			$replace_array = array($user_login,$user_login,$user_pass,$store_name,$store_login,$store_login_link);
			$client_message = str_replace($search_array,$replace_array,$client_message);
			templ_send_email($fromEmail,$fromEmailName,$user_email,$userName,$subject,$client_message,$extra='');
			
			$user_data = get_userdata ($user_id);
			if ($user_data !== false)
			{
				wp_clear_auth_cookie ();
				wp_set_auth_cookie ($user_data->ID, true);
				do_action ('wp_login', $user_data->user_login, $user_data);
				if($user_data->ID!=''){					
					$return_url=(isset($_REQUEST['redirect_id']) && $_REQUEST['redirect_id']!='') ? get_permalink($_REQUEST['redirect_id']) :  get_author_posts_url( $user_data->ID );
					$redirect_url= $return_url;
					wp_redirect($redirect_url);
					exit;
				}
			}		
		}
	}
	
	}
	else{
		$uri['controller'] = "home";
		$uri['method']     = "index"; 
		$uri['var']        = ""; 
	}
	
 }
 
/* 
	Display Login form 
*/

function tevolution_user_login($atts)
{
	extract( shortcode_atts( array (
			'form_name'   =>'loginform',				
			), $atts ) 
		);
		
	ob_start();
	
	$tmpdata = get_option('templatic_settings');
	remove_filter( 'the_content', 'wpautop' , 12);
	unset($_SESSION['redirect_to']);
	/* action call before login form */
	do_action('tevolution_before_login_from');
	/* add script for show hide forgot password box */
	
	if(is_user_logged_in()): 
			$user_id = get_current_user_id();
			wp_redirect(get_author_posts_url( $user_id ));
			exit;
	else:
		if(isset($_SESSION['update_password']) && $_SESSION['update_password']!='')
		{
			echo "<p class=\"success_msg\"> ".__('Password changed successfully. Please login with your new password.',DOMAIN)."</p>";
			unset($_SESSION['update_password']);
		}
	
		echo '<div class="login_form_l">';
		echo '<h3>'; _e('Sign In',DOMAIN); echo '</h3>';
		$flg=0;		
		if((isset($_POST['log']) && $_POST['log']!='') && (isset($_POST['pwd']) && $_POST['pwd']!='' ))
		{
			$log =  trim($_POST['log']);
			$pwd = trim($_POST['pwd']);
			$check = wp_authenticate($log,$pwd);
			
		
			$flg= ( is_wp_error($check)) ? '1' :'2';	
				
		}
		if((isset($_POST['log']) && $_POST['log']=='') || (isset($_POST['pwd']) && $_POST['pwd']=='' )){			
			$flg=1;
		}	
		
		$secure_cookie = '';
		/* If the user wants ssl but the session is not ssl, force a secure cookie.*/
		if ( !empty($_POST['log']) && !force_ssl_admin() ) 
		{
			$user_name = sanitize_user($_POST['log']);
			if ( $user = get_user_by('login',$user_name) )
			{		
				if ( get_user_option('use_ssl', $user->ID) ) 
				{
					$secure_cookie = true;
					force_ssl_admin(true);
				}
			}
		}
		
		if ( isset($_REQUEST['redirect_to'] )) {
			$redirect_to = $_REQUEST['redirect_to'];
			/* Redirect to https if user wants ssl*/
			if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
			$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
		} else {
			$redirect_to = admin_url();
		}
		
		if(isset($flg) && $flg==1)
		{
			echo '<p class="error_msg"> '.__(INVALID_USER_PW_MSG,DOMAIN).' </p>';
			/* while login is password is wrong than show the error message in pop up */
			$login_permalink	= get_tevolution_login_permalink();
			$register_permalink	= get_tevolution_register_permalink();
			$page_permalink		= get_permalink($post->ID);
			if($page_permalink != $login_permalink && $page_permalink !=$register_permalink ){
			?>
				<script type="text/javascript">
				jQuery(document).ready(function(){jQuery('#tmpl_reg_login_container').foundation('reveal', 'open')});
				jQuery("#tmpl_reg_login_container #tmpl_login_frm").show();
				jQuery("#tmpl_reg_login_container #tmpl_sign_up").hide();
				</script>
			<?php
			}
		}
		if(isset($flg) && $flg==2){		
			/* username and password correct then auto login with redirect author page*/
			$creds = array();
			$creds['user_login'] = $_POST['log'];
			$creds['user_password'] = $_POST['pwd'];
			$creds['remember'] = true;
			$user = wp_signon($creds, $secure_cookie);	
			$user = get_user_by('login',$_POST['log']);		
			if($user->ID!=''){
				$redirect_url=apply_filters('tevolution_login_redirect',get_author_posts_url( $user->ID ),$user);
				wp_redirect($redirect_url);
				exit;
			}
		}	
		
		/*Lost password action for retrive forget password */
		
			do_action('tmpl_forget_password_message',$form_name);		
		
		/*End lost password action for retrive forget password*/		
		$lang=(isset($_REQUEST['lang']) && $_REQUEST['lang']!="") ?'&lang='.$_REQUEST['lang'] : '';			
		?>
			<div class="login_form_box">
			
            	<?php 
				$tmpdata = get_option('templatic_settings');
				
				
				do_action('action_before_login_from');?>
				<form name="<?php  echo $form_name; ?>" id="<?php  echo $form_name; ?>" action="<?php echo get_permalink(); ?>" method="post" >
                <?php 
				
				echo do_action('show_meida_login_button',''); 
			
				/* if social media login is enable then show the separation login message */
				if((isset($tmpdata['allow_facebook_login']) && $tmpdata['allow_facebook_login']==1) || (isset($tmpdata['allow_google_login']) && $tmpdata['allow_google_login']==1) || isset($tmpdata['allow_twitter_login']) && $tmpdata['allow_twitter_login']==1){
					 echo "<p class='login_sep'>";
					 _e('OR use your account',DOMAIN);
					 echo "</p>";
				}	?>
					<input type="hidden" name="action" value="login" />                         
					<div class="form_row clearfix">
						<label><?php _e('Username',DOMAIN); ?> <span class="indicates">*</span> </label>
						<input type="text" name="log" id="user_login" value="<?php if(isset($user_login)){ echo esc_attr($user_login);} ?>" size="20" class="textfield" />
						<span id="user_loginInfo"></span> 
					</div>
					
					<div class="form_row clearfix">
						<label> <?php _e('Password',DOMAIN); ?> <span class="indicates">*</span> </label>
						<input type="password" name="pwd" id="user_pass" class="textfield" value="" size="20"  />
						<span id="user_passInfo"></span> 
					</div>
					<input type="hidden" name="redirect_to" value="<?php echo apply_filters('tevolution_login',$_SERVER['HTTP_REFERER'] );  ?>" />
					<input type="hidden" name="testcookie" value="1" />
					<div class="form_row rember clearfix">
					<label>
						<input name="rememberme" type="checkbox" id="rememberme" value="forever" class="fl" />
						<?php _e('Remember me on this computer',DOMAIN); ?> 
					</label>	
					
					 <!-- html to show social login -->
                    <a onclick="showhide_forgetpw('<?php  echo $form_name; ?>');" href="javascript:void(0)" class="lw_fpw_lnk"><?php _e('Forgot your password',DOMAIN);?>?</a> 
				    </div>
				 	
					<div class="form_row ">
				    <input class="b_signin_n" type="submit" value="<?php _e('Sign In',DOMAIN);?>"  name="submit" />		
					<p class="forgot_link">
					<?php 
						$register_page_id=get_option('tevolution_register');
						$login_page_id = get_option('tevolution_login');
						global $post;
						if(get_option('users_can_register') == 1 && $login_page_id != $post->ID)
						{
							_e('New User? ',DOMAIN);
							?>
								<a href="javascript:void(0)" class="logreg-link" id="tmpl-reg-link"><?php _e('Register Now',DOMAIN);?></a>
							<?php
						}
						?>			
					</p>
				    </div> 
					
					<?php do_action('login_form'); ?> 
							
				</form>
				<?php do_action('action_after_login_from');?>
				
				<?php do_shortcode('[frm_forgot_password submit_form="'.$form_name.'_forgot_pass"]'); ?>   
			</div>
			<!-- Enable social media(gigya plugin) if activated-->         
			<?php if(is_plugin_active('gigya-socialize-for-wordpress/gigya.php') && get_option('users_can_register')):          
					echo '<div id="componentDiv">';
					dynamic_sidebar('below_registration'); 
					echo '</div>';
				endif; 
				add_action('wp_footer','add_class_to_login_page');?>
			<!--End of plugin code-->
			
			<script  type="text/javascript" async >
				function showhide_forgetpw(form_id)
				{
					jQuery('form#'+form_id+' .lw_fpw_lnk').live('click', function(e){
						jQuery(this).closest('form#'+form_id).next().show();
						e.preventDefault();
						return false;
					});
				}
				
				function forget_email_validate(form_id){
					var email = jQuery('#'+form_id+' #user_login_email');
					var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					
					if(email.val()==''){
						jQuery('#'+form_id+' #forget_user_email_error').html("<?php _e('Please Enter E-mail',DOMAIN);?>");
						email.focus();
						return false;
					}else if (!filter.test(email.val())) {						
						jQuery('#'+form_id+' #forget_user_email_error').html("<?php _e('Please provide a valid email address',DOMAIN);?>");
						email.focus();
						return false;
					}else
					{
						jQuery('form#'+form_id+' .error_msg').remove();
						jQuery('form#'+form_id+' .success_msg').remove();
						jQuery('#'+form_id+' #forget_user_email_error').html("");
						jQuery.ajax({
						type: 'POST',
						url: ajaxUrl,
						data:'action=tmpl_forgot_pass_validation&user_login='+email.val(),			
						success: function(result){
								jQuery('form#'+form_id).prepend(result);
								jQuery('form#'+form_id+' .error_msg').delay(3000).fadeOut('slow');
								jQuery('form#'+form_id+' .success_msg').delay(3000).fadeOut('slow');
							}
						});
						return false;
					}
				}
			</script>
		<?php
			
		echo '</div>';
	endif;

	do_action('tevolution_after_login_from');/* action call after login form*/
	return ob_get_clean();
}
 
/* 
 *  display the tevolution user register form
 */
function tevolution_user_register($atts)
{	
	extract( shortcode_atts( array (
			'form_name'   =>'userform',				
			), $atts ) 
		);	
	ob_start();
	do_action("tmpl_registration_option_js");
	if(is_user_logged_in()): /* user login*/
			/* user already logged in then redirect user page*/
			$user_id = get_current_user_id();
			wp_redirect(get_author_posts_url( $user_id ));
			exit;
	else: 
		/* user not login*/
		include(TT_REGISTRATION_FOLDER_PATH.'registration_form.php');
	endif;
	return ob_get_clean();
}
 
 
/*
 * send the user password
 */
function tevolution_retrieve_password()
{
	global $wpdb;
	$errors = new WP_Error();
	
	if ( empty( $_POST['user_login'] ) && empty( $_POST['user_email'] ) )
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.',DOMAIN));
	if ( strpos($_POST['user_login'], '@') ) {
		$user_data = get_user_by('email',trim($_POST['user_login']));
		if ( empty($user_data) )
			$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.',DOMAIN));
	} else {
		$login = trim($_POST['user_login']);
		$user_data = get_user_by('login',$login);
	}
	if ( $errors->get_error_code() )
		return $errors;
	if ( !$user_data ) {
		$errors->add('invalidcombo', __('<strong>ERROR</strong>: Incorrect username or e-mail.',DOMAIN));
		return $errors;
	}
	/* redefining user_login ensures we return the right case in the email*/
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	do_action('retreive_password', $user_login);  /* Misspelled and deprecated*/
	do_action('retrieve_password', $user_login);
	$user_email = $_POST['user_login'];
	$user_login = $_POST['user_login'];
	
	$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_login like \"$user_login\" or user_email like \"$user_login\"");
	if ( empty( $user ) )
		return new WP_Error('invalid_key', __('Invalid key',DOMAIN));
		
	$new_pass = wp_generate_password(7,false);
	
	wp_set_password($new_pass, $user->ID);
	update_user_meta($user->ID, 'default_password_nag', true); /*Set up the Password change nag.*/
	$user_name = $user_data->user_nicename;
	$fromEmail = get_site_emailId_plugin();
	$fromEmailName = get_site_emailName_plugin();
	$tmpdata = get_option('templatic_settings');
	$email_subject =  @stripslashes($tmpdata['reset_password_subject']);
	if(@$email_subject == '')
	{
		$email_subject = __('[#site_title#] Your new password',DOMAIN);
	}
	$email_content =  @stripslashes($tmpdata['reset_password_content']);
	if(@$email_content == '')
	{
		$email_content = __("<p>Hi [#to_name#],</p><p>You have requested for a new password for your account [#user_email#]. Here is the new password</p><p> Login URL: [#login_url#] </p><p> User name: [#user_login#]</p> <p> Password: [#user_password#]</p><p>You may change this password in your profile once you login with the new password.</p><p>Thanks <br/> [#site_title#] </p>",ADMINDOMAIN);
	}
	$title = sprintf('[%s]'.__(' Your new password',DOMAIN), get_option('blogname'));
	
	$email_subject_array = array('[#site_title#]');
	$email_subject_replace_array = array(get_option('blogname'));
	$email_subject = str_replace($email_subject_array,$email_subject_replace_array,$email_subject);
	
	$login_url = "<a href='".get_tevolution_login_permalink()."'>".__('Login',DOMAIN)."</a>";
	$search_array_content = array('[#to_name#]','[#user_email#]','[#login_url#]','[#user_login#]','[#user_password#]','[#site_title#]');
	$replace_array_content = array($user_name,$user_data->user_email,$login_url,$user->user_login,$new_pass,get_option('blogname'));
	$email_content = str_replace($search_array_content,$replace_array_content,$email_content);
	/* Send forget password email send*/
	templ_send_email($fromEmail,$fromEmailName,$user_email,$user_name,$email_subject,$email_content,$extra='');
	return true;
}
/*
 *  display the user profile update and view field
 */
function tevolution_user_profile($atts)
{
	ob_start();
	if(!is_user_logged_in()): /* user not login*/
		/* user not logeed in then redirect login page	*/
		$login_url=get_tevolution_login_permalink();	
		wp_redirect($login_url);
		exit;
	else: /* user  login*/
	
		include(TT_REGISTRATION_FOLDER_PATH.'user_profile.php');
	
	endif;
	
	return ob_get_clean();
}
/*
* add a class to section to login page to show social media login on middle ofthe page
*/
function add_class_to_login_page()
{
	global $post;
	$login_page_id = get_option('tevolution_login');
	if($login_page_id == $post->ID && get_option('users_can_register') && strpos($post->post_content,'[tevolution_register]'))
	{
	?>
    	<script type="text/javascript" async>
			jQuery(document).ready(function(){
				jQuery('.entry-content').addClass('login_signup');
			});
		</script>
    <?php
	}
}




/*tmpl_forget_password_message hook call for display forgetpassword related message */
function tmpl_forget_password_message(){
	if(!is_user_logged_in()){
		global $error_message,$user_forget_password_errors;
		if ( is_wp_error($user_forget_password_errors) ) {
			echo '<p class="error_msg">'.__($error_message,DOMAIN).'</p>';
		}else
		{
			echo $message = '<div class="success_msg">'.__('Check your e-mail for your new password.',DOMAIN).'</div>';				
		}
	}
}

/*templ_forget_passowrd_action hook user for send reset possword email to user */
add_action('wp_head','templ_forget_passowrd_action');
add_action('wp_ajax_tmpl_forgot_pass_validation','templ_forget_passowrd_action');
add_action('wp_ajax_nopriv_tmpl_forgot_pass_validation','templ_ajax_fg_passowrd_action');
function templ_ajax_fg_passowrd_action(){
	global $error_message,$user_forget_password_errors;
	$user_forget_password_errors = tevolution_retrieve_password();			
	$error_message = $user_forget_password_errors->errors['invalid_email'][0];
	if(!is_user_logged_in()){
		if ( is_wp_error($user_forget_password_errors) ) {
			echo '<p class="error_msg">'.__($error_message,DOMAIN).'</p>';
		}else
		{
			echo $message = '<div class="success_msg">'.__('Check your e-mail for your new password.',DOMAIN).'</div>';				
		}
		exit;
	}
}
/* function called while retrive password. */
function templ_forget_passowrd_action(){
	
	if(!is_user_logged_in() && isset($_POST['action']) && $_POST['action']=='lostpassword' && !defined( 'DOING_AJAX' ) ){		
		global $error_message,$user_forget_password_errors;
		/*tevolution retrieve password email fulcation call */
		$user_forget_password_errors = tevolution_retrieve_password();			
		$error_message = $user_forget_password_errors->errors['invalid_email'][0];
		add_action('tmpl_forget_password_message','tmpl_forget_password_message');
		
	}
}
/**
 * Registration module Shortcode creation
 **/
add_shortcode('tevolution_login', 'tevolution_user_login');
add_shortcode('tevolution_register', 'tevolution_user_register');
add_shortcode('tevolution_profile', 'tevolution_user_profile');
/* logout from social site after logout from our site */
function logout_from_social_site() {
	if(file_exists(TT_REGISTRATION_FOLDER_PATH.'hybridauth/config.php'))
	{
		$hybridauth_config = TT_REGISTRATION_FOLDER_PATH.'hybridauth/config.php';
		require_once( TT_REGISTRATION_FOLDER_PATH."hybridauth/Hybrid/Auth.php" );
		$hybridauth = new Hybrid_Auth( $hybridauth_config );
		$hybridauth -> logoutAllProviders();
	}
}
add_action('wp_logout', 'logout_from_social_site');
?>