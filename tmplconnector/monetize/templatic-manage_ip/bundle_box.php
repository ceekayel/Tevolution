<?php 
$activated = @$_REQUEST['activated'];
$deactivate = @$_REQUEST['deactivate'];
if($activated){
	templatic_module_activationmsg('manage_ip','Security Manager','',$mod_message=__('You can set up the IPs straight away from',DOMAIN).' <a href='.admin_url('/admin.php?page=templatic_settings&tab=security-settings').'><strong>'.__('here',DOMAIN).'</strong></a> '.__('to block them on your site. Or You can block the IPs from inside the post. You will be able to see a meta box on Add / Edit post page. You can block the user from there as well.',DOMAIN),$realted_mod =''); 
}else{
	templatic_module_activationmsg('manage_ip','Security Manager','',$mod_message='',$realted_mod =''); 
}?>
<div id="templatic_manage_ip_module" class="widget_div">
     <div class="t_dashboard_icon"><img class="dashboard_img" id="security_manager" src = "<?php echo TEMPL_PLUGIN_URL.'tmplconnector/monetize/images/security_manager.png'; ?>" /></div>
    	<div class="inside">
          <div class="t_module_desc">          
               <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=templatic_settings&tab=security-settings"> <h3 class="hndle"><span><?php echo __('Security Manager',DOMAIN); ?></span></h3></a>
               <p class="mod_desc"><?php echo __('This module will allow you to block visitors based on their IP. Blocked visitors will not be able to submit content on the site.',DOMAIN);?></p>
          </div>
          <?php if(!is_active_addons('manage_ip')) { update_option('manage_ip_enabled','No'); ?>
               <div id="publishing-action" class="settings_style">
                    <a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&activated=manage_ip&true=1";?>" class="button-primary"><i class="fa fa-check"></i>&nbsp;<?php echo __('Activate &rarr;',DOMAIN); ?></a>
               </div>
          <?php } 		
          if (is_active_addons('manage_ip')) : ?>
          <div id="publishing-action" class="settings_style">
               <a title="<?php echo __('Settings',DOMAIN); ?>" class="templatic-tooltip set_lnk" href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_settings&tab=security-settings";?>" ><i class="fa fa-gear"></i></a>
               <a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&deactivate=manage_ip&true=0";?>" class="button"><i class="fa fa-times"></i>&nbsp;<?php echo __('Deactivate',DOMAIN); ?></a> 
          </div>
          <?php endif; ?>
     </div>
</div>