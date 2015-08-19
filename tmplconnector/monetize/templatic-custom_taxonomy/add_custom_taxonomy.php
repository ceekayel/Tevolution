<?php
/* The custom Taxanomy will be created fom here */
?>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h2><?php echo __('Add New Post type',ADMINDOMAIN); ?><a id="back_cutom_taxonomy" class="add-new-h2" href="<?php echo site_url().'/wp-admin/admin.php?page=custom_setup&ctab=custom_setup';?>" title="<?php echo __('Back to Post type list',ADMINDOMAIN);?>" name="btnviewlisting"/><?php echo __('Back to Post type list',ADMINDOMAIN); ?>
	</a></h2>
<?php
$edit_post = array();
if($_REQUEST['action'] == 'edit-type')
{
	$edit_post = get_option("templatic_custom_post");
	$_SESSION['taxonomy_post_type'] = $_REQUEST['post-type'];
}
else
{
	unset($_SESSION['taxonomy_post_type']);
}
?>
<script type="text/javascript" async src="<?php echo TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-custom_taxonomy/add_taxonomy_validations.js';?>"></script>
<form action="<?php echo site_url();?>/wp-admin/admin.php?page=custom_setup&ctab=custom_setup&action=add_taxonomy" method="post" name="custom_taxonomy" onsubmit="return check_taxonomy_form();" >
     <table class="form-table" id="form_table_taxonomy">
          <thead>
               <tr colspan="3">                   
                         <p class="tevolution_desc">
                         <?php echo __('Fill up the necessary information to create a new post type and matching taxonomies. Starred <span class="required">*</span> fields are compulsory, so you cannot leave them blank. These custom post will appear as one of the option in wp-admin (dashboard) menu panel. <br><strong> Note: </strong>Be careful while adding slugs, It will appear in the URL and you will not be able to edit them once you save taxonomy.',ADMINDOMAIN); ?>
                         </p>
               </tr>
          </thead>
          <tbody>
               <tr class="" id="post_title">
                    <th valign="top">
                    	<label for="post_name" class="form-textfield-label"><?php echo __('Post Type Name',ADMINDOMAIN); ?> <span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" value="<?php if($edit_post){ echo stripslashes($edit_post[$_REQUEST['post-type']]['labels']['name']); } ?>" name="post_name" id="post_name" />
                         <p class="description"><?php echo __('e.g. Places, Events etc',ADMINDOMAIN); ?>.</p>
                    </td>
               </tr>
               <tr class="" id="post_title_slug">
                    <th valign="top">
                    	<label for="post_slug" class="form-textfield-label"><?php echo __('Post Type Slug',ADMINDOMAIN); ?> <span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
                    </th>
                    <td>
                         <input onkeydown="return character_validation(event)" onkeyup="return character_validation(event)" type="text" class="regular-text" <?php if($edit_post) { ?>readonly="readonly" <?php } ?> value="<?php if($edit_post) { echo $edit_post[@$_REQUEST['post-type']]['rewrite']['slug']; } ?>" name="post_slug" id="post_slug" />
                         <p class="description"><?php echo __('e.g. places, events etc',ADMINDOMAIN); ?>.<strong> <?php echo __('Note',ADMINDOMAIN); ?>: </strong><?php echo __('Just enter small letters without any space, Maximum 20 characters are allowed.',ADMINDOMAIN); ?></p>
                    </td>
               </tr>
               <tr class="" id="tax_name">
                    <th valign="top">
                    	<label for="taxonomy_name" class="form-textfield-label"><?php echo __('Custom Taxonomy name',ADMINDOMAIN); ?><span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" value="<?php if($edit_post) { echo stripslashes($edit_post[@$_REQUEST['post-type']]['taxonomies'][0]); } ?>" name="taxonomy_name" id="taxonomy_name" />
                         <p class="description"><?php echo __('e.g. Categories, Event Categories etc.',ADMINDOMAIN); ?></p>
                    </td>
               </tr>
               <tr class="" id="tax_slug">
                    <th valign="top">
                    	<label for="taxonomy_slug"  class="textfield-label"><?php echo __('Taxonomy Slug ',ADMINDOMAIN); ?> <span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" onkeydown="return character_validation(event)" onkeyup="return character_validation(event)" <?php if($edit_post) { ?>readonly="readonly" <?php } ?> value="<?php if($edit_post) { echo $edit_post[@$_REQUEST['post-type']]['slugs'][0]; } ?>" name="taxonomy_slug" id="taxonomy_slug">                         <p class="description"><?php echo __('e.g. eventcategories, placecategories etc',ADMINDOMAIN); ?>.<strong> <?php echo __('Note',ADMINDOMAIN); ?>: </strong><?php echo __('Just enter small letters without any space, Maximum 20 characters are allowed.',ADMINDOMAIN); ?></p>
                    </td>
               </tr>
               <tr class="" id="tag_title">
                    <th valign="top">
                    	<label for="tag_name" class="textfield-label"><?php echo __('Custom Tag name',ADMINDOMAIN); ?><span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" value="<?php if($edit_post) { echo stripslashes($edit_post[@$_REQUEST['post-type']]['taxonomies'][1]); } ?>" name="tag_name" id="tag_name">
                         <p class="description"><?php echo __('e.g. Tags, Events Tags etc.',ADMINDOMAIN); ?></p>
                    </td>
               </tr>
               <tr class="" id="tag_slug_name">
                    <th valign="top">
                    	<label for="tag_slug"  class="textfield-label"><?php echo __('Tags Slug',ADMINDOMAIN); ?> <span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" onkeydown="return character_validation(event)" onkeyup="return character_validation(event)"  <?php if($edit_post) { ?>readonly="readonly" <?php } ?> value="<?php if($edit_post) { echo $edit_post[@$_REQUEST['post-type']]['slugs'][1]; } ?>" name="tag_slug" id="tag_slug">
                         <p class="description"><?php echo __('e.g. etags, placetags etc',ADMINDOMAIN); ?>.<strong> <?php echo __('Note',ADMINDOMAIN); ?>: </strong><?php echo __('Just enter small letters without any space, Maximum 20 characters are allowed.',ADMINDOMAIN); ?></p>
                    </td>
               </tr>
               <tr>
              		<th valign="top">
                    	<label for="description" class="form-textarea-label"><?php echo __('Description',ADMINDOMAIN) ;?></label>
                    </th>
                    <td>
                    <textarea class="tb_textarea textarea" cols="40" rows="4" name="description" id="description"><?php if($edit_post && isset($edit_post[$_REQUEST['post-type']]['description'])) { echo $edit_post[@$_REQUEST['post-type']]['description']; } ?></textarea>
                    <p class="description"><?php echo __('Explain the purpose of this custom post type in few words.',ADMINDOMAIN); ?></p>
                    </td>
               </tr>
			   <?php
				global $tevolutions_icon ;
				$tevolutions_icon = explode(',',$tevolutions_icon[0]);
			   if((isset($_REQUEST['action']) && $_REQUEST['action'] =='add_taxonomy') || (isset($_REQUEST['post-type']) )){
			   ?>
               <tr>
                    <th valign="top">
                    	<label for="upload_image" class="form-textarea-label"><?php echo __('Upload Icon',ADMINDOMAIN) ;?></label>
                    </th>
                    <td>
                         <input id="upload_image" type="text" size="36" name="upload_image" value="<?php if($edit_post) { echo $edit_post[@$_REQUEST['post-type']]['menu_icon']; } ?>" /> <?php echo __('Or',ADMINDOMAIN);?>
                          <a data-id="upload_image" id="Upload Icon" type="submit" class="upload_file_button button"><?php  echo __('Upload Image',ADMINDOMAIN);?></a>   	   
                         <p class="description"><?php echo __('Enter the URL or upload an image of 16 x 16 pixels for the custom post type icon.',ADMINDOMAIN); ?>.</p>                         
                    </td>
               </tr>
			   <?php } 
			   
			do_action('tevolution_add_custom_post_field',$edit_post);?>   
                  
          </tbody>
     </table>
     <table class="form-table" >
          <thead>
               <tr>
               	<th colspan="3"><div class="tevo_sub_title"><?php echo __('Labels For Taxonomy Section',ADMINDOMAIN); ?></div><p class="tevolution_desc"><?php echo __('Specify the labels (basically names) for your custom post type. These labels are used for your custom post type which indicates certain action like add new listing, category (helps in grouping your posts), tags etc. They will appear under your post type section on your wp-admin panel.',ADMINDOMAIN)?></p></th>
               </tr>
          </thead>
          <tbody>
               <tr>
                    <th valign="top">
                   		<label for="add_new" class="form-textfield-label"><?php echo __('Add New text',ADMINDOMAIN); ?></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" value="<?php if($edit_post){ echo stripslashes($edit_post[@$_REQUEST['post-type']]['labels']['add_new']); } else { echo __('Add New',ADMINDOMAIN); } ?>" name="add_new" id="add_new">
                         <p class="description"><?php echo __('Will be displayed on Add new item links. By default it will display Add New for all the post types.',ADMINDOMAIN); ?></p>
                    </td>
               </tr>
               <tr>
                    <th valign="top">
                    	<label for="add_new_item" class="form-textfield-label"><?php echo __('Add New Item',ADMINDOMAIN); ?></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" value="<?php if($edit_post){ echo stripslashes($edit_post[@$_REQUEST['post-type']]['labels']['add_new_item']); } else { echo __('Add New Item',ADMINDOMAIN); } ?>" name="add_new_item" id="add_new_item">
                         <p class="description"><?php echo __('Will be displayed on the post detail page while adding a post. By default it will display New Item.',ADMINDOMAIN); ?></p></div>
                    </td>
               </tr>
               <tr>
                    <th valign="top">
                    	<label for="edit_item" class="form-textfield-label"><?php echo __('Edit Item text',ADMINDOMAIN); ?></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" value="<?php if($edit_post){ echo stripslashes($edit_post[@$_REQUEST['post-type']]['labels']['edit_item']); } else { echo __('Edit Item',ADMINDOMAIN); } ?>" name="edit_item" id="edit_item">
                         <p class="description"><?php echo __('Will be displayed on the post detail page while editing a post. By default it will display Edit Item.',ADMINDOMAIN); ?></p></div>
                    </td>
               </tr>
               <tr>
                    <th valign="top">
                    	<label for="view_item" class="form-textfield-label"><?php echo __('View Item button text',ADMINDOMAIN); ?></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" value="<?php if($edit_post){ echo stripslashes($edit_post[@$_REQUEST['post-type']]['labels']['view_item']); } else { echo __('View Item',ADMINDOMAIN); } ?>" name="view_item" id="view_item">
                         <p class="description"><?php echo __('Will be displayed on the post detail page after you submit the post. By default it will display View Item.',ADMINDOMAIN); ?></p></div>
                    </td>
               </tr>
               <tr>
                    <th valign="top">
                    	<label for="search_items" class="form-textfield-label"><?php echo __('Search button text',ADMINDOMAIN); ?></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" value="<?php if($edit_post){ echo stripslashes($edit_post[@$_REQUEST['post-type']]['labels']['search_items']); } else { echo __('Search Item',ADMINDOMAIN); } ?>" name="search_items" id="search_items">
                         <p class="description"><?php echo __('Will be displayed on the Search button. By default it will display Search Items.',ADMINDOMAIN); ?></p></div>
                    </td>
               </tr>
               <tr>
                    <th valign="top">
                    	<label for="not_found" class="form-textfield-label"><?php echo __('No item found text',ADMINDOMAIN); ?></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" value="<?php if($edit_post){ echo stripslashes($edit_post[@$_REQUEST['post-type']]['labels']['not_found']); } else { echo __('No Item Found',ADMINDOMAIN);; } ?>" name="not_found" id="not_found">
                         <p class="description"><?php echo __('Will be displayed when there is no item in Posts or Pages. By default it will display No posts found/No pages found.',ADMINDOMAIN); ?></p></div>
                    </td>
               </tr>
               <tr>
                    <th valign="top">
                    	<label for="not_found_in_trash" class="form-textfield-label"><?php echo __('Not item found in Trash text',ADMINDOMAIN); ?></label>
                    </th>
                    <td>
                         <input type="text" class="regular-text" value="<?php if($edit_post){ echo  stripslashes($edit_post[@$_REQUEST['post-type']]['labels']['not_found_in_trash']); } else { echo __('No item found in Trash',ADMINDOMAIN); }?>" name="not_found_in_trash" id="not_found_in_trash">
                        <p class="description"><?php echo __('Will be displayed when there is no item in Trash. By default it will display No posts found in Trash/No pages found in Trash.',ADMINDOMAIN); ?></p></div>
                    </td>
               </tr>
          </tbody>
     </table>
    <input type="submit" class="button button-primary button-hero" value="<?php echo __('Save Taxonomy',ADMINDOMAIN); ?>" name="submit-taxonomy" id="submit-1">
</form>
</div>
<?php
if(isset($_POST['submit-taxonomy']) && $_POST['submit-taxonomy'] !='')
{
	$post_slug = sanitize_title($_POST['post_slug']);
	$taxonomy_slug = sanitize_title($_POST['taxonomy_slug']);
	$tag_slug = sanitize_title($_POST['tag_slug']);
	$location = site_url().'/wp-admin/admin.php';
	/* -- Register custom taxonomy  --- */
	$custom_tags = array();
	$custom_taxonomy = array();
	$cutom_post = array();
	$custom_post_type = $post_slug;
	$custom_cat_type = $taxonomy_slug;
	$custom_tag_type = $tag_slug;
	if($_POST['upload_image'])
	 {
		$upload_image = $_POST['upload_image'];
	 }
	else
	 {
		$upload_image = TEMPL_PLUGIN_URL.'/images/templatic-logo.png';
	 }
	/* Register custom post type array */
	if($post_slug)
	 {
		$cutom_post[$post_slug] =
					array(	'label' 			=> stripslashes($_POST['post_name']),
							'labels' 			=> array(	'name' 			 => 	stripslashes($_POST['post_name']),
													'singular_name' 	 => 	stripslashes($_POST['post_name']),
													'menu_name' 	 => 	stripslashes($_POST['post_name']),
													'add_new' 		 =>  stripslashes($_POST['add_new']),
													'add_new_item' 	 =>  stripslashes($_POST['add_new_item']),
													'edit_item' 		 =>  stripslashes($_POST['edit_item']),
													'new_item' 		 =>  stripslashes($_POST['new_item']),
													'view_item'		 =>  stripslashes($_POST['view_item']),
													'search_items' 	 =>  stripslashes($_POST['search_items']),
													'not_found' 		 =>  stripslashes($_POST['not_found']),
													'not_found_in_trash' =>  stripslashes($_POST['not_found_in_trash'])),
							'public' 			=> true,
							'has_archive'       => true,
							'can_export'		=> true,
							'show_ui' 		=> true, /* UI in admin panel*/
							'_builtin' 		=> false, /* It's a custom post type, not built in*/
							'_edit_link' 		=> 'post.php?post=%d',
							'capability_type' 	=> 'post',
							'menu_icon' 		=> $upload_image,
							'hierarchical' 	=> false,
							'rewrite' 		=> array("slug" => "$custom_post_type"), /* Permalinks*/
							'query_var' 		=> "$custom_post_type", /* This goes to the WP_Query schema*/
							'supports' 		=> array(	'title','author','excerpt','thumbnail','comments','editor', 'trackbacks','custom-fields','revisions'),
							'show_in_nav_menus'	=> true ,
							'description'       => $_POST['description'],
							'slugs'		     => array("$custom_cat_type","$custom_tag_type"),
							'taxonomies'	     => array($_POST['taxonomy_name'],stripslashes($_POST['tag_name']))
						);
	 }
		global $wpdb;
		/* add this taxonomy in custom fields selection */
		$all_post_type = '';
		$post_heading = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_title = '[#taxonomy_name#]' and $wpdb->posts.post_type = 'custom_fields'");
		$all_post_type = get_post_meta($post_heading, 'post_type' , true);
		/* Sort order update new create post type*/
		update_post_meta($post_heading,$post_slug.'_sort_order',get_post_meta($post_heading,'sort_order',true));		
		if(in_array($post_slug,explode(",",$all_post_type))){
			$all_post_type = $all_post_type; 
		}else{
			$all_post_type .= ",".$post_slug;
		 }
		update_post_meta($post_heading, 'post_type' ,$all_post_type);
		$post_category_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_title = 'Post Category' and $wpdb->posts.post_type = 'custom_fields'");
		update_post_meta($post_category_id, 'post_type_'.$post_slug.'' , 'all');
		/* Sort order update new create post type*/
		update_post_meta($post_category_id,$post_slug.'_sort_order',get_post_meta($post_category_id,'sort_order',true));
		update_post_meta($post_category_id,$post_slug.'_heading_type','[#taxonomy_name#]');
		$all_post_type = get_post_meta($post_category_id, 'post_type' , true);
		if(in_array($post_slug,explode(",",$all_post_type)))
		 {
			$all_post_type = $all_post_type; 
		 }
		else
		 {
			$all_post_type .= ",".$post_slug;
		 }
		update_post_meta($post_category_id, 'post_type' ,$all_post_type);
		
		$post_title_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_title = 'Post Title' and $wpdb->posts.post_type = 'custom_fields'");
		update_post_meta($post_title_id, 'post_type_'.$post_slug.'' , 'all');
		/* Sort order update new create post type*/
		update_post_meta($post_title_id,$post_slug.'_sort_order',get_post_meta($post_title_id,'sort_order',true));
		update_post_meta($post_title_id,$post_slug.'_heading_type','[#taxonomy_name#]');
		$all_post_type = get_post_meta($post_title_id, 'post_type' , true);
		if(in_array($post_slug,explode(",",$all_post_type)))
		 {
			$all_post_type = $all_post_type; 
		 }
		else
		 {
			$all_post_type .= ",".$post_slug;
		 }
		update_post_meta($post_title_id, 'post_type' ,$all_post_type);
		
		
		$post_excerpt_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_title = 'Post Excerpt' and $wpdb->posts.post_type = 'custom_fields'");
		update_post_meta($post_excerpt_id, 'post_type_'.$post_slug.'' , 'all');
		/* Sort order update new create post type*/
		update_post_meta($post_excerpt_id,$post_slug.'_sort_order',get_post_meta($post_excerpt_id,'sort_order',true));
		update_post_meta($post_excerpt_id,$post_slug.'_heading_type','[#taxonomy_name#]');
		$all_post_type = get_post_meta($post_excerpt_id, 'post_type' , true);
		if(in_array($post_slug,explode(",",$all_post_type)))
		 {
			$all_post_type = $all_post_type; 
		 }
		else
		 {
			$all_post_type .= ",".$post_slug;
		 }
		update_post_meta($post_excerpt_id, 'post_type' ,$all_post_type);
		
		$post_image_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_title = 'Post Images' and $wpdb->posts.post_type = 'custom_fields'");
		update_post_meta($post_image_id, 'post_type_'.$post_slug.'' , 'all');
		/* Sort order update new create post type*/
		update_post_meta($post_image_id,$post_slug.'_sort_order',get_post_meta($post_image_id,'sort_order',true));
		update_post_meta($post_image_id,$post_slug.'_heading_type','[#taxonomy_name#]');
		$all_post_type = get_post_meta($post_image_id, 'post_type' , true);
		if(in_array($post_slug,explode(",",$all_post_type)))
		 {
			$all_post_type = $all_post_type; 
		 }
		else
		 {
			$all_post_type .= ",".$post_slug;
		 }
		update_post_meta($post_image_id, 'post_type' ,$all_post_type);
		
		
		$post_content_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_title = 'Post Content' and $wpdb->posts.post_type = 'custom_fields'");
		update_post_meta($post_content_id, 'post_type_'.$post_slug.'' , 'all');
		/* Sort order update new create post type*/
		update_post_meta($post_content_id,$post_slug.'_sort_order',get_post_meta($post_content_id,'sort_order',true));
		update_post_meta($post_content_id,$post_slug.'_heading_type','[#taxonomy_name#]');
		$all_post_type = get_post_meta($post_content_id, 'post_type' , true);
		if(in_array($post_slug,explode(",",$all_post_type)))
		 {
			$all_post_type = $all_post_type; 
		 }
		else
		 {
			$all_post_type .= ",".$post_slug;
		 }
		update_post_meta($post_content_id, 'post_type' ,$all_post_type);
		/* add this taxonomy in custom fields selection */
	/* Register custom taxonomy*/
	if($taxonomy_slug)
	 {
		$custom_taxonomy[$taxonomy_slug] =	
					array (	"hierarchical" => true, 
							"label" 		=> stripslashes($_POST['taxonomy_name']),
							"post_type"	=> str_replace(" ","",strtolower(sanitize_title($_POST['post_name']))),
							"post_slug"	=> str_replace(" ","",strtolower($_POST['post_slug'])),
							'labels' 		=> array(	'name' 			=>  stripslashes($_POST['taxonomy_name']),
												'singular_name' 	=>  sanitize_title($_POST['taxonomy_slug']),
												'search_items' 	=>  CUSTOM_MENU_CAT_SEARCH,
												'popular_items' 	=>  CUSTOM_MENU_CAT_SEARCH,
												'all_items' 		=>  CUSTOM_MENU_CAT_ALL,
												'parent_item' 		=>  CUSTOM_MENU_CAT_PARENT,
												'parent_item_colon' =>  CUSTOM_MENU_CAT_PARENT_COL,
												'edit_item' 		=>  CUSTOM_MENU_CAT_EDIT,
												'update_item'		=>  CUSTOM_MENU_CAT_UPDATE,
												'add_new_item' 	=>  CUSTOM_MENU_CAT_ADDNEW,
												'new_item_name' 	=>  CUSTOM_MENU_CAT_NEW_NAME,	), 
							'public' 		=> true,
							'show_ui' 	=> true,
							'rewrite' 		 => array("slug" => $custom_cat_type),);
	 }
	 /*Register custom tag */
	if($tag_slug)
	 {
		$custom_tags[$tag_slug] = 
					array(	"hierarchical" => false,
							"label" 		=> stripslashes($_POST['tag_name']),
							"post_type"	=> str_replace(" ","",strtolower(sanitize_title($_POST['post_name']))),
							"post_slug"	=> str_replace(" ","",strtolower($_POST['post_slug'])),
							'labels' 		=> array(	'name' 			=>  stripslashes($_POST['tag_name']),
												'singular_name' 	=>  sanitize_title($_POST['tag_slug']),
												'search_items' 	=>  CUSTOM_MENU_TAG_SEARCH,
												'popular_items' 	=>  CUSTOM_MENU_TAG_POPULAR,
												'all_items' 		=>  CUSTOM_MENU_TAG_ALL,
												'parent_item' 		=>  CUSTOM_MENU_TAG_PARENT,
												'parent_item_colon' =>  CUSTOM_MENU_TAG_PARENT_COL,
												'edit_item' 		=>  CUSTOM_MENU_TAG_EDIT,
												'update_item'		=>  CUSTOM_MENU_TAG_UPDATE,
												'add_new_item' 	=>  CUSTOM_MENU_TAG_ADD_NEW,
												'new_item_name' 	=>  CUSTOM_MENU_TAG_NEW_ADD,	),  
							'public' 		=> true,
							'show_ui' 	=> true,
							'rewrite' 		 => array("slug" => $custom_tag_type),	);
	 }
if(get_option('templatic_custom_post')):
	$original = array();
	if($_SESSION['taxonomy_post_type'])
	  {
		$original = get_option('templatic_custom_post');
		$tax_slug = $original[$_SESSION['taxonomy_post_type']]['slugs'][0];
		$tag_slug = $original[$_SESSION['taxonomy_post_type']]['slugs'][1];
		$or_arr = $cutom_post[$_POST['post_slug']];
		$cutom_post[$_POST['post_slug']] = array_replace($original[$_SESSION['taxonomy_post_type']],$or_arr);
		$post_arr_merge = $cutom_post;
		global $wpdb;
		$wpdb->query("Update $wpdb->posts SET post_type = '".str_replace(" ","",strtolower($_POST['post_slug']))."' where post_type = '".str_replace(" ","",strtolower($original[$_SESSION['taxonomy_post_type']]['label']))."'");
		$wpdb->query("Update $wpdb->term_taxonomy SET taxonomy = '".$_POST['taxonomy_slug']."' where taxonomy = '".$tax_slug."'");
		$wpdb->query("Update $wpdb->term_taxonomy SET taxonomy = '".$_POST['tag_slug']."' where taxonomy = '".$tag_slug."'");
		unset($original[$_SESSION['taxonomy_post_type']]);
		if(empty($original))
		{
			$original = 1;
		}
	  }
	if($original)
	  {
		if($original == 1):
			$post_arr_merge = $cutom_post;
		else:	
			$post_arr_merge = array_merge($original,$cutom_post);
		endif;	
	  }
	else
	  {
		 $post_arr_merge = array_merge(get_option('templatic_custom_post'),$cutom_post);
	  }
else:
	$post_arr_merge = $cutom_post;
endif;
ksort($post_arr_merge);
update_option('templatic_custom_post',$post_arr_merge);
if(get_option('templatic_custom_taxonomy')):
	$original = array();
	if(isset($_SESSION['taxonomy_post_type']))
	  {
		$post_arr = get_option('templatic_custom_post');
		$original = get_option('templatic_custom_taxonomy');
		if($_POST['taxonomy_slug']):
			$or_arr = $custom_taxonomy[sanitize_title($_POST['taxonomy_slug'])];
			$custom_taxonomy[sanitize_title($_POST['taxonomy_slug'])] = array_replace($original[$tax_slug],$or_arr);
		endif;
		$taxonomy_arr_merge = $custom_taxonomy;
		unset($original[$tax_slug]);
		if(empty($original))
		{
			$original = 1;
		}
	  }
	if($original)
	  {
  		  if($original == 1):
			  $taxonomy_arr_merge = $custom_taxonomy;
		  else:
			  $taxonomy_arr_merge = array_merge($original,$custom_taxonomy);
		  endif;	  
	  }
	else
	  {
		  $taxonomy_arr_merge = array_merge(get_option('templatic_custom_taxonomy'),$custom_taxonomy);
	  }
else:
	$taxonomy_arr_merge = $custom_taxonomy;
endif;
ksort($taxonomy_arr_merge);
update_option('templatic_custom_taxonomy',$taxonomy_arr_merge);
if(get_option('templatic_custom_tags')):
	$original = array();
	if(isset($_SESSION['taxonomy_post_type']))
	  {
		$post_arr = get_option('templatic_custom_post');
		$original = get_option('templatic_custom_tags');
		if($_POST['tag_slug']):
			$or_arr = $custom_tags[sanitize_title($_POST['tag_slug'])];
			$custom_tags[sanitize_title($_POST['tag_slug'])] = array_replace($original[$tag_slug],$or_arr);
		endif;	
		$tags_arr_merge = $custom_tags;
		unset($original[$tag_slug]);
		if(empty($original))
		{
			$original = 1;
		}
	  }
	if($original)
	  {
  		  if($original == 1):
		  		$tags_arr_merge = $custom_tags;
		  else:
		  		$tags_arr_merge = array_merge($original,$custom_tags);
		  endif;		
	  }
	else
	  {
		  $tags_arr_merge = array_merge(get_option('templatic_custom_tags'),$custom_tags);
	  }
else:
	$tags_arr_merge = $custom_tags;
endif;
ksort($tags_arr_merge);
update_option('templatic_custom_tags',$tags_arr_merge);
?>	
	<form action="<?php echo $location; ?>" method="get" id="frm_edit_custom_taxonomy" name="frm_edit_custom_taxonomy">
        <input type="hidden" value="custom_setup" name="page" />
        <input type="hidden" value="custom_setup" name="ctab" />
        <input type="hidden" value="add" name="custom_msg_type" />
    </form>
	<script type="text/javascript" async>document.frm_edit_custom_taxonomy.submit();</script>';
<?php	
	exit;	
}
?>