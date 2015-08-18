<?php
/*
 * Function Name: tevolution_breadcrumb_trail_items
 * Return: display the breadcrumb as per submit.edit and delete submit page.
 */

add_filter('breadcrumb_trail_items','tevolution_breadcrumb_trail_items');
function tevolution_breadcrumb_trail_items($trail){
	global $post;	
	$post_type = get_post_type(@$_REQUEST['pid']);
	if(get_post_meta(@$post->ID,'submit_post_type',true)!="" && $post_type==get_post_meta(@$post->ID,'submit_post_type',true)){
		$replace_title='Submit '.ucfirst($post_type);
		if(@$_REQUEST['action'] =='delete'){
			$title = __("Delete ".$post_type);
		}
		if(@$_REQUEST['action'] =='edit'){
			$title = __("Edit ".$post_type);
		}
		
		if(in_array(ucfirst($replace_title),$trail)){
			$trail[1]=$title;
		}
	}	
	return $trail;
}
/*
 * Function Name: tevolution_form_page_template
 * Return: display the submit form from front end side
 */
if(isset($_REQUEST['pid']) && isset($_REQUEST['action']) && $_REQUEST['pid']!="" && $_REQUEST['action']!=""){
 add_action('the_title','tevolution_submit_the_title',10,2);	
	function tevolution_submit_the_title($title,$post_id){		
		
		$post_type = get_post_type($_REQUEST['pid']);		
		if(get_post_meta($post_id,'submit_post_type',true)!="" && $post_type==get_post_meta($post_id,'submit_post_type',true)){
			$PostTypeObject = get_post_type_object($post_type);
			$_PostTypeName = $PostTypeObject->labels->name;
			if($_REQUEST['action'] =='delete'){
				$title = __("Delete ",DOMAIN);
				$title .= $_PostTypeName;
			}
			if($_REQUEST['action'] =='edit'){
				$title = __("Edit ",DOMAIN);
				$title .= $_PostTypeName;
			}
		}		
		return $title;
	}
} 
function tevolution_form_page_template($atts){
	extract( shortcode_atts( array (
			'post_type'   =>'post',				
			), $atts ) 
		);	
	ob_start();
	remove_filter( 'the_content', 'wpautop' , 12);
	
	
	global $wpdb,$post,$current_user,$all_cat_id,$monetization,$validation_info;
	/* set the submit post type on submit form page */
	if(get_post_meta($post->ID,'submit_post_type',true)=="" || $post_type!=get_post_meta($post->ID,'submit_post_type',true)){
		update_post_meta($post->ID,'submit_post_type',$post_type);	
	}
	
	if(get_post_meta($post->ID,'is_tevolution_submit_form',true)=="" || '1'!=get_post_meta($post->ID,'is_tevolution_submit_form',true)){
		update_post_meta($post->ID,'is_tevolution_submit_form',1);	
	}
	
	do_action('submit_form_before_content');
	add_action('wp_head','register_submit_js');
	$submit_post_type = get_post_meta($post->ID,'submit_post_type',true);
	/* If user not login and try to edit post then run this code */
	if(!$current_user->ID && isset($_REQUEST) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['pid']))
	{
		wp_redirect(get_tevolution_login_permalink());
		exit;
	}	
	if ( (isset($_GET['action']) && isset($_GET['_wpnonce'])) && (! wp_verify_nonce( $_GET['_wpnonce'], 'edit_link' ) )){
		return '<p>'.__('your security settings do not permit you to edit this content',DOMAIN).'</p>';
	}
		
	/* End */
	if($submit_post_type!=$post_type && $submit_post_type!='')
	{
		echo '<span class="message_error2">'.__("The tevolution post type and tevolution submit form shortcode post type doesn't match. Please select the same post type.",DOMAIN).'</span>';
		return;
	}
	$post_type_search = in_array($post_type,array_keys(get_option('templatic_custom_post')));

	if(!$post_type_search && $post_type !='post')
	 {		
		echo '<p><span class="message_error2" >'.__('You have not selected any post type yet',DOMAIN).'</span></p>';
		return ;
	 }
	 
	global $cat_array;
	$cat_array = array();
	$tmpdata = get_option('templatic_settings');
	
	/*Check request pid */
	$edit_id =(isset($_REQUEST['pid']) && $_REQUEST['pid']!='')?$_REQUEST['pid']: '';
	
	if((isset($_REQUEST['category']) && count($_REQUEST['category']) > 0 ) || $tmpdata['templatic-category_custom_fields'] == 'No' || $_REQUEST['fields']){
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			if(isset($_REQUEST['lang'])){
				$url = site_url().'/?page=preview&lang='.$_REQUEST['lang'];
			}elseif($sitepress->get_current_language()){
				
				if($sitepress->get_default_language() != $sitepress->get_current_language()){
					$url = site_url().'/'.$sitepress->get_current_language().'/?page=preview';
				}else{
					$url = site_url().'/?page=preview';
				}	
			}else{
				$url = site_url().'/?page=preview';
			}
		}else{
			$url = site_url().'/?page=preview';
		}
		$form_action_url = tmpl_get_ssl_normal_url($url);	
	}else if($_REQUEST['backandedit'] && isset($_SESSION['custom_fields'])){
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			if(isset($_REQUEST['lang'])){
				$url = get_permalink($post->ID).'/?backandedit=1&fields=custom_fields&lang='.$_REQUEST['lang'];
			}elseif($sitepress->get_current_language()){
				if(strpos(get_permalink($post->ID),$sitepress->get_current_language()) == false && $sitepress->get_default_language() != $sitepress->get_current_language())
					$url = get_permalink($post->ID).'/'.$sitepress->get_current_language().'/?backandedit=1&fields=custom_fields';
				else
					$url = get_permalink($post->ID).'?backandedit=1&fields=custom_fields';
			}else{
				$url = get_permalink($post->ID).'?backandedit=1&fields=custom_fields';
			}
		}else{
			$url = get_permalink($post->ID).'?backandedit=1&fields=custom_fields';
		}
		$form_action_url = $url;
	}elseif($tmpdata['templatic-category_custom_fields'] == 'Yes' && $_REQUEST['action'] == 'edit'){
		//$url=(isset($_REQUEST['lang']) && $_REQUEST['lang']!='')? site_url().'/?page=preview&lang='.$_REQUEST['lang'] : site_url().'/?page=preview';
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			if(isset($_REQUEST['lang'])){
				$url = site_url().'/?page=preview&lang='.$_REQUEST['lang'];
			}elseif($sitepress->get_current_language()){
			
				if($sitepress->get_default_language() != $sitepress->get_current_language()){
					$url = site_url().'/'.$sitepress->get_current_language().'/?page=preview';
				}else{
					$url = site_url().'/?page=preview';
				}
				
			}else{
				$url = site_url().'/?page=preview';
			}
		}else{
			$url = site_url().'/?page=preview';
		}
		$form_action_url = tmpl_get_ssl_normal_url($url);	
	}else{
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;			
			if(isset($_REQUEST['lang'])){
				$url = get_permalink($post->ID).'/?lang='.$_REQUEST['lang'];
			}elseif($sitepress->get_current_language()==$sitepress->get_default_language()){
				$url = get_permalink($post->ID);
			}else{
				$url = get_permalink($post->ID);
			}
		}else{
			$url = get_permalink($post->ID);
		}
		$form_action_url = $url;
	}
	
	$post_id = $post->ID;	
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
	$taxonomy = $taxonomies[0];
	/*Display the category  type like checkbox, select box or multicheckbox*/
	$cat_display = (@$tmpdata['templatic-category_type']!="")? $tmpdata['templatic-category_type'] : 'checkbox';
	
	
	
	if(isset($_REQUEST['category']) && count($_REQUEST['category']) > 0)
	{		
		$_SESSION['category'] =  $_REQUEST['category'];
	}
	
	/*Fetch category id */
	if(isset($edit_id) && $edit_id !=''){
		global $monetization;		
		$get_category = wp_get_post_terms($edit_id,$taxonomy);
		foreach($get_category as $_get_category)
		{
			$cat_array[] = $_get_category->term_id;
		}
		$all_cat_id = implode(',',$cat_array);		
	}else{
		if(isset($cat_display) && $cat_display == 'checkbox' && isset($_SESSION['category']) && $_SESSION['category'] != '')
		{
			foreach($_SESSION['category'] as $_category_arr)
			{
				$category = explode(",",$_category_arr);
				$cat_array[] = $category[0];
			}			
		}else
		{	
			if(isset($_SESSION['category']) && class_exists('monetization'))
			{				
				$cat_array = $monetization->templ_get_selected_category_id($_SESSION['category']);
				$cat_array_price = $monetization->templ_fetch_category_price($_SESSION['category']);				
			}
			
		}
	}	
	/*End category id */
	/*Fetch form fields array */
	$form_fields=fetch_submit_page_form_fields($taxonomy);
	
	$validation_info = array();
	foreach($form_fields as $key=>$val)	{	
		$str = ''; $fval = '';
		$field_val = $key.'_val';
		$val['title']=(isset($val['title']))? $val['title'] :'';		
		$validation_info[] = array(
							'title'	       => $val['title'],
							'name'	       => $key,
							'espan'	       => $key.'_error',
							'type'	       => $val['type'],
							'text'	       => $val['text'],
							'is_require'	  => @$val['is_require'],
							'validation_type'=> $val['validation_type'],
							'search_ctype'=> $val['search_ctype']
					);
	}
	
	
	/* CONDOTION TO SHOW AN ERROR MSG IF USER'S IP IS BLOCKED */
	$ip = (function_exists('templ_fetch_ip'))?templ_fetch_ip():'';
	if($ip == ""){ 
		global $post,$wp_query;  
		/*  Edit title of submit form page template when editing any post START  */
		
		/*  Edit title of submit form page template when editing any post END  */
		
		if(isset($_REQUEST['ecptcha']) == 'captch') {
			$a = get_option("recaptcha_options");
			$blank_field = $a['no_response_error'];
			$incorrect_field = $a['incorrect_response_error'];
			echo '<div class="error_msg">'.$incorrect_field.'</div>';
		}
		if(isset($_REQUEST['invalid']) == 'playthru') {
			echo '<div class="error_msg">You need to play the game to submit post successfully.</div>';
		}
		?>
          <!-- Start Login Form -->
		<?php if($current_user->ID=='' && is_active_addons('templatic-login') && !isset($_REQUEST['category']) && count($_REQUEST['category']) <= 0 && ($current_user->ID=='' && !isset($_REQUEST['fields']) && $_REQUEST['fields'] =='')  || ($current_user->ID=='' && $tmpdata['templatic-category_custom_fields'] == 'No') && is_active_addons('templatic-login')) {  
                    if(is_active_addons('templatic-login') && $current_user->ID=='' ){
					$_SESSION['redirect_to']=get_permalink();
					templ_fecth_login_onsubmit();
				}
				/*Unset the frontend redirect to session variable after login or register */
				unset($_SESSION['redirect_to']);
          } ?>
          <!-- End Login Form -->
          <?php
		/* Edit Form Security Code */
			$post_sql = $wpdb->get_row("select post_author,ID from $wpdb->posts where post_author = '".$current_user->ID."' and ID = '".@$_REQUEST['pid']."'");
			if((count($post_sql) <= 0) && ($current_user->ID != '') && ($current_user->ID != 1) && (isset($_REQUEST['pid'])))
			{ 
				_e('ERROR: Sorry, you are not allowed to edit this post.',DOMAIN);
			}
			else{
				/* insert user only when templatic login.registration wizard is activated */
				if($current_user->ID =='' && $_POST['user_email'] && is_active_addons('templatic-login'))
				{	
					$_SESSION['category'] = $_REQUEST['category'];
					$_SESSION['custom_fields']['cur_post_id'] = $_POST['cur_post_id'];
					$_SESSION['test'] = $_POST['cur_post_id'];
					$current_user_id = templ_insertuser_with_listing();	
				}
				
				global $submit_form_validation_id;
				$submit_form_validation_id = "submit_form";
				echo '<form name="submit_form" id="submit_form" class="form_front_style" action="'.$form_action_url.'" method="post" enctype="multipart/form-data">';
				if(is_active_addons('templatic-login') && $current_user->ID=='' && $tmpdata['templatic-category_custom_fields'] == 'No'){
					templ_fetch_registration_onsubmit(); /* display registration form is registration addon activate */
				}
				if(is_active_addons('templatic-login') && $current_user->ID=='' && $tmpdata['templatic-category_custom_fields'] == 'Yes' && !isset($_REQUEST['category']) && count($_REQUEST['category']) <= 0){
					$_SESSION['redirect_to']=get_permalink();
					templ_fetch_registration_onsubmit(); /* display registration form is registration addon activate */
				}
				/*Unset the frontend redirect to session variable after login or register */
				unset($_SESSION['redirect_to']);	
				global $post;
				$action = @$_REQUEST['action'];
				//if(isset($_SESSION['category']) && count($_SESSION['category']) > 0 && !isset($_REQUEST['category']) && count($_REQUEST['category']) <= 0)
				if(!isset($_REQUEST['category']) && count($_REQUEST['category']) <= 0 && isset($_SESSION['category']) && $_SESSION['category']!="")
				{
					if(isset($_SESSION['category']) && $_SESSION['category']!="" && ( $_REQUEST['backandedit'] == 1 || $_REQUEST['register'] == 1 ))
					{
						$all_cat_id = implode(",",templ_get_custom_categoryid($_SESSION['category']));
					}
				}elseif(isset($_REQUEST['category']) && count($_REQUEST['category']) > 0)
				{
					$all_cat_id = implode(",",templ_get_custom_categoryid($_REQUEST['category']));
				}
				if(is_active_addons('monetization'))
				{
				/* fetch categories only when category wise custom fields are allow */				
				if(!isset($_REQUEST['category']) && count(@$_REQUEST['category']) <= 0 && !isset($_REQUEST['fields']) && @$_REQUEST['fields'] =='' && ($tmpdata['templatic-category_custom_fields'] == 'Yes' && @$_REQUEST['action'] !='edit') && !isset($_REQUEST['ecptcha']) && @$_REQUEST['register'] == '')
				{
					
					$button_text  = NEXT_STEP;
					$default_custom_metaboxes = get_post_fields_templ_plugin($post_type,'custom_fields','post');//custom fields for all category.
					if(!isset($_REQUEST['backandedit']) && $_REQUEST['backandedit'] == '' && !isset($_SESSION['category']) && count($_SESSION['category'])<=0 && (!isset($_REQUEST['register'] ) && $_REQUEST['register'] ==''))
					{
						unset($_SESSION['category']);
						unset($_SESSION['custom_fields']);
						unset($_SESSION['file_info']);
					}					
					echo '<div class="cont_box">';	
						display_custom_category_field_plugin($default_custom_metaboxes,'custom_fields','post',$post_type);//displaty  post category html.
					echo '</div>';					
				}else
				{
					$button_text  = PREVIEW_BUTTON_TEXT;
					/* fetch categories only when category wise custom fields are not allow */
					if(isset($_REQUEST['backandedit']) == 1 && isset($_REQUEST['action']) == 'edit' && $tmpdata['templatic-category_custom_fields'] == 'Yes')
					{
						$all_cat_id = implode(',',$cat_array);
					}
					if(!isset($all_cat_id)){ $all_cat_id='';}
					$custom_metaboxes = array();
					/* Fetch Heading type custom fields */
					$heading_type = fetch_heading_per_post_type($post_type);
					if(count($heading_type) > 0)
					{
						foreach($heading_type as $_heading_type){
							$custom_metaboxes[$_heading_type] = get_post_custom_fields_templ_plugin($post_type,$all_cat_id,$taxonomy,$_heading_type);//custom fields for custom post type..
						}
					}else{
						$custom_metaboxes[] = get_post_custom_fields_templ_plugin($post_type,$all_cat_id,$taxonomy,'');//custom fields for custom post type..
					}
					$default_custom_metaboxes = get_post_fields_templ_plugin($post_type,'custom_fields','post');//custom fields for default post type.
					$all_cat_id_array = explode(",",$all_cat_id);
					if($tmpdata['templatic-category_custom_fields'] == 'No'){
						if(isset($_REQUEST['action']) && $_REQUEST['action'] =='edit'){
							display_custom_category_name($default_custom_metaboxes,$all_cat_id_array,$taxonomy);//display selected category name when come for edit .
						}
						display_custom_post_field_plugin($custom_metaboxes,'custom_fields',$post_type);//displaty custom fields html.
					
					}
					if($tmpdata['templatic-category_custom_fields'] == 'Yes'){
						display_custom_category_name($default_custom_metaboxes,$all_cat_id_array,$taxonomy);//display selected category name.
						display_custom_post_field_plugin($custom_metaboxes,'custom_fields',$post_type);//displaty default post html.
					}
					/* if You have successfully activated monetization then this function will be included for listing prices */
					if(is_active_addons('monetization'))
					{
						global $monetization;
						if(class_exists('monetization')){			
							global $current_user;
							$user_have_pkg = $monetization->templ_get_packagetype($current_user->ID,$post_type); /* User selected package type*/
							$user_last_postid = $monetization->templ_get_packagetype_last_postid($current_user->ID,$post_type); /* User last post id*/
							$user_have_days = $monetization->templ_days_for_packagetype($current_user->ID,$post_type); /* return alive days(numbers) of last selected package  */
							$is_user_have_alivedays = $monetization->is_user_have_alivedays($current_user->ID,$post_type); /* return user have an alive days or not true/false */
							//check last user post package type check
							if($current_user->ID && $user_have_pkg==2)// check user wise post per  Subscription limit number post post 
							{
								$package_id=get_user_meta($current_user->ID,'package_selected',true);// get the user selected price package id
								if(!$package_id)
									$package_id=get_user_meta($current_user->ID,$post_type.'_package_select',true);// get the user selected price package id
								$user_limit_post=get_user_meta($current_user->ID,'total_list_of_post',true); //get the user wise limit post count on price package select
								if(!$user_limit_post)	
									$user_limit_post=get_user_meta($current_user->ID,$post_type.'_list_of_post',true); //get the user wise limit post count on price package select
								$package_limit_post=get_post_meta($package_id,'limit_no_post',true);// get the price package limit number of post
								$user_have_pkg = get_post_meta($package_id,'package_type',true); 
								$post_types = explode(',',get_post_meta($package_id,'package_post_type',true)); 
								if(in_array($post_type,$post_types)): $is_posttype_inpkg=1; else: $is_posttype_inpkg=0; endif; // check is this taxonomy included in package or not
							}
							
							//echo $user_have_pkg."==".$is_user_have_alivedays."==".$package_limit_post."==".$user_limit_post."==".$is_posttype_inpkg;
							if($user_have_pkg == 1  || !$is_user_have_alivedays || !$current_user->ID || $package_limit_post <= $user_limit_post || !$is_posttype_inpkg){								
								
								if(isset($edit_id) && $edit_id !='' && (!isset($_POST['renew']))){
								$pkg_id = get_post_meta($edit_id,'package_select',true); /* user comes to edit fetch selected package */
								}else{ $pkg_id =''; }
								$monetization->fetch_monetization_packages_front_end($pkg_id,'all_packages',$post_type,$taxonomy,''); /* call this function to fetch price packages which have to show even no categories selected */
								if(!isset($all_cat_id)){ $all_cat_id ==0;}elseif(isset($_REQUEST['backandedit'])){ $all_cat_id = implode(',',$cat_array);}else if(isset($edit_id) && $edit_id !=''){ $all_cat_id = $all_cat_id; }
								
								$monetization->fetch_monetization_packages_front_end($pkg_id,'packages_checkbox',$post_type,$taxonomy,$all_cat_id); /* call this function to fetch price packages */
								if(!isset($_REQUEST['action']) && isset($_REQUEST['action']) !='edit'){
										$monetization->fetch_package_feature_details($edit_id,$pkg_id,$all_cat_id); /* call this function to display fetured packages */
										if($user_have_pkg == 2 && $user_have_days > 0){
											echo "<div class='form_row clearfix act_success'>".sprintf(SUBMIT_LISTING_DAYS_TEXT,$user_have_days)."</div>";
										}	
								}
								$coupons = get_posts(array('post_type'=>'coupon_code','post_status'=>'publish')); // show only if coupon available
								if($coupons)
								{
									if(!isset($_REQUEST['action']) && isset($_REQUEST['action']) !='edit'){
										$coupon_code = '';
										if(@$_REQUEST['backandedit']) { $coupon_code = $_SESSION['custom_fields']['add_coupon']; }else if(isset($edit_id) && $edit_id !=''){ $coupon_code = get_post_meta($edit_id,'add_coupon',true); }else{ $coupon_code = ''; } /* coupon code when click ok GBE*/
										templ_get_coupon_fields($coupon_code); /* fetch coupon code */
									}
								}
							}else
							{
								$featured_type= $monetization->templ_get_featured_type($current_user->ID, $post_type);
								echo '<input type="hidden" name="all_cat" id="all_cat" value="0"/>';
								echo '<input type="hidden" name="all_cat_price" id="all_cat_price"  value="0" />';
								echo '<input type="hidden" name="feture_price" id="feture_price"  value="0" />';
								echo '<input type="hidden" name="cat_price" id="cat_price"  value="0" />';
								echo '<input type="hidden" name="last_selected_pkg" value="'.$package_id.'" />';	
								if(isset($_REQUEST['category'])){
					
									$total_price = $monetization->templ_fetch_category_price($_REQUEST['category']);
								}elseif(isset($edit_id) && $edit_id !='' && $_REQUEST['category'] ==''){
									$total_price = get_post_meta($edit_id,'total_price',true);
								}
								echo '<input type="hidden" name="total_price" id="total_price" value="'.$total_price.'" />';
								echo '<span id="result_price" style="display:none;"></span> ';
								echo '<input type="hidden" name="package_select" value="'.$package_id.'" />';
								echo '<input type="hidden" name="featured_h" value="'.get_post_meta($user_last_postid,'featured_h',true).'" />';
								echo '<input type="hidden" name="featured_c" value="'.get_post_meta($user_last_postid,'featured_c',true).'" />';
								echo '<input type="hidden" name="user_last_postid" value="'.$user_last_postid.'" />';
								
								echo "<span id='process2' style='display:none;'></span>";
								echo "<span id='packages_checkbox' style='display:none;'></span>";
								echo '<input type="hidden" name="featured_type" value="'.$featured_type.'">';
							}
						}
						//for getting the alive days	
						if($current_user->ID){if(function_exists('templ_days_for_user_packagetype'))$alive_days= $monetization->templ_days_for_user_packagetype($current_user->ID, $post_type);}
						/* monetization end */
					}
					templ_captcha_integrate('submit'); /* Display recaptcha in submit form */	
					if((!isset($_REQUEST['pid']) && $_REQUEST['pid'] == '') || ( isset($_REQUEST['renew']) && $_REQUEST['renew'] == 1))
					{
						if((!isset($_REQUEST['backandedit'] ) && $_REQUEST['backandedit'] =='' ) && (!isset($_REQUEST['register'] ) && $_REQUEST['register'] =='')){
							unset($_SESSION['category']);
							unset($_SESSION['custom_fields']);
							unset($_SESSION['file_info']);
							unset($_SESSION['user_email']);
							unset($_SESSION['user_fname']);
						}
						tevolution_show_term_and_condition(); // show terms and conditions check box
					}
				}//
			}
				?>
                         <span class="message_error2" id="common_error"></span>
                         <input type="hidden" name="cur_post_type" id="cur_post_type" value="<?php echo $post_type; ?>"  />
                         <input type="hidden" name="cur_post_taxonomy" id="cur_post_taxonomy" value="<?php echo $taxonomy; ?>"  />
                         <input type="hidden" name="cur_post_id" value="<?php echo $post_id; ?>"  />
                         <?php if(isset($edit_id) && $edit_id !=''): ?>
                             <input type="hidden" name="pid" id="pid" value="<?php echo $edit_id; ?>"  />
                        <?php endif; ?>    
                         <?php if(isset($_REQUEST['renew']) && $_REQUEST['renew'] !=''): ?>
                             <input type="hidden" name="renew" id="renew" value="<?php echo $_REQUEST['renew']; ?>"  />
                        <?php endif; 
                          global $submit_button;
                         if(!isset($submit_button)){ $submit_button = ''; }
                         ?>
                        <?php if(!isset($_REQUEST['category']) && count(@$_REQUEST['category']) <= 0 && !isset($_REQUEST['fields']) && @$_REQUEST['fields'] =='' && ($tmpdata['templatic-category_custom_fields'] == 'Yes' && @$_REQUEST['action'] !='edit')):?>
                         <input type="submit" name="preview" value="<?php  _e('Next Step',DOMAIN);?>" class="normal_button" <?php echo $submit_button; ?>/>    
                        <?php else:?>
                              <input type="submit" name="preview" value="<?php  _e('Preview This Listing',DOMAIN);?>" class="normal_button" <?php echo $submit_button; ?>/>    
                        <?php endif;
						if(isset($_SESSION['user_fname']) && $_SESSION['user_fname']!= '' && (!isset($_REQUEST['backandedit']) && $_REQUEST['backandedit'] != 1))
						{?>
                         <input type="hidden" value="<?php echo @$_SESSION['user_fname'];?>" id="user_fname" name="user_fname" >
						<?php 
						}?>
                         <input type="hidden" value="<?php echo @$alive_days;?>" id="alive_days" name="alive_days" >
                         <?php if(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'):?>
                         <input type="hidden" name="action" value="<?php echo $_REQUEST['action']?>" />
                         <?php endif;?>
                    </form>
                    <?php	
				
				include_once(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/submition_validation.php');
			}
			
		
	}else//ip block else condition
	{ 
		echo '<div class="error_msg">';
			_e('Apologies, your IP has been blocked for this domain. You are not able to use the submit form.',DOMAIN); 
		echo '</div>';
	}
	
	/* END OF BLOCK IP CONDITION */
	do_action('submit_form_after_content');
	
	return ob_get_clean();
}
/*
 * Function Name: fetch_submit_page_form_fields
 *
 */
function fetch_submit_page_form_fields($taxonomy='')
{
	global $post,$wpdb;
	$tmpdata = get_option('templatic_settings');
	$form_fields = array();
	if(!isset($_REQUEST['category']) && count(@$_REQUEST['category']) <= 0 && !isset($_REQUEST['fields']) && @$_REQUEST['fields'] =='' && $tmpdata['templatic-category_custom_fields'] == 'Yes'  && @$_REQUEST['action'] != 'edit')
	{
		$form_fields['category'] = array(
							   'name' 	      => $taxonomy,
							   'espan'	      => 'category_span',
							   'type'	           => $tmpdata['templatic-category_type'],
							   'text'	           => __('Please select Category',DOMAIN),
							   'validation_type' => 'require'
							   );
	}else{
		if($tmpdata['templatic-category_custom_fields'] == 'No')
		{
			$form_fields['category'] = array(
									   'name'	           => $taxonomy,
									   'espan'	      => 'category_span',
									   'type'	           => $tmpdata['templatic-category_type'],
									   'text'	           => __('Please select Category',DOMAIN),
									   'validation_type' => 'require'
									);
		
			$args  =	array( 'post_type' => 'custom_fields',
						  'posts_per_page' => -1	,
						  'post_status' => array('publish'),
						  'meta_query' => array(
						  					'relation' => 'AND',
											array(
												'key'     => 'post_type_'.get_post_meta($post->ID,'submit_post_type',true).'',
												'value'   => array( get_post_meta($post->ID,'submit_post_type',true),'all'),
												'compare' => 'IN',
												'type'    => 'text'
											),
											array(
												'key'     => 'show_on_page',
												'value'   =>  array('user_side','both_side'),
												'compare' => 'IN'
											),
											array(
												'key' => 'is_active',
												'value' =>  '1',
												'compare' => '='
											),
											array(
												'key'     => 'is_require',   //
												'value'   =>  '0',           // changed condition
												'compare' => '>'             //
											)
										)
						);
		}else{
				if((isset($_REQUEST['category']) && $_REQUEST['category']!="") || $_REQUEST['backandedit'] == 1)
				$all_cat_id = implode(",",templ_get_custom_categoryid($_SESSION['category']));
					$args  =  array( 'post_type' => 'custom_fields',
								  'posts_per_page' => -1	,
								  'post_status' => array('publish'),
								  'meta_query' => array(
							      					'relation' => 'AND',
													array(
														'key'     => 'post_type_'.get_post_meta($post->ID,'submit_post_type',true).'',
														'value'   =>array( get_post_meta($post->ID,'submit_post_type',true),'all'),
														'compare' => 'IN',
														'type'    => 'text'
													),
													array(
														'key'     => 'show_on_page',
														'value'   =>  array('user_side','both_side'),
														'compare' => 'IN'
													),
													array(
														'key' => 'is_active',
														'value' =>  '1',
														'compare' => '='
													),
													array(
														'key'     => 'is_require',    //
														'value'   =>  '0',    		  // changed condition	
														'compare' => '>'			  //	
													)
												),
							'tax_query' => array(
										'relation' => 'OR',
										array(
											'taxonomy' => $taxonomy,
											'field' => 'id',
											'terms' => array($all_cat_id),
											'operator'  => 'IN'
										),
										array(
											'taxonomy' => 'category',
											'field' => 'id',
											'terms' => 1,
											'operator'  => 'IN'
										)
								
							 			)
							);
		}
			$extra_field_sql = null;
			add_filter('posts_join', 'custom_field_posts_where_filter');
			$extra_field_sql = new WP_Query($args);			
			remove_filter('posts_join', 'custom_field_posts_where_filter');
			if($extra_field_sql->have_posts())
			 {
				while ($extra_field_sql->have_posts()) : $extra_field_sql->the_post();
					$title = get_the_title();
					$name = get_post_meta($post->ID,'htmlvar_name',true);
					$type = get_post_meta($post->ID,'ctype',true);
					$require_msg = get_post_meta($post->ID,'field_require_desc',true);
					$is_require = get_post_meta($post->ID,'is_require',true);
					$validation_type = get_post_meta($post->ID,'validation_type',true);
					$search_ctype = get_post_meta($post->ID,'search_ctype',true);
					if($name != 'category')
					{
						$form_fields[$name] = array(
										   'title'	      => $title,
										   'name'	           => $name,
										   'espan'	      => $name.'_error',
										   'type'	           => $type,
										   'text'	           => $require_msg,
										   'is_require'	 => $is_require,
										   'validation_type' => $validation_type,
										   'search_ctype' => $search_ctype
										 );
					}
				endwhile;
				wp_reset_query();
  			}
		
	}
	
	return $form_fields;
}
/*
Name: tevolution_tiny_mce_before_init
desc : tinymce validation.
*/
add_action('wp_head','tevolution_submit_form');
function tevolution_submit_form()
{
	global $post;
	if(!strstr($_SERVER['REQUEST_URI'],'/wp-admin/') && get_post_meta(@$post->ID,'is_tevolution_submit_form',true)==1)
		add_filter( 'tiny_mce_before_init', 'tevolution_tiny_mce_before_init',10,2 );
}
function tevolution_tiny_mce_before_init( $initArray ,$editor_id)
{
	global $validation_info,$post;
	wp_reset_query();
	wp_reset_postdata();
	
	for($i=0;$i<count($validation_info);$i++) {
			$title = $validation_info[$i]['title'];
			$name = $validation_info[$i]['name'];
			$espan = $validation_info[$i]['espan'];
			$type = $validation_info[$i]['type'];
			$text = __($validation_info[$i]['text'],DOMAIN);
			$validation_type = $validation_info[$i]['validation_type'];
			$is_required = $validation_info[$i]['is_require'];
			
			//finish post type wise replace post category, post title, post content, post expert, post images
			
			if($type=='texteditor'){
				
				{
				?>
				<script>
					var content_id = '<?php echo $name; ?>';
					var espan = '<?php echo $espan; ?>';
				</script>
			<?php
				 $initArray['setup'] = <<<JS
[function(ed) { 
    ed.onKeyUp.add(function(ed, e) {
        if(tinyMCE.activeEditor.id == content_id) {
            var content = tinyMCE.get(content_id).getContent().replace(/<[^>]+>/g, "");
            var len = content.length;
            if (len > 0) {
				jQuery('#'+espan).text("");
				jQuery('#'+espan).removeClass("message_error2");
				return true;
            } 
         }
    });
}][0]
JS;
    
				}
			}
		}
	wp_reset_query();
	wp_reset_postdata();
	return $initArray;
}
?>
