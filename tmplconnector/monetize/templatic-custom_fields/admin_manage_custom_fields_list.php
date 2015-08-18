<?php
/* Custom Fields Listing page */
if(@$_REQUEST['pagetype']=='delete')
{
	$postid = $_REQUEST['field_id'];
	wp_delete_post($postid);
	$url = site_url().'/wp-admin/admin.php';
	echo '<form action="'.$url.'" method="get" id="frm_custom_field" name="frm_custom_field">
	<input type="hidden" value="custom_fields" name="page"><input type="hidden" value="delsuccess" name="custom_field_msg">
	</form>
	<script>document.frm_custom_field.submit();</script>
	';exit;	
}
?>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h2><?php echo __('Manage custom fields',ADMINDOMAIN);?> 
	<a id="add_custom_fields" class="add-new-h2" href="<?php echo site_url().'/wp-admin/admin.php?page=custom_fields&action=addnew';?>" title="<?php echo __('Add custom field',ADMINDOMAIN);?>" name="btnviewlisting"/><?php echo __('Add a custom field',ADMINDOMAIN); ?>
	</a></h2>
    
    <p class="tevolution_desc"><?php echo sprintf(__('Within this section you can define new fields for your website (mostly used for the submission forms). Created fields can be both category and post type specific. For more details on them %s',ADMINDOMAIN),'<a href ="http://templatic.com/docs/tevolution-guide/#customfields" target="blank">click here</a>');?></p>
	<p class="tevolution_desc"><?php echo __('<b>Restrictions</b>',ADMINDOMAIN);?></p>
	<ul class="tevolution_list">
		<li><?php echo __('Do not delete default fields that are automatically assigned to each new post type. Default fields include: Post category, Post title, Post content, Post excerpt and Post images.',ADMINDOMAIN);?></li>
		<li><?php echo __('Display location for some default fields cannot be changed.',ADMINDOMAIN);?></li>
	</ul>
	<p class="tevolution_desc"><b><?php echo __('Quick Tips',ADMINDOMAIN);?></b></p>
	<ul class="tevolution_list">
		<li><?php echo __('You can change the "Display location" option for each field by just dragging them up and down on this page.',ADMINDOMAIN);?></li>
		<li><?php echo __('To show more custom fields per page click on the "Screen Options" button in the top right corner.',ADMINDOMAIN);?></li>
		<li><?php echo __('If you ever decide to start over and delete your created custom fields just click on the "Reset All Custom Fields" button below.',ADMINDOMAIN);?></li>
	</ul>
    <form action="" method="post">
    	<input type="hidden" name="custom_reset"  value="1" />
		 <input type="submit" name="reset_custom_fields" value="<?php echo __('Reset All Custom Fields',ADMINDOMAIN);?>" class="button-primary reset_custom_fields" />
    </form>
	<ul class="subsubsub">
    	<li><a class="<?php if(!isset($_REQUEST['search_subtab'])){echo "current";}?>" href="<?php echo site_url().'/wp-admin/admin.php?page=custom_fields';?>" ><?php echo __('Manage Custom Fields',ADMINDOMAIN);?></a> | </li>
        <li><a class="<?php if(isset($_REQUEST['search_subtab'])){echo "current";}?>" href="<?php echo site_url().'/wp-admin/admin.php?page=custom_fields&search_subtab=search_custom_fileds';?>"><?php echo __('Manage Search Custom Fields',ADMINDOMAIN);?></a></li>
    </ul>
    
	<?php if(isset($_REQUEST['custom_field_msg'])){?>
		<div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px;" >
			<?php if($_REQUEST['custom_field_msg']=='delsuccess'){
					echo __('Custom field deleted successfully.',ADMINDOMAIN);	
				} if($_REQUEST['custom_field_msg']=='success'){
					if($_REQUEST['custom_msg_type']=='add') {
						echo __('Custom field created successfully.',ADMINDOMAIN);
					} else {
						echo __('Custom field updated successfully.',ADMINDOMAIN);
					}
				}
			?>
		</div>
	<?php }?>
    <?php if(isset($_REQUEST['search_custom_field_msg'])){?>
    	<div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px;" >
			<?php if($_REQUEST['search_custom_field_msg']=='removesuccess'){
					echo __('Successfully removed Custom field from search form.',ADMINDOMAIN);	
				}?>
		</div>
    <?php }
    
	wp_enqueue_script( 'jquery-ui-sortable' );
	
	if(!isset($_REQUEST['search_subtab'])):?>
    <form name="post_custom_fields" id="post_custom_fields" action="" method="post">
		<?php
			$custom_fields_list_table = new custom_fields_list_table();
			$custom_fields_list_table->prepare_items();
			$custom_fields_list_table->search_box('search', 'search_field');
			$custom_fields_list_table->display();
		?>
	</form>    
    <?php else:?>
    <!-- manage Search Custom fields -->
     <form name="post_search_custom_fields" id="post_search_custom_fields" action="" method="post">
		<?php
			$custom_fields_list_table = new search_custom_fields_list_table();
			$custom_fields_list_table->prepare_items();
			$custom_fields_list_table->search_box('search', 'search_field');
			$custom_fields_list_table->display();
		?>
	</form>    
    
    <?php endif;?>
</div>