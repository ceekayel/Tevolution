<?php
/******************************************************************
=======  PLEASE DO NOT CHANGE BELOW CODE  =====
You can add in below code but don't remove original code.
This code to include registration, login and edit profile page.
This file is included in functions.php of theme root at very last php coding line.
You can call registration, login and edit profile page  by the link 
edit profile : http://mydomain.com/?ptype=profile  => echo site_url().'/?ptype=profile';
registration : http://mydomain.com/?ptype=register => echo site_url().'/?ptype=register';
login : http://mydomain.com/?ptype=login => echo site_url().'/?ptype=login';
logout : http://mydomain.com/?ptype=login&action=logout => echo site_url().'/?ptype=login&action=logout';
********************************************************************/

global $wp_query,$wpdb,$wp_rewrite,$post;
define('TEMPL_REGISTRATION_FOLDER_PATH',TEMPL_MONETIZE_FOLDER_PATH.'templatic-registration/');

/* Registration module related constant variable */
define('NEW_PW_TEXT',__('New Password','templatic'));
define('CONFIRM_NEW_PW_TEXT',__('Confirm New Password','templatic'));
define('EDIT_PROFILE_UPDATE_BUTTON',__('Update','templatic'));
define('GET_NEW_PW_TEXT',__('Get New Password','templatic'));
define('ABOUT_TEXT',__('About you','templatic'));
define('YR_WEBSITE_TEXT',__('Your Website','templatic'));
define('ABOUT_U_TEXT',__('Provide brief information about yourself','templatic'));
define('TEMPL_REGISTRATION_FOLDER',TEMPL_MONETIZE_FOLDER_PATH . "templatic-registration/");
define('TEMPL_REGISTRATION_URI',TEMPL_MONETIZE_FOLDER_PATH. "templatic-registration/");
define('TT_REGISTRATION_FOLDER_PATH',TEMPL_MONETIZE_FOLDER_PATH.'templatic-registration/');
include_once(TEMPL_REGISTRATION_FOLDER.'registration_language.php');
if(!defined('PLEASE_SELECT')) 
	define('PLEASE_SELECT',__('Please Select','templatic'));
/**--below are the main file which will work with registration -**/	

if(is_admin() && strstr($_SERVER['REQUEST_URI'],'/wp-admin/' )){
	include_once(TEMPL_REGISTRATION_FOLDER_PATH . 'admin_registration_functions.php');	
}else{
	if(file_exists(TEMPL_REGISTRATION_FOLDER_PATH . 'registration_functions.php'))
	{	
		include_once(TEMPL_REGISTRATION_FOLDER_PATH . 'registration_functions.php');	
	}
}
include_once(TEMPL_REGISTRATION_FOLDER_PATH . 'login_box_widget.php');
include_once(TEMPL_REGISTRATION_FOLDER_PATH . 'shortcodes_registration.php');

add_action( 'after_setup_theme', 'theme_login_setup',11 );


function theme_login_setup(){
	add_filter('wp_nav_menu_items', 'filter_my_theme_nav_bars', 10, 2);
}
/* show login or register link under our menu */
function filter_my_theme_nav_bars($items, $args) {
	global $current_user;	
	
	$login_url=get_tevolution_login_permalink();	
	
	$register_url=get_tevolution_register_permalink();
	
	$theme_locations = apply_filters('tmpl_logreg_links',array('primary','footer'));
	
	/*Primary Menu location */
	/* Check the condition for theme menu location prompt, footer and secondary */
	if(in_array($args->theme_location,$theme_locations))
	{
		if($current_user->ID){
			$loginlink = '<li class="tmpl-login' . ((is_home())? ' ' : '') . '"><a href="' .wp_logout_url(home_url()). '">' . __('Log out','templatic') . '</a></li>'; 
		}else{
			$loginlink = '<li class="tmpl-login' . (($_REQUEST['ptype']=='login')? ' current_page_item' : '') . '" ><a data-reveal-id="tmpl_reg_login_container" href="javascript:void(0);" onClick="tmpl_login_frm();">' . __('Login','templatic') . '</a></li>'; 
		}
		if($current_user->ID){
			$reglink = '<li class="tmpl-login' . ((is_author())? ' current-menu-item ' : '') . '"><a href="' . get_author_posts_url($current_user->ID) . '">' . $current_user->display_name . '</a></li>'; 
		}else{
			$users_can_register = get_option('users_can_register');
			if($users_can_register){				
				$reglink = '<li class="tmpl-login' . (($_REQUEST['ptype']=='register')? ' current_page_item' : '') . '"><a data-reveal-id="tmpl_reg_login_container" href="javascript:void(0);" onClick="tmpl_registretion_frm();">' . __('Register','templatic') . '</a></li>';
				
			}
		}		
		$items = $items. $loginlink.$reglink ;
	} 		
    return $items;
}

/*
	return user custom fields for register or profile page.
*/
function fetch_user_registration_fields($validate,$user_id='',$form_name='')
{
	global $form_fields_usermeta,$user_validation_info,$current_user;
	/* Fetch the user custom fields */
	$form_fields_usermeta=fetch_user_custom_fields();
	$user_validation_info = array();	
	if($form_fields_usermeta){
	foreach($form_fields_usermeta as $key=>$val)
	{ 
		if(($form_name == 'popup_register' || $form_name == 'register_login_widget' ) && ($key != 'user_email' && $key != 'user_fname' ))
		{
			continue;
		}
		if($validate == 'register')
			$validate_form = $val['on_registration'];
		else
			$validate_form = $val['on_profile'];
			
		if($validate_form){
        $str = ''; $fval = '';
        $field_val = $key.'_val';
		
        if(isset($field_val) && $field_val){ $fval = $field_val; }else{ $fval = $val['default']; }
      
        if($val['is_require'])
        {
            $user_validation_info[] = array(
                                       'name'	=> $key,
                                       'espan'	=> $key.'_error',
                                       'type'	=> $val['type'],
                                       'text'	=> $val['label'],
                                       );
        }
		
		if($key)
		{
			if($user_id != '' )
			{
				$fval = get_user_meta($user_id,$key,true);
			}
			else
			{
				$fval = get_user_meta($current_user->ID,$key,true);
			}
		}
		
        if($val['type']=='text')
        {
			if(!(is_templ_wp_admin() && ( $key == 'user_email' || $key == 'user_fname' || $key == 'display_name'))) /* CONDITION FOR EMAIL AND USER NAME FIELD */
			{
				if($key=='user_email')
				{
					$fval=($fval=='')?$current_user->user_email: $fval;
					
				}
				
				if($key=='user_fname')
				{
					if($validate != 'register')
					{					
						$readonly = 'readonly="readonly"';
						$background_color = 'style="background-color:#EEEEEE"';
					}
					$fval=($fval=='')?$current_user->user_login: $fval;
				}
				if($key=='display_name')
				{
					$fval=($fval=='')?$current_user->display_name: $fval;
					
				}
				$str = '<input '.@$readonly.' name="'.$key.'" type="text" '.$val['extra'].' '.@$background_color.' value="'.$fval.'">';
				$readonly = '';
				$background_color = '';
				if($val['is_require'])
				{
					$str .= '<span id="'.$key.'_error"></span>';
				}
			}
        }elseif($val['type']=='hidden')
        {
            $str = '<input name="'.$key.'" type="hidden" '.$val['extra'].' value="'.$fval.'">';	
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='textarea')
        {
            $str = '<textarea name="'.$key.'" '.$val['extra'].'>'.$fval.'</textarea>';	
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='file')
        {
            $str = '<input name="'.$key.'" type="file" '.$val['extra'].' value="'.$fval.'">';
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='include')
        {
            $str = @include_once($val['default']);
        }else
        if($val['type']=='head')
        {
            $str = '';
        }else
        if($val['type']=='date')
        {	
			wp_enqueue_style('jQuery_datepicker_css',TEMPL_PLUGIN_URL.'css/datepicker/jquery.ui.all.min.css');	
			?>
			<script type="text/javascript">	
				jQuery(function(){
				var pickerOpts = {
						showOn: "both",
						dateFormat: 'yy-mm-dd',
						monthNames: objectL11tmpl.monthNames,
						monthNamesShort: objectL11tmpl.monthNamesShort,
						dayNames: objectL11tmpl.dayNames,
						dayNamesShort: objectL11tmpl.dayNamesShort,
						dayNamesMin: objectL11tmpl.dayNamesMin,
						isRTL: objectL11tmpl.isRTL,
						/*buttonImage: "<?php echo TEMPL_PLUGIN_URL; ?>css/datepicker/images/cal.png",*/
						buttonText: '<i class="fa fa-calendar"></i>',
					};	
					jQuery("#<?php echo $key;?>").datepicker(pickerOpts);					
				});
			</script>
			<?php
			$str = '<input name="'.$key.'" id="'.$key.'" type="text" '.$val['extra'].' value="'.$fval.'">';			
			if($val['is_require'])
			{
			$str .= '<span id="'.$key.'_error"></span>';	
			}

        }else
        if($val['type']=='catselect')
        {
            $term = get_term( (int)$fval, CUSTOM_CATEGORY_TYPE1);
            $str = '<select name="'.$key.'" '.$val['extra'].'>';
            $args = array('taxonomy' => CUSTOM_CATEGORY_TYPE1);
            $all_categories = get_categories($args);
            foreach($all_categories as $key => $cat) 
            {
            
                $seled='';
                if($term->name==$cat->name){ $seled='selected="selected"';}
                $str .= '<option value="'.$cat->name.'" '.$seled.'>'.$cat->name.'</option>';	
            }
            $str .= '</select>';
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='catdropdown')
        {
            $cat_args = array('name' => 'post_category', 'id' => 'post_category_0', 'selected' => $fval, 'class' => 'textfield', 'orderby' => 'name', 'echo' => '0', 'hierarchical' => 1, 'taxonomy'=>CUSTOM_CATEGORY_TYPE1);
            $cat_args['show_option_none'] = __('Select Category','templatic');
            $str .=wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='select')
        {
			 $option_values_arr = explode(',', $val['options']);
			 $option_titles_arr = explode(',',$val['option_titles']);
			 if (function_exists('icl_register_string')) {		
				icl_register_string('templatic', $val['option_titles'].'_'.$key,$val['option_titles']);	
				$option_titles_arr = icl_t('templatic', $val['option_titles'].'_'.$key,$val['option_titles']);
		   }
            $str = '<select name="'.$key.'" '.$val['extra'].'>';
			 $str .= '<option value="" >'.PLEASE_SELECT.'</option>';	
            for($i=0;$i<count($option_values_arr);$i++)
            {
                $seled='';
                
                if($fval==$option_values_arr[$i]){ $seled='selected="selected"';}
                $str .= '<option value="'.$option_values_arr[$i].'" '.$seled.'>'.$option_titles_arr[$i].'</option>';	
            }
            $str .= '</select>';
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='catcheckbox')
        {
            $fval_arr = explode(',',$fval);
            $str .= $val['tag_before'].get_categories_checkboxes_form(CUSTOM_CATEGORY_TYPE1,$fval_arr).$oval.$val['tag_after'];
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='catradio')
        {
            $args = array('taxonomy' => CUSTOM_CATEGORY_TYPE1);
            $all_categories = get_categories($args);
            foreach($all_categories as $key1 => $cat) 
            {
                
                
                    $seled='';
                    if($fval==$cat->term_id){ $seled='checked="checked"';}
                    $str .= $val['tag_before'].'<input name="'.$key.'" type="radio" '.$val['extra'].' value="'.$cat->name.'" '.$seled.'> '.$cat->name.$val['tag_after'].'</div>';
                
            }
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='checkbox')
        {
            if($fval){ $seled='checked="checked"';}
            $str = '<input name="'.$key.'" id="'.$key.'" type="checkbox" '.$val['extra'].' value="1" '.$seled.'>';
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='upload')
        {
			$wp_upload_dir = wp_upload_dir();
			$dirinfo = wp_upload_dir();
			$path = $dirinfo['path'];
			$url = $dirinfo['url'];
			$extention = tev_findexts($fval);
			$img_type = array('png','gif','jpg','jpeg','ico');
			$str = '<input name="'.$key.'" type="hidden" '.@$val['extra'].' '.@$uclass.' value="'.$fval.'" > ';
			$str .='<div class="upload_box">
						<div class="hide_drag_option_ie">
							<p>'. __('You can drag &amp; drop images from your computer to this box.','templatic').'</p>
							<p>'. __('OR','templatic').'</p>
						</div>
						<div class="tmpl_single_uploader">
		                	<div id="fancy-contact-form">
							<div class="dz-default dz-message" ><span  id="fancy-'. $key.'"><span><i class="fa fa-folder"></i>'.__('Upload Image','templatic').'</span></span></div>
							<span  id="image-'.$key.'">';
								
								if(in_array($extention,$img_type))
									$str .='<br/><img id="img_'.$key.'" src="'.$fval.'" border="0" class="company_logo" height="80" width="80" /><span class="ajax-file-upload-red" onclick="delete_image(\''.basename($fval).'\')">'.__('Delete','templatic').'</span>';
							$str .='</span>
							</div></div>';
							?>
						<script type="text/javascript" async>
							var image_thumb_src = '<?php echo  $wp_upload_dir['url'];?>/';
							jQuery(document).ready(function(){
								var settings = {
									url: '<?php echo TEMPL_PLUGIN_URL; ?>tmplconnector/monetize/templatic-custom_fields/single-upload.php',
									dragDrop:true,
									fileName: "<?php echo $key; ?>",
									allowedTypes:"jpeg,jpg,png,gif,doc,pdf,zip",	
									returnType:"json",
									multiple:false,
									showDone:false,
									showAbort:false,
									showProgress:true,
									onSuccess:function(files,data,xhr)
									{
										jQuery('#image-<?php echo $key; ?>').html('');
										if(jQuery('#img_<?php echo $key; ?>').length > 0)
										{
											jQuery('#img_<?php echo $key; ?>').remove();
										}
									    var img = jQuery('<img height="100px" width="100px" id="img_<?php echo $key; ?>">'); /*Equivalent: $(document.createElement('img'))*/
										data = data+'';
										var id_name = data.split('.'); 
										var img_name = '<?php echo bloginfo('template_url')."/images/tmp/"; ?>'+id_name[0]+"."+id_name[1];
										img.attr('src', img_name);
										img.appendTo('#image-<?php echo $key; ?>');
										jQuery('#image-<?php echo $key; ?>').css('display','');
										jQuery('#<?php echo $key; ?>').val(image_thumb_src+data);
										jQuery('.ajax-file-upload-filename').css('display','none');
										jQuery('.ajax-file-upload-red').css('display','none');
										jQuery('.ajax-file-upload-progress').css('display','none');
									},
									showDelete:true,
									deleteCallback: function(data,pd)
									{
										for(var i=0;i<data.length;i++)
										{
											jQuery.post("<?php echo TEMPL_PLUGIN_URL; ?>tmplconnector/monetize/templatic-custom_fields/delete_image.php",{op:"delete",name:data[i]},
											function(resp, textStatus, jqXHR)
											{
												/*Show Message  */
												jQuery('#image-<?php echo $key; ?>').html("<div>File Deleted</div>");
												jQuery('#<?php echo $key; ?>').val('');
											});
										 }
										pd.statusbar.hide(); /*You choice to hide/not.*/
									}
								}
								var uploadObj = jQuery("#fancy-"+'<?php echo $key; ?>').uploadFile(settings);
							});
							function delete_image(name)
							{
								jQuery.ajax({
									 url: '<?php echo TEMPL_PLUGIN_URL; ?>tmplconnector/monetize/templatic-custom_fields/delete_image.php?op=delete&name='+name,
									 type: 'POST',
									 success:function(result){			 
										jQuery('#image-<?php echo $key; ?>').html("<div>File Deleted</div>");
										jQuery('#<?php echo $key; ?>').val('');			
									}				 
								 });
							}
						</script>
						<?php
			if($fval!='' && (in_array($extention,$img_type))){
				$str .='
				<input type="hidden" name="prev_upload" value="'.$fval.'" />
				';	
			}
			if($val['is_require'])
			{
				$str .='<span id="'.$key.'_error"></span>';	
			}
			
			$str .= '</div>';
			
        }
        else
        if($val['type']=='radio')
        {
            $options = $val['options'];
		  $option_titles = $val['option_titles'];	
		  if (function_exists('icl_register_string')) {		
				icl_register_string('templatic', $val['option_titles'].'_'.$key,$val['option_titles']);	
				$option_titles = icl_t('templatic', $val['option_titles'].'_'.$key,$val['option_titles']);
		   }
            if($options)
            {
			  $chkcounter = 0;
                $option_values_arr = explode(',',$options);
			 $option_titles_arr = explode(',',$option_titles);
			 $str='<div class="form_cat_left hr_input_radio">';
                for($i=0;$i<count($option_values_arr);$i++)
                {
                    $seled='';
				$chkcounter++;
                    if($fval==$option_values_arr[$i]){$seled='checked="checked"';}
                    $str .= '<div class="form_cat">'.$val['tag_before'].'<label for="'.$key.'_'.$chkcounter.'"><input id="'.$key.'_'.$chkcounter.'" name="'.$key.'" type="radio" '.$val['extra'].'  value="'.$option_values_arr[$i].'" '.$seled.'> '.$option_titles_arr[$i].$val['tag_after']."</label>".'</div>';
                }
                if($val['is_require'])
                {
                    $str .= '<span id="'.$key.'_error"></span>';	
                }
			$str.="</div>";
            }
        }else
        if($val['type']=='multicheckbox')
        {
            $options = $val['options'];
		  $option_titles = $val['option_titles'];		  
		    if (function_exists('icl_register_string')) {		
				icl_register_string('templatic', $val['option_titles'].'_'.$key,$val['option_titles']);	
				$option_titles = icl_t('templatic', $val['option_titles'].'_'.$key,$val['option_titles']);
		   }
            if($options)
            {  
				$chkcounter = 0;
                $option_values_arr = explode(',',$options);
			 $option_titles_arr = explode(',',$option_titles);
			 $str='<div class="form_cat_left hr_input_multicheckbox">';
                for($i=0;$i<count($option_values_arr);$i++)
                {
                    $chkcounter++;
                    $seled='';
					if($fval)
					{
				   		if(in_array($option_values_arr[$i],$fval)){ $seled='checked="checked"';}
					}
                    $str .= $val['tag_before'].'<label for="'.$key.'_'.$chkcounter.'"><input name="'.$key.'[]"  id="'.$key.'_'.$chkcounter.'" type="checkbox" '.$val['extra'].' value="'.$option_values_arr[$i].'" '.$seled.'> '.$option_titles_arr[$i]."</label>".$val['tag_after'];
                }
                if($val['is_require'])
                {
                    $str .= '<span id="'.$key.'_error"></span>';	
                }
			 $str.="</div>";
            }
        }
        else
        if($val['type']=='packageradio')
        {
            $options = $val['options'];
            foreach($options as $okey=>$oval)
            {
                $seled='';
                if($fval==$okey){$seled='checked="checked"';}
                $str .= $val['tag_before'].'<input name="'.$key.'" type="radio" '.$val['extra'].' value="'.$okey.'" '.$seled.'> '.$oval.$val['tag_after'];	
            }
            if($val['is_require'])
            {
                $str .= '<span id="'.$key.'_error"></span>';	
            }
        }else
        if($val['type']=='geo_map')
        {
            do_action('templ_submit_form_googlemap');	
        }else
        if($val['type']=='image_uploader')
        {
            do_action('templ_submit_form_image_uploader');	
        }
	   
	   if (function_exists('icl_register_string')) {		
			icl_register_string('templatic', $val['type'].'_'.$key,$val['label']);	
			$val['label'] = icl_t('templatic', $val['type'].'_'.$key,$val['label']);
	   }
        if($val['is_require'] && !is_admin())
        {
            $label = '<label>'.$val['label'].' <span class="indicates">*</span> </label>';
        }
		elseif($val['is_require'] && is_admin())
        {
           $label = '<label> <span class="indicates">*</span> </label>';
        }
		elseif(is_admin())
        {
            $label = '';
        }elseif($val['type']=='head'){
		  $label = '<h3>'.$val['label'].'</h3>'; 
	   }else
        {
            $label = '<label>'.$val['label'].'</label>';
        }
		if(!(is_templ_wp_admin() && ( $key == 'user_email' || $key == 'user_fname' || $key == 'description'))) /* CONDITION FOR EMAIL AND USER NAME FIELD */
		{			
			if($val['type']=='texteditor')
			{
				echo $val['outer_st'].$label.$val['tag_st'];
				 echo $val['tag_before'].$val['tag_after'];
					$settings =   array(
						'wpautop' => false,
						'media_buttons' => $media_pro,
						'textarea_name' => $key,
						'textarea_rows' => apply_filters('tmpl_wp_editor_rows',get_option('default_post_edit_rows',6)), /* rows="..."*/
						'tabindex' => '',
						'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>',
						'editor_class' => '',
						'toolbar1'=> 'bold,italic,underline,bullist,numlist,link,unlink,forecolor,undo,redo',
						'editor_height' => '150',
						'teeny' => false,
						'dfw' => false,
						'tinymce' => true,
						'quicktags' => false
					);			
					if(isset($fval) && $fval != '') 
					{  $content=$fval; }
					else{$content= $fval; } 				
					wp_editor( $content, $key, $settings);				
			
					if($val['is_require'])
					{
						$str .= '<span id="'.$key.'_error"></span>';	
					}
				echo $str.$val['tag_end'].$val['outer_end'];
			}else{	
				if(is_admin())
					echo $val['outer_st'].$val['label'].$val['tag_st'].$str.$val['tag_end'].$val['outer_end'];
				else
					echo $val['outer_st'].$label.$val['tag_st'].$str.$val['tag_end'].$val['outer_end'];
			}
        }
		}
	}
	}
}
/*
	get the login page URL
*/
function get_tevolution_login_permalink(){
	
	$login_page_id=get_option('tevolution_login');
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') && function_exists('icl_object_id')){
		$login_page_id = icl_object_id( $login_page_id, 'page', false, ICL_LANGUAGE_CODE );
	 }
	return get_permalink($login_page_id);
}

/*
	get the registration page uRL
*/
function get_tevolution_register_permalink(){
	
	$register_page_id=get_option('tevolution_register');
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') && function_exists('icl_object_id')){									
		$register_page_id = icl_object_id( $register_page_id, 'page', false, ICL_LANGUAGE_CODE );
	 }
	 if($register_page_id !='')
		return get_permalink($register_page_id);
}
/*
	get the profile page URL
 */
function get_tevolution_profile_permalink(){
	
	$profile_page_id=get_option('tevolution_profile');
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') && function_exists('icl_object_id')){
		$profile_page_id = icl_object_id( $profile_page_id, 'page', false, ICL_LANGUAGE_CODE );
	 }
	return get_permalink($profile_page_id);
}

/* verification of user name and email on registration page */
add_action('wp_ajax_tmpl_ajax_check_user_email','tmpl_ajax_check_user_email');
add_action('wp_ajax_nopriv_tmpl_ajax_check_user_email','tmpl_ajax_check_user_email');

/* verification of user name and email on registration page. Previous code was in - Tevolution\tmplconnector\monetize\templatic-registration\ajax_check_user_email.php */
function tmpl_ajax_check_user_email()
{
	require(ABSPATH."wp-load.php");	
	global $wpdb,$current_user;
	if(isset($_REQUEST['user_email']) && $_REQUEST['user_email']!= '' )
	{
		$user_email = $_REQUEST['user_email'];
		$cur_user_email = $current_user->user_email;	
		if($cur_user_email != $user_email){
			$count_email =  email_exists($user_email); /* check email id registered/valid */
		}
		echo $count_email.",email";exit;
	}
	elseif(isset($_REQUEST['user_fname']) && $_REQUEST['user_fname']!= '')
	{
		$user_fname = $_REQUEST['user_fname'];
		$cur_user_login = $current_user->user_login;	
		if($cur_user_login != $user_fname){
			$user = get_user_by('login',$user_fname);
		}
		$count_fname = count($user->ID);
		echo $count_fname.",fname";exit;
	}
}
/* user cutom fields array*/
function fetch_user_custom_fields(){	
	global $wpdb,$custom_post_meta_db_table_name,$current_user,$form_fields_usermeta;
	
	$args = array(
				'post_type'       => 'custom_user_field',
				'post_status'     => 'publish',
				'numberposts'	   => -1,
				'meta_key'        => 'sort_order',
				'orderby'         => 'meta_value_num',
				'meta_value_num'  => 'sort_order',
				'order'           => 'ASC'
			);
	$custom_metaboxes_fields = get_posts( $args );
	if(isset($custom_metaboxes_fields) && $custom_metaboxes_fields != '')
	{
		$form_fields_usermeta_usermeta = array();
		foreach($custom_metaboxes_fields as $custom_metaboxes)
		{
			$name            = $custom_metaboxes->post_name;
			$site_title      = stripslashes($custom_metaboxes->post_title);
			$type            = get_post_meta($custom_metaboxes->ID,'ctype',true);
			$default_value   = get_post_meta($custom_metaboxes->ID,'default_value',true);
			$is_require      = get_post_meta($custom_metaboxes->ID,'is_require',true);
			$admin_desc      = $custom_metaboxes->post_content;
			$option_values   = get_post_meta($custom_metaboxes->ID,'option_values',true);
			$option_titles   = get_post_meta($custom_metaboxes->ID,'option_titles',true);
			$on_registration = get_post_meta($custom_metaboxes->ID,'on_registration',true);
			$on_profile      = get_post_meta($custom_metaboxes->ID,'on_profile',true);
			$on_author_page  = get_post_meta($custom_metaboxes->ID,'on_author_page',true);
			
			if(is_admin())
			{
				$label      = '<tr><th>'.$site_title.'</th>';
				$outer_st   = '<table class="form-table">';
				$outer_end  = '</table>';
				$tag_st     = '<td>';
				$tag_end    = '<span class="message_note">'.$admin_desc.'</span></td></tr>';
				$tag_before = '';
				$tag_after  = '';
			} else {
				$label      = $site_title;
				$outer_st   = '<div class="form_row clearfix">';
				$outer_end  = '</div>';
				$tag_st     = '';
				$tag_end    = '<span class="message_note">'.$admin_desc.'</span>';
				$tag_before = '';
				$tag_after  = '';
			}
			
			if($type == 'text')
			{
				$form_fields_usermeta[$name] = array("label"		        => $label,
												"type"		   => 'text',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="textfield"',
												"is_require"	   => $is_require,
												"outer_st" 	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
											);
			}
			if($type == 'head')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'head',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="head"',
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
											);
			}
			elseif($type == 'checkbox')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'checkbox',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="checkbox"',
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'textarea')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'textarea',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="textarea"',
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'texteditor')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'texteditor',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="mce"',
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => '<div class="clear">',
												"tag_after"       => '</div>',
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'select')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'select',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'"',
												"options"	        => $option_values,
												"option_titles"   => $option_titles,
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'radio')
			{
				
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'radio',
												"default"	        => $default_value,
												"extra"		   => '',
												"options"	        => $option_values,
												"option_titles"   => $option_titles,
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => '',
												"tag_after"       => '',
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'multicheckbox')
			{
				
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'multicheckbox',
												"default"	        => $default_value,
												"extra"		   => '',
												"options"	        =>  $option_values,
												"option_titles"   => $option_titles,
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => '<div class="form_cat">',
												"tag_after"       => '</div>',
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'date')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'date',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" size="25" class="textfield_date"',
												"is_require"	   => $is_require,
												"outer_st" 	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,												
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'upload')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'upload',
												"default"	        => $default_value,
												"extra"		   => 'id="'.$name.'" class="textfield"',
												"is_require"	   => $is_require,
												"outer_st"	   => $outer_st,
												"outer_end"	   => $outer_end,
												"tag_st"	        => $tag_st,
												"tag_end"	        => $tag_end,
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);
			}
			elseif($type == 'head')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => $label,
												"type"		   => 'head',
												"outer_st"	   => '<h1 class="form_title">',
												"outer_end"	   => '</h1>',
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page
												);
			}
			elseif($type == 'geo_map')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => '',
												"type"		   => 'geo_map',
												"default"	        => $default_value,
												"extra"		   => '',
												"is_require"	   => $is_require,
												"outer_st"	   => '',
												"outer_end"	   => '',
												"tag_st"	        => '',
												"tag_end"	        => '',
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);		
			}
			elseif($type == 'image_uploader')
			{
				$form_fields_usermeta[$name] = array(
												"label"		   => '',
												"type"		   => 'image_uploader',
												"default"	        => $default_value,
												"extra"		   => '',
												"is_require"	   => $is_require,
												"outer_st"	   => '',
												"outer_end"	   => '',
												"tag_st"	        => '',
												"tag_end"	        => '',
												"tag_before"      => $tag_before,
												"tag_after"       => $tag_after,
												"on_registration" => $on_registration,
												"on_profile"	   => $on_profile,
												"on_author_page"  => $on_author_page,
												);		
			}
			
				
		}
		
		return $form_fields_usermeta;
	}/* finish if condition */
	
}
/*
	check user name while login.
*/
add_action( 'wp_ajax_nopriv_ajaxcheckusername', 'ajaxcheckusername' );
function ajaxcheckusername(){
	header('Content-Type: application/json; charset=utf-8');
 
 	$info = array();
    $info['user_login'] = $_POST['username'];
   
   	$user = get_user_by('login',$_POST['username']);
	
	echo $count_fname = count($user->ID);
    
    die();
}