<?php
global $upload_folder_path,$current_user,$upload_folder_path;

$current_user = wp_get_current_user();
$cur_post_type = $_POST['submit_post_type'];

if(!isset($post_title))
	$post_title=stripslashes($_POST['post_title']);
if(!isset($post_content))
	$post_content=$_POST['post_content'];

if($_REQUEST['pid'])
{	/* exicute when comes for edit the post */
	$large_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'medium');
	$thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'thumb');
	$largest_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'large');		
}

/* Set curent language in cookie */
if(is_plugin_active('wpml-translation-management/plugin.php')){
	global $sitepress;
	$_COOKIE['_icl_current_language'] = $sitepress->get_current_language();
}

$_REQUEST['imgarr'] = (isset($_REQUEST['imgarr']) && $_REQUEST['imgarr']!='')? explode(",",$_REQUEST['imgarr']): '';

?>
<!-- start content part-->
<div id="content" role="main" class="large-9 small-12 columns">
	
	<?php do_action('templ_preview_before_post_title');/*do_action before the preview post title */?>
	
	<?php if($post_title != ""): ?>
		<h1 class="entry-title"><?php echo stripslashes($post_title); ?></h1>
    <?php endif; ?>
        
	<?php do_action('templ_preview_after_post_title');/*do_action after previwe post title. */?>
    
    <?php do_action('tmpl_preview_before_post_gallery');
	
	do_action('tmpl_preview_page_gallery');/* Add Action for preview display single post image gallery. */
	
	do_action('tmpl_preview_after_post_gallery');
	
	do_action('templ_preview_before_post_content'); /*Add Action for before preview post content. */
	
	if(isset($post_content) && $post_content !=''): /* Check condition for post content not balank */?>      
            <section class="entry-content">
                <h2><span>
					<?php	
						$post_description=ucfirst(str_replace('Post',$cur_post_type,'Post Description')); 
						if(function_exists('icl_register_string')){
							icl_register_string(DOMAIN,$post_description,$post_description);
						}
						if(function_exists('icl_t')){
							$post_description1 = icl_t(DOMAIN,$post_description,$post_description);
						}else{
							$post_description1 = __($post_description,DOMAIN); 
						}
						echo $post_description1;
					?>
				</span></h2>
                <p><?php echo nl2br(stripslashes($post_content)); ?></p>    
            </section>        
    <?php endif; /* Finish the post content condition */
	
	do_action('templ_preview_after_post_content'); /*Add Action for after preview post content. */
	
	do_action('tmpl_preview_page_fields_collection',$cur_post_type);
	
	do_action('templ_preview_page_file_upload');	/* Add action for preview file upload*/
	
	do_action('templ_preview_address_map');	/*Add action for display preview map */?>
</div>
<!--End content part -->
<script type="text/javascript" async >
(function($) {	
	Demo.init();
})(jQuery); 
</script>