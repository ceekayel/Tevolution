<?php 
$activated = @$_REQUEST['activated'];
$deactivate = @$_REQUEST['deactivate'];
if($activated){
templatic_module_activationmsg('custom_fields_templates','Custom Fields Manager','',$mod_message=__('In order to use this feature, you can create the custom fields',ADMINDOMAIN).' <a href='.admin_url('/admin.php?page=custom_fields').'>'.__('here',ADMINDOMAIN).'</a>.',$realted_mod =''); 
}else{
templatic_module_activationmsg('custom_fields_templates','Custom Fields Manager','',$mod_message='',$realted_mod =''); 
}?>
<div id="templatic_customfields" class="widget_div">
	
	<div class="t_dashboard_icon"><img class="dashboard_img" id="custum_field" src = "<?php echo TEMPL_PLUGIN_URL.'tmplconnector/monetize/images/custum_field.png'; ?>" /></div>
    <div class="inside">
		<div class="t_module_desc">
        <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=custom_fields"><h3 class="hndle"><span><?php echo __('Custom Fields Manager',ADMINDOMAIN); ?></span></h3></a>
    	<p class="mod_desc"><?php
		echo __('With this module you can easily populate a submission form with fields. The feature also allows you to control on which pages will the submitted value show. Category-specific and post type-specific fields are also possible!',ADMINDOMAIN);
		?></p>
        </div>
		<?php if(!is_active_addons('custom_fields_templates')) { ?>
		<div id="publishing-action" class="settings_style">
		<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&activated=custom_fields_templates&true=1";?>" class="button-primary"><i class="fa fa-check"></i><?php echo __('Activate &rarr;',ADMINDOMAIN); ?></a></div>
		<?php } 
		if (is_active_addons('custom_fields_templates')) : ?>
		<div id="publishing-action" class="settings_style">
		<a id="custom_fields_setting" class="templatic-tooltip set_lnk"  href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_settings&eco_system_custom_fields_tour_step=1#custom_fields_setting";?>"><i class="fa fa-gear"></i></a>
		<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&deactivate=custom_fields_templates&true=0";?>" class="button"><i class="fa fa-times"></i>&nbsp;<?php echo __('Deactivate ',ADMINDOMAIN); ?></a>
	
		<?php 
			global $wpdb;
			$user_meta_table = $wpdb->prefix."usermeta";
			$chk_tour = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
			$flag=0;
			if ( in_array( 'templatic_ecosystem_plugin_custom_fields_install', $chk_tour )){ 
				$flag=1;
			}else{
				$flag=0;
			}
		?>
		</div>
		<?php endif; ?>
	</div>
</div>