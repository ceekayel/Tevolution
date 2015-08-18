<?php
define('ADD_FAVOURITE_TEXT',__('Add to favorite',DOMAIN));
define('REMOVE_FAVOURITE_TEXT',__('Added',DOMAIN));
//This function would add properly to favorite listing and store the value in wp_usermeta table user_favorite field
function add_to_favorite($post_id,$language='')
{
	global $current_user,$post;
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		global  $sitepress;
		$sitepress->switch_lang($language);
	}
	$user_meta_data = array();
	$user_meta_data = get_user_meta($current_user->ID,'user_favourite_post',true);
	$user_meta_data[]=$post_id;
	update_user_meta($current_user->ID, 'user_favourite_post', $user_meta_data);
	echo '<a href="javascript:void(0);" class="removefromfav" onclick="javascript:addToFavourite(\''.$post_id.'\',\'remove\');">'.__('Added',DOMAIN).'</a>';
	
}
//This function would remove the favorited property earlier
function remove_from_favorite($post_id,$language='')
{
	global $current_user,$post;
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		global  $sitepress;
		$sitepress->switch_lang($language);
	}
	$user_meta_data = array();
	$user_meta_data = get_user_meta($current_user->ID,'user_favourite_post',true);
	if(in_array($post_id,$user_meta_data))
	{
		$user_new_data = array();
		foreach($user_meta_data as $key => $value)
		{
			if($post_id == $value)
			{
				$value= '';
			}else{
				$user_new_data[] = $value;
			}
		}	
		$user_meta_data	= $user_new_data;
	}
	update_user_meta($current_user->ID, 'user_favourite_post', $user_meta_data); 	
	echo '<a class="addtofav" href="javascript:void(0);"  onclick="javascript:addToFavourite(\''.$post_id.'\',\'add\');">';
	_e('Add to favorite',DOMAIN); 
	echo '</a>';
}
/*
Name :tevolution_favourite_html
Description : add to favourite HTML code LIKE addtofav link and remove to fav link
*/
function tevolution_favourite_html($user_id='',$post='')
{
	global $current_user,$post;
	$post_id = $post->ID;
	$add_to_favorite = __('Add to favorite',DOMAIN);
	$added = __('Added',DOMAIN);
	if(function_exists('icl_register_string')){
		icl_register_string(DOMAIN,'tevolution'.$add_to_favorite,$add_to_favorite);
		$add_to_favorite = icl_t(DOMAIN,'tevolution'.$add_to_favorite,$add_to_favorite);
		icl_register_string(DOMAIN,'tevolution'.$added,$added);
		$added = icl_t(DOMAIN,'tevolution'.$added,$added);
	}
	$user_meta_data = get_user_meta($current_user->ID,'user_favourite_post',true);
	if($post->post_type !='post'){
		if(is_tax()){ $class=""; }else{ if(!isset($_GET['post_type']) && @$_GET['post_type']==''){ $class=""; }else{ $class="";  } }  
		if($user_meta_data && in_array($post_id,$user_meta_data))
		{
			?>
		<span id="tmplfavorite_<?php echo $post_id;?>" class="fav fav_<?php echo $post_id;?>"  > <a href="javascript:void(0);" class="removefromfav <?php echo $class; ?> small_btn" onclick="javascript:addToFavourite('<?php echo $post_id;?>','remove');"><?php echo $added;?></a>   </span>    
			<?php
		}else{
		?>
		<span id="tmplfavorite_<?php echo $post_id;?>" class="fav fav_<?php echo $post_id;?>"><a href="javascript:void(0);" class="addtofav <?php echo $class; ?> small_btn"  onclick="javascript:addToFavourite('<?php echo $post_id;?>','add');"><?php echo $add_to_favorite;?></a></span>
		<?php } 
	}
}
 /*Add action wp_footer for add to attend event script */
add_action('wp_footer', 'tevolution_addtoattendevent_script'); 
function tevolution_addtoattendevent_script()
{
	if(is_404()){
		return '';
	}
	

	?>
    <script type="text/javascript">
	/* <![CDATA[ */
	function addToFavourite(post_id,action)
	{
		<?php 
		global $current_user;
		if($current_user->ID==''){ 
			if(function_exists('get_tevolution_register_permalink')){
				$link =  get_tevolution_register_permalink();
			}else{
				$link = site_url();
			}
		?>
	
		window.location.href="<?php echo $link; ?>";
		<?php 
		} else {
		$language='';
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$language="&language=".$current_lang_code;
		}
		?>
		var fav_url; 
		if(action == 'add')
		{
				fav_url = '<?php echo plugin_dir_url( __FILE__ ); ?>/ajax_event.php?ptype=favorite&action=add&pid='+post_id+"<?php echo $language;?>";
			
		}
		else
		{
				fav_url = '<?php echo plugin_dir_url( __FILE__ ); ?>/ajax_event.php?ptype=favorite&action=removed&pid='+post_id+"<?php echo $language;?>";
			//	document.getElementById('post-'+post_id).style.display='none';	
			
		}
	
		var $ac = jQuery.noConflict();
		$ac.ajax({	
			url: fav_url ,
			type: 'GET',
			dataType: 'html',
			timeout: 20000,
			error: function(){
				alert("Error loading user's attending event.");
			},
			success: function(html){	
			<?php 
			if(isset($_REQUEST['sort']) && $_REQUEST['sort']=='favourites')
			{ ?>
				document.getElementById('post-'+post_id).style.display='none';	
				<?php
			}
			?>
				jQuery('.fav_'+post_id).html(html);
			}
		});
		return false;
		<?php } ?>
	}
	/* ]]> */
	</script>
	<?php
		
}
add_action('init','tev_add_to_favourites',11);
/* Add this to add to favourites only if current theme is support */
function tev_add_to_favourites(){
	global $current_user;
	if(current_theme_supports('tevolution_my_favourites') ){
		
		add_action('templ_post_title','tevolution_favourite_html',11,@$post);
	}
}
?>