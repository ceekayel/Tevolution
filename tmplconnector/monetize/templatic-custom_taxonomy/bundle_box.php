<?php
if(isset($_REQUEST['activated']) || isset($_REQUEST['deactivate']))
{
	$activated = @$_REQUEST['activated'];
	$deactivate = @$_REQUEST['deactivate'];
	if($activated)
	{
		templatic_module_activationmsg('custom_taxonomy','Custom Post Types Manager','',$mod_message='<a href='.admin_url('/admin.php?page=custom_taxonomy').'><strong>'.__('Start',DOMAIN).' </strong></a> '.__('creating a postype.',DOMAIN),$realted_mod =''); 
	}
	else
	{
		templatic_module_activationmsg('custom_taxonomy','Custom Post Types Manager','',$mod_message='',$realted_mod =''); 
	}
}?>
<div id="templatic_posttype" class="widget_div">
	
    <div class="t_dashboard_icon"><img class="dashboard_img" id="custom_post" src = "<?php echo TEMPL_PLUGIN_URL.'tmplconnector/monetize/images/custom_post.png'; ?>" /></div>
	<div class="inside">
		<div class="t_module_desc">
       <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=custom_taxonomy"> <h3 class="hndle"><span><?php echo __('Custom Post Types Manager',ADMINDOMAIN); ?></span></h3></a>
		<p class="mod_desc"><?php
		echo __('Creating a new custom post type is tricky if you&lsquo;re not very familiar with PHP & WordPress coding - This module solves that problem. With this manager you can easily create new post types and taxonomies. Every created post type / taxonomy will work flawlessly with custom fields and price packages.',ADMINDOMAIN);
		?></p>
        </div>
		<?php if(!is_active_addons('custom_taxonomy')) { ?>
		<div id="publishing-action" class="settings_style">
		<a id="publishing_action_custom_taxonomy" href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&activated=custom_taxonomy&true=1";?>" class="button-primary"><i class="fa fa-check"></i>&nbsp;<?php echo __('Activate &rarr;',ADMINDOMAIN); ?></a></div>
		<?php } 
		if (is_active_addons('custom_taxonomy')) : ?>
		<div id="publishing-action" class="settings_style">
			<a title="<?php echo __('Settings',ADMINDOMAIN); ?>" id="custom_taxonomy_setting" class="templatic-tooltip set_lnk" href="<?php echo site_url()."/wp-admin/admin.php?page=custom_taxonomy";?>"><i class="fa fa-gear"></i></a> 
			<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&deactivate=custom_taxonomy&true=0";?>" class="button"><i class="fa fa-times"></i>&nbsp;<?php echo __('Deactivate ',ADMINDOMAIN); ?></a> 
			
			<?php 
				global $wpdb;
				$user_meta_table = $wpdb->prefix."usermeta";
				$chk_tour = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
				$flag=0;
				if ( in_array( 'templatic_ecosystem_plugin_custom_taxonomy_install', $chk_tour )){ 
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
if(isset($_REQUEST['start']) && $_REQUEST['start']=="templatic_ecosystem_plugin_custom_taxonomy_install"){
	activate_single_tour($_REQUEST['start']);
}?>