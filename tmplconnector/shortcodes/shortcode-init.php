<?php
/**
 * Shortcodes init
 */
/*Submit form page shortcode*/
include_once('shortcode_submit_form_page.php');
/* People listing shortcode */
include_once('shortcode_people.php');
/* People listing shortcode */
include_once('shortcode_post_upgrade.php');
	
include_once('shortcode_taxonomies_map.php');

function tevolution_map_page($atts)
{
	
	extract( shortcode_atts( array (
				'post_type'   =>'post',
				'image'       => 'thumbnail',
				'latitude'    => '21.167086220869788',
				'longitude'   => '72.82231945000001',
				'map_type'    => 'ROADMAP',
				'map_display' => '1',
				'zoom_level'  => '13',
				'height'      => '450'
				), $atts ) 
			);
	ob_start();
	remove_filter( 'the_content', 'wpautop' , 12);
	/*fetch the category by post type*/
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));	
	$cat_args = array(
				'taxonomy'=>$taxonomies[0],
				'orderby' => 'name', 				
				'hierarchical' => 'true',
				'title_li'=>''
			);	
	$r = wp_parse_args( $cat_args);	
	$catname_arr=get_categories( $r );
	
	$catinfo_arr = get_categories_postinfo($catname_arr,$post_type,$image);
	display_google_map($catinfo_arr,$atts,$catname_arr);
	
	return ob_get_clean();
}
/*
 * Function Name: display_google_map
 * Return: display the google map
 */
function display_google_map($catinfo_arr,$atts,$catname_arr)
{
	
	extract( shortcode_atts( array (
  		'post_type'   =>'post',
		'image'       => 'thumbnail',
		'latitude'    => '21.167086220869788',
		'longitude'   => '72.82231945000001',
		'map_type'    => 'ROADMAP',
		'map_display' => '1',
		'zoom_level'  => '13',
		'height'      => '450'
		), $atts ) );	
	
	
	wp_print_scripts( 'google-maps-apiscript');
	wp_print_scripts( 'google-clusterig');
	
	$google_map_customizer=get_option('google_map_customizer');/* store google map customizer required formate.*/
	?>
     <script type="text/javascript">
		var CITY_MAP_CENTER_LAT= '<?php echo $latitude?>';
		var CITY_MAP_CENTER_LNG= '<?php echo $longitude?>';
		var CITY_MAP_ZOOMING_FACT= <?php echo $zoom_level;?>;
		var infowindow;
		<?php if($map_display == 1) { ?>
		var multimarkerdata = new Array();
		<?php }?>
		var zoom_option = '<?php echo $map_display; ?>';
		var markers = {<?php echo $catinfo_arr;?>};
		
		/*var markers = '';*/
		var map = null;
		var mgr = null;
		var mc = null;
		var markerClusterer = null;
		var showMarketManager = false;
		var PIN_POINT_ICON_HEIGHT = 50;
		var PIN_POINT_ICON_WIDTH = 50;
		var infobox;
		if(MAP_DISABLE_SCROLL_WHEEL_FLAG)
		{
			var MAP_DISABLE_SCROLL_WHEEL_FLAG = 'No';	
		}
		
		function setCategoryVisiblity( category, visible ) {		
		   var i;
		   if ( mgr && category in markers ) {
			  for( i = 0; i < markers[category].length; i += 1 ) {
				 if ( visible ) {
					mgr.addMarker( markers[category][i], 0 );
				 } else {
					mgr.removeMarker( markers[category][i], 0 );
				 }
			  }
			  mgr.refresh();
		   }
		}
		function initialize() {
		  var isDraggable = jQuery(document).width() > 480 ? true : false;
		  var myOptions = {
			zoom: CITY_MAP_ZOOMING_FACT,
			draggable: isDraggable,
			center: new google.maps.LatLng(CITY_MAP_CENTER_LAT, CITY_MAP_CENTER_LNG),
			mapTypeId: google.maps.MapTypeId.<?php echo $map_type;?>
		  }
		   map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
		   mgr = new MarkerManager( map );
		   var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
		   map.setOptions({styles: styles});
		   google.maps.event.addListener(mgr, 'loaded', function() {
			  if (markers) {				  
				 for (var level in markers) {					 	
					google.maps.event.addDomListener( document.getElementById( level ), 'click', function() {
					   setCategoryVisiblity( this.id, this.checked );
					});	
					
					for (var i = 0; i < markers[level].length; i++) {						
					   var details = markers[level][i];					  
					   var image = new google.maps.MarkerImage(details.icons,new google.maps.Size(PIN_POINT_ICON_WIDTH, PIN_POINT_ICON_HEIGHT));
					   var myLatLng = new google.maps.LatLng(details.location[0], details.location[1]);
					   <?php if($map_display == 1) { ?>
						 multimarkerdata[i]  = new google.maps.LatLng(details.location[0], details.location[1]);
					   <?php } ?>
					   markers[level][i] = new google.maps.Marker({
						  title: details.name,
						  position: myLatLng,
						  icon: image,
						  clickable: true,
						  draggable: false,
						  flat: true
					   });					   
					   
					attachMessage(markers[level][i], details.message);
					}
					mgr.addMarkers( markers[level], 0 );
					
					/*New infobundle			*/
					 infoBubble = new InfoBubble({
						maxWidth:210,minWidth:210,minHeight:"auto",padding:0,content:details.message,borderRadius:0,borderWidth:0,borderColor:"none",overflow:"visible",backgroundColor:"#fff"
					  });			
					/*finish new infobundle*/
			
			/*Start			*/
                google.maps.event.addListener(markers, "click", function (e) {														    
					infoBubble.open(map, details.message);					
                });
			
					
				 }
				  <?php if($map_display == 1) { ?>
					 var latlngbounds = new google.maps.LatLngBounds();
					for ( var j = 0; j < multimarkerdata.length; j++ )
						{
						 latlngbounds.extend( multimarkerdata[ j ] );
						}
					   map.fitBounds( latlngbounds );
				  <?php } ?>
				 mgr.refresh();
			  }
		   });
		   
			/* but that message is not within the marker's instance data */
			function attachMessage(marker, msg) {
			  	var myEventListener = google.maps.event.addListener(marker, 'click', function() {
					infoBubble.setContent( msg );
					infoBubble.open(map, marker);															
				});
			}
			
		}
		
		google.maps.event.addDomListener(window, 'load', initialize);
		
		
	</script>
	<div class="map_sidebar">
     <div class="top_banner_section_in clearfix "> 
		 <div class="TopLeft"><span id="triggermap"></span></div>
		   <div class="TopRight"></div>
		   <div class="iprelative">
          <div id="map_canvas" style="width: 100%; height:<?php echo $height;?>px" class="map_canvas"></div>  
		 </div>
          <?php if($catname_arr):?>
               <div class="map_category" id="toggleID">
				<?php foreach($catname_arr as $catname):
				
				if($catname->term_icon != '')
						$term_icon=$catname->term_icon;
					else	
						$term_icon=apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL."tmplconnector/monetize/images/pin.png");
				
				 ?>
                         <label>
                         <input type="checkbox" value="<?php echo $catname->name;?>" checked="checked" id="<?php echo $catname->slug;?>" name="<?php echo $catname->slug;?>">
                         <img height="14" width="8" alt="" src="<?php echo $term_icon;?>"> <?php echo esc_attr(urldecode($catname->name));?>
                         </label> 
                    <?php endforeach;?>
               </div>
               <div id="toggle_category" class="toggleon" onclick="toggle_category();"></div>
          <?php endif;?>	          
     </div>
     </div>
     <script type="text/javascript">
	 
	 var maxMap = document.getElementById( 'triggermap' );		
		google.maps.event.addDomListener(maxMap, 'click', showFullscreen);
		function showFullscreen() {
			  /* window.alert('DIV clicked');*/
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
					 /*map.setCenter(darwin);*/
					 window.setTimeout(function() { 
					var center = map.getCenter(); 
					google.maps.event.trigger(map, 'resize'); 
					map.setCenter(center); 
			   		}, 100);			 }
	function toggle_category(){
			var div1 = document.getElementById('toggleID');
			if (div1.style.display == 'none') {
				div1.style.display = 'block';
			} else {
				div1.style.display = 'none';
			}
			
			if(document.getElementById('toggle_category').getAttribute('class') == 'paf_row toggleoff'){		
				jQuery("#toggle_category").removeClass("paf_row toggleoff").addClass("paf_row toggleon");
			} else {		
				jQuery("#toggle_category").removeClass("paf_row toggleon").addClass("paf_row toggleoff");
			}
			
			if(document.getElementById('toggle_category').getAttribute('class').search('toggleoff')!=-1 && document.getElementById('toggle_category').getAttribute('class').search('map_category_fullscreen') !=-1){		
				jQuery("#toggle_category").removeClass("paf_row toggleoff map_category_fullscreen").addClass("paf_row toggleon map_category_fullscreen");
			} 
			if(document.getElementById('toggle_category').getAttribute('class').search('toggleon') !=-1 && document.getElementById('toggle_category').getAttribute('class').search('map_category_fullscreen') !=-1){
				jQuery("#toggle_category").removeClass("paf_row toggleon map_category_fullscreen").addClass("paf_row toggleoff map_category_fullscreen");
			}
		}
	</script>
     
     <?php
}
/*
 * Function name: get_categories_post_info
 * Return: post info array for display on google map
 */
function get_categories_postinfo($catname_arr,$post_type,$map_image_size='thumbnail')
{
	remove_all_actions('posts_where');
	foreach($catname_arr as $cat)
	{	
		$catname=$cat->slug;
		$cat_ID=$cat->term_id;		
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));					
		
		$args=apply_filters('map_shortcode',array( 
				   'post_type'      => trim($post_type),
				   'posts_per_page' => -1    ,
				   'post_status'    => 'publish',             
				   'tax_query'      => array(                
									  array(
										 'taxonomy' =>$taxonomies[0],
										 'field'    => 'id',
										 'terms'    => $cat_ID,
										 'operator' => 'IN'
									  )            
				    				   ),        
				   'order_by'       =>'date',
				   'order'          => 'ASC'
			   ),$taxonomies[0],$cat_ID);		  	  		 
		$post_details= new WP_Query($args);
		$content_data='';
		global $post;
		if ($post_details->have_posts()) :
			$srcharr = array("'");
			$replarr = array("\'");
			while ( $post_details->have_posts() ) : $post_details->the_post();									
					$ID =get_the_ID();	
					if($post->post_parent){
						$ID =$post->post_parent;	
					}
					$title = get_the_title($ID);
					$plink = get_permalink($ID);
					$lat = get_post_meta($ID,'geo_latitude',true);
					$lng = get_post_meta($ID,'geo_longitude',true);					
					$address = str_replace($srcharr,$replarr,(get_post_meta($ID,'address',true)));
					/*$contact = str_replace($srcharr,$replarr,(get_post_meta($ID,'contact',true)));*/
					/*$timing = str_replace($srcharr,$replarr,(get_post_meta($ID,'timing',true)));		*/
					/*Fetch the image for display in map */
					if ( has_post_thumbnail()){
						$post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), $map_image_size);						
						$post_images = @$post_img[0];
					}else{
						$post_img = bdw_get_images_plugin($ID,$map_image_size);					
						$post_images = @$post_img[0]['file'];
					}
					
					$imageclass='';
					if($post_images)
						$post_image='<div class=map-item-img><a href="'.$plink.'"><img src="'.$post_images.'"  width=120 height=160/></a></div>';
					else{
						$post_image='';
						$imageclass='no_map_image';
					}
					
					$image_class=($post_image)?'map-image' :'';
					if($cat->term_icon != '')
						$term_icon=$cat->term_icon;
					else	
						$term_icon=apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL."tmplconnector/monetize/images/pin.png");
						
					$name_title=html_entity_decode($title);
					if(!isset($more)){ $more='...'; } 
					if($lat && $lng)
					{ 
						$retstr ="{";
						$retstr .= "'name':'$name_title',";
						$retstr .= "'location': [$lat,$lng],";						
						$retstr .= "'message':'<div class=\"google-map-info $image_class forrent\"><div class=map-inner-wrapper><div class=\"map-item-info ".$imageclass."\">$post_image";
						$retstr .= "<h6><a href=\"$plink\" class=\"ptitle\" style=\"color:#444444;font-size:14px;\"><span>$title</span></a></h6>";
						if($address){$retstr .= "<span style=\"font-size:10px;\">$address</span>";}
						$retstr .= "<p class=\"link-style1\"><a href=\"$plink\" class=\"$title\">$more</a></div></div></div>";
						$retstr .= "',";
						$retstr .= "'icons':'$term_icon',";
						$retstr .= "'pid':'$ID'";
						$retstr .= "}";						
						$content_data[] = $retstr;
					}				
			endwhile;	
			wp_reset_query();
		endif;
		if($content_data)	
			$cat_content_info[]= "'$catname':[".implode(',',$content_data)."]";			
	}	
	if($cat_content_info!="")	
		return implode(',',$cat_content_info);
	else
		return '';		
}
/* display email protect from spam boat*/
function tev_email_encode( $atts, $email ){
	$atts = extract( shortcode_atts( array('email'=>$email),$atts ));
	
	if(function_exists('antispambot')){
		return '<a href="'.antispambot("mailto:".$email).'">'.antispambot($email).'</a>';
		}
}
add_shortcode( 'email', 'tev_email_encode' ); /* protect from spambot*/
/**
 * Shortcode creation
 **/
 
add_shortcode('post_upgrade', 'tevolution_post_upgrade_template');
add_shortcode('submit_form', 'tevolution_form_page_template');
add_shortcode('advance_search_page', 'tevolution_advance_search_page');
add_shortcode('map_page', 'tevolution_map_page');
add_shortcode('tevolution_author_list', 'tevolution_author_list_fun');
add_shortcode('tevolution_listings_map', 'tevolution_all_list_map');
?>
