<?php
/*
 * Function Name: tmpl_google_map_search_ajax
 * Get listing result on ajax
 */
add_action('wp_ajax_search_map_ajax','tmpl_google_map_search_ajax');
add_action('wp_ajax_nopriv_search_map_ajax','tmpl_google_map_search_ajax');
function tmpl_google_map_search_ajax(){
	global $wpdb,$wp_query,$current_cityinfo,$htmlvar_name;
	
	$s = sanitize_text_field( $_REQUEST['s'] );
	$flg=0;
	$results_id='';
	/* get request post type */
	if(isset($_REQUEST['post_type']) && is_array($_REQUEST['post_type'])){
		foreach($_REQUEST['post_type'] as $posttype){
			$post_type.="'".$posttype."',";
		}
		$post_type=sanitize_text_field(substr($post_type,0,-1));
		$flg=1;
		$results_id='tmpl-search-results';
	}else{
		$post_type = "'".sanitize_text_field($_REQUEST['post_type'])."'";
	}
	$posttype = str_replace("'",'',$post_type);
	/*Get the pagged number*/
	$paged = (@$_REQUEST['paged']>1)?$_REQUEST['paged']:1;	
	/*number of listing display on page*/
	$posts_per_page=(isset($_REQUEST['data_map']) && $_REQUEST['data_map']==1) ? '400': get_option('posts_per_page');
	
	if(isset($_REQUEST['s']) && !empty($_REQUEST['s'])){
		$args3=array('s'           	  => ($s!='')?$s:' ',
					 'post_type'      => $_REQUEST['post_type'],
					 'post_status'    => 'publish',
					 'posts_per_page' => $posts_per_page,
					 'paged'		  => $paged,
					);
		$wp_query->set('is_search',true);
		$flg=1;
		$results_id='tmpl-search-results';
	}else{
		/* Get the data on post type taxonomy wise */
		if(isset($_REQUEST['taxonomy']) && $_REQUEST['taxonomy']!='' && isset($_REQUEST['slug']) && $_REQUEST['slug']!=''){
			$args3=array('post_type'      => array($post_type),
						 'post_status'    => 'publish',
						 'posts_per_page' => $posts_per_page,
						 'paged'		  => $paged,
						 'tax_query' => array(array('taxonomy' => $_REQUEST['taxonomy'],'field' => 'slug','terms' => $_REQUEST['slug'])),
						);
		}else{
			/* Get the data as per post type wise */
			$args3=array('post_type'      => array($post_type),
						 'post_status'    => 'publish',
						 'posts_per_page' => $posts_per_page,
						 'paged'		  => $paged,
						);
		}
	}
	
	/* Get the post type wise custom fields on category page*/
	if(function_exists('tmpl_get_category_list_customfields')){
		$htmlvar_name = tmpl_get_category_list_customfields($posttype);
	}else{
		global $htmlvar_name;
	}
	
	add_filter('posts_clauses','tmpl_search_map_latitude_longitude');
	if(($posttype=='listing' || $post_type=='listing') && (function_exists('directory_sortby_where') || function_exists('directory_category_filter_orderby')) ){
		add_filter('posts_where', 'directory_sortby_where');
		add_filter('posts_orderby', 'directory_category_filter_orderby');
	}
	if(($posttype=='event' || $post_type=='event') && (function_exists('event_manager_posts_where') || function_exists('event_manager_filter_orderby')) ){
		add_filter('posts_where', 'event_manager_posts_where');
		add_filter('posts_orderby', 'event_manager_filter_orderby');
		$results_id='tmpl-search-results-event';
	}
	if(($posttype=='property' || $post_type=='property') && (function_exists('tmpl_alphabets_sortby_where') || function_exists('tmpl_property_manager_filter_orderby')) ){
		add_filter('posts_where', 'tmpl_alphabets_sortby_where');
		add_filter('posts_orderby', 'tmpl_property_manager_filter_orderby');
	}

	if(is_plugin_active('Tevolution/templatic.php') && isset($_REQUEST['is_list_filter']) && $_REQUEST['is_list_filter'] == 1)
		add_filter( 'posts_where' , 'tmpl_search_filter_where');
	
	do_action('tmpl_google_map_ajax_posts_where',$post_type);/* add plugin wise poast type posts where filters call*/
	
	if(isset($_REQUEST['data_map']) && $_REQUEST['data_map']==1){
		unset($args3['paged']);
	}
	
	$post_query = new WP_Query( $args3 );

	do_action('tmpl_remove_google_map_ajax_posts_where',$post_type);/* remove plugin wise poast type posts where filters call*/
	
	$post_query->set('is_search',true);	
	if(isset($_REQUEST['data_map']) && $_REQUEST['data_map']==1){
		templ_googlemap_marker_data($post_query,$htmlvar_name);
	}else{		
		/*echo $post_query->request;*/
	}
	
	if ( $post_query->have_posts() ) {
		global $post;
		if($flg==1){
			echo '<div id="'.$results_id.'" class="list">';
		}else{
			echo '<div id="'.$results_id.'" >';
		}
		while ( $post_query->have_posts() ) : $post_query->the_post();
			global $post;
			get_template_part( 'tevolution-search', $post->post_type);
		endwhile;
		echo "</div>";
		wp_reset_query();

	}else{ /* No results found */ ?>
        <p class='nodata_msg'><?php _e( 'Apologies, but no results were found for the requested archive.', SF_DOMAIN ); ?></p> 
		<?php		
	}

	/* display number of results as pagination */
	if($post_query->max_num_pages !=1):
	?>
     <div id="listpagi">
          <div class="pagination pagination-position search_pagination" data-url="<?php echo  esc_url( get_pagenum_link( $big ) );?>">
                <?php
                	$big = 999999999; /* need an unlikely integer*/
					echo paginate_links( array(
						'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format' => '/page/%#%',
						'current' => max( 1, $paged ),
						'before_page_number' => '<strong>',
						'after_page_number' => '</strong>',
						'prev_text'    => '<strong>'.__('Previous',DOMAIN).'</strong>',
						'next_text'    => '<strong>'.__('Next',DOMAIN).'</strong>',
						'total' => $post_query->max_num_pages
					) );
                ?>
          </div>
     </div>
     <?php endif;
	exit;
}

/* Get the Google Map Maker json script */
function templ_googlemap_marker_data($post_query,$htmlvar_name){
	if ( $post_query->have_posts() ) {
		global $post;
		$term_icon=$default='';
		$pids=array();
		if(isset($_REQUEST['taxonomy']) && $_REQUEST['taxonomy']!='' && isset($_REQUEST['slug']) && $_REQUEST['slug']!=''){			
			$term=get_term_by( 'slug',$_REQUEST['slug'] , $_REQUEST['taxonomy'] ) ;
			$term_icon=$term->term_icon;
			$default=1;
		}
		$templatic_settings=get_option('templatic_settings');
		while ( $post_query->have_posts() ) : $post_query->the_post();
			$post_id=$ID = get_the_ID();
			/* get the taxonomy map marker if its not set */
			if($term_icon=='' || $default==''){
				$taxonomies = get_object_taxonomies( (object) array( 'post_type' => get_post_type(),'public'   => true, '_builtin' => true ));
				$post_categories = get_the_terms( get_the_ID() ,$taxonomies[0]);
				foreach($post_categories as $post_category){
					if($post_category->term_icon){
						$term_icon=$post_category->term_icon;
						break;
					}else{
						$term_icon=apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png');
					}
				}
			}
			
			$lat = get_post_meta(get_the_ID(),'geo_latitude',true);
			$lng = get_post_meta(get_the_ID(),'geo_longitude',true);			
			if($lat && $lng && !in_array($ID,$pids))
			{
				if(!isset($more)){ $more =''; }
				$retstr ='{';
				$retstr .= '"name":"'.str_replace($title_srcharr,$title_replarr,$post->post_title).'",';
				$retstr .= '"location": ['.$lat.','.$lng.'],';
				$retstr .= '"message":"<div class=\'google-map-info map-image\'><div class=map-inner-wrapper><div class=\'map-item-info no_map_image\'><i class=\'fa fa-circle-o-notch fa-spin fa-2x\'></i></div></div></div>';
				$retstr .= '",';
				$retstr .= '"load_content":"1",';
				$retstr .= '"icons":"'.$term_icon.'",';
				$retstr .= '"pid":"'.$ID.'"';
				$retstr .= '}';
				$content_data[] = $retstr;
			}
			$pids[]=$ID;
		endwhile;
		if($content_data)
			$catinfo_arr= '{"markers":['.implode(',',$content_data)."]}";
	}else{
		$catinfo_arr= '{"markers":[]}';
	}	
	echo $catinfo_arr;
	exit;
}

/*Load marker contet using ajax after google map search marker */
add_action('wp_ajax_mapmarker_post_detail','tmpl_mapmarker_post_detail');
add_action('wp_ajax_nopriv_mapmarker_post_detail','tmpl_mapmarker_post_detail');
function tmpl_mapmarker_post_detail(){
	global $wpdb,$post;
	$templatic_settings=get_option('templatic_settings');
	/* Get the post object using post id*/
	$request_post_id=array_filter(explode(',',$_REQUEST['post_id']));
	$retstr ='';
	foreach($request_post_id as $value){
		$post=get_post($value);
		$post_id=$post->ID;
		$post_images='';
		$address = get_post_meta($post_id,'address',true);
		if($post->post_type=='listing'){
			$timing=get_post_meta($post_id,'listing_timing',true);
			$contact=get_post_meta($post_id,'phone',true);
		}
		if($post->post_type=='event'){
			$st_time=get_post_meta($post_id,'st_time',true);
			$end_time=get_post_meta($post_id,'end_time',true);
			$timing=$st_time.' To '.$end_time;
			$contact=get_post_meta($post_id,'phone',true);
		}
		/*Get the post thumbnail id using post id */
		$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		if ($post_thumbnail_id ){
			$post_img = wp_get_attachment_image_src( $post_thumbnail_id, 'thumbnail');
			$post_images=$post_img[0];
		}else{
			$post_img = bdw_get_images_plugin($post_id,'thumbnail');
			$post_images = $post_img[0]['file'];
		}
		$imageclass='';
		if($post_images)
			$post_image='<div class=map-item-img><a href='.get_permalink($ID).'><img width=\'150\' height=\'150\' class=\'map_image\' src=\''.$post_images.'\' /></a></div>';
		else{
			$post_image='';
			$imageclass='no_map_image';
		}
		$image_class=($post_image)?'map-image' :'';
		/*Comment */
		$comment_status = $post->comment_status;
		$comment_count= count(get_comments(array('post_id' => $post_id)));
		$review=($comment_count ==1 )? __('review',LDOMAIN):__('reviews',LDOMAIN);
		/*Create marker infowindow content */
		$retstr .= '<div class="google-map-info '.$image_class.'"><div class="map-inner-wrapper"><div class="map-item-info '.$imageclass.'">';
		$retstr .=$post_image;
		$retstr .= '<h6><a href='.get_permalink($post_id).' class=ptitle><span>'.$post->post_title.'</span></a></h6>';
		if($address){$retstr .= '<p class=address>'.trim($address).'</p>';}
		$retstr .= apply_filters('tmpl_map_after_address_fields','',$post->ID);
		if($timing){$retstr .= "<p class=timing>$timing</p>";}
		if($contact){$retstr .= '<p class=contact>'.ltrim(rtrim($contact)).'</p>';}
		$retstr .= apply_filters('tmpl_map_after_contact_fields','',$post->ID);
		if($website){$retstr .= '<p class=website><a href= '.trim($website).'>'.trim($website).'</a></p>';}
		$retstr .= apply_filters('tmpl_map_custom_fields','',$post->ID);
		if($comment_status =='open'){
			if($templatic_settings['templatin_rating']=='yes'){
				$rating=draw_rating_star_plugin(get_post_average_rating($post_id));
				$retstr .= '<div class=map_rating>'.str_replace('"','',$rating).' <span><a href='.get_permalink($post_id).'#comments>'.$comment_count.' '.$review.'</a></span></div>';
			}elseif(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('single_average_rating')){
				$rating=get_single_average_rating($post_id);
				$retstr .= '<div class=map_rating>'.stripcslashes(str_replace('"','',$rating)).'<span> <a href='.get_permalink($post_id).'#comments>'.$comment_count.' '.$review.'</a></span></div>';
			}
		}
		$retstr .= '</div></div></div>';
	
	}
	echo $retstr;
	exit;
}

/*
 * Function Name: tmpl_search_map_latitude_longitude
 * posts clauses query filter on google map drag and zoom listener
 */
function tmpl_search_map_latitude_longitude($posts_clauses){
	global $wpdb,$wp_query,$current_cityinfo;
	$postcodes="{$wpdb->prefix}postcodes";
	
	$sw_lat=trim($_REQUEST['sw_lat']);
	$ne_lat=trim($_REQUEST['ne_lat']);
	$ne_lng=trim($_REQUEST['ne_lng']);
	$sw_lng=trim($_REQUEST['sw_lng']);
	
	if($ne_lng > $sw_lng)
	{
		$lng_temp = $ne_lng;
		$ne_lng = $sw_lng;
		$sw_lng = $lng_temp;
	}
	
	if($sw_lat!='' && $ne_lat!='' && $ne_lng!='' && $sw_lng!=''){
		$posts_clauses['join'] .=" INNER JOIN {$wpdb->prefix}postmeta pml on pml.post_id={$wpdb->prefix}posts.ID AND pml.meta_key='geo_latitude' INNER JOIN {$wpdb->prefix}postmeta pmlng on pmlng.post_id={$wpdb->prefix}posts.ID AND pmlng.meta_key='geo_longitude'";
		$posts_clauses['where'].=" AND ((pml.meta_value > ".$sw_lat."  AND  pml.meta_value < ".$ne_lat.") AND (pmlng.meta_value > ".$ne_lng."  AND  pmlng.meta_value <".$sw_lng.") )";
	}
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		$language = ICL_LANGUAGE_CODE;
		$posts_clauses['where'].=" AND t.language_code='".$language."'";
	}
	return $posts_clauses;
}

/* Autocomplete search results function */
add_action('wp_ajax_tevolution_autocomplete_callBack','tmpl_tevolution_autocomplete_callBack');
add_action('wp_ajax_nopriv_tevolution_autocomplete_callBack','tmpl_tevolution_autocomplete_callBack');
function tmpl_tevolution_autocomplete_callBack(){
	global $wpdb,$wp_query,$current_cityinfo;
	
	if(is_array($_REQUEST['post_type'])){
		$post_type=$_REQUEST['post_type'];
	}else{
		$post_type= explode(',',$_REQUEST['post_type']);
	}
	if(!empty($post_type))
	$post_type = array_filter(array_unique($post_type));
	$resultsPosts = array();
	$resultsTerms = array();

	/* End Fetch results from post type taxonomy */	
	if(!empty($_REQUEST['mkey']) && in_array('cats',$_REQUEST['mkey'])){
		foreach($post_type as $posttype){
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $posttype,'public'   => true, '_builtin' => true ));
			$args = array('page' => 1,'number' => 5,'search' => $_REQUEST['search_text'],'hide_empty' => 0) ;
			$terms_results = get_terms( $taxonomies[0], $args );
			$taxonomy_obj = get_taxonomy($taxonomies[0]);
			foreach($terms_results as $term){
				$resultsTerms[] = array(
					'label' => html_entity_decode(str_replace("&#8217;","'",$term->name)),
					'title' => '<label>'.$term->name.'</label> <span class="type">'.$taxonomy_obj->labels->name.'</span>',
					'url'   => get_term_link($term->slug,$term->taxonomy),
				);
			}
		}
	}
	/* End Fetch results from post type taxonomy */	
	if(!empty($_REQUEST['mkey']) && in_array('tags',$_REQUEST['mkey'])){

		foreach($post_type as $posttype){
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $posttype,'public'   => true, '_builtin' => true ));
			$args = array('page' => 1,'number' => 5,'search' => $_REQUEST['search_text'],'hide_empty' => 0) ;
			$terms_results = get_terms( $taxonomies[1], $args );
			$taxonomy_obj = get_taxonomy($taxonomies[1]);
			foreach($terms_results as $term){
				$resultsTerms[] = array(
					'label' => html_entity_decode(str_replace("&#8217;","'",$term->name)),
					'title' => '<label>'.$term->name.'</label> <span class="type">'.$taxonomy_obj->labels->name.'</span>',
					'url'   => get_term_link($term->slug,$term->taxonomy),
				);
			}
		}
	}

	/*Finish Fetch result from post type taxonomy */
	$args3=array('s'              => $_REQUEST['search_text'],
				 'post_type'      => $post_type,
				 'post_status'    => 'publish',
				 'posts_per_page' => 5,
				 'orderby'	      => 'title',
				 'order'		  => 'ASC'
				);

	/* Post where filter call for fetach current city wise results */
	if(function_exists('location_multicity_where') && isset($_REQUEST['search_in_city']) && $_REQUEST['search_in_city'] == 1 ){
		add_filter('posts_where','location_multicity_where');
	}

	$post_query = new WP_Query( $args3 );
	if ( $post_query->have_posts() ) {
		while ( $post_query->have_posts() ) : $post_query->the_post();
			if(get_the_title()!=''){
				$obj = get_post_type_object( get_post_type() );
				$resultsPosts[] = array(
					'label' => html_entity_decode(str_replace("&#8217;","'",get_the_title())),
					'title' => '<label>'.get_the_title().'</label> <span class="type">'.ucfirst($obj->labels->singular_name).'</span>',
					'url'   => get_the_permalink(get_the_ID()),
				);
			}
		endwhile;
		wp_reset_query();
	}

	/* Search address */
	wp_reset_query();
	if(!empty($_REQUEST['mkey']) && in_array('address',$_REQUEST['mkey'])){	
		$args4=array('post_type'      => $post_type,
					 'post_status'    => 'publish',
					 'posts_per_page' => 5,
					 'meta_query'     =>array('relation' => 'OR',
											   array('key'     => 'address','value'   => $_REQUEST['search_text'],'compare' => 'LIKE')
										),
					);

		$address_query = new WP_Query( $args4 );
		if ( $address_query->have_posts() ){
			while ( $address_query->have_posts() ) : $address_query->the_post();
			$resultsPosts[] = array(
						'label' => html_entity_decode(str_replace("&#8217;","'",get_post_meta(get_the_ID(),'address',true))),
						'title' => '<label>'.get_post_meta(get_the_ID(),'address',true).'</label> <span class="type">'.__('Location',DOMAIN).'</span>',
						'url'   =>  get_the_permalink(get_the_ID()),
					);
			endwhile;
			wp_reset_query();
		}
	}
	/* Finish Address */
	/* Remove Post where filter call for fetach current city wise results */
	if(function_exists('location_multicity_where') && isset($_REQUEST['search_in_city']) && $_REQUEST['search_in_city'] == 1 ){
		remove_filter('posts_where','location_multicity_where');
	}

	$results = array_merge( $resultsTerms, $resultsPosts );
	echo json_encode( array( 'results' => $results) );
	die();
}

/* Address auto complete search callback function */
add_action('wp_ajax_tevolution_autocomplete_address_callBack','tmpl_tevolution_autocomplete_address_callBack');
add_action('wp_ajax_nopriv_tevolution_autocomplete_address_callBack','tmpl_tevolution_autocomplete_address_callBack');
function tmpl_tevolution_autocomplete_address_callBack(){

	global $wpdb,$wp_query,$current_cityinfo;

	$post_type=$_REQUEST['post_type'];
	$resultsPosts = array();
	/* Search address */
	wp_reset_query();
	$args4=array('post_type'      => $post_type,
				 'post_status'    => 'publish',
				 'posts_per_page' => -1,
				 'meta_query'     => array('relation' => 'OR',
										array('key' => 'address','value' => $_REQUEST['search_text'],'compare' => 'LIKE')
									),
				);

	/* Post where filter call for fetach current city wise results */
	if(function_exists('location_multicity_where')){
		add_filter('posts_where','location_multicity_where');
	}
	$address_query = new WP_Query( $args4 );
	$address_res=array();
	if ( $address_query->have_posts() ){
		$i=0;
		while ( $address_query->have_posts() ) : $address_query->the_post();
			$address=get_post_meta(get_the_ID(),'address',true);
			if(!in_array($address,$address_res)){
				$resultsPosts[] = array(
							'label' => html_entity_decode(str_replace("&#8217;","'",get_post_meta(get_the_ID(),'address',true))),
							'title' => '<label>'.$address.'</label>',
							'url'   =>  get_the_permalink(get_the_ID()),
						);
				$address_res[]=$address;
				$i++;
			}
			if($i==5){
				break;
			}
		endwhile;
		wp_reset_query();
	}

	/* Remove Post where filter call for fetach current city wise results */
	if(function_exists('location_multicity_where')){
		remove_filter('posts_where','location_multicity_where');
	}
	/* Finish Address */	
	echo json_encode( array( 'results' => $resultsPosts) );
	exit;
}