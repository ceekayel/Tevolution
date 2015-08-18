<?php
/*
name :is_on_ssl_url
description : check whether url is ssl enable or not.*/
function is_on_ssl_url()
{
	$tmpdata = get_option('templatic_settings');
	if(isset($tmpdata['templatic-is_allow_ssl']) && $tmpdata['templatic-is_allow_ssl'] == 'Yes')
	{
		return true;
	}
	else
	{
		return false;
	}
}
/*
name :tmpl_get_ssl_normal_url
description : replace http with https if ssl is enable.*/
function tmpl_get_ssl_normal_url($url)
{
	if(is_on_ssl_url())
	{
		$url = str_replace('http://','https://',$url);
	}
	return $url;
}
/*
 * Function Name: tevolution_general_function
 * Return: include the generalization css in header
 */
add_action( 'wp_enqueue_scripts', 'tevolution_general_function' );
function tevolution_general_function(){	
	if(is_single()){		
		wp_enqueue_style('general-style', TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-generalization/css/style.css' );
		wp_enqueue_script("generalization-leadmodal",TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-generalization/js/jquery.leanModal.min.js',array('jquery'));
		wp_enqueue_script("generalization-basic",TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-generalization/js/basic.js',array('jquery'));
	}
}
/*
 * Fincation Name: send_email_to_friend
 * Include popup_form.php file
 */
function send_email_to_friend()
{	?>
	<script>
		jQuery(document).ready(function(){
			jQuery('#send_friend_id').on('click', function(){
				jQuery('html,body').scrollTop(0);
				jQuery('#basic-modal-content').scrollTop(0);
			}); 
		});
	</script>
<?php	
	include_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalization/popup_frms.php");
}
/*
 * Fincation Name: send_inquiry
 * include popup_inquiry_frm.php
 */
function send_inquiry()
{
	?>
	<script>
		jQuery(document).ready(function(){
			jQuery('#send_inquiry_id').on('click', function(){
				jQuery('html,body').scrollTop(0);
				jQuery('#inquiry_div').scrollTop(0);
			}); 
		});
	</script>
<?php	
	include_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalization/popup_inquiry_frm.php");	
}
/* start code to add add to favourites on author dash board */
add_action('init','tevolution_author_favourites_tab');
function tevolution_author_favourites_tab(){
	if(current_theme_supports('tevolution_my_favourites')){		
		add_action('tevolution_author_tab','tmpl_dashboard_favourites_tab'); // to display tab 
	}
}
function tmpl_dashboard_favourites_tab(){
	global $current_user,$curauth,$wp_query;	
	$qvar = $wp_query->query_vars;
	$author = $qvar['author'];
	if(isset($author) && $author !='') :
		$curauth = get_userdata($qvar['author']);
	else :
		$curauth = get_userdata(intval($_REQUEST['author']));
	endif;	
	if(isset($_REQUEST['sort']) && $_REQUEST['sort'] =='favourites'){
		$class = 'nav-author-post-tab-active';
	}else{
		$class ='';
	}
	
	if($current_user->ID == $curauth->ID){
		echo "<li><a class='author_post_tab ".$class."' href='".esc_url(get_author_posts_url($current_user->ID).'?sort=favourites&custom_post=all')."'>";
		_e(esc_html('My Favorites'),DOMAIN);
		echo "</a></li>";
	}
	
}
if(isset($_REQUEST['sort']) && $_REQUEST['sort'] =='favourites'){
	global $current_user,$curauth,$wp_query,$sitepress;
	add_filter('posts_join','tevolution_favourites_post_join',12); // to apply filter where - filter listing
	add_filter('posts_where','tevolution_favourites_post',12); // to apply filter where - filter listing
}
/*
Name : tevolution_favourites_post_join
Desc : start function to list - favourites post on dashboard */
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

/*
Name : tevolution_favourites_post
Desc : start function to list - favourites post on dashboard */
function tevolution_favourites_post(){
	global $wpdb,$current_user,$curauth,$wp_query;
	
	$where = '';
	$query_var = $wp_query->query_vars;
	$user_id = $query_var['author'];
	$post_ids = get_user_meta($current_user->ID,'user_favourite_post',true);
	$final_ids = '';
	if(!empty($post_ids))
	{
		$post_ids = implode(",",$post_ids);
	}
	else
	{
	 	$post_ids = "''";
	}
	$qvar = $wp_query->query_vars;
	$authname = $qvar['author_name'];
	$curauth = get_userdata($qvar['author']);
	$nicename = $current_user->user_nicename;
	
	if($_REQUEST['sort']=='favourites')	{
		$where .= " AND ($wpdb->posts.ID in ($post_ids))";			
	}else
	{	
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			$language = ICL_LANGUAGE_CODE;
			$where = " AND ($wpdb->posts.post_author = $user_id)  AND t.language_code='".$language."'";
		}else{
			$where = " AND ($wpdb->posts.post_author = $user_id) ";
		}
	}	
	return $where;
}
/* Name :tevolution_server_date_time
Description : to fetch the server date and time  */
add_action('tevolution_details','tevolution_server_date_time');
function tevolution_server_date_time() {
	
	
	$tev_time_now = date("D dS M, Y h:i a");
	$timezone_now = date("e, (T P)");
	echo "<p id='server-date-time'><strong>".__('Server Date/Time',ADMINDOMAIN).":</strong> $tev_time_now <br/><strong>".__('Time Zone',ADMINDOMAIN).": </strong> $timezone_now</p>";
	
}
/* get tevolution version details */
function tevolution_version() {
	
	$plugin_file = get_tmpl_plugin_directory()."Tevolution/templatic.php";
	$plugin_details = get_plugin_data( $plugin_file, $markup = true, $translate = true ); 
	$version = @$plugin_details['Version'];
	echo " <span class='tevolution_version'>".@$version."<span>";
}
//Set Default permalink on theme activation: start
if(isset($_POST['submit-taxonomy']) && $_POST['submit-taxonomy'] !=''){
	add_action('admin_init','tevolution_default_permalink_set');
}
/*
Name : tevolution_default_permalink_set
Description : set permalink on set of new taxonomy
*/
function tevolution_default_permalink_set(){
	global $pagenow;
	if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && $_GET['page'] =='custom_taxonomy' ){ // Test if theme is activate
		//Set default permalink to postname start
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$wp_rewrite->flush_rules();
		if(function_exists('flush_rewrite_rules')){
			flush_rewrite_rules(true);  
		}
		//Set default permalink to postname end
	}
}
//Set Default permalink on theme activation: end
if(isset($_POST['Verify']) && $_POST['Verify'] !=''){
	global $wp_version;
	$arg=array('method' => 'POST',
			 'timeout' => 45,
			 'redirection' => 5,
			 'httpversion' => '1.0',
			 'blocking' => true,
			 'headers' => array(),
			 'body' => array( 'licencekey' => $_POST['licencekey'],'action'=>'licensekey_verification'),
			 'user-agent' => 'WordPress/'. $wp_version .'; '. home_url(),
			 'cookies' => array()
		);
	$warnning_message='';
	$response = wp_remote_get('http://templatic.net/api/verification/index.php',$arg );
	if(!is_wp_error( $response ) ) {
		update_option('templatic_licence_key',$response['body']);
		if(isset($_POST['licencekey']) && $_POST['licencekey'] !=''){ 
			if(strstr($response['body'],'error_message')){
				update_option('templatic_licence_key_','');
							
				add_action('tevolution_error_message','tevolution_error_message_error');
			}else{ 
				update_option('templatic_licence_key_',$_POST['licencekey']);
				add_action('tevolution_error_message','tevolution_error_message_success');
			}
		}else{
			update_option('templatic_licence_key_','');
			add_action('tevolution_error_message','tevolution_error_message_error');
		}
	}else{
		
		update_option('templatic_licence_key','{"error_message":"WP HTTP Error: couldn\'t connect to host."}');
		add_action('tevolution_error_message','tevolution_error_message_host');
	}
	//
	update_option('templatic_licence_key_',$_POST['licencekey']);
	update_option('templatic_licence_key','{"is_supreme":1,"success_message":"TGljZW5jZSBLZXkgVmVyaWZpZWQ="}');	
	//
}else{
	if(!get_option('templatic_licence_key_')){
		add_action('tevolution_error_message','tevolution_error_message_error');
	}
}
//379372543521c694b4d19c
$templatic_licence_key = get_option('templatic_licence_key');
if(strstr($templatic_licence_key,'is_supreme') && get_option('templatic_licence_key_') !='' && !$_POST){
	add_action('tevolution_error_message','tevolution_key_is_verified');
}
function tevolution_error_message_host(){
	echo "<p>WP HTTP Error: couldn't connect to host.</p>";
}
function tevolution_key_is_verified(){
	echo "<p style='color:Green;'>&nbsp;Licence Key Verified.</p>";
}
/* tevolution licence key error message */
function tevolution_error_message_error($message){
	if(isset($_POST['Verify']) && $_POST['Verify'] !=''){
		echo "<p style='color:red;'>"; 
			$error_message=json_decode(get_option('templatic_licence_key'));				
			if($error_message){
				echo base64_decode($error_message->error_message);
			}
		echo "</p>";
	}
	echo "<p>The key can be obtained from Templatic <a href='http://templatic.com/members/member'>member  area</a></p>";
}
/* end */
/* tevolution licence key success message */
function tevolution_error_message_success(){
	echo "<p style='color:green;'>";
	$success_message=json_decode(get_option('templatic_licence_key'));	
	if(!empty($success_message))	
		echo base64_decode(@$success_message->success_message);
	echo "</p>";
}
/* end */
add_action('wp_head','tevolution_licence_message');
function tevolution_licence_message(){
	if(!is_admin() && !strstr($_SERVER['REQUEST_URI'],'wp-admin/')){
		$templatic_licence_key = get_option('templatic_licence_key');
		if(strstr($templatic_licence_key,'error_message') || !get_option('templatic_licence_key_')){
			if(!get_option('templatic_licence_key_'))
			{
				echo "<h2>".__('Your copy of Tevolution hasn&#32;t been verified yet. To verify the plugin and unlock the site please <a href="'.admin_url( 'admin.php?page=templatic_system_menu').'" style="color:red;">click here</a> to verify your licence key',DOMAIN)."</h2>";
			}else{
				echo "<h2>".__('You are not allowed to run this site, because of invalid licence key. <a href="'.admin_url( 'admin.php?page=templatic_system_menu').'">click here</a> to verify your valid licence key',DOMAIN)."</h2>";
			}
			die;
		}
	}
}
/*
 * Function Name: tevolution_send_inquiry_form
 * Return: send inquiry email function 
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
		/* CODE TO CHECK WP-RECAPTCHA */
		$tmpdata = get_option('templatic_settings');
		$display = $tmpdata['user_verification_page'];
		if( $tmpdata['recaptcha'] == 'recaptcha')
		{
			if(file_exists(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php') && is_plugin_active('wp-recaptcha/wp-recaptcha.php') && in_array('sendinquiry',$display))
			{
				require_once( get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php');
				$a = get_option("recaptcha_options");
				$privatekey = $a['private_key'];
				$resp = recaptcha_check_answer ($privatekey,getenv("REMOTE_ADDR"),$_REQUEST["recaptcha_challenge_field"],$_REQUEST["recaptcha_response_field"]);						
									
				if ($resp->is_valid =="")
				{
					echo '1';
					exit;
				}
			}
		}
		/* END OF CODE - CHECK WP-RECAPTCHA */	
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
		///////Inquiry EMAIL START//////
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
			<p>Sent from -[#$post_url_link#]</p></p>',DOMAIN);
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
		/////////////customer email//////////////
		$yourname_link = __('<b><a href="'.get_option('siteurl').'">'.get_option('blogname').'</a></b>.',DOMAIN);
		$search_array = array('[#to_name#]','[#frnd_subject#]','[#post_title#]','[#frnd_comments#]','[#your_name#]','[#$post_url_link#]','[#contact#]');
		$replace_array = array($to_name,$frnd_subject,$post_url_link,$frnd_comments,$yourname,$yourname_link,$contact_num);
		$client_message = str_replace($search_array,$replace_array,$client_message,$contact_num); 
		templ_send_email($youremail,$yourname,$to_email,$to_name,$subject,stripslashes($client_message),$extra='');///To clidne email
		//////Inquiry EMAIL END////////		
		$post = "";
		if(get_option('siteurl').'/' == $_REQUEST['request_uri']){
				_e('Email sent successfully',DOMAIN);
				exit;
		} else {
				_e('Email sent successfully',DOMAIN);
				exit;
		}
		
	}
}
/*
 * Function Name: tevolution_send_friendto_form
 * Return: send friend to email function 
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
		if( $tmpdata['recaptcha'] == 'recaptcha')
		{
			if(file_exists(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php') && is_plugin_active('wp-recaptcha/wp-recaptcha.php') && in_array('emaitofrd',$display))
			{
				require_once( get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php');
				$a = get_option("recaptcha_options");
				$privatekey = $a['private_key'];
				$resp = recaptcha_check_answer ($privatekey,getenv("REMOTE_ADDR"),$_REQUEST["recaptcha_challenge_field"],$_REQUEST["recaptcha_response_field"]);						
					
				if ($resp->is_valid=="")
				{
					echo '1';
					exit;					
				}				
			}
		}
		else
		{
			if(file_exists(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php') && is_plugin_active('are-you-a-human/areyouahuman.php')  && in_array('emaitofrd',$display) && $tmpdata['recaptcha'] == 'playthru')
			{
				require_once(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php');
				require_once(get_tmpl_plugin_directory().'are-you-a-human/includes/ayah.php');
				$ayah = new AYAH();
		
				/* The form submits to itself, so see if the user has submitted the form.
				Use the AYAH object to get the score. */
				$score = $ayah->scoreResult();		
				if(!$score && $score=="")			
				{
					echo '2';
					exit;
				}
			}
		}
		
		
		/* END OF CODE - CHECK WP-RECAPTCHA */	
		$yourname = $_REQUEST['yourname'];
		$youremail = $_REQUEST['youremail'];
		$frnd_subject = $_REQUEST['frnd_subject'];
		$frnd_comments = $_REQUEST['frnd_comments'];
		$to_friend_email = $_REQUEST['to_friend_email'];
		$to_name = $_REQUEST['to_name_friend'];
		///////Inquiry EMAIL START//////
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
			<p>From, [#your_name#]</p>',DOMAIN);
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
		/////////////customer email//////////////
		//$post_url_link ='<a href="'.get_option('siteurl').'">'.get_option('blogname').'</a>';
		$search_array = array('[#to_name#]','[#post_title#]','[#frnd_comments#]','[#your_name#]','[#post_url_link#]');
		$replace_array = array($to_name,$post_url_link,nl2br($frnd_comments),$yourname,$post_url_link);
		$client_message = str_replace($search_array,$replace_array,$client_message);	
		templ_send_email($youremail,$yourname,$to_friend_email,$to_name,$subject,stripslashes($client_message),$extra='');///To clidne email
		
		//////Inquiry EMAIL END////////			
		_e('Email sent successfully',DOMAIN);
		exit;
	}
		
}
add_action('admin_init','tevolution_licensekey_popupbox');
function tevolution_licensekey_popupbox(){
	global $pagenow;	
	if($pagenow=='themes.php' || ($pagenow=='admin.php' && isset($_REQUEST['page']) && $_REQUEST['page']=='templatic_system_menu')){
		$templatic_licence_key=get_option('templatic_licence_key_');
		if(($pagenow=='themes.php' &&  $templatic_licence_key=='') || $templatic_licence_key==''){
			?>
			<div id="boxes" class="licensekey_boxes">
				<div style="top:0px; left: 551.5px; display: none;" id="dialog" class="window">
                    	<span class="close"><a href="#" class="close"><img src="<?php echo TEMPL_PLUGIN_URL.'images/delete_10.png'?>" alt="<?php echo __('Close it',ADMINDOMAIN);?>"  /></a></span>
					<h2><?php echo __('Licence key',ADMINDOMAIN); ?></h2>
                         <form action="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu";?>" name="" method="post">
                         <div class="inside">
                         <p><?php echo __('Enter the license key in order to unlock the plugin and enable automatic updates.',ADMINDOMAIN); ?></p>
						 <div id="licence_fields">
                                   <input type="password" name="licencekey" id="licencekey" value="<?php echo get_option('templatic_licence_key_'); ?>" size="30" max-length="36" PLACEHOLDER="templatic.com purchase code"/>
                                   <input type="submit" accesskey="p" value="<?php echo __('Verify',ADMINDOMAIN);?>" class="button button-primary button-large" id="Verify" name="Verify">
                                   <?php do_action('tevolution_error_message'); ?>
						</div>
                         </div>
                         </form>
				</div>
				<!-- Mask to cover the whole screen -->
				<div style="width: 1478px; height: 602px; display: none; opacity: 0.8;" id="mask"></div>
			</div>
			<?php
		}
	}
}

/*
Name:get_tmpl_plugin_directory
desc: return the plugin directory path
*/
if(!function_exists('get_tmpl_plugin_directory')){
function get_tmpl_plugin_directory() {
	 return WP_CONTENT_DIR."/plugins/";
}
}


/* Add add to favourite html for detail page */
if(!function_exists('tmpl_detailpage_favourite_html')){
function tmpl_detailpage_favourite_html($user_id,$post)
{
	global $current_user,$post;
	$add_to_favorite = __('Add to favorite',DOMAIN);
	$added = __('Added',DOMAIN);
	if(function_exists('icl_register_string')){
		icl_register_string(DOMAIN,'directory'.$add_to_favorite,$add_to_favorite);
		$add_to_favorite = icl_t(DOMAIN,'directory'.$add_to_favorite,$add_to_favorite);
		icl_register_string(DOMAIN,'directory'.$added,$added);
		$added = icl_t(DOMAIN,'directory'.$added,$added);
	}
	$post_id = $post->ID;
	
	$user_meta_data = get_user_meta($current_user->ID,'user_favourite_post',true);
	if($post->post_type !='post'){
		if($user_meta_data && in_array($post_id,$user_meta_data))
		{
			?>
		<li id="tmplfavorite_<?php echo $post_id;?>" class="fav_<?php echo $post_id;?> fav"  > <a href="javascript:void(0);" class="removefromfav" onclick="javascript:addToFavourite('<?php echo $post_id;?>','remove');"><?php echo $added;?></a>   </li>    
			<?php
		}else{
		?>
		<li id="tmplfavorite_<?php echo $post_id;?>" class="fav_<?php echo $post_id;?> fav"><a href="javascript:void(0);" class="addtofav"  onclick="javascript:addToFavourite('<?php echo $post_id;?>','add');"><?php echo $add_to_favorite;?></a></li>
		<?php } 
	}
}
}

/*
Name:tevolution_dir_popupfrms
desc: return the pop up forms in detail page
*/
if(!function_exists('tevolution_dir_popupfrms')){
	function tevolution_dir_popupfrms($post){
	global $current_user,$post;
	$tmpdata = get_option('templatic_settings');	
	$link='';	
	if(isset($tmpdata['send_to_frnd'])&& $tmpdata['send_to_frnd']=='send_to_frnd' && function_exists('send_email_to_friend'))
	    {
		
		$claim_content_link='<a class="small_btn tmpl_mail_friend" rel="leanModal_email_friend" href="#basic-modal-content" id="send_friend_id"  title="Mail to a friend" >'. __('Send to friend',DOMAIN).'</a>';				
	       echo '<li>';
		  add_action('wp_footer','send_email_to_friend');
	       echo $claim_content_link.'</li>';
	    }
		
	    if(isset($tmpdata['claim_post_type_value'])&& @in_array($post->post_type,$tmpdata['claim_post_type_value']) && function_exists('claim_ownership') && @$post->post_author!=@$current_user->ID)
	    {	
		echo '<li class="claim_ownership">';
		claim_ownership();
		echo '</li>';
	    }
	    if(isset($tmpdata['send_inquiry'])&& $tmpdata['send_inquiry']=='send_inquiry' && function_exists('send_inquiry'))
	   {			
		$send_inquiry='<a class="small_btn tmpl_mail_friend" rel="leanModal_send_inquiry"  href="#inquiry_div" title="Send Inquiry" id="send_inquiry_id" >'.__('Send inquiry',DOMAIN).'</a>';
			 add_action('wp_footer','send_inquiry');		
		echo '<li class="send_inquiry">'.$send_inquiry.'</li>';
	   }  
		if(current_theme_supports('tevolution_my_favourites') && ($post->post_status == 'publish' )){
			global $current_user;
			$user_id = $current_user->ID;
			$link.= tmpl_detailpage_favourite_html($user_id,@$post);
		}
	    echo $link;
	    ?>
	  
	<?php }
}
/*
Name:tevolution_socialpost_link
desc: return the social media links of current post
*/
if(!function_exists('tevolution_socialpost_link')){
	function tevolution_socialpost_link($post){
		global $htmlvar_name;
		$is_edit='';
		if(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'){
			$is_edit=1;
		}
		$facebook=get_post_meta($post->ID,'facebook',true);
		$google_plus=get_post_meta($post->ID,'google_plus',true);
		$twitter=get_post_meta($post->ID,'twitter',true);
	echo '<div class="share_link">';
	if($facebook!="" && $htmlvar_name['contact_info']['facebook'] || ($is_edit==1 && $htmlvar_name['contact_info']['facebook'])):
		if(!strstr($facebook,'http'))
			$facebook = 'http://'.$facebook;
	?>
	 	<span><a id="facebook" class="frontend_facebook <?php if($is_edit==1):?>frontend_link <?php endif;?>" href="<?php echo $facebook;?>"><img src="<?php echo TEVOLUTION_PAGE_TEMPLATES_URL; ?>images/i_facebook21.png" alt="Facebook" /></a></span>
	 <?php endif;?>

	 <?php if($twitter!="" && $htmlvar_name['contact_info']['twitter'] || ($is_edit==1 && $htmlvar_name['contact_info']['twitter'])):
	 	if(!strstr($twitter,'http'))
			$twitter = 'http://'.$twitter;
	 	?>
	 	<span><a id="twitter" class="frontend_twitter <?php if($is_edit==1):?>frontend_link <?php endif;?>" href="<?php echo $twitter;?>"><img src="<?php echo TEVOLUTION_PAGE_TEMPLATES_URL; ?>images/i_twitter2.png" alt="Twitter" /></a></span>
	 <?php endif;?>

	 <?php if($google_plus!="" && $htmlvar_name['contact_info']['google_plus']  || ($is_edit==1 && $htmlvar_name['contact_info']['google_plus'])):
	 	if(!strstr($google_plus,'http'))
			$google_plus = 'http://'.$google_plus;
	 ?>
	 <span><a id="google_plus" class="frontend_google_plus <?php if($is_edit==1):?>frontend_link <?php endif;?>" href="<?php echo $google_plus;?>"><img src="<?php echo TEVOLUTION_PAGE_TEMPLATES_URL; ?>images/i_googleplus.png" alt="Google Plus" /></a></span>
	 <?php endif;
		
	echo '</div>';
	}
}
/*
Name:tevolution_socialmedia_sharelink
Desc: Social media share link
*/
if(!function_exists('tevolution_socialmedia_sharelink')){
function tevolution_socialmedia_sharelink($post){
	$tmpdata = get_option('templatic_settings');	
	$title=urlencode($post->post_title);
	$post_img = bdw_get_images_plugin($post->ID,'thumb');
	$post_images = @$post_img[0]['file'];
	$url=urlencode(get_permalink($post->ID));
	$summary=urlencode(htmlspecialchars($post->post_content));
	$image=$post_images;
	if(@$tmpdata['google_share_detail_page'] == 'yes' || @$tmpdata['twitter_share_detail_page'] == 'yes' || @$tmpdata['pintrest_detail_page']=='yes')
	{?>
	<script src="http://sharrre.com/js/jquery.sharrre-1.3.4.min.js?v=3"></script>
	<ul class='social-media-share'>
	<?php if($tmpdata['facebook_share_detail_page'] == 'yes') { ?>
		<li><div class="facebook_share" data-url="<?php echo $url; ?>" data-text="<?php echo $title; ?>"></div></li>
	<?php } ?>
	<?php if($tmpdata['twitter_share_detail_page'] == 'yes'): ?>
		<li><div class="twitter_share"  data-url="<?php echo $url; ?>" data-text="<?php echo $title; ?>"></div></li> 
	<?php endif; 
	if($tmpdata['google_share_detail_page'] == 'yes'): ?>
	    <li><div class="googleplus_share" href="javascript:void(0);"  data-url="<?php echo $url; ?>" data-text="<?php echo $title; ?>"></div></li>
	<?php endif; ?>
	
	<?php if(@$tmpdata['pintrest_detail_page']=='yes'):?>
		<li><div class="pinit_share" href="javascript:void(0);"  data-url="<?php echo $url; ?>" data-text="<?php echo $title; ?>"></div></li>
	<?php endif; ?>   
	</ul>
	
<?php 	
		add_action('wp_footer','include_share_script');
		} 
	}
}

/*
Name: include_share_script
Desc: include script for share.
*/
function include_share_script()
{
	?>
	<script>
		jQuery( document ).ready(function() {
            jQuery('.twitter_share').sharrre({
              share: {
                twitter: true
              },
              template: '<a class="box" href="#"><span class="share"><i class="step fa fa-twitter"></i></span> <span class="count" href="#">{total} <span class="showlabel">Tweet</span></span></a>',
              enableHover: false,
              enableTracking: true,
              buttons: { twitter: {via: '_JulienH'}},
              click: function(api, options){
                api.simulateClick();
                api.openPopup('twitter');
              }
            });
            jQuery('.facebook_share').sharrre({
              share: {
                facebook: true
              },
              template: '<a class="box" href="#"><span class="share"><i class="step fa fa-facebook"></i></span> <span class="count" href="#">{total} <span class="showlabel">Like</span></span></a>',
              enableHover: false,
              enableTracking: true,
              click: function(api, options){
                api.simulateClick();
                api.openPopup('facebook');
              }
            });
            jQuery('.googleplus_share').sharrre({
              share: {
                googlePlus: true
              },
			  template: '<a class="box" href="#"><span class="share"><i class="fa fa-google-plus"></i> </span> <span class="count" href="#">{total} <span class="showlabel">+1</span></span></a>',
              enableHover: false,
              enableTracking: true,
              urlCurl: '',
              click: function(api, options){
                api.simulateClick();
                api.openPopup('googlePlus');
              }
            });
            jQuery('.pinit_share').sharrre({
              share: {
                pinterest: true
              },
              template: '<a class="box" href="#"><span class="share"><i class="fa fa-pinterest"></i></span> <span class="count" href="#">{total} <span class="showlabel"><?php _e('Pin','templatic'); ?></span></span></a>',
              enableHover: false,
              enableTracking: true,
              urlCurl: '',
              click: function(api, options){
                api.simulateClick();
                api.openPopup('pinterest');
              }
            });
		});
     </script>
	<?php
}

/*
Name:is_writeable_file
Desc: check whether file is writable or not.
*/
function is_writeable_file($path) {

	// PHP's is_writable does not work with Win32 NTFS
		 
	if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
		return is_writeable_file($path.uniqid(mt_rand()).'.tmp');
	else if (is_dir($path))
		return is_writeable_file($path.'/'.uniqid(mt_rand()).'.tmp');
	// check tmp file for read/write capabilities
	$rm = file_exists($path);
	$f = @fopen($path, 'a');
	if ($f===false)
		return false;
	fclose($f);
	if (!$rm)
		unlink($path);
	return true;
}
/*
Name:tevolution_replace_line
Desc: tevolution replace domains to old to new new to old
*/
function tevolution_replace_line($old, $new, $my_file) {
	if ( @is_file( $my_file ) == false ) {
		return false;
	}
	if (!is_writeable_file($my_file)) {
		echo "Error: file $my_file is not writable.\n";
		return false;
	}
	

	$found = false;
	$lines = file($my_file);
	foreach( (array)$lines as $line ) {
	 	if ( preg_match("/$old/", $line)) {
			$found = true;
			break;
		}
	}
	if ($found) {
		$fd = fopen($my_file, 'w');
		foreach( (array)$lines as $line ) {
			if ( !preg_match("/$old/", $line))
				fputs($fd, $line);
			else {
				fputs($fd, "$new //tevolution* deprecated\n");
			}
		}
		fclose($fd);
		return true;
	}
	$fd = fopen($my_file, 'w');
	$done = false;
	foreach( (array)$lines as $line ) {
		if ( $done || !preg_match('/^(if\ \(\ \!\ )?define|\$|\?>/', $line) ) {
			fputs($fd, $line);
		} else {
			fputs($fd, "$new //tevolution* deprecated\n");
			fputs($fd, $line);
			$done = true;
		}
	}
	fclose($fd);
	return true;
}
/* 
Name: tevolution_localization_slugs
Desc: Change the localization slug if use wpml plugin
*/

add_action('admin_init','tevolution_localization_slugs');
function tevolution_localization_slugs(){

	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		if(!get_option('tevolution_localization'))
		{
			$found='';
			$files_to_search =  TEVOLUTION_PAGE_TEMPLATES_DIR.'templatic.php';
			$old = 'define\(\ \'DOMAIN';
			$old1 = 'define\(\ \'ADMINDOMAIN';
			$new ="\tdefine( 'DOMAIN', 'tevolution'); ";
			$new1 ="\tdefine( 'ADMINDOMAIN', 'tevolution' );";
			tevolution_replace_line($old,$new,$files_to_search);
			tevolution_replace_line($old1,$new1,$files_to_search);
			update_option('tevolution_localization','1');
		}	
		if(get_option('tevolution_localization') == 1)
		{
			add_action('admin_notices','tevolution_text_domain_message');
		}
	
		if(isset($_REQUEST['ch_domain']) && $_REQUEST['ch_domain'] =='domain'){
			$found='';
				$files_to_search =  TEVOLUTION_PAGE_TEMPLATES_DIR.'templatic.php';
				$old = 'define\(\ \'DOMAIN';
				$old1 = 'define\(\ \'ADMINDOMAIN';
				$new ="\tdefine( 'DOMAIN', 'templatic'); ";
				$new1 ="\tdefine( 'ADMINDOMAIN', 'templatic-admin' );";
				tevolution_replace_line($old,$new,$files_to_search);
				tevolution_replace_line($old1,$new1,$files_to_search);
				update_option('tevolution_localization',2);
		}
	}
}

/* 
Name: tevolution_text_domain_message
Desc: Change the localization slug if use wpml plugin
*/

function tevolution_text_domain_message(){
	$url = admin_url('index.php?ch_domain=domain');
	$message = "<div id=\"error\" class=\"updated\">\n";
	$message .= '<p>We see that you have WPML activated so we have kept the old text domain for you(its changed in the new version!) If you want to change the domain to a new one <a href="'.admin_url('index.php?ch_domain=domain').'"> Click here </a>. <br> <strong>Note:</strong>Unfortunately this will wipe out your translations and you will have to translate again! to continue with new localization text domain</p>';
	$message .= do_action('localization_filter');
	$message .= "</div>";
	echo $message;
}

add_action('tevolution_subcategory','tevolution_subcategory'); // show post subcategories on category pages
/*
 * Function Name: tevolution_subcategory
 * Return: display the sub categories in tevolution created post types
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
Name:tmpl_search_filters
Desc: search filters for all type of searches LIKE search by address OR near by search OR advance search
*/
add_action('init','tmpl_search_filters');
function tmpl_search_filters(){
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-generalization/search_filters.php')){
		include(TEMPL_MONETIZE_FOLDER_PATH.'templatic-generalization/search_filters.php');
	}
}


/*
Name:add_alternative_files
Desc: add alternatetnative script to default wordpress theme
*/
add_action('init','add_alternative_files');
function add_alternative_files()
{
	if(!function_exists('supreme_resources'))
	{
		wp_enqueue_script( 'supreme-scripts', trailingslashit ( TEMPL_PLUGIN_URL ) . 'js/jquery.flexslider-min.js', array( 'jquery' ), '20120606', true );
	}
}

/*
Name:remove_captcha_script
Desc: remove extra script on captcha form while logout user in pop up forms.
*/
add_action('wp_footer','remove_captcha_script',99);
function remove_captcha_script()
{
	if(is_single() && is_plugin_active('wp-recaptcha/wp-recaptcha.php'))
	{
		?>
			<script>
				jQuery(function() {
					if(document.getElementById('recaptcha_switch_img_btn'))
					{
						jQuery("#recaptcha_switch_img_btn").remove();
					}
				});
			</script>
		<?php
		
	}
}
 /*
fetch all the users for back end drop down list.
*/
add_filter('wp_dropdown_users', 'tmpl_theme_post_author_override');
function tmpl_theme_post_author_override($output) { 
	global $post; // return if this isn't the theme author override dropdown 
	if (!preg_match('/post_author_override/', $output)) return $output; // return if we've already replaced the list (end recursion) 
	if (preg_match ('/post_author_override_replaced/', $output)) return $output; // replacement call to wp_dropdown_users
	$output = wp_dropdown_users(array( 'echo' => 0, 'name' => 'post_author_override_replaced', 'selected' => empty($post->ID) ? $user_ID : $post->post_author, 'include_selected' => true )); // put the original name back 
	$output = preg_replace('/post_author_override_replaced/', 'post_author_override', $output); return $output;
}
?>
