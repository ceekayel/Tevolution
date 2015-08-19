<?php
/*
 * insert default custom feilds ans its related option
 */
global $wp_query,$wpdb,$wp_rewrite,$current_user;
/**-- conditions for activation Custom Fields --**/

if((isset($_REQUEST['activated']) && $_REQUEST['activated']=='custom_fields_templates') && (isset($_REQUEST['true']) && $_REQUEST['true']==1) || (isset($_REQUEST['activated']) && $_REQUEST['activated']=='true'))
{
		update_option('custom_fields_templates','Active');
		$templatic_settings=get_option('templatic_settings');
		
		if(!isset($templatic_settings['templatic-category_custom_fields']) && $templatic_settings['templatic-category_custom_fields']=='')
			$tmpdata['templatic-category_custom_fields'] = 'No';
		else
			$tmpdata['templatic-category_custom_fields'] = $templatic_settings['templatic-category_custom_fields'];
		
		update_option('templatic_settings',array_merge($templatic_settings,$tmpdata));
		
}elseif((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'custom_fields_templates') && (isset($_REQUEST['true']) && $_REQUEST['true']==0)){
		delete_option('custom_fields_templates');
}
/**-- coding to add sub menu under main menu--**/

/* Files related to monetization */
	
	add_filter('set-screen-option', 'custom_fields_set_screen_option', 10, 3);
	
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/tevolution_custom_fields_functions.php') )
	{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/tevolution_custom_fields_functions.php");	
	}
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/custom_fields_preview_function.php') )
	{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/custom_fields_preview_function.php");	
	}
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/custom_fields_function.php') )
	{
		if(is_admin() && strstr($_SERVER['REQUEST_URI'],'/wp-admin/')){
			include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/admin_custom_fuctions.php");
		}else{
			include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/custom_fields_function.php");
		}
	}	
	
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/admin_manage_post_type_custom_fields_table.php') && is_admin() )
	{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/admin_manage_post_type_custom_fields_table.php");	
	}	
	
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/language.php') )
	{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/language.php");
	}
	
	add_filter('body_class','remove_admin_bar',10,2);/* call body class for remove admin bar */
	add_action( 'init', 'custom_fields_preview' ,11);
	add_action('admin_init','create_default_wordpress_customfields');
	
	add_action('templatic_general_setting_data','post_page_setting_data');/* call general setting data */
	
	add_action('admin_init','post_expire_session_table_create');
	add_action('admin_notices','tevolution_custom_fields_notice',30);
	add_action('admin_notices','templatic_site_info_tracking_notice',30);

/* Set the file extension for allown only image/picture file extension in upload file*/
$extension_file=array('.jpg','.JPG','jpeg','JPEG','.png','.PNG','.gif','.GIF','.jpe','.JPE');  
global $extension_file;

/* set screen option for custom field*/
function custom_fields_set_screen_option($status, $option, $value) {
	if ( 'custom_fields_per_page' == $option ) return $value;
}

/*
 * unset the admin-bar class on preview and success page 
 */
function remove_admin_bar($classes,$class){
		
	if((isset($_REQUEST['page']) && ($_REQUEST['page'] == "preview" || $_REQUEST['page'] == "success")) || (isset($_REQUEST['ptype']) && $_REQUEST['ptype']=='return') ){
		if(($key = array_search('admin-bar', $classes)) !== false) {
		    unset($classes[$key]);
		}
	}
	return $classes;
}


/* include the success page for different payment gateway  */
function custom_fields_preview()
{	
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "payment")
	{
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/post_upgrade_payment.php");
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "paynow")
	{
		global $_wp_additional_image_sizes;		
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/custom_fields_paynow.php");
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "upgradenow")
	{
		global $_wp_additional_image_sizes;		
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/post_upgrade_pay.php");
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "success")
	{
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/success.php");
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "paypal_pro_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-paypal_pro/includes/paypal_pro_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "authorizedotnet_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-authorizedotnet/includes/authorizedotnet_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "googlecheckout_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-googlecheckout/includes/googlecheckout_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "worldpay_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-worldpay/includes/worldpay_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "eway_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-eway/includes/eway_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "ebay_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-ebay/includes/ebay_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "ebs_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-ebs/includes/ebs_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "psigate_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-psigate/includes/psigate_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "2co_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-2co/includes/2co_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "stripe_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-stripe/includes/stripe_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "braintree_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-Braintree/includes/braintree_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "inspire_commerce_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-InspireCommerce/includes/inspire_commerce_success.php';
		include($dir);
		exit;
	}
	if(isset($_GET['stripe-listener']) && $_GET['stripe-listener'] == 'recurring') {
		$dir = get_tmpl_plugin_directory() . 'Tevolution-stripe/includes/stripe_listener.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['pmethod']) && $_REQUEST['pmethod'] == "paypal_express_checkout")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-paypal_express_checkout/includes/paypal_express_checkout_success.php';
		include($dir);
		exit;
	}
}
/* Insert wordpress default fields in posts table when plugin activated */
function create_default_wordpress_customfields()
{
	/*Reset tevolution Custom Fields */
	if(isset($_POST['reset_custom_fields']) && (isset($_POST['custom_reset']) && $_POST['custom_reset']==1))
	{
		update_option('tmpl_default_fields_inserted','');
	}
	
	global $wpdb,$pagenow,$table_name;
	if(($pagenow=='plugins.php' || $pagenow=='themes.php' ||  (isset($_REQUEST['page']) && ($_REQUEST['page']=='custom_setup' || $_REQUEST['ctab']=='custom_fields' ))))
	{
		
		$args = array(
		  'public' => true,
		  'label'  => 'Fields'
		);
		register_post_type( 'custom_fields', $args );
		
		/* this option will set after default fields inserted */
		
		update_option('tmpl_default_fields_inserted','1');
		
		/*Reset tevolution Custom Fields */
		if(isset($_POST['reset_custom_fields']) && (isset($_POST['custom_reset']) && $_POST['custom_reset']==1))
		{
			$args=array('post_type'      => 'custom_fields',
					  'posts_per_page' => -1	,
					  'post_status'    => array('publish'),
					  'order'          => 'ASC'
					);
			$custom_field = new WP_Query($args);
			if($custom_field):
				while ($custom_field->have_posts()) : $custom_field->the_post();
					wp_delete_post( get_the_ID(), true);
				endwhile;
			endif;
	
		}
		
		/* Here You have to pass "$exclude_post_types" same variable in other plugins as well.
		*/
		$exclude_post_type = apply_filters('reset_exclude_post_types',array());
		$cus_pos_type = get_option("templatic_custom_post");
		$post_type_arr='post,';
		$heading_post_type_arr='post,';
		if($cus_pos_type && count($cus_pos_type) > 0)
		{
			foreach($cus_pos_type as $key=> $_cus_pos_type)
			{
				if(!empty($exclude_post_type)){
					if(!in_array($key,$exclude_post_type)){
						$post_type_arr .= $key.",";
					}
				}else{
					$post_type_arr .= $key.",";
				}
				$heading_post_type_arr .= $key.",";
			}
		}
		$post_type_arr = substr($post_type_arr,0,-1);
		$heading_post_type_arr = substr($heading_post_type_arr,0,-1);
		
		
				 
		 /* Insert Post heading type into posts */
		 $taxonomy_name = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_title = '[#taxonomy_name#]' and $wpdb->posts.post_type = 'custom_fields'");
		 if(count($taxonomy_name) == 0)
		 {
			$my_post = array(
						 'post_title'   => '[#taxonomy_name#]',
						 'post_content' => 'It is a default heading type used for grouping certain custom fields together under the same particular heading at front end. (e.g. place information, event information etc.)',
						 'post_status'  => 'publish',
						 'post_author'  => 1,
						 'post_name'    => 'basic_inf',
						 'post_type'    => "custom_fields",
					);
			$post_meta = array(
				'post_type'	      => $heading_post_type_arr,
				'ctype'	          =>'heading_type',
				'site_title'	  =>'[#taxonomy_name#]',
				'htmlvar_name'    =>'basic_inf',
				'sort_order' 	  => '1',
				'post_sort_order' 	  => '1',
				'is_active' 	  => '1',
				'show_on_page'    => 'user_side',
				'show_on_detail'  => '0',
				'show_in_column'  => '0',
				'is_search'       =>' 0',
				'is_edit' 	      => 'true',
				'is_submit_field' => '1',
				'heading_type'    => '[#taxonomy_name#]',
				);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			 {
				add_post_meta($post_id, $key, $_post_meta);
			 }
			
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
			 }
		 }else{
                                                                           /* Reset if user change this type */
                                                                           update_post_meta($taxonomy_name->ID, 'ctype','heading_type' );
			
                                                                           $post_type=get_post_meta($taxonomy_name->ID, 'post_type',true );
			if(!strstr($post_type,'post'))
				update_post_meta($taxonomy_name->ID, 'post_type',$post_type.',post' );
					
			update_post_meta($taxonomy_name->ID, 'post_type_post','post' );
			update_post_meta($taxonomy_name->ID, 'taxonomy_type_category','category' );
			
			if(get_post_meta($taxonomy_name->ID,'post_sort_order',true)){
				update_post_meta($taxonomy_name->ID, 'post_sort_order',get_post_meta($taxonomy_name->ID,'post_sort_order',true) );
			}else{
				update_post_meta($taxonomy_name->ID, 'post_sort_order',1 );
			}
		 }
		 
		/* Insert Post Category into posts */
		$post_category = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'category' and $wpdb->posts.post_type = 'custom_fields'");		
		if(count($post_category) == 0)
		 {
			$my_post = array(
						 'post_title'   => 'Post Category',
						 'post_content' => '',
						 'post_status'  => 'publish',
						 'post_author'  => 1,
						 'post_name'    => 'category',
						 'post_type'    => "custom_fields",
					);
			$post_meta = array(
				'post_type'          => $post_type_arr,
				'ctype'              =>'post_categories',
				'htmlvar_name'       =>'category',
				'sort_order'         => '1',
				'is_active'          => '1',
				'is_require'         => '1',
				'show_on_page'       => 'user_side',
				'is_edit'            => 'true',
				'show_on_detail'     => '1',
				'show_on_listing'    => '0',
				'show_in_column'     => '0',
				'is_submit_field'    => '1',
				'is_search'          =>'0',
				'field_require_desc' => __('Please Select Category',DOMAIN),
				'validation_type'    => 'require',
				'heading_type'       => '[#taxonomy_name#]',
				);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			{
				add_post_meta($post_id, $key, $_post_meta);
			}
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
				add_post_meta($post_id, 'post_sort_order' , '1');
				add_post_meta($post_id, 'post_heading_type' , '[#taxonomy_name#]');
			 }
		 }else{ 
                                                                         /* Set always active post if user deactive */    
                                                                           update_post_meta($post_category->ID, 'is_active', '1');
                                                                           
			$post_type=get_post_meta($post_category->ID, 'post_type',true );
			if(!strstr($post_type,'post'))
				update_post_meta($post_category->ID, 'post_type',$post_type.',post' );
					
			update_post_meta($post_category->ID, 'post_type_post','post' );
			update_post_meta($post_category->ID, 'taxonomy_type_category','category' );
			update_post_meta($post_category->ID, 'show_in_post_search','1' );
			update_post_meta($post_category->ID, 'show_on_detail','1' );
			if(get_post_meta($post_category->ID,'post_sort_order',true)){
				update_post_meta($post_category->ID, 'post_sort_order',get_post_meta($post_category->ID,'post_sort_order',true) );
			}else{
				update_post_meta($post_category->ID, 'post_sort_order',1 );
			}
			
			if(get_post_meta($post_category->ID,'post_heading_type',true)){
				update_post_meta($post_category->ID, 'post_heading_type',get_post_meta($post_category->ID,'post_heading_type',true) );
			}else{
				update_post_meta($post_category->ID, 'post_heading_type','[#taxonomy_name#]' );
			} 
		 }
		 /* Finish The category custom field */
		 
		 /* Insert Post title into posts */
		$post_title = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_title' and $wpdb->posts.post_type = 'custom_fields'");
		if(count($post_title) == 0)
		 {
			$my_post = array(
						 'post_title'   => 'Post Title',
						 'post_content' => '',
						 'post_status'  => 'publish',
						 'post_author'  => 1,
						 'post_name'    => 'post_title',
						 'post_type'    => "custom_fields",
					);
			$post_meta = array(
				'post_type'          => $post_type_arr,
				'ctype'              =>'text',
				'htmlvar_name'       =>'post_title',
				'sort_order'         => '2',
				'is_active'          => '1',
				'is_require'         => '1',
				'show_on_page'       => 'user_side',
				'is_edit'            => 'true',
				'show_on_detail'     => '0',
				'show_on_success'    => '1',
				'show_on_listing'    => '1',
				'show_in_column'     => '0',
				'is_search'          => '0',
				'is_submit_field'    => '1',
				'field_require_desc' => __('Please Enter title',DOMAIN),
				'validation_type'    => 'require',
				'heading_type'       => '[#taxonomy_name#]',
				);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			 {
				add_post_meta($post_id, $key, $_post_meta);
			 }
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
			 }
	 
		 }else{
                                                                            /* Set always active post title id user deactive */    
                                                                           update_post_meta($post_title->ID, 'is_active', '1');
                                                                      
			$post_type=get_post_meta($post_title->ID, 'post_type',true );
			if(!strstr($post_type,'post'))
				update_post_meta($post_title->ID, 'post_type',$post_type.',post' );
					
			update_post_meta($post_title->ID, 'post_type_post','post' );
			update_post_meta($post_title->ID, 'taxonomy_type_category','category' );
			update_post_meta($post_title->ID, 'show_in_post_search','1' );
			
			if(get_post_meta($post_title->ID,'post_sort_order',true)){
				update_post_meta($post_title->ID, 'post_sort_order',get_post_meta($post_title->ID,'post_sort_order',true) );
			}else{
				update_post_meta($post_title->ID, 'post_sort_order',2 );
			}
			
			if(get_post_meta($post_title->ID,'post_heading_type',true)){
				update_post_meta($post_title->ID, 'post_heading_type',get_post_meta($post_title->ID,'post_heading_type',true) );
			}else{
				update_post_meta($post_title->ID, 'post_heading_type','[#taxonomy_name#]' );
			} 
		 
		 }
		 /* Finish the post title custom fields */
		 
		  /* Insert Post content into posts */
		 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_content' and $wpdb->posts.post_type = 'custom_fields'");
		 if(count($post_content) == 0)
		 {
			$my_post = array(
						 'post_title'  => 'Post Content',
						 'post_content'=> '',
						 'post_status' => 'publish',
						 'post_author' => 1,
						 'post_name'   => 'post_content',
						 'post_type'   => "custom_fields",
					);
			$post_meta = array(
				'post_type'          => $post_type_arr,
				'ctype'              =>'texteditor',
				'show_in_column'     => '0',
				'htmlvar_name'       =>'post_content',
				'sort_order'         => '3',
				'is_active'          => '1',
				'is_require'         => '1',
				'show_on_page'       => 'user_side',
				'is_edit'            => 'true',
				'show_on_detail'     => '1',
				'show_on_listing'    => '1',
				'show_in_column'     => '0',
				'is_search'          => '0',
				'is_submit_field'    => '1',
				'field_require_desc' => __('Please Enter content',DOMAIN),
				'validation_type'    => 'require',
				'heading_type'       => '[#taxonomy_name#]',
				'listing_heading_type'=> '[#taxonomy_name#]',);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			 {
				add_post_meta($post_id, $key, $_post_meta);
			 }
			
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
			 }
		 }else{
                                                                           /* Set always active post if user deactive */    
                                                                           update_post_meta($post_content->ID, 'is_active', '1');
                                                                           
			$post_type=get_post_meta($post_content->ID, 'post_type',true );
			if(!strstr($post_type,'post'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',post' );
					
			update_post_meta($post_content->ID, 'post_type_post','post' );
			update_post_meta($post_content->ID, 'taxonomy_type_category','category' );
			update_post_meta($post_content->ID, 'show_in_post_search','1' );
			
			if(get_post_meta($post_content->ID,'post_sort_order',true)){
				update_post_meta($post_content->ID, 'post_sort_order',get_post_meta($post_content->ID,'post_sort_order',true) );
			}else{
				update_post_meta($post_content->ID, 'post_sort_order',3 );
			}
			
			if(get_post_meta($post_content->ID,'post_heading_type',true)){
				update_post_meta($post_content->ID, 'post_heading_type',get_post_meta($post_content->ID,'post_heading_type',true) );
			}else{
				update_post_meta($post_content->ID, 'post_heading_type','[#taxonomy_name#]' );
			} 
		 }
		 /* Finish the post content custom field */
		 
		  /* Insert Post excerpt into posts */
		 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_excerpt' and $wpdb->posts.post_type = 'custom_fields'");
		 if(count($post_content) == 0)
		 {
			$my_post = array(
						 'post_title'   => 'Post Excerpt',
						 'post_content' => '',
						 'post_status'  => 'publish',
						 'post_author'  => 1,
						 'post_name'    => 'post_excerpt',
						 'post_type'    => "custom_fields",
					);
			$post_meta = array(
				'post_type'      => $post_type_arr,
				'ctype'          => 'textarea',
				'htmlvar_name'   => 'post_excerpt',
				'sort_order'     => '4',
				'is_active'      => '1',
				'is_require'     => '0',
				'show_on_page'   => 'user_side',
				'show_in_column' => '0',
				'show_on_listing'=> '1',
				'is_edit'        => 'true',
				'show_on_detail' => '1',
				'show_in_column' => '0',
				'is_search'      => '0',
				'is_submit_field'=> '0',
				'heading_type'   => '[#taxonomy_name#]',
				);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			 {
				add_post_meta($post_id, $key, $_post_meta);
			 }
			
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
			 }
		 }else{
                                                                           /* Set always active post if user deactive */    
                                                                           update_post_meta($post_content->ID, 'is_active', '1');
                                                                           
			$post_type=get_post_meta($post_content->ID, 'post_type',true );
			if(!strstr($post_type,'post'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',post' );
					
			update_post_meta($post_content->ID, 'post_type_post','post' );
			update_post_meta($post_content->ID, 'taxonomy_type_category','category' );
			update_post_meta($post_content->ID, 'show_in_post_search','1' );
			
			if(get_post_meta($post_content->ID,'post_sort_order',true)){
				update_post_meta($post_content->ID, 'post_sort_order',get_post_meta($post_content->ID,'post_sort_order',true) );
			}else{
				update_post_meta($post_content->ID, 'post_sort_order',3 );
			}
			
			if(get_post_meta($post_content->ID,'post_heading_type',true)){
				update_post_meta($post_content->ID, 'post_heading_type',get_post_meta($post_content->ID,'post_heading_type',true) );
			}else{
				update_post_meta($post_content->ID, 'post_heading_type','[#taxonomy_name#]' );
			} 
		 }
		 /* Finish The post excerpt custom field */
		 /* Insert Post Contact Info heading into posts */
		$field_label = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'field_label' and $wpdb->posts.post_type = 'custom_fields'"); 	 

		if(count($field_label) == 0)
		{
			$my_post = array(
			 'post_title' => 'Label of Field',
			 'post_content' => '',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'field_label',
			 'post_type' => "custom_fields",
			);
			$post_meta = array(
			'post_type'=> $post_type_arr,
			'post_type_post'=> 'post',
			'ctype'=>'heading_type',
			'htmlvar_name'=>'field_label',
			'field_category' =>'all',
			'sort_order' => '19',
			'event_sort_order' => '19',
			'is_active' => '1',
			'is_submit_field' => '0',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_post' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'is_delete' => '0'
			);
			wp_set_post_terms($post_id,'1','category',true);
			$post_id = wp_insert_post( $my_post );

			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}

			/*wp_set_post_terms($post_id,'1','category',true);*/
			foreach($post_meta as $key=> $_post_meta)
			{
				add_post_meta($post_id, $key, $_post_meta);
			}
		}else{
			$post_type=get_post_meta($field_label->ID, 'post_type',true );
			if(!strstr($post_type,'post'))
				update_post_meta($field_label->ID, 'post_type',$post_type.',post' );
					
			update_post_meta($field_label->ID, 'post_type_post','post' );
			update_post_meta($field_label->ID, 'taxonomy_type_category','category' );
			update_post_meta($field_label->ID, 'show_in_post_search','1' );
			
			if(get_post_meta($field_label->ID,'post_sort_order',true)){
				update_post_meta($field_label->ID, 'post_sort_order',get_post_meta($field_label->ID,'post_sort_order',true) );
			}else{
				update_post_meta($field_label->ID, 'post_sort_order',4 );
			}
			
		}
		 /* Insert Post image_uploader into posts */
		 $post_images = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_images' and $wpdb->posts.post_type = 'custom_fields'");
		 if(count($post_images) == 0)
		 {
			$my_post = array(
						 'post_title'   => 'Post Images',
						 'post_content' => '',
						 'post_status'  => 'publish',
						 'post_author'  => 1,
						 'post_name'    => 'post_images',
						 'post_type'    => "custom_fields",
					);
			$post_meta = array(
				'post_type'	   => $post_type_arr,
				'post_type_post'=> 'post',
				'ctype'		   =>'image_uploader',
				'site_title'	   =>'Post Images',
				'htmlvar_name'    =>'post_images',
				'sort_order' 	   => '5',
				'is_active' 	   => '1',
				'is_require' 	   => '1',
				'show_on_page'    => 'user_side',
				'show_in_column'  => '0',
				'show_on_detail'  => '1',
				'show_on_listing' => '1',
				'show_in_email'   => '0',
				'is_edit'         => 'true',
				'validation_type' => 'require',
				'is_search'       => '0',
				'is_submit_field' => '1',
				'heading_type'    => '[#taxonomy_name#]',
				);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			 {
				add_post_meta($post_id, $key, $_post_meta);
			 }
			
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
			 }
		 }else{
                                                                           /* Set always active post if user deactive */    
                                                                           update_post_meta($post_images->ID, 'is_active', '1');                               
		 
			$post_type=get_post_meta($post_images->ID, 'post_type',true );
			if(!strstr($post_type,'post'))
				update_post_meta($post_images->ID, 'post_type',$post_type.',post' );
					
			update_post_meta($post_images->ID, 'post_type_post','post' );
			update_post_meta($post_images->ID, 'taxonomy_type_category','category' );
			update_post_meta($post_images->ID, 'show_in_post_search','1' );
			
			if(get_post_meta($post_images->ID,'post_sort_order',true)){
				update_post_meta($post_images->ID, 'post_sort_order',get_post_meta($post_images->ID,'post_sort_order',true) );
			}else{
				update_post_meta($post_images->ID, 'post_sort_order',4);
			}
			
			if(get_post_meta($post_images->ID,'post_heading_type',true)){
				update_post_meta($post_images->ID, 'post_heading_type',get_post_meta($post_content->ID,'post_heading_type',true) );
			}else{
				update_post_meta($post_images->ID, 'post_heading_type','Label of Field' );
			} 
		 }
		 
                                                  /* Finish the post images custom fields */
		$post_tags = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_tags' and $wpdb->posts.post_type = 'custom_fields'");
		if(count($post_tags) == 0)
		{
		}else{
			update_post_meta($post_tags->ID, 'is_edit','true' );
		}
		 /* Finish the taxonomy name heading custom fields */
		 
                                                  /* Reset Location & Map  type if user changes */
		$post_location_info = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'locations_info' and $wpdb->posts.post_type = 'custom_fields'");
		if(count($post_location_info) == 0)
		{
                                                            update_post_meta($post_location_info->ID, 'ctype','heading_type' );
		}
                    
		global $wp_post_types;
		if ( isset( $wp_post_types[ 'custom_fields' ] ) ) {
			unset( $wp_post_types[ 'custom_fields' ] );
		}
		
	}/* First if condition*/
	
}


/*
* Create action for general settings in tevolution
*/	
function post_page_setting_data($column)
{
	$tmpdata = get_option('templatic_settings');
	
		$templatic_theme = wp_get_theme();		
	?>
	    <p class="tevolution_desc"> <?php echo __('This is the main Tevolution settings area. As you add Tevolution add-ons their settings will appear here. <br><b>Note:</b> Do not forget to click on "Save all settings" at the bottom when done with tweaking settings. You should also clear Tevolution cache (top of the page) after every major change. ',ADMINDOMAIN)?> </p>
			
			<div id="theme_support_setting">
			<?php 
			if(strtolower($templatic_theme->get( 'Author' ))!='templatic' && (!current_theme_supports('home_listing_type_value') || ((function_exists('directory_admin_notices') || function_exists('event_manager_admin_notices') )&& (!current_theme_supports('tev_taxonomy_sorting_opt') || !current_theme_supports('tev_taxonomy_excerpt_opt'))) )):?>
					<p><?php echo __("If you are not using one of Directory based themes from templatic then copy below code and paste it in your theme's functions.php file located in your active WordPress directory to enhance your theme functionality.",ADMINDOMAIN); ?></p>
			<?php endif;
			
			if(strtolower($templatic_theme->get( 'Author' ))!='templatic' && !current_theme_supports('home_listing_type_value')):?>
					<p class="tevolution_desc"><?php echo __('Display different post type on home page   -   add_theme_support("home_listing_type_value");',ADMINDOMAIN); ?></p>
			<?php endif;
			
			if(strtolower($templatic_theme->get( 'Author' ))!='templatic' && !current_theme_supports('author_box')):?>
					<p class="tevolution_desc"><?php echo __('Display different post type on author page   -   do_action("author_box") on author page;',ADMINDOMAIN); ?></p>
			<?php endif;
			
			if(strtolower($templatic_theme->get( 'Author' ))!='templatic' && !current_theme_supports('tev_taxonomy_sorting_opt') && function_exists('directory_admin_notices')):?>               
			<p class="tevolution_desc"><?php echo __('Display sorting option on taxonomy page    -   add_theme_support("tev_taxonomy_sorting_opt");',ADMINDOMAIN); ?></p>
			<?php endif;
			
			if(strtolower($templatic_theme->get( 'Author' ))!='templatic' && !current_theme_supports('tev_taxonomy_excerpt_opt')  && function_exists('directory_admin_notices')):?>                        
			<p class="tevolution_desc"><?php echo __('Display excerpt setting on post listing page   -   add_theme_support("tev_taxonomy_excerpt_opt");',ADMINDOMAIN); ?></p>
			<?php endif;?> 
			</div>
	
		<!-- Sub Menu For General Settings Section-->	
		<div class="wp-filter tev-sub-menu" >
		<ul id="tev_general_settings" class="filter-links">
			<li class="submit_page_settings active"><a id="submit_page_settings" href="javascript:void(0);" class="current"><?php echo __('Submission Page',ADMINDOMAIN); ?></a></li>
			<?php do_action('tevolution_before_subsettings'); 
			
			/* show if current theme support - home page display with different post types OR not */
			if(current_theme_supports('theme_home_page') && get_option('show_on_front') =='posts'){
			?>
				<li class="home_page_settings"><a id="home_page_settings" href="javascript:void(0);"><?php echo __('Home page',ADMINDOMAIN); ?></a></li>
			<?php
			}
			do_action('tevolution_after_homepagelink'); 
			if(current_theme_supports('home_listing_type_value') || current_theme_supports('tev_taxonomy_excerpt_opt')): ?>
				<li class="listing_page_settings"><a id="listing_page_settings" href="javascript:void(0);"><?php echo __('Category Page',ADMINDOMAIN); ?></a></li>
			<?php endif;
			do_action('tevolution_after_catpagelink'); 
			?>
			
			<li class="detail_page_settings"><a id="detail_page_settings" href="javascript:void(0);"><?php echo __('Detail Page',ADMINDOMAIN); ?></a></li>
			<?php do_action('tevolution_after_detailpagelink');  ?>			
			
			<li class="registration_page_setup"><a id="registration_page_setup" href="javascript:void(0);" ><?php echo __('Registration Page',ADMINDOMAIN); ?></a></li>
			<?php 
			do_action('tevolution_after_regpagelink'); 
			
			?>
			<li class="general_claim_setting"><a id="general_claim_setting" href="javascript:void(0);"><?php echo __('Claim Ownership',ADMINDOMAIN); ?></a></li>
			
			<?php do_action('tevolution_after_subsettings'); ?>
            
            <li class="captcha_settings"><a id="captcha_settings" href="javascript:void(0);"><?php echo __('Captcha',ADMINDOMAIN); ?></a></li>
		</ul> 
		</div>
		<?php
		do_action('tmpl_start_general_settings');
		/* Category page settings start */
		?>
				<!-- Submit page settings start -->
		<table id="submit_page_settings" class="tmpl-general-settings form-table active-tab">
			<tr>                    
				<td colspan="2">
				   <p class="tevolution_desc"><strong><?php _e('Tip: ',ADMINDOMAIN); ?></strong><?php echo sprintf(__('Generate a submission page for a new post type by entering the following shortcode in a new page <strong>[submit_form post_type= &acute;your_post_type_name&acute;]</strong>. For details on this please open the %s',ADMINDOMAIN),'<a href="http://templatic.com/docs/tevolution-guide" target= "_blank" > documentation guide</a>'); ?></p><br />
				</td>
			</tr> 
	   		<tr>
                <th>
                    <label><?php echo __('Category specific fields',ADMINDOMAIN);	$templatic_category_custom_fields =  @$tmpdata['templatic-category_custom_fields']; if(!isset($templatic_category_custom_fields) && $templatic_category_custom_fields == ''){update_option('templatic-category_custom_fields','No');}?></label>
                </th>
                <td> 
				
					<div class="input-switch">
						<input type="checkbox"  id="templatic-category_custom_fields" name="templatic-category_custom_fields" value="Yes" <?php if($templatic_category_custom_fields == 'Yes' || $templatic_category_custom_fields ==''){?>checked="checked"<?php }?> />
						<label for="templatic-category_custom_fields" class="checkbox">&nbsp;<?php echo __('Enable',ADMINDOMAIN);?></label>
					</div>
             
                    <p class="description"><?php echo __('Displays different fields for different categories on submission page. For more information, open the <a href="http://templatic.com/docs/tevolution-guide/#basic_settings" title="Tevolution Guid" target="_blank">Custom Fields Guide</a>',ADMINDOMAIN);?></p>
                </td>
            </tr>
			 <tr>
				<th><label><?php echo __('Category Display',ADMINDOMAIN); ?></label></th>
				<td>
					<div class="element">
						 <div class="input_wrap">
							<?php $templatic_category_type =  @$tmpdata['templatic-category_type']; ?>
						   <select id="templatic-category_type" name="templatic-category_type" style="vertical-align:top;width:200px;" >							
							<option value="checkbox" <?php if($templatic_category_type == 'checkbox' ) { echo "selected=selected";  } ?>><?php echo __('Check Box',ADMINDOMAIN); ?></option>
							<option value="multiselectbox" <?php if($templatic_category_type == 'multiselectbox' ) { echo "selected=selected";  } ?>><?php echo __('Multi-select Box',ADMINDOMAIN); ?></option>
							<option value="select" <?php if($templatic_category_type == 'select' ) { echo "selected=selected";  } ?>><?php echo __('Select Box',ADMINDOMAIN); ?></option>
						   </select> 
					</div>
					</div>
				   <label for="ilc_tag_class"><p class="description"><?php echo __('Specify the format in which you want to display the categories on Submit page.',ADMINDOMAIN);?></p></label>
				</td>
			 </tr>
			 <tr>
				<th><label><?php echo __('Maximum image upload size',ADMINDOMAIN);	$templatic_image_size =  @$tmpdata['templatic_image_size']; ?></label></th>
				<td>
					<div class="element">
						 <div class="input_wrap">
						 <input type="text" id="templatic_image_size" name="templatic_image_size" value="<?php echo $templatic_image_size; ?>"/> </div>
						</div>
					</div>
				   <label for="ilc_tag_class"><p class="description"><?php echo __('The size is in kilobytes, e.g. 1MB = 1024KB. Enter only the number.',ADMINDOMAIN);?></p></label>
				</td>
			 </tr> 
		  	 <tr>
				<th><label><?php echo __('Default status for free submissions',ADMINDOMAIN);	$post_default_status =  @$tmpdata['post_default_status']; ?></label></th>
				<td>
					<select name="post_default_status">
						<option value="draft" <?php if($post_default_status == 'draft')echo "selected";?>><?php echo __('Draft',ADMINDOMAIN); ?></option>
						<option value="publish" <?php if($post_default_status == 'publish')echo "selected";?>><?php echo __('Published',ADMINDOMAIN); ?></option>
					</select>
				</td>
			 </tr> 
			<tr>
				<th><label><?php echo __('Default status for paid submissions',ADMINDOMAIN);	$post_default_status_paid =  @$tmpdata['post_default_status_paid']; ?></label></th>
				<td>
					<select name="post_default_status_paid">
						<option value="draft" <?php if($post_default_status_paid == 'draft')echo "selected";?>><?php echo __('Draft',ADMINDOMAIN); ?></option>
						<option value="publish" <?php if($post_default_status_paid == 'publish')echo "selected";?>><?php echo __('Published',ADMINDOMAIN); ?></option>
					</select>
				</td>
			 </tr> 
		
		 	<tr>
				<th><label><?php echo __('Default status for expired listings',ADMINDOMAIN);	$post_listing_ex_status =  @$tmpdata['post_listing_ex_status']; ?></label></th>
				<td>
					<select name="post_listing_ex_status">
						<option value="draft" <?php if($post_listing_ex_status == 'draft')echo "selected";?>><?php echo __('Draft',ADMINDOMAIN); ?></option>
						<option value="trash" <?php if($post_listing_ex_status == 'trash')echo "selected";?>><?php echo __('Trash',ADMINDOMAIN); ?></option>
					</select>
				</td>
			 </tr> 
			 
		 	 <tr>
				<th><label><?php echo __('User listing expiry notification email',ADMINDOMAIN);	$listing_email_notification =  @$tmpdata['listing_email_notification']; ?></label></th>
				<td>
					<select name="listing_email_notification">
					<option value="">-- Choose One --</option>
					 <option value="1" <?php if($listing_email_notification == '1')echo "selected";?>>1</option>
					 <option value="2" <?php if($listing_email_notification == '2')echo "selected";?>>2</option>
					 <option value="3" <?php if($listing_email_notification == '3')echo "selected";?>>3</option>
					 <option value="4" <?php if($listing_email_notification == '4')echo "selected";?>>4</option>
					 <option value="5" <?php if($listing_email_notification == '5')echo "selected";?>>5</option>
					 <option value="6" <?php if($listing_email_notification == '6')echo "selected";?>>6</option>
					 <option value="7" <?php if($listing_email_notification == '7')echo "selected";?>>7</option>
					 <option value="8" <?php if($listing_email_notification == '8')echo "selected";?>>8</option>
					 <option value="9" <?php if($listing_email_notification == '9')echo "selected";?>>9</option>
					 <option value="10" <?php if($listing_email_notification == '10')echo "selected";?>>10</option>
					</select>
					
					<p class="description"><?php echo __('Select number of days prior to expiry',ADMINDOMAIN);?></p>
				</td>
			 </tr> 
			<tr>
				<th><label><?php echo __('Terms and conditions',ADMINDOMAIN); 
				$tev_accept_term_condition =  @$tmpdata['tev_accept_term_condition'];
				if($tev_accept_term_condition ==1){ $checked ="checked=checked"; }else{
					$checked='';
				}
				?> <label> </th>
				<td>
					<div class="input-switch">
						<input id="tev_accept_term_condition" type="checkbox" value="1" name="tev_accept_term_condition" <?php echo $checked; ?>/>
						<label for="tev_accept_term_condition">&nbsp; <?php echo __('Enable',ADMINDOMAIN); ?></label>
					</div>
				</td>
			</tr>
			<?php do_action('templ_general_setting_before_tc'); ?>	
			<tr>
				<th><label><?php echo __('Terms and condition text',ADMINDOMAIN); 
				$term_condition_content =  stripslashes(@$tmpdata['term_condition_content']);
				?> <label> </th>
				<td>
					<textarea class="tb_textarea" id="term_condition_content" name="term_condition_content"><?php echo $term_condition_content; ?></textarea>
					 <p class="description"><?php echo __('Enter your terms in the above box. You can use HTML to create a link to your full terms of use page.',ADMINDOMAIN);?></p>
				</td>
			</tr>
			
			<?php do_action('templ_submitform_new_row'); ?>
            <tr>
				<td colspan="2">
				<p class="submit" style="clear: both;">
				  <input type="submit" name="Submit"  class="button button-primary button-hero" value="<?php echo __('Save All Settings',ADMINDOMAIN);?>" />
				  <input type="hidden" name="settings-submit" value="Y" />
				</p>
				</td>
			</tr>
		</table>
		
		<table id="listing_page_settings" class="tmpl-general-settings form-table">
		<?php
		$fl=0; 
		if(current_theme_supports('home_listing_type_value')) : $fl=1; ?>
		<tr>
			<td colspan="2"><p class="tevolution_desc"><?php echo __("Category page settings apply to the default 'listing' post type and any new custom post types you create.",ADMINDOMAIN); ?></p>
		    </td>
		</tr>
			
		<?php endif;
		do_action('before_listing_page_setting');
					do_action('tmpl_main_listing_page_setting');
		do_action('after_listing_page_setting');
		
		
		if(current_theme_supports('tev_taxonomy_excerpt_opt')) :?>
			<?php if($fl==0): ?>
			<tr>
				<th colspan="2"><div class="tevo_sub_title"><?php echo __('Category Page',ADMINDOMAIN);?></div></th>
			</tr>
		<?php endif; ?>    
			<tr>
				<th><label><?php echo __('Length Of Summary ',ADMINDOMAIN); ?></label></th>
				<td>
					<input type="text" name="excerpt_length" value="<?php echo $tmpdata['excerpt_length']; ?>" />
					<p class="description"><?php echo __("If you haven't entered excerpt in your post we will display here mentioned number of characters from your post description .",ADMINDOMAIN);?></p>
				</td>
			</tr>
			<tr>
				<th><label><?php echo __('Title For Continue Link ',ADMINDOMAIN); ?></label></th>
				<td>
					<input type="text" name="excerpt_continue" value="<?php echo $tmpdata['excerpt_continue']; ?>" />
					<p class="description"><?php echo __('Mention the title you want to show for a link which will be redirected to post detail page ',ADMINDOMAIN);?></p>
				</td>
			</tr>
		<?php endif;
		
		if(current_theme_supports('tev_taxonomy_sorting_opt')): ?>
		<tr class="templatic_sorting">
			<th valign="top"><label><?php echo __('Sorting options',ADMINDOMAIN);?></label></th>
			<td>
				<label><input type="checkbox" class="checkall" name="sorting_option[]" <?php if(!empty($tmpdata['sorting_option']) && in_array('select_all',$tmpdata['sorting_option'])) echo 'checked';?> onclick="SelectAllSorting()" value="select_all" /> <?php echo __("Select all",ADMINDOMAIN);?></label><br/>
				<label for="title_alphabetical"><input type="checkbox" id="title_alphabetical" name="sorting_option[]" value="title_alphabetical" <?php if(!empty($tmpdata['sorting_option']) && in_array('title_alphabetical',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php  echo __('Alphabetical',ADMINDOMAIN);?></label><br/>
				<label for="date_asc"><input type="checkbox" id="date_asc" name="sorting_option[]" value="date_asc" <?php if(!empty($tmpdata['sorting_option']) && in_array('date_asc',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php echo __('Publish Date Ascending',ADMINDOMAIN);?></label><br/>
				<label for="date_desc"><input type="checkbox" id="date_desc" name="sorting_option[]" value="date_desc" <?php if(!empty($tmpdata['sorting_option']) && in_array('date_desc',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php echo __('Publish Date Descending',ADMINDOMAIN);?></label><br />
			
				<label for="random"><input type="checkbox" id="random" name="sorting_option[]" value="random" <?php if(!empty($tmpdata['sorting_option']) && in_array('random',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php echo __('Random',ADMINDOMAIN);?></label><br />
					
				<?php if($tmpdata['templatin_rating']=='yes' || is_plugin_active('Templatic-MultiRating/multiple_rating.php')){ ?>
				<label for="rating"><input type="checkbox" id="rating" name="sorting_option[]" value="rating" <?php if(!empty($tmpdata['sorting_option']) && in_array('rating',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php echo __('Rating',ADMINDOMAIN);?></label><br />
				<?php } ?>
				<label for="reviews"><input type="checkbox" id="reviews" name="sorting_option[]" value="reviews" <?php if(!empty($tmpdata['sorting_option']) && in_array('reviews',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php echo __('Reviews ',ADMINDOMAIN);?></label><br />
				<label for="title_asc"><input type="checkbox" id="title_asc" name="sorting_option[]" value="title_asc" <?php if(!empty($tmpdata['sorting_option']) && in_array('title_asc',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php  echo __('Title Ascending',ADMINDOMAIN);?></label><br/>
				<label for="title_desc"><input type="checkbox" id="title_desc" name="sorting_option[]" value="title_desc" <?php if(!empty($tmpdata['sorting_option']) && in_array('title_desc',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php echo __('Title Descending',ADMINDOMAIN);?></label><br />
				
				
				<?php do_action('taxonomy_sorting_option','sorting_option');?>
				<p class="description"><?php echo __('For the "Rating" option to work you must enable the "Show rating" setting available in the "Detail Page" tab above.',ADMINDOMAIN);?></p>
				<script type="text/javascript">
				function SelectAllSorting()
				{
					jQuery('.templatic_sorting').find(':checkbox').attr('checked', jQuery('.checkall').is(":checked"));
				}
				</script>
			</td>
		</tr>
        <?php do_action('after_listing_page_sorting');/* do action after listing page sorting option*/
		endif;?>
			<tr>
				<td colspan="2">
				<p class="submit" style="clear: both;">
				  <input type="submit" name="Submit"  class="button button-primary button-hero" value="<?php echo __('Save All Settings',ADMINDOMAIN);?>" />
				  <input type="hidden" name="settings-submit" value="Y" />
				</p>
				</td>
			</tr>
		</table>
		
		<table id="detail_page_settings" class="tmpl-general-settings form-table">
		<tr>
			<td colspan="2"><p class="tevolution_desc"><?php echo __('Control your detail page settings from this screen which will affect the user experience for your visitors.',ADMINDOMAIN);?></p></div>
		    </td>
		</tr> 
		<?php do_action('before_detail_page_setting');

		do_action('before_related_post');?>
		 <tr>
				<th><label><?php echo __('Filter related posts by',ADMINDOMAIN);	$related_post =  @$tmpdata['related_post']; ?></label></th>
				<td>
					<label for="related_post_categories"><input id="related_post_categories" type="radio" name="related_post" value="categories"  <?php if(isset($related_post) && $related_post=='categories') echo 'checked'; ?>/>&nbsp;<?php echo __('Category',ADMINDOMAIN);?></label>&nbsp;&nbsp;
					<label for="related_post_tags"> <input id="related_post_tags" type="radio" name="related_post" value="tags" <?php if(isset($related_post) && $related_post=='tags') echo 'checked'; ?>/>&nbsp;<?php echo __('Tag',ADMINDOMAIN);?></label>
				</td>
			 </tr>
			 <tr>
				<th><label><?php echo __('Number of related posts',ADMINDOMAIN);	$related_post_numbers =  @$tmpdata['related_post_numbers']; ?></label></th>
				<td>
					<label for="related_post_numbers">
						<input id="related_post_numbers" type="text" value="<?php if(isset($related_post_numbers)){ echo @$related_post_numbers;}else{ echo 3;}  ?>" size="4" name="related_post_numbers">
					</label>
				</td>
			 </tr>
			
		<?php do_action('after_related_post');
		
		if(!current_theme_supports('remove_tevolution_sharing_opts')){ ?>                         
			<tr>
				<th><label><?php echo __('View counters',ADMINDOMAIN);	$templatic_view_counter =  @$tmpdata['templatic_view_counter']; ?></label></th>
				<td>
					<div class="input-switch">						 
					<input type="checkbox" name="templatic_view_counter" value="Yes" <?php if($templatic_view_counter == 'Yes' || $templatic_view_counter ==''){?>checked="checked"<?php }?> id="yes" /><label for="yes">&nbsp;<?php echo __('Enable',ADMINDOMAIN);?>	</label>		 					 
					</div>
				</td>
			</tr>
			<tr>
				<th><label><?php echo __('Show sharing buttons',ADMINDOMAIN);?></label></th>
				<td>
					<?php
					$facebook_share_detail_page =  @$tmpdata['facebook_share_detail_page']; 
					$google_share_detail_page =  @$tmpdata['google_share_detail_page'];
					$twitter_share_detail_page =  @$tmpdata['twitter_share_detail_page'];
					$pintrest_detail_page =  @$tmpdata['pintrest_detail_page'];
					?>
					<label for="facebook_share_detail_page_yes"><input id="facebook_share_detail_page_yes" type="checkbox" name="facebook_share_detail_page" value="yes"  <?php if(isset($facebook_share_detail_page) && $facebook_share_detail_page=='yes') echo 'checked'; ?>/>&nbsp;<?php echo __('Facebook',ADMINDOMAIN);?></label> <br/>
					
					<label for="google_share_detail_page_yes"><input id="google_share_detail_page_yes" type="checkbox" name="google_share_detail_page" value="yes"  <?php if(isset($google_share_detail_page) && $google_share_detail_page=='yes') echo 'checked'; ?>/>&nbsp;<?php  echo __('GooglePlus',ADMINDOMAIN);?></label> <br/>
					
					<label for="pintrest_detail_page_yes"><input id="pintrest_detail_page_yes" type="checkbox" name="pintrest_detail_page" value="yes"  <?php if(isset($pintrest_detail_page) && $pintrest_detail_page=='yes') echo 'checked'; ?>/>&nbsp;<?php  echo __('Pintrest',ADMINDOMAIN);?></label> <br/>
					
					<label for="twitter_share_detail_page_yes"><input id="twitter_share_detail_page_yes" type="checkbox" name="twitter_share_detail_page" value="yes"  <?php if(isset($twitter_share_detail_page) && $twitter_share_detail_page=='yes') echo 'checked'; ?>/>&nbsp;<?php  echo __('Twitter',ADMINDOMAIN);?></label> <br/>
					
					<p class="description"><?php echo __('Once enabled, selected sharing buttons will appear below the image gallery on detail pages',ADMINDOMAIN);?></p>
					<?php do_action('test'); ?>
				</td>
				
			</tr>                        
			<?php 
		} 
		do_action('after_detail_page_setting');
		
		?>
		 <tr>
			<td colspan="2">
			<p class="submit" style="clear: both;">
			  <input type="submit" name="Submit"  class="button button-primary button-hero" value="<?php echo __('Save All Settings',ADMINDOMAIN);?>" />
			  <input type="hidden" name="settings-submit" value="Y" />
			</p>
			</td>
		</tr>
		</table>

	
		<?php
}
	

/*
	post_expire_session_table_create table, when any post expired then it willmanage with the entry of this table
 */
function post_expire_session_table_create(){
	global $wpdb,$pagenow,$table_name;
	$table_name = $wpdb->prefix . "post_expire_session";
	
	if(($pagenow=='plugins.php' || (isset($_REQUEST['page']) && ($_REQUEST['page']=='templatic_system_menu' || $_REQUEST['page']=='transcation' || $_REQUEST['page']=='monetization'))) && get_option('tev_table_updates') !='inserted'){
		
		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
		{
			$sql = 'CREATE TABLE `'.$table_name.'` (
						`session_id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`execute_date` DATE NOT NULL ,
						`is_run` TINYINT( 4 ) NOT NULL DEFAULT "0"
					)DEFAULT CHARSET=utf8';
			$wpdb->query($sql);
		}
		$field_check = $wpdb->get_var("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_price'");
		if('term_price' != $field_check){
			$wpdb->query("ALTER TABLE $wpdb->terms ADD term_price varchar(100) NOT NULL DEFAULT '0'");
		}
		
		update_option('tev_table_updates','inserted');
	}
	
}
/*
 * display message on admin notices for clear tevolution query cache
 */
function tevolution_custom_fields_notice(){
	global $wpdb; 
	$taxonomy = get_option("templatic_custom_taxonomy");
	$tag = get_option("templatic_custom_tags");
	if((isset($_REQUEST['ctab']) && ($_REQUEST['ctab']=='custom_fields' || $_REQUEST['page']=='custom_taxonomies_permalink' ) && !isset($_REQUEST['activated'])) || (isset($_REQUEST['taxonomy']) && array_key_exists($_REQUEST['taxonomy'],$taxonomy)) || (isset($_REQUEST['taxonomy']) && array_key_exists($_REQUEST['taxonomy'],$tag)) || (isset($_REQUEST['page']) && ($_REQUEST['page']=='custom_setup' || $_REQUEST['page']=='templatic_settings' ) )){
			
			if(isset($_POST['tevolution_query']) && $_POST['tevolution_query']!='' && isset($_POST['tevolution_cache']) && $_POST['tevolution_cache']==1){
				$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name like '%s'",'%_tevolution_query_%' ));
				update_option('tevolution_query_cache',0);
				?>
                    <div id="tevolution_message" class="updated fade below-h2">
                    <p><?php echo __('Tevolution cache has been successfully cleared.',ADMINDOMAIN);?></p>
                    </div>
                    <?php
			}
			
			if(isset($_POST['tevolution_query_cache']) && $_POST['tevolution_query_cache']!=''){				
				update_option('tevolution_cache_disable',$_REQUEST['tevolution_cache_disable']);
			}
			
		$tevolution_query_cache=get_option('tevolution_query_cache');
		$tevolution_cache_disable=get_option('tevolution_cache_disable');
		do_action('before_cache_msg');
		if(!isset($_POST['tevolution_query']))
		{ ?>
			<div id="message" class="update-nag below-h2 tev-cache-msg clearfix" style="width: 60%; height: 40px;">
				
                   <?php if($tevolution_cache_disable ==1){ ?> 
				   <div>
				   <form action="" method="post" style="width: 70%; float: left;">
				   <input type="hidden" name="tevolution_cache" value="1" />
					<p><?php echo __('In order to apply the changes made to the site you must clear the Tevolution cache.',ADMINDOMAIN);?> <input class="button-primary" type="submit" name="tevolution_query" value="<?php echo __('Clear cache',ADMINDOMAIN);?>" /></p>	
					</form>
				    <form action="" method="post" style="float: left; margin: 12px 0px;">
						<input type="hidden" name="tevolution_cache_disable" value=""/><input class="button-secondary" type="submit" name="tevolution_query_cache" value="<?php echo __('Disable Cache',ADMINDOMAIN);?>" />
					</form></div>
                    
					<?php }else{ ?>
					<form action="" method="post" style="width: 50%; float: left; text-align:right;"><p><?php echo __('Tevolution caching is disabled.',ADMINDOMAIN);?>&nbsp;&nbsp;</p></form>
					<form action="" method="post" style="width: 50%; float: left; margin: 10px 0px; text-align:left;">
					  <input type="hidden" name="tevolution_cache_disable" value="1"/><input class="button-secondary" type="submit" name="tevolution_query_cache" value="<?php echo __('Enable Cache',ADMINDOMAIN);?>" />	
					</form>
					<?php } ?>
			</div>
        <?php
		}
		do_action('after_cache_msg');
	}
}

/*
* change information for upgraded post
*/
add_action('init','tevolution_post_upgrade_success');

function tevolution_post_upgrade_success(){
   if(isset($_REQUEST['pmethod']) && $_REQUEST['pmethod'] !='' && isset($_REQUEST['pid']) && $_REQUEST['pid'] !='' & $_REQUEST['upgrade'] =='pkg'){
		remove_action('paypal_successfull_return_content','successfull_return_paypal_content',10);
		remove_action('paypalpro_successfull_return_content','successfull_return_paypal_content',10);
		remove_action('paypalpro_submit_post_details','successfull_return_paypal_content',10);
		remove_action('strip_successfull_return_content','successfull_return_paypal_content',10);
		remove_action('strip_submit_post_details','successfull_return_paypal_content',10);
		remove_action('strip_successfull_return_content','successfull_return_paypal_content',10);
		remove_action('braintree_successfull_return_content','successfull_return_paypal_content',10);
		
		add_action('inspire_commerce_successfull_return_content','successfull_return_paypal_content_message',10);
		add_action('braintree_successfull_return_content','successfull_return_paypal_content_message',10);
		add_action('strip_submit_post_details','successfull_return_paypal_content_message',10);
		add_action('paypal_successfull_return_content','successfull_return_paypal_content_message',10);
		add_action('paypalpro_submit_post_details','successfull_return_paypal_content_message',10);
		remove_action('tevolution_submition_success_post_content','tevolution_submition_success_post_submited_content',10);
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/post_upgrade_pay_success.php");
   }
}

/*
 * change succes message of post upgrade
 */
function successfull_return_paypal_content_message(){

	global $post,$wp_query;
	$pid = @$_REQUEST['pid'];
	$pkg_details = get_post_meta($pid,'upgrade_data',true);
	$paid_amount = get_post_meta($pid,'paid_amount',true);
	$package_select = $pkg_details['package_select'];
	$pkgdata = get_post($package_select); 
	$ptype = get_post_meta($pkgdata->ID,'package_type',true);
	if($ptype ==2){
		$allow = get_post_meta($pkgdata->ID,'limit_no_post',true);
		$taxonomies = get_post_meta($pkgdata->ID,'package_post_type',true);
		$pst_types = get_post_types();
		$taxonomies = explode(',',$taxonomies);
		for($t=0; $t<=count($taxonomies); $t++){
			if(in_array($taxonomies[$t],$pst_types)){
				$taxonomies1 .= Ucfirst($taxonomies[$t]).",";
			}
		}
		
		$subcontent = sprintf(__('This package allows you to submit %s posts in %s',ADMINDOMAIN),$allow,"<strong>".rtrim($taxonomies1,',')."</strong>" );
	}
	$post_tax = fetch_page_taxonomy($pkg_details['cur_post_id']);
	/* Here array separated by category id and price amount */
	
	if($pkg_details['category'])
	{
		$category_arr = $pkg_details['category'];
		foreach($category_arr as $_category_arr)
		 {
			$category[] = explode(",",$_category_arr);
		 }
		foreach($category as $_category)
		 {
			 $post_category[] = $_category[0];
		 }
	}
	/* set post categories start */
	wp_set_post_terms( $pid,'',$post_tax,false);
	if($post_category){
		foreach($post_category as $_post_category)
		{ 
			if(taxonomy_exists($post_tax)):
				wp_set_post_terms( $pid,$_post_category,$post_tax,true);
			endif;
		}
	} 
	/* set post categories end */
	$pkgname = $pkgdata->post_title;
	$subject = __('Payment procedure has been completed.',ADMINDOMAIN);
	
	$content = sprintf(__('You have now upgraded to a new package %s You paid %s to upgrade your package.',ADMINDOMAIN),"<strong>".$pkgname."</strong>.<br/>","<strong>".display_amount_with_currency_plugin($paid_amount)."</strong>");
	echo "<h2>".$subject."</h2>";
	echo "<p>".$content."</p>";
	echo "<p>".$subcontent."</p>";
}

/* Hook which display the captcha settings on general settings area of tevolution */

add_action('templatic_general_setting_data','tmpl_captcha_setting_option',30);
function tmpl_captcha_setting_option(){
	$tmpdata = get_option('templatic_settings');	
	$user_verification_page =  @$tmpdata['user_verification_page'];
	do_action('tmpl_before_start_captcha_settings');
	/*if captcha plugin is enabled than deactivate and update the option from it in templatic settings*/
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(is_plugin_active( 'wp-recaptcha/wp-recaptcha.php' ))
	{
		$captcha_option = get_option("recaptcha_options");
		$site_key = $captcha_option['site_key'];
		$secretkey = $captcha_option['secret'];
		$comments_theme = $captcha_option['comments_theme'];
		$recaptcha_language = $captcha_option['recaptcha_language'];
		$tmpdata['site_key'] = $site_key;
		$tmpdata['secret'] = $secretkey;
		$tmpdata['comments_theme'] = $comments_theme;
		$tmpdata['captcha_language'] = $recaptcha_language;
		/*update captcha settings*/
		update_option('templatic_settings',$tmpdata);
		/*deactivate captcha plugin*/
		deactivate_plugins( ( 'wp-recaptcha/wp-recaptcha.php' ) );
	}
	
	$lang_array = array('en'=>__('English',ADMINDOMAIN),'ar'=>__('Arabic',ADMINDOMAIN),'bg'=>__('Bulgarian',ADMINDOMAIN),'ca'=>__('Catalan Valencian',ADMINDOMAIN),'cs'=>__('Czech',ADMINDOMAIN),'da'=>__('Danish',ADMINDOMAIN),'de'=>__('German',ADMINDOMAIN),'el'=>__('Greek',ADMINDOMAIN),'en_gb'=>__('British English',ADMINDOMAIN),'es'=>__('Spanish',ADMINDOMAIN),'fa'=>__('Persian',ADMINDOMAIN),'fr'=>__('French',ADMINDOMAIN),'fr_ca'=>__('Canadian French',ADMINDOMAIN),'hi'=>__('Hindi',ADMINDOMAIN),'hr'=>__('Croatian',ADMINDOMAIN),'hu'=>__('Hungarian',ADMINDOMAIN),'id'=>__('Indonesian',ADMINDOMAIN),'it'=>__('Italian',ADMINDOMAIN),'iw'=>__('Hebrew',ADMINDOMAIN),'ja'=>__('Jananese',ADMINDOMAIN),'ko'=>__('Korean',ADMINDOMAIN),'lt'=>__('Lithuanian',ADMINDOMAIN),'lv'=>__('Latvian',ADMINDOMAIN),'nl'=>__('Dutch',ADMINDOMAIN),'no'=>__('Norwegian',ADMINDOMAIN),'pl'=>__('Polish',ADMINDOMAIN),'pt'=>__('Portuguese',ADMINDOMAIN),'ro'=>__('Romanian',ADMINDOMAIN),'ru'=>__('Russian',ADMINDOMAIN),'sk'=>__('Slovak',ADMINDOMAIN),'sl'=>__('Slovene',ADMINDOMAIN),'sr'=>__('Serbian',ADMINDOMAIN),'sv'=>__('Swedish',ADMINDOMAIN),'th'=>__('Thai',ADMINDOMAIN),'tr'=>__('Turkish',ADMINDOMAIN),'uk'=>__('Ukrainian',ADMINDOMAIN),'vi'=>__('Vietnamese',ADMINDOMAIN),'zh_cn'=>__('Simplified Chinese',ADMINDOMAIN),'zh_tw'=>__('Traditional Chinese',ADMINDOMAIN));
	?>
	<table id="captcha_settings" class="tmpl-general-settings form-table">
    <tr>
		<td colspan="2"><p class="tevolution_desc"><?php echo __('Keep your website spam-free by activating reCAPTCHA challenge to any of the functions or pages below. Please register your website for reCAPTCHA <a title="Get your CAPTCHA API Keys" href="https://www.google.com/recaptcha/admin#list">here</a> and add the keys you will get from there in the fields given below.',ADMINDOMAIN); ?></p>
		</td>
	</tr> 
    <tbody>
    	<tr valign="top">
            <th scope="row"><?php echo __('Site Key',ADMINDOMAIN); ?></th>
            <td>
               <input type="text" value="<?php  echo @$tmpdata['site_key'] ?>" size="40" name="site_key">
            </td>
         </tr>
         <tr valign="top">
            <th scope="row"><?php echo __('Secret Key',ADMINDOMAIN); ?></th>
            <td>
               <input type="text" value="<?php  echo @$tmpdata['secret'] ?>" size="40" name="secret">
            </td>
         </tr>
      </tbody>
      <tbody><tr valign="top">
            <th scope="row"><?php echo __('Theme',ADMINDOMAIN); ?></th>
            <td>
               <select id="comments_theme" name="comments_theme">
					<option <?php if($tmpdata['comments_theme'] == 'standard'){ ?>selected="selected" <?php } ?> value="standard"><?php echo __('Standard',ADMINDOMAIN); ?></option> 
                	<option <?php if($tmpdata['comments_theme'] == 'light'){ ?>selected="selected" <?php } ?> value="light"><?php echo __('Light',ADMINDOMAIN); ?></option> 
                	<option <?php if($tmpdata['comments_theme'] == 'dark'){ ?>selected="selected" <?php } ?> value="dark"><?php echo __('Dark',ADMINDOMAIN); ?></option> 
                </select> 
            </td>
         </tr>

         <tr valign="top">
            <th scope="row"><?php echo __('Language',ADMINDOMAIN); ?></th>
            <td>
				<select id="captcha_language" name="captcha_language">
                	<?php foreach($lang_array as $key=>$val)
					{ ?>
						<option <?php if($tmpdata['captcha_language'] == $key){ ?>selected="selected" <?php } ?> value="<?php echo $key; ?>"><?php echo $val; ?></option> 
                  <?php }
                     ?>
               	</select> 
            </td>
         </tr>
      </tbody>
    <tr>
        <th><label><?php echo __('Enable spam verification for',ADMINDOMAIN);?></label></th>
        <td class="captcha_chk">            
            <label><input type='checkbox' name="user_verification_page[]" id="user_verification_page" <?php if(count($user_verification_page) > 0 && in_array('registration', $user_verification_page)){ echo "checked=checked"; } ?> value="registration"/> <?php echo __('Registration page and Comment form',ADMINDOMAIN); ?></label><div class="clearfix"></div>
            <label><input type='checkbox' name="user_verification_page[]" id="user_verification_page" <?php if(count($user_verification_page) > 0 && in_array('submit', $user_verification_page)){ echo "checked=checked"; } ?> value="submit"/> <?php echo __('Submit listing page',ADMINDOMAIN); ?></label><div class="clearfix"></div>				  
            <label><input type='checkbox' name="user_verification_page[]" id="user_verification_page" <?php if(count($user_verification_page) > 0 && in_array('claim', $user_verification_page)){ echo "checked=checked"; } ?> value="claim"/> <?php echo __('Claim Ownership',ADMINDOMAIN); ?></label><div class="clearfix"></div>
            <label><input type='checkbox' name="user_verification_page[]" id="user_verification_page" <?php if(count($user_verification_page) > 0 && in_array('emaitofrd', $user_verification_page)){ echo "checked=checked"; } ?> value="emaitofrd"/> <?php echo __('Email to Friend',ADMINDOMAIN); ?></label><div class="clearfix"></div><div class="clearfix"></div>
            <label><input type='checkbox' name="user_verification_page[]" id="user_verification_page" <?php if(count($user_verification_page) > 0 && in_array('sendinquiry', $user_verification_page)){ echo "checked=checked"; } ?> value="sendinquiry"/> <?php echo __('Send Inquiry',ADMINDOMAIN); ?></label><div class="clearfix"></div><div class="clearfix"></div>
        
        </td>
    </tr>
	<tr>
		<td colspan="2">
			<p class="submit" style="clear: both;">
			<input type="submit" name="Submit"  class="button button-primary button-hero" value="<?php echo __('Save All Settings',ADMINDOMAIN);?>" />
			<input type="hidden" name="settings-submit" value="Y" />
			</p>
		</td>
	</tr>
	</table>
    <?php
	do_action('tmpl_after_start_captcha_settings');
}

function templatic_site_info_tracking_notice(){
	global $wpdb; 
	
        if(isset($_POST['tmpl_site_info_tracking_allow']) || isset($_POST['tmpl_site_info_tracking_not_allow'])){
            /* if true than allow */
            if(isset($_POST['tmpl_site_info_tracking_allow'])){
                update_option('tmpl_site_info_tracking',1);
            }else{
                update_option('tmpl_site_info_tracking',2);
            }
        }
        $is_set = get_option("tmpl_site_info_tracking");
        
        if($is_set =='' && ((isset($_REQUEST['ctab']) && ($_REQUEST['ctab']=='custom_fields' || $_REQUEST['page']=='custom_taxonomies_permalink' ) && !isset($_REQUEST['activated'])) || (isset($_REQUEST['page']) && ($_REQUEST['page']=='custom_setup' || $_REQUEST['page']=='templatic_settings' )) )){
	 ?>
            <div id="message" class="update-nag below-h2 tev-cache-msg clearfix" style="width: 60%; height: 40px;">
                <div>
                    <form action="" method="post" style="width: 70%; float: left;">
                        
                         <p><?php echo __('Tevolution may get site data periodicaly from the site .',ADMINDOMAIN);?> 
                            <input class="button-primary" type="submit" name="tmpl_site_info_tracking_allow" value="<?php echo __('Allow',ADMINDOMAIN);?>" />
                            <input class="button-secondary" type="submit" name="tmpl_site_info_tracking_not_allow" value="<?php echo __('Do Not Allow',ADMINDOMAIN);?>" />
                         </p>	
                    </form>
                </div>
            </div>
                        
        <?php } ?>
            
        <?php
}
?>