<?php
/* Custom fields function - Templatic custom fields functions */
/*
	Name: validation_type_cmb_plugin
	Desc: Return Validation type on manage/Add custom fields form
*/
function validation_type_cmb_plugin($validation_type = ''){
	$validation_type_display = '';
	$validation_type_array = array(" "=>__("Select validation type",DOMAIN),"require"=>__("Require",DOMAIN),"phone_no"=>__("Phone No.",DOMAIN),"digit"=>__("Digit",DOMAIN),"email"=>__("Email",DOMAIN));
	foreach($validation_type_array as $validationkey => $validationvalue){
		if($validation_type == $validationkey){
			$vselected = 'selected';
		} else {
			$vselected = '';
		}
		$validation_type_display .= '<option value="'.$validationkey.'" '.$vselected.'>'.__($validationvalue,DOMAIN).'</option>';
	}
	return $validation_type_display;
}
/*
Name : templ_number_of_days
desc : difference between two date date must be in Y-m-d format
*/
function templ_number_of_days($date1, $date2,$adays =30) {
	$date1Array = explode('-', $date1);
	$date1Epoch = mktime(0, 0, 0, $date1Array[1],
	$date1Array[2], $date1Array[0]);
	$date2Array = explode('-', $date2);
	$date2Epoch = mktime(0, 0, 0, $date2Array[1],
	$date2Array[2], $date2Array[0]);
	
	if(date('Y-m-d',$date1Epoch) == date('Y-m-d',$date2Epoch)){
		$date_diff =$adays;
		return $date_diff;
	}else{
		$date_diff = $date2Epoch - $date1Epoch;
		return round($date_diff / 60 / 60 / 24);
	}
	
}
/*
Name : templ_get_parent_categories
Args : pass the taxonomy
desc : return the array of categories
*/
function templ_get_parent_categories($taxonomy) {
	$cat_args = array(
	'taxonomy'=>$taxonomy,
	'orderby' => 'name', 				
	'hierarchical' => 'true',
	'parent'=>0,
	'hide_empty' => 0,	
	'title_li'=>'');				
	$categories = get_categories( $cat_args );	/* fetch parent categories */
	return $categories;
}
/*
Name : templ_get_child_categories
Args : pass the taxonomy, parent id
desc : return the array of child categories
*/
function templ_get_child_categories($taxonomy,$parent_id) {
	$args = array('child_of'=> $parent_id,'hide_empty'=> 0,'taxonomy'=>$taxonomy);                        
	$child_cats = get_categories( $args );	/* get child cats */
	return $child_cats;
}
function custom_field_posts_where_filter($join)
{
	global $wpdb, $pagenow, $wp_taxonomies,$ljoin;
	$language_where='';
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		$language = ICL_LANGUAGE_CODE;
		$join .= " {$ljoin} JOIN {$wpdb->prefix}icl_translations t ON {$wpdb->posts}.ID = t.element_id			
			AND t.element_type IN ('post_custom_fields') JOIN {$wpdb->prefix}icl_languages l ON t.language_code=l.code AND l.active=1 AND t.language_code='".$language."'";
	}	
	return $join;
}
/* 
Name :get_post_custom_fields_templ_plugin
description : Returns all custom fields
*/
function get_post_custom_fields_templ_plugin($post_types,$category_id='',$taxonomy='',$heading_type='',$remove_post_id='') {
	global $wpdb,$post,$_wp_additional_image_sizes,$sitepress;
	if(@$_REQUEST['page'] != 'paynow'  && @$_REQUEST['page'] != 'transcation')
	{
		$category_id = explode(",",$category_id);
	}
	
 	$tmpdata = get_option('templatic_settings');
	remove_all_actions('posts_where');
	$remove_post_id_array = explode(",",$remove_post_id);
	if($tmpdata['templatic-category_custom_fields'] == 'No')
	{
		if($heading_type)
		  {
			$args=
			array( 
			'post_type' => 'custom_fields',
			'posts_per_page' => -1	,
			'post_status' => array('publish'),
			'post__not_in' => $remove_post_id_array,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'post_type_'.$post_types.'',
					'value' => array('all',$post_types),
					'compare' => 'IN',
					'type'=> 'text'
				),
				array(
					'key' => 'post_type',
					'value' => $post_types,
					'compare' => 'LIKE',
					'type'=> 'text'
				),
				array(
					'key' => 'show_on_page',
					'value' =>  array('user_side','both_side'),
					'compare' => 'IN',
					'type'=> 'text'
				),
				
				array(
					'key' => 'is_active',
					'value' =>  '1',
					'compare' => '='
				),
				array(
					'key' => 'heading_type',
					'value' =>  array('basic_inf',$heading_type),
					'compare' => 'IN'
				)
	
			),		 
			'meta_key' => 'sort_order',
			'orderby' => 'meta_value_num',
			'meta_value_num'=>'sort_order',
			'order' => 'ASC'
			);
		  }
		 else
		  {
			$args=
			array( 
			'post_type' => 'custom_fields',
			'posts_per_page' => -1	,
			'post_status' => array('publish'),
			'post__not_in' => $remove_post_id_array,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'post_type_'.$post_types.'',
					'value' => array('all',$post_types),
					'compare' => 'In',
					'type'=> 'text'
				),
				array(
					'key' => 'show_on_page',
					'value' =>  array('user_side','both_side'),
					'compare' => 'IN',
					'type'=> 'text'
				),
				
				array(
					'key' => 'is_active',
					'value' =>  '1',
					'compare' => '='
				)),
			'meta_key' => 'sort_order',
			'orderby' => 'meta_value_num',
			'meta_value_num'=>'sort_order',
			'order' => 'ASC'
			);
		  }
	}
	else
	{
		if($heading_type)
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
					'value' => array('all',$post_types),
					'compare' => 'In',
					'type'=> 'text'
				),
				array(
					'key' => 'post_type',
					'value' => $post_types,
					'compare' => 'LIKE',
					'type'=> 'text'
				),
				array(
					'key' => 'show_on_page',
					'value' =>  array('user_side','both_side'),
					'compare' => 'IN',
					'type'=> 'text'
				),
				
				array(
					'key' => 'is_active',
					'value' =>  '1',
					'compare' => '='
				),
				array(
					'key' => 'heading_type',
					'value' =>  $heading_type,
					'compare' => '='
				)
	
			),
			'tax_query' => array(
					'relation' => 'OR',
				array(
					'taxonomy' => $taxonomy,
					'field' => 'id',
					'terms' => $category_id,
					'operator'  => 'IN'
				),
				array(
					'taxonomy' => 'category',
					'field' => 'id',
					'terms' => 1,
					'operator'  => 'IN'
				)
				
			 ),
			 
			'meta_key' => 'sort_order',
			'orderby' => 'meta_value_num',
			'meta_value_num'=>'sort_order',
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
					'value' => array('all',$post_types),
					'compare' => 'In',
					'type'=> 'text'
				),
				array(
					'key' => 'show_on_page',
					'value' =>  array('user_side','both_side'),
					'compare' => 'IN',
					'type'=> 'text'
				),
				
				array(
					'key' => 'is_active',
					'value' =>  '1',
					'compare' => '='
				)
			),
			'tax_query' => array(
					'relation' => 'OR',
				array(
					'taxonomy' => $taxonomy,
					'field' => 'id',
					'terms' => $category_id,
					'operator'  => 'IN'
				),
				array(
					'taxonomy' => 'category',
					'field' => 'id',
					'terms' => 1,
					'operator'  => 'IN'
				)
				
			 ),
			 
			'meta_key' => 'sort_order',
			'orderby' => 'meta_value_num',
			'meta_value_num'=>'sort_order',
			'order' => 'ASC'
			);
	  }
	}
	$post_query = null;
	remove_all_actions('posts_orderby');	
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$post_query = new WP_Query($args);		
	$post_meta_info = $post_query;	
	$return_arr = array();
	
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			if(get_post_meta($post->ID,"ctype",true)){
				$options = explode(',',get_post_meta($post->ID,"option_values",true));
			}
			$custom_fields = array(
					"id"		=> $post->ID,
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
					"field_require_desc"  => get_post_meta($post->ID,"field_require_desc",true),
					"style_class"  => get_post_meta($post->ID,"style_class",true),
					"extra_parameter"  => get_post_meta($post->ID,"extra_parameter",true),
					"show_in_email" =>get_post_meta($post->ID,"show_in_email",true),
					"range_min" =>get_post_meta($post->ID,"range_min",true),
					"range_max" =>get_post_meta($post->ID,"range_max",true),
					"search_ctype" =>get_post_meta($post->ID,"search_ctype",true),
					);
			if($options)
			{
				$custom_fields["options"]=$options;
			}
			$return_arr[get_post_meta($post->ID,"htmlvar_name",true)] = $custom_fields;
		endwhile;wp_reset_query();
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	return $return_arr;
}
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
					'key' => 'heading_type',
					'value' =>  $heading_type,
					'compare' => '='
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
		
		'meta_key' => 'sort_order',
		'orderby' => 'meta_value_num',
		'meta_value_num'=>'sort_order',
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
Name :get_post_fields_templ_plugin
description : Returns all default custom fields
*/
function get_post_fields_templ_plugin($post_types,$category_id='',$taxonomy='') {
	global $wpdb,$post;
	remove_all_actions('posts_where');
	$tmpdata = get_option('templatic_settings');
	if($tmpdata['templatic-category_custom_fields'] == 'Yes'){
		$args=
		array( 
		'post_type' => 'custom_fields',
		'posts_per_page' => -1	,
		'post_status' => array('publish'),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'post_type_'.$post_types.'',
				'value' => array($post_types,'all'),
				'compare' => 'IN',
				'type'=> 'text'
			),
			array(
				'key' => 'show_on_page',
				'value' =>  array('user_side','both_side'),
				'compare' => 'IN',
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
				'value' => array($post_types,'all'),
				'compare' => 'IN',
				'type'=> 'text'
			),
			array(
				'key' => 'show_on_page',
				'value' =>  array('user_side','both_side'),
				'compare' => 'IN',
				'type'=> 'text'
			),
			
			array(
				'key' => 'is_active',
				'value' =>  '1',
				'compare' => '='
			)
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'category',
				'field' => 'id',
				'terms' => 1,
				'operator'  => 'IN'
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
					"desc"      =>  $post->post_content,
					"option_values" => get_post_meta($post->ID,"option_values",true),
					"is_require"  => get_post_meta($post->ID,"is_require",true),
					"is_active"  => get_post_meta($post->ID,"is_active",true),
					"show_on_listing"  => get_post_meta($post->ID,"show_on_listing",true),
					"show_on_detail"  => get_post_meta($post->ID,"show_on_detail",true),
					"validation_type"  => get_post_meta($post->ID,"validation_type",true),
					"style_class"  => get_post_meta($post->ID,"style_class",true),
					"extra_parameter"  => get_post_meta($post->ID,"extra_parameter",true),
					"show_in_email" =>get_post_meta($post->ID,"show_in_email",true),
					"heading_type" => get_post_meta($post->ID,"heading_type",true),
					);
			if($options)
			{
				$custom_fields["options"]=$options;
			}
			$return_arr[get_post_meta($post->ID,"htmlvar_name",true)] = $custom_fields;
		endwhile;
	}
	return $return_arr;
}
/* 
Name :get_search_post_fields_templ_plugin
description : Returns all default custom fields
*/
function get_search_post_fields_templ_plugin($post_types,$category_id='',$taxonomy='') {
	global $wpdb,$post,$sitepress;
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
		remove_all_actions('posts_where');
		$args=
		array( 
		'post_type' => 'custom_fields',
		'posts_per_page' => -1	,
		'post_status' => array('publish'),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'post_type_'.$post_types,
				'value' => array('all',$post_types),
				'compare' => 'In',
				'type'=> 'text'
			),
			array(
				'key' => 'is_search',
				'value' =>  '1',
				'compare' => '='
			),
			
			array(
				'key' => 'is_active',
				'value' =>  '1',
				'compare' => '='
			)
		),		 
		
		);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	add_filter('posts_orderby', 'search_custom_field_posts_orderby_filter');
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
	remove_filter('posts_orderby', 'search_custom_field_posts_orderby_filter');
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		add_filter('posts_where', array($sitepress,'posts_where_filter'));	
	}
	return $return_arr;
}
/* 
Name :display_search_custom_post_field_plugin
description : Returns all search custom fields html
*/
function display_search_custom_post_field_plugin($custom_metaboxes,$session_variable,$post_type,$show_label=0){
			
		foreach($custom_metaboxes as $key=>$val) {
			$name = $val['name'];
			$site_title = $val['label'];
			$type = $val['type'];
			$htmlvar_name = $val['htmlvar_name'];
			$admin_desc = $val['desc'];
			$option_values = $val['option_values'];
			$default_value = $val['default'];
			$style_class = $val['style_class'];
			$extra_parameter = $val['extra_parameter'];
			if(!$extra_parameter){ $extra_parameter ='';}
			/* Is required CHECK BOF */
			$is_required = '';
			$input_type = '';
			
			/* Is required CHECK EOF */
			$value = "";
			if(@$_REQUEST['pid'])
			{
				$post_info = get_post($_REQUEST['pid']);
				if($name == 'post_title') {
					$value = $post_info->post_title;
				}
				elseif($name == 'post_content') {
					$value = $post_info->post_content;
				}
				elseif($name == 'post_excerpt'){
					$value = $post_info->post_excerpt;
				}
				else {
					$value = get_post_meta($_REQUEST['pid'], $name,true);
				}
			
			}
			if(@$_SESSION[$session_variable] && @$_REQUEST['backandedit'])
			{
				$value = @$_SESSION[$session_variable][$name];
			}
			$radio_type = '';
			if($type == 'radio')
				$radio_type = '_radio';
		?>
        <input type="hidden" name="search_custom[<?php echo $name.$radio_type;?>]" value="<?php echo $type;?>"  />
		<?php 
		/* Don't show post title on advance search because search field is already there */
		if($name !='post_title'){ 
			if($type=='text' || ($type=='range_type' && $val['range_min']=='' && $val['range_max']=='')){

			if($name == 'geo_latitude' || $name == 'geo_longitude') {
				$extra_script = 'onblur="changeMap();"';
				
			} else {
				$extra_script = '';
				
			}?>
			<div class="form_row clearfix">
				<?php if($show_label==1){ ?>
					<label class="r_lbl"><?php echo $site_title; ?></label>
				<?php } ?>
				<input PLACEHOLDER="<?php echo $site_title; ?>" name="<?php echo $name;?>" id="<?php echo $name;?>" value="<?php if(isset($value))echo $value;?>" type="text" class="textfield <?php echo $style_class;?>" <?php echo $extra_parameter; ?> <?php echo $extra_script;?> PLACEHOLDER="<?php echo $val['default']; ?> "/>
			 	<span class="message_note msgcat submit"><?php echo $admin_desc;?></span>
			</div>
			<?php
			}elseif($type=='date'){
				//jquery data picker
			?>     
				<script type="text/javascript">
					jQuery(function(){
						var pickerOpts = {						
							showOn: "both",
							dateFormat: 'yy-mm-dd',
							//buttonImage: "<?php echo TEMPL_PLUGIN_URL;?>css/datepicker/images/cal.png",
							buttonText: '<i class="fa fa-calendar"></i>',
							monthNames: objectL11tmpl.monthNames,
							monthNamesShort: objectL11tmpl.monthNamesShort,
							dayNames: objectL11tmpl.dayNames,
							dayNamesShort: objectL11tmpl.dayNamesShort,
							dayNamesMin: objectL11tmpl.dayNamesMin,
							isRTL: objectL11tmpl.isRTL,
						};	
						jQuery("#tmpl_search_<?php echo $name;?>").datepicker(pickerOpts);
					});
				</script>
				<div class="form_row clearfix">
					<input PLACEHOLDER="<?php echo $site_title; ?>" type="text" name="<?php echo $name;?>" id="tmpl_search_<?php echo $name;?>" class="textfield <?php echo $style_class;?>" value="<?php echo esc_attr(stripslashes($value)); ?>" size="25" <?php echo 	$extra_parameter;?> />
				</div>				
			<?php
			}
			elseif($type=='texteditor'){	?>
				<div class="form_row clearfix">
				<?php if($show_label ==1){ ?>
					<label class="r_lbl"><?php echo $site_title; ?></label>
				<?php } 
					// default settings
					$settings =   array(
						'wpautop' => false, // use wpautop?
						'media_buttons' => false, // show insert/upload button(s)
						'textarea_name' => $name, // set the textarea name to something different, square brackets [] can be used here
						'textarea_rows' => '10', // rows="..."
						'tabindex' => '',
						'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
						'editor_class' => '', // add extra class(es) to the editor textarea
						'teeny' => false, // output the minimal editor config used in Press This
						'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
						'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
						'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
					);				
					if(isset($value) && $value != '') 
					{  $content=$value; }
					else{$content= $val['default']; } 				
					wp_editor( $content, $name, $settings);
				?>
				</div>
			<?php
			}elseif($type=='textarea'){ 
			?>		
				<div class="form_row clearfix">
					<textarea PLACEHOLDER="<?php echo $site_title; ?>" name="<?php echo $name;?>" id="<?php echo $name;?>" class="<?php if($style_class != '') { echo $style_class;}?> textarea" <?php echo $extra_parameter;?> placeholder="<?php echo @$val['default']; ?>"><?php if(isset($value))echo $value;?></textarea> 
				</div>
			<?php
			}elseif($type=='radio'){
			
				if($name != 'position_filled' || @$_REQUEST['pid']): ?>
				<div class="form_row clearfix">
				<?php if($show_label==1){ ?>
					<label class="r_lbl"><?php echo $site_title; ?></label>
				<?php } 			
				$options = $val['option_values'];				
				$option_titles = $val['option_title'];	
				 if($val['option_title'][0]==''){
					 $option_titles = explode(',',$val['option_values']);
				 }
				if($options)
				{  $chkcounter = 0;
					echo '<div class="form_cat_left">';
					echo '<ul class="hr_input_radio">';
					$option_values_arr = explode(',',$options);									
					for($i=0;$i<count($option_values_arr);$i++)
					{
						 if($option_values_arr[$i]=='') continue;
						$chkcounter++;
						$seled='';
						if($default_value == $option_values_arr[$i]){ $seled='checked="checked"';}
						if (isset($value) && trim($value) == trim($option_values_arr[$i])){ $seled='checked="checked"';}
						echo '<li>
							<label class="r_lbl">
								<input name="'.$key.'_radio"  id="'.$key.'_'.$chkcounter.'" type="radio" value="'.$option_values_arr[$i].'" '.$seled.'  '.$extra_parameter.' /> '.$option_titles[$i].'
							</label>
						</li>';
					}
					echo '</ul></div>';
				}
				?>
				</div>
			<?php
				endif;
			}elseif($type=='multicheckbox'){
				
				if($name != 'position_filled' || @$_REQUEST['pid']): ?>
					<div class="form_row clearfix">
                    <?php if($show_label==1){ ?>
						<label class="r_lbl"><?php echo $site_title; ?></label>
					<?php } 
                         $options = $val['option_values'];
					     $option_titles = $val['option_title'];
						 if($val['option_title'][0]==''){
							 $option_titles = explode(',',$val['option_values']);
						 }
                         if($options)
                         {  $chkcounter = 0;
                              echo '<div class="form_cat_left hr_input_multicheckbox">';
                              $option_values_arr = explode(',',$options);
                              for($i=0;$i<count($option_values_arr);$i++)
                              {
								  if($option_values_arr[$i]=='') continue;
                                   $chkcounter++;
                                   $seled='';
                                   if($default_value == $option_values_arr[$i]){ $seled='checked="checked"';}
                                   if (isset($value) && trim($value) == trim($option_values_arr[$i])){ $seled='checked="checked"';}
                                   echo '<div class="form_cat">
                                        <label class="r_lbl">
                                             <input name="'.$key.'[]"  id="'.$key.'_'.$chkcounter.'" type="checkbox" value="'.$option_values_arr[$i].'" '.$seled.'  '.$extra_parameter.' /> '.$option_titles[$i].'
                                        </label>
                                   </div>';
                              }
                              echo '</div>';
                         } ?>
					</div>
					<?php endif;	
			}elseif($type=='select'){
			?>	<div class="form_row clearfix">	
				<?php if($show_label==1){ ?>
					<label class="r_lbl"><?php echo $site_title; ?></label>
				<?php } ?>
				<select name="<?php echo $name;?>" id="<?php echo $name;?>" class="textfield textfield_x <?php echo $style_class;?>" <?php echo $extra_parameter;?>>
				<option value=""><?php echo sprintf(__('Please Select %s',DOMAIN),$site_title);?></option>
				<?php if($option_values){
					$option_values_arr = explode(',',$option_values);
					$option_titles = $val['option_title'];					
					for($i=0;$i<count($option_values_arr);$i++)
					{
					?>
					<option value="<?php echo $option_values_arr[$i]; ?>" <?php if($value==$option_values_arr[$i]){ echo 'selected="selected"';} else if($default_value==$option_values_arr[$i]){ echo 'selected="selected"';}?>><?php echo ($option_titles[$i])? $option_titles[$i]:$option_values_arr[$i]; ?></option>
					<?php	
					}
				 }?>
			   
				</select>
				</div>
			<?php
			}
			elseif(!isset($_REQUEST['action']) && $type=='post_categories' && @$tmpdata['templatic-category_custom_fields'] == 'No')
				{
				/* fetch catgories on action */ ?>
				<div class="form_row clearfix">
					<?php if($show_label==1){ ?>
						<label class="r_lbl"><?php echo $site_title; ?></label>
					<?php } ?>
					<div class="category_label"><?php include (TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/category.php');?></div>
					<?php echo $is_required_msg;
					if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php else:
							$PostTypeObject = get_post_type_object($post_type);
							$_PostTypeName = $PostTypeObject->labels->name;?>
							 <span class="message_note msgcat"><?php _e('Select at least one category for your ',DOMAIN); echo $_PostTypeName; ?></span>
					<?php endif;?>
					
				 </div>    
				<?php }
			else if($type=='upload'){  ?>
					<div class="form_row clearfix">
             	    <?php if($show_label==1){ ?>
						<label class="r_lbl"><?php echo $site_title; ?></label>
					<?php } ?>
                     <input type="file" value="<?php echo $_SESSION['upload_file']; ?>" name="<?php echo $name; ?>" class="fileupload uploadfilebutton" id="<?php echo $name; ?>" />
					 <?php if($_REQUEST['pid']): ?>
						<p class="resumback"><a href="<?php echo get_post_meta($_REQUEST['pid'],$name, $single = true); ?>"><?php echo basename(get_post_meta($_REQUEST['pid'],$name, $single = true)); ?></a></p>
					 <?php elseif($_SESSION['upload_file']): ?>
						<p class="resumback"><a href="<?php echo $_SESSION['upload_file'][$name]; ?>"><?php echo basename($_SESSION['upload_file'][$name]); ?></a></p>
					 <?php endif; ?>
					 </div>
			<?php }elseif($type=='min_max_range' ){ ?>                
                <input type="text" name="<?php echo $name.'_min';?>" id="<?php echo $name;?>_min" value="" placeholder="<?php echo $site_title.' ';_e('Min value',DOMAIN);?>" class="min_range"/>
                <input type="text" name="<?php echo $name.'_max';?>" id="<?php echo $name;?>_max" value="" placeholder="<?php echo $site_title.' ';_e('Max value',DOMAIN);?>" class="max_range"/>
                
                <?php
			}elseif($type=='slider_range' && $val['range_min']!='' && $val['range_max']!=''){
				$min_range=$val['range_min'];
				$max_range=$val['range_max'];
				
				wp_enqueue_script("jquery-ui-slider");	
				?>
				<div class="form_row clearfix">
               <?php if($show_label==1){ ?>
					<label class="r_lbl"><?php echo $site_title; ?></label>
				<?php } ?>
                <input type="text" name="<?php echo $name;?>" id="<?php echo $name;?>_range" value="<?php echo $max_range; ?>" style="border:0; font-weight:bold;"  readonly="readonly"/>
                </label>
                <div id="<?php echo $name;?>_range_type" class="clearfix" style="width:90%;"></div>
                <script type="text/javascript">
				jQuery(function(){jQuery("#<?php echo $name?>_range_type").slider({range:true,min:<?php echo $min_range;?>,max:<?php echo $max_range; ?>,values:[<?php echo $min_range;?>,<?php echo $max_range; ?>],slide:function(e,t){jQuery("#<?php echo $name;?>_range").val(t.values[0]+" - "+t.values[1])}});jQuery("#<?php echo $name;?>_range").val(jQuery("#<?php echo $name?>_range_type").slider("values",0)+" - "+jQuery("#<?php echo $name?>_range_type").slider("values",1))})
				</script>
				</div>
                <?php
			}elseif($type=='min_max_range_select'){
				$title_min_range=explode(',',$val['search_min_option_title']);
				$value_min_range=explode(',',$val['search_min_option_values']);
				$title_max_range=explode(',',$val['search_max_option_title']);
				$value_max_range=explode(',',$val['search_max_option_values']);
				?>
                <div class="form_row clearfix">
                <select name="<?php echo $name;?>_min" id="<?php echo $name;?>_min" class="textfield textfield_x <?php echo $style_class;?>" <?php echo $extra_parameter;?>>
                    <option value=""><?php echo sprintf(__('Please Select %s Min value',DOMAIN),$site_title);?></option>
                    <?php if(!empty($value_min_range)){
                    for($i=0;$i<count($value_min_range);$i++){?>
                        <option value="<?php echo $value_min_range[$i]; ?>" <?php if($value==$value_min_range[$i]){ echo 'selected="selected"';} else if($default_value==$value_min_range[$i]){ echo 'selected="selected"';}?>><?php echo ($title_min_range[$i])? $title_min_range[$i]:$value_min_range[$i]; ?></option>
                        <?php	
                        }
                    }?>                
                </select>
                </div>
                
                 <div class="form_row clearfix">
                <select name="<?php echo $name;?>_max" id="<?php echo $name;?>_max" class="textfield textfield_x <?php echo $style_class;?>" <?php echo $extra_parameter;?>>
                    <option value=""><?php echo sprintf(__('Please Select %s Max value',DOMAIN),$site_title);?></option>
                    <?php if(!empty($value_max_range)){
                    for($i=0;$i<count($value_max_range);$i++){?>
                        <option value="<?php echo $value_max_range[$i]; ?>" <?php if($value==$value_max_range[$i]){ echo 'selected="selected"';} else if($default_value==$value_max_range[$i]){ echo 'selected="selected"';}?>><?php echo ($title_max_range[$i])? $title_max_range[$i]:$value_max_range[$i]; ?></option>
                        <?php	
                        }
                    }?>                
                </select>
                </div>
                <?php
			}else{ 
			
				do_action('advancesearch_custom_fieldtype',$key,$val,$post_type);	

			}
						
			if($type != 'image_uploader' ) {
				if($admin_desc != ''): 
					if(@$_REQUEST['pid']): ?>
					 <span class="message_note msgcat submit"><?php echo $admin_desc;?></span>
				   <?php endif;
 				    endif; 
					
					if($type!='geo_map') {
					echo $is_required_msg;
					}
			} 
			if($type=='geo_map') { ?>
				<div class="form_row clearfix">
				<input PLACEHOLDER="<?php _e('Address',DOMAIN); ?>" type="text" name="address" id="address" value="<?php echo @$_REQUEST['address']; ?>"/>
				 <?php if($admin_desc == ''):?>
                           <span class="message_note"><?php echo $GET_MAP_MSG;?></span>
                 <?php endif; ?>	
				 </div>
			 <?php } 
			 }
		}
		wp_reset_query();
		wp_reset_postdata();
}
/* 
Name :display_custom_category_field_plugin
description : Returns category custom fields html
*/
function display_custom_category_field_plugin($custom_metaboxes,$session_variable,$post_type,$cpost_type='post'){ 
	foreach($custom_metaboxes as $key=>$val) { 
		$name = $val['name'];
		$site_title = $val['label'];
		$type = $val['type'];
		$htmlvar_name = $val['htmlvar_name'];
		$admin_desc = $val['desc'];
		$option_values = $val['option_values'];
		$default_value = $val['default'];
		$style_class = $val['style_class'];
		$extra_parameter = $val['extra_parameter'];
		if(!$extra_parameter){ $extra_parameter ='';}
		/* Is required CHECK BOF */
		$is_required = '';
		$input_type = '';
		if($val['is_require'] == '1'){
			$is_required = '<span class="required">*</span>';
			$is_required_msg = '<span id="'.$name.'_error" class="message_error2"></span>';
		} else {
			$is_required = '';
			$is_required_msg = '';
		}
		/* Is required CHECK EOF */
		if(@$_REQUEST['pid'])
		{
			$post_info = get_post($_REQUEST['pid']);
			if($name == 'post_title') {
				$value = $post_info->post_title;
			}else {
				$value = get_post_meta($_REQUEST['pid'], $name,true);
			}
			
		}else if(@$_SESSION[$session_variable] && @$_REQUEST['backandedit'])
		{
			$value = @$_SESSION[$session_variable][$name];
		}else{
			$value='';
		}
	   
	if(!isset($_REQUEST['action']) && $type=='post_categories')
	{ /* fetch catgories on action */ 
		wp_reset_query();
		global $post;
		$PostTypeObject = get_post_type_object(get_post_meta($post->ID,'submit_post_type',true));
		$_PostTypeName = $PostTypeObject->labels->name;
		?>
	<div class="form_row clearfix">
	  
			<label><?php echo $_PostTypeName.__(' Categories',DOMAIN).$is_required; ?></label>
             <div class="category_label"><?php include(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/category.php');?></div>
			 <?php echo $is_required_msg;?>
            <?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php else:
            		?>
				<span class="message_note msgcat"><?php _e("In which category you'd like to publish this ",DOMAIN); echo strtolower($_PostTypeName).'?'; ?></span>
			<?php endif;?>
     </div>    
    <?php 
	}elseif(isset($_REQUEST['upgpkg']) && $_REQUEST['upgpkg'] ==1){
		wp_reset_query();
		global $post;
		$PostTypeObject = get_post_type_object(get_post_meta($post->ID,'submit_post_type',true));
		$_PostTypeName = $PostTypeObject->labels->name;

		 ?>
		<div class="form_row clearfix">
	  
			<label><?php echo $_PostTypeName. __('Category',DOMAIN).$is_required; ?></label>
             <div class="category_label"><?php include(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/category.php');?></div>
			 <?php echo $is_required_msg;?>
            <?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php else:
					$PostTypeObject = get_post_type_object($post_type);
					$_PostTypeName = $PostTypeObject->labels->name;?>
				<span class="message_note msgcat"><?php _e("n which category you'd like to publish this ",DOMAIN); echo strtolower($_PostTypeName) ."?"; ?></span>
			<?php endif;?>
     </div>    
	<?php }
	do_action('show_additional_custom_field',$type,$site_title,$is_required,$cpost_type);
	}
}
/*
	Name : templ_get_selected_category_id
	Desc : get selected category ID
	*/
function templ_get_custom_categoryid($category_id){
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
Name :display_custom_category_name
description : Returns category name in custom fields page.
*/
function display_custom_category_name($custom_metaboxes,$session_variable,$taxonomy){
	foreach($custom_metaboxes as $key=>$val) {
		$type = $val['type'];	
		$site_title = $val['label'];	
	?>
	
	   <?php if($type=='post_categories')
		{ 
		 ?>
		 <div class="form_row clearfix categories_selected">
			<label><?php echo __('Category',DOMAIN); ?></label>
             <div class="category_label">
			 <?php 			
				 for($i=0;$i<count($session_variable);$i++)
				 {
					if($i == (count($session_variable) -1 ))
						$sep = '';
					else
						$sep = ',';
					$category_name = get_term_by('id', $session_variable[$i], $taxonomy);
					if($category_name)
					 {
						echo "<strong>".$category_name->name.$sep."</strong>";
						echo '<input type="hidden"  value="'.$session_variable[$i].'" name="category[]">';
						echo '<input type="hidden"  value="'.$session_variable[$i].'" name="category_new[]">';
					 }
				}
				if(isset($_SESSION['custom_fields']['cur_post_id']) && count($_SESSION['custom_fields']['cur_post_id']) > 0 && !isset($_REQUEST['cur_post_id']) && $_REQUEST['category'] == '')
					$id = $_SESSION['custom_fields']['cur_post_id'];
				elseif(isset($_REQUEST['cur_post_id']) && count($_REQUEST['cur_post_id']) > 0)
					$id = $_REQUEST['cur_post_id'];
				$permalink = get_permalink( $id );
		?></div>
		<?php
		/* Go back and edit link */
		if(strpos($permalink,'?'))
		{
			  if($_REQUEST['pid']){ $postid = '&amp;pid='.$_REQUEST['pid']; }
				 $gobacklink = $permalink."&backandedit=1&amp;".$postid;
		}else{
			if($_REQUEST['pid']){ $postid = '&amp;pid='.$_REQUEST['pid']; }
			$gobacklink = $permalink."?backandedit=1";
		}
			if(!isset($_REQUEST['pid']) || (isset($_REQUEST['renew']) && $_REQUEST['renew'] == 1)){
			?>
			  <a href="<?php echo $gobacklink; ?>" class="btn_input_normal fl" ><?php _e('Go back and edit',DOMAIN);?></a>
			<?php } ?>
		
		</div>   	
		<?php }	
	}
}
/* 
Name :display_custom_post_field_plugin
description : Returns all custom fields html
*/
function display_custom_post_field_plugin($custom_metaboxes,$session_variable,$post_type){
	$tmpdata = get_option('templatic_settings');	

	//echo '<pre>';
	//print_r($custom_metaboxes);	
	
	foreach($custom_metaboxes as $heading=>$_custom_metaboxes)
	  {		 
		$activ = fetch_active_heading($heading);
		if($activ):
			$PostTypeObject = get_post_type_object($post_type);
			$_PostTypeName = $PostTypeObject->labels->name;
			if(function_exists('icl_register_string')){
				icl_register_string(DOMAIN,$_PostTypeName.'submit',$_PostTypeName);
				$_PostTypeName =icl_t(DOMAIN,$_PostTypeName.'submit',$_PostTypeName);
			}
			$_PostTypeName = $_PostTypeName . ' ' . __('Information',DOMAIN);
			if($heading == '[#taxonomy_name#]' && $_custom_metaboxes)
			{
				if(!function_exists('icl_register_string')){
					$PostTypeName1 = __($_PostTypeName,DOMAIN); 
				}else
				{
					$PostTypeName1 = $_PostTypeName;
				}
			?>	
            	<div class="sec_title <?php echo $_custom_metaboxes['basic_inf']['style_class']; ?>"><h3><?php echo ucfirst($PostTypeName1); ?></h3></div>
			<?php
            }
			else
			{
				if($_custom_metaboxes){
				if(function_exists('icl_register_string')){
					icl_register_string(DOMAIN,$heading,$heading);
				}
				if(function_exists('icl_t')){
					$heading = icl_t(DOMAIN,$heading,$heading);
				}else{
					$heading = sprintf(__("%s",DOMAIN),$heading);
				}
				echo "<div class='sec_title'><h3>".$heading."</h3>";
				if(@$_custom_metaboxes['basic_inf']['desc']!=""){echo '<p>'.$_custom_metaboxes['basic_inf']['desc'].'</p>';}
				echo "</div>";
				}
			}
		endif;	
		
		foreach($_custom_metaboxes as $key=>$val) {
			$name = $val['name'];
			$site_title = $val['label'];
			$type = $val['type'];
			$htmlvar_name = $val['htmlvar_name'];			
			
			//set the post category , post title, post content, post image and post expert replace as per post type
			if($htmlvar_name=="category")
			{
				$site_title=str_replace('Post Category',__('Category',DOMAIN),$site_title);
			}
			if($htmlvar_name=="post_title")
			{
				$site_title=str_replace('Post Title',__('Title',DOMAIN),$site_title);
			}
			if($htmlvar_name=="post_content")
			{
				$site_title=str_replace('Post Content',__('Description for your listing',DOMAIN),$site_title);
			}
			if($htmlvar_name=="post_excerpt")
			{
				$site_title=str_replace('Post Excerpt',__('Short brief about your listing',DOMAIN),$site_title);
			}
			if($htmlvar_name=="post_images")
			{
				$site_title=str_replace('Post Images',__('Images',DOMAIN),$site_title);
			}
			//finish post type wise replace post category, post title, post content, post expert, post images
			$admin_desc = $val['desc'];
			$option_values = $val['option_values'];
			$default_value = $val['default'];
			$style_class = $val['style_class'];
			$extra_parameter = $val['extra_parameter'];
			$field_require_desc = $val['field_require_desc'];
			if(!$extra_parameter){ $extra_parameter ='';}
			/* Is required CHECK BOF */
			$is_required = '';
			$input_type = '';
			if(trim($val['validation_type']) != ''){
				if($val['is_require'] == '1'){
				$is_required = '<span class="required">*</span>';
				}
				
				$is_required_msg = '<span id="'.$name.'_error" class="message_error2"></span>';
			} else {
				$is_required = '';
				$is_required_msg = '';
			}
			/* Is required CHECK EOF */
			$value = "";
			if(@$_REQUEST['pid'])
			{
				$post_info = get_post($_REQUEST['pid']);
				if($name == 'post_title') {
					$value = $post_info->post_title;
				}
				elseif($name == 'post_content') {
					$value = $post_info->post_content;
				}
				elseif($name == 'post_excerpt'){
					$value = $post_info->post_excerpt;
				}
				else {
					$value = get_post_meta($_REQUEST['pid'], $name,true);
				}
			
			}
			if(@$_SESSION[$session_variable] && @$_REQUEST['backandedit'])
			{
				$value = @$_SESSION[$session_variable][$name];
			}
			$value = apply_filters('SelectBoxSelectedOptions',$value,$name);
			if($value ==''){
				$value= @$val['default'];
			}

		/* custom fields loop continue when custom field type equal to heading type */
		if($type=='heading_type'){
			continue;
		}
		?>
		<div class="form_row clearfix <?php echo $style_class. ' '.$name;?>">
		   <?php if($type=='text'){?>
		   <label><?php echo $site_title.$is_required; ?></label>
		   <?php if($name == 'geo_latitude' || $name == 'geo_longitude') {
				$extra_script = 'onblur="changeMap();"';
				
			} else {
				$extra_script = '';
				
			}?>
             <?php do_action('tmpl_custom_fields_'.$name.'_before'); ?>
			    <input name="<?php echo $name;?>" id="<?php echo $name;?>" value="<?php if(isset($value) && $value!=''){ echo stripslashes($value); } ?>" type="text" class="textfield <?php echo $style_class;?>" <?php echo $extra_parameter; ?> <?php echo $extra_script;?> placeholder="<?php echo @$val['default']; ?>"/>
              <?php echo $is_required_msg;?>
			 	<?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;?>
             <?php do_action('tmpl_custom_fields_'.$name.'_after'); ?>
			<?php
			}elseif($type=='date'){
				//jquery data picker			
			?>     
				<script type="text/javascript">
					jQuery(function(){
						var pickerOpts = {						
							showOn: "both",
							dateFormat: 'yy-mm-dd',
							//buttonImage: "<?php echo TEMPL_PLUGIN_URL;?>css/datepicker/images/cal.png",
							buttonText: '<i class="fa fa-calendar"></i>',
							buttonImageOnly: false,
							monthNames: objectL11tmpl.monthNames,
							monthNamesShort: objectL11tmpl.monthNamesShort,
							dayNames: objectL11tmpl.dayNames,
							dayNamesShort: objectL11tmpl.dayNamesShort,
							dayNamesMin: objectL11tmpl.dayNamesMin,
							isRTL: objectL11tmpl.isRTL,
							onChangeMonthYear: function(year, month, inst) {
							  	jQuery("#<?php echo $name;?>").blur();
						     },
						     onSelect: function(dateText, inst) {
							   //jQuery("#<?php echo $name;?>").focusin();
							     jQuery("#<?php echo $name;?>").blur();
						     }
						};	
						jQuery("#<?php echo $name;?>").datepicker(pickerOpts);
					});
				</script>
				<label><?php echo $site_title.$is_required; ?></label>
                <?php do_action('tmpl_custom_fields_'.$name.'_before'); ?>
				<input type="text" name="<?php echo $name;?>" id="<?php echo $name;?>" class="textfield <?php echo $style_class;?>" value="<?php echo esc_attr(stripslashes($value)); ?>" size="25" <?php echo 	$extra_parameter;?> placeholder="<?php echo @$val['default']; ?>"/>
				 <?php echo $is_required_msg;?>
				<?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;?>
                <?php do_action('tmpl_custom_fields_'.$name.'_after'); ?>	          
			<?php
			}
			elseif($type=='multicheckbox')
			{ ?>
			 <label><?php echo $site_title.$is_required; ?></label>
			<?php
				$options = $val['option_values'];				
				$option_titles = $val['option_title'];	
				if(!is_array($value))		{
					if(strstr($value,','))
					{							
						update_post_meta($_REQUEST['pid'],$htmlvar_name,explode(',',$value));
						$value=get_post_meta($_REQUEST['pid'],$htmlvar_name,true);
					}
				}
				if(!isset($_REQUEST['pid']) && !isset($_REQUEST['backandedit']))
				{
					$default_value = explode(",",$val['default']);
				}
	
				if($options)
				{  
					$chkcounter = 0;
					echo '<div class="form_cat_left hr_input_multicheckbox">';
					do_action('tmpl_custom_fields_'.$name.'_before');
					$option_values_arr = explode(',',$options);
					$option_titles_arr = explode(',',$option_titles);
					for($i=0;$i<count($option_values_arr);$i++)
					{
						$chkcounter++;
						$seled='';
						if(isset($_REQUEST['pid']) || isset($_REQUEST['backandedit']))
						  {
							$default_value = $value;
						  }
						if($default_value !=''){
						if(in_array($option_values_arr[$i],$default_value)){ 
						$seled='checked="checked"';} }	
						$option_titles_arr[$i] = (!empty($option_titles_arr[$i])) ? $option_titles_arr[$i] : $option_values_arr[$i];
						echo '
						<div class="form_cat">
							<label>
								<input name="'.$key.'[]"  id="'.$key.'_'.$chkcounter.'" type="checkbox" value="'.$option_values_arr[$i].'" '.$seled.'  '.$extra_parameter.' /> '.$option_titles_arr[$i].'
							</label>
						</div>';
					}
					echo '</div>';
					?>
                     <?php echo $is_required_msg;?>
					<?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;?>
					<?php
					do_action('tmpl_custom_fields_'.$name.'_after');
				}
			}		
			elseif($type=='texteditor'){	?>
				<label><?php echo $site_title.$is_required; ?></label>
                <?php do_action('tmpl_custom_fields_'.$name.'_before'); ?>
				<?php
					$media_pro = false;
					include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
					if(is_plugin_active("Media-Pro/media-pro.php") && $htmlvar_name=="post_content")
					{
						$media_pro = true;
					}
					// default settings
					$settings =   array(
						'wpautop' => false, // use wpautop?
						'media_buttons' => $media_pro, // show insert/upload button(s)
						'textarea_name' => $name, // set the textarea name to something different, square brackets [] can be used here
						'textarea_rows' => '10', // rows="..."
						'tabindex' => '',
						'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
						'editor_class' => '', // add extra class(es) to the editor textarea
						'teeny' => false, // output the minimal editor config used in Press This
						'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
						'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
						'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
					);				
					if(isset($value) && $value != '') 
					{  $content=$value; }
					else{$content= $val['default']; } 				
					wp_editor( stripslashes($content), $name, $settings);
				?>
                <?php echo $is_required_msg;?>
				<?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;?>
                <?php do_action('tmpl_custom_fields_'.$name.'_after'); ?>
			<?php
			}elseif($type=='textarea'){ 
			?>
                <label><?php echo $site_title.$is_required; ?></label>
                <?php do_action('tmpl_custom_fields_'.$name.'_before'); ?>
                <textarea name="<?php echo $name;?>" id="<?php echo $name;?>" class="<?php if($style_class != '') { echo $style_class;}?> textarea" <?php echo $extra_parameter;?> placeholder="<?php echo @$val['default']; ?>"><?php if(isset($value))echo stripslashes($value);?></textarea>
               	 <?php echo $is_required_msg;?>
                <?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;?>
                <?php do_action('tmpl_custom_fields_'.$name.'_after'); ?>
			<?php
			}elseif($type=='radio'){
			?>
			<?php if($name != 'position_filled' || @$_REQUEST['pid']): ?>
			 <label class="r_lbl"><?php echo $site_title.$is_required; ?></label>
            <?php do_action('tmpl_custom_fields_'.$name.'_before'); ?>
			<?php
				$options = $val['option_values'];
				$option_title = $val['option_title'];
				if($options)
				{ 
					$chkcounter = 0;
					echo '<div class="form_cat_left">';
					$option_values_arr = explode(',',$options);
					$option_titles_arr = explode(',',$option_title);
			
					if($option_title ==''){  $option_titles_arr = $option_values_arr;  }
					
					echo '<ul class="hr_input_radio">';
					for($i=0;$i<count($option_values_arr);$i++)
					{
						$chkcounter++;
						$seled='';
						
						if($default_value == $option_values_arr[$i]){ $seled='checked="checked"';}
						if (isset($value) && trim($value) == trim($option_values_arr[$i])){ $seled='checked="checked"';}
						$event_type = array("Regular event", "Recurring event");
						
						if($key == 'event_type'):
							if (trim(@$value) == trim($event_type[$i])){ $seled="checked=checked";}
							echo '<li>
									<input name="'.$key.'"  id="'.$key.'_'.$chkcounter.'" type="radio" value="'.$event_type[$i].'" '.$seled.'  '.$extra_parameter.' /> <label for="'.$key.'_'.$chkcounter.'">'.$option_titles_arr[$i].'</label>
								</li>';
						else:
							echo '<li><input name="'.$key.'"  id="'.$key.'_'.$chkcounter.'" type="radio" value="'.$option_values_arr[$i].'" '.$seled.'  '.$extra_parameter.' /> <label for="'.$key.'_'.$chkcounter.'">'.$option_titles_arr[$i].'</label>
								</li>';
						endif;
					}
					echo '</ul>';	
					
					echo '</div>';
				}
				?>
                 <?php echo $is_required_msg;?>
				<?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;?>
				<?php
			 do_action('tmpl_custom_fields_'.$name.'_after');
			 endif;	
			}elseif($type=='select'){
			?>
			 <label><?php echo $site_title.$is_required; ?></label>
				<?php do_action('tmpl_custom_fields_'.$name.'_before'); ?>
                <select name="<?php echo $name;?>" id="<?php echo $name;?>" class="textfield textfield_x <?php echo $style_class;?>" <?php echo $extra_parameter;?>>
				<option value=""><?php _e("Please Select",DOMAIN);?></option>
				<?php if($option_values){
				//$option_values_arr = explode(',',$option_values);
				$option_title = ($val['option_title']) ? $val['option_title'] : $val['option_values'];
				$option_values_arr = apply_filters('SelectBoxOptions',explode(',',$option_values),$name);
				$option_title_arr = apply_filters('SelectBoxTitles',explode(',',$option_title),$name);
				for($i=0;$i<count($option_values_arr);$i++)
				{
				?>
				<option value="<?php echo $option_values_arr[$i]; ?>" <?php if($value==$option_values_arr[$i]){ echo 'selected="selected"';} else if($default_value==$option_values_arr[$i]){ echo 'selected="selected"';}?>><?php echo $option_title_arr[$i]; ?></option>
				<?php	
				}
				?>
				<?php }?>
			   
				</select>
                 <?php echo $is_required_msg;?>
				<?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;?>
                <?php do_action('tmpl_custom_fields_'.$name.'_after'); ?>
			<?php
			}
			elseif(!isset($_REQUEST['action']) && $type=='post_categories' && $tmpdata['templatic-category_custom_fields'] == 'No')
				{ 
				/* fetch catgories on action */ ?>
				<div class="form_row clearfix">
				  
						<label><?php echo $site_title.$is_required; 
						global $cat_required;
						$cat_required = $field_require_desc;
						?></label>
						 <div class="category_label"><?php include (TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/category.php');?></div>
						 <?php echo $is_required_msg;?>
						 <?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php else: 
						 	$PostTypeObject = get_post_type_object($post_type);
							$_PostTypeName = $PostTypeObject->labels->name;?>
								 <span class="message_note msgcat"><?php 
								 if($_custom_metaboxes['category']['desc'] !=''){
									echo $_custom_metaboxes['category']['desc'];
								 }else{
									_e('Select at least one category for your ',DOMAIN); echo $_PostTypeName;
								 }?></span>
						<?php endif;?>
											
				 </div>    
				<?php }
			else if($type=='upload'){ ?>
                        <label><?php echo $site_title.$is_required; ?></label>
                         <?php do_action('tmpl_custom_fields_'.$name.'_before'); ?>
                         
                         <input type="file" value="<?php echo $_SESSION['upload_file']; ?>" name="<?php echo $name; ?>" class="fileupload uploadfilebutton" id="<?php echo $name; ?>" placeholder="<?php echo @$val['default']; ?>"/>
                    <?php do_action('tmpl_custom_fields_'.$name.'_after'); ?>
                    
                    <?php if($_REQUEST['pid']): ?>
                    	<p class="resumback"><a href="<?php echo get_post_meta($_REQUEST['pid'],$name, $single = true); ?>"><?php echo basename(get_post_meta($_REQUEST['pid'],$name, $single = true)); ?></a></p>
                    <?php elseif($_SESSION['upload_file'] && @$_REQUEST['backandedit']): 
					 $upload_file=strtolower(substr(strrchr($_SESSION['upload_file'][$name],'.'),1));					 
					 if($upload_file=='jpg' || $upload_file=='jpeg' || $upload_file=='gif' || $upload_file=='png' || $upload_file=='jpg' ):
				?>
                    	<p class="resumback"><img src="<?php echo $_SESSION['upload_file'][$name]; ?>" /></p>
                    	<?php else:?>
                    	<p class="resumback"><a href="<?php echo $_SESSION['upload_file'][$name]; ?>"><?php echo basename($_SESSION['upload_file'][$name]); ?></a></p>
                         <?php endif;?>
                    <?php endif; ?>
                    <?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif; ?>
                    <?php echo $is_required_msg;?>
			<?php 
			}else if($type=='oembed_video'){ ?>
				<label><?php echo $site_title.$is_required; ?></label>				
				<?php do_action('tmpl_custom_fields_'.$name.'_before'); ?>
				<input name="<?php echo $name;?>" id="<?php echo $name;?>" value="<?php if(isset($value) && $value!=''){ echo stripslashes($value); } ?>" type="text" class="textfield <?php echo $style_class;?>" <?php echo $extra_parameter; ?> <?php echo $extra_script;?> placeholder="<?php echo @$val['default']; ?>"/>
				<?php echo $is_required_msg;
				if($admin_desc!=""):?>
					<div class="description"><?php echo $admin_desc; ?></div>
				<?php endif;
				do_action('tmpl_custom_fields_'.$name.'_after');
			}elseif($type=='range_type'){					
					$range_min=$val['range_min'];
					$range_max=$val['range_max'];
					global $validation_info;					
					if($val['is_require']==0 && $range_min!="" && $range_max!='' && $val['search_ctype']=='slider_range'){
						$validation_info[] = array(
								'title'	       => $val['label'],
								'name'	       => $key,
								'espan'	       => $key.'_error',
								'type'	       => $val['type'],
								'text'	       => $val['text'],
								'is_require'	  => 1,
								'validation_type'=> 'digit',
								'search_ctype'=> $val['search_ctype']
						);
					}
				?>
		   		<label><?php echo $site_title.$is_required; ?></label>
             	<?php do_action('tmpl_custom_fields_'.$name.'_before'); ?>                
			    <input name="<?php echo $name;?>" id="<?php echo $name;?>" value="<?php if(isset($value) && $value!=''){ echo stripslashes($value); } ?>" type="text" class="textfield <?php echo $style_class;?>" <?php echo $extra_parameter; ?> <?php echo $extra_script;?> placeholder="<?php echo @$val['default']; ?>" min="<?php echo $range_min?>" max="<?php echo $range_max?>"/>
              <?php echo $is_required_msg;?>
			 	<?php if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;?>
             <?php do_action('tmpl_custom_fields_'.$name.'_after'); ?>
			<?php
			}else{ //
				do_action('tevolution_custom_fieldtype',$key,$val,$post_type);
			}
			
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			
			if($type == 'image_uploader' && !is_plugin_active("Media-Pro/media-pro.php")) {
				add_action('wp_footer','callback_on_footer_fn');
			?>
                    <label><?php echo $site_title.$is_required ?></label>
                    <?php include (TEMPL_MONETIZE_FOLDER_PATH."templatic-custom_fields/image_uploader.php"); ?>
                    <span class="message_note"><?php echo $admin_desc;?></span>
                    <span class="message_error2" id="post_images_error"></span>
					<span class="safari_error" id="safari_error"></span>
			<?php } ?> 
			
			<?php        
               
			if($type=='geo_map') { ?>
				<?php include_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-custom_fields/location_add_map.php"); ?>
                    
                    <?php if($admin_desc == ''):?>
                    	<span class="message_note"><?php echo $GET_MAP_MSG;?></span>
                    <?php endif; ?>
			<?php } ?>
         
         <?php do_action('show_new_custom_field',$type,$site_title,$is_required); ?>      
		 
		 <?php do_action('show_additional_custom_field',$type,$site_title,$is_required,$post_type); ?>     
		 
			</div>    
		<?php
		}
	}
}
/*
 * Function Name: tevolution_comment_status_meta_box
 * Display the review meta box 
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
 * Function Name: tevolution_comment_meta_box
 * Display the review meta box 
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
Name :ptthemes_taxonomy_meta_box
description : Function to add metaboxes in taxonomies BOF
*/
if(!function_exists('ptthemes_taxonomy_meta_box')){
	function ptthemes_taxonomy_meta_box($post_id) {
		global $pagenow,$post;			
		/* Tevolution Custom Post Type custom field meta box */
		if($pagenow=='post.php' || $pagenow=='post-new.php'){			
			if(isset($_REQUEST['post_type']) && $_REQUEST['post_type']!=''){
				$posttype=$_REQUEST['post_type'];
			}else{
				$posttype=(get_post_type(@$_REQUEST['post']))? get_post_type($_REQUEST['post']) :'post';
			}
			
			$post_type_post['post']= (array)get_post_type_object( 'post' );			
			$custom_post_types=get_option('templatic_custom_post');
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
						$meta_name=($val=='[#taxonomy_name#]')? sprintf(__('%s Information',ADMINDOMAIN),$new_menu_name) : sprintf(__('%1$s',ADMINDOMAIN),$val);
						add_meta_box('ptthemes-settings'.$key,$meta_name,'tevolution_custom_meta_box_content',$post_type,'normal','high',array( 'post_types' => $post_type,'heading_type'=>$val));
					}
					/* Price package Meta Box */
					   if(is_active_addons('monetization')){
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
									  array('key' => 'show_package',
										   'value' =>  array(1),
										   'compare' => 'IN',
										   'type'=> 'text'),
									  array('key' => 'package_status',
										   'value' =>  '1',
										   'compare' => '=')),'orderby' => 'menu_order','order' => 'ASC');
							}
							$package_query = new WP_Query($pargs); // Show price package box only when - price packages are available for that post type in backend
							if(count($package_query->posts) != 0)
								add_meta_box('ptthemes-settings-price-package',__('Price Packages',ADMINDOMAIN),'tevolution_featured_list_fn',$post_type,'normal','high',array( 'post_types' => $post_type));			
					
						
					}
					add_meta_box( 'ptthemes-settings-image-gallery', __( 'Image Gallery', ADMINDOMAIN ), 'tevolution_images_box', $post_type, 'side','',$post );
				}
				
			}// finish the custom post type foreach
			
		}// finish the pagenow if condition
		
		/* Finish Tevolution Custom Post Type custom field meta box */
		//add_meta_box("post_type_meta", "Post type options", "post_type_meta", "page", "side", "default");		
	}	
}
/* 
Name :ptthemes_taxonomy_metabox_insert
description : Function to add metaboxes BOF
*/
if(!function_exists('ptthemes_taxonomy_metabox_insert')){
function ptthemes_taxonomy_metabox_insert($post_id) {
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
		
		/*for($i=0;$i<count($post_image);$i++){
			if(!in_array($post_image[$i]['id'],$image_gallery)){				
				 wp_delete_post($post_image[$i]['id'], true );
			}
		}*/		
    } 
   /* Finish image gallery sorting */
	if(is_templ_wp_admin() && isset($_POST['template_post_type']) && $_POST['template_post_type'] != '')
	{
		update_post_meta(@$_POST['post_ID'], 'template_post_type', @$_POST['template_post_type']);
	}
	// store map template option data
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
	//
	// verify nonce
    if (!wp_verify_nonce(@$_POST['templatic_meta_box_nonce'], basename(__FILE__)) && !isset($_POST['featured_type']) ) {
       return $post_id;
    }
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    $pt_metaboxes = get_post_admin_custom_fields_templ_plugin($_POST['post_type']);    
    $pID = $_POST['post_ID'];
    $counter = 0;
	
    foreach ($pt_metaboxes as $pt_metabox) { // On Save.. this gets looped in the header response and saves the values submitted
	
		if($pt_metabox['type'] == 'text' OR $pt_metabox['type'] == 'oembed_video' OR $pt_metabox['type'] == 'select' OR $pt_metabox['type'] == 'checkbox' OR $pt_metabox['type'] == 'textarea' OR $pt_metabox['type'] == 'radio'  OR $pt_metabox['type'] == 'upload' OR $pt_metabox['type'] == 'date' OR $pt_metabox['type'] == 'multicheckbox' OR $pt_metabox['type'] == 'geo_map' OR $pt_metabox['type'] == 'texteditor' OR $pt_metabox['type'] == 'range_type') // Normal Type Things...
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
    if($_POST['featured_type'] != get_post_meta($pID, 'featured_type', true)){
		if($_POST['featured_type']):
			if($_POST['featured_type'] == 'both'):
				 update_post_meta($pID, 'featured_c', 'c');
				 update_post_meta($pID, 'featured_h', 'h');
				 update_post_meta($pID, 'featured_type', $_POST['featured_type']);
			endif;
			if($_POST['featured_type'] == 'c'):
				 update_post_meta($pID, 'featured_c', 'c');
				 update_post_meta($pID, 'featured_h', 'n');
				 update_post_meta($pID, 'featured_type', $_POST['featured_type']);
			endif;	 
			if($_POST['featured_type'] == 'h'):
				 update_post_meta($pID, 'featured_h', 'h');
				 update_post_meta($pID, 'featured_c', 'n');
				 update_post_meta($pID, 'featured_type', $_POST['featured_type']);
			endif;
			if($_POST['featured_type'] == 'none'):
				 update_post_meta($pID, 'featured_h', 'n');
				 update_post_meta($pID, 'featured_c', 'n');
				 update_post_meta($pID, 'featured_type', $_POST['featured_type']);
			endif;
			if($_POST['featured_type'] == 'n'):
				 update_post_meta($pID, 'featured_h', 'n');
				 update_post_meta($pID, 'featured_c', 'n');
				 update_post_meta($pID, 'featured_type', $_POST['featured_type']);
			endif;	
		else:
			 update_post_meta($pID, 'featured_type', 'none');
			 update_post_meta($pID, 'featured_c', 'n');
			 update_post_meta($pID, 'featured_h', 'n');
		endif;
	}
    
     if($_POST['package_select'] && $_POST['package_select']){
		 update_post_meta($pID, 'package_select', $_POST['package_select']);				 
	}
	
	if($_POST['alive_days'] != '' || $_POST['package_select'] != ''){
		
		$listing_price_pkg = $monetization->templ_get_price_info($_POST['package_select'],'');	
		
		/* Insert total amount with featured price */
		$total_amount = 0;
		if(is_active_addons('monetization'))
		{
			global $monetization;
			if(class_exists('monetization'))
			{
				$total_amount = $monetization->tmpl_get_payable_amount($_POST['package_select'],$_POST['featured_type'],$_POST['category']);
			}
		}

		$alive_days = $listing_price_pkg[0]['alive_days'];
		if(isset($listing_price_pkg[0]['alive_days'])){
			$alive_days = $listing_price_pkg[0]['alive_days'];
		}else{
			$alive_days = 30;
		}
	
		update_post_meta($pID, 'paid_amount', $total_amount);
		update_post_meta($pID, 'alive_days', $alive_days);
		
		/* Insert transaction entry from backend */
		if(is_active_addons('monetization') && $pID!=''){
			global $trans_id;
			$transection_db_table_name=$wpdb->prefix.'transactions';
			$post_trans_id  = $wpdb->get_row("select * from $transection_db_table_name where post_id  = '".$pID."' AND package_type is NULL") ;
			if(count($post_trans_id)==0){
				$trans_id = insert_transaction_detail('',$pID);
			}
			
		} 	
	}
    /* Finish proce package sabe form backend */
}
}
/* - Function to add metaboxes EOF - */

/*
Name:tev_findexts
desc : return file extension
*/
function tev_findexts($path) 
{ 
 $pathinfo = pathinfo($path);
 $ext = $pathinfo['extension'];
 return $ext; 
} 
 
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
		if($pt_metabox['type'] == 'text' OR $pt_metabox['type'] == 'select' OR $pt_metabox['type'] == 'radio' OR $pt_metabox['type'] == 'checkbox' OR $pt_metabox['type'] == 'textarea' OR $pt_metabox['type'] == 'upload' OR $pt_metabox['type'] == 'date' OR $pt_metabox['type'] == 'multicheckbox' OR $pt_metabox['type'] == 'texteditor' OR $pt_metabox['type'] == 'range_type')
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
								  
				}
				
				elseif ($pt_metabox['type'] == 'texteditor'){
					$pt_metaboxvalue =get_post_meta($post_id,$pt_metabox["name"],true);
					$default = $pt_metabox['default'];			
					echo  '<tr class="row-'.$pt_id.'">';
					echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</th>';
					echo "<td>";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_before');					
					// default settings
					$settings =   array(
								'wpautop'       => false, // use wpautop?
								'media_buttons' => false, // show insert/upload button(s)
								'textarea_name' => $pt_metabox["name"], // set the textarea name to something different, square brackets [] can be used here
								'textarea_rows' => '10', // rows="..."
								'tabindex'      => '',
								'editor_css'    => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
								'editor_class'  => '', // add extra class(es) to the editor textarea
								'teeny'         => false, // output the minimal editor config used in Press This
								'dfw'           => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
								'tinymce'       => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
								'quicktags'     => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
							);				
					if(isset($pt_metaboxvalue) && $pt_metaboxvalue != '') 
					{  $content=$pt_metaboxvalue; }
					else{$content= $pt_metaboxvalue; } 				
					wp_editor( $content, $pt_metabox["name"], $settings);
					
					
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
					echo  '<p class="description">'.$pt_metabox['desc'].'</p>'."\n";
					echo  '</td></tr>'."\n";								  
				}
				elseif ($pt_metabox['type'] == 'select'){ 
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
					$array_title = ($pt_metabox['option_title']) ? $pt_metabox['option_title']: $array;
		
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
				}
				elseif ($pt_metabox['type'] == 'multicheckbox'){
					
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
				}
				 elseif ($pt_metabox['type'] == 'date'){
					 
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
								//buttonImage: "<?php echo TEMPL_PLUGIN_URL; ?>css/datepicker/images/cal.png",
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
					echo  '<input size="40" class="pt_input_text" type="text" value="'.$pt_metaboxvalue.'" id="'.$pt_metabox["name"].'" name="'.$pt_metabox["name"].'" placeholder="'.$default.'"/>';
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
					
					echo  '<p class="description">'.$pt_metabox['desc'].'</p>';
					echo  '</td></tr>';
								  
				}
				elseif ($pt_metabox['type'] == 'radio'){
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
					//echo  '<p class="value"><input type="checkbox" '.$checked.' class="pt_input_checkbox"  id="'.$pt_id.'" value="1" name="'. $pt_metabox["name"] .'" /></p>';
					echo  '<p class="value"><input id="'. $pt_metabox["name"] .'" type="text" size="36" name="'.$pt_metabox["name"].'" value="'.$pt_metaboxvalue.'" />';
	                echo  '<input id="'. $pt_metabox["name"] .'_button" type="button" value="Browse" /></p>';
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
					echo  '<p class="description">'.$pt_metabox['desc'].'</p>'."\n";
					echo  '</td></tr>'."\n";
				}elseif ($pt_metabox['type'] == 'upload'){
				   $pt_metaboxvalue = get_post_meta($post->ID,$pt_metabox["name"],true);
				   if($pt_metaboxvalue!=""):
						$up_class="upload ".$pt_metaboxvalue;
						echo  '<tr>';
			
						echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label></th>';
						//echo  '<td><input type="file" class="'.$up_class.'"  id="'. $pt_metabox["name"] .'" name="'. $pt_metabox["name"] .'" value="'.$pt_metaboxvalue.'"/>';
						echo  '<td><input id="'. $pt_metabox["name"] .'" type="text" size="36" name="'.$pt_metabox["name"].'" value="'.$pt_metaboxvalue.'" />';
		                echo  '<input id="'. $pt_metabox["name"] .'_button" type="button" value="Browse" />';
						echo  '<p><a href="'.$pt_metaboxvalue.'">'.basename($pt_metaboxvalue).'</a></p>'."\n";
						echo  '<p class="description">'.$pt_metabox['desc'].' </p>';
						$dirinfo = wp_upload_dir();
						$path = $dirinfo['path'];
						$url = $dirinfo['url'];
						$extention = tev_findexts(get_post_meta($post->ID,$pt_metabox["name"], $single = true));
						$img_type = array('png','gif','jpg','jpeg','ico');
						if(in_array($extention,$img_type))
							echo '<img src="'.get_post_meta($post->ID,$pt_metabox["name"], $single = true).'" border="0" class="company_logo" height="140" width="140" />';
						echo  '</td></tr>';
				   else:
					$up_class="upload has-file";
					echo  '<tr>';
					echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label></th>';
						//echo  '<td><input type="file" class="'.$up_class.'"  id="'. $pt_metabox["name"] .'" name="'. $pt_metabox["name"] .'" value="'.$pt_metaboxvalue.'"/>';
						echo  '<td><input id="'. $pt_metabox["name"] .'" type="text" size="36" name="'.$pt_metabox["name"].'" value="'.$pt_metaboxvalue.'" />';
		                echo  '<input id="'. $pt_metabox["name"] .'_button" type="button" value="Browse" />';
						echo  '<p><a href="'.$pt_metaboxvalue.'">'.basename($pt_metaboxvalue).'</a></p>'."\n";
						echo  '<p class="description">'.$pt_metabox['desc'].' </p>';
						echo  '</td></tr>'."\n";
				  endif;
				  ?>
                      <script type="text/javascript">
				  jQuery(document).ready(function() {
					jQuery('#<?php echo $pt_metabox["name"] .'_button'; ?>').click(function(html) {
					 formfield = jQuery('#<?php echo $pt_metabox["name"]?>').attr('name');
					 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
					 window.send_to_editor = function(html) {
						
						 imgurl1 = jQuery('img',html).attr('src');
						imgurl = '';
						 if(!imgurl1){
							imgurl = jQuery(html).attr('href'); 
						 }else{
							imgurl =imgurl1;
						 }
						
						 jQuery('#<?php echo $pt_metabox["name"]; ?>').val(imgurl);
						 tb_remove();
					}
					return false;
				 });
				});
				  </script>
					 
                      <?php
				}elseif($pt_metabox['type'] == 'oembed_video'){					
					$pt_metaboxvalue =get_post_meta($post_id,$pt_metabox["name"],true);
					$default = $pt_metabox['default'];
					echo  '<tr>';
					echo  '<th><label for="'.$pt_id.'">'.$pt_metabox['label'].'</label>'."</th>";
					echo "<td>";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_before');
					echo  "<input size='100' class='regular-text pt_input_text' type='".$pt_metabox['type']."' value='".$pt_metaboxvalue."' name='".$pt_metabox["name"]."' id='".$pt_id."' ".$extra_script." placeholder='".$default."'/>"."\n";
					do_action('tmpl_custom_fields_'.$pt_metabox["name"].'_after');
					echo  '<p class="description">Please paste oEmbed video link url.<br>'.$pt_metabox['desc'].'</p>';
					echo '</td></tr>';
				}else {
					if($pt_metabox['type'] == 'geo_map'){
						echo  '<tr>';
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
		echo "No custom fields was inserted for this post type."."<a href='".site_url('wp-admin/admin.php?page=custom_fields')."'> Click Here </a> to add fields for this post.";
	}
}
}
/* action to add option of featured listing in add listing page in wp-admin */
function tevolution_featured_list_fn($post_id){
	global $post;
	$post_id = $post->ID;
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
	echo  '<th valign="top"><label for="alive_days">'.__('Price Package',DOMAIN).'</label></th>';
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
	$monetization->fetch_package_feature_details_backend($post_id,$pkg_id,''); /* call this function to display fetured packages */
	echo  '<input type="hidden" value="'.$alive_days.'" class="regular-text pt_input_text" name="alive_days" id="alive_days" size="100" />';
	echo '</td>';
	echo '</tr>';
	echo "</tbody>";
	echo "</table>";
	
	
}
/* - Function to fetch the contents in metaboxes EOF - */
add_action('admin_menu', 'ptthemes_taxonomy_meta_box');
add_action('save_post', 'ptthemes_taxonomy_metabox_insert');
/* 
Name : get_image_phy_destination_path_plugin
description : Return Upload directory path
*/
function get_image_phy_destination_path_plugin()
{	
	$wp_upload_dir = wp_upload_dir();
	$path = $wp_upload_dir['path'];
	$url = $wp_upload_dir['url'];
	  $destination_path = $path."/";
      if (!file_exists($destination_path)){
      $imagepatharr = explode('/',str_replace(ABSPATH,'', $destination_path));
	   $year_path = ABSPATH;
		for($i=0;$i<count($imagepatharr);$i++)
		{
		  if($imagepatharr[$i])
		  {
			$year_path .= $imagepatharr[$i]."/";
			  if (!file_exists($year_path)){
				  mkdir($year_path, 0777);
			  }     
			}
		}
	}
	  return $destination_path;
}
/* 
Name : get_image_size_plugin
description : Create Image from different extension
*/
function get_image_size_plugin($src)
{
	$filextenson = stripExtension_plugin($src);
	if($filextenson == "jpeg" || $filextenson == "jpg")
	  {
		$img = imagecreatefromjpeg($src);  
	  }
	
	if($filextenson == "png")
	  {
		$img = imagecreatefrompng($src);  
	  }
	if($filextenson == "gif")
	  {
		$img = imagecreatefromgif($src);  
	  }
	if($img !=''){
		$width = imageSX($img);
		$height = imageSY($img);
	}
	
	return array('width'=>$width,'height'=>$height);
	
}
/* 
Name : stripExtension_plugin
description : Return the extension of file
*/
function stripExtension_plugin($filename = '') {
    if (!empty($filename)) 
	   {
        $filename = strtolower($filename);
		if($filename !='')
			$extArray = explode("[/\\.]", $filename);
        $p = count($extArray) - 1;
        $extension = $extArray[$p];
        return $extension;
    } else {
        return false;
    }
}

/* 
Name : image_resize_custom_plugin
description : Image resize
*/
function image_resize_custom_plugin($src,$dest,$twidth,$theight)
{
	global $image_obj;
	// Get the image and create a thumbnail
	$img_arr = explode('.',$dest);
	$imgae_ext = strtolower($img_arr[count($img_arr)-1]);
	if($imgae_ext == 'jpg' || $imgae_ext == 'jpeg')
	{
		$img = imagecreatefromjpeg($src);
	}elseif($imgae_ext == 'gif')
	{
		$img = imagecreatefromgif($src);
	}
	elseif($imgae_ext == 'png')
	{
		$img = imagecreatefrompng($src);
	}
	if($img)
	{
		$width = imageSX($img);
		$height = imageSY($img);
	
		if (!$width || !$height) {
			echo "ERROR:Invalid width or height";
			exit(0);
		}
		
		if(($twidth<=0 || $theight<=0))
		{
			return false;
		}
		$image_obj->load($src);
		$image_obj->resize($twidth,$theight);
		$new_width = $image_obj->getWidth();
		$new_height = $image_obj->getHeight();
		$imgname_sub = '-'.$new_width.'X'. $new_height.'.'.$imgae_ext;
		$img_arr1 = explode('.',$dest);
		unset($img_arr1[count($img_arr1)-1]);
		$dest = implode('.',$img_arr1).$imgname_sub;
		$image_obj->save($dest);
		
		
		return array(
					'file' => basename( $dest ),
					'width' => $new_width,
					'height' => $new_height,
				);
	}else
	{
		return array();
	}
}
/* 
Name : move_original_image_file_plugin
description : Image move in Upload folder
*/
function move_original_image_file_plugin($src,$dest)
{
	copy($src, $dest);
	//unlink($src);
	$dest = explode('/',$dest);
	$img_name = $dest[count($dest)-1];
	$img_name_arr = explode('.',$img_name);
	$my_post = array();
	$my_post['post_title'] = $img_name_arr[0];
	$my_post['guid'] = get_bloginfo('url')."/files/".get_image_rel_destination_path_plugin().$img_name;
	return $my_post;
}
/* 
Name : get_image_rel_destination_path_plugin
description : Image Final path
*/
function get_image_rel_destination_path_plugin()
{
	$today = getdate();
	if ($today['month'] == "January"){
	  $today['month'] = "01";
	}
	elseif ($today['month'] == "February"){
	  $today['month'] = "02";
	}
	elseif  ($today['month'] == "March"){
	  $today['month'] = "03";
	}
	elseif  ($today['month'] == "April"){
	  $today['month'] = "04";
	}
	elseif  ($today['month'] == "May"){
	  $today['month'] = "05";
	}
	elseif  ($today['month'] == "June"){
	  $today['month'] = "06";
	}
	elseif  ($today['month'] == "July"){
	  $today['month'] = "07";
	}
	elseif  ($today['month'] == "August"){
	  $today['month'] = "08";
	}
	elseif  ($today['month'] == "September"){
	  $today['month'] = "09";
	}
	elseif  ($today['month'] == "October"){
	  $today['month'] = "10";
	}
	elseif  ($today['month'] == "November"){
	  $today['month'] = "11";
	}
	elseif  ($today['month'] == "December"){
	  $today['month'] = "12";
	}
	global $upload_folder_path;
	$tmppath = $upload_folder_path;
	global $blog_id;
	if($blog_id)
	{
		return $user_path = $today['year']."/".$today['month']."/";
	}else
	{
		return $user_path = get_option( 'siteurl' ) ."/$tmppath".$today['year']."/".$today['month']."/";
	}
}
/* 
Name : get_site_emailId_plugin
description : Get site email Id
*/
function get_site_emailId_plugin()
{
	$generalinfo = get_option('mysite_general_settings');
	if($generalinfo['site_email'])
	{
		return $generalinfo['site_email'];
	}else
	{
		return get_option('admin_email');
	}
}
/* 
Name : get_site_emailName_plugin
description : Get site email Name
*/
function get_site_emailName_plugin()
{
	$generalinfo = get_option('mysite_general_settings');
	if($generalinfo['site_email_name'])
	{
		return stripslashes($generalinfo['site_email_name']);
	}else
	{
		return stripslashes(get_option('blogname'));
	}
}
/* 
Name : display_amount_with_currency_plugin
description : Display Amount with symbol
*/
function display_amount_with_currency_plugin($amount,$currency = ''){
	$amt_display = '';
	if($amount != ""){
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
Name : bdw_get_images_plugin
description : Resize image
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
						$img_arr = wp_get_attachment_image_src($id, 'thumbnail'); // Get the thumbnail url for the attachment
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

/* Paginaton start BOF
   Function that performs a Boxed Style Numbered Pagination (also called Page Navigation).
   Function is largely based on Version 2.4 of the WP-PageNavi plugin */
function pagenavi_plugin($before = '', $after = '') {
    global $wpdb, $wp_query;
	
    $pagenavi_options = array();
   // $pagenavi_options['pages_text'] = ('Page %CURRENT_PAGE% of %TOTAL_PAGES%:');
    $pagenavi_options['current_text'] = '%PAGE_NUMBER%';
    $pagenavi_options['page_text'] = '%PAGE_NUMBER%';
    $pagenavi_options['first_text'] = __('First Page',DOMAIN);
    $pagenavi_options['last_text'] = __('Last Page',DOMAIN);
    $pagenavi_options['next_text'] = apply_filters('text_pagi_next','<strong class="next page-numbers">'.__('NEXT',DOMAIN).'</strong>');
    $pagenavi_options['prev_text'] = apply_filters('text_pagi_prev','<strong class="prev page-numbers">'.__('PREV',DOMAIN).'</strong>');
    $pagenavi_options['dotright_text'] = '...';
    $pagenavi_options['dotleft_text'] = '...';
    $pagenavi_options['num_pages'] = 5; //continuous block of page numbers
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
        //templ_round_num() custom function - Rounds To The Nearest Value.
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
                echo '<a class="page-numbers" href="'.esc_url(get_pagenum_link($max_page)).'" title="'.$last_page_text.'">'.$last_page_text.'</a>';
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
function templ_round_num($num, $to_nearest) {
   /*Round fractions down (http://php.net/manual/en/function.floor.php)*/
   return floor($num/$to_nearest)*$to_nearest;
}
/*--Paginaton start EOF--*/
/**-- Upload BOF --**/
function get_file_upload($file_details)
{
	global $upload_folder_path;
	$wp_upload_dir = wp_upload_dir();
	$path = $wp_upload_dir['path'];
	$url = $wp_upload_dir['url'];
	$destination_path = $wp_upload_dir['path'].'/';
	if (!file_exists($destination_path))
	{
		$imagepatharr = explode('/',$upload_folder_path);
		$year_path = ABSPATH;
		for($i=0;$i<count($imagepatharr);$i++)
		{
		  if($imagepatharr[$i])
		  {
			 $year_path .= $imagepatharr[$i]."/";
			  if (!file_exists($year_path)){
				  mkdir($year_path, 0777);
			  }     
			}
		}
	   $imagepatharr = explode('/',$imagepath);
	   $upload_path = ABSPATH . "$upload_folder_path";
	  if (!file_exists($upload_path)){
		mkdir($upload_path, 0777);
	  }
	  for($i=0;$i<count($imagepatharr);$i++)
	  {
		  if($imagepatharr[$i])
		  {
			  $year_path = ABSPATH . "$upload_folder_path".$imagepatharr[$i]."/";
			  if (!file_exists($year_path))
			  {
				  mkdir($year_path, 0777);
			  }     
			  @mkdir($destination_path, 0777);
		}
	  }
	}
	
	if($file_details['name'])
	{		
		$srch_arr = array(' ',"'",'"','?','*','!','@','#','$','%','^','&','(',')','+','=');
		$replace_arr = array('_','','','','','','','','','','','','','','','');
		$name = time().'_'.str_replace($srch_arr,$replace_arr,$file_details['name']);
		$tmp_name = $file_details['tmp_name'];
		$target_path = $destination_path . str_replace(',','',$name);
		$extension_file = array('.php','.js');
		$file_ext= substr($target_path, -4, 4);		
		if(!in_array($file_ext,$extension_file))
		{
			if(@move_uploaded_file($tmp_name, $target_path))
			{
				$imagepath1 = $url."/".$name;
				return $imagepath1 = $imagepath1;
			}
		}
	}	
}
/**-- Upload resume EOF --**/
/*  Here I made an array of user custom fields */
function user_fields_array()
{
	global $post;
	remove_all_actions('posts_where');
	$user_args=
	array( 'post_type' => 'custom_user_field',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
	   'relation' => 'AND',
		array(
			'key' => 'on_registration',
			'value' =>  '1',
			'compare' => '='
		)
	),
	'meta_key' => 'sort_order',
	'orderby' => 'meta_value',
	'order' => 'ASC'
	);
	$user_meta_sql = null;
	$user_meta_sql = new WP_Query($user_args);
	if($user_meta_sql)
 	{
	while ($user_meta_sql->have_posts()) : $user_meta_sql->the_post();
	$name = $post->post_name;
	$site_title = $post->post_title;
	$type = get_post_meta($post->ID,'ctype',true);
	$is_require = get_post_meta($post->ID,'is_require',true);
	$admin_desc = $post->post_content;
	$option_values = get_post_meta($post->ID,'option_values',true);
	$on_registration = get_post_meta($post->ID,'on_registration',true);
	$on_profile = get_post_meta($post->ID,'on_profile',true);
	$on_author_page =  get_post_meta($post->ID,'on_author_page',true);
	if($type=='text'){
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'text',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" size="25" class="textfield"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clearfix">',
		"outer_end"	=>	'</div>',
		"tag_st"	=>	'',
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}elseif($type=='checkbox'){
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'checkbox',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" size="25" class="checkbox"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clearfix checkbox_field">',
		"outer_end"	=>	'',
		"tag_st"	=>	'',
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span></div>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}elseif($type=='textarea'){
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'textarea',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" size="25" class="textarea"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clearfix">',
		"outer_end"	=>	'</div>',
		"tag_st"	=>	'',
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
		
	}elseif($type=='texteditor'){
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'texteditor',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" size="25" class="mce"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clear">',
		"outer_end"	=>	'</div>',
		"tag_before"=>	'<div class="clear">',
		"tag_after"=>	'</div>',
		"tag_st"	=>	'',
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}elseif($type=='select'){
		//$option_values=explode(",",$option_values );
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'select',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'"',
		"options"	=> 	$option_values,
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clear">',
		"outer_end"	=>	'</div>',
		"tag_st"	=>	'',
		"tag_end"	=>	'',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}elseif($type=='radio'){
		//$option_values=explode(",",$option_values );
		$form_fields_usermeta[$name] = array(
			"label"		=> $site_title,
			"type"		=>	'radio',
			"default"	=>	$default_value,
			"extra"		=>	'',
			"options"	=> 	$option_values,
			"is_require"	=>	$is_require,
			"outer_st"	=>	'<div class="form_row clear">',
			"outer_end"	=>	'</div>',
			"tag_before"=>	'<div class="form_cat">',
			"tag_after"=>	'</div>',
			"tag_st"	=>	'',
			"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
			"on_registration"	=>	$on_registration,
			"on_profile"	=>	$on_profile,
			"on_author_page" => $on_author_page,
			);
	}elseif($type=='multicheckbox'){
		//$option_values=explode(",",$option_values );
		$form_fields_usermeta[$name] = array(
			"label"		=> $site_title,
			"type"		=>	'multicheckbox',
			"default"	=>	$default_value,
			"extra"		=>	'',
			"options"	=> 	$option_values,
			"is_require"	=>	$is_require,
			"outer_st"	=>	'<div class="form_row clear">',
			"outer_end"	=>	'</div>',
			"tag_before"=>	'<div class="form_cat">',
			"tag_after"=>	'</div>',
			"tag_st"	=>	'',
			"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
			"on_registration"	=>	$on_registration,
			"on_profile"	=>	$on_profile,
			"on_author_page" => $on_author_page,
			);
	
	}elseif($type=='date'){
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'date',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" size="25" class="textfield_date"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clearfix">',
		"outer_end"	=>	'</div>',
		//"tag_st"	=>	'<img src="'.get_template_directory_uri().'/images/cal.gif" alt="Calendar"  onclick="displayCalendar(document.userform.'.$name.',\'yyyy-mm-dd\',this)" style="cursor: pointer;" align="absmiddle" border="0" class="calendar_img" />',
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
		
	}elseif($type=='upload'){
	$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'upload',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" class="textfield"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clearfix upload_img">',
		"outer_end"	=>	'</div>',
		"tag_st"	=>	'',
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}elseif($type=='head'){
	$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'head',
		"outer_st"	=>	'<h5 class="form_title">',
		"outer_end"	=>	'</h5>',
		);
	}elseif($type=='geo_map'){
	$form_fields_usermeta[$name] = array(
		"label"		=> '',
		"type"		=>	'geo_map',
		"default"	=>	$default_value,
		"extra"		=>	'',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'',
		"outer_end"	=>	'',
		"tag_st"	=>	'',
		"tag_end"	=>	'',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);		
	}elseif($type=='image_uploader'){
	$form_fields_usermeta[$name] = array(
		"label"		=> '',
		"type"		=>	'image_uploader',
		"default"	=>	$default_value,
		"extra"		=>	'',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'',
		"outer_end"	=>	'',
		"tag_st"	=>	'',
		"tag_end"	=>	'',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}
  endwhile;
  return $form_fields_usermeta;
}
}
/* With the help of User custom fields array, To fetch out the user custom fields */
function display_usermeta_fields($user_meta_array)
{
	$form_fields_usermeta	= $user_meta_array;
	global $user_validation_info;
	$user_validation_info = array();
  foreach($form_fields_usermeta as $key=>$val)
	{
		
		if($key!='user_email' && $key!='user_fname')
			continue;
	$str = ''; $fval = '';
	$field_val = $key.'_val';
	if(isset($_REQUEST['user_fname']) || (!isset($_REQUEST['backandedit'])  && $_REQUEST['backandedit'] == '')){ $field_val = $_REQUEST[$key]; } elseif(isset($_REQUEST['backandedit']) && $_REQUEST['backandedit'] == '1' ) {$field_val = $_SESSION['custom_fields'][$key]; }
	if(@$field_val){ $fval = $field_val; }else{ $fval = $val['default']; }
   
	if($val['is_require'])
	{
		$user_validation_info[] = array(
								   'name'	=> $key,
								   'espan'	=> $key.'_error',
								   'type'	=> $val['type'],
								   'text'	=> $val['label'],
								   );
	}
	if($val['type']=='text')
	{
		$str = '<input name="'.$key.'" type="text" '.$val['extra'].' value="'.$fval.'">';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';
		}
	}elseif($val['type']=='hidden')
	{
		$str = '<input name="'.$key.'" type="hidden" '.$val['extra'].' value="'.$fval.'">';	
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='textarea')
	{
		$str = '<textarea name="'.$key.'" '.$val['extra'].'>'.$fval.'</textarea>';	
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='file')
	{
		$str = '<input name="'.$key.'" type="file" '.$val['extra'].' value="'.$fval.'">';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='include')
	{
		$str = @include_once($val['default']);
	}else
	if($val['type']=='head')
	{
		$str = '';
	}else
	if($val['type']=='date')
	{
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
						//buttonImage: "<?php echo TEMPL_PLUGIN_URL; ?>css/datepicker/images/cal.png",
						buttonText: '<i class="fa fa-calendar"></i>',
					};	
					jQuery("#<?php echo $key;?>").datepicker(pickerOpts);					
				});
			</script>
        <?php
		$str = '<input name="'.$key.'" id="'.$key.'" type="text" '.$val['extra'].' value="'.$fval.'">';			
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='catselect')
	{
		$term = get_term( (int)$fval, CUSTOM_CATEGORY_TYPE1);
		$str = '<select name="'.$key.'" '.$val['extra'].'>';
		$args = array('taxonomy' => CUSTOM_CATEGORY_TYPE1);
		$all_categories = get_categories($args);
		foreach($all_categories as $key => $cat) 
		{
		
			$seled='';
			if($term->name==$cat->name){ $seled='selected="selected"';}
			$str .= '<option value="'.$cat->name.'" '.$seled.'>'.$cat->name.'</option>';	
		}
		$str .= '</select>';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='catdropdown')
	{
		$cat_args = array('name' => 'post_category', 'id' => 'post_category_0', 'selected' => $fval, 'class' => 'textfield', 'orderby' => 'name', 'echo' => '0', 'hierarchical' => 1, 'taxonomy'=>CUSTOM_CATEGORY_TYPE1);
		$cat_args['show_option_none'] = __('Select Category',DOMAIN);
		$str .=wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='select')
	{
		$str = '<select name="'.$key.'" '.$val['extra'].'>';
		 $str .= '<option value="" >'.PLEASE_SELECT.' '.$val['label'].'</option>';	
		$option_values_arr = explode(',', $val['options']);
		for($i=0;$i<count($option_values_arr);$i++)
		{
			$seled='';
			
			if($fval==$option_values_arr[$i]){ $seled='selected="selected"';}
			$str .= '<option value="'.$option_values_arr[$i].'" '.$seled.'>'.$option_values_arr[$i].'</option>';	
		}
		$str .= '</select>';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='catcheckbox')
	{
		$fval_arr = explode(',',$fval);
		$str .= $val['tag_before'].get_categories_checkboxes_form(CUSTOM_CATEGORY_TYPE1,$fval_arr).$oval.$val['tag_after'];
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='catradio')
	{
		$args = array('taxonomy' => CUSTOM_CATEGORY_TYPE1);
		$all_categories = get_categories($args);
		foreach($all_categories as $key1 => $cat) 
		{
			
			
				$seled='';
				if($fval==$cat->term_id){ $seled='checked="checked"';}
				$str .= $val['tag_before'].'<input name="'.$key.'" type="radio" '.$val['extra'].' value="'.$cat->name.'" '.$seled.'> '.$cat->name.$val['tag_after'];	
			
		}
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='checkbox')
	{
		if($fval){ $seled='checked="checked"';}
		$str = '<input name="'.$key.'" type="checkbox" '.$val['extra'].' value="1" '.$seled.'>';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='upload')
	{
		
		$str = '<input name="'.$key.'" type="file" '.$val['extra'].' '.$uclass.' value="'.$fval.'" > ';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}
	else
	if($val['type']=='radio')
	{
		$options = $val['options'];
		if($options)
		{
			$option_values_arr = explode(',',$options);
			for($i=0;$i<count($option_values_arr);$i++)
			{
				$seled='';
				if($fval==$option_values_arr[$i]){$seled='checked="checked"';}
				$str .= $val['tag_before'].'<input name="'.$key.'" type="radio" '.$val['extra'].'  value="'.$option_values_arr[$i].'" '.$seled.'> '.$option_values_arr[$i].$val['tag_after'];
			}
			if($val['is_require'])
			{
				$str .= '<span id="'.$key.'_error"></span>';	
			}
		}
	}else
	if($val['type']=='multicheckbox')
	{
		$options = $val['options'];
		if($options)
		{  $chkcounter = 0;
			
			$option_values_arr = explode(',',$options);
			for($i=0;$i<count($option_values_arr);$i++)
			{
				$chkcounter++;
				$seled='';
				$fval_arr = explode(',',$fval);
				if(in_array($option_values_arr[$i],$fval_arr)){ $seled='checked="checked"';}
				$str .= $val['tag_before'].'<input name="'.$key.'[]"  id="'.$key.'_'.$chkcounter.'" type="checkbox" '.$val['extra'].' value="'.$option_values_arr[$i].'" '.$seled.'> '.$option_values_arr[$i].$val['tag_after'];
			}
			if($val['is_require'])
			{
				$str .= '<span id="'.$key.'_error"></span>';	
			}
		}
	}
	else
	if($val['type']=='packageradio')
	{
		$options = $val['options'];
		foreach($options as $okey=>$oval)
		{
			$seled='';
			if($fval==$okey){$seled='checked="checked"';}
			$str .= $val['tag_before'].'<input name="'.$key.'" type="radio" '.$val['extra'].' value="'.$okey.'" '.$seled.'> '.$oval.$val['tag_after'];	
		}
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='geo_map')
	{
		do_action('templ_submit_form_googlemap');	
	}else
	if($val['type']=='image_uploader')
	{
		do_action('templ_submit_form_image_uploader');	
	}
	
	if (function_exists('icl_register_string')) {		
			icl_register_string(DOMAIN, $val['type'].'_'.$key,$val['label']);	
			$val['label'] = icl_t(DOMAIN, $val['type'].'_'.$key,$val['label']);
	   }
	if($val['is_require'])
	{
		$label = '<label>'.$val['label'].' <span class="indicates">*</span> </label>';
	}else
	{
		$label = '<label>'.$val['label'].'</label>';
	}
	if($val['type']=='texteditor')
			{
				echo $val['outer_st'].$label.$val['tag_st'];
				 echo $val['tag_before'].$val['tag_after'];
            // default settings
					$settings =   array(
						'wpautop' => false, // use wpautop?
						'media_buttons' => false, // show insert/upload button(s)
						'textarea_name' => $key, // set the textarea name to something different, square brackets [] can be used here
						'textarea_rows' => '10', // rows="..."
						'tabindex' => '',
						'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
						'editor_class' => '', // add extra class(es) to the editor textarea
						'teeny' => false, // output the minimal editor config used in Press This
						'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
						'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
						'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
					);				
					if(isset($fval) && $fval != '') 
					{  $content=$fval; }
					else{$content= $fval; } 				
					wp_editor( $content, $key, $settings);				
			
					if($val['is_require'])
					{
						$str .= '<span id="'.$key.'_error"></span>';	
					}
				echo $str.$val['tag_end'].$val['outer_end'];
			}else{	
				echo $val['outer_st'].$label.$val['tag_st'].$str.$val['tag_end'].$val['outer_end'];
			}
 }
}
/* Return User name */
function get_user_name_plugin($fname,$lname='')
{
	global $wpdb;
	if($lname)
	{
		$uname = $fname.'-'.$lname;
	}else
	{
		$uname = $fname;
	}
	$nicename = strtolower(str_replace(array("'",'"',"?",".","!","@","#","$","%","^","&","*","(",")","-","+","+"," "),array('','','','-','','-','-','','','','','','','','','','-','-',''),$uname));
	$nicenamecount = $wpdb->get_var("select count(user_nicename) from $wpdb->users where user_nicename like \"$nicename\"");
	if($nicenamecount=='0')
	{
		return trim($nicename);
	}else
	{
		$lastuid = $wpdb->get_var("select max(ID) from $wpdb->users");
		return $nicename.'-'.$lastuid;
	}
}
/* Rerturns user currently in admin area or in front end */
function is_templ_wp_admin()
{
	if(strstr($_SERVER['REQUEST_URI'],'/wp-admin/'))
	{
		return true;
	}
	return false;
}
/* 
Name : is_valid_coupon_plugin
description : Return coupon valid or not
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
Name : get_payable_amount_with_coupon_plugin
description : Return Total amt
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
Name : get_payable_amount_with_coupon_plugin
description : Return Amt by filtering
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
/*
Name :templ_captcha_integrate
Description : put this function where you want to use captcha
*/
function templ_captcha_integrate($form)
{
	$tmpdata = get_option('templatic_settings');
	$display = @$tmpdata['user_verification_page'];
	if(isset($tmpdata['recaptcha']) &&  $tmpdata['recaptcha'] == 'recaptcha')
	{
		$a = get_option("recaptcha_options");
		if(file_exists(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php') && is_plugin_active('wp-recaptcha/wp-recaptcha.php') && in_array($form,$display))
		{
			require_once(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php');
			echo '<label class="recaptcha_claim">'; _e('Verify Captcha',DOMAIN); echo ' : </label>  <span>*</span>';
			$publickey = $a['public_key']; // you got this from the signup page ?>
			<div class="claim_recaptcha_div"><?php echo recaptcha_get_html($publickey,'',is_ssl()); ?> </div>
	<?php }
	}
	elseif(isset($tmpdata['recaptcha']) && $tmpdata['recaptcha'] == 'playthru')
	{ ?>
	<?php /* CODE TO ADD PLAYTHRU PLUGIN COMPATIBILITY */
		if(file_exists(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php') && is_plugin_active('are-you-a-human/areyouahuman.php')  && in_array($form,$display))
		{
			require_once( get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php');
			require_once(get_tmpl_plugin_directory().'are-you-a-human/includes/ayah.php');
			$ayah = ayah_load_library();
			echo $ayah->getPublisherHTML();
		}
	}
}
/* NAME : FETCH POST DEFAULT STATUS
DESCRIPTION : THIS FUNCTION WILL FETCH THE DEFAULT STATUS OF THE POSTS SET BY THE ADMIN IN BACKEND GENERAL SETTINGS */
function fetch_posts_default_status()
{
	$tmpdata = get_option('templatic_settings');
	$post_default_status = $tmpdata['post_default_status'];
	return $post_default_status;
}
/* EOF - FETCH DEFAULT STATUS FOR POSTS */
/* NAME : FETCH POST DEFAULT PAID STATUS
DESCRIPTION : THIS FUNCTION WILL FETCH THE DEFAULT STATUS OF THE PAID POSTS SET BY THE ADMIN IN BACKEND GENERAL SETTINGS */
function fetch_posts_default_paid_status()
{
	$tmpdata = get_option('templatic_settings');
	$post_default_status = $tmpdata['post_default_status_paid'];
	if($post_default_status ==''){
		$post_default_status ='publish';
	}
	return $post_default_status;
}
/* EOF - FETCH DEFAULT STATUS FOR PAID POSTS */
/*
 * add action for add calender css and javascript file inside html head tag
 */ 
add_action ('wp_head', 'header_css_javascript');
add_action('admin_head','header_css_javascript',12);
add_action('init','tev_css'); // add tevolution css on top
function tev_css(){
	wp_enqueue_style('jQuery_datepicker_css',TEMPL_PLUGIN_URL.'css/datepicker/jquery.ui.all.min.css');	
}
/*
 * Function Name:header_css_javascript
 * Front side add css and javascript file in side html head tag 
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
function header_css_javascript()  {
	global $current_user, $wp_locale,$post;
	$is_submit=get_post_meta( @$post->ID,'is_tevolution_submit_form',true);
	//wp_enqueue_script('jquery_ui_core',TEMPL_PLUGIN_URL.'js/jquery.ui.core.js');	
	$register_page_id=get_option('tevolution_register');
	$login_page_id=get_option('tevolution_login');
	$profile_page_id=get_option('tevolution_profile');
	
	if(is_admin() || ($is_submit==1 || $register_page_id== @$post->ID || $login_page_id== @$post->ID || $profile_page_id== @$post->ID)){
		wp_enqueue_script('jquery-ui-datepicker');
		 //localize our js
		$aryArgs = array(
			'monthNames'        => strip_array_indices( $wp_locale->month ),
			'monthNamesShort'   => strip_array_indices( $wp_locale->month_abbrev ),
			'monthStatus'       => __( 'Show a different month', DOMAIN ),
			'dayNames'          => strip_array_indices( $wp_locale->weekday ),
			'dayNamesShort'     => strip_array_indices( $wp_locale->weekday_abbrev ),
			'dayNamesMin'       => strip_array_indices( $wp_locale->weekday_initial ),
			// is Right to left language? default is false
			'isRTL'             => @$wp_locale->is_rtl,
		);
	 
		// Pass the array to the enqueued JS
		wp_localize_script( 'jquery-ui-datepicker', 'objectL11tmpl', $aryArgs );		
	}
	
}
/*
Name : tmpl_show_on_detail
Desc : Show on detail page enable fields
*/
function tmpl_show_on_detail($cur_post_type,$heading_type){
	global $wpdb,$post;
	remove_all_actions('posts_where');
	add_filter('posts_join', 'custom_field_posts_where_filter');
	if($heading_type)
	 {
		$args = array( 'post_type' => 'custom_fields',
				'posts_per_page' => -1	,
				'post_status' => array('publish'),
				'meta_query' => array(
				 'relation' => 'AND',
				array(
					'key' => 'post_type_'.$cur_post_type.'',
					'value' => $cur_post_type,
					'compare' => '=',
					'type'=> 'text'
				),
				array(
					'key' => 'show_on_page',
					'value' =>  array('user_side','both_side'),
					'compare' => 'IN'
				),
				array(
					'key' => 'is_active',
					'value' =>  '1',
					'compare' => '='
				),
				array(
					'key' => 'heading_type',
					'value' =>  $heading_type,
					'compare' => '='
				),
				array(
					'key' => 'show_on_detail',
					'value' =>  '1',
					'compare' => '='
					)
				),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value',
				'order' => 'ASC'
		);
	 }
	else
	 {
		$args = array( 'post_type' => 'custom_fields',
			'posts_per_page' => -1	,
			'post_status' => array('publish'),
			'meta_query' => array(
			 'relation' => 'AND',
			array(
				'key' => 'post_type_'.$cur_post_type.'',
				'value' => $cur_post_type,
				'compare' => '=',
				'type'=> 'text'
			),
			array(
				'key' => 'show_on_page',
				'value' =>  array('user_side','both_side'),
				'compare' => 'IN'
			),
			array(
				'key' => 'is_active',
				'value' =>  '1',
				'compare' => '='
			),
			array(
				'key' => 'show_on_detail',
				'value' =>  '1',
				'compare' => '='
				)
			),
			'meta_key' => 'sort_order',
			'orderby' => 'meta_value',
			'order' => 'ASC'
		);
 
	 }
	$post_query = null;
	$upload = array();
	$post_query = new WP_Query($args);
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	return $post_query;
}
add_action('templatic_fields_onpreview','tmpl_show_custom_fields_onpreview',10,2);
/*
Name : tmpl_show_custom_fields_onpreview
Desc : Show on detail page enable fields
*/
function tmpl_show_custom_fields_onpreview($session,$cur_post_type){
	global $wpdb,$post,$upload;
	
	$heading_type = fetch_heading_per_post_type($cur_post_type);
	if(count($heading_type) > 0)
	{
		foreach($heading_type as $_heading_type)		
			$post_meta_info_arr[$_heading_type] = tmpl_show_on_detail($cur_post_type,$_heading_type);
	}
	else
		$post_meta_info_arr[] = tmpl_show_on_detail($cur_post_type,'');
	
	echo "<div class='grid02 rc_rightcol clearfix'>";
	echo "<ul class='list'>";
	if($post_meta_info_arr)
	{	
		foreach($post_meta_info_arr as $key=> $post_meta_info)
		 {
			$activ = fetch_active_heading($key);
			$j=0;
			while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
				if($post->post_name != 'post_content' && $post->post_name != 'post_title' && $post->post_name != 'category' && $post->post_name != 'post_images' && $post->post_name != 'post_excerpt')
				{
					if($j==0){
						if($activ):
							if($key == '[#taxonomy_name#]'):
								echo '<div class="sec_title"><h3>'.$cur_post_type.__(' Information',DOMAIN).'</h3></div>';
							else:
								echo "<li><h3>".$key."</h3></li>";
							endif;
						endif;
						$j++;
					}
					if(isset($session[$post->post_name]) && $session[$post->post_name]!=""){
						if(get_post_meta($post->ID,"ctype",true) == 'multicheckbox')
						{
							foreach($session[$post->post_name] as $value)
							{
								$_value .= $value.",";	 
							}
							echo "<li><p>".$post->post_title.": ".substr($_value,0,-1)."</p></li>"; 
						}else
						{
		
							 echo "<li><p>".$post->post_title.": ".stripslashes($session[$post->post_name])."</p></li>";
						}
					}				
					if(get_post_meta($post->ID,"ctype",true) == 'upload')
					{
						$upload[] = $post->post_name;
					}
				}
			endwhile;
		}
	}
	echo "</ul>";
	echo "</div>";
}


/*************************** LOAD THE BASE CLASS *******************************
 * The Tmpl_WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('Tmpl_WP_List_Table')){
    include_once( WP_PLUGIN_DIR . '/Tevolution/templatic.php');
}
class custom_fields_list_table extends Tmpl_WP_List_Table
{
	/***** FETCH ALL THE DATA AND STORE THEM IN AN ARRAY *****
	* Call a function that will return all the data in an array and we will assign that result to a variable $custom_fields_data. FIRST OF ALL WE WILL FETCH DATA FROM POST META TABLE STORE THEM IN AN ARRAY $custom_fields_data */
	function fetch_custom_fields_data($post_id = '' ,$post_title = '')
	{ 
		$fields_label  = $post_title;
		$show_in_post_type = get_post_meta($post_id,"post_type",true);
		$is_edit = get_post_meta($post_id,"is_edit",true);
		$type = get_post_meta($post_id,"ctype",true);
		$html_var = get_post_meta($post_id,"htmlvar_name",true);
		$admin_desc = get_post_field('post_content', $post_id);
		$sort_order = get_post_meta($post_id,'sort_order', true);
		if(get_post_meta($post_id,"is_active",true))
		  {
			$active = 'Yes';
		  }	
		else
		  {
			$active = 'No';
		  }	
		if($is_edit =='true'){
			$edit_url = admin_url("admin.php?page=custom_fields&action=addnew&amp;field_id=$post_id");
		}else{ $edit_url ='#'; }
		
		/* Start WPML Language conde*/
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
		{
			global $wpdb, $sitepress_settings,$sitepress;			
			global $id, $__management_columns_posts_translations, $pagenow, $iclTranslationManagement;
			// get posts translations
            // get trids		
			// get trids		            		  
            $trids = $wpdb->get_col("SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_type='post_custom_fields' AND element_id IN (".$post_id.")");		 
            $ptrs = $wpdb->get_results("SELECT trid, element_id, language_code, source_language_code FROM {$wpdb->prefix}icl_translations WHERE trid IN (". join(',', $trids).")");		  
            foreach($ptrs as $v){
                $by_trid[$v->trid][] = $v;
            }		 
		 
		   foreach($ptrs as $v){			  
                if($v->element_id == $post_id){
                    $el_trid = $v->trid;
                    foreach($ptrs as $val){
                        if($val->trid == $el_trid){
                            $__management_columns_posts_translations[$v->element_id][$val->language_code] = $val;					   
                        }
                    }
                }
            }		  
		$country_url = '';		
		$active_languages = $sitepress->get_active_languages();
        	foreach($active_languages as $k=>$v){				
			if($v['code']==$sitepress->get_current_language()) continue;
			 $post_type = isset($_REQUEST['page']) ? $_REQUEST['page'] : 'custom_fields';						
			 if(isset($__management_columns_posts_translations[$id][$v['code']]) && $__management_columns_posts_translations[$id][$v['code']]->element_id){
				  // Translation exists
				 $img = 'edit_translation.png';
				 $alt = sprintf(__('Edit the %s translation',ADMINDOMAIN), $v['display_name']);				 
				 $link = 'admin.php?page='.$post_type.'&action=addnew&amp;field_id='.$__management_columns_posts_translations[$id][$v['code']]->element_id.'&amp;lang='.$v['code'];				 
				  
			  }else{
				   // Translation does not exist
				$img = 'add_translation.png';
				$alt = sprintf(__('Add translation to %s',ADMINDOMAIN), $v['display_name']);
                	$src_lang = $sitepress->get_current_language() == 'all' ? $sitepress->get_default_language() : $sitepress->get_current_language();				        					
                    $link = '?page='.$post_type.'&action=addnew&trid='.$post_id.'&amp;lang='.$v['code'].'&amp;source_lang=' . $src_lang;
			  }
			  
			  if($link){
				 if($link == '#'){
					icl_pop_info($alt, ICL_PLUGIN_URL . '/res/img/' .$img, array('icon_size' => 16, 'but_style'=>array('icl_pop_info_but_noabs')));                    
				 }else{
					$country_url.= '<a href="'.$link.'" title="'.$alt.'">';
					$country_url.= '<img style="padding:1px;margin:2px;" border="0" src="'.ICL_PLUGIN_URL . '/res/img/' .$img.'" alt="'.$alt.'" width="16" height="16" />';
					$country_url.= '</a>';
				 }
			  }			  
			}//finish foreach
		 
		 
		/*Finish WPML language code  */
		$meta_data = array(
			'ID'=> $post_id,
			'title'	=> '<strong><a href="'.$edit_url.'">'.$fields_label.'</a></strong><input type="hidden" name="custom_sort_order[]" value="' . esc_attr( $post_id ) . '" />',
			'icl_translations' => $country_url,
			'html_var' => $html_var,
			'show_in_post_type' 	=> $show_in_post_type,
			'type' => $type,
			'active' 	=> $active,
			'admin_desc' => $admin_desc
			);
		}else
		{
			$meta_data = array(
			'ID'=> $post_id,
			'title'	=> '<strong><a href="'.$edit_url.'">'.$fields_label.'</a></strong><input type="hidden" name="custom_sort_order[]" value="' . esc_attr( $post_id ) . '" />',			
			'show_in_post_type' 	=> $show_in_post_type,
			'html_var' => $html_var,
			'type' => $type,
			'active' 	=> $active,
			'admin_desc' => $admin_desc
			);
		}
		return $meta_data;
	}
	function custom_fields_data()
	{
		global $post, $paged, $query_args,$sitepress_settings,$sitepress;
		$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$per_page = get_option('posts_per_page');
		if(isset($_POST['s']) && $_POST['s'] != '')
		{
			$search_key = $_POST['s'];
			$args = array(
				'post_type' 		=> 'custom_fields',
				'suppress_filters' => false,
				'posts_per_page' 	=> $per_page,
				'post_status' 		=> array('publish'),
				'paged' 			=> $paged,
				's'					=> $search_key,
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value_num',
				'meta_value_num'=>'sort_order',
				'order' => 'ASC'
				
				);
		}
		else
		{
			$args = array(
				'post_type' 		=> 'custom_fields',
				'suppress_filters' => false,
				'posts_per_page' 	=> '-1',
				'paged' 			=> $paged,
				'post_status' 		=> array('publish'),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value_num',
				'meta_value_num'=>'sort_order',
				'order' => 'ASC'
				);
		}
		$post_meta_info = null;		
		add_filter('posts_join', 'custom_field_posts_where_filter');
		$post_meta_info = new WP_Query($args);		
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
				$custom_fields_data[] = $this->fetch_custom_fields_data($post->ID,$post->post_title);
		endwhile;
		remove_filter('posts_join', 'custom_field_posts_where_filter');
		return $custom_fields_data;
	}
	/* EOF - FETCH CUSTOM FIELDS DATA */
	
	/* DEFINE THE COLUMNS FOR THE TABLE */
	function get_columns()
	{	
		/*WPML lamguage translation plugin is active */
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
		{
			$country_flag = '';
			$languages = icl_get_languages('skip_missing=0');
			if(!empty($languages)){
				foreach($languages as $l){
					if(!$l['active']) echo '<a href="'.$l['url'].'">';
					if(!$l['active']) $country_flag .= '<img src="'.$l['country_flag_url'].'" height="12" alt="'.$l['language_code'].'" width="18" />'.' ';
					if(!$l['active']) echo '</a>';
				}
			}
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => __('Field name',ADMINDOMAIN),
				'icl_translations' => $country_flag,
				'show_in_post_type' => __('Shown in post-type',ADMINDOMAIN),
				'html_var' => __('Variable name',ADMINDOMAIN),
				'type' => __('Type',ADMINDOMAIN),
				'active' => __('Status',ADMINDOMAIN),
				'admin_desc' => __('Description',ADMINDOMAIN)
				);
		}else
		{
			$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Field name',ADMINDOMAIN),			
			'show_in_post_type' => __('Shown in post-type',ADMINDOMAIN),
			'html_var' => __('Variable name',ADMINDOMAIN),
			'type' => __('Type',ADMINDOMAIN),
			'active' => __('Active',ADMINDOMAIN),
			'admin_desc' => __('Description',ADMINDOMAIN)
			);
		}
		return $columns;
	}
	
	function process_bulk_action()
	{ 
		//Detect when a bulk action is being triggered...
		if('delete' == $this->current_action() )
		{
			 foreach($_REQUEST['checkbox'] as $postid)
			  {
				 wp_delete_post($postid);
			  }	 
			 $url = site_url().'/wp-admin/admin.php';
			 wp_redirect($url."?page=custom_fields&custom_field_msg=delsuccess");
			 exit;	
		}
	}
    
	function prepare_items()
	{
		$per_page = $this->get_items_per_page('custom_fields_per_page', 10);
		$columns = $this->get_columns(); /* CALL FUNCTION TO GET THE COLUMNS */
		$hidden = array();
		$sortable = array();
		$sortable = $this->get_sortable_columns(); /* GET THE SORTABLE COLUMNS */
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action(); /* FUNCTION TO PROCESS THE BULK ACTIONS */
		$data = $this->custom_fields_data(); /* RETIRIVE THE PACKAGE DATA */
		
		/* FUNCTION THAT SORTS THE COLUMNS */
		function usort_reorder($a,$b)
		{
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
			$result = strcmp(@$a[$orderby], @$b[$orderby]); //Determine sort order			
			return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
		}
		//if(is_array($data))
		//usort( $data, 'usort_reorder');
		
		$current_page = $this->get_pagenum(); /* GET THE PAGINATION */
		$total_items = count($data); /* CALCULATE THE TOTAL ITEMS */
		if(is_array($data))
		$this->found_data = array_slice($data,(($current_page-1)*$per_page),$per_page); /* TRIM DATA FOR PAGINATION*/
		$this->items = $this->found_data; /* ASSIGN SORTED DATA TO ITEMS TO BE USED ELSEWHERE IN CLASS */
		/* REGISTER PAGINATION OPTIONS */
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,      //WE have to calculate the total number of items
			'per_page'    => $per_page         //WE have to determine how many items to show on a page
		) );
	}
	
	/* To avoid the need to create a method for each column there is column_default that will process any column for which no special method is defined */
	function column_default( $item, $column_name )
	{
		switch( $column_name )
		{
			case 'cb':
			case 'title':
			case 'icl_translations':
			case 'show_in_post_type':
			case 'html_var':
			case 'type':
			case 'active':
			case 'admin_desc':
			return $item[ $column_name ];
			default:
			return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}
	
	/* DEFINE THE COLUMNS TO BE SORTED */
	function get_sortable_columns()
	{
		$sortable_columns = array(
			'title' => array('title',true),
			'show_in_post_type' => array('show_in_post_type',true)
			);
		return $sortable_columns;
	}
	
	function column_title($item)
	{
		$is_editable = get_post_meta($item['ID'],'is_edit',true);
		$is_deletable = get_post_meta($item['ID'],'is_delete',true);
		
			$action1 = array(
			'edit' => sprintf('<a href="?page=%s&action=%s&field_id=%s">Edit</a>',$_REQUEST['page'],'addnew',$item['ID'])
			);
		
		$action2 = array('delete' => sprintf('<a href="?page=%s&pagetype=%s&field_id=%s" onclick="return confirm(\'Are you sure for deleteing custom field?\')">Delete Permanently</a>','custom_fields','delete',$item['ID']));		
		$actions = array_merge($action1,$action2);
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions , $always_visible = false) );
	}
	
	function get_bulk_actions()
	{
		$actions = array(
			'delete' => 'Delete permanently'
			);
		return $actions;
	}
	
	function column_cb($item)
	{ 
		return sprintf(
			'<input type="checkbox" name="checkbox[]" id="checkbox[]" value="%s" />', $item['ID']
			);
	}
}
/*
Name :templ_searching_filter_where
decs : searching filter for custom fields return the where condition 
*/
add_filter('posts_where', 'templ_searching_filter_where');
function templ_searching_filter_where($where){
	if(is_search() && @$_REQUEST['adv_search'] ==1)
	{
		global $wpdb;
		$serch_post_types = $_REQUEST['post_type'];
		$s = get_search_query();
		$custom_metaboxes = get_search_post_fields_templ_plugin($serch_post_types,'','user_side','1');
		foreach($custom_metaboxes as $key=>$val) {
		$name = $key;
			if($_REQUEST[$name]){ 
				$value = $_REQUEST[$name];
				if($name == 'proprty_desc' || $name == 'event_desc'){
					$where .= " AND ($wpdb->posts.post_content like \"%$value%\" )";
				} else if($name == 'property_name'){
					$where .= " AND ($wpdb->posts.post_title like \"%$value%\" )";
				}else {
					$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$name' and ($wpdb->postmeta.meta_value like \"%$value%\" ))) ";
					/* Placed "AND" instead of "OR" because of Vedran said results are ignoring address field */
				}
			}
		}
		
		 /* Added for tags searching */
		if(is_search() && !@$_REQUEST['catdrop']){
			$where .= " OR  ($wpdb->posts.ID in (select p.ID from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p ,$wpdb->postmeta t where c.name like '".$s."' and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.ID = t.post_id and p.post_status = 'publish' group by  p.ID))";
		}
	}
	return $where;
}
/* Fetch posts which field type has heading type */
function fetch_heading_posts()
{
	global $wpdb,$post;
	remove_all_actions('posts_where');
	$heading_title = array();
	$args=
	array( 
	'post_type' => 'custom_fields',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => 'ctype',
			'value' => 'heading_type',
			'compare' => '=',
			'type'=> 'text'
		),
		array(
			'key' => 'is_active',
			'value' => '1',
			'compare' => '=',
			'type'=> 'text'
		)
	),
	'meta_key' => 'sort_order',
	'orderby' => 'meta_value_num',
	'meta_value_num'=>'sort_order',
	'order' => 'ASC'
	);
	$post_query = null;
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$post_query = new WP_Query($args);
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	$post_meta_info = $post_query;
	
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			$heading_title[$post->post_name] = $post->post_title;
		endwhile;
	}
	return $heading_title;
}
function fetch_heading_per_post_type($post_type)
{
	global $wpdb,$post;
	remove_all_actions('posts_where');
	$heading_title = array();
	$args=
	array( 
	'post_type' => 'custom_fields',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => 'ctype',
			'value' => 'heading_type',
			'compare' => '=',
			'type'=> 'text'
		),
		array(
			'key' => 'post_type',
			'value' => $post_type,
			'compare' => 'LIKE',
			'type'=> 'text'
		)
		
	),
	'meta_key' => 'sort_order',	
	'orderby' => 'meta_value_num',
	'meta_value_num'=>'sort_order',
	'order' => 'ASC'
	);
	$post_query = null;
	remove_all_actions('posts_orderby');
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$post_query = new WP_Query($args);
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	$post_meta_info = $post_query;
	
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
		$otherargs=
		array( 
		'post_type' => 'custom_fields',
		'posts_per_page' => -1	,
		'post_status' => array('publish'),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'is_active',
				'value' => '1',
				'compare' => '=',
				'type'=> 'text'
			),
			array(
				'key' => 'heading_type',
				'value' => $post->post_title,
				'compare' => '=',
				'type'=> 'text'
			)
		));
		$other_post_query = null;
		$other_post_query = new WP_Query($otherargs);
		if(count($other_post_query->post) > 0)
		  {
			$heading_title[$post->post_name] = $post->post_title;
		  }
		endwhile;
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	return $heading_title;
}
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
/* Add action for preview page */
/* Add action for preview map display */
add_action('templ_preview_address_map','templ_preview_address_map_display');
/*
 * Function Name:templ_preview_address_map_display
 * Return : Display the post preview detail map
 */
function templ_preview_address_map_display()
{	
	if(isset($_POST['address']))
	{
		 $add_str = @$_POST['address'];
		 $geo_latitude = $_POST['geo_latitude'];
	      $geo_longitude = $_POST['geo_longitude'];
		 $map_type=isset($_POST['map_view'])?$_POST['map_view']:'';		 
		?>
        <div class="row">
                <h3 class="submit_info_section"><span><?php _e('Map',DOMAIN); ?></span></h3>
                <div class="clearfix"></div>
				<p><strong><?php _e('Location',DOMAIN); echo $add_str;?>: </strong></p>
				<div id="gmap" class="graybox img-pad">
					<?php if($geo_longitude &&  $geo_latitude):
                            if($_SESSION["file_info"]):
                                foreach($_SESSION["file_info"] as $image_id=>$val):
                                    $thumb_image = get_template_directory_uri().'/images/tmp/'.$val;
                                    break;
                                endforeach;
                            endif;	
                            $pimg = $thumb_image;
                            if(!$pimg):
                                $pimg = get_template_directory_uri()."/images/img_not_available.png";
                            endif;	
                            $title = $post_title;
                            $address = $add_str;
							$link = get_permalink($post->ID);
                            require_once (TEMPL_MONETIZE_FOLDER_PATH . 'templatic-custom_fields/preview_map.php');
                            $retstr .= "<div class=\"google-map-info map-image\"><div class=map-inner-wrapper> <div class=map-item-info><div class=map-item-img><a href=\"\"><img src=\"$pimg\" width=\"150\" height=\"150\" alt=\"\" /></a></div>";
                            $retstr .= "<h6><a href=\"\" class=\"ptitle\" ><span>$title</span></a></h6>";
                            if($address){$retstr .= "<p class=address>$address</p>";}
							$retstr .= "</div></div></div>";
							
                            preview_address_google_map_plugin($geo_latitude,$geo_longitude,$retstr,$map_type);
                          else: ?>
                        <iframe src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $add_str;?>&amp;ie=UTF8&amp;z=14&amp;iwloc=A&amp;output=embed" height="358" width="100%" scrolling="no" frameborder="0" ></iframe>
                    <?php endif; ?>
				
				</div>
			</div>
        <?php
		
	}
}
/* add action for display preview detail page fields collectio */
add_action('tmpl_preview_page_fields_collection','tmpl_preview_detail_page_fields_collection_display');
function tmpl_preview_detail_page_fields_collection_display($cur_post_type)
{		
	if(is_active_addons('custom_fields_templates'))
	{
		$heading_type = fetch_heading_per_post_type($cur_post_type);
		if(count($heading_type) > 0)
		 {
			foreach($heading_type as $_heading_type)
			 {
				$post_meta_info = tmpl_show_on_detail($cur_post_type,$_heading_type); /* return fields selected for detail page  */
			 }
		 }
		else
		 {
			 $post_meta_info = tmpl_show_on_detail($cur_post_type,''); /* return fields selected for detail page  */
		 }
	}
	else{
		$post_meta_info = array();
	}
	
	if($post_meta_info)
	{ /* DISPLAY CUSTOM FIELDS VALUE */
		do_action('templatic_fields_onpreview',$_SESSION['custom_fields'],$cur_post_type);
	}	
	
	
}
/* Add Action for display the preview page post image gallery  */
add_action('tmpl_preview_page_gallery','tmpl_preview_detail_page_gallery_display');
function tmpl_preview_detail_page_gallery_display()
{
	?>
    <div>
    <?php
	$thumb_img_counter = 0;
	/* gallery begin */
		if($_SESSION["file_info"])
		{
			/*$post_type = get_post_type($post->ID);
			$post_type_object = get_post_type_object($post_type);
			$single_gallery_post_type = $post_type_object->labels->name; */
			$single_gallery_post_type=$_SESSION['custom_fields']['cur_post_type'];
			
			$thumb_img_counter = $thumb_img_counter+count($_SESSION["file_info"]);
			$image_path = get_image_phy_destination_path_plugin();
			$tmppath = "/".$upload_folder_path."tmp/";
			foreach($_SESSION["file_info"] as $image_id=>$val):
				$thumb_image = get_template_directory_uri().'/images/tmp/'.$val;
				break;
			endforeach;	
		 ?>
             <div class="content_details">
                 <div class="graybox">
                 <?php $f=0; foreach($_SESSION["file_info"] as $image_id=>$val):
				 		$curry = date("Y");
                        $currm = date("m");
                        $src = TEMPLATEPATH.'/images/tmp/'.$val;
						$img_title = pathinfo($val);									 
				  ?>
                    <?php if($largest_img_arr): ?>
                    		<?php  foreach($largest_img_arr as $value):
								 $name = end(explode("/",$value['file']));
								  if($val == $name):	
							?>
			               		<img src="<?php echo  $value['file'];?>" alt=""  width="700"/>
                        	<?php endif;
								endforeach;?>
                    <?php else: ?>
                        <img src="<?php echo $thumb_image;?>" alt=""   width="600"/>
                    <?php endif; ?>    
                  <?php if($f==0) break; endforeach;?>
                 </div>
             </div>
             <div class="submit_info_section">
                <h3><?php echo MORE_PHOTOS; echo " ";  echo __($single_gallery_post_type,DOMAIN); ?></h3>
              </div>
             <div id="gallery">
			 	<ul class="more_photos">
				 <?php
                    foreach($_SESSION["file_info"] as $image_id=>$val)
                    {
                        $curry = date("Y");
                        $currm = date("m");
                        $src = TEMPLATEPATH.'/images/tmp/'.$val;
						$img_title = pathinfo($val);						
                        if($val):
                        if(file_exists($src)):
                           		 $thumb_image = get_template_directory_uri().'/images/tmp/'.$val; ?>
                            	 <li><a href="<?php echo $thumb_image;?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $thumb_image;?>" alt="" height="70" width="70" title="<?php echo $img_title['filename'] ?>" /></a></li>
                        <?php else: ?>
                            <?php
								if($largest_img_arr):
                                foreach($largest_img_arr as $value):
                                    $name = end(explode("/",$value['file']));									
                                    if($val == $name):?>
                                    <li><a href="<?php echo $value['file']; ?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $value['file'];?>" alt="" height="70" width="70" title="<?php echo $img_title['filename'] ?>" /></a></li>
                            <?php
                                    endif;
                                endforeach;
								endif;
                            ?>
                        <?php endif; ?>
                        
                        <?php else: ?>
                        <?php if($thumb_img_arr): ?>
                            <?php 
                            $thumb_img_counter = $thumb_img_counter+count($thumb_img_arr);
                            for($i=0;$i<count($thumb_img_arr);$i++):
                                $thumb_image = $large_img_arr[$i];
								
								if(!is_array($thumb_image)):
                            ?>
                              <li><a href="<?php echo $thumb_image;?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $thumb_image;?>" alt="" height="70" width="70" title="<?php echo $img_title['filename'] ?>" /></a></li>
                              <?php endif;?>
                        <?php endfor; ?>
                        <?php endif; ?>	
                        <?php endif; ?>
                    <?php
                    $thumb_img_counter++;
                    } ?>
					</ul>
             </div>
	<?php }/* gallery end */?>
	    <div class="clearfix"></div>
    </div>
<?php
}
/*  Finish add action for preview page */
/* 
 * add action for display file upload custom field
 */
add_action('templ_preview_page_file_upload','templ_preview_page_file_upload_display');
function templ_preview_page_file_upload_display()
{
	global $upload;
	if($_FILES && $upload)
	{
		foreach($upload as $_upload)
		 {
			$upload_file[$_upload] = get_file_upload($_FILES[$_upload]);
			$_SESSION['upload_file'] = $upload_file;
		 }
	}
	?>
	<?php
	if(isset($_SESSION['upload_file']) && $_SESSION['upload_file'] != ""):
	   foreach($_SESSION['upload_file'] as $fileval):
		if($fileval):
	?>
		  <p><?php echo __('Click here to download File',ADMINDOMAIN); ?> <a href="<?php echo $fileval; ?>" class="normal_button main_btn"><?php echo __('Download',DOMAIN); ?></a></p>
	<?php
		endif;
	   endforeach;
	endif;	
}
/*
 * Advance search function 
 */
if(!is_admin())
{
	add_action('init', 'advance_search_template_function_',11);
}
function advance_search_template_function_(){
	add_action('pre_get_posts', 'advance_search_template_function',11);
	
	
}
function advance_search_template_function($query){		

	if(is_search() && (isset($_REQUEST['search_template']) && $_REQUEST['search_template']==1) )
	{		
		remove_all_actions('posts_where');
		do_action('advance_search_action');
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			 global $sitepress;
			 add_filter('posts_join', array($sitepress,'posts_join_filter'), 10, 2);
			 add_filter('posts_where', array($sitepress,'posts_where_filter'), 10, 2);
		}
		add_filter('posts_where', 'advance_search_template_where');	
				
	}else
	{
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			remove_filter('posts_join', 'wpml_search_language');
		}
	}
}
/*
 * Function Name: advance_search_template_where
 * Return : sql where 
 */
function advance_search_template_where($where)
{	
	
	if(isset($_REQUEST['search_template']) && $_REQUEST['search_template']==1 && is_search())
	{
		
		
		global $wpdb;
		$post_type=$_REQUEST['post_type'];		
		$tag_s=$_REQUEST['tag_s'];
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));

		if(isset($_REQUEST['todate']) && $_REQUEST['todate']!=''):
			$todate = trim($_REQUEST['todate']);		
		else:
			$todate ='';
		endif;
		if(isset($_REQUEST['frmdate']) && $_REQUEST['frmdate']!=''):
			$frmdate = trim($_REQUEST['frmdate']);
		else:
			$frmdate ='';
		endif;
		if(isset($_REQUEST['articleauthor']) && $_REQUEST['articleauthor']!=''):
			$articleauthor = trim($_REQUEST['articleauthor']);
		else:
			$articleauthor = '';
		endif;
		
		if(isset($_REQUEST['exactyes']) && $_REQUEST['exactyes']!=''):
			$exactyes = trim($_REQUEST['exactyes']);
		else:
			$exactyes ='';
		endif;
		
		if(isset($_REQUEST['todate']) && $_REQUEST['todate'] != ""){
			$todate = $_REQUEST['todate'];
			$todate= explode('/',$todate);
			$todate = $todate[2]."-".$todate[0]."-".$todate[1];
			
		}
		if(isset($_REQUEST['frmdate']) && $_REQUEST['frmdate'] != ""){
			$frmdate = $_REQUEST['frmdate'];
			$frmdate= explode('/',$frmdate);
			$frmdate = $frmdate[2]."-".$frmdate[0]."-".$frmdate[1];
		}
		
		if(is_plugin_active( 'Tevolution-Events/events.php') && (isset($_REQUEST['post_type']) && $_REQUEST['post_type']=='event'))
		{
			add_filter('posts_orderby', 'event_manager_filter_orderby',11);
		}
		
		if($todate!="" && $frmdate=="")
		{
			$where .= " AND   DATE_FORMAT($wpdb->posts.post_date,'%Y-%m-%d %G:%i:%s') >='".$todate."'";
		}
		else if($frmdate!="" && $todate=="")
		{
			
			$where .= " AND  DATE_FORMAT($wpdb->posts.post_date,'%Y-%m-%d %G:%i:%s') <='".$frmdate."'";
		}
		else if($todate!="" && $frmdate!="")
		{
			$where .= " AND  DATE_FORMAT($wpdb->posts.post_date,'%Y-%m-%d %G:%i:%s') BETWEEN '".$todate."' and '".$frmdate."'";
			
		}
		if($articleauthor!="" && $exactyes!=1)
		{
			$where .= " AND  $wpdb->posts.post_author in (select $wpdb->users.ID from $wpdb->users where $wpdb->users.display_name  like '".$articleauthor."') ";
		}
		if($articleauthor!="" && $exactyes==1)
		{
			$where .= " AND  $wpdb->posts.post_author in (select $wpdb->users.ID from $wpdb->users where $wpdb->users.display_name  = '".$articleauthor."') ";
		}		
		//search custom field
		if(isset($_REQUEST['search_custom']) && is_array($_REQUEST['search_custom']))
		{
			foreach($_REQUEST['search_custom'] as $key=>$value)
			{
				if($_REQUEST[$key]!="" && $key != 'category' && $key != 'st_date' && $key != 'end_date'  && $value!='slider_range' && $value!='multicheckbox' && $value!='min_max_range_select')
				{
					/* exclude category, start date, end date, slider range, multicheckbox field and include other custom fields type query where concate */
					if(!strstr($key,'_radio')) /*all custom field type query where concate except radio field */
					{
						if(is_array($_REQUEST[$key]))
						{
							foreach($_REQUEST[$key] as $val)
							{
									$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and ($wpdb->postmeta.meta_value like \"%$val%\" ))) ";
							}
						}
						else
						{
							if(strtolower($_REQUEST[$key])!='any'){
								$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and ($wpdb->postmeta.meta_value like \"%$_REQUEST[$key]%\" ))) ";					
							}
						}	
					}
					else /*only radio custom field query where concate */
					{
						$key_value = explode('_radio',$key);
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key_value[0]' and ($wpdb->postmeta.meta_value like \"%$_REQUEST[$key]%\" ))) ";	
					}
				}elseif($value=='slider_range' || $value=='min_max_range'){
					/*Rnage type custom field query where concate */
					if($value=='min_max_range'){						
						$min_value=trim($_REQUEST[$key.'_min']);
						$max_value=trim($_REQUEST[$key.'_max']);
					}else{
						$key_value = explode('-',$_REQUEST[$key]);
						$min_value=trim($key_value[0]);
						$max_value=trim($key_value[1]);
					}
					if($min_value!='' && $max_value!=''){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and ($wpdb->postmeta.meta_value >= $min_value and  $wpdb->postmeta.meta_value <= $max_value))) ";
					}elseif($min_value!=''){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and $wpdb->postmeta.meta_value >= $min_value)) ";
					}elseif($max_value!=''){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and $wpdb->postmeta.meta_value <= $max_value)) ";
					}

				}elseif($value=='min_max_range_select'){
					$min_value=trim($_REQUEST[$key.'_min']);
					$max_value=trim($_REQUEST[$key.'_max']);
					if($min_value!='' && $max_value!='' && strtolower($min_value)!='any' && strtolower($max_value)!='any'){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and ($wpdb->postmeta.meta_value >= $min_value and  $wpdb->postmeta.meta_value <= $max_value))) ";
					}elseif($min_value!='' && strtolower($min_value)!='any'){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and $wpdb->postmeta.meta_value >= $min_value)) ";
					}elseif($max_value!='' && strtolower($max_value)!='any'){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and $wpdb->postmeta.meta_value <= $max_value)) ";
					}
					
					
				}else{
					/*Multicheckbox custom field query where concate */
					if(!empty($_REQUEST[$key]) && $key != 'st_date' && $key != 'end_date' && $value!='slider_range'){
						$where.=" AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='".$key."' AND (";
						$count=count($_REQUEST[$key]);
						$c=1;	
						foreach($_REQUEST[$key] as $val){
							if($c<$count){
								$seprator='OR';	
							}else{
								$seprator='';	
							}
							$where .= "  ($wpdb->postmeta.meta_value like '%".$val."%' ) $seprator ";
							$c++;
						}						
						$where.=')))';
					}
				}
				
				if($_REQUEST[$key]!="" && $key == 'st_date' ){
					$templatic_current_tab = isset($event_manager_setting['templatic-current_tab'])? $event_manager_setting['templatic-current_tab']:'';
						if(!isset($_REQUEST['etype']))			
						{	
							$_REQUEST['etype']=($templatic_current_tab == '')?'current':$templatic_current_tab;
							$to_day = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
						}
						
						if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!=''){
							$where .= "  AND $wpdb->posts.post_title like '".$_REQUEST['sortby']."%'";
						}
						
						if(isset($_REQUEST['etype']) && $_REQUEST['etype']=='upcoming')
						{				
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_st_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') >'".$today."')) ";
						}			
						elseif(isset($_REQUEST['etype']) && $_REQUEST['etype']=='past')
						{				
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_end_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') < '".$today."')) ";
						}elseif($_REQUEST['etype']=='current')
						{
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= "  AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_st_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') <='".$today."')) AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_end_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') >= '".$today."')) ";
						}
					$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='st_date' and ($wpdb->postmeta.meta_value BETWEEN  '".$_REQUEST['st_date']."' AND '".$_REQUEST['end_date']."' ))) ";
				}
				if($_REQUEST[$key]!="" && $key == 'end_date'){
					 $templatic_current_tab = isset($event_manager_setting['templatic-current_tab'])? $event_manager_setting['templatic-current_tab']:'';
						if(!isset($_REQUEST['etype']))			
						{	
							$_REQUEST['etype']=($templatic_current_tab == '')?'current':$templatic_current_tab;
							$to_day = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
						}
						
						if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!=''){
							$where .= "  AND $wpdb->posts.post_title like '".$_REQUEST['sortby']."%'";
						}
						
						if(isset($_REQUEST['etype']) && $_REQUEST['etype']=='upcoming')
						{				
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_st_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') >'".$today."')) ";
						}			
						elseif(isset($_REQUEST['etype']) && $_REQUEST['etype']=='past')
						{				
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_end_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') < '".$today."')) ";
						}elseif($_REQUEST['etype']=='current')
						{
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= "  AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_st_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') <='".$today."')) AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_end_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') >= '".$today."')) ";
						}
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='end_date' and ($wpdb->postmeta.meta_value BETWEEN  '".$_REQUEST['st_date']."' AND '".$_REQUEST['end_date']."' ))) ";
				}
			}
		}
		//finish custom field			
		
		if(isset($_REQUEST['category']) && $_REQUEST['category']!="" &&  $_REQUEST['category'] !=0)
		{
			
			$scat = $_REQUEST['category'];
			$where .= " AND  $wpdb->posts.ID in (select $wpdb->term_relationships.object_id from $wpdb->term_relationships join $wpdb->term_taxonomy on $wpdb->term_taxonomy.term_taxonomy_id=$wpdb->term_relationships.term_taxonomy_id where $wpdb->term_taxonomy.taxonomy=\"$taxonomies[0]\" AND $wpdb->term_taxonomy.term_id=\"$scat\" ) ";
		}
		
		 /* Added for tags searching */
		if(is_search() && $_REQUEST['tag_s']!=""){
			$where .= " AND  ($wpdb->posts.ID in (select p.ID from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p ,$wpdb->postmeta t where c.name like '".$tag_s."' and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.ID = t.post_id and p.post_status = 'publish' group by  p.ID))";
		}
		
		return $where;
	}
   
	return $where;
}
function wpml_search_language($where)
{
	$language = ICL_LANGUAGE_CODE;
	$where .= " and t.language_code='".$language."'";
	return $where;
}
if(( @$_REQUEST['post'] ) && isset($_REQUEST['post'])){
	$post_type = get_post_type( @$_REQUEST['post'] );
}else{
	$post_type = '';
}
/*
 * Function Name: do_daily_schedule_expire_session
 * Return: execute post session expire daily once
 */
function do_daily_schedule_expire_session(){
	/////////////////Post EXPIRY SETTINGS CODING START/////////////////
	global $table_prefix,$wpdb,$table_name;
	$table_name = $table_prefix . "post_expire_session";
	$transection_db_table_name = $wpdb->prefix.'transactions'; 
	$current_date = date_i18n('Y-m-d',strtotime(date('Y-m-d')));	
	
	$today_executed = $wpdb->get_var("select session_id from $table_name where execute_date='".$current_date."'");	
	if($today_executed && $today_executed>0){
		//why blank?
	}else{ 
			$tmpdata = get_option('templatic_settings');
			$listing_email_notification = @$tmpdata['listing_email_notification'];
			if($listing_email_notification != ""){
				$number_of_grace_days = $listing_email_notification;
	
				$postid_str = $wpdb->get_results("select p.ID,p.post_author,p.post_date, p.post_title,t.payment_date,t.post_id from $wpdb->posts p,$transection_db_table_name t where p.ID = t.post_id and p.post_status='publish' AND t.package_type is NULL and datediff('".$current_date."',date_format(t.payment_date,'%Y-%m-%d')) = (select meta_value from $wpdb->postmeta pm where post_id=p.ID  and meta_key='alive_days')-$number_of_grace_days ");				
				
				foreach($postid_str as $postid_str_obj)
				{
					
					$ID = $postid_str_obj->ID;
					$paid_date = $wpdb->get_var("select payment_date from $transection_db_table_name t where post_id = '".$ID."' AND t.package_type is NULL order by t.trans_id DESC"); // change it to calculate expired day as per transactions
					$auth_id = $postid_str_obj->post_author;
					$post_author = $postid_str_obj->post_author;
					$post_date = date_i18n(get_option('date_format'),strtotime($postid_str_obj->post_date));
					$paid_on = date_i18n(get_option('date_format'),strtotime($paid_date));
					$post_title = $postid_str_obj->post_title;
					$userinfo = $wpdb->get_results("select user_email,display_name,user_login from $wpdb->users where ID=\"$auth_id\"");
					
					do_action('tmpl_post_expired_beforemail',$postid_str_obj);
					
					$user_email = $userinfo[0]->user_email;
					$display_name = $userinfo[0]->display_name;
					$user_login = $userinfo[0]->user_login;
					
					$fromEmail = get_site_emailId_plugin();
					$fromEmailName = get_site_emailName_plugin();
					$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
					$alivedays = get_post_meta($ID,'alive_days',true);
					$productlink = get_permalink($ID);
					$loginurl = get_tevolution_login_permalink();
					$siteurl = home_url();
					$client_message = $tmpdata['listing_expiration_content'];
					$search_array = array('[#user_login#]','[#post_link#]','[#post_title#]','[#post_date#]','[#transection_date#]','[#alivedays#]','[#days_left#]','[#site_login_url_link#]','[#user_login#]','[#user_email#]','[#site_name#]');
					$replace_array = array($display_name,$productlink,$post_title,$post_date,$paid_on,$alivedays,$number_of_grace_days,$loginurl,$user_login,$user_email,$store_name);
					$client_message=str_replace($search_array,$replace_array,$client_message);
					$subject = $tmpdata['listing_expiration_subject'];
					templ_send_email($fromEmail,$fromEmailName,$user_email,$display_name,$subject,$client_message,$extra='');
					do_action('tmpl_post_expired_aftermail');
				}
			}			
			
			$postid_str = $wpdb->get_var("select group_concat(p.ID),t.payment_date,t.post_id from $wpdb->posts p,$transection_db_table_name t where  p.ID = t.post_id and p.post_status='publish'  and datediff('".$current_date."',date_format(t.payment_date,'%Y-%m-%d')) = (select meta_value from $wpdb->postmeta pm where post_id=p.ID  and meta_key='alive_days')");
	
			if($postid_str)
			{
				$tmpdata = get_option('templatic_settings');
				$listing_ex_status = $tmpdata['post_listing_ex_status'];
				if($listing_ex_status=='')
				{
					$listing_ex_status = 'draft';
				}
				$wpdb->query("update $wpdb->posts set post_status=\"$listing_ex_status\" where ID in ($postid_str)");
			}
	
			$wpdb->query("insert into $table_name (execute_date,is_run) values ('".$current_date."','1')");
		
	}
}
add_action( 'daily_schedule_expire_session', 'do_daily_schedule_expire_session' );
add_action( 'init', 'tevolution_daily_schedule_expire_session' );
/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function tevolution_daily_schedule_expire_session() {	
	if ( ! wp_next_scheduled( 'daily_schedule_expire_session' ) ) {		
		wp_schedule_event( time(), 'daily', 'daily_schedule_expire_session');
	}
}
add_action('admin_init','tev_transaction_msg');
function tev_transaction_msg()
{
	
	add_action('tevolution_transaction_msg','tevolution_transaction_msg_fn');
	add_action('tevolution_transaction_mail','tevolution_transaction_mail_fn');
}
function tevolution_transaction_msg_fn()
{
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
				$payment_type = $orderinfo->payment_method;
				$payment_date =  date_i18n(get_option('date_format'),strtotime($orderinfo->payment_date));
				if(isset($_REQUEST['ostatus']) && @$_REQUEST['ostatus']!='')
					$trans_status = $wpdb->query("update $transection_db_table_name SET status = '".$_REQUEST['ostatus']."' where trans_id = '".$orderId."'");
				$user_detail = get_userdata($orderinfo->user_id); // get user details 
				$user_email = $user_detail->user_email;
				$user_login = $user_detail->display_name;
				$my_post['ID'] = $pid;
				
				if(isset($_REQUEST['action']) && $_REQUEST['action']== 'confirm')
				{
					$payment_status = APPROVED_TEXT;
					$status = 'publish';
				}
				elseif(isset($_REQUEST['action']) && $_REQUEST['action']== 'pending')
				{
					$payment_status = PENDING_MONI;
					$status = 'draft';
				}
				elseif(isset($_REQUEST['action']) && $_REQUEST['action']== 'cancel')
				{
					$payment_status = PENDING_MONI;
					$status = 'draft';
				}
				
				$my_post['post_status'] = $status;
				wp_update_post( $my_post );
				
				$to = get_site_emailId_plugin();
				$productinfosql = "select ID,post_title,guid,post_author from $wpdb->posts where ID = $pid";
				$productinfo = get_post($pid);
				$post_name = $productinfo->post_title;
				$post_type_mail = $productinfo->post_type;
				$transaction_details="";
				$transaction_details .= "-------------------------------------------------- <br/>\r\n";
				$transaction_details .= __('Payment Details for',DOMAIN).": ".$post_name."<br/>\r\n";
				$transaction_details .= "-------------------------------------------------- <br/>\r\n";
				$transaction_details .= __('Status',DOMAIN).": ".$payment_status."<br/>\r\n";
				$transaction_details .= __('Type',DOMAIN).": $payment_type <br/>\r\n";
				$transaction_details .= __('Date',DOMAIN).": $payment_date <br/>\r\n";
				$transaction_details .= "-------------------------------------------------- <br/>\r\n";
				$transaction_details = $transaction_details;
				$subject = get_option('post_payment_success_admin_email_subject');
				if(!$subject)
				{
					$subject = __("Payment Success Confirmation Email",DOMAIN);
				}
				$content = get_option('payment_success_email_content_to_admin');
				if(!$content){
				$content = __("<p>Howdy [#to_name#] ,</p><p>You have received a payment of [#payable_amt#] on [#site_name#]. Details are available below</p><p>[#transaction_details#]</p><p>Thanks,<br/>[#site_name#]</p>",DOMAIN);
				}
				$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
				$fromEmail = get_option('admin_email');
				$fromEmailName = stripslashes(get_option('blogname'));	
				$search_array = array('[#to_name#]','[#payable_amt#]','[#transaction_details#]','[#site_name#]');
				$replace_array = array($fromEmail,$payable_amount,$transaction_details,$store_name);
				$filecontent = str_replace($search_array,$replace_array,$content);
				@templ_send_email($fromEmail,$fromEmailName,$to,$user_login,$subject,$filecontent,''); // email to admin
				// post details
					$post_link = get_permalink($pid);
					$post_title = '<a href="'.$post_link.'">'.stripslashes($productinfo->post_title).'</a>'; 
					$aid = $productinfo->post_author;
					$userInfo = get_userdata($aid);
					$to_name = $userInfo->user_nicename;
					$to_email = $userInfo->user_email;
					$user_email = $userInfo->user_email;
				
				$transaction_details ="";
				$transaction_details .= __('Information Submitted URL',DOMAIN)." <br/>\r\n";
				$transaction_details .= "-------------------------------------------------- <br/>\r\n";
				$transaction_details .= "  $post_title <br/>\r\n";
				$transaction_details = __($transaction_details,DOMAIN);
				
				$subject = get_option('payment_success_email_subject_to_client');
				if(!$subject)
				{
					$subject = __("Payment Success Confirmation Email",DOMAIN);
				}
				$content = get_option('payment_success_email_content_to_client');
				if(!$content)
				{
					$content = __("<p>Hello [#to_name#]</p><p>Here's some info about your payment...</p><p>[#transaction_details#]</p><p>If you'll have any questions about this payment please send an email to [#admin_email#]</p><p>Thanks!,<br/>[#site_name#]</p>",DOMAIN);
				}
				
				$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
				$search_array = array('[#to_name#]','[#transaction_details#]','[#site_name#]','[#admin_email#]');
				$replace_array = array($to_name,$transaction_details,$store_name,get_option('admin_email'));
				$content = str_replace($search_array,$replace_array,$content);
				//@mail($user_email,$subject,$content,$headers);// email to client
				/* if user submits the free form then mail will not sent to them */
				if($orderinfo->payable_amt > 0)
				{
					templ_send_email($fromEmail,$fromEmailName,$user_email,$user_login,$subject,$content,$extra='');
				}
					
			}
		}
	}
}
/*
 * Function Name: tranaction_upgrade_post
 * Description : save data for upgrade post from transaction approved.
 */
add_action('tranaction_upgrade_post','tranaction_upgrade_post');
function tranaction_upgrade_post($orderId)
{
	$catids_arr = array();
	$my_post = array();
	$pid = $orderId; /* it will be use when going for RENEW */
	$upgrade_post = get_post_meta($pid,'upgrade_data',true);
	$last_postid = $pid;
	$alive_days = $upgrade_post['alive_days'];
	$payment_method = get_post_meta($last_postid,'upgrade_method',true);
	$coupon = @$upgrade_post['add_coupon'];
	$featured_type = @$upgrade_post['featured_type'];
	$payable_amount = @$upgrade_post['total_price'];
	$post_tax = fetch_page_taxonomy($upgrade_post['cur_post_id']);		

	/*delete custom fields */
	$heading_type = fetch_heading_per_post_type(get_post_type($last_postid));
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' =>get_post_type($last_postid),'public'   => true, '_builtin' => true ));
	$taxonomy = $taxonomies[0];
	if(count($heading_type) > 0)
	{
		foreach($heading_type as $_heading_type){
			$upgrade_custom_metaboxes[] = get_post_custom_fields_templ_plugin(get_post_type($last_postid),$upgrade_post['category'],$taxonomy,$_heading_type);//custom fields for custom post type..
		}
	}else{
		$upgrade_custom_metaboxes[] = get_post_custom_fields_templ_plugin($post_type,$upgrade_post['category'],$taxonomy,'');//custom fields for custom post type..
	}
	$terms = wp_get_post_terms( $last_postid, $taxonomy,  array("fields" => "ids") ); 
	
	if(count($heading_type) > 0)
	{
		foreach($heading_type as $_heading_type){
			$custom_metaboxes[] = get_post_custom_fields_templ_plugin(get_post_type($last_postid),$terms,$taxonomy,$_heading_type);//custom fields for custom post type..
		}
	}else{
		$custom_metaboxes[] = get_post_custom_fields_templ_plugin($post_type,$terms,$taxonomy,'');//custom fields for custom post type..
	}


	for($h=0;$h<count($heading_type);$h++)
	{
		$result[] = array_diff_key($custom_metaboxes[$h],$upgrade_custom_metaboxes[$h]);
	}

	for($r=0;$r<count($result);$r++)
	{
		$custom_fields_name = array_keys($result[$r]);
		for($i=0;$i<count($custom_fields_name);$i++)
		{
			$custom_fields_value = get_post_meta($last_postid,$custom_fields_name[$i],true);
			delete_post_meta($last_postid,$custom_fields_name[$i],$custom_fields_value);
		}
	}
	/**/
	/* Here array separated by category id and price amount */
	if($upgrade_post['category'])
	{
		$category_arr = $upgrade_post['category'];
		foreach($category_arr as $_category_arr)
		 {
			$category[] = explode(",",$_category_arr);
		 }
		foreach($category as $_category)
		 {
			 $post_category[] = $_category[0];
			 $category_price[] = $_category[1];
		 }
	}
	//exit;
	if($payable_amount <= 0)
	{	
		if($upgrade_post['package_select'] !='')
		{
			global $monetization;
			$post_default_status = $monetization->templ_get_packaget_post_status($current_user->ID, get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true));
			if($post_default_status =='recurring'){
				$post = get_post($custom_fields['cur_post_id']);
				
				$post_default_status = $monetization->templ_get_packaget_post_status($current_user->ID, $post->post_parent,'submit_post_type',true);
				if($post_default_status =='trash'){
					$post_default_status ='draft';
				}
			}
		}else{
			$post_default_status = fetch_posts_default_status();
		}
	}else
	{
		$post_default_status = 'draft';
	}
	
	
			$submit_post_type = get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true);
			$package_post=get_post_meta($upgrade_post['package_select'],'limit_no_post',true);
			//$user_limit_post=get_user_meta($current_user_id,$submit_post_type.'_list_of_post',true);
			$user_limit_post=get_user_meta($current_user_id,'total_list_of_post',true);
		
				//$limit_post=get_user_meta($current_user_id,$submit_post_type.'_list_of_post',true);
			
				update_post_meta($last_postid,'package_select',$upgrade_post['package_select']);				
				update_post_meta($last_postid,'paid_amount',$upgrade_post['total_price']);				
				$limit_post=get_user_meta($current_user_id,'total_list_of_post',true);				
				update_user_meta($current_user_id,$submit_post_type.'_list_of_post',$limit_post+1);
				update_user_meta($current_user_id,'total_list_of_post',$limit_post+1);
				update_user_meta($current_user_id,$submit_post_type.'_package_select',$upgrade_post['package_select']);
				update_user_meta($current_user_id,'package_selected',$upgrade_post['package_select']);
				
			foreach($upgrade_post as $key=>$val)
			{ 
				if($key != 'category' && $key != 'paid_amount' && $key != 'post_title' && $key != 'post_content' && $key != 'imgarr' && $key != 'Update' && $key != 'post_excerpt' && $key != 'alive_days')
				  { //echo $key; echo $val;
					if($key=='recurrence_bydays')
					{ 
						$val=implode(',',$val);
						update_post_meta($last_postid, $key, $val);
					}
					else
					{
						update_post_meta($last_postid, $key, $val);
					}
					
				  }
			}
			/* set post categories start */
			wp_set_post_terms( $last_postid,'',$post_tax,false);
			if($post_category){
			foreach($post_category as $_post_category)
			 { 
				if(taxonomy_exists($post_tax)):
					wp_set_post_terms( $last_postid,$_post_category,$post_tax,true);
				endif;
			 }
			} 
			/* set post categories end */
		
		 
		 /* Condition for Edit post */
			if( @$pid){
				$post_default_status = get_post_status($pid);
			}else{
				$post_default_status = 'publish';
			}
		
			if(class_exists('monetization')){
			
					global $monetization;
					$monetize_settings = $monetization->templ_set_price_info($last_postid,$pid,$payable_amount,$alive_days,$payment_method,$coupon,$featured_type);
	
			}
}
function tevolution_transaction_mail_fn()
{
	if(isset($_REQUEST['submit']) && $_REQUEST['submit'] !='')
	{
		$orderId = $_REQUEST['trans_id'];
		global $wpdb,$transection_db_table_name;
		$transection_db_table_name = $wpdb->prefix . "transactions";
		
		$ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
		$orderinfo = $wpdb->get_row($ordersql);
	
		$pid = $orderinfo->post_id;
		/* save post data while upgrade post from transaction listing */
		if(get_post_meta($pid,'upgrade_request',true) == 1 && (isset($_REQUEST['ostatus']) && $_REQUEST['ostatus'] == 1))
		{
			do_action('tranaction_upgrade_post',$pid); /* add an action to save upgrade post data. */
		}
		$payment_type = $orderinfo->payment_method;
		$payment_date =  date_i18n(get_option('date_format'),strtotime($orderinfo->payment_date));
		if(isset($_REQUEST['ostatus']) && @$_REQUEST['ostatus']!='')
			$trans_status = $wpdb->query("update $transection_db_table_name SET status = '".$_REQUEST['ostatus']."' where trans_id = '".$orderId."'");
		$user_detail = get_userdata($orderinfo->user_id); // get user details 
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
			$payment_status = APPROVED_TEXT;
		elseif(isset($_REQUEST['ostatus']) && $_REQUEST['ostatus']== 2)
			$payment_status = ORDER_CANCEL_TEXT;
		elseif(isset($_REQUEST['ostatus']) && $_REQUEST['ostatus']== 0)
			$payment_status = PENDING_MONI;
		$to = get_site_emailId_plugin();
		$productinfosql = "select ID,post_title,guid,post_author from $wpdb->posts where ID = $pid";
		$productinfo = get_post($pid);
	   $post_name = $productinfo->post_title;
	   $post_type_mail = $productinfo->post_type;
		$transaction_details="";
		$transaction_details .= "-------------------------------------------------- <br/>\r\n";
			$transaction_details .= __('Payment Details for Listing',DOMAIN).": $post_name <br/>\r\n";
			$transaction_details .= "-------------------------------------------------- <br/>\r\n";
			$transaction_details .= __('Status',DOMAIN).": ".$payment_status."<br/>\r\n";
			$transaction_details .= __('Type',DOMAIN).": $payment_type <br/>\r\n";
			$transaction_details .= __('Date',DOMAIN).": $payment_date <br/>\r\n";
			$transaction_details .= "-------------------------------------------------- <br/>\r\n";
			$transaction_details = $transaction_details;
			$subject = get_option('post_payment_success_admin_email_subject');
			if(!$subject)
			{
				$subject = __("Payment Success Confirmation Email",DOMAIN);
			}
			$content = get_option('payment_success_email_content_to_admin');
			if(!$content)
			{
				$content = __("<p>Howdy [#to_name#] ,</p><p>You have received a payment of [#payable_amt#] on [#site_name#]. Details are available below</p><p>[#transaction_details#]</p><p>Thanks,<br/>[#site_name#]</p>",DOMAIN);
			}
			$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
			$fromEmail = get_option('admin_email');
			$fromEmailName = stripslashes(get_option('blogname'));	
			$search_array = array('[#to_name#]','[#payable_amt#]','[#transaction_details#]','[#site_name#]');
			$replace_array = array($fromEmail,$payable_amount,$transaction_details,$store_name);
			$filecontent = str_replace($search_array,$replace_array,$content);
			@templ_send_email($fromEmail,$fromEmailName,$to,$user_login,$subject,$filecontent,''); // email to admin
			// post details
				$post_link = get_permalink($pid);
				$post_title = '<a href="'.$post_link.'">'.stripslashes($productinfo->post_title).'</a>'; 
				$aid = $productinfo->post_author;
				$userInfo = get_userdata($aid);
				$to_name = $userInfo->user_nicename;
				$to_email = $userInfo->user_email;
				$user_email = $userInfo->user_email;
			
			$transaction_details ="";
			$transaction_details .= __('Information Submitted URL',DOMAIN)." <br/>\r\n";
			$transaction_details .= "-------------------------------------------------- <br/>\r\n";
			$transaction_details .= "  $post_title <br/>\r\n";
			$transaction_details = $transaction_details;
			
			$subject = get_option('payment_success_email_subject_to_client');
			if(!$subject)
			{
				$subject = __("Payment Success Confirmation Email",DOMAIN);
			}
			$content = get_option('payment_success_email_content_to_client');
			if(!$content)
			{
				$content = __("<p>Hello [#to_name#]</p><p>Here's some info about your payment...</p><p>[#transaction_details#]</p><p>If you'll have any questions about this payment please send an email to [#admin_email#]</p><p>Thanks!,<br/>[#site_name#]</p>",DOMAIN);
			}
			$store_name = get_option('blogname');
			$search_array = array('[#to_name#]','[#transaction_details#]','[#site_name#]','[#admin_email#]');
			$replace_array = array($to_name,$transaction_details,$store_name,get_option('admin_email'));
			$content = str_replace($search_array,$replace_array,$content);
			//@mail($user_email,$subject,$content,$headers);// email to client
			templ_send_email($fromEmail,$fromEmailName,$user_email,$user_login,$subject,$content,$extra='');
	}
}
add_action('init','tev_success_msg');
function tev_success_msg(){
	add_action('tevolution_submition_success_msg','tevolution_submition_success_msg_fn');
}
function tevolution_submition_success_msg_fn(){
	global $wpdb;
	if(isset($_REQUEST['upgrade']) && $_REQUEST['upgrade'] !=''){
		$upgrade_data = get_post_meta($_REQUEST['pid'],'upgrade_data',true);
		$paymentmethod = get_post_meta($_REQUEST['pid'],'upgrade_method',true);
		$paidamount = $upgrade_data['total_price'];
		
		if($paidamount<=0)
		{
			$pid = $_REQUEST['pid']; /* it will be use when going for RENEW */
			$upgrade_post = get_post_meta($pid,'upgrade_data',true);
			$last_postid = $pid;
			$post_tax = fetch_page_taxonomy($upgrade_post['cur_post_id']);
			/* Here array separated by category id and price amount */
			if($upgrade_post['category'])
			{
				$category_arr = $upgrade_post['category'];
				foreach($category_arr as $_category_arr)
				 {
					$category[] = explode(",",$_category_arr);
				 }
				foreach($category as $_category)
				 {
					 $post_category[] = $_category[0];
					 $category_price[] = $_category[1];
				 }
			}
			/* set post categories start */
			wp_set_post_terms( $last_postid,'',$post_tax,false);
			if($post_category){
				foreach($post_category as $_post_category)
				{ 
					if(taxonomy_exists($post_tax)):
						wp_set_post_terms( $last_postid,$_post_category,$post_tax,true);
					endif;
				}
			}
			/* set post categories end */
		}
	}else{
		$paymentmethod = get_post_meta($_REQUEST['pid'],'paymentmethod',true);
		$paidamount = get_post_meta($_REQUEST['pid'],'paid_amount',true);
		
	}

	if($paidamount !='')
		$paid_amount = display_amount_with_currency_plugin( number_format($paidamount, 2 ) );
	
	
	$permalink = get_permalink($_REQUEST['pid']);
	$RequestedId = $_REQUEST['pid'];
	
	$tmpdata = get_option('templatic_settings');
	
	if($paymentmethod == 'prebanktransfer'){
		$post_default_status = 'draft';
	}else{
		$post_default_status = $tmpdata['post_default_status'];
	}
	
	$post_status = $wpdb->get_var("select $wpdb->posts.post_status from $wpdb->posts where $wpdb->posts.ID = ".$_REQUEST['pid']);
	$suc_post = get_post($_REQUEST['pid']);
	
	if($post_default_status == 'publish' && $post_status == 'publish'){
		//$post_link = get_permalink($_REQUEST['pid']);
		$post_link = "<a href='".get_permalink($_REQUEST['pid'])."'>".__("Click here",DOMAIN)."</a> ".__('for a preview of the submitted content.',DOMAIN);
	}else{
		$post_link ='';
	}
	$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
	
	if($paymentmethod == 'prebanktransfer')
	{
		$paymentupdsql = "select option_value from $wpdb->options where option_name='payment_method_".$paymentmethod."'";
		$paymentupdinfo = $wpdb->get_results($paymentupdsql);
		$paymentInfo = unserialize($paymentupdinfo[0]->option_value);
		$payOpts = $paymentInfo['payOpts'];
		$bankInfo = $payOpts[0]['value'];
		$accountinfo = $payOpts[1]['value'];
	}
	$orderId = $_REQUEST['pid'];
	$siteName = "<a href='".site_url()."'>".$store_name."</a>";
	$search_array = array('[#post_type#]','[#payable_amt#]','[#bank_name#]','[#account_number#]','[#submition_Id#]','[#store_name#]','[#submited_information_link#]','[#site_name#]');
	$replace_array = array($suc_post->post_type,$paid_amount,@$bankInfo,@$accountinfo,$orderId,$store_name,$post_link,$siteName);
	$transaction = $wpdb->prefix."transactions";
	$fetch_status = $wpdb->get_var("select status from $transaction t where post_id=$orderId order by t.trans_id DESC");
	$posttype_obj = get_post_type_object($suc_post->post_type);
	$post_lable = ( @$posttype_obj->labels->menu_name ) ? strtolower( @$posttype_obj->labels->menu_name ) :  strtolower( $posttype_obj->labels->singular_name );
	$theme_settings = get_option('templatic_settings');
	if($fetch_status)
	{
		$filecontent = stripslashes($theme_settings['post_added_success_msg_content']);
		if(!$filecontent){
			$filecontent = POST_SUCCESS_MSG;
		}
	}
	elseif($_SESSION['custom_fields']['action']=='edit' && !isset($_REQUEST['upgrade'])){
		$filecontent = sprintf(__('<p class="sucess_msg_prop">Thank you for submitting your %s at our site, your %s request has been updated successfully.</p><p>[#submited_information_link#]</p>',DOMAIN),$suc_post->post_type,$suc_post->post_type); 
	}elseif($paymentmethod == 'prebanktransfer' && $_SESSION['custom_fields']['action']!='edit'){
		if (function_exists('icl_register_string')) 
		{
			$filecontent = icl_t(DOMAIN, 'post_pre_bank_trasfer_msg_content',get_option('post_pre_bank_trasfer_msg_content'));
		}
		else
		{
			$filecontent = stripslashes($theme_settings['post_pre_bank_trasfer_msg_content']);
		}
		if(!$filecontent){
			$filecontent = POST_POSTED_SUCCESS_PREBANK_MSG;
		}
	}else{
		$filecontent = stripslashes($theme_settings['post_added_success_msg_content']);
		if(!$filecontent){
			$filecontent = POST_SUCCESS_MSG;
		}
	}	
	$filecontent = str_replace($search_array,$replace_array,$filecontent); 
	echo $filecontent;
}
/* add feature listing options */
add_action('init','tevolution_add_featured_fn1');
function tevolution_add_featured_fn1(){
	add_action('tevolution_featured_list','tevolution_featured_list_fn');
}
/* Function : tevolution_show_term_and_condition
   Desc : to display terms and conditions checkbox
*/
function tevolution_show_term_and_condition()
{
	$tmpdata = get_option('templatic_settings');
	if(isset($tmpdata['tev_accept_term_condition']) && $tmpdata['tev_accept_term_condition'] != "" && $tmpdata['tev_accept_term_condition'] == 1){	?>
			<div class="form_row clearfix">
             	<label>&nbsp;
             	 <input name="term_and_condition" id="term_and_condition" value="" type="checkbox" class="chexkbox" onclick="hide_error()"/>
                 <?php if(isset($tmpdata['term_condition_content']) && $tmpdata['term_condition_content']!=''){
						echo stripslashes($tmpdata['term_condition_content']); 
				 }else{
					_e('Accept terms and conditions.',DOMAIN);
				 }?></label>
				 <span class="error message_error2" id="terms_error"></span>
            </div>
            <script type="text/javascript">
			  function hide_error(){
				if(document.getElementById('term_and_condition').checked)
				{
					document.getElementById('terms_error').innerHTML  = '';
				}
			  }
              function check_term_condition()
			  {
				if(document.getElementById('term_and_condition'))
				{
					if(document.getElementById('term_and_condition').checked)
					{	
						return true;
					}else
					{
						//alert('<?php _e('Please accept Term and Conditions',DOMAIN);?>');
						document.getElementById('terms_error').innerHTML  = '<?php _e("Please accept Term and Conditions.",DOMAIN);?>';
						return false; // add comment return false nothing add and directoly submit then only term condition error will be shown
					}
				}
			  }
            </script>
    <?php global $submit_button;
		$submit_button = 'onclick="return check_term_condition();"';
	}
}
/*
 * Function Name: tevolution_submition_success_post_submited_content
 * Return: display the submited post information
 */
add_action('tevolution_submition_success_post_content','tevolution_submition_success_post_submited_content');
function tevolution_submition_success_post_submited_content()
{
	?>
     <!-- Short Detail of post -->
	<div class="submit_info_section sis_on_submitinfo">
		<h3><?php _e(POST_DETAIL,DOMAIN);?></h3>
	</div>
    <div class="submited_info">
	<?php
	global $wpdb,$post,$current_user;
	remove_all_actions('posts_where');
	$cus_post_type = get_post_type($_REQUEST['pid']);
	$args = 
	array( 'post_type' => 'custom_fields',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
	   'relation' => 'AND',
		array(
			'key' => 'post_type_'.$cus_post_type.'',
			'value' => $cus_post_type,
			'compare' => '=',
			'type'=> 'text'
		),
		array(
			'key' => 'show_on_page',
			'value' =>  array('user_side','both_side'),
			'compare' => 'IN'
		),
		array(
			'key' => 'is_active',
			'value' =>  '1',
			'compare' => '='
		),
		array(
			'key' => 'show_on_success',
			'value' =>  '1',
			'compare' => '='
		)
	),
		'meta_key' => 'sort_order',
		'orderby' => 'meta_value_num',
		'order' => 'ASC'
	);
	$post_query = null;
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$post_meta_info = new WP_Query($args);	
	
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	$suc_post = get_post($_REQUEST['pid']);
	
	$paidamount = get_post_meta($_REQUEST['pid'],'paid_amount',true);
	$success_post_type_object = get_post_type_object($suc_post->post_type);
	$success_post_title = $success_post_type_object->labels->menu_name;
		if($post_meta_info)
		  {
			echo "<div class='grid02 rc_rightcol clearfix'>";
			echo "<ul class='list'>";
			//echo "<li><p>Post Title : </p> <p> ".stripslashes($suc_post->post_title)."</p></li>";
			printf( __( '<li><p class="submit_info_label">Title:</p> <p class="submit_info_detail"> %s </p></li>', DOMAIN ),  stripslashes($suc_post->post_title)  ); 
			
			while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
				$post->post_name=get_post_meta(get_the_ID(),'htmlvar_name',true);
				$htmlvar_name = get_post_meta($post->ID,"htmlvar_name",true);							
				if(get_post_meta($post->ID,"ctype",true) == 'post_categories')
				{
					$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $suc_post->post_type,'public'   => true, '_builtin' => true ));	
					
					$category_name = wp_get_post_terms($_REQUEST['pid'], $taxonomies[0]);
					if($category_name)
					{
						$_value = '';
						
						foreach($category_name as $value)
						 {
							$_value .= $value->name.",";
						 }
						 echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'> ".substr($_value,0,-1)."</p></li>";
						
					}
					 do_action('tmpl_on_success_after_categories');
				}
				if(get_post_meta($post->ID,"ctype",true) == 'heading_type' )
				  {
					
					 echo "<li><h3>".stripslashes($post->post_title)." </h3></li>";
					  do_action('tmpl_on_success_after_heading');
				  }
				if(get_post_meta($_REQUEST['pid'],$post->post_name,true))
				  {
					if(get_post_meta($post->ID,"ctype",true) == 'multicheckbox' )
					  {
						$_value = '';
							
						$option_values = explode(",",get_post_meta($post->ID,'option_values',true));				
						$option_titles = explode(",",get_post_meta($post->ID,'option_title',true));
						$field=get_post_meta($_REQUEST['pid'],$post->post_name,true);						
						$checkbox_value='';
						for($i=0;$i<count($option_values);$i++){
							if(in_array($option_values[$i],$field)){
								if($option_titles[$i]!=""){
									$checkbox_value .= $option_titles[$i].',';
								}else{
									$checkbox_value .= $option_values[$i].',';
								}
							}
						}						
						 echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'> ".substr($checkbox_value,0,-1)."</p></li>";
						 do_action('tmpl_on_success_after_'.$htmlvar_name,$post->ID);
					  }
					  
					
					elseif(get_post_meta($post->ID,"ctype",true) == 'radio')
					{
						$option_values = explode(",",get_post_meta($post->ID,'option_values',true));				
						$option_titles = explode(",",get_post_meta($post->ID,'option_title',true));
						for($i=0;$i<count($option_values);$i++){
							if(get_post_meta($_REQUEST['pid'],$post->post_name,true) == $option_values[$i]){
								if($option_titles[$i]!=""){
									$rado_value = $option_titles[$i];
								}else{
									$rado_value = $option_values[$i];
								}
								echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'> ".$rado_value."</p></li>";
							}
						}
					}else
					 {
						 $custom_field=stripslashes(get_post_meta($_REQUEST['pid'],$post->post_name,true));
						 if(substr($custom_field, -4 ) == '.jpg' || substr($custom_field, -4 ) == '.png' || substr($custom_field, -4 ) == '.gif' || substr($custom_field, -4 ) == '.JPG' 
										|| substr($custom_field, -4 ) == '.PNG' || substr($custom_field, -4 ) == '.GIF'){
							  echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'> <img src='".$custom_field."'  width='200'/></p></li>";
						 }							 
						 else
						 {
						   if(get_post_meta($post->ID,'ctype',true) == 'upload')
							{
							  echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'>".__('Click here to download File',ADMINDOMAIN)."<a href=".get_post_meta($_REQUEST['pid'],$post->post_name,true).">Download</a></p></li>";
							}
						   else
							{
							 if(get_post_meta($post->ID,"ctype",true) == 'texteditor'){
								 echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p> ".get_post_meta($_REQUEST['pid'],$post->post_name,true)."</p></li>"; 
							 }else{
							 	 echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'> ".get_post_meta($_REQUEST['pid'],$post->post_name,true)."</p></li>";
							 }
							}
						 }
					 }
				  }
					if($post->post_name == 'post_content' && $suc_post->post_content!='')
					 {
						$suc_post_con = $suc_post->post_content;
					 }
					if($post->post_name == 'post_excerpt' && $suc_post->post_excerpt!='')
					 {
						$suc_post_excerpt = $suc_post->post_excerpt;
					 }
					if(get_post_meta($post->ID,"ctype",true) == 'geo_map')
					 {
						$add_str = get_post_meta($_REQUEST['pid'],'address',true);
						$geo_latitude = get_post_meta($_REQUEST['pid'],'geo_latitude',true);
						$geo_longitude = get_post_meta($_REQUEST['pid'],'geo_longitude',true);
						$map_view = get_post_meta($_REQUEST['pid'],'map_view',true);
					 }
					 
					 do_action('tmpl_on_success_after_'.$htmlvar_name);
			endwhile;
			if(get_post_meta($_REQUEST['pid'],'package_select',true))
			{
					$package_name = get_post(get_post_meta($_REQUEST['pid'],'package_select',true));
					if (function_exists('icl_register_string')) {									
						$package_name->post_title = icl_t('tevolution-price', 'package-name'.$package_name->ID,$package_name->post_title);
					}
					$package_type = get_post_meta($package_name->ID,'package_type',true);
					if($package_type  ==2){
						$pkg_type = __('Pay per subscription',DOMAIN); 
					}else{ 
						$pkg_type = __('Pay per post',DOMAIN); 
					} ?>
					<li><p class="submit_info_label"><?php _e('Package Type',DOMAIN);?>: </p> <p class="submit_info_detail"> <?php echo $pkg_type;?></p></li>
				 
<?php
			}
			if(get_post_meta($_REQUEST['pid'],'alive_days',true))
			{
				 echo "<li><p class='submit_info_label'>"; _e('Validity',DOMAIN); echo ": </p> <p class='submit_info_detail'> ".get_post_meta($_REQUEST['pid'],'alive_days',true).' '; _e('Days',DOMAIN); echo "</p></li>";
			}
			if(get_user_meta($suc_post->post_author,'list_of_post',true))
			{
				 echo "<li><p class='submit_info_label'>"; _e('Number of Posts',DOMAIN).": </p> <p class='submit_info_detail'> ".get_user_meta($suc_post->post_author,'list_of_post',true)."</p></li>";
			}
			if(get_post_meta(get_post_meta($_REQUEST['pid'],'package_select',true),'recurring',true))
			{
				$package_name = get_post(get_post_meta($_REQUEST['pid'],'package_select',true));
				 echo "<li><p class='submit_info_label'>"; _e('Recurring Charges',DOMAIN).": </p> <p class='submit_info_detail'> ".fetch_currency_with_position(get_post_meta($_REQUEST['pid'],'paid_amount',true))."</p></li>";
			}
			if(is_active_addons('monetization') && $paidamount > 0){
				fetch_payment_description($_REQUEST['pid']);
			}
			echo "</ul>";
			echo "</div>";
		  }		 
		do_action('after_tevolution_success_msg');
	?>
	</div>
	<?php if(isset($suc_post_con)): ?>
	    <div class="row">
		  <div class="twelve columns">
			  <div class="title_space">
				 <div class="submit_info_section">
					<h3><?php _e('Post Description', DOMAIN);?></h3>
				 </div>
				 <p><?php echo nl2br($suc_post_con); ?></p>
			  </div>
		   </div>
	    </div>
	<?php endif; ?>
	
	<?php if(isset($suc_post_excerpt)): ?>
		 <div class="row">
			<div class="twelve columns">
				<div class="title_space">
					<div class="submit_info_section">
						<h3><?php _e('Post Excerpt',DOMAIN);?></h3>
					</div>
					<p><?php echo nl2br($suc_post_excerpt); ?></p>
				</div>
			</div>
		</div>
	<?php endif; ?>
	
	<?php
	if(@$add_str)
	{
	?>
		<div class="row">
			<div class="title_space">
				<div class="submit_info_section">
					<h3><?php _e('Map',DOMAIN); ?></h3>
				</div>
				<p><strong><?php _e('Location',DOMAIN); echo " : "; echo $add_str;?></strong></p>
			</div>
			<div id="gmap" class="graybox img-pad">
				<?php if($geo_longitude &&  $geo_latitude): 
						$pimgarr = bdw_get_images_plugin($_REQUEST['pid'],'thumb',1);
						$contact = get_post_meta($_REQUEST['pid'],'phone',true);
						$website = get_post_meta($_REQUEST['pid'],'website',true);
						
						$pimg = $pimgarr[0]['file'];
						if(!$pimg):
							$pimg = plugin_dir_url( __FILE__ )."images/img_not_available.png";
						endif;	
						$title = stripslashes($suc_post->post_title);
						$address = $add_str;
						require_once (TEMPL_MONETIZE_FOLDER_PATH . 'templatic-custom_fields/preview_map.php');
						$retstr ="";
						$link = get_permalink($_REQUEST['pid']);
						$retstr .= "<div class=\"google-map-info map-image\"><div class=map-inner-wrapper><div class=map-item-info><div class=map-item-img><a href=\"$link\"><img src=\"$pimg\" width=\"150\" height=\"150\" alt=\"\" /></a></div>";
                              $retstr .= "<h6><a href=\'".get_permalink($_REQUEST['pid'])."\' class=\"ptitle\" ><span>$title</span></a></h6>";
                              if($address){$retstr .= "<p class=address>$address</p>";}
						if($contact){$retstr .= '<p class=contact>'.$contact.'</p>';}
						if($website){$retstr .= '<p class=website><a href= '.$website.'>'.$website.'</a></p>';}
						$retstr .= "</div></div></div>";
						
						
						preview_address_google_map_plugin($geo_latitude,$geo_longitude,$retstr,$map_view);
					  else:
				?>
						<iframe src="//maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $add_str;?>&amp;ie=UTF8&amp;z=14&amp;iwloc=A&amp;output=embed" height="358" width="100%" scrolling="no" frameborder="0" ></iframe>
				<?php endif; ?>
			</div>
		</div>
	<?php } ?>
	
	
	<!-- End Short Detail of post -->
     <?php
}
function tmpl_widget_wpml_filter(){
	global $wpdb;
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		$language = ICL_LANGUAGE_CODE;
		$where = " AND t.language_code='".$language."'";
	}
	return $where;
}
add_action( 'admin_init','tevolution_custom_menu_class' );
function tevolution_custom_menu_class() 
{
    global $menu;
	$my_menu = array();
	$custom_post_types = get_option("templatic_custom_post");
	if(!empty($custom_post_types))
	{
		foreach ($custom_post_types as $content_type=>$content_type_label) {	
			if(isset($content_type_label['labels']['menu_name']) && $content_type_label['labels']['menu_name']!='')
				$my_menu[] = $content_type_label['labels']['menu_name'];
				
		}
	}
	if(!empty($menu))
	{
		foreach( $menu as $key => $value )
		{
			if( in_array($value[0], $my_menu)){
				$menu[$key][4] .= " tevolution-custom-icon";
			}
		}
	}
}
/** add favourites class to body*/
add_filter('body_class','directory_favourites_class',11,2);
function directory_favourites_class($classes,$class){
	if(isset($_GET['sort']) && $_GET['sort'] =='favourites'){
			$classes[] .= " tevolution-favoutites";
	}
	return $classes;
}
/*
 * Function Name: tevolution_images_box
 * Return: 
 */
function tevolution_images_box($post){
	?>
	<div id="images_gallery_container">
		<ul class="images_gallery">          
			<?php
			
				if(function_exists('bdw_get_images_plugin'))
				{
					$post_image = bdw_get_image_gallyer_plugin($post->ID,'thumbnail');					
				}
				$image_gallery='';
				foreach($post_image as $image){					
					echo '<li class="image" data-attachment_id="' . $image['id'] . '">
							' . wp_get_attachment_image( $image['id'], 'thumbnail' ) . '
							<ul class="actions">
								<li><a href="#" id="'.$image['id'].'" class="delete" title="' . __( 'Delete image', DOMAIN ) . '">' . __( 'Delete', DOMAIN ) . '</a></li>
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
			// Uploading files
			var image_gallery_frame;
			var $image_gallery_ids = jQuery('#tevolution_image_gallery');
			var $images_gallery = jQuery('#images_gallery_container ul.images_gallery');
			jQuery('.add_tevolution_images').on( 'click', 'a', function( event ) {
				var $el = $(this);
				var attachment_ids = $image_gallery_ids.val();
				event.preventDefault();
				// If the media frame already exists, reopen it.
				if ( image_gallery_frame ) {
					image_gallery_frame.open();
					return;
				}
				// Create the media frame.
				image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
					// Set the title of the modal.
					title: '<?php echo __( 'Add images gallery', ADMINDOMAIN ); ?>',
					button: {
						text: '<?php echo __( 'Add to gallery', ADMINDOMAIN ); ?>',
					},
					multiple: true
				});
				// When an image is selected, run a callback.
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
										<li><a href="#" class="delete" title="<?php echo __( 'Delete image', ADMINDOMAIN ); ?>"><?php echo __( 'Delete', ADMINDOMAIN); ?></a></li>\
									</ul>\
								</li>');
						}
					} );
					$image_gallery_ids.val( attachment_ids );
				});
				// Finally, open the modal.
				image_gallery_frame.open();
			});
			// Image ordering
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
			// Remove images
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
add_action('wp_ajax_delete_gallery_image','delete_gallery_image');
function delete_gallery_image(){
	wp_delete_post($_REQUEST['image_id'],true);
	echo '1';
	exit;
}
add_action('wp_ajax_custom_field_sortorder','tevolution_custom_field_sortorder');
function tevolution_custom_field_sortorder(){
	
	$user_id = get_current_user_id();	
	if(isset($_REQUEST['paging_input']) && $_REQUEST['paging_input']!=0 && $_REQUEST['paging_input']!=1){
		$custom_fields_per_page=get_user_meta($user_id,'custom_fields_per_page',true);
		$j =$_REQUEST['paging_input']*$custom_fields_per_page+1;
		$test='';
		$i=$custom_fields_per_page;		
		for($j; $j >= count($_REQUEST['custom_sort_order']);$j--){			
			if($_REQUEST['custom_sort_order'][$i]!=''){
				update_post_meta($_REQUEST['custom_sort_order'][$i],'sort_order',$j);	
			}
			$i--;	
		}
	}else{
		$j=1;
		for($i=0;$i<count($_REQUEST['custom_sort_order']);$i++){
			update_post_meta($_REQUEST['custom_sort_order'][$i],'sort_order',$j);		
			$j++;
		}
	}	
	exit;
}

/*
 * Function Name: tevolution_search_custom_field_sortorder
 * Return : search form sort order
 */
add_action('wp_ajax_search_custom_field_sortorder','tevolution_search_custom_field_sortorder');
function tevolution_search_custom_field_sortorder(){
	
	$user_id = get_current_user_id();	
	if(isset($_REQUEST['paging_input']) && $_REQUEST['paging_input']!=0 && $_REQUEST['paging_input']!=1){
		$custom_fields_per_page=get_user_meta($user_id,'custom_fields_per_page',true);
		$j =$_REQUEST['paging_input']*$custom_fields_per_page+1;
		$test='';
		$i=$custom_fields_per_page;		
		for($j; $j >= count($_REQUEST['custom_sort_order']);$j--){			
			if($_REQUEST['custom_sort_order'][$i]!=''){
				update_post_meta($_REQUEST['custom_sort_order'][$i],'search_sort_order',$j);	
			}
			$i--;	
		}
	}else{
		$j=1;
		for($i=0;$i<count($_REQUEST['custom_sort_order']);$i++){
			update_post_meta($_REQUEST['custom_sort_order'][$i],'search_sort_order',$j);		
			$j++;
		}
	}	
	exit;
}

if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'preview')
{
	add_filter( 'wp_title', 'preview_page_title' );
}
function preview_page_title()
{
	echo sprintf(__("%s preview page",DOMAIN),ucfirst($_REQUEST['cur_post_type']));
}
/*
 * Function Name: bdw_get_image_gallyer_plugin
 * Fetch all images for particular post on backend
 */
function bdw_get_image_gallyer_plugin($iPostID,$img_size='thumb',$no_images='') 
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
function callback_on_footer_fn(){ ?>
	<script type="text/javascript">
		jQuery.noConflict();
		var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
		var is_safari = navigator.userAgent.indexOf("Safari") > -1;
		if ((is_chrome)&&(is_safari)) {is_safari=false;}
		if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
			jQuery("#safari_error").html("<?php _e("Safari will allow you to upload only one image, so we suggest you use some other browser.",DOMAIN);?>");
		}
	</script>
<?php }


/* 
 * Function Name: tevolution_post_detail_after_singular
 * Return: display the post related custom fields display
 */
add_action("single_post_custom_fields",'tevolution_post_detail_after_singular');
function tevolution_post_detail_after_singular()
{
	if((is_single() || is_archive()) && get_post_type()=='post'){		
		global $post;
			$post_type= get_post_type();
			$cus_post_type = get_post_type($post->ID);
			$PostTypeObject = get_post_type_object($cus_post_type);
			$PostTypeLabelName = $PostTypeObject->labels->name;
			
			$heading_type = fetch_heading_per_post_type(get_post_type());
			wp_reset_query();
			if(count($heading_type) > 0)
			{
				foreach($heading_type as $_heading_type)
				{	
					if(is_single()){
						$custom_metaboxes[$_heading_type] = get_post_custom_fields_templ_plugin($post_type,'','',$_heading_type);//custom fields for custom post type..
					}
					if(is_archive()){
						$post_meta_info = listing_fields_collection();//custom fields for custom post type..						
						while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
							if(get_post_meta($post->ID,"ctype",true)){
								$options = explode(',',get_post_meta($post->ID,"option_values",true));
							}
							$custom_fields = array(
									"id"		=> $post->ID,
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
									"show_in_email" =>get_post_meta($post->ID,"show_in_email",true),
									);
							if($options)
							{
								$custom_fields["options"]=$options;
							}
							$return_arr[get_post_meta($post->ID,"htmlvar_name",true)] = $custom_fields;
						endwhile;wp_reset_query();
						$custom_metaboxes[$_heading_type]=$return_arr;
						//
					}
				}
			}			
		echo '<div class="single_custom_field">';		
		$j=0;
		foreach($custom_metaboxes as $mainkey=> $_htmlvar_name):
		$r=0;		
		if(!empty($_htmlvar_name) || $_htmlvar_name!='')
		{
		  foreach($_htmlvar_name as $key=> $_htmlvar_name):	
			if( $key!="post_content" && $key!="post_excerpt" &&  $key!='category' && $key!='post_title' && $key!='post_images' && $key!='basic_inf' && $_htmlvar_name['show_on_detail'] == 1)
			{
				if($_htmlvar_name['type'] == 'multicheckbox' && get_post_meta($post->ID,$key,true) !=''):
					if($r==0){
						 if( $mainkey == '[#taxonomy_name#]' ){
						 	echo '<h3>'.ucfirst($post_type).' ';_e("Information",DOMAIN);echo '</h3>';
							$r++;
						 }else{
						 	echo '<h3>';_e($mainkey,DOMAIN);echo '</h3>';
							$r++;
						 }
					}
			?>
						<li><label><?php echo $_htmlvar_name['label']; ?></label> : <span><?php echo implode(",",get_post_meta($post->ID,$key,true)); ?></span></li>
	               <?php elseif($_htmlvar_name['type']=='upload' && get_post_meta($post->ID,$key,true) !=''):
						if($r==0){
							 if( $mainkey == '[#taxonomy_name#]' ){
							 	echo '<h3>'.ucfirst($PostTypeLabelName).' ';_e("Information",DOMAIN);echo '</h3>';
								$r++;
							 }else{
							 	echo '<h3>';_e($mainkey,DOMAIN);echo '</h3>';
								$r++;
							 }
						}
			?>
               	 		<li><label><?php echo $_htmlvar_name['label']; ?> </label>: <span> <?php echo __('Click here to download File',ADMINDOMAIN); ?> <a href="<?php echo stripslashes(get_post_meta($post->ID,$key,true)); ?>">Download</a></span></li>
			<?php else: 
					/* else start */					
					if(get_post_meta($post->ID,$key,true) !=''):
						if($r==0){
							 if( $mainkey == '[#taxonomy_name#]' ){
							 	echo '<h3>'.ucfirst($PostTypeLabelName).' ';_e("Information",DOMAIN);echo '</h3>';
								$r++;
							 }else{
							 	echo '<h3>';_e($mainkey,DOMAIN);echo '</h3>';
								$r++;
							 }
						}
						
					?>
					
						<?php if($_htmlvar_name['type']=='radio'){
								$options = explode(',',$_htmlvar_name['option_values']);
								$options_title = explode(',',$_htmlvar_name['option_title']);
						
								for($i=0; $i<= count($options); $i++){
									$val = $options[$i];
									if(trim($val) == trim(get_post_meta($post->ID,$key,true))){ 
										$val_label = $options_title[$i];
														
									}
								}
								if($val_label ==''){ $val_label = get_post_meta($post->ID,$post->post_name,true); } // if title not set then display the value ?>
								<li><label><?php echo $_htmlvar_name['label']; ?></label> : <span><?php echo $val_label ; ?></span></li>
						<?php
							}else{ ?>
								<li><label><?php echo $_htmlvar_name['label']; ?></label> : <span><?php echo stripslashes(get_post_meta($post->ID,$key,true)); ?></span></li>
						<?php	}

				  endif;
				/*else end */				  ?>
			<?php endif; ?>
	<?php  	$i++; } // first if condition finish
			$j++;
				
			endforeach;	
		}			
		endforeach;
		echo '</div>';		
	}
	
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
function tev_wp_terms_checklist($post_id = 0, $args = array()) {
	global  $cat_array;
 	$defaults = array(
		'descendants_and_self' => 0,
		'selected_cats' => false,
		'popular_cats' => false,
		'walker' => null,
		'taxonomy' => 'category',
		'checked_ontop' => true
	);

	if(isset($_REQUEST['backandedit']) != '' || (isset($_REQUEST['pid']) && $_REQUEST['pid']!="") ){
		$place_cat_arr = $cat_array;
		$post_id = $_REQUEST['pid'];
	}
	else
	{
		if(!empty($cat_array)){
			for($i=0; $i < count($cat_array); $i++){
				$place_cat_arr[] = @$cat_array[$i]->term_taxonomy_id;
			}
		}
	}
	$args = apply_filters( 'wp_terms_checklist_args', $args, $post_id );
	$template_post_type = get_post_meta($post->ID,'submit_post_type',true);
	extract( wp_parse_args($args, $defaults), EXTR_SKIP );

	if ( empty($walker) || !is_a($walker, 'Walker') )
		$walker = new Tev_Walker_Category_Checklist;

	$descendants_and_self = (int) $descendants_and_self;

	$args = array('taxonomy' => $taxonomy);

	$tax = get_taxonomy($taxonomy);
	$args['disabled'] = !current_user_can($tax->cap->assign_terms);

	if ( is_array( $selected_cats ) )
		$args['selected_cats'] = $selected_cats;
	elseif ( $post_id && (!isset($_REQUEST['upgpkg']) && !isset($_REQUEST['renew'])) )
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
		// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
		$checked_categories = array();
		$keys = array_keys( $categories );
		$c=0;
		foreach( $keys as $k ) {
			if ( in_array( $categories[$k]->term_id, $args['selected_cats'] ) ) {
				$checked_categories[] = $categories[$k];
				unset( $categories[$k] );
			}
		}

		// Put checked cats on top
		echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
	}
	// Then the rest of them

	echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
	if(empty($categories) && empty($checked_categories)){

			echo '<span style="font-size:12px; color:red;">'.sprintf(__('You have not created any category for %s post type.So, this listing will be submited as uncategorized.',DOMAIN),$template_post_type).'</span>';
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
class Tev_Walker_Category_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this
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

	/**
	 * Start the element output.
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 * @param int    $id       ID of the current term.
	 */
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		extract($args);
		if ( empty($taxonomy) )
			$taxonomy = 'category';

		if ( $taxonomy == 'category' )
			$name = 'post_category';
		else
			$name = 'tax_input['.$taxonomy.']';
		
		$selected = array();
		if(!empty($_SESSION['category']) && @$_REQUEST['backandedit'] ==1){
			$cats = $_SESSION['category'];
			foreach($cats as $key=>$value){
					$cat = explode(',',$value);
					$selected[] .= $cat[0];
			}
			$selected_cats = $selected;
		}
		if($category->term_price !='' &&  $category->term_price!='0' ){$cprice = "&nbsp;(".fetch_currency_with_position($category->term_price).")"; }else{ $cprice =''; }
	//	$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'>" . '<label class="selectit"><input value="' . $category->term_id . ','.$category->term_price.'" type="checkbox" name="category[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) .    ' onclick="fetch_packages('.$category->term_price.',this.form)"/> ' . esc_html( apply_filters('the_category', $category->name )) . $cprice.'</label>';
	}

	/**
	 * Ends the element output, if needed.
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 */
	function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}

add_action('admin_init','is_cdlocalization');
/*
Name: is_cdlocalization
Desc: check is it codestyling localization or not
*/
if(!function_exists('is_cdlocalization')){
function is_cdlocalization(){
	if(is_plugin_active('codestyling-localization/codestyling-localization.php')){
		return true;
	}else{
		return false;
	}
}
}

/*
tmpl_checkRemoteFile: to check image availbale or not
*/

if(!function_exists('tmpl_checkRemoteFile')){
	function tmpl_checkRemoteFile($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		// don't download content
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if(curl_exec($ch)!==FALSE)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
/*
Name: login_redirect_to
Desc: Added filter while submitting a form as a logout user redirect to submit form page.
*/
add_filter('tevolution_login_redirect_to','login_redirect_to');
add_filter('tevolution_register_redirect_to','login_redirect_to');
function login_redirect_to($redirect_to){

	if(isset($_SESSION['redirect_to']) && $_SESSION['redirect_to']!=""){
		$redirect_to=$_SESSION['redirect_to'];
	}
	return $redirect_to;
}

/*
get the full page URL specially for pagination n all
*/
function tmpl_directory_full_url($post_type)
{
    global $wp_query;
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
    $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    $host = (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST']))? $_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];	
	
	if(!is_tax() && is_archive() && !is_search())
	{			
		$current_term = $wp_query->get_queried_object();
		$post_type=(get_post_type()!='')? get_post_type() : get_query_var('post_type');
		$permalink = get_post_type_archive_link($post_type);
		$permalink=str_replace('&'.$post_type.'_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
		$permalink=str_replace('&event_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
	}elseif(is_search()){
		$search_query_str=str_replace('&'.$post_type.'_sortby=alphabetical&sortby='.@$_REQUEST['sortby'],'',$_SERVER['QUERY_STRING']);
		$permalink= site_url()."?".$search_query_str;
	}else{
		$current_term = $wp_query->get_queried_object();
		$permalink=($current_term->slug) ?  get_term_link($current_term->slug, $current_term->taxonomy):'';
		if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!='')
			$permalink=str_replace('&'.$post_type.'_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
		
	}	
	
	if(false===strpos($permalink,'?')){
	    $url_glue = '?';
	}else{
		$url_glue = '&amp;';	
	}
    return $permalink.$url_glue;
}

/*
 get the heading type for selected post type
 */
function tmpl_fetch_heading_post_type($post_type){
	
	global $wpdb,$post,$heading_title;
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
	
	remove_all_actions('posts_where');
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	remove_action('pre_get_posts','location_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$heading_title = array();
	$args=
	array( 
	'post_type' => 'custom_fields',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => 'ctype',
			'value' => 'heading_type',
			'compare' => '=',
			'type'=> 'text'
		),
		array(
			'key' => 'post_type',
			'value' => $post_type,
			'compare' => 'LIKE',
			'type'=> 'text'
		)
		
	),
	'meta_key' => 'sort_order',	
	'orderby' => 'meta_value_num',
	'meta_value_num'=>'sort_order',
	'order' => 'ASC'
	);
	$post_query = null;
	remove_all_actions('posts_orderby');
	
	$post_query = get_transient( '_tevolution_query_heading'.trim($post_type).$cur_lang_code);
	if ( false === $post_query && get_option('tevolution_cache_disable')==1){
		$post_query = new WP_Query($args);
		set_transient( '_tevolution_query_heading'.trim($post_type).$cur_lang_code, $post_query, 12 * HOUR_IN_SECONDS );
	}elseif(get_option('tevolution_cache_disable')==''){
		$post_query = new WP_Query($args);
	}
	$post_meta_info = $post_query;	
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			$otherargs=
			array( 
			'post_type' => 'custom_fields',
			'posts_per_page' => -1	,
			'post_status' => array('publish'),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'is_active',
					'value' => '1',
					'compare' => '=',
					'type'=> 'text'
				),
				array(
					'key' => 'heading_type',
					'value' => $post->post_title,
					'compare' => '=',
					'type'=> 'text'
				)
			));		
			
			$other_post_query = null;
			$htmlvar_name=get_post_meta(get_the_ID(),'htmlvar_name',true);
			$other_post_query = get_transient( '_tevolution_query_heading'.trim($post_type).trim($htmlvar_name).$cur_lang_code );			
			if ( false === $other_post_query  && get_option('tevolution_cache_disable')==1) {
				$other_post_query = new WP_Query($otherargs);				
				set_transient( '_tevolution_query_heading'.trim($post_type).trim($htmlvar_name).$cur_lang_code, $other_post_query, 12 * HOUR_IN_SECONDS );
			}elseif(get_option('tevolution_cache_disable')==''){				
				$other_post_query = new WP_Query($otherargs);
			}
			if(count($other_post_query->post) > 0)
			{
				$heading_title[$htmlvar_name] = $post->post_title;
			}
		endwhile;
		wp_reset_query();
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	return $heading_title;
}

/* get all custom fields of post types pass in argument */

function tmpl_single_page_custom_field($post_type){
	
	$custom_post_type = tevolution_get_post_type();
	
	if((is_single() || $_GET['page']=='preview') && $post_type !=''){
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
	$is_edit='';
	if(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'){
		$is_edit=1;
	}
	remove_all_actions('posts_where');		
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');

		if($is_edit==1){
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
											'key'     => 'ctype',
											'value'   => 'heading_type',
											'compare' => '!='
										),
										array(
											'key'     => 'heading_type',
											'value'   =>  array('basic_inf',$heading),
											'compare' => 'IN'
										)
									),
						'meta_key' => 'sort_order',
						'orderby' => 'meta_value',
						'order' => 'ASC'
				);
			$post_query = new WP_Query($args);
		}else{
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
										array(
											'key'     => 'heading_type',
											'value'   =>  array('basic_inf',$heading),
											'compare' => 'IN'
										)
									),
						'meta_key' => 'sort_order',
						'orderby' => 'meta_value',
						'order' => 'ASC'
			);
		
			/* save the data on transient to get the fast results */
			$post_query = get_transient( '_tevolution_query_single'.trim($post_type).trim($heading_key).$cur_lang_code );
			if ( false === $post_query && get_option('tevolution_cache_disable')==1 ) {
				$post_query = new WP_Query($args);
				set_transient( '_tevolution_query_single'.trim($post_type).trim($heading_key).$cur_lang_code, $post_query, 12 * HOUR_IN_SECONDS );
			}elseif(get_option('tevolution_cache_disable')==''){
				$post_query = new WP_Query($args);
			}

		}
		
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

define('TMPL_HEADING_TITLE',__('Other Information',DOMAIN));
/* To display the custom fields on detail page */
function tmpl_fields_detail_informations($not_show = array('title'),$title_text = TMPL_HEADING_TITLE){
	global $post,$htmlvar_name,$heading_type;

	$j=0;
	if(!empty($htmlvar_name)){
		echo '<div class="tevolution_custom_field">';
		
		foreach($htmlvar_name as $key=>$value){
			$i=0;
			if(!empty($value)){
			foreach($value as $k=>$val){
					
				if(isset($_REQUEST['page']) && $_REQUEST['page'] =='preview' && isset($_SESSION['custom_fields'][$k])){
					$field= $_SESSION['custom_fields'][$k];		
				}else{
					$field= get_post_meta($post->ID,$k,true);		
				} 
				$tmpl_key = ($key=='basic_inf')? $title_text: $heading_type[$key];
				
				/* Show other custom fields */
				if($k!='post_title' && $k!='category' && $k!='post_content' && $k!='post_excerpt' && $k!='post_images' && $k!='listing_timing' && $k!='address' && $k!='listing_logo' && $k!='video' && $k!='post_tags' && $k!='map_view' && $k!='proprty_feature' && $k!='phone' && $k!='email' && $k!='website' && $k!='twitter' && $k!='facebook' && $k!='google_plus' && $k!='contact_info' && !in_array($k,$not_show))
				{
					/* To display the title and Locations information on top */
					$key_value = get_post_meta($post->ID,$k,true);
					if($i==0 && $key_value !=''){ 
					echo '<h2 class="custom_field_headding">'.$tmpl_key.'</h2>';
					$i++;
					
						/* Show locations informations - country/state/city*/
						if($htmlvar_name['basic_inf']['post_city_id'] && $htmlvar_name['basic_inf']['post_city_id']['type'] =='multicity' && $k=='post_city_id'){
								global $wpdb,$country_table,$zones_table,$multicity_table;
								if(isset($_REQUEST['page']) && $_REQUEST['page'] =='preview'){
									$city= $_SESSION['custom_fields']['post_city_id'];		
									$country_id= @$_SESSION['custom_fields']['country_id'];		
									$zones_id= @$_SESSION['custom_fields']['zones_id'];		
								}else{
									$city= get_post_meta($post->ID,'post_city_id',true);		
									$zones_id= get_post_meta($post->ID,'zones_id',true);		
									$country_id= get_post_meta($post->ID,'country_id',true);		
								} 
								$cityinfo = $wpdb->get_results($wpdb->prepare("select cityname from $multicity_table where city_id =%d",$city ));
								if($country_id !='')
									$countryinfo = $wpdb->get_results($wpdb->prepare("select country_name from $country_table where country_id =%d",$country_id ));
								if($zones_id !='')
									$zoneinfo = $wpdb->get_results($wpdb->prepare("select zone_name from $zones_table where zones_id =%d",$zones_id ));
								
								if($countryinfo[0]->country_name){
									?><p class='<?php echo $val['style_class'];?>'><label><?php _e('Country',DOMAIN); ?>:</label> <strong><span><?php echo $countryinfo[0]->country_name; ?></span></strong></p>
								<?php }
									if($zoneinfo[0]->zone_name){ ?>
									<p class='<?php echo $val['style_class'];?>'><label><?php _e('State',DOMAIN); ?>:</label> <strong><span><?php echo $zoneinfo[0]->zone_name; ?></span></strong></p>
								<?php } 
									if($cityinfo[0]->cityname){ ?>
									<p class='<?php echo $val['style_class'];?>'><label><?php _e('City',DOMAIN); ?>:</label> <strong><span><?php echo $cityinfo[0]->cityname; ?></span></strong></p>
							<?php }
						}
					
					}
					if($val['type'] == 'multicheckbox' &&  ($field!="" || $is_edit==1)):
						$checkbox_value = '';				
						$option_values = explode(",",$val['option_values']);				
						$option_titles = explode(",",$val['option_title']);
						for($i=0;$i<count($option_values);$i++){ 
							if(isset($option_values[$i]) && $option_values[$i] !='' && count($field)>0){
								if($option_values[$i] !='' && is_array($field) && in_array($option_values[$i],$field)){
									if($option_titles[$i]!=""){
										$checkbox_value .= $option_titles[$i].',';
									}else{
										$checkbox_value .= $option_values[$i].',';
									}
								}
							}
						}
					?>
					<p class='<?php echo $val['style_class']; ?>'><label><?php echo $val['label']; ?>:&nbsp; </label> <strong><span <?php if($is_edit==1):?>id="frontend_multicheckbox_<?php echo $k;?>" <?php endif;?> class="multicheckbox"><?php echo substr($checkbox_value,0,-1);?></span></strong></p>

				 <?php 
				elseif($val['type']=='radio' && ($field || $is_edit==1)):
					$option_values = explode(",",$val['option_values']);				
					$option_titles = explode(",",$val['option_title']);
					for($i=0;$i<count($option_values);$i++){
						if($field == $option_values[$i]){
							if($option_titles[$i]!=""){
								$rado_value = $option_titles[$i];
							}else{
								$rado_value = $option_values[$i];
							}
						?>
					   <p class='<?php echo $val['style_class'];?>'><label><?php echo $val['label']; ?>:&nbsp; </label><strong><span <?php if($is_edit==1):?>id="frontend_radio_<?php echo $k;?>" <?php endif;?>><?php echo $rado_value;?></span></strong></p>
					   <?php
						}
					}
				elseif($val['type']=='oembed_video' && ($field || $is_edit==1)):?>
					<p class='<?php echo $val['style_class'];?>'><label><?php echo $val['label']; ?>:&nbsp;</label>
						<?php if($is_edit==1):?>					
						<span id="frontend_edit_<?php echo $k;?>" class="frontend_oembed_video button" ><?php _e('Edit Video',DOMAIN);?></span>
						<input type="hidden" class="frontend_<?php echo $k;?>" name="frontend_edit_<?php echo $k;?>" value='<?php echo $field;?>' />
						<?php endif;?>
					<span class="frontend_edit_<?php echo $k;?>"><?php             
					$embed_video= wp_oembed_get( $field);            
					if($embed_video!=""){
						echo $embed_video;
					}else{
						echo $field;
					}
					?></span></p>
				<?php	
				endif;
				if($val['type']  == 'upload' || ($is_edit==1 && $val['type']  == 'upload'))
				{
					 $upload_file=strtolower(substr(strrchr($_SESSION['upload_file'][$name],'.'),1));					 
					 if($is_edit==1):?>
						<p class="<?php echo $val['style_class'];?>"><label><?php echo $val['label']; ?>: </label>
							<span class="entry-header-<?php echo $k;?> span_uploader" >
							<span style="display:none;" class="frontend_<?php echo $k;?>"><?php echo $field?></span>                            
							<span id="fronted_upload_<?php echo $k;?>" class="frontend_uploader button"  data-src="<?php echo $field?>">	                 	
								<span><?php echo __( 'Upload ', ADMINDOMAIN ).$val['label']; ?></span>
							</span>
							</span>
						</p>
					<?php elseif($upload_file=='jpg' || $upload_file=='jpeg' || $upload_file=='gif' || $upload_file=='png' || $upload_file=='jpg' ):?>
						<p class="<?php echo $val['style_class'];?>"><img src="<?php echo $field; ?>" /></p>
					<?php else:?>
						<p class="<?php echo $val['style_class'];?>"><label><?php echo $val['label']; ?>: </label><a href="<?php echo $field; ?>" target="_blank"><?php echo basename($field); ?></a></p>
					<?php endif;
				}
				if(($val['type'] != 'multicheckbox' && $val['type'] != 'radio' && $val['type'] != 'multicity' && $val['type']  != 'upload' && $val['type'] !='oembed_video') && ($field!='' || $is_edit==1)):				
				?>
					<p class='<?php echo $val['style_class'];?>'>
						<label><?php echo $val['label']; ?>:&nbsp;</label>
						<?php if($val['type']=='texteditor'):?>
						<div <?php if($is_edit==1):?>id="frontend_<?php echo $val['type'].'_'.$k;?>" class="frontend_<?php echo $k; if($val['type']=='texteditor'){ echo ' editblock';} ?>" <?php endif;?>>
							<?php echo $field;?>
						</div>
					<?php else: ?>
						<strong><span <?php if($is_edit==1):?>id="frontend_<?php echo $val['type'].'_'.$k;?>" contenteditable="true" class="frontend_<?php echo $k;?>" <?php endif;?>>
							<?php echo $field;?>
						</span></strong>
					<?php endif;?>
					</p>
				<?php
				endif; 

				}// End If condition
				
				$j++;
			}// End second foreach
			}
		}// END First foreach
		echo '</div>';
	}
}

/*
 * detail page show categories and tags 
 */
define('TMPL_CATEGORY_LABEL', __('Posted In ',DOMAIN));
function tmpl_get_the_posttype_taxonomies($label,$tax,$title = TMPL_CATEGORY_LABEL)
{
	global $post;
	$taxonomy_category = get_the_taxonomies();		
	$taxonomy_category = str_replace($label.':',$title,$taxonomy_category[$tax]);		
	$taxonomy_category = substr($taxonomy_category,0,-1);	
	return $taxonomy_category;
}

/*
 * detail page show tags
 */
define('TMPL_TAGS_LABEL', __('Tagged In ',DOMAIN));
function tmpl_get_the_posttype_tags($label,$taxtag,$title = TMPL_TAGS_LABEL)
{	
	global $post;
	$taxonomy_tag = get_the_taxonomies();		
	
	$taxonomy_tag = str_replace($label.':',__('Tagged In ',DOMAIN), @$taxonomy_tag[$taxtag]);		
	$taxonomy_tag = substr($taxonomy_tag,0,-1);	

	return $taxonomy_tag;
}

/*================================================ To get the category page custom fields ======================================================*/

/*
 return the custom fields - which selected as show on category page
 */
function tmpl_get_category_list_customfields($post_type){
	global $wpdb,$post,$posttitle;
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
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
									'key'     => 'show_on_listing',
									'value'   =>  '1',
									'compare' => '='
								)
							),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value',
				'suppress_filters' => true,
				'order' => 'ASC'
		);
	
	remove_all_actions('posts_where');		
	remove_action('pre_get_posts','location_pre_get_posts',12);
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	remove_action('pre_get_posts', 'advance_search_template_function',11);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	/* Set the results in transient to get fast results */
	$post_query = get_transient( '_tevolution_query_taxo'.trim($post_type).trim($heading_key).$cur_lang_code );	
	if ( false === $post_query && get_option('tevolution_cache_disable')==1 ) {
		$post_query = new WP_Query($args);		
		set_transient( '_tevolution_query_taxo'.trim($post_type).trim($heading_key).$cur_lang_code, $post_query, 12 * HOUR_IN_SECONDS );		
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
Description : get a drop down of categories -- */
function tmpl_get_category_dl_options($selected,$tcatslug)
{ 
		$cat_args = array('name' => 'scat', 'id' => 'scat', 'selected' => $selected, 'class' => 'select', 'orderby' => 'name', 'echo' => '0', 'hierarchical' => 1, 'taxonomy'=>$tcatslug,'hide_empty'  => 0);
		$cat_args['show_option_none'] = __('Select Category',EDOMAIN);
		return wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
}


/* Changes for add search criteria for search result page */
/*
 * function: tmpl_get_search_criteria
 * Shows search criteria from advanced search form
 * 
 * */
add_action('after_search_result_label','tmpl_get_search_criteria');
function tmpl_get_search_criteria()
{
	global $wpdb;
	
$htmlvar_name = tmpl_get_advance_search_list_customfields(@$_REQUEST['post_type']);

	if(isset($_REQUEST['search_template']) && $_REQUEST['search_template'] ==1 )
	{
		echo '<div class="other_search_criteria">';
		    if(isset($_REQUEST['category']) && !empty($_REQUEST['category']))
		    {
				$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $_REQUEST['post_type'],'public'   => true, '_builtin' => true ));
				echo '<label>';
				_e('Category: ',THEME_DOMAIN);
				echo '</label>';
			    echo tmpl_get_the_category_by_ID($_REQUEST['category'],$taxonomies[0]).', '; 
			}
			if(isset($_REQUEST['tag_s']) && !empty($_REQUEST['tag_s']))
			{
				echo '<label>';
				_e('Tags: ',THEME_DOMAIN);
				echo '</label>';
				echo $_REQUEST['tag_s'].', ';
			} 
			if(isset($_REQUEST['articleauthor']) && !empty($_REQUEST['articleauthor']))
			{
				echo '<label>';
				_e('Author: ',THEME_DOMAIN);
				echo '</label>';
				echo $_REQUEST['articleauthor'];
			}
			foreach($_REQUEST['search_custom'] as $searchkey=>$searchval )
			{
				foreach($htmlvar_name as $key=>$val)
				{
					if( $key == $searchkey && !empty($_REQUEST[$searchkey]))
					{
						echo '<label>'.$val['label'].':</label> '.$_REQUEST[$searchkey].', ';
					}
				}
			}
		echo '</div>';	
	}
}


/*
 return the custom fields - which selected as show on Advance search form
 */
function tmpl_get_advance_search_list_customfields($post_type){
	global $wpdb,$post,$posttitle;
	if(is_array($post_type)){
		$post_type = $post_type[0];
	}else{
		$post_type = $post_type;
	}
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
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
									'key'     => 'is_search',
									'value'   =>  '1',
									'compare' => '='
								)
							),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value',
				'suppress_filters' => true,
				'order' => 'ASC'
		);
	
	remove_all_actions('posts_where');		
	remove_action('pre_get_posts','location_pre_get_posts',12);
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	remove_action('pre_get_posts', 'advance_search_template_function',11);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	/* Set the results in transient to get fast results */

	$post_query = new WP_Query($args);

	remove_filter('posts_join', 'custom_field_posts_where_filter');
	
	$htmllist_advance_search_var_name='';
	if($post_query->have_posts())
	{
		while ($post_query->have_posts()) : $post_query->the_post();
			$ctype = get_post_meta($post->ID,'ctype',true);
			$post_name=get_post_meta($post->ID,'htmlvar_name',true);
			$style_class=get_post_meta($post->ID,'style_class',true);
			$label=get_post_meta($post->ID,'admin_title',true);
			$option_title=get_post_meta($post->ID,'option_title',true);
			$option_values=get_post_meta($post->ID,'option_values',true);
			
			$htmllist_advance_search_var_name[$post_name] = array( 'type'=>$ctype,
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
	return $htmllist_advance_search_var_name;
	
}

/* 
Name :tmpl_get_the_category_by_ID
description : to get the category name from category id for custom post type
Parameters: $cat_ID: Category id and $texonomy: custom post type texonomy
*/
function tmpl_get_the_category_by_ID( $cat_ID,$texonomy ) {
      $cat_ID = (int) $cat_ID;
      $category = get_term( $cat_ID, $texonomy );
	
	        if ( is_wp_error( $category ) )
               return $category;

	        return ( $category ) ? $category->name : '';
}


?>
