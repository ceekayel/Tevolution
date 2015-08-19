<?php
/*
* includes the main files of tevolution default ad-on such as claim ownership,custom taxonomy,csutom fields,etc. and common functions.
*/
if (!isset($_SESSION)) { session_start(); }

require_once(TEMPL_MONETIZE_FOLDER_PATH."/templatic-claim_ownership/install.php" ); 
require_once(TEMPL_MONETIZE_FOLDER_PATH."/templatic-custom_taxonomy/install.php" );
require_once(TEMPL_MONETIZE_FOLDER_PATH."/templatic-custom_fields/install.php" );
require_once(TEMPL_MONETIZE_FOLDER_PATH."/templatic-monetization/install.php" );
require_once(TEMPL_MONETIZE_FOLDER_PATH."/templatic-ratings/install.php" );
require_once(TEMPL_MONETIZE_FOLDER_PATH."/templatic-registration/install.php" );
require_once (TEMPL_MONETIZE_FOLDER_PATH . "/templatic-widgets/templatic_browse_by_categories_widget.php");			
require_once (TEMPL_MONETIZE_FOLDER_PATH . "/templatic-widgets/templatic_advanced_search_widget.php");			
require_once (TEMPL_MONETIZE_FOLDER_PATH . "/templatic-widgets/templatic_people_list_widget.php");			
require_once (TEMPL_MONETIZE_FOLDER_PATH . "/templatic-widgets/templatic_metakey_search_widget.php");			

require_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalization/general_functions.php" );

/* Add to favourites for tevolution*/
if(file_exists(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalization/add_to_favourites.php") && (!strstr($_SERVER['REQUEST_URI'],'/wp-admin/') || strstr($_SERVER['REQUEST_URI'],'/admin-ajax.php') )){
	require_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalization/add_to_favourites.php" );
}

/*
 do action for admin menu
*/
add_action('admin_menu', 'templ_add_admin_menu_'); /* create templatic admin menu */
function templ_add_admin_menu_()
{
	do_action('templ_add_admin_menu_');
}


add_action('templ_add_admin_menu_', 'templ_add_mainadmin_menu_', 0);
add_action('templ_add_admin_menu_', 'templ_remove_mainadmin_sub_menu_');
if(!function_exists('templ_remove_mainadmin_sub_menu_')){
	function templ_remove_mainadmin_sub_menu_(){
		remove_submenu_page('templatic_system_menu', 'templatic_system_menu'); 
		add_submenu_page( 'templatic_system_menu', __('Overview','templatic-admin'), __('Overview','templatic-admin'), 'administrator', 'templatic_system_menu', 'templatic_connector_class' );
	}
}
/*
 include the main.connector.class.php file
*/
function templatic_connector_class()
{
	 require_once(TEVOLUTION_PAGE_TEMPLATES_DIR.'classes/main.connector.class.php' );	
}
/*
	Return the main menu at admin sidebar
*/
function templ_add_mainadmin_menu_()
{
	$menu_title = __('Tevolution', 'templatic');
	if (function_exists('add_object_page'))
	{
		if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'templatic_system_menu'){
			$icon = TEMPL_PLUGIN_URL.'favicon-active.png';
		}else{
			$icon = TEMPL_PLUGIN_URL.'favicon-active.png';
		}
		$hook = add_menu_page("Admin Menu", $menu_title, 'administrator', 'templatic_system_menu', 'dashboard_bundles', '',3); /* title of new sidebar*/
	}else{
		add_menu_page("Admin Menu", $menu_title, 'administrator',  'templatic_wp_admin_menu', 'design','');		
	} 
}
/*
 return the connection with dashboard wizards(bundle box)
*/
function dashboard_bundles()
{
	$Templatic_connector = New Templatic_connector;
	require_once(TEVOLUTION_PAGE_TEMPLATES_DIR.'classes/main.connector.class.php' );	
	if(isset($_REQUEST['tab']) && $_REQUEST['tab'] =='extend') { 	
		$Templatic_connector->templ_extend();
	}else if(isset($_REQUEST['tab']) && $_REQUEST['tab'] =='payment-gateways') { 	
		$Templatic_connector->templ_payment_gateway();
	}else if((!isset($_REQUEST['tab'])&& @$_REQUEST['tab']=='') || isset($_REQUEST['tab']) && $_REQUEST['tab'] =='overview') { 	
		$Templatic_connector->templ_overview();
		$Templatic_connector->templ_dashboard_extends();
	}
  
}

/*
 return main CSS of Plugin
*/
add_action('admin_head', 'templ_add_my_stylesheet'); /* include style sheet */
add_action('wp_head', 'templ_add_my_stylesheet',0); /* include style sheet */	

function templ_add_my_stylesheet()
{
  /* Respects SSL, Style.css is relative to the current file */
  wp_enqueue_script('jquery');
 
	/* Tevolution Plug-in Style Sheet File In Desktop view only  */	
	if (function_exists('tmpl_wp_is_mobile') && !tmpl_wp_is_mobile()) {
		wp_enqueue_style('tevolution_style',TEMPL_PLUGIN_URL.'css.minifier.php','',false);
	}
	if(function_exists('theme_get_settings')){
		if(theme_get_settings('supreme_archive_display_excerpt')){
			if(function_exists('tevolution_excerpt_length')){
				add_filter('excerpt_length', 'tevolution_excerpt_length');
			}
			if(function_exists('new_excerpt_more')){
				add_filter('excerpt_more', 'new_excerpt_more');
			}
		}
	}
}
/*
return each add-ons is activated or not
*/
function is_active_addons($key)
{
  $act_key = get_option($key);
  if ($act_key != '')
  {
    return true;
  }
}
/*
 Function will remove the admin dashboard widget
*/
function templ_remove_dashboard_widgets()
{
  /* Globalize the metaboxes array, this holds all the widgets for wp-admin*/
  global $wp_meta_boxes;
  /* Remove the Dashboard quickpress widget*/
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
  /* Remove the Dashboard  incoming links widget*/
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
  /* Remove the Dashboard secondary widget*/
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}
add_action('wp_dashboard_setup', 'templ_remove_dashboard_widgets');

/* -- coding to add submenu under main menu-- */
add_action('templ_add_admin_menu_', 'templ_add_page_menu');
function templ_add_page_menu()
{

	/*tevolution_menu_before_general_settings hook for add additional menu before general settings */
	do_action('tevolution_menu_before_general_settings');
	
	$menu_title2 = __('Settings', 'templatic-admin');
	add_submenu_page('templatic_system_menu', $menu_title2, $menu_title2,'administrator', 'templatic_settings', 'my_page_templates_function');
	
	/*tevolution_menu_after_general_settings hook for add additional menu after general settings */
	do_action('tevolution_menu_after_general_settings');		
}

/*
	Email, security , and set up steps menu selected
 */
add_action('admin_footer','tevolution_menu_script');
function tevolution_menu_script()
{
	?>
	<script  type="text/javascript" async >
     jQuery(document).ready(function(){	
          if(jQuery('#adminmenu ul.wp-submenu li').hasClass('current'))
          {
               <?php if(isset($_REQUEST['page']) && $_REQUEST['page']=='templatic_system_menu' && isset($_REQUEST['tab']) && $_REQUEST['tab']=='setup-steps' ):?>
               jQuery('#adminmenu ul.wp-submenu li').removeClass('current');
               jQuery('#adminmenu ul.wp-submenu li a').removeClass('current');								
               jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_system_menu&tab=setup-steps"]').attr('href', function() {					    
                        jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_system_menu&tab=setup-steps"]').addClass('current');
                        jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_system_menu&tab=setup-steps"]').parent().addClass('current');
               });
               <?php endif;?>
               
          }
          jQuery('.reset_custom_fields').click( function() {
               if(confirm("<?php echo __('All your modifications done with this, will be deleted forever! Still you want to proceed?','templatic-admin');?>")){
                    return true;
               }else{
                    return false;
               }	
          });
     });
     </script>
     <?php
}

/* include general_settings.php file */
function my_page_templates_function()
{	
	include(TEMPL_MONETIZE_FOLDER_PATH.'templatic-generalization/general_settings.php');
	
}

/*
	Redirect on plugin dashboard after activating plugin
*/
add_action('admin_init', 'my_plugin_redirect');
function my_plugin_redirect()
{  
	global $pagenow;	
	if (get_option('myplugin_redirect_on_first_activation') == 'true' && $pagenow=='plugins.php' ){
		update_option('myplugin_redirect_on_first_activation', 'false');
		wp_redirect(MY_PLUGIN_SETTINGS_URL);
	}
	if (get_option('myplugin_redirect_on_first_activation') == 'true' && $pagenow=='themes.php' ){
		update_option('myplugin_redirect_on_first_activation', 'false');
		wp_redirect(site_url().'/wp-admin/themes.php');
	}
}

/*
 * View counter for detail page
 */
function view_counter_single_post($pid){	
	if($_SERVER['HTTP_REFERER'] == '' || !strstr($_SERVER['HTTP_REFERER'],$_SERVER['REQUEST_URI']))
	{
		$viewed_count = get_post_meta($pid,'viewed_count',true);
		$viewed_count_daily = get_post_meta($pid,'viewed_count_daily',true);
		$daily_date = get_post_meta($pid,'daily_date',true);
	
		update_post_meta($pid,'viewed_count',$viewed_count+1);
	if(get_post_meta($pid,'daily_date',true) == date('Y-m-d')){
			update_post_meta($pid,'viewed_count_daily',$viewed_count_daily+1);
		} else {
			update_post_meta($pid,'viewed_count_daily','1');
		}
		update_post_meta($pid,'daily_date',date('Y-m-d'));
	}
}

/*
 * return the count of post view 
 */
if(!function_exists('user_single_post_visit_count')){
function user_single_post_visit_count($pid)
{
	if(get_post_meta($pid,'viewed_count',true))
	{
		return get_post_meta($pid,'viewed_count',true);
	}else
	{
		return '0';	
	}
}
}
/*
 * Function Name:user_single_post_visit_count_daily
 * Argument: Post id
 */
if(!function_exists('user_single_post_visit_count_daily')){
function user_single_post_visit_count_daily($pid)
{
	if(get_post_meta($pid,'viewed_count_daily',true))
	{
		return get_post_meta($pid,'viewed_count_daily',true);
	}else
	{
		return '0';	
	}
}
}
/*
 * add view count display after the content
 */
if( !function_exists('view_count')){
function view_count( $content ) {	
	
	if ( is_single()) 
	{
		global $post;
		$sep =" , ";
		$custom_content='';
		$custom_content.="<p>".__('Visited','templatic')." ".user_single_post_visit_count($post->ID)." ".__('times','templatic');
		$custom_content.= $sep.user_single_post_visit_count_daily($post->ID).__(" Visits today",'templatic')."</p>";
		$custom_content .= $content;
		echo $custom_content;
	} 
}
}
/*
 * show counter and share button after custom fields.
 */
function teamplatic_view_counter()
{
   $settings = get_option( "templatic_settings" );   	
   if(isset($settings['templatic_view_counter']) && $settings['templatic_view_counter']=='Yes')
   {	
		global $post;
		view_counter_single_post($post->ID);
		view_count('');
   }  
   view_sharing_buttons('');
	
}

/*Remove the  the_content filter to add view counter everywhere in single page and add action tmpl_detail_page_custom_fields_collection before the custom field display*/

add_action('tmpl_detail_page_custom_fields_collection','teamplatic_view_counter',5);
function view_sharing_buttons($content)
{
	global $post;	
	if (is_single() && ($post->post_type!='post' && $post->post_type!='page'  && $post->post_type!='product'   && $post->post_type!='product_variation' )) 
	{
		$post_img = bdw_get_images_plugin($post->ID,'thumb');
		$post_images = $post_img[0];
		$title=urlencode($post->post_title);
		$url=urlencode(get_permalink($post->ID));
		$summary=urlencode(htmlspecialchars($post->post_content));
		$image=$post_images;
		$settings = get_option( "templatic_settings" );
		
		if($settings['facebook_share_detail_page'] =='yes' || $settings['google_share_detail_page'] == 'yes' || $settings['twitter_share_detail_page'] == 'yes' || $settings['pintrest_detail_page']=='yes'){
		echo '<div class="share_link">';
			if($settings['facebook_share_detail_page'] == 'yes')
			  {
				?>
				<a onClick="window.open('//www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $title;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&amp;p[images][0]=<?php echo $image;?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)" id="facebook_share_button"><?php _e('Facebook Share.',T_DOMAIN); ?></a>
				<?php
			  }
			if($settings['google_share_detail_page'] == 'yes'): ?>
				<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
				<div class="g-plus" data-action="share" data-annotation="bubble"></div> 
			<?php endif;
			
			if($settings['twitter_share_detail_page'] == 'yes'): ?>
					<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-text='<?php echo htmlentities($post->post_content);?>' data-url="<?php echo get_permalink($post->ID); ?>" data-counturl="<?php echo get_permalink($post->ID); ?>"><?php _e('Tweet',T_DOMAIN); ?></a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			<?php endif;
			
			if(@$settings['pintrest_detail_page']=='yes'):?>
               <!-- Pinterest -->
               <div class="pinterest"> 
                    <a href="//pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;media=<?php echo $image; ?>&amp;description=<?php the_title(); ?>" ><?php _e('Pin It','templatic');?></a>;
                    <script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>                    
               </div>
               <?php endif; 
		echo '</div>';
		}
	}
	return $content;
}

/*
	return the currency code 
*/
function templatic_get_currency_type()
{
	global $wpdb;
	$option_value = get_option('currency_code');
	if($option_value)
	{
		return stripslashes($option_value);
	}else
	{
		return 'USD';
	}
	
}
/* 
	this function returns the currency with position selected in currency settings 
*/
function fetch_currency_with_position($amount,$currency = '')
{
	$amt_display = '';
	if($amount==''){ $amount =0; }
	$decimals=get_option('tmpl_price_num_decimals');
	$decimals=($decimals!='')?$decimals:2;
	if($amount >=0 )
	{
		if(@$amount !='')
			$amount = number_format( (float)($amount),$decimals,'.','');
			$currency = get_option('currency_symbol');
			$position = get_option('currency_pos');
		if($position == '1')
		{
			$amt_display = $currency.$amount;
		}
		else if($position == '2')
		{
			$amt_display = $currency.' '.$amount;
		}
		else if($position == '3')
		{
			$amt_display = $amount.$currency;
		}
		else
		{
			$amt_display = $amount.' '.$currency;
		}
		return apply_filters('tmpl_price_format',$amt_display,$amount,$currency);
	}
}

/* 
	this function returns the currency with position selected in currency settings 
*/
function fetch_currency_with_symbol($amount,$currency = '')
{
	$amt_display = '';
	if($amount==''){ $amount =0; }
	$decimals=get_option('tmpl_price_num_decimals');
	$decimals=($decimals!='')?$decimals:2;
	if($amount >=0 )
	{
		if(@$amount !='')
			$amount = $amount;
			$currency = get_option('currency_symbol');
			$position = get_option('currency_pos');
		if($position == '1')
		{
			$amt_display = $currency.$amount;
		}
		else if($position == '2')
		{
			$amt_display = $currency.' '.$amount;
		}
		else if($position == '3')
		{
			$amt_display = $amount.$currency;
		}
		else
		{
			$amt_display = $amount.' '.$currency;
		}
		return apply_filters('tmpl_price_format',$amt_display,$amount,$currency);
	}
}

/* eof - display currency with position */

/*  this function will display the legends description on email settings page in general settings */
function templatic_legend_notification()
{
	$legend_display = '<div class="tevo_sub_title">'.__('Email shortcodes','templatic-admin').'  </div>';
	$legend_display .= '<p class="tevolution_desc">'.__('Email shortcodes are essentially variables you can use to display dynamic content. Shortcodes are specific to mails. Shortcodes in the mail content can be re-arranged or removed but new shortocdes can not be added from other Email Templates.','templatic-admin').'</p>';
	$legend_display .= '<p style="line-height:30px;width:100%;"><label style="float:left;width:200px;">[#to_name#]</label>'.__('Name of the recipient.','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#site_name#]</label>'.__('Site name as you provided in General Settings','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#site_login_url#]</label>'.__('Site\'s login page URL','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#user_login#]</label>'.__('The users username','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#user_email#]</label>'.__('The users email','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#user_password#]</label>'.__('User password','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#site_login_url_link#]</label>'.__('Login page URL','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#post_link#]</label>'.__('Post permalink','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#post_title#]</label>'.__('Post Title','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#post_type#]</label>'.__('Post Type','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#post_date#]</label>'.__('Date of post','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#information_details#]</label>'.__('Details about the submitted post.','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#transaction_details#]</label>'.__('Transaction details.','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#frnd_subject#]</label>'.__('Subject fields for the "Send to friend" form','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#frnd_comments#]</label>'.__('"Send to Friend" content','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#your_name#]</label>'.__('Sender\'s name','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#submited_information_link#]</label>'.__('URL of the detail page','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#payable_amt#]</label>'.__('Payable amount','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#bank_name#]</label>'.__('Bank name','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#account_number#]</label>'.__('Account number','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#submition_Id#]</label>'.__('Submission ID','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#transection_date#]</label>'.__('Payment date','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#transection_id#]</label>'.__('Transection ID','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#alivedays#]</label>'.__('Alive days','templatic-admin').'<br />
	<label style="float:left;width:200px;">[#days_left#]</label>'.__('Number of days remaining','templatic-admin').'</p><br />';
	
	return $legend_display;
}
/* eof - templatic legends */

/*
	return the currency symbol
*/
function tmpl_fetch_currency(){
	$currency = get_option('currency_symbol');
	if($currency){
		return $currency;
	}else{
		return '$';
	}	
}
/* eof fetch currency*/
/*

	Function through which we sent mail  
*/
function templ_send_email($fromEmail,$fromEmailName,$toEmail,$toEmailName,$subject,$message,$extra='')
{
	
	$fromEmail = apply_filters('templ_send_from_emailid', $fromEmail);
	$fromEmailName = apply_filters('templ_send_from_emailname', $fromEmailName);
	$toEmail = apply_filters('templ_send_to_emailid', $toEmail);
	$toEmailName = apply_filters('templ_send_to_emailname', $toEmailName);
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	/* Additional headers*/
	/*$headers .= 'To: '.$toEmailName.' <'.$toEmail.'>' . "\r\n";*/
	if($fromEmail!="")
	{
		$headers .= 'From: '.$fromEmailName.' <'.$fromEmail.'>' . "\r\n";
	}else	
		$headers .= 'From: '.get_option('admin_email')."\r\n";
		
	$subject = apply_filters('templ_send_email_subject', $subject);
	$message = apply_filters('templ_send_email_content', $message);
	$headers = apply_filters('templ_send_email_headers', $headers);
	
	$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';	
		
	/* Mail it*/
	if(templ_fetch_mail_type())
	{
		@mail($toEmail, $subject, $message, $headers);	
	}else
	{
		wp_mail($toEmail, $subject, $message, $headers);	
	}
	
}
/* EOF - TEMPLATIC SEND EMAIL */
/* 
THIS FUNCTION WILL FETCH THE EMAIL SETTINGS FOR PHP OR WP MAIL */
function templ_fetch_mail_type()
{
	$tmpdata = get_option('templatic_settings');
	if(@$tmpdata['php_mail'] == 'php_mail')
	{
		return true;	
	}
	return false;
}
/* EOF - FETCH MAIL OPTION */

/*
	Update link on author page
 */
function changes_post_update_link($link)
{
	global $post;
	$postid=$post->ID;
	$post_type=$post->post_type;
	$postdate = $post->post_date;
	/*get the submitted page id from post type*/
	$args=array(	
		'post_type' => 'page',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'meta_query' => array(
								array(
									'key' => 'is_tevolution_submit_form',
									'value' => '1',
									'compare' => '='
									),				
								array(
									'key' => 'submit_post_type',
									'value' => $post_type,
									'compare' => '='
									)
								)
			);
	remove_all_actions('posts_where');
	$the_query  = new WP_Query( $args );	
	if( $the_query->have_posts()):
		foreach($the_query as $post):
			if($post->ID != ""):
				$page_id=$post->ID;
			endif;	
		endforeach;
		/*get the front side submitted page id permalink*/
		$page_link=get_permalink($page_id);
		$edit_link = '';
		$review_link = '';
		if(strpos($page_link, "?"))
		{
			$edit_link = $page_link."&pid=".$postid."&action=edit";
			$review_link = $page_link."&pid=".$postid."&renew=1";
			$delete_link = $page_link."&pid=".$postid."&page=preview&action=delete";
		}
		else
		{
			$edit_link = $page_link."?pid=".$postid."&action=edit";
			$review_link = $page_link."?pid=".$postid."&renew=1";
			$delete_link = $page_link."?pid=".$postid."&page=preview&action=delete";
		}
		$exp_days = get_time_difference_plugin( $postdate, $postid);
		$link = '';
		if($exp_days > 0 && $exp_days != '' )
		 {
			$link='<a class="post-edit-link" title="Edit Item" href="'.$edit_link.'" target="_blank">'.__('Edit','templatic').'</a>&nbsp;&nbsp;';
		 }
		else
         {		
			$link.='<a class="post-edit-link" title="Renew Item" href="'.$review_link.'" target="_blank">'.__('Renew','templatic').'</a>&nbsp;&nbsp;';
		 }	
		 $link.='&nbsp;<a class="post-edit-link" title="Delete Item" href="'.$delete_link.'" target="_blank">'.__('Delete','templatic').'</a>&nbsp;&nbsp;';
	endif;
	if(is_author()){
		return $link;
	}
}
/*
 * add filter for changes the edit post link for author wise
 */
add_filter('edit_post_link', 'changes_post_update_link');

/* Get expire days */
function get_time_difference_plugin($start, $pid)
{
  if($start)
	{
		$alive_days = get_post_meta($pid,'alive_days',true);
		$uts['start']      =    strtotime( $start );
		$uts['end']        =    mktime(0,0,0,date('m',strtotime($start)),date('d',strtotime($start))+$alive_days,date('Y',strtotime($start)));
	
		/*$post_days = gregoriantojd(date('m'), date('d'), date('Y')) - gregoriantojd(date('m',strtotime($start)), date('d',strtotime($start)), date('Y',strtotime($start)));*/
		$post_days = (strtotime(date("Y-m-d")) - strtotime(date('Y-m-d',strtotime($start))) ) / (60 * 60 * 24);
		$days = $alive_days-$post_days;
	
		if($days>0)
		{
			return $days;	
		}else{
			return( false );
		}
	}
}
/*
	Enter language details when wp_insert_post in process ( during insert the post )
*/
function wpml_insert_templ_post($last_post_id,$post_type){
	global $wpdb,$sitepress;
	$icl_table = $wpdb->prefix."icl_translations";
	$current_lang_code= ICL_LANGUAGE_CODE;
	$element_type = "post_".$post_type;
	$default_languages = ICL_LANGUAGE_CODE;
	$default_language = $sitepress->get_default_language();
	$trid = $wpdb->get_var($wpdb->prepare("select trid from $icl_table order by trid desc LIMIT %d,%d",0,1));
	
	$update = "update $icl_table set language_code = '".$current_lang_code."' where element_id = '".$last_post_id."'";
	$wpdb->query($update);
}

/*
 * Include wordpress jquery sortable tevolution admin-script in admin side
 */
add_action('admin_head','tevolution_admin_script');
if(!function_exists('tevolution_admin_script')){
	function tevolution_admin_script()
	{	
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_register_script('admin-script',TEMPL_PLUGIN_URL."js/admin-script.js");
		wp_enqueue_script('admin-script');
		$screen = get_current_screen();
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			/*$site_url = icl_get_home_url();*/
			$site_url = get_bloginfo( 'wpurl' )."/wp-admin/admin-ajax.php?lang=".ICL_LANGUAGE_CODE ;
			$tevolutionajaxUrl=TEVOLUTION_PAGE_TEMPLATES_URL.'tevolution-ajax.php?lang='.ICL_LANGUAGE_CODE;
		}else{
			$site_url = get_bloginfo( 'wpurl' )."/wp-admin/admin-ajax.php" ;
			$tevolutionajaxUrl=TEVOLUTION_PAGE_TEMPLATES_URL.'tevolution-ajax.php';
		}
		?>
		
		<script  type="text/javascript" async >
			var ajaxUrl = "<?php echo esc_js( $site_url); ?>";
			var tevolutionajaxUrl = "<?php echo esc_js( $tevolutionajaxUrl); ?>";
		</script>
		 <?php
	}
}
/* Action Edit,renew and delete link on author page */
/*
	display renew, edit and delete link in author page
 */
add_action('templ_show_edit_renew_delete_link', 'tevolution_author_renew_delete_link');
function tevolution_author_renew_delete_link()
{
	global $post,$author_post,$current_user,$wpdb;
	$author_post=$post;	
	$post_author_id=$post->post_author;
	$exp_days='';
	$delete_link='';
	if((is_author() && is_user_logged_in()) && ($current_user->ID==$post_author_id))
	{			
		$link='';
		$title='';
		$postid=$post->ID;
		$post_type=$post->post_type;
		$postdate = $post->post_date;
	
		$transection_db_table_name = $wpdb->prefix.'transactions'; 
		$post_date = $wpdb->get_var("select payment_date from $transection_db_table_name t where post_id = '".$postid."' AND (package_type is NULL OR package_type=0) order by t.trans_id DESC"); /* change it to calculate expired day as per transactions*/
		if(!isset($post_date))
			$post_date =  get_the_date('Y-m-d', $postid);
		/*
		 * Get the posted price package details
		 */
		$package_id=get_post_meta($post->ID,'package_select',true);
		$package_name=get_the_title($package_id);
		$alive_days=get_post_meta($post->ID,'alive_days',true);
		$recurring=get_post_meta($package_id,'recurring',true);
		$billing_num=get_post_meta($package_id,'billing_num',true);
		$billing_per=get_post_meta($package_id,'billing_per',true);
		
		
		$expire_date = date_i18n(get_option('date_format'),strtotime("+$alive_days day", strtotime($post_date)));
		if(function_exists('fetch_currency_with_position'))
		{
			$paid_amount=fetch_currency_with_position(get_post_meta($post->ID,'paid_amount',true));
		}
		echo '<div class="author_price_details">';
		/*if post status is draft than don't show the package information.*/
		if(get_post_status($post->ID) == 'draft')
		{
			echo '<p class="package_expire"><strong>'.__('This listing is not published yet, contact site administrator for more details.','templatic').'</strong></p>';
		}
		else
		{
			if (function_exists('icl_register_string')) {									
				$package_name = icl_t('tevolution-price', 'package-name'.$package_id,$package_name);
			}
			
			echo ($package_id)? '<p class="package_name"><strong>'.__('Package Name','templatic').':</strong> '.$package_name.'</p>' : '';
			echo (get_post_meta($post->ID,'paid_amount',true))? '<p class="package_price"><strong>'.__('Price','templatic').':</strong> '.$paid_amount.'</p>' : '';		
			
			if($recurring==1){
				if($billing_per=='M')
					$billingper='month';
				elseif($billing_per=='D')
					$billingper='day';
				else
					$billingper='year';
					
				$next_billing_date = date_i18n(get_option('date_format'),strtotime("+$billing_num $billingper", strtotime($post_date)));
				echo ($alive_days)? '<p class="package_expire"><strong>'.__('Next Billing will occur on: ','templatic').'</strong>'.$next_billing_date.'</p>' : '';
			}else{
				echo ($alive_days)? '<p class="package_expire"><strong>'.__('Expires On: ','templatic').'</strong>'.$expire_date.'</p>' : '';
			}
		}
		echo "</div>";
		/* Finish Price Package Details */
		
		/*get the submitted page id from post type*/
		$args=array(	
			'post_type' => 'page',
			'post_status' => 'publish',							
			'meta_query' => array(
								array(
									'key' => 'is_tevolution_submit_form',
									'value' => '1',
									'compare' => '='
									),				
								array(
									'key' => 'submit_post_type',
									'value' => $post_type,
									'compare' => '='
									)
								)
				);
				
		$upgradeid = $wpdb->get_var("select ID from $wpdb->posts where post_content like '%[post_upgrade%' and post_type='page' and post_status ='publish' LIMIT 0,1");
		$page_upgrade_link = get_permalink($upgradeid);
		remove_all_actions('posts_where');
		$the_query  = new WP_Query( $args );		
		if( $the_query->have_posts()):
			while($the_query->have_posts()): $the_query->the_post();
				if(get_the_ID()!= ""):
					$page_id=get_the_ID();
					if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') && function_exists('icl_object_id')){
						$page_id = icl_object_id( get_the_ID(), 'page', false, ICL_LANGUAGE_CODE );
						$page_upgrade_link = get_permalink(icl_object_id( $upgradeid, 'page', false, ICL_LANGUAGE_CODE ));
					}
				endif;	
			endwhile;	
			/*get the front side submitted page id permalink*/
			$page_link=get_permalink($page_id);
			$edit_link = '';
			$review_link = '';
			if(strpos($page_link, "?"))
			{
				$edit_link = apply_filters('tevolution_post_edit_link' ,$page_link."&amp;pid=".$postid."&amp;action=edit",$postid,'edit');
				$upgrade_link = apply_filters('tevolution_post_upgrade_link',$page_upgrade_link."&amp;upgpkg=1&amp;pid=".$postid,$postid,'upgrade');
				$review_link = apply_filters('tevolution_post_renew_link',$page_link."&amp;pid=".$postid."&amp;renew=1",$postid,'renew');
				$delete_link = $page_link."&amp;pid=".$postid."&amp;page=preview&amp;action=delete";
			}
			else
			{
				$edit_link = apply_filters('tevolution_post_edit_link' ,$page_link."?pid=".$postid."&amp;action=edit",$postid,'edit');
				$upgrade_link = apply_filters('tevolution_post_upgrade_link',$page_upgrade_link."?pid=".$postid."&amp;upgpkg=1",$postid,'upgrade');;
				$review_link = apply_filters('tevolution_post_renew_link',$page_link."?pid=".$postid."&amp;renew=1",$postid,'renew');
				$delete_link = $page_link."?pid=".$postid."&amp;page=preview&amp;action=delete";
			}
			$exp_days = get_time_difference_plugin( $post_date, $postid);
			$link = '';
			if($exp_days > 0 && $exp_days != '' )
			 {
				$link.='<a class="button secondary_btn tiny_btn post-edit-link" title="Edit Entry" href="'.wp_nonce_url($edit_link,'edit_link').'" target="_blank">'.__('Edit','templatic').'</a>&nbsp;&nbsp;';
				if(is_price_package($current_user->ID,$post_type) > 1)
				{
					$link.='<a class="button secondary_btn tiny_btn post-edit-link" title="Upgrade Package" href="'.wp_nonce_url($upgrade_link,'upgrade_link').'" target="_blank">'.__('Upgrade','templatic').'</a>&nbsp;&nbsp;';
				}
			 }
			else
			 {		
				$link.='<a class="button secondary_btn tiny_btn post-edit-link" title="Renew Entry" href="'.wp_nonce_url($review_link,'renew_link').'" target="_blank">'.__('Renew','templatic').'</a>&nbsp;&nbsp;';
			 }
			 $link.='<a class="button secondary_btn tiny_btn post-edit-link autor_delete_link" data-deleteid="'.$postid.'" title="Delete Entry" href="javascript:void(0);">'.__('Delete','templatic').'</a>&nbsp;&nbsp;';
			 wp_reset_query();
		endif;
		$title.=$link;	
		echo $title;
	}
	$post=$author_post;
 
   do_action('templ_cancel_recurring_payment', $delete_link, $exp_days);
}
/*
 * create a post upgrade page.
 */
add_action('admin_init','tevolution_post_upgrade_insert');
function tevolution_post_upgrade_insert(){
	 global $wpdb,$pagenow;
	 /*Set the Submit listing page */
	 
	if(($pagenow=='plugins.php' || $pagenow=='themes.php') && get_option('tev_post_upgrade_page') !='inserted'){
		 $upgradeid = $wpdb->get_var("select ID from $wpdb->posts where post_content like '%[post_upgrade%' and post_type='page' and post_status ='publish' LIMIT 0,1");
		 if(count($upgradeid) == 0)
		 {
			$my_post = array(
				 'post_title' => 'Upgrade your subscription',
				 'post_content' => "Upgrade the listing in category of your choice. [post_upgrade']",
				 'post_status' => 'publish',
				 'comment_status' => 'closed',
				 'post_author' => 1,
				 'post_name' => 'post-upgrade',
				 'post_type' => "page",
				);
			$post_meta = array(
				'_wp_page_template' => 'default',
				'_edit_last'        => '1',
				
				);
			$post_id = wp_insert_post( $my_post );		
		 }
		 update_option('tev_post_upgrade_page','inserted');
	}
	
	/* auto populate map on detail page */
	$templatic_settings = get_option('templatic_settings');
	if($templatic_settings['direction_map'] !='No' || $templatic_settings['direction_map'] ==''){
		$templatic_data['direction_map'] ='yes';
		update_option('templatic_settings',array_merge($templatic_settings,$templatic_data));
	}
}
/*
 * Function Name: tevolution_listing_after_title
 * Return: display tevolution base templatic page post title like, tevolution archive page, taxonomy, single page and search page
 */
 
add_action('templ_post_title','tevolution_listing_after_title',12);
function tevolution_listing_after_title()
{
	global $post,$htmlvar_name,$posttitle,$wp_query;	
	
	$is_archive = get_query_var('is_ajax_archive');	
	if((is_archive() || $is_archive == 1) || is_tax() || is_search() || is_single()){
		$post_id=get_the_ID();
		$tmpdata = get_option('templatic_settings');
		$total_rating_average=(function_exists('get_post_average_rating')) ? get_post_average_rating($post_id): '';
		if($tmpdata['templatin_rating']=='yes' &&  $total_rating_average!=''):?>
		   <div class="listing_rating">
				<div class="directory_rating_row"><span class="single_rating"> <?php echo draw_rating_star_plugin($total_rating_average);?> </span></div>
		   </div>
	  <?php else:
	  			do_action('show_multi_rating');
	  		endif;
	}
}
/*
 * Function Name: single_post_comment_ratings
 * Return: display the rating start on comment box
 */
add_action('tmpl_before_comments','single_post_comment_ratings',99);
function single_post_comment_ratings()
{
	/* Add ratings after default fields above the comment box, always visible */
     $tmpdata = get_option('templatic_settings');
     if($tmpdata['templatin_rating']=='yes'):
		add_action( 'comment_form_logged_in_after', 'ratings_in_comments' );
		add_action( 'comment_form_after_fields', 'ratings_in_comments' );
		add_action( 'comment_text', 'display_rating_star' );
     endif;	
}
if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php'))
{
	add_action('wp_footer','remove_thoughful_comment_moderate_row',100);
	function remove_thoughful_comment_moderate_row($comments)
	{
		global $post;
		if(get_post_meta($post->ID,'author_moderate',true) != 1)
		{?>
			<script>
				jQuery(document).ready(function() {
					jQuery("p.tc-frontend").remove();
				});
			</script>
		<?php
		}
	}
}
/* fetch comment template while post is publish */
add_action('for_comments','single_post_comment');
function single_post_comment()
{
	global $post;
	 
	if($post->post_status =='publish'){
	?>
		<?php comments_template(); ?>
<?php
	}
}
/*
 * Include the single post image fancybox related script.
 */
add_action('wp_head','single_post_template_head');
function single_post_template_head()
{
	global $current_user,$wpdb,$post,$wp_query;
	/*/fetch the tevolution post type*/
	$custom_post_type=tevolution_get_post_type();
	
	/*by default display visual editor on frontend */
	add_filter( 'wp_default_editor', create_function('', 'return "tinymce";') );
	
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		/*$site_url = icl_get_home_url();*/
		$site_url = get_bloginfo( 'wpurl' )."/wp-admin/admin-ajax.php?lang=".ICL_LANGUAGE_CODE ;
		$tevolutionajaxUrl=TEVOLUTION_PAGE_TEMPLATES_URL.'tevolution-ajax.php?lang='.ICL_LANGUAGE_CODE;
	}else{
		$site_url = get_bloginfo( 'wpurl' )."/wp-admin/admin-ajax.php" ;
		$tevolutionajaxUrl=TEVOLUTION_PAGE_TEMPLATES_URL.'tevolution-ajax.php';
	}
	$a = get_option('recaptcha_options');
	$tmpdata = get_option('templatic_settings');
	$delete_msg =__('Are you really sure want to DELETE this post? Deleted post can not be recovered later.','templatic');
	?>
	
	<script  type="text/javascript" async >
		var ajaxUrl = "<?php echo esc_js( $site_url); ?>";
		var tevolutionajaxUrl = "<?php echo esc_js( $tevolutionajaxUrl); ?>";
		var upload_single_title = "<?php _e("Upload Image",'templatic');?>"; 
		var RecaptchaOptions = { theme : '<?php echo $a['comments_theme']; ?>', lang : '<?php echo $a['recaptcha_language']; ?>', tabindex :'<?php echo $a['comments_tab_index']?>' };
		<?php if(is_author()): ?>
		var delete_auth_post = "<?php echo wp_create_nonce( "auth-delete-post" );?>";
		var currUrl = "<?php echo ( is_ssl() ) ? 'https://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] : 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];?>";
		var delete_confirm="<?php echo $delete_msg;?>";
		var deleting="<?php _e('Deleting.','templatic');?>";
		<?php endif;?>
		var current_user="<?php echo $current_user->ID?>";
		var favourites_sort="<?php echo (isset($_REQUEST['sort']) && $_REQUEST['sort']=='favourites')? 1:'';?>";
		<?php
		if((is_single() )): ?>
		<!--Single Page inquiry from variable -->
		var current_post_id="<?php echo $post->ID;?>";
		var captcha_invalid_msg="<?php _e(CAPTCHA_INVALID,'templatic');?>";
		var fullname_error_msg="<?php _e('Please enter your name','templatic'); ?>";
		var email_error_msg="<?php _e(ENTER_VALID_EMAIL,'templatic');?>";
		var email_balnk_msg="<?php _e('Please enter your email address.','templatic');?>";
		var subject_error_msg="<?php _e(ENTER_SUBJECT_LINE,'templatic');?>";
		var comment_error_msg="<?php _e(ENTER_MESSAGE,'templatic');?>";
		var friendname_error_msg="<?php _e(FRIEND_NAME_VALIDATION,'templatic'); ?>";
		var friendemail_error_msg="<?php _e(FRIEND_EMAIL_VALIDATION,'templatic'); ?>";
		var friend_comment_error_msg="<?php _e(ENTER_COMMENTS,'templatic'); ?>";
		var claim_error_msg="<?php _e(ENTER_CLAIM_MESSAGE,'templatic');?>";
		var already_claimed_msg="<?php _e(apply_filters('tmpl_already_claimed_text','Already Claimed'),'templatic');?>";
		<!--END single page inquiry form variable -->
			
		<?php if($tmpdata['templatin_rating']=='yes'): /* templatic rating enable then define rating related script variable */?>
			var RATING_IMAGE_ON = '<?php echo '<i class="fa fa-star rating-on"></i>' ?>';
			var RATING_IMAGE_OFF = '<?php echo '<i class="fa fa-star rating-off"></i>' ?>';
			var POSTRATINGS_MAX = "<?php echo POSTRATINGS_MAX;?>";
			<?php if($tmpdata['validate_rating'] == 'yes'){?>
			var VALIDATION_MESSAGE = "<?php _e("Please give rating",'templatic');?>";
			var VALIDATION_RATING = 1;
			<?php } else {?>var VALIDATION_RATING = 0; <?php } ?>
			<?php endif;?>
		
		<?php endif; /*finish single page javascript variable condition */
		if(function_exists('get_tevolution_login_permalink')){
			$store_login = "<a href='".get_tevolution_login_permalink()."'>".__('Sign in','templatic')."</a>";
		}?>
		/*check wether payment gateway validattion is statisfied or not*/
		var validate_gateway = true;
		var user_email_error ="<?php _e(EMAIL_EXISTS,'templatic');?>";
		var user_email_verified="<?php _e(EMAIL_CORRECT,'templatic');?>";
		var user_fname_error="<?php _e(USER_EXISTS,'templatic');?>";
		var user_login_link ="<?php _e(' or ','templatic'); echo $store_login?>";
		var user_fname_verified="<?php _e(USER_AVAILABLE,'templatic');?>";
		var user_name_verified='';
		var user_name_error="<?php _e(INCORRECT_USER,'templatic'); ?>";
		var submit_form_error="<?php _e("Please Login before you submit a form.",'templatic'); ?>";
		
		var TWEET="<?php _e('Tweet','templatic');?>";
		var FB_LIKE="<?php _e('Share','templatic');?>";
		var PINT_REST="<?php _e('Pin','templatic'); ?>";
    </script>
    <?php
	wp_enqueue_script('tevolution-jquery-script',TEMPL_PLUGIN_URL.'js/tevolution-script.min.js',array('jquery','jquery-ui-autocomplete'),true); /* include jQuery*/
	
	/*Include payment validation script on single preview page */
	//if(isset($_REQUEST['page']) && $_REQUEST['page']=='preview')
	{
		?>
        <!--<script type="text/javascript" src="<?php echo CUSTOM_FIELDS_URLPATH; ?>js/payment_gateway_validation.js"></script>-->
		<?php
	}
	
	
	/*
	Sorting options for all taxonomies from one page.
	*/
	/* Set the sorting options for tevolution post type */
	global $wp_query;
	
	$currentTaxonomy = get_query_var('taxonomy');
	/*show sorting option for taxonomy page event if there is no listing*/
	if(is_search()){
		$post_type = get_query_var('post_type');	
	}elseif ($currentTaxonomy) {
		$taxObject = get_taxonomy($currentTaxonomy);
		$postTypeArray = $taxObject->object_type;
		$post_type =  $postTypeArray[0];
	}else{
		$post_type = get_post_type();	
	}

	$post_type = (get_post_type()!='')? get_post_type() : $post_type;
	$exclude_post_type = tmpl_addon_name();
	if($post_type=='' || !in_array($post_type,array_keys($exclude_post_type))){ $post_type= apply_filters('tmpl_default_posttype','directory'); }
	if($post_type!=''){
		$post_type = apply_filters('tmpl_tev_sorting_for_'.$post_type,$post_type);
		/* category page */
		add_action($post_type.'_after_subcategory','tmpl_archives_sorting_opt');
		/* archive page*/
                add_action($post_type.'_after_archive_title','tmpl_archives_sorting_opt');
		/*Search Page */
		add_action($post_type.'_after_search_title','tmpl_archives_sorting_opt',11);
		
		/* for default tevolution post type without directory */	
		add_action('templ_after_categories_description','tmpl_archives_sorting_opt');
		add_action('templ_after_archive_title','tmpl_archives_sorting_opt');
	}

	/*Remove author page pre get posts action */
	if(is_author()){
		remove_action('pre_get_posts','tevolution_author_post');
	}
}
/*
 * Function Name: single_post_template_footer
 * Include the single post image fancybox related script.
 */
add_action('wp_footer','single_post_template_footer');
function single_post_template_footer()
{
	global $current_user,$wpdb,$post,$wp_query;
	/*fetch the tevolution post type*/
	$custom_post_type=tevolution_get_post_type();
	?>
	<script>
	<?php 
		/*Single Page javascript variable */
		if((is_single() && in_array(get_post_type(),$custom_post_type)) ): ?>
		var IMAGE_LOADING  = '<?php echo CUSTOM_FIELDS_URLPATH."images/lightbox-ico-loading.gif"; ?>';
		var IMAGE_PREV     = '<?php echo CUSTOM_FIELDS_URLPATH."images/lightbox-btn-prev.gif"; ?>';
		var IMAGE_NEXT     = '<?php echo CUSTOM_FIELDS_URLPATH."images/lightbox-btn-next.gif"; ?>';
		var IMAGE_CLOSE    = '<?php echo CUSTOM_FIELDS_URLPATH."images/lightbox-btn-close.gif"; ?>';
		var IMAGE_BLANK    = '<?php echo CUSTOM_FIELDS_URLPATH."images/lightbox-blank.gif"; ?>';		
		jQuery(function() {
			jQuery('#gallery a').lightBox();
		});
		<?php endif; ?>
	</script>
	<?php
	if((is_single() && in_array(get_post_type(),$custom_post_type)) ){
		?>
		<script  type="text/javascript" src="<?php echo CUSTOM_FIELDS_URLPATH; ?>js/jquery.lightbox.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo CUSTOM_FIELDS_URLPATH; ?>css/jquery.lightbox.css" media="screen" />	
		<?php
	}	
}

/*
 * Function Name: tevolution_submit_form_sidebar
 * Return : submit page sidebar
 */
add_action( 'get_sidebar', 'tevolution_submit_form_sidebar');
function tevolution_submit_form_sidebar($name)
{	
	global $post;	
	if(($name=='primary' || $name=='') && is_page()){
		if(get_post_meta($post->ID,'submit_post_type',true) && get_post_meta($post->ID,'is_tevolution_submit_form',true)){
			
			$post_type=get_post_meta($post->ID,'submit_post_type',true);			
			echo '<aside class="sidebar large-3 small-12 columns" id="sidebar-primary">';
			dynamic_sidebar('add_'.$post_type.'_submit_sidebar');
			echo '</aside>';
		}
		
		/*Call author page side bar in edit profile page */
		$profile_page_id=get_option('tevolution_profile');
		if(function_exists('icl_object_id')){
			$profile_page_id = icl_object_id($profile_page_id, 'page', false);
		}		
		if($profile_page_id==$post->ID &&  is_active_sidebar( 'author-page-sidebar' ) ){
			echo '<aside class="sidebar large-3 small-12 columns" id="sidebar-primary">';
			dynamic_sidebar( 'author-page-sidebar' );
			echo '</aside>';
		}		
	}
}
/*
 * Function Name: tevolution_disable_sidebars
 * Return: disable primary sidebar on submit page
 */
add_filter( 'sidebars_widgets', 'tevolution_disable_sidebars' );
function tevolution_disable_sidebars( $sidebars_widgets ) {	
	
	global $wpdb,$wp_query,$post;
	if (!is_admin() && is_page()) {
		wp_reset_query();
		wp_reset_postdata();
		if(get_post_meta( @$post->ID,'submit_post_type',true) && get_post_meta( @$post->ID,'is_tevolution_submit_form',true))
		{	
			$post_type=get_post_meta($post->ID,'submit_post_type',true);	
			if(!empty($sidebars_widgets['add_'.$post_type.'_submit_sidebar'])){
				$sidebars_widgets['primary'] = false;
				$sidebars_widgets['primary-sidebar'] = false;
			}
		}
		
		/*remove primary side bar on Edit profile page  */
		$profile_page_id=get_option('tevolution_profile');
		if(function_exists('icl_object_id')){
			$profile_page_id = icl_object_id($profile_page_id, 'page', false);
		}		
		if($profile_page_id==$post->ID && !empty($sidebars_widgets['author-page-sidebar'])  ){
			$sidebars_widgets['primary'] = false;
			$sidebars_widgets['primary-sidebar'] = false;
		}
	}
	
	
	
	return $sidebars_widgets;
}
add_action('wp_enqueue_scripts','tevolution_googlemap_script');
add_action('admin_enqueue_scripts','tevolution_googlemap_script');

/*
	Add goggle maps apis and marker manager script for clustering
*/
function tevolution_googlemap_script(){	
	global $post,$pagenow;
	if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; }
	
	/* call google map js on admin only where we need it */
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_style('jquery-ui-css');
	
	?>
	<script>
		var closeimg = '<?php echo apply_filters('tmpl_infobubble_close_btn','https://maps.gstatic.com/intl/en_us/mapfiles/close.gif');?>';
	</script>
	<?php
	$lang = '';
	
	/* Translate google map by language set by WPML
	* set language parameter when wpml is activated and append to google map script as query string
	* Variables : $lang ,value: current language constatne set by WPML
	*/
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') && defined('ICL_LANGUAGE_CODE'))
		$lang = '&amp;language='.ICL_LANGUAGE_CODE;
		
	if((!strstr($_SERVER['REQUEST_URI'],'wp-admin') || ( defined('DOING_AJAX') && DOING_AJAX )) && !is_author()){
		
		wp_enqueue_script( 'google-maps-apiscript', $http.'maps.googleapis.com/maps/api/js?v=3.exp&libraries=places'.$lang,true);
		wp_enqueue_script( 'google-clustering', TEVOLUTION_PAGE_TEMPLATES_URL.'js/markermanager.js',true  );
	}else{
		if($pagenow =='post-new.php' || ($pagenow =='post.php' && isset($_REQUEST['action']) && $_REQUEST['action']=='edit')){
			wp_enqueue_script( 'google-maps-apiscript', $http.'maps.googleapis.com/maps/api/js?v=3.exp&libraries=places'.$lang,true);
			wp_enqueue_script( 'google-clustering', TEVOLUTION_PAGE_TEMPLATES_URL.'js/markermanager.js',true  );
		}
	
	}

	/*
	include font awesome css.
	*/
	/* Register our stylesheet. */
   wp_register_style( 'fontawesomecss', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
   wp_enqueue_style( 'fontawesomecss' );
	
	if (function_exists('tmpl_wp_is_mobile') && tmpl_wp_is_mobile()) {
		
		/* different sequence of parent theme css in child themes */
		if( file_exists(get_stylesheet_directory().'/theme-style.css'))
		{
			wp_enqueue_style( 'tmpl_mobile_view',TEMPL_PLUGIN_URL.'css/style-mobile.css',array('tmpl_dir_css'));

			if(function_exists('supreme_prefix')){
				$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
				if ( isset($supreme2_theme_settings['rtlcss']) && $supreme2_theme_settings['rtlcss']==1 && !is_admin() ) { 
					wp_enqueue_style( 'tmpl_mobile_rtl_view',TEMPL_PLUGIN_URL.'css/style-mobile-rtl.css',array('tmpl_mobile_view'));
				}
			}

		}else{
			wp_enqueue_style( 'tmpl_mobile_view',TEMPL_PLUGIN_URL.'css/style-mobile.css',array('directory-css'));
			if(function_exists('supreme_prefix')){
				$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
				if ( isset($supreme2_theme_settings['rtlcss']) && $supreme2_theme_settings['rtlcss']==1 && !is_admin() ) { 
					wp_enqueue_style( 'tmpl_mobile_rtl_view',TEMPL_PLUGIN_URL.'css/style-mobile-rtl.css',array('tmpl_mobile_view'));
				}
			}
		}
	}
	
	/* include the wordpress comment -reply .js only on detail page */
	if ( !is_single() ){
		wp_dequeue_script('comment-reply');
	}
}
/* Find out the google map folder from our other plugin root */
add_action('init','tevolution_googlemap_support',1);
function tevolution_googlemap_support()
{
	$plugin_folder= trailingslashit(WP_PLUGIN_DIR);
	if($handler = opendir($plugin_folder)) {
	  while (($sub = readdir($handler)) !== FALSE) {
		 if ($sub != "." && $sub != ".." && $sub != "Thumb.db" && $sub != "Thumbs.db") {			 
			if(is_dir($plugin_folder.$sub) && stripos($sub, 'Tevolution')!== false) {
				
				$plugin=explode('Tevolution-',$sub);
	
				if($plugin[0] ==''){
				if(is_plugin_active($sub.'/'.strtolower($plugin[1]).'.php')){
					$google_maps=read_folder_directory($plugin_folder.$sub);
					if(!empty($google_maps)){
						if(file_exists($google_maps.'/google_maps.php')){
							include_once($google_maps.'/google_maps.php');
							break;
						}
					}
				} }
			}
		 }
	  }
	  closedir($handler);
     }
}
/*
	Find out the google_maps Folder inside the tevolution folder
 */
function read_folder_directory($dir)
{
   $listDir = array();
   if($handler = opendir($dir)) {
	  while (($sub = readdir($handler)) !== FALSE) {		 
		 if ($sub != "." && $sub != ".." && $sub != "Thumb.db" && $sub != "Thumbs.db") {
			if(is_dir($dir."/".$sub) && stripos($sub, 'google-maps')!== false){
			    $listDir = $dir."/".$sub;
			}
		 }
	  }
	  closedir($handler);
   }
   return $listDir;
} 
/*
 * Wp_ajax action call for saving email related settings

 * save the email related settings data 
 */
add_action('wp_ajax_nopriv_save_email_data','save_email_data_callback');
add_action('wp_ajax_save_email_data','save_email_data_callback');
function save_email_data_callback(){
	global $wpdb;
	$settings = get_option( "templatic_settings" );
	$a = array();
	foreach($_REQUEST as $key=>$val){
		if(!current_theme_supports('listing_excerpt_setting') && $key=='listing_hide_excerpt')
			continue;
		$settings[$key] = isset($_REQUEST[$key]) ? $_REQUEST[$key] : '';
		$a[$key] = $val;
		if (function_exists('icl_register_string')) {
			icl_register_string('templatic',$key,$val);
		}
	}
	update_option('templatic_settings', $settings);
	echo $b = json_encode($a);
	exit;
}
/*
 * Wp_ajax action call for reset email related settings
 * reset the email related settings data 
 */
add_action('wp_ajax_nopriv_reset_email_data','reset_email_data_callback');
add_action('wp_ajax_reset_email_data','reset_email_data_callback');
function reset_email_data_callback(){
	global $wpdb;
	$settings = get_option( "templatic_settings" );
	$default_subject="";
	$default_msg="";
	
	/**
	* 
	* set default values for email subject
	* 
	**/
	if( @$_REQUEST['subject'] !="" ){
		if( @$_REQUEST['subject']=="mail_friend_sub" ){
			$settings['mail_friend_sub'] = __("Check out this post",'templatic');
		}
		if( @$_REQUEST['subject']=="send_inquirey_email_sub" ){
			$settings['send_inquirey_email_sub'] = __("Inquiry email",'templatic');
		}
		if( @$_REQUEST['subject']=="registration_success_email_subject" ){
			$settings['registration_success_email_subject'] = __("Thank you for registering!",'templatic');
		}
		if( @$_REQUEST['subject']=="admin_registration_success_email_subject" ){
			$settings['admin_registration_success_email_subject'] = __("New user registration",'templatic');
		}
		if( @$_REQUEST['subject']=="post_submited_success_email_subject" ){
			$settings['post_submited_success_email_subject'] = __("A new post has been submitted on your site",'templatic');
		}
		if( @$_REQUEST['subject']=="payment_success_email_subject_to_client" ){
			$settings['payment_success_email_subject_to_client'] = __("Thank you for your submission!",'templatic');
		}
		if( @$_REQUEST['subject']=="payment_success_email_subject_to_admin" ){
			$settings['payment_success_email_subject_to_admin'] = __("You have received a payment",'templatic');
		}
		if( @$_REQUEST['subject']=="pre_payment_success_email_subject_to_admin" ){
			$settings['pre_payment_success_email_subject_to_admin'] = __("Submission pending payment",'templatic');
		}
		if( @$_REQUEST['subject']=="admin_post_upgrade_email_subject" ){
			$settings['admin_post_upgrade_email_subject'] = __("A New Upgrade Request",'templatic');
		}
		if( @$_REQUEST['subject']=="client_post_upgrade_email_subject" ){
			$settings['client_post_upgrade_email_subject'] = __("Payment Pending For Upgrade Request: #[#post_id#]",'templatic');
		}
		if( @$_REQUEST['subject']=="reset_password_subject" ){
			$settings['reset_password_subject'] = __("[#site_title#] Your new password",'templatic');
		}
		if( @$_REQUEST['subject']=="claim_ownership_subject" ){
			$settings['claim_ownership_subject'] = __("New Claim Submitted",'templatic');
		}
		if( @$_REQUEST['subject']=="listing_expiration_subject" ){
			$settings['listing_expiration_subject'] = __("Listing expiration Notification",'templatic');
		}
		if( @$_REQUEST['subject']=="payment_cancelled_subject" ){
			$settings['payment_cancelled_subject'] = __("Payment Cancelled",'templatic');
		}
		if( @$_REQUEST['subject']=="update_listing_notification_subject" ){
			$settings['update_listing_notification_subject'] = __("[#post_type#] ID #[#submition_Id#] has been updated",'templatic');
		}
		if( @$_REQUEST['subject']=="renew_listing_notification_subject" ){
			$settings['renew_listing_notification_subject'] = __("[#post_type#] renew of ID:#[#submition_Id#]",'templatic');
		}
		if( @$_REQUEST['subject']=="pending_listing_notification_subject" ){
			$settings['pending_listing_notification_subject'] = __("Listing payment not confirmed",'templatic');
		}
		
	}
	/**
	* 
	* set default values for email message
	* 
	**/
	if( @$_REQUEST['message'] !="" ){
		if( @$_REQUEST['message']=="mail_friend_description" ){
			$settings['mail_friend_description'] = __("<p>Hey [#to_name#],</p><p>[#frnd_comments#]</p><p>Link: [#post_title#]</p><p>Cheers<br/>[#your_name#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="send_inquirey_email_description" ){
			$settings['send_inquirey_email_description'] = __("<p>Hello [#to_name#],</p><p>This is an inquiry regarding the following post: <b>[#post_title#]</b></p><p>Subject: [#frnd_subject#]</b></p><p>Link: <b>[#post_title#]</b> </p><p>Contact number: [#contact#]</p><p>[#frnd_comments#]</p><p>Thank you,<br />[#your_name#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="registration_success_email_content" ){
			$settings['registration_success_email_content'] = __("<p>Dear [#user_name#],</p><p>Thank you for registering and welcome to [#site_name#]. You can proceed with logging in to your account.</p><p>Login here: [#site_login_url_link#]</p><p>Username: [#user_login#]</p><p>Password: [#user_password#]</p><p>Feel free to change the password after you login for the first time.</p><p>&nbsp;</p><p>Thanks again for signing up at [#site_name#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="admin_registration_success_email_content" ){
			$settings['admin_registration_success_email_content'] = __("<p>Dear admin,</p><p>A new user has registered on your site [#site_name#].</p><p>Login Credentials: [#site_login_url_link#]</p><p>Username: [#user_login#]</p><p>Password: [#user_password#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="post_submited_success_email_content" ){
			$settings['post_submited_success_email_content'] = __('<p>Dear [#to_name#],</p><p>A new submission has been made on your site with the details below.</p><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>','templatic');			
		}
		if( @$_REQUEST['message']=="payment_success_email_content_to_client" ){
			$settings['payment_success_email_content_to_client'] = __("<p>Hello [#to_name#],</p><p>Your submission has been approved! You can see the listing here:</p><p>[#transaction_details#]</p><p>If you'll have any questions about this please send an email to [#admin_email#]</p><p>Thanks!,<br/>[#site_name#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="payment_success_email_content_to_admin" ){
			$settings['payment_success_email_content_to_admin'] = __("<p>Howdy [#to_name#],</p><p>You have received a payment of [#payable_amt#] on [#site_name#]. Details are available below</p><p>[#transaction_details#]</p><p>Thanks,<br/>[#site_name#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="post_added_success_msg_content" ){
			$settings['post_added_success_msg_content'] = '<p>'.__("Thank you! We have successfully received the submitted information.",'templatic').'</p><p>[#submited_information_link#]<p>'.__("Thanks!",'templatic').'<br/> [#site_name#].</p>';			
		}
		if( @$_REQUEST['message']=="post_payment_success_msg_content" ){
			$settings['post_payment_success_msg_content'] = '<h4>'.__("Your payment has been successfully received. The submitted content is now published.",'templatic').'</h4><p><a href="[#submited_information_link#]" >'.__("View your submitted information",'templatic').'</a></p><h5>'.__("Thank you for participating at",'templatic').' [#site_name#].</h5>';			
		}
		if( @$_REQUEST['message']=="post_payment_cancel_msg_content" ){
			$settings['post_payment_cancel_msg_content'] ="<h3>Sorry! Your listing has been canceled due to some reason. To get the details on it, contact us at [#admin_email#].</h3><h5>Thank you for your kind co-operation with [#site_name#]</h5>";			
		}
		if( @$_REQUEST['message']=="post_pre_bank_trasfer_msg_content" ){
			$settings['post_pre_bank_trasfer_msg_content'] = '<p>'.__("To complete the transaction, please transfer ",'templatic').' <b>[#payable_amt#] </b> '.__("to our bank account on the details below.",'templatic').'</p><p>'.__("Bank Name:",'templatic').' <b>[#bank_name#]</b></p><p>'.__("Account Number:",'templatic').' <b>[#account_number#]</b></p><p>'.__("Please include the following number as the reference:",'templatic').'[#submition_Id#]</p><p>[#submited_information_link#] </p><p>'.__("Thank you!",'templatic').'<br/>[#site_name#]</p>';		
		}
		if( @$_REQUEST['message']=="pre_payment_success_email_content_to_admin" ){
			$settings['pre_payment_success_email_content_to_admin'] = __("<p>Dear [#to_name#],</p><p>A payment from username [#user_login#] is now pending on a submission or subscription to one of your plans.</p><p>[#transaction_details#]</p><p>Thanks!<br/>[#site_name#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="contact_us_email_content" ){
			$settings['contact_us_email_content'] = __("<p>Dear [#to_name#] ,</p><p>You have an inquiry message. Here are the details</p><p> Name: [#user_name#] </p> <p> Email: [#user_email#] </p> <p> Message: [#user_message#] </p>",'templatic');			
		}
		if( @$_REQUEST['message']=="admin_post_upgrade_email_content" ){
			$settings['admin_post_upgrade_email_content'] = __("<p>Howdy [#to_name#],</p><pA new upgrade request has been submitted to your site.</p><p>Here are some details about it.</p><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="client_post_upgrade_email_content" ){
			$settings['client_post_upgrade_email_content'] = __("<p>Dear [#to_name#],</p><p>Your [#post_type_name#] has been updated by you . Here is the information about the [#post_type_name#]:</p>[#information_details#]<br><p>[#site_name#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="reset_password_content" ){
			$settings['reset_password_content'] = __("<p>Hi [#to_name#],</p><p>Here is the new password you have requested for your account [#user_email#].</p><p> Login URL: [#login_url#] </p><p>User name: [#user_login#]</p> <p> Password: [#user_password#]</p><p>You may change this password in your profile once you login with the new password above.</p><p>Thanks <br/> [#site_title#] </p>",'templatic');
		}
		if( @$_REQUEST['message']=="claim_ownership_content" ){
			$settings['claim_ownership_content'] = __("<p>Dear admin,</p><p>[#claim_name#] has submitted a claim for the post below.</p><p>[#message#]</p><p>Link: [#post_title#]</p><p>From:  [#your_name#]</p><p>Email: [#claim_email#]<p>Phone Number: [#your_number#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="listing_expiration_content" ){
			$settings['listing_expiration_content'] = __("<p>Dear [#user_login#],<p><p>Your listing -<b>[#post_title#]</b> posted on [#post_date#] and paid on [#transection_date#] for [#alivedays#] days.</p><p>Is going to expire in [#days_left#] day(s). Once the listing expires, it will no longer appear on the site.</p><p> In case you wish to renew this listing, please login to your member area on our site and renew it as soon as it expires. You can login on the following link [#site_login_url_link#].</p><p>Your login ID is <b>[#user_login#]</b> and Email ID is <b>[#user_email#]</b>.</p><p>Thank you,<br />[#site_name#].</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="payment_cancelled_content" ){
			$settings['payment_cancelled_content'] = __("[#post_type#] has been cancelled with transaction id [#transection_id#]",'templatic');			
		}
		if( @$_REQUEST['message']=="update_listing_notification_content" ){
			$settings['update_listing_notification_content'] = __("<p>Dear [#to_name#],</p><p>[#post_type#] ID #[#submition_Id#] has been updated on your site.</p><p>You can review it again by clicking on its title in this email or through your admin dashboard.</p>[#information_details#]<br><p>[#site_name#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="renew_listing_notification_content" ){
			$settings['renew_listing_notification_content'] = __("<p>Dear [#to_name#],</p><p>Your [#post_type#] has been renewed by you . Here is the information about the [#post_type#]:</p><p>[#information_details#]</p><p>[#site_name#]</p>",'templatic');			
		}
		if( @$_REQUEST['message']=="pending_listing_notification_content" ){
			$settings['pending_listing_notification_content'] = __("<p>Hi [#to_name#],<br />A listing request on the below details has been rejected.<p>[#transaction_details#]</p>Please try again later.<br />Thanks you.<br />[#site_name#]</p>",'templatic');			
		}
	}
	/**
	* 
	* Save default setting to database
	* 
	*/
	$settings=apply_filters('tevolution_reset_email_data',$settings);
	update_option('templatic_settings', $settings);	
	$updated_settings = get_option( "templatic_settings" );
	$json_value ="";
	if( @$_REQUEST['subject']!="" ){
		$json_value .='"'.$_REQUEST['subject'].'":"'.$updated_settings[$_REQUEST['subject']].'",';
	}
	if( @$_REQUEST['message']!="" ){
		$json_value .='"'.$_REQUEST['message'].'":"'.addslashes($updated_settings[$_REQUEST['message']]).'"';
	}
	echo '[{'.$json_value.'}]';
	exit;
}
/*
	Display rating on detail page 
*/
add_action('tevolution_display_rating','tevolution_display_rating');
function tevolution_display_rating($post_id){
	/*action to show rating*/
	do_action('show_multi_rating');
	$tmpdata = get_option('templatic_settings');
	if($tmpdata['templatin_rating']=='yes'):
		$total=get_post_total_rating(get_the_ID());
		$total=($total=='')? 0: $total; ?>
			<div class="tevolution_rating">
			<?php if(($total==1)){ ?>
				<div class="tevolution_rating_row"><span class="single_rating"> <?php echo draw_rating_star_plugin(get_post_average_rating(get_the_ID()));?> <span><?php echo $total.' '; echo '<a href="#comments">'; _e('Review','templatic'); echo '</a>'; ?></span></span></div>
			<?php }else{ ?>
				<div class="tevolution_rating_row"><span class="single_rating"> <?php echo draw_rating_star_plugin(get_post_average_rating(get_the_ID()));?> <span><?php echo $total.' '; echo '<a href="#comments">'; _e('Reviews','templatic'); echo '</a>';  ?></span></span></div>
			<?php } ?>
			  </div>
	<?php endif;

}

/**
 * Output an unordered list of checkbox <input> elements labelled
 * with term names. Taxonomy independent version of wp_category_checklist().
 *
 * @since 3.0.0
 *
 * @param int $post_id
 * @param array $args
 
Display the categories check box like wordpress - wp-admin/includes/meta-boxes.php
 */
function tmpl_get_wp_category_checklist_plugin($post_id = 0, $args = array()) {
	global  $cat_array;
 	$defaults = array(
		'descendants_and_self' => 0,
		'selected_cats' => false,
		'popular_cats' => false,
		'walker' => null,
		'taxonomy' => 'category',
		'checked_ontop' => true
	);
	
	if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=""){
		$place_cat_arr = $cat_array;
		$post_id = $post_id;
	}

	$args = apply_filters( 'wp_terms_checklist_args', $args, $post_id );
	$template_post_type = get_post_meta($post->ID,'submit_post_type',true);
	extract( wp_parse_args($args, $defaults), EXTR_SKIP );

	if ( empty($walker) || !is_a($walker, 'Walker') )
		$walker = new Tev_Walker_Category_Checklist_Backend;

	$descendants_and_self = (int) $descendants_and_self;

	$args = array('taxonomy' => $taxonomy);

	$tax = get_taxonomy($taxonomy);
	$args['disabled'] = !current_user_can($tax->cap->assign_terms);
	
	if ( is_array( $selected_cats ) )
		$args['selected_cats'] = $selected_cats;
	elseif ( $post_id )
		$args['selected_cats'] = wp_get_object_terms($post_id, $taxonomy, array_merge($args, array('fields' => 'ids')));
	else
		$args['selected_cats'] = array();

	if ( is_array( $popular_cats ) )
		$args['popular_cats'] = $popular_cats;
	else
		$args['popular_cats'] = get_terms( $taxonomy, array( 'get' => 'all', 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'hierarchical' => false ) );

	if ( $descendants_and_self ) {
		$categories = (array) get_terms($taxonomy, array( 'child_of' => $descendants_and_self, 'hierarchical' => 0, 'hide_empty' => 0 ) );
		$self = get_term( $descendants_and_self, $taxonomy );
		array_unshift( $categories, $self );
	} else {
		$categories = (array) get_terms($taxonomy, array('get' => 'all'));
	}

	if ( $checked_ontop ) {
		/* Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)*/
		$checked_categories = array();
		$keys = array_keys( $categories );
		$c=0;
		foreach( $keys as $k ) {
			if ( in_array( $categories[$k]->term_id, $args['selected_cats'] ) ) {
				$checked_categories[] = $categories[$k];
				unset( $categories[$k] );
			}
		}

		/* Put checked cats on top*/
		echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
	}
	/* Then the rest of them*/

	echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
	if(empty($categories) && empty($checked_categories)){

			echo '<span style="font-size:12px; color:red;">'.sprintf(__('You have not created any category for %s post type. So, this listing will be submited as uncategorized.','templatic'),$template_post_type).'</span>';
	}
}

/**
 * Walker to output an unordered list of category checkbox <input> elements.
 *
 * @see Walker
 * @see wp_category_checklist()
 * @see wp_terms_checklist()
 * @since 2.5.1
 */
class Tev_Walker_Category_Checklist_Backend extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); /*TODO: decouple this*/
    var $selected_cats = array();
	
	
	/**
	 * Starts the list before the elements are added.
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		extract($args);
		if ( empty($taxonomy) )
			$taxonomy = 'category';

		if ( $taxonomy == 'category' )
			$name = 'post_category';
		else
			$name = 'tax_input['.$taxonomy.']';
		
		$selected = array();
		$trm_id = $category->term_id;
		if(!isset($trm_id))
		{
			$trm_id = 'all';			
		}

		if($trm_id){ 
			if(in_array($trm_id,$selected_cats) || in_array('all',$selected_cats))
				{ $checked = "checked=checked"; } 
		}else{  }; 
		/* replace '-' with '_' as it will cause error in valiation in package submit */
		$taxonomy = str_replace('-','_',$taxonomy);
		
		if($category->term_price !=''){ $cprice = "&nbsp;(".fetch_currency_with_position($category->term_price).")"; }else{ $cprice =''; }		
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}' class='{$taxonomy}'>" . '<label for="in-'.$taxonomy.'-' . $category->term_id . '" class="selectit"><input value="' . $category->term_id .'" type="checkbox" name="category[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . $checked .    '/> ' . esc_html( apply_filters('the_category', $category->name )) . $cprice.'</label>';

	}

	function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}

/*
 * Function name: tevolution_success_container_breadcrumb
 * Display breadcrumb on success page
 */
add_action("templ_before_success_container_breadcrumb",'tevolution_success_container_breadcrumb');
function tevolution_success_container_breadcrumb(){
	if(function_exists('supreme_get_settings'))
	{
		if ( current_theme_supports( 'breadcrumb-trail' ) && supreme_get_settings('supreme_show_breadcrumb')){
			breadcrumb_trail( array( 'separator' => '/','after'=>'<span class="sep">/</span> '. __('Added Successfully','templatic')) );
		}
	}elseif(current_theme_supports( 'breadcrumb-trail' ))
	{
		breadcrumb_trail( array( 'separator' => '/','after'=>'<span class="sep">/</span> '. __('Added Successfully','templatic')) );
	}
}


/*
 * This function use in tevolution post type detail page for display flexslider
 */
add_action('wp_footer','tmpl_tevolution_single_slider_script');
function tmpl_tevolution_single_slider_script(){
	/*check action edit for fronted edit  */
	$is_edit=(isset($_REQUEST['action']) && $_REQUEST['action']=='edit')?'1':'';	
	if(is_single() && $is_edit==''){
		global $post;
		$post_id = $post->ID;
		if(get_post_meta($post_id,'_event_id',true)){
			$post_id=get_post_meta($post_id,'_event_id',true);
		}
		$slider_more_listing_img = bdw_get_images_plugin($post_id,'tevolution_thumbnail');
		
		/*get the theme setting option*/
		$supreme2_theme_settings = (function_exists('supreme_prefix')) ? get_option(supreme_prefix().'_theme_settings'):'';
		
		/*Slider div id */
		$silde_gallery_id=apply_filters('tmpl_detail_slider_gallery_id','silde_gallery');
		$slider_id=apply_filters('tmpl_detail_slider_id','slider');
		/*tmpl_detail_slider_options filter hook for change any slider option */
		$slider_options = apply_filters('tmpl_detail_slider_options',array('animation'=>'slide','slideshow'=>'false','direction'=>'horizontal','slideshowSpeed'=>7000,'animationLoop'=>'true','startAt'=> 0,'smoothHeight'=> 'true','easing'=> "swing",'pauseOnHover'=> 'true','video'=> 'true','controlNav'=> 'true','directionNav'=> 'true','prevText'=> '','nextText'=> '','animationLoop'=>'true','itemWidth'=>'60','itemMargin'=>'20')
									  );
		?>
        <script  type="text/javascript" async >
			jQuery(window).load(function()
			{ 
				jQuery('#<?php echo $silde_gallery_id?>').flexslider({
					animation: '<?php echo $slider_options['animation'];?>',
					<?php if(!empty($slider_more_listing_img) && count($slider_more_listing_img) > apply_filters('tmpl_slider_image_count',4)):?>
					controlNav: true,
					directionNav: true,
					prevText: '<?php echo $slider_options['prevText'];?>',
					nextText: '<?php echo $slider_options['nextText'];?>',
					<?php 
					else: ?>
					controlNav: <?php echo $slider_options['controlNav'];?>,
					directionNav: <?php echo $slider_options['directionNav'];?>,
					<?php endif; ?>
					animationLoop: false,
					slideshow: <?php echo $slider_options['slideshow'];?>,
					itemWidth: <?php echo $slider_options['itemWidth'];?>,
					itemMargin: <?php echo $slider_options['itemMargin'];?>,
					<?php if($supreme2_theme_settings['rtlcss'] ==1){ ?>
					rtl: true,
					<?php } ?>
					touch:true,
					asNavFor: '#slider'
				  });
				jQuery('#<?php echo $slider_id;?>').flexslider(
				{
					animation: '<?php echo $slider_options['animation'];?>',
					slideshow: <?php echo $slider_options['slideshow'];?>,
					direction: '<?php echo $slider_options['direction'];?>',
					slideshowSpeed: 7000,
					animationLoop: <?php echo $slider_options['animationLoop'];?>,
					startAt: 0,
					smoothHeight: <?php echo $slider_options['smoothHeight'];?>,
					easing: '<?php echo $slider_options['easing'];?>',
					pauseOnHover: <?php echo $slider_options['pauseOnHover'];?>,
					video: <?php echo $slider_options['video'];?>,
					controlNav: <?php echo $slider_options['controlNav'];?>,
					directionNav: <?php echo $slider_options['directionNav'];?>,	
					touch:true,					
					start: function(slider)
					{
						jQuery('body').removeClass('loading');
					}
					<?php if($supreme2_theme_settings['rtlcss'] ==1){ ?>
					,rtl: true,
					<?php } ?>
				});
			});
			/*FlexSlider: Default Settings*/
		</script>
        <?php
		
	}
}


/* Include foundation js start */
/* 	Add foundation basic .js
	Here with different function because we needs to add in footer with no js conflicts,
	there should not same other script load from plug-in */
add_action( 'wp_footer', 'tmpl_foundation_script',99 );
/* add script in footer*/
if(!function_exists('tmpl_foundation_script')){
	function tmpl_foundation_script(){ 	
	?>
	<script id="tmpl-foundation" src="<?php echo TEMPL_PLUGIN_URL; ?>js/foundation.min.js"> </script>
<?php
	}
}

/* Include foundation js end */

/*
* include currency settings while submit form.
*/
add_action('wp_head','include_currency_script');
function include_currency_script()
{
	global $post;
	if(get_post_meta($post->ID,'is_tevolution_submit_form',true) == 1 || (isset($_GET['upgpkg']) && $_GET['upgpkg'] == 1) || is_tax() || is_archive())
	{
	$num_decimals   = absint( get_option( 'tmpl_price_num_decimals' ) );
	$num_decimals 	= ($num_decimals!='')?$num_decimals:'0';
	$decimal_sep    = wp_specialchars_decode( stripslashes( get_option( 'tmpl_price_decimal_sep' ) ), ENT_QUOTES );
	$decimal_sep 	= ($decimal_sep!='')?$decimal_sep:'.';
	$thousands_sep  = wp_specialchars_decode( stripslashes( get_option( 'tmpl_price_thousand_sep' ) ), ENT_QUOTES );
	$thousands_sep 	= ($thousands_sep!='')?$thousands_sep:',';
	$currency = get_option('currency_symbol');
	$position = get_option('currency_pos');
	?>
    <script>
		var currency = '<?php echo get_option('currency_symbol'); ?>';
		var position = '<?php echo get_option('currency_pos'); ?>';
		var num_decimals    = '<?php echo $num_decimals; ?>';
		var decimal_sep     = '<?php echo $decimal_sep ?>';
		var thousands_sep   = '<?php echo $thousands_sep; ?>';
	</script>
    <?php
	}
}
/* show auto install tab  for tevolution plugins in plugins area*/
if(is_admin()){
	add_action('admin_notices','insert_plugin_data');
}
function insert_plugin_data()
{
	global $pagenow,$wpdb;
	$post_type = tevolution_get_post_type();
	$post_type_key = 0;
	$key = explode(",",apply_filters('post_type_key',$post_type_key));
	if(!empty($post_type) && count(array_intersect($post_type, $key)) > 0 && $pagenow == 'plugins.php' && !get_option('hide_tevolution_ajax_notification'))
	{
		$i = 0;
		$j = 0;
		foreach($post_type as $key)
		{
			if($i == 0 && function_exists('insert_'.$key.'_data') && !get_option( 'hide_'.$key.'_ajax_notification' ))
			{
			?>
				<div class="tevolution-welcome-panel">
					<div class="tmpl-auto-install-yb">
			<?php	
			}
			
			/*call the function for particualr post type insert sample data link*/
			if(function_exists('insert_'.$key.'_data') && !get_option( 'hide_'.$key.'_ajax_notification' ))
			{
				echo '<p>'; call_user_func('insert_'.$key.'_data');echo '</p>';
				?>
				<span id="install-notification-nonce" class="hidden"><?php  echo wp_create_nonce( 'install-notification-nonce' ); ?></span>
				<a style="float:right" class="install-dismiss dismiss-tevolution-autoinstall-notification"  href="javascript:;"><?php _e('Dismiss','templatic-admin'); ?></a>
				<?php
				$j++;
			}
			$i = $j;
		}
		if($j > 0 )
		{
		?>
				</div>
			</div>
		<?php	
		}
	}
}
/*action to hide sample data.*/
add_action( 'wp_ajax_tevolution_hide_autoinstall_notification', 'tevolution_hide_admin_notification' );
function tevolution_hide_admin_notification() {
	if( wp_verify_nonce( $_REQUEST['nonce'], 'install-notification-nonce' ) ) {
		if( update_option( 'hide_tevolution_ajax_notification', true ) ) {
			die( '1' );
		} else {
			die( '0' );
		}
	}
}

/* check page hase the short code for login, registration page */
add_action('wp_ajax_check_page_has_shortcode','check_page_has_shortcode_callback');
function check_page_has_shortcode_callback(){
          
          $shord_code = $_REQUEST['shortcode'];
          $page = get_page($_REQUEST['pageid'] ); 
          if(has_shortcode($page->post_content,$shord_code)){
                  $result['result'] = '1';
          }else{
                  $result['result'] = '0';
          }
          echo json_encode($result);
          exit;
}

/* check required system requirement for templatic themes */
add_filter('tmpl_after_install_delete_button','tmpl_health_check');
if(!function_exists('tmpl_health_check')){
	function tmpl_health_check($dummy_data_msg = ''){

		global $wpdb,$tmpl_requirements;
		ob_start();
		$tmpl_requirements = array('wp_version'=>'4','php_version'=>'5.5','mysql_version'=>'5','curl_enabled'=>true,'gd_installed'=>true);
		
		/* check WP version */
		$wpversion = get_bloginfo('version');
		$wp_version_check = version_compare($tmpl_requirements['wp_version'], $wpversion ); 
		/* returns -1 if the first version is lower than the second, 0 if they are equal, and 1 if the second is lower */
		$required_wp_version = ($wp_version_check == 1) ? 'class="required" style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/un-tick.png) no-repeat left 9px"' : 'style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/tick.png) no-repeat left 6px"';

		/* check PHP version */
		$php_version = phpversion();
		$php_version_check = version_compare($tmpl_requirements['php_version'], $php_version ); 
		/* returns -1 if the first version is lower than the second, 0 if they are equal, and 1 if the second is lower */
		$required_php_version = ($php_version_check == 1) ? 'class="required" style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/un-tick.png) no-repeat left 9px"' : 'style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/tick.png) no-repeat left 6px"';
		
		/* check MYSQL version */	
		$mysql_version = $wpdb->db_version();
		$mysql_version_check = version_compare($tmpl_requirements['mysql_version'], $mysql_version);
		$required_mysql_version = ($mysql_version_check == 1) ? 'class="required" style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/un-tick.png) no-repeat left 9px"' : 'style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/tick.png) no-repeat left 6px"';
		/* returns -1 if the first version is lower than the second, 0 if they are equal, and 1 if the second is lower */	
		
		/* check curl */
		$required_curl = '';
		$curl_enabled = true;
		if(!function_exists('curl_version')){
			$curl_enabled = false;
			$curl_help_text = '( Contact your host provider and request them to enable cURL from php.ini )';
		}
		$required_curl = ($curl_enabled) ? 'style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/tick.png) no-repeat left 6px"' : 'class="required" style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/un-tick.png) no-repeat left 9px"';

		/* check gd library */
		$required_gd = '';$gd_installed = true;	
		if (!extension_loaded('gd') && !function_exists('gd_info')) {
			$gd_help_text = '( Contact your host provider and request them to install GD Libarary )';
			$gd_installed = false;
			
		}
		$required_gd = ($gd_installed) ? 'style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/tick.png) no-repeat left 6px"':'class="required" style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/un-tick.png) no-repeat left 9px"';
		
		/* check gd memcache */
		$required_memcache = '';
		if(!class_exists('Memcache')){
			$is_mem = true;
		}
		$required_memcache = ($is_mem) ? 'class="required" style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/un-tick.png) no-repeat left 9px"' : 'style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/tick.png) no-repeat left 6px"';
		
		/* check sesison */
		session_name('My_SESSION_HUMANS'); /* Create a unique instance of your session variables */
		session_start();
		$sess_id = session_id();
		$is_session_working = true;
		
		if(!empty($sess_id))
		{
			$is_session_working = true;
			$session_check = 'Session is working';
			$required_session = 'style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/tick.png) no-repeat left 6px"';
		}
		else
		{
		  $is_session_working = false;	
		  $session_check = 'OOps session is not working on your server....';
		  $required_session = 'style="background:url('.TEVOLUTION_PAGE_TEMPLATES_URL.'images/un-tick.png) no-repeat left 9px"';
		}
		
		
		if($wp_version_check == 1 || $php_version_check == 1 || $mysql_version_check == 1 || !($is_session_working) || !($gd_installed) || !($curl_enabled))
		{
			echo '<div id="system-notification" class="tmpl-auto-install-yb" ><h4>'.__("System check for theme compatibility",'templatic-admin').' </h4>
			<p>Uh oh, we think some corrections are required at your server to run this theme properly. We recommend talking with your web host to correct the things highlighted in <b class="required">red</b> below.</p>
			<ul id="system_info">
				<li '.$required_wp_version.'>WordPress 4.0 or higher</li>
				<li '.$required_mysql_version.'>MySQL 5 or higher</li>
				<li '.$required_php_version.'>PHP 5.5 or higher</li>
				<li '.$required_curl.'>cURL library support '.@$curl_help_text.'</li>
				<li '.$required_gd.'>GD 2 library support '.@$gd_help_text.'</li>
				<li '.$required_session.'>'.$session_check.'</li>
			</ul>
			
			<p>Settings for this should be straight forward for server guys. Still if you need some assistance, you are welcome to <a href="http://templatic.com/contact/">contact us</a>. </p>
			</div>
			<style type="text/css">
				#system-notification ul#system_info li{
						padding-left: 21px;
				}
			</style>';
		}
		return ob_get_clean();
	}
}		
?>