<?php
/*
 * Currency and payment listing functions
 */

/* class to fetch payment gateways */
add_action('admin_init','templ_pricing_options');
function templ_pricing_options(){
	include(TEMPL_MONETIZATION_PATH."templatic-payment_options/admin_payment_options_class.php");	
}
/*
	List all payment methods installed
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
			$install_message = __('Sorry there is no such payment gateway','templatic-admin');	
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
				$message = __('Status updated successfully.','templatic-admin');
			}
		}	
		$updatestatus = "update $wpdb->options set option_value= '$option_value_str' where option_id='".$_GET['id']."'";
		$wpdb->query($updatestatus);
	}
	?>
	<div class="wrap">
		<div class="tevolution_paymentgatway">
		<div class="tevo_sub_title"><?php echo __('Manage Payment Options','templatic-admin'); ?></div>
		<p class="tevolution_desc"><?php echo __('Manage the available payment gateways. To download and install more please visit the <a href="http://templatic.com/members/member" title="Plugins" target="_blank">member area (Plugins Download section)</a>','templatic-admin'); ?>.</p>
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
 * sort order of payment gateway
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
				/*update_post_meta($_REQUEST['custom_sort_order'][$i],'sort_order',$j);	*/
			}
			$i--;	
		}
	}else{
		$j=1;		
		for($i=0;$i<count($_REQUEST['payment_order']);$i++){
			$sort_order['display_order']=$j;
			$payment_info=get_option('payment_method_'.$_REQUEST['payment_order'][$i]);			
			update_option('payment_method_'.$_REQUEST['payment_order'][$i],array_merge($payment_info,$sort_order));
			/*update_post_meta($_REQUEST['custom_sort_order'][$i],'sort_order',$j);		*/
			$j++;
		}
	}	
	exit;
}

/* Currency settings for for back end */

function tmpl_currency_settings(){ ?>
	<div class="tevo_sub_title"><?php echo __('Manage Currency','templatic-admin'); ?></div>
	<p class="tevolution_desc"><?php echo __('Define the currency in which you want to take payment on your site, you can set currency position with its amount as per your currency standards.','templatic-admin'); ?></p>
	<?php
		if(@$_REQUEST['submit_currency'] != '')
		{
			update_option('currency_symbol',$_REQUEST['currency_symbol']);
			update_option('currency_code',$_REQUEST['currency_code']);
			update_option('currency_pos',$_REQUEST['currency_pos']);
			update_option('tmpl_price_thousand_sep',$_REQUEST['tmpl_price_thousand_sep']);
			update_option('tmpl_price_num_decimals',$_REQUEST['tmpl_price_num_decimals']);
			update_option('tmpl_price_decimal_sep',$_REQUEST['tmpl_price_decimal_sep']);
		}	
		?>
		<script type="text/javascript" async >
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
		<form action="<?php echo site_url();?>/wp-admin/admin.php?page=monetization&tab=currency_settings" method="post" name="currency_settings" id="currency_form" onclick="return check_currency_form();">
			<table style="width:60%"  class="form-table email-wide-table">
			<tbody>
				<tr >
					<th valign="top">
					<label for="currency_symbol" class="form-textfield-label"><?php echo __(CURRENCY_SYMB,'templatic-admin'); ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
					</th>
					<td valign="top">
					<div id="cur_sym" class="currency_sets">
						<input type="text" class="" class="form-radio radio" value="<?php echo get_option('currency_symbol'); ?>" name="currency_symbol" id="currency_symbol" PLACEHOLDER="<?php echo __('Currency Symbol','templatic-admin'); ?>"/>
                       
					</div>
					<div id="cur_code" class="currency_sets">
						<input type="text" class="" class="form-radio radio" value="<?php echo get_option('currency_code'); ?>" name="currency_code" id="currency_code" PLACEHOLDER="<?php echo __('Currency Code','templatic-admin'); ?>"/>
						
					</div>
					<p class="description"><?php echo __('Your currency symbol can be any character like alphabets, numbers, alplhanumeric etc,','templatic-admin');?> <span class="description"><?php echo  CURRENCY_CODE_DESC; ?></p>
					</td>
				</tr>
				
				<tr >
					<th valign="top">
						<label for="currency_pos" class="form-textfield-label"><?php echo __(CURRENCY_POS,'templatic-admin'); ?> <span class="required"><?php echo REQUIRED_TEXT; ?></span></label>
					</th>
					<td colspan="2">
					
						<select name="currency_pos" id="currency_pos" onchange="currency_pos_change(this.value,'<?php echo get_option('currency_symbol'); ?>');">
						<option value="1" <?php if(get_option('currency_pos') == '1') { echo "selected=selected"; } ?>><?php echo __(SYMB_BFR_AMT,'templatic-admin'); ?></option>
						<option value="2" <?php if(get_option('currency_pos') == '2') { echo "selected=selected"; } ?>><?php echo __(SPACE_BET_BFR_AMT,'templatic-admin'); ?></option>
						<option value="3" <?php if(get_option('currency_pos') == '3') { echo "selected=selected"; } ?>><?php echo __(SYM_AFTR_AMT,'templatic-admin'); ?></option>
						<option value="4" <?php if(get_option('currency_pos') == '4') { echo "selected=selected"; } ?>><?php echo __(SPACE_BET_AFTR_AMT,'templatic-admin'); ?></option>
						</select><br/>
						
						<div id="show_price_exp"></div>
					</td>
				</tr>
				</tr>
				<tr valign="top">
						<th class="" >
							<label for="tmpl_price_thousand_sep" class="form-textfield-label"><?php echo __('Thousand Separator','templatic-admin'); ?></label>
						</th>
	                    <td class="forminp forminp-text">
							<?php $tmpl_price_thousand_sep = get_option('tmpl_price_thousand_sep'); 
							?>
	                    	<input type="text" class="" value="<?php echo $tmpl_price_thousand_sep; ?>" style="width:50px;" id="tmpl_price_thousand_sep" name="tmpl_price_thousand_sep">
							<p class="description"><?php echo __('This sets the thousand separator of displayed prices.','templatic-admin');?></p>
						</td>
	            </tr>
				<tr valign="top">
						<th class="">
							<label for="tmpl_price_decimal_sep" class="form-textfield-label"><?php echo __('Decimal Separator','templatic-admin'); ?></label>
						</th>
						<?php $tmpl_price_decimal_sep = get_option('tmpl_price_decimal_sep'); 
								if(!$tmpl_price_decimal_sep){ $tmpl_price_decimal_sep =''; } 
						?>
	                    <td class="forminp forminp-text">
	                    	<input type="text" class="" value="<?php echo $tmpl_price_decimal_sep; ?>" style="width:50px;" id="tmpl_price_decimal_sep" name="tmpl_price_decimal_sep">
							<p class="description"><?php echo __('This sets the decimal separator of displayed prices.','templatic-admin');?></p>
						</td>
	            </tr>
				<tr valign="top">
						<th class="">
							<label for="tmpl_price_num_decimals" class="form-textfield-label"><?php echo __('Number of Decimals','templatic-admin'); ?></label>
						</th>
	                    <td class="forminp forminp-number">
							<?php $tmpl_price_num_decimals = get_option('tmpl_price_num_decimals');
								
								if($tmpl_price_num_decimals=='' && $tmpl_price_num_decimals==0){ $tmpl_price_num_decimals ='2'; }
							?>
	                    	<input type="number" step="1" min="0" class="" value="<?php echo $tmpl_price_num_decimals; ?>" style="width:50px;" id="tmpl_price_num_decimals" name="tmpl_price_num_decimals">
							<p class="description"><?php echo __('This sets the number of decimal points shown in displayed prices.','templatic-admin');?></p>
						</td>
	            </tr>
				<tr>
					<td colspan="2">
						<input type="submit" class="button-primary button button-hero" value="<?php echo __('Save Settings','templatic-admin');?>" name="submit_currency" id="submit_currency">
					</td>
				</tr>
			</tbody>
			</table>
		</form>
		<br/>
		</div>
	<?php
}
?>