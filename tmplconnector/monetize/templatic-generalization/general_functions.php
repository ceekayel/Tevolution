<?php
/*
 side add css and javascript file in side html head tag 
 */
if(!function_exists('strip_array_indices')){
	function strip_array_indices( $ArrayToStrip ) {
		if(!empty($ArrayToStrip)){
			foreach( $ArrayToStrip as $objArrayItem) {
				$NewArray[] =  $objArrayItem;
			}
		}
		return( $NewArray );
	}
}

/*
* to fetch the server date and time
*/
add_action('tevolution_details','tevolution_server_date_time');
function tevolution_server_date_time() {
	
	
	$tev_time_now = date("D dS M, Y h:i a");
	$timezone_now = date("e, (T P)");
	echo "<p id='server-date-time'><strong>".__('Server Date/Time','templatic-admin').":</strong> $tev_time_now <br/><strong>".__('Time Zone','templatic-admin').": </strong> $timezone_now</p>";
	
}
/* 
* get t evolution version details
*/
function tevolution_version() {
	
	$plugin_file = get_tmpl_plugin_directory()."Tevolution/templatic.php";
	$plugin_details = get_plugin_data( $plugin_file, $markup = true, $translate = true ); 
	$version = @$plugin_details['Version'];
	echo " <span class='tevolution_version'>".@$version."<span>";
}

/*
 * send inquiry mail function 
 */
add_action('wp_ajax_tevolution_send_inquiry_form','tevolution_send_inquiry_form');
add_action('wp_ajax_nopriv_tevolution_send_inquiry_form','tevolution_send_inquiry_form');
function tevolution_send_inquiry_form(){
	global $wpdb;
	$post = array();
	if( @$_REQUEST['postid'] ){
		$post = get_post($_REQUEST['postid']);
	}
	if(isset($_REQUEST['your_iemail']) && $_REQUEST['your_iemail'] != "")
	{	
		/* CODE TO CHECK CAPTCHA */
		$tmpdata = get_option('templatic_settings');
		$display = $tmpdata['user_verification_page'];
		if(!empty($display) &&  in_array('sendinquiry',$display))
		{
			/*fetch captcha private key*/
			$privatekey = $tmpdata['secret'];
			/*get the response from captcha that the entered captcha is valid or not*/
			$response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=".$privatekey."&response=".$_REQUEST["g-recaptcha-response"]."&remoteip=".getenv("REMOTE_ADDR"));
			/*decode the captcha response*/
			$responde_encode = json_decode($response['body']);
			/*check the response is valid or not*/
			if (!$responde_encode->success)
			{
				echo '1';
				exit;
			}
		}
		/* END OF CODE - CHECK CAPTCHA */	
		$yourname = $_REQUEST['full_name'];
		$youremail = $_REQUEST['your_iemail'];
		$contact_num = $_REQUEST['contact_number'];
		$frnd_subject = $_REQUEST['inq_subject'];
		$frnd_comments = $_REQUEST['inq_msg'];
		$post_id = $_REQUEST['listing_id'];	
		$to_email = (get_post_meta($post->ID,'email',true)!="")? get_post_meta($post->ID,'email',true): get_the_author_meta( 'user_email', $post->post_author )  ;
		$userdata = get_userdata($post->post_author);
		$to_name = $userdata->data->display_name;
		if($post_id != "")
		{
			$productinfosql = "select ID,post_title from $wpdb->posts where ID ='".$post_id."'";
			$productinfo = $wpdb->get_results($productinfosql);
			foreach($productinfo as $productinfoObj)
			{
				$post_title = stripslashes($productinfoObj->post_title); 
			}
		}
		/*Inquiry EMAIL START*/
		global $General;
		global $upload_folder_path;
		$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
		$tmpdata = get_option('templatic_settings');	;
		$email_subject = stripslashes($tmpdata['send_inquirey_email_sub']);
		$email_content = stripslashes($tmpdata['send_inquirey_email_description']);	
		
		
		if($email_content == "" && $email_subject=="")
		{
			$message1 =  __('[SUBJECT-STR]You might be interested in [SUBJECT-END]
			<p>Dear [#to_name#],</p>
			<p>[#frnd_comments#]</p>
			<p>Link : <b>[#post_title#]</b> </p>
			<p>Contact number : [#contact#]</p>
			<p>From, [#your_name#]</p>
			<p>Sent from -[#$post_url_link#]</p></p>','templatic');
			$filecontent_arr1 = explode('[SUBJECT-STR]',$message1);
			$filecontent_arr2 = explode('[SUBJECT-END]',$filecontent_arr1[1]);
			$subject = $filecontent_arr2[0];
			if($subject == '')
			{
				$subject = $frnd_subject;
			}
			$client_message = $filecontent_arr2[1];
		} else {
			$client_message = $email_content;
		}
		$subject = stripslashes($frnd_subject);
	
		$post_url_link = '<a href="'.$_REQUEST['link_url'].'">'.$post_title.'</a>';
		/*customer email*/
		$yourname_link = __('<b><a href="'.get_option('siteurl').'">'.get_option('blogname').'</a></b>.','templatic');
		$search_array = array('[#to_name#]','[#frnd_subject#]','[#post_title#]','[#frnd_comments#]','[#your_name#]','[#$post_url_link#]','[#contact#]');
		$replace_array = array($to_name,$frnd_subject,$post_url_link,$frnd_comments,$yourname,$yourname_link,$contact_num);
		$client_message = str_replace($search_array,$replace_array,$client_message,$contact_num); 
		
		templ_send_email($youremail,$yourname,$to_email,$to_name,$subject,stripslashes($client_message),$extra='');/*/To clidne email*/
		/*Inquiry EMAIL END*/
		$post = "";
		if(get_option('siteurl').'/' == $_REQUEST['request_uri']){
				_e('Email sent successfully','templatic');
				exit;
		} else {
				_e('Email sent successfully','templatic');
				exit;
		}
		
	}
}
/*
 * send to friend email function 
 */
add_action('wp_ajax_tevolution_send_friendto_form','tevolution_send_friendto_form');
add_action('wp_ajax_nopriv_tevolution_send_friendto_form','tevolution_send_friendto_form');
function tevolution_send_friendto_form(){
	
	global $wpdb,$General,$upload_folder_path,$post;
	$postdata = array();
	if( @$_REQUEST['post_id']!="" ){
		$postdata = get_post($_REQUEST['post_id']);
	}
	if( @$_REQUEST['yourname'] )
	{
		/* CODE TO CHECK WP-RECAPTCHA */
		$tmpdata = get_option('templatic_settings');
		$display = $tmpdata['user_verification_page'];
		if(!empty($display) &&  in_array('emaitofrd',$display))
		{
			/*fetch captcha private key*/
			$privatekey = $tmpdata['secret'];
			/*get the response from captcha that the entered captcha is valid or not*/
			$response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=".$privatekey."&response=".$_REQUEST["g-recaptcha-response"]."&remoteip=".getenv("REMOTE_ADDR"));
			/*decode the captcha response*/
			$responde_encode = json_decode($response['body']);
			/*check the response is valid or not*/
			if (!$responde_encode->success)
			{
				echo '1';
				exit;					
			}				
		}

		
		/* END OF CODE - CHECK WP-RECAPTCHA */	
		$yourname = $_REQUEST['yourname'];
		$youremail = $_REQUEST['youremail'];
		$frnd_subject = $_REQUEST['frnd_subject'];
		$frnd_comments = $_REQUEST['frnd_comments'];
		$to_friend_email = $_REQUEST['to_friend_email'];
		$to_name = $_REQUEST['to_name_friend'];
		/*Inquiry EMAIL START*/
		global $General,$wpdb;
		global $upload_folder_path;
		$post_title = stripslashes($postdata->post_title);
		$tmpdata = get_option('templatic_settings');	;
		$email_subject =$tmpdata['mail_friend_sub'];
		$email_content =$tmpdata['mail_friend_description'];
		
		
		if($email_content == "" && $email_subject=="")
		{
			$message1 =  __('[SUBJECT-STR]You might be interested in [SUBJECT-END]
			<p>Dear [#to_name#],</p>
			<p>[#frnd_comments#]</p>
			<p>Link : <b>[#post_title#]</b> </p>
			<p>From, [#your_name#]</p>','templatic');
			$filecontent_arr1 = explode('[SUBJECT-STR]',$message1);
			$filecontent_arr2 = explode('[SUBJECT-END]',$filecontent_arr1[1]);
			$subject = $filecontent_arr2[0];
			if($subject == '')
			{
				$subject = $frnd_subject;
			}
			$client_message = $filecontent_arr2[1];
		}else
		{
			$client_message = $email_content;
		}
		$subject = $frnd_subject;
		$post_url_link = '<a href="'.$_REQUEST['link_url'].'">'.$post_title.'</a>';
		/*customer email*/
		
		$search_array = array('[#to_name#]','[#post_title#]','[#frnd_comments#]','[#your_name#]','[#post_url_link#]');
		$replace_array = array($to_name,$post_url_link,nl2br($frnd_comments),$yourname,$post_url_link);
		$client_message = str_replace($search_array,$replace_array,$client_message);	
		templ_send_email($youremail,$yourname,$to_friend_email,$to_name,$subject,stripslashes($client_message),$extra='');/*/To clidne email*/
		
		/*Inquiry EMAIL END*/
		_e('Email sent successfully','templatic');
		exit;
	}
		
}

/*
* return the plugin directory path
*/
if(!function_exists('get_tmpl_plugin_directory')){
function get_tmpl_plugin_directory() {
	 return WP_CONTENT_DIR."/plugins/";
}
}

/*
* Add add to favourite html for detail page 
*/
if(!function_exists('tmpl_detailpage_favourite_html')){
function tmpl_detailpage_favourite_html($user_id,$post)
{
	global $current_user,$post;
	$add_to_favorite = __('Add to favorites','templatic');
	$added = __('Added','templatic');
	if(function_exists('icl_register_string')){
		icl_register_string('templatic','directory'.$add_to_favorite,$add_to_favorite);
		$add_to_favorite = icl_t('templatic','directory'.$add_to_favorite,$add_to_favorite);
		icl_register_string('templatic','directory'.$added,$added);
		$added = icl_t('templatic','directory'.$added,$added);
	}
	$post_id = $post->ID;
	
	$user_meta_data = get_user_meta($current_user->ID,'user_favourite_post',true);
	if($post->post_type !='post'){
		if($user_meta_data && in_array($post_id,$user_meta_data))
		{
			
			?>
			<li id="tmplfavorite_<?php echo $post_id;?>" class="fav_<?php echo $post_id;?> fav"  >
				<?php do_action('tmpl_before_rfav'); ?>
                
				<a href="javascript:void(0);" class="removefromfav" data-id='<?php echo $post_id; ?>'  onclick="javascript:addToFavourite('<?php echo $post_id;?>','remove');"><i class="fa fa-heart"></i><?php echo $added;?>
				</a>
				<?php do_action('tmpl_after_rfav'); ?>
			</li>    
			<?php
		}else{
			if($current_user->ID ==''){
				$data_reveal_id ='data-reveal-id="tmpl_reg_login_container"';
			}else{
				$data_reveal_id ='';
			}
		?>
		<li id="tmplfavorite_<?php echo $post_id;?>" class="fav_<?php echo $post_id;?> fav">
			<?php do_action('tmpl_before_addfav'); ?>
			<a href="javascript:void(0);" <?php echo $data_reveal_id; ?> class="addtofav" data-id='<?php echo $post_id; ?>'   onclick="javascript:addToFavourite('<?php echo $post_id;?>','add');"><i class="fa fa-heart-o"></i><?php echo $add_to_favorite;?></a>
			<?php do_action('tmpl_before_addfav'); ?>
		</li>
		<?php } 
	}
}
}

/*
* check whether file is writable or not.
*/
function is_writeable_file($path) {

	/* PHP's is_writable does not work with Win32 NTFS */
	/* recursively return a temporary file path */
	if ($path{strlen($path)-1}=='/') 
		return is_writeable_file($path.uniqid(mt_rand()).'.tmp');
	else if (is_dir($path))
		return is_writeable_file($path.'/'.uniqid(mt_rand()).'.tmp');
	/* check tmp file for read/write capabilities */
	$rm = file_exists($path);
	$f = @fopen($path, 'a');
	if ($f===false)
		return false;
	fclose($f);
	if (!$rm)
		unlink($path);
	return true;
}
                                                                                                    
add_action('tevolution_subcategory','tevolution_subcategory'); /* show post subcategories on category pages*/
/*
 *  display the sub categories in tevolution created post types
 */
 
if(!function_exists('tevolution_subcategory')){
	function tevolution_subcategory(){
		global $wpdb,$wp_query;	
		$current_term = $wp_query->get_queried_object();	
		
		$term_id = $wp_query->get_queried_object_id();
		$taxonomy_name = $current_term ->taxonomy;	
		do_action('tevolution_category_query');
		$featured_catlist_list =  wp_list_categories('title_li=&child_of=' . $term_id .'&echo=0&taxonomy='.$taxonomy_name.'&show_count=0&hide_empty=1&pad_counts=0&show_option_none=&orderby=name&order=ASC');
		if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
		{
			remove_filter( 'terms_clauses','locationwise_change_category_query',10,3 );	
		}
		if(!strstr(@$featured_catlist_list,'No categories'))
		{
			echo '<div id="tev_sub_categories">';
			echo '<ul>';
			echo $featured_catlist_list;
			echo '</ul>';
			echo '</div>';
		}
	}
}


/*
* search filters for all type of searches LIKE search by address OR near by search OR advance search
*/
add_action('init','tmpl_search_filters');
function tmpl_search_filters(){
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-generalization/search_filters.php')){
		include(TEMPL_MONETIZE_FOLDER_PATH.'templatic-generalization/search_filters.php');
	}
}


/*
* add alternative script to default wordpress theme
*/

if(!strstr($_SERVER['REQUEST_URI'],'/wp-admin/') || strstr($_SERVER['REQUEST_URI'],'/admin-ajax.php')){
	add_action('init','add_alternative_files');
}
function add_alternative_files()
{
	if(!function_exists('tmpl_theme_css_scripts'))
	{
		wp_enqueue_script( 'tmpl-slider-js', trailingslashit ( TEMPL_PLUGIN_URL ) . 'js/jquery.flexslider.js', array( 'jquery' ), '20120606', true );
	}
}
/*
* fetch all the users for back end drop down list.
*/
add_filter('wp_dropdown_users', 'tmpl_theme_post_author_override',99);
function tmpl_theme_post_author_override($output) { 
	global $post; /* return if this isn't the theme author override dropdown */
	if (!preg_match('/post_author_override/', $output)) return $output; /* return if we've already replaced the list (end recursion) */
	if (preg_match ('/post_author_override_replaced/', $output)) return $output; /* replacement call to wp_dropdown_users*/
	$output = wp_dropdown_users(array( 'echo' => 0, 'name' => 'post_author_override_replaced', 'selected' => empty($post->ID) ? $user_ID : $post->post_author, 'include_selected' => true )); /* put the original name back */
	$output = preg_replace('/post_author_override_replaced/', 'post_author_override', $output); return $output;
}


/*
* Print option for display view for listing page.(list,grid)
*/
add_action('admin_init','tmpl_default_view_settings');

function tmpl_default_view_settings(){
	/* DOING_AJAX is define then return false for admin ajax*/
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {		
		return ;	
	}
	/* Show default view option only if theme suppoted different views for theme */	
	if(current_theme_supports('tmpl_show_pageviews')){
		add_action('before_listing_page_setting','directory_before_listing_page_setting_callback');
		if(!function_exists('directory_before_listing_page_setting_callback')){ 
			function directory_before_listing_page_setting_callback(){ 
				$get_plug_data = get_option('templatic_settings');
				$googlemap_setting=get_option('city_googlemap_setting');
			?>
			<tr>
			  <th><label>
				  <?php echo __('Default page view','templatic-admin'); ?>
				</label></th>
			  <td><label for="default_page_view1">
				  <input type="radio" id="default_page_view1" name="default_page_view" value="gridview" <?php if( @$get_plug_data['default_page_view']=='gridview') echo "checked=checked";?> />
				  <?php echo __('Grid','templatic-admin'); ?>
				</label>
				&nbsp;&nbsp;
				<label for="default_page_view2">
				  <input type="radio" id="default_page_view2" name="default_page_view" value="listview" <?php if( @$get_plug_data['default_page_view']== "" || $get_plug_data['default_page_view']=='listview') echo "checked=checked";?> />
				  <?php echo __('List','templatic-admin'); ?>
				</label>
				<?php do_action('tmpl_other_page_view_option'); ?>
			   </td>
			</tr>
			<?php
			}
		}
	}
	
}
/* 
* show home page display option with different post type.
*/
add_action('tmpl_start_general_settings','tmpl_start_generalsettings_options');
function tmpl_start_generalsettings_options(){ 
		do_action('tev_before_homepage_settings');
		$tmpdata =get_option('templatic_settings');
		/* show if current theme support - home page display with different post types OR not */
		if(current_theme_supports('theme_home_page') && get_option('show_on_front') =='posts'){
		?>
		<table class="tmpl-general-settings form-table" id="home_page_settings">
		<tr id="home_page_settings">
				<th colspan="2"><div class="tevo_sub_title"><?php echo __('Home page settings','templatic-admin'); ?></div>
				</th>
		</tr> 
		<tr>
		<th><label><?php echo __('Homepage displays','templatic-admin'); ?> </label></th>
			<td>
			<?php 
			$posttaxonomy = get_option("templatic_custom_post");
			if(!empty($posttaxonomy))
			{
				foreach($posttaxonomy as $key=>$_posttaxonomy):
					if($key == 'admanager')
						continue;
					?>
					<div class="element">
						<label for="home_listing_type_value_<?php echo $key; ?>"><input type="checkbox" name="home_listing_type_value[]" id="home_listing_type_value_<?php echo $key; ?>" value="<?php echo $key; ?>" <?php if(@$tmpdata['home_listing_type_value'] && in_array($key,$tmpdata['home_listing_type_value'])) { echo "checked=checked";  } ?>>&nbsp;<?php echo __($_posttaxonomy['label'],'templatic-admin'); ?></label>
					</div>
				<?php endforeach;  }
			else
			{
				$url = '<a target=\"_blank\" href='.admin_url("admin.php?page=custom_setup&ctab=custom_setup&action=add_taxonomy").'>';
				$url .= __('here','templatic-admin');
				$url .= '</a>'; 
				 echo __('Please create a custom post type from ','templatic-admin');
				 echo $url;
			}
			 do_action('templ_post_type_description');?>  <p class="description"><?php echo sprintf(__('For this option to work you must select set the "Front page displays" option within %s to "Your latest posts".','templatic-admin'),'<a href="'.admin_url().'options-reading.php" target= "_blank">WordPress reading settings</a>');?></p>           
			</td>
		</tr>	
		<?php 
			$ordervalue = @$tmpdata['tev_front_page_order'];
			if($ordervalue ==''){ $ordervalue ='ddesc'; }
		?>
		<tr>
			<th><label><?php echo __('Sorting options for home page','templatic-admin'); ?> </label></th>
			<td>
				<?php $orders = array('dasc'=>'Publish Date Ascending','ddesc'=>'Publish Date Descending','random'=>'Random','asc'=>'Title Ascending','desc'=>'Title Descending'); ?>
				<select name="tev_front_page_order" id="tev_front_page_order">
					<?php foreach($orders as $key=>$value){ ?>
							<option value="<?php echo $key; ?>" <?php if($key == @$ordervalue) { echo "selected=selected";  } ?> ><?php echo $value; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<p class="submit" style="clear: both;">
			
					<input type="submit" value="<?php _e('Save All Settings','templatic-admin'); ?>" class="button button-primary button-hero" name="Submit">
				</p>
			</td>
		</tr>
		<?php }
		
		do_action('tev_after_homepage_settings');	?>
		</table>
		<?php
}


/* 
* add or remove posts from favourite 
*/
add_action('wp_ajax_tmpl_add_to_favourites','tmpl_add_to_favourites');
add_action('wp_ajax_nopriv_tmpl_add_to_favourites','tmpl_add_to_favourites');

/* add or remove post to your favourites */
/* previously this code was in - Tevolution\tmplconnector\monetize\templatic-generalization\ajax_event.php */

function tmpl_add_to_favourites()
{
	define( 'DOING_AJAX', true );
	require(ABSPATH."wp-load.php");
	if(isset($_REQUEST['ptype']) &&$_REQUEST['ptype'] == 'favorite'){
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global  $sitepress;
			$sitepress->switch_lang($_REQUEST['language']);
		}
		/* add to favoirites */
		if(isset($_REQUEST['action1']) && $_REQUEST['action1']=='add')	{
			if(isset($_REQUEST['st_date']) && $_REQUEST['st_date'] != '' && $_REQUEST['st_date'] != 'undefined' )
			{
				if(isset($_REQUEST['language']) && $_REQUEST['language'] != '')
				{
					add_to_favorite($_REQUEST['pid'],$_REQUEST['language']);exit;
				}
				else
				{
					add_to_favorite($_REQUEST['pid']);exit;
				}
			}
			else
			{
				if(isset($_REQUEST['language']) && $_REQUEST['language'] != '')
				{
					add_to_favorite($_REQUEST['pid'],$_REQUEST['language']);exit;
				}
				else
				{
					add_to_favorite($_REQUEST['pid']);exit;
				}
			}
		}
		/*  remove from favoirites */
		else{
			if(isset($_REQUEST['st_date']) && $_REQUEST['st_date'] != '' && $_REQUEST['st_date'] != 'undefined')
			{
				if(isset($_REQUEST['language']) && $_REQUEST['language'] != '')
				{
					remove_from_favorite($_REQUEST['pid'],$_REQUEST['language']);exit;
				}
				else
				{
					remove_from_favorite($_REQUEST['pid']);exit;
				}
			}
			else
			{
				if(isset($_REQUEST['language']) && $_REQUEST['language'] != '')
				{
					remove_from_favorite($_REQUEST['pid'],$_REQUEST['language']);exit;
				}
				else
				{
					remove_from_favorite($_REQUEST['pid']);exit;
				}
			}
		}
	}
}


/*
* Display the images in mobile view - to get the different images when mobile view is loaded
*/
function tmpl_mobile_archive_image($image_size='thumbnail'){
	global $post,$wpdb,$wp_query;
	
	$post_id = get_the_ID();
	if(get_post_meta($post_id,'_event_id',true)){
		$post_id=get_post_meta($post_id,'_event_id',true);
	}
	
	$featured=get_post_meta($post_id,'featured_c',true);
	$featured=($featured=='c')?'featured_c':'';
	 if ( has_post_thumbnail()):
		echo '<div class="event_img">';
		do_action('inside_listing_image');
		echo '<a href="'.get_permalink().'">';
		if($featured){echo '<span class="featured_tag">'.__('Featured',EDOMAIN).'</span>';}
		the_post_thumbnail($image_size); 
		echo '</a></div>';
	else:
		if(function_exists('bdw_get_images_plugin'))
		{
			$post_img = bdw_get_images_plugin($post_id,$image_size);						
			$thumb_img = @$post_img[0]['file'];
			$attachment_id = @$post_img[0]['id'];
			$attach_data = get_post($attachment_id);
			$img_title = $attach_data->post_title;
			$img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
		}
		?>
		<div class="event_img"> 
			<?php do_action('inside_listing_image');?>
			<a href="<?php the_permalink();?>">
			<?php if($featured){echo '<span class="featured_tag">'.__('Featured',EDOMAIN).'</span>';}?>
			<?php if($thumb_img):?>
				<img itemprop="image" src="<?php echo $thumb_img; ?>"  alt="<?php echo $img_alt; ?>" title="<?php echo $img_title; ?>" />
			<?php else:?>    
				<img itemprop="image" src="http://placehold.it/60x60" alt=""  />
			<?php endif;?>
			</a>	
		</div>
   <?php endif;
}

/* Check is user submitted post in passed post type in argument */

function tmpl_get_user_post_inposttype($post_type){
	global $post,$wp_query,$wpdb,$curauth;
	$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')):'';
	$posts = new $wp_query('author='.$curauth->ID.'&post_type='.$post_type);
	if($posts->have_posts()){
		return true;
	}else{
		return false;
	}
}

/*
	return file extension
*/
function tev_findexts($path) 
{ 
 $pathinfo = pathinfo($path);
 $ext = $pathinfo['extension'];
 return $ext; 
} 
/* 
	Display Amount with symbol
*/
function display_amount_with_currency_plugin($amount,$currency = ''){
	$amt_display = '';
	
	if($amount != ""){
	
	/* get the options from backend to format the price*/
	$num_decimals    = absint( get_option( 'tmpl_price_num_decimals' ) );
	$currency        = isset( $args['currency'] ) ? $args['currency'] : '';
	$decimal_sep     = wp_specialchars_decode( stripslashes( get_option( 'tmpl_price_decimal_sep' ) ), ENT_QUOTES );
	$thousands_sep   = wp_specialchars_decode( stripslashes( get_option( 'tmpl_price_thousand_sep' ) ), ENT_QUOTES );

	$amount           = apply_filters( 'raw_tmpl_price', floatval( $amount ) );
	$amount           = apply_filters( 'formatted_tmpl_price', number_format( $amount, $num_decimals, $decimal_sep, $thousands_sep ), $amount, $num_decimals, $decimal_sep, $thousands_sep );

	if ( apply_filters( 'tmpl_price_trim_zeros', true ) && $num_decimals > 0 ) {
		/* $amount = tmpl_trim_zeros( $amount ); */
	}
	
	
	$currency = do_action('before_currency').get_option('currency_symbol').do_action('after_currency');
	$position = get_option('currency_pos');
		if($position == '1'){
		$amt_display = $currency.$amount;
	} else if($position == '2'){
		$amt_display = $currency.' '.$amount;
	} else if($position == '3'){
		$amt_display = $amount.$currency;
	} else {
		$amt_display = $amount.' '.$currency;
	}
	return $amt_display;
	}
}
/* 
	Resize the image
*/
function bdw_get_images_plugin($iPostID,$img_size='thumb',$no_images='') 
{
	if(is_admin() && isset($_REQUEST['author']) && $_REQUEST['author']!=''){
		remove_action('pre_get_posts','tevolution_author_post');
	}
   $arrImages = get_children('order=ASC&orderby=menu_order ID&post_type=attachment&post_mime_type=image&post_parent=' . @$iPostID );	
	$counter = 0;
	$return_arr = array();	
 
	if (has_post_thumbnail( $iPostID ) && is_tax()){
		
		$img_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $iPostID ), 'thumbnail' );
		$imgarr['id'] = get_post_thumbnail_id( $iPostID );;
		$imgarr['file'] = $img_arr[0];
		$return_arr[] = $imgarr;
		
	}else{
		if($arrImages) 
		{
			
		   foreach($arrImages as $key=>$val)
		   {		  
				$id = $val->ID;
				if($val->post_title!="")
				{
					if($img_size == 'thumb')


					{
						$img_arr = wp_get_attachment_image_src($id, 'thumbnail'); /* Get the thumbnail url for the attachment*/
						$imgarr['id'] = $id;
						$imgarr['file'] = $img_arr[0];
						$return_arr[] = $imgarr;
					}
					else
					{
						$img_arr = wp_get_attachment_image_src($id, $img_size); 
			
						$imgarr['id'] = $id;
						$imgarr['file'] = $img_arr[0];
						$return_arr[] = $imgarr;
					}
				}
				$counter++;
				if($no_images!='' && $counter==$no_images)
				{
					break;	
				}
				
		   }
		}
			
	}  return $return_arr;
}

/* Pagination start BOF
   Function that performs a Boxed Style Numbered Pagination (also called Page Navigation).
   Function is largely based on Version 2.4 of the WP-PageNavi plugin */
   
function pagenavi_plugin($before = '', $after = '') {
    global $wpdb, $wp_query;
	
    $pagenavi_options = array();
 
    $pagenavi_options['current_text'] = '%PAGE_NUMBER%';
    $pagenavi_options['page_text'] = '%PAGE_NUMBER%';
    $pagenavi_options['first_text'] = __('First Page','templatic');
    $pagenavi_options['last_text'] = __('Last Page','templatic');
    $pagenavi_options['next_text'] = apply_filters('text_pagi_next','<strong>'.__('NEXT','templatic').'</strong>');
    $pagenavi_options['prev_text'] = apply_filters('text_pagi_prev','<strong>'.__('PREV','templatic').'</strong>');
    $pagenavi_options['dotright_text'] = '...';
    $pagenavi_options['dotleft_text'] = '...';
    $pagenavi_options['num_pages'] = 5; /*continuous block of page numbers*/
    $pagenavi_options['always_show'] = 0;
    $pagenavi_options['num_larger_page_numbers'] = 0;
    $pagenavi_options['larger_page_numbers_multiple'] = 5;
 
    if (!is_single()) {
        $request = $wp_query->request;
        $posts_per_page = intval(get_query_var('posts_per_page'));
        $paged = intval(get_query_var('paged'));
        $numposts = $wp_query->found_posts;
        $max_page = $wp_query->max_num_pages;
 
        if(empty($paged) || $paged == 0) {
            $paged = 1;
        }
 
        $pages_to_show = intval($pagenavi_options['num_pages']);
        $larger_page_to_show = intval($pagenavi_options['num_larger_page_numbers']);
        $larger_page_multiple = intval($pagenavi_options['larger_page_numbers_multiple']);
        $pages_to_show_minus_1 = $pages_to_show - 1;
        $half_page_start = floor($pages_to_show_minus_1/2);
        $half_page_end = ceil($pages_to_show_minus_1/2);
        $start_page = $paged - $half_page_start;
 
        if($start_page <= 0) {
            $start_page = 1;
        }
 
        $end_page = $paged + $half_page_end;
        if(($end_page - $start_page) != $pages_to_show_minus_1) {
            $end_page = $start_page + $pages_to_show_minus_1;
        }
        if($end_page > $max_page) {
            $start_page = $max_page - $pages_to_show_minus_1;
            $end_page = $max_page;
        }
        if($start_page <= 0) {
            $start_page = 1;
        }
 
        $larger_per_page = $larger_page_to_show*$larger_page_multiple;
        /*templ_round_num() custom function - Rounds To The Nearest Value.*/
        $larger_start_page_start = (templ_round_num($start_page, 10) + $larger_page_multiple) - $larger_per_page;
        $larger_start_page_end = templ_round_num($start_page, 10) + $larger_page_multiple;
        $larger_end_page_start = templ_round_num($end_page, 10) + $larger_page_multiple;
        $larger_end_page_end = templ_round_num($end_page, 10) + ($larger_per_page);
 
        if($larger_start_page_end - $larger_page_multiple == $start_page) {
            $larger_start_page_start = $larger_start_page_start - $larger_page_multiple;
            $larger_start_page_end = $larger_start_page_end - $larger_page_multiple;
        }
        if($larger_start_page_start <= 0) {
            $larger_start_page_start = $larger_page_multiple;
        }
        if($larger_start_page_end > $max_page) {
            $larger_start_page_end = $max_page;
        }
        if($larger_end_page_end > $max_page) {
            $larger_end_page_end = $max_page;
        }
        if($max_page > 1 || intval($pagenavi_options['always_show']) == 1) {
             $pages_text = str_replace("%CURRENT_PAGE%", number_format_i18n($paged), @$pagenavi_options['pages_text']);
            $pages_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pages_text);
			previous_posts_link($pagenavi_options['prev_text']);
       
            if ($start_page >= 2 && $pages_to_show < $max_page) {
                $first_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['first_text']);
                echo '<a href="'.esc_url(get_pagenum_link()).'" class="first page-numbers" title="'.$first_page_text.'">'.$first_page_text.'</a>';
                if(!empty($pagenavi_options['dotleft_text'])) {
                    echo '<span class="expand page-numbers">'.$pagenavi_options['dotleft_text'].'</span>';
                }
            }
 
            if($larger_page_to_show > 0 && $larger_start_page_start > 0 && $larger_start_page_end <= $max_page) {
                for($i = $larger_start_page_start; $i < $larger_start_page_end; $i+=$larger_page_multiple) {
                    $page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
                    echo '<a href="'.esc_url(get_pagenum_link($i)).'" class="page-numbers" title="'.$page_text.'">'.$page_text.'</a>';
                }
            }
 
            for($i = $start_page; $i  <= $end_page; $i++) {
                if($i == $paged) {
                    $current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
                    echo '<a  class="current page-numbers">'.$current_page_text.'</a>';
                } else {
                    $page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
                    echo '<a href="'.esc_url(get_pagenum_link($i)).'" class="page-numbers" title="'.$page_text.'"><strong>'.$page_text.'</strong></a>';
                }
            }
 
            if ($end_page < $max_page) {
                if(!empty($pagenavi_options['dotright_text'])) {
                    echo '<span class="expand page-numbers">'.$pagenavi_options['dotright_text'].'</span>';
                }
                $last_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['last_text']);
                echo '<a class="page-numbers last" href="'.esc_url(get_pagenum_link($max_page)).'" title="'.$last_page_text.'">'.$last_page_text.'</a>';
            }
           
            if($larger_page_to_show > 0 && $larger_end_page_start < $max_page) {
                for($i = $larger_end_page_start; $i <= $larger_end_page_end; $i+=$larger_page_multiple) {
                    $page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
                    echo '<a href="'.esc_url(get_pagenum_link($i)).'" class="page-numbers" title="'.$page_text.'">'.$page_text.'</a>';
                }
            }
            echo $after;
			 next_posts_link($pagenavi_options['next_text'], $max_page);
        }
    }
}

/*add class attributes on next and previous link in pagination */
add_filter('next_posts_link_attributes', 'tevolution_posts_link_attributes');
add_filter('previous_posts_link_attributes', 'tevolution_posts_link_attributes');
function tevolution_posts_link_attributes() {
    return 'class="next page-numbers"';
}

function templ_round_num($num, $to_nearest) {
   /*Round fractions down (http://php.net/manual/en/function.floor.php)*/
   return floor($num/$to_nearest)*$to_nearest;
}

/* Returns user currently in admin area or in front end */
function is_templ_wp_admin()
{
	if(strstr($_SERVER['REQUEST_URI'],'/wp-admin/') && !isset($_REQUEST['front']))
	{
		return true;
	}
	return false;
}

/* 
 Return uploaded file is valid or not
*/
function is_valid_coupon_plugin($coupon)
{
	global $wpdb;
    $couponsql = $wpdb->get_var( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE post_title = %s AND post_type='coupon_code'", $coupon ));
	$couponinfo = $couponsql;
	if($couponinfo)
	{
		if($couponinfo == $coupon)
		{
			return true;
		}
	}
	return false;
}


/* 
	Return the total amount
*/
function get_payable_amount_with_coupon_plugin($total_amt,$coupon_code)
{
	$discount_amt = get_discount_amount_plugin($coupon_code,$total_amt);
	if($discount_amt>0)
	{
		return $total_amt-$discount_amt;
	}else
	{
		return $total_amt;
	}
}
/* 
	Return Amt by filtering
*/
function get_discount_amount_plugin($coupon,$amount)
{
	global $wpdb;
	if($coupon!='' && $amount>0)
	{
		$couponsql = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='coupon_code'", $coupon ));
		$couponinfo = $couponsql;
		$start_date = strtotime(get_post_meta($couponinfo,'startdate',true));
		$end_date 	= strtotime(get_post_meta($couponinfo,'enddate',true));
		$todays_date = strtotime(date("Y-m-d"));
		if ($start_date <= $todays_date && $end_date >= $todays_date)
		{
			if($couponinfo)
			{
				if(get_post_meta($couponinfo,'coupondisc',true)=='per')
				{
					$discount_amt = ($amount*get_post_meta($couponinfo,'couponamt',true))/100;
				}
				elseif(get_post_meta($couponinfo,'coupondisc',true)=='amt')
				{
					$discount_amt = get_post_meta($couponinfo,'couponamt',true);
				}
				return $discount_amt;
			}
		}
	}
	return '0';
}
/* 
	this function will fetch the default status of the posts set by the admin in backend general settings 
*/
function fetch_posts_default_status()
{
	$tmpdata = get_option('templatic_settings');
	$post_default_status = $tmpdata['post_default_status'];
	return $post_default_status;
}

/*
 * add action for add calender css and javascript file inside html head tag
 */ 
add_action ('wp_head', 'header_css_javascript');
add_action('admin_head','header_css_javascript',12);

function header_css_javascript()  {
	global $current_user, $wp_locale,$post,$pagenow;
	
	if(($pagenow!='plugins.php' && $pagenow!='themes.php') || $pagenow==''){
		$is_submit=get_post_meta( @$post->ID,'is_tevolution_submit_form',true);
		$register_page_id=get_option('tevolution_register');
		$login_page_id=get_option('tevolution_login');
		$profile_page_id=get_option('tevolution_profile');
		
		wp_enqueue_style('jQuery_datepicker_css',TEMPL_PLUGIN_URL.'css/datepicker/jquery.ui.all.min.css');	
		if(is_admin() || ($is_submit==1 || $register_page_id== @$post->ID || $login_page_id== @$post->ID || $profile_page_id== @$post->ID)){
			wp_enqueue_script('jquery-ui-datepicker');
			 /*localize our js*/
			$aryArgs = array(
				'monthNames'        => strip_array_indices( $wp_locale->month ),
				'monthNamesShort'   => strip_array_indices( $wp_locale->month_abbrev ),
				'monthStatus'       => __( 'Show a different month', 'templatic' ),
				'dayNames'          => strip_array_indices( $wp_locale->weekday ),
				'dayNamesShort'     => strip_array_indices( $wp_locale->weekday_abbrev ),
				'dayNamesMin'       => strip_array_indices( $wp_locale->weekday_initial ),
				/* is Right to left language? default is false*/
				'isRTL'             => @$wp_locale->is_rtl,
			);
		 
			/* Pass the array to the enqueued JS*/
			wp_localize_script( 'jquery-ui-datepicker', 'objectL11tmpl', $aryArgs );		
		}
	}	
		/* icl lang nav css function call for custom page */
		$request_page=apply_filters('tmpl_requets_page_icl_lang',array('preview','success','payment','paypal_pro_success','authorizedotnet_success','googlecheckout_success','worldpay_success','eway_success','ebay_success','ebs_success','psigate_success','2co_success','stripe_success','braintree_success','inspire_commerce_success','recurring','paypal_express_checkout'));
		if((isset($_REQUEST['page']) && ( !empty($request_page) && in_array(@$_REQUEST['page'],$request_page) ) || isset($_REQUEST['ptype'])) && is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
		{
			icl_lang_sel_nav_css($show = true);
		}	
	
}
function tevolution_transaction_mail_fn()
{
	if(isset($_REQUEST['submit']) && $_REQUEST['submit'] !='')
	{
		$tmpdata = get_option('templatic_settings');
		$orderId = $_REQUEST['trans_id'];
		global $wpdb,$transection_db_table_name;
		$transection_db_table_name = $wpdb->prefix . "transactions";
		
		$ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
		$orderinfo = $wpdb->get_row($ordersql);
	
		$pid = $orderinfo->post_id;
		/* save post data while upgrade post from transaction listing */
		if(get_post_meta($pid,'upgrade_request',true) == 1 && (isset($_REQUEST['ostatus']) && $_REQUEST['ostatus'] == 1))
		{
			do_action('tranaction_upgrade_post',$pid,$orderId); /* add an action to save upgrade post data. */
		}
		
		if($orderinfo->payment_method != '' && $orderinfo->payment_method != '-')
			$payment_type = $orderinfo->payment_method;
		else
			$payment_type = __('Free','templatic');
					
		$payment_date =  date_i18n(get_option('date_format'),strtotime($orderinfo->payment_date));
		if(isset($_REQUEST['ostatus']) && @$_REQUEST['ostatus']!='')
			$trans_status = $wpdb->query("update $transection_db_table_name SET status = '".$_REQUEST['ostatus']."' where trans_id = '".$orderId."'");
		$user_detail = get_userdata($orderinfo->user_id); /* get user details */
		$user_email = $user_detail->user_email;
		$user_login = $user_detail->display_name;
		$my_post['ID'] = $pid;
		if(isset($_REQUEST['ostatus']) && $_REQUEST['ostatus']== 1)
			$status = 'publish';
		else
			$status = 'draft';
		$my_post['post_status'] = $status;
		wp_update_post( $my_post );
		
		if(isset($_REQUEST['ostatus']) && $_REQUEST['ostatus']== 1)
		{
			$payment_status = APPROVED_TEXT;
			if($orderinfo->payforfeatured_h == 1  && $orderinfo->payforfeatured_c == 1){
				update_post_meta($pid, 'featured_c', 'c');
				update_post_meta($pid, 'featured_h', 'h');
				update_post_meta($pid, 'featured_type', 'both');			
			}elseif($orderinfo->payforfeatured_c == 1){
				update_post_meta($pid, 'featured_c', 'c');
				update_post_meta($pid, 'featured_type', 'c');
			}elseif($orderinfo->payforfeatured_h == 1){
				update_post_meta($pid, 'featured_h', 'h');
				update_post_meta($pid, 'featured_type', 'h');
			}else{
				update_post_meta($pid, 'featured_type', 'none');	
			}
		}
		elseif(isset($_REQUEST['ostatus']) && $_REQUEST['ostatus']== 2)
		{
			$payment_status = ORDER_CANCEL_TEXT;
			if($orderinfo->payforfeatured_h == 0  && $orderinfo->payforfeatured_c == 0){
				update_post_meta($pid, 'featured_c', '');
				update_post_meta($pid, 'featured_h', '');
				update_post_meta($pid, 'featured_type', 'none');			
			}elseif($orderinfo->payforfeatured_c == 0){
				update_post_meta($pid, 'featured_c', '');
				update_post_meta($pid, 'featured_type', 'none');
			}elseif($orderinfo->payforfeatured_h == 0){
				update_post_meta($pid, 'featured_h', '');
				update_post_meta($pid, 'featured_type', 'none');
			}else{
				update_post_meta($pid, 'featured_type', 'none');	
			}
		}
		elseif(isset($_REQUEST['ostatus']) && $_REQUEST['ostatus']== 0)
		{
			$payment_status = PENDING_MONI;
			if($orderinfo->payforfeatured_h == 0  && $orderinfo->payforfeatured_c == 0){
				update_post_meta($pid, 'featured_c', '');
				update_post_meta($pid, 'featured_h', '');
				update_post_meta($pid, 'featured_type', 'none');			
			}elseif($orderinfo->payforfeatured_c == 0){
				update_post_meta($pid, 'featured_c', '');
				update_post_meta($pid, 'featured_type', 'none');
			}elseif($orderinfo->payforfeatured_h == 0){
				update_post_meta($pid, 'featured_h', '');
				update_post_meta($pid, 'featured_type', 'none');
			}else{
				update_post_meta($pid, 'featured_type', 'none');	
			}
		}
		$to = get_site_emailId_plugin();
		$productinfosql = "select ID,post_title,guid,post_author from $wpdb->posts where ID = $pid";
		$package_id = $orderinfo->package_id;
		$package_name = get_post($package_id);
		$productinfo = get_post($pid);
	    $post_name = $productinfo->post_title;
	    $post_type_mail = $productinfo->post_type;
		$transaction_details="";
		$transaction_details .= "-------------------------------------------------- <br/>\r\n";
			$transaction_details .= __('Payment Details for Listing','templatic').": $post_name <br/>\r\n";
			$transaction_details .= "-------------------------------------------------- <br/>\r\n";
			$transaction_details .= __('Package Name','templatic').": ".$package_name->post_title."<br/>\r\n";
			$transaction_details .= __('Status','templatic').": ".$payment_status."<br/>\r\n";
			$transaction_details .= __('Type','templatic').": $payment_type <br/>\r\n";
			$transaction_details .= __('Date','templatic').": $payment_date <br/>\r\n";
			$transaction_details .= "-------------------------------------------------- <br/>\r\n";
			$transaction_details = $transaction_details;
			if((isset($_REQUEST['ostatus']) && $_REQUEST['ostatus'] == 1 ))
			{
				$subject = $tmpdata['payment_success_email_subject_to_admin'];
				if(!$subject)
				{
					$subject = __("You have received a payment",'templatic');
				}
				$content = $tmpdata['payment_success_email_content_to_admin'];
				if(!$content){
					$content = __("<p>Howdy [#to_name#],</p><p>A post has been approved of [#payable_amt#] on [#site_name#].",'templatic').' '.__('Details are available below','templatic').'</p><p>[#transaction_details#]</p><p>'.__('Thanks,','templatic').'<br/>[#site_name#]</p>';
				}
			}
			if((isset($_REQUEST['ostatus']) && $_REQUEST['ostatus'] == 0 ))
			{
				$subject = $tmpdata['pending_listing_notification_subject'];
				if(!$subject)
				{
					$subject = __("Listing payment not confirmed",'templatic');
				}
				$content = $tmpdata['pending_listing_notification_content'];
				if(!$content)
				{
					$content = __("<p>Hi [#to_name#],<br />A listing request on the below details has been rejected.<p>[#transaction_details#]</p>Please try again later.<br />Thanks you.<br />[#site_name#]</p>",'templatic');
				}
			}
			
			$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
			$fromEmail = get_option('admin_email');
			$fromEmailName = stripslashes(get_option('blogname'));	

			$search_array = array('[#to_name#]','[#payable_amt#]','[#transaction_details#]','[#site_name#]');
			$replace_array = array($fromEmailName,display_amount_with_currency_plugin($orderinfo->payable_amt),$transaction_details,$store_name);
			$filecontent = str_replace($search_array,$replace_array,$content);
			if((isset($_REQUEST['ostatus']) && ( $_REQUEST['ostatus'] != 3 || $_REQUEST['ostatus'] != 2 )))
			{
				@templ_send_email($fromEmail,$fromEmailName,$to,$user_login,$subject,stripslashes($filecontent),''); /* email to admin*/
			}
			/* post details*/
				$post_link = get_permalink($pid);
				$post_title = '<a href="'.$post_link.'">'.stripslashes($productinfo->post_title).'</a>'; 
				$aid = $productinfo->post_author;
				$userInfo = get_userdata($aid);
				$to_name = $userInfo->user_nicename;
				$to_email = $userInfo->user_email;
				$user_email = $userInfo->user_email;
			
			$transaction_details ="";
			$transaction_details .= __('Information Submitted URL','templatic')." <br/>\r\n";
			$transaction_details .= "-------------------------------------------------- <br/>\r\n";
			$transaction_details .= "  $post_title <br/>\r\n";
			$transaction_details = $transaction_details;
			if((isset($_REQUEST['ostatus']) && $_REQUEST['ostatus'] == 1 ))
			{
				$subject = $tmpdata['payment_success_email_subject_to_client'];
				if(!$subject)
				{
					$subject = __("Thank you for your submission!",'templatic');
				}
				$content = $tmpdata['payment_success_email_content_to_client'];
				if(!$content)
				{
					$content = __("<p>Hello [#to_name#],</p><p>Your submission has been approved! You can see the listing here:</p><p>[#transaction_details#]</p><p>If you'll have any questions about this please send an email to [#admin_email#]</p><p>Thanks!,<br/>[#site_name#]</p>",'templatic');
				}
			}
			if((isset($_REQUEST['ostatus']) && $_REQUEST['ostatus'] == 0 ))
			{
				$subject = $tmpdata['pending_listing_notification_subject'];
				if(!$subject)
				{
					$subject = __("Listing payment not confirmed",'templatic');
				}
				$content = $tmpdata['pending_listing_notification_content'];
				if(!$content)
				{
					$content = __("<p>Hi [#to_name#],<br />A listing request on the below details has been rejected.<p>[#transaction_details#]</p>Please try again later.<br />Thanks you.<br />[#site_name#]</p>",'templatic');
				}
			}
			if((isset($_REQUEST['ostatus']) && $_REQUEST['ostatus'] == 2 ))
			{
				$subject = $tmpdata['payment_cancelled_subject'];
				if(!$subject)
				{
					$subject = __("Payment Cancelled",'templatic');
				}
				$content = $tmpdata['payment_cancelled_content'];
				if(!$content)
				{
					$content = __("<p>[#post_type#] has been cancelled with transaction id [#transection_id#]</p>",'templatic');
				}
			}
			$store_name = get_option('blogname');
			$search_array = array('[#to_name#]','[#transaction_details#]','[#site_name#]','[#admin_email#]','[#transection_id#]','[#post_type#]');
			$replace_array = array($to_name,$transaction_details,$store_name,get_option('admin_email'),$_REQUEST['trans_id'],ucfirst(get_post_type($pid)));
			$content = str_replace($search_array,$replace_array,$content);
			
			if((isset($_REQUEST['ostatus']) && ( $_REQUEST['ostatus'] != 3  )))
			{
				templ_send_email($fromEmail,$fromEmailName,$user_email,$user_login,$subject,stripslashes($content),$extra='');
			}
			/*transaction delete code*/
			if((isset($_REQUEST['ostatus']) && ( $_REQUEST['ostatus'] == 3  )))
			{
				
				global $wpdb,$transection_db_table_name;
				$transection_db_table_name = $wpdb->prefix . "transactions";
				$orderId = $_REQUEST['trans_id'];
				$ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
				$orderinfo = $wpdb->get_row($ordersql);
				$pid = $orderinfo->post_id;
				$package_id = $orderinfo->package_id;
				$users_packageperlist=$wpdb->prefix.'users_packageperlist';
				$cur_user_id = $orderinfo->user_id;
				$package_type = get_post_meta($package_id,'package_type',true);
				$sql=$wpdb->get_row("SELECT * FROM $users_packageperlist WHERE user_id=".$cur_user_id." AND package_id=".$package_id." AND status=1 AND post_id = 0");
				$subscriber_id = $sql->subscriber_id;
				$package_type = get_post_meta($sql->package_id,'package_type',true);
				if($package_type == 2){
					$subscribe_post = $wpdb->get_results("SELECT * FROM $users_packageperlist WHERE user_id=".$cur_user_id." AND package_id=".$package_id." AND status=1 AND subscriber_id LIKE '".$subscriber_id."'");
					foreach($subscribe_post as $key=>$subscribe_post_object)
					{
						/* Update post*/
						$my_post = array();
						$my_post['ID'] = $subscribe_post_object->post_id;
						$my_post['post_status'] = 'draft';
						
						/* Update the post into the database*/
						wp_update_post( $my_post );
					}
				}
				$wpdb->query("delete from $transection_db_table_name where trans_id=\"$orderId\"");
				wp_redirect(admin_url('admin.php?page=transcation'));
				exit;
			}
	}
}

/* 
* include script for back nad front end for media upload
*/
add_action('admin_enqueue_scripts', 'tmpl_tevolutions_scripts');
add_action('wp_enqueue_scripts', 'tmpl_tevolutions_scripts');
function tmpl_tevolutions_scripts() {
	global $pagenow,$post;
	$register_page_id=get_option('tevolution_register');
	$profile_page_id=get_option('tevolution_profile');
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') && function_exists('icl_object_id')){
		$profile_page_id = icl_object_id( $profile_page_id, 'page', false, ICL_LANGUAGE_CODE );
		$register_page_id = icl_object_id( $register_page_id, 'page', false, ICL_LANGUAGE_CODE );
	}
	if ((isset($_GET['page']) && $_GET['page'] == 'location_settings' ) || @$pagenow == 'edit-tags.php' || @$pagenow == 'post.php' || @$pagenow == 'profile.php' || @$pagenow == 'post-new.php' || @get_post_meta($post->ID,'is_tevolution_submit_form',true) == 1 || @$post->ID == @$profile_page_id || @$post->ID == @$register_page_id || $pagenow == 'user-edit.php' || (isset($_GET['upgpkg']) && $_GET['upgpkg'] == 1 ) || (isset($_GET['action']) && $_GET['action'] == 'add_taxonomy' )  || (isset($_GET['action']) && $_GET['action'] == 'edit-type' )  ) {
        wp_enqueue_media();
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script( 'jquery-ui-sortable' );
        wp_register_script('media_upload_scripts', TEVOLUTION_PAGE_TEMPLATES_URL.'js/media_upload_scripts.js', array('jquery'));
        wp_register_script('drag_drop_media_upload_scripts', TEVOLUTION_PAGE_TEMPLATES_URL.'js/jquery.uploadfile.js', array('jquery'),false);
		wp_enqueue_script('drag_drop_media_upload_scripts');
		/*added js for hide show accordion on submit form*/
		if(!is_admin())
			wp_register_script('submission_post_form_tab_script', TEVOLUTION_PAGE_TEMPLATES_URL.'js/post_submit.js','','',true);
		wp_enqueue_script('submission_post_form_tab_script');
        wp_enqueue_script('media_upload_scripts');
    }
	if(tmpl_wp_is_mobile() && is_admin() && strstr($_SERVER['REQUEST_URI'],'/wp-admin/')){
		 wp_enqueue_script('mobile-script', TEVOLUTION_PAGE_TEMPLATES_URL.'js/tevolution-mobile-script.js', array('jquery'));
	}
}


/* get categories of selected post type from add custom fields */
add_action('wp_ajax_tmpl_ajax_custom_taxonomy','tmpl_ajax_custom_taxonomy');
add_action('wp_ajax_nopriv_tmpl_ajax_custom_taxonomy','tmpl_ajax_custom_taxonomy');

/* get categories of selected post type from add custom fields when ajax request. Previously this code wsa in - Tevolution\tmplconnector\monetize\templatic-custom_fields\ajax_custom_taxonomy.php */
function tmpl_ajax_custom_taxonomy()
{
	?>
	<ul class="categorychecklist form_cat">
	<li>
		<input type="checkbox" name="selectall" id="selectall" class="checkbox" onclick="displaychk_frm();" />
		<label for="selectall">&nbsp;<?php _e('Select All','templatic'); ?></label>
	</li>
	<?php
		$scats = $_REQUEST['scats'];
		$pid = explode(',',$scats);
		if($_REQUEST['post_type'] == 'all' || $_REQUEST['post_type'] == 'all,')
		{
			$custom_post_types_args = array();
			$custom_post_types = get_option("templatic_custom_post");
			tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>'category','popular_cats' => true,'selected_cats'=>$pid ) );
			foreach ($custom_post_types as $content_type=>$content_type_label) {
				$taxonomy = $content_type_label['slugs'][0];
				
				echo "<li><label style='font-weight:bold;'>".$content_type_label['taxonomies'][0]."</label></li>";
				if($taxonomy!='')
				tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>$taxonomy,'popular_cats' => true,'selected_cats'=>$pid ) );
			}
		}
		else
		{
			$my_post_type = explode(",",substr($_REQUEST['post_type'],0,-1));
			/*get_wp_category_checklist_plugin('category','');*/
			foreach($my_post_type as $_my_post_type)
			{
				if($_my_post_type!='all'){
					$taxonomy = get_taxonomy( $_my_post_type );
					echo "<li><label style='font-weight:bold;'>".$taxonomy->labels->name."</label></li>";
					if($_my_post_type!='')
						tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>$_my_post_type,'popular_cats' => true,'selected_cats'=>$pid ) );
				}
			}
		}
	?>
	</ul>
	<?php
	exit;
}


/* Fetch heading type custom fields as per post type wise */
function fetch_heading_per_post_type($post_type)
{
	global $wpdb,$post;
	remove_all_actions('posts_where');
	$heading_title = array();
	$args=array('post_type'      => 'custom_fields',
				'posts_per_page' => -1	,
				'post_status'    => array('publish'),
				'meta_query'     => array('relation' => 'AND',
										array('key' => 'ctype','value' => 'heading_type','compare' => '=','type'=> 'text'),
										array('key' => 'post_type','value' => $post_type,'compare' => 'LIKE','type'=> 'text')			
									),
				'meta_key'       => $post_type.'_sort_order',	
				'orderby'        => 'meta_value_num',
				'meta_value_num' => $post_type.'_sort_order',
				'order'          => 'ASC'
		);
	
	$post_meta_info = null;
	remove_all_actions('posts_orderby');
	add_filter('posts_join', 'custom_field_posts_where_filter');
	
	$post_meta_info = new WP_Query($args);	
	remove_filter('posts_join', 'custom_field_posts_where_filter');	

	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
		/*Fetch custom fields heading type wise */
		
		if(isset($_REQUETS['page']) && $_REQUEST['page'] =='custom_fields'){
		
			/* to display all heading types */
			
			$otherargs=array('post_type' => 'custom_fields',
						 'posts_per_page' => -1	,
   		                 'post_status' => array('publish'),
						 'meta_query' => array('relation' => 'AND',
											array('key' => 'is_active','value' => '1','compare' => '=','type'=> 'text'),
											array('key' => $post_type.'_heading_type','value' => htmlspecialchars_decode($post->post_title),'compare' => '=','type'=> 'text'),
										)
						 );
		}else{
		
			/* to display custom heading types */
			
			$otherargs=array('post_type' => 'custom_fields',
						 'posts_per_page' => -1	,
   		                 'post_status' => array('publish'),
						 'meta_query' => array('relation' => 'AND',
											array('key' => 'is_active','value' => '1','compare' => '=','type'=> 'text'),
											array('key' => $post_type.'_heading_type','value' => htmlspecialchars_decode($post->post_title),'compare' => '=','type'=> 'text'),
											array('key' => 'is_submit_field', 'value' =>  '1','compare' => '='),
										)
						 );			
			if(is_admin() || (isset($_REQUEST['pid']) && $_REQUEST['pid']!='' && isset($_REQUEST['action']) && $_REQUEST['action']=='edit') || (isset($_REQUEST['action_edit']) && $_REQUEST['action_edit']=='edit')){
				/* Unset is submit field  on edit listing page for display all custom fields post type wise*/
				unset($otherargs['meta_query'][2]);	
			}	
		}
		
		$other_post_query = null;
		$other_post_query = new WP_Query($otherargs);		

		if(count($other_post_query->post) > 0 || (is_admin() && isset($_REQUEST['page']) && isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']!='' ))
		{
			$heading_title[$post->post_name] = $post->post_title;
		}
		endwhile;wp_reset_query();
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	
	return $heading_title;
}

/*
get the active headings
*/
function fetch_active_heading($head)
{
	global $wpdb,$post;
	$query = "SELECT $wpdb->posts.* FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id	AND $wpdb->postmeta.meta_key = 'is_active' AND $wpdb->postmeta.meta_value = '1'	AND $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'custom_fields' AND $wpdb->posts.post_title = '".$head."'"; 
	$querystr = $wpdb->get_row($query);
	if(count($querystr) == 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

/*
 return the custom fields - which selected as show on category page
 */
function tmpl_get_category_list_customfields($post_type){
	global $wpdb,$post,$posttitle;
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';	

	if(strpos($post_type,',') !== false){ /* get the multipal post type wise custom fields*/
		$post_types=explode(',',$post_type);
		foreach($post_types as $type){
			$meta_query[]=array('key'     => 'post_type_'.$type.'',
								'value'   => $type,
								'compare' => '=',
								'type'    => 'text'
					  );
        }
		
		$args = apply_filters('tmpl_archive_vars_args',array( 'post_type' => 'custom_fields',
			'posts_per_page' => -1	,
			'post_status' => array('publish'),
			'meta_query' => array('relation' => 'AND',										  
								  $meta_query,
								  array(
										'key'     => 'show_on_listing',
										'value'   =>  '1',
										'compare' => '='
									)
							),
							
							'meta_key' => 'sort_order',
							'orderby' => 'meta_value',					
							'order' => 'ASC'),$meta_query);

	}else{
	
		$args = apply_filters('tmpl_dir_category_vars_arg',array( 'post_type' => 'custom_fields',
				  'posts_per_page' => -1	,
				  'post_status' => array('publish'),
				  'meta_query' => array('relation' => 'AND',
														  array(
																	'key'     => 'post_type_'.$post_type.'',
																	'value'   => $post_type,
																	'compare' => '=',
																	'type'    => 'text'
														  ),		
														  array(
																	'key'     => 'is_active',
																	'value'   =>  '1',
																	'compare' => '='
														  ),
														  array(
																	'key'     => 'show_on_listing',
																	'value'   =>  '1',
																	'compare' => '='
														  )
												),
				  'meta_key' => 'sort_order',
				  'orderby' => 'meta_value',					
				  'order' => 'ASC'
			),$post_type);
	}
	
	remove_all_actions('posts_where');		
	remove_action('pre_get_posts','location_pre_get_posts',12);
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_filter('posts_where', 'event_manager_posts_where',11);
	remove_filter('pre_get_posts', 'event_home_page_feature_listing');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	remove_action('pre_get_posts', 'advance_search_template_function',11);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	/* Set the results in transient to get fast results */
	
	if (get_option('tevolution_cache_disable')==1 && false === ( $post_query = get_transient( '_tevolution_query_taxo'.trim($post_type).$cur_lang_code ) ) ) {	
		$post_query = new WP_Query($args);		
		set_transient( '_tevolution_query_taxo'.trim($post_type).$cur_lang_code, $post_query, 12 * HOUR_IN_SECONDS );		
	}elseif(get_option('tevolution_cache_disable')==''){
		$post_query = new WP_Query($args);		
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	
	$htmllistvar_name='';
	if($post_query->have_posts())
	{
		while ($post_query->have_posts()) : $post_query->the_post();
			$ctype = get_post_meta($post->ID,'ctype',true);
			$post_name=get_post_meta($post->ID,'htmlvar_name',true);
			$style_class=get_post_meta($post->ID,'style_class',true);
			$label=get_post_meta($post->ID,'admin_title',true);
			$option_title=get_post_meta($post->ID,'option_title',true);
			$option_values=get_post_meta($post->ID,'option_values',true);
			
			$htmllistvar_name[$post_name] = array( 'type'=>$ctype,
												'htmlvar_name'=> $post_name,
												'style_class'=> $style_class,
												'option_title'=> $option_title,
												'option_values'=> $option_values,
												'label'=> $post->post_title
											  );
			$posttitle[] = $post->post_title;
		endwhile;
		wp_reset_query();
	}	
	return $htmllistvar_name;
	
}
/* 
	This function will return the custom fields in "Instant search", We can use it any where were we want list of all custom fields.
*/
function templ_get_all_custom_fields($post_types,$category_id='',$taxonomy='') {
	global $wpdb,$post,$sitepress;
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
	remove_all_actions('posts_where');
	/* Fetch custom fields set is search form page */
	$args=array( 'post_type' => 'custom_fields',
				'posts_per_page' => -1	,
				'post_status' => array('publish'),
				'meta_query' => array('relation' => 'AND',
									array('key' => 'post_type_'.$post_types,'value' => array('all',$post_types),'compare' => 'In','type'=> 'text'),
									/*array('key' => 'is_search','value' =>  '1','compare' => '='),			*/
									array('key' => 'is_active','value' =>  '1','compare' => '=')
								),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value_num',
				'meta_value_num'=>'sort_order',
				'order' => 'ASC'
				);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$post_query = null;	
	
	
	$post_query = get_transient( '_tevolution_query_search'.trim($post_types).$cur_lang_code );
	if ( false === $post_query && get_option('tevolution_cache_disable')==1 ) {
		$post_query = new WP_Query($args);
		set_transient( '_tevolution_query_search'.trim($post_types).$cur_lang_code, $post_query, 12 * HOUR_IN_SECONDS );		
	}elseif(get_option('tevolution_cache_disable')==''){
		$post_query = new WP_Query($args);	
	}
	
	$post_meta_info = $post_query;	
	wp_reset_postdata();
	$return_arr = array();
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			if(get_post_meta($post->ID,"ctype",true)){
				$options = explode(',',get_post_meta($post->ID,"option_values",true));
			}
			
			if(get_post_meta($post->ID,"search_ctype",true)!=''){
				$search_type=get_post_meta($post->ID,"search_ctype",true);
			}else{
				$search_type=get_post_meta($post->ID,"ctype",true);
			}			
			
			$custom_fields = array(
					"name"		=> get_post_meta($post->ID,"htmlvar_name",true),
					"label" 	=> $post->post_title,
					"htmlvar_name" 	=> get_post_meta($post->ID,"htmlvar_name",true),
					"default" 	=> get_post_meta($post->ID,"default_value",true),
					"type" 		=> $search_type,
					"desc"      => $post->post_content,
					"option_values" => get_post_meta($post->ID,"option_values",true),
					"option_title" => explode(',',get_post_meta($post->ID,"option_title",true)),
					"is_require"  => get_post_meta($post->ID,"is_require",true),
					"is_active"  => get_post_meta($post->ID,"is_active",true),
					"show_on_listing"  => get_post_meta($post->ID,"show_on_listing",true),
					"show_on_detail"  => get_post_meta($post->ID,"show_on_detail",true),
					"validation_type"  => get_post_meta($post->ID,"validation_type",true),
					"style_class"  => get_post_meta($post->ID,"style_class",true),
					"extra_parameter"  => get_post_meta($post->ID,"extra_parameter",true),
					"range_min"  => get_post_meta($post->ID,"range_min",true),
					"range_max"  => get_post_meta($post->ID,"range_max",true),
					"search_ctype"  => get_post_meta($post->ID,"search_ctype",true),
					
					);
			
			if($search_type=='min_max_range_select'){
				$custom_fields["search_min_option_title"]=get_post_meta($post->ID,"search_min_option_title",true);
				$custom_fields["search_min_option_values"]=get_post_meta($post->ID,"search_min_option_values",true);
				$custom_fields["search_max_option_title"]=get_post_meta($post->ID,"search_max_option_title",true);
				$custom_fields["search_max_option_values"]=get_post_meta($post->ID,"search_max_option_values",true);
			}
			
			if($options)
			{
				$custom_fields["options"]=$options;
			}
			$return_arr[get_post_meta($post->ID,"htmlvar_name",true)] = $custom_fields;
		endwhile;
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');		
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		add_filter('posts_where', array($sitepress,'posts_where_filter'));	
	}
	return $return_arr;
}



/* -------------------- Mobile view code --------------------*/
/*
	Check if device is mobile or not. Return true if mobile device is detected
	Function located in wp-includes/vars.php, but sometimes it shows undefined
	Test if the current browser runs on a mobile device (smart phone, tablet, etc.) */
 
if(!function_exists('twp_is_mobile')){
	function twp_is_mobile() {
		static $is_mobile;

		if ( isset($is_mobile) )
			return $is_mobile;

		if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
			$is_mobile = false;
		} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false /* many mobile devices (all iPhone, iPad, etc.)*/
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
				$is_mobile = true;
		} else {
			$is_mobile = false;
		}

		return $is_mobile;
	}
}
if(!function_exists('tmpl_wp_is_mobile')){
	/*
	Check if device is mobile or not. Return true if mobile devie is detected
	*/
	function tmpl_wp_is_mobile(){
		if(function_exists('supreme_prefix')){  
			$pref = supreme_prefix();
		}else{
			$pref = sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );
		}
		
		$theme_options = get_option($pref.'_theme_settings');
		
		$is_mobile_enabled= @$theme_options['tmpl_mobile_view'];
		if($is_mobile_enabled !=0 || $is_mobile_enabled ==''){
			$is_mobile_enabled=1;
		}

		
			if($is_mobile_enabled==1){
				if ( (twp_is_mobile() || (isset($_REQUEST['device']) && $_REQUEST['device']=='mobile')) && (!preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT'])) && !strstr('windows phone',$_SERVER['HTTP_USER_AGENT']))){ /* if not desktop then return true */	
					return true;
				}else{
					return false;
				}	
			}else{
				return false;
			}
	}
}


if ( function_exists('tmpl_wp_is_mobile') && tmpl_wp_is_mobile() ){
		
	/*
	* Same Way This function will return the taxonomy/category page template.
	*/ 
	add_filter( "archive_template", "tmpl_get_mob_archive_template",99) ;
	add_filter( "taxonomy_template", "tmpl_get_mob_archive_template",99) ; 
	add_filter( "single_template", "tmpl_get_mob_single_template",99) ;
	add_filter( "search_template", "tmpl_get_mob_archive_template",99) ;
	add_filter( "page_template", "tmpl_get_mob_page_template",99);
	add_filter( "author_template", "tmpl_get_mob_author_template",99);
	add_filter( "comments_template", "tmpl_plugin_comment_template",99);
	add_action( 'init', 'tmpl_mob_preview_template' ,10);
	add_filter('body_class', 'tmpl_body_class_for_mobile');
	add_action('author_box', 'tmpl_author_mobiledashboard');
}else{
	/* add the author box on author dashboard */
	add_action('author_box', 'tmpl_author_dashboard');
}
/* add class in  body when theme load in mobile */
function tmpl_body_class_for_mobile($classes) {
        $id = get_current_blog_id();
        $slug = strtolower(str_replace(' ', '-', trim(get_bloginfo('name'))));
        $classes[] = $slug;
        $classes[] = 'mobile-view';
        return $classes;
}

/* return add ons name from post type */

function tmpl_addon_name(){
	
	global $addons_posttype,$tmpl_addons_posttype;
	if(empty($tmpl_addons_posttype)){
		$tmpl_addons_posttype = array();
	}
	/* array of all templatic tevolution add-ons */
	$addons_posttype = apply_filters('tmpl_addon_of_posttypes',array('listing'=>'Directory','event'=>'Events','property'=>'RealEstate','classified'=>'Classifieds'));
	
	return $addons_posttype = array_merge($addons_posttype,$tmpl_addons_posttype);
}

/* return add ons name from Payment methods */

function tmpl_payment_methods(){

	/* array of all templatic payment add-ons which goes to third party payment site */
	$payment_methods = apply_filters('tmpl_addons_payment_methods',array('paypal','Dwolla','2co'));
	
	return $payment_methods;
}
	
/* return the template in mobile view for archive,category and tags pages */
function tmpl_get_mob_archive_template(){
	/* auto detect mobile devices */
	$addons_posttype =tmpl_addon_name();
	
	/* Different template for mobile view */
	if (tmpl_wp_is_mobile()) {
		$template = '/mobile-'.get_post_type().'.php';
	}
	
	
	/* check if mobile template available in child theme else call from related plugin */
	if ( file_exists(STYLESHEETPATH .$template)) {
			
		$mob_template = STYLESHEETPATH .$template;			
		
	}else if ( file_exists(TEMPLATEPATH .$template) ) {
		
		$mob_template = TEMPLATEPATH . $template;
		
	}else{
		if(file_exists( WP_PLUGIN_DIR.'/Tevolution-'.$addons_posttype[get_post_type()].'/templates'.$template))
			$mob_template = WP_PLUGIN_DIR.'/Tevolution-'.$addons_posttype[get_post_type()].'/templates'.$template;
		else{
			$exclude_post_type = array('event','listing','property');
			if(!in_array(get_post_type(),$exclude_post_type))
			{
				if(file_exists(WP_PLUGIN_DIR.'/Tevolution-Directory/templates/mobile-listing.php'))
				{
					$mob_template = WP_PLUGIN_DIR.'/Tevolution-Directory/templates/mobile-listing.php';
				}
				else
				{
					$mob_template = WP_PLUGIN_DIR.'/Tevolution/templates/taxonomy-tevolution.php';
				}
			}
		}		
	}
	if(!is_category()){
		return $mob_template;
	}
}

/* Detail page template for mobile view */

function tmpl_get_mob_single_template(){
	
	$addons_posttype =tmpl_addon_name();

		/* Different template for mobile view */
	if (function_exists('tmpl_wp_is_mobile') && tmpl_wp_is_mobile() ){
		$template = '/mobile-single-'.get_post_type().'.php';
	}
	
	/* check if mobile template available in child theme else call from related plugin */
	if ( file_exists(STYLESHEETPATH .$template)) {
			
		$mob_template = STYLESHEETPATH .$template;			
		
	}else if ( file_exists(TEMPLATEPATH .$template) ) {
		
		$mob_template = TEMPLATEPATH . $template;
		
	}else{
		if(file_exists( WP_PLUGIN_DIR.'/Tevolution-'.$addons_posttype[get_post_type()].'/templates'.$template))
			$mob_template = WP_PLUGIN_DIR.'/Tevolution-'.$addons_posttype[get_post_type()].'/templates'.$template;
		else
			$mob_template = WP_PLUGIN_DIR.'/Tevolution-Directory/templates/mobile-single-listing.php';
	}
	global $post;

	if ($post->post_type == 'post') {
		if ( file_exists(STYLESHEETPATH .'/single.php')) {
			
			$mob_template = STYLESHEETPATH .'/single.php';			
		
		}else if ( file_exists(TEMPLATEPATH .'/single.php') ) {
		
			$mob_template = TEMPLATEPATH . '/single.php';
		}
	}
	return $mob_template;
}

/* show different pages  in mobile  */

function tmpl_get_mob_page_template($page_template){
	global $post;
	$template= "/mobile-templates/mobile-front-page.php";
	if(is_front_page() || is_home()){
		if ( file_exists(STYLESHEETPATH .$template)) {
			
			$page_template = STYLESHEETPATH .$template;			
		
		}else{
			if( file_exists(TEMPLATEPATH .$template))
			$page_template = TEMPLATEPATH . $template;
		
		}
	}elseif(is_page()){ /* if page is load in mobile then call this template. This will call 'mobile-page.php' from theme's root */
		$template = '/mobile-templates/mobile-page.php';
		if( file_exists(get_template_directory() . $template))
			$page_template = get_template_directory() . $template;
	}
	return $page_template;
}
/*
	Preview page template for mobile view 
*/
function tmpl_mob_preview_template(){

	$addons_posttype =tmpl_addon_name();
	
	/* Different template for mobile view */ 
	$template = '/mobile-single-'.get_post_type().'-preview.php';
	
	if((isset($_REQUEST['page']) && $_REQUEST['page'] == "preview")  && isset($_REQUEST['cur_post_type']) && in_array($_REQUEST['cur_post_type'],$custom_post_type)  && $_REQUEST['cur_post_type']!='event')
	{
		
		
		if ( file_exists(STYLESHEETPATH . $template)) {
			
			$single_template_preview = STYLESHEETPATH . $template;			
			
		} else if ( file_exists(TEMPLATEPATH . $template) ) {
			
			$single_template_preview = TEMPLATEPATH . $template;
			
		}else{
			
			if(file_exists( WP_PLUGIN_DIR.'/Tevolution-'.$addons_posttype[get_post_type()].'/templates'.$template))
			$single_template_preview = WP_PLUGIN_DIR.'/Tevolution-'.$addons_posttype[get_post_type()].'/templates'.$template;
			
		}		
		include($single_template_preview);
		exit;
	}
}

/* Comment template for mobile view */
function tmpl_plugin_comment_template(){
	global $post;
	$template= "/mobile-templates/mobile-comments.php";
	
	if( file_exists(get_template_directory() . $template))
		$comment_template = get_template_directory() . $template;
		
	return $comment_template ;
	
}

/* get the author page template for mobile view */
function tmpl_get_mob_author_template($author_template){
	$template= "/mobile-templates/mobile-author.php";
	
		if( file_exists(get_template_directory() . $template))
			$author_template = get_template_directory() . $template;
	
	return $author_template;
}

/* add additional script to custom pages */
add_action('wp_head','tmpl_add_compatibility_scripts');
if(!function_exists('tmpl_add_compatibility_scripts'))
{
	function tmpl_add_compatibility_scripts()
	{
		if((isset($_REQUEST['page']) && ($_REQUEST['page'] == 'preview' || $_REQUEST['page'] == 'success')) && is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
		{
			icl_lang_sel_nav_css($show = true);
		}
	}
}
/*
*start function to list - favourites post on dashboard 
*/
function tevolution_favourites_post_join($join){

	global $wpdb, $pagenow, $wp_taxonomies,$ljoin,$sitepress;
	$language_where='';	
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		$post_types=get_option('templatic_custom_post');
		$posttype='';		
		foreach($post_types as $key=>$value){
			$posttype.="'post_".$key."',";
		}
		$posttype=substr($posttype,0,-1);
		$language = ICL_LANGUAGE_CODE;
		$join = " {$ljoin} JOIN {$wpdb->prefix}icl_translations t1 ON {$wpdb->posts}.ID = t1.element_id			
			AND t1.element_type IN (".$posttype.") JOIN {$wpdb->prefix}icl_languages l1 ON t1.language_code=l1.code AND l1.active=1 AND t1.language_code='".$language."'";
	}
	return $join;
}

 /* Function for checked all default screen options on Appearance->Menu*/

add_filter( 'hidden_meta_boxes', 'tmpl_add_taxonomy_screen_options_menu' );
function tmpl_add_taxonomy_screen_options_menu( $hidden ) {
 if ( ! $user_id)
           $user_id = get_current_user_id(); 
	
	if(is_admin){
	 // Set the default hiddens if it has not been set yet
       if ( ! get_user_option( $meta_key['metaboxhidden_nav'], $user_id ) ) {
		   $meta_value=serialize($hidden);
		   update_user_option( $user_id, $meta_key['metaboxhidden_nav-menus'], $meta_value, true );
	    }
	
   }
  return $hidden;
}

/*
	return page to insert user
*/
function templ_insertuser_with_listing(){
	include_once(TEMPL_REGISTRATION_FOLDER_PATH.'single_page_checkout_insertuser.php');	
	return $current_user_id;
}
/*tevolution captcha script*/
add_action('wp_head','tmpl_captcha_script');
function tmpl_captcha_script()
{
	global $post;
	$tmpdata = get_option('templatic_settings');
	$display = @$tmpdata['user_verification_page'];
	/*condition to check whether captcha is enable or not in tevolution general settings*/
	if(is_array($display) && !empty($display) && (@in_array('registration', $display) || @in_array('submit', $display) || @in_array('claim', $display) || @in_array('emaitofrd', $display) || @in_array('sendinquiry', $display)))
	{
		?>
		<script type="text/javascript">
		  var onloadCallback = function() {
			/* Renders the HTML element with id 'example1' as a reCAPTCHA widget.*/
			/* The id of the reCAPTCHA widget is assigned to 'widgetId1'.*/
		   <?php if( @in_array('emaitofrd', $display) && is_single()) { ?>
			grecaptcha.render('snd_frnd_cap', {
				'sitekey' : '<?php echo $tmpdata['site_key']; ?>',
				'theme' : '<?php echo $tmpdata['comments_theme']; ?>',
				'hl'	: '<?php echo $tmpdata['captcha_language']; ?>'
			});
			<?php } ?>
			<?php if( @in_array('sendinquiry', $display) && is_single()) { ?>
			grecaptcha.render('inquiry_frm_popup', {
				'sitekey' : '<?php echo $tmpdata['site_key']; ?>',
				'theme' : '<?php echo $tmpdata['comments_theme']; ?>',
				'hl'	: '<?php echo $tmpdata['captcha_language']; ?>'
			});
			<?php do_action('show_captcha'); } ?>
			<?php if( @in_array('registration', $display)) { ?>
			if(jQuery('#comment_captcha').length > 0)
			{
				grecaptcha.render('comment_captcha', {
					'sitekey' : '<?php echo $tmpdata['site_key']; ?>',
					'theme' : '<?php echo $tmpdata['comments_theme']; ?>',
					'hl'	: '<?php echo $tmpdata['captcha_language']; ?>'
				});
			}
			if(jQuery('#popup_register_register_cap').length > 0)
			{
				grecaptcha.render('popup_register_register_cap', {
					'sitekey' : '<?php echo $tmpdata['site_key']; ?>',
					'theme' : '<?php echo $tmpdata['comments_theme']; ?>',
					'hl'	: '<?php echo $tmpdata['captcha_language']; ?>'
				});
			}
			if(jQuery('#register_login_widget_register_cap').length > 0)
			{
				grecaptcha.render('register_login_widget_register_cap', {
					'sitekey' : '<?php echo $tmpdata['site_key']; ?>',
					'theme' : '<?php echo $tmpdata['comments_theme']; ?>',
					'hl'	: '<?php echo $tmpdata['captcha_language']; ?>'
				});
			}
			if(jQuery('#userform_register_cap').length > 0)
			{
				grecaptcha.render('userform_register_cap', {
					'sitekey' : '<?php echo $tmpdata['site_key']; ?>',
					'theme' : '<?php echo $tmpdata['comments_theme']; ?>',
					'hl'	: '<?php echo $tmpdata['captcha_language']; ?>'
				});
			}
			<?php } ?>
			if(jQuery('#contact_recaptcha_div').length > 0)
			{
				grecaptcha.render('contact_recaptcha_div', {
					'sitekey' : '<?php echo $tmpdata['site_key']; ?>',
					'theme' : '<?php echo $tmpdata['comments_theme']; ?>',
					'hl'	: '<?php echo $tmpdata['captcha_language']; ?>'
				});
			}
			<?php if( @in_array('claim', $display) && is_single()) { ?>
			if(jQuery('#claim_ship_cap').length > 0)
			{
				grecaptcha.render('claim_ship_cap', {
					'sitekey' : '<?php echo $tmpdata['site_key']; ?>',
					'theme' : '<?php echo $tmpdata['comments_theme']; ?>',
					'hl'	: '<?php echo $tmpdata['captcha_language']; ?>'
				});
			}
			<?php } ?>
			<?php if( @in_array('submit', $display)) {?>
			grecaptcha.render('captcha_div', {
				'sitekey' : '<?php echo $tmpdata['site_key']; ?>',
				'theme' : '<?php echo $tmpdata['comments_theme']; ?>',
				'hl'	: '<?php echo $tmpdata['captcha_language']; ?>',
			});
			<?php } ?>
		  };
		</script>
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&amp;render=explicit" async defer></script>
		<?php
		
	}
}
/*action to captcha on comment form*/
add_action('comment_form', 'templ_show_recaptcha_in_comments');
/*action to check captcha is wrong, spam the comment*/
add_action('preprocess_comment', 'templ_captcha_check_comment');
function templ_show_recaptcha_in_comments()
{
	  global $user_ID;
	  $tmpdata = get_option('templatic_settings');
	  $display = @$tmpdata['user_verification_page'];
	  if(@in_array('registration', $display) && get_post_type() != 'post')
	  {
	  /*submit-button re-ordering */
      add_action('wp_footer', 'templ_save_comment_script');
	  $comment_string = <<<COMMENT_FORM
            <div id="recaptcha-submit-btn-area">&nbsp;</div>
            <noscript>
            <style type='text/css'>#submit {display:none;}</style>
            <input name="submit" type="submit" id="submit-alt" tabindex="6"
                value="Submit Comment"/> 
            </noscript>
COMMENT_FORM;

        $use_ssl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on");

        $escaped_error = htmlentities($_GET['rerror'], ENT_QUOTES);

        echo '<div id="comment_captcha"></div>' . $comment_string;
	 }
}
/* this is what does the submit-button re-ordering */
function templ_save_comment_script() {
	$javascript = <<<JS
		<script type="text/javascript">
		var sub = document.getElementById('submit');
		document.getElementById('recaptcha-submit-btn-area').appendChild (sub);
		document.getElementById('submit').tabIndex = 6;
		if ( typeof _recaptcha_wordpress_savedcomment != 'undefined') {
			document.getElementById('comment').value = 
				_recaptcha_wordpress_savedcomment;
		}
		</script>
JS;
	echo $javascript;
}
/*action to check captcha is wrong, spam the comment*/
function templ_captcha_check_comment($comment_data) {
	global $user_ID;
     $tmpdata = get_option('templatic_settings');
	  $display = @$tmpdata['user_verification_page'];
	  if(@in_array('registration', $display))
	  {
        /* do not check trackbacks/pingbacks*/
        if ($comment_data['comment_type'] == '' && get_post_type() != 'post') {
            $tmpdata = get_option('templatic_settings');
            /*fetch captcha private key*/
            $privatekey = $tmpdata['secret'];
            /*get the response from captcha that the entered captcha is valid or not*/
            $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=".$privatekey."&response=".$_REQUEST["g-recaptcha-response"]."&remoteip=".getenv("REMOTE_ADDR"));
            /*decode the captcha response*/
            $responde_encode = json_decode($response['body']);
            /*check the response is valid or not*/
            if (!$responde_encode->success) {
                add_filter('pre_comment_approved',
                    create_function('$a', 'return \'spam\';'));
            }
        }
    }
	return $comment_data;
}

/************ captcha on admin registration page *************/
/* for showing captcha on registration page in backend */
add_action('init','tmpl_captcha_on_admin_registration');

function tmpl_captcha_on_admin_registration(){

	$tmpdata = get_option('templatic_settings');
	$display = (!empty($tmpdata['user_verification_page']))? $tmpdata['user_verification_page'] : array();
	if((isset($_REQUEST['action']) && $_REQUEST['action'] == 'register') && in_array('registration',$display ))
	{
		 if ( is_multisite() )  {
			add_action('signup_extra_fields', 'tmpl_show_recaptcha_in_registration');
		} else {
			add_action('register_form', 'tmpl_show_recaptcha_in_registration');
		}
	}	
}

/* display recaptcha */
function tmpl_show_recaptcha_in_registration($errors) {

	/* if it's for wordpress mu, show the errors */
   if ( is_multisite() )   {
		$error = $errors->get_error_message('captcha');
		echo '<label for="verification">Verification:</label>';
		echo ($error ? '<p class="error">' . $error . '</p>' : '');
		echo tmpl_get_recaptcha_html();
	} else {        /* for regular wordpress */
		echo tmpl_get_recaptcha_html();
	}
}

/* html for captcha */
function tmpl_get_recaptcha_html() {
	$tmpdata = get_option('templatic_settings');
	
	return '<div class="g-recaptcha" data-sitekey="' .
		$tmpdata['site_key'] .
		'" data-theme="' . $tmpdata['comments_theme'] .
		'"></div><script type="text/javascript"' .
		'src="https://www.google.com/recaptcha/api.js?hl=' .
		$tmpdata['recaptcha_language'] .
		'"></script>';
}

/* for captcha varification */
add_action('init','tmpl_captcha_varification_admin_registration');

function tmpl_captcha_varification_admin_registration(){
	
	$tmpdata = get_option('templatic_settings');
	$display = (!empty($tmpdata['user_verification_page'])) ? $tmpdata['user_verification_page'] : array();
	if((isset($_REQUEST['action']) && $_REQUEST['action'] == 'register') && in_array('registration',$display ))
	{
	 if ( is_multisite() ) {
            add_filter('wpmu_validate_user_signup', 'validate_recaptcha_response_wpmu');
        } else {
            add_filter('registration_errors', 'tmpl_validate_recaptcha_response');
        }
	}	
}

/* get response  */
 function tmpl_validate_recaptcha_response($errors) {
 
 $tmpdata = get_option('templatic_settings');
 
	if (empty($_POST['g-recaptcha-response']) ||
		$_POST['g-recaptcha-response'] == '') {
		$errors->add('blank_captcha', __('Blank Captcha','templatic'));
		return $errors;
	}

	/* secret key */
	$secretkey = $tmpdata['secret'];
	
	/*get the response from captcha that the entered captcha is valid or not*/
	$response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=".$secretkey."&response=".$_REQUEST["g-recaptcha-response"]."&remoteip=".getenv("REMOTE_ADDR"));
	
	/* get response code */
	$response = json_decode($response['body']);
	
	if (!$response->success)
	{
		$errors->add('captcha_wrong', __('Wrong Captcha','templatic'));
		return $errors;
		
	}	

	return $errors;
}	
/************ captcha on admin registration page ends *************/


/* get all custom fields of post types pass in argument */

function tmpl_single_page_custom_field($post_type){
	
	$custom_post_type = tevolution_get_post_type();
	
	if((is_single() || $_POST['ptype']=='preview') && $post_type !=''){
		global $wpdb,$post,$htmlvar_name,$pos_title;
		
		$cus_post_type = $post_type;
		$heading_type = tmpl_fetch_heading_post_type($post_type);
		
		if(count($heading_type) > 0)
		{
			foreach($heading_type as $key=>$heading)
			{	
				/* fetch the custom fields of detail page*/
				$htmlvar_name[$key] = tmpl_get_single_page_customfields_details($post_type,$heading,$key);
			}
		}
		return $htmlvar_name;
	}
}

/*
 Get the custom fields details for detail page.
 */
if(!function_exists('tmpl_get_single_page_customfields_details')){
function tmpl_get_single_page_customfields_details($post_type,$heading='',$heading_key=''){	
	
	global $wpdb,$post,$posttitle;
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
	
	remove_all_actions('posts_where');		
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');


		$args = apply_filters('tmpl_nondir_htmlvar_name_query',array( 'post_type' => 'custom_fields',
					'posts_per_page' => -1	,
					'post_status' => array('publish'),
					'meta_query' => array('relation' => 'AND',
                                                                                                                                                                array(
                                                                                                                                                                          'key'     => 'post_type_'.$post_type.'',
                                                                                                                                                                          'value'   => $post_type,
                                                                                                                                                                          'compare' => '=',
                                                                                                                                                                          'type'    => 'text'
                                                                                                                                                                ),		
                                                                                                                                                                array(
                                                                                                                                                                          'key'     => 'is_active',
                                                                                                                                                                          'value'   =>  '1',
                                                                                                                                                                          'compare' => '='
                                                                                                                                                                ),
                                                                                                                                                                array(
                                                                                                                                                                          'key'     => 'show_on_detail',
                                                                                                                                                                          'value'   =>  '1',
                                                                                                                                                                          'compare' => '='
                                                                                                                                                                ),
                                                                                                                                                                array(
                                                                                                                                                                          'key'     => $post->post_type.'_heading_type',
                                                                                                                                                                          'value'   =>  array('basic_inf',$heading),
                                                                                                                                                                          'compare' => 'IN'
                                                                                                                                                                )
                                                                                                                                                      ),
					'meta_key' => 'sort_order',
					'orderby' => 'meta_value',
					'order' => 'ASC'
		),$post_type,$heading,$heading_key);
	
		/* save the data on transient to get the fast results */
		
	$post_query = new WP_Query($args);
          
		
		/* Join to make the custom fields WPML compatible */
		remove_filter('posts_join', 'custom_field_posts_where_filter');
		
		$htmlvar_name='';
		if($post_query->have_posts())
		{
			while ($post_query->have_posts()) : $post_query->the_post();
				$ctype = get_post_meta($post->ID,'ctype',true);
				$post_name=get_post_meta($post->ID,'htmlvar_name',true);
				$style_class=get_post_meta($post->ID,'style_class',true);
				$option_title=get_post_meta($post->ID,'option_title',true);
				$option_values=get_post_meta($post->ID,'option_values',true);
				$default_value=get_post_meta($post->ID,'default_value',true);
				$htmlvar_name[$post_name] = array( 'type'=>$ctype,
                                                                                                                                                                          'label'=> $post->post_title,
                                                                                                                                                                          'style_class'=>$style_class,
                                                                                                                                                                          'option_title'=>$option_title,
                                                                                                                                                                          'option_values'=>$option_values,
                                                                                                                                                                          'default'=>$default_value,
                                                                                                                                                                          );			
			endwhile;
			wp_reset_query();
		}
		return $htmlvar_name;
		
	}
}

/*
Name :fetch_page_taxonomy
Description : fetch page taxonomy 
*/

function fetch_page_taxonomy($pid){
	global $wp_post_types;
	$post_type = get_post_meta($pid,'submit_post_type',true);
	/* code to fetch custom Fields */
	$custom_post_types_args = array();
	$custom_post_types = get_post_type_object($post_type);
	$args_taxonomy = get_option('templatic_custom_post');
	if  ($custom_post_types) {
		 foreach ($custom_post_types as $content_type) {
			$post_slug = @$custom_post_types->rewrite['slug'];
			
			if($post_type == strtolower('post')){
				$taxonomy = 'category';
			}else{
				$taxonomy = $args_taxonomy[$post_slug]['slugs'][0];
			}
	  }
	}	
	return $taxonomy;
}

/* in menu section we add this hook to show all the custom taxonomies boxes */
global $pagenow;

if($pagenow == 'nav-menus.php'){
	add_filter('default_hidden_meta_boxes','tmpl_default_hidden_meta_boxes_fun');
}

function tmpl_default_hidden_meta_boxes_fun(){
	return array();
}

/*
* Shows category ang tags on preview page
*/

if(!function_exists('directory_post_preview_categories_tags')){
function directory_post_preview_categories_tags($cats,$tags)
{
	global $heading_title;
	$session=$_SESSION['custom_fields'];
	$cur_post_type=($_REQUEST['cur_post_type']!="")? $_REQUEST['cur_post_type']:'listing';
	$heading_type = tmpl_fetch_heading_post_type($cur_post_type);		
	$htmlvar_name = get_tevolution_single_customfields($cur_post_type,'[#taxonomy_name#]','basic_inf');/*custom fields for custom post type..*/
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $cur_post_type,'public'   => true, '_builtin' => true ));
	if(is_array($htmlvar_name) && !empty($htmlvar_name))
		$htm_keys = array_keys($htmlvar_name);
	
	$taxonomy_category='';
	for($c=0; $c < count($cats); $c++)
	{	
		if($c < ( count($cats) - 1)){
			$sep = ', ';
		}else{
			$sep = ' ';
		}
		$cat_id =  explode(',',$cats[$c]);
		$term = get_term_by('id', $cat_id[0], $taxonomies[0]);

		$term_link = get_term_link( $term, $taxonomies[0] );		
		$taxonomy_category .= '<a href="' . $term_link . '">' . $term->name . '</a>'.$sep; 
	}
	if($taxonomy_category !='' && !empty($htm_keys) && is_array($htm_keys) && in_array('category',$htm_keys))
	{		
		echo "<span>".__('Posted in ','templatic')."</span>".$taxonomy_category;		
	}
	
	$tag_terms = explode(',',$tags);
	$sep = ",";
	$i = 0;
	if(!empty($tag_terms[0])){
		for($t=0; $t < count($tag_terms); $t++){

			if($t < ( count($tag_terms) - 1)){
				$sep = ', ';
			}else{
				$sep = ' ';
			}
			$term = get_term_by('name', $tag_terms[$t], 'listingtags');

			if(empty($term)){
				$termname = $tag_terms[$t];
			}else{
				$termname = $term->name;
			}
			$taxonomy_tag .= '<a href="#">' .$termname. '</a>'.$sep;
		}
		if(!empty($tag_terms)){			
			echo sprintf(__('Tagged In %s','templatic'),$taxonomy_tag);			
		}
	}
}
}
/*
 *return array for listing custom fields
 */
function get_tevolution_single_customfields($post_type,$heading='',$heading_key=''){	
	global $wpdb,$post,$posttitle;
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
	remove_all_actions('posts_where');		
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');


	$args = array( 'post_type' => 'custom_fields',
				'posts_per_page' => -1	,
				'post_status' => array('publish'),
				'meta_query' => array('relation' => 'AND',
								array(
									'key'     => 'post_type_'.$post_type.'',
									'value'   => $post_type,
									'compare' => '=',
									'type'    => 'text'
								),		
								array(
									'key'     => 'is_active',
									'value'   =>  '1',
									'compare' => '='
								),
								array(
									'key'     => 'show_on_detail',
									'value'   =>  '1',
									'compare' => '='
								),
								array('key' => $post_type.'_heading_type','value' =>  array('basic_inf',$heading),'compare' => 'IN')
							),
				'meta_key'       => $post_type.'_sort_order',
				'orderby'        => 'meta_value_num',
				'meta_value_num' => $post_type.'_sort_order',
				'order'          => 'ASC'
	);

	if (get_option('tevolution_cache_disable')==1 && false === ($post_query = get_transient( '_tevolution_query_single'.trim($post_type).trim($heading_key).$cur_lang_code ))  ) {
		$post_query = new WP_Query($args);
		set_transient( '_tevolution_query_single'.trim($post_type).trim($heading_key).$cur_lang_code, $post_query, 12 * HOUR_IN_SECONDS );
	}elseif(get_option('tevolution_cache_disable')==''){
		$post_query = new WP_Query($args);
	}	
	
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	
	$htmlvar_name='';
	if($post_query->have_posts())
	{
		while ($post_query->have_posts()) : $post_query->the_post();
			$ctype = get_post_meta($post->ID,'ctype',true);
			if($ctype=='heading_type')
				continue;
			$post_name=get_post_meta($post->ID,'htmlvar_name',true);
			$style_class=get_post_meta($post->ID,'style_class',true);
			$option_title=get_post_meta($post->ID,'option_title',true);
			$option_values=get_post_meta($post->ID,'option_values',true);
			$default_value=get_post_meta($post->ID,'default_value',true);
			$htmlvar_name[$post_name] = array( 'type'=>$ctype,
									    'label'=> $post->post_title,
										'style_class'=>$style_class,
										'option_title'=>$option_title,
										'option_values'=>$option_values,
										'default'=>$default_value,
										);			
		endwhile;
		wp_reset_query();
	}
	return $htmlvar_name;
	
}


/* Script for detail page map and cookies js*/
add_action('wp_footer','tmpl_common_script_style',99);
function tmpl_common_script_style(){
	$custom_post_type = tevolution_get_post_type();
	if(in_array(!get_post_type(),$custom_post_type) ||  (is_archive() ||  is_category())){
            ?>
            <script type="text/javascript" async >
                jQuery(document).ready(function() {
                    var custom_wrap_taxonomy = '.tevolution_taxonomy_wrap';
                    var custom_wrap_archive = '.tevolution_archive_wrap';
                    jQuery("blockquote").before('<span class="before_quote"></span>').after('<span class="after_quote"></span>'), jQuery(".viewsbox a#listview").click(function(i) {
                        i.preventDefault(), jQuery(custom_wrap_taxonomy).removeClass("grid"), jQuery(custom_wrap_taxonomy).addClass("list"), jQuery(custom_wrap_archive).removeClass("grid"), jQuery(custom_wrap_archive).addClass("list"), jQuery(".viewsbox a").attr("class", ""), jQuery(this).attr("class", "active"), jQuery(".viewsbox a.gridview").attr("class", ""), jQuery.cookie("display_view", "list"), jQuery("#directory_listing_map").css("visibility", "hidden"), jQuery(custom_wrap_taxonomy).show(), jQuery(custom_wrap_archive).show(), jQuery("#listpagi").show(), jQuery("#directory_listing_map").height(0), "undefined" != typeof infoBubble && infoBubble.close()
                    }), jQuery(".viewsbox a#gridview").click(function(i) {
                        i.preventDefault(), jQuery(custom_wrap_taxonomy).removeClass("list"), jQuery(custom_wrap_taxonomy).addClass("grid"), jQuery(custom_wrap_archive).removeClass("list"), jQuery(custom_wrap_archive).addClass("grid"), jQuery(".viewsbox a").attr("class", ""), jQuery(this).attr("class", "active"), jQuery(".viewsbox .listview a").attr("class", ""), jQuery.cookie("display_view", "grid"), jQuery("#directory_listing_map").css("visibility", "hidden"), jQuery("#directory_listing_map").height(0), jQuery(custom_wrap_taxonomy).show(), jQuery(custom_wrap_archive).show(), jQuery("#listpagi").show(), "undefined" != typeof infoBubble && infoBubble.close()
                    }), jQuery(".viewsbox a#locations_map").click(function(i) {
                        i.preventDefault(), jQuery(".viewsbox a").attr("class", ""), jQuery(this).attr("class", "active"), jQuery(".viewsbox .listview a").attr("class", ""), jQuery(".viewsbox a.gridview").attr("class", ""), jQuery(custom_wrap_taxonomy).hide(), jQuery(custom_wrap_archive).hide(), jQuery("#listpagi").hide(), jQuery("#directory_listing_map").css("visibility", "visible"), jQuery("#directory_listing_map").height("auto"), jQuery.cookie("display_view", "locations_map")
                    })
                });
            </script>
    <?php
    }
}
?>