<?php
get_header(); // display header 
global $upload_folder_path;
if(isset($_POST['preview'])){
	
	$_SESSION['custom_fields'] = $_POST; // set custom_fields session	
	if(isset($_POST['category']))
	 {
		$_SESSION['category'] = $_POST['category'];
	 }
}
/* show preview of uploaded image begin */
global $upload_folder_path,$current_user;
if(isset($_POST['imgarr']) && $_POST['imgarr']!=""){
	$_SESSION['file_info']=	explode(",",$_POST['imgarr']);
	$_SESSION["templ_file_info"] = explode(",",$_POST['imgarr']);
}
if($_SESSION["file_info"])
{
	foreach($_SESSION["file_info"] as $image_id=>$val)
	{
		 $image_src =  get_template_directory_uri().'/images/tmp/'.$val;
		 break;
	}				
	
}else
{
	/* exucutre when come after go back nad edit */
	$image_src = @$thumb_img_arr[0];
	if($_REQUEST['pid']){
		$large_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'medium');
		$thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'thumb');
	}
	$image_src = $large_img_arr[0];		
}
$current_user = wp_get_current_user();
$cur_post_id = $_SESSION['custom_fields']['cur_post_id'];
$cur_post_type = get_post_meta($cur_post_id,'submit_post_type',true);
if ((isset($_GET['action']) && isset($_GET['_wpnonce'])) && (! wp_verify_nonce( $_GET['_wpnonce'], 'delete_link' )) ){
	die('<p>'.__('your security settings do not permit you to delete this content',DOMAIN).'</p>');
}
if(!isset($post_title))
	$post_title=stripslashes($_SESSION['custom_fields']['post_title']);
if(!isset($post_content))
	$post_content=$_SESSION['custom_fields']['post_content'];
//contion for captcha inserted properly or not.
$tmpdata = get_option('templatic_settings');
if(isset($tmpdata['user_verification_page']) && $tmpdata['user_verification_page'] != "")
{
	$display = $tmpdata['user_verification_page'];
}
else
{
	$display = "";	
}
$id = $_SESSION['custom_fields']['cur_post_id'];
$permalink = get_permalink( $id );
if( is_plugin_active('wp-recaptcha/wp-recaptcha.php') && $tmpdata['recaptcha'] == 'recaptcha' && in_array('submit',$display) && 'preview' == @$_REQUEST['page'] && 'delete' != $_REQUEST['action'] ){
		require_once( get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php');
		$a = get_option("recaptcha_options");
		$privatekey = $a['private_key'];
						$resp = recaptcha_check_answer ($privatekey,
								getenv("REMOTE_ADDR"),
								$_POST["recaptcha_challenge_field"],
								$_POST["recaptcha_response_field"]);
											
		if (!$resp->is_valid ) {
			if($_REQUEST['pid'] != '')
			 {
				wp_redirect(get_permalink($cur_post_id).'/?ptype=post_event&pid='.$_REQUEST['pid'].'&action=edit&backandedit=1&ecptcha=captch');
			 }
			 else
			 {
				wp_redirect(get_permalink($cur_post_id).'/?ptype=post_event&backandedit=1&ecptcha=captch');	 
			 }
			exit;
		} 
	}
if(file_exists(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php') && is_plugin_active('are-you-a-human/areyouahuman.php') && $tmpdata['recaptcha'] == 'playthru'  && in_array('submit',$display) && 'preview' == @$_REQUEST['page'] && 'delete' != $_REQUEST['action'] )
{
	require_once( get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php');
	require_once(get_tmpl_plugin_directory().'are-you-a-human/includes/ayah.php');
	$ayah = new AYAH();
	$score = $ayah->scoreResult();
	if(!$score)
	{
		wp_redirect(get_permalink($cur_post_id).'/?ptype=post_event&backandedit=1&invalid=playthru');
		exit;
	}
}
if($_REQUEST['pid'])
{	/* exicute when comes for edit the post */
	$large_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'medium');
	$thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'thumb');
	$largest_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'large');		
}
/* show preview of uploaded image end */
do_action('templ_before_container_breadcrumb'); /*do action for display the bradcrumb in between header and container. */
?>
<!-- start content part-->
<div id="content" role="main">
	<?php do_action('templ_inside_container_breadcrumb'); /*do action for display the bradcrumn  inside the container. */ ?>
	
	<?php include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/submit_preview_buttons.php"); /* fetch publish options and button options */?>
	
	<?php do_action('templ_preview_before_post_title');/*do_action before the preview post title */?>
		<?php if($post_title != ""): ?>
	    	<h1 class="entry-title"><?php echo stripslashes($post_title); ?></h1>
        <?php endif; ?>    
	<?php do_action('templ_preview_after_post_title');/*do_action after previwe post title. */?>
    
	<?php do_action('tmpl_preview_page_gallery');/* Add Action for preview display single post image gallery. */?> 
	
		
	<?php do_action('templ_preview_before_post_content'); /*Add Action for before preview post content. */?> 
	<?php if(isset($post_content) && $post_content !=''): /* Check condition for post content not balank */?>      
            <div class="entry-content">
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
            </div>        
    <?php endif; /* Finish the post content condition */ ?>
    
    <?php do_action('templ_preview_after_post_content'); /*Add Action for after preview post content. */?> 	
   
	<?php		
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete'):
			do_action('tmpl_detail_page_custom_fields_collection');
		else:	
			do_action('tmpl_preview_page_fields_collection',$cur_post_type);
		endif;	
	?>  
           	
	<?php do_action('templ_preview_page_file_upload');// Add action for preview file upload	 ?>
      
	<?php do_action('templ_preview_address_map');/*Add action for display preview map */?>	
     <div id="back-top" class="get_direction clearfix">
          <a href="#top" class="button getdir" style=""><?php _e('Back to Top',DOMAIN);?></a>
     </div>
</div>
<!--End content part -->
<div class="sidebar" id="sidebar-primary">
<?php dynamic_sidebar($cur_post_type.'_detail_sidebar');?>
</div>
<?php get_footer(); ?>