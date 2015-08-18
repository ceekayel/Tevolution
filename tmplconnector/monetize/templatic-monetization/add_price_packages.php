<script type="text/javascript">
function showcategory(str,scat)
{  	
	if (str=="")
	  {
	  document.getElementById("field_category").innerHTML="";
	  return;
	  }else{
	  document.getElementById("field_category").innerHTML="";
	  document.getElementById("process").style.display ="block";
	  }
		if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
		else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
		xmlhttp.onreadystatechange=function()
	  {
	    if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		 document.getElementById("process").style.display ="none";
		 document.getElementById("field_category").innerHTML=xmlhttp.responseText;
		}
	  } 
	   var valarr = '';
	  if(str == 'all,all')
	    {
			var valspl = str.split(",");
			valarr = valspl[1];
		}
	  else
	    {
			var val = [];
			var valfin = '';			
			jQuery("input[name='package_post_type[]']").each(function() {
				if (jQuery(this).attr('checked'))
				{	
					val = jQuery(this).val();
					valfin = val.split(",");
					valarr+=valfin[1]+',';
				}
			});
			
		}
		if(valarr==''){ valarr ='all'; }
	  url = "<?php echo plugin_dir_url( __FILE__ ); ?>ajax_categories_dropdown.php?post_type="+valarr+'&scats='+scat+'&page=monetization&is_ajax=1'
	  xmlhttp.open("GET",url,true);
	  xmlhttp.send();
}
function selectall_price_package_posttype()
{
	dml = document.forms['monetization'];
	chk = dml.elements['package_post_type[]'];
	len = dml.elements['package_post_type[]'].length;
	
	if(document.getElementById('selectall_post_type').checked == true) { 
		for (i = 0; i < len; i++)
		chk[i].checked = true ;
	} else { 
		for (i = 0; i < len; i++)
		chk[i].checked = false ;
	}
}
</script>
<script type="text/javascript" src="<?php echo TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-monetization/add_package_validations.js';?>"></script>
<?php global $wpdb,$post;
if(isset($_REQUEST['package_id']) && $_REQUEST['package_id'] !== '')
{
	$pkid = $_REQUEST['package_id'];
	$package_id = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = '".$pkid."' AND post_status = 'publish'");
	$id = $package_id[0]->ID;
} ?>
<div class="wrap">	
	<div class="tevo_sub_title">
	<?php 
	if(isset($_REQUEST['action']) && $_REQUEST['action'] =='edit'){
		echo __('Edit Package',ADMINDOMAIN);
	}else{
		echo ADD_NEW; 
	}?>
	<a id="back_to_list" href="<?php echo site_url();?>/wp-admin/admin.php?page=monetization&tab=packages" name="btnviewlisting" class="add-new-h2" title="<?php echo BACK_LINK_TEXT; ?>"/><?php echo BACK_LINK_TEXT; ?></a>
	</div>
	<p class="tevolution_desc"><?php echo ADD_NEW_PACKAGE_DESC;?>.</p>
	
	<form action="<?php echo site_url();?>/wp-admin/admin.php?page=monetization&action=add_package&tab=packages" method="post" name="monetization" id="monetization" onsubmit="return check_frm();" >
	<input type="hidden" name="package_id" value="<?php if(isset($_REQUEST['package_id']) && $_REQUEST['package_id'] !== '') { echo $_REQUEST['package_id']; } ?>">
	
	<table style="width:60%"  class="form-table" id="form_table_monetize">
	<thead>
		<tr>
			<th colspan="2"><div class="tevo_sub_title"><?php echo MONETIZATION_SETTINGS; ?></div></th>
		</tr>
	</thead>
	<tbody>
		<tr class="" id="package_type">
			<th valign="top">
				<label for="package_type"><?php echo PACKAGE_TYPE; ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
			</th>
			
			<?php
				if(isset($id) && $id != ''){
					if(get_post_meta($id,'package_type',true) == '1')
					{
						$show_desc = 'style=display:block;';
						$show_desc2 = 'style=display:none;';
					}else{
						$show_desc2 = 'style=display:block;';
					}
				}else{
					$show_desc2 = 'style=display:none;';
				}
			?>
			<td>
				<input type="radio" class="form-radio radio" value="1" name="package_type" id="pay_per_post" <?php if((isset($id) && $id != '') && @get_post_meta($id,'package_type',true) == '1') { echo  "checked=checked"; }elseif(!@$id){ echo  "checked=checked"; } ?> onclick="showlistpost(this);" />&nbsp;<label for="pay_per_post"><?php echo PAY_PER_POST; ?></label>
				&nbsp;
				<input type="radio" class="form-radio radio" value="2" name="package_type" id="pay_per_sub" <?php if((isset($id) && $id != '') && get_post_meta($id,'package_type',true) == '2') { echo  "checked=checked"; }?> onclick="showlistpost(this);" />&nbsp;<label for="pay_per_sub"><?php echo PAY_PER_SUB; ?></label></br>
				<p id="pay_per_post_desc" class="description" <?php echo @$show_desc; ?> ><?php echo PER_POST_DESC; ?></p>
				<p id="pay_per_sub_desc" class="description" <?php echo @$show_desc2; ?>><?php echo PER_SUBSCRIPTION_DESC; ?></p>
			</td>
		</tr>
        <tr id="number_of_post" <?php if((isset($id) && $id != '') && get_post_meta($id,'package_type',true) == '2'):?> style="display:'';"<?php else:?> style="display:none;"<?php endif;?>>
        <th valign="top"><label for="limit_no_post"><?php echo LIMIT_NO_POST;?><span class="required"><?php echo REQUIRED_TEXT; ?></span></label></th>
           <td>
            <input type="text" name="limit_no_post" value="<?php if((isset($id) && $id != '') && get_post_meta($id,'limit_no_post',true) !="") { echo  get_post_meta($id,'limit_no_post',true); }?>"  id="limit_no_post" /><br />
                <p class="description"><?php echo NO_POST_DESC; ?>.</p>
           </td>
        </tr>
		<tr class="" id="package_title">
			<th valign="top">
				<label for="package_title" class="form-textfield-label"><?php echo PACKAGE_TITLE; ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
			</th>
			<td>
				<input type="text" class="regular-text" value="<?php if(isset($package_id[0]) && $package_id[0] != '') { echo $package_id[0]->post_title; } ?>" name="package_name" id="package_name" />
				<br/><p class="description"><?php echo PACKAGE_NAME_DESC; ?>.</p>
			</td>
		</tr>
		<tr>
			<th valign="top">
				<label for="package_desc" class="form-textfield-label"><?php echo PACKAGE_DESC_TITLE; ?></label>
			</th>
			<td>
				<textarea name="package_desc" cols="50" rows="5" id="title_desc"><?php if(isset($package_id[0]) && $package_id[0] != '') { echo stripslashes($package_id[0]->post_content); } ?></textarea><br/><p class="description"><?php echo PACKAGE_DESC; ?>.</p>
			</td>
		</tr>
		<tr>
			<th valign="top">
				<label for="package_post_type" class="form-textfield-label"><?php echo SELECT_POST_TYPES; ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
			</th>
			<td>
               <?php $post_types = get_option('templatic_custom_post');	
				$pkg_post_type = array();
				if(isset($id) && $id != '') { 
					$pctype = get_post_meta($id,"package_post_type",true);
					$pkg_post_type = explode(',',$pctype); 		
					$scats = get_post_meta($id,"category",true);	
					if($scats ==''){
						$scats ='0';
					}					
				}
			?>
               
               	<fieldset>				
				<label for="selectall_post_type"><input type="checkbox" name="package_post_type[]" id="selectall_post_type" onClick="showcategory(this.value,'<?php echo $scats; ?>');selectall_price_package_posttype();" value="all,all"  <?php if(in_array('all',$pkg_post_type)){ echo 'checked="checked"';}?>/>&nbsp;<?php echo __('Select All', ADMINDOMAIN);?></label><br />
				
                    <label for="post_type_post"><input type="checkbox" name="package_post_type[]" id="post_type_post" onClick="showcategory(this.value,'<?php echo $scats; ?>');" value="post,category" <?php if(in_array('post',$pkg_post_type) || in_array('all',$pkg_post_type)){ echo 'checked="checked"';}?> />&nbsp;<?php echo 'Post';?></label><br />
				<?php
				$i=1;			
				foreach ($post_types as $key => $post_type) {	
					$slugs = $post_type['slugs'][0];
					?>
						
					<label for="post_type_<?php echo $i; ?>"><input type="checkbox" name="package_post_type[]" id="post_type_<?php echo $i; ?>" onClick="showcategory(this.value,'<?php echo $scats; ?>');" value="<?php echo $key.",".$slugs; ?>" <?php if(in_array($key,$pkg_post_type) || in_array('all',$pkg_post_type)){ echo 'checked="checked"';}?>/>
						<?php echo $post_type['label'];?></label><br />
						
				<?php				
				$i++;	
				} ?>
                    </fieldset>               
				
			</td>
		</tr>
		<tr>
			<th valign="top">
				<label for="package_categories" class="form-textfield-label"><?php echo PACKAGE_CATEGORIES; ?> </label>
			</th>
			<td>
				<div class="element cf_checkbox wp-tab-panel" id="field_category">
				<label for="selectall"><input type="checkbox" name="selectall" id="selectall" class="checkbox" onclick="displaychk_frm();" />&nbsp;<?php if(is_admin()){  echo __('Select All',	ADMINDOMAIN); }else{ _e('Select All',	DOMAIN); } ?></label>
					<ul id="category_checklist" data-wp-lists="list:listingcategory" class="categorychecklist form_cat">
					<?php 
					$pctype = '';
					if(isset($id) && $id != '')
					{
						$pctype = get_post_meta($id,"package_post_type",true);
						$post_type = explode(',',$pctype);
						
						$tax = get_post_meta($id,"category",true);
						$pid = explode(',',$tax);
						$pkg_id = $_REQUEST['$post_id'];
					
						if(in_array('all',$post_type))
						{
							tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>'category','popular_cats' => $popular_ids,'selected_cats'=>$pid ) );
							foreach ($post_types as $key => $post_type)
							{
								//get_wp_category_checklist_plugin($post_type['slugs'][0],$pid);
			
								$taxonomy = $post_type['slugs'][0];
								echo "<li><label style='font-weight:bold;'>".$post_type['taxonomies'][0]."</label></li>";
								tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>$taxonomy,'popular_cats' => $popular_ids,'selected_cats'=>$pid ) );
							}
						}
						else
						{
							foreach ($post_types as $key => $post_type)
							{
								if(in_array($key,$pkg_post_type)){
									//get_wp_category_checklist_plugin($post_type['slugs'][0],$pid);
									
									$taxonomy = $post_type['slugs'][0];
									echo "<li><label style='font-weight:bold;'>".$post_type['taxonomies'][0]."</label></li>";
									tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>$taxonomy,'popular_cats' => $popular_ids,'selected_cats'=>$pid  ) );
								}
							}
						}
					}
					else
					{
						tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>'category','popular_cats' => $popular_ids,'selected_cats'=>$pid  ));
						foreach ($post_types as $key => $post_type)
						{ 
							//get_wp_category_checklist_plugin($post_type['slugs'][0],'');
							echo "<li><label style='font-weight:bold;'>".$post_type['taxonomies'][0]."</label></li>";
							tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>$post_type['slugs'][0],'popular_cats' => $popular_ids,'selected_cats'=>$pid  ) );
						}
					} ?>
					</ul>
				</div>
				<span id='process' style='display:none;'><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/process.gif" alt='Processing..' /></span>
				
			</td>
		</tr>
		<?php if(!is_plugin_active( 'Tevolution-FieldsMonetization/fields_monetization.php')) {?>
		<tr>
			<th valign="top">
				<label for="show_package" class="form-textfield-label"><?php echo SHOW_PACKAGE; ?></label>
			</th>
			<td>
				<input type="checkbox" name="show_package" id="show_package" value="1" <?php if(isset($id) && $id != '' && get_post_meta($id, 'show_package', true) == 1){ echo 'checked=checked'; } ?> />
				&nbsp;<label for="show_package"><?php echo SHOW_PACKAGE_TITLE; ?></label><br/>
				<p class="description"><?php echo SHOW_PACKAGE_DESC;?>.</p>
			</td>
		</tr>
		<?php
		}do_action('fields_monetization',$id); ?>
		<tr class="" id="package_price">
			<th valign="top">
				<label for="package_amount" class="form-textfield-label"><?php echo PACKAGE_AMOUNT; ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
			</th>
			<td>
				<input type="text" name="package_amount" id="package_amount" value="<?php if(isset($id) && $id != '') { echo get_post_meta($id, 'package_amount', true); } ?>">
				<br/><p class="description"><?php echo PRICE_AMOUNT_DESC;?>.</p>
			</td>
		</tr>
		<?php $recurring = @get_post_meta($id, 'recurring', true); ?>
		<tr class="" id="billing_period" <?php if($recurring == 1) { ?>style="display:none;";<?php } ?>>
			<th valign="top">
				<label for="billing_period" class="form-textfield-label"><?php echo BILLING_PERIOD; ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
			</th>
			<td>
				<input type="text" class="billing_num" name="validity" id="validity" value="<?php if(isset($id) && $id != '') { echo get_post_meta($id, 'validity', true); } ?>">
				<select name="validity_per" id="validity_per" class="textfield billing_per">
					<option value="D" <?php if(isset($id) && $id != '' && get_post_meta($id, 'validity_per', true) == 'D'){ echo 'selected="selected"';}?>><?php echo DAYS_TEXT; ?></option>
					<option value="M" <?php if(isset($id) && $id != '' && get_post_meta($id, 'validity_per', true) == 'M'){ echo 'selected="selected"';}?>><?php echo MONTHS_TEXT; ?></option>
					<option value="Y" <?php if(isset($id) && $id != '' && get_post_meta($id, 'validity_per', true) == 'Y'){ echo 'selected="selected"';}?>><?php echo YEAR_TEXT; ?></option>
				</select><br/>
				<p class="description"><?php echo BILLING_PERIOD_DESC;?></p>
			</td>
		</tr>
		<tr class="">
			<th valign="top">
				<label for="package_status" class="form-textfield-label"><?php echo PACKAGE_STATUS; ?></label>
			</th>
			<td>
				<input type="checkbox" name="package_status" id="package_status" value="1" <?php if(isset($id) && $id != '' && get_post_meta($id, 'package_status', true) == 1){ echo 'checked=checked'; } ?> />
				&nbsp;<label for="package_status"><?php echo __("Yes",ADMINDOMAIN); ?></label><br/>
                <p class="description"><?php echo __('Check marking it will activate this package for the post categories selected above on your site.',ADMINDOMAIN);?></p>
			</td>
		</tr>
		<tr>
			<th valign="top">
				<label for="is_recurring" class="form-textfield-label" style="width:100px;"><?php echo IS_RECURRING; ?>?</label>
			</th>
			 <?php if( @get_post_meta($id, 'recurring', true) == 1) { $checked = "checked=checked"; }else{ $checked = " "; } ?>
			<td>
				<label><input type="checkbox" name="recurring" id="recurring"  value='1' onclick="rec_div_show(this.id)" <?php echo $checked ; ?>/>&nbsp; <?php echo YES; ?></label>
				<br/>
				<p class="description"><?php echo RECURRING_DESC;?><b><?php echo __("Works only with PayPal!",ADMINDOMAIN);?></b></p>
			</td>
		</tr>
		<tr id="rec_tr" <?php if((isset($id) && get_post_meta($id, 'recurring', true) == 0)  || (!isset($id) || $id == '')){ echo 'style="display:none;"'; }?>>
			<th valign="top">
				<label for="recurring_billing" class="form-textfield-label"><?php echo RECURRING_BILLING_PERIOD; ?></label>
			</th>
			<td>
				<span class="option_label"><?php echo CHARGE_USER; ?> </span>
				<input type="text" class="textfield billing_num" name="billing_num" id="billing_num" value="<?php if(isset($id) && $id != '') { echo get_post_meta($id, 'billing_num', true); } ?>">
				<select name="billing_per" id="billing_per" class="textfield billing_per">
					<option value="D" <?php if(isset($id) && $id != '' && get_post_meta($id, 'billing_per', true) =='D'){ echo 'selected=selected';}?> ><?php echo DAYS_TEXT; ?></option>
					<option value="M" <?php if(isset($id) && $id != '' && get_post_meta($id, 'billing_per', true) =='M'){ echo 'selected=selected';}?> ><?php echo MONTHS_TEXT; ?></option>
					<option value="Y" <?php if(isset($id) && $id != '' && get_post_meta($id, 'billing_per', true) =='Y'){ echo 'selected=selected';}?> ><?php echo YEAR_TEXT; ?></option>
				</select><br/>
				<p class="description"><?php echo RECURRING_BILLING_PERIOD_DESC; ?>.</p>
			</td>
		</tr>
		<tr id="rec_tr1" <?php if((isset($id) && get_post_meta($id, 'recurring', true) == 0)  || (!isset($id) || $id == '')){ echo 'style="display:none;"'; }?>>
			<th valign="top">
				<label for="billing_cycle" class="form-textfield-label"><?php echo RECURRING_BILLING_CYCLE; ?></label>
			</th>
			<td>
				<input type="text" class="textfield" name="billing_cycle" id="billing_cycle" value="<?php if(isset($id) && $id != '') { echo get_post_meta($id, 'billing_cycle', true); } ?>"><br/><p class="description"><?php echo RECURRING_BILLING_CYCLE_DESC; ?>.</p>
			</td>
		</tr>
        <tr id="rec_tr2" <?php if((isset($id) && get_post_meta($id, 'recurring', true) == 0)  || (!isset($id) || $id == '')){ echo 'style="display:none;"'; }?>>
        	<th valign="top"><label class="form-textfield-label"><?php echo __('Free trial period',ADMINDOMAIN)?></label></th>
        	<td>
            	<label for="first_free_trail_period">
                	<input id="first_free_trail_period" type="checkbox" name="first_free_trail_period" value="1" <?php if(isset($id) && $id != '' && get_post_meta($id, 'first_free_trail_period', true) =='1'){ echo 'checked=checked';}?>  />&nbsp;<?php echo __('Enable',ADMINDOMAIN);?>
                </label>                
                <p class="description"><?php echo __('With this enabled the first period of the subscription will be free. For the second period the user will be charged the amount you specified above. This only works with PayPal. ',ADMINDOMAIN);?></p>
            </td>
        </tr>
		<?php do_action('add_new_row_pricepackages',$id); ?>
	</tbody>
	
	<thead>
		<tr>
			<th colspan="2"><div class="tevo_sub_title"><?php echo SETTINGS_FOR_FEATURED; ?></div>
			<p class="tevolurion_desc"><?php echo SETTINGS_FOR_FEATURED_DESC; ?>.</p></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th valign="top">
				<label for="is_featured" class="form-textfield-label"><?php echo IS_FEATURED; ?>?</label>
			</th>
			<td>
				<label for="is_featured"><input type="checkbox" name="is_featured" id="is_featured" value="1" <?php if(isset($id) && $id != '' && get_post_meta($id, 'is_featured', true) == 1){ echo 'checked=checked'; } ?> onClick="show_featured_package(this.id);" />&nbsp;
				<?php echo __("Yes",ADMINDOMAIN); ?></label><br/>
				<p class="description"><?php echo FEATURED_STATUS_DESC; ?>.</p>
			</td>
		</tr>
		<tr id="featured_home" <?php if((isset($id) && get_post_meta($id, 'is_featured', true) == 0)  || (!isset($id) || $id == '')) { echo 'style="display:none;"'; } ?>>
			<th valign="top">
				<label for="feature_amount" class="form-textfield-label"><?php echo FEATURED_AMOUNT_HOME; ?></label>
			</th>
			<td>
				<input type="text" name="feature_amount" id="feature_amount" value="<?php if(isset($id) && $id != '' &&get_post_meta($id, 'feature_amount', true) != "") { echo get_post_meta($id, 'feature_amount', true); } ?>">
			</td>
		</tr>
		<tr id="featured_cat" <?php if((isset($id) && get_post_meta($id, 'is_featured', true) == 0)  || (!isset($id) || $id == '')){ echo 'style="display:none;"'; } ?>>
			<th valign="top">
				<label for="feature_cat_amount" class="form-textfield-label"><?php echo FEATURED_AMOUNT_CAT; ?></label>
			</th>
			<td>
				<input type="text" name="feature_cat_amount" id="feature_cat_amount" value="<?php if(isset($id) && $id != '' &&get_post_meta($id, 'feature_cat_amount', true) != "") { echo get_post_meta($id, 'feature_cat_amount', true); } ?>">
			</td>
		</tr>
		
	<?php
	if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
	?>
	<thead>
		<tr>
			<th colspan="2"><div class="tevo_sub_title"><?php echo SETTINGS_FOR_THOUGHTFUL_COMMENT; ?></div><br/>
			<span class="tevo_desc"><?php echo SETTINGS_FOR_THOUGHTFUL_COMMENT_DESC; ?>.</span></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th valign="top">
				<label for="can_author_mederate" class="form-textfield-label"><?php echo CAN_AUTHOR_MODERATE; ?></label>
			</th>
			<td>
				<label for="can_author_mederate"><input type="checkbox" name="can_author_mederate" id="can_author_mederate" value="1" <?php if(isset($id) && $id != '' && get_post_meta($id, 'can_author_mederate', true) == 1){ echo 'checked=checked'; } ?> onClick="show_comment_package(this.id);"/>&nbsp;
				<?php echo YES; ?></label><br/>
				<p class="description"><?php echo THOUGHTFUL_COMMENT_STATUS_DESC; ?>.</p>
			</td>
		</tr>
		<tr id="comment_moderation_charge" <?php if((isset($id) && get_post_meta($id, 'can_author_mederate', true) == 0)  || (!isset($id) || $id == '')) { echo 'style="display:none;"'; } ?>>
			<th valign="top">
				<label for="comment_mederation_amount" class="form-textfield-label"><?php echo THOUGHTFUL_COMMENT_CHARGE; ?></label>
			</th>
			<td>
				<input type="text" name="comment_mederation_amount" id="comment_mederation_amount" value="<?php if(isset($id) && $id != '' && get_post_meta($id, 'comment_mederation_amount', true) != "") { echo get_post_meta($id, 'comment_mederation_amount', true); } ?>">
			</td>
		</tr>
	</tbody>
	<?php } ?>
		<tr>
			<td colspan="2"><input type="submit" class="button-primary form-submit form-submit submit" value="<?php echo __('Save Settings',ADMINDOMAIN); ?>" name="submit" id="submit-1"></td>
		</tr>
	</tbody>
	</table>
	</form>
</div>
<?php
/* POSTING PACKAGE DATA TO THE DATABASE */
if(isset($_POST['package_name']) && isset($_REQUEST['action']) && $_REQUEST['action'] == 'add_package')
{
	/* CALL A FUNCTION TO INSERT DATA INTO DATABASE */
	global $monetization;
	
	$monetization->insert_package_data($_POST);
}
?>
