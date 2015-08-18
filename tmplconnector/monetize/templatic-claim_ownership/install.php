<?php
global $wp_query,$wpdb,$wp_rewrite;
/* ACTIVATING CLAIM OWNERSHIP */
if((isset($_REQUEST['activated']) && $_REQUEST['activated'] == 'claim_ownership') && (isset($_REQUEST['true']) && $_REQUEST['true'] == 1 )){ 
	update_option('claim_ownership','Active'); //ACTIVATING
	update_option('claim_enabled','Yes');
	$types['claim_post_type_value'] = get_post_types();
	$tmpdata = get_option('templatic_settings');	
	update_option('templatic_settings',array_merge($tmpdata,$types));	
}elseif((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'claim_ownership') && (isset($_REQUEST['true']) && @$_REQUEST['true'] == 0 )){
	delete_option('claim_enabled');
	delete_option('claim_ownership'); //DEACTIVATING
}
/* EOF - CLAIM OWNERSHIP ACTIVATION */


if(is_active_addons('claim_ownership')){
	/* define claimownership Constants variable*/
	define('ID_TEXT',__('ID',ADMINDOMAIN));
	define('CLAIM_AUTHOR_NAME_TEXT',__('Author',ADMINDOMAIN));
	define('CLAIMER_TEXT',__('Claimant',ADMINDOMAIN));
	define('CONTACT_NUM_TEXT',__('Contact',ADMINDOMAIN));
	define('CONTACT_EMAIL_TEXT',__('Email',ADMINDOMAIN));
	define('ACTION_TEXT',__('Action',ADMINDOMAIN));
	define('DETAILS_CLAIM',__('Detail',ADMINDOMAIN));
	define('VERIFY_CLAIM',__('Verify',ADMINDOMAIN));
	define('DECLINE_CLAIM',__('Decline',ADMINDOMAIN));
	define('DECLINED',__('Declined',ADMINDOMAIN));
	define('VIEW_CLAIM',__('View Post',ADMINDOMAIN));
	define('DELETE_CLAIM',__('Delete this request',ADMINDOMAIN));
	define('NO_CLAIM',__('No Claim request for this post.',ADMINDOMAIN));
	define('REMOVE_CLAIM_REQUEST',__('Remove Claim Request',ADMINDOMAIN));
	define('YES_VERIFIED',__('Verified',ADMINDOMAIN));
	define('POST_VERIFIED_TEXT',__('This post is verified.',ADMINDOMAIN));
	define('CLAIM_BUTTON',__('Claim this post',ADMINDOMAIN));
	define('OWNER_VERIFIED',__('Owner Verified Listing',ADMINDOMAIN));
	define('OWNER_TEXT',__('Do you own this post?',ADMINDOMAIN));
	define('VERIFY_OWNERSHIP_FOR',__('Verify your ownership for',ADMINDOMAIN));
	define('FULL_NAME',__('Full Name',ADMINDOMAIN));
	define('EMAIL',__('Your Email',ADMINDOMAIN));
	define('CONTACT',__('Contact No',ADMINDOMAIN));
	define('CLAIM',__('Your Claim',ADMINDOMAIN));
	define('DELETE_CONFIRM_ALERT',__('Are you sure want to delete this claim?',ADMINDOMAIN));
	define('ENTRY_DELETED',__('Claim Deleted',ADMINDOMAIN));
	define('NO_CLAIM_REQUEST',__('No post has been claimed on your site.',ADMINDOMAIN));
	define('STATUS',__('Status',ADMINDOMAIN));
	define('PENDING',__('Pending',ADMINDOMAIN));
	define('SELECT_POST_TYPE',__('Select Post Types',ADMINDOMAIN));
	define('SELECT_DISPLAY_TYPE',__('Select Display Type',ADMINDOMAIN));
	define('LINK',__('Link',ADMINDOMAIN));
	define('BUTTON',__('Button',ADMINDOMAIN));
	define('ALREADY_CLAIMED',__('Already Claimed',ADMINDOMAIN));
	
	/* INCLUDING A FUNCTIONS FILE */
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-claim_ownership/claim_functions.php'))
	{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-claim_ownership/claim_functions.php");
	}
	/* INCLUDING A WIDGET FILE */
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-claim_ownership/claim_widget.php'))
	{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-claim_ownership/claim_widget.php");
	}
	
	/* CALL A FUNCTION TO ADD DASHBOARD METABOX */
	add_action('wp_dashboard_setup','add_claim_dashboard_metabox');
	
	/* CALL A FUNCTION TO ADD METABOX IN POST TYPES */
	add_action('admin_init','add_claim_metabox_posts');
	
	/* CALL A FUNCTION TO ADD A WIDGET */	
	add_action('widgets_init','add_claim_widget');
	
	/*
	 * Add Action for display basic setting data
	 */
	add_action('templatic_general_setting_data','claim_setting_data',11);
}

/*
 * Function Name: claim_setting_data
 * Return: display the claim section in general setting menu section
 */	
function claim_setting_data($column)
{
	$tmpdata = get_option('templatic_settings');		
	?>
	<tr id="general_claim_setting">                    
		<th colspan="2">
			<div class="tevo_sub_title"><?php echo __('Claim ownership settings',ADMINDOMAIN);?></div>
			<label for="ilc_tag_class"><p class="tevolution_desc"><?php echo sprintf(__('Claim ownership allows visitors to claim a certain post on your site as their own. For details on how this works visit the %s.',ADMINDOMAIN),'<a href="http://templatic.com/docs/tevolution-guide/#claim_settings" title="Claim Settings" target="_blank">documentation guide</a>'); ?></p></label>
		</th>
	</tr> 
	<tr>
		<th><label><?php echo __('Enable claim ownership for',ADMINDOMAIN);?></label></th>
		<td>
			<?php $value = @$tmpdata['claim_post_type_value']; 
			$posttaxonomy = get_option("templatic_custom_post");
			if(!empty($posttaxonomy))
			{
				foreach($posttaxonomy as $key=>$_posttaxonomy):?>									
				<div class="element">
					<label for="<?php echo "claim_".$key; ?>"><input type="checkbox" name="claim_post_type_value[]" id="<?php echo "claim_".$key; ?>" value="<?php echo $key; ?>" <?php if(@$value && in_array($key,$value)) { echo "checked=checked";  } ?>>&nbsp;<?php echo $_posttaxonomy['label'];  ?></label>
				</div>
				<?php endforeach; 
			}else{
				echo sprintf(__(' No custom post type has been created at your site yet. Please %s to list it here.',ADMINDOMAIN),'<a href="?page=custom_taxonomy"> create it </a>');		
			}?><p class="description"><?php echo __('Once enabled, a "Claim Ownership" button will appear inside detail pages (above the tabs).',ADMINDOMAIN)?></p><br />
		</td>
	</tr>
	<?php
}
/* Finish claim setting data */
?>