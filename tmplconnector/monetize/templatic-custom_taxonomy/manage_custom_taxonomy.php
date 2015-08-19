<!-- Custom Taxonomies Lists -->
<div class="wrap">
	<?php if(isset($_REQUEST['custom_msg_type']) && $_REQUEST['custom_msg_type'] == 'add'):?>
	<div class="message updated"><p><?php echo sprintf(__('Custom post type saved successfully , Sidebar area for this taxonomy (Listing page , Detail page + Add listing page) has been created in <strong><a href="%s">Widgets</a></strong> area.','templatic'),site_url('/wp-admin/widgets.php')); ?></p></div>
	<?php endif; ?>
	<?php if(isset($_REQUEST['custom_msg_type']) && $_REQUEST['custom_msg_type'] == 'delete'):?>
	<div class="message updated"><p><?php echo __('Custom post type deleted successfully','templatic-admin'); ?></p></div>
	<?php endif; ?>    
	<div id="icon-edit" class="icon32 icon32-posts-post"><br/></div>
	<h2>
	<?php echo __("Custom Post Types",'templatic-admin'); ?>
	<a class="add-new-h2" id="add_custom_taxonomy" href="<?php echo admin_url("admin.php?page=custom_setup&ctab=custom_setup&action=add_taxonomy"); ?>"><?php echo __('Add Custom Post Type','templatic-admin'); ?></a>
	</h2>
	<p class="tevolution_desc">
	
	<?php echo __('Start adding new custom post types by simply clicking above "Add Custom Post Type" link (e.g. jobs, cars etc). To learn more about using custom post types in general visit the <a href="http://codex.wordpress.org/Post_Types">WordPress codex</a>. To know how to properly utilize this functionality inside Tevolution, please refer to the <a href="http://templatic.com/docs/tevolution-guide/#intro_cpt">documentation guide</a>.<br><br> <strong>Tip:</strong> After you create a custom post type, you should also follow the set of steps to make it all work together. For example your new post type needs its custom fields and price packages if you want it to have its own ones. The post type will need its own submission page and categories.','templatic-admin'); ?>
	
	</p>
     
     <?php do_action('tevolution_custom_taxonomy_msg');?>
     
</div><br />
<form name="all_custom_post_types" id="posts-filter" action="<?php echo admin_url("admin.php?page=custom_setup"); ?>" method="post" >
	<?php
	$templ_list_table = new taxonmy_list_table();
	$templ_list_table->prepare_items();
	$templ_list_table->display();
	?>
</form>