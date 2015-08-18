<?php 
if(isset($_REQUEST['activated']) && $_REQUEST['activated']=='claim_ownership'){
	templatic_module_activationmsg('claim_ownership','Claim Ownership','',$mod_message=__('Now move on to the',ADMINDOMAIN).' <a href='.admin_url('/admin.php?page=templatic_settings').'>'.__('General Settings',ADMINDOMAIN).'</a> '.__('page to select the post types for your site. You need to set up the widget in order to show the',ADMINDOMAIN).' <strong>'.__('Claim for this post',ADMINDOMAIN).'</strong> '.__('link. You can set up the widget',ADMINDOMAIN).' <a href='.admin_url('/widgets.php').'>'.__('here',ADMINDOMAIN).'</a>.',$realted_mod =''); 
}elseif(isset($_REQUEST['deactivate']) && $_REQUEST['deactivate']=='claim_ownership'){
	templatic_module_activationmsg('claim_ownership','Claim Ownership','',$mod_message='',$realted_mod =''); 
}?>
<div id="templatic_claimownership" class="widget_div">
	
	<div class="t_dashboard_icon"><img class="dashboard_img" id="claim_image" src = "<?php echo TEMPL_PLUGIN_URL.'tmplconnector/monetize/images/claim.png'; ?>" /></div>
	<div class="inside">
		<div class="t_module_desc">
         <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=templatic_settings#general_claim_setting"><h3 class="hndle"><span><?php echo __('Claim Post Manager',ADMINDOMAIN); ?></span></h3></a>
        <p class="mod_desc"><?php
		echo __('Claim Post functionality will allow your site visitors to claim posts from you or other members. Once a post is claimed you will have to verify it and make the necessary changes. This functionality will/can work for all your created post types. ',ADMINDOMAIN);
		?></p>
        </div>
		<?php if(!is_active_addons('claim_ownership')) { delete_option('claim_enabled'); ?>
		<div id="publishing-action" class="settings_style">
		<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&activated=claim_ownership&true=1";?>" class="button-primary"><i class="fa fa-check"></i><?php echo __('Activate &rarr;',ADMINDOMAIN); ?></a></div>
		<?php } 
		if (is_active_addons('claim_ownership')) :  update_option('claim_enabled','No'); ?>
		<div id="publishing-action" class="settings_style">
			<a class="templatic-tooltip set_lnk" id="WpEcoWorld_claim" href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_settings";?>"><i class="fa fa-gear"></i>
			<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&deactivate=claim_ownership&true=0";?>" class="button"><i class="fa fa-times"></i>&nbsp;<?php echo __('Deactivate',ADMINDOMAIN); ?></a>
			
			</a>
			<?php 
				global $wpdb;
				$user_meta_table = $wpdb->prefix."usermeta";
				$chk_tour = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
				$flag=0;
				if ( in_array( 'templatic_wpecoworld_plugin_claim_install', $chk_tour )){ 
					$flag=1;
				}else{
					$flag=0;
				}
			?>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php 
if(isset($_REQUEST['start']) && $_REQUEST['start']=="templatic_wpecoworld_plugin_claim_install"){
	activate_single_tour($_REQUEST['start']);
}?>