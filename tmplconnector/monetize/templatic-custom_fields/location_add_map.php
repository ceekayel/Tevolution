<?php
if(get_post_meta($post->ID,'zooming_factor',true))
{ $zooming_factor = get_post_meta($post->ID,'zooming_factor',true);
}else{
	$zooming_factor = 13; }
	
if(get_post_meta($post->ID,'map_view',true))
{ 
	$maptype = get_post_meta($post->ID,'map_view',true);
	if($maptype=='Street map'){$maptype = 'ROADMAP';} elseif($maptype=='Satellite Map') { $maptype = 'SATELLITE'; } elseif($maptype=='Terrain Map') { $maptype = 'TERRAIN'; }  else { $maptype = 'ROADMAP'; }
}else{
	$maptype = 'ROADMAP';
	}

$google_map_customizer=get_option('google_map_customizer');// store google map customizer required formate.
?>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.5&sensor=false&libraries=places"></script>
<script type="text/javascript">
/* <![CDATA[ */
var map;
var latlng;
var geocoder;
var address;
var lat;
var lng;
var centerChangedLast;
var reverseGeocodedLast;
var currentReverseGeocodeResponse;
var CITY_MAP_CENTER_LAT = '<?php echo apply_filters('tmpl_mapcenter_lat',40.714623); ?>';
var CITY_MAP_CENTER_LNG = '<?php echo apply_filters('tmpl_mapcenter_lang',-74.006605);?>';
var CITY_MAP_ZOOMING_FACT = '<?php echo apply_filters('tmpl_map_zooming',13); ?>';
var street_map_view='<?php echo (isset($_SESSION['custom_fields']['map_view']))? $_SESSION['custom_fields']['map_view'] :'';?>';
var street_map_view_post ='<?php echo ($post->ID!='' && get_post_meta($post->ID,'map_view',true)=='Street map')? 'Street map' : ''?>';
var panorama;
  function initialize() {
    var latlng = new google.maps.LatLng(CITY_MAP_CENTER_LAT,CITY_MAP_CENTER_LNG);
    var isDraggable = jQuery(document).width() > 480 ? true : false;
    var myOptions = {
      zoom: <?php echo $zooming_factor;?>,
      center: latlng,
	 draggable: isDraggable,
      mapTypeId: google.maps.MapTypeId.<?php echo $maptype;?>
    };
    	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);  	
	var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
	map.setOptions({styles: styles});
	jQuery('input[name=map_view]').parent(".radio").removeClass('active');			
	var radio = jQuery('input[name=map_view]:checked');
	var updateDay = radio.val();	
	if(updateDay=='Road Map'){
		map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
		google.maps.event.trigger(map, 'resize');
		map.setCenter(map.center); // be sure to reset the map center as well	
		street_map_view='Road Map';
	}else if(updateDay=='Terrain Map'){
		map.setMapTypeId(google.maps.MapTypeId.TERRAIN);
		google.maps.event.trigger(map, 'resize');
		map.setCenter(map.center); // be sure to reset the map center as well	
		street_map_view='Terrain Map';
	}else if(updateDay=='Satellite Map'){
		map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
		google.maps.event.trigger(map, 'resize');
		map.setCenter(map.center); // be sure to reset the map center as well	
		street_map_view='Satellite Map';
	}
	
    
    geocoder = new google.maps.Geocoder();
	google.maps.event.addListener(map, 'zoom_changed', function() {
			document.getElementById("zooming_factor").value = map.getZoom();
		});
	setupEvents();
	 if(street_map_view_post=='Street map' || street_map_view=='Street map' || updateDay=='Street Map') {
		
		var geo_latitude= jQuery('#geo_latitude').val();
	 	var geo_longitude= jQuery('#geo_longitude').val();
		var berkeley = new google.maps.LatLng(geo_latitude,geo_longitude);
		var sv = new google.maps.StreetViewService();
		sv.getPanoramaByLocation(berkeley, 50, processSVData);
	 }
	
   // centerChanged();
  }
  function setupEvents() {
    reverseGeocodedLast = new Date();
    centerChangedLast = new Date();
	
    setInterval(function() {
      if((new Date()).getSeconds() - centerChangedLast.getSeconds() > 1) {
        if(reverseGeocodedLast.getTime() < centerChangedLast.getTime())
          reverseGeocode();
      }
    }, 1000);
	google.maps.event.addListener(map, 'zoom_changed', function() {
			//document.getElementById("zooming_factor").value = map.getZoom();
		});
	}
  function getCenterLatLngText() {
    return '(' + map.getCenter().lat() +', '+ map.getCenter().lng() +')';
  }
  function centerChanged() {
    centerChangedLast = new Date();
    var latlng = getCenterLatLngText();
    //document.getElementById('latlng').innerHTML = latlng;
    document.getElementById('address').innerHTML = '';
    currentReverseGeocodeResponse = null;
  }
  function reverseGeocode() {
    reverseGeocodedLast = new Date();
    geocoder.geocode({latLng:map.getCenter()},reverseGeocodeResult);
  }
  function reverseGeocodeResult(results, status) {
    currentReverseGeocodeResponse = results;
    if(status == 'OK') {
      if(results.length == 0) {
        document.getElementById('address').innerHTML = 'None';
      } else {
        document.getElementById('address').innerHTML = results[0].formatted_address;
      }
    } else {
      document.getElementById('address').innerHTML = 'Error';
    }
  }
  function geocode() {
	var location='';
	/*if (jQuery('#city_id').length) {
		var city_name=jQuery("#city_id option:selected").html();
		location+=','+city_name;
	}*/	
	if (jQuery('#zones_id').length) {
		var zones_name=jQuery("#zones_id option:selected").html();
		location+=','+zones_name+',';
	}
	if (jQuery('#country_id').length) {
		var country_name=jQuery("#country_id option:selected").html();
		location+=country_name;
	}
	
	
    var address = document.getElementById("address").value;
    var location_address= address+location;        
    if(address) {	    
		geocoder.geocode({
		'address': location_address,
		'partialmatch': false}, geocodeResult);
	 }
  }
  function geocodeResult(results, status) {	 
    if (status == 'OK' && results.length > 0) {
      map.fitBounds(results[0].geometry.viewport);
	  map.setZoom(<?php echo $zooming_factor;?>);
	  addMarkerAtCenter();
	  
    } else {
      alert("Geocode was not successful for the following reason: " + status);
    }
	
}
  function addMarkerAtCenter() {
	var marker = new google.maps.Marker({
        position: map.getCenter(),
		icon: '<?php echo TEMPL_PLUGIN_URL; ?>/images/pin.png',
		draggable: true,
        map: map
    });
	
	updateMarkerAddress('Dragging...');
	updateMarkerPosition(marker.getPosition());
	updateMarkerPositionend(marker.getPosition());
	geocodePosition(marker.getPosition());
	google.maps.event.addListener(marker, 'dragstart', function() {
    	updateMarkerAddress('Dragging...');
    });
	
    google.maps.event.addListener(marker, 'drag', function() {
    	updateMarkerPosition(marker.getPosition());
    });
	
    google.maps.event.addListener(marker, 'dragend', function() {
		updateMarkerPositionend(marker.getPosition());										  
    		geocodePosition(marker.getPosition());
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
  
  function updateMarkerAddress(str)
   {
	 //document.getElementById('address').value = str;
   }
   
  function updateMarkerStatus(str)
   {
  	 document.getElementById('markerStatus').innerHTML = str;
   }
   
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
 
	var geocoder = new google.maps.Geocoder();
	function geocodePosition(pos) {
	  geocoder.geocode({
		latLng: pos
	  }, function(responses) {
		if (responses && responses.length > 0) {
		  updateMarkerAddress(responses[0].formatted_address);
		} else {
		  updateMarkerAddress('Cannot determine address at this location.');
		}
	  });
	}
  function changeMap()
   {
		var newlatlng = document.getElementById('geo_latitude').value;
		var newlong = document.getElementById('geo_longitude').value;
		var latlng = new google.maps.LatLng(newlatlng,newlong);
		var map = new google.maps.Map(document.getElementById('map_canvas'), {
		zoom: <?php echo $zooming_factor;?>,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.<?php echo $maptype;?>
	  });
		
		var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
		map.setOptions({styles: styles});
	
		var marker = new google.maps.Marker({
		position: latlng,
		title: 'Point A',
		icon: '<?php echo TEMPL_PLUGIN_URL; ?>/images/pin.png',
		map: map,
		draggable: true
	  });
		
	updateMarkerAddress('Dragging...');
	updateMarkerPosition(marker.getPosition());
	geocodePosition(marker.getPosition());
    google.maps.event.addListener(marker, 'dragstart', function() {
    	updateMarkerAddress('Dragging...');
    });
	
    google.maps.event.addListener(marker, 'drag', function() {
    	//updateMarkerStatus('Dragging...');
    	updateMarkerPosition(marker.getPosition());
    });
	
    google.maps.event.addListener(marker, 'dragend', function() {
    	//updateMarkerStatus('Drag ended');
    	geocodePosition(marker.getPosition());
   });
	
   }
/* Find Out Street View Available or not  */
function processSVData(data, status) {
  if (status == google.maps.StreetViewStatus.OK) {
	  
  panorama = new google.maps.StreetViewPanorama(document.getElementById('map_canvas'));
    var marker = new google.maps.Marker({
	 position: data.location.latLng,
	 map: map,
	 icon: '<?php echo TEMPL_PLUGIN_URL; ?>/images/pin.png',
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
	 // Set the Pano to use the passed panoID
	 panorama.setPano(markerPanoID);
	 panorama.setPov({
	   heading: 270,
	   pitch: 0
	 });
	 panorama.setVisible(true);
    });
  } else {
    alert('Street View data not found for this location. So change your Map view');
  }
}
	
google.maps.event.addDomListener(window, 'load', initialize);
<?php if(isset($_REQUEST['pid']) || isset($_REQUEST['post']) || isset($_REQUEST['backandedit'])|| isset($_REQUEST['renew'])):?>
	google.maps.event.addDomListener(window, 'load', changeMap);
<?php else: ?>
	google.maps.event.addDomListener(window, 'load', geocode);
<?php endif; ?>
jQuery(document).ready(function() {
	jQuery('#set_address_map').click(function(){	
		jQuery('#address_latitude').fadeIn('slow');
		jQuery('#address_longitude').fadeIn('slow');
	});
});
/* ]]> */
</script>
<?php
if(is_templ_wp_admin()): ?>
     <div class="form_row clearfix">
          <label><?php echo $pt_metabox['label'].@$is_required; ?></label>
          <input type="text" class="pt_input_text" value="<?php if(isset($_REQUEST['post']))echo esc_html(get_post_meta($_REQUEST['post'],'address',true)); ?>" id="address" name="address" />
          <a id="set_address_map"  class="btn_input_normal btn_spacer button" onclick="geocode();initialize();" /><?php _e('Set Address on Map',DOMAIN);?></a>
          <p class="description"><?php echo $pt_metabox['desc']; ?></p>
           <input type="hidden" class="textfield" value="<?php if(isset($_REQUEST['post']))echo get_post_meta($_REQUEST['post'],'zooming_factor',true); ?>" id="zooming_factor" name="zooming_factor" />     
          <span class="message_error2" id="address_error"></span>
     </div>
     
     <div id="address_latitude" class="form_row clearfix" style="display:none">
          <label><?php _e("Address Latitude",DOMAIN); ?><span></span></label>
          <input type="text" onblur="changeMap();" class="textfield" value="<?php if(isset($_REQUEST['post']))echo get_post_meta($_REQUEST['post'],'geo_latitude',true); ?>" id="geo_latitude" name="geo_latitude" />
          <span class="message_note"><?php _e("Enter latitude for Google Map perfection. e.g.  39.955823048131286",DOMAIN); ?></span><span class="" id="geo_latitude_error"></span>
     </div>
     
     <div id="address_longitude" class="form_row clearfix" style="display:none">
          <label><?php _e("Address Longitude",DOMAIN); ?><span></span></label>
          <input type="text" placeholder="" onblur="changeMap();" class="textfield" value="<?php if(isset($_REQUEST['post']))echo get_post_meta($_REQUEST['post'],'geo_longitude',true); ?>" id="geo_longitude" name="geo_longitude" />
          <span class="message_note"><?php _e("Enter logngitude for Google Map perfection. e.g.  -75.14408111572266",DOMAIN); ?></span><span class="" id="geo_longitude_error"></span>
     </div>
     
     <div class="form_row clearfix">
          <div id="map_canvas" class="backend_map map_wrap form_row clearfix"></div>
     </div>    
<?php else: ?>
     <div class="form_row clearfix">
         <label><?php echo $site_title.$is_required; ?></label>
         <?php	   
         $addval = '';
         $zoomval = '';
         $latval = '';
         $longval = '';
         if(isset($_REQUEST['pid']))
         {
              $addval = get_post_meta($_REQUEST['pid'],'address',true);
              $zoomval = get_post_meta($_REQUEST['pid'],'zooming_factor',true);
              $latval = get_post_meta($_REQUEST['pid'],'geo_latitude',true);
              $longval = get_post_meta($_REQUEST['pid'],'geo_longitude',true);
         }
         if(isset($_SESSION['custom_fields']) && isset($_REQUEST['backandedit']))
         {
              $addval = $_SESSION['custom_fields']['address'];
              $zoomval = $_SESSION['custom_fields']['zooming_factor'];
              $latval = $_SESSION['custom_fields']['geo_latitude'];
              $longval = $_SESSION['custom_fields']['geo_longitude'];
         }
         ?>
         <input type="text" class="textfield" value="<?php echo esc_html($addval); ?>" id="address" name="address"  <?php echo $val['extra_parameter']; ?>/>
         <a id="set_address_map" class="btn_input_normal btn_spacer button" onclick="geocode();initialize();" /><?php _e('Set Address on Map',DOMAIN);?></a>
         <span class="message_note"><?php echo $admin_desc;?></span>
         <span class="message_error2" id="address_error"></span>
         <input type="hidden" class="textfield" value="<?php echo $zoomval; ?>" id="zooming_factor" name="zooming_factor" />     
	</div>
     
     
     <div id="address_latitude" class="form_row clearfix" style="display:none">
          <label><?php _e("Address Latitude",DOMAIN); ?><span></span></label>
          <input type="text" onblur="changeMap();" class="textfield" value="<?php echo $latval; ?>" id="geo_latitude" name="geo_latitude" />
          <span class="message_note"><?php _e("Please enter latitude for google map perfection. eg.: 39.955823048131286",DOMAIN); ?></span><span class="" id="geo_latitude_error"></span>
     </div>
     
     <div id="address_longitude" class="form_row clearfix" style="display:none">
          <label><?php _e("Address Longitude",DOMAIN); ?><span></span></label>
          <input type="text" placeholder="" onblur="changeMap();" class="textfield" value="<?php echo $longval; ?>" id="geo_longitude" name="geo_longitude" />
          <span class="message_note"><?php _e("Please enter logngitude for google map perfection. eg.: -75.14408111572266",DOMAIN); ?></span><span class="" id="geo_longitude_error"></span>
     </div>
     
     <div class="form_row clearfix">
          <div id="map_canvas" class="form_row clearfix"></div>
     </div>
     
<?php endif; ?>
