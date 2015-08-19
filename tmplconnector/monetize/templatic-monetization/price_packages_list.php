<?php 
/*include package class file to fetch listing of of price package*/

include_once(TEMPL_MONETIZE_FOLDER_PATH.'templatic-monetization/price_package_class.php');

?>
<form method="post" action="" id="posts-filter">
     <div class="wrap">
     
     <div class="tevo_sub_title"><?php echo __('Manage Price Packages','templatic-admin'); ?>
     	<a id="add_price_package" class="add-new-h2" href="<?php echo admin_url("admin.php?page=monetization&action=add_package&tab=packages"); ?>"><?php echo __('Add New Package','templatic-admin'); ?></a>
     </div>
     <p class="tevolution_desc"><?php echo __('Price Packages allow you to monetize your submission form and make money. For more on how they work visit the <a href="http://templatic.com/docs/tevolution-guide/#price_packages" title="Price Packages" target="_blank">Price Package Guide</a>','templatic-admin');?>.</p>
     <?php if(isset($_REQUEST['package_msg']))
     { ?>
          <div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px;" >
          <?php if($_REQUEST['package_msg'] == 'delete')
          {
         		echo __('Package permanently deleted.','templatic-admin');	
          } elseif($_REQUEST['package_msg']=='success')
          {
			if($_REQUEST['package_msg_type']=='add')
			{
				echo __('Package created successfully.','templatic-admin');
			} else
			{
				echo __('Package updated successfully.','templatic-admin');
			}
          } ?>
          </div>
     <?php }
     wp_enqueue_script( 'jquery-ui-sortable' );
		echo '<div class="tevolution_price_package">';
		$templ_list_table = new templatic_List_Table();
		$templ_list_table->prepare_items();
		$templ_list_table->search_box('search', 'search_id');
		$templ_list_table->display();
		echo '</div>';    
     if(isset($_REQUEST['page']) && isset($_REQUEST['tag'])): ?>
          <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
          <input type="hidden" name="tag" value="<?php echo $_REQUEST['tag']; ?>" />
          
     <?php endif; ?>
     </div>
</form>