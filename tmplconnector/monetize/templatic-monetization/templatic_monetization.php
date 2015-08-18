<div class="wrap">
<div id="icon-edit" class="icon32"><br></div>
<h2><?php echo __('Monetization',ADMINDOMAIN);?></h2>
<p class="tevolution_desc"><?php echo __('Tevolution provides a lot of options using which you can monetize your site. You can charge your users for submitting their listing in different ways using price packages, we have a wide range of payment gateways using which you can charge your users. What&acute;s more ? You can even add discount coupon codes to give seasonal discount to your users.',ADMINDOMAIN); ?></p>
<?php if(@$message){?>
<div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px; width:47%" >
  <?php echo $message;?>
</div>
<?php }?>
<div id="icon-options-general" class="icon32"><br></div>
	<h2 class="nav-tab-wrapper">
	<?php  
	  	$tab = '';
		if(isset($_REQUEST['tab']))
		{
			$tab = $_REQUEST['tab'];
		}
		$class = ' nav-tab-active'; ?>
	
	 <a id="packages_settings" class='nav-tab<?php if($tab == 'packages' || $tab == '' ) echo $class;  ?>' href='?page=monetization&tab=packages'><?php echo __('Price Packages',ADMINDOMAIN); ?> </a>
	 <a id="payment_options_settings" class='nav-tab<?php if($tab == 'payment_options') echo $class;  ?>' href='?page=monetization&tab=payment_options'><?php echo __('Payment Gateways',ADMINDOMAIN); ?> </a>
	 <a class='nav-tab<?php if($tab == 'manage_coupon') echo $class;  ?>' href='?page=monetization&tab=manage_coupon'><?php echo __('Manage Coupons',ADMINDOMAIN); ?> </a>
     <?php do_action('templatic_monetizations_tabs',$tab,$class); // add additional tabs in monetization section?>     
    </h2>
	<?php
		if($tab == 'payment_options' )
		{ 
			/* to fetch current installed payment add-ons */
			payment_option_plugin_function();
		}
		elseif( $tab == 'manage_coupon'  )
		{
			manage_coupon_plugin_function();
		}elseif(isset($_REQUEST['tab']) && ($_REQUEST['tab']!='payment_options' && $_REQUEST['tab']!='manage_coupon' && $_REQUEST['tab']!='packages' )){
				
			do_action('templatic_monetizations_tab_content'); // add additional tabs conrent section
			
		}else
		{
			if((isset($_REQUEST['action']) && $_REQUEST['action'] == 'add_package') || (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'))
			 {
				 include (TEMPL_MONETIZATION_PATH."add_price_packages.php");
			 }
			 else
			 {
				if($tab == 'packages' || $tab == ''){
				 include (TEMPL_MONETIZATION_PATH."price_packages_list.php"); }
			 }
		}		
?>
</div>