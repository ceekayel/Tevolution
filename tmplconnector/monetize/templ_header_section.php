<div class="wrap tevolution-table">
<div class="icon32" id="icon-index"><br/></div>
<h2><?php echo __('Tevolution',ADMINDOMAIN);  tevolution_version();
?></h2>
<?php 
$tab = @$_REQUEST['tab'];
switch($tab){
    case 'overview':
		$sclass = "";
		$title = __("Overview",ADMINDOMAIN);
		$oclass="nav-tab-active";
		$eclass='';
		$pclass='';
		$class="tevolution_setup_boxes";
		break;
	case 'setup-steps':
		$sclass = "nav-tab-active";
		$title = __("Setup steps",ADMINDOMAIN);
		$oclass="";
		$eclass='';
		$pclass='';
		$class="tevolution_setup_boxes";
		break;
	case 'extend':
		$eclass = "nav-tab-active";
		$title = __("Extend",ADMINDOMAIN);
		$eaclass ="active";
		$oclass='';
		$pclass='';
		$sclass="";
		$class="tevolution_setup_boxes";
		break; 	
	case 'payment-gateways':
		$pclass = "nav-tab-active";
		$title = __("Payment Gateways",ADMINDOMAIN);
		$class="tevolution_setup_boxes";
		$sclass="";
		$oclass='';
		$eclass='';
		break;
	case '':
		$oclass = "nav-tab-active";
		$title = __("Overview",ADMINDOMAIN);
		$eclass='';
		$class="tevolution_setup_boxes";
		$pclass='';
		$sclass="";
		break;
} ?>
<h2 class="nav-tab-wrapper">
     <a href="?page=templatic_system_menu&amp;tab=overview" class="nav-tab <?php echo $oclass; ?>"><?php echo __('Overview',ADMINDOMAIN); ?></a>
     <a href="?page=templatic_system_menu&amp;tab=extend" class="nav-tab <?php echo $eclass; ?>"><?php echo __('Extend',ADMINDOMAIN); ?></a>
     <a href="?page=templatic_system_menu&amp;tab=payment-gateways" class="nav-tab <?php echo $pclass; ?>"><?php echo __('Payment gateways',ADMINDOMAIN); ?></a>
</h2>
<?php do_action('tevolution_plugin_list'); ?>
<div id="tevolution_bundled_boxes" class="<?php echo $class; ?>">