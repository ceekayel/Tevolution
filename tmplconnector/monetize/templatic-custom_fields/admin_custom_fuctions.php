<?php
/*
 * custom fields realted function called in backend.
 */

add_action('admin_menu', 'tmpl_taxonomy_meta_box');
add_action('save_post', 'tmpl_taxonomy_metabox_insert');
add_action('admin_notices','tmpl_compare_addon_version');
/* show notice if php version is less than 5.3 and other addon version */
function tmpl_compare_addon_version(){
	$error = error_get_last();
	
	$themedata = wp_get_theme();
	$i = 0;

	?>
		<style>.dump_http{ display:none; }</style>
	<?php
	$messaeg = '';
	$phpversion = phpversion();
	$weprefer = 5.3;
	if(version_compare($phpversion,$weprefer,'<')){
		$message1 .= "Your PHP version is not compatible update it to 5.3 or 5.3+";
	}else{
		$message1 ='';
	}
	if(!empty($keys1) && !empty($wp_plugins)){
	
	$message .= "<div id='message' class='tmpl_addon_message updated'>";
	
	$response = wp_remote_get("http://templatic.com/updates/api/index.php?action=package_details");
	if(!empty($response))
	$responde_encode = json_decode($response['body']);
	
	$themename = $themedata->Name;
	$keys1 = $responde_encode->$themename->versions;
	
	
	if(!empty($keys1)){
		foreach($keys1 as $k =>$v){
			$new_tversion =  $k;
		}

		if(version_compare($themedata->Version , $new_tversion, '<')){
			$message .= "<p> Your are using ".$themedata->Name." theme with version ".$themedata->Version." and latest version is <span style='color:red;'>$new_tversion</span> </p>";
		}else{
			$message .= "<p> Your are using ".$themedata->Name." theme with version $new_tversion </p>";
		}
	}
	
	$message .=$message1;
	$i =0;
	$wp_plugins = get_plugins();
	/* get all active plug ins of templatic */
	if(!empty($wp_plugins)){
	foreach ( (array)$wp_plugins as $plugin_file => $plugin_data ) {
		if(is_plugin_active($plugin_file) || is_plugin_active_for_network( $plugin_file )){
			if($plugin_data['Author'] =='Templatic')
			{
				$plugins[$plugin_file] =  $plugin_data;
			}
		}
	}
	}
	$message .= "<ul>";
	foreach($plugins as $key => $val){
		if($responde_encode)
		$keys = $responde_encode->$key->versions;
		if(!empty($keys)){
			foreach($keys as $k =>$v){
				$new_version =  $k;
			}
		}
		if(version_compare($val['Version'], $new_version,'<')){
			$style ="style=color:red;";
			$message .= "<li><span class='tplugin_name'>".$val['Name']."</span> | <span class='tversion'>".$val['Version']."</span> | <span class='tlatest_version' $style>".$new_version."</span></li>";
			$i++;
		}else{
			$style ='';
		}
	}
	$message .= "</ul>";
	$message .= "</div>";
	
	echo $message;
	}
}
/*
* Set Default permalink on theme activation: end
*/
if(isset($_POST['Verify']) && $_POST['Verify'] !=''){
	global $wp_version;
	
	$HTTP_HOST=$_SERVER['HTTP_HOST'];
	$SERVER_ADDR=$_SERVER['SERVER_ADDR'];
	$arg=array('method' => 'POST',
			 'timeout' => 45,
			 'redirection' => 5,
			 'httpversion' => '1.0',
			 'blocking' => true,
			 'headers' => array(),
			 'body' => array( 'licencekey' => $_POST['licencekey'],'action'=>'licensekey_verification','HTTP_HOST'=>$HTTP_HOST,'SERVER_ADDR'=>$SERVER_ADDR),
			 'user-agent' => 'WordPress/'. $wp_version .'; '. home_url(),
			 'cookies' => array()
		);
	$warnning_message='';
	$response = wp_remote_get('http://templatic.com/members/tevolution_api/verification/index.php',$arg );

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
	
}else{
	if(!get_option('templatic_licence_key_')){
		add_action('tevolution_error_message','tevolution_error_message_error');
	}
}

/*
* show error message if tevolution licence key is wrong
*/
$templatic_licence_key = get_option('templatic_licence_key');
if(strstr($templatic_licence_key,'is_supreme') && get_option('templatic_licence_key_') !='' && !$_POST){
	add_action('tevolution_error_message','tevolution_key_is_verified');
}
function tevolution_error_message_host(){
	echo "<span>".__("WP HTTP Error: couldn't connect to host.",ADMINDOMAIN)."</span>";
}
/*
* show error message if tevolution licence key is correct
*/
function tevolution_key_is_verified(){
	echo "<span class='dashicons dashicons-yes'></span>";
}
/* 
* tevolution licence key error message 
*/
function tevolution_error_message_error($message){
	if(isset($_POST['Verify']) && $_POST['Verify'] !=''){
		echo "<span style='color:red;' >"; 
			$error_message=json_decode(get_option('templatic_licence_key'));				
			if($error_message){
				echo base64_decode($error_message->error_message);
			}
		echo "</span>";
	}
	echo "<p>".__('The key can be obtained from Templatic',ADMINDOMAIN)." <a href='http://templatic.com/members/member'>".__('member area',ADMINDOMAIN)."</a></p>";
}
/* 
* tevolution licence key success message 
*/
function tevolution_error_message_success(){
	echo "<span style='color:green;'>";
	$success_message=json_decode(get_option('templatic_licence_key'));	
	if(!empty($success_message))	
		echo base64_decode(@$success_message->success_message);
	echo "</span>";
}
/*
* pop up box for license key on admin side.
*/
add_action('admin_init','tevolution_licensekey_popupbox');
function tevolution_licensekey_popupbox(){
	global $pagenow;	
	if($pagenow=='themes.php' || ($pagenow=='admin.php' && isset($_REQUEST['page']) && $_REQUEST['page']=='templatic_system_menu')){
		$templatic_licence_key=get_option('templatic_licence_key_');
		if(($pagenow=='themes.php' &&  $templatic_licence_key=='') || $templatic_licence_key==''){
			?>
			<div id="boxes" class="licensekey_boxes">
				<div style="top:0px; left: 551.5px; display: none;" id="dialog" class="window">
                    	<span class="close"><a href="#" class="close"><span class="dashicons dashicons-no close-btn"></span></a></span>
					<h2><?php echo __('Licence key',ADMINDOMAIN); ?></h2>
                         <form action="<?php /* echo site_url()."/wp-admin/admin.php?page=templatic_system_menu"; */ ?>" name="" method="post">
                         <div class="inside">
						 <?php
						$templatic_licence_key = get_option('templatic_licence_key');
						if(get_option('templatic_licence_key_') =='' && !$_POST){
						?>
                         <p><?php echo __('Enter the license key in order to unlock the plugin and enable automatic updates.',ADMINDOMAIN); ?></p>
						 <?php } ?>
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

/* Activate add on when run the auto install */
function tmpl_overview_box()
{
	global $wpdb;
	$wp_user_roles_arr = get_option($wpdb->prefix.'user_roles');
	global $wpdb;
	
		$post_counts = $wpdb->get_var("select count(post_id) from $wpdb->postmeta where (meta_key='pt_dummy_content' || meta_key='tl_dummy_content') and meta_value=1");
		$menu_msg1 = '';
		$menu_msg2 = '';
		$menu_msg3 = '';
		$dummy_theme_message = '';
		$dummydata_title = '';
		/* help links */
		$menu_msg1=$dummydata_title=$dummy_theme_message='';
		$menu_msg1 .= "<ul><li><a href='".site_url("/wp-admin/user-new.php")."'>".__('Add listing agents',ADMINDOMAIN)."</a></li>";
		$menu_msg1 .= "<li><a href='".site_url("/wp-admin/admin.php?page=monetization&action=add_package&tab=packages")."'>".__("Set pricing options",ADMINDOMAIN)."</a></li>";
		$menu_msg1 .= "<li><a href='".site_url("/wp-admin/admin.php?page=monetization&tab=payment_options")."'>".__('Setup payment types',ADMINDOMAIN)."</a></li></ul>";

		$menu_msg2 = "<ul><li><a href='".site_url("/wp-admin/admin.php?page=templatic_settings#listing_page_settings")."'>".__('Setup category page',ADMINDOMAIN)."</a> and <a href='".site_url("/wp-admin/admin.php?page=templatic_settings#detail_page_settings")."'>".__('detail page',ADMINDOMAIN)."</a></li>";
		$menu_msg2 .= "<li><a href='".site_url("/wp-admin/admin.php?page=templatic_settings#registration_page_setup")."'>".__('Setup registration',ADMINDOMAIN)."</a> and <a href='".site_url("/wp-admin/admin.php?page=templatic_settings#submit_page_settings")."'>".__('submission page',ADMINDOMAIN)."</a></li>";
		
		$menu_msg2 .= "<li><a href='".site_url("/wp-admin/admin.php?page=templatic_settings&tab=email")."'>".__('Manage and customize emails',ADMINDOMAIN)."</a></li></ul>";
		
		$menu_msg3 = "<ul><li><a href='".site_url("/wp-admin/widgets.php")."'>Manage Widgets </a>,  <a href='".site_url("/wp-admin/customize.php")."'>".__('Add your logo',ADMINDOMAIN)." </a></li>";
		$menu_msg3 .= "<li><a href='".site_url("/wp-admin/customize.php")."'>".__('Change site colors',ADMINDOMAIN)." </a></li>";
		$menu_msg3 .= "<li><a href='".site_url("/wp-admin/nav-menus.php?action=edit")."'>".__('Manage menu navigation',ADMINDOMAIN)."</a></li>";
		
		$my_theme = wp_get_theme();
		$theme_name = $my_theme->get( 'Name' );
		$version = $my_theme->get( 'Version' );
		$dummydata_title .= '<h3 class="twp-act-msg">'.sprintf (__('Thank you. %s is now activated.',ADMINDOMAIN),'Tevolution').'</h3>';
		
		$dummydata_title .=apply_filters('tevoluton_overviewbox_datacontent','');
		/* theme message */	
		$dummy_theme_message .='<div class="tmpl-wp-desc">The Tevolution is ideal for creating and monetizing an online sites. To help you setup the theme, please refer to its <a href="http://templatic.com/docs/tevolution-guide/">Installation Guide</a> to help you better understand the theme&#39;s functions. To help you get started, we have outlined a few recommended steps below to get you going. Should you need any assistance please also visit the Tevolution <a href="http://templatic.com/docs/submit-a-ticket/">helpdesk</a>. </div>';
		
		/* guilde and support link */	
		
		$dummy_nstallation_link  = '<div class="tmpl-ai-btm-links clearfix"><ul><li>Need Help?</li><li><a href="http://templatic.com/docs/tevolution-guide/">Installation Guide</a></li><li><a href="http://templatic.com/docs/submit-a-ticket/">HelpDesk</a></li></ul><p><a href="http://templatic.com">Team Templatic</a> at your service</p></div>';
		if($post_counts>0){
			$theme_name = get_option('stylesheet');
			
			$dummy_data_msg='';
			$dummy_data_msg = $dummydata_title;
			
			$dummy_data_msg .= $dummy_theme_message;
			
			$dummy_data_msg .='<div class="wrapper_templatic_auto_install_col3"><div class="templatic_auto_install_col3"><h4>'.__('Next Steps',ADMINDOMAIN).'</h4>'.$menu_msg1.'</div>';
			$dummy_data_msg .='<div class="templatic_auto_install_col3"><h4>'.__('Advance Options',ADMINDOMAIN).'</h4>'.$menu_msg2.'</div>';
			$dummy_data_msg .='<div class="templatic_auto_install_col3"><h4>'.__('Customize Your Website',ADMINDOMAIN).'</h4>'.$menu_msg3.'</div></div>';
			$dummy_data_msg .='<div class="ref-tev-msg">'.__('Please refer to &quot;Tevolution&quot; and other sections on the left side menu for more of the advanced options.',ADMINDOMAIN).'</div>';
			$dummy_data_msg .= $dummy_nstallation_link;
			
		}else{
			$theme_name = get_option('stylesheet');
			$dummy_data_msg='';
			$dummy_data_msg = $dummydata_title;
			
			
			$dummy_data_msg .= $dummy_theme_message;
			
			$dummy_data_msg .='<div class="wrapper_templatic_auto_install_col3"><div class="templatic_auto_install_col3"><h4>'.__('Next Steps',ADMINDOMAIN).'</h4>'.$menu_msg1.'</div>';
			$dummy_data_msg .='<div class="templatic_auto_install_col3"><h4>'.__('Advance Options',ADMINDOMAIN).'</h4>'.$menu_msg2.'</div>';
			$dummy_data_msg .='<div class="templatic_auto_install_col3"><h4>'.__('Customize Your Website',ADMINDOMAIN).'</h4>'.$menu_msg3.'</div></div>';
			$dummy_data_msg .='<div class="ref-tev-msg">'.__('Please refer to &quot;Tevolution&quot; and other sections on the left side menu for more of the advanced options.',ADMINDOMAIN).'</div>';
			$dummy_data_msg .= $dummy_nstallation_link;
		}
		
		if(isset($_REQUEST['dummy_insert']) && $_REQUEST['dummy_insert']){
			$theme_name = str_replace(' ','',strtolower(wp_get_theme()));
			require_once (get_template_directory().'/library/functions/auto_install/auto_install_data.php');
			
			$args = array(
						'post_type' => 'page',
						'meta_key' => '_wp_page_template',
						'meta_value' => 'page-templates/front-page.php'
						);
			$page_query = new WP_Query($args);
			$front_page_id = $page_query->post->ID;
			update_option('page_on_front',$front_page_id);
			
			/*BEING Cretae primary menu */
			$nav_menus=wp_get_nav_menus( array('orderby' => 'name') );
			$navmenu=array();
			if(!$nav_menus){
				foreach($nav_menus as $menus){
					$navmenu[]=$menus->slug;	
				}
				/*Primary menu */
				if(!in_array('primary',$navmenu)){
					$primary_post_info[] = array('post_title'=>'Home','post_id'   =>$front_page_id,'_menu_item_type'=>'post_type','_menu_item_object'=>'page','menu_item_parent'=>0);
					/*Get submit listing page id */
					$submit_listing_id = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'submit-listing' and $wpdb->posts.post_type = 'page'");
					$primary_post_info[] = array('post_title'=>'','post_id'   =>$submit_listing_id->ID,'_menu_item_type'=>'post_type','_menu_item_object'=>'page','menu_item_parent'=>0);
					/*Insert primary menu */	
					wp_insert_name_menu_auto_install($primary_post_info,'primary');					
					
				}/* end primary nav menu if condition */
				/*Secondary menu */
				if(!in_array('secondary',$navmenu)){
					/*Home Page */
					$secondary_post_info[] = array('post_title'=>'Home','post_id'   =>$front_page_id,'_menu_item_type'=>'post_type','_menu_item_object'=>'page','menu_item_parent'=>0);
					
					/*Get the  listing category list */
					$args = array( 'taxonomy' =>'listingcategory','orderby'=> 'id','order' => 'ASC', );
					$terms = get_terms('listingcategory', $args);
					if($terms){
						$i=0;
						foreach($terms as $term){
							$menu_item_parent=($i!=0)?'1':'0';
							$secondary_post_info[] = array('post_title'=>'','post_content'=>$term->description,'post_id' =>$term->term_id,'_menu_item_type'=>'taxonomy','_menu_item_object'=>'listingcategory','menu_item_parent'=>$menu_item_parent);
							$i++;
						}
					}
					/*finish listingcategory menu */
					/*Get people page id */
					$people_id = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'people' and $wpdb->posts.post_type = 'page'");
					$secondary_post_info[] = array('post_title'=>'','post_id'   =>$people_id->ID,'_menu_item_type'=>'post_type','_menu_item_object'=>'page','menu_item_parent'=>0);
					/*Get all in one map page id */
					$all_in_one_map_id = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'all-in-one-map' and $wpdb->posts.post_type = 'page'");
					$secondary_post_info[] = array('post_title'=>'','post_id'   =>$all_in_one_map_id->ID,'_menu_item_type'=>'post_type','_menu_item_object'=>'page','menu_item_parent'=>0);					
					
					/*Blog menu */
					$args = array( 'taxonomy' =>'category','orderby'=> 'id','order' => 'ASC','exclude'=>array('1'));
					$terms = get_terms('category', $args);
					if($terms){
						$i=0;
						foreach($terms as $term){
							$menu_item_parent=($i!=0)?'1':'0';
							$secondary_post_info[] = array('post_title'=>'','post_content'=>$term->description,'post_id' =>$term->term_id,'_menu_item_type'=>'taxonomy','_menu_item_object'=>'category','menu_item_parent'=>$menu_item_parent);
							$i++;
						}
					}
					/*finish blog menu */
					
					/*Get contact us page id */
					$contact_us_id = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'contact-us' and $wpdb->posts.post_type = 'page'");
					$secondary_post_info[] = array('post_title'=>'','post_id'   =>$contact_us_id->ID,'_menu_item_type'=>'post_type','_menu_item_object'=>'page','menu_item_parent'=>0);					
					/*Insert secondary menu */	
					wp_insert_name_menu_auto_install($secondary_post_info,'secondary');
				}
			}

			/*END primary menu */
			
			wp_redirect(admin_url().'themes.php?x=y');
		}
		if(isset($_REQUEST['dummy']) && $_REQUEST['dummy']=='del'){
			tmpl_delete_dummy_data();
			wp_redirect(admin_url().'themes.php');
		}
		
		define('THEME_ACTIVE_MESSAGE','<div id="ajax-notification" class="welcome-panel tmpl-welcome-panel"><div class="welcome-panel-content">'.$dummy_data_msg.'<span id="ajax-notification-nonce" class="hidden">' . wp_create_nonce( 'ajax-notification-nonce' ) . '</span><a href="javascript:;" id="dismiss-ajax-notification" class="templatic-dismiss" style="float:right">Dismiss</a></div></div>');
		echo THEME_ACTIVE_MESSAGE;
}

/*
	This function will return the custom fields on admin site.
*/
function get_post_admin_custom_fields_templ_plugin($post_types,$category_id='',$taxonomy='',$heading_type='') {
	global $wpdb,$post,$post_custom_field;
	$post_custom_field = $post;
	remove_all_actions('posts_where');
	add_filter('posts_join', 'custom_field_posts_where_filter');
	if($heading_type!='')
	{		
		$args=
		array( 
		'post_type' => 'custom_fields',
		'posts_per_page' => -1	,
		'post_status' => array('publish'),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'post_type_'.$post_types.'',
				'value' => $post_types,
				'compare' => '=',
				'type'=> 'text'
			),
			array(
					'key' => $post_types.'_heading_type',
					'value' =>  htmlspecialchars_decode($heading_type),
					'compare' => '='
			),
			array(
				'key' => 'post_type',
				'value' =>  $post_types,
				'compare' => 'LIKE',
				'type'=> 'text'
			),
			
			array(
				'key' => 'show_on_page',
				'value' =>  array('admin_side','both_side'),
				'compare' => 'IN',
				'type'=> 'text'
			),
			array(
				'key' => 'is_active',
				'value' =>  '1',
				'compare' => '='
			)
		),
		
		'meta_key' => $post_types.'_sort_order',
		'orderby' => 'meta_value_num',
		'meta_value_num'=> $post_types.'_sort_order',
		'order' => 'ASC'
		);
	}else{
		$args=
		array( 
		'post_type' => 'custom_fields',
		'posts_per_page' => -1	,
		'post_status' => array('publish'),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'post_type_'.$post_types.'',
				'value' => $post_types,
				'compare' => '=',
				'type'=> 'text'
			),
			array(
				'key' => 'show_on_page',
				'value' =>  array('admin_side','both_side'),
				'compare' => 'IN',
				'type'=> 'text'
			),
			array(
				'key' => 'post_type',
				'value' =>  $post_types,
				'compare' => 'LIKE',
				'type'=> 'text'
			),
			array(
				'key' => 'is_active',
				'value' =>  '1',
				'compare' => '='
			)
		),
		
		'meta_key' => 'sort_order',
		'orderby' => 'meta_value_num',
		'meta_value_num'=>'sort_order',
		'order' => 'ASC'
		);
	}
	
	$post_query = null;
	$post_query = new WP_Query($args);	
	$post_meta_info = $post_query;
	$return_arr = array();
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			if(get_post_meta($post->ID,"ctype",true)){
				$options = explode(',',get_post_meta($post->ID,"option_values",true));
			}
			$custom_fields = array(
					"name"		=> get_post_meta($post->ID,"htmlvar_name",true),
					"label" 	=> $post->post_title,
					"htmlvar_name" 	=> get_post_meta($post->ID,"htmlvar_name",true),
					"default" 	=> get_post_meta($post->ID,"default_value",true),
					"type" 		=> get_post_meta($post->ID,"ctype",true),
					"desc"      => $post->post_content,
					"option_title" => get_post_meta($post->ID,"option_title",true),
					"option_values" => get_post_meta($post->ID,"option_values",true),
					"is_require"  => get_post_meta($post->ID,"is_require",true),
					"is_active"  => get_post_meta($post->ID,"is_active",true),
					"show_on_listing"  => get_post_meta($post->ID,"show_on_listing",true),
					"show_on_detail"  => get_post_meta($post->ID,"show_on_detail",true),
					"validation_type"  => get_post_meta($post->ID,"validation_type",true),
					"style_class"  => get_post_meta($post->ID,"style_class",true),
					"extra_parameter"  => get_post_meta($post->ID,"extra_parameter",true),
					);
			if($options)
			{
				$custom_fields["options"]=$options;
			}
			$return_arr[get_post_meta($post->ID,"htmlvar_name",true)] = $custom_fields;
		endwhile;
		wp_reset_query();
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	$post = $post_custom_field;
	return $return_arr;
}
/*
	Add the metabox in admin which display the metabox to manager reviews 
*/
function tevolution_comment_status_meta_box($post) {	
?>
<input name="advanced_view" type="hidden" value="1" />
<p class="meta-options">
	<label for="comment_status" class="selectit"><input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($post->comment_status, 'open'); ?> /> <?php echo __( 'Allow reviews.',ADMINDOMAIN ) ?></label><br />
	<label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($post->ping_status, 'open'); ?> /> <?php printf( __( 'Allow <a href="%s" target="_blank">trackbacks and pingbacks</a> on this page.' ,ADMINDOMAIN), __( 'http://codex.wordpress.org/Introduction_to_Blogging#Managing_Comments' ,ADMINDOMAIN) ); ?></label>
	<?php do_action('post_comment_status_meta_box-options', $post); ?>
</p>
<?php
}
/*
 Display the review meta box 
*/
function tevolution_comment_meta_box( $post ) {
	global $wpdb;
	wp_nonce_field( 'get-comments', 'add_comment_nonce', false );
	?>
	<p class="hide-if-no-js" id="add-new-comment"><a href="#commentstatusdiv" onclick="commentReply.addcomment(<?php echo $post->ID; ?>);return false;"><?php echo __('Add reviews',ADMINDOMAIN); ?></a></p>
	<?php
	$total = get_comments( array( 'post_id' => $post->ID, 'number' => 1, 'count' => true ) );
	$wp_list_table = _get_list_table('WP_Post_Comments_List_Table');
	$wp_list_table->display( true );
	if ( 1 > $total ) {
		echo '<p id="no-comments">' . __('No reviews yet.', ADMINDOMAIN) . '</p>';
	} else {
		$hidden = get_hidden_meta_boxes( get_current_screen() );
		if ( ! in_array('commentsdiv', $hidden) ) {
			?>
			<script type="text/javascript">jQuery(document).ready(function(){commentsBox.get(<?php echo $total; ?>, 10);});</script>
			<?php
		}
		?>
		<p class="hide-if-no-js" id="show-comments"><a href="#commentstatusdiv" onclick="commentsBox.get(<?php echo $total; ?>);return false;"><?php echo __('Show reviews',ADMINDOMAIN); ?></a> <span class="spinner"></span></p>
		<?php
	}
	wp_comment_trashnotice();
}


/* 
	Function to add meta boxes in taxonomies BOF
*/
if(!function_exists('tmpl_taxonomy_meta_box')){
	function tmpl_taxonomy_meta_box($post_id) {
		global $pagenow,$post,$post_type_post;			
		/* Tevolution Custom Post Type custom field meta box */
		if($pagenow=='post.php' || $pagenow=='post-new.php'){			
			if(isset($_REQUEST['post_type']) && $_REQUEST['post_type']!=''){
				$posttype=$_REQUEST['post_type'];
			}else{
				$posttype=(get_post_type(@$_REQUEST['post']))? get_post_type($_REQUEST['post']) :'post';
			}
			
			$post_type_post['post']= (array)get_post_type_object( 'post' );			
			$custom_post_types= apply_filters('tmpl_allow_postmetas_posttype',get_option('templatic_custom_post'));
			$custom_post_types=array_merge($custom_post_types,$post_type_post);
			
			foreach($custom_post_types as $post_type => $value){
				if($posttype==$post_type){
				    	remove_meta_box('commentstatusdiv', $post_type, 'normal');
						add_meta_box('commentstatusdiv', __('Review Settings', ADMINDOMAIN), 'tevolution_comment_status_meta_box', $post_type, 'normal', 'low');
					
					if ( ( 'publish' == get_post_status( @$_REQUEST['post'] ) || 'private' == get_post_status( @$_REQUEST['post'] ) ) && post_type_supports($post_type, 'comments') ){
						remove_meta_box('commentsdiv', $post_type, 'normal');
						add_meta_box('commentsdiv', __('Reviews',ADMINDOMAIN), 'tevolution_comment_meta_box', $post_type, 'normal', 'low');
					}
					
					add_filter('posts_join', 'custom_field_posts_where_filter');
					$heading_type=fetch_heading_per_post_type($post_type);
					remove_filter('posts_join', 'custom_field_posts_where_filter');
					$new_post_type_obj = get_post_type_object($post_type);
					$new_menu_name = $new_post_type_obj->labels->menu_name;
					
					foreach($heading_type as $key=>$val){
						$meta_name=(tmplCompFld($val)==tmplCompFld('[#taxonomy_name#]'))? sprintf(__('Basic Informations',ADMINDOMAIN),$new_menu_name) : sprintf(__('%1$s ',ADMINDOMAIN),$val);
						
						if(tmplCompFld($val)== tmplCompFld('Label of Field')){ $meta_name =  __('Other Information',ADMINDOMAIN); }
						$val = apply_filters('tmpl_show_heading_inbackend',$val);
						$pt_metaboxes = get_post_admin_custom_fields_templ_plugin($post_type,'','admin_side',$val);
						
						if($pt_metaboxes ){ 
								add_meta_box('tmpl-settings_'.$key,$meta_name,'tevolution_custom_meta_box_content',$post_type,'normal','high',array( 'post_types' => $post_type,'heading_type'=>$val));
							
						}
					}
					/* Price package Meta Box */
					    global $monetization;
						
							if(is_plugin_active('Tevolution-FieldsMonetization/fields_monetization.php')){
								$pargs = array('post_type' => 'monetization_package','posts_per_page' => -1,'post_status' => array('publish'),'meta_query' => array('relation' => 'AND',
									  array('key' => 'package_post_type',
										   'value' => $post_type,'all',
										   'compare' => 'LIKE'
										   ,'type'=> 'text'),									  
									  array('key' => 'package_status',
										   'value' =>  '1',
										   'compare' => '=')),'orderby' => 'menu_order','order' => 'ASC');
								
							}else{
							$pargs = array('post_type' => 'monetization_package','posts_per_page' => -1,'post_status' => array('publish'),'meta_query' => array('relation' => 'AND',
									  array('key' => 'package_post_type',
										   'value' => $post_type,'all',
										   'compare' => 'LIKE'
										   ,'type'=> 'text'),
									  array('key' => 'package_status',
										   'value' =>  '1',
										   'compare' => '=')),'orderby' => 'menu_order','order' => 'ASC');
							}
							$package_query = new WP_Query($pargs); /* Show price package box only when - price packages are available for that post type in backend */
							if(count($package_query->posts) != 0)
							{
								add_meta_box('tmpl-settings-price-package',__('Price Packages',ADMINDOMAIN),'tevolution_featured_list_fn',$post_type,'normal','high',array( 'post_types' => $post_type));			}
					
					if($post_type!='admanager'){
						add_meta_box( 'tmpl-settings-image-gallery', __( 'Image Gallery', ADMINDOMAIN ), 'tevolution_images_box', $post_type, 'side','',$post );
					}
				}
				
			}
			
		}
		
	}	
}
/*
 * Fetch all images for particular post on backend
 */
function bdw_get_image_gallery_plugin($iPostID,$img_size='thumb',$no_images='') 
{
	if(is_admin() && isset($_REQUEST['author']) && $_REQUEST['author']!=''){
		remove_action('pre_get_posts','tevolution_author_post');
	}
     $arrImages = get_children('order=ASC&orderby=menu_order ID&post_type=attachment&post_mime_type=image&post_parent=' . $iPostID );	
	$counter = 0;
	$return_arr = array();	

	if($arrImages) 
	{
		
	   foreach($arrImages as $key=>$val)
	   {		  
			$id = $val->ID;
			if($val->post_title!="")
			{
				$img_arr = wp_get_attachment_image_src($id, $img_size); 
				$imgarr['id'] = $id;
				$imgarr['file'] = $img_arr[0];
				$return_arr[] = $imgarr;	
			}
	
			$counter++;
			if($no_images!='' && $counter==$no_images)
			{
				break;	
			}
			
	   }
	}	
	return $return_arr;
}
/*
 * gallery box while post add/edit post
 */
function tevolution_images_box($post){
	?>
	<div id="images_gallery_container">
		<ul class="images_gallery">          
			<?php
			
				if(function_exists('bdw_get_images_plugin'))
				{
					$post_image = bdw_get_image_gallery_plugin($post->ID,'thumbnail');					
				}
				$image_gallery='';
				foreach($post_image as $image){					
					echo '<li class="image" data-attachment_id="' . $image['id'] . '">
							' . wp_get_attachment_image( $image['id'], 'thumbnail' ) . '
							<ul class="actions">
								<li><a href="#" id="'.$image['id'].'" class="delete" title="' . __( 'Delete image', DOMAIN ) . '"><i class="dashicons dashicons-no"></i></a></li>
							</ul>
						</li>';
					$image_gallery.=$image['id'].',';	
				}
					
			?>
		</ul>
		<input type="hidden" id="tevolution_image_gallery" name="tevolution_image_gallery" value="<?php echo esc_attr( substr(@$image_gallery,0,-1) ); ?>" />		
	</div>
     <div class="clearfix image_gallery_description">
     <p class="add_tevolution_images hide-if-no-js">
		<a href="#"><?php echo __( 'Add images gallery', ADMINDOMAIN ); ?></a>
	 </p>
     <p class="description"><?php echo __('<b>Note:</b> You cannot directly select the images from the media library, instead you have to upload a new image.',ADMINDOMAIN);?></p>
     </div>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			/* Uploading files */
			var image_gallery_frame;
			var $image_gallery_ids = jQuery('#tevolution_image_gallery');
			var $images_gallery = jQuery('#images_gallery_container ul.images_gallery');
			jQuery('.add_tevolution_images').on( 'click', 'a', function( event ) {
				var $el = $(this);
				var attachment_ids = $image_gallery_ids.val();
				event.preventDefault();
				/* If the media frame already exists, reopen it. */
				if ( image_gallery_frame ) {
					image_gallery_frame.open();
					return;
				}
				/* Create the media frame.  */
				image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
					/* Set the title of the modal.  */
					title: '<?php echo __( 'Add images gallery', ADMINDOMAIN ); ?>',
					button: {
						text: '<?php echo __( 'Add to gallery', ADMINDOMAIN ); ?>',
					},
					multiple: true
				});
				/* When an image is selected, run a callback.  */
				image_gallery_frame.on( 'select', function() {
					var selection = image_gallery_frame.state().get('selection');
					selection.map( function( attachment ) {
						attachment = attachment.toJSON();
						if ( attachment.id ) {
							attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;
							$images_gallery.append('\
								<li class="image" data-attachment_id="' + attachment.id + '">\
									<img src="' + attachment.url + '" />\
									<ul class="actions">\
										<li><a href="#" class="delete" title="<?php echo __( 'Delete image', ADMINDOMAIN ); ?>"><i class="fa fa-times"></i></a></li>\
									</ul>\
								</li>');
						}
					} );
					$image_gallery_ids.val( attachment_ids );
				});
				/* Finally, open the modal. */
				image_gallery_frame.open();
			});
			/* Image ordering */
			$images_gallery.sortable({
				items: 'li.image',
				cursor: 'move',
				scrollSensitivity:40,
				forcePlaceholderSize: true,
				forceHelperSize: false,
				helper: 'clone',
				opacity: 0.65,
				placeholder: 'wc-metabox-sortable-placeholder',
				start:function(event,ui){
					ui.item.css('background-color','#f6f6f6');
				},
				stop:function(event,ui){
					ui.item.removeAttr('style');
				},
				update: function(event, ui) {
					var attachment_ids = '';
					$('#images_gallery_container ul li.image').css('cursor','default').each(function() {
						var attachment_id = jQuery(this).attr( 'data-attachment_id' );
						attachment_ids = attachment_ids + attachment_id + ',';
					});
					$image_gallery_ids.val( attachment_ids );
				}
			});
			/* Remove images */
			jQuery('#images_gallery_container').on( 'click', 'a.delete', function() {
				
				jQuery(this).closest('li.image').remove();
				var attachment_ids = '';
				jQuery('#images_gallery_container ul li.image').css('cursor','default').each(function() {
					var attachment_id = jQuery(this).attr( 'data-attachment_id' );
					attachment_ids = attachment_ids + attachment_id + ',';
				});						
				$image_gallery_ids.val( attachment_ids );
				var delete_id=jQuery(this).closest('li.image ul.actions li a').attr('id');
				if(delete_id!=''){
					jQuery.ajax({
						url:"<?php echo esc_js( get_bloginfo( 'wpurl' ) . '/wp-admin/admin-ajax.php' ); ?>",
						type:'POST',
						data:'action=delete_gallery_image&image_id=' + delete_id,
						success:function(results) {
						}
					});
				}
				return false;
			} );
		});
	</script>
     <?php
}
/* 
	Save the values of fields we set in back-end meta boxes 
*/
if(!function_exists('tmpl_taxonomy_metabox_insert')){
function tmpl_taxonomy_metabox_insert($post_id) {
    global $globals,$wpdb,$post,$monetization;
     
    /*Image Gallery sorting */
    if(isset($_POST['tevolution_image_gallery']) && $_POST['tevolution_image_gallery']!=''){
		$image_gallery=explode(',',$_POST['tevolution_image_gallery']);		
		for($m=0;$m<count($image_gallery);$m++)
		{
			if($image_gallery[$m]!=''){
				$my_post = array();
				$my_post['ID'] = $image_gallery[$m];
				$my_post['menu_order'] = $m;
				wp_update_post( $my_post );
			}
		}
		
		$post_image = bdw_get_images_plugin($post_id,'thumbnail');
    } 
   /* Finish image gallery sorting */
	if(is_templ_wp_admin() && isset($_POST['template_post_type']) && $_POST['template_post_type'] != '')
	{
		update_post_meta(@$_POST['post_ID'], 'template_post_type', @$_POST['template_post_type']);
	}
	if(is_templ_wp_admin() && isset($_POST['map_image_size']))			
		update_post_meta($_POST['post_ID'], 'map_image_size', $_POST['map_image_size']);
	if(is_templ_wp_admin() && isset($_POST['map_width']))			
		update_post_meta($_POST['post_ID'], 'map_width', $_POST['map_width']);
	if(is_templ_wp_admin() && isset($_POST['map_height']))			
		update_post_meta($_POST['post_ID'], 'map_height', $_POST['map_height']);
	if(is_templ_wp_admin() && isset($_POST['map_center_latitude']))			
		update_post_meta($_POST['post_ID'], 'map_center_latitude', $_POST['map_center_latitude']);
	if(is_templ_wp_admin() && isset($_POST['map_center_longitude']))
		update_post_meta($_POST['post_ID'], 'map_center_longitude', $_POST['map_center_longitude']);
	if(is_templ_wp_admin() && isset($_POST['map_type']))
		update_post_meta($_POST['post_ID'], 'map_type', $_POST['map_type']);
	if(is_templ_wp_admin() && isset($_POST['map_display']))
		update_post_meta($_POST['post_ID'], 'map_display', $_POST['map_display']);
	if(is_templ_wp_admin() && isset($_POST['map_zoom_level']))
		update_post_meta($_POST['post_ID'], 'map_zoom_level', $_POST['map_zoom_level']);
	if(is_templ_wp_admin() && isset($_POST['zooming_factor']))
		update_post_meta($_POST['post_ID'], 'zooming_factor', $_POST['zooming_factor']);
	if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
		if(is_templ_wp_admin() && isset($_POST['author_moderate']))
			update_post_meta($_POST['post_ID'], 'author_moderate', $_POST['author_moderate']);
	}	
	/* verify nonce */
    if (!wp_verify_nonce(@$_POST['templatic_meta_box_nonce'], basename(__FILE__)) && !isset($_POST['featured_type']) ) {
       return $post_id;
    }
    /* check autosave */
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    $pt_metaboxes = get_post_admin_custom_fields_templ_plugin($_POST['post_type']);    
    $pID = $_POST['post_ID'];
    $counter = 0;
	
    foreach ($pt_metaboxes as $pt_metabox) { /* On Save.. this gets looped in the header response and saves the values submitted */
	
		if($pt_metabox['type'] == 'text' OR $pt_metabox['type'] == 'oembed_video' OR $pt_metabox['type'] == 'select' OR $pt_metabox['type'] == 'checkbox' OR $pt_metabox['type'] == 'textarea' OR $pt_metabox['type'] == 'radio'  OR $pt_metabox['type'] == 'upload' OR $pt_metabox['type'] == 'date' OR $pt_metabox['type'] == 'multicheckbox' OR $pt_metabox['type'] == 'geo_map' OR $pt_metabox['type'] == 'texteditor' OR $pt_metabox['type'] == 'range_type')
        	{
			
            $var = $pt_metabox["name"];			
			if($pt_metabox['type'] == 'geo_map'){ 
				update_post_meta($pID, 'address', $_POST['address']);
				update_post_meta($pID, 'geo_latitude', $_POST['geo_latitude']);
				update_post_meta($pID, 'geo_longitude', $_POST['geo_longitude']);
			}
			if( get_post_meta( $pID, $pt_metabox["name"] ) == "" )
			{
				add_post_meta($pID, $pt_metabox["name"], $_POST[$var], true );
			}
			elseif($_POST[$var] != get_post_meta($pID, $pt_metabox["name"], true))
			{
				update_post_meta($pID, $pt_metabox["name"], $_POST[$var]);
			}
			elseif($_POST[$var] == "")
			{
				delete_post_meta($pID, $pt_metabox["name"], get_post_meta($pID, $pt_metabox["name"], true));
			}
			else{
				update_post_meta($pID, $pt_metabox["name"], $_POST[$var]);
			}
		} 
    } 
    
    /* Save price package from backend */
    if(isset($_POST['featured_c']) && $_POST['featured_c']!='' && isset($_POST['featured_h']) && $_POST['featured_h']!=''){
		update_post_meta($pID, 'featured_c', 'c');
		update_post_meta($pID, 'featured_h', 'h');
		update_post_meta($pID, 'featured_type', 'both');			
	}elseif(isset($_POST['featured_c']) && $_POST['featured_c']!=''){
		update_post_meta($pID, 'featured_c', 'c');
		update_post_meta($pID, 'featured_type', 'c');
		update_post_meta($pID, 'featured_h', 'n');
	}elseif(isset($_POST['featured_h']) && $_POST['featured_h']!=''){
		update_post_meta($pID, 'featured_h', 'h');
		update_post_meta($pID, 'featured_type', 'h');
		update_post_meta($pID, 'featured_c', 'n');
	}else{
		update_post_meta($pID, 'featured_type', 'none');
		update_post_meta($pID, 'featured_h', 'n');
		update_post_meta($pID, 'featured_c', 'n');
	}
    
     if($_POST['package_select'] && $_POST['package_select']){
		 	update_post_meta($pID, 'package_select', $_POST['package_select']);	
			
			$is_home_featured = get_post_meta($_POST['package_select'],'is_home_featured',true);
			$is_category_featured = get_post_meta($_POST['package_select'],'is_category_featured',true);
		
		
		if($is_category_featured && $is_home_featured)
		{
		
			update_post_meta($pID, 'featured_c', 'c');

			update_post_meta($pID, 'featured_h', 'h');

			update_post_meta($pID, 'featured_type', 'both');	
		
		}

		 elseif($is_category_featured){

			update_post_meta($pID, 'featured_c', 'c');

			update_post_meta($pID, 'featured_type', 'c');

			

		}
		elseif($is_home_featured){

			update_post_meta($pID, 'featured_h', 'h');

			update_post_meta($pID, 'featured_type', 'h');

			

		}
	}
	
	if($_POST['alive_days'] != '' || $_POST['package_select'] != ''){
		
		$listing_price_pkg = $monetization->templ_get_price_info($_POST['package_select'],'');	
		
		/* Insert total amount with featured price */
		$total_amount = 0;

		global $monetization;
		if(class_exists('monetization'))
		{
			$total_amount = $monetization->tmpl_get_payable_amount($_POST['package_select'],$_POST['featured_type'],$_POST['category']);
		}
		

		$alive_days = $listing_price_pkg[0]['alive_days'];
		if(isset($listing_price_pkg[0]['alive_days'])){
			$alive_days = $listing_price_pkg[0]['alive_days'];
		}else{
			$alive_days = 30;
		}
	
		update_post_meta($pID, 'paid_amount', $total_amount);
		update_post_meta($pID, 'alive_days', $alive_days);
		
		/* Insert transaction entry from back end */
		if($pID!=''){
			global $trans_id;
			$transection_db_table_name=$wpdb->prefix.'transactions';
			$post_trans_id  = $wpdb->get_row("select * from $transection_db_table_name where post_id  = '".$pID."' AND (package_type is NULL OR package_type=0)") ;
			if(count($post_trans_id)==0){
				$trans_id = insert_transaction_detail('',$pID);
			}
			
		} 	
	}
    /* Finish price package save form backend */
}
}
/* - Function to add meta boxes EOF - */
/* - Function to fetch the contents in metaboxes BOF - */
if(!function_exists('ptthemes_meta_box_content')){
function tevolution_custom_meta_box_content($post, $metabox ) {
	$heading_type=$metabox['args']['heading_type'];

	$pt_metaboxes = get_post_admin_custom_fields_templ_plugin($metabox['args']['post_types'],'','admin_side',$heading_type);
	$post_id = $post->ID;
    $output = '';
    if($pt_metaboxes){
		if(get_post_meta($post_id,'remote_ip',true)  != ""){
			$remote_ip = get_post_meta($post_id,'remote_ip',true);
		} else {
			$remote_ip= getenv("REMOTE_ADDR");
		}
		if(get_post_meta($post_id,'ip_status',true)  != ""){
			$ip_status = get_post_meta($post_id,'ip_status',true);
		} else {
			$ip_status= '0';
		}
		$geo_latitude= get_post_meta($post_id,'geo_latitude',true);
		$geo_longitude= get_post_meta($post_id,'geo_longitude',true);	
	   echo '<table id="tvolution_fields" style="width:100%"  class="form-table">'."\n";  
	   echo '<input type="hidden" name="templatic_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />
	   <input type="hidden" name="remote_ip" value="'.$remote_ip.'" />
	  
	   <input type="hidden" name="ip_status" value="'.$ip_status.'" />';
	   foreach ($pt_metaboxes as $pt_id => $pt_metabox) {
		if($pt_metabox['type'] == 'text' OR $pt_metabox['type'] == 'select' OR $pt_metabox['type'] == 'radio' OR $pt_metabox['type'] == 'checkbox' OR $pt_metabox['type'] == 'textarea' OR $pt_metabox['type'] == 'upload' OR $pt_metabox['type'] == 'date' OR $pt_metabox['type'] == 'multicheckbox' OR $pt_metabox['type'] == 'texteditor' OR $pt_metabox['type'] == 'range_type'  && $pt_metabox["name"] !='post_content')
				$pt_metaboxvalue = get_post_meta($post_id,$pt_metabox["name"],true);
				$style_class = $pt_metabox['style_class'];
				if (@$pt_metaboxvalue == ""  ) {
					$pt_metaboxvalue = $pt_metabox['default'];
				}
				
				if($pt_metabox['type'] == 'text' || $pt_metabox['type']=='range_type'){
					if($pt_metabox["name"] == 'geo_latitude' || $pt_metabox["name"] == 'geo_longitude') {
						$extra_script = 'onblur="changeMap();"';
					} else {
						$extra_script = '';
					}
					$pt_metaboxvalue =get_post_meta($post_id,$pt_metabox["name"],true);
					$default = $pt_metabox['default'];
					echo  '<tr class="row-'.$pt_id.'">';
					echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label>'."</th>";
					echo "<td>";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_before');
					echo  '<input size="100" class="regular-text pt_input_text" type="'.$pt_metabox['type'].'" value="'.$pt_metaboxvalue.'" name="'.$pt_metabox["name"].'" id="'.$pt_id.'" '.$extra_script.' placeholder="'.$default.'"/>'."\n";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
					echo  '<p class="description">'.$pt_metabox['desc'].'</p>';
					echo '</td></tr>';							  
				}
				
				elseif ($pt_metabox['type'] == 'textarea'){
					$pt_metaboxvalue =get_post_meta($post_id,$pt_metabox["name"],true);
					$default = $pt_metabox['default'];	
					echo '<tr class="row-'.$pt_id.'">';
					echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label></th>';
					echo "<td>";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_before');
					echo  '<textarea class="pt_input_textarea" name="'.$pt_metabox["name"].'" id="'.$pt_id.'" placeholder="'.$default.'">' . $pt_metaboxvalue . '</textarea>';
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
					echo  '<p class="description">'.$pt_metabox['desc'].'</p>';
					echo  "</td></tr>";
								  
				}elseif ($pt_metabox['type'] == 'texteditor'){
					$value =get_post_meta($post_id,$pt_metabox["name"],true);
					$default = $pt_metabox['default'];			
					echo  '<tr class="row-'.$pt_id.'">';
					echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</th>';
					echo "<td>";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_before');					
					/* default settings  */
						$media_pro = apply_filters('tmpl_media_button_pro',false);
						include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
						$name = $pt_metabox["name"];
						/* Wp editor on submit form */
						$settings =   array(
							'wpautop' => false,
							'media_buttons' => $media_pro,
							'textarea_name' => $name,
							'textarea_rows' => apply_filters('tmpl_wp_editor_rows',get_option('default_post_edit_rows',6)), /* rows="..."*/
							'tabindex' => '',
							'editor_css' => '<style>#tmpl-settingsbasic_inf{width:640px;margin-left:0px;}</style>',
							'editor_class' => '',
							'toolbar1'=> 'bold,italic,underline,bullist,numlist,link,unlink,forecolor,undo,redo',
							'editor_height' => '150',
							'teeny' => false,
							'dfw' => false,
							'tinymce' => true,
							'quicktags' => false
						);				
						if(isset($value) && $value != '') 
						{  $content=$value; }
						else{$content= $val['default']; } 				
						wp_editor( stripslashes($content), $name, apply_filters('tmpl_wp_editor_settings',$settings,$name));
					
					
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
					echo  '<p class="description">'.$pt_metabox['desc'].'</p>'."\n";
					echo  '</td></tr>'."\n";								  
				}elseif ($pt_metabox['type'] == 'select'){ 
					echo '<tr class="row-'.$pt_id.'">';
					echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label></th>';
					echo "<td>";
					
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_before');
					echo  '<select class="pt_input_select '.$style_class.'" id="'.$pt_id.'" name="'. $pt_metabox["name"] .'">';
					echo  '<option value="">Select '.$pt_metabox['label'].'</option>';
					if(is_array($pt_metabox['option_values'])){
						$array = $pt_metabox['option_values'];
					}else{
						$array = explode(',',$pt_metabox['option_values']);
					}
					

					if(is_array($pt_metabox['option_title'])){
						$pt_metabox['option_title'] = $pt_metabox['option_title'];
					}else{
						$pt_metabox['option_title'] = explode(',',$pt_metabox['option_title']);
					}
					$array_title = ($pt_metabox['option_title'][0]!='') ? $pt_metabox['option_title']: $array;
					if($array){
						for ($a=0; $a < count($array); $a++ ) {
							$selected = '';
							if($pt_metabox['default'] == $array[$a]){$selected = 'selected="selected"';} 
							if($pt_metaboxvalue == $array[$a]){$selected = 'selected="selected"';}
							echo  '<option value="'. $array[$a] .'" '. $selected .'>' . $array_title[$a] .'</option>';
						}
					}
					echo  '</select><p class="description">'.$pt_metabox['desc'].'</p>'."\n";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
					echo  "</td></tr>";
				}elseif ($pt_metabox['type'] == 'multicheckbox'){
					
						echo  '<tr class="row-'.$pt_id.'">';
						echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label></th>';
						echo "<td>";
						$array = $pt_metabox['options'];							
						$option_title = explode(",",$pt_metabox['option_title']);						
						if($pt_metabox['option_title']== ''){
							$option_title = $array;
						}else{
							$option_title = explode(",",$pt_metabox['option_title']);
						}						
						if($pt_metaboxvalue){
							if(!is_array($pt_metaboxvalue) && strstr($pt_metaboxvalue,','))
							{							
								update_post_meta($post->ID,$pt_metabox['htmlvar_name'],explode(',',$pt_metaboxvalue));
								$pt_metaboxvalue=get_post_meta($post->ID,$pt_metabox['htmlvar_name'],true);
							}	
						}						
						do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_before'); 
						if($array){
							echo "<div class='hr_input_multicheckbox'>";
							$i=1;
							foreach ( $array as $id => $option ) {
							   
								$checked='';
								if(is_array($pt_metaboxvalue)){
								$fval_arr = $pt_metaboxvalue;
								if(in_array($option,$fval_arr)){ $checked='checked=checked';}
								}elseif($pt_metaboxvalue !='' && !is_array($pt_metaboxvalue)){ 
								$fval_arr[] = array($pt_metaboxvalue,'');
								
								if(in_array($option,$fval_arr[0])){ $checked='checked=checked';}
								}else{
								$fval_arr = $pt_metabox['default'];
								if(is_array($fval_arr)){
								if(in_array($option,$fval_arr)){$checked = 'checked=checked';}  }
								}
								echo  "\t\t".'<div class="multicheckbox"><input type="checkbox" '.$checked.' id="multicheckbox_'.$option.'" class="pt_input_radio" value="'.$option.'" name="'. $pt_metabox["name"] .'[]" />  <label for="multicheckbox_'.$option.'">' . $option_title[($i-1)] .'</label></div>'."\n";				$i++;
							}
							echo "</div>";
						}
						do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
						echo  '<p class="description">'.$pt_metabox['desc'].'</p>'."\n";
						echo  '</td></tr>';
				}elseif ($pt_metabox['type'] == 'date'){
					 
					 ?>
					 <script type="text/javascript">	
						jQuery(function(){
						var pickerOpts = {
								showOn: "both",
								dateFormat: 'yy-mm-dd',
								monthNames: objectL11tmpl.monthNames,
								monthNamesShort: objectL11tmpl.monthNamesShort,
								dayNames: objectL11tmpl.dayNames,
								dayNamesShort: objectL11tmpl.dayNamesShort,
								dayNamesMin: objectL11tmpl.dayNamesMin,
								isRTL: objectL11tmpl.isRTL,
								/*buttonImage: "<?php echo TEMPL_PLUGIN_URL; ?>css/datepicker/images/cal.png", */
								buttonText: '<i class="fa fa-calendar"></i>',
							};	
							jQuery("#<?php echo $pt_metabox["name"];?>").datepicker(pickerOpts);
						});
					</script>
					 <?php
					$pt_metaboxvalue =get_post_meta($post_id,$pt_metabox["name"],true);
					$default = $pt_metabox['default'];					
					echo  '<tr class="row-'.$style_class.'">';
					echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label></th>';
					echo "<td>";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_before');
					echo  '<input size="40" class="pt_input_text" type="text" readonly="readonly" value="'.$pt_metaboxvalue.'" id="'.$pt_metabox["name"].'" name="'.$pt_metabox["name"].'" placeholder="'.$default.'"/>';
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
					
					echo  '<p class="description">'.$pt_metabox['desc'].'</p>';
					echo  '</td></tr>';
								  
				}elseif ($pt_metabox['type'] == 'radio'){
						echo  '<tr class="row-'.$style_class.'">';
						echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label></th>';
						$array = $pt_metabox['options'];
						$option_title = explode(",",$pt_metabox['option_title']);
						
						if($pt_metabox['option_title']== ''){
							$option_title = $array;
						}else{
							$option_title = explode(",",$pt_metabox['option_title']);
						}
			
				
						echo '<td>';
						do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_before'); 
						$i=1;
						
						if($array){
							echo '<ul class="hr_input_radio">';
							foreach ( $array as $id => $option ) {
							   $checked='';
							   if($pt_metabox['default'] == $option){$checked = 'checked="checked"';} 
								if(trim($pt_metaboxvalue) == trim($option)){$checked = 'checked="checked"';}
								$event_type = array("Regular event", "Recurring event");
								if($pt_metabox["name"] == 'event_type'):
									if (trim(@$value) == trim(@$event_type[$i])){ $seled="checked=checked";}									
									echo  '<li><input type="radio" '.$checked.' class="pt_input_radio" value="'.$event_type[($i-1)].'" name="'. $pt_metabox["name"] .'" id="'. $pt_metabox["name"].'_'.$i .'" />   <label for="'. $pt_metabox["name"].'_'.$i .'">' . $option_title[($i-1)] .'<label></li>';
								else:
									echo  '<li><input type="radio" '.$checked.' class="pt_input_radio" value="'.$option.'" name="'. $pt_metabox["name"] .'" id="'. $pt_metabox["name"].'_'.$i .'" />  <label for="'. $pt_metabox["name"].'_'.$i .'">' . $option_title[($i-1)] .'</label></li>';
								endif;
								$i++;
							}
							
							echo '</ul>';
						}
						do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
						echo  '<p class="description">'.$pt_metabox['desc'].'</p>'."\n";
						echo "</td>";
						echo  '</tr>';
				}
				elseif ($pt_metabox['type'] == 'checkbox'){
					if($pt_metaboxvalue == '1') { $checked = 'checked="checked"';} else {$checked='';}
					echo  "<tr>";
					echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label></th>';
					echo "<td>";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_before');
					/*echo  '<p class="value"><input type="checkbox" '.$checked.' class="pt_input_checkbox"  id="'.$pt_id.'" value="1" name="'. $pt_metabox["name"] .'" /></p>'; */
					echo  '<p class="value"><input id="'. $pt_metabox["name"] .'" type="text" size="36" name="'.$pt_metabox["name"].'" value="'.$pt_metaboxvalue.'" />';
	                echo  '<input id="'. $pt_metabox["name"] .'_button" type="button" value="Browse" /></p>';
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
					echo  '<p class="description">'.$pt_metabox['desc'].'</p>'."\n";
					echo  '</td></tr>'."\n";
				}elseif ($pt_metabox['type'] == 'upload'){
					/* html for image upload for submit form backend end */
					 $pt_metaboxvalue = get_post_meta($post->ID,$pt_metabox["name"],true);
					
						$up_class="upload ".$pt_metaboxvalue;
						echo  '<tr>';
			
						echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label></th>';
						/* echo  '<td><input type="file" class="'.$up_class.'"  id="'. $pt_metabox["name"] .'" name="'. $pt_metabox["name"] .'" value="'.$pt_metaboxvalue.'"/>'; */
						echo  '<td><input id="'. $pt_metabox["name"] .'" type="hidden" size="36" name="'.$pt_metabox["name"].'" value="'.$pt_metaboxvalue.'" />';
		                ?><div class="upload_box">
							<div class="hide_drag_option_ie">
                                <p><?php echo __('You can drag &amp; drop images from your computer to this box.',DOMAIN); ?></p>
                                <p><?php echo __('OR',DOMAIN); ?></p>
                             </div>
                             <?php 
						echo '<div class="tmpl_single_uploader">';
						do_action('tmpl_custom_fields_'.$name.'_before');
						$wp_upload_dir = wp_upload_dir();?>
						
						
		                	<div id="<?php echo $pt_metabox["name"]; ?>"></div>
							<div id="fancy-contact-form">
							<div class="dz-default dz-message" ><span  id="fancy-<?php echo $pt_metabox["name"]; ?>"><span><i class="fa fa-folder"></i>  <?php _e('Upload Image',DOMAIN); ?></span></span></div>
							<span class="default-img-uploaded" id="image-<?php echo $pt_metabox["name"]; ?>">
							<?php
								$dirinfo = wp_upload_dir();
								$path = $dirinfo['path'];
								$url = $dirinfo['url'];
								$extention = tev_findexts(get_post_meta($post->ID,$pt_metabox["name"], $single = true));
								$img_type = array('png','gif','jpg','jpeg','ico');
								if(in_array($extention,$img_type))
									echo '<br/><img id="img_'.$pt_metabox["name"].'" src="'.get_post_meta($post->ID,$pt_metabox["name"], $single = true).'" border="0" class="company_logo" height="140" width="140" />';
							?><?php if($pt_metaboxvalue != ''){?><span class="ajax-file-upload-red" onclick="delete_image('<?php echo basename($pt_metaboxvalue);?>','<?php echo $pt_metabox["name"]; ?>')"><?php echo __('Delete',ADMINDOMAIN); ?></span> <?php } ?></span>
							</div>
						<script>
							var image_thumb_src = '<?php echo  $wp_upload_dir['url'];?>/';
							jQuery(document).ready(function(){
								var settings = {
									url: '<?php echo plugin_dir_url( __FILE__ ); ?>single-upload.php',
									dragDrop:true,
									fileName: "<?php echo $pt_metabox["name"]; ?>",
									allowedTypes:"jpeg,jpg,png,gif,doc,pdf,zip",
									returnType:"json",
									multiple:false,
									showDone:false,
									showAbort:false,
									showProgress:true,
									onSuccess:function(files,data,xhr)
									{
										jQuery('#image-<?php echo $pt_metabox["name"]; ?>').html('');
										if(jQuery('#img_<?php echo $pt_metabox["name"]; ?>').length > 0)
										{
											jQuery('#img_<?php echo $pt_metabox["name"]; ?>').remove();
										}
									    var img = jQuery('<img height="60px" width="60px" id="img_<?php echo $pt_metabox["name"]; ?>">');
									    data = data+'';
										var id_name = data.split('.'); 
										var img_name = '<?php echo bloginfo('template_url')."/images/tmp/"; ?>'+id_name[0]+"."+id_name[1];
										
										img.attr('src', img_name);
										img.appendTo('#image-<?php echo $pt_metabox["name"]; ?>');
										jQuery('#image-<?php echo $pt_metabox["name"]; ?>').css('display','');
										jQuery('#<?php echo $pt_metabox["name"]; ?>').val(image_thumb_src+data);
										jQuery('.ajax-file-upload-filename').css('display','none');
										jQuery('.ajax-file-upload-red').css('display','none');
										jQuery('.ajax-file-upload-progress').css('display','none');
										
									},
									showDelete:true,
									deleteCallback: function(data,pd)
									{
										for(var i=0;i<data.length;i++)
										{
											jQuery.post("<?php echo plugin_dir_url( __FILE__ ); ?>delete_image.php",{op:"delete",name:data[i]},
										function(resp, textStatus, jqXHR)
										{
											/*Show Message  */
											jQuery('#image-<?php echo $pt_metabox["name"]; ?>').html("<div>File Deleted</div>");
											jQuery('#<?php echo $pt_metabox["name"]; ?>').val('');	 
										});
									 }      
									pd.statusbar.hide(); /* You choice to hide/not. */

								}
								}
								var uploadObj = jQuery("#fancy-"+'<?php echo $pt_metabox["name"]; ?>').uploadFile(settings);
							});
							function delete_image(name,field_name)
							{
								jQuery.ajax({
									 url: '<?php echo TEMPL_PLUGIN_URL; ?>tmplconnector/monetize/templatic-custom_fields/delete_image.php?op=delete&name='+name,
									 type: 'POST',
									 success:function(result){
									 	jQuery('#image-'+field_name).html("<div>File Deleted</div>");
										jQuery('#'+field_name).val('');			
									}				 
								 });
							}
						</script>
						<?php
						echo '</div>';
						echo  '<p class="description">'.$pt_metabox['desc'].' </p>';
						echo  '</div></td></tr>';
						
				}elseif($pt_metabox['type'] == 'oembed_video'){
					$pt_metaboxvalue =get_post_meta($post_id,$pt_metabox["name"],true);
					$default = $pt_metabox['default'];
					echo  '<tr>';
					echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label>'."</th>";
					echo "<td>";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_before');
					echo  "<input size='100' class='regular-text pt_input_text' type='".$pt_metabox['type']."' value='".$pt_metaboxvalue."' name='".$pt_metabox["name"]."' id='".$pt_id."' ".$extra_script." placeholder='".$default."'/>"."\n";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
					echo  '<p class="description">'.$pt_metabox['desc'].'</p>';
					echo '</td></tr>';
				}else {
					if($pt_metabox['type'] == 'geo_map'){
						echo  '<tr>';
						echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label>'."</th>";
						echo '<td colspan=2 id="tvolution_map">';
						include_once(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/location_add_map.php");
						if(@$admin_desc):
							echo '<p class="description">'.$admin_desc.'</p>'."\n";
						else:
							echo '<p class="description">'.@$GET_MAP_MSG.'</p>'."\n";
						endif;
	
						 echo  '</td> </tr>';
					}else{
						do_action('tevolution_backend_custom_fieldtype',$pt_id,$pt_metabox,$post);
					}
				}
			}
		
		global $post_type;
		
		echo "</tbody>";
		echo "</table>";
	}else{
		echo __("No custom fields was inserted for this post type.",ADMINDOMAIN)."<a href='".site_url('wp-admin/admin.php?page=custom_setup&ctab=custom_fields')."'> ".__('Click Here',ADMINDOMAIN)." </a> ".__('to add fields for this post.',ADMINDOMAIN);
	}
}
}
/* action to add option of featured listing in add listing page in wp-admin */
function tevolution_featured_list_fn($post_id){
	global $post;
	$post_id = $post->ID;
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
	global $monetization;
	
	if(get_post_meta($post_id,'featured_type',true) == "h"){ $checked = "checked=checked"; }
	elseif(get_post_meta($post_id,'featured_type',true) == "c"){ $checked1 = "checked=checked"; }
	elseif(get_post_meta($post_id,'featured_type',true) == "both"){ $checked2 = "checked=checked"; }
	elseif(get_post_meta($post_id,'featured_type',true) == "none"){ $checked3 = "checked=checked"; }
	else { $checked = ""; }
	if(get_post_meta($post_id,'alive_days',true) != '')
	{
		$alive_days = get_post_meta($post_id,'alive_days',true);	 
	}

	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));
	$taxonomy = $taxonomies[0];
	global $monetization;
	echo "<table id='tvolution_price_package_fields' class='form-table'>";
	echo "<tbody>";
	echo '<tr>';
	echo  '<th valign="top"><label for="alive_days">'.__('Price Package',ADMINDOMAIN).'</label></th>';
	echo  '<td>';
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));				
	$post_categories = get_the_terms( $post_id ,$taxonomies[0]);
	$post_cat = '';
	if(!empty($post_categories))
	{
		foreach($post_categories as $post_category){
			$post_cat.=$post_category->term_id.',';		
		}
	}
	$post_cat=substr(@$post_cat,0,-1);	
	$pkg_id = get_post_meta($post_id,'package_select',true); /* user comes to edit fetch selected package */
	$monetization->fetch_monetization_packages_back_end($pkg_id,'all_packages',$post->post_type,$taxonomy,$post_cat); /* call this function to fetch price packages which have to show even no categories selected */
	/* call this function to display featured packages */
	echo  '<input type="hidden" value="'.$alive_days.'" class="regular-text pt_input_text" name="alive_days" id="alive_days" size="100" />';
	echo '</td>';
	echo '</tr>';
	echo "</tbody>";
	echo "</table>";
}

/* add  default menu icons */
add_action( 'admin_init','tevolution_custom_menu_class' );
function tevolution_custom_menu_class() 
{
    global $menu;
	if(get_option('update_tax_icon') ==''){
		$tevolution_post=get_option('templatic_custom_post');
		foreach($tevolution_post as $key=>$value){
			if($key!="" && !$tevolution_post[$key]['menu_icon'])
			{
				$tevolution_post[$key]['menu_icon']=	TEMPL_PLUGIN_URL.'images/templatic-logo.png';
			}
		}
		update_option('templatic_custom_post',$tevolution_post);
		update_option('update_tax_icon','done');
	}
}

/*script to check htmlvar name is uniue or not*/
add_action('admin_footer','tmpl_htmlvar_name_validation');

function tmpl_htmlvar_name_validation(){
	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('#htmlvar_name').blur(function(){ 
				var htmlvar_name = jQuery("#htmlvar_name").val();
				jQuery.ajax({
					url:ajaxUrl,
					type:'POST',
					data:"action=check_htmlvar_name&htmlvar_name="+htmlvar_name+"&page=custom_setup&is_ajax=1",
					success:function(results) {
						if(jQuery("#tmpl_html_error").length <= 0 && results == 'yes'){
							jQuery("#htmlvar_name").after('<p id="tmpl_html_error" class="error">'+'<?php echo __('This variable name already exists, please enter a unique name.',DOMAIN); ?>'+'</p>');
							jQuery('#html_var_name').addClass('form-invalid');
							var flag = 0;
							jQuery("#tmpl_html_error").after('<input type="hidden" name="is_valid_html" id="is_valid_html" value="yes" />');
						}else{
							jQuery("#tmpl_html_error").remove();
							jQuery("#is_valid_html").remove();
							jQuery('#html_var_name').removeClass('form-invalid');
							var flag = 1;
						}
					}
				});
			});
		});
	</script>
	<?php
}


/* function to call AJAX to check the html variable name is exists or not */

add_action( 'wp_ajax_check_htmlvar_name', 'tmpl_check_check_htmlvar_name' );
if( !function_exists( 'tmpl_check_check_htmlvar_name' ) ){
	function tmpl_check_check_htmlvar_name(){
		global $wp_query;
		/* check if same html variable name is available */
		$args=array('post_type'=> 'custom_fields','posts_per_page'=> 1,
					'meta_query'=> array('relation' => 'AND',
							array('key' => 'htmlvar_name','value' => $_REQUEST['htmlvar_name'],'compare' => '=')
						),
			);
		$wp_query = new WP_Query($args);
	
		if(have_posts()){
			echo 'yes';
			die();
		}else{
			echo 'no';
			die();
		}
	}
}
/* delete image from gallery meta box */
add_action('wp_ajax_delete_gallery_image','delete_gallery_image');
function delete_gallery_image(){
	wp_delete_post($_REQUEST['image_id'],true);
	echo '1';
	exit;
}
/* action called while we change transaction status from backend. */
add_action('admin_init','tev_transaction_msg');
function tev_transaction_msg()
{
	
	add_action('tevolution_transaction_msg','tevolution_transaction_msg_fn');
	add_action('tevolution_transaction_mail','tevolution_transaction_mail_fn');
}
/* function while change in status from transaction listing  */
function tevolution_transaction_msg_fn()
{
	$tmpdata = get_option('templatic_settings');
	if(count($_REQUEST['cf'])>0)
	{
		for($i=0;$i<count($_REQUEST['cf']);$i++)
		{
			$cf = explode(",",$_REQUEST['cf'][$i]);
			$orderId = $cf[0];
			if(isset($_REQUEST['action']) && $_REQUEST['action'] !='' && $_REQUEST['action'] !='delete'){
				global $wpdb,$transection_db_table_name;
				$transection_db_table_name = $wpdb->prefix . "transactions";
				
				$ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
				$orderinfo = $wpdb->get_row($ordersql);
				$pid = $orderinfo->post_id;
				/* save post data while upgrade post from transaction listing */
				if(get_post_meta($pid,'upgrade_request',true) == 1  && (isset($_REQUEST['action']) && $_REQUEST['action'] == 'confirm'))
				{ 
					do_action('tranaction_upgrade_post',$pid); /* add an action to save upgrade post data. */
				}
				if($orderinfo->payment_method != '' && $orderinfo->payment_method != '-')
					$payment_type = $orderinfo->payment_method;
				else
					$payment_type = __('Free',DOMAIN);
	
				$payment_date =  date_i18n(get_option('date_format'),strtotime($orderinfo->payment_date));
				if(isset($_REQUEST['ostatus']) && @$_REQUEST['ostatus']!='')
					$trans_status = $wpdb->query("update $transection_db_table_name SET status = '".$_REQUEST['ostatus']."' where trans_id = '".$orderId."'");
				$user_detail = get_userdata($orderinfo->user_id);
				$user_email = $user_detail->user_email;
				$user_login = $user_detail->display_name;
				$my_post['ID'] = $pid;
				
				if(isset($_REQUEST['action']) && $_REQUEST['action']== 'confirm')
				{
					$payment_status = APPROVED_TEXT;
					$status = 'publish';
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
				elseif(isset($_REQUEST['action']) && $_REQUEST['action']== 'pending')
				{
					$payment_status = PENDING_MONI;
					$status = 'draft';
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
				elseif(isset($_REQUEST['action']) && $_REQUEST['action']== 'cancel')
				{
					$payment_status = PENDING_MONI;
					$status = 'draft';
				}
				
				$my_post['post_status'] = $status;
				wp_update_post( $my_post );
				/*set featured option of post*/
				
				$to = get_site_emailId_plugin();
				$package_id = $orderinfo->package_id;
				$package_name = get_post($package_id);
				$productinfosql = "select ID,post_title,guid,post_author from $wpdb->posts where ID = $pid";
				$productinfo = get_post($pid);
				$post_name = $productinfo->post_title;
				$post_type_mail = $productinfo->post_type;
				$transaction_details="";
				$transaction_details .= "-------------------------------------------------- <br/>\r\n";
				$transaction_details .= __('Payment Details for',DOMAIN).": ".$post_name."<br/>\r\n";
				$transaction_details .= "-------------------------------------------------- <br/>\r\n";
				$transaction_details .= __('Package Name',DOMAIN).": ".$package_name->post_title."<br/>\r\n";
				$transaction_details .= __('Status',DOMAIN).": ".$payment_status."<br/>\r\n";
				$transaction_details .= __('Type',DOMAIN).": $payment_type <br/>\r\n";
				$transaction_details .= __('Date',DOMAIN).": $payment_date <br/>\r\n";
				$transaction_details .= "-------------------------------------------------- <br/>\r\n";
				$transaction_details = $transaction_details;
				if((isset($_REQUEST['action']) && $_REQUEST['action'] == 'confirm' ) || (isset($_REQUEST['action2']) && $_REQUEST['action2'] == 'confirm' ))
				{
					$subject = $tmpdata['payment_success_email_subject_to_admin'];
					if(!$subject)
					{
						$subject = __("You have received a payment",DOMAIN);
					}
					$content = $tmpdata['payment_success_email_content_to_admin'];
					if(!$content){
						$content = __("<p>Howdy [#to_name#],</p><p>A post has been approved of [#payable_amt#] on [#site_name#].",DOMAIN).' '.__('Details are available below',DOMAIN).'</p><p>[#transaction_details#]</p><p>'.__('Thanks,',DOMAIN).'<br/>[#site_name#]</p>';
					}
				}
				if((isset($_REQUEST['action']) && $_REQUEST['action'] == 'pending' ) || (isset($_REQUEST['action2']) && $_REQUEST['action2'] == 'pending' ))
				{
					$subject = $tmpdata['pending_listing_notification_subject'];
					if(!$subject)
					{
						$subject = __("Listing payment not confirmed",DOMAIN);
					}
					$content = $tmpdata['pending_listing_notification_content'];
					if(!$content)
					{
						$content = __("<p>Hi [#to_name#],<br />A listing request on the below details has been rejected.<p>[#transaction_details#]</p>Please try again later.<br />Thanks you.<br />[#site_name#]</p>",DOMAIN);
					}
				}
				$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
				$fromEmail = get_option('admin_email');
				$fromEmailName = stripslashes(get_option('blogname'));	
				$search_array = array('[#to_name#]','[#payable_amt#]','[#transaction_details#]','[#site_name#]');
				$replace_array = array($fromEmailName,display_amount_with_currency_plugin($orderinfo->payable_amt),$transaction_details,$store_name);
				$filecontent = str_replace($search_array,$replace_array,$content);
				if((isset($_REQUEST['action']) && $_REQUEST['action'] != 'delete' ) || (isset($_REQUEST['action2']) && $_REQUEST['action2'] != 'delete' ) && (isset($_REQUEST['action']) && $_REQUEST['action'] != 'cancel' ) || (isset($_REQUEST['action2']) && $_REQUEST['action2'] != 'cancel' ))
				{
					@templ_send_email($fromEmail,$fromEmailName,$fromEmail,$user_login,$subject,stripslashes($filecontent),''); /* email to admin*/
				}
				/* post details  */
					$post_link = get_permalink($pid);
					$post_title = '<a href="'.$post_link.'">'.stripslashes($productinfo->post_title).'</a>'; 
					$aid = $orderinfo->user_id;
					$userInfo = get_userdata($aid);
					$to_name = $userInfo->user_nicename;
					$to_email = $userInfo->user_email;
					$user_email = $userInfo->user_email;
				
				$transaction_details ="";
				$transaction_details .= __('Information Submitted URL',DOMAIN)." <br/>\r\n";
				$transaction_details .= "-------------------------------------------------- <br/>\r\n";
				$transaction_details .= "  $post_title <br/>\r\n";
				$transaction_details = __($transaction_details,DOMAIN);
				if((isset($_REQUEST['action']) && $_REQUEST['action'] == 'confirm' ) || (isset($_REQUEST['action2']) && $_REQUEST['action2'] == 'confirm' ))
				{
					$subject = $tmpdata['payment_success_email_subject_to_client'];
					if(!$subject)
					{
						$subject = __("Thank you for your submission!",DOMAIN);
					}
					$content = $tmpdata['payment_success_email_content_to_client'];
					if(!$content)
					{
						$content = __("<p>Hello [#to_name#],</p><p>Your submission has been approved! You can see the listing here:</p><p>[#transaction_details#]</p><p>If you'll have any questions about this please send an email to [#admin_email#]</p><p>Thanks!,<br/>[#site_name#]</p>",DOMAIN);
					}
				}
				if((isset($_REQUEST['action']) && $_REQUEST['action'] == 'pending' ) || (isset($_REQUEST['action2']) && $_REQUEST['action2'] == 'pending' ))
				{
					$subject = $tmpdata['pending_listing_notification_subject'];
					if(!$subject)
					{
						$subject = __("Listing payment not confirmed",DOMAIN);
					}
					$content = $tmpdata['pending_listing_notification_content'];
					if(!$content)
					{
						$content = __("<p>Hi [#to_name#],<br />A listing request on the below details has been rejected.<p>[#transaction_details#]</p>Please try again later.<br />Thanks you.<br />[#site_name#]</p>",DOMAIN);
					}
				}
				if((isset($_REQUEST['action']) && $_REQUEST['action'] == 'cancel' ) || (isset($_REQUEST['action2']) && $_REQUEST['action2'] == 'cancel' ))
				{
					$subject = $tmpdata['payment_cancelled_subject'];
					if(!$subject)
					{
						$subject = __("Payment Cancelled",DOMAIN);
					}
					$content = $tmpdata['payment_cancelled_content'];
					if(!$content)
					{
						$content = __("<p>[#post_type#] has been cancelled with transaction id [#transection_id#]</p>",DOMAIN);
					}
				}
				$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
				$search_array = array('[#to_name#]','[#transaction_details#]','[#site_name#]','[#admin_email#]','[#transection_id#]','[#post_type#]');
				$replace_array = array($to_name,$transaction_details,$store_name,get_option('admin_email'),$orderId,ucfirst(get_post_type($pid)));
				$content = str_replace($search_array,$replace_array,$content);
				/*@mail($user_email,$subject,$content,$headers); email to client  */
				/* if user submits the free form then mail will not sent to them */
				if($orderinfo->payable_amt > 0)
				{
					if((isset($_REQUEST['action']) && $_REQUEST['action'] != 'delete' ) || (isset($_REQUEST['action2']) && $_REQUEST['action2'] != 'delete' ))
					{
						templ_send_email($fromEmail,$fromEmailName,$user_email,$user_login,$subject,stripslashes($content),$extra='');
					}
				}
			}
			/*set post status while delete transaction */
			if((isset($_REQUEST['action']) && $_REQUEST['action'] =='delete') || (isset($_REQUEST['action2']) && $_REQUEST['action2'] =='delete'))
			{
				global $wpdb,$transection_db_table_name;
				$transection_db_table_name = $wpdb->prefix . "transactions";
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
						/* Update post */
						$my_post = array();
						$my_post['ID'] = $subscribe_post_object->post_id;
						$my_post['post_status'] = 'draft';
						
						/* Update the post into the database */
						wp_update_post( $my_post );
					}
				}
			}
		}
	}
}

?>