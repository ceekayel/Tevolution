<?php
if(!defined('PLEASE_SELECT')) 
	define('PLEASE_SELECT',__('Please Select',DOMAIN));
global $wpdb,$transection_db_table_name,$external_queries;
$transection_db_table_name = $wpdb->prefix."transactions";
if(count(@$_REQUEST['cf'])>0)
{
	do_action('tevolution_transaction_msg');
}
if(isset($_REQUEST['Search']) && $_REQUEST['Search'])
{
	global $post,$wpdb,$transection_db_table_name,$external_queries;
	
		$post_table = $wpdb->prefix."posts";
		if(@$_REQUEST['post_types'] || @$_REQUEST['type']){
			$select_post_table = " , $post_table as p ";
		}	
		$transsql_select = "select * ";
		$transsql_count = "select count(t.trans_id) ";
		$transsql_from= " from $transection_db_table_name as t $select_post_table";
		$transsql_conditions = " where 1=1 ";
		if(@$_REQUEST['id'])
		{
			$id = @$_REQUEST['id'];
			$transsql_conditions .= " and t.post_id = $id";
		}
		if(@$_REQUEST['srch_orderno'])
		{
			$srch_orderno = @$_REQUEST['srch_orderno'];
			$transsql_conditions .= " and t.trans_id = $srch_orderno";
		}
		if(@$_REQUEST['srch_name'])
		{
			$srch_name = @$_REQUEST['srch_name'];
			$transsql_conditions .= " and (t.billing_name like '%$srch_name%' OR t.pay_email like '%$srch_name%')";
		}
		if(@$_REQUEST['srch_payment'])
		{
			$srch_payment = @$_REQUEST['srch_payment'];
			$transsql_conditions .= " and t.payment_method like \"$srch_payment\"";
		}
		
		if(@$_REQUEST['srch_payid'])
		{
			$srch_payid = @$_REQUEST['srch_payid'];
			$transsql_conditions .= " and t.paypal_transection_id like '%$srch_payid%'";
		}
		
		if(@$_REQUEST['post_types'])
		{
			$post_type = @$_REQUEST['post_types'];
			$transsql_conditions .= " and p.post_type like '%$post_type%' and p.ID = t.post_id";
		}
		if(@$_REQUEST['trans_from_date'] && @$_REQUEST['trans_to_date'])
		{
			$trans_from_date = @$_REQUEST['trans_from_date'].' 00:00:00';
			$trans_to_date = @$_REQUEST['trans_to_date'].' 23:59:00';
			$transsql_conditions .= " and t.payment_date >= '$trans_from_date' and t.payment_date <= '$trans_to_date' ";
		}
		elseif(@$_REQUEST['trans_from_date'])
		{
			$trans_from_date = @$_REQUEST['trans_from_date'].' 00:00:00';
			$transsql_conditions .= " and t.payment_date >= '$trans_from_date' ";
		}
		elseif(@$_REQUEST['trans_to_date'])
		{
			$trans_to_date = @$_REQUEST['trans_to_date'].' 23:59:00';
			$transsql_conditions .= " and t.payment_date <= '$trans_to_date' ";
		}
		if( @$external_queries != "" ){
			$transsql_conditions .= $external_queries;
		}
		$_SESSION['query_string'] = $transsql_select.$transsql_from.$transsql_conditions;
		
}else{
	unset($_SESSION['query_string']);
	global $post,$wpdb,$transection_db_table_name;	
	$post_table = $wpdb->prefix."posts";
	$select_post_table = '';
	if(@$_REQUEST['post_types'] || @$_REQUEST['type']){
		$select_post_table = " , $post_table as p ";
	}	
	$transsql_select = "select * ";
	$transsql_count = "select count(t.trans_id) ";
	$transsql_from= " from $transection_db_table_name as t $select_post_table";
	$transsql_conditions = " where 1=1 AND package_type is NULL";
	$_SESSION['query_string'] = $transsql_select.$transsql_from.$transsql_conditions;
}
if(isset($_REQUEST['Reset']) && $_REQUEST['Reset']!= '')
{
	unset($_SESSION['query_string']);
	unset($_REQUEST);
	global $post,$wpdb,$transection_db_table_name;
		$post_table = $wpdb->prefix."posts";
		if(@$_REQUEST['post_types'] || @$_REQUEST['type']){
			$select_post_table = " , $post_table as p ";
		}	
		$transsql_select = "select * ";
		$transsql_count = "select count(t.trans_id) ";
		$transsql_from= " from $transection_db_table_name as t $select_post_table";
		$transsql_conditions = " where 1=1 AND package_type is NULL";
		$_SESSION['query_string'] = $transsql_select.$transsql_from.$transsql_conditions;
}
if(isset($_REQUEST['trans_setting']) &&  $_REQUEST['trans_setting'] != '')
{
	$settings = get_option( "templatic_settings" );
	$_POST['trans_post_type_value']=isset($_POST['trans_post_type_value'])?$_POST['trans_post_type_value']:array();
	foreach($_POST as $key=>$val)
	{
		$settings[$key] = isset($_POST[$key])?$_POST[$key]:'';
		update_option('templatic_settings', $settings);
	}
}
include(TEMPL_MONETIZATION_PATH."admin_transaction_class.php");	/* class to fetch transaction class */
//----------------------------------------------------
?>
<script>
function change_transstatus(tid,post_id){
		if (tid=="")
	  {
	  document.getElementById("p_status_"+tid).innerHTML="";
	  return;
	  }
	  if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }else{// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
		xmlhttp.onreadystatechange=function()
	  {
	    if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		document.getElementById("p_status_"+tid).innerHTML=xmlhttp.responseText;
		}
	  }
	  url = "<?php echo plugin_dir_url( __FILE__ ); ?>ajax_update_status.php?post_id="+post_id+"&trans_id="+tid;
	  xmlhttp.open("GET",url,true);
	  xmlhttp.send();
}
</script>
<div class="wrap">
<div class="icon32 icon32-posts-post" id="icon-edit"></div>
<h2><?php echo __('Transaction Report',ADMINDOMAIN);?></h2>
<p class="tevolution_desc"> <?php echo __('Whatever sales are done on your site are recorded and displayed here as the transactions. Few things that you can perform here are easily changing the payment status manually (if you want), search for particular transaction using the below given fields, sort your all transactions according to your payment gateway by clicking the column "Payment Method" and last but not the least, you can also export all your transactions to CSV file. <a href="javascript:void(0);" title="Plugins" id="trans_color_setting">You can change transcation color settings from here.</a>.',ADMINDOMAIN);?></p>
	<div class="tevolution_normal ordersearch">
		<div id="poststuff">
		<div class="postbox">
				<h3 class="hndle"><span><?php echo __('Generate Transaction Report',ADMINDOMAIN)?></span></h3>
	<div class="transaction_page_set">
    <form method="post" action="" name="ordersearch_frm">
        <table class="form-table" cellspacing="1" cellpadding="4" border="0" >
            <?php	do_action('add_fields_before_transaction_fields');		?>
			<tr>
				<th valign="center"><?php echo __('Search by transaction ID',ADMINDOMAIN); ?></th>
				<td valign="center"><input type="text" class="regular-text" value="" name="srch_orderno" id="srch_orderno" />&nbsp;<input type="submit" name="Search" value="<?php echo __('Search',ADMINDOMAIN); ?>" class="button-primary"  />
                    <p class="description"><?php echo __('Enter unique Id to search transaction', ADMINDOMAIN);?></p></td>
            </tr>
            <tr style="border-top: 1px solid #ccc; ">
				<th  valign="center"><?php echo __('Post Type',ADMINDOMAIN); ?></th>
				<td valign="center">	
				<?php
			$custom_post_types_args = array();
			$custom_post_types = get_option("templatic_custom_post");
			$i = 0;
			?>
				<select name="post_types" id="post_types"  >
				<option value="0"><?php echo PLEASE_SELECT; ?></option>
			<?php
            foreach ($custom_post_types as $content_type=>$content_type_label) { ?>
            	<option value="<?php echo $content_type; ?>" <?php if(isset($_REQUEST['post_types']) && $_REQUEST['post_types']== content_type ) {?> selected="selected" <?php } ?>><?php echo $content_type_label['label']; ?></option>                    
            <?php
			}
				$i++;	
        ?></select><br /><p class="description"><?php echo __('Select the post type, transactions of which you want to search', ADMINDOMAIN);?></p></td>
        	</tr>
            <tr>
				<th valign="center"><?php echo __('Payment Type',ADMINDOMAIN); ?></th>
				<td valign="center">
				<?php
					$targetpage = site_url("/wp-admin/admin.php?page=transcation");
					$paymentsql = "select * from $wpdb->options where option_name like 'payment_method_%' order by option_id";
					$paymentinfo = $wpdb->get_results($paymentsql);
					if($paymentinfo)
					{
						foreach($paymentinfo as $paymentinfoObj)
						{
							$paymentInfo = unserialize($paymentinfoObj->option_value);
							$paymethodKeyarray[$paymentInfo['key']] = $paymentInfo['key'];
							ksort($paymethodKeyarray);
						}
					} ?>
					<select name="srch_payment" >
						<option value=""> <?php echo __('Select Payment Type',ADMINDOMAIN); ?> </option>
						<?php 
						if(!empty($paymethodKeyarray))
						{
							foreach($paymethodKeyarray as $key=>$value) {
								if($value) { ?>
								<option value="<?php echo $value;?>" <?php if($value == @$_REQUEST['srch_payment']){?> selected<?php }?>><?php echo __(ucfirst($value),ADMINDOMAIN); ?></option>
						<?php	} 	
							}
						}?>
					</select><p class="description"><?php echo __('Select the payment method (gateway), using which transactions have been done.', ADMINDOMAIN);?></p></td>
            </tr>
			<tr>	
				<th valign="center"><?php echo __('Name/Email',ADMINDOMAIN); ?></th>
				<td valign="center" colspan="4"><input type="text" class="regular-text" value="" name="srch_name" id="srch_name" /><br /><p class="description"><?php echo __('Enter the name or email Id using which transactions have been done', ADMINDOMAIN);?></p></td>
			</tr>
			<tr>
				<?php
					add_action('admin_footer','show_trans_date_picker');
					function show_trans_date_picker()
					{
				?>
					 <script type="text/javascript">	
						jQuery(function(){
						var pickerOpts = {
								showOn: "both",
								dateFormat: 'yy-mm-dd',
								buttonText: '<i class="fa fa-calendar"></i>',
								monthNames: objectL11tmpl.monthNames,
								monthNamesShort: objectL11tmpl.monthNamesShort,
								dayNames: objectL11tmpl.dayNames,
								dayNamesShort: objectL11tmpl.dayNamesShort,
								dayNamesMin: objectL11tmpl.dayNamesMin,
								isRTL: objectL11tmpl.isRTL,
							};	
							jQuery("#trans_from_date").datepicker(pickerOpts);
							jQuery("#trans_to_date").datepicker(pickerOpts);
						});
					</script>
				<?php } 
				?>
				<th valign="center"><?php echo __('Search by transaction date',ADMINDOMAIN); ?></th>
				<td valign="center" ><input type="text" PLACEHOLDER="<?php echo __('From',ADMINDOMAIN); ?>" class="regular-text" value="<?php if(isset($_REQUEST['trans_from_date']) && $_REQUEST['trans_from_date']!= ''){ echo $_REQUEST['trans_from_date'];}?>" name="trans_from_date" id="trans_from_date" />
				<input type="text" PLACEHOLDER="<?php echo __('To',ADMINDOMAIN); ?>" class="regular-text" value="<?php if(isset($_REQUEST['trans_to_date']) && $_REQUEST['trans_to_date']!= ''){ echo $_REQUEST['trans_to_date'];}?>" name="trans_to_date" id="trans_to_date" /><p class="description"><?php echo __('Select the time duration in which transactions have been done.', ADMINDOMAIN);?></p></td>
			</tr>
			<?php	do_action('add_fields_after_transaction_fields');		?>
			<tr>
				<th></th>
				<td valign="center"><input type="submit" name="Search" value="<?php echo __('Search',ADMINDOMAIN); ?>" class="button-primary"  />&nbsp;<input type="submit" name="Reset" value="<?php echo __('Reset',ADMINDOMAIN); ?>"  class="button-secondary action" /></td>
        	</tr>
			
            <tr style="border-top: 1px solid #ccc; "><br/>
            	<td colspan="2"><p><?php echo __('Export the transaction data from here ',ADMINDOMAIN); ?>&nbsp;&nbsp;<a class="button-primary" href="<?php echo plugin_dir_url( __FILE__ ).'export_transaction.php';?>" title="Export To CSV" class="i_export"><?php echo __('Export To CSV',ADMINDOMAIN);?></a></p></td>
            </tr>
    </table>
	</form>
	</div>
	</div>
	</div>
	</div>
	<?php add_action('admin_footer','trans_color_setting_script');
	function trans_color_setting_script()
	{
		?>
	<script>
		jQuery( "#trans_color_setting" ).click(function() {
			jQuery( '#trans_frm_id' ).toggle();
		});
	</script>
	<?php 
	} ?>
	<div style="display:none;" id="trans_frm_id" class="tevolution_normal ordersearch" class="tevolution_normal ordersearch">
		<div id="poststuff">
			<div class="postbox">
				<h3 class="hndle"><span><?php echo __('Transaction color settings',ADMINDOMAIN); ?></span></h3>
	<div class="transaction_page_set">
	<form  method="post" action=""  name="transaction_frm">
		<?php
			$tmpdata = get_option('templatic_settings');
			add_action('admin_print_scripts-widgets.php', 'templatic_load_color_picker_script');
			add_action('admin_print_styles-widgets.php', 'templatic_load_color_picker_style');
		?>
		<p class="tevolution_desc"><?php echo __('You can select the different colors for the different post types here which will be applied to that transaction report.It basically helps you in differentiating the transactions done with various post types.',ADMINDOMAIN); ?></p>
		<table class="form-table tbl_transaction_frm">
			<tr>
				<th valign="center"><label><?php echo __('Transaction Settings',ADMINDOMAIN);?></label></th>
				<td valign="center">
				   <div class="element">
					 <?php $value = array(); if(isset($tmpdata['trans_post_type_value'])) { $value = $tmpdata['trans_post_type_value']; } ?>
					
					
					<?php $posttaxonomy = get_option("templatic_custom_post");
						foreach ($posttaxonomy as $type=>$types) :
						
						$color_taxonomy = 'trans_post_type_colour_'.$type;
						$color_value = $tmpdata[$color_taxonomy];
						?>
						<script type="text/javascript">
							jQuery(document).ready(function($){
								jQuery('#trans_post_type_colour_<?php echo $type; ?>').farbtastic('#color_<?php echo $type; ?>');
							});
							function showColorPicker(id)
							{
								document.getElementsByName(id)[0].style.display = '';				
							}
						</script>
					<?php
						if(isset($color_value) && $color_value!= '') { $color_taxonomy_value = $color_value; } else { $color_taxonomy_value = '#'; }?>						
			<label for="trans_post_type_value_<?php echo $type;?>" style="min-width: 100px; display:inline-block;"> <input <?php if(isset($value) && in_array($type,$value)) { echo "checked=checked";  } ?> type="checkbox" value="<?php echo $type; ?>" id="trans_post_type_value_<?php echo $type;?>" name="trans_post_type_value[]"><?php echo " ".$types['label']; ?> </label>
  
		<input type="text" name="trans_post_type_colour_<?php echo $type; ?>" onclick="showColorPicker(this.id);" id="color_<?php echo $type; ?>" value="<?php if(isset($color_taxonomy_value) && $color_taxonomy_value != '') { echo $color_taxonomy_value; }?>" ><img style="position:relative;vertical-align:middle;" src="<?php echo  plugin_dir_url( __FILE__ ); ?>images/Color_block.png" /></label>
		<div id="trans_post_type_colour_<?php echo $type; ?>"  name="color_<?php echo $type; ?>" style="display:none" ></div>
		<div class="clearfix"></div>
						<?php endforeach; ?>
				  </div>
				</td>
			 </tr>
			 <tr>
				<td></td>
				<td><p style="clear: both;" class="submit"><input type="submit" value="<?php echo __('Save All Settings',ADMINDOMAIN);?>" class="button-primary" name="trans_setting"></td>
			 </tr>
		</table>	
	</form>
	</div>
	</div>
	</div>
	</div>
	<form method="post" action="" name="order_frm">
<?php
do_action('tevolution_before_transaction_table');
$templ_list_table = new wp_list_transaction();
$templ_list_table->prepare_items();
$templ_list_table->display();
?>
</form>
<?php
echo '</div>'; ?>
<script>
function reportshowdetail(custom_id)
{
	if(document.getElementById('reprtdetail_'+custom_id).style.display=='none')
	{
		document.getElementById('reprtdetail_'+custom_id).style.display='';
	}else
	{
		document.getElementById('reprtdetail_'+custom_id).style.display='none';	
	}
}
</script>
