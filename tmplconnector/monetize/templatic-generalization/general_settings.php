<?php
if(isset($_POST["settings-submit"]) && $_POST["settings-submit"] == 'Y' ) 
{
	templatic_save_settings();
	$url_parameters = isset($_GET['tab'])? 'updated=true&tab='.$_GET['tab'] : 'updated=true';
	$sub_url_parameters = isset($_GET['sub_tab'])? '&sub_tab='.$_GET['sub_tab'] : '';
	$map_url_parameters = isset($_GET['map_tab'])? '&map_tab='.$_GET['map_tab'] : '';
	echo "<script>location.href='".admin_url('admin.php?page=templatic_settings&'.$url_parameters.$sub_url_parameters.$map_url_parameters.'')."'</script>";

}

/*
Name : templatic_save_settings
Description : Save all general settings
*/
function templatic_save_settings() {
   global $pagenow;
   $settings = get_option( "templatic_settings" );    
   $filename = basename($_SERVER['PHP_SELF']);
   if ( $filename == 'admin.php' && $_GET['page'] == 'templatic_settings' )
   {		
		/* POST BLOCKED IP ADDRESSES */
			/* CALL A FUNCTION TO SAVE IP DATA */	
		if(function_exists('insert_ip_address_data')){
			foreach(explode(',',$_POST['block_ip']) as $ips){
				$ipsss[] = trim(preg_replace('/\s+/', '', $ips));
			}
			$ipsss = implode(',',$ipsss);
			insert_ip_address_data($ipsss);
		}
		/*Saving general settings data: Start*/
		
			
		if(isset($_REQUEST['tab']) && $_REQUEST['tab']=="security-settings" || $_REQUEST['tab']==""){
			$_POST['site_key']=($_POST['site_key'])?$_POST['site_key']:'';
			$_POST['secret']=($_POST['secret'])?$_POST['secret']:'';
			$_POST['comments_theme']=($_POST['comments_theme'])?$_POST['comments_theme']:'';
			$_POST['captcha_language']=($_POST['captcha_language'])?$_POST['captcha_language']:'';
			$_POST['user_verification_page']=isset($_POST['user_verification_page'])?$_POST['user_verification_page']:array();		
			$_POST['templatic-is_allow_ssl']=isset($_POST['templatic-is_allow_ssl'])?$_POST['templatic-is_allow_ssl']:'No';
		}	
		if(isset($_REQUEST['tab']) && $_REQUEST['tab']=="email")
		{
			$_POST['send_to_frnd']=($_POST['send_to_frnd'])?$_POST['send_to_frnd']:'';		
			$_POST['send_inquiry']=($_POST['send_inquiry'])?$_POST['send_inquiry']:'';		
		}
		
		if((isset($_REQUEST['tab']) && @$_REQUEST['tab']=="general") || $_REQUEST['tab']=="")
		{
			$_POST['tev_accept_term_condition']=isset($_POST['tev_accept_term_condition'])?$_POST['tev_accept_term_condition']:'';	
			$_POST['sorting_option']=isset($_POST['sorting_option'])?$_POST['sorting_option']:array();
			$_POST['home_listing_type_value']=isset($_POST['home_listing_type_value'])?$_POST['home_listing_type_value']:array();
			
			
			$_POST['facebook_share_detail_page']=isset($_POST['facebook_share_detail_page'])?$_POST['facebook_share_detail_page']:array();
			$_POST['google_share_detail_page']=isset($_POST['google_share_detail_page'])?$_POST['google_share_detail_page']:array();
			$_POST['twitter_share_detail_page']=isset($_POST['twitter_share_detail_page'])?$_POST['twitter_share_detail_page']:array();
			$_POST['pintrest_detail_page']=isset($_POST['pintrest_detail_page'])?$_POST['pintrest_detail_page']:array();
			$_POST['templatin_rating']=isset($_POST['templatin_rating'])?$_POST['templatin_rating']:array();
			$_POST['validate_rating']=isset($_POST['validate_rating'])?$_POST['validate_rating']:array();
			$_POST['allow_autologin_after_reg']=isset($_POST['allow_autologin_after_reg'])?$_POST['allow_autologin_after_reg']:array();
			
			$_POST['templatic_view_counter']=isset($_POST['templatic_view_counter'])?$_POST['templatic_view_counter']:'No';
			$_POST['pippoint_oncategory']=isset($_POST['pippoint_oncategory'])?$_POST['pippoint_oncategory']:'0';
			
			$_POST['google_map_show']=isset($_POST['google_map_show'])?$_POST['google_map_show']:'No';
			$_POST['google_map_full_width']=isset($_POST['google_map_full_width'])?$_POST['google_map_full_width']:'No';
			$_POST['google_map_hide']=isset($_POST['google_map_hide'])?$_POST['google_map_hide']:'No';
			
			$_POST['direction_map']=isset($_POST['direction_map'])?$_POST['direction_map']:'No';
			$_POST['category_googlemap_widget']=isset($_POST['category_googlemap_widget'])?$_POST['category_googlemap_widget']:'no';
			
			$_POST['templatic-category_custom_fields']=isset($_POST['templatic-category_custom_fields'])?$_POST['templatic-category_custom_fields']:'No';
			
			$_POST['claim_post_type_value']=isset($_POST['claim_post_type_value'])?$_POST['claim_post_type_value']:'';		
			$_POST['listing_hide_excerpt']=(isset($_POST['listing_hide_excerpt']))?$_POST['listing_hide_excerpt']:array();		
			$_POST['related_post_type']=(isset($_POST['related_post_type']))?$_POST['related_post_type']:array();
			
			$_POST['allow_facebook_login']=isset($_POST['allow_facebook_login'])?$_POST['allow_facebook_login']:'';
			$_POST['facebook_key']=isset($_POST['facebook_key'])?$_POST['facebook_key']:'';
			$_POST['facebook_secret_key']=isset($_POST['facebook_secret_key'])?$_POST['facebook_secret_key']:'';
			$_POST['allow_google_login']=isset($_POST['allow_google_login'])?$_POST['allow_google_login']:'';
			$_POST['google_key']=isset($_POST['google_key'])?$_POST['google_key']:'';
			$_POST['google_secret_key']=isset($_POST['google_secret_key'])?$_POST['google_secret_key']:'';
			$_POST['allow_twitter_login']=isset($_POST['allow_twitter_login'])?$_POST['allow_twitter_login']:'';
			$_POST['twitter_key']=isset($_POST['twitter_key'])?$_POST['twitter_key']:'';
			$_POST['twitter_secret_key']=isset($_POST['twitter_secret_key'])?$_POST['twitter_secret_key']:'';
		}
		foreach($_POST as $key=>$val)
		{
			$settings[$key] = ($_POST[$key] || $_POST[$key]==0) ? $_POST[$key] : '';
			update_option('templatic_settings',$settings);
		}
		/* Saving general settings data: Start */
   }
   
   do_action('templatic_save_extra_settings');
}


/* general setting tab filter */
add_filter('templatic_general_settings_tab', 'general_setting',10); 
function general_setting($tabs ) {
	
	$tabs['general']=__('General Settings','templatic');
	return $tabs;
}

/*
 * Create email setting data action
 */
add_action('templatic_general_data_email','email_setting_data',10);
function email_setting_data($column)
{
	$tmpdata = get_option('templatic_settings');		
	switch($column)
	{
		case 'email':	
			?>
		
			<thead>
				<tr>
					<th class="first-th">
						<label for="email_type" class="form-textfield-label"><?php echo __('Email Templates','templatic-admin'); ?></label>
					</th>
					
					<th class="last-th">
						<label for="email_desc" class="form-textfield-label"><?php echo __('Actions','templatic-admin'); ?></label>
					</th>
				</tr>
				</thead>
				<tr class="email-to-friend alternate">
					<td>
					<label class="form-textfield-label"><?php echo __('Send to friend','templatic-admin'); ?></label>
					</td>
					
					<td>
					<a href="javascript:void(0);" title="email-to-friend,edit-email-to-friend" onclick="open_quick_edit('email-to-friend','edit-email-to-friend')"><?php echo __("Quick Edit",'templatic-admin');?></a> 
					| 
					<a href="javascript:void(0);" onclick="reset_to_default('mail_friend_sub','mail_friend_description','email-to-friend');"><?php echo __("Reset",'templatic-admin');?></a>
					<span class="spinner" style="margin:2px 18px 0;"></span>
					<span class="qucik_reset"><?php echo __("Data reset",'templatic-admin');?></span>
					</td>
				</tr>
				<tr class="edit-email-to-friend alternate" style="display:none">
					<td width="100%" colspan="2">
						<h4 class="edit-sub-title"><?php echo __("Quick Edit",'templatic-admin');?></h4>
						<table width="98%" align="left" class="tab-sub-table">
							<tr>
								<td style="line-height:10px">
									<label class="form-textfield-label sub-title"><?php echo __('Subject','templatic-admin'); ?></label>
								</td>
								<td width="90%" style="line-height:10px">
									<input type="text" name="mail_friend_sub" id="mail_friend_sub" value="<?php if(isset($tmpdata['mail_friend_sub'])){echo stripslashes($tmpdata['mail_friend_sub']);}else{ echo __('Check out this post','templatic-admin');} ?>"/>
								</td>
							</tr>
							<tr>
								<td style="line-height:10px">
									<label class="form-textfield-label sub-title"><?php echo __('Message','templatic-admin'); ?></label>
								</td>
								<td width="90%" style="line-height:10px">
									<?php
									$settings =   array(
													'wpautop' => false, /* use wpautop?*/
													'media_buttons' => false, /* show insert/upload button(s)*/
													'textarea_name' => 'mail_friend_description', /* set the textarea name to something different, square brackets [] can be used here*/
													'textarea_rows' => '7', /* rows="..."*/
													'tabindex' => '',
													'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', /* intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".*/
													'editor_class' => '', /* add extra class(es) to the editor textarea*/
													'teeny' => true, /* output the minimal editor config used in Press This*/
													'dfw' => true, /* replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)*/
													'tinymce' => true, /* load TinyMCE, can be used to pass settings directly to TinyMCE using an array()*/
													'quicktags' => true /* load Quicktags, can be used to pass settings directly to Quicktags using an array()*/
												);	
									/* default settings*/
									if($tmpdata['mail_friend_description'] != ""){
										$content = stripslashes($tmpdata['mail_friend_description']);
									}else{
										$content = '<p>Hey [#to_name#],</p><p>[#frnd_comments#]</p><p>Link: [#post_title#]</p><p>Cheers<br/>[#your_name#]</p>';
									}
									wp_editor( $content, 'mail_friend_description', $settings);
								?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="buttons">
										<div class="inline_update">
										<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",'templatic-admin');?></a>
										<a class="button-secondary alignright cancel " href="javascript:void(0);" onclick="open_quick_edit('edit-email-to-friend','email-to-friend')" accesskey="c"><?php echo __('Cancel','templatic-admin');?></a>
										<span class="save_error" style="display:none"></span><span class="spinner"></span>
										</div>
									</div>	
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="inquiry-email">
					<td><label class="form-textfield-label"><?php echo __('Inquiry email','templatic-admin'); ?></label></td>
					
					<td>
						<a href="javascript:void(0);" onclick="open_quick_edit('inquiry-email','edit-inquiry-email')"><?php echo __("Quick Edit",'templatic-admin');?></a>
						|
						<a href="javascript:void(0);" onclick="reset_to_default('send_inquirey_email_sub','send_inquirey_email_description','inquiry-email');"><?php echo __("Reset",'templatic-admin');?></a>
						<span class="spinner" style="margin:2px 18px 0;"></span>
						<span class="qucik_reset"><?php echo __("Data reset",'templatic-admin');?></span>
					</td>
				</tr>
				<tr class="edit-inquiry-email" style="display:none">
					<td width="100%" colspan="3">
						<h4 class="edit-sub-title"><?php echo __("Quick Edit",'templatic-admin');?></h4>
						<table width="98%" align="left" class="tab-sub-table">
							<tr>
								<td style="line-height:10px">
									<label class="form-textfield-label sub-title"><?php echo __('Subject','templatic-admin'); ?></label>
								</td>
								<td width="90%" style="line-height:10px">
									<input type="text" name="send_inquirey_email_sub" id="send_inquirey_email_sub" value="<?php if(isset($tmpdata['send_inquirey_email_sub'])){echo stripslashes($tmpdata['send_inquirey_email_sub']);}else{echo 'Inquiry email';} ?>"/>
								</td>
							</tr>
							<tr>
								<td style="line-height:10px">
									<label class="form-textfield-label sub-title"><?php echo __('Message','templatic-admin'); ?></label>
								</td>
								<td width="90%" style="line-height:10px">
									<?php
									$settings =   array(
													'wpautop' => false, /* use wpautop?*/
													'media_buttons' => false, /* show insert/upload button(s)*/
													'textarea_name' => 'send_inquirey_email_description', /* set the textarea name to something different, square brackets [] can be used here*/
													'textarea_rows' => '7', /* rows="..."*/
													'tabindex' => '',
													'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', /* intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".*/
													'editor_class' => '', /* add extra class(es) to the editor textarea*/
													'teeny' => true, /* output the minimal editor config used in Press This*/
													'dfw' => true, /* replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)*/
													'tinymce' => true, /* load TinyMCE, can be used to pass settings directly to TinyMCE using an array()*/
													'quicktags' => true /* load Quicktags, can be used to pass settings directly to Quicktags using an array()*/
												);	
									if($tmpdata['send_inquirey_email_description'] != ""){
										$content = stripslashes($tmpdata['send_inquirey_email_description']);
									}else{
										$content = '<p>Hello [#to_name#],</p><p>'.__('This is an inquiry regarding the following post','templatic').': <b>[#post_title#]</b></p><p><b>'.__('Subject','templatic').': [#frnd_subject#]</b></p><p>'.__('Link','templatic').' : <b>[#post_title#]</b> </p><p>'.__('Contact number','templatic').' : [#contact#]</p><p>[#frnd_comments#]</p><p>'.__('Thank you','templatic').',<br />[#your_name#]</p>';
									}
									wp_editor( $content, 'send_inquirey_email_description', $settings);
								?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="buttons">
									<div class="inline_update"><a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",'templatic-admin');?></a></div>
										<div><a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-inquiry-email','inquiry-email')" accesskey="c"><?php echo __('Cancel','templatic-admin');?></a>
										<span class="save_error" style="display:none"></span><span class="spinner"></span>
										</div>
										
									</div>	
								</td>
							</tr>
						</table>
						
					</td>
				</tr>
<?php	break;
	}
}
/*
 * Apply filter for get the general setting tabs
 * if you want to create new main tab in general setting menu then use 'templatic_general_settings_tab' filter hook and pass the tabs arrya in filter hook function and return tabs array.
 */
@$tabs = apply_filters('templatic_general_settings_tab',$tabs);	
echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>'; 

?>
<h2><?php echo __('Tevolution Settings','templatic-admin');?></h2>
<h2 class="nav-tab-wrapper">
<?php
$i=0;
foreach( $tabs as $tab => $name ){
	if($i==0)	
		$tab_key=$tab;	
		
	$current_tab=isset($_REQUEST['tab'])?$_REQUEST['tab']:$tab_key;			
	$class = ( $tab == $current_tab) ? ' nav-tab-active' : '';				
	echo "<a class='nav-tab$class' href='?page=templatic_settings&tab=$tab'>$name</a>";	
	$i++;
}
echo '</h2>';
/* Finish the general setting menu main tabs */


/* Display the message */
do_action('action_before_success_message');
if((isset($_REQUEST['updated']) && $_REQUEST['updated'] == 'true') && (!isset($_REQUEST['map_tab'])) ): ?>
	<div class="act_success updated" id="message">
		<p><?php echo __("<strong>Record updated successfully</strong>",'templatic-admin'); ?> .</p>
	</div>
<?php endif; 
do_action('action_after_success_message');?>
<!--Finish the display message-->

<div class="templatic_settings">
	<?php
	
	if(isset($_REQUEST['tab']) && $_REQUEST['tab'] =='email' ){
		$frm_id = "email_setting_form";  ?>
		<p class="tevolution_desc">
			<?php echo __('Use this section to manage outgoing emails and content messages generated by Tevolution. The outgoing ("From") email address can be changed from WordPress ','templatic-admin');?>
			<a href="<?php echo admin_url('options-general.php');?>"><?php echo __("General Settings",'templatic-admin');?></a>
		</p>
	<?php
	}else{
		$frm_id = "general_setting_form";
	}
	?>
    <form method="post" name="general_setting_form" id="<?php echo $frm_id; ?>" class="form_style" action="<?php admin_url( 'themes.php?page=templatic_settings' ); ?>">
	
	<?php 
		$tmpdata = get_option('templatic_settings');
		if(isset($_REQUEST['tab']) && $_REQUEST['tab'] =='email'){ 
		?>
		<div class="wp-filter tev-sub-menu" >
			<ul id="tev_email_settings" class="filter-links">
				<li class="active"><a id="email_settings" href="javascript:void(0);" class="current"><?php echo __('Emails','templatic-admin'); ?></a></li>
				<li><a id="notifications_settings" href="javascript:void(0);"><?php echo __('Notifications','templatic-admin'); ?></a></li>
			</ul>
		</div>
		<!-- Email settings start -->
		<div id="email_settings" class="tmpl-email-settings active-tab">
		<table class="form-table email-wide-table email-settings">
		<tbody>
			<tr>					
			<td>
			
			<table>						
				<tr>
					<th><label><?php echo __('Outgoing Emails Method','templatic-admin');?></label></th>
					<td>
						<div class="input_wrap"> <label for="php_mail"><input type="radio" id="php_mail" name="php_mail" value="php_mail" <?php if(isset($tmpdata['php_mail']) && $tmpdata['php_mail'] == 'php_mail'){?>checked="checked"<?php }?> />&nbsp;<?php echo __('PHP - mail()','templatic-admin');?></label>&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="wp_smtp"><input type="radio" id="wp_smtp" name="php_mail" <?php if(isset($tmpdata['php_mail']) && $tmpdata['php_mail'] == 'wp_smtp'){?> checked="checked"<?php }?> value="wp_smtp" />&nbsp;<?php echo __('WordPress - wp_mail()','templatic-admin');?>
						</label></div>
					   <p class="description"><?php echo __("Tevolution uses mail() by default. Change the setting to wp_mail() only if you're using a third-party plugin for modifying outgoing emails.",'templatic-admin'); ?></p>
					</td>
				</tr>
			</table>

			 <table >	
				<tr>
					<th><label><?php echo __('Detail Page Forms','templatic-admin');?></label></th>
					<td>
					<div class="input_wrap"> <label for="send_to_frnd"><input type="checkbox" id="send_to_frnd" name="send_to_frnd" value="send_to_frnd" <?php if(isset($tmpdata['send_to_frnd']) && $tmpdata['send_to_frnd'] == 'send_to_frnd'){?>checked="checked"<?php }?> />&nbsp;<?php echo __('Send to Friend','templatic-admin');?></label>&nbsp;&nbsp;&nbsp;&nbsp;<br/><label for="send_inquiry"><input type="checkbox" id="send_inquiry" name="send_inquiry" <?php if(isset($tmpdata['send_inquiry']) && $tmpdata['send_inquiry'] == 'send_inquiry'){?> checked="checked"<?php }?> value="send_inquiry" /><?php echo __('Send Inquiry','templatic-admin');?>
					</label>
					</div>
				</td>
				</tr>
                
                <tr>
				<td colspan="2">
					<p style="clear: both;" class="submit">
				  <input type="submit" value="<?php echo __('Save All Settings','templatic-admin');?>" class="button button-primary button-hero" name="Submit">
				  <input type="hidden" value="Y" name="settings-submit">
				</p>
				</td>
				</tr>
			 </table>
			</td>
			</tr>
		</tbody>
		</table>
		
	<?php	}

		$j=0;
		$i=0;
    	foreach( $tabs as $tab => $name ){
			if($j==0)				
				$tab_key=$tab;					
			
			if($current_tab=='general'): /* Display the general setting subtabs menu */
				/*display general setting tab wise displaydata*/
				do_action('templatic_general_setting_data');
			endif;
			if(isset($_REQUEST['tab']) && $_REQUEST['tab']==$tab):	
				if($tab =='email'){
				?>
				<table class="widefat post email-wide-table">
					<?php
					do_action('templatic_general_data_'.$tab,$tab); /* add action hook 'templatic_general_data' for show the general setting tabs data. pass the general setting tabs key. */	
					?>
				</table>
				<?php
				}else{
					do_action('templatic_general_data_'.$tab,$tab); /* add action hook 'templatic_general_data' for show the general setting tabs data. pass the general setting tabs key. */					
				}
			endif;
			
			$tab_key="";
			$current_tab='';
			$j++;
		}	
    ?>
    </form>
</div>