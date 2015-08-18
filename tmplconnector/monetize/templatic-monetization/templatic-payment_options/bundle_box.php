<?php 
$activated = @$_REQUEST['activated'];
$deactivate = @$_REQUEST['deactivate'];
if($activated){
templatic_module_activationmsg('templatic_payment_options','Payment Options','',$mod_message=__('You can Activate or Deactivate the payment gateways straight away from',ADMINDOMAIN).' <a href='.site_url()."/wp-admin/admin.php?page=monetization".'><strong>'.__('here',ADMINDOMAIN).'</strong></a>. '.__('On activation of each gateway, it will automatically integrate with your site. This module is dependent on Custom Post Types module hence make sure you are active on Custom Post type manager module as well in order to use the payment gateways on your site.',ADMINDOMAIN),$realted_mod =''); 
}else{
templatic_module_activationmsg('templatic_payment_options','Payment Options','',$mod_message='',$realted_mod =''); 
}?>
<div id="templatic_payments" class="widget_div">
	<div class="inside">
		<div class="t_module_desc">
        <h3 class="hndle"><span><?php echo __('Templatic - Payment Options',ADMINDOMAIN); ?></span></h3>
        <p class="mod_desc"><?php
		echo __('Another classic feature from templatic. This feature adds the payment gateways like Paypal, Paypal Pro, Google Checkout etc. on your site. You are free to edit these options and also you can enable or disable each of them. ',ADMINDOMAIN);
		?></p>
        </div>
		<?php if(!is_active_addons('templatic_payment_options')) { ?>
		<div id="publishing-action" class="settings_style">
		<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&activated=templatic_payment_options&true=1";?>" class="button-primary"><i class="fa fa-check"></i>&nbsp;<?php echo __('Activate &rarr;',ADMINDOMAIN); ?></a></div>
		
		<?php } 
		if (is_active_addons('templatic_payment_options')) : ?>
		<div id="publishing-action" class="settings_style">
			<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&deactivate=templatic_payment_options&true=0";?>" class="button"><i class="fa fa-times"></i>&nbsp;<?php echo __('Deactivate ',ADMINDOMAIN); ?></a>
			<a title="<?php echo __('Settings',ADMINDOMAIN); ?>" class="templatic-tooltip set_lnk" href="<?php echo site_url()."/wp-admin/admin.php?page=monetization";?>"><i class="fa fa-gear"></i><span class="custom">
			<?php echo __('This link will redirect you to Payment Options page where you can Activate or Deactivate Payment gateways on your site.',ADMINDOMAIN);?>
			<b class="tooltip_arrow"></b>
			</span></a>
		</div>
		<?php endif; ?>
	</div>
</div>