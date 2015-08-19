<?php
/*
 * save package data and other package related functions
 */
if (!class_exists('monetization')) {
class monetization
{
	/* NAME : INSERT PACKAGE DATA
	DESCRIPTION : THIS FUNCTION INSERTS PACKAGE DATA INTO POSTMETA TABLE CREATING A POST WITH POST TYPE PACKAGE */
	function insert_package_data($post_details)
	{
		global $last_postid,$wpdb;
		$package_name = $post_details['package_name'];
		$package_desc = $post_details['package_desc'];
		$package_type = $post_details['package_type'];
		$package_post_type = $post_details['package_post_type'];
		$package_post_type = implode(',',$post_details['package_post_type']);		
		
		$custom_taxonomy = get_option('templatic_custom_taxonomy',true);
		$custm_category_type = array_keys($custom_taxonomy);
		$post_category = array('category');
		$package_taxonomy_type = array_merge($custm_category_type,$post_category);
		
	
		$package_categories = implode(',',$post_details['category']);
		$package_post = array(
			'post_title' 	=> $package_name,
			'post_content'  => $package_desc,
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type'     => 'monetization_package' );			
		/* CREATING A POST OBJECT AND INSERT THE POST INTO THE DATABAE */
		if($_REQUEST['package_id'])
		{
			$package_id = $_REQUEST['package_id'];
			$package_post['ID'] = $_REQUEST['package_id'];
			$menu_order = get_post($package_post['ID']);
			$package_post['menu_order'] = $menu_order->menu_order;
			$last_postid = wp_insert_post( $package_post );
			
			if (function_exists('icl_register_string')) {									
				icl_register_string('tevolution-price', 'package-name'.$last_postid,$package_name);
				icl_register_string('tevolution-price', 'package-desc'.$last_postid,$package_desc);			
			}
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'monetization_package'); /* insert post in language */
			}		
			
				foreach($package_taxonomy_type as $key=> $_tax)
				{
					wp_delete_object_term_relationships( $last_postid, $_tax ); 
					foreach($_POST['category'] as $category)
					 {	
						global $wpdb;					
						$taxonomy = get_term_by('id',$category,$_tax);
						if($taxonomy ){
							wp_set_post_terms($last_postid,$category,$_tax,true); 
						}
					 }
				}

		
			$msg_type = 'edit';
			
		}
		else
		{
			$last_postid = wp_insert_post( $package_post );
			if (function_exists('icl_register_string')) {									
				icl_register_string('tevolution-price', 'package-name'.$last_postid,$package_name);
				icl_register_string('tevolution-price', 'package-desc'.$last_postid,$package_desc);			
			}
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($last_postid,'monetization_package'); /* insert post in language */
			}
			if($package_post_type == 'all')
			{
				foreach($package_taxonomy_type as $key=> $_tax)
				{
					foreach($_POST['category'] as $category)
					{
						$package_taxonomy_type=$wpdb->get_var("select taxonomy from $wpdb->term_taxonomy where term_id=".$category);
						wp_set_post_terms($last_postid,$category,$_tax,true);
					}
				}
			}
			else
			{
				foreach($_POST['category'] as $category)
				{
					$package_taxonomy_type=$wpdb->get_var("select taxonomy from $wpdb->term_taxonomy where term_id=".$category);
					wp_set_post_terms($last_postid,$category,$package_taxonomy_type,true);
				}
			}
			$msg_type = 'add';
		}
		/* INSERT THE PACKAGE DATA INTO THE POSTMETA TABLE */
		$show_package = $post_details['show_package'];
		$package_amount = $post_details['package_amount'];
		$package_validity = $post_details['validity'];
		$package_validity_per = $post_details['validity_per'];
		$package_status = $post_details['package_status'];
		$package_is_recurring = ($post_details['recurring'] ==1) ? 1 : 0;
		$package_billing_num = $post_details['billing_num'];
		$package_billing_per = $post_details['billing_per'];
		$package_billing_cycle = $post_details['billing_cycle'];
		
		$subscription_as_pay_post = $post_details['subscription_as_pay_post'];		
		$is_home_page_featured = $post_details['is_home_page_featured'];
		$is_category_page_featured = $post_details['is_category_page_featured'];
		$package_is_home_featured = $post_details['is_home_featured'];
		$package_is_category_featured = $post_details['is_category_featured'];
		$package_feature_amount = $post_details['feature_amount'];
		$package_feature_cat_amount = $post_details['feature_cat_amount'];
		$package_home_page_feature_alive_days = $post_details['home_page_alive_days'];
		$package_cat_page_feature_alive_days = $post_details['cat_page_alive_days'];
		$subscription_days_free_trail = $post_details['subscription_days_free_trail'];
		$days_for_no_post = $post_details['days_for_no_post'];
		
		$limit_no_post = $post_details['limit_no_post'];
		$first_free_trail_period = $post_details['first_free_trail_period'];
		$custom = array('package_type' => $package_type,
						'package_post_type' => $package_post_type,
						'subscription_as_pay_post'=>$subscription_as_pay_post,
						'category' => $package_categories,
						'show_package' => $show_package,
						'package_amount' => $package_amount,
						'validity' => $package_validity,
						'validity_per' => $package_validity_per,
						'package_status' => $package_status,
						'recurring' => $package_is_recurring,
						'billing_num' => $package_billing_num,
						'billing_per' => $package_billing_per,
						'billing_cycle' => $package_billing_cycle,
						'first_free_trail_period' => $first_free_trail_period,
						'is_home_page_featured' => $is_home_page_featured,
						'is_category_page_featured' => $is_category_page_featured,
						'is_home_featured' => $package_is_home_featured,
						'is_category_featured' => $package_is_category_featured,
						'feature_amount' => $package_feature_amount,
						'feature_cat_amount' => $package_feature_cat_amount,
						'limit_no_post'=>$limit_no_post,
						'home_page_alive_days'=>$package_home_page_feature_alive_days,
						'cat_page_alive_days'=> $package_cat_page_feature_alive_days,
						'subscription_days_free_trail'=>$subscription_days_free_trail,
						'days_for_no_post'=>$days_for_no_post
						);
						
		$custom=apply_filters('insert_package_data',$custom);
		foreach($custom as $key=>$val)
		{				
			update_post_meta($last_postid, $key, $val);
		}
		
		if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
			update_post_meta($last_postid, 'can_author_mederate', $post_details['can_author_mederate']);
			update_post_meta($last_postid, 'comment_mederation_amount', $post_details['comment_mederation_amount']);
		}		
		
		do_action('save_price_package');
		
		$url = site_url().'/wp-admin/admin.php?page=monetization';
		echo '<form action="'.$url.'" method="get" id="frm_edit_package" name="frm_edit_package">
					<input type="hidden" value="monetization" name="page"><input type="hidden" value="success" name="package_msg"><input type="hidden" value="'.$msg_type.'" name="package_msg_type">
					<input type="hidden" value="packages" name="tab">
			  </form>
			  <script>document.frm_edit_package.submit();</script>';
			  exit;
	}
	
	/*
		 To display the feature details of the price packages  in backend
	*/
	function fetch_monetization_packages_back_end($pkg_id,$div_id,$post_type,$taxonomy_slug,$post_cat)
	{
		global $post,$wpdb,$current_user;
		$edit_id = $post->ID;
		echo "<input type='hidden' id='submit_post_type' name='submit_post_type' value='".$_REQUEST['post_type']."'>";
		echo "<input type='hidden' id='cur_post_type' name='cur_post_type' value='".$_REQUEST['post_type']."'>";
		echo "<input type='hidden' id='submit_page_id' name='submit_page_id' value='".$post->ID."'>";
		echo "<input type='hidden' id='total_price' name='total_price' >";
		global $wpdb, $wp_query,$post,$packages_post;
		$packages_post=$post;
		if(!is_plugin_active( 'Tevolution-FieldsMonetization/fields_monetization.php')) {
				if($div_id=='ajax_packages_checkbox'){
					$post_cat='1,'.$post_cat;
					$pargs = array('post_type' => 'monetization_package',
							'posts_per_page' => -1,
							'post_status' => array('publish'),
							'tax_query' => array('relation' => 'OR', array('taxonomy' => $taxonomy_slug,'field' => 'id','terms' => explode(',',$post_cat),'operator'  => 'IN'),array('taxonomy' => 'category','field' => 'id','terms' => 1,'operator'  => 'IN') ),
							'meta_query' => array('relation' => 'AND',
											  array('key' => 'package_post_type',
												   'value' => $post_type,'all',
												   'compare' => 'LIKE'
												   ,'type'=> 'text'),
											  array('key' => 'package_status',
												   'value' =>  '1',
												   'compare' => '=')
									),
							'orderby' => 'menu_order',
							'order' => 'ASC'
						);
				}elseif($post_cat!=''){						
						$pargs = array('post_type' => 'monetization_package',
							'posts_per_page' => -1,
							'post_status' => array('publish'),
							'tax_query' => array('relation' => 'OR', array('taxonomy' => $taxonomy_slug,'field' => 'id','terms' => explode(',',$post_cat),'operator'  => 'IN'),array('taxonomy' => 'category','field' => 'id','terms' => 1,'operator'  => 'IN') ),
							'meta_query' => array('relation' => 'AND',
											  array('key' => 'package_post_type',
												   'value' => $post_type,'all',
												   'compare' => 'LIKE'
												   ,'type'=> 'text'),
											  array('key' => 'package_status',
												   'value' =>  '1',
												   'compare' => '=')
									),
							'orderby' => 'menu_order',
							'order' => 'ASC'
						);
				}else{
					$pargs = array('post_type' => 'monetization_package',
							'posts_per_page' => -1,
							'post_status' => array('publish'),
							
							'meta_query' => array('relation' => 'AND',
											  array('key' => 'package_post_type',
												   'value' => $post_type,'all',
												   'compare' => 'LIKE'
												   ,'type'=> 'text'),
											  array('key' => 'package_status',
												   'value' =>  '1',
												   'compare' => '=')
									),
							'orderby' => 'menu_order',
							'order' => 'ASC'
						);
				}
			}
			else
			{
				$pargs = array('post_type' => 'monetization_package',
					'posts_per_page' => -1,
					'post_status' => array('publish'),
 				     'meta_query' => array('relation' => 'AND',
									  array('key' => 'package_post_type',
										   'value' => $post_type,'all',
										   'compare' => 'LIKE'
										   ,'type'=> 'text'),
									  array('key' => 'package_status',
										   'value' =>  '1',
										   'compare' => '=')
							),
				     'orderby' => 'menu_order',
			          'order' => 'ASC'
				);
			}
			wp_reset_query();
			$package_query = null;			
			$package_query = new WP_Query($pargs);		
			if($pkg_id !=''){
				$selected_pkg = $pkg_id;
			}
			?>
             <input type="hidden" name="package_free_submission" id="package_free_submission">
             <input type="hidden" name="package_select" id="pkg_id" value="<?php echo get_post_meta($post->ID,'package_select',true); ?>">
			<input type="hidden" name="pkg_type" id="pkg_type">
            <div id="plan" class="content step-plan active clearfix">
            <?php
			while($package_query->have_posts())
			{ 
				$package_query->the_post();				
				$package_type = get_post_meta(get_the_ID(),'package_type',true);
				$package_post_type = get_post_meta(get_the_ID(),'package_post_type',true);
				$package_categories = get_post_meta(get_the_ID(),'category',true);
				$show_package = get_post_meta(get_the_ID(),'show_package',true);
				$package_amount = get_post_meta(get_the_ID(),'package_amount',true);
				$recurring = get_post_meta(@$post_id,'recurring',true);
				if($package_type == 2 && $recurring == 1){
					$package_validity = get_post_meta(get_the_ID(),'billing_num',true);
					$package_validity_per =get_post_meta(get_the_ID(),'billing_per',true);
				}else{
					$package_validity = get_post_meta(get_the_ID(),'validity',true);
					$package_validity_per = get_post_meta(get_the_ID(),'validity_per',true);
				}
				$package_status = get_post_meta(get_the_ID(),'package_status',true);
				$recurring = get_post_meta(get_the_ID(),'recurring',true);
				$billing_num = get_post_meta(get_the_ID(),'billing_num',true);
				$billing_per = get_post_meta(get_the_ID(),'billing_per',true);
				$billing_cycle = get_post_meta(get_the_ID(),'billing_cycle',true);
				$is_featured = get_post_meta(get_the_ID(),'is_featured',true);
				$feature_amount_home = get_post_meta(get_the_ID(),'feature_amount',true);
				$feature_cat_amount = get_post_meta(get_the_ID(),'feature_cat_amount',true);  
				$featured_h = get_post_meta(get_the_ID(),'home_featured_type',true); 
				$featured_c = get_post_meta(get_the_ID(),'featured_type',true);
				$package_is_recurring = get_post_meta(get_the_ID(),'recurring',true);
				$package_billing_num = get_post_meta(get_the_ID(),'billing_num',true);
				$package_billing_per =get_post_meta(get_the_ID(),'billing_per',true);
				$package_billing_cycle =get_post_meta(get_the_ID(),'billing_cycle',true);
				
					if(isset($category_id)){ $catid = $category_id; }else{ $catid =''; }
					if(isset($cat_array) && $cat_array != "")
					{
						$catid = $cat_array;
					}
					else
					{
						if(isset($_REQUEST['category'])){
						$catid = $_REQUEST['category'];
						}else{ $catid =''; }
					}
					tmpl_display_package_html($post,$post_type);
					?>
					
				<!-- DISPLAY THE PACKAGE IN FRONT END -->	
					
		<?php 
			}
			?>
            </div>
            <?php
			global $monetization;
				if(class_exists('monetization')){
					if(isset($edit_id) && $edit_id !='' )
					{
						if(get_post_meta($edit_id,'package_select',true)){
							$packg_id = get_post_meta($edit_id,'package_select',true);
						}
						else{
							$packg_id = get_user_meta($current_user->ID,$post_type.'_package_select',true);
						}
						echo '<div id="show_featured_option">';
							$monetization->tmpl_fetch_price_package_featured_option($current_user->ID,$post_type,$edit_id,$packg_id,$is_user_select_subscription_pkg);
						echo '</div>';
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
		wp_reset_query();
		wp_reset_postdata();
		$post=$packages_post;			
	}
	/*
	 To display the feature details of the price packages 
	*/
	function fetch_package_feature_details_backend($edit_id='',$png_id='',$all_cat_id){	
		/* set feature price when Go back and edit */
		if(isset($edit_id) && $edit_id !=''){
			$price_select =  get_post_meta($edit_id,'package_select',true); /* selected package */
			$is_featured = get_post_meta($price_select,'is_featured',true); /* package is featured or not */
			if($is_featured ==1){
				$featured_h = get_post_meta($price_select,'feature_amount',true); 
				$featured_c = get_post_meta($price_select,'feature_cat_amount',true); 
				$is_featured_h = get_post_meta($edit_id,'featured_h',true); 
				$is_featured_c = get_post_meta($edit_id,'featured_c',true); 
				$featured_type = get_post_meta($edit_id,'featured_type',true); 
			}		
		}else{
			$featured_h =0;
			$featured_c =0;
		}	
		
		?>
			<!-- FETCH FEATURED POST PRICES IN BACK END -->
            <?php global $post; 
		  	$post_type = (get_post_meta($post->ID,'template_post_type',true)!="")? get_post_meta($post->ID,'template_post_type',true):get_post_meta($post->ID,'submit_post_type',true); ?>
			<div class="form_row clearfix is_backend_featured" id="show_featured_option">
				<label><strong><?php _e('Would you like to make this ','templatic-admin').$post_type; _e('featured?','templatic-admin'); ?></strong></label>
				<div class="feature_label">
					<label><input type="checkbox" name="featured_h" id="featured_h" value="<?php echo $featured_h; ?>" onclick="featured_list(this.id)" <?php if(@$is_featured_h !="" && $is_featured_h =="h"){ echo "checked=checked"; } ?>/><?php _e(FEATURED_H,'templatic-admin'); ?> <span id="ftrhome"><?php if(isset($featured_h) && $featured_h !=""){ echo "(".fetch_currency_with_position($featured_h).")"; }else{ echo "(".fetch_currency_with_position('0').")"; } ?></span></label>
					<label><input type="checkbox" name="featured_c" id="featured_c" value="0" onclick="featured_list(this.id)" <?php if(@$is_featured_c !="" && $is_featured_c =="c"){ echo "checked=checked"; } ?>/><?php _e(FEATURED_C,'templatic-admin'); ?><span id="ftrcat"><?php if(isset($featured_c) && $featured_c !=""){ echo "(".fetch_currency_with_position($featured_c).")"; }else{ echo "(".fetch_currency_with_position('0').")"; } ?></span></label>
					<?php
						if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
							$author_moderate = get_post_meta($edit_id,'author_moderate',true);
							$comment_mederation_amount = get_post_meta($price_select,'comment_mederation_amount',true);
						?>
							<label><input type="checkbox" name="author_can_moderate_comment" id="author_can_moderate_comment" value="0" onclick="featured_list(this.id)" <?php if(@$author_moderate !="" && $author_moderate =="1"){ echo "checked=checked"; } ?>/><?php _e(MODERATE_COMMENT,'templatic'); ?><span id="ftrcomnt"><?php if(isset($author_moderate) && $author_moderate =="1"){ echo "(".fetch_currency_with_position($comment_mederation_amount).")"; }else{ echo "(".fetch_currency_with_position('0').")"; } ?></span></label>
							<input type="hidden" name="author_moderate" id="author_moderate" value="0"/>
						<?php	
						}
					?>
					<input type="hidden" name="featured_type" id="featured_type" value="<?php echo ($featured_type)? $featured_type : 'none'?>"/>
					<span id='process' style='display:none;'><i class="fa fa-circle-o-notch fa-spin"></i></span>
					
				</div>
				<?php				
					$msg_note = sprintf(__("An additional amount will be charged to make this %s featured. You have the option to feature your %s on home page or category page or both.",'templatic-admin'),$post_type,$post_type);
					if(function_exists('icl_register_string')){
						icl_register_string('templatic-admin',$msg_note,$msg_note);
					}
					
					if(function_exists('icl_t')){
						$msg_note1 = icl_t('templatic-admin',$msg_note,$msg_note);
					}else{
						$msg_note1 = __($msg_note,'templatic-admin'); 
					}
				?>
				<span class="message_note"><?php _e($msg_note1,'templatic-admin');?></span>
				<span id="category_span" class="message_error2"></span>
			</div>
			<!-- END - FETCH FEATURED POST PRICE -->
                    <span id="cat_price" style="display:none;"></span>
                    <span id="pkg_price" style="display:none;"></span>
                    <span id="feture_price" style="display:none;"></span>
                    <span id="result_price" style="display:none;">                              
			
			</div>
	<?php
	}
	/*  THIS FUNCTION WILL FETCH ALL THE PACKAGES IN BACK END */
	function fetch_monetization_packages_front_end($pkg_id,$div_id,$post_type,$taxonomy_slug,$post_cat)
	{
		global $wpdb,$post;
		$post_fcategories = explode(',',$post_cat);

		/* FETCH ALL THE POSTS WITH POST TYPE PACKAGE */
		if($div_id != 'ajax_packages_checkbox'){ $class ='form_row_pkg clearfix'; }
		$package_ids = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'monetization_package' AND post_status = 'publish'");
			if($div_id !='all_packages'){ /* this query will execute only for category wise packages */
				$pargs = array('post_type' => 'monetization_package','posts_per_page' => -1	,'post_status' => array('publish'),
					  'meta_query' => array('relation' => 'AND',array('key' => 'package_post_type','value' => $post_type,'compare' => 'LIKE','type'=> 'text'),array('key' => 'show_package','value' =>  array(''),'compare' => 'IN','type'=> 'text'),array('key' => 'package_status','value' =>  '1','compare' => '=')),
					  'tax_query' => array( array('taxonomy' => $taxonomy_slug,'field' => 'id','terms' => $post_fcategories,'include_children'=>false,'operator'  => 'IN') ),
				'orderby' => 'menu_order',
				'order' => 'ASC'
				);
			}else{ /* this query will execute for all package need to show even no category selected */
				$pargs = array('post_type' => 'monetization_package','posts_per_page' => -1	,'post_status' => array('publish'),
					  'meta_query' => array('relation' => 'AND',array('key' => 'package_post_type','value' =>$post_type,'compare' => 'LIKE','type'=> 'text'),array('key' => 'package_status','value' =>  '1','compare' => '=')),
				'orderby' => 'menu_order',
				'order' => 'ASC'
				);
			}
			
			wp_reset_query();
			$package_query = null;
			
			/* do action for add any query or filter before wp_query */
			do_action('price_package_before_query');
			
			$package_query = new WP_Query($pargs);

			/* do action for add any query or filter after wp_query */
			do_action('price_package_after_query');
			
			if($div_id =='all_packages'){
			/* display this fields only when no deiv ID argument pass from funnction, so the intention is to display this fields only once */
			if(isset($_REQUEST['backandedit']) && $_REQUEST['backandedit'] !=''){
				$cat_price = $_SESSION['custom_fields']['all_cat_price'];
				}else{ $cat_price =''; }
				if(isset($_REQUEST['category']) && $_REQUEST['category'] != ""){
					$cats_of =  count($_REQUEST['category']); 
				}
				else
				{ $cats_of = "";}
			 ?>
			<input type="hidden" name="all_cat" id="all_cat" value="0"/>
            <?php 
			$tmpdata = get_option('templatic_settings');
			if(isset($tmpdata['templatic-category_type']) && $tmpdata['templatic-category_type'] == 'select'):
			?>	
			<input type="hidden" name="all_cat_price" id="all_cat_price" value="<?php if(isset($_REQUEST['category']) && $_REQUEST['category'] !=""){ if(is_array($_REQUEST['category']) && $cats_of >0){ $cat = explode(",",$_REQUEST['category'][0]); echo $cat[1]; }else{ echo $_REQUEST['category'];  }  }else{ if(isset($cat_price) && $cat_price !=''){ echo $cat_price; }else{ echo "0"; } }  ?>"/>
            <?php else: ?>
            <input type="hidden" name="all_cat_price" id="all_cat_price" value="<?php if(isset($_REQUEST['category']) && $_REQUEST['category'] !=""){ echo $this->templ_fetch_category_price(@$_REQUEST['category']);  }else{ if(isset($cat_price) && $cat_price !=''){ echo $cat_price; }else{ echo "0"; } }  ?>"/>
            <?php endif;
			} ?>
			
			<div id="<?php echo $div_id; ?>" class="<?php echo $class; ?>">
			<?php
			
			if( $package_query->have_posts() && (!isset($_REQUEST['action']) && @$_REQUEST['action'] !='edit'))
			{
				?>
                <input type="hidden" name="pkg_id" id="pkg_id">
                <input type="hidden" name="pkg_type" id="pkg_type">
                <input type="hidden" name="package_free_submission" id="package_free_submission">
                <input type="hidden" name="upgrade" id="upgrade">
                <div class="clearfix" id="plan" >
                <?php
				if($div_id =='all_packages'){ ?>
					<div class="sec_title"><h3 id="package_data"><?php _e('Select a Package','templatic'); ?></h3></div>
					<span class="message_error2" id="all_packages_error"></span>
				<?php }		
			/* FETCH ALL THE PACKAGE DATA FROM POST META TABLE */
			$selected_pkg = $_SESSION['custom_fields']['package_select'];
			if($pkg_id !=''){
				$selected_pkg = $pkg_id;
			}		
			while($package_query->have_posts())
			{
				$package_query->the_post();
				if(isset($_REQUEST['upgpkg']) && $_REQUEST['upgpkg']==1 && $pkg_id==$post->ID){
					continue;
				}
				$disply_price_package=apply_filters('tevolution_price_package_loop_frontend','1',$post,$post_type);
				if($disply_price_package==''){
					continue;	
				}
				
				tmpl_display_package_html($post,$post_type);	
			} 
			echo '</div>';
		} ?>    		 
    </div>        
        
	<?php
	}
	/*
	 calculate pricing as per category selection
	*/
	function templ_fetch_category_price($category_id){
		if(isset($category_id))
			foreach($category_id as $_category_arr)
			{
			$category[] = explode(",",$_category_arr);
			}
		if(isset($category))
			foreach($category as $_category){
				$arr_category[] = $_category[0];
				$arr_category_price[] = $_category[1];
			}
			
		return $cat_price = @array_sum($arr_category_price);	
	}
	
	/*
	 get selected category ID
	*/
	function templ_get_selected_category_id($category_id){
		if(isset($category_id))
			foreach($category_id as $_category_arr)
			{
				$category[] = explode(",",$_category_arr);
			}
		if(isset($category))
			foreach($category as $_category){
				$arr_category[] = $_category[0];
				$arr_category_price[] = $_category[1];
			}
			
		return $cat_array = $arr_category;	
	}
	/*
	 get selected category price
	*/
	function templ_total_selected_cats_price($category_id){
		global $wpdb;
		if(!empty($category_id)){
			$cat_price = $wpdb->get_var("select sum(t.term_price) from $wpdb->terms t ,$wpdb->term_taxonomy tt where t.term_id = tt.term_taxonomy_id and tt.term_taxonomy_id in($category_id)");
			return $cat_price;	
		}
	}
	
	/*
	return selected price package information.
	*/
	function templ_get_price_info($pkg_id='',$price='')
	{ 
		global $wpdb,$recurring,$billing_num,$billing_per,$billing_cycle;
		
		
		if($pkg_id !="")
		{
			$subsql = " and p.ID =\"$pkg_id\"";	
		}
		
		wp_reset_query();
		$post = get_post($pkg_id); 
		
		if($post)
		{
			$info = array();
			$recurring = get_post_meta($post->ID,'recurring',true);
			if($recurring ==1){
			$validity = get_post_meta($post->ID,'billing_num',true);
			$vper = get_post_meta($post->ID,'billing_per',true);
			}else{
			$vper = get_post_meta($post->ID,'validity_per',true);
			$validity = get_post_meta($post->ID,'validity',true);
			}
			$cats = get_post_meta($post->ID,'category',true);
			$is_featured = get_post_meta($post->ID,'is_featured',true);
			
			$billing_num = get_post_meta($post->ID,'billing_num',true);
			$billing_per = get_post_meta($post->ID,'billing_per',true);
			$billing_cycle = get_post_meta($post->ID,'billing_cycle',true);
			if(($validity != "" || $validity != 0))
			{
				if($vper == 'M')
				{
					$tvalidity = $validity*30 ;
				}else if($vper == 'Y'){
					$tvalidity = $validity*365 ;
				}else{
					$tvalidity = $validity ;
				}
			}
			$info['title'] = $post->post_title;
			$info['package_type']=get_post_meta($post->ID,'package_type',true);
			$info['price'] = get_post_meta($post->ID,'package_amount',true);
			$info['days'] = @$tvalidity;
			$info['alive_days'] = @$tvalidity;
			$info['cat'] = $cats;
			$info['subscription_as_pay_post'] = get_post_meta($post->ID,'subscription_as_pay_post',true);
			$info['is_featured'] = $is_featured;
			
			/*Get the price package featured option */
			$info['is_home_page_featured'] = get_post_meta($post->ID,'is_home_page_featured',true);
			$info['is_category_page_featured'] = get_post_meta($post->ID,'is_category_page_featured',true);
			$info['feature_amount'] = get_post_meta($post->ID,'feature_amount',true);
			$info['feature_cat_amount'] = get_post_meta($post->ID,'feature_cat_amount',true);
			
			$info['is_home_featured'] = get_post_meta($post->ID,'is_home_featured',true);
			$info['is_category_featured'] =get_post_meta($post->ID,'is_category_featured',true);
			/*End get the price package featured option */
			$info['title_desc'] =$post->post_content;
			$info['is_recurring'] =$recurring;
			if($recurring == '1') {
				$info['billing_num'] = $billing_num;
				$info['billing_per'] = $billing_per;
				$info['billing_cycle'] = $billing_cycle;
			}
			$price_info[] = $info;
		}
		return @$price_info;
	}
	
	/*
	 set the price information of listing
	*/
	function templ_set_price_info($last_postid,$pid,$payable_amount,$alive_days,$payment_method,$coupon,$featured_type){
		$monetize_settings = array();
		$monetize_settings['paid_amount'] = $payable_amount;
		if($pid !='' && $alive_days ==""){
			$monetize_settings['alive_days'] = 'Unlimited'; }
		$monetize_settings['alive_days'] = $alive_days;
		$monetize_settings['paymentmethod'] = $payment_method;
		$monetize_settings['coupon_code'] = $coupon;
				$monetize_settings["paid_amount"] = $payable_amount;
		$monetize_settings["coupon_code"] = $coupon;
		if(!$featured_type){
			  $monetize_settings['featured_type'] = 'none';
			  $monetize_settings['featured_c'] = 'n';
			  $monetize_settings['featured_h'] = 'n';
		}
		if($featured_type == 'c'){
			 $monetize_settings['featured_h'] = 'n';
			 $monetize_settings['featured_c'] = 'c';
		}
		if($featured_type == 'h')
		 {
			 $monetize_settings['featured_c'] = 'n';
			 $monetize_settings['featured_h'] = 'h';
		 }
 		if($featured_type == 'both')
		 {
			 $monetize_settings['featured_c'] = 'c';
			 $monetize_settings['featured_h'] = 'h';
		 }
 		if($featured_type == 'none')
		 {
			 $monetize_settings['featured_c'] = 'n';
			 $monetize_settings['featured_h'] = 'n';
		 }
		foreach($monetize_settings as $key=>$val)
		{
				update_post_meta($last_postid, $key, $val);
		}
	
	}
	
	/*
		return the total price of selected categories
	*/
	function templ_total_price($taxonomy){
		$args = array('hierarchical' => true ,'hide_empty' => 0, 'orderby' => 'term_group');
		$terms = get_terms($taxonomy, $args);
		$total_price=0;
		foreach($terms as $term){
				$total_price += $term->term_price;
			
		}
		return $total_price;
	}
	/*
	 return the user last post featured type
	*/	
	function templ_get_featured_type($cur_user_id , $post_type){
		global $wpdb;
		/*package_select - package id of last post in database*/
		$user_last_post = $wpdb->get_row("select * from $wpdb->posts p where p.post_type='".$post_type."' and p.post_author = '".$cur_user_id."' order by p.ID DESC LIMIT 0,1");
		$user_last_post_id = @$user_last_post->ID; /* last inserted post */
		$featured_type=get_post_meta($user_last_post_id,'featured_type',true);
		return $featured_type;
	}
	
	/*
	return the package type of current user
	*/	
	function templ_get_packagetype($cur_user_id , $post_type){
		global $wpdb;
		/*package_select - package id of last post in database*/
		/*fetch only user publish user last post*/

		$user_last_post = $wpdb->get_row("select * from $wpdb->posts p where p.post_type = '".$post_type."' and p.post_author = '".$cur_user_id."' and post_status IN ('publish','trash') order by p.ID DESC LIMIT 0,1");
		$user_last_post_id = @$user_last_post->ID; /* last inserted post */
		
		$selected_pkg = get_post_meta($user_last_post_id,'package_select',true);/* selected package id to fetch package type*/
		
		$package_type = get_post_meta($selected_pkg,'package_type',true); /* 1- Single Submission, 2- Subscription */
		if(!$package_type){ $package_type =1; }
		return $package_type;
	}	
	
	/*
	 return the last post id
	*/	
	function templ_get_packagetype_last_postid($cur_user_id , $post_type){
		global $wpdb;
		/*package_select - package id of last post in database*/
		
		$user_last_post = $wpdb->get_row("select * from $wpdb->posts p where p.post_type NOT IN('attachment','inherit','nav-menu','page','post') and p.post_author = '".$cur_user_id."' and post_status IN ('publish','draft','trash') order by p.ID DESC LIMIT 0,1");
		$user_last_post_id = @$user_last_post->ID; /* last inserted post */
		
		return $user_last_post_id;
	}	
	
	
	/*
	 return the last post status of current user
	*/	
	function templ_get_packaget_post_status($cur_user_id , $post_type,$package_id){
		global $wpdb;
		/*package_select - package id of last post in database*/
		
		$user_last_post = $wpdb->get_row("select * from $wpdb->posts p where p.post_type NOT IN('attachment','inherit','nav-menu','page','post') and p.post_author = '".$cur_user_id."' and (p.post_status='publish' or p.post_status='draft') order by p.ID DESC LIMIT 0,1");
		if($user_last_post){
			$post_status = $user_last_post->post_status;
		}else{
			$post_status = fetch_posts_default_status();
		}
		return $post_status;
	}
	/*
	 fetch the details of package type user selected when come to submit the listing
	*/
	function templ_days_for_packagetype($cur_user_id , $post_type){
		global $wpdb;		
		$package_type = $this->templ_get_packagetype($cur_user_id , $post_type); /* 1- pay per posy, 2- Subscription */
		if($package_type == 2){
			if($cur_user_id){ 
	
			$adays = $wpdb->get_row("select * from $wpdb->posts p where p.post_type NOT IN('attachment','inherit','nav-menu','page','post') and p.post_author = '".$cur_user_id."' order by p.ID DESC LIMIT 0,1");
			if($adays->ID){ 
			$alive_day = get_post_meta($adays->ID,'alive_days',true);
			$publish_date =  strtotime($adays->post_date);
			$publish_date =  date('Y-m-d',$publish_date);
			$curdate = date('Y-m-d');
			
			$days = templ_number_of_days($publish_date,$curdate);
			if(($days == @$alive_days && $days < @$alive_days) || $days ==0){ $alive_days = $alive_day - $days; }else{ $alive_days =0; }
			return $alive_days;
			}
		}}
	}
	
	/*
	 get the alive days to check after submission for pay per subscription price package
	*/
	function templ_days_for_user_packagetype($cur_user_id , $post_type){
		global $wpdb;		
		$package_id = get_user_meta($cur_user_id ,'package_select',true); 
		$package_type = get_post_meta($package_id,'package_type',true);/* 1- pay per posy, 2- Subscription */		
		if($package_type == 2){
			if($cur_user_id){ 	
				$adays = $wpdb->get_row("select * from $wpdb->posts p where p.post_type NOT IN('attachment','inherit','nav-menu','page','post') and p.post_author = '".$cur_user_id."' order by p.ID DESC LIMIT 0,1");
				if($adays->ID){ 
					$alive_day = get_post_meta($adays->ID,'alive_days',true);
					$publish_date =  strtotime($adays->post_date);
					$publish_date =  date('Y-m-d',$publish_date);
					$curdate = date('Y-m-d');
					
					$days = templ_number_of_days($publish_date,$curdate);
					if(($days == $alive_days && $days < $alive_days) || $days ==0){ $alive_days = $alive_day - $days; }else{ $alive_days =0; }
					return $alive_days;
				}
			}
		}
	}
	/*
	* fetch the alive days of package type user selected when come to submit the listing
	*/
	function templ_free_alive_days_for_user_packagetype($cur_user_id='' , $post_type='',$package_id=''){
		global $wpdb;		
		$package_type = get_post_meta($package_id,'package_type',true);/* 1- pay per posy, 2- Subscription */		
		if($package_type == 2){
			if($cur_user_id){
				$adays = $wpdb->get_row("select * from $wpdb->posts p where p.post_type NOT IN('attachment','inherit','nav-menu','page','post') and p.post_author = '".$cur_user_id."' order by p.ID ASC LIMIT 0,1");
				if($adays->ID){ 
					$alive_day = get_post_meta($package_id,'subscription_days_free_trail',true);
					$publish_date =  strtotime($adays->post_date);
					$publish_date =  date('Y-m-d',$publish_date);
					$curdate = date('Y-m-d');
					
					$days = templ_number_of_days($publish_date,$curdate);
					if(( $days < $alive_day) || $days ==0){ $alive_days = $alive_day - $days; }else{ $alive_days =0; }
					return $alive_days;
				}
			}
		}
	}
	/*
	 fetch the details of package type user selected when come to submit the listing
	*/
	function is_user_have_alivedays($cur_user_id , $post_type){
		global $wpdb;
		
		$package_type = $this->templ_get_packagetype($cur_user_id , $post_type); /* 1- pay per posy, 2- Subscription */
		if($package_type == 2){
			if($cur_user_id){ 
			$adays = $wpdb->get_row("select * from $wpdb->posts p where p.post_type NOT IN('attachment','inherit','nav-menu','page','post') and p.post_author = '".$cur_user_id."' order by p.ID DESC LIMIT 0,1");
			if($adays->ID){
			$alive_day = get_post_meta($adays->ID,'alive_days',true);
			$publish_date =  strtotime($adays->post_date);
			$publish_date =  date('Y-m-d',$publish_date);
			$curdate = date('Y-m-d');
			
			$days = templ_number_of_days($publish_date,$curdate,30);
			/*echo $alive_day."=".$days;*/
				if($alive_day > $days && $days == 0){
					return false;
				}else{
					return true;
				}
			}else{
				return true;
			}
			}else{
				return true;
			}
		}else{
			return true;
		}
	}
	
	/*
	 fetch the details of package type user selected when come to submit the listing
	*/
	function is_package_have_alivedays($cur_user_id , $post_type,$package_id=''){
		global $wpdb;
		$package_type = get_post_meta($package_id,'package_type',true); /* 1- pay per posy, 2- Subscription */
		if($package_type == 2){
			if($cur_user_id){ 
				global $post,$wp_query;
				/*query to fetch all the enabled price package*/
				$args=array('post_type'      	=> $post_type,
							'posts_per_page' 	=> 1	,
							'post_status'    	=> array('publish'),
							'author'			=> $cur_user_id,
							'order'				=> 'ASC'
							);
				
				$post_query = null;
				$post_query = new WP_Query($args);	
				$post_meta_info = $post_query;
				if($post_meta_info->found_posts <= 0){
					return true;
				}
				elseif($post_meta_info->posts[0]->ID){
					$package_select = get_post_meta($post_meta_info->posts[0]->ID,'package_select',true);
					$alive_day = (get_post_meta($package_select,'days_for_no_post',true))?get_post_meta($package_select,'days_for_no_post',true):get_post_meta($post_meta_info->posts[0]->ID,'alive_days',true);
					$publish_date =  strtotime($post_meta_info->posts[0]->post_date);
					$publish_date =  date('Y-m-d',$publish_date);
					$curdate = date('Y-m-d');
					$days = templ_number_of_days($publish_date,$curdate,30);

					if($alive_day > $days ){
						return true;
					}else{
						return false;
					}
				}else{
					return true;
				}
				}else{
					return true;
				}
			}else{
				return true;
			}
	}
	
	/*
	* fetch the details of package type user selected when come to submit the listing
	*/
	function is_user_package_have_alivedays($cur_user_id='' , $post_type='',$package_id=''){
		global $wpdb,$monetization,$current_user;
		$users_packageperlist=$wpdb->prefix.'users_packageperlist';
		$package_type = get_post_meta($package_id,'package_type',true);
		$sql="SELECT * FROM $users_packageperlist WHERE user_id=".$current_user->ID." AND 	package_id=".@$_POST['package_select']." AND status=1";
		if($package_type == 2){
			if($cur_user_id){ 
			$adays = $wpdb->get_row("SELECT * FROM $users_packageperlist WHERE user_id=".$cur_user_id." AND package_id=".$package_id." AND status=1 ");
			$listing_price_info = $monetization->templ_get_price_info($package_id);
			/* Package Alive days */
			$alive_days=$listing_price_info[0]['alive_days'];
			if($adays->ID){
			$alive_day = (get_post_meta($adays->package_id,'days_for_no_post',true))?get_post_meta($adays->package_id,'days_for_no_post',true):get_post_meta($adays->package_id,'alive_days',true);
			$publish_date =  strtotime($adays->date);
			$publish_date =  date('Y-m-d',$publish_date);
			$curdate = date('Y-m-d');
			
			$days = templ_number_of_days($publish_date,$curdate,30);
			if($alive_days > $days && $days == 0){
					return false;
				}else{
					return true;
				}
			}else{
				return true;
			}
			}else{
				return true;
			}
		}else{
			return true;
		}
	}
	
	
	/* return total price including featured price , package price and category price */
	function tmpl_get_payable_amount($pkg_id=0,$featured_type=0,$scats='')
	{
		global $wpdb;
		
		$package_price = get_post_meta($pkg_id,'package_amount',true);
		if(isset($featured_type) && $featured_type == 'h')
		{
			$feature_amount = get_post_meta($pkg_id,'feature_amount',true);
		}elseif(isset($featured_type) && $featured_type == 'c')
		{
			$feature_amount = get_post_meta($pkg_id,'feature_cat_amount',true);
		}elseif(isset($featured_type) && $featured_type == 'both')
		{
			$feature_amount = get_post_meta($pkg_id,'feature_cat_amount',true)+get_post_meta($pkg_id,'feature_amount',true);
		}
		
		for($i=0;$i<count($scats);$i++)
		{
			$cat_price = explode(",",$scats[$i]);
			$category_price = get_term( $cat_price[0], $_POST['cur_post_taxonomy']);
			$final_cat_price += $category_price->term_price; 
		}
		return $package_price+$feature_amount+$final_cat_price;
	}
	/*
	 * fetch the price package for submit form. 
	 */
	function tmpl_fetch_price_package($user_id='',$post_type='',$page_id='')
	{
		global $post,$wp_query,$current_user,$wpdb;
		$transaction_tabel = $wpdb->prefix . "transactions";
		/*query to fetch all the enabled price package*/
		$args=array('post_type' => 'monetization_package',
					'posts_per_page' => -1	,
					'post_status' => array('publish'),
					'meta_query' => array('relation' => 'AND',
									  array('key' => 'package_status',
											'value' =>  '1',
											'compare' => '='
											),
				  					  array('key' => 'package_post_type',
											'value' =>  $post_type,
											'compare' => 'LIKE'
											)
								),
				    'orderby' => 'menu_order',
				    'order' => 'ASC'
			);

		/*Check user submited price package subscription */
		$package_id=get_user_meta($current_user->ID,'package_selected',true);/* get the user selected price package id*/
		if(!$package_id)
			$package_id=get_user_meta($current_user->ID,$post_type.'_package_select',true);/* get the user selected price package id*/
		$user_limit_post=get_user_meta($current_user->ID,$post_type.'_list_of_post',true); /*get the user wise limit post count on price package select*/
		if($user_limit_post==''){
			$user_limit_post='0';
		}
		if(isset($_REQUEST['upgpkg']) && $_REQUEST['upgpkg']==1)
		{
			$pkg_id = get_post_meta($_REQUEST['pid'],'package_select',true);
		}
		
		
		$package_post_type = explode(",",get_post_meta($package_id,'package_post_type',true));
		$package_sub_id=get_user_meta($current_user->ID,'sub_id',true);/* get the user selected price package id*/
		$package_limit_post=get_post_meta($package_sub_id,'limit_no_post',true);/* get the price package limit number of post*/
		$user_have_pkg = get_post_meta($package_id,'package_type',true); 
		$user_last_postid = $this->templ_get_packagetype_last_postid($current_user->ID,$post_type); /* User last post id*/
		$user_have_days = $this->templ_days_for_packagetype($current_user->ID,$post_type); /* return alive days(numbers) of last selected package  */
		$is_user_have_alivedays = $this->is_package_have_alivedays($current_user->ID,$post_type,$package_id); /* return user have an alive days or not true/false */
		
		$is_user_package_have_alivedays = $this->is_user_package_have_alivedays($current_user->ID,$post_type,$package_id); /* return user have an alive days or not true/false */
		$subscription_days_free_trail = (get_user_meta($current_user->ID,'package_free_submission',true))?get_user_meta($current_user->ID,'package_free_submission',true):0;
		
		$package_avlie_days = $this->templ_free_alive_days_for_user_packagetype($current_user->ID,$post_type,$package_id);
		$price_pacage_alive_days = (get_post_meta($package_id,'subscription_days_free_trail',true))?get_post_meta($package_id,'subscription_days_free_trail',true):0;/* get the price package limit number of post*/
		/*check last user post package type check*/
		if($current_user->ID )/* check user wise post per  Subscription limit number post post */
		{		
			/*Only get the pay per subscription package id from postmeta */
			$package_id_sql= "SELECT post_id from {$wpdb->prefix}postmeta where meta_key='package_type' AND meta_value=2";
			/*Get the user last transaction  */
			if($wpdb->query("SHOW TABLES LIKE '".$transaction_tabel."'")==1):
				$transaction_status = $wpdb->get_results("SELECT status,package_id FROM $transaction_tabel where payforpackage=1 AND user_id=".$current_user->ID." AND package_id in(".$package_id_sql.") order by trans_id DESC LIMIT 1");
				$trans_status=@$transaction_status[0]->status;
				$trans_package_id=@$transaction_status[0]->package_id;
				$post_types = explode(',',get_post_meta($package_id,'package_post_type',true)); 
				if(in_array($post_type,$post_types)): $is_posttype_inpkg=1; else: $is_posttype_inpkg=0; endif; /* check is this taxonomy included in package or not*/
			endif;	
		}
		$paypersubscription=0;
		$data_price = '';
		$listing_price_info = $this->templ_get_price_info($package_id);
		$subscription_alive_days = $listing_price_info[0]['alive_days'];
		/*alive days calculation of particualr price package*/
		$cal_pakg_alive_days = (get_post_meta($package_id,'days_for_no_post',true))?get_post_meta($package_id,'days_for_no_post',true):$subscription_alive_days;
		if($cal_pakg_alive_days > 0 )
		{
			$current_date = strtotime(date('Y-m-d h:i:s'));	
			$postid_str = $wpdb->get_results("select p.ID,t.payment_date,t.post_id from $wpdb->posts p,$transaction_tabel t where t.user_id=".$current_user->ID." AND (t.package_type is NULL OR t.package_type=0) group by t.trans_id order by t.trans_id ASC LIMIT 0,1");
			if(count($postid_str) > 0)
			{
				foreach($postid_str as $post_day)
				{
					$days_for_no_post = $current_date -  strtotime($post_day->payment_date);
					$days_for_no_post = floor($days_for_no_post/86400);
					$days_left = $cal_pakg_alive_days - $days_for_no_post;
				}
			}
			else
			{
				$days_left = $cal_pakg_alive_days;
			}
		}
		/*package alive days calculation finish*/
		if($current_user->ID && ($package_limit_post > $user_limit_post && $is_user_have_alivedays == 1 && $package_limit_post!=$user_limit_post && $user_limit_post!='' && $trans_status==1) &&  $subscription_days_free_trail >0 && $subscription_days_free_trail>=$package_avlie_days &&  in_array($post_type,$package_post_type) && get_user_meta($current_user->ID,'upgrade',true) != 'upgrade' && get_user_meta($current_user->ID,'package_free_submission_completed',true) != 'completed' && $days_left >=0 ){
			/*user purchase pay per subscription then show per pay post pirce package if user will be go through Single Submission wise */
			$args['meta_query'][2]=array('key' => 'package_type','value' =>  array(1,2),'compare' => 'NOT IN');
			$paypersubscription=1;
			$data_price = 1;
		}
		else if($current_user->ID && ($package_limit_post > $user_limit_post && $package_limit_post!=$user_limit_post && $user_limit_post !=''  && $trans_status==1) && in_array($post_type,$package_post_type)  && get_user_meta($current_user->ID,'package_free_submission_completed',true) != 'completed' && $days_left >=0){
			/*user purchase pay per subscription then show per pay post pirce package if user will be go through Single Submission wise */
			$args['meta_query'][2]=array('key' => 'package_type','value' =>  1,'compare' => '=');
			$paypersubscription=1;
		}
		else if($current_user->ID && ($package_limit_post > $user_limit_post && $package_limit_post!=$user_limit_post && $user_limit_post !='' && $subscription_days_free_trail >0 && ( $subscription_days_free_trail<=$price_pacage_alive_days || $price_pacage_alive_days ==0)   && $trans_status==1) && in_array($post_type,$package_post_type)  && get_user_meta($current_user->ID,'package_free_submission_completed',true) == 'completed' && $days_left >=0){
			/*user purchase pay per subscription then show per pay post pirce package if user will be go through Single Submission wise */
			$args['meta_query'][2]=array('key' => 'package_type','value' =>  1,'compare' => '=');
			$paypersubscription=1;
		}
			/* Finish user submitted price package subscription*/
		
		
		$post_query = null;
		$post_query = new WP_Query($args);	

        $post_meta_info = $post_query;	
		$i = 0;
		/*loop to fetch the pay per listing package*/
		if($post_meta_info->have_posts() || $paypersubscription==1){
			$is_single_price_package = $this->tmpl_fetch_is_single_price_package($current_user->ID,$post_type,$post->ID);
			if($data_price == 1)
			{?>
				 <input type="hidden" name="upgrade_price" id="upgrade_price" value="<?php echo get_post_meta($package_id,'package_amount',true); ?>">
              <?php
			}
			
			/** when user comes with shortlink provide in package - the first tab of select packages should not be display */
			if(isset($_REQUEST['pkg_id']) && $_REQUEST['pkg_id'] !=''){
				$firs_tab_class=" ";
			}else{
				$firs_tab_class=" active ";
			}
		?>
			<input type="hidden" name="pkg_id" id="pkg_id">
			<input type="hidden" name="pkg_type" id="pkg_type">
            <input type="hidden" name="package_free_submission" id="package_free_submission">
            <input type="hidden" name="upgrade" id="upgrade">
             <input type="hidden" name="completed" id="completed" value="<?php echo get_user_meta($current_user->ID,'package_free_submission_completed',true); ?>">
			<div id="step-plan" <?php if(is_numeric($is_single_price_package)) { ?> style="display:none;" <?php } ?>class="accordion-navigation step-wrapper step-plan current"><a class="step-heading active" href="#"><span>1</span><span><?php 
			
			$post_type = trim($post_type);
			$PostTypeObject = get_post_type_object($post_type);
			$_PostTypelabel = $PostTypeObject->labels->name;
			
			_e('Select your payment plan','templatic'); ?></span><span><i class="fa fa-caret-down"></i><i class="fa fa-caret-right"></i></span></a>
			<div id="plan" class="content step-plan <?php echo $firs_tab_class; ?> clearfix">
				<div id="packagesblock-wrap" class="block">
                	<?php 
					/*Display purchases pay per subscription package info */
					if($paypersubscription==1):
					?>
                    <div class="packageblock clearifx">
                        <ul data-price="0" data-id="<?php echo $package_sub_id; ?>"  <?php if(get_user_meta($current_user->ID,'upgrade',true) != 'upgrade') { ?> data-free="<?php echo get_post_meta($package_id,'subscription_days_free_trail',true); ?>"  <?php } ?> data-subscribed='1' data-type="2"  data-post="1" class="packagelistitems">
                        <li>
                        <div class="col-md-3 col-sm-6">
                            <div class="panel panel-default text-center">
                            	<div class="panel-heading">
	                                <h3><?php echo get_the_title($package_sub_id); ?></h3>
                                </div>
                                <div class="panel-desc">
	                                 <div class="panel-body">    
                                     <?php _e('You have already subscribed to this package. ','templatic');?>
                                    <p><?php 
									echo sprintf(__('This package allows you to add %d listings, you have already added %d. You still have %d listings left in your package.','templatic'),$package_limit_post,$user_limit_post,$package_limit_post-$user_limit_post);?></p>
                                     <?php
									 		if(get_user_meta($current_user->ID,'package_free_submission',true) > 0 && !isset($_REQUEST['page']) && !isset($_REQUEST['pmethod']) && get_user_meta($current_user->ID,'package_free_submission_completed',true) != 'completed')
										   	{
											   ?>
												<p class="margin_right panel-type price package_type"><?php echo '<label>'; _e('Number of free submissions: ','templatic');echo '</label>'; echo '<span>'; echo get_user_meta($current_user->ID,'package_free_submission',true);_e(' Submitted '); echo  (get_post_meta($package_sub_id,'subscription_days_free_trail',true) - get_user_meta($current_user->ID,'package_free_submission',true)); _e(' Left.'); echo '</span>'; ?> </p>
												<?php
										   	}
									 		/*condition to check submit listing within following days*/
									 		if(get_post_meta($package_sub_id,'days_for_no_post',true) > 0)
										   	{
												$current_date = strtotime(date('Y-m-d h:i:s'));	
												$postid_str = $wpdb->get_results("select p.ID,t.payment_date,t.post_id from $wpdb->posts p,$transaction_tabel t where t.user_id=".$current_user->ID." AND (t.package_type is NULL OR t.package_type=0) group by t.trans_id order by t.trans_id ASC LIMIT 0,1");
												if(count($postid_str) > 0)
												{
													foreach($postid_str as $post_day)
													{
														$days_for_no_post = $current_date -  strtotime($post_day->payment_date);
														$days_for_no_post = floor($days_for_no_post/86400);
														$days_left = get_post_meta($package_id,'days_for_no_post',true) - $days_for_no_post;
													}
												}
												else
												{
													$days_left = get_post_meta($package_sub_id,'days_for_no_post',true);
												}
											   ?>
												<p class="margin_right panel-type price package_type"><?php echo '<label>'; _e('Submit listing within following days: ','templatic');echo '</label>'; echo '<span>'; echo $days_left; echo '</span>'; ?> </p>
											   <?php
										   }?>
                                    </div> <!-- panel-body -->
                                    <?php 
									if(in_array($post_type,$package_post_type) && get_user_meta($current_user->ID,'upgrade',true) != 'upgrade' && $subscription_days_free_trail >0  && $is_user_have_alivedays == 1)
									{?>
                                        <div class="upgrade-button">
                                          <!--  <a data-id="<?php echo $package_id; ?>" data-upgrade="upgrade"  class="btn btn-lg btn-primary button select-plan"><?php _e('Upgrade','templatic'); ?></a> -->
                                        </div>
                                    <?php } ?>
                                    <div class="pkg-button">
                                       <a data-id="<?php echo $package_sub_id; ?>"  class="btn button button-primary button-large select-plan"><?php _e('Select','templatic'); ?></a>                                             
                                    </div> <!-- list-group -->
                                </div><!-- panel-desc -->
                            </div> <!-- panel panel-default -->         
                            <!-- package description -->
                        </div><!-- packages block div closed here -->
                        </li>
                        </ul>
                    </div><!-- package block div closed here -->
                    <?php
					
					endif;
					
					while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
						/*check whether the price package is pay per listing*/
							$i++;
							if(isset($_REQUEST['upgpkg']) && $_REQUEST['upgpkg']==1 && $pkg_id==$post->ID){
								continue;
							}
							$disply_price_package=apply_filters('tevolution_price_package_loop_frontend','1',$post,$post_type);
							if($disply_price_package==''){
								continue;	
							}
							
                                                                tmpl_display_package_html($post,$post_type);
							
					endwhile;
					?>
				</div> <!-- End #packageblock-wrap -->
			</div> <!-- End #panel1 -->
			</div>
		<?php
		}

	}
	/* fetch featured option for particular price package selected while submitting listing */
	function tmpl_fetch_price_package_featured_option($user_id='',$post_type='',$post_id='',$pkg_id='',$is_user_select_subscription_pkg='')
	{
		/* Set curent language in cookie */
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global  $sitepress;
			$_COOKIE['_icl_current_language'] = $sitepress->get_current_language();
		}
		$package_selected = get_post($pkg_id);
		if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="" && !is_admin()){
			$edit_id = $_REQUEST['pid'];
		}
		elseif(is_admin() && @$_REQUEST['action'] != 'tmpl_tevolution_submit_from_package_featured_option')
		{
			$edit_id = $post_id;
		}
		if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
			$author_can_moderate_comment = get_post_meta($pkg_id,'can_author_mederate',true);
		}
			$num_decimals   = absint( get_option( 'tmpl_price_num_decimals' ) );
			$num_decimals 	= ($num_decimals!='')?$num_decimals:'0';
			$decimal_sep    = wp_specialchars_decode( stripslashes( get_option( 'tmpl_price_decimal_sep' ) ), ENT_QUOTES );
			$decimal_sep 	= ($decimal_sep!='')?$decimal_sep:'.';
			$thousands_sep  = wp_specialchars_decode( stripslashes( get_option( 'tmpl_price_thousand_sep' ) ), ENT_QUOTES );
			$thousands_sep 	= ($thousands_sep!='')?$thousands_sep:',';
			$currency = get_option('currency_symbol');
			$position = get_option('currency_pos');
			
			$package_amount = apply_filters( 'formatted_tmpl_price', number_format( get_post_meta($pkg_id,'package_amount',true), $num_decimals, $decimal_sep, $thousands_sep ), $amount, $num_decimals, $decimal_sep, $thousands_sep );
			global  $wpdb,$current_user;
			global  $wpdb;
			 ?>
			<script type="text/javascript" async>
				var currency = '<?php echo get_option('currency_symbol'); ?>';
				var position = '<?php echo get_option('currency_pos'); ?>';
				var num_decimals    = '<?php echo $num_decimals; ?>';
				var decimal_sep     = '<?php echo $decimal_sep ?>';
				var thousands_sep   = '<?php echo $thousands_sep; ?>';
				<?php if(((isset($edit_id) && $edit_id !='' && (isset($_REQUEST['renew']))) || (!isset($edit_id) && $is_user_select_subscription_pkg == 0) || (isset($_REQUEST['backandedit']) && $_REQUEST['backandedit'] == 1) || (isset($_REQUEST['upgpkg']) && $_REQUEST['upgpkg'] == 1)) && (function_exists('is_price_package') && is_price_package($current_user->ID,$post_type,$post_id) > 0) )
					{ ?>
						var pkg_price = parseFloat(<?php echo get_post_meta($pkg_id,'package_amount',true); ?>);
						var edit = 0;
				<?php }
					else
					{
					?>
						var pkg_price = parseFloat(0);
						var edit = 1;
				<?php
					}
					?>
			</script>
            <?php
			$featured_h = (get_post_meta($pkg_id,'feature_amount',true))?get_post_meta($pkg_id,'feature_amount',true):0;  /*home page featured amount*/
			$featured_c = (get_post_meta($pkg_id,'feature_cat_amount',true))?get_post_meta($pkg_id,'feature_cat_amount',true):0; /*category page featured amount*/
			
			$is_home_featured = get_post_meta($pkg_id,'is_home_featured',true);/*is price package amount includes the home page featured amount*/
			$is_category_featured = get_post_meta($pkg_id,'is_category_featured',true); /*is price package amount includes the category page featured amount*/
			
			$is_home_page_featured = get_post_meta($pkg_id,'is_home_page_featured',true);/*is price package includes the home page featured */
			$is_category_page_featured = get_post_meta($pkg_id,'is_category_page_featured',true); /*is price package includes the category page featured*/	
			
			$package_alive_days = $this->templ_free_alive_days_for_user_packagetype($current_user->ID,$post_type,$pkg_id);
			$subscription_days_free_trail = get_post_meta($pkg_id,'subscription_days_free_trail',true);
			
			$edit_is_home_page_featured = get_post_meta($edit_id,'featured_h',true);
			$edit_is_cat_page_featured = get_post_meta($edit_id,'featured_c',true);
			
			if((@$is_home_page_featured || @$is_category_page_featured) && (!$is_home_featured || !$is_category_featured))
			{
			?>
				<div class="form_row clearfix" id="is_featured">
					<?php if(($is_home_page_featured && !@$is_home_featured) || ($is_category_page_featured && !@$is_category_featured)){ ?><label><strong><?php _e('Would you like to make this ','templatic').$post_type; _e('featured?','templatic'); ?></strong></label><?php } ?>
					<div class="feature_label">
						<?php
						if(!@$is_home_featured && $is_home_page_featured)
						{?>
							<label><input type="checkbox" <?php if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && $edit_is_home_page_featured == 'h' ){?>  checked="checked"  <?php if(!is_admin()){ ?>disabled="disabled" <?php } } elseif( $_SESSION['custom_fields']['featured_h']  != '') { ?>  checked="checked" <?php } ?> name="featured_h" id="featured_h" value="<?php echo apply_filters( 'formatted_tmpl_price', number_format( $featured_h, $num_decimals, $decimal_sep, $thousands_sep ), $amount, $num_decimals, $decimal_sep, $thousands_sep ); ?>" /><?php _e('Yes &sbquo; feature this listing on homepage.','templatic'); ?> <span id="ftrhome"><?php if(isset($featured_h) && $featured_h !=""){ echo "(".display_amount_with_currency_plugin($featured_h).")"; } ?></span></label>
					<?php }
						if(!@$is_category_featured && $is_category_page_featured){?>
							<label><input type="checkbox" <?php if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && $edit_is_cat_page_featured == 'c' ){?>  checked="checked"  <?php if(!is_admin()){ ?>disabled="disabled" <?php }  } elseif( $_SESSION['custom_fields']['featured_c']  != '') { ?>  checked="checked" <?php } ?> name="featured_c" id="featured_c" value="<?php echo apply_filters( 'formatted_tmpl_price', number_format( $featured_c, $num_decimals, $decimal_sep, $thousands_sep ), $amount, $num_decimals, $decimal_sep, $thousands_sep ); ?>" /><?php _e('Yes &sbquo; feature this listing on category page.','templatic'); ?><span id="ftrcat"><?php if(isset($featured_c) && $featured_c !=""){ echo "(".display_amount_with_currency_plugin($featured_c).")"; } ?></span></label>
					<?php }
						if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
							
							if($pkg_id)
							{
								$comment_mederation_amount = get_post_meta($pkg_id,'comment_mederation_amount',true);
							}
							if(!$comment_mederation_amount){ $comment_mederation_amount =0; }
							{
							?>
								<label><input type="checkbox" name="author_can_moderate_comment" id="author_can_moderate_comment" value="<?php echo $comment_mederation_amount; ?>" onclick="featured_list(this.id)" <?php if(@$_SESSION['custom_fields']['author_can_moderate_comment'] !=""){ echo "checked=checked"; } ?>/><?php echo ' ';_e(MODERATE_COMMENT,'templatic'); ?><span id="ftrcomnt"><?php if(isset($author_can_moderate_comment) && $author_can_moderate_comment !=""){ echo "(".fetch_currency_with_position($comment_mederation_amount).")"; }else{ echo "(".fetch_currency_with_position('0').")"; } ?></span></label>
								<input type="hidden" name="author_moderate" id="author_moderate" value="0"/>
							<?php	
							}
						}
						?>
						<input type="hidden" name="featured_type" id="featured_type" value="<?php echo ($featured_type)? $featured_type : 'none'?>"/>
						<span id='process' style='display:none;'><i class="fa fa-circle-o-notch fa-spin"></i></span>
					
					</div>
				</div>
			<?php }
			$cat_price=0;
			if(isset($_SESSION['custom_fields']) && $_SESSION['custom_fields']['category']!=''){
				
				foreach($_SESSION['custom_fields']['category'] as $category){
					$category_price = explode(',',$category);
					$cat_price+=$category_price[1];	
				}				
				$package_price=get_post_meta($_SESSION['custom_fields']['pkg_id'],'package_amount',true);				
			}
			if(isset($_REQUEST['backandedit']) && $_REQUEST['backandedit']==1){
				$cat_price = $_SESSION['custom_fields']['all_cat_price'];
				$package_price=get_post_meta($_REQUEST['pkg_id'],'package_amount',true);				
			}
			if(@$_REQUEST['front_end'] == 1 || @$_REQUEST['backandedit'] == 1 || (function_exists('is_price_package') && is_price_package($current_user->ID,$post_type,$post_id) <= 0) || (isset($_REQUEST['pid']) && !empty($_REQUEST['pid']) && isset($_REQUEST['action']) && !empty($_REQUEST['action'])))
			{
			?>
			<div id="price_package_price_list" class="form_row clearfix" style="display:none;">
            	<div class="form_cat">
	               <span class="total_charges"><b><?php _e('Total Charges:','templatic'); echo ' '; ?></b></span>
					<span id="before_cat_price_id"  <?php if($cat_price<=0):?> style="display:none;"<?php endif;?> ><?php if($position == '1'){ echo $currency; }else if($position == '2'){ echo $currency.'&nbsp;';}?></span>
					<span id="cat_price" <?php if($cat_price<=0):?>style="display:none;"<?php endif;?> ><?php echo apply_filters( 'formatted_tmpl_price', number_format( $cat_price, $num_decimals, $decimal_sep, $thousands_sep ), $amount, $num_decimals, $decimal_sep, $thousands_sep );?></span>
					<span id="cat_price_id" style="display:none;"><?php if($position == '3'){ echo $currency; }else if($position != 1 && $position != 2 && $position !=3){ echo '&nbsp;'.$currency; } ?>		</span>		
					<span id="cat_price_add" style="display:none;"><?php echo '+'; ?> </span>	
                    <?php if((isset($edit_id) && $edit_id !='' && (isset($_REQUEST['renew']))) || (!isset($edit_id) && $is_user_select_subscription_pkg == 0) || isset($_REQUEST['upgpkg']) && $_REQUEST['upgpkg']==1  || isset($_REQUEST['backandedit']) && $_REQUEST['backandedit']==1 )
						 { ?>
					<span id="pakg_add" <?php if($package_price<=0):?>style="display:none;"<?php endif;?>><?php echo '+';?> 	</span>	
					
					<span id="before_pkg_price_id" <?php if(@$package_amount <=0){ ?>style="display:none;" <?php } ?>><?php if($position == '1'){ echo $currency; }else if($position == '2'){ echo $currency.'&nbsp;'; } ?></span>
					<span id="pkg_price" <?php if(@$package_amount <=0){?> style="display:none;" <?php } ?> ><?php if(isset($package_amount) && $package_amount !=""){ echo $package_amount; } else{ echo "0";}?></span>
					<span id="pkg_price_id" <?php if(@$package_amount <=0){ ?>style="display:none;" <?php } ?> ><?php if($position == '3'){ echo $currency; }else if($position != 1 && $position != 2 && $position !=3){ echo '&nbsp;'.$currency; } ?>	</span>	
					<span id="pakg_price_add" style="display:none;" ><?php echo '+'; ?> </span>	
                    <?php } ?>
                    
					
					
					<span id="before_feture_price_id" style="display:none;"><?php if($position == '1'){ echo $currency; }else if($position == '2'){ echo $currency.'&nbsp;'; } ?></span>
					<span id="feture_price" style="display:none;"><?php if($fprice !=""){ echo $fprice ; }else{ echo "0"; }?></span>
					<span id="feture_price_id" style="display:none;"><?php if($position == '3'){ echo $currency; }else if($position != 1 && $position != 2 && $position !=3){ echo '&nbsp;'.$currency; } ?></span>	
					
					<span id="cat_price_total_price" style="display:none;"><?php echo "<span id='result_price_equ'>=</span>"; ?>
					<?php if($position == '1'){ echo '<span id="currency_before_result_price">'.$currency.'</span>'; }else if($position == '2'){ echo '<span id="currency_before_space_result_price">'.$currency.'&nbsp;</span>'; } ?>
					<span id="result_price"><?php if($total_price != ""){ echo $total_price; }else if($catid != ""){  echo $catprice->term_price; }else{ echo "0";} ?></span>
					<?php if($position == '3'){ echo '<span id="currency_after_result_price">'.$currency.'</span>'; }else if($position != 1 && $position != 2 && $position !=3){ echo '<span id="currency_after_space_result_price">&nbsp;'.$currency."</span>"; } ?></span>
					
					
				</div>
				<span class="message_note"> </span>
				<span id="category_span" class="message_error2"></span>
			<!-- END - FETCH TOTAL PRICE -->
			</div>
			<?php
			}
	}
	
	/* fetch featured option for particular price package selected while submitting listing */
	function tmpl_fetch_is_single_price_package($user_id='',$post_type='',$post_id='')
	{
		global $post,$wp_query;
		/*query to fetch all the enabled price package*/
		$args=array('post_type'      => 'monetization_package',
					'posts_per_page' => -1	,
					'post_status'    => array('publish'),
					'meta_query'     => array('relation' => 'AND',
										array('key' => 'package_status','value' =>  '1','compare' => '='),
										array('key' => 'package_post_type','value' =>  $post_type,'compare' => 'LIKE')
									)
					);

		$post_query = null;
		$post_query = new WP_Query($args);	
		$post_meta_info = $post_query;	
		
		/*return the different value for price package have more than one price package , single price package and none of them.*/
		if($post_meta_info->found_posts > 1){
			return 'show_price_package';			
		}elseif($post_meta_info->found_posts == 1){
			if(get_post_meta($post_meta_info->posts[0]->ID,'package_type',true) == 1){
				return $post_meta_info->posts[0]->ID;
			}else{
				return 'show_price_package';
			}
		}else{
			return false;
		}
	}
} /* class end */
}
if(!isset($monetization))
{
	$monetization = new monetization();
}

/* function to fetch transactions */
function tmpl_get_transaction_status($tid,$pid){
	global $wpdb,$transection_db_table_name;
	$transection_db_table_name = $wpdb->prefix.'transactions'; 
	$trans_status = $wpdb->get_row("select status,payable_amt from $transection_db_table_name t where trans_id = '".$tid."' order by t.trans_id DESC");
	$result = '';
	if($trans_status->status == 0 && $trans_status->payable_amt>0){
		$result = '<a id="p_status_'.$tid.'" onclick="change_transstatus('.$tid.','.$pid.')" style="color:#E66F00; font-weight:normal;"  href="javascript:void(0);">'.__('Pending','templatic').'</a>';
	}else if($trans_status->status == 1){
		$result = '<span style="color:green; font-weight:normal;">'.__('Approved','templatic').'</span>';
	}
	else if($trans_status->status == 2){
		$result = '<span style="color:red; font-weight:normal;">'.__('Cancel','templatic').'</span>';
	}
	else if($trans_status->status == 0 && $trans_status->payable_amt<=0){
		$result = "-";
	}
	return apply_filters('tmpl_get_transaction_status',$result,$tid,$pid);
}
/*	To fetch payment option	*/
function fetch_payment_description($pid)
{
	global $wpdb;
	$transection_db_table_name = $wpdb->prefix.'transactions';
	$transsql_select = "select * from $transection_db_table_name where post_id = ". $pid." AND (package_type is NULL OR package_type=0) ORDER BY trans_id DESC LIMIT 1";
	$transsql_result = $wpdb->get_row($transsql_select);
	
	$payment_options = get_option('payment_method_'.$transsql_result->payment_method);
	$payment_method_name = $payment_options['name'];
	if($transsql_result->status){
		$status = __("Approved",'templatic');
	}else{
		$status = __("Pending",'templatic');
	}
	
	$decimals=get_option('tmpl_price_num_decimals');
	$decimals=($decimals!='')?$decimals:2;
	
	echo "<li><p class='submit_info_label'>". __('Amount','templatic') .": </p> <p class='submit_info_detail'> ".fetch_currency_with_symbol(number_format(@$transsql_result->payable_amt,$decimals,".",""))."</p></li>";
	if($transsql_result->payment_method!="")
	{
		if(function_exists('icl_register_string')){
			icl_register_string('templatic',$payment_method_name,$payment_method_name);
		}
		
		if(function_exists('icl_t')){
			$payment_method_name = icl_t('templatic',$payment_method_name,$payment_method_name);
		}else{
			$payment_method_name = __($payment_method_name,'templatic'); 
		}
		echo "<li><p class='submit_info_label'>". __('Payment Method','templatic') .": </p> <p class='submit_info_detail'> ".@$payment_method_name."</p></li>";
	}
	if(function_exists('icl_register_string')){
			icl_register_string('templatic',$status,$status);
		}
		
		if(function_exists('icl_t')){
			$status = icl_t('templatic',$status,$status);
		}else{
			$status = __($status,'templatic'); 
		}
	echo "<li><p class='submit_info_label'>". __('Status','templatic') .": </p> <p class='submit_info_detail'> ".$status."</p></li>";
}
/*
	Insert transaction detail in transaction table.
*/
function insert_transaction_detail($paymentmethod='',$last_postid,$is_upgrade=0,$is_package=0,$is_featured_h=0,$is_featured_c=0,$is_category=0)
{
	/* Transaction Report */
	global $wpdb,$payable_amount,$current_user;
	if($payable_amount==''){
		$payable_amount='0';
	}
	if($is_upgrade == 1)
	{
		$post_details=get_post_meta($last_postid,'upgrade_data',true);
		$package_select=$post_details['package_select'];
	}else
	{
		$package_select=get_post_meta($last_postid,'package_select',true);
	}
	
	$package_select=($package_select)?$package_select:$_POST['pkg_id'];

	$post_author  = $wpdb->get_row("select * from $wpdb->posts where ID = '".$last_postid."'") ;
	$post_title  = $post_author->post_title;
	$post_date = $post_author->post_date;
	$post_author  = ($post_author->post_author)? $post_author->post_author : $current_user->ID  ;
	$uinfo = get_userdata($post_author);
	$user_fname = $uinfo->display_name;
	$user_email = $uinfo->user_email;
	$user_billing_name = $uinfo->display_name;
	
	$billing_Address = '';
	if($paymentmethod == "")
	 {
		$paymentmethod = "-"; 
	 }
	global $transection_db_table_name;
	if(is_admin() && !DOING_AJAX){
		$status =1;
	}else{
		if(get_post_status( $last_postid ) == 'publish' && $payable_amount <=0)
			$status = 1;
		else
			$status = 0;
	}
	
	/* id edit post then insert payment date as post publish date because of correct expiration date is set */
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'){
		$pdate = $post_date; /* if edit then set payment date as post date */
	}else{
		$pdate = date("Y-m-d H:i:s"); /* if new post then add today date as payment date */
	}
	
	$transection_db_table_name=$wpdb->prefix.'transactions';
	$transaction_insert = 'INSERT INTO '.$transection_db_table_name.' set 
		post_id="'.$last_postid.'",
		user_id = "'.$post_author.'",
		post_title ="'.strip_tags($post_title).'",
		payment_method="'.$paymentmethod.'",
		payable_amt="'.str_replace(',', '', $payable_amount).'",
		payment_date="'.$pdate.'",
		paypal_transection_id="",
		status="'.$status.'",
		user_name="'.$user_fname.'",
		pay_email="'.$user_email.'",
		billing_name="'.$user_billing_name.'",
		billing_add="'.$billing_Address.'",
		package_id="'.$package_select.'",
		payforpackage="'.$is_package.'",
		payforfeatured_h="'.$is_featured_h.'",
		payforfeatured_c="'.$is_featured_c.'",
		payforcategory="'.$is_category.'"';
	
	$wpdb->query($transaction_insert);
	return $wpdb->insert_id;
	/* End Transaction Report */
}
/*
 fetch payment method name.
*/
function get_payment_method($method)
{
	global $wpdb;
	$paymentsql = "select * from $wpdb->options where option_name like 'payment_method_$method'";
	$paymentinfo = $wpdb->get_results($paymentsql);
	if($paymentinfo)
	{
		foreach($paymentinfo as $paymentinfoObj)
		{
			$paymentInfo = unserialize($paymentinfoObj->option_value);
			return $paymentInfo['name'];
		}
	}
}

/* function returns the number of days the listing will be published in particular price package */
function tmpl_show_package_period($post_id)
{
	global $wpdb,$current_user;
	/*check price package is recurring or not*/
	$recurring = get_post_meta($post_id,'recurring',true);
	if($recurring ==1){
		$validity = get_post_meta($post_id,'billing_num',true);
		$vper = get_post_meta($post_id,'billing_per',true);
	}else{
		$vper = get_post_meta($post_id,'validity_per',true);
		$validity = get_post_meta($post_id,'validity',true);
	}
	
	if(($validity != "" || $validity != 0))
	{
		if($vper == 'M')
		{
			$tvalidity = $validity*30 ;
		}else if($vper == 'Y'){
			$tvalidity = $validity*365 ;
		}else{
			$tvalidity = $validity ;
		}
	}
	do_action('tmpl_before_success_price_package',$post_id);
	
	
	if(get_post_meta($post_id,'package_type',true) == 2)
	{ ?>
		<p class="panel-type price package_type"><?php echo '<label>'; _e('Package Type: ','templatic'); echo '</label>'; echo ' <span>'; _e('Subscription','templatic'); echo '</span>'; ?> </p>
        <p class="panel-type price package_type"><?php echo '<label>'; _e('Listing duration: ','templatic'); echo '</label>'; echo ' <span>';  echo $tvalidity; _e(" days",'templatic'); echo '</span>'; ?> </p>
		<p class="margin_right panel-type price package_type"><?php echo '<label>'; _e('Number of listings included in the package: ','templatic');echo '</label>'; echo '<span>'; echo get_post_meta($post_id,'limit_no_post',true); echo '</span>'; ?> </p>
       <?php  if(get_post_meta($post_id,'days_for_no_post',true) > 0)
	   {
			?>
           	<p class="margin_right panel-type price package_type"><?php echo '<label>'; _e('Listings can be submitted within: ','templatic');echo '</label>'; echo '<span>'; echo get_post_meta($post_id,'days_for_no_post',true); echo ' '; _e('days','templatic'); echo '</span>'; ?> </p>
           <?php
	   }
	   if(get_post_meta($current_user->ID,'package_free_submission',true) > 0 && !isset($_REQUEST['page']) && !isset($_REQUEST['pmethod']) && get_user_meta($current_user->ID,'package_free_submission_completed',true) != 'completed' )
	   {
		   ?>
           	<p class="margin_right panel-type price package_type"><?php echo '<label>'; _e('Number of free submissions: ','templatic');echo '</label>'; echo '<span>'; echo get_post_meta($current_user->ID,'package_free_submission',true);_e(' Submitted '); echo  get_post_meta($post_id,'subscription_days_free_trail',true); _e(' Left.'); echo '</span>'; ?> </p>
            <?php
	   }
	} 
	elseif(get_post_meta($post_id,'package_type',true) == 1)
	{ 	?>
		<p class="margin_right panel-type price package_type"><?php echo '<label>'; _e('Package Type: ','templatic'); echo '</label>'; echo '<span>'; _e('Single Submission','templatic'); echo '</span>'; ?> </p>
        <p class="panel-type price package_type"><?php echo '<label>'; _e('Listing duration: ','templatic'); echo '</label>'; echo ' <span>';  echo $tvalidity; _e(" days",'templatic'); echo '</span>'; ?> </p>
       <?php
	}
	$package_billing_num = get_post_meta($post_id,'billing_num',true);
	$package_billing_per =get_post_meta($post_id,'billing_per',true);
	$package_billing_cycle =get_post_meta($post_id,'billing_cycle',true);
	$first_free_trail_period =get_post_meta($post_id,'first_free_trail_period',true);
	$days_for_no_post =get_post_meta($post_id,'subscription_days_free_trail',true);
	if(get_post_meta($post_id,'recurring',true)=='1')
	{
	echo '<p class=""><label>'; _e('Recurring period','templatic'); echo ':&nbsp;</label><span>'.get_post_meta($post_id,'billing_num',true) ."&nbsp;";
	if($package_billing_per == 'D')
	{
		if($package_billing_num==1){ _e('Day','templatic'); }else{ _e('Days','templatic'); }
	}
	elseif($package_billing_per == 'M')
	{
		if($package_billing_num==1){ _e('Month','templatic'); }else{ _e("Months",'templatic'); }
	}
	else
	{
		if($package_billing_num==1){ _e('Year','templatic'); }else{ _e('Years','templatic'); }
	}
	echo "</p>";
	if($package_billing_cycle == '' || $package_billing_cycle == 0)
	{
		$package_billing_cycle = __('unlimited','templatic');
	}
	echo '<p class=""><label>'; _e('Number of cycles','templatic'); echo ':&nbsp;</label><span>'.$package_billing_cycle."&nbsp;<p>";
	}
	if($first_free_trail_period==1 && !isset($_REQUEST['page']) && !isset($_REQUEST['pmethod'])){ 
	?>
	<p><?php _e('This price package will offer free trial period or plan to bill the first instalment of a recurring payment only for PayPal payment gateway.','templatic');?></p>
	<?php }
	if($days_for_no_post>0 && !isset($_REQUEST['page']) && !isset($_REQUEST['pmethod']) && get_user_meta($current_user->ID,'package_free_submission_completed',true) != 'completed' ){ 
	?>
	<p class="margin_right"><?php echo '<label>'; _e('Number of free submissions: ','templatic');echo '</label>'; echo '<span>'; echo $days_for_no_post; echo '</span>'; ?></p>
	<?php }
	
	do_action('tmpl_after_success_price_package',$post_id);
}

/* function returns the html for featured option that is included by default in price package */
function tmpl_show_package_included_featured_option($post_id)
{

	/* package is featured or not */
	$is_featured = get_post_meta($post_id,'is_featured',true); 
	if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
		$author_can_moderate_comment = get_post_meta($post_id,'can_author_mederate',true);
	}
	
		/*home page featured amount*/
		$featured_h = get_post_meta($post_id,'feature_amount',true); 
		/*category page featured amount*/
		$featured_c = get_post_meta($post_id,'feature_cat_amount',true); 
		/*is price package amount includes the home page featured amount*/
		$is_home_featured = get_post_meta($post_id,'is_home_featured',true);
		$is_home_featured_checked = '';
		$is_home_featured_disabled = '';
		
		$is_home_page_featured = get_post_meta($post_id,'is_home_page_featured',true);
		$is_category_page_featured = get_post_meta($post_id,'is_category_page_featured',true);

		
		$is_home_featured_text = (get_post_meta($post_id,'is_home_featured',true))?__('Homepage','templatic'):__('Yes &sbquo; feature this listing on homepage','templatic');
		
		/*is price package amount includes the category page featured amount*/
		$is_category_featured = get_post_meta($post_id,'is_category_featured',true); 
	
		$is_category_featured_text = (get_post_meta($post_id,'is_category_featured',true))?__('Category page','templatic'):__('Yes &sbquo; feature this listing on categorypage','templatic');
		if($is_home_featured || $is_category_featured)
		{
			?>
				<p><label><strong><?php _e('Listings submitted with this package will be automatically featured on: ','templatic');?></strong></label>
				<?php
				if($is_home_featured && $is_category_featured){
				?>
					<span><?php _e('Homepage and Category page','templatic'); ?> </span>
				<?php
				}
				
				if($is_home_featured && !$is_category_featured)
				{ ?>
					<span><?php echo $is_home_featured_text; ?> </span>
				<?php
				}
				if($is_category_featured && !$is_home_featured)
				{ ?>
					<span><?php echo $is_category_featured_text; ?></span>
				<?php 
				} ?>
				</p>
				<span id='process' style='display:none;'><i class="fa fa-circle-o-notch fa-spin"></i></span>
							
			<?php
		}else{
			if((get_post_meta($post_id,'feature_amount',true) || get_post_meta($post_id,'feature_cat_amount',true)) && ($is_home_page_featured || $is_category_page_featured)){
				echo '<p>';
					echo '<strong>';_e('Listings submitted with this package will be featured on: ','templatic');echo '</strong>';
				if($is_home_page_featured && !$is_home_featured){
					_e('Homepage','templatic');
					echo " (".fetch_currency_with_position(get_post_meta($post_id,'feature_amount',true));
					if(get_post_meta($post_id,'home_page_alive_days',true)){ _e(" for ",'templatic');echo get_post_meta($post_id,'home_page_alive_days',true)." ";_e("days",'templatic'); }
					echo ") ";
				}
				if($is_category_page_featured && !$is_category_featured){
					_e('Category page','templatic');
					echo " (".fetch_currency_with_position(get_post_meta($post_id,'feature_cat_amount',true));
					if(get_post_meta($post_id,'cat_page_alive_days',true)){ _e(" for ",'templatic');echo get_post_meta($post_id,'cat_page_alive_days',true)." ";_e("days",'templatic'); }
					echo ")";
				}
			}
			
		}
}
/* include js for price calculation while listing submission*/
add_action('wp_enqueue_scripts', 'calculate_price_package');
function calculate_price_package() {
	global $pagenow,$post;
	if (@$pagenow == 'post.php' || @$pagenow == 'post-new.php' || @get_post_meta($post->ID,'is_tevolution_submit_form',true) == 1 || @get_post_meta($post->ID,'is_tevolution_upgrade_form',true) == 1) {
        wp_register_script('calculate_package_price', TEVOLUTION_PAGE_TEMPLATES_URL.'tmplconnector/monetize/templatic-monetization/js/calculate_package_price.js','','',true);
        wp_enqueue_script('calculate_package_price');
    }  
}
/* 
 * Display category as per price package.
 */
add_action( 'wp_ajax_nopriv_tmpl_tevolution_select_pay_per_subscription_price_package','tmpl_tevolution_select_pay_per_subscription_price_package');
add_action( 'wp_ajax_tmpl_tevolution_select_pay_per_subscription_price_package' ,'tmpl_tevolution_select_pay_per_subscription_price_package');
function tmpl_tevolution_select_pay_per_subscription_price_package(){
	global $current_user,$post,$monetization;
	$post_type = $_REQUEST['submit_post_type'];
	$result = '';
	$result .= $monetization->tmpl_fetch_is_single_price_package($current_user->ID,$post_type,$post->ID);/*fetch the price package*/
	echo $result;exit;
}

/*
 * Insert/update user package per list 
 * This function save users package information per post submitted
 */
function insert_update_users_packageperlist($post_id,$_post,$trans_id){
	global $wpdb,$current_user,$monetization;
	if($_post['package_select']==''){
		return;	
	}
	$users_packageperlist=$wpdb->prefix.'users_packageperlist';
	$subscriber_id=rand().strtotime(date('Y-m-d'));
	$listing_price_info = $monetization->templ_get_price_info($_post['pkg_id']);
	
	if($listing_price_info[0]['package_type']==2){
		/*Get the active selected package id user wise  */
		$sql="SELECT * FROM $users_packageperlist WHERE user_id=".$current_user->ID." AND 	package_id=".$_post['package_select']." AND status=1";		
		$results=$wpdb->get_results($sql);
		
		/*Get the existing user subscriber id */
		if($results[0]->subscriber_id!= '')
			$subscriber_id=$results[0]->subscriber_id;
			
		$packageperlist_insert = "INSERT INTO ".$users_packageperlist." set 
			user_id = ".$current_user->ID.",
			post_id =".$post_id.",
			package_id =".$_post['package_select'].",
			trans_id=".$trans_id.",
			subscriber_id='".$subscriber_id."',
			date='".date("Y-m-d")."',
			status=1";
	}
	else
	{
		$packageperlist_insert = "INSERT INTO ".$users_packageperlist." set 
			user_id = ".$current_user->ID.",
			post_id =".$post_id.",
			package_id =".$_post['package_select'].",
			trans_id=".$trans_id.",
			subscriber_id='".$subscriber_id."',
			date='".date("Y-m-d")."',
			status=1";
	}
	$wpdb->query($packageperlist_insert);	
}

/*
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
add_action( 'init', 'tevolution_daily_schedule_expire_featured_option' );

function tevolution_daily_schedule_expire_featured_option() {	
	if ( ! wp_next_scheduled( 'daily_schedule_featured_option' ) ) {		
		wp_schedule_event( time(), 'daily', 'daily_schedule_featured_option');
	}
}
add_action( 'wp_footer', 'do_daily_schedule_featured_option' );
/*
 * check whether the listing expire the home or category page featured option
 */
if(!function_exists('do_daily_schedule_featured_option'))
{
	function do_daily_schedule_featured_option()
	{
		$post_type = tevolution_get_post_type();
		global $post,$wp_query,$monetization;
		/*query to fetch all the post with tevolution post type*/
			$args=
			array( 
			'post_type' => $post_type,
			'posts_per_page' => -1	,
			'post_status' => array('publish'),
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => 'featured_c',
					'value' =>  'c',
					'compare' => 'LIKE'
					),
				array(
					'key' => 'featured_h',
					'value' =>  'h',
					'compare' => 'LIKE'
					)
				)
			);
		$post_query = null;
		$post_query = new WP_Query($args);		
		$post_meta_info = $post_query;	
		if($post_meta_info){
			while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
				/*select the post package id*/
				$package_select = get_post_meta($post->ID,'package_select',true);
				/*select the post date*/
				$post_date = strtotime($post->post_date);
				/*set the currnet date*/
				$current_date = strtotime(date_i18n('Y-m-d G:i:s'));
				/*get the difference between of current date and package alive date*/
				$day_diff = floor(($current_date - $post_date) / (60 * 60 * 24));
				/*if the difference between of current date and package alive date is greater than home page alive days of tha price package for that particular post */
				$home_page_alive_days = get_post_meta($package_select,'home_page_alive_days',true);
				if($day_diff > $home_page_alive_days && $home_page_alive_days !='')
				{
					/*set home page featured option*/
					if(get_post_meta($post->ID,'featured_h',true) != '')
						update_post_meta($post->ID,'featured_h','n');
					/*set featured_type option to category page featured if cat page featured alive days is not expired*/
					if(get_post_meta($post->ID,'featured_c',true) == 'c')
					{
						update_post_meta($post->ID,'featured_type','c');
					}
					else
					{
						update_post_meta($post->ID,'featured_type','none');
					}
				}
				/*if the difference between of current date and package alive date is greater than category page alive days of tha price package for that particular post */
				$cat_page_alive_days = get_post_meta($package_select,'cat_page_alive_days',true);
				if($day_diff > $cat_page_alive_days && $cat_page_alive_days != '')
				{
					/*set category page featured option*/
					if(get_post_meta($post->ID,'featured_c',true) != ''){
						update_post_meta($post->ID,'featured_c','n');
					}
					/*set featured_type option to home page featured if home page featured alive days is not expired*/
					if(get_post_meta($post->ID,'featured_h',true) == 'h')
					{
						update_post_meta($post->ID,'featured_type','h');
					}else
					{
						update_post_meta($post->ID,'featured_type','none');
					}
				}
			endwhile;
			wp_reset_query();
			wp_reset_postdata();
		}
	}
}
/*
* display price package html on submit form page.
*/
function tmpl_display_package_html($post,$post_type='')
{
	global $current_user,$transaction_table,$wpdb;
	$transaction_table = $wpdb->prefix . "transactions";
	/*Check user submitted price package subscription */
	$package_id=get_user_meta($current_user->ID,'package_selected',true);/* get the user selected price package id*/
	if(!$package_id)
		$package_id=get_user_meta($current_user->ID,$post_type.'_package_select',true);/* get the user selected price package id*/
	$user_limit_post=get_user_meta($current_user->ID,$post_type.'_list_of_post',true); /*get the user wise limit post count on price package select*/
	if($user_limit_post==''){
		$user_limit_post='0';
	}
	if(isset($_REQUEST['upgpkg']) && $_REQUEST['upgpkg']==1)
	{
		$pkg_id = get_post_meta($_REQUEST['pid'],'package_select',true);
	}
	
	
	$package_post_type = explode(",",get_post_meta($package_id,'package_post_type',true));
	$user_package = get_user_meta($current_user->ID,$post_type.'_package_select',true);
	/*check selected package */
	if($user_package){
		if($wpdb->query("SHOW TABLES LIKE '".$transaction_table."'")==1):
			$transaction_status = $wpdb->get_results("SELECT status,package_id FROM $transaction_table where payforpackage=1 AND user_id=".$current_user->ID." AND package_id =".$user_package." order by trans_id DESC LIMIT 1");
		endif;
	}
	$package_disable_class = '';
	$trans_status=@$transaction_status[0]->status;
	$trans_package_id=@$transaction_status[0]->package_id;
	if(count($transaction_status)!=0 && $trans_status==0 &&  $post->ID == $trans_package_id && get_post_meta($post->ID,'package_type',true) == 2 ){
		$package_disable_class = 'overlay_opacity';
	}
	$class = '';
	if(is_admin())
	{
		$package_select = get_post_meta($_REQUEST['post'],'package_select',true);
		if($post->ID == $package_select)
		{
			$class = 'selected';
		}
	}
	
	if (function_exists('icl_register_string')) {	
		icl_register_string('tevolution-price', 'package-name'.$package_desc,$post->post_title);			
		$post->post_title = icl_t('tevolution-price', 'package-name'.$post->ID,$post->post_title);
		$post->post_content = icl_t('tevolution-price', 'package-desc'.$post->ID,$post->post_content);
	}	
	
	?>
  		<div class="packageblock clearifx <?php echo $package_disable_class; ?>">
            <ul data-price="<?php echo get_post_meta($post->ID,'package_amount',true); ?>"  <?php if(get_user_meta($current_user->ID,'upgrade',true) != 'upgrade' && get_post_meta($post->ID,'subscription_days_free_trail',true) >  get_post_meta($current_user->ID,'package_free_submission',true) &&  get_user_meta($current_user->ID,'package_free_submission_completed',true) != 'completed' ) { ?> data-free="<?php echo get_post_meta($post->ID,'subscription_days_free_trail',true); ?>" <?php } ?> data-subscribed='0' data-id="<?php echo $post->ID; ?>" data-type="<?php echo get_post_meta($post->ID,'package_type',true); ?>" <?php if(get_post_meta($post->ID,'subscription_as_pay_post',true)) { ?> data-post="<?php echo get_post_meta($post->ID,'subscription_as_pay_post',true); ?>" <?php } ?> class="packagelistitems <?php echo $class; ?>" >
                <li>
                    <div class="col-md-3 col-sm-6">
                		<div class="panel panel-default text-center">
                            <div class="panel-heading">
                                <h3><?php echo $post->post_title; ?></h3>
                            </div>
                             <?php
							 if(count($transaction_status)!=0 && $trans_status==0 &&  $post->ID == $trans_package_id && get_post_meta($post->ID,'package_type',true) == 2 ){
								if($current_user->ID )/* check user wise post per  Subscription limit number post post */
								{		
									/*Only get the pay per subscription package id from postmeta */
									$package_id_sql= "SELECT post_id from {$wpdb->prefix}postmeta where meta_key='package_type' AND meta_value=2";
									/*Get the user last transaction  */
									if($wpdb->query("SHOW TABLES LIKE '".$transaction_table."'")==1):
										$transaction_status = $wpdb->get_results("SELECT status,package_id FROM $transaction_table where payforpackage=1 AND user_id=".$current_user->ID." AND package_id in(".$package_id_sql.") order by trans_id DESC LIMIT 1");
										$trans_status=$transaction_status[0]->status;
										$trans_package_id=$transaction_status[0]->package_id;
										if(count($transaction_status)!=0 && $trans_status==0 && in_array($post_type,$package_post_type) ){				
											$admin_email=get_option('admin_email');
											echo sprintf(__('You have subscribed to this package but your transaction is not approved yet. Please %s contact%s the administrator of the site for more details.','templatic'),'<a style="position:relative;z-index:1;" href="mailto:'.$admin_email.'">','</a>');
										}
									endif;	
									
									$post_types = explode(',',get_post_meta($package_id,'package_post_type',true)); 
									if(in_array($post_type,$post_types)): $is_posttype_inpkg=1; else: $is_posttype_inpkg=0; endif; /* check is this taxonomy included in package or not*/
								}
							 }
							?>
                  			<div class="panel-desc">
                        		<div class="panel-body">
                                    <span class="panel-title price"><?php  echo "<label>"; _e('Price: ','templatic'); echo "</label><span>".display_amount_with_currency_plugin(get_post_meta($post->ID,'package_amount',true)); ?></span></span> 
                                    <span class="days">
                                        <?php 
                                            /*show particular price package period or days*/
                                            tmpl_show_package_period($post->ID);
                                        ?>
                                    </span>
                                     <?php
                                        /*show particular price package includes fetured options*/
                                        echo tmpl_show_package_included_featured_option($post->ID);
                                    ?>
                            <!-- package description -->
                                    <div class="moreinfo">
                                        <?php  echo $post->post_content; ?>
                                    </div> 
                                </div> <!-- panel-body -->
                                <div class="pkg-button">
                                    <a data-id="<?php echo $post->ID; ?>"  class="btn btn-lg btn-primary button select-plan"><?php _e('Select','templatic'); ?></a>
                                </div> <!-- list-group -->
                    		</div><!-- panel-desc -->
               			</div> <!-- panel panel-default -->         
                	<!-- package description -->
            		</div><!-- packages block div closed here -->
                </li>
            </ul>
        </div>  
    <?php
}
/*return the count of price package.to check whether price package is enable or not.*/
function is_price_package($user_ID='',$post_type='',$post_isID='')
{
	global $post,$wp_query,$current_user,$wpdb;
	$transaction_tabel = $wpdb->prefix . "transactions";
	/*query to fetch all the enabled price package*/
	$args=array('post_type' => 'monetization_package',
				'posts_per_page' => -1	,
				'post_status' => array('publish'),
				'meta_query' => array('relation' => 'AND',
								  array('key' => 'package_status',
										'value' =>  '1',
										'compare' => '='
										),
								  array('key' => 'package_post_type',
										'value' =>  $post_type,
										'compare' => 'LIKE'
										)
							),
				'orderby' => 'menu_order',
				'order' => 'ASC'
		);
	$post_query = null;
	$post_query = new WP_Query($args);	
	return count($post_query->posts);
}
?>