<?php
/*
 * added filter for tevolution page template.
 */
/*
Name : listing_fields_collection
Desc : Return the collection for category listing page
*/
function tevolution_listing_fields_collection()
{
	global $wpdb,$post,$htmlvar_name,$pos_title;
	
	$cus_post_type = get_post_type();
	$args = 
 apply_filters('tmpl_custom_fileds_query',array( 'post_type' => 'custom_fields',
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
			'key' => 'show_on_listing',
			'value' =>  '1',
			'compare' => '='
		)
	),
		'meta_key' => 'sort_order',
		'orderby' => 'meta_value',
		'order' => 'ASC'
	),$cus_post_type);
	
	remove_all_actions('posts_where');
	$post_query = null;
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$post_query = new WP_Query($args);
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	
	$htmlvar_name='';
	if($post_query->have_posts())
	{
		while ($post_query->have_posts()) : $post_query->the_post();
			$ctype = get_post_meta($post->ID,'ctype',true);
			$post_name=get_post_meta($post->ID,'htmlvar_name',true);
			$style_class=get_post_meta($post->ID,'style_class',true);
			$htmlvar_name[$post_name] = array( 'type'=>$ctype,
												'label'=> $post->post_title,
												'style_class'=>$style_class
											  );
			$pos_title[] = $post->post_title;
		endwhile;
		wp_reset_query();
	}
}
add_action('templ_before_categories_title','tevolution_listing_fields_collection');
add_action('templ_before_archive_title','tevolution_listing_fields_collection');
/* 
 * Function Name: tevolution_details_field_collection
 * DESCRIPTION : this function wil return the custom fields of the post detail page <br />
 */
function tevolution_details_field_collection()
{
	global $wpdb,$post,$single_htmlvar_name;
	if(is_single()){
	
		$cus_post_type = get_post_type();
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
				'key' => 'show_on_detail',
				'value' =>  '1',
				'compare' => '='
			)
		),
			'meta_key' => 'sort_order',
			'orderby' => 'meta_value_num',
			'meta_value_num'=>'sort_order',
			'order' => 'ASC'
		);
		remove_all_actions('posts_where');
		$post_meta_info = null;
		add_filter('posts_join', 'custom_field_posts_where_filter');
		$post_meta_info = new WP_Query($args);
		remove_filter('posts_join', 'custom_field_posts_where_filter');
				
		$single_htmlvar_name='';
		if($post_meta_info->have_posts())
		{
			while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
				$ctype = get_post_meta($post->ID,'ctype',true);
				$post_name = get_post_meta($post->ID,'htmlvar_name',true);
				$single_htmlvar_name[$post_name] = $ctype;
				$single_pos_title[] = $post->post_title;
			endwhile;
			wp_reset_query();
		}
	}	
}
add_action('templ_before_post_title','tevolution_details_field_collection');
/*
 * Function Name: tevolution_get_taxonomy
 * Return: fetch the tevolution taxonomy array
 */
function tevolution_get_taxonomy()
{
	$templatic_custom_taxonomy = get_option('templatic_custom_taxonomy');
	$tevolution_taxonomy = array();
	if(!empty($templatic_custom_taxonomy) && count($templatic_custom_taxonomy))
	{
		foreach($templatic_custom_taxonomy as $key=>$value){
			$tevolution_taxonomy[]=$key;
		}
	}
	return apply_filters('tevolution_get_taxonomy',$tevolution_taxonomy);
}
/*
 * Function Name: tevolution_get_taxonomy
 * Return: fetch the tevolution taxonomy array
 */
function tevolution_get_taxonomy_tags()
{
	$templatic_custom_tags = get_option('templatic_custom_tags');
	$tevolution_taxonomy_tags=array();
	if(!empty($templatic_custom_tags) && count($templatic_custom_tags > 0))
	{
		foreach($templatic_custom_tags as $key=>$value){
			$tevolution_taxonomy_tags[]=$key;
		}
	}	
	return apply_filters('tevolution_get_taxonomy_tags',$tevolution_taxonomy_tags);
}
/*
 * Function Name: tevolution_get_post_type
 * Return: fetch the tevolution post type array
 */
function tevolution_get_post_type()
{
	$templatic_custom_post = get_option('templatic_custom_post');
	
	$tevolution_post_type=array();
	if($templatic_custom_post){
		foreach($templatic_custom_post as $key=>$value){
			$tevolution_post_type[]=$key;
		}
	}
	return apply_filters('tevolution_get_post_type',$tevolution_post_type);
}
/*
 * Apply filter for custom post type archive page template
 * Function Name: tevolution_get_archive_page_template
 */
function tevolution_get_archive_page_template($archive_template)
{
	global $wpdb,$wp_query,$post;
	$custom_post_type=tevolution_get_post_type();
	$post_type=(get_post_type()!='')? get_post_type() : get_query_var('post_type');
	if(is_archive() && in_array($post_type,$custom_post_type))
	{
		if ( file_exists(STYLESHEETPATH . '/archive-'.get_post_type(). '.php')) {
			
			$archive_template = STYLESHEETPATH . '/archive-'.get_post_type(). '.php';
			
		} else if ( file_exists(TEMPLATEPATH . '/archive-'.get_post_type(). '.php') ) {
			
			$archive_template = TEMPLATEPATH . '/archive-'.get_post_type(). '.php';
			
		}elseif( file_exists(TEVOLUTION_PAGE_TEMPLATES_DIR . 'templates/archive-tevolution.php')){
			
			$archive_template = TEVOLUTION_PAGE_TEMPLATES_DIR . 'templates/archive-tevolution.php';
		}
	}
	return $archive_template;
}
add_filter( "archive_template", "tevolution_get_archive_page_template");
/*
 * Apply filter for taxonomy page 
 * Function name: get_taxonomy_product_post_type_template
 */ 
function tevolution_get_taxonomy_page_template($taxonomy_template)
{	
	global $wpdb,$wp_query,$post;
	/*fetch the current page texonomy*/
	$current_term = $wp_query->get_queried_object();	
	/*fetch the tevolution taxonomy*/
	$custom_taxonomy=tevolution_get_taxonomy();
	/*fetch the tevolution post type*/
	$custom_post_type=tevolution_get_post_type();
	
	if(in_array($current_term->taxonomy,$custom_taxonomy) || in_array(get_post_type(),$custom_post_type))
	{
		if ( file_exists(STYLESHEETPATH . '/taxonomy-'.$current_term->taxonomy. '.php')) {
			
			$taxonomy_template = STYLESHEETPATH . '/taxonomy-'.$current_term->taxonomy. '.php';
			
		} else if ( file_exists(TEMPLATEPATH . '/taxonomy-'.$current_term->taxonomy. '.php') ) {
			
			$taxonomy_template = TEMPLATEPATH . '/taxonomy-'.$current_term->taxonomy. '.php';
			
		}else{
			
			$taxonomy_template = TEVOLUTION_PAGE_TEMPLATES_DIR . 'templates/taxonomy-tevolution.php';

		}
	}
	return $taxonomy_template;
}
add_filter( "taxonomy_template", "tevolution_get_taxonomy_page_template",11) ;
/*
 * Apply filter for taxonomy page for tags 
 * Function name: get_tevolution_tag_page_template
 */ 
function tevolution_get_tag_page_template($tags_template)
{	
	global $wpdb,$wp_query,$post;

	/*fetch the current page texonomy*/
	$current_term = $wp_query->get_queried_object();
	/*fetch the tevolution taxonomy*/
	$custom_taxonomy_tags = tevolution_get_taxonomy_tags();
	/*fetch the tevolution post type*/
	$custom_post_type = tevolution_get_post_type();
	if(in_array($current_term->taxonomy,$custom_taxonomy_tags))
	{		
		if ( file_exists(STYLESHEETPATH . '/taxonomy-'.$current_term->taxonomy. '.php')) {

			$tags_template = STYLESHEETPATH . '/taxonomy-'.$current_term->taxonomy. '.php';

		} else if ( file_exists(TEMPLATEPATH . '/taxonomy-'.$current_term->taxonomy. '.php') ) {

			$tags_template = TEMPLATEPATH . '/taxonomy-'.$current_term->taxonomy. '.php';

		}else{

			$tags_template = TEVOLUTION_PAGE_TEMPLATES_DIR . 'templates/taxonomy-tevolution-tag.php';

		}
	}
	return $tags_template;
}
add_filter( "taxonomy_template", "tevolution_get_tag_page_template",11) ;
/*
 * Apply filter for single template page
 * Function name: get_tevolution_single_template
 */ 
function tevolution_get_single_template($single_template)
{
	global $wpdb,$wp_query,$post;
	/*fetch the tevolution post type*/
	$custom_post_type = tevolution_get_post_type();
	if(in_array(get_post_type(),$custom_post_type))
	{
		if ( file_exists(STYLESHEETPATH . '/single-'.get_post_type(). '.php')) {

			$single_template = STYLESHEETPATH . '/single-'.get_post_type(). '.php';

		} else if ( file_exists(TEMPLATEPATH . '/single-'.get_post_type(). '.php') ) {

			$single_template = TEMPLATEPATH . '/single-'.get_post_type(). '.php';

		}else{

			$single_template = TEVOLUTION_PAGE_TEMPLATES_DIR . 'templates/single-tevolution.php';

		}
	}	
	return $single_template;
}
add_filter( "single_template", "tevolution_get_single_template",11) ;
/*Start function for required single custom post type template */
/*
 * Function Name: tevolution_post_title
 * Return: display post title
 */
function tevolution_post_title()
{
	$title = get_the_title();
	$is_related = get_query_var('is_related');
	if ( strlen( $title ) == 0 )
		return;
	if (( is_singular() || is_single()) && !is_front_page() && !is_home() && !$is_related)
		$title = sprintf( '<h1 class="entry-title">%s</h1>', $title );
	else
		$title = sprintf( '<h2 class="entry-title"><a itemprop="url" href="%s" title="%s" rel="bookmark">%s</a></h2>', get_permalink(), the_title_attribute( 'echo=0' ), $title);
	echo $title;
}
add_action('templ_post_title','tevolution_post_title');
/*
 * Function Name: tevolution_post_single_content
 * Return: display the single post content
 */
function tevolution_post_single_content()
{
	global $post;
	$is_edit='';
	if(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'){
		$is_edit=1;
	}	
	$post_type_object = get_post_type_object(get_post_type());
    $post_type_label = $post_type_object->labels->name;
	$post_description=str_replace('Post',$post_type_label, __('Post Description','templatic'));
	$post_description = apply_filters('custom_post_type_desscription_title',$post_description,$post_type_label);
	
	if($post->post_content!='')  {
	if(function_exists('tmpl_wp_is_mobile') && !tmpl_wp_is_mobile()){
	if(function_exists('icl_t')){
		icl_register_string('templatic',$post_type_label,$post_type_label);
		$post_type_label = icl_t('templatic',$post_type_label,$post_type_label);
	}else{
		$post_type_label = @$post_type_label;
	}
	?>
		<h2><?php echo $post_type_label.' ';_e('Description','templatic');?></h2>
	<?php }?>
	<div class="entry-content frontend-entry-content <?php if($is_edit==1):?>editblock<?php endif;?>">
    	<?php the_content();?>
    </div>
	<?php }
}
add_action('templ_post_single_content','tevolution_post_single_content');
/*
 * Function taxonomy_class
 * Return: display taxonomy post list class
 */
function templ_post_class()
{
	echo get_templ_post_class();
}
/*
 * Function Name: get_taxonomy_class
 * Return: taxonomy post list class name
 */
function get_templ_post_class($class = '')
{
	global $wpdb,$wp_query,$post;
	/*fetch the current page texonomy*/
	$current_term = $wp_query->get_queried_object();
	/*fetch the tevolution taxonomy*/
	$custom_taxonomy = tevolution_get_taxonomy();
	/*fetch the tevolution taxonomy*/
	$custom_taxonomy_tags = tevolution_get_taxonomy_tags();

	/*fetch the tevolution post type*/
	$custom_post_type = tevolution_get_post_type();
	if(is_archive() || (in_array($current_term->taxonomy,$custom_taxonomy)|| in_array($current_term->taxonomy,$custom_taxonomy_tags)  ) && in_array(get_post_type(),$custom_post_type) || DOING_AJAX)
	{
		$classes[]=get_post_type()."-".get_the_ID();
                                                  $package_select =get_post_meta(get_the_ID(),'package_select',true);
                                                  $package_featured_type = get_post_meta($package_select,'is_category_page_featured',true);
                                                  if($package_featured_type  == 1)
                                                            $featured=get_post_meta(get_the_ID(),'featured_c',true);
                                                  $classes[]=($featured=='c')?'featured_c':'';

	}
	$classes = apply_filters( 'get_templ_post_class', $classes);

	if(is_author()){
		if(!empty($classes))
		$classes = join( ' post ', $classes );
	}else{
		if(!empty($classes))
		$classes = join( ' ', $classes );
	}
	return $classes;
}
/* tevolution excerpt support for theme */
add_action('init','tev_excerpt_fun');
function tev_excerpt_fun(){
	if(!current_theme_supports('no-tevolutionexcerpt')){
		add_filter('excerpt_length', 'tevolution_excerpt_len',20);
		add_filter('excerpt_more', 'tevolution_excerpt_more',20);
	}
}
/*show content with excerpt length and continue link*/
add_filter('the_content','tevolution_the_content',20);
function tevolution_the_content($content){
	global $post;
	if((is_archive() || is_search()) && current_theme_supports('tev_taxonomy_excerpt_opt')){
		$tmpdata = get_option('templatic_settings');
		return limited_content($tmpdata['excerpt_length'],$tmpdata['excerpt_continue']);
	}
	return $content;
}
/*show content with excerpt length and continue link*/
function limited_content($content_length = 250, $content_more) {
	global $post;
	$tmpdata = get_option('templatic_settings');
	if(function_exists('icl_t')){
		icl_register_string('templatic',$tmpdata['excerpt_continue'],$tmpdata['excerpt_continue']);
		$link = icl_t('templatic',$tmpdata['excerpt_continue'],$tmpdata['excerpt_continue']);
	}else{
		$link = @$tmpdata['excerpt_continue'];
	}

	$more_link_text =($tmpdata['excerpt_continue']!='')? $tmpdata['excerpt_continue']: __('Read More','templatic');
	$content = get_the_content();
	$excerpt_more='... <a class="moretag" href="'. get_permalink($post->ID) . '">'.$more_link_text.'</a>';
	$content= wp_trim_words( $content, $content_length, $excerpt_more );
	return $content;
}
/* filter for excerpt length */
function tevolution_excerpt_len($length ) {
	if(current_theme_supports('tev_taxonomy_excerpt_opt')){
		$tmpdata = get_option('templatic_settings');
		if($tmpdata['excerpt_length']){
			return $tmpdata['excerpt_length'];
		}
	}
	return $length;
}
function tevolution_excerpt_more($more) {
	global $post;
	if(current_theme_supports('tev_taxonomy_excerpt_opt')){
		$tmpdata = get_option('templatic_settings');

		if(function_exists('icl_t')){
			icl_register_string('templatic',$tmpdata['excerpt_continue'],$tmpdata['excerpt_continue']);
			$link = icl_t('templatic',$tmpdata['excerpt_continue'],$tmpdata['excerpt_continue']);
		}else{
			$link = @$tmpdata['excerpt_continue'];
		}
		if(isset($tmpdata['excerpt_continue']) && $tmpdata['excerpt_continue']){
			return '... <a class="moretag" href="'. get_permalink($post->ID) . '">'.$link.'</a>';
		}
	}else{
		if(function_exists('supreme_prefix()')){
			$prefx = supreme_prefix();
		}else{
			$prefx = sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );
		}

		$tmpdata = get_option($prefx.'_theme_settings');
		if(function_exists('icl_t')){
			icl_register_string('templatic',@$tmpdata['templatic_excerpt_link'],@$tmpdata['templatic_excerpt_link']);
			$link = icl_t('templatic',@$tmpdata['templatic_excerpt_link'],@$tmpdata['templatic_excerpt_link']);
		}else{
			$link = (isset($tmpdata['templatic_excerpt_link']))?$tmpdata['templatic_excerpt_link'] : '';
		}
		if(isset($tmpdata['templatic_excerpt_link']) && $tmpdata['templatic_excerpt_link']){
			return '... <a class="moretag" href="'. get_permalink($post->ID) . '">'.$link.'</a>';
		}else{
			return '... <a class="moretag" href="'. get_permalink($post->ID) . '">'.__('Read more &raquo;','templatic').'</a>';
		}
	}
}

/* Search Template part file include */
add_action( 'get_template_part_tevolution-search','tmpl_tevolution_search_template_part',12,3);
function tmpl_tevolution_search_template_part($slug,$name,$htmlvar_name = ''){
	if(!empty($htmlvar_name))
		$htmlvar_name = $htmlvar_name;

	$directoy_plugin_path=plugin_dir_path(dirname( dirname(__FILE__ )));
	$event_plugin_path=plugin_dir_path(dirname( dirname(__FILE__) ));
	$property_plugin_path=plugin_dir_path(dirname(dirname( __FILE__ )));

	$custom_post_type = apply_filters('directory_post_type_template',tevolution_get_post_type());
	/* Get the tevolution post type*/
	$post_types=tevolution_get_post_type();
	
	if ( file_exists(STYLESHEETPATH . "/{$slug}-{$name}.php")) {

		$search_template = STYLESHEETPATH . "/{$slug}-{$name}.php";

	}else if(file_exists(TEMPLATEPATH."/{$slug}-{$name}.php")){

		$search_template = TEMPLATEPATH. "/{$slug}-{$name}.php";

	}else if($name=='event' && file_exists($event_plugin_path. "Tevolution-Events/templates/{$slug}-{$name}.php") && in_array($name,$post_types)){

		$search_template = $event_plugin_path. "Tevolution-Events/templates/{$slug}-{$name}.php";
		include($search_template);

	}else if($name=='property' && file_exists($property_plugin_path. "Tevolution-RealEstate/templates/{$slug}-{$name}.php") && in_array($name,$post_types)){

		$search_template = $property_plugin_path. "Tevolution-RealEstate/templates/{$slug}-{$name}.php";
		include($search_template);

	}else if($name=='classified' && file_exists($property_plugin_path. "Tevolution-Classifieds/templates/{$slug}-{$name}.php") && in_array($name,$post_types)){

		$search_template = $property_plugin_path. "Tevolution-Classifieds/templates/{$slug}-{$name}.php";
		include($search_template);

	}else if(in_array($name,$custom_post_type)  && file_exists($directoy_plugin_path. "Tevolution-Directory/templates/tevolution-search-listing.php") && in_array($name,$post_types) && $name!='event'){

		$search_template = $directoy_plugin_path. "Tevolution-Directory/templates/tevolution-search-listing.php";
		include($search_template);

	}else if(in_array($name,$post_types)){
		/*apply filters for change the tevolution post type search template parth as per post type */
		if(file_exists(TEVOLUTION_PAGE_TEMPLATES_DIR. "templates/{$slug}-{$name}.php"))
			$search_template = apply_filters('get_tevolution_search_template_part',TEVOLUTION_PAGE_TEMPLATES_DIR. "templates/{$slug}-{$name}.php",$slug,$name);
		
		include($search_template);
	}
}

/* post type preview page Template part file include */
add_action( 'get_template_part_tevolution-single','tmpl_tevolution_single_preview_page',12,2);
function tmpl_tevolution_single_preview_page($slug,$name){

	$directoy_plugin_path=plugin_dir_path(dirname( dirname(__FILE__ )));
	$event_plugin_path=plugin_dir_path(dirname( dirname(__FILE__) ));
	$property_plugin_path=plugin_dir_path(dirname(dirname( __FILE__ )));
	$addons_posttype = tmpl_addon_name();
	$custom_post_type = apply_filters('tmpl_single_preview_post_type',tevolution_get_post_type());
	/* Get the tevolution post type*/
	$post_types =$_REQUEST['submit_post_type'];	

	if ( file_exists(STYLESHEETPATH . "/{$slug}-{$name}.php")) {

		$single_preview = STYLESHEETPATH . "/{$slug}-{$name}.php";

	}else if(file_exists(TEMPLATEPATH."/{$slug}-{$name}.php")){

		$single_preview = TEMPLATEPATH. "/{$slug}-{$name}.php";

	}else if($post_types=='event' && file_exists($event_plugin_path. "Tevolution-Events/templates/{$slug}-{$name}.php") && in_array($post_types,$custom_post_type)){

		$single_preview = $event_plugin_path. "Tevolution-Events/templates/{$slug}-{$name}.php";
		include($single_preview);

	}else if($post_types=='property' && file_exists($property_plugin_path. "Tevolution-RealEstate/templates/{$slug}-{$name}.php") && in_array($post_types,$custom_post_type)){

		$single_preview = $property_plugin_path. "Tevolution-RealEstate/templates/{$slug}-{$name}.php";
		include($single_preview);

	}else if(in_array($post_types,$custom_post_type)  && in_array($post_types,$custom_post_type) && $post_types!='event' && file_exists(WP_PLUGIN_DIR. "/Tevolution-".$addons_posttype[$post_types]."/templates/{$slug}-{$name}.php")){  
		
		
		if(file_exists( WP_PLUGIN_DIR."/Tevolution-".$addons_posttype[$post_types]."/templates/{$slug}-{$name}.php"))
			$single_preview = WP_PLUGIN_DIR."/Tevolution-".$addons_posttype[$post_types]."/templates/{$slug}-{$name}.php";
		else{
			$single_preview = $directoy_plugin_path. "Tevolution-Directory/templates/tevolution-single-listing-preview.php";
		}
		include($single_preview);

	}else if(in_array($post_types,$custom_post_type) || $post_types == 'post'){
	
		/*apply filters for change the tevolution post type search template parth as per post type */
		$single_preview = apply_filters('get_tevolution_single_preview',TEVOLUTION_PAGE_TEMPLATES_DIR. "templates/tevolution-single-post-preview.php",$slug,$name);
		include($single_preview);
	}
}
?>