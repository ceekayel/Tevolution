<?php
/*
 * This file use for set tevolution curl
 * Curl file is make for faster result responce
 *  */
class Tevolution_wp_json_api {

    /**
     * Server object
     *
     * @var WP_JSON_ResponseHandler
     */
    protected $server;

    /**
     * Register the post-related routes
     *
     * @param array $routes Existing routes
     * @return array Modified routes
     */
    public function register_routes($routes) {
        $post_routes = array(
            // route for popular post responce using curl
            '/popular/post/(?P<post_type>[\w-]+)/popular-per/(?P<popular_per>[\w-]+)/number/(?P<number>[\w-_0-9]+)' => array(
                array($this, 'get_popular_posts'), WP_JSON_Server::READABLE
            ),
            '/recent/review' => array(
                array($this, 'get_recent_review_comments'), WP_JSON_Server::READABLE
            ),
            '/browse/category-tag' => array(
                array($this, 'get_browse_by_category_tag'), WP_JSON_Server::READABLE
            ),
            '/browse/neighborhood' => array(
                array($this, 'get_neighborhood'), WP_JSON_Server::READABLE
            ),
            '/event/datewise' => array(
                array($this, 'get_event_calendar_widget_datewise'), WP_JSON_Server::READABLE
            ),
            '/browse/all-categories-list' => array(
                array($this, 'get_all_categories_list_widget'), WP_JSON_Server::READABLE
            ),
        );

        return array_merge($routes, $post_routes);
    }

    /* Return popular posts in widget Popular Posts */
    public function get_popular_posts($post_type,$popular_per,$number) {

        if($popular_per == 'views'){

                $args_popular=array(
                        'post_type'=>$post_type,
                        'post_status'=>'publish',
                        'posts_per_page' => $number,
                        'meta_key'=>'viewed_count',
                        'orderby' => 'meta_value_num',
                        'meta_value_num'=>'viewed_count',
                        'order' => 'DESC'
                );		

        }elseif($popular_per == 'dailyviews'){

                $args_popular=array(
                        'post_type'=>$post_type,
                        'post_status'=>'publish',
                        'posts_per_page' => $number,
                        'meta_key'=>'viewed_count_daily',
                        'orderby' => 'meta_value_num',
                        'meta_value_num'=>'viewed_count_daily',
                        'order' => 'DESC'
                );

        }else{

                $args_popular=array(
                        'post_type'=>$post_type,
                        'post_status'=>'publish',
                        'posts_per_page' => $number,					
                        'orderby' => 'comment_count',					
                        'order' => 'DESC'
                );
        }

        $location_post_type = get_option('location_post_type');
        if(is_array($location_post_type) && !empty($location_post_type)){      
            foreach($location_post_type as $location_post_types)
            {
                      $post_types = explode(',',$location_post_types);
                      $post_type1[] = $post_types[0];
            }
        }                        

        /* filter for current city wise populer posts */
        if(is_plugin_active('Tevolution-LocationManager/location-manager.php') && in_array($post_type,$post_type1)){
                add_filter('posts_where', 'location_multicity_where');
        }

        $popular_post_query = new WP_Query($args_popular);

        if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
                remove_filter('posts_where', 'location_multicity_where');
        }
        
        $response = new WP_JSON_Response();
        $response->set_data($popular_post_query->posts);

        return $response;
    }
    
    /*	Function for getting recent comments -- */
    public function get_recent_review_comments($filter = array()){
                
                $g_size = isset($filter['g_size'])? $filter['g_size'] : 30;
                $no_comments = isset($filter['count'])? $filter['count'] : 10;
                $comment_lenth = isset($filter['comment_lenth'])? $filter['comment_lenth'] : 60;
                $post_type = isset($filter['post_type'])? $filter['post_type'] : 'post';
                $title = isset($filter['title'])? $filter['title'] : '';
            
		global $wpdb, $tablecomments, $tableposts,$rating_table_name;
		$tablecomments = $wpdb->comments;
		$tableposts    = $wpdb->posts;
		
		if(post_type_exists( $post_type )){
			$post_type = $post_type;
		}else{
			$post_type='post';
		}
		$args = array(
			'status' => 'approve',
			'karma' => '',
			'number' => $no_comments,
			'offset' => '',
			'orderby' => 'comment_date',
			'order' => 'DESC',
			'post_type' => $post_type,
		);
		$location_post_type = get_option('location_post_type');
                    if(is_array($location_post_type) && !empty($location_post_type)){  
			foreach($location_post_type as $location_post_types)
			{
				$post_types = explode(',',$location_post_types);
				$post_type1[] = $post_types[0];
			}
                    }
			
		/* filter for current city wise populer posts */
		if(is_plugin_active('Tevolution-LocationManager/location-manager.php') && in_array($post_type,$post_type1)){
			add_filter('comments_clauses','location_comments_clauses');
		}
		$comments = get_comments($args);
		/* remove filter for current city wise populer posts */
		if(is_plugin_active('Tevolution-LocationManager/location-manager.php') && in_array($post_type,$post_type1)){
			remove_filter('comments_clauses','location_comments_clauses');
		}
                $html = '';
		if($comments)
		{
			if( $title <> "")
			{
				$html .= ' <h3 class="widget-title">'.$title.'</h3>';
			}
			$html .= '<ul class="recent_comments">';
			foreach($comments as $comment)
			{ 
				$comment_id           = $comment->comment_ID;
				$comment_content      = strip_tags($comment->comment_content);		
				$comment_excerpt      = wp_trim_words( $comment_content, $comment_lenth, '' );
				$permalink            = get_permalink($comment->ID)."#comment-".$comment->comment_ID;
				$comment_author_email = $comment->comment_author_email;
				$comment_post_ID      = $comment->comment_post_ID;
				$post_title           = stripslashes(get_the_title($comment_post_ID));
				$permalink            = get_permalink($comment_post_ID);
				$size=60;
				$html .= "<li class='clearfix'><span class=\"li".$comment_id."\">";				
				if('' == @$comment->comment_type){
					$html .=  '<a href="'.$permalink.'">';
					if(get_user_meta($comment->user_id,'profile_photo',true)){
						$html .= '<img class="avatar avatar-'.absint( $size ).' photo" width="'.absint( $size ).'" height="'.absint( $size ).'" src="'.get_user_meta($comment->user_id,'profile_photo',true).'" />';
					}else{
						$html .= get_avatar($comment->comment_author_email, 60);
					}
					
					$html .= '</a>';
				}
				elseif( ('trackback' == $comment->comment_type) || ('pingback' == $comment->comment_type) ){
					$html .=  '<a href="'.$permalink.'">';
					if(get_user_meta($comment->user_id,'profile_photo',true)){
						$html .= '<img class="avatar avatar-'.absint( $size ).' photo" width="'.absint( $size ).'" height="'.absint( $size ).'" src="'.get_user_meta($comment->user_id,'profile_photo',true).'" />';
					}else{
						$html .= get_avatar($comment->comment_author_email, 60);
					}
					$html .= '</a>';
				}							
				$html .= "</span>\n";
				$html .= '<div class="review_info" >' ;
				$html .=  '<a href="'.$permalink.'" class="title">'.$post_title.'</a>';
				$tmpdata = get_option('templatic_settings');
				$rating_table_name = $wpdb->prefix.'ratings';
				if($tmpdata['templatin_rating'] == 'yes'):
					$post_rating = $wpdb->get_var("select rating_rating from $rating_table_name where comment_id='$comment_id'");
					if(function_exists('draw_rating_star_plugin')){
						/*fetch rating from tevolution.*/
						$html .= "<div class='rating'>".apply_filters('tmpl_show_tevolution_rating','',$post_rating)."</div>";
					}
				endif;
                                if(function_exists('tmpl_rating_html')){
                                    $html .= tmpl_rating_html($comment_id,true);
                                }
				$html .= $comment_excerpt;
				if(function_exists('supreme_prefix')){
					$pref = supreme_prefix();
				}else{
					$pref = sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );
				}
				$theme_options = get_option($pref.'_theme_settings');				
				if(isset($theme_options['templatic_excerpt_link']) && $theme_options['templatic_excerpt_link']!='')
				{
					$read_more = $theme_options['templatic_excerpt_link'];
				}
				else
				{
					$read_more = __('Read more &raquo;','templatic');
				}
				$view_comment = __('View the entire comment','templatic');
				$html .= "<a class=\"comment_excerpt\" href=\"" . $permalink . "\" title=\"".$view_comment."\">";
				$html .= "&nbsp;".$read_more;
				$html .= "</a></div>";
				$html .= '</li>';
			}
			$html .= "</ul>";
		}
                $result[] = $html; 
                $response = new WP_JSON_Response();
                $response->set_data($result);
                return $response;
	}
        
    /* Widget Browse by category and tag */
    public function get_browse_by_category_tag($filter = array()){
        
        global $current_cityinfo;
        $browseby = isset($filter['browseby'])? $filter['browseby'] : '';
        $categories_count = isset($filter['categories_count'])? $filter['categories_count'] : 0;
        $post_type = isset($filter['post_type']) ? apply_filters('widget_post_type', $filter['post_type']): '';

        /* Get all the taxonomies for this post type */
        $taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));

        if($browseby =='tags'){
                if($post_type!='post'){	
                        $taxo=$taxonomies[1];
                }else
                        $taxo='ptags';
        }else{
                if($post_type!='post'){	
                        $taxo=$taxonomies[0];
                }else
                        $taxo='category';
        }

        if(is_plugin_active('woocommerce/woocommerce.php') && $filter['post_type'] == 'product'){
                $taxo = $taxonomies[1];
        }
        
        $html = '';
        /* browse by tags */
        if($browseby =='tags'){ 
                $html .= '<div class="browse_by_tag">';
                $args = array( 'taxonomy' => $taxo );

                if ( false === ( $terms = get_transient( '_tevolution_query_browsetags'.$post_type.$cur_lang_code) )  && get_option('tevolution_cache_disable')==1) {
                        $terms = get_terms($taxo, $args);
                        set_transient( '_tevolution_query_browsetags'.$post_type.$cur_lang_code, $terms, 12 * HOUR_IN_SECONDS );				
                }elseif(get_option('tevolution_cache_disable')==''){
                        $terms = get_terms($taxo, $args);
                }

                if($terms):
                        $html .= '<ul>';
                        foreach ($terms as $term) {	
                                if($taxo !='' && $term->slug !=''){
                                    $html .= "<li><a href=". get_term_link($term->slug, $taxo).">". __($term->name,'templatic')."</a></li>";
                                } 
                        }
                        $html .= '</ul>';
                else:
                        $html .= __('No Tag Available','templatic');
                endif;
                
                $html .= '</div>';
        }else{
            /* browse by categories */

            $cat_args = array(
                                'taxonomy'=>$taxo,
                                'orderby' => 'name', 
                                'show_count' => $categories_count, 
                                'hide_empty'	=> 1,
                                'echo'     => 0,
                                'hierarchical' => 'true',
                                'pad_counts' => 0,
                                'title_li'=>'');	

            $transient_name=(!empty($current_cityinfo))? $current_cityinfo['city_slug']: '';		
            if ( false === ( $widget_category_list = get_transient( '_tevolution_query_browsecategories'.$post_type.$transient_name.$cur_lang_code )) && get_option('tevolution_cache_disable')==1 ) {
                    do_action('tevolution_category_query');
                    $widget_category_list =  wp_list_categories($cat_args);
                    set_transient( '_tevolution_query_browsecategories'.$post_type.$transient_name.$cur_lang_code, $widget_category_list, 12 * HOUR_IN_SECONDS );				
            }elseif(get_option('tevolution_cache_disable')==''){
                    do_action('tevolution_category_query');
                    $widget_category_list =  wp_list_categories($cat_args);			
            }
                    $html .= '<ul class="browse_by_category">';
                    $html .= $widget_category_list;
                    $html .= "</ul>";				

            }
            
            $result[] = $html; 
            $response = new WP_JSON_Response();
            $response->set_data($result);
            return $response;
    }
    
    /* Event plugin event-calender mouse hover return result */
    public function get_event_calendar_widget_datewise($filter = array()){
	global $post,$wpdb,$current_cityinfo;
        
        $todaydate = isset($filter['date'])? $filter['date'] : '';
        $urlddate = isset($filter['urlddate'])? $filter['urlddate'] : 0;

	$page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts where post_content like '%[calendar_event]%' and post_type='page' and post_status='publish' limit 0,1");
	if(function_exists('icl_object_id')){
		$page_id = icl_object_id($page_id, 'page', false);
	}
	/*set link of calendar as per wpml*/
	$thelink = get_permalink($page_id)."?cal_date=$urlddate";
	/*register post status recurring*/
	register_post_status( 'recurring' );
	/*query to fetch events to show on calendar widget*/
	
        $args = array( 'post_type' => 'event',
                        'posts_per_page' => 5,
                        'post_status' => array('recurring','publish'),
                        'meta_key' => 'st_date',
                        'orderby' => 'meta_value',
                        'order' => 'ASC',
                        'meta_query' => array(
                        'relation' => 'AND',
                            array(
                                    'key' => 'st_date',
                                    'value' => $todaydate,
                                    'compare' => '<=',
                                    'type' => 'DATE'
                            ),
                            array(
                                    'key' => 'end_date',
                                    'value' => $todaydate,
                                    'compare' => '>=',
                                    'type' => 'DATE'
                            ),
                            array(
                                    'key' => 'event_type',
                                    'value' => 'Regular event',
                                    'compare' => '=',
                                    'type'=> 'text'
                            ),				
                        )
                    );
	$location_post_type = get_option('location_post_type');
	if(is_array($location_post_type) && count($location_post_type) >1){
		$location_post_type = implode(',',$location_post_type);
	}else{
		$location_post_type = $location_post_type[0];
	}
	/*if location manager plugin is activated and events post type is selected in manage location than fetch events city wise*/
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php') && in_array('event',$location_post_type))
	{
		global $cityid;
		$cityid = $_REQUEST['city_id'];
		add_filter('posts_where', 'location_city_filter');
	}
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php')  && strstr($location_post_type,'event,'))
	{
		add_filter('posts_where', 'location_multicity_where');
	}
	
	$my_query1 = null;
	$my_query1 = new WP_Query($args);
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php') && in_array('event',$location_post_type))
	{
		remove_filter('posts_where', 'location_multicity_where');
	}
	$post_info = '';
	global $posts;
	
	if( $my_query1->have_posts() )
	{
		while ($my_query1->have_posts()) : $my_query1->the_post();
					
			/* separate out recurring events with regular events */
			$is_recurring = get_post_meta($post->ID,'event_type',true);
                      	/*html for recurring event in pop up*/
			if(tmpl_is_parent($post)){ 												
				$post_info .=' 
				<a class="event_title" href="'.get_permalink($post->ID).'">'.$post->post_title.'</a><small>'.
				'<span class="wid_event_list"><b class="label">'.__('Location :',EDOMAIN).'</b><b class="label_info">'.get_post_meta($post->ID,'address',true) .'</b></span>'.
				'<span class="wid_event_list"><b class="label">'.__('Start Date :',EDOMAIN).'</b><b class="label_info">'.get_formated_date(get_post_meta($post->ID,'st_date',true)).' '.get_formated_time(get_post_meta($post->post_parent,'st_time',true)) .'</b></span>'. 
				'<span class="wid_event_list"><b class="label">'.__('End Date :',EDOMAIN).'</b><b class="label_info">'.get_formated_date(get_post_meta($post->ID,'end_date',true)).' '.get_formated_time(get_post_meta($post->post_parent,'end_time',true)) .'</b></span></small>';
			}else{
				/*html for regular event in pop up*/
				if(strtolower($is_recurring) == strtolower('Regular event')){
					$post_info .=' 
					<a class="event_title" href="'.get_permalink($post->ID).'">'.$post->post_title.'</a><small>'.
					'<span class="wid_event_list"><b class="label">'.__('Location :',EDOMAIN).'</b><b class="label_info">'.get_post_meta($post->ID,'address',true) .'</b></span>'.
					'<span class="wid_event_list"><b class="label">'.__('Start Date :',EDOMAIN).'</b><b class="label_info">'.get_formated_date(get_post_meta($post->ID,'st_date',true)).' '.get_formated_time(get_post_meta($post->ID,'st_time',true)) .'</b></span>'. 
					'<span class="wid_event_list"><b class="label">'.__('End Date :',EDOMAIN).'</b><b class="label_info">'.get_formated_date(get_post_meta($post->ID,'end_date',true)).' '.get_formated_time(get_post_meta($post->ID,'end_time',true)) .'</b></span></small>';
				}
			}
			
		endwhile;
		if($my_query1->found_posts>5)
			$post_info .= "<a class=\"more_events\" href=\"$thelink\" >". __('View more',EDOMAIN) . "</a>";
	}else{
		$post_info .=__('No Event in this date',EDOMAIN);
	}
	
	$result[] = $post_info; 
        $response = new WP_JSON_Response();
        $response->set_data($result);
        return $response;
}

/* this function return the category icon, location theme only */
function tmpl_show_category_icon($term_icon){
    if($term_icon == '')
            $term_icon = get_stylesheet_directory_uri().'/images/map-icon.png';

    return '<img src="'.$term_icon.'" alt='.$term_icon.'/>';
}

public function get_all_categories_list_widget($filter = array()){
        global $current_cityinfo;
        $cur_lang_code = (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) ? ICL_LANGUAGE_CODE : 'en';
        $post_type = isset($filter['post_type'])? $filter['post_type'] : 'listing';
        $category_level = isset($filter['category_level'])? $filter['category_level'] : 1;
        $number_of_category = isset($filter['number_of_category'])? $filter['number_of_category'] : 6;
        $hide_empty_cat = isset($filter['hide_empty_cat'])? $filter['hide_empty_cat'] : 0;
        $taxonomies = get_object_taxonomies((object) array('post_type' => $post_type, 'public' => true, '_builtin' => true));
        
            $args5 = array(
                    'orderby' => 'name',
                    'taxonomy' => $taxonomies[0],
                    'order' => 'ASC',
                    'parent' => '0',
                    'show_count' => 0,
                    'hide_empty' => 0,
                    'pad_counts' => true,
                );
            
            $html = '';
            $html .= '<section class="category_list_wrap row">';
            
            /* set wp_categories on transient */
            if (get_option('tevolution_cache_disable') == 1 && false === ( $categories = get_transient('_tevolution_query_catwidget' . $post_type . $cur_lang_code) )) {
                    $categories = get_categories($args5);
                    set_transient('_tevolution_query_catwidget' . $post_type . $cur_lang_code, $categories, 12 * HOUR_IN_SECONDS);
            } elseif (get_option('tevolution_cache_disable') == '') {
                    $categories = get_categories($args5);
            }

            if (!isset($categories['errors'])) {
                foreach ($categories as $category) {
                        /* set child wp_categories on transient */

                $transient_name = (!empty($current_cityinfo)) ? $current_cityinfo['city_slug'] : '';
                if (get_option('tevolution_cache_disable') == 1 && false === ( $featured_catlist_list = get_transient('_tevolution_query_catwidget' . $category->term_id . $post_type . $transient_name . $cur_lang_code) )) {
                    do_action('tevolution_category_query');
                    $featured_catlist_list = wp_list_categories('title_li=&child_of=' . $category->term_id . '&echo=0&depth=' . $category_level . '&number=' . $number_of_category . '&taxonomy=' . $taxonomies[0] . '&show_count=1&hide_empty=' . $hide_empty_cat . '&pad_counts=0&show_option_none=');
                    set_transient('_tevolution_query_catwidget' . $category->term_id . $post_type . $transient_name . $cur_lang_code, $featured_catlist_list, 12 * HOUR_IN_SECONDS);
                } elseif (get_option('tevolution_cache_disable') == '') {
                    do_action('tevolution_category_query');
                    $featured_catlist_list = wp_list_categories('title_li=&child_of=' . $category->term_id . '&echo=0&depth=' . $category_level . '&number=' . $number_of_category . '&taxonomy=' . $taxonomies[0] . '&show_count=1&hide_empty=' . $hide_empty_cat . '&pad_counts=0&show_option_none=');
                }
                
                if (is_plugin_active('Tevolution-LocationManager/location-manager.php')) {
                    remove_filter('terms_clauses', 'locationwise_change_category_query', 10, 3);
                }
                
                $parent = get_term($category->term_id, $taxonomies[0]);
                if ($hide_empty_cat == 1) {
                   if ($parent->count != 0 || $featured_catlist_list != "") {
                                         
                        $html .= '<article class="category_list large-4 medium-4 small-6 xsmall-12 columns">';
                        
                        if ($parent) {
                            $parents = '<a href="' . get_term_link($parent, $taxonomies[0]) . '" title="' . esc_attr($parent->name) . '">' . apply_filters('list_cats', $parent->name, $parent) . '</a>';
                            if ($hide_empty_cat == 1) {
                                if ($parent->count != 0) {

                                    $html .= '<h3>';
                                    //do_action('show_categoty_map_icon', $parent->term_icon);
                                    if(function_exists('show_categoty_map_icon')){
                                        $html .= tmpl_show_category_icon($parent->term_icon);
                                    }
                                    $html .= $parents;
                                    $html .= '</h3>';
                                }
                            }else {
                        
                                $html .= '<h3>';
                                //do_action('show_categoty_map_icon', $parent->term_icon);
                                if(function_exists('show_categoty_map_icon')){
                                    $html .= tmpl_show_category_icon($parent->term_icon);
                                }
                                $html .= $parents;
                                $html .= '</h3>';
                            }

                            if (@$featured_catlist_list != "") {
                                if ($number_of_category != 0) {
                                     if ($parent->count == 0) {
                                
                                        $html .= '<h3>';
                                        //do_action('show_categoty_map_icon', $parent->term_icon);
                                        if(function_exists('show_categoty_map_icon')){
                                            $html .= tmpl_show_category_icon($parent->term_icon);
                                        }
                                        $html .= $parents;
                                        $html .= '</h3>';
                                    }
                                    $html .= '<ul>';
                                    $html .= $featured_catlist_list;
                                    $html .= '<li class="view"> <a href="'. get_term_link($parent, $taxonomies[0]) .'">';
                                    $html .= __('View all &raquo;', DIR_DOMAIN);
                                    $html .= '</a> </li></ul>';
                                }   
                            }
                        }
                        $html .= '</article>';
                    }
                } else {
                           
                        $html .= '<article class="category_list large-4 medium-4 small-6 xsmall-12 columns">';
                        if ($parent && $taxonomies[0]) {
                            $parents = '<a href="' . get_term_link($parent, $taxonomies[0]) . '" title="' . esc_attr($parent->name) . '">' . apply_filters('list_cats', $parent->name, $parent) . '</a>';
                            $html .= '<h3>';
                        
                            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                            if (is_plugin_active('Tevolution-CategoryIcon/tevolution-categoryicon.php')) {
                                if (@$parent->category_icon == '') {
                                    //do_action('show_categoty_map_icon', $term_icon);
                                    if(function_exists('show_categoty_map_icon')){
                                        $html .= tmpl_show_category_icon($term_icon);
                                    }
                                }
                            } else {
                                    //do_action('show_categoty_map_icon', $parent->term_icon);
                                    if(function_exists('show_categoty_map_icon')){
                                        $html .= tmpl_show_category_icon($parent->term_icon);
                                    }
                            }
                            $html .= $parents;
                            $html .= '</h3>';

                            if (@$featured_catlist_list != "") {
                                if ($number_of_category != 0) {

                                    $html .= '<ul>';
                                    $html .= $featured_catlist_list;
                                    $html .= '<li class="view"> <a href="'. get_term_link($parent, $taxonomies[0]) .'">';
                                    $html .=  __('View all &raquo;', DIR_DOMAIN);
                                    $html .= '</a> </li></ul>';
                                }
                            }
                        }
                        
                    $html .= '</article>';
                }
            }
        } else {
                
            $html .= '<p>' . __('Invalid Category.', DIR_DOMAIN) . '</p>';
        }
        $html .= '</section>';
 
        $result[] = $html; 
        $response = new WP_JSON_Response();
        $response->set_data($result);
        return $response;
    }
    
}