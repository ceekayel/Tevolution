<?php
add_filter('body_class', 'map_body_class',11);
function map_body_class($classes) {
	global $post;
	
	$template = get_post_meta($post->ID,'_wp_page_template',true);
	if($template =='page-templates/full-page-map.php'){
		$classes[] = 'full-width-map';
		return $classes;
	}else{
		$classes[] = '';
		return $classes;	
	}
}
/* tevolution_all_list_map - All Listings on Map */
function tevolution_all_list_map($atts ){
		global $current_cityinfo;
		extract( shortcode_atts( array (
				'post_type'   => 'post',
				'latitude'    => '21.167086220869788',
				'longitude'   => '72.82231945000001',
				'map_type'    => 'ROADMAP',
				'map_display' => '',
				'zoom_level'  => 13,
				'clustering'  => 1,
				'height'      => 900,
				), $atts ) 
			);
		$clustering = $clustering;
		if($post_type !=''){
			$post_info=(strstr($post_type,','))? explode(',',$post_type):explode(',',$post_type) ;
		}else{
			$post_info= array('post');
		}		
		$jsAdmin = TEMPL_PLUGIN_URL."/js/taxonomiesmap.js";
		wp_register_script( 'wp_show_taxonomies_map_js', $jsAdmin);
		wp_enqueue_script( 'wp_show_taxonomies_map_js');
		
		$mapcategory_info =get_shortcode_map_categoryinfo($post_info);	
		//$mappost_info =get_map_postinfo($post_info);	
		
		
		$tmpdata = get_option('templatic_settings');			
		$maptype = $map_type;		
		$latitude    = $latitude;
		$longitude   = $longitude;
		$map_type    = $map_type;
		$map_display = $map_display;
		$zoom_level  = $zoom_level;
		
		wp_print_scripts( 'google-maps-apiscript' );		
		wp_print_scripts( 'google-clusterig' );		
		wp_print_scripts( 'wp_show_taxonomies_map_js' );
		
		$google_map_customizer=get_option('google_map_customizer');// store google map customizer required formate.
		if(!isset($_GET['h'])){
			$PHP_SELF = $_SERVER['PHP_SELF'];
		?>	
		 <script type="text/javascript">	
			window.location.href = "?h=" + screen.availHeight;
		 </script>
		<?php }
		
		if(!ctype_digit($_GET['h'])){
			$PHP_SELF = $_SERVER['PHP_SELF'];
		?>	
		 <script type="text/javascript">	
			window.location.href = "?h=" + screen.availHeight;
		 </script>
		<?php }	?>
        <script type="text/javascript">
		
		var map_latitude= '<?php echo $latitude?>';
		var map_longitude= '<?php echo $longitude?>';
		var map_zomming_fact= <?php echo $zoom_level;?>;		
		<?php if($map_display == 1) { ?>
		var multimarkerdata = new Array();
		<?php }?>
		var zoom_option = '<?php echo $map_display; ?>';
		var markers = '';
		var markerArray = [];	
		var ClustererMarkers=[];
		var m_counter=0;
		var map = null;
		var mgr = null;
		var mc = null;		
		var mClusterer = null;
		var showMarketManager = false;
		var PIN_POINT_ICON_HEIGHT = 32;
		var PIN_POINT_ICON_WIDTH = 20;
		var clustering = '<?php echo $clustering; ?>';
		var infobox;
		var infoBubble;
		function initialize(){		
			var isDraggable = jQuery(document).width() > 480 ? true : false;
			var myOptions = {
				scrollwheel: false,
				zoom: map_zomming_fact,
				draggable: isDraggable,
				center: new google.maps.LatLng(map_latitude, map_longitude),
				mapTypeId: google.maps.MapTypeId.<?php echo $map_type;?>
			}
			map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
			var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
			map.setOptions({styles: styles});
			// Initialize Fluster and give it a existing map		 
			mgr = new MarkerManager( map );
			
		}
		
		google.maps.event.addDomListener(window, 'load', initialize);
		google.maps.event.addDomListener(window, 'load', taxo_googlemap_initialize);
		</script>      
		<div class="full_map_page full_map_template">
          <div class="map_sidebar">
          <div class="top_banner_section_in clearfix">
               <div class="TopLeft"><span id="triggermap"></span></div>
               <div class="TopRight"></div>
               <div class="iprelative">
               	<div id="map_canvas" style="width: 100%; height:<?php echo $_GET['h']; ?>; " class="map_canvas"></div>               
                    <div id="map_loading_div" style="width: 100%; height:<?php echo $_GET['h']-200; ?>px; display: none;"></div>                     
                    <div id="map_marker_nofound"><?php _e('<p>Your selected category do not have any records yet at your current location.</p>',LM_DOMAIN) ?></div>     
               </div>             
              
               <form id="ajaxform" name="slider_search" class="pe_advsearch_form" action="javascript:void(0);"  onsubmit="return(new_googlemap_ajaxSearch());">
                	<div class="paf_search"><input  type="text" class="" id="search_string" name="search_string" value="" placeholder="<?php _e('Title or Keyword',DOMAIN);?>" onclick="this.placeholder=''" onmouseover="this.placeholder='<?php _e('Title or Keyword',DOMAIN);?>'"/></div>
               
               <?php if($post_info):?>
               	<div class="paf_row map_post_type" id="toggle_postID" style="display:block;">
                    <?php for($c=0;$c<count($post_info);$c++):
							if(@$post_info[$c])
							{
								$PostTypeObject = get_post_type_object($post_info[$c]);
								$_PostTypeName = $PostTypeObject->labels->name;?>
					<div class="mw_cat_title">
                    <label><input type="checkbox" data-category="<?php echo str_replace("&",'&amp;',$post_info[$c]).'categories';?>" onclick="taxo_googlemap_initialize(this);"  value="<?php echo str_replace("&",'&amp;',$post_info[$c]);?>"  <?php if(!empty($_POST['posttype']) && !in_array(str_replace("&",'&amp;',$post_info[$c]) ,$_POST['posttype'])):?> <?php else:?> checked="checked" <?php endif;?> class="<?php echo str_replace("&",'&amp;',$post_info[$c]).'custom_categories';?>" id="<?php echo str_replace("&",'&amp;',$post_info[$c]).'custom_categories';?>" name="posttype[]"> <?php echo ucfirst($_PostTypeName);?></label><span id='<?php echo $post_info[$c].'_toggle';?>' class="toggle_post_type toggleon" onclick="custom_post_type_taxonomy('<?php echo $post_info[$c].'categories';?>',this)"></span></div>
                         <div class="custom_categories <?php echo str_replace("&",'&amp;',$post_info[$c]).'custom_categories';?>" id="<?php echo str_replace("&",'&amp;',$post_info[$c]).'categories';?>" >
                         	 <?php foreach($mapcategory_info[$post_info[$c]] as $key => $value){ ?>
                    				<label><input type="checkbox" onclick="taxo_googlemap_initialize(this);"  value="<?php echo $value['slug'];?>"  <?php if(!empty($_POST['categoryname']) && !in_array($key,$_POST['categoryname'])):?> <?php else:?> checked="checked" <?php endif;?> id="<?php echo $key;?>" name="categoryname[]"><img height="14" width="8" alt="" src="<?php echo $value['icon']?>"> <?php echo $value['name']?></label>
                    
                    <?php }?>
                         </div>
                         
                    <?php }
						endfor;?>
                    </div>
                    <div id="toggle_post_type" class="paf_row toggleon" onclick="toggle_post_type();"></div>
                    <?php endif;?>
               </form>     
               
          </div>
          </div>
		 </div>
        <script>
		var maxMap = document.getElementById( 'triggermap' );		
		google.maps.event.addDomListener(maxMap, 'click', showFullscreen);
		function showFullscreen() {
			  // window.alert('DIV clicked');
				jQuery('#map_canvas').toggleClass('map-fullscreen');
				jQuery('.map_category').toggleClass('map_category_fullscreen');
				jQuery('.map_post_type').toggleClass('map_category_fullscreen');
				jQuery('#toggle_post_type').toggleClass('map_category_fullscreen');
				jQuery('#trigger').toggleClass('map_category_fullscreen');
				jQuery('body').toggleClass('body_fullscreen');
				jQuery('#loading_div').toggleClass('loading_div_fullscreen');
				jQuery('#advmap_nofound').toggleClass('nofound_fullscreen');
				jQuery('#triggermap').toggleClass('triggermap_fullscreen');
				
				jQuery('.TopLeft').toggleClass('TopLeft_fullscreen');		
					 //map.setCenter(darwin);
					 window.setTimeout(function() { 
					var center = map.getCenter(); 
					google.maps.event.trigger(map, 'resize'); 
					map.setCenter(center); 
			   		}, 100);			 }
		</script>
<?php
}
function get_shortcode_map_categoryinfo($post_type){
	for($i=0;$i<count($post_type);$i++){		
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type[$i],'public'   => true, '_builtin' => true ));	
		$cat_args = array(
					'taxonomy'=>$taxonomies[0],
					'orderby' => 'name', 				
					'hierarchical' => 'true',
					'title_li'=>''
				);	
		$r = wp_parse_args( $cat_args);	
		$catname_arr=get_categories( $r );	
		$categoriesinfo='';
		foreach($catname_arr as $cat)	{			
			if(@$cat->term_icon)
				$term_icon=$cat->term_icon;
			else
				$term_icon=TEVOLUTION_LOCATION_URL.'images/pin.png';			
			
			
				$categoriesinfo[]=array( 'slug'=> @$cat->slug,'name'=>$cat->name,'icon'=>$term_icon);	
			
			
		}
		$catinfo_arr[$post_type[$i]]=$categoriesinfo;
	}		
	return $catinfo_arr;
}


add_action('wp_ajax_nopriv_taxonomies_googlemap_initialize','taxonomies_googlemap_initialize');
add_action('wp_ajax_taxonomies_googlemap_initialize','taxonomies_googlemap_initialize');
function taxonomies_googlemap_initialize(){
	global $wpdb,$current_cityinfo;
	$j=0;
	$pids=array("");
	$post_type =(explode(',',substr($_REQUEST['posttype'],0,-1)));
	$categoryname =(explode(',',substr($_REQUEST['categoryname'],0,-1)));
	$templatic_settings=get_option('templatic_settings');
	
	for($i=0;$i<count($post_type);$i++){
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type[$i],'public'   => true, '_builtin' => true ));	
		$cat_args = array(
					'taxonomy'=>$taxonomies[0],
					'orderby' => 'name', 
					'order'=>'ASC',
					'hierarchical' => 'true',
					'title_li'=>''
				);
		$r = wp_parse_args( $cat_args);	
		$catname_arr=get_categories( $r );	
		foreach($catname_arr as $cat)	{
			$catname=$cat->slug;
			if(!in_array($cat->slug,$categoryname))
				continue;
				
			$cat_ID.=$cat->term_id.',';
		}
				
			$args3=array('post_type'      => trim($post_type[$i]),
				   'posts_per_page' => -1    ,
				   'post_status'    => 'publish',     
				   'tax_query'      => array(                
									  array(
										 'taxonomy' => $taxonomies[0],
										 'field'    => 'id',
										 'terms'    => explode(',',$cat_ID),
										 'operator' => 'IN'
									  )            
								   ), 					  
				   'order_by'       =>'date',
				   'order'          => 'ASC'
				   );	
			
			add_filter( 'posts_where', 'taxonomies_google_search_posts_where', 10, 2 );
			$post_details= new WP_Query($args3);			
			remove_filter( 'posts_where', 'taxonomies_google_search_posts_where', 10, 2 );	
			$content_data='';					
			if ($post_details->have_posts()) :
				$srcharr = array("'","<br />","\n","\r");
				$replarr = array("\'","","","");				
				while ( $post_details->have_posts() ) : $post_details->the_post();	
						$post_categories = get_the_terms( get_the_ID() ,$taxonomies[0]);
						foreach($post_categories as $post_category)
						if($post_category->term_icon){
							$term_icon=$post_category->term_icon;
							break;
						}else{
							$term_icon=TEMPL_PLUGIN_URL.'images/pin.png';
						}
						
						$ID =get_the_ID();				
						$title = get_the_title($ID);
						$plink = get_permalink($ID);
						$lat = get_post_meta($ID,'geo_latitude',true);
						$lng = get_post_meta($ID,'geo_longitude',true);					
						$address = stripcslashes(str_replace($srcharr,$replarr,(get_post_meta($ID,'address',true))));
						$contact = str_replace($srcharr,$replarr,(get_post_meta($ID,'phone',true)));
						$website = get_post_meta($ID,'website',true);
						/*Fetch the image for display in map */
						if ( has_post_thumbnail()){
							$post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');						
							$post_images=$post_img[0];
						}else{
							$post_img = bdw_get_images_plugin($ID,'thumbnail');					
							$post_images = $post_img[0]['file'];
						}
						
						$imageclass='';
						if($post_images)
							$post_image='<div class=map-item-img><a href='.$plink.'><img src='.$post_images.' width=150 height=150/></a></div>';
						else{
							$post_image='';
							$imageclass='no_map_image';
						}
						
						$image_class=($post_image)?'map-image' :'';
						$comment_count= count(get_comments(array('post_id' => $ID)));
						$review=($comment_count <=1 )? __('review',DOMAIN):__('reviews',DOMAIN);	
						
						if(($lat && $lng )&& !in_array($ID,$pids))
						{ 	
							$retstr ='{';
							$retstr .= '"name":"'.(str_replace($title_srcharr,$title_replarr,$post->post_title)).'",';
							$retstr .= '"location": ['.$lat.','.$lng.'],';
							$retstr .= '"message":"<div class=\"google-map-info '.$image_class.'\"><div class=map-inner-wrapper><div class=\"map-item-info '.$imageclass.'\">'.$post_image;
							$retstr .= '<h6><a href='.$plink.' class=ptitle><span>'.$title.'</span></a></h6>';							
							if($address){$retstr .= '<p class=address>'.trim($address).'</p>';}
							$retstr .= apply_filters('tmpl_map_after_address_fields','',$ID );
							if($contact){$retstr .= '<p class=contact>'.ltrim(rtrim($contact)).'</p>';}
							$retstr .= apply_filters('tmpl_map_after_contact_fields','',$ID );
							if($website){$retstr .= '<p class=website><a href= '.trim($website).'>'.trim($website).'</a></p>';}
							$retstr .= apply_filters('tmpl_map_custom_fields','',$ID );
							if($templatic_settings['templatin_rating']=='yes'){
								$rating=draw_rating_star_plugin(get_post_average_rating(get_the_ID()));
								$retstr .= '<div class=map_rating>'.str_replace('"','',$rating).' <span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
							}elseif(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('single_average_rating')){
								$rating=get_single_average_rating(get_the_ID());
								$retstr .= '<div class=map_rating>'.stripcslashes(str_replace('"','',$rating)).'<span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
							}
							$retstr .= '</div></div></div>';
							$retstr .= '",';
							$retstr .= '"icons":"'.$term_icon.'",';
							$retstr .= '"pid":"'.$ID.'"';
							$retstr .= '}';
							$content_data[] = $retstr;
							$j++;
						}	
						
						$pids[]=$ID;
						
				endwhile;	
				wp_reset_query();
				wp_reset_postdata();
			endif;
			
			if($content_data)	
				$cat_content_info[]= implode(',',$content_data);
	}
	
	//
	if($cat_content_info)
	{
		echo '[{"totalcount":"'.$j.'",'.substr(implode(',',$cat_content_info),1).']';
	}else
	{
		echo '[{"totalcount":"0"}]';
	}
	
	exit;
}
/*
 * Function name: google_search_posts_where
 * Return : pass the search post title
 */
function taxonomies_google_search_posts_where( $where, &$wp_query){
	global $wpdb;	
	
	if(isset($_REQUEST['search_string']) && $_REQUEST['search_string']!=''){
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $_REQUEST['search_string']) ) . '%\'';
		
		$where .= " OR  ($wpdb->posts.ID in (select p.ID from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p where c.name like '".esc_sql( like_escape( $_REQUEST['search_string']) )."' and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.post_status = 'publish' group by  p.ID))";
	}	
	return $where;	
}
?>
