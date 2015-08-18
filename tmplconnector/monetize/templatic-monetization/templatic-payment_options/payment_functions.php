<?php
global $wp_query,$wpdb,$wp_rewrite;
add_action('init','templ_pricing_options');
function templ_pricing_options(){
	include(TEMPL_MONETIZATION_PATH."templatic-payment_options/admin_payment_options_class.php");	/* class to fetch payment gateways */
}
/*
name : templatic_payment_option_preview_page
description : fetch all the active payment method for preview page.*/
function templatic_payment_option_preview_page()
{
		global $wpdb,$monetization;
		$paymentsql = "select * from $wpdb->options where option_name like 'payment_method_%' order by option_id";
		$paymentinfo = $wpdb->get_results($paymentsql);		
		if($paymentinfo)
		{
			$paymentOptionArray = array();
			$paymethodKeyarray = array();
			$i=0;
			foreach($paymentinfo as $paymentinfoObj)
			{
				$paymentInfo = unserialize($paymentinfoObj->option_value);
				if($paymentInfo['isactive'])
				{
					$paymethodKeyarray[] = $paymentInfo['key'];
					$paymentOptionArray[$paymentInfo['display_order']][] = $paymentInfo;
					$i++;
				}
			}
			if($i==1):?>
               	<h5 class="payment_head"> 
					<?php 
						$pay_with_title = 'Pay With';
						if(function_exists('icl_register_string')){
							icl_register_string(ADMINDOMAIN,$pay_with_title,$pay_with_title);
						}
						
						if(function_exists('icl_t')){
							$pay_with_title1 = icl_t(ADMINDOMAIN,$pay_with_title,$pay_with_title);
						}else{
							$pay_with_title1 = __($pay_with_title,ADMINDOMAIN); 
						}
						echo apply_filters('tevolution_payment_title',$pay_with_title1);
					?>
                </h5>
               <?php else:?>
				<h5 class="payment_head"> 
					<?php 
						$select_payment_method_title = SELECT_PAY_MEHTOD_TEXT;
						if(function_exists('icl_register_string')){
							icl_register_string(ADMINDOMAIN,$select_payment_method_title,$select_payment_method_title);
						}
						
						if(function_exists('icl_t')){
							$select_payment_method_title1 = icl_t(ADMINDOMAIN,$select_payment_method_title,$select_payment_method_title);
							echo apply_filters('tevolution_payment_title',$select_payment_method_title1);
						}else{							
							echo apply_filters('tevolution_payment_title',__('Select Payment Method',DOMAIN));
						}
						
					?>
                </h5>
               <?php 
			endif;
			echo '<ul class="payment_method">';
			ksort($paymentOptionArray);
			if($paymentOptionArray)
			{
				foreach($paymentOptionArray as $key=>$paymentInfoval)
				{
					$count_payopts = count($paymentOptionArray);
					for($i=0;$i<count($paymentInfoval);$i++)
					{
						
						$paymentInfo = $paymentInfoval[$i];
						$jsfunction = 'onclick="showoptions(this.value);"';
						$chked = '';
						if($key==1)
						{
							$chked = 'checked="checked"';
						}elseif($count_payopts == 1 && $paymentInfo['key'] == 'prebanktransfer' ){
							$chked = 'checked="checked"';
						}
						$disable_input = false;
						$payment_display_name = "";
						if(isset($_SESSION['custom_fields']['package_select']) && isset($_SESSION['custom_fields']['total_price']) )
						$listing_price_info = $monetization->templ_get_price_info($_SESSION['custom_fields']['package_select'],$_SESSION['custom_fields']['total_price']);
						$payment_display_name = $paymentInfo['name'];
					?>
		<li id="<?php echo $paymentInfo['key'];?>">
		  <label><input <?php echo $jsfunction;?>  type="radio" value="<?php echo $paymentInfo['key'];?>" id="<?php echo $paymentInfo['key'];?>_id" name="paymentmethod" <?php echo $chked; if($disable_input){echo "disabled=true";}?> />  
						<?php 
							if(function_exists('icl_register_string')){
								$context = DOMAIN;
								icl_register_string($context,$payment_display_name,$payment_display_name);
							}
							if(function_exists('icl_t')){
								$payment_display_name = icl_t(DOMAIN,$payment_display_name,$payment_display_name);
							}
							else
							{
								$payment_display_name = sprintf(__('%1$s',DOMAIN), __($payment_display_name,DOMAIN));
							}
							echo $payment_display_name;
						?>
						</label> 
					</li>
		  <?php
					}
				}
			}else
			{
			?>
			<li><?php echo NO_PAYMENT_METHOD_MSG;?></li>
			<?php
			}
			
		?>
 	  
  </ul>
  <?php
  if($paymentOptionArray)
		{
			echo "<div class='payment_method payment_credit_card_info'>";
			foreach($paymentOptionArray as $key=>$paymentInfoval)
			{
				$count_payopts = count($paymentOptionArray);
				for($i=0;$i<count($paymentInfoval);$i++)
				{
					
					$paymentInfo = $paymentInfoval[$i];
					$jsfunction = 'onclick="showoptions(this.value);"';
					$chked = '';
					if($key==1)
					{
						$chked = 'checked="checked"';
					}elseif($count_payopts == 1 && $paymentInfo['key'] == 'prebanktransfer' ){
						$chked = 'checked="checked"';
					}
					$disable_input = false;
					$payment_display_name = "";
					if(isset($_SESSION['custom_fields']['package_select']) && isset($_SESSION['custom_fields']['total_price']) )
					$listing_price_info = $monetization->templ_get_price_info($_SESSION['custom_fields']['package_select'],$_SESSION['custom_fields']['total_price']);
					$payment_display_name = $paymentInfo['name'];
				
					if(file_exists(get_tmpl_plugin_directory() . 'Tevolution-'.$paymentInfo['key'].'/includes/'.$paymentInfo['key'].'.php'))
					{
						include(get_tmpl_plugin_directory() . 'Tevolution-'.$paymentInfo['key'].'/includes/'.$paymentInfo['key'].'.php');
					}
					
					
					if(file_exists(TEMPL_PAYMENT_FOLDER_PATH.$paymentInfo['key'].'/'.$paymentInfo['key'].'.php'))
					{
					
						include_once(TEMPL_PAYMENT_FOLDER_PATH.$paymentInfo['key'].'/'.$paymentInfo['key'].'.php');
						
					} 				 
				}
			}
			echo '</div>';
		}

	}
		?>
		<script type="text/javascript">
		 /* <![CDATA[ */
		function showoptions(paymethod)
		{
		<?php
		for($i=0;$i<count($paymethodKeyarray);$i++)
		{
		?>
		showoptvar = '<?php echo $paymethodKeyarray[$i]?>options';
		if(document.getElementById(showoptvar))
		{
			document.getElementById(showoptvar).style.display = 'none';
			if(paymethod=='<?php echo $paymethodKeyarray[$i]?>')
			{
				document.getElementById(showoptvar).style.display = '';
			}
		}
		
		<?php
		}	
		?>
		}
		<?php
		for($i=0;$i<count($paymethodKeyarray);$i++)
		{
		?>
		if(document.getElementById('<?php echo $paymethodKeyarray[$i];?>_id').checked)
		{
		showoptions(document.getElementById('<?php echo $paymethodKeyarray[$i];?>_id').value);
		}
		<?php
		}	
		?>
		/* ]]> */
		 </script>
		 <?php	
}
/*
name : templatic_get_payment_options
description : fetch payment option values. */
function templatic_get_payment_options($method)
{
	global $wpdb;
	$paymentsql = "select * from $wpdb->options where option_name like 'payment_method_$method'";
	$paymentinfo = $wpdb->get_results($paymentsql);
	if($paymentinfo)
	{
		foreach($paymentinfo as $paymentinfoObj)
		{
			$option_value = unserialize($paymentinfoObj->option_value);
			$paymentOpts = $option_value['payOpts'];
			$optReturnarr = array();
			for($i=0;$i<count($paymentOpts);$i++)
			{
				$optReturnarr[$paymentOpts[$i]['fieldname']] = $paymentOpts[$i]['value'];
			}
			//echo "<pre>";print_r($optReturnarr);
			return $optReturnarr;
		}
	}
}
/*
Name: payment_menthod_response_url
Desc : Return Response url of payment method
*/
function payment_menthod_response_url($paymentmethod,$last_postid,$renew,$pid,$payable_amount,$trans_id='')
{
	global $current_user;	
	if(isset($_REQUEST['lang']) && $_REQUEST['lang']!="")
	{
		$language="&lang=".$_REQUEST['lang'];
	}
	if($pid>0 && $renew=='')
	{
		wp_redirect(get_author_posts_url($current_user->ID));
		exit;
	}else
	{
		if($payable_amount == '' || $payable_amount <= 0)
		{
			$suburl .= "&pid=$last_postid";
			wp_redirect(get_option('siteurl')."/?page=success$suburl");
			exit;
		}else
		{
			$paymentmethod = $paymentmethod;
			$paymentSuccessFlag = 0;
			if($paymentmethod == 'prebanktransfer' || $paymentmethod == 'payondelivery')
			{
				if($renew =='upgrade'){
					$suburl = "&upgrade=1";
				}elseif(($renew)){
					$suburl = "&renew=1";
				}
				$suburl .= "&pid=$last_postid";
				if($trans_id!=''){
					$suburl .= "&trans_id=$trans_id";
				}
				if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
					global $sitepress;
					if(isset($_REQUEST['lang'])){
						$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl.$_REQUEST['lang'];
						
						
					}elseif($sitepress->get_current_language()){
							if($sitepress->get_default_language() != $sitepress->get_current_language()){
								$url = site_url().'/'.$sitepress->get_current_language().'/?page=success&paydeltype='.$paymentmethod.$suburl;
							}else{
								$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl;
							}
					}else{
						$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl;
					}
				}else{
					$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl;
				}
				echo '<script type="text/javascript">location.href="'.$url.'";</script>';
			}
			else
			{
				if(file_exists(TEMPL_PAYMENT_FOLDER_PATH.$paymentmethod.'/'.$paymentmethod.'_response.php') && $paymentmethod == 'paypal')
				{
					include_once(TEMPL_PAYMENT_FOLDER_PATH.$paymentmethod.'/'.$paymentmethod.'_response.php');
				}
				elseif(file_exists(get_tmpl_plugin_directory(). 'Tevolution-'.$paymentmethod.'/includes/'.$paymentmethod.'_response.php'))
				{
					include_once(get_tmpl_plugin_directory(). 'Tevolution-'.$paymentmethod.'/includes/'.$paymentmethod.'_response.php');
				}
			}	
		}
	}
}
/*
Name:templ_payment_methods
Desc : List all payment methods installed
*/
function templ_payment_methods(){ 
	global $wpdb;
	if(isset($_REQUEST['install']) && $_REQUEST['install']!='' || isset($_REQUEST['uninstall']) && $_REQUEST['uninstall']!='')
	{
		if($_REQUEST['install'])
		{
			$foldername = $_REQUEST['install'];
		}else
		{
			$foldername = $_REQUEST['uninstall'];
		}
		if(file_exists(get_tmpl_plugin_directory() . 'Tevolution-'.$foldername))
		{
			include(get_tmpl_plugin_directory() . 'Tevolution-'.$foldername.'/includes/install.php');
		}
		elseif(file_exists(plugin_dir_path( __FILE__ ).'payment/'.$foldername))
		{
			include(plugin_dir_path( __FILE__ ).'payment/'.$foldername.'/install.php');
		}else
		{
			$install_message = __('Sorry there is no such payment gateway',ADMINDOMAIN);	
		}
	}
	if( @$_GET['status']!='' && @$_GET['id']!='')
	{
		$paymentupdsql = "select option_value from $wpdb->options where option_id='".@$_GET['id']."'";
		$paymentupdinfo = $wpdb->get_results($paymentupdsql);
		if($paymentupdinfo)
		{
			foreach($paymentupdinfo as $paymentupdinfoObj)
			{
				$option_value = unserialize($paymentupdinfoObj->option_value);
				$option_value['isactive'] = $_GET['status'];
				$option_value_str = serialize($option_value);
				$message = __('Status updated successfully.',ADMINDOMAIN);
			}
		}	
		$updatestatus = "update $wpdb->options set option_value= '$option_value_str' where option_id='".$_GET['id']."'";
		$wpdb->query($updatestatus);
	}
	?>
<div class="wrap">
	<div class="tevo_sub_title"><?php echo __('Manage Currency',ADMINDOMAIN); ?></div>
	<p class="tevolution_desc"><?php echo __('Define the currency in which you want to take payment on your site, you can set currency position with its amount as per your currency standards.',ADMINDOMAIN); ?></p>
	<?php
		if(@$_REQUEST['submit_currency'] != '')
			{
				update_option('currency_symbol',$_REQUEST['currency_symbol']);
				update_option('currency_code',$_REQUEST['currency_code']);
				update_option('currency_pos',$_REQUEST['currency_pos']);
			}
		?>
		<script type="text/javascript">
		function check_currency_form()
		{
			jQuery.noConflict();
			var currency_symbol = jQuery('#currency_symbol').val();
			var currency_code = jQuery('#currency_code').val();
			if( currency_symbol == "" || currency_code == "" )
			{
				if(currency_symbol =="")
					jQuery('#cur_sym').addClass('form-invalid');
					jQuery('#cur_sym').change(func_cur_sym);
				if(currency_code == '')
					jQuery('#cur_code').addClass('form-invalid');
					jQuery('#cur_code').change(func_cur_code);
				return false;
			}
			function func_cur_sym()
			{
				var currency_symbol = jQuery('#package_name').val();
				if( currency_symbol == '' )
				{
					jQuery('#cur_sym').addClass('form-invalid');
					return false;
				}
				else if( currency_symbol != '' )
				{
					jQuery('#cur_sym').removeClass('form-invalid');
					return true;
				}
			}
			function func_cur_code()
			{
				var currency_code = jQuery('#package_amount').val();
				if( currency_code == '' )
				{
					jQuery('#cur_code').addClass('form-invalid');
					return false;
				}
				else if( currency_code != '' )
				{
					jQuery('#cur_code').removeClass('form-invalid');
					return true;
				}
			}		
		
		}
		function currency_pos_change(str,sym)
		{ 
				if(str == 2){
					document.getElementById('show_price_exp').innerHTML = "e.g. "+sym + " 10";
				}else if(str == 3){
					document.getElementById('show_price_exp').innerHTML = "e.g. "+"10"+sym;
				}else if(str == 4){
					document.getElementById('show_price_exp').innerHTML = "e.g. "+"10 "+sym;
				}else{
					document.getElementById('show_price_exp').innerHTML = "e.g. "+sym+"10";
				}
		}
		</script>
		<div class="wrap"><br/>
		<form action="<?php echo site_url();?>/wp-admin/admin.php?page=monetization&tab=payment_options" method="post" name="currency_settings" id="currency_form" onclick="return check_currency_form();">
			<table style="width:60%"  class="form-table">
			<tbody>
				<tr >
					<td valign="top">
					<label for="currency_symbol" class="form-textfield-label"><?php echo CURRENCY_SYMB; ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
					</td>
					<td valign="top">
					<div id="cur_sym" class="currency_sets">
						<input type="text" class="" class="form-radio radio" value="<?php echo get_option('currency_symbol'); ?>" name="currency_symbol" id="currency_symbol" PLACEHOLDER="<?php echo __('Currency Symbol',ADMINDOMAIN); ?>"/>
                       
					</div>
					<div id="cur_code" class="currency_sets">
						<input type="text" class="" class="form-radio radio" value="<?php echo get_option('currency_code'); ?>" name="currency_code" id="currency_code" PLACEHOLDER="<?php echo __('Currency Code',ADMINDOMAIN); ?>"/>
						
					</div>
					<p class="description"><?php echo __('Your currency symbol can be any character like alphabets, numbers, alplhanumeric etc,',ADMINDOMAIN);?> <span class="description"><?php echo  CURRENCY_CODE_DESC; ?></p>
					</td>
				</tr>
				
				<tr >
					<td valign="top">
						<label for="currency_pos" class="form-textfield-label"><?php echo CURRENCY_POS; ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
					</td>
					<td colspan="2">
					
						<select name="currency_pos" id="currency_pos" onchange="currency_pos_change(this.value,'<?php echo get_option('currency_symbol'); ?>');">
						<option value="1" <?php if(get_option('currency_pos') == '1') { echo "selected=selected"; } ?>><?php echo SYMB_BFR_AMT; ?></option>
						<option value="2" <?php if(get_option('currency_pos') == '2') { echo "selected=selected"; } ?>><?php echo SPACE_BET_BFR_AMT; ?></option>
						<option value="3" <?php if(get_option('currency_pos') == '3') { echo "selected=selected"; } ?>><?php echo SYM_AFTR_AMT; ?></option>
						<option value="4" <?php if(get_option('currency_pos') == '4') { echo "selected=selected"; } ?>><?php echo SPACE_BET_AFTR_AMT; ?></option>
						</select><br/>
						
						<div id="show_price_exp"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" class="button-primary form-submit form-submit submit" value="<?php echo __('Save Currency',ADMINDOMAIN);?>" name="submit_currency" id="submit_currency">
					</td>
				</tr>
			</tbody>
			</table>
		</form>
		<br/>
		</div>
		<div class="tevo_sub_title"><?php echo __('Manage Payment Options',ADMINDOMAIN); ?></div>
		<p class="tevolution_desc"><?php echo __('Manage the available payment gateways. To download and install more please visit the <a href="http://templatic.com/members/member" title="Plugins" target="_blank">member area (Plugins Download section)</a>',ADMINDOMAIN); ?>.</p>
	<div class="tevolution_paymentgatway">
	<?php
	wp_enqueue_script( 'jquery-ui-sortable' );
	
	$wp_list_payment_options = New wp_list_payment_options();
	$wp_list_payment_options->prepare_items();
	$wp_list_payment_options->display();
	?>
	</div>
</div>
<?php 
}
/*
Name :return_page
Desc : payment options return page 
*/
add_action( 'init', 'return_page' );
function return_page()
{
	if(isset($_REQUEST['ptype']) && $_REQUEST['ptype'] == 'return'){
		if( file_exists( TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/return.php" ) ){
			include (TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/return.php");
			exit;
		}
	}
	if(isset($_REQUEST['ptype']) && $_REQUEST['ptype'] == 'cancel'){
		if( file_exists( TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/cancel.php" ) ){
			include (TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/cancel.php");
			exit;
		}elseif( file_exists( TEMPL_MONETIZATION_PATH."templatic-payment_options/payment_cancel.php" ) ){
			include ( TEMPL_MONETIZATION_PATH."templatic-payment_options/payment_cancel.php" );
			exit;
		}
	}
	if(isset($_REQUEST['ptype']) && $_REQUEST['ptype'] == 'notifyurl'){
		if( file_exists( TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/ipn_process.php" ) ){
			include (TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/ipn_process.php");
			exit;
		}
	}
}
/*
 * Add action for display the paypal successful return message display
 * Function Name: successfull_return_paypal_content
 * Return: display the paypal successful message display
 */
add_action('paypal_successfull_return_content','successfull_return_paypal_content',10,3);
function successfull_return_paypal_content($post_id,$subject,$content)
{
	echo "<h1 class='page-title'>".$subject."</h1>";
	do_action('after_successfull_return_paypal_process');
	echo "<p>".$content."</p>";
}
/*
 * Function Name: tevolution_paymentgateway_sortorder
 * Return: sort order of payment gateway
 */
add_action('wp_ajax_paymentgateway_sortorder','tevolution_paymentgateway_sortorder');
function tevolution_paymentgateway_sortorder(){
	
	$user_id = get_current_user_id();	
	if(isset($_REQUEST['paging_input']) && $_REQUEST['paging_input']!=0 && $_REQUEST['paging_input']!=1){
		$package_per_page=get_user_meta($user_id,'package_per_page',true);
		$j =$_REQUEST['paging_input']*$package_per_page+1;
		$test='';
		$i=$package_per_page;		
		for($j; $j >= count($_REQUEST['payment_order']);$j--){			
			if($_REQUEST['custom_sort_order'][$i]!=''){
				$sort_order['display_order']=$j;
				$payment_info=get_option('payment_method_'.$_REQUEST['payment_order'][$i]);
				update_option('payment_method_'.$_REQUEST['payment_order'][$i],array_merge($payment_info,$sort_order));
				//update_post_meta($_REQUEST['custom_sort_order'][$i],'sort_order',$j);	
			}
			$i--;	
		}
	}else{
		$j=1;		
		for($i=0;$i<count($_REQUEST['payment_order']);$i++){
			$sort_order['display_order']=$j;
			$payment_info=get_option('payment_method_'.$_REQUEST['payment_order'][$i]);			
			update_option('payment_method_'.$_REQUEST['payment_order'][$i],array_merge($payment_info,$sort_order));
			//update_post_meta($_REQUEST['custom_sort_order'][$i],'sort_order',$j);		
			$j++;
		}
	}	
	exit;
}
/*
Name: payment_menthod_response_url
Desc : Return Response url of payment method
*/
function payment_upgrade_response_url($paymentmethod,$last_postid,$renew,$pid,$payable_amount)
{
	global $current_user;	
	if(isset($_REQUEST['lang']) && $_REQUEST['lang']!="")
	{
		$language="&lang=".$_REQUEST['lang'];
	}
	if($pid>0 && $renew=='')
	{
		wp_redirect(get_author_posts_url($current_user->ID));
		exit;
	}else
	{
		if($payable_amount == '' || $payable_amount <= 0)
		{
			$suburl .= "&pid=$last_postid";
			wp_redirect(get_option('siteurl')."/?page=success$suburl");
			exit;
		}else
		{
			$paymentmethod = $paymentmethod;
			$paymentSuccessFlag = 0;
			if($paymentmethod == 'prebanktransfer' || $paymentmethod == 'payondelivery')
			{
	
				$suburl = "&upgrade=1";
				
				$suburl .= "&pid=$last_postid";
				if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
					global $sitepress;
					if(isset($_REQUEST['lang'])){
						$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl.$_REQUEST['lang'];
						
						
					}elseif($sitepress->get_current_language()){
						
						if($sitepress->get_default_language() != $sitepress->get_current_language()){
							$url = site_url().'/'.$sitepress->get_current_language().'/?page=success&paydeltype='.$paymentmethod.$suburl;
						}else{
							$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl;
						}
					}else{
						$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl;
					}
				}else{
					$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl;
				}
				echo '<script type="text/javascript">location.href="'.$url.'";</script>';
			}
			else
			{
				if(file_exists(TEMPL_PAYMENT_FOLDER_PATH.$paymentmethod.'/'.$paymentmethod.'_response.php') && $paymentmethod == 'paypal')
				{
					include_once(TEMPL_PAYMENT_FOLDER_PATH.$paymentmethod.'/'.$paymentmethod.'_response.php');
				}
				elseif(file_exists(get_tmpl_plugin_directory(). 'Tevolution-'.$paymentmethod.'/includes/'.$paymentmethod.'_response.php'))
				{
					include_once(get_tmpl_plugin_directory(). 'Tevolution-'.$paymentmethod.'/includes/'.$paymentmethod.'_response.php');
				}
			}	
		}
	}
}
?>