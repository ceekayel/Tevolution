<?php
/*
 * add/edit user custo field
 */
global $wpdb,$current_user;
$post_id = @$_REQUEST['cf'];
$post_val = get_post($post_id);
if(isset($_POST['save_user']) && $_POST['save_user'] != "")
{
	$my_post = array();
	$my_post['post_title']   = $_POST['site_title'];
	$my_post['post_name']    = $_POST['htmlvar_name'];
	$my_post['post_content'] = $_POST['admin_desc'];
	$my_post['post_status']  = (isset($_POST['is_active']))? $_POST['is_active']: 'draft';
	$my_post['post_type']    = 'custom_user_field';
	$custom = array("ctype"		     => $_POST['ctype'],
				 "sort_order" 		=> $_POST['sort_order'],
				 "option_values"	=> $_POST['option_values'],
				 "option_titles"    => $_POST['option_titles'],
				 "is_require"		=> (isset($_POST['is_require']))? $_POST['is_require']:0,
				 "on_registration"	=> (isset($_POST['on_registration']))? $_POST['on_registration'] :0,
				 "on_profile"		=> (isset($_POST['on_profile']))? $_POST['on_profile']:0,
				 "on_author_page"	=> (isset($_REQUEST['on_author_page']))? $_REQUEST['on_author_page']:0,
			);
	if($_REQUEST['cf'])
	{
		$cf = $_REQUEST['cf'];
		$my_post['ID'] = $_REQUEST['cf'];
		$last_postid = wp_insert_post( $my_post );
		$msgtype = 'edit-suc';
	}else
	{
		$last_postid = wp_insert_post( $my_post );
		$msgtype = 'add-suc';
	}
	/* Finish the place geo_latitude and geo_longitude in postcodes table*/
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		if(function_exists('wpml_insert_templ_post'))
			wpml_insert_templ_post($last_postid,'custom_user_field'); /* insert post in language */
	}
	foreach($custom as $key=>$val)
	{				
		update_post_meta($last_postid, $key, $val);
	}
	
	$url = site_url().'/wp-admin/admin.php';
	echo '<form action="'.$url.'#option_display_custom_usermeta" method="get" id="frm_edit_customuser_fields" name="frm_edit_customuser_fields">
	<input type="hidden" value="custom_setup" name="page"><input type="hidden" value="user_custom_fields" name="ctab"><input type="hidden" value="'.$msgtype.'" name="msgtype">
	</form>
	<script>document.frm_edit_customuser_fields.submit();</script>
	';exit;
}
?>
<script type="text/javascript" src="<?php echo TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-registration/add_user_custom_fields_validations.js';?>"></script>
<div class="wrap">
<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h2>
	<?php if(isset($_REQUEST['cf']) && $_REQUEST['cf'])
		 {  
		 	echo __('Edit Custom User Meta',ADMINDOMAIN); 
		 }else{
			echo __('Add a field for users&rsquo; profile',ADMINDOMAIN); 			
		 }?>
	  	<a href="<?php echo site_url();?>/wp-admin/admin.php?page=custom_setup&ctab=user_custom_fields" name="btnviewlisting" id="edit_custom_user_custom_field" class="add-new-h2" title="<?php echo __('Back to Manage fields list',ADMINDOMAIN);?>">
			<?php echo __('Back to Manage fields list',ADMINDOMAIN); ?>
		</a> 
    </h2>
	<form class="form_style" action="<?php echo site_url();?>/wp-admin/admin.php?page=custom_setup&ctab=user_custom_fields&action=addnew" method="post" name="custom_fields_frm" onsubmit="return chk_userfield_form();">	
	
	<input type="hidden" name="save" value="1" /> 
	<?php if(isset($_REQUEST['cf']) && $_REQUEST['cf']){?>
	<input type="hidden" name="cf" value="<?php echo $_REQUEST['cf'];?>" />
	<?php }?>
	<input type="hidden" name="post_type" id="post_type" value="registration" />
	<input type="hidden" name="clabels" id="clabels" value="<?php if(isset($post_val->clabels)) { echo $post_val->clabels; } ?>" />
	<input type="hidden" name="default_value" id="default_value" value="<?php if(isset($post_val->default_value)) { echo $post_val->default_value; } ?>" />
	<input type="hidden" name="admin_title" id="admin_title" value="<?php if(isset($post_val->admin_title)) { echo $post_val->admin_title; } ?>" />
	
	<table class="form-table" style="width:50%;" id="form_table_user_custom_field">       
          <tbody>
               <!-- field type start -->
               <tr style="display:block;" >
                    <th>
                    <label for="field_type" class="form-textfield-label"><?php echo __('Field type',ADMINDOMAIN);?></label>
                    </th>
                    <td>
                         <select name="ctype" id="ctype" onchange="usershow_option_add(this.value)" >
                              <option value="date" <?php if(get_post_meta($post_id,"ctype",true)=='date'){ echo 'selected="selected"';}?>><?php echo __('Date Picker',ADMINDOMAIN);?></option>
                              <option value="multicheckbox" <?php if(get_post_meta($post_id,"ctype",true)=='multicheckbox'){ echo 'selected="selected"';}?>><?php echo __('Multi Checkbox',ADMINDOMAIN);?></option>
                              <option value="radio" <?php if(get_post_meta($post_id,"ctype",true)=='radio'){ echo 'selected="selected"';}?>><?php echo __('Radio',ADMINDOMAIN);?></option>
                              <option value="select" <?php if(get_post_meta($post_id,"ctype",true)=='select'){ echo 'selected="selected"';}?>><?php echo __('Select',ADMINDOMAIN);?></option>
                              <option value="text" <?php if(get_post_meta($post_id,"ctype",true)=='text'){ echo 'selected="selected"';}?>><?php echo __('Text',ADMINDOMAIN);?></option>
                              <option value="textarea" <?php if(get_post_meta($post_id,"ctype",true)=='textarea'){ echo 'selected="selected"';}?>><?php echo __('Textarea',ADMINDOMAIN);?></option>
                              <option value="texteditor" <?php if(get_post_meta($post_id,"ctype",true)=='texteditor'){ echo 'selected="selected"';}?>><?php echo __('Text Editor',ADMINDOMAIN);?></option>
                              <option value="head" <?php if(get_post_meta($post_id,"ctype",true)=='head'){ echo 'selected="selected"';}?>><?php echo __('Text Heading',ADMINDOMAIN);?></option>
                              <option value="upload" <?php if(get_post_meta($post_id,"ctype",true)=='upload'){ echo 'selected="selected"';}?>><?php echo __('Upload',ADMINDOMAIN);?></option>
                         </select>
                    </td>
               </tr>
			<!-- field type end -->
		
               <!-- option value start -->
               <tr id="ctype_option_tr_id"  <?php if(get_post_meta($post_id,"ctype",true)=='select'){?> style="display:block;" <?php }else{?> style="display:none;" <?php }?> >
                    <th><?php echo __('Option values',ADMINDOMAIN);?></th>
                    <td> 
                    	<input type="text" class="regular-text" name="option_values" id="option_values" value="<?php echo get_post_meta($post_id,"option_values",true);?>" size="50" />
	                    <p class="description"><?php echo __('Separate multiple option values with a comma. eg. Yes, No(Do not add space after comma)',ADMINDOMAIN);?></p>
                    </td>
               </tr>
                <tr id="ctype_titles_tr_id"  <?php if(get_post_meta($post_id,"ctype",true)=='select'){?> style="display:block;" <?php }else{?> style="display:none;" <?php }?> >
                    <th><?php echo __('Option titles',ADMINDOMAIN);?></th>
                    <td> 
                    	<input type="text" class="regular-text" name="option_titles" id="option_titles" value="<?php echo get_post_meta($post_id,"option_titles",true);?>" size="50" />
	                    <p class="description"><?php echo __('Separate multiple option titles with a comma. eg. Yes, No',ADMINDOMAIN);?></p>
                    </td>
               </tr>
               <!-- option value end -->
		
               <!-- fieldname start -->
               <tr id="ctype_option_tr_id"  <?php if(get_post_meta($post_id,"ctype",true)=='select'){?> style="display:block;" <?php }else{?> style="display:block;" <?php }?> >
                    <th><?php echo __('Field name',ADMINDOMAIN);?></th>
                    <td>  
                    	<input type="text" class="regular-text" name="site_title" id="site_title" value="<?php if(isset($post_val->post_title)) { echo $post_val->post_title; } ?>" />
                    	<p class="description"><?php echo __('The name you enter here will be used in both the registration form and in the user dashboard.',ADMINDOMAIN);?></p>
                    </td>
               </tr>
               <!-- field name end -->
		
               <!-- field description start -->
               <tr id="ctype_option_tr_id"  <?php if(get_post_meta($post_id,"ctype",true)=='select'){?> style="display:block;" <?php }else{?> style="display:none;" <?php }?> >
                    <th><?php echo __('Field description',ADMINDOMAIN);?></th>
                    <td> 
                    	<input type="text" class="regular-text" name="admin_desc" id="admin_desc" value="<?php if(isset($post_val->post_content)) { echo $post_val->post_content; } ?>" />
                    	<p class="description"><?php echo __('Custom field description which will appear in the front-end as well as the backend.',ADMINDOMAIN);?></p>
                    </td>
               </tr>
               <!-- field description end -->
		
               <!-- htmlvar_name1 name start-->
               <tr id="htmlvar_name1" style="display:block;" >
                    <th><?php echo __('HTML variable name',ADMINDOMAIN);?></th>
                    <td>
                    	<input type="text" class="regular-text" name="htmlvar_name" id="htmlvar_name" value="<?php if(isset($post_val->post_name)) { echo $post_val->post_name; } ?>"<?php if(isset($_REQUEST['cf']) && $_REQUEST['cf']){?>readonly="readonly"<?php } ?> />
                    	<p class="description"><?php echo __('Enter a unique name for the field. Use only lowercase letters and numbers, no space allowed.',ADMINDOMAIN);?></p>
                    </td>
               </tr>
               <!-- htmlvar_name1 name end-->
		
               <!-- start order1 start-->
               <tr id="sort_order1" style="display:block;" >
                    <th><?php echo __('Position (Display order)',ADMINDOMAIN);?></th>
                    <td> 
                    	<input type="text" class="regular-text" name="sort_order" id="sort_order"  value="<?php echo get_post_meta($post_id,"sort_order",true);?>" />
                    	<p class="description"><?php echo __('Enter a number that will determine the position of the field inside the registration form, e.g., 5.',ADMINDOMAIN);?></p>
                    </td>
               </tr>
               <!-- start order1 end-->
		
               <!-- status start-->
               <tr id="sort_order1" style="display:block;" >
                    <th><?php echo __('Active',ADMINDOMAIN);?></th>
                    <td>  
                    	<input type="checkbox" name="is_active" id="is_active" value="publish" <?php if(isset($post_val->post_status) && $post_val->post_status=='publish'){ echo 'checked="checked"';}?> />&nbsp;<label for="is_active"><?php echo __('Yes',ADMINDOMAIN);?></label>                        
                         <p class="description"><?php echo __('Uncheck this box only if you want to create the field but not use it right away.',ADMINDOMAIN);?></p></td>
               </tr>
               <!-- status end-->
		
               <!-- Compulsory start -->
               <tr id="is_require_id"  <?php if(get_post_meta($post_id,"ctype",true)=='head'){?> style="display:none;" <?php }else{ ?>style="display:block;"<?php }?>>
                    <th><?php echo __('Compulsory',ADMINDOMAIN);?></th>
                    <td>
                    	<input type="checkbox" name="is_require" id="is_require" value="1"  <?php if(get_post_meta($post_id,"is_require",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="is_require"><?php echo __('Yes',ADMINDOMAIN);?></label>                         
                         <p class="description"><?php echo __("Check this option if this should be a required field.",ADMINDOMAIN);?></p>
                    </td>
               </tr>
               <!-- Compulsory end-->	
				
               <!-- on Registration page start -->               
               <tr style="display:block;">
               	<th><?php echo __('Show the field on',ADMINDOMAIN);?></th>
                    <td>
                    	
                         	<p id="for_profile"><input type="checkbox" name="on_profile" id="on_profile" value="1"  <?php if(get_post_meta($post_id,"on_profile",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="on_profile"><?php echo __('Edit profile page',ADMINDOMAIN);?></label></p>
                         	<p id="for_registration"><input type="checkbox" name="on_registration" id="on_registration" value="1"  <?php if(get_post_meta($post_id,"on_registration",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="on_registration"><?php echo __('Registration page',ADMINDOMAIN);?></label>
                            <p class="description"><?php echo __('Only username and email field will be shown in pop up register form.',ADMINDOMAIN); ?></p></p>
                            <p id="for_author"><input type="checkbox" name="on_author_page" id="on_author_page" value="1"  <?php if(get_post_meta($post_id,"on_author_page",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="on_author_page"><?php echo __('User dashboard page',ADMINDOMAIN);?></label></p>                             
                         
                    </td>                    
               </tr>
               <!--in authot box  page end-->
         
               <tr style="display:block">
               	<td class="save" colspan="2">
					<?php
					if(isset($_REQUEST['cf']) && $_REQUEST['cf'] !=''){
						$val = __('Update Changes',ADMINDOMAIN);
					}else{
						$val = __('Save all changes',ADMINDOMAIN);
					}
					?>
		               <input type="submit" class="button button-primary button-hero" name="save_user"  id="save" value="<?php echo $val; ?>" />
          	     </td>
               </tr>
		</tbody>
	</table>
	</form>
</div>
<script type="text/javascript">
function usershow_option_add(htmltype)
{
	if(htmltype=='select' || htmltype=='multiselect' || htmltype=='radio' || htmltype=='multicheckbox')
	{
		document.getElementById('ctype_option_tr_id').style.display='block';
		document.getElementById('ctype_titles_tr_id').style.display='block';
	}else
	{
		document.getElementById('ctype_option_tr_id').style.display='none';
		document.getElementById('ctype_titles_tr_id').style.display='none';
	}
	
	if(htmltype=='head')
	{
		document.getElementById('is_require_id').style.display='none';	
	}
	else
	{
		document.getElementById('is_require_id').style.display='block';	
	}
}
if(document.getElementById('ctype').value)
{
	usershow_option_add(document.getElementById('ctype').value)	;
}
</script>
