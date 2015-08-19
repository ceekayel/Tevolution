<?php
/*
 * transaction detail page for backend
 */
$orderId = $_REQUEST['trans_id'];
if(isset($_REQUEST['submit']) && $_REQUEST['submit'] !='')
{
	do_action('tevolution_transaction_mail');
}
global $wpdb,$transection_db_table_name;
$transection_db_table_name = $wpdb->prefix."transactions";
$ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
$orderinfoObj = $wpdb->get_row($ordersql);
			
?>
<div class="wrap">
<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php echo TRANSACTION_REPORT_TEXT; ?> <a title="Back to transaction list" class="add-new-h2" name="btnviewlisting" href="<?php echo site_url() ?>/wp-admin/admin.php?page=transcation"><?php echo BACK_TO_TRANSACTION_LINK; ?></a>
	</h2>
	<p class="description"><?php echo __('Here you can view the transaction detail.',ADMINDOMAIN); ?></p>
	<?php if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success'){ ?>
          <div class="update-nag" style="text-align:left;">
         		<?php echo ORDER_STATUS_SAVE_MSG;?>
          </div>
     <?php }?>
	<div class="tevolution_normal">
		<div id="poststuff">
			<div class="postbox">
				<h3><span><?php _e('Transaction Report',DOMAIN); ?></span></h3>
				<div class="transaction_detail_page">
					<div  class="order_frm">
						<h3><?php _e('Transaction Detail',DOMAIN); ?></h3>
						<div class="inside">
							<p class="stat"><?php echo get_order_detailinfo_transaction_report($orderId); ?></p>
						</div>
					</div>
				</div>
				<?php
				/*if($orderinfoObj->post_id !=0){*/
				$package_select_id = get_post_meta($orderinfoObj->post_id,'package_select',true);
				if($package_select_id ==''){
					$package_select_id = $orderinfoObj->package_id;
				}
				if($package_select_id)
				{
					?>
					<div  class="transaction_detail_frm">
						<h3><?php _e('Package Detail',DOMAIN); ?></h3>
						<div class="inside">
							<p class="stat"><?php echo get_order_detailinfo_price_package($orderId); ?></p>
						</div>
					</div>
					<?php
				} 
				?>
			</div>
		</div>
		<?php if($orderinfoObj->payforpackage == 0 || $orderinfoObj->payforpackage == 1): ?>
			<div id="poststuff">
				<div class="postbox">
					<h3><span><?php 
						$post_type = get_post($orderinfoObj->post_id);
						echo __('Transaction Information',ADMINDOMAIN); 
					?></span></h3>
					
					<div class="inside">
						<p class="stat"><?php echo get_order_detailinfo_tableformat($orderId,1); ?></p>
					</div>
				</div>
			</div>
		<?php endif; ?>	
		</div>
		<div class="tevolution_side">
			<div id="poststuff">
				<div class="postbox">
					<h3><span><?php _e('Transaction Status',DOMAIN); ?></span></h3>
					<div class="inside">
						<p class="stat">
							<form action="<?php echo site_url("/wp-admin/admin.php?page=transcation&amp;action=edit&amp;msg=success&amp;trans_id=".$_GET['trans_id']);?>" method="post">
								<div class="orderstatus_class">
									<input type="hidden" name="act" value="orderstatus">
									<select name="ostatus">
										<option value="0" <?php if($orderinfoObj->status==0){?> selected="selected"<?php }?>><?php echo PENDING_MONI;?></option>
									   <option value="1" <?php if($orderinfoObj->status==1){?> selected="selected"<?php }?>><?php echo APPROVED_TEXT;?></option>
									   <option value="2" <?php if($orderinfoObj->status==2){?> selected="selected"<?php }?>><?php echo ORDER_CANCEL_TEXT;?></option>
                                       <option value="3" <?php if($orderinfoObj->status==3){?> selected="selected"<?php }?>><?php echo __('Delete',ADMINDOMAIN);?></option>
									</select>
								</div>
								<div class="submit_orderstatus_class"><input type="submit" name="submit" value="<?php echo ORDER_UPDATE_TITLE; ?>" class="button-primary" ></div>
								<input type="hidden" name="update_transaction_status" id="update_transaction_status" value="<?php echo $orderinfoObj->post_id; ?>" />
							</form>
						</p>
					</div>
				</div>
			</div>
			<div id="poststuff">
				<div class="postbox">
					<h3><span><?php _e('User Information',DOMAIN); ?></span></h3>
					<div class="inside">
							<?php echo get_order_user_info($orderId); ?>
					</div>
				</div>
			</div>
		</div>
</div>