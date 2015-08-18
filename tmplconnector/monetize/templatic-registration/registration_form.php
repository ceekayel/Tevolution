<?php 
/*
 * Function Name: tevolution_register_user
 * Return : create new user
 */
if(!function_exists('tevolution_register_user')){
	function tevolution_register_user($user_login, $user_email){
		
		global $wpdb;
		$errors = new WP_Error();
		/* CODE TO CHECK CAPTCHA ON REGISTRATION PAGE - FOR WP-RECAPTCHA*/
		$tmpdata = get_option('templatic_settings');
		$display = $tmpdata['user_verification_page'];
		if($tmpdata['recaptcha'] == 'playthru')
		{
			/* CODE TO CHECK THE RESULTS OF PLAYTHRU */	
			if(file_exists(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php') && is_plugin_active('are-you-a-human/areyouahuman.php')  && in_array('registration',$display))
			{
				require_once( get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php');
				require_once(get_tmpl_plugin_directory().'are-you-a-human/includes/ayah.php');		
				$ayah = new AYAH();		
				$score = $ayah->scoreResult();
				if($score == '')
				{
					$errors->add('captcha', sprintf(__('ERROR: %s',DOMAIN),INVALIDPLAY));		
				}
			}
		}
		/* END OF CODE */
		$user_login = sanitize_user( $user_login );
		$user_email = apply_filters( 'user_registration_email', $user_email );
		// Check the username
		if ( $user_login == '' )
			$errors->add('empty_username', __('ERROR: Please enter a username.',DOMAIN));
		elseif ( !validate_username( $user_login ) ) {
			$errors->add('invalid_username', __('<strong>ERROR</strong>: This username is invalid.  Please enter a valid username.',DOMAIN));
			$user_login = '';
		} elseif ( username_exists( $user_login ) )
			$errors->add('username_exists', __('<strong>ERROR</strong>: This username is already registered, please choose another one.',DOMAIN));
		// Check the e-mail address
		if ($user_email == '') {
			$errors->add('empty_email', __('<strong>ERROR</strong>: Please type your e-mail address.',DOMAIN));
		} elseif ( !is_email( $user_email ) ) {
			$errors->add('invalid_email', __('<strong>ERROR</strong>: The email address isn&#8217;t correct.',DOMAIN));
			$user_email = '';
		} elseif ( email_exists( $user_email ) )
			$errors->add('email_exists', __('<strong>ERROR</strong>: This email is already registered, please choose another one.',DOMAIN));
		do_action('register_post', $user_login, $user_email, $errors);
		// captcha validation
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if(file_exists(ABSPATH.'wp-content/plugins/wp-recaptcha/recaptchalib.php')){
			require_once( ABSPATH.'wp-content/plugins/wp-recaptcha/recaptchalib.php');
		}
		$a = get_option("recaptcha_options");
		$show_in_registration = $a['show_in_registration']; // get option for captcha is enable for registration page
	
		if(is_plugin_active('wp-recaptcha/wp-recaptcha.php') && is_multisite() && $show_in_registration){
					$privatekey = $a['private_key'];
				
					/* blank field validation */
					if (empty($_POST['recaptcha_response_field']) || $_POST['recaptcha_response_field'] == '') {
							$errors->add('blank_captcha',  __('<strong>ERROR</strong>: Please fill in the reCAPTCHA form.',DOMAIN));
							return $errors;
						}
					
					/* get response for entered captcha */
						$resp = recaptcha_check_answer ($privatekey,
								getenv("REMOTE_ADDR"),
								$_POST["recaptcha_challenge_field"],
								$_POST["recaptcha_response_field"]);						
							
				/* if captcha is not matched then add an error message */							
					if (!$resp->is_valid ) {
						$errors->add('captcha_wrong', __('<strong>ERROR</strong>: That reCAPTCHA response was incorrect.',DOMAIN));
					}
			}
		$errors = apply_filters( 'registration_errors', $errors );
		if ( $errors->get_error_code() )
			return $errors;
		$user_pass = wp_generate_password(12,false);
		$user_id = wp_create_user( $user_login, $user_pass, $user_email );	
		$activation_key = md5($user_login).rand().time();
		global $upload_folder_path;
		global $form_fields_usermeta;
		fetch_user_custom_fields();
		foreach($form_fields_usermeta as $fkey=>$fval)
		{
			$fldkey = "$fkey";
			$$fldkey = $_POST["$fkey"];
			
			if($fval['type']=='upload')
			{
				if($_FILES[$fkey]['name'] && $_FILES[$fkey]['size']>0)
				{
					$dirinfo = wp_upload_dir();
					$path = $dirinfo['path'];
					$url = $dirinfo['url'];
					$destination_path = $path."/";
					$destination_url = $url."/";
					
					$src = $_FILES[$fkey]['tmp_name'];
					$file_ame = date('Ymdhis')."_".$_FILES[$fkey]['name'];
					$target_file = $destination_path.$file_ame;
					if(move_uploaded_file($_FILES[$fkey]["tmp_name"],$target_file))
					{
						$image_path = $destination_url.$file_ame;
					}else
					{
						$image_path = '';	
					}
					
					$_POST[$fkey] = $image_path;
					$$fldkey = $image_path;
				}
				
			}
			update_user_meta($user_id, $fkey, $$fldkey); // User Custom Metadata Here
		}
		$userName = $_POST['user_fname'];
		update_user_meta($user_id, 'first_name', $_POST['first_name']); // User First Name Information Here
		update_user_meta($user_id, 'last_name', $_POST['last_name']); // User Last Name Information Here
		update_user_meta($user_id,'activation_key',$activation_key); // User activation key here
		update_user_meta($user_id,'user_password',$user_pass);
		$user_nicename = $_POST['user_fname'].$_POST['user_lname']; //generate nice name
		$updateUsersql = "update $wpdb->users set user_url=\"$user_web\", display_name=\"$userName\"  where ID=\"$user_id\"";
		$wpdb->query($updateUsersql);
		if ( $user_id ) {
			$user_info = get_userdata($user_id);
			$user_login = $user_info->user_login;
			$user_pass = get_user_meta($user_id,'user_password',true);	
			$activation_key = get_user_meta($user_id,'activation_key',true);	
			$tmpdata = get_option('templatic_settings');
			$subject = stripslashes($tmpdata['registration_success_email_subject']);
			$client_message = stripslashes($tmpdata['registration_success_email_content']);
			$fromEmail = get_site_emailId_plugin();
			$fromEmailName = get_site_emailName_plugin();	
			$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
			if($subject=="" && $client_message=="")
			{
				//registration_email($user_id);
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
		
			/////////////customer email//////////////
			$search_array = array('[#user_name#]','[#user_login#]','[#user_password#]','[#site_name#]','[#site_login_url#]','[#site_login_url_link#]');
			$replace_array = array($user_login,$user_login,$user_pass,$store_name,$store_login,$store_login_link);
			$client_message = str_replace($search_array,$replace_array,$client_message);
			templ_send_email($fromEmail,$fromEmailName,$user_email,$userName,$subject,$client_message,$extra='');
		}
		
		if ( !$user_id ) {
			$errors->add('registerfail', sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the ',DOMAIN).'<a href="mailto:%s">webmaster</a> !', get_option('admin_email')));
			return $errors;
		}
		else
		{
			$tmpdata = get_option('templatic_settings');		
			if(!$tmpdata['allow_autologin_after_reg']) // auto login not allowed
			 {
				$_SESSION['successfull_register']='1';
				$register_redirect_url=apply_filters('tevolution_register_redirect',get_permalink());
				$redirect_to = wp_redirect($register_redirect_url); // redirect on login page
			 }
		}	
		return array($user_id,$user_pass);
	}
}
if(isset($_POST) && $_POST['action']=='register'){
	
	$errors = tevolution_register_user( $_POST['user_fname'], $_POST['user_email']);		
	if ( !is_wp_error($errors) ) 
	{
		$_POST['log'] = $user_login;
		$_POST['pwd'] = $errors[1];
		$_POST['testcookie'] = 1;
		
		$secure_cookie = '';
		// If the user wants ssl but the session is not ssl, force a secure cookie.
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
		if(isset( $_REQUEST['reg_redirect_link'] ) && $_REQUEST['reg_redirect_link'] != "")	{ 
			$redirect_to = $_REQUEST['reg_redirect_link'];
		} else {
			$redirect_to = Unaccent(get_author_posts_url($errors[0]));	
		}
		
		
		if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
			$secure_cookie = false;
			$tmpdata = get_option('templatic_settings');
			if($tmpdata['allow_autologin_after_reg'])
			{
				$creds = array();
				$creds['user_login'] = $_POST['user_fname'];
				$creds['user_password'] = $errors[1];
				$creds['remember'] = true;
				$user = wp_signon($creds, $secure_cookie);				
				if ( !is_wp_error($user) ) 	{
					$register_redirect_url=apply_filters('tevolution_register_redirect',$redirect_to);
					wp_redirect($register_redirect_url);
					exit();
				}
			}
			exit();
	}else{					
		if($errors->errors['username_exists'][0]!="")
		{
			echo '<p class="error_msg">'.$errors->errors['username_exists'][0].'</p>';
		}
		if($errors->errors['email_exists'][0]!="")
		{
			echo '<p class="error_msg">'.$errors->errors['email_exists'][0].'</p>';
		}
		if($errors->errors['captcha'][0]!="")
		{
			echo '<p class="error_msg">'.$errors->errors['captcha'][0].'</p>';
		}elseif($errors->errors['captcha_wrong'][0]!="")
		{
			echo '<p class="error_msg">'.$errors->errors['captcha_wrong'][0].'</p>';
		}
		elseif($errors->errors['blank_captcha'][0]!="")
		{
			echo '<p class="error_msg">'.$errors->errors['blank_captcha'][0].'</p>';
		}
		
	}
}
if(isset($_SESSION['successfull_register']) && $_SESSION['successfull_register']!='')
{
	echo "<p class=\"success_msg\"> ".REGISTRATION_SUCCESS_MSG."</p>";
	unset($_SESSION['successfull_register']);
}
remove_filter( 'the_content', 'wpautop' , 12); 
if ( get_option('users_can_register') ) { ?>
<div id="sign_up">  
  <div class="registration_form_box">
    <h3><?php _e('Sign Up Now',DOMAIN) ?> </h3>
    <?php
	global $submit_form_validation_id;
	$submit_form_validation_id = "userform";
	
	$action =(isset($_REQUEST['ptype']) && ($_REQUEST['ptype']=='login' || $_REQUEST['ptype']=='register')) ? tmpl_get_ssl_normal_url(home_url().'/?ptype=login&amp;action=register') : tmpl_get_ssl_normal_url( get_permalink());
	?>
 
    <form name="userform" id="userform" action="<?php echo $action ?>" method="post" enctype="multipart/form-data" >  
        <input type="hidden" name="reg_redirect_link" value="<?php echo apply_filters('tevolution_register_redirect_to',@$_SERVER['HTTP_REFERER']);?>" />
			<input type="hidden" name="user_email_already_exist" id="user_email_already_exist" value="" />
	   <input type="hidden" name="user_fname_already_exist" id="user_fname_already_exist" value="" />
        <input type="hidden" name="action" id="user_action" value="register" />
	  
      <?php do_action('templ_registration_form_start');
			//fetch the user custom fields for registration page.
			fetch_user_registration_fields('register');
			
		do_action('templ_registration_form_end');
		
		$tmpdata = get_option('templatic_settings');
		$display = @$tmpdata['user_verification_page'];
		if(isset($tmpdata['recaptcha']) && $tmpdata['recaptcha'] == 'recaptcha')
		{
			$a = get_option("recaptcha_options");
			if(file_exists(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php') && is_plugin_active('wp-recaptcha/wp-recaptcha.php') && in_array('registration',$display))
			{
				require_once(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php');
				echo '<label class="recaptcha_claim">'; _e('Verify Captcha',DOMAIN); echo ' : </label>  <span>*</span>';
				 $errors = new WP_Error();
				// if site is multisite
				if(is_multisite()){
					do_action('signup_extra_fields',$errors); // added $errors for error message for sove the fattle error of non object
				}else{
					do_action('register_form');
				}
			}
		}
		elseif(isset($tmpdata['recaptcha']) && $tmpdata['recaptcha'] == 'playthru')
		{ 
			/* CODE TO ADD PLAYTHRU PLUGIN COMPATIBILITY */
			if(file_exists(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php') && is_plugin_active('are-you-a-human/areyouahuman.php')  && in_array('registration',$display))
			{
				require_once( get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php');
				require_once(get_tmpl_plugin_directory().'are-you-a-human/includes/ayah.php');
				$ayah = ayah_load_library();
				echo $ayah->getPublisherHTML();
			}
		}
			/* ENF OF CODE */?>
      <input type="submit" name="registernow" value="<?php _e('Register Now',DOMAIN);?>" class="b_registernow" id="registernow_form" />
    </form>
  </div>
</div>
<?php include_once(TT_REGISTRATION_FOLDER_PATH . 'registration_validation.php');?>
<?php }else{
	_e('<p>Registration is disabled on this website !</p>',DOMAIN);
} ?>
