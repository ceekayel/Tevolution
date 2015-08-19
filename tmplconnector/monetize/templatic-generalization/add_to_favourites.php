<?php
define('REMOVE_FAVOURITE_TEXT',__('Added','templatic'));
/* This function would add properly to favorite listing and store the value in wp_usermeta table user_favorite field */
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
	echo '<a href="javascript:void(0);" class="removefromfav added" data-id='.$post_id.' onclick="javascript:addToFavourite(\''.$post_id.'\',\'remove\');"><i class="fa fa-heart"></i>'.__('Added','templatic').'</a>';
	
}
/* This function would remove the favorited property earlier */
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
	echo '<a class="addtofav removed" href="javascript:void(0);" data-id='.$post_id.'  onclick="javascript:addToFavourite(\''.$post_id.'\',\'add\');"><i class="fa fa-heart-o"></i>';
	_e('Add to favorites','templatic'); 
	echo '</a>';
}
/*
	add to favourite HTML code LIKE addtofav link and remove to fav link
*/
function tevolution_favourite_html($user_id='',$post='')
{
	if(function_exists('tmpl_wp_is_mobile') && !tmpl_wp_is_mobile()){
		global $current_user,$post;
		$post_id = $post->ID;
		$add_to_favorite = __('Add to favorites','templatic');
		$added = __('Added','templatic');
		if(function_exists('icl_register_string')){
			icl_register_string('templatic','tevolution'.$add_to_favorite,$add_to_favorite);
			$add_to_favorite = icl_t('templatic','tevolution'.$add_to_favorite,$add_to_favorite);
			icl_register_string('templatic','tevolution'.$added,$added);
			$added = icl_t('templatic','tevolution'.$added,$added);
		}
		$user_meta_data = get_user_meta($current_user->ID,'user_favourite_post',true);
		if($post->post_type !='post'){
			if(is_tax()){ $class=""; }else{ if(!isset($_GET['post_type'])){ $class=""; }else{ $class="";  } }  
			if($user_meta_data && in_array($post_id,$user_meta_data))
			{
				
				?>
				<span id="tmplfavorite_<?php echo $post_id;?>" class="fav fav_<?php echo $post_id;?>"  > <a href="javascript:void(0);"  id="tmpl_login_frm_<?php echo $post_id; ?>" data-id='<?php echo $post_id; ?>'  class="removefromfav <?php echo $class; ?> small_btn" onclick="javascript:addToFavourite('<?php echo $post_id;?>','remove');"><?php echo $added;?></a>   </span>    
				<?php
			}else{
				
				if($current_user->ID ==''){
					$data_reveal_id ='data-reveal-id="tmpl_reg_login_container"';
				}else{
					$data_reveal_id ='';
				}
				
			?>
				<span id="tmplfavorite_<?php echo $post_id;?>" class="fav fav_<?php echo $post_id;?>"><a href="javascript:void(0);" <?php echo $data_reveal_id; ?> id="tmpl_login_frm_<?php echo $post_id; ?>" data-id='<?php echo $post_id; ?>'  class="addtofav <?php echo $class; ?> small_btn"  onclick="javascript:addToFavourite('<?php echo $post_id;?>','add');"><?php echo $add_to_favorite;?></a></span>
			<?php } 
		}
	}
}
add_action('init','tev_add_to_favourites',11);
/* Add this to add to favourites only if current theme is support */
function tev_add_to_favourites(){
	global $current_user;
	if(current_theme_supports('tevolution_my_favourites') && !DOING_AJAX){
		global $post;
		add_action('templ_post_title','tevolution_favourite_html',11,@$post);
	}
}
/*
*add post id while add to favorite as a logout user
*/
add_action('wp_footer','add_post_id_add_to_fav',99);
function add_post_id_add_to_fav()
{
	global $current_user;
	if($current_user->ID == '')
	{
		?>
        <script type="text/javascript" async >
			jQuery(function() {
				jQuery('.addtofav').live('click',function(){
					post_id = jQuery(this).attr('data-id');
					/*add  html while login to add to favorite*/
					jQuery('#tmpl_login_frm form#loginform').append('<input type="hidden" name="post_id" value="'+post_id+'" />');
					jQuery('#tmpl_login_frm form#loginform').append('<input type="hidden" name="addtofav" value="addtofav" />');
					jQuery('#tmpl_login_frm form#loginform [name=redirect_to]').val(jQuery(location).attr('href'));
					/*add  html while register to add to favorite*/
					jQuery('#tmpl_sign_up form#userform').append('<input type="hidden" name="post_id" value="'+post_id+'" />');
					jQuery('#tmpl_sign_up form#userform').append('<input type="hidden" name="addtofav" value="addtofav" />');
					jQuery('#tmpl_sign_up form#userform [name=reg_redirect_link]').val(jQuery(location).attr('href'));
				});
			});
		</script>
       	<?php
	}
}
/*
* update user while add to favorite when he/she login or register.
*/
add_filter('tevolution_register_redirect','tmpl_add_fav_logout_user',10,2);
add_filter('tevolution_login_redirect','tmpl_add_fav_logout_user',10,2);
function tmpl_add_fav_logout_user($redirect_url,$user)
{
	if(isset($_POST['addtofav']) && $_POST['addtofav'] == 'addtofav' && isset($_POST['post_id']) && $_POST['post_id'] != '')
	{
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global  $sitepress;
			$sitepress->switch_lang($language);
		}
		$user_meta_data = array();
		$user_meta_data = get_user_meta($current_user->ID,'user_favourite_post',true);
		$user_meta_data[]= $_POST['post_id'];
		update_user_meta($user->ID, 'user_favourite_post', $user_meta_data);
	}
	if(isset($_POST['reg_redirect_link']) && $_POST['reg_redirect_link'] != '')
	{
		$return_url = $_POST['reg_redirect_link'];
	}
	elseif(isset($_POST['redirect_to']) && $_POST['redirect_to'] != '')
	{
		$return_url = $_POST['redirect_to'];
	}
	else
	{
		$return_url = $redirect_url;
	}
	return $return_url;
}

?>