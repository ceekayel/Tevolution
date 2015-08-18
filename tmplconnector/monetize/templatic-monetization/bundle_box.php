<?php 
$activated = @$_REQUEST['activated'];
$deactivate = @$_REQUEST['deactivate'];
if($activated){
	$mod_message=__('Now move on to the',DOMAIN).' <a href='.admin_url('/admin.php?page=monetization').'>'.__('Monetization',DOMAIN).'</a> '.__('page where you can add different packages on your site.',DOMAIN);
	if(!is_active_addons('custom_taxonomy'))
		$mod_message.=__('You need to activate',DOMAIN).' <strong>'.__('Templatic Custom Post Types',DOMAIN).'</strong> '.__('add-on in order to use this feature on your site.',DOMAIN);
	
templatic_module_activationmsg('monetization','Monetization','',$mod_message,$realted_mod =''); 
}else{
templatic_module_activationmsg('monetization','Monetization','',$mod_message='',$realted_mod =''); 
} ?>
<div id="templatic_monetization" class="widget_div">
	<div class="t_dashboard_icon"><img class="dashboard_img" id="monetization" src = "<?php echo TEMPL_PLUGIN_URL.'tmplconnector/monetize/images/monetilazaion.png'; ?>" /></div>
	<div class="inside">
		<div class="t_module_desc">
        <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=monetization"><h3 class="hndle"><span><?php echo __('Monetization',ADMINDOMAIN); ?></span></h3></a>
		<p class="mod_desc"><?php
		echo __('Making money with WordPress is no easy task, that&lsquo;s why we created several features that will make that process easier. The Monetization module allows you to setup price packages and control the currency, coupons and payment gateways used on the site. Every price package is category and post type-specific for unparalleled flexibility. ',ADMINDOMAIN);
		?></p>
        </div>
		<?php if(is_active_addons('monetization'))
		{ ?>
		<div id="publishing-action" class="settings_style">
			<a title="<?php echo __('Settings',ADMINDOMAIN); ?>" id="WpEcoWorld_monetization" class="templatic-tooltip set_lnk" href="<?php echo site_url()."/wp-admin/admin.php?page=monetization";?>"><i class="fa fa-gear"></i></a>
			<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&deactivate=monetization&true=0";?>" class="button"><i class="fa fa-times"></i>&nbsp;<?php echo __('Deactivate',ADMINDOMAIN); ?></a> 
			
			<?php 
				global $wpdb;
				$user_meta_table = $wpdb->prefix."usermeta";
				$chk_tour = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
				$flag=0;
				if ( in_array( 'templatic_wpecoworld_plugin_monetization_install', $chk_tour )){ 
					$flag=1;
				}else{
					$flag=0;
				}
			?>
		</div>
	<?php } else { ?>
		<div id="publishing-action" class="settings_style">
		<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&activated=monetization&true=1";?>" class="button-primary"><i class="fa fa-check"></i>&nbsp;<?php echo __('Activate &rarr;',ADMINDOMAIN); ?></a></div>
	<?php } ?>
	</div>
</div>
<?php 
if(isset($_REQUEST['start']) && $_REQUEST['start']=="templatic_wpecoworld_plugin_monetization_install"){
	activate_single_tour($_REQUEST['start']);
}?>