<?php
/*
 * show map while submit form.
 */
$addval = '';
$zoomval = '';
$latval = '';
$longval = '';
if(isset($_REQUEST['pid']) && $_REQUEST['pid']!='')
{
	$addval = get_post_meta($_REQUEST['pid'],'address',true);
	$zoomval = get_post_meta($_REQUEST['pid'],'zooming_factor',true);
	$latval = get_post_meta($_REQUEST['pid'],'geo_latitude',true);
	$longval = get_post_meta($_REQUEST['pid'],'geo_longitude',true);
	$post_id  = @$_REQUEST['pid'];
	
}elseif(isset($_SESSION['custom_fields']) && $_SESSION['custom_fields']['address']!=''){
	$addval =$_SESSION['custom_fields']['address'];
	$zoomval = $_SESSION['custom_fields']['zooming_factor'];
	$latval = $_SESSION['custom_fields']['geo_latitude'];
	$longval = $_SESSION['custom_fields']['geo_longitude'];
	$post_id = $post->ID;
}elseif(isset($_REQUEST['action']) && $_REQUEST['action']=='submit_category_custom_fields'){
	$addval =$_REQUEST['address'];
	$zoomval = $_REQUEST['zooming_factor'];
	$latval = $_REQUEST['geo_latitude'];
	$longval = $_REQUEST['geo_longitude'];
	
}

if(get_post_meta($post_id,'zooming_factor',true)){
	$zooming_factor = get_post_meta($post_id,'zooming_factor',true);
}else{
	$zooming_factor = 13; 
}	
if(get_post_meta($post_id,'map_view',true))
{ 
	$maptype = get_post_meta($post_id,'map_view',true);
	if($maptype=='Street map'){$maptype = 'ROADMAP';} elseif($maptype=='Satellite Map') { $maptype = 'SATELLITE'; } elseif($maptype=='Terrain Map') { $maptype = 'TERRAIN'; }  else { $maptype = 'ROADMAP'; }
}else{
	$maptype = 'ROADMAP';
}

$google_map_customizer=get_option('google_map_customizer');/* store google map customizer required formate.*/
wp_print_scripts( 'google-maps-apiscript' );
?>

<script type="text/javascript">
/* <![CDATA[ */
var map;
var marker;
var autocomplete;
var latlng;
var geocoder;
var address;
var lat;
var lng;
var currentReverseGeocodeResponse;
var CITY_MAP_CENTER_LAT = '<?php echo apply_filters('tmpl_mapcenter_lat',40.714623); ?>';
var CITY_MAP_CENTER_LNG = '<?php echo apply_filters('tmpl_mapcenter_lang',-74.006605);?>';
var CITY_MAP_ZOOMING_FACT = '<?php echo apply_filters('tmpl_map_zooming',13); ?>';
var street_map_view='<?php echo (isset($_POST['map_view']))? $_POST['map_view'] :'';?>';
var street_map_view_post ='<?php echo ($post_id!='' && get_post_meta($post_id,'map_view',true)=='Street map')? 'Street map' : ''?>';
var geocoder = new google.maps.Geocoder();
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
	marker = new google.maps.Marker();
	jQuery('input[name=map_view]').parent(".radio").removeClass('active');			
	var radio = jQuery('input[name=map_view]:checked');
	var updateDay = radio.val();	
	if(updateDay=='Road Map'){
		map.setMapTypeId(google.maps.MapTypeId.ROADMAP);		
		street_map_view='Road Map';
	}else if(updateDay=='Terrain Map'){
		map.setMapTypeId(google.maps.MapTypeId.TERRAIN);
		street_map_view='Terrain Map';
	}else if(updateDay=='Satellite Map'){
		map.setMapTypeId(google.maps.MapTypeId.SATELLITE);		
		street_map_view='Satellite Map';
	}
	
    
    geocoder = new google.maps.Geocoder();
	google.maps.event.addListener(map, 'zoom_changed', function() {
		document.getElementById("zooming_factor").value = map.getZoom();
	});
	
    /* Initialize autocomplete.*/
	var inputField = document.getElementById('address');
	autocomplete = new google.maps.places.Autocomplete(inputField);	
	google.maps.event.addListener(autocomplete, 'place_changed', function() {
	  var place = autocomplete.getPlace();	  
	  if (place.geometry) {
		initialize();
		var location = place.geometry.location;
		map.panTo(location);
		map.setZoom(12);
		marker.setMap(null);
		marker = new google.maps.Marker({
			position: map.getCenter(),
			icon: '<?php echo apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png'); ?>',
			draggable: true,
			map: map
		});		
		updateMarkerPosition(marker.getPosition());
		google.maps.event.addListener(marker, 'drag', function() {    	
			updateMarkerPosition(marker.getPosition());
		});
		setTimeout("set_address_mapview()",500); 
		show_error_msg_map('','');
	  }
	});

	
	 var geo_latitude= jQuery('#geo_latitude').val();
	 var geo_longitude= jQuery('#geo_longitude').val();	 
	 if((street_map_view_post=='Street map' && (street_map_view=='Street map' || updateDay=='Street map')) && (geo_latitude!='' && geo_latitude != 0  && geo_longitude!='' && geo_longitude != 0) ) {		
		
		var geo_latitude= jQuery('#geo_latitude').val();
	 	var geo_longitude= jQuery('#geo_longitude').val();		
		var berkeley = new google.maps.LatLng(geo_latitude,geo_longitude);
		var sv = new google.maps.StreetViewService();
		sv.getPanoramaByLocation(berkeley, 50, processSVData);		
	 }
	 
	  /* Add a DOM event listener to react when the user selects a country.*/
	 if(jQuery('#country_id').length >0){
		  google.maps.event.addDomListener(document.getElementById('country_id'), 'change', setAutocompleteCountry);
		  google.maps.event.addListener(map, 'idle', function() {
			autocomplete.setBounds(map.getBounds());
		  });
	 }
}

function geocode() {
	var location='';	
	if (jQuery('#zones_id').length && jQuery("#zones_id").val() !='') {
		var zones_name=jQuery("#zones_id option:selected").html();
		location+=','+zones_name+',';
	}
	if (jQuery('#country_id').length && jQuery("#country_id").val() !='') {
		var country_name=jQuery("#country_id option:selected").html();
		location+=country_name;
	}
	
	
	var address = document.getElementById("address").value;	
	var placeholder= jQuery('#address').attr('placeholder');
	var location_address= address+location;	
	
	if(address!='' && address!='Enter a location') {
		geocoder.geocode({
		'address': location_address,
		'partialmatch': false}, geocodeResult);
	}else{
		document.getElementById('geo_latitude').value = '';
		document.getElementById('geo_longitude').value = '';
		document.getElementById('zooming_factor').value = '';
	}
}
  
function geocodeResult(results, status) {	
    if (status == 'OK' && results.length > 0) {
      map.fitBounds(results[0].geometry.viewport);
	  map.setCenter(results[0].geometry.location);	
	  map.setZoom(<?php echo $zooming_factor;?>);	  
	  marker.setMap(null);
	  marker = new google.maps.Marker();
	  addMarkerAtCenter(results);
	  show_error_msg_map('','');
    } else {		
                    show_error_msg_map("Error: google is not able to find out the address you enter, Please enter correct address.");
                    /*alert("Geocode was not successful for the following reason: " + status);*/
    }
}

function getCenterLatLngText() {
	return '(' + map.getCenter().lat() +', '+ map.getCenter().lng() +')';
}
function addMarkerAtCenter(results) {
	marker = new google.maps.Marker({
		position: results[0].geometry.location,
		icon: '<?php echo apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png'); ?>',
		draggable: true,
		map: map
	});		
	
	updateMarkerPosition(marker.getPosition());	
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
   
function updateMarkerPosition(latLng)
{
	document.getElementById('geo_latitude').value = latLng.lat();
	document.getElementById('geo_longitude').value = latLng.lng();
}


function changeMap()
{
	var newlatlng = document.getElementById('geo_latitude').value;
	var newlong = document.getElementById('geo_longitude').value;
	/* address latitude and longitude blank then return */
	if(newlatlng=='' && newlong==''){
		return '';	
	}
	var latlng = new google.maps.LatLng(newlatlng,newlong);
	map = new google.maps.Map(document.getElementById('map_canvas'), {
		zoom: <?php echo $zooming_factor;?>,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.<?php echo $maptype;?>
	});
	
	var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
	map.setOptions({styles: styles});	
	marker = new google.maps.Marker({
		position: latlng,
		title: 'Point A',
		icon: '<?php echo apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png'); ?>',
		map: map,
		
	});
	
	updateMarkerPosition(marker.getPosition());
	google.maps.event.addListener(marker, 'drag', function() {    	
		updateMarkerPosition(marker.getPosition());
	});

}
/* Find Out Street View Available or not  */
function processSVData(data, status) {
	
	if (status == google.maps.StreetViewStatus.OK) {
		panorama = new google.maps.StreetViewPanorama(document.getElementById('map_canvas'));		
                panorama.set('addressControl', false);
		marker = new google.maps.Marker({
		 position: data.location.latLng,
		 map: map,
		 icon: '<?php echo apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png'); ?>',
		 title: data.location.description
		});
		panorama.setPano(data.location.pano);
		panorama.setPov({
		 heading: 270,
		 pitch: 0
		});    
		google.maps.event.addListener(marker, 'click', function() {
		 var markerPanoID = data.location.pano;
		 /* Set the Pano to use the passed panoID*/
		 panorama.setPano(markerPanoID);
		 panorama.setPov({
		   heading: 270,
		   pitch: 0
		 });
		 panorama.setVisible(true);
		});
		
	  show_error_msg_map('','');
	} else {
		/*alert('Street View data not found for this location. So change your Map view');*/
		show_error_msg_map("Street View data not found for this location. So change your Map view",'1');		
	}
	
	return true;
}
	
google.maps.event.addDomListener(window, 'load', initialize);
<?php if(isset($_REQUEST['pid']) || isset($_REQUEST['post'])):?>
	google.maps.event.addDomListener(window, 'load', changeMap);
<?php else: ?>
	google.maps.event.addDomListener(window, 'load', geocode);
<?php endif; ?>

/* JavaScript Document*/
jQuery(document).ready(function(){
	
	/* Display map view as per city change */
	jQuery('select[name^=post_city_id]').live( 'change', function(e) {
		/* Set address marker if address not blank */
		var address = document.getElementById("address").value;
		var placeholder=jQuery('#address').attr('placeholder');		
		if(address=='' && address=='Enter a location'){			
			var city_name=jQuery("#city_id option:selected").html();
			geocoder.geocode( { 'address': city_name}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
				  map.setCenter(results[0].geometry.location);					  
				  map.setZoom(jquery('#zooming_factor').val());
				}
			});
		}
		
	});


        /* Set map view on map view click  */
        jQuery('input[name=map_view]').live("click", function(e) {    
                initialize();	
                geocode();
                setTimeout("set_address_mapview()",500);
        });
});
/* Show map address error message on google map */
function show_error_msg_map(msg,set){	
	
	if(msg!=''){
		jQuery('#map_address_message').html(msg);
		jQuery('#map_address_message').fadeIn('slow');
	}else{
		jQuery('#map_address_message').html('');
		jQuery('#map_address_message').css('display','none');
	}
	if(set==1){
		changeMap();		
	}
}

/* [START region_setcountry]*/
/* Set the country restriction based on user input.*/
/* Also center and zoom the map on the given country.*/
function setAutocompleteCountry() {
  var country = jQuery('select#country_id option:selected').attr('data-name'); /*document.getElementById('country').value;*/
  if (country != '') {	  
    autocomplete.setComponentRestrictions({ 'country': country });    
  }else{
	autocomplete.setComponentRestrictions([]);  
  }
}
/* [END region_setcountry]*/

/* Set google map view  */
function set_address_mapview(){
	
	jQuery('input[name=map_view]').parent(".radio").removeClass('active');			
	var radio = jQuery('input[name=map_view]:checked');
	var updateDay = radio.val();	
	if(updateDay=='Road Map'){
		map.setMapTypeId(google.maps.MapTypeId.ROADMAP);		
		street_map_view='Road Map';		
	}else if(updateDay=='Terrain Map'){
		map.setMapTypeId(google.maps.MapTypeId.TERRAIN);
		street_map_view='Terrain Map';		
	}else if(updateDay=='Satellite Map'){
		map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
		street_map_view='Satellite Map';
	}
	
	var geo_latitude= jQuery('#geo_latitude').val();
 	var geo_longitude= jQuery('#geo_longitude').val();		
	if((updateDay=='Street map' || updateDay=='Street Map' ) && (geo_latitude!='' && geo_latitude != 0  && geo_longitude!='' && geo_longitude != 0) ) {
		var berkeley = new google.maps.LatLng(geo_latitude,geo_longitude);
		var sv = new google.maps.StreetViewService();
		sv.getPanoramaByLocation(berkeley, 50, processSVData);
	}
}
/* ]]> */
</script>
<?php
if(is_templ_wp_admin()): /* Didsplay google map address in backend */

?>
    <div class="clearfix">
      <input type="text" class="pt_input_text regular-text" value="<?php if(isset($_REQUEST['post']))echo esc_html(get_post_meta($_REQUEST['post'],'address',true)); ?>" id="address" name="address" placeholder="<?php echo __('Enter a location','templatic-admin')?>" />
      <p class="description"><?php echo $pt_metabox['desc']; ?></p>
      <span class="message_error2" id="address_error"></span>
      
      <input type="hidden" class="textfield" value="<?php if(isset($_REQUEST['post']))echo get_post_meta($_REQUEST['post'],'zooming_factor',true); ?>" id="zooming_factor" name="zooming_factor" />
      <input type="hidden" class="textfield" value="<?php if(isset($_REQUEST['post']))echo get_post_meta($_REQUEST['post'],'geo_latitude',true); ?>" id="geo_latitude" name="geo_latitude" />
      <input type="hidden" class="textfield" value="<?php if(isset($_REQUEST['post']))echo get_post_meta($_REQUEST['post'],'geo_longitude',true); ?>" id="geo_longitude" name="geo_longitude" />
    </div>
     
    <div class="form_row clearfix">
        <div class="google-map-wrapper">
            <div id="map_canvas" class="backend_map map_wrap form_row clearfix"></div>
            <div id="map_address_message" style="display:none"></div>
        </div>
    </div>    
<?php else: 
	do_action('tmpl_before_geomap');
	?>
    <div class="form_row clearfix">     
    	<input type="text" class="textfield" value="<?php echo esc_html($addval); ?>" id="address" name="address"  <?php echo $val['extra_parameter']; ?> placeholder="<?php _e('Enter a location','templatic')?>" />
        <span class="message_note"><?php echo $admin_desc;?></span>
        <span class="message_error2" id="address_error"></span>
        <input type="hidden" class="textfield" value="<?php echo $zoomval; ?>" id="zooming_factor" name="zooming_factor" />
        <input type="hidden" class="textfield" value="<?php echo $latval; ?>" id="geo_latitude" name="geo_latitude" />
        <input type="hidden" class="textfield" value="<?php echo $longval; ?>" id="geo_longitude" name="geo_longitude" />
    </div>
    <div class="form_row clearfix">
        <div class="google-map-wrapper">
            <div id="map_canvas" class="form_row clearfix"></div>
            <div id="map_address_message" style="display:none;"></div>
        </div>
    </div>
<?php 
	do_action('tmpl_after_geomap');
	endif; ?>