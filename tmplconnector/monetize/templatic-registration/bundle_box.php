<?php 
$activated = @$_REQUEST['activated'];
$deactivate = @$_REQUEST['deactivate'];
if($activated){
templatic_module_activationmsg('templatic-login','User registration/Login Management','',$mod_message=__('You can use this feature directly by setting up a widget T - Login Dashboard wizard in your site',ADMINDOMAIN).' <a href='.admin_url('/widgets.php').'><strong>'.__('from here',ADMINDOMAIN).'</strong></a>. '.__('On activation of this module, it will automatically add two user custom fields on your site. Add some more',ADMINDOMAIN).' <a href='.admin_url('/admin.php?page=user_custom_fields').'><strong>'.__('from here',ADMINDOMAIN).'</strong></a>.',$realted_mod =''); 
}else{
templatic_module_activationmsg('templatic-login','User registration/Login Management','',$mod_message='',$realted_mod =''); 
}?>
<div id="templatic_userreg" class="widget_div">
	
	<div class="t_dashboard_icon"><img  id="user_sample_image" class="dashboard_img" src = "<?php echo TEMPL_PLUGIN_URL; ?>tmplconnector/monetize/images/user_sample_image.png" /></div>
    <div class="inside">
		<div class="t_module_desc">
        <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=user_custom_fields"><h3 class="hndle"><span><?php echo __('User registration/Login Management',ADMINDOMAIN); ?></span></h3></a>
		<p class="mod_desc"><?php
		echo __('Activate this module if you plan on allowing visitors to login and post content on your site. Use the "Profile Fields Setup" section to create new fields for the registration form and collect unique information about your visitors.',ADMINDOMAIN);
		?></p>
        </div>
		<?php if(!is_active_addons('templatic-login')) { ?>
		
		<div id="publishing-action" class="settings_style">
		<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&activated=templatic-login&true=1";?>" class="button-primary"><i class="fa fa-check"></i>&nbsp;<?php echo __('Activate &rarr;',ADMINDOMAIN); ?></a></div>
		<?php } 
		if (is_active_addons('templatic-login')) :  ?>
		<div id="publishing-action" class="settings_style">
			<a id="WpEcoWorld_user_custom_fields" class="templatic-tooltip set_lnk" href="<?php echo site_url()."/wp-admin/admin.php?page=user_custom_fields";?>" ><i class="fa fa-gear"></i></a>
			<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&deactivate=templatic-login&true=0";?>" class="button"><i class="fa fa-times"></i>&nbsp;<?php echo __('Deactivate ',ADMINDOMAIN); ?></a> 		
		<?php 
			global $wpdb;
			$user_meta_table = $wpdb->prefix."usermeta";
			$chk_tour = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
			$flag=0;
			if ( in_array( 'templatic_weecoworld_plugin_user_registration_install', $chk_tour )){ 
				$flag=1;
			}else{
				$flag=0;
			}
		?>
		</div><?php endif; ?>
	</div>
</div>
<?php 
if(isset($_REQUEST['start']) && $_REQUEST['start']=="templatic_weecoworld_plugin_user_registration_install"){
	activate_single_tour($_REQUEST['start']);
}?>