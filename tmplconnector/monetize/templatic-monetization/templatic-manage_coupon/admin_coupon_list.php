<?php
/* Display the list of the coupons */
global $wpdb,$custom_usermeta_db_table_name;
	include(TEMPL_MONETIZATION_PATH."templatic-manage_coupon/admin_coupon_code_class.php");	/* class to fetch coupon code class */
if( isset($_REQUEST['action_del']) && $_REQUEST['action_del'] == 'delete' )
{
	$cids = $_REQUEST['cf'];
	foreach( $cids as $cid )
	{
		wp_delete_post($cid);
	}
	$url = site_url().'/wp-admin/admin.php';
	echo '<form action="'.$url.'#manage_coupon" method="get" id="frm_user_emta" name="frm_user_emta">
	<input type="hidden" value="monetization" name="page"><input type="hidden" value="manage_coupon" name="tab"><input type="hidden" value="delsuccess" name="usermetamsg">
	</form>
	<script>document.frm_user_emta.submit();</script>
	';exit;
}
?>
<div class="wrap">
<div class="tevo_sub_title"><?php echo __('Manage Coupons',ADMINDOMAIN);?>  
<a id="coupon_list" href="<?php echo site_url().'/wp-admin/admin.php?page=monetization&tab=manage_coupon&action=addnew';?>" title="<?php echo __('Add a field for Coupon',ADMINDOMAIN);?>" name="btnviewlisting" class="add-new-h2" /><?php echo __('Add a new coupon',ADMINDOMAIN); ?></a>
</div>
<p class="tevolution_desc"><?php echo __('This section lets you display and add coupons on your website as a part of the publicity or offers with the option of defining its validity period.',ADMINDOMAIN);?></p>
<?php
if(@$_REQUEST['usermetamsg']=='delsuccess')
{
	$message = __('Coupon deleted successfully.',ADMINDOMAIN);	
} 
	if(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype']=='add-suc') {
			$message = __('Coupon created successfully.',ADMINDOMAIN);
		} elseif(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype'] =='edit-suc') {
			$message = __('coupon updated successfully.',ADMINDOMAIN);
		}
?>
<?php if(@$message){?>
<div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px;" >
  <?php echo $message;?>
</div>
<?php }?>
<form name="frm_coupon_code" id="frm_coupon_code" action="" method="post" >
	<?php
	$templ_list_table = new wp_list_coupon_code();
	$templ_list_table->prepare_items();
	$templ_list_table->search_box('search', 'search_id');
	$templ_list_table->display();
	echo '</div>'; ?>
	<input type="hidden" name="check_compare">
</form>
<script type="text/javascript">
function seleciona() 
{
	if (frm_coupon_code.master.checked==true)
	{
		for (i=0; i<document.frm_coupon_code.elements.length;i++)
		{
			document.frm_coupon_code.elements[i].checked=true
		}	
	}
	if (frm_coupon_code.master.checked==false)
	{
		for (i=0; i<document.frm_coupon_code.elements.length;i++)
		{
		document.frm_coupon_code.elements[i].checked=false
		}
	}
}
function comparision()
{
	d=document.frm_coupon_code;
	var total="";
	var len = d.checkbox.length;
	if(!len) len = 1;
	var chk = 'unchecked';
	if(len == 1)
	{
		if(d.checkbox.checked)
		{
			chk = 'checked';
			d.check_compare.value=d.check_compare.value+d.checkbox.value+',';
		}
	}
	else
	{
		for(var i=0; i < len; i++)
		{
			if(d.checkbox[i].checked) 
			{
				chk = 'checked';
			}
		}
	}
	if(chk == 'unchecked')
	{
		alert("Please select check Box");
		return false;
	}
	if(confirm("are you sure you want to delete this record"))
	{
		for(var i=0; i < d.checkbox.length; i++)
		{
			if(d.checkbox[i].checked) 
			{
				total +=d.checkbox[i].value + "\n";
				d.check_compare.value=d.check_compare.value+d.checkbox[i].value+',';
			}
		}
	}
	else
	{
		return false;
	}
}
function user_showdetail(custom_id)
{
	if(document.getElementById('userdetail_'+custom_id).style.display=='none')
	{
		document.getElementById('userdetail_'+custom_id).style.display='';
	}else
	{
		document.getElementById('userdetail_'+custom_id).style.display='none';	
	}
}
function confirmSubmit()
{
var agree=confirm("Are you sure you want to delete?");
if (agree)
	return true ;
else
	return false ;
}
</script>
</div>