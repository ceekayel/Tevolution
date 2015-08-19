<?php
/*
 * define constants for claim ownership
 */
global $wp_query,$wpdb,$wp_rewrite;

/* activating claim ownership */

if((isset($_REQUEST['activated']) && $_REQUEST['activated'] == 'claim_ownership') && (isset($_REQUEST['true']) && $_REQUEST['true'] == 1 )){ 
	update_option('claim_ownership','Active'); 
	update_option('claim_enabled','Yes');
	$types['claim_post_type_value'] = get_post_types();
	$tmpdata = get_option('templatic_settings');	
	update_option('templatic_settings',array_merge($tmpdata,$types));	
}elseif((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'claim_ownership') && (isset($_REQUEST['true']) && @$_REQUEST['true'] == 0 )){
	delete_option('claim_enabled');
	delete_option('claim_ownership'); 
}

/* eof - claim ownership activation */

/* define Claim Ownership Constants variable	*/
define('ID_TEXT',__('ID','templatic-admin'));
define('CLAIM_AUTHOR_NAME_TEXT',__('Author','templatic-admin'));
define('CLAIMER_TEXT',__('Claimant','templatic-admin'));
define('CONTACT_NUM_TEXT',__('Contact','templatic-admin'));
define('CONTACT_EMAIL_TEXT',__('Email','templatic-admin'));
define('ACTION_TEXT',__('Action','templatic-admin'));
define('DETAILS_CLAIM',__('Detail','templatic-admin'));
define('VERIFY_CLAIM',__('Verify','templatic-admin'));
define('DECLINE_CLAIM',__('Decline','templatic-admin'));
define('DECLINED',__('Declined','templatic-admin'));
define('VIEW_CLAIM',__('View Post','templatic-admin'));
define('DELETE_CLAIM',__('Delete this request','templatic-admin'));
define('NO_CLAIM',__('No Claim request for this post.','templatic-admin'));
define('REMOVE_CLAIM_REQUEST',__('Remove Claim Request','templatic-admin'));
define('YES_VERIFIED',__('Verified','templatic-admin'));
define('POST_VERIFIED_TEXT',__('This post is verified.','templatic-admin'));
define('CLAIM_BUTTON',__('Claim this post','templatic-admin'));
define('OWNER_VERIFIED',__('Owner Verified Listing','templatic-admin'));
define('OWNER_TEXT',__('Do you own this post?','templatic-admin'));
define('VERIFY_OWNERSHIP_FOR',__('Verify your ownership for','templatic-admin'));
define('FULL_NAME',__('Full Name','templatic-admin'));
define('EMAIL',__('Your Email','templatic-admin'));
define('CONTACT',__('Contact No','templatic-admin'));
define('CLAIM',__('Your Claim','templatic-admin'));
define('DELETE_CONFIRM_ALERT',__('Are you sure want to delete this claim?','templatic-admin'));
define('ENTRY_DELETED',__('Claim Deleted','templatic-admin'));
define('NO_CLAIM_REQUEST',__('No post has been claimed on your site.','templatic-admin'));
define('STATUS',__('Status','templatic-admin'));
define('PENDING',__('Pending','templatic-admin'));
define('SELECT_POST_TYPE',__('Select Post Types','templatic-admin'));
define('SELECT_DISPLAY_TYPE',__('Select Display Type','templatic-admin'));
define('LINK',__('Link','templatic-admin'));
define('BUTTON',__('Button','templatic-admin'));
define('ALREADY_CLAIMED',__(apply_filters('tmpl_already_claimed_text','Already Claimed'),'templatic-admin'));

/* including a functions file */
if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-claim_ownership/claim_functions.php'))
{
	include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-claim_ownership/claim_functions.php");
}
/* include menu after custom fields menu */
add_action('templ_add_admin_menu_', 'templ_add_claim_page_menu',13);

function templ_add_claim_page_menu(){
	$menu_title3 = __('Submitted Claims', 'templatic-admin');
	add_submenu_page('templatic_system_menu', $menu_title3, $menu_title3,'administrator', 'ownership_listings', 'tmpl_claimed_listing');

}

/* Load claim listings page */
function tmpl_claimed_listing(){
	include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-claim_ownership/manage_claim_listings.php");
}
/* call a function to add dashboard metabox */
add_action('wp_dashboard_setup','add_claim_dashboard_metabox');

/* call a function to add metabox in post types */
add_action('admin_init','add_claim_metabox_posts');

/*
	Add Action for display basic setting data
*/
add_action('templatic_general_setting_data','claim_setting_data',11);


/*
	Display the claim section in general setting menu section
*/	
function claim_setting_data($column)
{
	$tmpdata = get_option('templatic_settings');		
	?>
	<table id="general_claim_setting" class="tmpl-general-settings form-table">
	<tr>                    
		<td colspan="2">
			<label for="ilc_tag_class"><p class="tevolution_desc"><?php echo sprintf(__('Claim ownership allows visitors to claim a certain post on your site as their own. For details on how this works visit the %s.','templatic-admin'),'<a href="http://templatic.com/docs/tevolution-guide/#claim_settings" title="Claim Settings" target="_blank">documentation guide</a>'); ?></p></label>
		</td>
	</tr> 
	<tr>
		<th><label><?php echo __('Enable claim ownership for','templatic-admin');?></label></th>
		<td>
			<?php $value = @$tmpdata['claim_post_type_value']; 
			$posttaxonomy = get_option("templatic_custom_post");
			if(!empty($posttaxonomy))
			{
				foreach($posttaxonomy as $key=>$_posttaxonomy):
					if($key=='admanager' || $key == 'booking'){
						continue;
					}
				?>									
				<div class="element">
					<label for="<?php echo "claim_".$key; ?>"><input type="checkbox" name="claim_post_type_value[]" id="<?php echo "claim_".$key; ?>" value="<?php echo $key; ?>" <?php if(@$value && in_array($key,$value)) { echo "checked=checked";  } ?>>&nbsp;<?php echo $_posttaxonomy['label'];  ?></label>
				</div>
				<?php endforeach; 
			}else{
				echo sprintf(__(' No custom post type has been created at your site yet. Please %s to list it here.','templatic-admin'),'<a href="?page=ownership_listings"> create it </a>');		
			}?><p class="description"><?php echo __('Once enabled, a "Claim Ownership" button will appear inside detail pages (above the tabs).','templatic-admin')?></p><br />
                                                                                <p class="description"><?php echo __('Notes: <br />1. Check this feature without logging in to the site. <br />2. The claim ownership button will not remain visible for the users who has submitted the specific listings. ','templatic-admin')?></p><br />
		</td>
	</tr>
	<tr>
		<th></th>
		<td style="float:right;">
			<p><a href="<?php echo site_url()."/wp-admin/admin.php?page=ownership_listings"; ?>"><?php _e('View all claimed lIstings','templatic-admin'); ?></a></p>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<p class="submit" style="clear: both;">
		  <input type="submit" name="Submit"  class="button button-primary button-hero" value="<?php echo __('Save All Settings','templatic-admin');?>" />
         
		  <input type="hidden" name="settings-submit" value="Y" />
		</p>
		</td>
	</tr>
	</table>
	<?php
}
/* Finish claim setting data */
?>