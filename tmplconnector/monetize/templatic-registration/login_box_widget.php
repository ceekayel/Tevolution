<?php
/**-- Function to register new user EOF --**/
/*
name : templatic_widget_retrieve_password
description : Function for retrive password BOF --**/
function templatic_widget_retrieve_password() {
	global $wpdb;
	$errors = new WP_Error();
	$login = trim($_POST['user_login']);
	if (empty( $_POST['user_login'] ) )
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.',DOMAIN));
	if ( strpos($_POST['user_login'], '@') ) {
		$user_data = get_user_by('email',$login);
		if ( empty($user_data) )
			$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.',DOMAIN));
	} else {
		$user_data = get_user_by('email',$login);
	}
	if ( $errors->get_error_code() )
		return $errors;
	if ( !$user_data ) {
		$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.',DOMAIN));
		return $errors;
	}
	 /* redefining user_login ensures we return the right case in the email */
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$user_email = $_POST['widget_user_remail'];
	$user_login = $_POST['user_login'];
	
	$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_login like \"$user_login\" or user_email like \"$user_login\"");
	if ( empty( $user ) )
		return new WP_Error('invalid_key', __('Invalid key',DOMAIN));
		
	$new_pass = wp_generate_password(12,false);
	wp_set_password($new_pass, $user->ID);
	update_user_meta($user->ID, 'default_password_nag', true); //Set up the Password change nag.
	$user_email = $user_data->user_email;
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
	templ_send_email($fromEmail,$fromEmailName,$user_email,$user_name,$email_subject,$email_content,$extra='');///forgot password email
	return true;
}
/**-- Function for retrive password EOF --**/
/*  Go inside when user login */
if(isset($_REQUEST['widgetptype']) == 'login')
{	
	include_once( ABSPATH.'wp-load.php' );
	include_once(ABSPATH.'wp-includes/registration.php');
	$secure_cookie = '';
	if ( !empty($_POST['log']) && !force_ssl_admin() ) {
		$user_name = sanitize_user($_POST['log']);
		if ( $user = get_userdata($user_name) ) {
			if ( get_user_option('use_ssl', $user->ID) ) {
				$secure_cookie = true;
				force_ssl_admin(true);
			}
		}
	} 
	if(@$_REQUEST['redirect_to']=='' && @$user)
	{
		$_REQUEST['redirect_to'] = site_url()."/author/".$user->user_nicename;
	}
	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];
		/*  Redirect to https if user wants ssl */
		if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
			$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
		$redirect_to = admin_url();
	}
	if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
		$secure_cookie = false;
	$user = wp_signon('', $secure_cookie);
	$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);
	
	if (!is_wp_error($user) ) {
		// If the user can't edit posts, send them to their profile.
		if ( !current_user_can('edit_posts') && ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) )
			$redirect_to = admin_url('profile.php');				
			wp_safe_redirect($redirect_to);
			exit();
	}
	$errors = $user;
	
	/*  If cookies are disabled we can't log in even with a valid user+pass */
	if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
		$errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress.",DOMAIN));
	
	if ( !is_wp_error($user) ) 
	{
		wp_safe_redirect($redirect_to);
		exit();
	}
}
class loginwidget_plugin extends WP_Widget {
	
	function loginwidget_plugin() {
		//Constructor
		$widget_ops = array('classname' => 'Login Dashboard wizard', 'description' => __('The widget shows account-related links to logged-in visitors. Visitors that are not logged-in will see a login form. Works best in sidebar areas.',ADMINDOMAIN) );		
		$this->WP_Widget('widget_login', __('T &rarr; Login Box',ADMINDOMAIN), $widget_ops);
	}
	
	function widget($args, $instance) {
		// prints the widget
		extract($args, EXTR_SKIP);
		$title = empty($instance['title']) ? 'Dashboard' : apply_filters('widget_title', $instance['title']);		
		if(isset($_REQUEST['widgetptype']) && $_REQUEST['widgetptype'] == 'forgetpass')
		{
			$errors = templatic_widget_retrieve_password();
			if ( !is_wp_error($errors) ) {
				$for_msg = __('Check your e-mail for the new password.',DOMAIN);
			}
		} ?>						
		<script  type="text/javascript" >
			function showhide_forgetpw()
			{
				if(document.getElementById('lostpassword_form').style.display=='none')
				{
					document.getElementById('lostpassword_form').style.display = ''
					document.getElementById('register_form').style.display = 'none'
				}else
				{
					document.getElementById('lostpassword_form').style.display = 'none';
					document.getElementById('register_form').style.display = 'none'
				}	
			}
			function showhide_register()
			{
				if(document.getElementById('register_form').style.display=='none')
				{
					document.getElementById('register_form').style.display = ''
					document.getElementById('lostpassword_form').style.display = 'none'
				}else
				{
					document.getElementById('register_form').style.display = 'none';
					document.getElementById('lostpassword_form').style.display = 'none'
				}	
			}
		</script>
	
		<div class="widget login_widget" id="login_widget">
          <?php
			global $current_user;
			if($current_user->ID && is_user_logged_in())// user loged in
			{
			?>
                    <h3  class="widget-title"><?php echo $title;?></h3>
                    <ul class="xoxo blogroll">
                    <?php 
                         $authorlink = get_author_posts_url($current_user->ID);													
                         echo '<li><a href="'. get_author_posts_url($current_user->ID).'">'; _e('Dashboard',DOMAIN); echo '</a></li>';
                         
                         
                         echo '<li><a href="'.get_tevolution_profile_permalink().'">'; _e('Edit profile',DOMAIN); echo '</a></li>';
                         echo '<li><a href="'.get_tevolution_profile_permalink().'#chngpwdform">'; _e('Change password',DOMAIN); echo '</a></li>';
                         $user_link = get_author_posts_url($current_user->ID);
                         if(strstr($user_link,'?') ){$user_link = $user_link.'&list=favourite';}else{$user_link = $user_link.'?list=favourite';}
                         do_action('tevolution_login_dashboard_content');
                         echo '<li><a href="'.wp_logout_url(get_option('siteurl')."/").'">'; _e('Logout',DOMAIN); echo '</a></li>';
                         ?>
                    </ul>
			<?php
			}else// user not logend in
			{
				if($title){
					echo '<h3>'.$title.'</h3>';
				}
				global $errors,$reg_msg ;
				if(@$_REQUEST['widgetptype'] == 'login')
				{
					if(is_object($errors))
					{
						$login_link = sprintf(__('<a href="%s">Lost your password?</a>',DOMAIN),get_tevolution_login_permalink());
						echo "<p class=\"error_msg\">";
						_e('The password you entered is incorrect. Please try again.',DOMAIN); echo ' '.$login_link;
						echo '</p>';
					
					}
					$errors = new WP_Error();
				}
				include_once(TEMPL_MONETIZE_FOLDER_PATH.'templatic-registration/js/login.js.php');
				?>
				<form name="loginwidgetform" id="loginwidgetform" action="#login_widget" method="post" >
					<input type="hidden" name="widgetptype" value="login" />
					<div class="form_row">
						<label><?php _e('Username',DOMAIN);?>  <span>*</span></label>  <input name="log" id="widget_user_login" type="text" class="textfield" /> <span id="user_login_info"></span>
					</div>
					<div class="form_row">
						<label><?php _e('Password',DOMAIN);?>  <span>*</span></label>  <input name="pwd" id="widget_user_pass" type="password" class="textfield" /><span id="your_pass_info"></span>
					</div>
					<input type="hidden" name="redirect_to" value="<?php if(isset($_SERVER['HTTP_REFERER'])) echo $_SERVER['HTTP_REFERER']; ?>" />
					<input type="hidden" name="testcookie" value="1" />
					<div class="form_row rember clearfix">
					 <label>
						<input name="rememberme" type="checkbox" id="rememberme" value="forever" class="fl" />
						<?php _e('Remember me on this computer',DOMAIN); ?> 
					 </label>	
					</div>
					<div class="form_row clearfix">
						<input type="submit" name="submit" value="<?php _e('Sign In',DOMAIN);?>" class="b_signin button-primary" /> 
					</div>
					<p class="forgot_link">
						<?php $register_page_id=get_option('tevolution_register');?>
						<a href="<?php echo tmpl_get_ssl_normal_url(get_tevolution_register_permalink()); ?>" class="lw_new_reg_lnk"><?php _e('New User? Register Now',DOMAIN);?></a>
						<a href="javascript:void(0);showhide_forgetpw();" class="lw_fpw_lnk"><?php _e('Forgot password',DOMAIN);?></a> 
					</p>
					<?php do_action('login_form');?>
				</form> 
				<?php 
					if(@$_REQUEST['widgetptype'] == 'login')
					{
						if($reg_msg )
							echo "<p class=\"error_msg\">".$reg_msg.'</p>';	
						if(is_object($errors))
						{
							foreach($errors as $errorsObj)
							{
								foreach($errorsObj as $key=>$val)
								{
									for($i=0;$i<count($val);$i++)
									{
									echo "<p class=\"error_msg\">".$val[$i].'</p>';	
									}
								} 
							}
						}
						$errors = new WP_Error();
					}
					?>
		
				<!--  Forgot password section #start  -->           
				<div id="lostpassword_form"  <?php if(isset($_REQUEST['widgetptype']) && $_REQUEST['widgetptype'] == 'forgetpass'){?> style="display:block;" <?php }else{?> style="display:none;" <?php }?> >
				  <?php 
					
					if(@$_REQUEST['widgetptype'] == 'forgetpass')
					{
						if($for_msg )
							echo "<p class=\"success_msg\">".$for_msg.'</p>';	
						if(is_object($errors))
						{
							foreach($errors as $errorsObj)
							{
								foreach($errorsObj as $key=>$val)
								{
									for($i=0;$i<count($val);$i++)
									{
									echo "<p class=\"error_msg\">".$val[$i].'</p>';	
									}
								} 
							}
						}				
					} ?>
					<h4><?php _e('Forgot password',DOMAIN); ?> </h4> 
					<form name="lostpasswordform" id="lostpasswordform" method="post" action="#login_widget">
						<div class="form_row clearfix"> <label>
							<input type="hidden" name="widgetptype" value="forgetpass" />
							<?php _e('Email',DOMAIN);?>: </label>
							<input type="text" name="user_login" id="user_login1" value="<?php echo esc_attr($user_login); ?>" size="20" class="textfield" />
							<?php do_action('lostpassword_form'); ?>
						</div>
						<input type="submit" name="wp-submit" value="<?php _e('Get New Password',DOMAIN);?>" class="b_forgotpass button-primary" />
					</form>  
				</div>    
				<!--  forgot password #end  -->     
             <?php }// finish user loged in condition
		  echo '</div>'; 
	}
	
	function update($new_instance, $old_instance) {
		//save the widget		
		return $new_instance;
	}
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'title' => __("Dashboard",DOMAIN) ) );		
		$title = strip_tags($instance['title']);		
		?>
		<p>
          	<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php echo __('Login Box Title',DOMAIN);?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
               </label>
          </p>
		<?php
	}
}	
/**- function to add facebook login EOF -**/
add_action( 'widgets_init', create_function('', 'return register_widget("loginwidget_plugin");') );
?>
