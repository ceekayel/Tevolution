<?php
if($_POST['user_email'] != '')
{
	if (  $_POST['user_email'] == '' )
	{
		get_header();
		echo "<div class=error_msg>".__('Email for Contact Details is Empty. Please enter Email, your all information will sent to your Email.',DOMAIN)."</div>";	
		echo '<h6><b><a href="'.get_permalink($_POST['cur_post_id']).'/?backandedit=1">Return to '.__(SUBMIT_POST_TEXT).'</a></b></h6>';
		get_footer();
		exit;
	}
	
	if(isset($_REQUEST['action']) && $_REQUEST['action']!='frontend_edit_submit_data'){
		require( 'wp-load.php' );
		require(ABSPATH.'wp-includes/registration.php');
	}

	global $wpdb;
	$errors = new WP_Error();
	
	$user_email = $_POST['user_email'];
	$user_login = $_POST['user_fname'];
	$user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );
	
	// Check the username
	if ( $user_login == '' )
		$errors->add('empty_username', __('ERROR: Please enter a username.'));
	elseif ( !validate_username( $user_login ) ) {
		$errors->add('invalid_username', __('<strong>ERROR</strong>: This username is invalid.  Please enter a valid username.'));
		$user_login = '';
	} elseif ( username_exists( $user_login ) )
		$errors->add('username_exists', __('<strong>ERROR</strong>: '.$user_email.' This username is already registered, please choose another one.'));
	// Check the e-mail address
	if ($user_email == '') {
		$errors->add('empty_email', __('<strong>ERROR</strong>: Please type your e-mail address.'));
	} elseif ( !is_email( $user_email ) ) {
		$errors->add('invalid_email', __('<strong>ERROR</strong>: The email address isn&#8217;t correct.'));
		$user_email = '';
	} elseif ( email_exists( $user_email ) )
		$errors->add('email_exists', __('<strong>ERROR</strong>: '.$user_email.' This email is already registered, please choose another one.'));
	do_action('register_post', $user_login, $user_email, $errors);	
	
	if($errors)
	{
		foreach($errors as $errorsObj)
		{
			foreach($errorsObj as $key=>$val)
			{
				for($i=0;$i<count($val);$i++)
				{
					echo "<div class=error_msg>".$val[$i].'</div>';	
				}
			} 
		}		
	}	
	if ( $errors->get_error_code() )
	{
		echo '<h6><b><a href="'.get_permalink($_SESSION['custom_fields']['cur_post_id']).'/?backandedit=1">Return to '.__(SUBMIT_POST_TEXT).'</a></b></h6>';
		get_footer();
		exit;
	}
		
	$user_pass = wp_generate_password(12,false);
	$user_id = wp_create_user( $user_login, $user_pass, $user_email );
	$crd = array();
	$crd['user_login'] = $user_login;
	$crd['user_password'] = $user_pass;
	$crd['remember'] = $_POST['remember'];
		if ( !empty($crd['remember']) ):
			$crd['remember'] = true;
		else:
			$crd['remember'] = false;
		endif;
		
		
	$user = wp_signon($crd, true );
	if ( !$user_id ) {
		$errors->add('registerfail', sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !',DOMAIN), get_option('admin_email')));
		exit;
	}
	
	$user_fname = $_POST['user_fname'];
	$user_phone = $_POST['user_phone'];
	$userName = $_POST['user_fname'];
	$user_nicename = strtolower(str_replace(array("'",'"',"?",".","!","@","#","$","%","^","&","*","(",")","-","+","+"," "),array('-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-'),$user_login));
	$user_nicename = get_user_name_plugin($user_fname,''); //generate nice name
	$user_address_info = array(
							"user_phone" 	=> $user_phone,
							"first_name"	=>	$_POST['first_name'],
							);
		foreach($user_address_info as $key=>$val)
		{
			update_user_meta($user_id, $key, $val); // User Address Information Here
		}
		$updateUsersql = "update $wpdb->users set user_url=\"$user_web\", user_nicename=\"$user_nicename\" , display_name=\"$user_fname\"  where ID=\"$user_id\"";
		$wpdb->query($updateUsersql);
		$user_meta_array = user_fields_array();
		foreach($user_meta_array as $fkey=>$fval)
		{
			$fldkey = "$fkey";
			$fldkey = $_POST["$fkey"];
			
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
					$extension_file1=array('.php','.js'); 		
					$file_ext= substr($target_file, -4, 4);
					if(!in_array($file_ext,$extension_file1))
					{
					if(move_uploaded_file($_FILES[$fkey]["tmp_name"],$target_file))
					{
						$image_path = $destination_url.$file_ame;
					}else
					{
						$image_path = '';
					}
					}
					$_POST['custom_fields'][$fkey] = $image_path;
					$fldkey = $image_path;
				}
				
			}
			update_user_meta($user_id, $fkey, $fldkey); // User Custom Metadata Here
		}
	
	if ( $user_id) 
	{		
		///////REGISTRATION EMAIL START//////
		$fromEmail = get_site_emailId_plugin();
		$fromEmailName = get_site_emailName_plugin();
		$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
		$user_fname = $_POST['user_fname'];
		global $upload_folder_path;
		$tmpdata = get_option('templatic_settings');
		$clientdestinationfile = stripslashes($tmpdata['registration_success_email_content']); // ABSPATH . $upload_folder_path . "notification/emails/registration.txt";		
		if(!$clientdestinationfile)
		{
			$client_message = REGISTRATION_EMAIL_DEFAULT_TEXT;
			$filecontent_arr1 = explode('[SUBJECT-STR]',$client_message);
			$filecontent_arr2 = explode('[SUBJECT-END]',$filecontent_arr1[1]);
			$subject = $filecontent_arr2[0];
			$client_message = $filecontent_arr2[1];
		}
		else
		{
			$client_message = $clientdestinationfile;
		}
		if($subject == '')
		 {
			$subject = "Thank you for registering!";
		 }
		$store_login_url = '<a href="'.get_tevolution_login_permalink().'">'.__('Login').'</a>';
		/////////////customer email//////////////
		$search_array = array('[#user_name#]','[#user_login#]','[#user_password#]','[#site_name#]','[#site_login_url#]','[#site_login_url_link#]');
		$replace_array = array($user_fname,$user_login,$user_pass,$store_name,$store_login,$store_login_url);
		$client_message = str_replace($search_array,$replace_array,$client_message);			
		templ_send_email($fromEmail,$fromEmailName,$user_email,$userName,$subject,$client_message,$extra='');///To client email
		//////REGISTRATION EMAIL END////////
	}
	$current_user_id = $user_id;
	global $post,$wpdb;
	$id = $_POST['cur_post_id'];
	$permalink = get_permalink( $id );	
	
	// return script when frontend edit submit form
	if(isset($_REQUEST['action']) && $_REQUEST['action']==apply_filters('tevolution_register_action','frontend_edit_submit_data')){
		return $current_user_id;
	}

	$tmpdata = get_option('templatic_settings');
	if(@$tmpdata['templatic-category_custom_fields'] == 'Yes')
	{
		/* code to set the gobackend edit link AND RENEW LINK */
		if(strpos($permalink,'?'))
		{ 
			 $gobacklink = $permalink."&register=1";
		}else{ 
			$gobacklink = $permalink."?register=1";
		}
		if(isset( $_REQUEST['reg_redirect_link'] ) || $_REQUEST['reg_redirect_link'] != "")	{ 
			$redirect_to = $_REQUEST['reg_redirect_link'].'backandedit=1';
		} else {
			$redirect_to = Unaccent(get_author_posts_url($errors[0]));	
		}
		if ( !is_wp_error($user) ) 
		{
			wp_safe_redirect($gobacklink);
			exit();
		}
	}
}
?>
