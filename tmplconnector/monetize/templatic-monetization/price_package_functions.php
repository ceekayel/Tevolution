<?php
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
						//echo $last_postid."=".$category."=".$_tax;
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
		$package_is_featured = $post_details['is_featured'];
		$package_feature_amount = $post_details['feature_amount'];
		$package_feature_cat_amount = $post_details['feature_cat_amount'];
		$limit_no_post = $post_details['limit_no_post'];
		$first_free_trail_period = $post_details['first_free_trail_period'];
		$custom = array('package_type' => $package_type,
						'package_post_type' => $package_post_type,
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
						'is_featured' => $package_is_featured,
						'feature_amount' => $package_feature_amount,
						'feature_cat_amount' => $package_feature_cat_amount,
						'limit_no_post'=>$limit_no_post,
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
	/* EOF - INSERT PACKAGE DATA INTO THE DATABASE */
	/* EOF - DELETE PACKAGE DATA */
	/*
	Name :fetch_monetization_packages_back_end
	Description : To display the feature details of the price packages  in backed
	*/
	function fetch_monetization_packages_back_end($pkg_id,$div_id,$post_type,$taxonomy_slug,$post_cat)
	{
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
											  array('key' => 'show_package',
												   'value' =>  array(''),
												   'compare' => 'IN',
												   'type'=> 'text'),
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
											  array('key' => 'show_package',
												   'value' =>  array('1',''),
												   'compare' => 'IN',
												   'type'=> 'text'),
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
											  array('key' => 'show_package',
												   'value' => array('1'),
												   'compare' => 'IN',
												   'type'=> 'text'),
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
					} ?>
					
				<!-- DISPLAY THE PACKAGE IN FRONT END -->	
					<div class="backedn_package package">
					 <label><input type="radio" onclick="show_featuredprice(<?php echo get_the_ID(); ?>);" id="price_select_<?php echo get_the_ID(); ?>" name="package_select" value="<?php echo get_the_ID(); ?>" <?php if(@$selected_pkg == get_the_ID()) {?>  checked="checked" <?php  } ?>>
					 <h4><?php echo get_the_title(); ?></h4>
					 <p><?php echo get_the_content(); ?></p>
					 <p><?php echo __('Package type',ADMINDOMAIN);  echo ": ";  if($package_type  ==2){ echo __('Pay per subscription',ADMINDOMAIN); }else{ echo __('Pay per post',ADMINDOMAIN); } ?></p>
					 <?php
					 if($package_type  == 2 && get_post_meta(get_the_ID(),'limit_no_post',true) !=''){
						echo LIMIT_NO_POST.": ".get_post_meta(get_the_ID(),'limit_no_post',true);
					 }
					 ?>
					 <p class="cost"><span><?php echo __('Charges',ADMINDOMAIN); ?>: <?php echo fetch_currency_with_position($package_amount); ?></span>&nbsp;&nbsp; <span><?php echo VALIDITY_TEXT; ?>: <?php echo $package_validity; ?>&nbsp;
					 <?php  if($package_validity_per == 'D')
							{
								 echo DAYS_TEXT;
							}
							elseif($package_validity_per == 'M')
							{
								echo MONTHS_TEXT;
							}
							else
							{
								echo YEAR_TEXT;
							} ?></span></p>
					 <?php					
						 if($package_is_recurring=='1')
						 {
							echo '<p class=""><span>'; echo __('Recurring Charges',ADMINDOMAIN); echo ':&nbsp;</span><span>'.$package_billing_num ."&nbsp;";
							if($package_billing_per == 'D')
							{
								echo DAYS_TEXT;
							}
							elseif($package_billing_per == 'M')
							{
								echo MONTHS_TEXT;
							}
							else
							{
								echo YEAR_TEXT;
							}
							echo "&nbsp;".$package_billing_cycle."&nbsp;Cycle. </span>";
						 }
						  
						 ?>
					    </label>
					 </div>	
                          <span id='process2' style='display:none;'><img src="<?php echo TEMPL_PLUGIN_URL."tmplconnector/monetize/templatic-monetization/images/process.gif"; ?>" alt='Processing..' /></span>	
		<?php 
			}
		wp_reset_query();
		wp_reset_postdata();
		$post=$packages_post;			
	}
	/*
	Name :fetch_package_feature_details_backend
	Description : To display the feature details of the price packages 
	*/
	function fetch_package_feature_details_backend($edit_id='',$png_id='',$all_cat_id){	
		/* set feature price when Go back and edit */
		if(isset($edit_id) && $edit_id !=''){
			$price_select =  get_post_meta($edit_id,'package_select',true); /* selected package */
			$is_featured = get_post_meta($price_select,'is_featured',true); // package is featured or not 
			if($is_featured ==1){
				$featured_h = get_post_meta($price_select,'feature_amount',true); //
				$featured_c = get_post_meta($price_select,'feature_cat_amount',true); //
				$is_featured_h = get_post_meta($edit_id,'featured_h',true); //
				$is_featured_c = get_post_meta($edit_id,'featured_c',true); //
				$featured_type = get_post_meta($edit_id,'featured_type',true); //
			}		
		}else{
			$featured_h =0;
			$featured_c =0;
		}	
		?>
			<!-- FETCH FEATURED POST PRICES IN BACK END -->
            <?php global $post; 
		  	$post_type = (get_post_meta($post->ID,'template_post_type',true)!="")? get_post_meta($post->ID,'template_post_type',true):get_post_meta($post->ID,'submit_post_type',true); ?>
			<div class="form_row clearfix is_backend_featured" id="is_featured" <?php echo ($is_featured!=1) ?'style="display:none"':'';?>>
				<label><strong><?php _e('Would you like to make this ',ADMINDOMAIN).$post_type; _e('featured?',ADMINDOMAIN); ?></strong></label>
				<div class="feature_label">
					<label><input type="checkbox" name="featured_h" id="featured_h" value="<?php echo $featured_h; ?>" onclick="featured_list(this.id)" <?php if(@$is_featured_h !="" && $is_featured_h =="h"){ echo "checked=checked"; } ?>/><?php _e(FEATURED_H,ADMINDOMAIN); ?> <span id="ftrhome"><?php if(isset($featured_h) && $featured_h !=""){ echo "(".fetch_currency_with_position($featured_h).")"; }else{ echo "(".fetch_currency_with_position('0').")"; } ?></span></label>
					<label><input type="checkbox" name="featured_c" id="featured_c" value="0" onclick="featured_list(this.id)" <?php if(@$is_featured_c !="" && $is_featured_c =="c"){ echo "checked=checked"; } ?>/><?php _e(FEATURED_C,ADMINDOMAIN); ?><span id="ftrcat"><?php if(isset($featured_c) && $featured_c !=""){ echo "(".fetch_currency_with_position($featured_c).")"; }else{ echo "(".fetch_currency_with_position('0').")"; } ?></span></label>
					<?php
						if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
							$author_moderate = get_post_meta($edit_id,'author_moderate',true);
							$comment_mederation_amount = get_post_meta($price_select,'comment_mederation_amount',true); //
						?>
							<label><input type="checkbox" name="author_can_moderate_comment" id="author_can_moderate_comment" value="0" onclick="featured_list(this.id)" <?php if(@$author_moderate !="" && $author_moderate =="1"){ echo "checked=checked"; } ?>/><?php _e(MODERATE_COMMENT,DOMAIN); ?><span id="ftrcomnt"><?php if(isset($author_moderate) && $author_moderate =="1"){ echo "(".fetch_currency_with_position($comment_mederation_amount).")"; }else{ echo "(".fetch_currency_with_position('0').")"; } ?></span></label>
							<input type="hidden" name="author_moderate" id="author_moderate" value="0"/>
						<?php	
						}
					?>
					<input type="hidden" name="featured_type" id="featured_type" value="<?php echo ($featured_type)? $featured_type : 'none'?>"/>
					<span id='process' style='display:none;'><img src="<?php echo get_template_directory_uri()."/images/process.gif"; ?>" alt='Processing..' /></span>
					
				</div>
				<?php				
					$msg_note = sprintf(__("An additional amount will be charged to make this %s featured. You have the option to feature your %s on home page or category page or both.",ADMINDOMAIN),$post_type,$post_type);
					if(function_exists('icl_register_string')){
						icl_register_string(ADMINDOMAIN,$msg_note,$msg_note);
					}
					
					if(function_exists('icl_t')){
						$msg_note1 = icl_t(ADMINDOMAIN,$msg_note,$msg_note);
					}else{
						$msg_note1 = __($msg_note,ADMINDOMAIN); 
					}
				?>
				<span class="message_note"><?php _e($msg_note1,ADMINDOMAIN);?></span>
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
	/* NAME : FETCH PACKAGE IN FRONT END
	DESCRIPTION : THIS FUNCTION WILL FETCH ALL THE PACKAGES IN FRONT END */
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
					  'meta_query' => array('relation' => 'AND',array('key' => 'package_post_type','value' =>$post_type,'compare' => 'LIKE','type'=> 'text'),array('key' => 'show_package','value' =>  array(1),'compare' => 'IN','type'=> 'text'),array('key' => 'package_status','value' =>  '1','compare' => '=')),
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
				if($div_id =='all_packages'){ ?>
					<div class="sec_title"><h3 id="package_data"><?php _e('Select a Package',DOMAIN); ?></h3></div>
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
			
			$package_type = get_post_meta($post->ID,'package_type',true);
			$package_post_type = get_post_meta($post->ID,'package_post_type',true);
			$package_categories = get_post_meta($post->ID,'category',true);
			$show_package = get_post_meta($post->ID,'show_package',true);
			$package_amount = get_post_meta($post->ID,'package_amount',true);
			$recurring = get_post_meta($post_id,'recurring',true);
			if($package_type == 2 && $recurring == 1){
				$package_validity = get_post_meta($post->ID,'billing_num',true);
				$package_validity_per =get_post_meta($post->ID,'billing_per',true);
			}else{
				$package_validity = get_post_meta($post->ID,'validity',true);
				$package_validity_per = get_post_meta($post->ID,'validity_per',true);
			}
			$package_status = get_post_meta($post->ID,'package_status',true);
			$recurring = get_post_meta($post->ID,'recurring',true);
			$billing_num = get_post_meta($post->ID,'billing_num',true);
			$billing_per = get_post_meta($post->ID,'billing_per',true);
			$billing_cycle = get_post_meta($post->ID,'billing_cycle',true);
			$is_featured = get_post_meta($post->ID,'is_featured',true);
			$feature_amount_home = get_post_meta($post->ID,'feature_amount',true);
			$feature_cat_amount = get_post_meta($post->ID,'feature_cat_amount',true);  
			$featured_h = get_post_meta($post->ID,'home_featured_type',true); 
			$featured_c = get_post_meta($post->ID,'featured_type',true);
			$package_is_recurring = get_post_meta($post->ID,'recurring',true);
			$package_billing_num = get_post_meta($post->ID,'billing_num',true);
			$package_billing_per =get_post_meta($post->ID,'billing_per',true);
			$package_billing_cycle =get_post_meta($post->ID,'billing_cycle',true);
			$first_free_trail_period =get_post_meta($post->ID,'first_free_trail_period',true);
			if (function_exists('icl_register_string')) {	
				icl_register_string('tevolution-price', 'package-name'.$package_desc,$post->post_title);			
				$post->post_title = icl_t('tevolution-price', 'package-name'.$post->ID,$post->post_title);
				$post->post_content = icl_t('tevolution-price', 'package-desc'.$post->ID,$post->post_content);
		  	}	
			
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
				} ?>
				
			<!-- DISPLAY THE PACKAGE IN FRONT END -->	
				<div class="package">
				 <label><input type="radio" onclick="show_featuredprice(<?php echo $post->ID; ?>);" id="price_select_<?php echo $post->ID; ?>" name="package_select" value="<?php echo $post->ID; ?>" <?php if($selected_pkg == $post->ID) {?>  checked="checked" <?php  } ?>>
				 <h3><?php echo $post->post_title; ?></h3>
				 <p><?php echo $post->post_content; ?></p>
				 <p><?php _e('Package type',DOMAIN);  echo ": ";  if($package_type  ==2){ _e('Pay per subscription',DOMAIN); }else{ _e('Pay per post',DOMAIN); } ?></p>
				 <?php
				 if($package_type  == 2 && get_post_meta($post->ID,'limit_no_post',true) !=''){
					_e(LIMIT_NO_POST,DOMAIN);echo ": ".get_post_meta($post->ID,'limit_no_post',true);
				 }
					if($package_is_recurring ==0 || $package_is_recurring ==''){
				 ?>
				 <p class="cost"><span><?php _e('Charges',DOMAIN); ?>: <?php echo fetch_currency_with_position($package_amount); ?></span>&nbsp;&nbsp; <span><?php echo _e(VALIDITY_TEXT,DOMAIN); ?>: <?php echo $package_validity; ?>&nbsp;
				 <?php  
						if($package_validity_per == 'D')
						{
							if($package_validity==1){ _e('Day',DOMAIN); }else{ _e('Days',DOMAIN); }
						}
						elseif($package_validity_per == 'M')
						{
							if($package_validity==1){ _e('Month',DOMAIN); }else{ _e('Month',DOMAIN); }
						}
						else
						{
							if($package_validity==1){ _e('Year',DOMAIN); }else{ _e('Year',DOMAIN); } ;
						}
						
						?></span></p>
                     <?php }else{ ?>
					 <p class="cost"><span><?php _e('Charges',DOMAIN); ?>: <?php echo fetch_currency_with_position($package_amount); ?></span>&nbsp;&nbsp; <span></p>
					<?php }					 
					 if($package_is_recurring=='1')
					 {
						echo '<p class=""><span>'; _e('Recurring Charges',DOMAIN); echo ':&nbsp;</span><span>'.$package_billing_num ."&nbsp;";
						if($package_billing_per == 'D')
						{
							if($package_billing_num==1){ _e('Day',DOMAIN); }else{ _e('Day',DOMAIN); }
						}
						elseif($package_billing_per == 'M')
						{
							if($package_billing_num==1){ _e('Month',DOMAIN); }else{ _e('Month',DOMAIN); }
						}
						else
						{
							if($package_billing_num==1){ _e('Year',DOMAIN); }else{ _e('Year',DOMAIN); }
						}
						echo "&nbsp;".$package_billing_cycle."&nbsp;";  _e('Cycle',DOMAIN); echo "</span>";
					 }
					 if($first_free_trail_period==1){ 
					 ?>
                     <p><?php _e('This price package will be offer free trial periods or plan to bill the first installment of a recurring payment only for paypal payment gateway.',DOMAIN);?></p>
                     <?php } ?>
                        </label>
				 </div>		
	<?php } 
	} ?>    		 
    		
		</div>        
        
	<?php
	}
	/*
	Name : templ_fetch_category_price
	Desc : calculate pricing as per category selection
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
	Name : templ_get_selected_category_id
	Desc : get selected category ID
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
	Name : templ_total_selected_cats_price
	Desc : get selected category ID
	*/
	function templ_total_selected_cats_price($category_id){
		global $wpdb;
		if(!empty($category_id)){
			$cat_price = $wpdb->get_var("select sum(t.term_price) from $wpdb->terms t ,$wpdb->term_taxonomy tt where t.term_id = tt.term_taxonomy_id and tt.term_taxonomy_id in($category_id)");
			return $cat_price;	
		}
	}
	
	
	
	/*
	Name :fetch_package_feature_details
	Description : To display the feature details of the price packages 
	*/
	function fetch_package_feature_details($edit_id='',$png_id='',$all_cat_id){		
		/* set feature price when Go back and edit */
		if(!isset($_REQUEST['backandedit']) && @$_REQUEST['backandedit'] ==''){
			$_SESSION['custom_fields']= array(); // change it because fields come with values even we comes for new listing 
		
		}
		if(isset($edit_id) && $edit_id !=''){
			$price_select =  get_post_meta($edit_id,'package_select',true); /* selected package */
			$is_featured = get_post_meta($price_select,'is_featured',true); // package is featured or not 
			if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
				$author_can_moderate_comment = get_post_meta($pkg_id,'can_author_mederate',true);
			}
			if($is_featured ==1){
				$featured_h = get_post_meta($price_select,'feature_amount',true); //
				$featured_c = get_post_meta($price_select,'feature_cat_amount',true); //
			}
		}elseif(isset($_REQUEST['backandedit']) && @$_REQUEST['backandedit'] !=''){
			$pkg_id = $_SESSION['custom_fields']['package_select'];
			$is_featured = get_post_meta($pkg_id,'is_featured',true);
			if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
				$author_can_moderate_comment = get_post_meta($pkg_id,'can_author_mederate',true);
			}
			if($is_featured ==1){
				
				$featured_h = get_post_meta($pkg_id,'feature_amount',true); //
				$featured_c = get_post_meta($pkg_id,'feature_cat_amount',true); //
				if(!$featured_h){ $featured_h =0; }
				if(!$featured_c){ $featured_c =0; }
			}	
		}else{
			$featured_h =0;
			$featured_c =0;
			if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
				$author_can_moderate_comment = 0;
			}
		}
		$featured_type = @$_SESSION['custom_fields']['featured_type'];?>
			<!-- FETCH FEATURED POST PRICES IN FRONT END -->
            <?php global $post; 
		  	$post_type = (get_post_meta($post->ID,'template_post_type',true)!="")? get_post_meta($post->ID,'template_post_type',true):get_post_meta($post->ID,'submit_post_type',true); ?>
			
			<div id='process2' style='width:100%; visibility:hidden;'><img src="<?php echo TEMPL_PLUGIN_URL."tmplconnector/monetize/templatic-monetization/images/process.gif"; ?>" alt='Processing..' /></div>
			
			<div class="form_row clearfix" id="is_featured" <?php if(@$is_featured == '1'){ }else{ ?>style="display:none; } <?php }?>">
				<label><strong><?php _e('Would you like to make this ',DOMAIN).$post_type; _e('featured?',DOMAIN); ?></strong></label>
				<div class="feature_label">
				
					<label><input type="checkbox" name="featured_h" id="featured_h" value="<?php echo $featured_h; ?>" onclick="featured_list(this.id)" <?php if(@$_SESSION['custom_fields']['featured_h'] !=""){ echo "checked=checked"; } ?>/><?php _e('Yes &sbquo; feature this listing on homepage.',DOMAIN); ?> <span id="ftrhome"><?php if(isset($featured_h) && $featured_h !=""){ echo "(".fetch_currency_with_position($featured_h).")"; }else{ echo "(". __('Free',DOMAIN) .")"; } ?></span></label>
					
					<label><input type="checkbox" name="featured_c" id="featured_c" value="<?php echo $featured_c; ?>" onclick="featured_list(this.id)" <?php if(@$_SESSION['custom_fields']['featured_c'] !=""){ echo "checked=checked"; } ?>/><?php _e('Yes &sbquo; feature this listing on category page.',DOMAIN); ?><span id="ftrcat"><?php if(isset($featured_c) && $featured_c !=""){ echo "(".fetch_currency_with_position($featured_c).")"; }else{ echo "(". __('Free',DOMAIN).")"; } ?></span></label>
					
					
					
					<input type="hidden" name="featured_type" id="featured_type" value="<?php echo ($featured_type)? $featured_type : 'none'?>"/>
					<span id='process' style='display:none;'><img src="<?php echo get_template_directory_uri()."/images/process.gif"; ?>" alt='Processing..' /></span>
					
				</div>
				<?php
					if(function_exists('icl_register_string')){
						$msg_note = sprintf(__("An additional amount will be charged to make this %s featured. You have the option to feature your %s on home page or category page or both.",DOMAIN),$post_type,$post_type);
					
						icl_register_string(DOMAIN,$msg_note,$msg_note);
				
						$msg_note1 = icl_t(DOMAIN,$msg_note,$msg_note);
					?>
					<span class="message_note"><?php _e($msg_note1,DOMAIN);?></span>
					<?php
					}else{ 
						$post_type=get_post_meta(@$post->ID,'submit_post_type',true);
						$PostTypeObject = get_post_type_object($post_type);
						$_PostTypeName = $PostTypeObject->labels->name;?>
						<span class="message_note"><?php _e('An additional amount will be charged to make this',DOMAIN); echo " ".$_PostTypeName." "; _e('featured.',DOMAIN); _e('You have the option to feature your',DOMAIN); echo " ".$post_type." "; _e('on home page or category page or both.',DOMAIN); ?></span>
					<?php }
				?>
			
				<span id="category_span" class="message_error2"></span>
			</div>
			
			<div class="form_row clearfix feature_label" id="moderate_comment" <?php if(@$author_can_moderate_comment == 1){ }else{ ?>style="display:none; <?php }?>">
				<label><strong><?php _e('Would you like to make this ',DOMAIN).$post_type; _e('featured?',DOMAIN); ?></strong></label>
			<?php
						if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
							
							if($pkg_id)
							{
								$comment_mederation_amount = get_post_meta($pkg_id,'comment_mederation_amount',true);
							}
							if(!$comment_mederation_amount){ $comment_mederation_amount =0; }
							{
							?>
								<label><input type="checkbox" name="author_can_moderate_comment" id="author_can_moderate_comment" value="<?php echo $comment_mederation_amount; ?>" onclick="featured_list(this.id)" <?php if(@$_SESSION['custom_fields']['author_can_moderate_comment'] !=""){ echo "checked=checked"; } ?>/><?php echo ' ';_e(MODERATE_COMMENT,DOMAIN); ?><span id="ftrcomnt"><?php if(isset($author_can_moderate_comment) && $author_can_moderate_comment !=""){ echo "(".fetch_currency_with_position($comment_mederation_amount).")"; }else{ echo "(".fetch_currency_with_position('0').")"; } ?></span></label>
								<input type="hidden" name="author_moderate" id="author_moderate" value="0"/>
							<?php	
							}
						}
				
					   if(function_exists('icl_register_string')){
						$msg_note = sprintf(__("An additional amount will be charged to make this %s comment moderateable.",DOMAIN),$post_type);
						
							icl_register_string(DOMAIN,$msg_note,$msg_note);
	
							$msg_note1 = icl_t(DOMAIN,$msg_note,$msg_note);

							$msg_note1 = __($msg_note,DOMAIN); ?>
							<span class="message_note"><?php _e($msg_note1,DOMAIN);?></span>
					<?php	}else{
								_e("An additional amount will be charged to make this",DOMAIN); echo " ".$post_type." "; _e("comment moderateable.",DOMAIN);
							}				
					?>
					
			</div>
			<!-- END - FETCH FEATURED POST PRICE -->
			
			<!-- FETCH THE TOTAL AMOUNT TO BE PAID IN FRONT END -->
		
			<div id="price_package_price_list" <?php  if((isset($_SESSION['custom_fields']['total_price'] ) && $_SESSION['custom_fields']['total_price'] > 0 ) || isset($_SESSION['custom_fields']['cat_price']) || isset($_SESSION['custom_fields']['feture_price'] ) || (isset($_REQUEST['category']) && $this->templ_fetch_category_price(@$_REQUEST['category']) !=0)){ ?> style="display:block;" <?php }else{ ?> style="display:none;" <?php }   ?> class="form_row clearfix">
				<label><?php _e('Total price as per your selection.',DOMAIN);?> <span>*</span> <?php echo '<span class="message_note">'; _e("Final amount including discount will be displayed on preview page.",DOMAIN); echo "</span>"; ?></label>
				<div class="form_cat"  >
				<?php if(isset($category_id)){ $catid = $category_id; }else{ $catid =''; }
					if(isset($cat_array) && $cat_array != "")
					{
						$catid = $cat_array;
					}
					else
					{
						 $catid =''; 
					} 
					if(!isset($total_price))
					{
						$total_price = 0;
					}
				
					/* variables to set category pricing */
					if(isset($_REQUEST['category'])){
						$cat_price = $this->templ_fetch_category_price(@$_REQUEST['category']);
						$price_select = @$_SESSION['custom_fields']['package_select'];
						$packprice = get_post_meta(@$_SESSION['custom_fields']['package_select'],'package_amount',true);
						
						
						if($featured_type !=''){
							if($featured_type =='both'){
								$fprice = floatval(@$_SESSION['custom_fields']['featured_h']) + floatval(@$_SESSION['custom_fields']['featured_c']) ;
							}elseif($featured_type == 'c'){
								$fprice = floatval(floatval(@$_SESSION['custom_fields']['featured_c'])) ;
							}elseif($featured_type == 'h'){ 
								$fprice = floatval(@$_SESSION['custom_fields']['featured_h']) ;
							}else{
								$fprice =0;
							}
						}
						if($price_select !=''){
							$total_price = @$packprice+@$cat_price+@$fprice;
						}else{
							$total_price = $this->templ_fetch_category_price(@$_REQUEST['category']);
						}
					}elseif(isset($edit_id) && $edit_id !='' && @$_REQUEST['category'] =='' && !isset($_REQUEST['upgpkg']) && @$_REQUEST['upgpkg'] =='' && !isset($_REQUEST['renew'])){
						$cat_price = $this->templ_total_selected_cats_price($all_cat_id);
						$packprice = get_post_meta($price_select,'package_amount',true);/* package amount */
						
						$featured_type = get_post_meta($edit_id,'featured_type',true);
						$featured_h_price = get_post_meta($price_select,'feature_amount',true);
						$featured_c_price = get_post_meta($price_select,'feature_cat_amount',true);
						$total_price = get_post_meta($edit_id,'total_price',true);
						/* featured prices when comes for edit */
						if($featured_type =='both'){
							$fprice = floatval($featured_h_price + $featured_c_price) ;
						}elseif($featured_type == 'c'){
							$fprice = floatval($featured_c_price) ;
						}elseif($featured_type == 'h'){
							$fprice = floatval($featured_h_price) ;
						}else{
							$fprice =0;
						}
						if(get_post_meta($edit_id,'author_can_moderate_comment',true)){
							$fprice += get_post_meta($edit_id,'author_can_moderate_comment',true);
						}
					}elseif(isset($_SESSION['custom_fields'])){ /* selection when go back and edit */
				
						$cat_price = @$_SESSION['custom_fields']['all_cat_price'];
						$price_select = @$_SESSION['custom_fields']['package_select'];
						$packprice = get_post_meta(@$_SESSION['custom_fields']['package_select'],'package_amount',true);
						if(isset($_SESSION['custom_fields']['total_price'])){
							$total_price = @$_SESSION['custom_fields']['total_price'];
						}
						$featured_type = @$_SESSION['custom_fields']['featured_type'];
						
						if($featured_type =='both'){
							$fprice = floatval(@$_SESSION['custom_fields']['featured_h']) + floatval(@$_SESSION['custom_fields']['featured_c']) ;
						}elseif($featured_type == 'c'){
							$fprice = floatval(floatval(@$_SESSION['custom_fields']['featured_c'])) ;
						}elseif($featured_type == 'h'){ 
							$fprice = floatval(@$_SESSION['custom_fields']['featured_h']) ;
						}else{
							$fprice =0;
						}
						
						if($_SESSION['custom_fields']['author_can_moderate_comment'] >0 && @$_SESSION['custom_fields']['author_moderate'] == 1){
							$fprice += floatval(@$_SESSION['custom_fields']['author_can_moderate_comment']);
						}
						
					}else{
						$cat_price = 0;
						$price_select = '';
						$packprice = 0;
					}
					$currency = get_option('currency_symbol');
					$position = get_option('currency_pos');
					global  $wpdb;
					
					?><span id="before_cat_price_id" <?php if($cat_price <= 0){?> style="display:none;" <?php } ?>><?php if($position == '1'){ echo $currency; }else if($position == '2'){ echo $currency.'&nbsp;';}?></span>
					<span id="cat_price" <?php if($cat_price <= 0){?> style="display:none;" <?php } ?>>
					<?php if($catid != "") { $catprice = $wpdb->get_row("select * from $wpdb->term_taxonomy tt,  $wpdb->terms t where tt.term_taxonomy_id = '".$catid."' and tt.term_id = t.term_id"); if($catprice->term_price !=""){ echo $catprice->term_price; }else{ echo "0"; } }else{ if($cat_price !="") { echo $cat_price; }else{ echo '0'; } } ?></span>
					<span id="cat_price_id" <?php if($cat_price <= 0){?> style="display:none;" <?php } ?>><?php if($position == '3'){ echo $currency; }else if($position != 1 && $position != 2 && $position !=3){ echo '&nbsp;'.$currency; } ?>		</span>		
					
					<span id="pakg_add" <?php if($_SESSION['custom_fields']['all_cat_price'] <= 0  || $_SESSION['custom_fields']['all_cat_price'] == ''){?> style="display:none;" <?php } ?>><?php echo '+';?> 	</span>	
					
					<span id="before_pkg_price_id" <?php if($packprice <= 0){?> style="display:none;" <?php } ?>><?php if($position == '1'){ echo $currency; }else if($position == '2'){ echo $currency.'&nbsp;'; } ?></span>
					<span id="pkg_price" <?php if($packprice <= 0){?> style="display:none;" <?php } ?>><?php if(isset($price_select) && $price_select !=""){ echo $packprice; } else{ echo "0";}?></span>
					<span id="pkg_price_id" <?php if($packprice <= 0){?> style="display:none;" <?php } ?>><?php if($position == '3'){ echo $currency; }else if($position != 1 && $position != 2 && $position !=3){ echo '&nbsp;'.$currency; } ?>	</span>	
					
					<span id="pakg_price_add" <?php if($packprice <= 0 || $fprice <= 0){?> style="display:none;" <?php } ?>><?php echo '+'; ?> </span>	
					
					<span id="before_feture_price_id" <?php if($fprice <= 0){?> style="display:none;" <?php } ?> ><?php if($position == '1'){ echo $currency; }else if($position == '2'){ echo $currency.'&nbsp;'; } ?></span>
					<span id="feture_price"  <?php if($fprice <= 0){?> style="display:none;" <?php } ?>><?php if($fprice !=""){ echo $fprice ; }else{ echo "0"; }?></span>
					<span id="feture_price_id" <?php if($fprice <= 0){?> style="display:none;" <?php } ?>><?php if($position == '3'){ echo $currency; }else if($position != 1 && $position != 2 && $position !=3){ echo '&nbsp;'.$currency; } ?></span>	
					
					<span id="cat_price_total_price" <?php if(($fprice <= 0 || $fprice == '') && ($cat_price <= 0 || $cat_price == '') || ($fprice <= 0 || $fprice == '') && ($packprice <= 0 || $packprice == '') || ($packprice <= 0 || $packprice == '') && ($cat_price <= 0 || $cat_price == '')){?> style="display:none;" <?php } ?>><?php echo "<span id='result_price_equ'>=</span>"; ?>
					<?php if($position == '1'){ echo '<span id="currency_before_result_price">'.$currency.'</span>'; }else if($position == '2'){ echo '<span id="currency_before_space_result_price">'.$currency.'&nbsp;</span>'; } ?>
					<span id="result_price"><?php if($total_price != ""){ echo $total_price; }else if($catid != ""){  echo $catprice->term_price; }else{ echo "0";} ?></span>
					<?php if($position == '3'){ echo '<span id="currency_after_result_price">'.$currency.'</span>'; }else if($position != 1 && $position != 2 && $position !=3){ echo '<span id="currency_after_space_result_price">&nbsp;'.$currency."</span>"; } ?></span>
					
					<input type="hidden" name="total_price" id="total_price" value="<?php if($total_price != ""){ echo $total_price; }else if($catid != ""){  echo $catprice->term_price; }else{ echo "0";} ?>"/>
				</div>
				<span class="message_note"> </span>
				<span id="category_span" class="message_error2"></span>
			<!-- END - FETCH TOTAL PRICE -->
			</div>
	<?php
	}
	/* EOF - FETCH featured DATA */
	/*
	Name : templ_get_price_info
	Argument : pkg_id - selected price package id, total price for listing going to submit.
	Desc : return selected price package information.
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
			$info['price'] = get_post_meta($post->ID,'package_amount',true);
			$info['days'] = @$tvalidity;
			$info['alive_days'] = @$tvalidity;
			$info['cat'] = $cats;
			$info['is_featured'] = $is_featured;
			
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
	Name : templ_set_price_info
	Desc : set the price information of listing
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
	Name : templ_total_price
	Args : taxonomy name
	Desc : return the total price of selected categories
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
	Name : templ_get_featured_type
	Args : $cur_user_id = current user id
	Desc : return the user last post featured type
	*/	
	function templ_get_featured_type($cur_user_id , $post_type){
		global $wpdb;
		//package_select - package id of last post in database
		
		$user_last_post = $wpdb->get_row("select * from $wpdb->posts p where p.post_type='".$post_type."' and p.post_author = '".$cur_user_id."' order by p.ID DESC LIMIT 0,1");
		$user_last_post_id = @$user_last_post->ID; /* last inserted post */		
		$featured_type=get_post_meta($user_last_post_id,'featured_type',true);
		return $featured_type;
	}
	
	
	/*
	Name : templ_get_packagetype
	Args : $cur_user_id = current user id
	Desc : return the package type of current user
	*/	
	function templ_get_packagetype($cur_user_id , $post_type){
		global $wpdb;
		//package_select - package id of last post in database
		//fetch only user publish user last post
		$user_last_post = $wpdb->get_row("select * from $wpdb->posts p where p.post_type NOT IN('attachment','inherit','nav-menu','page','post') and p.post_author = '".$cur_user_id."' and post_status IN ('publish','trash') order by p.ID DESC LIMIT 0,1");
		$user_last_post_id = @$user_last_post->ID; /* last inserted post */
		
		$selected_pkg = get_post_meta($user_last_post_id,'package_select',true);/* selected package id to fetch package type*/
		
		$package_type = get_post_meta($selected_pkg,'package_type',true); /* 1- pay per post, 2- pay per subscription */
		if(!$package_type){ $package_type =1; }
		return $package_type;
	}	
	
	/*
	Name : templ_get_packagetype_last_postid
	Args : $cur_user_id = current user id
	Desc : return the last post id
	*/	
	function templ_get_packagetype_last_postid($cur_user_id , $post_type){
		global $wpdb;
		//package_select - package id of last post in database
		
		$user_last_post = $wpdb->get_row("select * from $wpdb->posts p where p.post_type NOT IN('attachment','inherit','nav-menu','page','post') and p.post_author = '".$cur_user_id."' and post_status IN ('publish','draft','trash') order by p.ID DESC LIMIT 0,1");
		$user_last_post_id = @$user_last_post->ID; /* last inserted post */
		
		return $user_last_post_id;
	}	
	
	
	/*
	Name : templ_get_packaget_post_status
	Args : $cur_user_id = current user id
	Desc : return the post status of current user
	*/	
	function templ_get_packaget_post_status($cur_user_id , $post_type,$package_id){
		global $wpdb;
		//package_select - package id of last post in database
		
		$user_last_post = $wpdb->get_row("select * from $wpdb->posts p where p.post_type NOT IN('attachment','inherit','nav-menu','page','post') and p.post_author = '".$cur_user_id."' and (p.post_status='publish' or p.post_status='draft') order by p.ID DESC LIMIT 0,1");
		if($user_last_post){
			$post_status = $user_last_post->post_status;
		}else{
			$post_status = fetch_posts_default_status();
		}
		return $post_status;
	}
	/*
	Name : templ_days_for_packagetype
	Args : $cur_user_id = current user id
	Desc : fetch the details of package type user selected when come to submit the listing
	*/
	function templ_days_for_packagetype($cur_user_id , $post_type){
		global $wpdb;		
		$package_type = $this->templ_get_packagetype($cur_user_id , $post_type); /* 1- pay per posy, 2- pay per subscription */
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
	Name : templ_days_for_user_packagetype
	Args : $cur_user_id = current user id
	Desc : fetch the details of package type user selected when come to submit the listing
	*/
	function templ_days_for_user_packagetype($cur_user_id , $post_type){
		global $wpdb;		
		$package_id = get_user_meta($cur_user_id ,'package_select',true); 
		$package_type = get_post_meta($package_id,'package_type',true);/* 1- pay per posy, 2- pay per subscription */		
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
	Name : is_user_have_alivedays
	Args : $cur_user_id = current user id
	Desc : fetch the details of package type user selected when come to submit the listing
	*/
	function is_user_have_alivedays($cur_user_id , $post_type){
		global $wpdb;
		
		$package_type = $this->templ_get_packagetype($cur_user_id , $post_type); /* 1- pay per posy, 2- pay per subscription */
		if($package_type == 2){
			if($cur_user_id){ 
			$adays = $wpdb->get_row("select * from $wpdb->posts p where p.post_type NOT IN('attachment','inherit','nav-menu','page','post') and p.post_author = '".$cur_user_id."' order by p.ID DESC LIMIT 0,1");
			if($adays->ID){
			$alive_day = get_post_meta($adays->ID,'alive_days',true);
			$publish_date =  strtotime($adays->post_date);
			$publish_date =  date('Y-m-d',$publish_date);
			$curdate = date('Y-m-d');
			
			$days = templ_number_of_days($publish_date,$curdate,30);
			//echo $alive_day."=".$days;
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
} /* class end */
}
if(!isset($monetization))
{
	$monetization = new monetization();
}
/*
NAme : recent_transactions_dashboard_widgets
Desc : admin dashboard transaction widgte setup
*/
function recent_transactions_dashboard_widgets() {
	global $current_user;
	if(is_super_admin($current_user->ID)) {
		wp_add_dashboard_widget('recent_transactions_dashboard_widgets', RECENT_TRANSACTION_TEXT, 'recent_transactions_dashboard_widget');
		
		global $wp_meta_boxes;
	
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	
		@$example_widget_backup = array('recent_transactions_dashboard_widgets' => $normal_dashboard['recent_transactions_dashboard_widgets']);
		unset($normal_dashboard['recent_transactions_dashboard_widgets']);
	
		$sorted_dashboard = array_merge($example_widget_backup, $normal_dashboard);
	
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}
}
add_action('wp_dashboard_setup', 'recent_transactions_dashboard_widgets' );
/*
NAme : recent_transactions_dashboard_widget
Desc : admin dashboard transaction widgte display
*/
function recent_transactions_dashboard_widget(){
	global $wpdb,$monetization;
	
	?>
<script type="text/javascript">
function change_poststatus(str)
{ 
	if (str=="")
	  {
	  document.getElementById("p_status_"+str).innerHTML="";
	  return;
	  }
	  if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }else{// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
		xmlhttp.onreadystatechange=function()
	  {
	    if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		document.getElementById("p_status_"+str).innerHTML=xmlhttp.responseText;
		}
	  }
	  url = "<?php echo plugin_dir_url( __FILE__ ); ?>ajax_update_status.php?post_id="+str;
	  xmlhttp.open("GET",url,true);
	  xmlhttp.send();
}
</script>
<?php
	$tmpdata = get_option('templatic_settings');
	if(isset($tmpdata['trans_post_type_value']) && count($tmpdata['trans_post_type_value']) > 0)
	{
		$post_args = array('post_status'=>'draft,publish','post_type' => $tmpdata['trans_post_type_value'],'order'=>'DESC','numberposts'=>get_option('posts_per_page'));
		$recent_posts = get_posts( $post_args );
		$no_alive_days = get_option('no_alive_days');
	if($recent_posts){
		
		$transactions = $wpdb->prefix."transactions";
		echo '<table class="widefat"  width="100%" >
			<thead>	';
		$th='	<tr>
				<th valign="top" align="left" style="width: 50%;">'.__('Transactions',DOMAIN).'</th>
				<th valign="top" align="left">'.__('With',DOMAIN).'</th>
				<th valign="top" align="left">'.__('Exp.',DOMAIN).'</th>
				<th valign="top" align="left">'.__('Status',DOMAIN).'</th>';
		$th .=	'</tr>';   
		echo $th;
		foreach($recent_posts as $posts) {			
			
			$color_taxonomy = 'trans_post_type_colour_'.$posts->post_type;			
			
			$featured_text = '';
			//Check for featured posts: start
			$featured_type = get_post_meta($posts->ID,'featured_type',true);
			if( 'h' == $featured_type ){
				$featured_text = '<div>'.__("Home",DOMAIN).'</div>';
			}elseif( 'c' == $featured_type ){
				$featured_text = '<div>'.__("Category",DOMAIN).'</div>';
			}elseif( 'both' == $featured_type ){
				$featured_text = '<div>'.__("Home, Category",DOMAIN).'</div>';
			}else{
				$featured_text = '';
			}
			
			$price_amount=(get_post_meta($posts->ID,'total_price',true)) ? fetch_currency_with_position(get_post_meta($posts->ID,'total_price',true)): fetch_currency_with_position('0');
		
			
			
			$sql="select * from $transactions where post_id=".$posts->ID." AND package_type is NULL";
			$tran_info = $wpdb->get_results($sql);
			
			$transaction_price_pkg = $monetization->templ_get_price_info($tran_info[0]->package_id,'');
			$publish_date =  date_i18n('Y-m-d',strtotime($tran_info[0]->payment_date));
			$alive_days = $transaction_price_pkg[0]['alive_days'];
			$expired_date = date_i18n(get_option("date_format"),strtotime($publish_date. "+$alive_days day"));
			
			if(isset($tmpdata[$color_taxonomy]) && $tmpdata[$color_taxonomy]!= '') { $color_taxonomy_value = $tmpdata[$color_taxonomy]; } 
			echo '<tr>
				<td valign="top" align="left" ><a href="'.admin_url().'admin.php?page=transcation&action=edit&trans_id='.$tran_info[0]->trans_id.'">'.$tran_info[0]->trans_id.'</a>&nbsp; <a href="'.site_url().'/wp-admin/post.php?post='.$posts->ID.'&action=edit">'.$posts->post_title.'</a>&nbsp;<div class="transaction_meta">'.__('On',ADMINDOMAIN)."&nbsp;".date_i18n(get_option("date_format"),strtotime($tran_info[0]->payment_date)).'&nbsp;'.__('with',ADMINDOMAIN)." ".get_the_title($tran_info[0]->package_id).'&nbsp;'.__('with amt.',ADMINDOMAIN).'<span style="color:green;">'.$price_amount.'</span></div></td>';
			//echo '<td valign="top" style="color:'.$color_taxonomy_value.'" align="left">'.ucfirst($posts->post_type).'</td>';	
			echo '<td valign="top" align="left">'.$tran_info[0]->payment_method.'</td>';	
			echo '<td valign="top" align="left">'.$expired_date.'</td>';
				if($no_alive_days !='1')
				{
					echo 	'<td valign="top" align="left">';
					if(get_post_meta($posts->ID,'alive_days',true)) { echo get_post_meta($posts->ID,'alive_days',true);} else { echo '0';} echo '</td>';
				}
			if(get_post_status($posts->ID) =='draft'){
			echo '<td valign="top" align="left" id="p_status_'.$posts->ID.'"><a href="javascript:void(0);" onclick="change_poststatus('.$posts->ID.')"  style="color:#E66F00">'.PENDING.'</a></td>';
			}else if(get_post_status($posts->ID) =='publish'){
			echo '<td valign="top" align="left" style="color:green" id="p_status_'.$posts->ID.'">'.APPROVED_TEXT.'</td>';
            }
			
			
			echo '</tr>';
			
			} 
		echo '</thead>	</table>';
		
		echo  '<p><a href="'.admin_url( 'admin.php?page=transcation' ).'">View More Transactions</a></p>';
		} else {
			echo __('No recent transaction available.',ADMINDOMAIN);
		}
	}
	else {
		echo '<p style="margin:0 0 10px">'.sprintf(__('No transaction type selected from  <a href="%s" >transaction settings</a>.',ADMINDOMAIN),admin_url( 'admin.php?page=transcation' )).'</p>';
	}
}
/* DELETE THE PACKAGE DATA */
if( (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete' && (isset($_REQUEST['package_id']) && $_REQUEST['package_id'] != '')))
{
	global $wpdb,$post;
	$id = $_REQUEST['package_id'];
	/* DELETING THE PACKAGE ON CLICK OF DELETE BUTTON OF DASHBOARD METABOX */
	delete_post_meta($id, 'package_type');
	delete_post_meta($id, 'package_post_type');
	delete_post_meta($id, 'category');
	delete_post_meta($id, 'show_package');
	delete_post_meta($id, 'package_amount');
	delete_post_meta($id, 'validity');
	delete_post_meta($id, 'validity_per');
	delete_post_meta($id, 'package_status');
	delete_post_meta($id, 'recurring');
	delete_post_meta($id, 'billing_num');
	delete_post_meta($id, 'billing_per');
	delete_post_meta($id, 'billing_cycle');
	delete_post_meta($id, 'is_featured');
	delete_post_meta($id, 'feature_amount');
	delete_post_meta($id, 'feature_cat_amount');
	wp_delete_post($id);
	$url = site_url().'/wp-admin/admin.php?page=monetization';
	echo '<form action="'.$url.'" method="get" id="frm_package" name="frm_package">
	<input type="hidden" value="monetization" name="page"><input type="hidden" value="delete" name="package_msg">
	<input type="hidden" value="packages" name="tab">
	</form>
	<script>document.frm_package.submit();</script>
	';exit;	
}
/* 
name : tmpl_get_transaction_status
description : FUNCTION TO FETCH TRANSACTIONS */
function tmpl_get_transaction_status($tid,$pid){
	global $wpdb,$transection_db_table_name;
	$transection_db_table_name = $wpdb->prefix.'transactions'; 
	$trans_status = $wpdb->get_var("select status from $transection_db_table_name t where trans_id = '".$tid."' order by t.trans_id DESC");
	$result = '';
	if($trans_status == 0){
		$result = '<a style="color:#E66F00; font-weight:normal;"  href="javascript:void(0);">'.__('Pending',DOMAIN).'</a>';
	}else if($trans_status == 1){
		$result = '<span style="color:green; font-weight:normal;">'.__('Approved',DOMAIN).'</span>';
	}
	else if($trans_status == 2){
		$result = '<span style="color:red; font-weight:normal;">'.__('Cancel',DOMAIN).'</span>';
	}
	return apply_filters('tmpl_get_transaction_status',$result,$tid,$pid);
}
//END OF FUNCTION
/*
name : fetch_payment_description
description : fetch payment option
*/
function fetch_payment_description($pid)
{
	global $wpdb;
	$transection_db_table_name = $wpdb->prefix.'transactions';
	$transsql_select = "select * from $transection_db_table_name where post_id = ". $pid." AND package_type is NULL ORDER BY trans_id DESC LIMIT 1";
	$transsql_result = $wpdb->get_row($transsql_select);
	$payment_options = get_option('payment_method_'.$transsql_result->payment_method);
	$payment_method_name = $payment_options['name'];
	if($transsql_result->status){
		$status = "Approved";
	}else{
		$status = "Pending";
	}
	echo "<li><p class='submit_info_label'>". __('Paid amount',DOMAIN) .": </p> <p class='submit_info_detail'> ".fetch_currency_with_position( @$transsql_result->payable_amt )."</p></li>";
	if($transsql_result->payment_method!="")
	{
		if(function_exists('icl_register_string')){
			icl_register_string(DOMAIN,$payment_method_name,$payment_method_name);
		}
		
		if(function_exists('icl_t')){
			$payment_method_name = icl_t(DOMAIN,$payment_method_name,$payment_method_name);
		}else{
			$payment_method_name = __($payment_method_name,DOMAIN); 
		}
		echo "<li><p class='submit_info_label'>". __('Payment Method',DOMAIN) .": </p> <p class='submit_info_detail'> ".@$payment_method_name."</p></li>";
	}
	if(function_exists('icl_register_string')){
			icl_register_string(DOMAIN,$status,$status);
		}
		
		if(function_exists('icl_t')){
			$status = icl_t(DOMAIN,$status,$status);
		}else{
			$status = __($status,DOMAIN); 
		}
	echo "<li><p class='submit_info_label'>". __('Status',DOMAIN) .": </p> <p class='submit_info_detail'> ".$status."</p></li>";
}
/*
name : insert_transaction_detail
description : insert transaction detail in transaction table.
*/
function insert_transaction_detail($paymentmethod='',$last_postid,$is_upgrade=0)
{
		/* Transaction Report */
		global $wpdb,$payable_amount;
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
		if($last_postid !=""){
		$post_author  = $wpdb->get_row("select * from $wpdb->posts where ID = '".$last_postid."'") ;
		$post_title  = $post_author->post_title ;
		$post_author  = $post_author->post_author ;
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
		if(is_admin()){
			$status =1;
		}else{
			$status =0;
		}
		$transection_db_table_name=$wpdb->prefix.'transactions';
		$transaction_insert = 'INSERT INTO '.$transection_db_table_name.' set 
			post_id="'.$last_postid.'",
			user_id = "'.$post_author.'",
			post_title ="'.$post_title.'",
			payment_method="'.$paymentmethod.'",
			payable_amt='.$payable_amount.',
			payment_date="'.date("Y-m-d H:i:s").'",
			paypal_transection_id="",
			status="'.$status.'",
			user_name="'.$user_fname.'",
			pay_email="'.$user_email.'",
			billing_name="'.$user_billing_name.'",
			billing_add="'.$billing_Address.'",
			package_id="'.$package_select.'"';		
		}			
		$wpdb->query($transaction_insert);
		return $wpdb->insert_id;
		/* End Transaction Report */
}
/*
name : get_payment_method
description : fetch payment method name.
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
/*
name : get_order_detailinfo_tableformat
description : fetch order information as a table format.
*/
function get_order_detailinfo_transaction_report($orderId,$isshow_paydetail=0)
{
	global $wpdb,$prd_db_table_name,$transection_db_table_name;
	$ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
	$orderinfo = $wpdb->get_results($ordersql);
	$orderinfo = $orderinfo[0];
	$post_id = $orderinfo->post_id;
	$package_select_id = get_post_meta($post_id,'package_select',true);
	$package_select_name = get_the_title($package_select_id);
	$coupon_code = get_post_meta($post_id,'coupon_code',true);	
	$alive_days = get_post_meta($post_id,'alive_days',true);	
	$message = '';
	if($isshow_paydetail)
	{
		
		$message .= '<style>.address_info {width:400px;}</style>';
	}
	$message .='
			
					<div class="order_info">
						<p> <span class="span"> '. __('Transaction ID',DOMAIN).' </span> : <span class="trans_strong">'.$orderinfo->trans_id.'  </span></p> 
						<p><span class="span"> '. __('Transaction Date',DOMAIN).' </span> : <span class="trans_strong">'.date_i18n(get_option('date_format').' '.get_option('time_format'),strtotime($orderinfo->payment_date)).'</span> </p>';
								if(!$alive_days)
								{
									$publishdate 	= get_post($post_id);
									$publish_date 	= strtotime($publishdate->post_date);
									$publish_date 	= date_i18n('Y-m-d',$publish_date);
									$expired_date 	= date_i18n(get_option("date_format"),strtotime($publish_date. "+$alive_days day"));
									$time_formate=get_option('time_format');
									$end_time = '';
									if(get_post_meta($post_id,'st_time',true))
									{
										$end_time = ' '.date($time_formate,strtotime(get_post_meta($post_id,'st_time',true)));
									}
									$message .='<div class="checkout_address" >
													<div class="address_info address_info2 fr">
														<p> <span class="span"> '. __('Expiry Date',DOMAIN).' </span> :  <span class="trans_strong">'.$expired_date.$end_time.'</span>  </p>
													</div>
												</div>';
								}
							$message .='<p><span class="span">'. __('Transaction Status',DOMAIN) .'</span>  : <span class="trans_strong">'. tmpl_get_transaction_status($orderinfo->trans_id,$orderinfo->post_id).'</span> </p>
					</div> <!--order_info -->
					<div class="checkout_address" >
						<div class="address_info address_info2 fr">
							<p> <span class="span"> '. __('Payment Method',DOMAIN).' </span> : <span class="trans_strong">'.get_payment_method($orderinfo->payment_method).'</span>  </p>
						</div>
					</div>
				';
				if($coupon_code)
				{
				$message .='<tr>
						<td align="left" valign="top" colspan="2">
							<div class="checkout_address" >
								<div class="address_info address_info2 fr">
									<h3> '. __('Coupon Code',DOMAIN).'  </h3>									
									<div class="address_row"><span class="trans_strong">'.$coupon_code.'</span>  </div>
								</div>
							</div><!-- checkout Address -->
						 </td>
					</tr>';
				}
				
			
		return $message;
}
/*
name : get_order_detailinfo_tableformat
description : fetch order information as a table format.
*/
function get_order_detailinfo_price_package($orderId,$isshow_paydetail=0)
{
	global $wpdb,$prd_db_table_name,$transection_db_table_name;
	$ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
	$orderinfo = $wpdb->get_results($ordersql);
	$orderinfo = $orderinfo[0];
	$post_id = $orderinfo->post_id;
	$package_select_id = get_post_meta($post_id,'package_select',true);
	$featured_c = get_post_meta($post_id,'featured_c',true);
	$featured_h = get_post_meta($post_id,'featured_h',true);
	$message = '';
	$recurring = get_post_meta($package_select_id,'recurring',true);
	$package_select_name = get_the_title($package_select_id);
	$message .='<div class="checkout_address" >
					<div class="address_info address_info2 fr">
						<p> <span class="span"> '. __('Package Type',DOMAIN).' </span> : <span class="trans_strong">'.$package_select_name.'</span>  </p>
					</div>
				</div>';
	if($recurring)
	{
		$message .='<div class="order_info">
						<p> <span class="span"> '. __('Recurring',DOMAIN).' </span> : <span class="trans_strong">'.__('Yes',DOMAIN).'</span> </p>
					</div>
					<div class="order_info">
						<p> <span class="span"> '. __('Recurring Price',DOMAIN).' </span> : <span class="trans_strong">'.fetch_currency_with_position(get_post_meta($post_id,'paid_amount',true)).'  </span></p>
					</div>
					';
	}
	if($featured_h == 'h')
	{
		$message .='<div class="order_info">
						<p> <span class="span"> '. __('Featured for home page',DOMAIN).' </span> : <img src="'.TEVOLUTION_PAGE_TEMPLATES_URL.'tmplconnector/monetize/images/icon-yes.png" /> </p>
					</div>';
	}
	if($featured_c == 'c')
	{
		$message .='<div class="order_info">
						<p> <span class="span"> '. __('Featured for category page',DOMAIN).' </span> : <img src="'.TEVOLUTION_PAGE_TEMPLATES_URL.'tmplconnector/monetize/images/icon-yes.png" /> </p>
					</div>';
	}
	if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php'))
	{
		if(get_post_meta($post_id,'author_moderate',true) == 1)
		{
			$message .='<div class="order_info">
						<p> <span class="span"> '. __('User caon moderate comment',DOMAIN).' </span> : <img src="'.TEVOLUTION_PAGE_TEMPLATES_URL.'tmplconnector/monetize/images/icon-yes.png" /> </p>
					</div>';
		}
	}
	return $message;
}
/*
name : get_order_detailinfo_tableformat
description : fetch order information as a table format.
*/
function get_order_detailinfo_tableformat($orderId,$isshow_paydetail=0)
{
	global $wpdb,$prd_db_table_name,$transection_db_table_name;
	$ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
	$orderinfo = $wpdb->get_results($ordersql);
	$orderinfo = $orderinfo[0];
	$post_id = $orderinfo->post_id;
	$post_type = get_post($post_id);
	$package_select_id = get_post_meta($post_id,'package_select',true);
	$package_select_name = get_the_title($package_select_id);
	$coupon_code = get_post_meta($post_id,'coupon_code',true);	
	$message = '';
	if($isshow_paydetail)
	{
		
		$message .= '<style>.address_info {width:400px;}</style>';
	}
	
			
		$message .='<table width="100%" class="table widefat post" >
		<thead>
						<tr>
						  <th width="5%" align="left" class="title" > '. __('Image',DOMAIN).'</th>
						  <th width="30%" align="left" class="title" >'. sprintf(__('%s Name',DOMAIN),ucfirst($post_type->post_type)).'</th>
						   <th width="25%" align="left" class="title" > '. __('Submitted by',DOMAIN).'</th>
						    <th width="25%" align="left" class="title" > '. __('Payment Method',DOMAIN).'</th>
						  <th width="15%" align="left" class="title" >'. __('Total Price',DOMAIN).'</th>
						</tr></thead>';
			 
					$product_image_arr = bdw_get_images_plugin($orderinfo->post_id,'thumb');
					$product_image = @$product_image_arr[0]['file'];
					if(!$product_image ){
						$product_image  = TEVOLUTION_PAGE_TEMPLATES_URL ."tmplconnector/monetize/images/no-image.png";
					}
					$message .= '<tr class="alternate">
									<td class="row1"><a href="'.get_permalink($orderinfo->post_id).'"><img src="'.$product_image.'" width=60 height=60 /></a></td>
									<td class="row1" ><a href="'.get_permalink($orderinfo->post_id).'">'.$orderinfo->post_title.'</a></td>
									<td class="row1 tprice"  align="left">'.$orderinfo->user_name.'</td>
									<td class="row1 tprice"  align="left">'.$orderinfo->payment_method.'</td>
									<td class="row1 tprice"  align="left">'.fetch_currency_with_position($orderinfo->payable_amt,2).'</td>
								</tr>';
	$message .='</table>';
	return $message;
}
function get_order_user_info($orderId,$isshow_paydetail=0)
{
	global $wpdb,$prd_db_table_name,$transection_db_table_name;
	$ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
	$orderinfo = $wpdb->get_results($ordersql);
	$orderinfo = $orderinfo[0];
	$post_id = $orderinfo->post_id;
	$message = '';
	$message .='
				<div class="trans_avatar">
					<div class="order_info">
						<p> <span class="span"> '. get_avatar($orderinfo->pay_email, 75 ).'  </p>
					</div>
				</div>
				<div class="trans_user_info">
					<div class="order_info">
						<p> <span class="span"> '. USERNAME_TEXT.' </span> : <span class="trans_strong">'.$orderinfo->user_name.'</span> </p>
					</div>
					<div class="order_info">
						<p> <span class="span"> '. __('User Email',DOMAIN).' </span> : <span class="trans_strong">'.$orderinfo->pay_email.'</span> </p>
					</div>
				</div>';
	return $message;
}
add_action('admin_init','post_price_package');
function post_price_package($post){
	global $post,$post_type,$post_id;
	if(!$post && isset($_GET['post']) && $_GET['post']!=''){ $post = get_post($_GET['post']); }
	
	if($post){
		$post_type = $post->post_type;
		$post_id = $post->ID; }
	$package_select=get_post_meta(@$post_id,'package_select',true);
	if($package_select!='' && $package_select!=0 && $post_type!='page'){
		add_meta_box("package_details", "Package Details", "price_package_meta_box",$post_type, "side", "high");
	}
}

function price_package_meta_box(){
	global $post;
	$package_id=get_post_meta($post->ID,'package_select',true);
	$alive_days=get_post_meta($post->ID,'alive_days',true);
	$featured_c=(get_post_meta($post->ID,'featured_c',true)=='c')? ''.__('Yes',DOMAIN) : ''.__('No',DOMAIN);	
	$featured_h=(get_post_meta($post->ID,'featured_h',true)=='h')? ''.__('Yes',DOMAIN) : ''.__('No',DOMAIN);	
	if(function_exists('fetch_currency_with_position'))
	{
		$paid_amount=fetch_currency_with_position(get_post_meta($post->ID,'paid_amount',true));
	}
	
	$package_name=get_the_title($package_id);
	?>
     <p><label><?php echo __('Package Name: ',ADMINDOMAIN);?></label><strong><?php echo $package_name;?></strong></p>
     <p><label><?php echo __('Total Amount: ',ADMINDOMAIN);?></label><strong><?php echo $paid_amount;?></strong></p>
     <p><label><?php echo __('Alive Days: ',ADMINDOMAIN);?></label><strong><?php echo $alive_days;?></strong></p>
     <p><label><?php echo __('Featured for home page? : ',ADMINDOMAIN);?></label><strong><?php echo $featured_h;?></strong></p>
     <p><label><?php echo __('Featured for category page? : ',ADMINDOMAIN);?></label><strong><?php echo $featured_c;?></strong></p>
    <?php
}
/*
 * Function Name: tevolution_price_package_order
 * Return: sortordering of price package
 */
add_action('wp_ajax_price_package_order','tevolution_price_package_order');
function tevolution_price_package_order(){
	
	$user_id = get_current_user_id();	
	if(isset($_REQUEST['paging_input']) && @$_REQUEST['paging_input']!=0 && @$_REQUEST['paging_input']!=1){
		$package_per_page=get_user_meta($user_id,'package_per_page',true);
		$j = @$_REQUEST['paging_input']*$package_per_page+1;
		$test='';
		$i=$custom_fields_per_page;		
		for($j; $j >= count($_REQUEST['price_package_order']);$j--){
			if($_REQUEST['price_package_order'][$i]!=''){				
				wp_update_post(array('ID'=> @$_REQUEST['price_package_order'][$i],'menu_order'	=> $j,));
			}
			$i--;	
		}
	}else{
		$j=1;
		for($i=0;$i<count($_REQUEST['price_package_order']);$i++){
			wp_update_post(array('ID'=> @$_REQUEST['price_package_order'][$i],'menu_order'	=> $j,));
			$j++;
		}
	}
	exit;
}
/*
 * Function Name: get_transaction_detail
 * Return: get the transcation id 
 */
function get_transaction_detail($payment,$post_id){
	global $wpdb,$transection_db_table_name;
	$transection_db_table_name=$wpdb->prefix.'transactions';
	$trans_id = $wpdb->get_var("select trans_id from $transection_db_table_name where post_id = '".$post_id."' AND package_type is NULL");
	return $trans_id;
}
?>
