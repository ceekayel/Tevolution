<?php
global $wpdb;
/* Custom Fields Listing page */
if(@$_REQUEST['pagetype']=='delete')
{
	$postid = $_REQUEST['field_id'];
	wp_delete_post($postid);
	$url = site_url().'/wp-admin/admin.php';
	echo '<form action="'.$url.'" method="get" id="frm_custom_field" name="frm_custom_field">
	<input type="hidden" value="custom_setup" name="page"><input type="hidden" value="custom_fields" name="ctab"><input type="hidden" value="delsuccess" name="custom_field_msg">
	</form>
	<script>document.frm_custom_field.submit();</script>
	';exit;	
}

/* get the tevolution base post type with wordpress post*/
$post_types = apply_filters('tmpl_custom_fields_filter',array_merge(tevolution_get_post_type(),array('post')));

?>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h2><?php echo __('Manage custom fields','templatic-admin');?> 
	<a id="add_custom_fields" class="add-new-h2" href="<?php echo site_url().'/wp-admin/admin.php?page=custom_setup&ctab=custom_fields&action=addnew';?>" title="<?php echo __('Add custom field','templatic-admin');?>" name="btnviewlisting"/><?php echo __('Add a custom field','templatic-admin'); ?>
	</a></h2>
    
    <p class="tevolution_desc"><?php echo sprintf(__('Within this section you can create new fields for your submission form. For more details about custom fields please visit the %s','templatic-admin'),'<a href ="http://templatic.com/docs/tevolution-guide/#customfields" target="blank">Tevolution guide.</a>');?></p>
	<p class="tevolution_desc"><?php echo __('<b>Restrictions</b>','templatic-admin');?></p>
	<ul class="tevolution_list">
		<li><?php echo __('Do not delete default fields that are automatically assigned to each new post type. Default fields include: Post category, Post title, Post content, Post excerpt and Post images.','templatic-admin');?></li>
		<li><?php echo __('Display location for some default fields cannot be changed.','templatic-admin');?></li>
	</ul>
	<p class="tevolution_desc"><b><?php echo __('Quick Tips','templatic-admin');?></b></p>
	<ul class="tevolution_list">
		<li><?php echo __('Change the "Sort Order" of each field by dragging it up or down on this page. This changed order will only apply on the submission form.','templatic-admin');?></li>
		<li><?php echo __('Show more/less custom fields per page at "Screen Options", located on top right corner.','templatic-admin');?></li>
		<li><?php echo __('To delete custom fields that you have created and start over just click on the "Reset Custom Fields" button located in the bottom right corner of the page.','templatic-admin');?></li>
	</ul>
    <?php 
	/* 
	 * Display custom fields post type wise link
	 */
	 
	
	if(!empty($post_types) && !isset($_REQUEST['search_subtab'])):?>
    <div class="wp-filter clearfix post-type-links">    	
		<ul class="filter-links">
        <li><strong><?php echo __('For','templatic-admin');?>: </strong>  </li>
		<!-- Show all tabs only if there us more then one post types -->
		<?php
			do_action('tmpl_custom_fields_post_type');
			
			$i=1;
			
			foreach($post_types as $type):
				/*Get the first post type */
				if($i==1){
					if(!isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields'] ==''){
						$_REQUEST['post_type_fields']=(isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']!="")?$_REQUEST['post_type_fields'] :$type; 
					}
				}
			
			/* get the submit form page using post type wise */
			$args=array('s'=>'submit_form','post_type'=>'page','posts_per_page'=>-1,
						'meta_query'     => array('relation' => 'AND',
								   array('key' => 'submit_post_type','value' => $type,'compare' => '='),
								   array('key' => 'is_tevolution_submit_form','value' => '1','compare' => '=')
								),
						);
			$post_query = new WP_Query($args);
			$submit_link='';
			if($post_query->have_posts()){
				while ($post_query->have_posts()) { $post_query->the_post();
					$submit_link='<a href="'.get_permalink().'" target="_blank" class="view_frm_link"><small>'.__(' View Form','templatic-admin').'</small></a>';
				}
			}
			
			if(!isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields'] ==''){
				$type = apply_filters('tmpl_default_posttype','listing');
			}
			
			
			/*Finish submit from page link using post type wise query */
		?>
			<li><a class="<?php if(isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']==$type){echo "current";}?>" href="<?php echo site_url().'/wp-admin/admin.php?page=custom_setup&ctab=custom_fields&post_type_fields='.$type;?>" ><?php echo ucfirst($type);?></a><?php if($submit_link!=''){echo '('.$submit_link.')';} if($i!=count($post_types)) {  } $i++;?> </li>
        <?php 
			do_action('tmpl_after_'.$type.'_post_type');
			endforeach;
			do_action('tmpl_custom_fields_post_type_end');
		?>
        </ul>
    </div>
    <?php endif;
	
	/* Display custom field save / update / delete related message */
	if(isset($_REQUEST['custom_field_msg'])){?>
		<div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px;" >
			<?php if($_REQUEST['custom_field_msg']=='delsuccess'){
					echo __('Custom field deleted successfully.','templatic-admin');	
				} if($_REQUEST['custom_field_msg']=='success'){
					if($_REQUEST['custom_msg_type']=='add') {
						echo __('Custom field created successfully.','templatic-admin');
					} else {
						echo __('Custom field updated successfully.','templatic-admin');
					}
				}
			?>
		</div>
	<?php }
	
	
	if(isset($_REQUEST['search_custom_field_msg'])){?>
    	<div class="updated fade below-h2 clearfix" id="message" style="padding:5px; font-size:12px; float:left; width:100%;" >
			<?php if($_REQUEST['search_custom_field_msg']=='removesuccess'){
					echo __('Successfully removed Custom field from search form.','templatic-admin');	
				}?>
		</div>
    <?php }
    
	wp_enqueue_script( 'jquery-ui-sortable' );
	$form_id=(isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']!="") ? 'post_custom_fields': 'all_post_custom_fields';
	?>
    <form name="post_custom_fields" id="<?php echo $form_id;?>" action="" method="post">
		<?php
			if(isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']!=''){
				echo '<input type="hidden" id="custom_filed_post_type" name="custom_filed_post_type" value="'.$_REQUEST['post_type_fields'].'" />';
			}
																 
			$custom_fields_list_table = new custom_fields_list_table();
			$custom_fields_list_table->prepare_items();
			$custom_fields_list_table->search_box('search', 'search_field');
			$custom_fields_list_table->display();
		?>
	</form>    
  
		
	<!-- Reset All custom fields button -->
    <form action="" method="post" class="reset_custom_fields_frm">
    	
		 <?php 
		 /* reset custom fields as per different post types */
		 if(isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields'] !=''){ ?>
			<input type="hidden" name="post_type_fields"  value="<?php echo $_REQUEST['post_type_fields']; ?>" />
			<input type="hidden" name="posttype_fld_reset"  value="1" />
		 <?php $post_type = ucfirst($_REQUEST['post_type_fields']); 
		 }else{ 
			$post_type = __('All','templatic-admin'); ?>
			<input type="hidden" name="custom_reset"  value="1" />
		 <?php } 
		 
		 $reset_text = (isset($_REQUEST['search_subtab']) && $_REQUEST['search_subtab'] == 'search_custom_fileds') ? __('Reset Search Custom Fields','templatic-admin') : sprintf(__('Reset %s Custom Fields','templatic-admin'),$post_type);
		 
		 ?>
		 <input type="submit" name="reset_custom_fields" value="<?php echo $reset_text;?>" class="button action reset_custom_fields" />
    </form>
</div>