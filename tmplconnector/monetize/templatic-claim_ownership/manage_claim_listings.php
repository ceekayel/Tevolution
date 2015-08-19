<?php
/*
 * submited claim listing.
 */
?>
<div class="wrap">
	<?php if(isset($_REQUEST['custom_msg_type']) && $_REQUEST['custom_msg_type'] == 'delete'):?>
	<div class="message updated"><p><?php echo __('Claim deleted successfully','templatic-admin'); ?></p></div>
	<?php endif; ?>    
	<div id="icon-edit" class="icon32 icon32-posts-post"><br/></div>
	<h2>
	<?php echo __("Manage Claims",'templatic-admin'); ?>
	</h2
     ><?php do_action('tevolution_claim_listing_msg');?>
     
</div><br />
<?php
/* Display custom field save / update / delete related message */
	if(isset($_REQUEST['claim_msg'])){?>
		<div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px;" >
			<?php if($_REQUEST['claim_msg']=='delsuccess'){
					echo __('Claim deleted successfully.','templatic-admin');	
				}
			?>
		</div>
	<?php }
?>
<form name="all_custom_post_types" id="posts-filter" action="<?php echo admin_url("admin.php?page=ownership_listings"); ?>" method="post" >
	<?php
	$templ_claimlist_table = new templ_claimlist_table();
	$templ_claimlist_table->prepare_items();
	$templ_claimlist_table->display();
	?>
</form>