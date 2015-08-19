<?php
/*
 * Function Name: tevolution_breadcrumb_trail_items
 * Return: display the breadcrumb as per submit.edit and delete submit page.
 */
add_filter('breadcrumb_trail_items','tevolution_breadcrumb_trail_items');
function tevolution_breadcrumb_trail_items($trail){
	global $post;	
	$post_id=(isset($_REQUEST['pid']) && $_REQUEST['pid']!='')? $_REQUEST['pid'] : '';
	$post_type = get_post_type($post_id);
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
				$title = __("Edit",DOMAIN)." ";
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
	
	/* Set global variable to user any where in tevolution submit form */
	global $wpdb,$post,$current_user,$all_cat_id,$monetization,$validation_info,$submit_form_validation_id;
	$validation_info = array();
	
	/* set the submit post type on submit form page */
	if(get_post_meta($post->ID,'submit_post_type',true)=="" || $post_type!=get_post_meta($post->ID,'submit_post_type',true)){
		update_post_meta($post->ID,'submit_post_type',$post_type);	
	}
	
	/*Update submit form post meta for its a tevolution submit form */
	if(get_post_meta($post->ID,'is_tevolution_submit_form',true)=="" || '1'!=get_post_meta($post->ID,'is_tevolution_submit_form',true)){
		update_post_meta($post->ID,'is_tevolution_submit_form',1);	
	}
	
	$submit_post_type = get_post_meta($post->ID,'submit_post_type',true);
	
	/* submit form post type not match  then result submit with message */
	if($submit_post_type!=$post_type && $submit_post_type!='')
	{
		echo '<span class="message_error2">'.__("The tevolution post type and tevolution submit form shortcode post type doesn't match. Please select the same post type.",DOMAIN).'</span>';
		return;
	}
	
	/* display message no post type registered */
	$post_type_search = in_array($post_type,array_keys(get_option('templatic_custom_post')));
	if(!$post_type_search && $post_type !='post')
	{		
		echo '<p><span class="message_error2" >'.__('You have not selected any post type yet',DOMAIN).'</span></p>';
		return ;
	}
	
	/* submit_form_return add hook for return before submit form display */
	if(apply_filters('submit_form_return',false)){
		return;	
	}
	
	/* submit_form_before_content hook for add additional html or information on this hook */
	do_action('submit_form_before_content');

	$submit_form_validation_id = "submit_form";
	/* */
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		global $sitepress;
		if(isset($_REQUEST['lang'])){
			$url = site_url().'/?page=paynow&lang='.$_REQUEST['lang'];
		}elseif($sitepress->get_current_language()){
			
			if($sitepress->get_default_language() != $sitepress->get_current_language()){
				$url = site_url().'/'.$sitepress->get_current_language().'/?page=paynow';
			}else{
				$url = site_url().'/?page=paynow';
			}	
		}else{
			$url = site_url().'/?page=paynow';
		}
	}else{
		$url = site_url().'/?page=paynow';
	}
	if(function_exists('tmpl_get_ssl_normal_url'))
	{
		$form_action_url =  tmpl_get_ssl_normal_url($url);
   	}
	else
	{
		$form_action_url = $url;
	}
	
	if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=''){
		global $cat_array;	
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
		$taxonomy = $taxonomies[0];		
		$get_category = wp_get_post_terms($_REQUEST['pid'],$taxonomy);		
		foreach($get_category as $_get_category)
		{
			$cat_array[] = $_get_category->term_id;			
		}
	}
	
	echo '<form name="submit_form" id="submit_form" class="dropzone form_front_style" action="'.$form_action_url.'" method="post" enctype="multipart/form-data">';
		wp_nonce_field('submit_form_action','submit_form_nonce_field');
		echo "<input type='hidden' id='submit_post_type' name='submit_post_type' value='".$post_type."'>";
		echo "<input type='hidden' id='submit_post_url' name='submit_post_url' value='".$_SERVER["REQUEST_URI"]."'>";
		echo "<input type='hidden' id='cur_post_type' name='cur_post_type' value='".$post_type."'>";
		echo "<input type='hidden' id='submit_page_id' name='submit_page_id' value='".$post->ID."'>";
		echo "<input type='hidden' id='total_price' name='total_price' >";
		$payment_url = fetch_single_payment_url($post_type,$post->ID);
		apply_filters('fetch_payment_url',$payment_url);
		$is_user_select_subscription_pkg = 0;
		if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=""){
			$edit_id = $_REQUEST['pid'];
			echo "<input type='hidden' id='submit_pid' name='pid' value='".$_REQUEST['pid']."'>";			
			/*  edit listing*/
			if(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'){
				echo "<input type='hidden' name='action' value='edit'>";
				echo "<input type='hidden' id='action_edit' name='action_edit' value='edit'>";
				echo "<input type='hidden' id='monetize_pkg_id' name='monetize_pkg_id' value='".get_post_meta($_REQUEST['pid'],'package_select',true)."'>";
			}
			/* Renew Listing */
			if(isset($_REQUEST['renew']) && $_REQUEST['renew']=='1'){
				echo "<input type='hidden' name='renew' value='1'>";
			}			
		}
		do_action('action_before_html');
		?>
		<div class="accordion" id="post-listing" >
			
		<?php		
			
			do_action('action_before_price_package',$post_type,$post->ID);/*hook before showing price package*/
			global $monetization;
			$is_single_price_package = $monetization->tmpl_fetch_is_single_price_package($current_user->ID,$post_type,$post->ID);
			/* if You have successfully activated monetization then this function will be included for listing prices */
			if(class_exists('monetization') && function_exists('is_price_package') && is_price_package($current_user->ID,$post_type,$post->ID) > 0 )
			{
				global $monetization;
				/*while edit a listing do not show packages*/
				if((isset($edit_id) && $edit_id !='' && (isset($_REQUEST['renew']))) || (!isset($edit_id)) ){		
					/*fetch the price package*/
					$user_have_pkg = $monetization->tmpl_fetch_price_package($current_user->ID,$post_type,$post->ID);
					echo "<input type='hidden' id='is_user_select_subscription_pkg' name='is_user_select_subscription_pkg' value='1' >";
				}
			}
			do_action('action_after_price_package',$post_type,$post->ID);/*hook after showing price package*/		
		
			
			/* Start submit form details structure */
			/*while edit a listing show default post tab active*/
			if((isset($edit_id) && $edit_id !='' && (!isset($_REQUEST['renew']))) || $is_user_select_subscription_pkg == 1 || ( function_exists('is_price_package') && is_price_package($current_user->ID,$post_type,$post->ID) <= 0 ) || is_numeric($is_single_price_package)){
				$post_heading_number = 1;
				$active = 'active';
			}
			else
			{
				$post_heading_number = 2;
				$active = '';
			}
			?>            
            <div id="step-post" class="accordion-navigation step-wrapper step-post">
            <a class="step-heading active" href="#"><span><?php echo $post_heading_number; ?></span><span><?php _e('Enter Details',DOMAIN); ?></span><span><i class="fa fa-caret-down"></i><i class="fa fa-caret-right"></i></span></a>
            	<div id="post" class="step-post content <?php echo $active; ?> clearfix">
            <?php
				if(!isset($_REQUEST['pkg_id'])){
					if(@isset($user_have_pkg))
					{
						$_REQUEST['pkg_id'] = $user_have_pkg;
					}
				}
				/*get the post type taxonomy */
				$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
				$taxonomy = $taxonomies[0];
				
				/* Fetch Heading type custom fields */			
				$heading_type = fetch_heading_per_post_type($post_type);
				
				$templatic_settings = get_option('templatic_settings');
				/*fetch category while editing a post*/
				if((isset($_REQUEST['pid']) && $_REQUEST['pid']!='' && isset($_REQUEST['action']) && $_REQUEST['action']=='edit') && (isset($templatic_settings['templatic-category_custom_fields']) && $templatic_settings['templatic-category_custom_fields']=='Yes') ){
					$all_cat_id = implode(",",$cat_array);
				}
				if(count($heading_type) > 0)
				{
					foreach($heading_type as $_heading_type){
						
						$custom_metaboxes[$_heading_type] = get_post_custom_fields_templ_plugin($post_type,$all_cat_id,$taxonomy,$_heading_type);/*custom fields for custom post type..*/
					}
				}else{					
					$custom_metaboxes[] = get_post_custom_fields_templ_plugin($post_type,$all_cat_id,$taxonomy,'');/*custom fields for custom post type..*/
				}
				
				/*Apply filter hook for create submit from va;lidation info array*/
				$validation_info=apply_filters('tevolution_submit_from_validation',$validation_info,$custom_metaboxes);
	
				$default_custom_metaboxes = get_post_fields_templ_plugin($post_type,$all_cat_id,$taxonomy);/*custom fields for all category.			*/
				/* Display post type category box */
				echo '<div id="submit_category_box">';				
					display_custom_category_field_plugin($default_custom_metaboxes,'custom_fields','post',$post_type);/*displaty  post category html.*/
				echo '</div>';					
				/* Display custom fields post type wuse */
				
				echo '<div id="submit_form_custom_fields" class="submit_form_custom_fields">';
					display_custom_post_field_plugin($custom_metaboxes,'custom_fields',$post_type);/*displaty default post html.*/
				echo '</div>';
				
				/*fetch price package featured option*/
				
				global $monetization;
				if(class_exists('monetization')){
					if((isset($edit_id) && $edit_id !='' && (!isset($_REQUEST['renew']))) || $is_user_select_subscription_pkg == 1 || $_SESSION['custom_fields']['featured_h'] || $_SESSION['custom_fields']['featured_c'] || (function_exists('is_price_package') && is_price_package($current_user->ID,$post_type,$post->ID) <= 0))
					{
						if(get_post_meta($edit_id,'package_select',true)){
							$packg_id = get_post_meta($edit_id,'package_select',true);
						}
						else{
							$packg_id = get_user_meta($current_user->ID,$post_type.'_package_select',true);
						}
						$monetization->tmpl_fetch_price_package_featured_option($current_user->ID,$post_type,$post->ID,$packg_id,$is_user_select_subscription_pkg);
					}
					else
					{
					?>
						<div style="display:none;" id="show_featured_option">
							<input type="checkbox" value="" id="featured_h" name="featured_h">
							<input type="checkbox" value="" id="featured_c" name="featured_c">
						</div>
					<?php
					}
				}		
				
				templ_captcha_integrate('submit'); /* Display recaptcha in submit form */	
				
				global $submit_button;
                $submit_button=(!isset($submit_button))?'':$submit_button; 
				if((!isset($_REQUEST['pid']) && $_REQUEST['pid'] == '') || ( isset($_REQUEST['renew']) && $_REQUEST['renew'] == 1))
				{
					tevolution_show_term_and_condition(); /* show terms and conditions check box*/
				}
				
				echo '<span class="message_error2" id="common_error"></span>';
				echo '<input type="button" id="continue_submit_from" name="continue_submit_from" value="'.__('Continue',DOMAIN).'" '.$submit_button.'/>&nbsp;&nbsp;';
				echo '<input type="button" class="secondray-button" data-reveal-id="preview_submit_from_'.$post_type.'"  id="preview_submit_from" name="preview" value="'.__('Preview',DOMAIN).'" />';
			?>
           		</div>
            </div>
            <?php
				
				/* Finish submit custom fields detail structure*/
				do_action('before_login_register_form',$post_type,$post->ID);
				if($current_user->ID=='') {  
				?>
                 <div id="step-auth" class="accordion-navigation step-wrapper step-auth">
		            <a class="step-heading active" href="#"><span id="span_user_login">3</span><span><?php _e('Login / Register',DOMAIN); ?></span><span><i class="fa fa-caret-down"></i><i class="fa fa-caret-right"></i></span></a>
        		    <div id="auth" class="step-auth content clearfix">
                    <?php
						/*display the login and register form while user submit a form without logged in.*/
						$_SESSION['redirect_to']=get_permalink();
						do_action('templ_fecth_login_onsubmit');
						$users_can_register = get_option('users_can_register');
						if($users_can_register):
							do_action('templ_fetch_registration_onsubmit');
						endif;
					?>
                    </div>
                 </div>
                 <?php
				}
				do_action('before_payment_option_form',$post_type,$post->ID);

				/* Delete option of pay cash on delivery because we removed it. */
					delete_option('payment_method_payondelivery');
				/* Delete option of pay cash on delivery because we removed it. */
				/*while edit a listing show default post tab active*/
				if((isset($edit_id) && $edit_id !='' && (!isset($_REQUEST['renew']))) || $is_user_select_subscription_pkg == 1)
				{
					$val=($current_user->ID != '')? '2':'3';
				}
				else
				{
					$val=($current_user->ID != '')? '3': '4';
				}
				?>
				<div id="step-payment" class="accordion-navigation step-wrapper step-payment" style="display:none;">
					<a class="step-heading active" href="#"><span id="select_payment"><?php echo $val; ?></span><span><?php _e('Payment',DOMAIN); ?></span><span><i class="fa fa-caret-down"></i><i class="fa fa-caret-right"></i></span></a>
					<div id="payment" class="step-payment content clearfix">
					<?php
						/* show only if coupon available */
						$coupons = get_posts(array('post_type'=>'coupon_code','post_status'=>'publish'));
						if($coupons){
							?>
                            <input type="hidden" name="is_coupon" id="is_coupon" value="1" />
                            <?php
							$coupon_code = '';
							/* fetch coupon code */
							if(function_exists('templ_get_coupon_fields')){
								templ_get_coupon_fields(''); 
							}
						}
						
						/*To display the payment gateways on submit form page*/
						templatic_payment_option_preview_page(); 
					?>
					<input type="button" id="submit_form_button" name="submit_form_button" value="<?php  _e('Pay Now',DOMAIN);?>" class="progress-button" />
					</div>
				</div>
				<?php

				do_action('after_payment_option_form',$post_type,$post->ID);
			
		?>
	</div>
    <?php


	echo '</form>';	
	
	echo '<div id="preview_submit_from_'.$post_type.'"  class="reveal-modal singular-'.$post_type.' preview_submit_from_data" data-reveal></div>';
	/* Include submit validation script file */
	include_once(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/submition_validation.php');
	
	/* submit_form_after_content hook for add additional html or information on this hook */	 
	do_action('submit_form_after_content');
	
	return ob_get_clean();
}
/*
 * Add filter hook : tevolution_submit_from_validation
 * This function will be create validation info global array for submit page validation
 */
add_filter('tevolution_submit_from_validation','tmpl_tevolution_submit_from_validation',10,2);
function tmpl_tevolution_submit_from_validation($validation_info,$custom_fields){
	global $validation_info;
	
	$tmpdata = get_option('templatic_settings');
	
	foreach($custom_fields as $custom_field){	
		foreach($custom_field as $key=>$value){
			if($value['is_require']=='1'){
			$value['type']	=($key=='category') ? $tmpdata['templatic-category_type'] : $value['ctype'];		
				
			$validation_info[] = array(
							'title'	       => $value['name'],
							'name'	       => $key,
							'espan'	       => $key.'_error',
							'type'	       => $value['type'],
							'text'	       => $value['field_require_desc'],
							'is_require'	  => @$value['is_require'],
							'validation_type'=> $value['validation_type'],
							'search_ctype'   => $value['search_ctype']
					);
			}
		}/*end second for each loop*/
		
	}/* End first for each loop*/
	
	
	return $validation_info;
}

/*
Name: tevolution_tiny_mce_before_init
tinymce validation.
*/

add_filter( 'tiny_mce_before_init', 'tevolution_submit_form_tiny_mce_before_init',100,2 );
function tevolution_submit_form_tiny_mce_before_init( $initArray ,$editor_id)
{

	if(!is_admin() || isset($_REQUEST['front']) && $_REQUEST['front']==1){	
	global $validation_info,$post;
	for($i=0;$i<count($validation_info);$i++) {
			$title = $validation_info[$i]['title'];
			$name = $validation_info[$i]['name'];
			$espan = $validation_info[$i]['espan'];
			$type = $validation_info[$i]['type'];
			$text = __($validation_info[$i]['text'],DOMAIN);
			$validation_type = $validation_info[$i]['validation_type'];
			$is_required = $validation_info[$i]['is_require'];
			
			/*finish post type wise replace post category, post title, post content, post expert, post images*/
			
			if($type=='texteditor'){								
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
            }else{
				jQuery('#'+espan).text("$text");
				jQuery('#'+espan).addClass("message_error2");
				return false;
			}
         }
    });

}][0]
JS;
				
			}
		}	
	}
	 return $initArray;
}


?>