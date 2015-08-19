<?php
/*
Name:tevolution_post_upgrade_template
desc: return the upgrade package
*/
function tevolution_post_upgrade_template(){
	ob_start();
	remove_filter( 'the_content', 'wpautop' , 12);
	/* if You have successfully activated monetization then this function will be included for listing prices */
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			if(isset($_REQUEST['lang'])){
				$url = site_url().'/?page=upgradenow&lang='.$_REQUEST['lang'];
			}elseif($sitepress->get_current_language()){
				
				if($sitepress->get_default_language() != $sitepress->get_current_language()){
					$url = site_url().'/'.$sitepress->get_current_language().'/?page=upgradenow';
				}else{
					$url = site_url().'/?page=upgradenow';
				}
			}else{
				$url = site_url().'/?page=upgradenow';
			}
	}else{
			$url = site_url().'/?page=upgradenow';
	}
                   
    echo '<form name="submit_form" id="submit_form" class="form_front_style" action="'.$url.'" method="post" enctype="multipart/form-data">';
	echo "<input type='hidden' id='total_price' name='total_price' >";
	echo "<input type='hidden' id='post_upgrade' name='post_upgrade' value='post_upgrade' >";
	echo '<div class="accordion" id="post-listing" >';
	$payment_url = fetch_single_payment_url($post_type,$post->ID);
	apply_filters('fetch_payment_url',$payment_url);

			global $post,$monetization;
			$upgrade_id = @$_REQUEST['pid'];
			$edit_id = @$_REQUEST['pid'];
			$all_cat_id='';
			$upgrade_url = get_permalink($post->ID)."?pid=".@$_REQUEST['pid']."&_wpnonce=".@$_REQUEST['_wpnonce'];
			$post_details = get_post($upgrade_id);
			$post_type = $post_details->post_type;
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => @$post_type,'public'   => true, '_builtin' => true ));
			$taxonomy = @$taxonomies[0];
		
		if(get_post_meta($post->ID,'submit_post_type',true)=="" || $post_type!=get_post_meta($post->ID,'submit_post_type',true)){
			update_post_meta($post->ID,'submit_post_type',$post_type);	
		}
		if(get_post_meta($post->ID,'is_tevolution_upgrade_form',true)=="" || '1'!=get_post_meta($post->ID,'is_tevolution_upgrade_form',true)){
			update_post_meta($post->ID,'is_tevolution_upgrade_form',1);	
		}
		if(isset($_SESSION['category']) && class_exists('monetization'))
		{	
			global $cat_array;
			$cat_array = $monetization->templ_get_selected_category_id($_SESSION['category']);
			$cat_array_price = $monetization->templ_fetch_category_price($_SESSION['category']);				
		}

		if(class_exists('monetization')){			
			global $current_user;
			$user_have_pkg = $monetization->templ_get_packagetype($current_user->ID,$post_type); /* User selected package type*/
			$user_last_postid = $monetization->templ_get_packagetype_last_postid($current_user->ID,$post_type); /* User last post id*/
			$user_have_days = $monetization->templ_days_for_packagetype($current_user->ID,$post_type); /* return alive days(numbers) of last selected package  */
			$is_user_have_alivedays = $monetization->is_user_have_alivedays($current_user->ID,$post_type); /* return user have an alive days or not true/false */
			if($current_user->ID)/* check user wise post per  Subscription limit number post post */
			{
				$package_id=get_user_meta($current_user->ID,'package_selected',true);/* get the user selected price package id*/
				if(!$package_id)
					$package_id=get_user_meta($current_user->ID,$post_type.'_package_select',true);/* get the user selected price package id*/
				$user_limit_post=get_user_meta($current_user->ID,'total_list_of_post',true); /*get the user wise limit post count on price package select*/
				if(!$user_limit_post)	
					$user_limit_post=get_user_meta($current_user->ID,$post_type.'_list_of_post',true); /*get the user wise limit post count on price package select*/
				$package_limit_post=get_post_meta($package_id,'limit_no_post',true);/* get the price package limit number of post*/
				$user_have_pkg = get_post_meta($package_id,'package_type',true); 
				$post_types = explode(',',get_post_meta($package_id,'package_post_type',true)); 
				if(in_array($post_type,$post_types)): $is_posttype_inpkg=1; else: $is_posttype_inpkg=0; endif; /* check is this taxonomy included in package or not*/
			}
			

				if(isset($upgrade_id) && $upgrade_id !=''){
				$pkg_id = get_post_meta($edit_id,'package_select',true); /* user comes to edit fetch selected package */
				}else{ $pkg_id =''; }
				
				$monetization->tmpl_fetch_price_package($current_user->ID,$post_type,$post->ID); /* call this function to fetch price packages which have to show even no categories selected */
				if(!isset($all_cat_id)){ $all_cat_id ==0;}elseif(isset($_REQUEST['backandedit'])){ if(!empty($cat_array)){ $all_cat_id = implode(',',$cat_array); } }else if(isset($edit_id) && $edit_id !=''){ $all_cat_id = @$all_cat_id; }
				echo '<span class="message_error2" id="all_packages_error"></span>';
				$post_heading_number = 2;
				$active = '';
				}

		?>
        <div id="step-post" class="accordion-navigation step-wrapper step-post">
				<a class="step-heading active" href="#"><span><?php echo $post_heading_number; ?></span><span><?php _e('Enter Details','templatic'); ?></span><span><i class="fa fa-caret-down"></i><i class="fa fa-caret-right"></i></span></a>
					<div id="post" class="step-post content <?php echo $active; ?> clearfix">
                    <?php
				
		$default_custom_metaboxes = array('post_categories'=>array('type'=>'post_categories','name'=>'category','label'=>'','htmlvar_name'=>'category','htmlvar_name'=>'category','is_require'=>1));
		
			echo '<div id="submit_category_box">';
				display_custom_category_field_plugin($default_custom_metaboxes,'custom_fields','post',$post_type);/*displaty  post category html.*/
			echo '</div>';
			/* $monetization->fetch_monetization_packages_front_end($pkg_id,'packages_checkbox',$post_type,@$taxonomy,@$all_cat_id);  call this function to fetch price packages */
				if(!isset($_REQUEST['action']) && isset($_REQUEST['action']) !='edit'){
				/*	$monetization->fetch_package_feature_details($edit_id,$pkg_id,@$all_cat_id); /* call this function to display fetured packages */
						$is_user_select_subscription_pkg = 0;
						?>
	                        <div style="display:none;" id="show_featured_option">
								<input type="checkbox" value="" id="featured_h" name="featured_h">
								<input type="checkbox" value="" id="featured_c" name="featured_c">
							</div>
                        <?php
						if($user_have_pkg == 2 && $user_have_days > 0){
							echo "<div class='form_row clearfix act_success'>".sprintf(SUBMIT_LISTING_DAYS_TEXT,$user_have_days)."</div>";
						}	
				}
			$coupons = get_posts(array('post_type'=>'coupon_code','post_status'=>'publish')); /* show only if coupon available*/
			if($coupons)
			{
				if(!isset($_REQUEST['action']) && isset($_REQUEST['action']) !='edit'){
					$coupon_code = '';
					if(@$_REQUEST['backandedit']) { $coupon_code = $_SESSION['upgrade_post']['add_coupon']; }else if(isset($edit_id) && $edit_id !=''){ 
					if(!isset($_REQUEST['upgpkg']) && $_REQUEST['upgpkg'] != 1){
						$coupon_code = get_post_meta($edit_id,'add_coupon',true);
					}
					}else{ $coupon_code = ''; } /* coupon code when click ok GBE*/
					if(function_exists('templ_get_coupon_fields')){
						templ_get_coupon_fields($coupon_code); /* fetch coupon code */
					}
				}
			}
			
		
			tevolution_show_term_and_condition(); /* show terms and conditions check box*/
			echo '<span class="message_error2" id="common_error"></span>';
			echo '<input type="button" id="continue_submit_from" name="continue_submit_from" value="'.__('Continue','templatic').'" '.$submit_button.'/>&nbsp;&nbsp;';
			echo '</div>';
		echo '</div>';

			/* Finish submit custom fields detail structure*/
				do_action('before_login_register_form',$post_type,$post->ID);
				if($current_user->ID=='') {  
				?>
                 <div id="step-auth" class="accordion-navigation step-wrapper step-auth">
		            <a class="step-heading active" href="#"><span id="span_user_login">3</span><span><?php _e('Login / Register','templatic'); ?></span><span><i class="fa fa-caret-down"></i><i class="fa fa-caret-right"></i></span></a>
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
				$val=($current_user->ID != '')? '3': '4';
				
				?>
				<div id="step-payment" class="accordion-navigation step-wrapper step-payment">
					<a class="step-heading active" href="#"><span id="select_payment"><?php echo $val; ?></span><span><?php _e('Payment','templatic'); ?></span><span><i class="fa fa-caret-down"></i><i class="fa fa-caret-right"></i></span></a>
					<div id="payment" class="step-payment content clearfix">
					<?php
						/*To display the payment gateways on submit form page*/
						templatic_payment_option_preview_page(); 
					?>
					<input type="button" id="submit_form_button" name="submit_form_button" value="<?php  _e('Submit','templatic');?>" class="progress-button" />
					</div>
				</div>
				<?php
				
				do_action('after_payment_option_form',$post_type,$post->ID);
			
		
			 global $submit_button;
		 if(!isset($submit_button)){ $submit_button = ''; }
		/*for getting the alive days	*/
		if($current_user->ID){if(function_exists('templ_days_for_user_packagetype'))$alive_days= $monetization->templ_days_for_user_packagetype($current_user->ID, $post_type);} ?>
		<script type="text/javascript" async>var recaptcha='<?php echo $recaptcha?>';</script>
		 <span class="message_error2" id="common_error"></span>
         <input type="hidden" name="cur_post_type" id="cur_post_type" value="<?php echo $post_type; ?>"  />
          <input type="hidden" name="submit_post_type" id="submit_post_type" value="<?php echo $post_type; ?>"  />
         <input type="hidden" name="cur_post_taxonomy" id="cur_post_taxonomy" value="<?php echo $taxonomy; ?>"  />
         <input type="hidden" name="upgrade_url" id="upgrade_url" value="<?php echo $upgrade_url; ?>"  />
         <input type="hidden" name="cur_post_id" value="<?php echo $post->ID; ?>"  />
         <input type="hidden" name="upgpkg" value="1"  />
         <?php if(isset($upgrade_id) && $upgrade_id !=''): ?>
                <input type="hidden" name="pid" id="pid" value="<?php echo $upgrade_id; ?>"  />
         <?php endif; ?>
		 <input type="hidden" value="<?php echo @$alive_days;?>" id="alive_days" name="alive_days" >
         <?php
         /*added the hidden filed of maximum category can select  for particular price package*/
		 if(isset($_SESSION['package_select']) && $_SESSION['package_select'] != '') {?>
		 <input type="hidden" name="category_can_select" id="category_can_select" value="<?php echo get_post_meta($_SESSION['package_select'],'category_can_select',true);?>" />
		 <?php } else {?>
		 <input type="hidden" name="category_can_select" id="category_can_select" value="" />
		 <?php } 
	 /* monetization end */
	global $post,$wpdb,$validation_info;
	$tmpdata = get_option('templatic_settings');
	$form_fields = array();
	if(!isset($_REQUEST['category']) && count(@$_REQUEST['category']) <= 0 && !isset($_REQUEST['fields']) && @$_REQUEST['fields'] =='' && @$_REQUEST['action'] != 'edit')
	{
		$form_fields['category'] = array(
							   'name' 	      => $taxonomy,
							   'espan'	      => 'category_span',
							   'type'	           => $tmpdata['templatic-category_type'],
							   'text'	           => __('Please select Category','templatic'),
							   'validation_type' => 'require'
							   );
	}
	$validation_info = array();
	foreach($form_fields as $key=>$val)
	{
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
							'validation_type'=> $val['validation_type']
					);
	}
		
	include_once(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/submition_validation.php');

			
    echo '</div>';
	echo "</form>";
	return ob_get_clean();
}
/*include_once(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/submition_validation.php');*/
?>
