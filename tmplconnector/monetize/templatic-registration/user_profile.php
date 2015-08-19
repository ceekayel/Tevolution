<?php
/*
 * edit profile form and save function
 */
global $current_user,$wpdb;
if(isset($_POST['action']) && $_POST['action']=='user_profile')
{
	$user_id = $current_user->ID;	
	if($user_id==$_POST['user_id'])
	{
		$user_email = $_POST['user_email'];
		$userName = $_POST['user_fname'];
		$user_website = $_POST['user_website'];
		$user_data=array('ID'=>$user_id,
					  'user_email'   => $_POST['user_email'],
					  'display_name' => $_POST['display_name'],
					  'user_url'      => $user_website,
					  );
		wp_update_user($user_data) ;
		global $form_fields_usermeta;
		/* Fetch the user custom fields */
		$form_fields_usermeta=fetch_user_custom_fields();		
		foreach($form_fields_usermeta as $fkey=>$fval)
		{
			$fldkey = "$fkey";
			$fldkey = $_POST["$fkey"];
			update_user_meta($user_id, $fkey, $fldkey); /* User Custom Metadata Here */
		}	
		$user_facebook = $_REQUEST['facebook'];
		$user_twitter = $_REQUEST['twitter'];
		$description = $_REQUEST['description'];
		update_user_meta($user_id, 'user_facebook', $user_facebook);
		update_user_meta($user_id, 'user_twitter',$user_twitter);		
		update_user_meta($user_id, 'description', trim($description));	
		$user_name=(get_user_meta($user_id,'first_name',true))? get_user_meta($user_id,'first_name',true): $userName;
		
		echo '<p class="success_msg"> '.__('Hi ','templatic').' <a href="'.get_author_posts_url($user_id).'">'.$user_name.'</a>, '.__('Your profile is updated successfully.','templatic').' </p>';
		
	}
}
if(isset($_POST['action']) && $_POST['action']=='changepwd')
{
	$user_id = $current_user->ID;
	if($_POST['new_passwd']== $_POST['cnew_passwd'])
	{
		$user_data=array('ID'=>$user_id, 'user_pass'   => $_POST['new_passwd'],);
		wp_update_user($user_data) ;
		
		echo '<p class="success_msg"> '.__('Password changed successfully. Please login with your new password.','templatic').' </p>';
		$_SESSION['update_password']='1';		
		wp_logout(); 
		wp_redirect(get_tevolution_login_permalink());
		
	}else{
		echo '<p class="error_msg"> '.__(PW_NO_MATCH_MSG,'templatic').' </p>';
	}
}
global $submit_form_validation_id;
$submit_form_validation_id = "userform";
remove_filter( 'the_content', 'wpautop' , 12);
?>
  
<div class="reg_cont_right">
     <!--user profile form -->
     <form name="userform" id="userform" action="<?php echo get_permalink(); ?>" method="post" enctype="multipart/form-data" >  
          <input type="hidden" name="action" value="user_profile" />
          <input type="hidden" name="user_id" value="<?php echo get_current_user_id();?>" />
          <input type="hidden" name="user_email_already_exist" id="user_email_already_exist" value="1" />
          <input type="hidden" name="user_fname_already_exist" id="user_fname_already_exist" value="1" />
          <?php
          if($_POST)
          {
               $user_email = $_POST['user_email'];	
               $user_fname = $_POST['user_fname'];	
          }else
          {
               $user_email = $current_user->user_email;	
               $user_fname = $current_user->display_name;
          }
          ?>
          <?php do_action('templ_profile_form_start');?>    
                
          <?php fetch_user_registration_fields('profile');?>
          
          <?php do_action('templ_profile_form_end');?>
          
          <input type="submit" name="update" value="<?php _e("Update",'templatic');?>" class="b_registernow btn_update_profile update-btn" />               
          <input type="button" name="Cancel" value="<?php _e("Cancel",'templatic'); ?>" class="b_registernow cancel-btn" onclick="window.location.href='<?php echo get_author_posts_url($current_user->ID);?>'"/>          
     </form>
     <!--end user profile form -->
   	<!--Change password form -->
     <form name="chngpwdform" id="chngpwdform" action="<?php echo get_permalink(); ?>" method="post">
          <input type="hidden" name="action"  value="changepwd"/>
          <input type="hidden" name="user_id" value="<?php echo get_current_user_id();?>" />
          <?php if(isset($message1)) { ?>
               <div class="sucess_msg"> <?php _e('Password changed successfully. Please login with your new password.','templatic'); ?> </div>          
          <?php } ?>
          <h3> <?php echo __(CHANGE_PW_TEXT,'templatic'); ?> </h3>
          <div class="form_row clearfix">
               <label><?php _e('New Password','templatic'); ?> <span class="indicates">*</span></label>   
               <input type="password" name="new_passwd" id="new_passwd"  class="textfield" />
          </div>
          <div class="form_row clearfix ">
               <label><?php _e('Confirm New Password','templatic'); ?> <span class="indicates">*</span></label>
               <input type="password" name="cnew_passwd" id="cnew_passwd"  class="textfield" />
          </div>
          <input type="submit" name="update" value="<?php _e("Update",'templatic');?>" class="b_registernow btn_update_profile update-btn" onclick="return chk_form_pw();" />          
          <input type="button" name="Cancel" value="<?php _e("Cancel",'templatic'); ?>" class="b_registernow cancel-btn" onclick="window.location.href='<?php echo get_author_posts_url($current_user->ID);?>'"/>
     </form>
     <!-- end change password form -->
</div>   
<script  type="text/javascript" async >
     /* <![CDATA[ */
     function chk_form_pw()
     {
          if(document.getElementById('new_passwd').value == '')
          {
               alert("<?php _e('Please enter New Password','templatic') ?>");
               document.getElementById('new_passwd').focus();
               return false;
          }
          if(document.getElementById('new_passwd').value.length < 4 )
          {
               alert("<?php _e('Please enter New Password minimum 5 chars','templatic') ?>");
               document.getElementById('new_passwd').focus();
               return false;
          }
          if(document.getElementById('cnew_passwd').value == '')
          {
               alert("<?php _e('Please enter Confirm New Password','templatic') ?>");
               document.getElementById('cnew_passwd').focus();
               return false;
          }
          if(document.getElementById('cnew_passwd').value.length < 4 )
          {
               alert("<?php _e('Please enter Confirm New Password minimum 5 chars','templatic') ?>");
               document.getElementById('cnew_passwd').focus();
               return false;
          }
          if(document.getElementById('new_passwd').value != document.getElementById('cnew_passwd').value)
          {
               alert("<?php _e('New Password and Confirm New Password should be same','templatic') ?>");
               document.getElementById('cnew_passwd').focus();
               return false;
          }
     }
     /* ]]> */
</script>
<?php include_once(TT_REGISTRATION_FOLDER_PATH . 'registration_validation.php');?>