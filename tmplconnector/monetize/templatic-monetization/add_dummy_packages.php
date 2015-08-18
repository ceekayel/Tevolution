<?php /* INSERT DUMMY PACKAGES IN MONETIZATION PRICE PACKAGES */
global $wp_query,$wpdb,$wp_rewrite;
$cus_pos_type = get_option("templatic_custom_post");
$post_type_arr='';
$cat_ids='';
if($cus_pos_type && count($cus_pos_type) > 0)
{
	foreach($cus_pos_type as $key=> $_cus_pos_type)
	{	
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $key,'public'   => true, '_builtin' => true ));
		if(isset($taxonomies[0]))
			$categories = get_terms($taxonomies[0], 'orderby=count&hide_empty=0');	
		if(isset($taxonomies[0]))
			$post_type_arr .= $key.','.$taxonomies[0].",";
		if(!empty($categories))
			foreach($categories as $cat_informs){
				$cat_ids.= $cat_informs->term_id.',';
			}
     
	}
}
	$args = array(
      'public' => true,
      'label'  => 'Monetization Package'
    );
    register_post_type( 'monetization_package', $args );
$post_info = array(
					"post_title"	=>	'Free',
					"post_content"	=>	'This package allows you to submit a free listing at no cost.',
					'post_status'   => 'publish',
					'post_author'   => 1,
					'post_type'     => 'monetization_package'
					);
$results = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type='monetization_package' AND post_title='Free'");
if(count($results) == '')
{
	$last_postid = wp_insert_post( $post_info );
	wp_set_post_terms($last_postid,'1','category',true);
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		if(function_exists('wpml_insert_templ_post'))
			wpml_insert_templ_post($last_postid,'monetization_package'); /* insert post in language */
	}
	if (function_exists('icl_register_string')) {									
		icl_register_string('tevolution-price', 'package-name'.$last_postid,'Free');
		icl_register_string('tevolution-price', 'package-desc'.$last_postid,'Free');			
	}
}
$post_info1 = array(
					"post_title"	=>	'Multi Listing Special',
					"post_content"	=>	'',
					'post_status'   => 'publish',
					'post_author'   => 1,
					'post_type'     => 'monetization_package'
					);
$results = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type='monetization_package' AND post_title='Multi Listing Special'");
if(count($results) == '')
{
	$last_postid1 = wp_insert_post( $post_info1 );
	wp_set_post_terms($last_postid1,'1','category',true);
	if (function_exists('icl_register_string')) {									
		icl_register_string('tevolution-price', 'package-name'.$last_postid1,'Multi Listing Special');
		icl_register_string('tevolution-price', 'package-desc'.$last_postid1,'Multi Listing Special');			
	}
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		if(function_exists('wpml_insert_templ_post'))
			wpml_insert_templ_post(@$last_postid1,'monetization_package'); /* insert post in language */
	}
}
$post_meta = array(
					"package_type"			=> '1',
					"package_post_type"		=> 'all,'.substr($post_type_arr,0,-1),	
					"category"               => 'all,'.substr($cat_ids,0,-1),
					"show_package"			=> '1',
					"package_amount"		=> '0',
					"validity" 			=> '12',
					"validity_per" 		=> 'M',
					"package_status"		=> '1',
					"recurring"			=> '0',
					"billing_num"			=> '',
					"billing_per"			=> '',
					"billing_cycle"		=> '',
					"is_featured"			=> '',
					"feature_amount"		=> '',
					"feature_cat_amount"	=> '');
foreach($post_meta as $key=>$val)
{
	add_post_meta(@$last_postid, $key, $val);
}
$post_meta1 = array(
					"package_type"			=> '2',
					"package_post_type"		=> 'all,'.substr($post_type_arr,0,-1),
					"category"               => 'all,'.substr($cat_ids,0,-1),
					"limit_no_post"          => '10', 
					"show_package"			=> '1',
					"package_amount"		=> '100',
					"validity" 			=> '18',
					"validity_per" 		=> 'M',
					"package_status"		=> '1',
					"recurring"			=> '1',
					"billing_num"			=> '1',
					"billing_per"			=> 'M',
					"billing_cycle"		=> '12',
					"is_featured"			=> '1',
					"feature_amount"		=> '15',
					"feature_cat_amount"	=> '15');
foreach($post_meta1 as $key=>$val)
{
	add_post_meta(@$last_postid1, $key, $val);
} 
global $wp_post_types;
if ( isset( $wp_post_types[ 'monetization_package' ] ) ) {
	unset( $wp_post_types[ 'monetization_package' ] );
}
?>
