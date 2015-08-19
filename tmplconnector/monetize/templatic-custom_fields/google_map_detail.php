<?php
/* show google map on detail page. */
$is_edit='';
if(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'){
	$is_edit=1;
}
wp_print_scripts( 'google-maps-apiscript' );
/* show map on detail page */
$zoom_level=($zooming_factor!="")?$zooming_factor:'13';
 if($geo_latitude && $geo_longitude){
$address=($post->ID)?get_post_meta($post->ID,'address',true) :$_REQUEST['address'];
$taxonomies = get_object_taxonomies( (object) array( 'post_type' => get_post_type($post->ID),'public'   => true, '_builtin' => true ));	
$post_categories = get_the_terms( $post->ID ,$taxonomies[0]);
if(!empty($post_categories)){
foreach($post_categories as $post_category){
	if($post_category->term_icon){
		$term_icon=$post_category->term_icon;
		break;
	}
}
}
$term_icon=(isset($term_icon) && $term_icon!="")?$term_icon:apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png');
$height = (!empty($heigh)) ? $heigh : 450; /* height is taken from "google-maps\direction_map_widget.php" if map widget is used in sidebar */
?>
<?php do_action('before_google_map_container');?>   
    <div id="map-container" style="height:<?php echo $height; ?>px;"></div>
<?php do_action('after_google_map_container');?>   

<div class=" get_direction clearfix">
<form action="" method="post" onsubmit="get_googlemap_directory(); return false;">
<input id="to-input" type="hidden" value="<?php echo $address;?>" placeholder="<?php _e('Enter a location',DOMAIN)?>"/>

<div class="google-map-directory">

<input id="from-input" type="text" placeholder="<?php _e('Enter Location',DIR_DOMAIN);?>" value="" /> 

<a href="javascript:void(0);" onclick="return set_direction_map()" class="b_getdirection getdir button" > <?php _e('Get Directions',DIR_DOMAIN);?> </a>
<a class="large_map b_getdirection button" target="_blank" href="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo urlencode($address);?>&amp;sll=<?php echo $geo_latitude;?>,<?php echo $geo_longitude;?>&amp;ie=UTF8&amp;hq=&amp;ll=<?php echo $geo_latitude;?>,<?php echo $geo_longitude;?>&amp;spn=0.368483,0.891953&amp;z=14&amp;iwloc=A"><?php _e('View Large Map',DIR_DOMAIN);?></a>
</div>
<div id="detail_map" style="display:none;">
<a href="javascript:void(0);" onclick="return Demo.get_closeDirections();" class="hide_map_direction" style="display:none"><i class="fa fa-times"></i></a>
<select onchange="Demo.getDirections();" id="travel-mode-input" style="display:none;">
  <option value="driving" selected="selected"><?php _e('By car',DIR_DOMAIN);?></option>
  <option value="transit"><?php _e('By public transit',DIR_DOMAIN);?></option>
  <option value="bicycling"><?php _e('By Bicycling',DIR_DOMAIN);?></option>
  <option value="walking"><?php _e('By Walking',DIR_DOMAIN);?></option>
</select>
<select onchange="Demo.getDirections();" id="unit-input" style="display:none;">
  <option value="metric"  selected="selected"><?php _e('Metric',DIR_DOMAIN);?></option>
  <option value="imperial"><?php _e('Imperial',DIR_DOMAIN);?></option>
</select>
</div>
</form>
<?php
$address = get_post_meta($post->ID,'address',true);
$address = str_replace('++','+',str_replace(' ','+',str_replace(',','+',$address)));
$google_map_customizer=get_option('google_map_customizer');/* store google map customizer required formate. */
?>
<div id="dir-container"></div>
</div>
<?php 
/* Include map only on detail page */

/*if(!is_page())*/
{ ?>
	<script type="text/javascript" async >
	function get_googlemap_directory(){
		set_direction_map();
	}
	function set_direction_map()
	{
		if(document.getElementById('from-input').value=="<?php _e('Enter Location',DIR_DOMAIN);?>" || document.getElementById('from-input').value=='')
		{
			alert("<?php _e('Please enter your address to get the direction map.',DIR_DOMAIN);?>");return false;
		}else
		{
			document.getElementById('travel-mode-input').style.display='';
			document.getElementById('detail_map').style.display='';
			document.getElementById('unit-input').style.display='';
			Demo.getDirections();	
		}
	}
	var currentReverseGeocodeResponse;
	var marker;
	var panorama;

	var Demo = {
	  /* HTML Nodes */
	  mapContainer: document.getElementById('map-container'),
	  dirContainer: document.getElementById('dir-container'),
	  fromInput: document.getElementById('from-input'),
	  toInput: document.getElementById('to-input'),
	  travelModeInput: document.getElementById('travel-mode-input'),
	  unitInput: document.getElementById('unit-input'),
	  /* API Objects */
	  dirService: new google.maps.DirectionsService(),
	  dirRenderer: new google.maps.DirectionsRenderer(),
	  map: null,
	  showDirections: function(dirResult, dirStatus) {
		if (dirStatus != google.maps.DirectionsStatus.OK) {
		  alert('Directions failed: ' + dirStatus);
		  return;
		}
		/* Show directions */
		Demo.dirRenderer.setMap(Demo.map);
		jQuery('.hide_map_direction').show();
		Demo.dirRenderer.setPanel(Demo.dirContainer);
		Demo.dirRenderer.setDirections(dirResult);
	  },
	  hideDirections: function (dirResult, dirStatus){	
		/* Hide directions */
		Demo.init();
		jQuery('.hide_map_direction').hide();
		/*hide by car and metric select box when we click on direction close button*/
		jQuery('#detail_map').hide();
		Demo.dirRenderer.setPanel();
		
	  },
	  get_closeDirections: function(){  
	  /* Close get direction results */
		var fromStr = Demo.fromInput.value;
		var toStr = Demo.toInput.value;
		var dirRequest = {
			origin: fromStr,
			destination: toStr,
			travelMode: Demo.getSelectedTravelMode(),
			unitSystem: Demo.getSelectedUnitSystem(),
			provideRouteAlternatives: true
		};
		Demo.dirService.route(dirRequest, Demo.hideDirections);
	  },
	  getSelectedTravelMode: function() {
		var value =Demo.travelModeInput.options[Demo.travelModeInput.selectedIndex].value;
		if (value == 'driving') {
		  value = google.maps.DirectionsTravelMode.DRIVING;
		} else if (value == 'bicycling') {
		  value = google.maps.DirectionsTravelMode.BICYCLING;
		} else if (value == 'walking') {
		  value = google.maps.DirectionsTravelMode.WALKING;
		}else if (value == 'transit') {
		  value = google.maps.DirectionsTravelMode.TRANSIT;
		} else {
		  alert('Unsupported travel mode.');
		}
		return value;
	  },
	  getSelectedUnitSystem: function() {
		return Demo.unitInput.options[Demo.unitInput.selectedIndex].value == 'metric' ?
			google.maps.DirectionsUnitSystem.METRIC :
			google.maps.DirectionsUnitSystem.IMPERIAL;
	  },
	  getDirections: function() {
		var fromStr = Demo.fromInput.value;
		var toStr = Demo.toInput.value;
		var dirRequest = {
		  origin: fromStr,
		  destination: toStr,
		  travelMode: Demo.getSelectedTravelMode(),
		  unitSystem: Demo.getSelectedUnitSystem(),
		  provideRouteAlternatives: true
		};
		Demo.dirService.route(dirRequest, Demo.showDirections);
	  },
		
		
	init: function() {
		  var geo_latitude= (jQuery('#geo_latitude').val()!='' && jQuery('#geo_latitude').length!=0) ? jQuery('#geo_latitude').val()  : <?php echo $geo_latitude;?>;
		  var geo_longitude=(jQuery('#geo_longitude').val()!='' && jQuery('#geo_longitude').length!=0) ? jQuery('#geo_longitude').val(): <?php echo $geo_longitude;?>;
		  var latLng = new google.maps.LatLng(geo_latitude, geo_longitude);

		  var isDraggable = jQuery(document).width() > 480 ? true : false;
		  Demo.map = new google.maps.Map(Demo.mapContainer, {  
			zoom: <?php echo $zoom_level;?>,
			scrollwheel: false,
			draggable: isDraggable,
			center: latLng,	  
			<?php if($map_type=='Road Map' || $map_type=='Satellite Map'|| $map_type=='Terrain Map'){
			if($map_type=='Satellite Map') { $map_type = SATELLITE; } elseif($map_type=='Terrain Map') { $map_type = @TERRAIN; } else { $map_type = ROADMAP; } ?>
			mapTypeId: google.maps.MapTypeId.<?php echo $map_type;?>
			<?php }else{?>
			mapTypeId: google.maps.MapTypeId.ROADMAP
			<?php }?>
		  });
		<?php if(strtolower($map_type) == strtolower('Street map')):?>
		 /* panorama = new google.maps.StreetViewPanorama(document.getElementById('map-container')); */
		  var latLng = Demo.map.getCenter();  
		  panorama = Demo.map.getStreetView();  
		  panorama.setPosition(latLng);
		  var sv = new google.maps.StreetViewService();	   
		  sv.getPanoramaByLocation(latLng, 50, processSVData);
		  panorama.setPov(/** @type {google.maps.StreetViewPov} */({
			heading: 265,
			pitch: 0
		  }));
		<?php else:?>
			marker = new google.maps.Marker({
				position: latLng, 
				map: Demo.map,
				icon: '<?php echo $term_icon; ?>',
				<?php if($is_edit==1):?>
				draggable: true,
				<?php endif;?>
				title:"<?php echo trim(str_replace('"','\"',$post->post_title));?>"
			});  
		<?php endif;?>	
		
		var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
		Demo.map.setOptions({styles: styles});
		
		 /* Initialize autocomplete. */
			var inputField = document.getElementById('from-input');
			autocomplete = new google.maps.places.Autocomplete(inputField);
			google.maps.event.addListener(
				autocomplete, 'place_changed', function() {
			  var place = autocomplete.getPlace();
			  if (place.geometry) {
				var location = place.geometry.location;
				map.panTo(location);
				map.setZoom(12);
				marker.setMap(map);
				marker.setPosition(location);
			  }
			});

			google.maps.event.addListener(Demo.map, 'idle', function() {
			  autocomplete.setBounds(Demo.map.getBounds());
			});
	  }
	};
	function processSVData(data, status) {			
		if (status == google.maps.StreetViewStatus.OK) {
			var marker = new google.maps.Marker({
				position: data.location.latLng,
				map: Demo.map,
				icon: '<?php echo $term_icon; ?>',
				title: data.location.description
			});
			
			panorama.setPano(data.location.pano);
				panorama.setPov({
				heading: 270,
				pitch: 0
			});
			panorama.setVisible(true);
			
			google.maps.event.addListener(marker, 'click', function() {
				var markerPanoID = data.location.pano;
				/* Set the Pano to use the passed panoID */
				panorama.setPano(markerPanoID);
					panorama.setPov({
					heading: 270,
					pitch: 0
				});
				panorama.setVisible(true);
			});
		} else {
			alert('Street View data not found for this location.');			
		}
	}

	/* Set address on map function */
	var geocoder = new google.maps.Geocoder();
	function geocode() {
		var address = jQuery("#frontend_address").html();
		if(address) {     
			geocoder.geocode({ 'address': address, 'partialmatch': false}, geocodeResult);
		}
	}
	/*  Get the google result as per set your address fine on mao*/
	function geocodeResult(results, status) {  
	  if (status == 'OK' && results.length > 0) {
		Demo.map.fitBounds(results[0].geometry.viewport);
		Demo.map.setZoom(<?php echo $zooming_factor;?>);
		addMarkerAtCenter(results[0].geometry.location);
	  }
	}
	function getCenterLatLngText() {
		return '(' + Demo.map.getCenter().lat() +', '+ Demo.map.getCenter().lng() +')';
	}
	function addMarkerAtCenter(latLng) {
	  if(latLng==''){
		var latLng = new google.maps.LatLng(<?php echo $geo_latitude;?>, <?php echo $geo_longitude;?>);  
	  }  
	  Demo.map = new google.maps.Map(Demo.mapContainer, {  
			zoom: <?php echo $zoom_level;?>,
			center: latLng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
			
	  });
	  var marker = new google.maps.Marker({
		position: Demo.map.getCenter(),
		icon: '<?php echo $term_icon; ?>',
		draggable: true,
		map: Demo.map
	  });  
	  
	  updateMarkerPosition(marker.getPosition());
	  updateMarkerPositionend(marker.getPosition()); 
	  
	  google.maps.event.addListener(Demo.map, 'zoom_changed', function() {    
		  document.getElementById("zooming_factor").value = Demo.map.getZoom();
	  });

	  google.maps.event.addListener( Demo.map, 'maptypeid_changed', function() {
		document.getElementById( "map_view" ).value = document.getElementById( "map_view" ).value = CheckMap_TypeID(Demo.map.getMapTypeId());
	  } );
	  
	  google.maps.event.addListener(marker, 'drag', function() {
			updateMarkerPosition(marker.getPosition());
	  });
	  var text = 'Lat/Lng: ' + getCenterLatLngText();

	  if(currentReverseGeocodeResponse) {
		var addr = '';
		if(currentReverseGeocodeResponse.size == 0) {
		  addr = 'None';
		} else {
		  addr = currentReverseGeocodeResponse[0].formatted_address;
		}
		text = text + '<br>' + 'address: <br>' + addr;
	  }
	  var infowindow = new google.maps.InfoWindow({ content: text });
	  google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(map,marker);
	  });
	}   

	/* Update latitude/ longitude value on drag marker */
	function updateMarkerPosition(latLng)
	{
	  document.getElementById('geo_latitude').value = latLng.lat();
	  document.getElementById('geo_longitude').value = latLng.lng();  
	}
	function updateMarkerPositionend(latLng){
	  jQuery('input[name=map_view]').parent(".radio").removeClass('active');     
	  var radio = jQuery('input[name=map_view]:checked');
	  var updateDay = radio.val();  
	  if(updateDay=='Street map'){
		var geo_latitude= latLng.lat();
		var geo_longitude= latLng.lng();    
		var berkeley = new google.maps.LatLng(geo_latitude,geo_longitude);
		var sv = new google.maps.StreetViewService();
		sv.getPanoramaByLocation(berkeley, 50, processSVData);
	  }
	} 

	/* Change the street view map  */
	function toggleStreetView() {  
	  var latLng = Demo.map.getCenter();  
	  panorama = Demo.map.getStreetView();	
	  var sv = new google.maps.StreetViewService();
	  sv.getPanoramaByLocation(latLng, 50, processSVData);				
	  
	  panorama.setPosition(latLng);
	  panorama.setPov(/** @type {google.maps.StreetViewPov} */({
		heading: 265,
		pitch: 0
	  }));
	  var toggle = panorama.getVisible();
	  if (toggle == false) {
		document.getElementById( "map_view" ).value = 'Street Map';
		panorama.setVisible(true);
	  } else {        
		document.getElementById( "map_view" ).value = CheckMap_TypeID(Demo.map.getMapTypeId());
		panorama.setVisible(false);
	  }  
	}

	/*  Change map type view */
	function CheckMap_TypeID(){
	  var maptypeid=''
	  if( Demo.map.getMapTypeId()=='roadmap')
		maptypeid='Road Map';
	  else if(Demo.map.getMapTypeId() =='terrain')
		maptypeid='Terrain Map';
	  else if(Demo.map.getMapTypeId() =='satellite' || Demo.map.getMapTypeId() =='hybrid')
		maptypeid='Satellite Map';
	  else
		maptypeid='Road Map';    

	  return maptypeid;
	}

	/* Onload handler to fire off the app. */
	google.maps.event.addDomListener(window, 'load', Demo.init);
	</script>
<?php
}
 }else{ 
$address = get_post_meta($post->ID,'address',true);
$address = str_replace('++','+',str_replace(' ','+',str_replace(',','+',$address)));
$address = "Manhattan, NYC, USA";
if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; }
?>
<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo $http; ?>maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $address;?>&amp;ie=UTF8&amp;hq=&amp;hnear=Surat,+Gujarat,+India&amp;ll=21.194655,72.557831&amp;spn=0.906514,1.783905&amp;z=10&amp;output=embed"></iframe><br /><small><a href="<?php echo $http; ?>maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=<?php echo $address;?>&amp;ie=UTF8&amp;hq=&amp;hnear=Surat,+Gujarat,+India&amp;ll=21.194655,72.557831&amp;spn=0.906514,1.783905&amp;z=10" style="color:#0000FF;text-align:left"><?php _e("View Larger Map",DIR_DOMAIN);?></a></small>
<?php }?>