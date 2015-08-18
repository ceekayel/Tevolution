<?php if((isset($_REQUEST['tab']) && $_REQUEST['tab'] !='overview')){ ?>
</div>
<?php }?>
</div>
</div>
<?php if((isset($_REQUEST['tab']) && $_REQUEST['tab'] =='overview') || !isset($_REQUEST['tab'])){ ?>
<div id="tevolution_dashboard_sidebar">
<div id="poststuff">
	<div class="postbox " id="formatdiv">
		<div class="handlediv" title="Click to toggle">
		<br>
		</div>
		<h3 class="hndle">
		<span><?php echo __('Server Time',ADMINDOMAIN); ?></span>
		</h3>
		<div class="inside">
				<?php do_action('tevolution_details'); ?>
		</div>
	</div>	
</div>
<div id="poststuff">
	<div class="postbox " id="formatdiv">
		<div class="handlediv" title="Click to toggle">
		<br>
		</div>
		<h3 class="hndle">
		<span><?php echo __('Licence key',ADMINDOMAIN); ?></span>
		</h3>
		<div id="licence_fields">
			<form action="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu";?>" name="" method="post">
			<div class="inside">
			 <div id="licence_fields">
			<p><?php echo __('Enter the license key in order to unlock the plugin and enable automatic updates.',ADMINDOMAIN); ?></p>
					<input type="password" name="licencekey" id="licencekey" value="<?php echo get_option('templatic_licence_key_'); ?>" size="30" max-length="36" PLACEHOLDER="templatic.com purchase code"/>
					<input type="submit" accesskey="p" value="<?php echo __('Verify',ADMINDOMAIN);?>" class="button button-primary button-large" id="Verify" name="Verify">
					<?php do_action('tevolution_error_message'); ?>
			</div>
			</div>
			</form>
		<div class="licence_fields">
	</div>	
</div>
</div>
<?php } ?>
