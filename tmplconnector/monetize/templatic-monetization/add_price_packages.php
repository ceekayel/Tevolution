<?php
/*
 * form for add/edit price package
 */
?>
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
		echo __('Edit Package','templatic-admin');
	}else{
		echo __('Add New Package','templatic-admin'); 
	}?>
	<a id="back_to_list" href="<?php echo site_url();?>/wp-admin/admin.php?page=monetization&tab=packages" name="btnviewlisting" class="add-new-h2" title="<?php echo __('Back to packages list','templatic-admin'); ?>"/><?php echo __('Back to packages list','templatic-admin'); ?></a>
	</div>
	
	<form action="<?php echo site_url();?>/wp-admin/admin.php?page=monetization&action=add_package&tab=packages" method="post" name="monetization" id="monetization" onsubmit="return check_frm();" >
	<input type="hidden" name="package_id" value="<?php if(isset($_REQUEST['package_id']) && $_REQUEST['package_id'] !== '') { echo $_REQUEST['package_id']; } ?>">
	
	<table style="width:60%"  class="form-table" id="form_table_monetize">
	
	<tbody>
		<?php do_action('admin_before_package_type',@$id); /*add action before package type*/ ?>
		<tr class="" id="package_type">
			<th valign="top">
				<label for="package_type"><?php echo __('Package Type','templatic-admin'); ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
			</th>
			
			<?php
				if(isset($id) && @$id != ''){
					if(get_post_meta(@$id,'package_type',true) == '1')
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
				<p id="pay_per_post_desc" class="description" <?php echo @$show_desc; ?> ><?php echo __(' Use Single submission if you want users to post once with this package. Use &raquoSubscription&raquo  to allow users to post a certain number of posts with this package.','templatic-admin'); ?></p>
				<p id="pay_per_sub_desc" class="description" <?php echo @$show_desc2; ?>><?php echo __('This option creates a subscription that allows members to submit a preset number of listings in a set amount of time.','templatic-admin'); ?></p>
			</td>
		</tr>
		<?php do_action('admin_after_package_type',@$id);/*add action aftet package type*/
			  do_action('admin_before_number_of_post',@$id); /*add action before number of post*/ ?>
        <tr id="number_of_post" <?php if((isset($id) && @$id != '') && get_post_meta(@$id,'package_type',true) == '2'):?> style="display:'';"<?php else:?> style="display:none;"<?php endif;?>>
        <th valign="top"><label for="limit_no_post"><?php echo __('Number of Posts','templatic-admin');?><span class="required"><?php echo REQUIRED_TEXT; ?></span></label></th>
           <td>
            	<input type="text" name="limit_no_post" value="<?php if((isset($id) && @$id != '') && get_post_meta(@$id,'limit_no_post',true) !="") { echo  get_post_meta(@$id,'limit_no_post',true); }?>"  id="limit_no_post" /><br />
    		    <p class="description"><?php echo __('Enter the number of posts members can submit with this price package, e.g. 10','templatic-admin'); ?>.</p>
           </td>
        </tr>
        <?php do_action('admin_after_number_of_post',@$id);/*add action after number of post*/
			  do_action('admin_before_package_title',@$id);/*add action before package title*/ ?>
		<tr class="" id="package_title">
			<th valign="top">
				<label for="package_title" class="form-textfield-label"><?php echo __('Title','templatic-admin'); ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
			</th>
			<td>
				<input type="text" class="regular-text" value="<?php if(isset($package_id[0]) && $package_id[0] != '') { echo $package_id[0]->post_title; } ?>" name="package_name" id="package_name" />
				<br/><p class="description"><?php echo __('The package name will be shown inside the submission form. Feel free to get creative','templatic-admin'); ?>.</p>
			</td>
		</tr>
		<?php do_action('admin_after_package_title',@$id);/*add action after package title*/
			  do_action('admin_before_package_desc',@$id);/*add action before package description*/ ?>
		<tr>
			<th valign="top">
				<label for="package_desc" class="form-textfield-label"><?php echo __('Package Description','templatic-admin'); ?></label>
			</th>
			<td>
				<textarea name="package_desc" cols="50" rows="5" id="title_desc"><?php if(isset($package_id[0]) && $package_id[0] != '') { echo stripslashes($package_id[0]->post_content); } ?></textarea><br/><p class="description"><?php echo __('In a few words, describe what this packages offers','templatic-admin'); ?>.</p>
			</td>
		</tr>
		<?php do_action('admin_after_package_desc',@$id);/*add action after package description*/
			  do_action('admin_before_package_post_type',@$id);/*add action before package post type*/ ?>
		<tr>
			<th valign="top">
				<label for="package_post_type" class="form-textfield-label"><?php echo __('Select Post Type','templatic-admin'); ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
			</th>
			<td>
               <?php 
			   $post_type = get_option('templatic_custom_post');
			   $post_types = apply_filters('tmpl_allow_monetize_posttype',$post_type);	
			 
				$pkg_post_type = array();
				if(isset($id) && $id != '') { 
					$pctype = get_post_meta($id,"package_post_type",true);
					$pkg_post_type = explode(',',$pctype); 		
					$scats = get_post_meta($id,"category",true);	
					if($scats ==''){
						$scats ='0';
					}					
				}	?>
               
               	<fieldset>				
				<label for="selectall_post_type"><input type="checkbox" name="package_post_type[]" id="selectall_post_type" onClick="showcategory(this.value,'<?php echo $scats; ?>');selectall_price_package_posttype();" value="all,all"  <?php if(in_array('all',$pkg_post_type)){ echo 'checked="checked"';}?>/>&nbsp;<?php echo __('Select All', 'templatic-admin');?></label><br />
				
                    <label for="post_type_post"><input type="checkbox" class="class_post_type" name="package_post_type[]" id="post_type_post" onClick="showcategory(this.value,'<?php echo $scats; ?>');" value="post,category" <?php if(in_array('post',$pkg_post_type) || in_array('all',$pkg_post_type)){ echo 'checked="checked"';}?> />&nbsp;<?php echo 'Post';?></label><br />
				<?php
				$i=1;			
				foreach ($post_types as $key => $post_type) {	
					$slugs = $post_type['slugs'][0];
					?>
						
					<label for="post_type_<?php echo $i; ?>"><input type="checkbox" class="class_post_type" name="package_post_type[]" id="post_type_<?php echo $i; ?>" onClick="showcategory(this.value,'<?php echo $scats; ?>');" value="<?php echo $key.",".$slugs; ?>" <?php if(in_array($key,$pkg_post_type) || in_array('all',$pkg_post_type)){ echo 'checked="checked"';}?>/>
						<?php echo $post_type['label'];?></label><br />
						
				<?php				
				$i++;	
				} ?>
                </fieldset>               
				
			</td>
		</tr>
		<?php do_action('admin_after_package_post_type',@$id);/*add action after package post type*/
			  do_action('admin_before_package_categories',@$id);/*add action before package categories*/ ?>
		<tr>
			<th valign="top">
				<label for="package_categories" class="form-textfield-label"><?php echo __('Select Categories','templatic-admin'); ?> </label>
			</th>
			<td>
				<div class="element cf_checkbox wp-tab-panel" id="field_category">
				<label for="selectall"><input type="checkbox" name="selectall" id="selectall" class="checkbox" onclick="displaychk_frm();" />&nbsp;<?php if(is_admin()){  echo __('Select All',	'templatic-admin'); }else{ _e('Select All',	'templatic'); } ?></label>
					<ul id="category_checklist" data-wp-lists="list:listingcategory" class="categorychecklist form_cat">
					<?php 
					/*tmpl_remove_terms_clauses filter use for remove wpml language filter in taxonomy terms clauses */
					$remove_terms_clauses=apply_filters('tmpl_remove_terms_clauses',array('monetization'));
					/*Remove stitepress terms claises filer for display all langauge wise category show  */
					if((isset($_REQUEST['page']) && in_array($_REQUEST['page'],$remove_terms_clauses)  ) && is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
						global $sitepress;
						remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 4 );
					}
					$pctype = '';
					if(isset($id) && @$id != '')
					{
						$pctype = get_post_meta($id,"package_post_type",true);
						$post_type = explode(',',$pctype);
						
						$tax = get_post_meta($id,"category",true);
						$pid = explode(',',$tax);
						$pkg_id = $_REQUEST['$post_id'];
					
						if(in_array('all',$post_type))
						{
                                                        /* show category of wordpress default post category */
							tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>'category','popular_cats' => $popular_ids,'selected_cats'=>$pid ) );
							foreach ($post_types as $key => $post_type)
							{
								/*get_wp_category_checklist_plugin($post_type['slugs'][0],$pid);*/
			
								$taxonomy = $post_type['slugs'][0];
								echo "<li><label style='font-weight:bold;'>".$post_type['taxonomies'][0]."</label></li>";
								tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>$taxonomy,'popular_cats' => $popular_ids,'selected_cats'=>$pid ) );
							}
						}
						else
						{
                                                        /* show category of wordpress default post category if only post selected */
                                                        if(in_array('category',$pkg_post_type)){
                                                            echo "<li><label style='font-weight:bold;'>" . __('Categories')."</label></li>";
                                                            tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>'category','popular_cats' => $popular_ids,'selected_cats'=>$pid ) );
                                                        }
                                                        
							foreach ($post_types as $key => $post_type)
							{
								if(in_array($key,$pkg_post_type))
                                                                {
									/*get_wp_category_checklist_plugin($post_type['slugs'][0],$pid);*/
									
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
							/*get_wp_category_checklist_plugin($post_type['slugs'][0],'');*/
							echo "<li><label style='font-weight:bold;'>".$post_type['taxonomies'][0]."</label></li>";
							tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>$post_type['slugs'][0],'popular_cats' => $popular_ids,'selected_cats'=>$pid  ) );
						}
					} ?>
					</ul>
				</div>
				<span id='process' style='display:none;'><i class="fa fa-circle-o-notch fa-spin"></i></span>
				
			</td>
		</tr>
		<?php do_action('admin_after_package_categories',@$id);/*add action after package categories*/
			  do_action('fields_monetization',@$id); /*add action for monetize plugin*/
			  do_action('admin_before_package_price',@$id);/*add action before package price*/?>
		<tr class="" id="package_price">
			<th valign="top">
				<label for="package_amount" class="form-textfield-label"><?php echo __('Amount','templatic-admin'); ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
			</th>
			<td>
				<input type="text" name="package_amount" id="package_amount" value="<?php if(isset($id) && $id != '') { echo get_post_meta($id, 'package_amount', true); } ?>">
				<br/><p class="description"><?php echo __("This is the price which will be the cost to submit on this package. Do not enter thousand separators. Use the dot (.) as the decimal separator (if necessary). <strong>Tip</strong>: Enter 0 to make the package free. <strong>If you select subscription as package type then you can not enter zero amount.</strong>",'templatic-admin');?>.</p>
			</td>
		</tr>
		<?php do_action('admin_after_package_price',@$id);/*add action after package price*/
			  do_action('admin_before_billing_period',@$id);/*add action before set price package billing period.*/ ?>
		<?php $recurring = @get_post_meta($id, 'recurring', true); ?>
		<tr class="" id="billing_period" <?php if($recurring == 1) { ?>style="display:none;";<?php } ?>>
			<th valign="top">
				<label for="billing_period" class="form-textfield-label"><?php echo __('Package Duration','templatic-admin'); ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
			</th>
			<td>
				<input type="text" class="billing_num" name="validity" id="validity" value="<?php if(isset($id) && $id != '') { echo get_post_meta($id, 'validity', true); } ?>">
				<select name="validity_per" id="validity_per" class="textfield billing_per">
					<option value="D" <?php if(isset($id) && $id != '' && get_post_meta($id, 'validity_per', true) == 'D'){ echo 'selected="selected"';}?>><?php echo __('Days','templatic-admin'); ?></option>
					<option value="M" <?php if(isset($id) && $id != '' && get_post_meta($id, 'validity_per', true) == 'M'){ echo 'selected="selected"';}?>><?php echo __('Months','templatic-admin'); ?></option>
					<option value="Y" <?php if(isset($id) && $id != '' && get_post_meta($id, 'validity_per', true) == 'Y'){ echo 'selected="selected"';}?>><?php echo __('Years','templatic-admin'); ?></option>
				</select><br/>
				<p class="description"><?php echo __('Enter the duration in number of days, months or years for this package.','templatic-admin');?></p>
			</td>
		</tr>
		<?php do_action('admin_after_billing_period',@$id);/*add action after set price package billing period*/
			  do_action('admin_before_package_status',@$id);/*add action before set price package status enable or not*/ ?>
        <tr id="number_of_post_days" <?php if((isset($id) && @$id != '') && get_post_meta($id,'package_type',true) == '2'):?> style="display:'';"<?php else:?> style="display:none;"<?php endif;?>>
        <th valign="top"><label for="days_for_no_post"><?php echo __('Allow users to submit listing within following days','templatic-admin');?><span class="required"><?php echo REQUIRED_TEXT; ?></span></label></th>
           <td>
            	<input type="text" name="days_for_no_post" value="<?php if((isset($id) && $id != '') && get_post_meta($id,'days_for_no_post',true) !="") { echo  get_post_meta($id,'days_for_no_post',true); }?>"  id="days_for_no_post" /><br />
    		    <p class="description"><?php echo __('User can submit listing within the number of days','templatic-admin');?>.</p>
    		    <p class="description"><?php echo __('Note: Submit listing within following days should be less than or equal to billing recurring package time.','templatic-admin');?></p>
           </td>
        </tr>
        <tr id="subscription_as_pay_per_post" <?php if((isset($id) && @$id != '') && get_post_meta($id,'package_type',true) == '2'):?> style="display:'';"<?php else:?> style="display:none;"<?php endif;?>>
        <th valign="top"><?php echo __('Allow one submission before payment','templatic-admin');?></th>
           <td>
	            <input type="checkbox" name="subscription_as_pay_post" value="1" <?php checked(get_post_meta($id,'subscription_as_pay_post',true), 1 ); ?>  id="subscription_as_pay_post" /><label for="subscription_as_pay_post"><?php echo __('Enable','templatic-admin');?></label><br />
                <p class="description"><?php echo __('Allow the submission form to process the first submitted listing instead of forcing the user to pay for the subscription package first.','templatic-admin'); ?></p>
           </td>
        </tr>
        <tr id="tr_subscription_days_free_trail" <?php if((isset($id) && @$id != '') && get_post_meta($id,'package_type',true) == '2'):?> style="display:'';"<?php else:?> style="display:none;"<?php endif;?>>
        <th valign="top"><?php echo __('Number of free posts allowed','templatic-admin');?></th>
           <td>
            	<input type="text" name="subscription_days_free_trail" value="<?php echo get_post_meta($id,'subscription_days_free_trail',true); ?>"  id="subscription_days_free_trail" /><br />
                <p class="description"><?php echo __('Enter the number of free posts a user can add before making payment for this package.','templatic-admin'); ?></p>
                <p id="error_subscription_days_free_trail_message" style="display:none;" class="error"><?php echo __('Number of free listing should be less than or equal to " Number of Posts ".','templatic-admin'); ?></p>
           </td>
        </tr>
		<tr class="">
			<th valign="top">
				<label for="package_status" class="form-textfield-label"><?php echo __('Enable Package','templatic-admin'); ?></label>
			</th>
			<td>
				<input type="checkbox" name="package_status" id="package_status" value="1" <?php if((isset($id) && $id != '' && get_post_meta($id, 'package_status', true) == 1) || (isset($_REQUEST['action']) && $_REQUEST['action']=='add_package')){ echo 'checked=checked'; } ?> />
				&nbsp;<label for="package_status"><?php echo __("Yes",'templatic-admin'); ?></label><br/>
			</td>
		</tr>
		<?php do_action('admin_after_package_status',@$id);/*add action after set price package status enable or not*/
			  do_action('admin_before_is_recurring',@$id);/*add action before set price package is recurring or not*/ ?>
		<tr>
			<th valign="top">
				<label for="is_recurring" class="form-textfield-label" style="width:100px;"><?php echo __('Recurring package','templatic-admin'); ?></label>
			</th>
			 <?php if( @get_post_meta($id, 'recurring', true) == 1) { $checked = "checked=checked"; }else{ $checked = " "; } ?>
			<td>
				<label><input type="checkbox" name="recurring" id="recurring"  value='1' onclick="rec_div_show(this.id)" <?php echo $checked ; ?>/>&nbsp; <?php echo YES; ?></label>
				<br/>
				<p class="description"><?php echo __('If "Yes" is selected, Listing owners will be billed automatically as soon as the price package\'s billing period expires. ','templatic-admin');?><b><?php echo sprintf(__("(Works with PayPal, <a href='%s'>InspireCommerce</a>, <a href='%s'>Braintree</a> and <a href='%s'>Stripe</a>).",'templatic-admin'),'http://templatic.com/plugins/payment-gateways/inspire-commerce','http://templatic.com/plugins/payment-gateways/braintree','http://templatic.com/plugins/payment-gateways/stripe');?></b></p>
			</td>
		</tr>
		<?php do_action('admin_after_is_recurring',@$id);/*add action after set price package is recurring or not*/
			  do_action('admin_before_recurring_billing',@$id);/*add action before set price package is recurring billing period*/ ?>
		<tr id="rec_tr" <?php if((isset($id) && get_post_meta($id, 'recurring', true) == 0)  || (!isset($id) || $id == '')){ echo 'style="display:none;"'; }?>>
			<th valign="top">
				<label for="recurring_billing" class="form-textfield-label"><?php echo __('Billing Period for Recurring package','templatic-admin'); ?></label>
			</th>
			<td>
				<span class="option_label"><?php echo __('Charge users every','templatic-admin'); ?> </span>
				<input type="text" class="textfield billing_num" name="billing_num" id="billing_num" value="<?php if(isset($id) && $id != '') { echo get_post_meta($id, 'billing_num', true); } ?>">
				<select name="billing_per" id="billing_per" class="textfield billing_per">
					<option value="D" <?php if(isset($id) && $id != '' && get_post_meta($id, 'billing_per', true) =='D'){ echo 'selected=selected';}?> ><?php echo __('Days','templatic-admin'); ?></option>
					<option value="M" <?php if(isset($id) && $id != '' && get_post_meta($id, 'billing_per', true) =='M'){ echo 'selected=selected';}?> ><?php echo __('Months','templatic-admin'); ?></option>
					<option value="Y" <?php if(isset($id) && $id != '' && get_post_meta($id, 'billing_per', true) =='Y'){ echo 'selected=selected';}?> ><?php echo __('Years','templatic-admin'); ?></option>
				</select><br/>
				<p class="description"><?php echo __('Time between each billing','templatic-admin'); ?>.</p>
			</td>
		</tr>
		<?php do_action('admin_after_recurring_billing',@$id);/*add action after set price package is recurring billing period*/
			  do_action('admin_before_billing_cycle',@$id);/*add action before price package billing cycle*/ ?>
		<tr id="rec_tr1" <?php if((isset($id) && get_post_meta($id, 'recurring', true) == 0)  || (!isset($id) || $id == '')){ echo 'style="display:none;"'; }?>>
			<th valign="top">
				<label for="billing_cycle" class="form-textfield-label"><?php echo __('Number of cycles','templatic-admin'); ?></label>
			</th>
			<td>
				<input type="text" class="textfield" name="billing_cycle" id="billing_cycle" value="<?php if(isset($id) && $id != '') { echo get_post_meta($id, 'billing_cycle', true); } ?>"><br/><p class="description"><?php echo __('The number of times members will be billed, i.e. the number of times the process will be repeated. <strong>Note:</strong> With PayPal the number of payment cycles can range from 2 to 52. Enter 0 for unlimited cycles.','templatic-admin'); ?></p>
			</td>
		</tr>
		<?php do_action('admin_after_billing_cycle',@$id);/*add action after set price package is recurring billing period*/
			  do_action('admin_before_first_free_trail_period',@$id);/*add action before Free trial period*/ ?>
        <tr id="rec_tr2" <?php if((isset($id) && get_post_meta($id, 'recurring', true) == 0)  || (!isset($id) || $id == '')){ echo 'style="display:none;"'; }?>>
        	<th valign="top"><label class="form-textfield-label"><?php echo __('Free trial period','templatic-admin')?></label></th>
        	<td>
				<div class="input-switch">
					<input id="first_free_trail_period" type="checkbox" name="first_free_trail_period" value="1" <?php if(isset($id) && $id != '' && get_post_meta($id, 'first_free_trail_period', true) =='1'){ echo 'checked=checked';}?>  />
					<label for="first_free_trail_period">&nbsp;<?php echo __('Enable','templatic-admin');?></label> 
				</div>
                <p class="description"><?php echo __('With this enabled the first period of the subscription will be free. For the second period the user will be charged the amount you specified above. This only works with PayPal. ','templatic-admin');?></p>
            </td>
        </tr>
        <?php do_action('admin_after_first_free_trail_period',@$id);/*add action after Free trial period*/
			  do_action('add_new_row_pricepackages',@$id); /*add action to add new field for custom fields*/
			?>
	</tbody>
	</table>
    <table style="width:60%"  class="form-table" id="form_table_monetize">
	<thead>
		<tr>
			<th colspan="2"><div class="tevo_sub_title"><?php echo __('Settings For Featured Entries','templatic-admin'); ?></div>
			<p class="tevolurion_desc"><?php echo __('These settings are must if you want to charge the users for featured posts otherwise leave them blank','templatic-admin'); ?>.</p></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action('admin_before_is_home_featured',@$id);/*add action before set price package is home featured or not*/ ?>
		<tr>
			<th valign="top">
				<label for="is_featured" class="form-textfield-label"><?php echo __('Featured options','templatic-admin'); ?></label>
			</th>
			<td>
				<label for="is_home_page_featured"><input type="checkbox" name="is_home_page_featured" id="is_home_page_featured" value="1" <?php if(isset($id) && $id != '' && get_post_meta($id, 'is_home_page_featured', true) == 1){ echo 'checked=checked'; } ?> onClick="show_featured_package(this.id);" />&nbsp;<?php echo __("Home page",'templatic-admin'); ?></label>&nbsp;
				<label for="is_category_page_featured"><input type="checkbox" name="is_category_page_featured" id="is_category_page_featured" value="1" <?php if(isset($id) && $id != '' && get_post_meta($id, 'is_category_page_featured', true) == 1){ echo 'checked=checked'; } ?> onClick="show_featured_package(this.id);" />&nbsp;<?php echo __("Category page",'templatic-admin'); ?></label><br/>
				<p class="description"><?php echo __('Select either or both to allow listing submitters to make their listing featured for an additional cost. You can also make the package have all listings in it featured by default.','templatic-admin'); ?>.</p>
			</td>
		</tr>
       <tr id="featured_home" <?php if((isset($id) && get_post_meta($id, 'is_home_page_featured', true) == 0) || (!isset($id) || $id == '') ) { echo 'style="display:none;"'; } ?> >
			<th valign="top">
				<label for="feature_amount" class="form-textfield-label"><?php echo __('Homepage featured price','templatic-admin'); ?></label>
			</th>
			<td>
                <div id="home_page_featured_price" <?php if((isset($id) && get_post_meta($id, 'is_home_featured', true) == 1) || get_post_meta($id, 'is_home_page_featured', true) != 1) { echo 'style="display:none;"'; } ?>>
                    <input type="text" name="feature_amount" id="feature_amount" value="<?php if(isset($id) && $id != '' && get_post_meta($id, 'feature_amount', true) != "") { echo get_post_meta($id, 'feature_amount', true); } ?>"/>   
                </div>
				
				<label for="is_home_featured"><input type="checkbox" name="is_home_featured" id="is_home_featured" value="1" <?php if(isset($id) && $id != '' && get_post_meta($id, 'is_home_featured', true) == 1){ echo 'checked=checked'; } ?> onClick="show_featured_package(this.id);" /><?php echo __('Select to make all listings on this package homepage featured without charging an extra amount.','templatic-admin'); ?>&nbsp;</label>
			</td>
		</tr>
		
         <tr id="home_page_featured_alive_days"  <?php if((isset($id) && get_post_meta($id, 'is_home_page_featured', true) == 0) || (!isset($id) || $id == '') ) { echo 'style="display:none;"'; } ?>>
			<th valign="top"><?php echo __('Featured status duration(in days)','templatic-admin'); ?></th>
			<td>
				<input type="text" name="home_page_alive_days" id="home_page_alive_days" value="<?php if(isset($id) && $id != '' && get_post_meta($id, 'home_page_alive_days', true) != "") { echo get_post_meta($id, 'home_page_alive_days', true); } ?>">
			</td>
		</tr>
        <?php do_action('admin_after_is_home_featured',@$id);/*add action after set price package is home featured or not*/
			  do_action('admin_before_is_category_featured',@$id);/*add action before set price package is category featured or not*/?>
        <tr id="featured_cat" <?php if((isset($id) && get_post_meta($id, 'is_category_page_featured', true) == 0)  || (!isset($id) || $id == '')) { echo 'style="display:none;"'; } ?>>
			<th valign="top">
				<label for="feature_cat_amount" class="form-textfield-label"><?php echo __('Category page featured price','templatic-admin'); ?></label>
			</th>
			<td>
                <div id="category_page_featured_price" <?php if((isset($id) && get_post_meta($id, 'is_category_featured', true) == 1 ) || get_post_meta($id, 'is_category_page_featured', true) != 1)  { echo 'style="display:none;"'; } ?>>
                        <input type="text" name="feature_cat_amount" id="feature_cat_amount" value="<?php if(isset($id) && $id != '' &&get_post_meta($id, 'feature_cat_amount', true) != "") { echo get_post_meta($id, 'feature_cat_amount', true); } ?>">
                </div>
				
				<label for="is_category_featured"><input type="checkbox" name="is_category_featured" id="is_category_featured" value="1" <?php if(isset($id) && $id != '' && get_post_meta($id, 'is_category_featured', true) == 1){ echo 'checked=checked'; } ?> onClick="show_featured_package(this.id);" /><?php echo __('Select to make all listings on this package category page featured without charging an extra amount.','templatic-admin'); ?>&nbsp;</label>
			</td>
		</tr>
		
        <tr id="category_page_featured_alive_days" <?php if((isset($id) && get_post_meta($id, 'is_category_page_featured', true) == 0)  || (!isset($id) || $id == '')) { echo 'style="display:none;"'; } ?>>
			<th valign="top"><?php echo __('Featured status duration(in days)','templatic-admin'); ?></th>
			<td>
				<input type="text" name="cat_page_alive_days" id="cat_page_alive_days" value="<?php if(isset($id) && $id != '' &&get_post_meta($id, 'cat_page_alive_days', true) != "") { echo get_post_meta($id, 'cat_page_alive_days', true); } ?>">
			</td>
		</tr>
	<?php
	if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
	?>
	<thead>
		<tr>
			<th colspan="2"><div class="tevo_sub_title"><?php echo __('Comment Moderation','templatic-admin'); ?></div><br/>
			<span class="tevo_desc"><?php echo __('Allows people to moderate comments on your site which came on the entries they submitted','templatic-admin'); ?>.</span></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th valign="top">
				<label for="can_author_mederate" class="form-textfield-label"><?php echo __('Allow author to moderate comments?','templatic-admin'); ?></label>
			</th>
			<td>
				<label for="can_author_mederate"><input type="checkbox" name="can_author_mederate" id="can_author_mederate" value="1" <?php if(isset($id) && $id != '' && get_post_meta($id, 'can_author_mederate', true) == 1){ echo 'checked=checked'; } ?> onClick="show_comment_package(this.id);"/>&nbsp;
				<?php echo YES; ?></label><br/>
				<p class="description"><?php echo __('Select this to allow listing authors to moderate reviews on the listings they submit using this price package','templatic-admin'); ?>.</p>
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
	<?php } 
		do_action('admin_after_is_category_featured',@$id);/*add action after set price package is category featured or not*/?>
		<tr>
			<td colspan="2"><input type="submit" class="button button-primary button-hero" value="<?php echo __('Save Settings','templatic-admin'); ?>" name="submit" id="submit-1"></td>
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
add_action('admin_footer','fetch_post_type_category');
function fetch_post_type_category()
{
	?>
	<script type="text/javascript">
		/*if we deselect any post type than select all checkbox should be uncheck.*/
		jQuery(".class_post_type").click(function(){
 
			if(jQuery(".class_post_type").length == jQuery(".class_post_type:checked").length) {
				jQuery("#selectall_post_type").attr("checked", "checked");
			} else {
				jQuery("#selectall_post_type").removeAttr("checked");
			}
	 
		});
		function showcategory(str,scat)
		{  	
			if (str=="")
			{
				document.getElementById("field_category").innerHTML="";
				return;
			}
			else
			{
				document.getElementById("field_category").innerHTML="";
				document.getElementById("process").style.display ="block";
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
			jQuery.ajax({
					url:ajaxUrl,
					type:'POST',
					data:"action=ajax_categories_dropdown&post_type="+valarr+'&scats='+scat+'&page=monetization&is_ajax=1',
					success:function(results) {
						 jQuery("#process").css('display',"none");
						 jQuery("#field_category").html(results);
					}
				});
				
				return false;
			 
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
	<?php
}
?>