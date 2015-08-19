<?php
/*
 * class to fetch user custom fields
 */
if( isset($_REQUEST['action_del']) && $_REQUEST['action_del'] == 'delete' )
{
	$cids = $_REQUEST['cf'];
	foreach( $cids as $cid )
	{
		wp_delete_post($cid);
	}
	$url = site_url().'/wp-admin/admin.php';
	echo '<form action="'.$url.'#user_custom_fields" method="get" id="frm_user_meta" name="frm_user_meta">
			<input type="hidden" value="custom_setup" name="page"><input type="hidden" value="user_custom_fields" name="ctab"><input type="hidden" value="delsuccess" name="usermetamsg">
		</form>
		<script>document.frm_user_meta.submit();</script>
		';
	exit;	
}
include(TT_REGISTRATION_FOLDER_PATH."admin_user_custom_fields_class.php");	/* class to fetch payment gateways */
?>
<div class="wrap">
     <div id="icon-edit" class="icon32 icon32-posts-post"><br/></div>
    	<h2>
		<?php echo __('Manage user profile fields','templatic');?>
     	<a id="add_user_custom_fields"href="<?php echo site_url().'/wp-admin/admin.php?page=custom_setup&ctab=user_custom_fields&action=addnew';?>" title="<?php echo __('Add a field for users&rsquo; profile','templatic');?>" name="btnviewlisting" class="add-new-h2" /><?php echo __('Add a new field','templatic'); ?></a>
     </h2>
     
      <p class="tevolution_desc"><?php echo __('The fields you add/edit here will be displayed in user&rsquo;s dashboard and profile area. Using these fields, you can make users fill in custom information about themselves from the registration page you create.','templatic');?></p>
     
     <?php
     if(isset($_REQUEST['usermetamsg']) && $_REQUEST['usermetamsg']=='delsuccess')
     {
          $message = __('Information Deleted successfully.','templatic');	
     } 
	if(isset($_REQUEST['usermetamsg']) && $_REQUEST['usermetamsg']=='usersuccess'){
          if($_REQUEST['msgtype']=='add-suc') {
			$message = __('Custom user info field created successfully.','templatic');
		} else {
			$message = __('Custom user info field updated successfully.','templatic');
		}
     }
     
	if(isset($message) && $message!=''){
		echo '<div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px;" >';
		echo $message;
		echo '</div>';
     }	
	?>
	
     <form name="register_custom_fields" id="register_custom_fields" action="" method="post" >
		<?php
          $templ_list_table = new wp_list_custom_user_field();
          $templ_list_table->prepare_items();
          $templ_list_table->search_box('search', 'search_id');
          $templ_list_table->display();
          ?>
          <input type="hidden" name="check_compare">
     </form>
</div>